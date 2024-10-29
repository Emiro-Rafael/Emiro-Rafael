<?php

class Warehouse
{
    protected $dbh;
    private $easypost_helper;
    private $user_id;
    private $address;
    private $address_id;
    private $picker_number;

    private $picker_user;
    private $picker_id;
    private $logged_in_users;

    private $items;

    protected static $order_table = 'candybar_order';
    protected static $order_history_table = 'OrderHistory';
    private static $guest_user_table = 'guest_user';
    private static $user_table = 'Users';
    
    public static $box_sizes = array(
        array
        (
            'name' => 'Small',
            'slug' => 'small',
            'length' => 9.5,
            'width' => 6.5,
            'height' => 3,
            'tier' => 1,
            'bundle_group' => 1,
        ),
        array
        (
            'name' => 'Medium',
            'slug' => 'medium',
            'length' => 10.5,
            'width' => 7,
            'height' => 3.5,
            'tier' => 1,
            'bundle_group' => 1,
        ),
        array
        (
            'name' => 'Large',
            'slug' => 'large',
            'length' => 11,
            'width' => 7,
            'height' => 3.5,
            'tier' => 2,
            'bundle_group' => 1,
        ),
        array
        (
            'name' => 'Xtra Large',
            'slug' => 'x_large',
            'length' => 13,
            'width' => 9,
            'height' => 4,
            'tier' => 2,
            'bundle_group' => 1,
        ),
        array
        (
            'name' => 'Lil Brown',
            'slug' => 'lil_brown',
            'length' => 13.5,
            'width' => 9,
            'height' => 9,
            'tier' => 2,
            'bundle_group' => 1,
        ),
        array
        (
            'name' => 'Big Brown',
            'slug' => 'big_brown',
            'length' => 18,
            'width' => 13.5,
            'height' => 9,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Big Ass',
            'slug' => 'corporate',
            'length' => 23.5,
            'width' => 17.5,
            'height' => 11.5,
            'tier' => 3,
            'bundle_group' => 1,
        ),
        /*
        array
        (
            'name' => 'Small Plus Small',
            'slug' => 'smallPlusSmall',
            'length' => 9.5,
            'width' => 6.5,
            'height' => 6,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Small Plus Medium',
            'slug' => 'smallPlusMedium',
            'length' => 10.5,
            'width' => 7,
            'height' => 6.5,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Small Plus Large',
            'slug' => 'smallPlusLarge',
            'length' => 11.5,
            'width' => 7.5,
            'height' => 6.5,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Medium Plus Medium',
            'slug' => 'mediumPlusMedium',
            'length' => 10.5,
            'width' => 7,
            'height' => 6.5,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Medium Plus Large',
            'slug' => 'mediumPlusLarge',
            'length' => 11.5,
            'width' => 7.5,
            'height' => 7,
            'tier' => 3,
            'bundle_group' => 2,
        ),
        array
        (
            'name' => 'Large Plus Large',
            'slug' => 'largePlusLarge',
            'length' => 11.5,
            'width' => 7.5,
            'height' => 7.5,
            'tier' => 3,
            'bundle_group' => 2,
        ),*/
    );

    public function __construct()
    {
        $this->dbh = SCModel::getSnackCrateDB();
        $this->easypost_helper = new EasypostHelper();

        $this->logged_in_users = array();
        $this->picker_number = $this->_setPickerCount();
        $this->picker_id = $_COOKIE['warehouse_id'];
        if($this->picker_id)
            $this->picker_user = 0; // get_user_meta( $this->picker_id, 'picker_user', true );
    }

    public function adjustPickerNumber()
    {
        // return false;
        if($this->picker_user >= $this->picker_number)
        {
            $arr = array();
            foreach($this->logged_in_users as $user_id)
            {
                $number = get_user_meta($user_id, 'picker_user', true);
                array_push($arr, $number);
            }
            sort($arr);
            for($i = 0; $i < $this->picker_user; $i++)
            {
                if( !in_array($i, $arr) )
                {
                    update_user_meta($this->picker_id, 'picker_user', $i );
                    $this->picker_user = $i;
                    break;
                }
            }
        }
        return $this->picker_user;
    }

