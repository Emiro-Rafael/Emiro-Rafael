<?php
require_once __DIR__ . "/../wp-load.php";

$dbh = SCModel::getSnackCrateDB();

$current_month_country = getCurrentMonthCountry();
$last_month_country = getCurrentMonthCountry(true);
$ungroupable_countries_str = "'Current', 'HOLD', 'Support', '{$current_month_country}', '{$last_month_country}', 'Italy', 'Ireland', 'Ireland HOLD'";
checkRegularOrders($ungroupable_countries_str);

checkMultiMainOrders($ungroupable_countries_str);

updateFulfilledOrders();

updateFulfilledCombinedOrders();

function getCurrentMonthCountry( $previous_month = false )
{
    if( $previous_month )
    {
	$month_year = date('m-Y', strtotime("-1 month"));   
    }
    else
    {
        $month_year = date('m-Y');
    }
    $dbh = SCModel::getSnackCrateDB();
    $stmt = $dbh->prepare("SELECT country_name FROM sap_monthly WHERE month_year = :month_year");
    $stmt->bindParam(":month_year", $month_year);
    $stmt->execute();
    $country = $stmt->fetch(PDO::FETCH_COLUMN);
    $stmt = null;
    if( empty($country) )
        $country = 'Current';

    return $country;
}

function updateFulfilledCombinedOrders()
{
	$dbh = SCModel::getSnackCrateDB();
    $directus = SCModel::getDirectus();
	$stmt = $dbh->prepare("SELECT * FROM candybar_order WHERE in_main_table = 2 AND `status` IN ('processing','printable')");
    $stmt->execute();
    $cb_orders = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;

    foreach($cb_orders as $cb_order)
    {
        $stmt = $dbh->prepare("SELECT email FROM Users WHERE id = :id");
        $stmt->bindParam(":id", $cb_order->user_id);
        $stmt->execute();
        $customer_email = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        $items = unserialize($cb_order->purchased);

        $post_id = current( array_keys($items) );

        $stmt = $directus->prepare("SELECT offered_product FROM plan_offers WHERE candybar_item_id = :post_id LIMIT 1");
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        $bonus_crate_name = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        $stmt = $dbh->prepare("SELECT * FROM OrdersBatch WHERE Email = :email AND First_Country LIKE '%||{$bonus_crate_name}'");
        $stmt->bindParam(":email", $customer_email);
        $stmt->execute();
        $shipped_order = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;

        if( !empty($shipped_order) )
        {
            $update = $dbh->prepare("UPDATE candybar_order SET `status` = 'fulfilled', shipment_id = :shipment_id WHERE id = :id");
            $update->bindParam(":id", $cb_order->id);
            $update->bindParam(":shipment_id", $shipped_order->shipment_id);
            $update->execute();
            $update = null;
        }
    }
}

function updateFulfilledOrders()
{
	$dbh = SCModel::getSnackCrateDB();
	$stmt = $dbh->prepare("SELECT payment_id FROM candybar_order WHERE in_main_table = 1 AND `status` IN ('processing','printable')");
	$stmt->execute();
	$payment_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
	$stmt = null;

    $possible_sub_payment_items = array(
        'holiday' => 'Holiday',
        'gummie' => 'Gummie Crate',
	    'belgium' => 'Belgium'
    );

	foreach($payment_ids as $payment_id)
	{
        $parts = explode('-', $payment_id);

        $base_payment_id = $parts[0];

        $sub_payment_item = empty($parts[1]) ? null : $parts[1];

        if( empty($sub_payment_item) )
        {
            $stmt = $dbh->prepare("SELECT * FROM OrdersBatch WHERE Payment_ID LIKE '{$base_payment_id}%' AND shipment_id IS NOT NULL");
        }
        else
        {
            $stmt = $dbh->prepare("SELECT * FROM OrdersBatch WHERE Payment_ID LIKE '{$base_payment_id}%' AND First_Country = :sub_payment_item AND shipment_id IS NOT NULL");
            $stmt->bindParam(":sub_payment_item", $possible_sub_payment_items[$sub_payment_item]);
        }
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        if( $orders )
        {
            $s_ids = array();
            foreach($orders as $order)
            {
                array_push($s_ids, $order->shipment_id);
            }
            $shipments = implode(',',$s_ids);
            $upd = $dbh->prepare("UPDATE candybar_order SET `status` = 'fulfilled', shipment_id = '{$shipments}' WHERE payment_id = '{$payment_id}'");
            $upd->execute();
            $upd = null;
        }
	}

	$payment_ids = null;
	$stmt = $dbh->prepare("SELECT payment_id FROM candybar_order WHERE in_main_table = 0 AND `status` = 'printable' AND shipment_id IS NOT NULL");
	$stmt->execute();
	$payment_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
	$stmt = null;

	foreach($payment_ids as $payment_id)
	{
		$upd = $dbh->prepare("UPDATE candybar_order SET `status` = 'fulfilled', ship_date = CURRENT_DATE() WHERE payment_id = :payment_id");
		$upd->bindParam(":payment_id", $payment_id);
        $upd->execute();
        $upd = null;
	}
}


