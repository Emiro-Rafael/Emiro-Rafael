<?php

class Cart extends General
{
    private $items;
    private $user_data;
    private $idempotency_token;

    private static $sold_counter_meta_key = 'times_sold';
    private static $confirmation_template_id = '0c2c9831-c080-4a11-b370-635630e03142';

    private static $free_shipping_minimum = 35;

    public function __construct()
    {
        if( !headers_sent() && session_status() === PHP_SESSION_NONE )
        {
            session_start();
        }

        parent::__construct();

        if(empty($_SESSION['cart']))
        {
            $_SESSION['cart'] = array();
        }

        $this->setItems();
        $this->_setIdempotencyToken();

        global $user_data;
        $this->user_data = $user_data;
    }

    private function _setIdempotencyToken()
    {
        $this->idempotency_token = hash( 'adler32', json_encode($this->items) );
    }

    private function _checkIdempotency()
    {
        if (empty($_SESSION['idempotency_tokens'])) return true;

        $tokens_to_check = array_filter(
            $_SESSION['idempotency_tokens'],
            function($token)
            {
                return time() < $token->expires;
            }
        );

        foreach($tokens_to_check as $token)
        {
            if($this->idempotency_token == $token->token)
            {
                return false;
            }
        }

        return true;
    }

    private function _removeIdempotencyToken()
    {
        foreach($_SESSION['idempotency_tokens'] as $key => $token )
        {
            if( $token->token == $this->idempotency_token )
            {
                unset($_SESSION['idempotency_tokens'][$key]);
            }
        }
    }

    private function _storeIdempotencyToken()
    {
        if( empty($_SESSION['idempotency_tokens']) )
        {
            $_SESSION['idempotency_tokens'] = array();
        }

        $token_data = new stdClass();

        $token_data->token = $this->idempotency_token;
        $token_data->expires = time() + 5*60;

        array_push($_SESSION['idempotency_tokens'], $token_data);
    }

    public function checkForLogout()
    {
        if( empty($this->user_data) )
        {
            echo '<script> window.location.href = "' . get_permalink( get_page_by_path( 'checkout/checkout-guest' ) ) . '?expired=1"; </script>';
        }
    }

    private function setItems()
    {
        $this->items = $_SESSION['cart'];
    }