    private function _setPickerCount()
    {
        return 1;
        $args = array(
            'role' => 'warehouse',
        );
        $users = get_users( $args );
        $count = 0;
        foreach($users as $user)
        {
            $session_tokens = get_user_meta( $user->ID, 'session_tokens', true );

            if( time() < current($session_tokens)['expiration'])
            {
                $count++;
                array_push($this->logged_in_users, $user->ID);
            }
        }
        return $count;
    }

    public function getPickers()
    {
        return $this->picker_number;
    }

    private function _getModulusFactor()
    {
        return array(
            'modulus' => $this->picker_user,
            'divisor' => $this->picker_number
        );
    }

    private function _getNextCustomer($skip = 0)
    {
        if( !empty($_GET['skip']) )
        {
            $skip = $_GET['skip'];
        }

        $modulus = $this->_getModulusFactor();

        $to_skip = $skip;
        $skipped = 0;
        while( empty($this->user_id) )
        {
            $stmt = $this->dbh->prepare("SELECT `user_id`, purchased, shipping_address FROM " . self::$order_table . "
                WHERE status = 'processing'
                AND in_main_table = 0
                AND hidden = 0
                AND session_id IS NULL
                AND 
                    (
                        ( is_guest = 0 AND MOD(`user_id`, {$modulus['divisor']}) = {$modulus['modulus']} )
                        OR
                        ( is_guest = 1 AND MOD( CAST(SUBSTRING(`user_id`, 2) AS SIGNED), {$modulus['divisor']}) = {$modulus['modulus']} )
                    )
                AND (preorder_date IS NULL OR preorder_date <= CURDATE())
                GROUP BY `user_id`, shipping_address
                ORDER BY order_date ASC                
                LIMIT {$skip}, 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt = null;

            if( !$result )
            {
                break;
            }
            
            $purchased = str_replace("'", '"', $result->purchased);
            $items = unserialize($purchased);
            if( is_array($items) && !$this->_checkForPreorders( array_keys($items) ) ){

                //if($skipped >= $to_skip)
                //{
                    $this->setUserId($result->user_id);
                    $this->setAddress($result->shipping_address);
                    break;
                //}
                $skipped++;
            }
            $skip++;
        }
    }

    private function _checkForPreorders( $post_ids )
    {
        foreach( $post_ids as $post_id )
        {
            $preorder_date = get_post_meta( $post_id, 'preorder-shipping-date', true );

            if( !empty($preorder_date) && strtotime($preorder_date) > time() )
            {
                return true;
            }
        }
        return false;
    }

    private function getItemImage($post_id) {
        $image_url = '';
        switch( get_post_type( $post_id ) )
        {
            case 'snack':
                $thumbnail_id = get_post_meta($post_id, "medium-thumbnail", true); 
                if(empty($thumbnail_id))
                {
                    $thumbnail_id = get_post_meta($post_id, "_thumbnail_id", true);
                    if(empty($thumbnail_id))
                    {
                        $image_url = 'https://place-hold.it/264x264';
                    }
                    else {
                        $image_url = wp_get_attachment_url($thumbnail_id);
                    }
                }
                else {
                    $image_url = wp_get_attachment_url($thumbnail_id);
                }
                break;
            case 'country':
            case 'collection':
                $image_id = get_post_meta($post_id, "featured-image", true);
                if(empty($image_id))
                {
                    $image_url = get_stylesheet_directory_uri() . "/assets/default/MysteryCrateHero-min.png";
                    
                }
                else {
                    $image_url = wp_get_attachment_url($image_id);
                }
                break;
        }
        return $image_url;
    }

    private function getBarcodeFromPart($part) {
        if($part == NULL) {
            return "";
        }

        return preg_match('/\d{5,}/', $part->full_name, $m) ? $m[0] : '';

        // if($barcode_hash == "") {
        //     return "";
        // }
        // $full_name = $part->fill_name;
        // $full_name_arr = explode($full_name, " | ");
        // if(count($full_name_arr) >= 3) {
        //     return $full_name_arr[2];
        // }
        // return "";
    }

    private function _getCustomerItems($printable = false)
    {
        if($printable)
        {
            $stmt = $this->dbh->prepare("SELECT id, purchased, payment_id, shipment_id, order_date, customization_notes, is_addon FROM " . self::$order_table . " WHERE user_id = :user_id AND shipping_address = :shipping_address AND status = 'printable' AND in_main_table = 0 AND hidden = 0 AND (preorder_date IS NULL OR preorder_date <= CURDATE()) AND session_id IS NULL");
        }
        else
        {
            $stmt = $this->dbh->prepare("SELECT id, purchased, payment_id, shipment_id, order_date, customization_notes, is_addon FROM " . self::$order_table . " WHERE user_id = :user_id AND shipping_address = :shipping_address AND status = 'processing' AND in_main_table = 0 AND hidden = 0 AND (preorder_date IS NULL OR preorder_date <= CURDATE()) AND session_id IS NULL");
        }
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":shipping_address", $this->address_id);
        $stmt->execute();

        $inventree = Inventree::getInstance();
        $items = array();
        $ids = array();
        $payment_ids = array();
        $shipment_ids = array();
        $order_dates = array();
        $customization_notes = [];
        $total_items = 0;

        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($rows as $row)
        {
            $purchased = str_replace("'", '"', $row->purchased);
            $purchase_items = unserialize($purchased);
            if( $this->_checkForPreorders( array_keys($purchase_items) ) )
            {
                continue;
            }

            array_push($ids, $row->id);
            array_push($payment_ids, $row->payment_id);

            if(!empty($row->customization_notes)) {
                array_push($customization_notes, $row->customization_notes);
            }

            if( !empty($row->shipment_id) && !in_array($row->shipment_id, $shipment_ids) )
                array_push($shipment_ids, $row->shipment_id);

            array_push($order_dates, $row->order_date);
            
            
            foreach($purchase_items as $post_id => $details)
            {
                switch( get_post_type( $post_id ) )
                {
                    case 'snack':

                        $internal_id_code = get_post_meta( $post_id, 'internal-id-code', true );
                        $main_part = $inventree -> getInventreePartByIPN($internal_id_code);
                        
                        if($main_part != NULL) {
                            $part_id = $main_part->pk;
                            $boms = $inventree->getBOMByPartId($part_id);

                            if(empty($boms)) {
                                if( !array_key_exists( $part_id, $items ) )
                                {
                                    $total_items++;
                                    $items[$part_id] = new stdClass();
                                    $items[$part_id]->item_name = get_the_title( $post_id );
                                    if(is_array($details)) {
                                        foreach($details as $quantity)
                                        {
                                            $items[$part_id]->quantity = $quantity;
                                        }
                                    } else {
                                        $items[$part_id]->quantity = $details;
                                    }
                                    $items[$part_id]->barcode = $this->getBarcodeFromPart( $main_part);
                                    $items[$part_id]->img = $this->getItemImage($post_id);
                                }else
                                {
                                    $items[$part_id]->quantity += $details;
                                } 
                            }
                            else {
                                foreach($boms as $bom) {
                                    $sub_part_id = $bom->sub_part;
                                    $sub_part_quantity = $bom->quantity;
                                    $sub_part = $inventree->getInventreePartById($sub_part_id);
                                    $sub_part_img = $sub_part->image ? "https://ops.snackcrate.com". $sub_part->image : "";
                                    if( !array_key_exists( $sub_part_id, $items ) ) {
                                        $total_items++;
                                        $items[$sub_part_id] = new stdClass();
                                        $items[$sub_part_id]->item_name = $sub_part->name;
                                        if(is_array($details)) {
                                            foreach($details as $quantity)
                                            {
                                                $items[$sub_part_id]->quantity = $quantity * $sub_part_quantity;
                                            }
                                        } else {
                                            $items[$sub_part_id]->quantity = $details * $sub_part_quantity;
                                        }
                                        $items[$sub_part_id]->barcode = $this->getBarcodeFromPart( $sub_part);
                                        $items[$sub_part_id]->img = $sub_part_img;
                                    }
                                    else {
                                        $items[$sub_part_id]->quantity += $details * $sub_part_quantity;
                                    }
                                }
                            }    
                        }
                        break;

                    case 'country':
                        $key = "country-". $post_id;
                        foreach($details as $size => $quantity)
                        {
                            if( !array_key_exists( $key, $items ) )
                            {
                                $items[$key] = array();
                            }

                            if( !array_key_exists( $size, $items[$key] ) )
                            {
                                $total_items++;
                                $internal_id_code_key = "internal-id-code_" .$size;
                                if(get_post_meta( $post_id, $internal_id_code_key, true )) {
                                    $internal_id_code = get_post_meta( $post_id, $internal_id_code_key, true );
                                }else {
                                    $internal_id_code_others = get_post_meta( $post_id, 'internal-id-code_Others', true );
                                    if(!empty($internal_id_code_others)) {
                                        $filtered_code_items = array_filter($internal_id_code_others, function($code_item) use ($size) {
                                            return $code_item['size'] === $size;
                                        });
                
                                        $internal_id_code = !empty($filtered_code_items) ? array_values($filtered_code_items)[0]['code'] : '';
                                    }
                                }

                                $part = $inventree -> getInventreePartByIPN($internal_id_code);

                                $items[$key][$size] = new stdClass();
                                $items[$key][$size]->item_name = get_the_title( $post_id ) . ' ' . CountryModel::getPrettyName($size);
                                $items[$key][$size]->quantity = $quantity;
                                $items[$key][$size]->barcode = $this->getBarcodeFromPart( $part);
                                $items[$key][$size]->img = $this->getItemImage($post_id);
                            }
                            else
                            {
                                $items[$key][$size]->quantity += $quantity;
                            }
                        }
                        break;

                    case 'collection':
                        $internal_id_code = get_post_meta( $post_id, 'internal-id-code', true );
                        $main_part = $inventree -> getInventreePartByIPN($internal_id_code);

                        if($main_part != NULL) {
                            if(!is_array($details)) {
                                $code = get_post_meta( $post_id, 'country-code', true);

                                $part_id = $main_part->pk;
                                $boms = $inventree->getBOMByPartId($part_id);

                                if(empty($boms)) {
                                    if( !array_key_exists( $part_id, $items ) )
                                    {
                                        $total_items++;
                                        $items[$part_id] = new stdClass();
                                        $items[$part_id]->item_name = get_the_title( $post_id );
                                        $items[$part_id]->quantity = $details;
                                        $items[$part_id]->barcode =  $this->getBarcodeFromPart( $main_part);
                                        $items[$part_id]->img = $this->getItemImage($post_id);
                                        
                                    }
                                    else
                                    {
                                        $items[$part_id]->quantity += $details;
                                    }    
                                }
                                else {
                                    foreach($boms as $bom) {
                                        $sub_part_id = $bom->sub_part;
                                        $sub_part_quantity = $bom->quantity;
                                        $sub_part = $inventree->getInventreePartById($sub_part_id);
                                        $sub_part_img = $sub_part->image ? "https://ops.snackcrate.com". $sub_part->image : "";
                                        if( !array_key_exists( $sub_part_id, $items ) ) {
                                            $total_items++;
                                            $items[$sub_part_id] = new stdClass();
                                            $items[$sub_part_id]->item_name = $sub_part->name;
                                            $items[$sub_part_id]->quantity = $details * $sub_part_quantity;
                                            $items[$sub_part_id]->barcode = $this->getBarcodeFromPart( $sub_part);
                                            $items[$sub_part_id]->img = $sub_part_img;
                                        }
                                        else {
                                            $items[$sub_part_id]->quantity += $details * $sub_part_quantity;
                                        }
                                    }
                                }

                            }
                            else {

                                $part_id = $main_part->pk;
                                $boms = $inventree->getBOMByPartId($part_id);
                                if(empty($boms)) {
                                    foreach($details as $size => $quantity)
                                    {
                                        if( !array_key_exists( $part_id, $items ) )
                                        {
                                            $items[$part_id] = array();
                                        }
        
                                        if( !array_key_exists( $size, $items[$part_id] ) )
                                        {
                                            $total_items++;
                                            $items[$part_id] = new stdClass();
                                            $items[$part_id]->item_name = get_the_title( $post_id ) . ' ' . CountryModel::getPrettyName($size);
                                            $items[$part_id]->quantity = $quantity;
                                            $items[$part_id]->barcode =$this->getBarcodeFromPart( $main_part);
                                            $items[$part_id]->img = $this->getItemImage($post_id);
                                        }
                                        else
                                        {
                                            $items[$part_id]->quantity += $quantity;
                                        }
                                    }
                                }else {
                                    foreach($boms as $bom) {
                                        $sub_part_id = $bom->sub_part;
                                        $sub_part_quantity = $bom->quantity;
                                        $sub_part = $inventree->getInventreePartById($sub_part_id);
                                        $sub_part_img = $sub_part->image ? "https://ops.snackcrate.com". $sub_part->image : "";
                                        
                                        foreach($details as $size => $quantity){
                                            if( !array_key_exists( $sub_part_id, $items ) )
                                            {
                                                $items[$sub_part_id] = array();
                                            }
            
                                            if( !array_key_exists( $size, $items[$sub_part_id] ) )
                                            {
                                                $total_items++;
                                                $items[$sub_part_id] = new stdClass();
                                                $items[$sub_part_id]->item_name = $sub_part->name;
                                                $items[$sub_part_id]->quantity = $quantity * $sub_part_quantity;
                                                $items[$sub_part_id]->barcode =$this->getBarcodeFromPart( $sub_part);
                                                $items[$sub_part_id]->img = $sub_part_img;
                                            }
                                            else
                                            {
                                                $items[$sub_part_id]->quantity += $quantity * $sub_part_quantity;
                                            }
                                        }
                                    }
                                }
                                
                            }   
                        } 
                        break;
                }
            }
        
        }
        $this->items = $items;


        $order_ids = implode(',', $ids);
        $session_id = uniqid('session_', true);

        $stmt = $this->dbh->prepare("UPDATE " .self::$order_table. " SET session_id = :session_id WHERE id IN ({$order_ids})");
        $stmt->bindParam(":session_id", $session_id);
        $stmt->execute();

        return array(
            'items' => $items,
            'ids' => $order_ids,
            'payment_ids' => implode(',', $payment_ids),
            'shipment_ids' => implode(',', $shipment_ids),
            'order_dates' => implode(',', $order_dates),
            'customization_notes' => $customization_notes,
            'total_items' => $total_items
        );
    }
    
