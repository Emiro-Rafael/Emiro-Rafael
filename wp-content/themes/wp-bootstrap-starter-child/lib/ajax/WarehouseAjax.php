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
                
                'get_packing_list' => 'getPackingList',
                'send_print_pack_request' => 'sendPrintPackRequest',
                'complete_pack_order' => 'completePackOrder',
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
    
    
    // POWER PICK
    public static function getOrderFromQueue()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
    
        try {
            $warehouse = new Warehouse();
            $warehouse->adjustPickerNumber();
            $items = $warehouse->getNextOrder(false);
            $customer_info = $warehouse->getCustomerInformation();

            $order = [
                'id' => $items['ids'],
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
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }

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
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }

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
       $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
       if (!$user) {
           wp_send_json_error(['error' => 'not_logged_in'], 300);
           return;
       }
       if (!in_array('warehouse', $user->roles)) {
           wp_send_json_error(['error' => 'not_allowed'], 300);
           return;
       }
        
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
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $order_id = $_POST['order_id'];
        $printer_barcode = $_POST['printer_barcode'];
      
        wp_send_json_success();
    }

    public static function completeOrder()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $order_id = $_POST['order_id'];
        
        wp_send_json_success();
    }

    
    // POWER PACK
    public static function getPackingList()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $packing_barcode = $_POST['packing_barcode'];
    
        
        
        // Find order info and box size by packing_barcode
        $order = [
            'id' => '321',
            'name' => 'Kyleasdf Roarke',
            'address_1' => '1750ff Wewatta St. Unit 1929',
            'address_2' => 'Denver hhhColorado 80202',
            
            'box_size' => 'small', // small || medium || large || xl || lil-brown || big-brown
        ];
        
        
        
        
        // If everything is good
//        update_user_meta(intval($_COOKIE['warehouse_id']), 'active_pack_order', $order);
//        wp_send_json_success($order);
        
        // If there was an error:
        wp_send_json_error([
            'title' => 'THIS IS AN ERROR.',
            'text' => 'This is the content of the error. This is the content of the error .This is the content of the error.',
        ], 406);
    }
    
    public static function sendPrintPackRequest()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $order_id = $_POST['order_id'];
        
        wp_send_json_success();
    }
    
    public static function completePackOrder()
    {
        $user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;
        if (!$user) {
            wp_send_json_error(['error' => 'not_logged_in'], 300);
            return;
        }
        if (!in_array('warehouse', $user->roles)) {
            wp_send_json_error(['error' => 'not_allowed'], 300);
            return;
        }
        $order_id = $_POST['order_id'];
        
        update_user_meta(intval($_COOKIE['warehouse_id']), 'active_pack_order', []);
        
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
