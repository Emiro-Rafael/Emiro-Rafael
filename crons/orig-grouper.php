<?php
require_once __DIR__ . "/../wp-load.php";

$dbh = SCModel::getSnackCrateDB();

checkMainTableOrders();

//checkRegularOrders();

updateFulfilledOrders();

updateCancelledOrders();


function updateCancelledOrders()
{
	$stripe = new \Stripe\StripeClient(
    		$_ENV['stripe_api_key']
	);
        $dbh = SCModel::getSnackCrateDB();

	$stmt = $dbh->prepare("SELECT payment_id FROM candybar_order WHERE `status` IN ('processing','printable') AND shipment_id IS NULL");
	$stmt->execute();
	$payments = $stmt->fetchAll(PDO::FETCH_COLUMN);

	foreach($payments as $p)
	{
        	$charge = $stripe->charges->retrieve($p,[]);

	        if( !empty($charge->refunded) )
        	{
                	$stmt = $dbh->prepare("UPDATE candybar_order SET status = 'canceled' WHERE payment_id ='{$p}'");
        	        $stmt->execute();
	                $stmt = null;
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
		'gummie' => 'Gummie Crate'
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
		$upd = $dbh->prepare("UPDATE candybar_order SET `status` = 'fulfilled' WHERE payment_id = :payment_id");
		$upd->bindParam(":payment_id", $payment_id);
                $upd->execute();
                $upd = null;
	}
}

function checkMainTableOrders()
{
    $dbh = SCModel::getSnackCrateDB();
    $stmt = $dbh->prepare("SELECT `user_id`, group_concat(payment_id) as p_ids FROM candybar_order WHERE `status` = 'processing' AND in_main_table = 1 GROUP BY `user_id`, shipping_address HAVING count(id) > 1");
    $stmt->execute();
    $uids = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;

    foreach($uids as $uid)
    {
        $payments = "'". str_replace(',', "','", $uid->p_ids)."'";
        $stmt = $dbh->prepare("SELECT * FROM OrderHistory WHERE Payment_ID IN ($payments)");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;
        
        $fulfilled_payments = array();
        $preorder_payments = array();
        foreach($orders as $order)
        {
            //SET TO FULFILLED
            if(!empty($order->trackingcode))
            {
                $stmt = $dbh->prepare("UPDATE candybar_order SET `status` = 'fulfilled' WHERE payment_id = :payment_id");
                $stmt->bindParam(":payment_id", $order->Payment_ID);
                $stmt->execute();
                $stmt = null;

                array_push($fulfilled_payments, $order->Payment_ID);
            }

            switch($order->First_Country)
            {
                case 'England':
                    $country = 'United Kingdom';
                    break;
                default:
                    $country = $order->First_Country;
                    break;
            }

            $post = get_page_by_title( $country, OBJECT, array('collection','country') );
            $preorder_date = get_post_meta( $post->ID, 'preorder-shipping-date', true );
            if( !empty($preorder_date) && strtotime($preorder_date) > time() )
            {
                array_push($preorder_payments, $order->Payment_ID);
            }
        }
        //echo '<pre>'.print_r($fulfilled_payments,1).'</pre>';
        //echo '<pre>'.print_r($preorder_payments,1).'</pre>';
        //echo '<pre>'.print_r($orders,1).'</pre>';
        //check if there's still more than 1 unfulfilled
        if( count($fulfilled_payments) + count($preorder_payments) < count($orders) - 1 )
        {
            // REMOVE FROM Orders AND ADD TO CANDYBAR QUEUE
            $stmt = $dbh->prepare("DELETE FROM Orders WHERE Payment_ID IN ($payments)");
            $stmt->execute();
            $stmt = null;
            $fulfilled_payments_str = "'". implode("','", $fulfilled_payments) ."'";
            $preorder_payments_str = "'". implode("','", $preorder_payments) ."'";
            $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE payment_id IN ($payments) AND payment_id NOT IN ($fulfilled_payments_str) AND payment_id NOT IN ($preorder_payments_str)");
	    $stmt->execute();
            $stmt = null;
        }
        elseif(count($preorder_payments) == count($orders))
        {
            $preorder_payments_str = "'". implode("','", $preorder_payments) ."'";
            // REMOVE FROM Orders AND ADD TO CANDYBAR QUEUE
            $stmt = $dbh->prepare("DELETE FROM Orders WHERE Payment_ID IN ($preorder_payments_str)");
            $stmt->execute();
            $stmt = null;

            $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE payment_id IN ($preorder_payments_str)");
            $stmt->execute();
            $stmt = null;
        }
    }
}

function checkRegularOrders()
{
    $dbh = SCModel::getSnackCrateDB();
    $stmt = $dbh->prepare("SELECT `user_id`, shipping_address FROM candybar_order WHERE `status` = 'processing' AND in_main_table = 0");
    $stmt->execute();
    $uids = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = null;

    foreach($uids as $uid)
    {
        $stmt = $dbh->prepare("SELECT customer_ID FROM Users WHERE id = :id");
        $stmt->bindParam(":id", $uid->user_id);
        $stmt->execute();
        $customer_id = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        $stmt = $dbh->prepare("SELECT * FROM `Address` WHERE id = :shipping_id");
        $stmt->bindParam(":shipping_id", $uid->shipping_address);
        $stmt->execute();

        $address = $stmt->fetch(PDO::FETCH_OBJ);$stmt = null;
        $stmt = $dbh->prepare("SELECT a.*, b.Price FROM Orders a 
                                INNER JOIN OrderHistory b ON a.Payment_ID = b.Payment_ID
                                WHERE a.customer_ID = :customer_id
                                AND a.First_Country != 'Current' 
                                AND a.Plan NOT IN ('16Snack', '16SnackW') 
                                AND a.`Address` = :address 
                                AND a.City = :city
                                AND a.`State` = :state 
                                AND a.Zip = :zip 
                                AND a.Shipping_Name = :shipping_name");
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":address", $address->address_1);
        $stmt->bindParam(":city", $address->city);
        $stmt->bindParam(":state", $address->state);
        $stmt->bindParam(":zip", $address->zipcode);
        $stmt->bindParam(":shipping_name", $address->shipping_name);
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

	if(!$orders)
	{
		continue;
	}

	foreach($orders as $order)
        {
            switch($order->First_Country)
            {
                case 'England':
                    $country = 'United Kingdom';
                    break;
                default:
                    $country = $order->First_Country;
                    break;
            }


            $post = get_page_by_title( $country, OBJECT, array('collection','country') );
            $preorder_date = get_post_meta( $post->ID, 'preorder-shipping-date', true );

	    if( empty($post) || ( !empty($preorder_date) && strtotime($preorder_date) > time() ) )
            {
                continue;
            }

            $cbid = str_replace("CandyBar Order #", "", $order->subscription_id);

            if( !empty($cbid) && substr($order->subscription_id,0,3) != 'sub' )            {
                $stmt = $dbh->prepare("UPDATE candybar_order SET in_main_table = 0 WHERE id = :id");
                $stmt->bindParam(":id", $cbid);
                $stmt->execute();
                $stmt = null;
            }
            else
            {
                $post_id = $post->ID;

                $purchased = array(
                    $post_id => array(
                        $order->Plan => 1
                    )
                );

                $items = serialize($purchased);
                $cost = number_format($order->Price / 100 , 2);
                $stmt = $dbh->prepare("INSERT INTO candybar_order (`user_id`, purchased, cost, shipping_address, order_date, status, payment_id, in_main_table)
                                        VALUES (:user_id, :purchased, :cost, :shipping_address, :order_date, 'processing', :payment_id, 0)");
                $stmt->bindParam(":user_id", $uid->user_id);
                $stmt->bindParam(":purchased", $items);
                $stmt->bindParam(":cost", $cost);
                $stmt->bindParam(":shipping_address", $uid->shipping_address);
                $stmt->bindParam(":order_date", $order->Order_Date);
                $stmt->bindParam(":payment_id", $order->Payment_ID);
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
