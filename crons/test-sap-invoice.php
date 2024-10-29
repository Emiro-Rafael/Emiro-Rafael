<?php
require_once __DIR__ . "/../wp-load.php";
require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

$sap = \SAPb1\SAPClient::createSession();
$sap_invoice = new SAPInvoice();

$dbh = SCModel::getSnackCrateDB();
$week_ago = date('Y-m-d', strtotime('-30 day'));
$stmt = $dbh->prepare("SELECT * FROM candybar_order 
                        WHERE id = 46235
			");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = null;

foreach($orders as $order)
{
    try
    {
	echo "{$order->id}: ";

    	$user_obj = User::getUserById($order->user_id);
        $card_code = SAPMaster::createCustomer($user_obj->email, $user_obj->customer_id, $user_obj->first_name, $user_obj->last_name);

   	$items = unserialize( $order->purchased );

    	$sap_invoice->setLineItems($items);
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
	die('<pre>'.print_r($e,1));
	    echo $e->getMessage() . "\n";
	    //continue

        $stmt = $dbh->prepare("UPDATE candybar_order SET sap_invoice_id = sap_invoice_id - 1 WHERE id = :id");
        $stmt->bindParam(":id", $order->id);
        $stmt->execute();
        $stmt = null;
    }
}
