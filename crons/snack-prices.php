<?php
require_once __DIR__ . "/../wp-load.php";
//require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';
//$sap = \SAPb1\SAPClient::createSession();

//$item_service = $sap->getService("Items");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$posts = get_posts([
    'post_type' => 'snack',
    'post_status' => 'publish',
    'numberposts' => -1,
]);

foreach($posts as $post)
{
    if( !metadata_exists( 'post', $post->ID, 'internal-id-code' ))
    {
        continue;
    }

    $item_code = get_post_meta( $post->ID, 'internal-id-code', true );

    

    if(empty($item_code) || $item_code == 'HK-GCH-005')
    {
	continue;
    }

    echo "{$item_code} - ";

    try {
        //$sap_snack_item = new SAPItem( $item_code, $sap );

        //$price = $sap_snack_item->getItemPrice();
        //$member_price = $sap_snack_item->getItemPrice( true );


        //OVERRIDE PRICE ON SELECT ITEM CODES USING TERMINATOR OF "BOM" -  KYLE
        //    $lstTwo = substr($item_code, -3);
        //    echo $lstTwo;
        //    if($lstTwo == 'BOM'){
        //    $member_price = get_post_meta( $post->ID, 'minimum-price', true );
        //    $price = ($member_price * .30) + $member_price;
            
        //}
        $member_price = get_post_meta( $post->ID, 'minimum-price', true );

        if(empty($member_price)) {
            echo "minimum-price missing for " . $post->ID . "\n";
            continue;
        }
        $price = ($member_price * .30) + $member_price;

        echo "{$price}, {$member_price}\n";

        update_post_meta($post->ID, 'price', $price);
        update_post_meta($post->ID, 'member-price', $member_price);
    } catch (Exception $e) {
	$dbh = SCModel::getSnackCrateDB();

        $message = $e->getMessage();

       

	$trace = $e->getTrace();
	$trace = serialize($trace);

        $stmt = $dbh->prepare("INSERT INTO sap_error_log (message, trace) VALUES (:message, :trace)");
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":trace", $trace);
        $stmt->execute();
        $stmt = null;

        continue;
    }
}
?>
