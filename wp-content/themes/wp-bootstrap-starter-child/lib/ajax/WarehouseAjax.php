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
            if ($public) {
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
        setcookie('warehouse_id', $user->ID, time() + 3600, '/');

        if (is_wp_error($user)) {
            wp_send_json_error(['errors' => ['email' => 'Invalid login credentials.']], 300);
        } else {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true, true);

            wp_send_json_success(array('redirect_url' => '/ops'));
        }
    }

    private static function validateWarehouseUserAccess()
    {
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
    public static function getOrderFromQueue()
    {
        if (!self::validateWarehouseUserAccess()) return;

        try {
            $warehouse = new Warehouse();
            $warehouse->adjustPickerNumber();
            $items = $warehouse->getNextOrder(false);
            $customer_info = $warehouse->getCustomerInformation();

            // $items['items'][0]['item_name'] = 'Allergy Test - Scan Skittles Chewies';
            // $items['items'][0]['quantity'] = '1';
            // $items['items'][0]['barcode'] = '4009900538800';
            // $items['items'][0]['customization'] = 'Allergy: Strawberries | No coconut (receive muliple Coconut Free Snacks, and a few other randoms, to fulfill the crate for August)';

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function orderSessionExpired()
    {
        if (!self::validateWarehouseUserAccess()) return;

        try {
            update_user_meta(intval($_COOKIE['warehouse_id']), 'active_order', []);
            $order_ids = $_POST['order_id'];
            $warehouse = new Warehouse();
            $warehouse->setOrdersSessionExpired($order_ids);
            wp_send_json_success(['success' => true]);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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

        function generateLabelZPL($elements)
        {
            $zpl_labels = [];
            $max_lines_per_label = 11; // Adjust based on space availability for contents
            $contents_chunks = array_chunk($elements['contents'], $max_lines_per_label);
            $total_pages = count($contents_chunks);
            $current_page = 1;

            $logo_data = "^FO30,20^GFA,2750,2750,25,J01gY01,J03hF8,J07hFC,J0hGFE,I01hHF,I03hHF8,I07hHFC,I0hIFE,001hJF,003hJF8,007hJFC,00hKFE,01hLF,03hLF8,07hLFC,0hMFE,1hNF,3hNF87hNFC7hNFE7hNFC:7hNFE7hNFC::::7KFE001IF01IF00JF007KF003IF01FFE00JFC7KF8I07FF00IF00IFE007JF8I07FE01FFC01JFC7KFJ01FF007FF00IFE003JFJ03FE01FF803JFC7JFEK0FF007FF00IFE003IFCK0FE01FF003JFC7JFCK07F003FF00IFC003IF8K0FE01FE007JFC7JF8K03F001FF00IFC001IFL07E01FC00KFC7JF8K03FI0FF00IF8001IFL03E01F801KFC7JF007F807FI07F00IF8I0FFE001E003E01F803KFC7JF00FFE1FFI07F00IF8I0FFE007F807E01F007KFC7JF00IF7FFI03F00IFJ07FC00FFE1FE01E007KFC7JF007KFI01F00IF00807FC01FFE7FE01C00LFC7JFI07JFJ0F00FFE01807FC03KFE01801LFC7JFJ01IFJ0F00FFE01C03FC03KFE01803LFC7JF8J03FFJ0700FFC01C03F803KFEJ03LFC7JF8K0FFJ0300FFC03C01F803KFEJ03LFC7JFCK07F0080100FFC03E01F803KFEJ01LFC7JFEK03F00CJ0FF803E01F803KFEK0LFC7KFCJ01F00EJ0FF807F00F803KFEK0LFC7LFCI01F00FJ0FF007F00FC03KFEK07KFC7NF001F00FJ0FFL07C03KFEK03KFC7NFE01F00F8I0FFL07C01FFE3FE001803KFC7JFC7FFE01F00FCI0FEL03C00FFC0FE003C01KFC7JFC3FFE01F00FEI0FEL03E007F807E007C01KFC7JF803F801F00FEI0FCL03EL03E007E00KFC7JF8K01F00FFI0FCL01FL03E00FF007JFC7JFL03F00FF800F8L01F8K07E01FF007JFC7JFL03F00FFC00F803FFE00FCK07E01FF803JFC7JFL07F00FFE00F807FFE00FEJ01FE01FFC01JFC7JFCK0FF00FFE00F007IF00FFJ03FE01FFC01JFC7KFJ03FF00IF00F007IF007FCI0FFE01FFE00JFC7KFE001QF7MF7FF003OFEJFC7hNFC::::::7MF03gYFC7LF8003FFEK0LF801FF8L03CL0JFC7KFCJ0FFEK03KF001FF8L03CL0JFC7KF8J03FEL0KFI0FF8L03CL0JFC7KFK01FEL07JFI0FF8L03CL0JFC7JFEL0FEL03IFEI0FF8L03CL0JFC7JFCL0FEL03IFEI07F8L03CL0JFC7JF8L07EL01IFCI07F8L03CL0JFC7JF80038003E008I01IFCI03F8L03CL0JFC7JF001FF00FE01FFC01IFCI03IFE00IFC01NFC7JF003FF83FE01FFC01IF8I03IFE00IFC01NFC7JF007FFC7FE01FFE00IF80401IFE00IFC01NFC7JF00IFDFFE01FFE00IF00601IFE00IFC01NFC7IFE00LFE01FFE01IF00600IFE00IFCJ03KFC7IFE00LFE01FFC01FFE00E00IFE00IFCJ03KFC7IFE01LFE00FF801FFE00F007FFE00IFCJ03KFC7IFE01LFEL01FFE01F007FFE00IFCJ03KFC7IFE01LFEL03FFC01F807FFE00IFCJ03KFC7IFE01LFEL03FFC01F803FFE00IFCJ03KFC7IFE00LFEL07FF803F803FFE00IFCJ03KFC7IFE00LFEL0IF803FC01FFE00IFCJ03KFC7IFE00LFEK03IF8K01FFE00IFC01NFC7JF007FFC7FEK03IFL01FFE00IFC01NFC7JF003FF81FE01FC01IFM0FFE00IFC01NFC7JF001FF00FE01FE00FFEM0FFE00IFC01NFC7JF8007C003E01FE00FFEM07FE00IFCL0JFC7JF8L03E01FF007FCM07FE00IFCL0JFC7JFCL07E01FF003FCM03FE00IFCL0JFC7JFEL0FE01FF803FC01IF003FE00IFCL0JFC7KFK01FE01FFC01F801IF803FE00IFCL0JFC7KF8J03FE01FFC00F803IF801FE00IFCL0JFC7KFCJ07FE01FFE00F003IFC01FE00IFCL0JFC7LFI01FFE01IF007007IFC00FE00IFCL0JFC7LFE00gYFC7hNFC::::7hNFE7hNFC7hNFE7hNFC,^FS"; // Placeholder for static logo ZPL code.

            $logo_width = 100; // Change as needed
            $logo_height = 50; // Change as needed
            // Calculate the total bytes based on the width and height
            $total_bytes = ($logo_width * $logo_height + 7) / 8; // Total bytes for a bitmap image
            // Format the logo ZPL
            $logo_zpl = "^FO10,20^GFA,$total_bytes,$logo_height,$logo_width,$logo_data";

            foreach ($contents_chunks as $contents_chunk) {
                $zpl = "^XA"; // Start of ZPL
                if ($current_page == 1) {
                    // Static Logo
                    $zpl .= $logo_zpl;
                    // Assembly Line Designation
                    $zpl .= "^FO470,20^GB320,100,5^FS"; // Draws a box (border) with width 400, height 100, and thickness 3
                    $zpl .= "^FO500,35^A0N,90,90^FD" . $elements['assemblyLine'] . "^FS";

                    // Data Matrix
                    $zpl .= "^FO610,160^BXN,10,200^FD" . $elements['dataMatrix'] . "^FS";

                    // Shipping Address
                    $zpl .= "^FO30,170^A0N,30,30^FDSHIP TO:^FS";
                    $y_position = 220;
                    foreach ($elements['address'] as $line) {
                        $zpl .= "^FO30,$y_position^A0N,24,24^FD" . $line . "^FS";
                        $y_position += 25; // Move down for the next line
                    }

                    $zpl .= "^FO20,400^GB770,5,5^FS";
                }

                // Contents List
                if ($current_page == 1) {
                    $zpl .= "^FO30,440^A0N,25,25^FDCONTENTS:^FS";
                    $y_position = 490;
                } else {
                    $zpl .= "^FO30,50^A0N,25,25^FDCONTENTS(cont):^FS";
                    $y_position = 100;
                }

                foreach ($contents_chunk as $content) {
                    $content = str_replace("–", "-", $content);
                    $zpl .= "^FO30,$y_position^A0N,20,26^FD" . $content . "^FS";
                    $y_position += 35;
                }

                // Page Header for Multi-page Labels
                if ($total_pages > 1) {
                    $zpl .= "^FO20,1190^A0N,15,15^FDPage $current_page of $total_pages^FS";
                }
                // Custom Order Note on Last Page Only
                if ($current_page == $total_pages) {
                    $y_position += 10;
                    $label_width = 812; // Label width in dots (for a 4-inch label at 203 DPI)
                    $text_length = strlen($line); // Number of characters in the line
                    $character_width = 14; // Approximate width of each character at font size 28
                    $text_width = $text_length * $character_width;
                    // Calculate the x-coordinate for centered text
                    $x_position = ($label_width - $text_width) / 2;
                    // Construct the ZPL command with centered x-position
                    // $y_position = 700;
                    $y_position += 10;
                    if (isset($elements['customOrderNote']) && !empty($elements['customOrderNote'])) {
                        $zpl .= "^FO272,$y_position^A0N,28,28^FD*** CUSTOM ORDER ***^FS";
                    }

                    $y_position += 35;
                    // foreach (explode("\n", $elements['customOrderNote']) as $line) {
                    // Check if 'customOrderNote' is set and not empty
                    if (isset($elements['customOrderNote']) && !empty($elements['customOrderNote'])) {

                        $margin = 10; // Define the left and right margin

                        foreach ($elements['customOrderNote'] as $line) {
                            $words = explode(' ', $line); // Split the line into words
                            $current_line = '';

                            foreach ($words as $word) {
                                $current_line_with_word = (empty($current_line) ? '' : $current_line . ' ') . $word;
                                $text_width = strlen($current_line_with_word) * $character_width;

                                if ($text_width > ($label_width - 2 * $margin)) {
                                    // Print the current line if adding the next word exceeds label width (considering margins)
                                    $x_position = $margin + (($label_width - 2 * $margin - (strlen($current_line) * $character_width)) / 2);
                                    $zpl .= "^FO$x_position,$y_position^A0N,28,28^FD" . $current_line . "^FS";
                                    $y_position += 30;

                                    // Start a new line with the current word
                                    $current_line = $word;
                                } else {
                                    $current_line = $current_line_with_word;
                                }
                            }

                            // Print the remaining words in the line if any
                            if (!empty($current_line)) {
                                $x_position = $margin + (($label_width - 2 * $margin - (strlen($current_line) * $character_width)) / 2);
                                $zpl .= "^FO$x_position,$y_position^A0N,28,28^FD" . $current_line . "^FS";
                                $y_position += 25;
                            }
                        }
                    }

                    $zpl .= "^FO20," . $y_position + 12 . "^GB770,5,5^FS";


                    $y_position += 40;

                    // Assuming $label_width is defined correctly, e.g., 812 for 4-inch width at 203 DPI
                    $label_width = 310;

                    // Parameters for PDF417 barcode
                    $barcode_element_width = 7.5; // Adjust this to desired element width
                    $barcode_columns = 4; // Can be adjusted depending on data size and needs
                    $barcode_width = $barcode_columns * $barcode_element_width; // Estimate width

                    // Calculate X position for centering
                    $x_position = ($label_width - $barcode_width) / 2;
                    $x_position = 90;
                    // Construct ZPL for PDF417 barcode
                    $zpl .= "^FO$x_position,1015^B7N,$barcode_columns,$barcode_element_width^FD" . $elements['barcodeData'] . "^FS";

                    $width = 700; // Set the width you want for the text block
                    $zpl .= "^FO50,1180^FB{$width},1,0,C,0^A0N,28,28^FD" . $elements['additionalText'] . "^FS";
                }

                $zpl .= "^XZ"; // End of ZPL

                $zpl_labels[] = $zpl;
                $current_page++;
            }
            return implode("\n", $zpl_labels);
        }



        $order_id = $_POST['order_id'];

        $warehouse = new Warehouse();
        $order_info = $warehouse->getCustomerDataByOrderId($order_id);
        $customer_id = $order_info->user_id;
        $customization_notes = $order_info->customization_notes;
        $barcode_reference = $order_info->barcode_reference;

        $customer_info = $warehouse->getCustomerDataByUserId($customer_id);
        $stripe_customer_id = $customer_info->customer_info;
        // print_r($order_info);

        $address = $_POST['address'];
        $address_input = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $address_input = str_replace("\\n", "\n", $address);
        $address_input = trim($address_input);
        $address_input = mb_convert_encoding($address_input, 'UTF-8', 'auto');
        $address_lines = explode("\n", $address_input);

        $elements = [
            'assemblyLine' => 'CB LN1',
            'dataMatrix' => $stripe_customer_id,
            'address' => $address_lines,
            'contents' => explode("\n", trim($_POST['contents'])),
            'customOrderNote' => $customization_notes,
            'barcodeData' => $barcode_reference,
            'additionalText' => $_POST['additionalText']
        ];

        $zpl_string = generateLabelZPL($elements);

        // echo $zpl_string;

        // File path for saving ZPL
        $uploads_dir = wp_upload_dir();
        $zpl_filename = 'label_output.zpl';
        $file_path = $uploads_dir['path'] . '/' . $zpl_filename;
        file_put_contents($file_path, $zpl_string);

        $file_url = $uploads_dir['url'] . '/' . $zpl_filename;


        require_once get_stylesheet_directory() . '/lib/zebra-print.class.php';
        $zebraPrint = new ZebraPrint();
        // $printerId = sanitize_text_field($_POST['printerId']); // Example input for printer ID
        $printerId = $_POST['printerId']; // Example input for printer ID
        $fileUrl = $file_url;

        // Assuming your ZebraPrint class has a method like this
        $result = $zebraPrint->sendFileToPrinter($printerId, $fileUrl);

        if ($result) {
            // echo "Label sent to the printer successfully.";
            // echo "<script>alert('Label sent to the printer successfully.')</script>";

            $message = sprintf(
                'Label sent to the printer (%s) successfully ZPL URL = %s',
                $printerId,
                esc_url($fileUrl)
            );

            wp_send_json_success($message);
        } else {
            wp_send_json_error('Failed to send label to the printer.');
        }
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
        if (!self::validateWarehouseUserAccess()) return;

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
        update_user_meta(intval($_COOKIE['warehouse_id']), 'active_pack_order', $order);
        wp_send_json_success($order);

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