    public function setOrdersSessionExpired($order_ids) {

        if( count( explode(',', $order_ids) ) > 1 )
        {
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET session_id = NULL WHERE id IN ({$order_ids})");
        }
        else
        {
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET session_id = NULL WHERE id = :order_id");
            $stmt->bindParam(":order_id", $order_ids);
        }
        $stmt->execute();
        $stmt = null;
    }

    public function sortItemsByCountry($items)
    {
        $countries = array();
        foreach( array_keys($items) as $post_id )
        {
            $terms = get_the_terms( $post_id, 'countries' );
            $countries[$terms[0]->slug][] = $post_id;
        }

        $return_items = array();

        foreach(array_merge(...array_values($countries)) as $post_id)
        {
            $return_items[$post_id] = $items[$post_id];
        }
        
        return $return_items;
    }

    private function setAddress($id)
    {
        $this->address_id = $id;
        $this->address = new Address($id);
    }

    private function setUserId($id)
    {
        $this->user_id = $id;
    }

    public function getOrderCount()
    {
        $user_ids = [];

        $stmt = $this->dbh->prepare( "SELECT `user_id`, purchased, is_addon, hidden FROM " . self::$order_table . " WHERE in_main_table = 0 AND hidden = 0 AND status = 'processing' AND (preorder_date IS NULL OR preorder_date <= CURDATE()) AND session_id IS NULL" );

        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        foreach($orders as $order)
        {

            if( in_array( $order->user_id, $user_ids ) )
            {
                continue;
            }
            $purchased = str_replace("'", '"', $order->purchased);
            $items = unserialize($purchased);
            if( is_array($items) && !$this->_checkForPreorders( array_keys($items) ) )
            {
                array_push($user_ids, $order->user_id);
            }
        }

        return count($user_ids);
    }