function checkMultiMainOrders($ungroupable_countries_str)
{
    $dbh = SCModel::getSnackCrateDB();

    /* 20240329 removed "AND Plan NOT IN ('16Snack', '16SnackW') " from the below sql. */
    $stmt = $dbh->prepare("SELECT * FROM Orders 
        WHERE First_Country NOT IN (".$ungroupable_countries_str.")
	    AND Country = 'United States of America'
        GROUP BY Shipping_Name, Email, `Address`, City
        HAVING COUNT(Payment_ID) > 1
    ");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;

    foreach($customers as $customer)
    {

        $user_select = $dbh->prepare("SELECT id FROM Users WHERE customer_ID = :customer_id");
        $user_select->bindParam(":customer_id", $customer->customer_ID);
	    $user_select->execute();
        $user_id = $user_select->fetch(PDO::FETCH_COLUMN);
        $user_select = null;

        if( !$user_id ) continue;

        $stmt = $dbh->prepare("SELECT * FROM Orders 
            WHERE Shipping_Name = :shipping_name AND Email = :email 
                AND `Address` = :address 
                AND City = :city 
                AND First_Country NOT IN ({$ungroupable_countries_str}) 
        ");
        $stmt->bindParam(":shipping_name", $customer->Shipping_Name);
        $stmt->bindParam(":email", $customer->Email);
        $stmt->bindParam(":address", $customer->Address);
        $stmt->bindParam(":city", $customer->City);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        $data = array(
	        'shipping_name' => $customer->Shipping_Name,
            'address_1' => $customer->Address,
            'address_2' => $customer->suite,
            'city' => $customer->City,
            'state' => $customer->State,
            'country' => $customer->Country,
            'zip' => $customer->Zip
        );

        $address_id = Address::addAddress( $data, $customer->customer_ID );

        foreach($orders as $order)
        {
            $country_parts = explode('||', $order->First_Country);

            if( !empty($country_parts[1]) )
            {
                $free_box_updated = updateFreeBox( $country_parts[1], $user_id, $dbh );
                if( !$free_box_updated ) continue;
                $order->First_Country = $country_parts[0];
            }

            echo "mq, {$user_id}, {$address_id}, {$customer->Email}, {$order->Payment_ID}\n";
            $stmt = $dbh->prepare("SELECT Price, subscription_id FROM OrderHistory WHERE Payment_ID = :payment_id");
            $stmt->bindParam(":payment_id", $order->Payment_ID);
            $stmt->execute();

            $oh = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt = null;

            $order->Price = $oh->Price;
            $order->subscription_id = $oh->subscription_id;

            $stmt = $dbh->prepare("SELECT customization_notes FROM Subscriptions WHERE customer_id = :customer_id");
            $stmt->bindParam(":customer_id", $order->customer_ID);
            $stmt->execute();
            $customization_notes = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;

            switch($order->First_Country)
            {
                case 'England':
                    $country = 'United Kingdom';
                    break;
                case 'RoadTrip':
                    $country = 'Road Trip';
                    break;
                case 'KitKat':
                    $country = 'Kit Kat';
                    break;                    
                default:
                    $country = $order->First_Country;
                    break;
            }

            $post = get_page_by_title( $country, OBJECT, array('collection','country') );
            $preorder_date = get_post_meta( $post->ID, 'preorder-shipping-date', true );

            if( 
                ($country != 'Holiday')
                &&
                ( empty($post) || ( !empty($preorder_date) && strtotime($preorder_date) > time() ) )
            )
            {
                continue;
            }

            $cbid = str_replace("CandyBar Order #", "", $order->subscription_id);

            if( !empty($cbid) && substr($order->subscription_id,0,3) != 'sub' )
            {
                $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE id = :id");
                $stmt->bindParam(":id", $cbid);
                $stmt->execute();
                $stmt = null;
            }
            else
            {
                $cost = number_format($order->Price / 100 , 2);
                $stmt = $dbh->prepare("INSERT INTO candybar_order (`user_id`, purchased, cost, shipping_address, order_date, status, payment_id, in_main_table, customization_notes)
                                        VALUES (:user_id, :purchased, :cost, :shipping_address, :order_date, 'processing', :payment_id, 0, :customization_notes)");
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":purchased", $order->purchased);
                $stmt->bindParam(":cost", $cost);
                $stmt->bindParam(":shipping_address", $address_id);
                $stmt->bindParam(":order_date", $order->Order_Date);
                $stmt->bindParam(":payment_id", $order->Payment_ID);
                $stmt->bindParam(":customization_notes", $customization_notes);
                $stmt->execute();
                $stmt = null;
            }

            $stmt = $dbh->prepare("DELETE FROM Orders WHERE Payment_ID = :payment_id");
            $stmt->bindParam(":payment_id", $order->Payment_ID);
            $stmt->execute();
            $stmt=null;
        }
    }
}

function checkRegularOrders($ungroupable_countries_str)
{
    $dbh = SCModel::getSnackCrateDB();
    $stmt = $dbh->prepare("SELECT `user_id`, shipping_address, order_date, SUM(in_main_table) as main_table_count, count(id) as order_count 
                            FROM candybar_order 
                            WHERE `status` IN ('processing', 'printable') AND in_main_table < 2
                                AND (preorder_date IS NULL OR preorder_date < NOW())
                            GROUP BY `user_id`, shipping_address 
                        ");
    $stmt->execute();
    $uids = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $stmt = null;
	
    foreach($uids as $uid)
    {
        if( substr($uid->user_id, 0, 1) == 'g' )
        {
            $stmt = $dbh->prepare("SELECT email FROM guest_user WHERE `user_id` = :id");
            $stmt->bindParam(":id", $uid->user_id);
            $stmt->execute();
            $customer_email = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;
        }
        else
        {
            $stmt = $dbh->prepare("SELECT email FROM Users WHERE id = :id");
            $stmt->bindParam(":id", $uid->user_id);
            $stmt->execute();
            $customer_email = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;
        }

        if( empty($customer_email) )
        {
            continue;
        }

        $stmt = $dbh->prepare("SELECT 
                TRIM(address_1) as address_1, 
                TRIM(city) as city, 
                TRIM(state) as state, 
                SUBSTR(zipcode,1,5) as zipcode, 
                TRIM(country) as country 
            FROM `Address` 
            WHERE id = :shipping_id");
        $stmt->bindParam(":shipping_id", $uid->shipping_address);
        $stmt->execute();

        $address = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;

        if( !in_array( $address->country, array('United States of America','USA','US') ) )
        {
            continue;
        }

        //if(date('Ym', strtotime($uid->order_date)) == '202403') {
        //    $ungroupable_countries_sql_str = str_replace("'Current', ", "", $ungroupable_countries_str);
        //} else {
            $ungroupable_countries_sql_str = $ungroupable_countries_str;
        //}

        /* 20240329 removed "AND a.Plan NOT IN ('16Snack', '16SnackW') AND TRIM(a.`State`) = :state " from the below sql. */
        $stmt = $dbh->prepare("SELECT a.* FROM Orders a 
                                WHERE a.Email = :email
                                AND a.First_Country NOT IN ($ungroupable_countries_sql_str)
                                AND TRIM(a.`Address`) = :address 
                                AND TRIM(a.City) = :city
                                AND SUBSTR(a.Zip,1,5) = :zip");

        $stmt->bindParam(":email", $customer_email);
      	$stmt->bindParam(":address", $address->address_1);
        $stmt->bindParam(":city", $address->city);
        //$stmt->bindParam(":state", $address->state);
        $stmt->bindParam(":zip", $address->zipcode);
        //$stmt->bindParam(":shipping_name", $address->shipping_name);
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        if( !$orders || ($uid->main_table_count == 1 && count($orders) == 1) )
        {
            continue;
        }

        foreach($orders as $order)
	    {
            $country_parts = explode('||', $order->First_Country);

            if( !empty($country_parts[1]) )
            {
                $free_box_updated = updateFreeBox( $country_parts[1], $uid->user_id, $dbh );
                if( !$free_box_updated ) continue;
                $order->First_Country = $country_parts[0];
            }

            echo "cb, {$uid->user_id}, {$uid->shipping_address}, {$customer_email}, {$order->Payment_ID}\n";
            $stmt = $dbh->prepare("SELECT Price, subscription_id FROM OrderHistory WHERE Payment_ID = :payment_id");
            $stmt->bindParam(":payment_id", $order->Payment_ID);
            $stmt->execute();

            $oh = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt = null;

            $order->Price = $oh->Price;
            $order->subscription_id = $oh->subscription_id;

            $stmt = $dbh->prepare("SELECT customization_notes FROM Subscriptions WHERE customer_id = :customer_id");
            $stmt->bindParam(":customer_id", $order->customer_ID);
            $stmt->execute();
            $customization_notes = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;

            switch($order->First_Country)
            {
                case 'England':
                    $country = 'United Kingdom';
                    break;
                case 'RoadTrip':
                    $country = 'Road Trip';
                    break;
                case 'KitKat':
                    $country = 'Kit Kat';
                    break;
                default:
                    $country = $order->First_Country;
                    break;
            }

            $post = get_page_by_title( $country, OBJECT, array('collection','country') );
            $preorder_date = get_post_meta( $post->ID, 'preorder-shipping-date', true );

            if( 
                ($country != 'Holiday')
                &&
                (empty($post) || ( !empty($preorder_date) && strtotime($preorder_date) > time() ) )
            )
            {
                continue;
            }

            $cbid = str_replace("CandyBar Order #", "", $order->subscription_id);

            if( !empty($cbid) && substr($order->subscription_id,0,3) != 'sub' )
            {
                $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE id = :id");
                $stmt->bindParam(":id", $cbid);
                $stmt->execute();
                $stmt = null;
            }
            else
            {

                $cost = number_format($order->Price / 100 , 2);
                $stmt = $dbh->prepare("INSERT INTO candybar_order (`user_id`, purchased, cost, shipping_address, order_date, status, payment_id, in_main_table, customization_notes)
                                        VALUES (:user_id, :purchased, :cost, :shipping_address, :order_date, 'processing', :payment_id, 0, :customization_notes)");
                $stmt->bindParam(":user_id", $uid->user_id);
                $stmt->bindParam(":purchased", $order->purchased);
                $stmt->bindParam(":cost", $cost);
                $stmt->bindParam(":shipping_address", $uid->shipping_address);
                $stmt->bindParam(":order_date", $order->Order_Date);
                $stmt->bindParam(":payment_id", $order->Payment_ID);
                $stmt->bindParam(":customization_notes", $customization_notes);
                $stmt->execute();
                $stmt = null;
            }

            $stmt = $dbh->prepare("DELETE FROM Orders WHERE Payment_ID = :payment_id");
            $stmt->bindParam(":payment_id", $order->Payment_ID);
            $stmt->execute();
            $stmt=null;
	    }
        
        $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE user_id = :user_id AND shipping_address = :shipping_address AND `status` IN ('processing', 'printable') AND in_main_table = 1");
        $stmt->bindParam(":user_id", $uid->user_id);
        $stmt->bindParam(":shipping_address", $uid->shipping_address);
        $stmt->execute();
        $stmt=null;
    }
}

function updateFreeBox( $country, $user_id, $dbh )
{
    if($country == 'Birthday') return true;
    
    $post = get_page_by_title( $country, OBJECT, array('collection','country') );
    $preorder_date = get_post_meta( $post->ID, 'preorder-shipping-date', true );

    if( empty($post) || ( !empty($preorder_date) && strtotime($preorder_date) > time() ) )
    {
        return false;
    }

    $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE in_main_table = 2 AND user_id = :user_id AND `status` = 'processing' AND purchased LIKE '%i:{$post->ID};%'");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $stmt = null;

    return true;
}
?>

