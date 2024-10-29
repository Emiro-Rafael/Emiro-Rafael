<?php
require_once __DIR__ . "/../wp-load.php";

$dbh = SCModel::getSnackCrateDB();

$stmt = $dbh->prepare("SELECT id, payment_id FROM candybar_order where shipment_id = ''");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt = null;
foreach($orders as $order)
{
	$stmt = $dbh->prepare("SELECT shipment_id FROM OrdersBatch WHERE Payment_ID = :payment_id");
	$stmt->bindParam(":payment_id", $order->payment_id);
	$stmt->execute();
	$shipment_id = $stmt->fetch(PDO::FETCH_COLUMN);
	$stmt = null;

	if($shipment_id)
	{
		$stmt = $dbh->prepare("UPDATE candybar_order SET shipment_id = :shipment_id WHERE id = :id");
		$stmt->bindParam(":shipment_id", $shipment_id);
		$stmt->bindParam(":id", $order->id);
		$stmt->execute();
		$stmt = null;
	}

	echo "{$shipment_id}, {$order->id} \n";
}
