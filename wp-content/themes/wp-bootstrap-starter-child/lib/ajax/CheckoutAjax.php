<?php

require_once('SCAjax.php');

class CheckoutAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'checkout';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'checkout' => 'beginCheckout',
                'sc_checkout_guest' => 'guestCheckout',
                'sc_confirm_payment' => 'confirmPayment',
                'sc_create_account' => 'createAccount',
                'sc_final_checkout_guest' => 'guestFinalCheckout',
                'sc_update_payment_method' => 'updatePaymentMethod',
                'sc_check_guest_email' => 'checkGuestEmail',
                'member_shipping_edit' => 'memberShippingUpdate',
                'shipping_confirmed' => 'shippingConfirmed',
                'confirm_pay_shipping_edit' => 'confirmPayShippingEdit'
            ]
        );
    }

    public static function beginCheckout()
    {
        try
        {
            Cart::checkCartItemsStock();
            global $user_data;
            if( empty( $user_data->email ) )
            {
                $send_data = array(
                    "callback" => "straightRedirect",
                    "callbackArguments" => array(
                        array(
                            'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-guest' ) ),
                        )
                    )
                );
                wp_send_json_success($send_data);
            }
            else
            {
                $cart = new Cart();
                $user = new User($user_data->email);
                $data = $user->getUserData();
                $user->setAddressData();
                $address = $user->getAddressData();
    
                $cart->setCheckoutSession($user_data->email, $data, $address);
    
                $send_data = array(
                    "callback" => "straightRedirect",
                    "callbackArguments" => array(
                        array(
                            'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-confirm-shipping' ) ),
                        )
                    )
                );
                wp_send_json_success($send_data);
            }
        }
        catch(Exception $e)
        {
            $error_message = $e->getMessage();
            
            if( strpos($error_message, "Please adjust the quantity") !== false )
            {
                $error_data = array(
                    "message" => $e->getMessage(),
                    "show_message" => true
                );
                wp_send_json_error($error_data, 500);
            }
            else
            {
                wp_send_json_error($e->getMessage(), 500);
            }
        }
    
        wp_die();
    }
    
    
    public static function guestCheckout()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            //back-end check for valid form submission
            /*
            if( empty( $data['firstname']) )
            {
                throw new Exception("Please enter a first name for shipping.");
            }
            elseif( empty( $data['lastname']) )
            {
                throw new Exception("Please enter a last name for shipping.");
            }
            elseif( empty( $data['email'] ) )
            {
                throw new Exception("Please enter a valid email.");
            }
            elseif( empty( $data['address_1'] ) )
            {
                throw new Exception("Please enter a shipping address.");
            }
            elseif( empty( $data['city'] ) )
            {
                throw new Exception("Please enter shipping city.");
            }
            elseif( empty( $data['state'] ) )
            {
                throw new Exception("Please enter shipping state.");
            }
            elseif( empty( $data['zipcode'] ) )
            {
                throw new Exception("Please enter shipping zipcode.");
            }
            */

            $cart = new Cart();
    
            $email = $data['email'];
            $user_data['firstname'] = $data['firstname'];
            $user_data['lastname'] = $data['lastname'];
    
            $address = array
            (
                'name' => $data['firstname'] . ' ' . $data['lastname'],
                'address_1' => $data['address_1'],
                'address_2' => $data['address_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zip' => $data['zipcode'],
                'country' => 'United States of America',
                'phone' => $data['phone'],
            );
    
            if( empty($data['billing_same']) || $data['billing_same'] != 'on' )
            {
                $billing = array
                (
                    'name' => $data['billing_firstname'] . ' ' . $data['billing_lastname'],
                    'address_1' => $data['billing_address_1'],
                    'address_2' => $data['billing_address_2'],
                    'city' => $data['billing_city'],
                    'state' => $data['billing_state'],
                    'zip' => $data['billing_zipcode'],
                    'country' => 'United States of America',
                    'phone' => $data['billing_phone'],
                );
            }
            else
            {
                $billing = null;
            }
    
            $cart->setCheckoutSession($email, $user_data, $address, $billing);
    
            if( !empty($data['optin']) && $data['optin'] == 'on' )
            {
                $_SESSION['checkout']['user_info']['optin'] = 1;
            }
            else
            {
                $_SESSION['checkout']['user_info']['optin'] = 0;
            }
    
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-confirm-pay' ) ),
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
    
    
    public static function confirmPayment()
    {
        $data = array_map( 'esc_attr', $_POST );
    
        try
        {
            $cart = new Cart();
            
            if( !empty($data['payment_method']) )
            {
                $cart->tokenCheckout( $data['token'] );
    
                $send_data = array(
                    "callback" => "straightRedirect",
                    "callbackArguments" => array(
                        array(
                            'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-thank-you-guest' ) )
                        )
                    )
                );
                
                wp_send_json_success($send_data);
            }
            else
            {
                $cart->checkout($data);
    
                $send_data = array(
                    "callback" => "straightRedirect",
                    "callbackArguments" => array(
                        array(
                            'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-thank-you' ) )
                        )
                    )
                );
                wp_send_json_success($send_data);
            }
        }
        catch(Exception $e)
        {
            if( $e->getMessage() == 'session-expired' )
            {
                $send_data = array(
                    "callback" => "straightRedirect",
                    "callbackArguments" => array(
                        array(
                            'redirect_link' => get_permalink( get_page_by_path( 'shopping-cart' ) ) . '?session_expired=1'
                        )
                    )
                );
                wp_send_json_success($send_data);
            }

            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }
    
    
    public static function createAccount()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $user = new User( $data['email'] );
    
            $user_id = $user->addToUsers($data, $data['pwd']);
    
            // creates wp account and logs in
            $user->login(
                array(
                    'email' => $data['email'],
                    'pwd' => $data['pwd'],
                )
            );
    
            $data['password'] = $data['pwd'];
            // login through account.snackcrate
            $user->accountLogin($data);
    
            Cart::unsetCheckoutSession();
    
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        'redirect_link' => get_bloginfo('url')
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
    
    
    public static function guestFinalCheckout()
    {
        try
        {
            $cart = new Cart();
            $data = stripslashes( $_POST['data'] );
    
            $cart->guestCheckout( json_decode($data, true) );
    
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-thank-you' ) )
                    )
                )
            );
            wp_send_json_success($send_data);
            wp_die();
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
            wp_die();
        }
    }
    
    
    public static function updatePaymentMethod()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            global $user_data;
            $user = new User($user_data->email);
            
            $new_card_id = $user->updatePaymentMethod($data);
    
            $send_data = array(
                "callback" => "updatePaymentSuccess",
                "callbackArguments" => array(
                    array(
                        'message' => "Payment method updated successfully",
                        'card_id' => base64_encode($new_card_id)
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
            wp_die();
        }
    }
    
    
    public static function checkGuestEmail()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $user = new User($data['email']);
            
            $user_exists = !empty( $user->isStripeCustomer() );
            
            wp_send_json_success($user_exists);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }
    
    
    public static function memberShippingUpdate()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
    
            $data['country'] = empty($data['country']) ? 'United States of America' : $data['country'];
            $data['shipping_name'] = $data['first_name'] . ' ' . $data['last_name'];
            if( $data['address_id'] == 0 )
            {
                global $user_data;
                $id = Address::addAddress($data, $user_data->customer_ID, $data['phone'], 0);
            }
            else
            {
                $address = new Address( $data['address_id'] );
                $address->update( $data );
                $id = $data['address_id'];
            }
            
    
            $send_data = array(
                "callback" => "updateShippingSuccess",
                "callbackArguments" => array(
                    array(
                        'id' => $id,
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
    
    
    public static function shippingConfirmed()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            global $user_data;

            if( empty($data['shipping_address']) )
            {
                throw new Exception("Shipping address has not been selected.");
            }

            $cart = new Cart();
            $cart->updateCheckoutShipping( $data['shipping_address'] );
    
            if( empty($data['billing_same']) || $data['billing_same'] != 'on' )
            {
                $billing = array
                (
                    'shipping_name' => $data['billing_firstname'] . ' ' . $data['billing_lastname'],
                    'address_1' => $data['billing_address_1'],
                    'address_2' => $data['billing_address_2'],
                    'city' => $data['billing_city'],
                    'state' => $data['billing_state'],
                    'zip' => $data['billing_zipcode'],
                    'country' => 'United States of America',
                );
                $billing_id = Address::addAddress( $billing, $user_data->customer_id );
                $cart->updateCheckoutBilling( $billing );
            }

            if( !empty($data['optin']) && $data['optin'] == 'on' )
            {
                $user = new User($user_data->email);
                $user->marketingOptIn(1);
            }
    
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        'redirect_link' => get_permalink( get_page_by_path( 'checkout/checkout-confirm-pay' ) ),
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

    public static function confirmPayShippingEdit()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $key = $data['address_id'];
            $_SESSION['checkout']['address'][$key] = array
            (
                'shipping_name' => $data['first_name'] .' '.$data['last_name'],
                'address_1' => $data['address_1'],
                'address_2' => $data['address_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zip' => $data['zip'],
                'country' => $data['country'] ?? 'United States of America',
            );

            $send_data = array(
                "callback" => "straightReload",
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new CheckoutAjax();
        }

        return self::$instance;
    }
}

CheckoutAjax::getInstance();