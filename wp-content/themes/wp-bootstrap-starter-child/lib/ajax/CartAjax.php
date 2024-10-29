<?php

require_once('SCAjax.php');

class CartAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'snack';
    protected $cart;

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'add_to_cart' => 'addToCart',
                'remove_from_cart' => 'removeFromCart',
                'sc_update_cart' => 'cartDirectUpdate',
                'add_to_cart_crate' => 'addToCartCrate',
                'remove_from_cart_crate' => 'removeFromCartCrate'
            ]
        );
    }


    public static function addToCart()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $cart = new Cart();
            $cart->addToCart($data['snack_id'], $data['qty']);
    
            $send_data = array(
                "callback" => "cartSuccess",
                "callbackArguments" => array(
                    array(
                        'snack_id' => $data['snack_id'],
                        'snack' => get_post_meta($data['snack_id'], 'user-friendly-name', true),
                        'crate_size' => null,
                        'quantity' => $data['qty'],
                        'cart' => json_encode( $_SESSION['cart'] ),
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e, 500);
        }
    
        wp_die();
    }
    
    
    public static function removeFromCart()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $snack_id = $data['snack_id'];

            $quantity = $_SESSION['cart'][$snack_id];

            $cart = new Cart();
            $cart->removeFromCart($snack_id);
    
            $send_data = array(
                "callback" => "cartRemovalSuccess",
                "callbackArguments" => array(
                    array(
                        'snack' => get_post_meta($snack_id, 'user-friendly-name', true),
                        'quantity' => $quantity,
                        'cart' => json_encode( $_SESSION['cart'] ),
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    
        wp_die();
    }
    
    
    public static function addToCartCrate()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            
            $country_id = $data['crate_country']; 
            $itemQuantity = $data['itemQuantity'];
            $crate_type = ( $data['drink_addon'] == 1 ) ? $data['crate_type'] . 'W' : $data['crate_type'];

            $cart = new Cart();
            $cart->addCrateToCart($country_id, $itemQuantity, $crate_type);

            $send_data = array(
                "callback" => "cartSuccess",
                "callbackArguments" => array(
                    array(
                        'snack_id' => $country_id,
                        'snack' => get_the_title($country_id) . ' ' . $crate_type,
                        'crate_size' => $crate_type,
                        'quantity' => $itemQuantity,
                        'cart' => json_encode( $_SESSION['cart'] ),
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }


    public static function removeFromCartCrate()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $country_id = $data['country_id'];
            $crate_size = $data['crate_size'];

            
            $quantity = $_SESSION['cart'][$country_id][$crate_size];
            
            $cart = new Cart();
            $cart->removeFromCartCrate($country_id, $crate_size);

            $send_data = array(
                "callback" => "cartRemovalSuccess",
                "callbackArguments" => array(
                    array(
                        'snack' => get_the_title($country_id) .' '.$crate_size,
                        'quantity' => $quantity,
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }
    
    public static function cartDirectUpdate()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            
            $cart_data = json_decode( stripslashes( $_POST['cart'] ), true );

            $cart = new Cart();
            $cart->directUpdate( $cart_data );

            $send_data = array(
                "callback" => "cartDirectUpdateSuccess",
                "callbackArguments" => array(
                    array(
                        'cart' => json_encode( $_SESSION['cart'] ),
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch( Exception $e )
        {
            wp_send_json_error($e, 500);
        }
    
        wp_die();
    }


    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new CartAjax();
        }

        return self::$instance;
    }
}

CartAjax::getInstance();