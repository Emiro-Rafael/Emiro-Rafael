<?php
require_once __DIR__ . "/../wp-load.php";
require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

$sap = \SAPb1\SAPClient::createSession();
$sap_invoice = new SAPInvoice();

$dbh = SCModel::getSnackCrateDB();
$week_ago = date('Y-m-d', strtotime('-7 day'));
$stmt = $dbh->prepare("SELECT * FROM candybar_order 
                        WHERE sap_invoice_id < 0
			AND status != 'canceled'
			AND order_date > '{$week_ago}'
                        ORDER BY sap_invoice_id DESC
			");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = null;

foreach($orders as $order)
{
    try
    {
	echo "{$order->id}: ";
        /**
         * check if this has been updated since the script started running because SAP is slow af
         *
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

    	$user_obj = User::getUserById($order->user_id);
        $card_code = SAPMaster::createCustomer($user_obj->email, $user_obj->customer_id, $user_obj->first_name, $user_obj->last_name);

   	$items = unserialize( $order->purchased );

    	$sap_invoice->setLineItems($items);
/*
        $is_reserve = false;
        foreach( array_keys($items) as $post_id )
        {
            $preorder_date = get_post_meta($post_id, 'preorder-shipping-date', true);
            if( !empty($preorder_date) && strtotime($preorder_date) > time() )
            {
                $is_reserve = true;
            }
        }
*/
    	$sap_invoice_document = $sap_invoice->create( $user_obj->email, $card_code ); // send sale data to SAP via creating an Invoice document

	if( !empty($sap_invoice_document) )
        {
        	$stmt = $dbh->prepare("UPDATE candybar_order SET sap_invoice_id = :sap_invoice_id WHERE id = :id");
        	$stmt->bindParam(":sap_invoice_id", $sap_invoice_document->DocNum);
        	$stmt->bindParam(":id", $order->id);
        	$stmt->execute();
        	$stmt = null;
        }
	echo $sap_invoice_document->DocNum ."\n";
    }
    catch(Exception $e)
    {
        $stmt = $dbh->prepare("UPDATE candybar_order SET sap_invoice_id = sap_invoice_id - 1 WHERE id = :id");
        $stmt->bindParam(":id", $order->id);
        $stmt->execute();
        $stmt = null;

        $message = $order->id . "\t" . $e->getMessage() ."\n";
        $trace = $e->getTrace();
	$trace = serialize($trace);
        $stmt = $dbh->prepare("INSERT INTO sap_error_log (message, trace) VALUES (:message, :trace)");
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":trace", $trace);
        $stmt->execute();
	$stmt = null;
    }
}
