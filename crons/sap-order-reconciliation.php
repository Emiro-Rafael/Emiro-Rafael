<?php
require_once __DIR__ . "/../../keys.php";
require_once __DIR__ . "/../wp-load.php";
require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbh = SCModel::getSnackCrateDB();

$stmt = $dbh->prepare("SELECT * FROM candybar_order
                        WHERE (sap_invoice_id = 0 OR sap_invoice_id IS NULL) AND status != 'canceled'
			AND (preorder_date IS NULL OR preorder_date < NOW() OR preorder_date = '2023-04-12')
                        ORDER BY order_date ASC
			LIMIT 30
			");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = null;

if( !$orders ) exit;

try
{
	$sap = \SAPb1\SAPClient::createSession();
	$sap_invoice = new SAPInvoice($sap);
}
catch( Exception $e )
{
	//echo "sap is trash";

    // The message
$message = "SAP SERVER IS DOWN. PLEASE RESTART SAP SERVER.";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail('saperrors-aaaalxjlv7fyfverixzlysh5tu@snackcrate.slack.com', 'SAP SERVER DOWN!!!', $message);

    //echo $e;
	die;
}

foreach($orders as $order)
{
    try
    {
        /**
         * check if this has been updated since the script started running because SAP is slow af
         */
       
        $stmt = $dbh->prepare("SELECT sap_invoice_id FROM candybar_order WHERE id = :id");
        $stmt->bindParam(":id", $order->id);
        $stmt->execute();
        $sap_check = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        if(!empty($sap_check))
        {
            continue;
        }
        /**
         * end check
         */
	echo "{$order->id}, ";
    	$user_obj = User::getUserById($order->user_id);
    	$card_code = SAPMaster::createCustomer($user_obj->email, $user_obj->customer_id, $user_obj->first_name, $user_obj->last_name, $sap);

        $items = unserialize( $order->purchased );
    	$sap_invoice->setLineItems($items);

        $is_reserve = false;
	/*
        foreach( array_keys($items) as $post_id )
        {
            $preorder_date = get_post_meta($post_id, 'preorder-shipping-date', true);
            if( !empty($preorder_date) && strtotime($preorder_date) > time() )
            {
                $is_reserve = true;
            }
        }
	*/

        
    	$sap_invoice_document = $sap_invoice->create( $user_obj->email, $card_code, $is_reserve, $order->payment_id ); // send sale data to SAP via creating an Invoice document
	echo $sap_invoice_document->DocNum . "\n";
     
	if( !empty($sap_invoice_document) )
        {
            
        	$stmt = $dbh->prepare("UPDATE candybar_order SET sap_invoice_id = :sap_invoice_id WHERE id = :id");
        	$stmt->bindParam(":sap_invoice_id", $sap_invoice_document->DocNum);
        	$stmt->bindParam(":id", $order->id);
        	$stmt->execute();
        	$stmt = null;
        }
    }
    catch(Exception $e)
    {
//	die('<pre>'.print_r($e,1));
	$message = $order->id . ": " . $e->getMessage();
	$trace = $e->getTrace();
   
	$trace = serialize($trace);
	echo $message . "\n";


    //ignore mystery - Kyle
    foreach( array_keys($items) as $post_id )
        {      
           
if($post_id != "25637"){
        
// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail('saperrors-aaaalxjlv7fyfverixzlysh5tu@snackcrate.slack.com', 'SAP ERROR!!!', $message);

}}
        $stmt = $dbh->prepare("UPDATE candybar_order SET sap_invoice_id = -1 WHERE id = :id");
        $stmt->bindParam(":id", $order->id);
        $stmt->execute();
        $stmt = null;


	$stmt = $dbh->prepare("INSERT INTO sap_error_log (message, trace) VALUES (:message, :trace)");
	$stmt->bindParam(":message", $message);
	$stmt->bindParam(":trace", $trace);
	$stmt->execute();
    }
}
