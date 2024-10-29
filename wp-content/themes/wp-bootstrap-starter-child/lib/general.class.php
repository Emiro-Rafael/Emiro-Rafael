<?php

class General
{
    protected $dbh;
    protected $stripe;
    protected $session_handler;
    
    protected static $user_table = 'Users';
    protected static $order_history_table = 'OrderHistory';
    protected static $guest_table = 'guest_user';
    protected static $candybar_order_table = 'candybar_order';
    protected static $candybar_order_item_table = 'candybar_order_item';
    protected static $address_table = 'Address';
    protected static $notification_table = 'notification';
    protected static $main_order_table = 'Orders';
    private static $session_table = 'candybar_cart_session';
    
    public function __construct()
    {
        $this->dbh = self::_getDbh();
        $this->stripe = new \Stripe\StripeClient(
            $_ENV['stripe_api_key']
        );
        $this->session_handler = Session::getInstance();
    }

    protected static function _getDbh()
    {
        return SCModel::getSnackCrateDB();
    }

    // get 7 day rolling tally of best sellers
    public static function getWeeklyBestSellers()
    {
        $dbh = self::_getDbh();

        $stmt = $dbh->prepare("SELECT purchased FROM " . self::$candybar_order_table . " WHERE order_date >= CURRENT_DATE() - INTERVAL 7 DAY");
        $stmt->execute();

        $serialized_purchases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt = null;

        $items_purchased = array();
        foreach( $serialized_purchases as $serialized_purchase )
        {
            $purchase = unserialize($serialized_purchase);

            foreach( $purchase as $post_id => $details )
            {
                if( is_array($details) )
                {
                    continue;
                }
                elseif( array_key_exists($post_id, $items_purchased) )
                {
                    $items_purchased[$post_id] += $details;
                }
                else
                {
                    $items_purchased[$post_id] = $details;
                }
            }
        }
        arsort( $items_purchased );
        
        $ranked_snack_ids = array_keys( $items_purchased );

        return $ranked_snack_ids;
    }

    // get current quantity of an item in active carts
    public static function getCurrentCartReserves( $post_id, $crate_size = null )
    {
        $dbh = self::_getDbh();
        $time = date('Y-m-d H:i:s');
        $stmt = $dbh->prepare( "SELECT items FROM " . self::$session_table . " WHERE expires > :time");
        $stmt->bindParam(":time", $time);
        $stmt->execute();

        $serialized_items_arr = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt = null;
        
        $reserved_stock = 0;
        switch( get_post_type($post_id) )
        {
            case 'snack':
                foreach($serialized_items_arr as $serialized_items)
                {
                    $items = unserialize($serialized_items);
                    if( in_array( $post_id, array_keys($items) ) )
                    {
                        $reserved_stock += $items[$post_id];
                    }
                }
                break;

            case 'country':
            case 'collection':
                foreach($serialized_items_arr as $serialized_items)
                {
                    $items = unserialize($serialized_items);
                    if( in_array( $post_id, array_keys($items) ) && in_array( $crate_size, array_keys($items[$post_id]) ) )
                    {
                        $reserved_stock += $items[$post_id][$crate_size];
                    }
                }
                break;
        }

        return $reserved_stock;
    }

    public static function checkPreorderStatus( $post_ids )
    {
        $result = false;
        foreach( $post_ids as $post_id )
        {
            $preorder_date = get_post_meta( $post_id, 'preorder-shipping-date', true );

            if( !empty($preorder_date) && strtotime($preorder_date) > time() )
            {
                if( !$result || ($result && strtotime($preorder_date) > $result) )
                {
                    $result = strtotime($preorder_date);
                }
            }
        }
        return $result;
    }
}