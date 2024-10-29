<?php

class StripeHelper
{
    private $stripe;
    private $stripe_customer;

    public function __construct($customer_id = null)
    {
        $this->stripe = self::_getStripe();

        global $user_data;
        if( !empty($customer_id) )
        {
            $this->stripe_customer = $this->stripe->customers->retrieve(
                $customer_id,
                []
            );
        }
        elseif( !empty($user_data) && !empty( $user_data->customer_ID ) )
        {
            $this->stripe_customer = $this->stripe->customers->retrieve(
                $user_data->customer_ID,
                []
            );
        }
        else
        {
            throw new Exception( "Unable to find Customer data." );
        }
    }

    private static function _getStripe()
    {
        return new \Stripe\StripeClient($_ENV['stripe_api_key']);
    }

    private function createCustomer()
    {
        $current_user = wp_get_current_user();
        return $this->stripe->customers->create( 
            array
            (
                "email" => $current_user->user_email,
                "name" => get_user_meta( get_current_user_id(), 'full_name', true ),
            ) 
        );
    }

    public function updateSubscription( $subscription_stripe_id, $old_plan, $new_plan, $term )
    {
        $plan_map = array(
            $old_plan->end_plan => $new_plan->end_plan,
            $old_plan->one_month_plan => $new_plan->one_month_plan,
            $old_plan->six_month_plan => $new_plan->six_month_plan,
            $old_plan->twelve_month_plan => $new_plan->twelve_month_plan,
        );

        $stripe_subscription_object = $this->stripe->subscriptions->retrieve(
            $subscription_stripe_id,
            []
        );

        $subscription_item = current(
            array_filter(
                $stripe_subscription_object->items->data,
                function($item) use ($plan_map)
                {
                    return in_array( $item->price->id, array_keys($plan_map) );
                }
            )
        );

        if( !empty($subscription_item) )
        {
            $this->stripe->subscriptionItems->create([
                'subscription' => $subscription_stripe_id,
                'price' => $plan_map[$subscription_item->price->id],
                'quantity' => 1,
                "prorate" => false,
            ]);

            // delete prior subscription item
            $this->stripe->subscriptionItems->delete(
                $subscription_item->id,
                ["proration_behavior" => "none"]
            );
        }
        else
        {
            throw new Exception('Unable to find old plan');
        }
    }

    private static function _gatherChargeData($items, $stripe_amount, $shipping_amount, $customer_id, $source)
    {
        $charge_data = array
        (
            "amount" => $stripe_amount,
            "customer" => $customer_id,
            "currency" => "usd",
            "source" => $source,
            "metadata" => array(
                "shipping" => $shipping_amount
            ),
            "capture" => false
        );

        /*
        foreach( $items as $post_id => $details )
        {
            switch( get_post_type( $post_id) )
            {
                case 'snack':
                    //$charge_data['metadata']['item_price_'.$post_id] = Cart::getItemTotal($post_id, 1);
                    //$charge_data['metadata']['item_name_'.$post_id] = get_the_title( $post_id );
                    break;

                case 'country':
                case 'collection':
                    foreach($details as $crate_size => $quantity)
                    {
                        $charge_data['metadata']['item_price_'.$post_id.'_'.$crate_size] = Cart::getItemTotal($post_id, 1, $crate_size);
                        $charge_data['metadata']['item_name_'.$post_id.'_'.$crate_size] = get_the_title( $post_id ) . ' ' . $crate_size;
                    }
                    break;
            }
        }
        */

        return $charge_data;
    }

    public function generatePayment( $cart, $source )
    {
        $order_total = $cart->getSubTotal() + $cart->getShippingTotal();
        $taxes = $cart->getTaxes();
        $stripe_amount = ($order_total) * 100; // stripe only deals in pennies...

        $items = $cart->getCartItems();
        $shipping_amount = $cart->getShippingTotal();
        
        $charge_data = self::_gatherChargeData( $items, $stripe_amount, $shipping_amount, $this->stripe_customer->id, $source );
        $charge_data['description'] = 'CandyBar order';

        return $this->stripe->charges->create( $charge_data );
    }

    public function capturePayment( $charge )
    {
        $this->stripe->charges->capture( $charge->id, [] );
    }

    public static function generatePaymentGuest($customer_id, $data, $shipping_amount, $source)
    {
        $stripe = self::_getStripe();

        $stripe_amount = ($data['total_amount']) * 100; // stripe only deals in pennies...

        $charge_data = self::_gatherChargeData( $_SESSION['cart'], $stripe_amount, $shipping_amount, $customer_id, $source );
        $charge_data['description'] = 'CandyBar order for ' . $data['email'];

        return $stripe->charges->create( $charge_data );
    }

    public static function captureGuestPayment( $charge )
    {
        $stripe = self::_getStripe();
        $stripe->charges->capture( $charge->id, [] );
    }

    // creates payment source, return token id
    public static function createSource($card_number, $exp_month = null, $exp_year = null, $cvv = null)
    {
        $stripe = self::_getStripe();
        $token = $stripe->tokens->create(
            array
            (
                'card' => array
                (
                    'number' => $card_number,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                    'cvc' => $cvv,
                )
            )
        );

        $new_source_response = $stripe->sources->create(
            array
            (
                'type' => 'card',
                'token' => $token->id
            )
        );
        return $new_source_response->id;
    }

    public static function addSource($customer_id, $token_id)
    {
        $stripe = self::_getStripe();

        $new_card_response = $stripe->customers->createSource(
            $customer_id,
            array
            (
                'source' => $token_id,
            )
        );

        $new_card_response->address_line1 = $_SESSION['checkout']['address']['shipping']['address_1'];
        
        if( !empty($_SESSION['checkout']['address']['shipping']['address_2']) )
        {
            $new_card_response->address_line2 = $_SESSION['checkout']['address']['shipping']['address_2'];
        }
        
        $new_card_response->address_city = $_SESSION['checkout']['address']['shipping']['city'];
        $new_card_response->address_state = $_SESSION['checkout']['address']['shipping']['state'];
        $new_card_response->address_zip = $_SESSION['checkout']['address']['shipping']['zip'];
        $new_card_response->address_country = $_SESSION['checkout']['address']['shipping']['country'];

        $new_card_response->save();

        return $new_card_response->id;
    }

    public static function refundCharge($charge_id)
    {
        $stripe = self::_getStripe();

        $refund_response = $stripe->refunds->create([
            'charge' => $charge_id,
        ]);
        
        return $refund_response;
    }
}