    public function checkForPrintable()
    {
        $stmt = $this->dbh->prepare("SELECT id, `user_id`, purchased, payment_id, shipment_id, shipping_address FROM " . self::$order_table . " WHERE status = 'printable' AND in_main_table = 0");
        $stmt->execute();

        $printables = $stmt->fetch(PDO::FETCH_OBJ);

        if( empty($printables) )
        {
            return false;
        }
        else
        {
            $this->setUserId($printables->user_id);
            $this->setAddress($printables->shipping_address);
            return explode(',', $printables->shipment_id);
        }
    }

    private function _setNextCustomerByOrder($order_id)
    {
        $stmt = $this->dbh->prepare("SELECT `user_id`, shipping_address FROM " . self::$order_table . " WHERE id = :order_id");
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();

        $customer_data = $stmt->fetch(PDO::FETCH_OBJ);

        $this->setUserId($customer_data->user_id);
        $this->setAddress($customer_data->shipping_address);
    }

    public function getNextOrder($printable = false, $order_id = null)
    {
        if( !empty($order_id) )
        {
            $this->_setNextCustomerByOrder($order_id);
        }
        elseif( empty($this->user_id) )
        {
            $this->_getNextCustomer();
        }

        $items = $this->_getCustomerItems($printable);

        return $items;
    }