    private function slack($message, $channel)
    {
        $ch = curl_init("https://slack.com/api/chat.postMessage");
        $data = http_build_query([
            "token" => $_ENV['slack_api_token'],
            "channel" => $channel, 
            "text" => $message,
            "username" => "InventreeBot",
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }

    public function addCrateToCart($post_id, $quantity, $size)
    {
        if( empty($_SESSION['cart'][$post_id][$size]) )
        {
            $_SESSION['cart'][$post_id][$size] = $quantity;
        }
        else
        {
            $_SESSION['cart'][$post_id][$size] += $quantity;
        }

        $this->session_handler->handleCartSessionUpdate();
    }

    public function directUpdate($data)
    {
        $_SESSION['cart'] = $data;

        $this->session_handler->handleCartSessionUpdate();
    }

    public function addToCart($post_id, $quantity)
    {
        if(array_key_exists($post_id, $_SESSION['cart']))
        {
            $_SESSION['cart'][$post_id] += $quantity;
        }
        else
        {
            $_SESSION['cart'][$post_id] = $quantity;
        }

        $this->session_handler->handleCartSessionUpdate();
    }

    public function removeFromCart($post_id)
    {
        unset($_SESSION['cart'][$post_id]);
        $this->session_handler->handleCartSessionUpdate();
    }

    public function removeFromCartCrate($post_id, $crate_size)
    {
        unset($_SESSION['cart'][$post_id][$crate_size]);

        if(count($_SESSION['cart'][$post_id]) == 0)
        {
            unset($_SESSION['cart'][$post_id]);
        }
        $this->session_handler->handleCartSessionUpdate();
    }

    public function getCartItems()
    {
        return $this->items;
    }

    public static function getCartNumber()
    {
        $cart_counter = 0;
        if(array_key_exists('cart', $_SESSION))
        {
            foreach($_SESSION['cart'] as $post_id => $cart_item)
            {
                if(empty($cart_item))
                {
                    continue;
                }

                switch(get_post_type($post_id))
                {
                    case 'snack':
                        $cart_counter += $cart_item;
                        break;
                    
                    case 'country':
                    case 'collection':
                        foreach($cart_item as $crate_count)
                        {
                            $cart_counter += $crate_count;
                        }
                        break;

                    default:
                        $cart_counter += $cart_item;
                }
            }
        }
        return $cart_counter;
    }

    private function updateItemsSoldCounter()
    {
        foreach($this->items as $post_id => $quantity)
        {
            if( in_array( get_post_type($post_id), array('snack') ) )
            {
                if($times_sold = get_post_meta($post_id, self::$sold_counter_meta_key, true))
                {
                    update_post_meta($post_id, self::$sold_counter_meta_key, $quantity + (int)$times_sold);
                }
                else
                {
                    add_post_meta($post_id, self::$sold_counter_meta_key, $quantity, true);
                }
            }
        }
    }

    private function _addToMainOrders($payment_id, $user_id, $customer_id, $cb_order_id = null)
    {
        $date = date( 'Y-m-d H:i:s' );
        $estimated_delivery = date( 'm-d-y', strtotime('+7 days') );
        $shipping_name = $_SESSION['checkout']['user_info']['first_name'] . ' ' . $_SESSION['checkout']['user_info']['last_name'];
        $country = $_SESSION['checkout']['address']['shipping']['country'] ?? 'United States of America';
        
        if( !$this->dbh->inTransaction() )
            $this->dbh->beginTransaction();

        foreach( $this->items as $post_id => $box_details )
        {
            $first_country = get_post_meta( $post_id, 'fulfillment-name', true );
            if( empty($first_country) )
            {
                $first_country = get_the_title( $post_id );
            }

            $country_model = new CountryModel($post_id);

            foreach( $box_details as $crate_size => $quantity )
            {
                $total = $country_model->getPrice($crate_size);
                $total = round( $total * 100 , 0 );
                
                for( $i = 0; $i < $quantity; $i++ )
                {
                    $stmt = $this->dbh->prepare("INSERT INTO " . self::$main_order_table . " 
                        (`Email`, `Shipping_Name`, `Plan`, `Address`, `suite`, `City`, `State`, `Zip`, `Country`, `First_Country`, `customer_ID`, `Order_Date`, `Address_Verified`, `Signature`, `Payment_ID`, `is_mystery`)
                        VALUES (:email, :shipping_name, :plan, :address, :suite, :city, :state, :zip, :country, :first_country, :customer_id, :order_date, 'NO', '', :payment_id, 0)");
                    $stmt->bindParam(":email", $_SESSION['checkout']['user_info']['email']);
                    $stmt->bindParam(":shipping_name", $shipping_name);
                    $stmt->bindParam(":plan", $crate_size);
                    $stmt->bindParam(":address", $_SESSION['checkout']['address']['shipping']['address_1']);
                    $stmt->bindParam(":suite", $_SESSION['checkout']['address']['shipping']['address_2']);
                    $stmt->bindParam(":city", $_SESSION['checkout']['address']['shipping']['city']);
                    $stmt->bindParam(":state", $_SESSION['checkout']['address']['shipping']['state']);
                    $stmt->bindParam(":zip", $_SESSION['checkout']['address']['shipping']['zip']);
                    $stmt->bindParam(":country", $country);
                    $stmt->bindParam(":first_country", $first_country);
                    $stmt->bindParam(":customer_id", $customer_id);
                    $stmt->bindParam(":order_date", $date);
                    $stmt->bindParam(":payment_id", $payment_id);
                    $stmt->execute();
                    $stmt = null;

                    $subscription_id = 'CandyBar Order #' . $cb_order_id;
                    $stmt = $this->dbh->prepare("INSERT INTO " . self::$order_history_table . "
                         (`CustomerID`, `OrderDate`, `Status`, `Price`, `estdeliverydate`, `trackingcode`, `email`, `Shipping_Name`, `Plan`, `Address`, `suite`, `City`, `State`, `Zip`, `Country`, `First_Country`, `Order_Date`, `Address_Verified`, `Signature`, `Payment_ID`, `subscription_id`, `is_mystery`)
                         VALUES (:CustomerID, :order_date, 'Received', :Price, :estdeliverydate, '', :email, :Shipping_Name, :Plan, :Address, :suite, :City, :State, :Zip, :Country, :First_Country, :order_date2, 'NO', '', :Payment_ID, :subscription_id, 0)");
                    $stmt->bindParam(':CustomerID', $customer_id);
                    $stmt->bindParam(':Price', $total);
                    $stmt->bindParam(':estdeliverydate', $estimated_delivery);
                    $stmt->bindParam(':email', $_SESSION['checkout']['user_info']['email']);
                    $stmt->bindParam(':Shipping_Name', $shipping_name);
                    $stmt->bindParam(':Plan', $crate_size);
                    $stmt->bindParam(':Address', $_SESSION['checkout']['address']['shipping']['address_1']);
                    $stmt->bindParam(':suite', $_SESSION['checkout']['address']['shipping']['address_2']);
                    $stmt->bindParam(':City', $_SESSION['checkout']['address']['shipping']['city']);
                    $stmt->bindParam(':State', $_SESSION['checkout']['address']['shipping']['state']);
                    $stmt->bindParam(':Zip', $_SESSION['checkout']['address']['shipping']['zip']);
                    $stmt->bindParam(':Country', $country);
                    $stmt->bindParam(':First_Country', $first_country);
                    $stmt->bindParam(':Payment_ID', $payment_id);
                    $stmt->bindParam(':subscription_id', $subscription_id);
                    $stmt->bindParam(":order_date", $date);
                    $stmt->bindParam(":order_date2", $date);
                    $stmt->execute();
                    $stmt = null;
                }
            }
        }
        
        return null;
    }

    // @TODO TAX STUFF
    private function _addOrderToDb($payment_id, $user_id = null, $customer_id = null)
    {
        $post_arr = array(
            'post_title' => 'CandyBar Order - ' . date('F d, Y') . ' @ ' . date('H:i:s'),
            'post_content' => '',
            'post_excerpt' => '',
            'post_status' => 'in_progress',
            'post_type' => 'order'
        );
        $new_post_id = wp_insert_post($post_arr, true);

        if( is_wp_error($new_post_id) )
        {
            throw new \Exception($new_post_id->get_error_message());
        }

        update_post_meta($new_post_id, 'items', $this->items); // add post meta with items in serialized array

        $purchased = serialize($this->items);
        $is_guest = substr($user_id, 0, 1) === 'g' ? 1 : 0;
        $cost = $this->getTotal();

        if( in_array( $_SESSION['checkout']['address']['shipping']['state'], array('FL', 'Florida') ) )
        {
            $tax = new Tax();
            $tax->setZipcode( $_SESSION['checkout']['address']['shipping']['zip'] );

            $tax_data = $tax->requestTaxRate();

            $tax_percentage = $tax_data->results[0]->taxSales;
            $tax_county = $tax_data->results[0]->geoCounty;

            $tax = round( $cost * $tax_percentage , 2 );
        }
        else 
        {
            $tax_percentage = NULL;
            $tax_county = NULL;
            $tax = 0;
        }
        

        $shipping_address_id = Address::addAddress( $_SESSION['checkout']['address']['shipping'], $customer_id, $_SESSION['checkout']['address']['shipping']['phone'] );

        $billing_address_id = null;
        if( !empty($_SESSION['checkout']['address']['billing']) )
        {
            $billing_address_id = Address::addAddress( $_SESSION['checkout']['address']['billing'], $customer_id, $_SESSION['checkout']['address']['billing']['phone'] );
        }

        $post_types = array_map(
            function($post_id)
            {
                return get_post_type( $post_id );
            },
            array_keys($this->items)
        );

        $in_main_table = 0;//( !in_array('snack', $post_types) && self::getCartNumber() == 1 ) ? 1 : 0;


        $check_preorder = self::checkPreorderStatus( array_keys($this->items) );
        if( $check_preorder )
        {
            $preorder_date = date('Y-m-d', $check_preorder);
        }
        else
        {
            $preorder_date = null;
        }

        $sales_order_reference = $this->_addSalesOrder($customer_id);

        if( !$this->dbh->inTransaction() )
            $this->dbh->beginTransaction();

        $subTotalCost = round($this->getSubTotal(), 2);        
        $shippingCost = round($this->getShippingTotal(), 2);

        $stmt = $this->dbh->prepare("INSERT INTO " . self::$candybar_order_table . " (user_id, purchased, cost, subtotal, shipping_cost, tax, is_guest, shipping_address, billing_address, status, payment_id, in_main_table, tax_county, tax_percentage, preorder_date, sales_order_reference)
                                    VALUES (:user_id, :purchased, :cost, :subtotal, :shipping_cost, :tax, :is_guest, :shipping_address, :billing_address, 'processing', :payment_id, :in_main_table, :tax_county, :tax_percentage, :preorder_date, :sales_order_reference)");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":purchased", $purchased);

        $stmt->bindParam(":cost", $cost);
        $stmt->bindParam(":subtotal", $subTotalCost);
        $stmt->bindParam(":shipping_cost", $shippingCost);

        $stmt->bindParam(":tax", $tax);
        $stmt->bindParam(":is_guest", $is_guest);
        $stmt->bindParam(":shipping_address", $shipping_address_id);
        $stmt->bindParam(":billing_address", $billing_address_id);
        $stmt->bindParam(":payment_id", $payment_id);
        $stmt->bindParam(":in_main_table", $in_main_table);
        $stmt->bindParam(":tax_county", $tax_county);
        $stmt->bindParam(":tax_percentage", $tax_percentage);
        $stmt->bindParam(":preorder_date", $preorder_date);
        $stmt->bindParam(":sales_order_reference", $sales_order_reference);
        $stmt->execute();
        $cb_order_id = $this->dbh->lastInsertId();

        $this->_addCandybarOrderItems($cb_order_id);

        return array(
            'wp_post_id' => $new_post_id,
            'cb_order_id' => $cb_order_id
        );
    }

    private function _addLineItemToSalesOrder($sales_order_id, $part_id, $price, $quantity) {
        $inventree = Inventree::getInstance();

        $line_item_data = array(
            'quantity' => $quantity,
            'reference' => '',
            'notes' => '',
            'overdue' => false,
            'part' => $part_id,
            'order' => $sales_order_id,
            'sale_price' => $price,
            'sale_price_currency' => 'USD',
            'target_date' => date('Y-m-d'),
            'link' => ''
        );
        
        $line_item = $inventree -> addLineItemToSalesOrder($line_item_data);

        return $line_item;
        
    }
    private function _addSalesOrder($customer_id)
    {
        $inventree = Inventree::getInstance();
        $name = $_SESSION['checkout']['user_info']['first_name'] . ' ' . $_SESSION['checkout']['user_info']['last_name'];
        $email = $_SESSION['checkout']['user_info']['email'];
        $customer = $inventree->getCompany($name, $email);

        if($customer == null) {
            //Create Customer on tree
            $customer_data = array(
                'name' => $name,
                'description' => $customer_id,
                'website' => '',
                'phone' => '',
                'address' => null,
                'email' => $email,
                'currency' => 'USD',
                'contact' => '',
                'link' => '',
                'image' => null,
                'active' => true,
                'is_customer' => true,
                'is_manufacturer' => false,
                'is_supplier' => false,
                'notes' => null,
                'address_count' => 0,
                'primary_address' => null
            );

            $customer = $inventree->createCompany($customer_data);
        }

        //Create Sales Order     

        $sales_order_data = array(
            'creation_date' => date('Y-m-d'),
            'target_date' => date('Y-m-d'),
            'description' => '',
            'line_items' => 0,
            'completed_lines' => 0,
            'link' => '',
            'project_code' => null,
            'project_code_detail' => null,
            'responsible' => null,
            'responsible_detail' => null,
            'contact' => null,
            'contact_detail' => null,
            'address' => null,
            'address_detail' => null,
            'status' => 10,
            'status_text' => 'Pending',
            'notes' => null,
            'barcode_hash' => '',
            'overdue' => false,
            'customer' => $customer->pk,
            'customer_reference' => '',
            'shipment_date' => null,
            'total_price' => '0',
            'order_currency' => 'USD'
        );
        $sales_order = $inventree -> createSalesOrder($sales_order_data);
        $sales_order_id = $sales_order->pk;
        $sales_order_reference = $sales_order->reference;

        foreach($this->items as $post_id => $item) {

            $post_type = get_post_type($post_id);
            switch( $post_type )
            {
                case "snack":
                    $quantity = $item;
                    $price = self::getItemTotal( $post_id, 1 );
                    
                    $internal_id_code = get_post_meta( $post_id, 'internal-id-code', true );
                    $inventree_part = $inventree -> getInventreePartByIPN($internal_id_code);
                    
                    if($internal_id_code == '') {
                        $message_to_slack = "We can't get internal-id-code for snack item.\nItemId - $post_id\nSalesOrderReference - $sales_order_reference";
                        $this->slack($message_to_slack, '#inventree_report');
                    }

                    $current_stock = get_post_meta( $post_id, 'stock', true );
                    update_post_meta($post_id, 'stock', $current_stock - $quantity);
                    
                    if($inventree_part != NULL) {
                        $part_id = $inventree_part->pk;
                        $this->_addLineItemToSalesOrder($sales_order_id, $part_id, $price, $quantity);
                    }

                    break;
                case "country":
                    foreach($item as $size => $quantity)
                    {
                        $current_stock = get_post_meta( $post_id, 'stock', true );
                        update_post_meta($post_id, 'stock', $current_stock - $quantity);

                        $price = self::getItemTotal( $post_id, 1, $size );

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

                        if($internal_id_code == '') {
                            $message_to_slack = "We can't get internal-id-code for country item.\nItemId - $post_id\nsize - $size\nSalesOrderReference - $sales_order_reference";
                            $this->slack($message_to_slack, '#inventree_report');
                        }

                        $inventree_part = $inventree -> getInventreePartByIPN($internal_id_code);

                        if($inventree_part != NULL) {
                            $part_id = $inventree_part->pk;
                            $this->_addLineItemToSalesOrder($sales_order_id, $part_id, $price, $quantity);
                        }
                    }
                    break;

                case "collection":
                    foreach($item as $size => $quantity)
                    {
                        $current_stock = get_post_meta( $post_id, 'stock', true );
                        update_post_meta($post_id, 'stock', $current_stock - $quantity);

                        $price = self::getItemTotal( $post_id, 1, $size );

                        $internal_id_code = get_post_meta( $post_id, 'internal-id-code', true );
                        $inventree_part = $inventree -> getInventreePartByIPN($internal_id_code);

                        if($internal_id_code == '') {
                            $message_to_slack = "We can't get internal-id-code for collection item.\nItemId - $post_id\nSalesOrderReference - $sales_order_reference";
                            $this->slack($message_to_slack, '#inventree_report');
                        }

                        if($inventree_part != NULL) {
                            $part_id = $inventree_part->pk;
                            $this->_addLineItemToSalesOrder($sales_order_id, $part_id, $price, $quantity);
                        }
                    }
                    break;
            }   
        }

        $inventree->issueOrder($sales_order_id);
        return $sales_order_reference;
    }

    private function _addCandybarOrderItems($order_id)
    {
        if( !$this->dbh->inTransaction() )
            $this->dbh->beginTransaction();

        foreach($this->items as $post_id => $item)
        {
            switch( get_post_type($post_id) )
            {
                case "snack":
                    $item_name = get_the_title( $post_id );
                    $quantity = $item;
                    $price = self::getItemTotal( $post_id, 1 );
                    $stmt = $this->dbh->prepare("INSERT INTO `". self::$candybar_order_item_table . "` (order_id, item_id, `name`, single_item_price, quantity)
                                        VALUES (:order_id, :item_id, :item_name, :price, :quantity)");
                    $stmt->bindParam(":order_id", $order_id);
                    $stmt->bindParam(":item_id", $post_id);
                    $stmt->bindParam(":item_name", $item_name);
                    $stmt->bindParam(":price", $price);
                    $stmt->bindParam(":quantity", $quantity);
                    $stmt->execute();
                    $stmt = null;
                    break;
                case "country":
                case "collection":
                    foreach($item as $size => $quantity)
                    {
                        $item_name = get_the_title( $post_id ) . ' ' . $size;
                        $price = self::getItemTotal( $post_id, 1, $size );

                        $stmt = $this->dbh->prepare("INSERT INTO `". self::$candybar_order_item_table . "` (order_id, item_id, `name`, single_item_price, quantity)
                                        VALUES (:order_id, :item_id, :item_name, :price, :quantity)");
                        $stmt->bindParam(":order_id", $order_id);
                        $stmt->bindParam(":item_id", $post_id);
                        $stmt->bindParam(":item_name", $item_name);
                        $stmt->bindParam(":price", $price);
                        $stmt->bindParam(":quantity", $quantity);
                        $stmt->execute();
                        $stmt = null;
                    }
                    break;
            }
        }
    }

    private function _gatherKlaviyoEventItems()
    {
        $event_items = array();
        $count = 0;
        foreach($this->items as $post_id => $post)
        {
            switch( get_post_type($post_id) )
            {
                case 'snack':
                    $snack_model = new SnackModel($post_id);
                    $quantity = $post;
                    $item = array(
                        'name' => get_the_title( $post_id ),
                        'image' => $snack_model->getThumbnail('medium'),
                        'code' => get_post_meta( $post_id, 'internal-id-code', true ),
                        'price' => number_format(self::getItemTotal( $post_id, $quantity ),2),
                        'quantity' => $quantity
                    );

                    array_push($event_items, $item);
                    $count++;
                    break;

                case 'country':
                    $country_model = new CountryModel( $post_id );

                    foreach($post as $crate_size => $quantity)
                    {
                        $item = array(
                            'name' => get_the_title( $post_id ) . ' ' . CountryModel::$pretty_names[$crate_size],
                            'image' => $country_model->getFeaturedImage(),
                            'code' => get_post_meta( $post_id, 'country-code', true ) . $crate_size,
                            'price' => number_format(self::getItemTotal( $post_id, $quantity, $crate_size ),2),
                            'quantity' => $quantity
                        );
                        array_push($event_items, $item);
                        $count++;

                        if($count >= 3) break;
                    }

                    break;

                case 'collection':

                    $collection_model = new CollectionModel( $post_id );

                    foreach($post as $crate_size => $quantity)
                    {
                        $item = array(
                            'name' => get_the_title( $post_id ),
                            'image' => $collection_model->getFeaturedImage(),
                            'code' => get_post_meta( $post_id, 'country-code', true ) . $crate_size,
                            'price' => number_format(self::getItemTotal( $post_id, $quantity, $crate_size ),2),
                            'quantity' => $quantity
                        );
                        array_push($event_items, $item);
                        $count++;

                        if($count >= 3) break;
                    }

                    break;
            }

            if($count >= 3) break;
        }

        return $event_items;
    }


    public function checkout($data, $user_id = null, $user = null, $customer_id = null)
    {
        try
        {   
            if( empty($this->items) )
            {
                throw new Exception( 'session-expired' );
            }

            if( !$this->_checkIdempotency() )
            {
                throw new Exception( 'Order already submitted.' );
            }
            else
            {
                $this->_storeIdempotencyToken();
            }
            
            // if we got here without this parameter passed, the user is already logged in, so get the user
            if( empty($user_id) )
            {
                global $user_data;
                $user_id = $user_data->id;
                $user = new User( $user_data->email );
            }
            elseif( empty($user) )
            {
                $user_email = get_userdata( $user_id )->user_email;
                $user = new User( $user_email );
            }
            else
            {
                throw new Exception( 'An error occurred while retrieving user\'s data. You may need to login again, or checkout as guest.' );
            }

            $customer_id = $customer_id ?? $user->getStripeCustomerId();
            $stripe_helper = new StripeHelper( $customer_id );
            $payment = $stripe_helper->generatePayment($this, $data['source']); // generate uncaptured payment
            $user_email = $user->getEmail();
            
            $purchase_user_meta = array(
                'order_id' => $new_post_id,
                'items' => $this->items,
            );
            $user_purchase_meta = $user->addCustomUserMeta('purchase', $purchase_user_meta);
            
            $new_post_ids = $this->_addOrderToDb($payment->id, $user_id, $customer_id);
            
            $this->updateItemsSoldCounter();

            $_SESSION['fbq_value'] = $this->getTotal();

            $this->session_handler->destroySession();
            
            $stripe_helper->capturePayment( $payment ); // no errors, capture the payment

            if( $this->dbh->inTransaction() )
                $this->dbh->commit();

            $this->_sendKlaviyoEvents( $user_email, $payment, $new_post_ids['cb_order_id'] );
        }
        catch(Exception $e)
        {   
            if( $this->dbh->inTransaction() )
                $this->dbh->rollBack();

            $error_message = $e->getMessage();
            $this->_rollbackCheckout($error_message, $user_purchase_meta, $new_post_ids);
            throw new Exception( $error_message );
        }
    }

    public function tokenCheckout( $token )
    {
        try
        {
            if( empty($this->items) )
            {
                throw new Exception( 'session-expired' );
            }
            
            if( !$this->_checkIdempotency() )
            {
                throw new Exception( 'Order already submitted.' );
            }
            else
            {
                $this->_storeIdempotencyToken();    
            }

            $email = $_SESSION['checkout']['user_info']['email'];
            $user = new User( $email );
            
            if( !empty($_SESSION['checkout']['user_info']['customer_id']) )
            {
                $customer_id = $_SESSION['checkout']['user_info']['customer_id'];
            }
            else
            {
                $customer_id = $user->createStripeCustomer();
                $_SESSION['checkout']['user_info']['customer_id'] = $customer_id;
            }
            
            $new_source_id = StripeHelper::addSource($customer_id, $token);
            
            $new_user_id = $user->addToGuests( $customer_id );
    
            $data = array
            (
                'total_amount' => $this->getTotal(),
                'token' => $token,
                'email' => $email,
            );
    
            $payment = StripeHelper::generatePaymentGuest($customer_id, $data, $this->getShippingTotal(), $new_source_id);
    
    
            $this->updateItemsSoldCounter();
    
            $new_post_ids = $this->_addOrderToDb( $payment->id, $new_user_id, $customer_id );

            $_SESSION['fbq_value'] = $this->getTotal();

            $this->session_handler->destroySession();

            StripeHelper::captureGuestPayment($payment);

            if( $this->dbh->inTransaction() )
                $this->dbh->commit();

            $this->_sendKlaviyoEvents( $email, $payment, $new_post_ids['cb_order_id'], true );
        }
        catch(Exception $e)
        {
            if( $this->dbh->inTransaction() )
                $this->dbh->rollBack();

            $error_message = $e->getMessage();
            $this->_rollbackCheckout($error_message, null, $new_post_ids);
            throw new Exception( $error_message );
        }
    }

    public static function getItemTotal($post_id, $quantity, $crate_size = null)
    {
        global $user_data;
        switch(get_post_type($post_id))
        {
            case 'snack':
                $single_item_price = get_post_meta( $post_id, 'price', true );
                $snack = new SnackModel($post_id);

                if( empty($user_data) )
                {
                    $has_subscription = 0;
                }
                else
                {
                    $has_subscription = !empty( get_user_meta( get_user_by('email', $user_data->email)->ID, 'has_subscription', true ) );
                }
                
                if( $has_subscription )
                {
                    $single_item_price = $snack->getDiscount($single_item_price);
                }
                break;

            case 'country':
                $country = new CountryModel($post_id);
                $single_item_price = $country->getPrice($crate_size);
                break;

            case 'collection':
                $collection = new CollectionModel($post_id);
                $single_item_price = $collection->getPrice();
                break;
        }

        return round($single_item_price * $quantity, 2);
    }

    public function arrangeCartItem($post_id, $item)
    {
        $cart_items = array();
        $cart_item = new stdClass();
        switch(get_post_type($post_id))
        {
            case 'snack':
                $snack = new SnackModel($post_id);
                $cart_item->name = $snack->getUserFriendlyName();
                $cart_item->quantity = $item;
                $cart_item->thumbnail = $snack->getThumbnail('large');
                $cart_item->crate_size = null;
                array_push($cart_items, $cart_item);
                break;

            case 'country':
                $country = new CountryModel($post_id);
                $cart_items = array_map(
                    function($key, $val) use ($country)
                    {
                        $cart_item = new stdClass();
                        $cart_item->name = $country->getUserFriendlyName() . ' ' . CountryModel::$pretty_names[$key];
                        $cart_item->quantity = $val;
                        $cart_item->thumbnail = $country->getFeaturedImage();
                        $cart_item->crate_size = $key;
                        return $cart_item;
                    },
                    array_keys($item), $item
                );
                break;

            case 'collection':
                $collection = new CollectionModel($post_id);
                $cart_item->thumbnail = $collection->getFeaturedImage();
                $cart_item->quantity = current($item);
                $cart_item->name = $collection->getUserFriendlyName();
                $cart_item->crate_size = null;
                array_push($cart_items, $cart_item);
                break;
        }
        return $cart_items;
    }

    public function getSubTotal()
    {
        $total = 0;
        foreach($this->items as $post_id => $item)
        {
            switch(get_post_type($post_id))
            {
                case 'snack':
                    $total += self::getItemTotal($post_id, $item);
                    break;
                case 'country':
                    foreach($item as $size => $quantity)
                    {
                        $total += self::getItemTotal($post_id, $quantity, $size);
                    }
                    break;
                case 'collection':
                    $total += self::getItemTotal($post_id, current($item));
                    break;
            }
        }

        return round($total, 2);
    }

    public function getTotal()
    {
        $total = $this->getSubTotal();
        
        $total += $this->getShippingTotal();

        $total += $this->getTaxes();

        return round($total, 2);
    }

    public static function getBillingAddress()
    {
        if( empty($_SESSION['checkout']['address']['billing']) )
        {
            return $_SESSION['checkout']['address']['shipping'];
        }
        return $_SESSION['checkout']['address']['billing'];
    }

    public function getShippingTotal()
    {
        /*
        foreach($this->items as $post_id => $qty)
        {
            if( get_post_type($post_id) !== 'snack')
            {
                return 0;
            }
        }
        */

        if($this->getSubTotal() >= self::$free_shipping_minimum)
        {
            return 0;
        }
        else
        {
            return 4.99;
        }
    }

    public function getTaxes()
    {
        //TODO: port logic over
        global $user_data;
        if(!empty($user_data))
        {
            $user = new User($user_data->email);
            
            $user->setAddressData();
            $address = $user->getAddressData();
            if( !empty($address->state) && in_array( $address->state, array('Florida', 'FL') ) )
            {
                $tax = new Tax();
                $tax->setZipcode($address->zip);
                $tax_data = $tax->requestTaxRate();
                $tax_rate = $tax_data->results[0]->taxSales;

                return round($this->getSubTotal() * $tax_rate, 2);
            }
        }

        return 0;
    }

    public function displayTaxes()
    {
        $taxes = $this->getTaxes();
        if($taxes > 0)
        {
            echo '$' . $taxes;
        }
    }

    public function displayShippingTotal()
    {
        $shipping = $this->getShippingTotal();
        if($shipping == 0)
        {
            echo 'FREE';
        }
        else
        {
            echo '$' . $shipping;
        }
    }

    public static function unsetCheckoutSession()
    {
        unset($_SESSION['checkout']);
    }

    public function updateCheckoutBilling( $billing )
    {
        $_SESSION['checkout']['address']['billing'] = $billing;
    }

    public function updateCheckoutShipping( $address_id )
    {
        $address = new Address( $address_id );
        $address_data = (array)$address->getData();
        $address_data['zip'] = $address_data['zipcode'];

        if( !in_array($address_data['country'], array("USA", "US", "United States of America", "United States", "Untied States of America") ) )
        {
            throw new Exception("Sorry, we're unable to ship CandyBar Orders to " . $address_data['country'] . " at this time.");
        }
        global $user_data;
        $this->setCheckoutSession( $user_data->email, $user_data, $address_data );
        //$_SESSION['checkout']['address']['shipping'] = $address_data;
    }

    public function setCheckoutSession($email, $data, $address, $billing = null)
    {
        self::unsetCheckoutSession();

        if( is_array($data) )
        {
            $data = (object)$data;
        }

        if( is_array($address) )
        {
            $address = (object)$address;
        }

        $_SESSION['checkout'] = array
        (
            'user_info' => array
            (
                'email' => $email,
                'first_name' => $data->firstname,
                'last_name' => $data->lastname,
            ),
            'address' => array
            (
                'shipping' => array
                (
                    'shipping_name' => $data->firstname .' '.$data->lastname,
                    'address_1' => $address->address_1,
                    'address_2' => $address->address_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'zip' => $address->zip,
                    'country' => $address->country,
                    'phone' => $address->phone,
                )
            ),
        );

        if( !empty($billing) )
        {
            if( is_array($billing) )
            {
                $billing = (object)$billing;
            }

            $_SESSION['checkout']['address']['billing'] = array
            (
                'shipping_name' => $billing->name,
                'address_1' => $billing->address_1,
                'address_2' => $billing->address_2,
                'city' => $billing->city,
                'state' => $billing->state,
                'zip' => $billing->zip,
                'country' => $billing->country,
                'phone' => $billing->phone,
            );
        }
    }

    public function getShippingProgress()
    {
        $progress = min( 100, (100 * $this->getSubTotal() / self::$free_shipping_minimum) );
        return $progress;
    }

    public function getShippingMinimum()
    {
        return self::$free_shipping_minimum;
    }
    /**
     * called when an error occurs on checkout
     * checks each parameter, if it exists, we undo it here
     * 
     * obj $payment: Stripe Charge Object
     * int $user_purchase_meta: ID of user meta for purchase
     * arr $new_post_ids: array with keys wp_post_id (wordpress post containing the purchase information) and cb_order_id (id of candybar order)
     */
    private function _rollbackCheckout($error_message, $user_purchase_meta = null, $new_post_ids = null)
    {
        try
        {
            if( !empty($user_purchase_meta) )
            {
                delete_metadata_by_mid( 'user', $user_purchase_meta );
            }
    
            if( !empty($new_post_ids) )
            {
                if( !empty($new_post_ids['wp_post_id']) )
                {
                    wp_delete_post( $new_post_ids['wp_post_id'], true );
                }
            }
    
            $_SESSION['cart'] = $this->items;

            $this->_removeIdempotencyToken();
        }
        catch(Exception $e)
        {
            throw new Exception($error_message);
        }
    }

    private function _sendKlaviyoEvents( $email, $payment, $order_number, $is_guest = false )
    {
        $address = $_SESSION['checkout']['address']['shipping'];
        $name = $address['shipping_name'];

        if( $is_guest )
        {
            if( strpos($name, ' ') === false )
            {
                $first_name = $name; 
                $last_name = ''; 
            }
            else
            {
                $first_name = substr( $name, 0, strpos($name, ' ') ); 
                $last_name = trim(substr( $name, strpos($name, ' ') )); 
            }
            
            $klaviyo_customer_properties = array(
                '$first_name' => $first_name,
                '$last_name' => $last_name
            );
        }
        else
        {
            $klaviyo_customer_properties = array();
        }

        $event_items = $this->_gatherKlaviyoEventItems();

        $subtotal = $this->getSubTotal();
        
        $total_cost = $subtotal + $this->getShippingTotal();

        $tax_amount = $this->getTaxes();

        SCKlaviyoHelper::getInstance()->sendEvent(
            'Placed Order',
            $email,
            $klaviyo_customer_properties,
            array(
                'Order Reference' => $payment->id,
                'Sales Source' => 'CandyBar',
                '$value' => $total_cost,
                'total' => "$" . number_format($total_cost,2),
                'Subtotal' => number_format($subtotal,2),
                'Tax' => number_format($tax_amount,2),
                'address' => array(
                    'name' => $name,
                    'street' => $address['address_1'] . (empty($address['address_2']) ? '' : ' ' . $address['address_2']),
                    'state' => "{$address['city']}, {$address['state']} {$address['zip']}",
                ),
                'last4' => $payment->card->last4,
                'candybar_order_id' => $order_number,
                'items' => $event_items
            )
        );

        foreach($this->items as $post_id => $item)
        {
            switch(get_post_type($post_id))
            {
                case 'snack':
                    SCKlaviyoHelper::getInstance()->sendEvent(
                        'Ordered Product',
                        $email,
                        array(),
                        array(
                            'Order Reference' => $payment->id,
                            'Sales Source' => 'CandyBar',
                            'Item Name' => get_the_title( $post_id ),
                            'quantity' => $item,
                            '$value' => self::getItemTotal($post_id, $item)
                        )
                    );
                    break;
                
                case 'country':
                    foreach($item as $size => $quantity)
                    {
                        SCKlaviyoHelper::getInstance()->sendEvent(
                            'Ordered Product',
                            $email,
                            array(),
                            array(
                                'Order Reference' => $payment->id,
                                'Sales Source' => 'CandyBar',
                                'Item Name' => get_the_title( $post_id . ' ' . $size ),
                                'quantity' => $quantity,
                                '$value' => self::getItemTotal($post_id, $quantity, $size)
                            )
                        );
                    }
                    break;

                case 'collection':
                    SCKlaviyoHelper::getInstance()->sendEvent(
                        'Ordered Product',
                        $email,
                        array(),
                        array(
                            'Order Reference' => $payment->id,
                            'Sales Source' => 'CandyBar',
                            'Item Name' => get_the_title( $post_id ),
                            'quantity' => current($item),
                            '$value' => self::getItemTotal($post_id, current($item))
                        )
                    );
                    break;
            }
        }

        SCKlaviyoHelper::getInstance()->addToList(
            $_ENV['klaviyo_list_id'],
            $email
        );
    }

    public static function checkCartItemsStock()
    {
        foreach( $_SESSION['cart'] as $post_id => $item )
        {
            switch(get_post_type($post_id))
            {
                case 'snack':
                    $snack = new SnackModel( $post_id );
                    $availability = $snack->getStock( null, true );
                    if( $item > $availability && !$snack->checkIfPreorder() )
                    {
                        $name = get_post_meta( $post_id, 'user-friendly-name', true );
                        throw new Exception("Please adjust the quantity of {$name} to no greater than {$availability}.");
                    }
                    elseif( $item < 0 )
                    {
                        $name = get_post_meta( $post_id, 'user-friendly-name', true );
                        throw new Exception("Please adjust the quantity of {$name} to be greater than or equal to 0.");
                    }
                    break;
                case 'country':
                case 'collection':
                    if(get_post_type($post_id) == 'country')
                    {
                        $model = new CountryModel( $post_id );
                    }
                    else
                    {
                        $model = new CollectionModel( $post_id );
                    }

                    foreach($item as $size => $quantity)
                    {
                        $availability = $model->getStock( $size, true );
                        if( $quantity > $availability && !$model->checkIfPreorder() )
                        {
                            $name = get_the_title( $post_id );
                            if(get_post_type($post_id) == 'country')
                            {
                                $name .= " " . CountryModel::getPrettyName($size);
                            }
                            throw new Exception("Please adjust the quantity of {$name} to no greater than {$availability}.");
                        }
                        if( $quantity < -0 )
                        {
                            $name = get_the_title( $post_id );
                            if(get_post_type($post_id) == 'country')
                            {
                                $name .= " " . CountryModel::getPrettyName($size);
                            }
                            throw new Exception("Please adjust the quantity of {$name} to be greater than or equal to 0.");
                        }
                    }
                    break;
            }
        }
    }
}