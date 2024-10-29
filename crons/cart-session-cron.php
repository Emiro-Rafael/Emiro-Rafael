<?php
require_once __DIR__ . "/../wp-load.php";

$dbh = SCModel::getSnackCrateDB();

$stmt = $dbh->prepare("DELETE FROM candybar_cart_session WHERE CURRENT_TIMESTAMP > expires");
$stmt->execute();
$stmt = null;