    public function getCustomerInformation()
    {
        if( empty($this->user_id) )
        {
            $this->_getNextCustomer();
        }

        if( substr($this->user_id, 0, 1) == 'g' )
        {
            $stmt = $this->dbh->prepare("SELECT email, first_name, last_name, `address`, country FROM " . self::$guest_user_table . " WHERE user_id = :user_id");
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->execute();
            
            $user_data = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt = null;
            //$address = unserialize($user_data->address);
            $address = $this->address->getData();

            $user_data->shipping_name = $user_data->first_name.' '.$user_data->last_name;
            $user_data->address_1 = $address->address_1;
            $user_data->address_2 = $address->address_2;
            $user_data->city = $address->city;
            $user_data->state = $address->state;
            $user_data->zip = $address->zipcode;
            $user_data->phone = $address->phone;
            return $user_data;
        }
        else
        {
            $stmt = $this->dbh->prepare("SELECT email FROM " . self::$user_table . " WHERE id = :user_id");
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->execute();
            $email = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;

            $user_data = $this->address->getData();
            $user_data->email = $email;
            //$user = new User($email);
            //$user->setAddressData();

            return $user_data;
        }
    }

    public function generateInvoicePDF($data, $print = true)
    {
        $ids = explode(',', $data['ids']);
        $payment_ids = explode(',', $data['payment_ids']);
        $order_dates = explode(',', $data['order_dates']);
        foreach($ids as $key => $id)
        {
            $upd = $this->dbh->prepare("SELECT customization_notes FROM " . self::$order_table . " WHERE id = :id");
            $upd->bindParam(":id", $id);
            $upd->execute();
            $customization_note = $upd->fetch(PDO::FETCH_COLUMN);
            $upd = null;

            $invoice_generator = new InvoiceGenerator($id, $data, $print, $payment_ids[$key], $order_dates[$key], $customization_note);
            $invoice_generator->generate();

            $upd = $this->dbh->prepare("UPDATE " . self::$order_table . " SET invoice_generated = CURRENT_TIMESTAMP WHERE id = :id");
            $upd->bindParam(":id", $id);
            $upd->execute();
            $upd = null;
        }
    }

