<?php
require_once __DIR__ . "/../wp-load.php";
require_once __DIR__ . "/../../keys.php";

$dbh = SCModel::getSnackCrateDB();

$stmt = $dbh->prepare("SELECT sum(quantity) FROM customer_refferals.candybar_order_item WHERE item_id = 18199");
$stmt->execute();
$sold = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt = null;


if( $sold >= 495 )
{
	update_post_meta(18199, 'preorder-shipping-date', '');
}

echo $sold;

