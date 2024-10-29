<?php

require_once('SCAjax.php');

class UserAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'user';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'sc_login' => 'login',
                'add_drink' => 'addDrink'
            ]
        );
    }


    public static function login()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $user = new User( $data['email'] );
            $user->login(
                array(
                    'email' => $data['email'],
                    'pwd' => $data['pwd'],
                )
            );
            $data['password'] = $data['pwd'];
            $user->accountLogin( $data );

            if( strpos($_SERVER['HTTP_REFERER'], "checkout") !== false )
            {
                $redirect_to = get_permalink( get_page_by_path( 'checkout/checkout-confirm-shipping' ) );
            }
            else
            {
                $redirect_to = '';
            }

            $send_data = array(
                "callback" => "loginSuccess",
                "callbackArguments" => array(
                    array(
                        'redirect_link' => $redirect_to
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

    public static function addDrink()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            User::addDrinkToSubscription($data);

            $send_data = array(
                "callback" => "addDrinkSuccess",
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
            self::$instance = new UserAjax();
        }

        return self::$instance;
    }
}

UserAjax::getInstance();