    private function _checkBundles($boxes, $data)
    {
        $bundles = array();
        
        foreach($boxes as $id => $box)
        {
            $details = self::$box_sizes[$box];
            $details['weight'] = ($data['weight_lb'][$id] * 16) + $data['weight_oz'][$id];
            if( array_key_exists( $details['bundle_group'], $bundles ) )
            {
                array_push($bundles[$details['bundle_group']], $details);
            }
            else
            {
                $bundles[$details['bundle_group']] = array($details);
            }
        }
        
        $final_bundles = array();
        foreach($bundles as $group => $bundle)
        {
            if( $group == 4 ) // not bundle material
            {
                foreach( $bundle as $chunk )
                {
                    array_push( $final_bundles, array( $chunk ) );
                }
            }
            else
            {
                foreach( array_chunk($bundle, 3) as $chunk )
                {
                    array_push($final_bundles, $chunk);
                }
            }
        }
        return $final_bundles;
    }

    public function generateLabel($data)
    {
        $to_address = $this->easypost_helper->createAddress( $data );

        $order_ids = explode(',', $data['ids']);
        $shipments = array();
        $trackingcode = $tracking_number = '';
        $payment_ids = explode(',', $data['payment_ids']);
        $shipping_cost = 0;

        $bundles = $this->_checkBundles($data['boxsize'], $data);
        
        foreach($bundles as $bundle)
        {
            $widths = array_map(
                function($b)
                {
                    return $b['width'];
                },
                $bundle
            );
            $heights = array_map(
                function($b)
                {
                    return $b['height'];
                },
                $bundle
            );
            $lengths = array_map(
                function($b)
                {
                    return $b['length'];
                },
                $bundle
            );
            $weights = array_map(
                function($b)
                {
                    return $b['weight'];
                },
                $bundle
            );
            
            $details = array(
                'weight' => array_sum($weights),
                'height' => array_sum($heights),
                'length' => max($lengths),
                'width' => max($widths),
            );

            if( $details['weight'] == 0 )
            {
                throw new Exception("Cannot have a box with a weight of zero!");
            }
            
            $parcel = $details;

            $total_value = 0;
            foreach($order_ids as $id)
            {
                $stmt = $this->dbh->prepare("SELECT cost FROM " . self::$order_table . " WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $cost = $stmt->fetch(PDO::FETCH_COLUMN);
                $stmt = null;
                $total_value += $cost;
            }

            $has_cold_pack = false;
            foreach($order_ids as $id)
            {
                $stmt = $this->dbh->prepare("SELECT * FROM candybar_order_item WHERE order_id = :order_id AND item_id = 384050");
                $stmt->bindParam(":order_id", $id);
                $stmt->execute();
                $order_items = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = null;

                if(!empty($order_items)) $has_cold_pack = true;
            }

            $shipment = $this->easypost_helper->createShipment( $to_address, $parcel, $payment_ids[0], $data['number_of_items'], $total_value, $has_cold_pack );

            $shipping_cost += $shipment->selected_rate->rate;
            $trackingcode = empty($trackingcode) ? $shipment->tracker->id : $trackingcode;
            $tracking_number = empty($tracking_number) ? $shipment->tracker->tracking_code : $tracking_number;
            array_push($shipments, $shipment->id);
        }
        
        $shipment_ids = implode(',', $shipments);

        foreach($order_ids as $key => $id)
        {
            // add shipment id(s) to candy_order table AND update status of order to fulfilled
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET shipment_id = :shipment_id, status = 'printable' WHERE id = :id");
            $stmt->bindParam(":shipment_id", $shipment_ids);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $stmt = null;

            $stmt = $this->dbh->prepare("SELECT `payment_id` FROM " . self::$order_table . " WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $candybar_payment_id = $stmt->fetch(PDO::FETCH_COLUMN);

            $stmt = null;

            // add tracking information to orderhistory table 
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_history_table . " SET trackingcode = :trackingcode, trackingnumber = :tracking_number WHERE Payment_ID = :payment_id");
            $stmt->bindParam(":trackingcode", $trackingcode);
            $stmt->bindParam(":tracking_number", $tracking_number);
            $stmt->bindParam(":payment_id", $candybar_payment_id);
            $stmt->execute();
            $stmt = null;

            /*
            try
            {
                // check if we need to create Delivery Doc in SAP
                $stmt = $this->dbh->prepare("SELECT sap_invoice_id FROM " . self::$order_table . " WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $sap_invoice_id = $stmt->fetch(PDO::FETCH_COLUMN);
                $stmt = null;

                if(empty($sap_invoice_id))
                {
                    continue;
                }
                $sap = new SAPInvoice();
                $invoice_data = $sap->getFromSAP($sap_invoice_id);

                if( $invoice_data->ReserveInvoice == 'tYES' )
                {
                    $delivery_id = $sap->createDelivery( $sap_invoice_id, $shipping_cost );

                    $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET sap_delivery_id = :delivery_id WHERE id = :id");
                    $stmt->bindParam(":delivery_id", $delivery_id);
                    $stmt->bindParam(":id", $id);
                    $stmt->execute();
                    $stmt = null;
                }
            }
            catch(Exception $e)
            {

            }
            */
            

            /*
            SCKlaviyoHelper::getInstance()->sendEvent(
                'Fulfilled Order',
                $data['email'],
                array(
                    '$city' => $data['city'],
                    '$region' => $data['state'],
                    '$country' => 'United States of America',
                    '$zip' => $data['zip']
                ),
                array(
                    'payment_id' => $payment_ids[$key],
                )
            );
            */
        }
    }

    public function getPrintables()
    {
        $stmt = $this->dbh->prepare("SELECT id FROM " . self::$order_table . " WHERE `status` = 'printable' AND shipment_id IS NULL AND in_main_table = 0");
        //$stmt = $this->dbh->prepare("SELECT id FROM " . self::$order_table . " WHERE `status` = 'printable' AND shipment_id IS NULL AND in_main_table = 0 AND invoice_generated IS NOT NULL AND invoice_generated < (DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute();
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt = null;

        return $ids;
    }

    public function printLabel($data)
    {
        $shipments = explode(',', $data['shipment_ids']);
        $labels = array();
        foreach($shipments as $shipment_id)
        {
            $label = $this->easypost_helper->getLabel($shipment_id);
            array_push($labels, $label);
        }

        return $labels;
    }

    public function nextOrder($ids, $set_fulfilled = 1)
    {
        if($set_fulfilled == 1)
        {
            foreach($ids as $id)
            {
                // add shipment id(s) to candy_order table AND update status of order to fulfilled
                $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET status = 'fulfilled', ship_date = CURRENT_DATE() WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $stmt = null;
            }
        }
        $max_id = min($ids);
        $stmt = $this->dbh->prepare("SELECT id FROM " . self::$order_table . " WHERE status = 'printable' AND id > :maxid");
        $stmt->bindParam(":maxid", $max_id);
        $stmt->execute();
        $nextid = $stmt->fetch(PDO::FETCH_COLUMN);

        $stmt = null;

        return $nextid;
    }

    public function setAsPrintable( $ids )
    {
        if( count( explode(',', $ids) ) > 1 )
        {
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET status = 'printable' WHERE id IN ({$ids})");
        }
        else
        {
            $stmt = $this->dbh->prepare("UPDATE " . self::$order_table . " SET status = 'printable' WHERE id = :order_id");
            $stmt->bindParam(":order_id", $ids);
        }
        $stmt->execute();
        $stmt = null;
    }

    public function checkOrder( $order_id )
    {
        $stmt = $this->dbh->prepare("SELECT `status` FROM " . self::$order_table . " WHERE id = :order_id");
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        $status = $stmt->fetch(PDO::FETCH_COLUMN);

        $stmt = null;

        switch( $status )
        {
            case 'processing':
                throw new Exception("Order not ready for printing");
                break;
            case 'fulfilled':
                throw new Exception("Order has already been fulfilled");
                break;
            case 'canceled':
                throw new Exception("Order has been canceled");
                break;
            default:
                return true;
        }
    }

    public function getScanforms( $printed = false )
    {
        if( $printed )
        {
            $stmt = $this->dbh->prepare("SELECT * FROM candybar_scanform WHERE printed_at IS NOT NULL");
        }
        else
        {
            $stmt = $this->dbh->prepare("SELECT * FROM candybar_scanform WHERE printed_at IS NULL");
        }

        $stmt->execute();
        $scan_forms = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        return $scan_forms;
    }

    public function printScanform($id)
    {
        $stmt = $this->dbh->prepare("SELECT link FROM candybar_scanform WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $link = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        $stmt = $this->dbh->prepare("UPDATE candybar_scanform SET printed_at = NOW() WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $stmt = null;
        
        return $link;
    }
}
