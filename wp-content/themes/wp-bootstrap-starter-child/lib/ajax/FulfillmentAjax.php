<?php

require_once('SCAjax.php');

class FulfillmentAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'fulfillment';
    private static $pwd = 'Messenger';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'generate_shipping_label' => 'generateLabel',
                'print_shipping_label' => 'printLabel',
                'next_order_fulfillment' => 'nextOrderFulfillment',
                'generate_invoice' => 'invoiceGeneration',
                'allow_fulfill' => 'allowFulfill',
                'pack_order' => 'packOrder',
                'picker_number' => 'pickerNumber',
                'user_picker_number' => 'setUserPickerNumber',
                'login_picker' => 'loginPicker',
                'print_scanform' => 'printScanform',
            ]
        );
    }

    public static function loginPicker()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $user = get_user_by( 'login', $data['email'] );

            if( !$user || !in_array('fulfillment', $user->roles) || !wp_check_password( $data['password'], $user->data->user_pass, $user->ID ) )
            {
                throw new Exception("Login failed.");
            }

            $_SESSION['allow_fulfill'] = 1;
            $_SESSION['fulfiller_user_id'] = $user->ID;

            $fulfillment = new Fulfillment();
            $current_pickers = $fulfillment->getPickers();

            $_SESSION['picker_user'] = $current_pickers;
            //update_user_meta( $user->ID, 'picker_user', $current_pickers );

            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            $send_data = array(
                "callback" => "straightReload"
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }

    public static function setUserPickerNumber()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $_SESSION['picker_user'] = $data['user_number'];

            $send_data = array(
                "callback" => "straightReload"
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }

    public static function pickerNumber()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            if( !get_option('picker_number') )
            {
                add_option('picker_number', $data['pickers']);
            }
            else
            {
                update_option('picker_number', $data['pickers']);
            }

            $send_data = array(
                "callback" => "straightReload"
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }

    public static function packOrder()
    {
        try
        {
            $fulfillment = new Fulfillment();

            $check_order_ready = $fulfillment->checkOrder( $_POST['order_id'] );
            
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        "redirect_link" => add_query_arg(
                            array(
                                "pack" => 1,
                                "order_id" => $_POST['order_id']
                            ),
                            get_permalink( get_page_by_path( 'fulfillment' ) )
                        )
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

    public static function generateLabel()
    {
        try
        {
            $fulfillment = new Fulfillment();
            $labels = $fulfillment->generateLabel( $_POST );

            $send_data = array(
                "callback" => "fulfillmentSuccess",
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }

        wp_die();
    }

    public static function printLabel()
    {
        try
        {
            $fulfillment = new Fulfillment();
            $labels = $fulfillment->printLabel( $_POST );

            $send_data = array(
                "callback" => "printLabelSuccess",
                "callbackArguments" => array(
                    array(
                        'labels' => $labels
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


    public static function nextOrderFulfillment()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $ids = explode(',', $data['ids']);
            $fulfillment = new Fulfillment();
            $next_id = $fulfillment->nextOrder( $ids, $data['setFulfilled'] );

            /*
            $send_data = array(
                "callback" => "fulfillmentSuccess",
            );
            */
            $send_data = array(
                "callback" => "straightRedirect",
                "callbackArguments" => array(
                    array(
                        "redirect_link" => add_query_arg(
                            array(
                                "pack" => 1,
                                "order_id" => $next_id
                            ),
                            get_permalink( get_page_by_path( 'fulfillment' ) )
                        )
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


    public static function invoiceGeneration()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $fulfillment = new Fulfillment();
            $fulfillment->generateInvoicePDF( $data );

            $fulfillment->setAsPrintable($data['ids']);

            $invoices = array_map(
                function($id)
                {
                    return get_stylesheet_directory_uri() . '/assets/generated_files/candybar_order_'.$id.'.pdf';
                },
                explode(',', $data['ids'])
            );

            $send_data = array(
                "callback" => "invoiceGenerationSuccess",
                "callbackArguments" => array(
                    array(
                        'invoices' => $invoices
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
    
    public static function allowFulfill()
    {
        $data = array_map( 'esc_attr', $_POST );
        if($data['pwd'] == self::$pwd)
        {
            $_SESSION['allow_fulfill'] = 1;

            $send_data = array(
                "callback" => "straightReload",
            );
            wp_send_json_success($send_data);
        }
        else
        {
            unset($_SESSION['allow_fulfill']);
            wp_send_json_error("Incorrect credentials", 500);
        }
        wp_die();
    }

    public static function printScanform()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );
            $fulfillment = new Fulfillment();
            $link = $fulfillment->printScanform($data['id']);

            $send_data = array(
                "callback" => "printedScanform",
                "callbackArguments" => array(
                    array(
                        'link' => $link
                    )
                )
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new FulfillmentAjax();
        }

        return self::$instance;
    }
}

FulfillmentAjax::getInstance();