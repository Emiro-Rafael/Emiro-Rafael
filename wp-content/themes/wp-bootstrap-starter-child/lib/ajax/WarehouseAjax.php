<?php

require_once('SCAjax.php');

class WarehouseAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'warehouse';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'login_warehouse' => 'loginWarehouse',
                'get_order_from_queue' => 'getOrderFromQueue',
                'return_order_to_queue' => 'returnOrderToQueue',
                'order_session_expired' => 'orderSessionExpired',
                'get_orders_in_queue' => 'getOrdersInQueue',
                'send_print_request' => 'sendPrintRequest',
                'complete_order' => 'completeOrder',
                'calc_orders_bundle_box_size' => 'calcOrdersBundleBoxSize',                
                'get_packing_list' => 'getPackingList',
                'send_print_pack_request' => 'sendPrintPackRequest',
                'complete_pack_order' => 'completePackOrder',

                'print_label' => 'printLabel'
            ]
        );
    }
    
    public function setup_actions($public = TRUE)
    {
        $className = $this->getClassName();
        
        foreach ($this->actions as $action_name => $method_name) {
            add_action("wp_ajax_{$action_name}", array($className, $method_name));
            if($public) {
                add_action("wp_ajax_nopriv_{$action_name}", array($className, $method_name));
            }
        }
    }

    public static function loginWarehouse()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $user = get_user_by('email', $email);
    
        if (!$user) {
            wp_send_json_error(['errors' => ['email' => 'User not found.']], 300);
            return;
        }
    
        $credentials = array(
            'user_login'    => $user->user_login,
            'user_password' => $password,
            'remember'      => true,
        );
    
        $user = wp_signon($credentials, true);
        setcookie('warehouse_id', $user->ID, time()+3600, '/');
    
        if (is_wp_error($user)) {
            wp_send_json_error(['errors' => ['email' => 'Invalid login credentials.']], 300);
        } else {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true, true);
        
            wp_send_json_success(array('redirect_url' => '/ops'));
        }
    }

    private static function validateWarehouseUserAccess() {
        
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            return wp_send_json_error(['error' => 'not_logged_in'], 300);
        }
        if (!in_array('warehouse', $user->roles) && !in_array('administrator', $user->roles)) {
            return wp_send_json_error(['error' => 'not_allowed'], 300);
        }
        return true;
    }
    
    
    // POWER PICK

    public static function getSuitebleOrdersBundleBoxSize($orders_bundle_data, $box_sizes) {

        function gramToOz($grams) {
            return $grams * 0.03527396;
        }
        $dimensions = [];

        foreach ($orders_bundle_data['items'] as $item) {
            // If item has nested products (like country products)
            if (is_array($item) && !isset($item['parameters'])) {
                $products = array_values($item);
            } else {
                $products = [$item];
            }
    
            // Process each product
            foreach ($products as $product) {
                $quantity = $product['quantity'] ?? 1;
                
                // Add dimensions for each quantity
                for ($i = 0; $i < $quantity; $i++) {
                    $dimensions[] = [
                        'length' => floatval($product['parameters']['length']['value']),
                        'width'  => floatval($product['parameters']['width']['value']),
                        'height' => floatval($product['parameters']['height']['value']),
                        'weight' => floatval($product['parameters']['weight']['value'])
                    ];
                }
            }
        }
    
        // Calculate totals
        $total_weight = 0;
        $total_volume = 0;
        $max_dimension = 0;
    
        foreach ($dimensions as $dim) {
            $total_weight += $dim['weight'];
            $total_volume += $dim['length'] * $dim['width'] * $dim['height'];
            $max_dimension = max($max_dimension, $dim['length'], $dim['width'], $dim['height']);
        }
    
        // Find suitable box
        $suitable_box = null;
        foreach ($box_sizes as $box) {
            $box_max_dim = max($box->int_length, $box->int_width, $box->int_height);
            $box_volume = $box->int_length * $box->int_width * $box->int_height;
    
            if ($box_max_dim >= $max_dimension && $box_volume >= $total_volume) {
                $suitable_box = $box;
                break;
            }
        }

        return (object) [
            'suitable_box' => $suitable_box ? $suitable_box->name : null,
            'total_weight' => round(gramToOz($total_weight), 2),
            'total_volume' => round($total_weight, 0),
            'max_dimension' => $max_dimension
        ];
    }

    public static function calcOrdersBundleBoxSize() {
        if (!self::validateWarehouseUserAccess()) return;

        if (empty($_POST['orders_bundle_data'])) {
            wp_send_json_error('No order bundle data received', 400);
            return;
        }
        // Get order data from POST
        $orders_bundle_data = json_decode(stripslashes($_POST['orders_bundle_data']), true); 

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error([
                'message' => 'JSON decode error: ' . json_last_error_msg(),
                'raw_data' => $_POST['orders_bundle_data']
            ]);
            return;
        }

        if (empty($orders_bundle_data['items'])) {
            wp_send_json_error(['message' => 'Invalid order data structure']);
            return;
        }

    
        $warehouse = new Warehouse();
        $box_sizes = $warehouse->getBoxSizes();

        if(empty($box_sizes)) return wp_send_json_error('No box sizes found', 400);

        $bundle_box_data = self::getSuitebleOrdersBundleBoxSize($orders_bundle_data, $box_sizes);

        
        $result = [
            'suitable_box' => $bundle_box_data->suitable_box ?? '',
            'total_weight' => $bundle_box_data->total_weight ?? 0,
            'total_volume' => $bundle_box_data->total_volume ?? 0,
            'max_dimension' => $bundle_box_data->max_dimension ?? 0
        ];

        if($bundle_box_data) {
            $resultSaving = $warehouse->saveOrdersBundleBoxSize(explode(",", $orders_bundle_data['id']), $result);
        }
    
        return wp_send_json_success($result);
    }

    public static function getOrderFromQueue()
    {
        if (!self::validateWarehouseUserAccess()) return;
    
        try {
            $warehouse = new Warehouse();
            $warehouse->adjustPickerNumber();
            $items = $warehouse->getNextOrder(false);
            $customer_info = $warehouse->getCustomerInformation();
            
            $order = [
                'id' => $items['ids'],
                'box_size_data' => $items['box_size_data'],
                'name' => $customer_info->shipping_name,
                'address_1' => $customer_info->address_1 . " " . $customer_info->address_2,
                'address_2' => $customer_info->city . " " . $customer_info->state . " " . $customer_info->zip,
                'items' => $items['items'],
                'total_items' => $items['total_items'] 
            ];

            update_user_meta(intval($_COOKIE['warehouse_id']), 'active_order', $order);
            wp_send_json_success($order);

        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
       
    }

    public static function returnOrderToQueue()
    {
        if (!self::validateWarehouseUserAccess()) return;

        try {
            update_user_meta(intval($_COOKIE['warehouse_id']), 'active_order', []);
            $order_ids = $_POST['order_id'];
            $warehouse = new Warehouse();
            $warehouse->setOrdersSessionExpired($order_ids);
            wp_send_json_success(['success' => true]);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
        
    }

    public static function orderSessionExpired() {
        if (!self::validateWarehouseUserAccess()) return;

        try {
            update_user_meta(intval($_COOKIE['warehouse_id']), 'active_order', []);
            $order_ids = $_POST['order_id'];
            $warehouse = new Warehouse();
            $warehouse->setOrdersSessionExpired($order_ids);
            wp_send_json_success(['success' => true]);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function getOrdersInQueue()
    {
        if (!self::validateWarehouseUserAccess()) return;
        
        try {
            $warehouse = new Warehouse();
            $orders_left = $warehouse->getOrderCount();
            wp_send_json_success(['count' => $orders_left]);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

    }

    public static function sendPrintRequest()
    {
        if (!self::validateWarehouseUserAccess()) return;

        $order_id = $_POST['order_id'];
        $printer_barcode = $_POST['printer_barcode'];
      
        wp_send_json_success();
    }

    public static function completeOrder()
    {
        if (!self::validateWarehouseUserAccess()) return;

        $order_id = $_POST['order_id'];
        
        wp_send_json_success();
    }

    
    // POWER PACK
    public static function getPackingList()
    {
        if (!self::validateWarehouseUserAccess()){
            return;
        }

        $packing_barcode = $_POST['packing_barcode'];
        
        $dbh = SCModel::getSnackCrateDB();

        $stmt = $dbh->prepare("SELECT candybar_order.*, Address.shipping_name AS name, Address.address_1, Address.address_2,
                                      Address.city, Address.state, Address.country, Address.zipcode  
                               FROM candybar_order 
                               LEFT JOIN Address ON Address.id = candybar_order.shipping_address
                               WHERE barcode_reference = :barcode_reference");

        $stmt->bindParam(":barcode_reference", $packing_barcode);
        $stmt->execute();

        $order = null;

        while($o = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(!$order){
                $order = $o;
                $order['order_ids'] = [$order['id']];
                continue;
            }

            $order['order_ids'][] = $o['id'];
        }
        

        if(!$order){
            wp_send_json_error([
                'title' => 'Shipment error',
                'text' => 'Shipment does not exist.',
            ], 406);

            return;
        }

        if($order['status'] == 'fulfilled'){
            wp_send_json_error([
                'title' => 'Shipment error',
                'text' => 'This shipment has already been completed.',
            ], 406);

            return;
        }

        if(!$order['shipment_id'] && !$order['shipment_error']){
            wp_send_json_error([
                'title' => 'Shipment error',
                'text' => 'This shipment has not completed purchasing. Please wait a few minutes and try again.',
            ], 406);

            return;
        }

        if(!$order['shipment_id'] && $order['shipment_error']){
            wp_send_json_error([
                'title' => 'Shipment error',
                'text' => $order['shipment_error'],
            ], 406);

            return;
        }

        $shipmentId = $order['shipment_id'];

        $stmt = null;

        // If order has shipment_id, use easypost api to retreive label url
        $easyPostHelper = new EasyPostHelper();

        try {
            $label = $easyPostHelper->getCurrentLabel($shipmentId);
        } catch(\Exception $e){
            wp_send_json_error([
                'title' => 'Label retrieval error.',
                'text' => 'Label retrieval failed, try again later',
            ], 406);

            return;
        }

        if(!$label){
            wp_send_json_error([
                'title' => 'Label missing.',
                'text' => 'Label for matched shipment is missing',
            ], 406);

            return;
        }

        $order['label_url'] = $label;

        wp_send_json_success($order);
    }

    public static function printLabel()
    {
        if (!self::validateWarehouseUserAccess()){
            return;
        }

        $zp = new ZebraPrint();

        $response = $zp->sendFileToPrinter($_POST['printerId'], $_POST['fileUrl']);

        wp_send_json($response);
    }
    
    public static function sendPrintPackRequest()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles) && !in_array('administrator', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $order_id = $_POST['order_id'];
        
        wp_send_json_success();
    }
    
    public static function completePackOrder()
    {
        if (!self::validateWarehouseUserAccess()) return;
        
        $order_ids = $_POST['order_ids'];
        $ids = explode(',', $order_ids);
        
        $dbh = SCModel::getSnackCrateDB();

        $stmt = $dbh->prepare("UPDATE `candybar_order` SET status = 'fulfilled' WHERE id in (:" . implode(',:', array_keys($ids)) . ")");
        foreach ($ids as $k => $id) {
            $stmt->bindValue(":". $k, $id);
        }
        $stmt->execute();
        $stmt = null;
        
        wp_send_json_success();
    }


    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new WarehouseAjax();
        }

        return self::$instance;
    }
}

WarehouseAjax::getInstance();
