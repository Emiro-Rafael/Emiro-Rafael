<?php
require_once __DIR__ . "/../wp-load.php";

$dbh = SCModel::getSnackCrateDB();
$stripe = new \Stripe\StripeClient($_ENV['stripe_api_key']);
try
{
    $stmt = $dbh->prepare("SELECT id, purchased, payment_id, cost FROM candybar_order 
                            WHERE in_main_table >= 0
			    AND `status` IN ('processing','printable')
                            AND id NOT IN (SELECT order_id FROM candybar_order_item GROUP BY order_id)");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;
    foreach( $orders as $order )
    {
        echo "{$order->id}\n";
	$items = unserialize($order->purchased);

	try
	{
	$charge_object = $stripe->charges->retrieve(
		$order->payment_id,
		[]
	);
	}
	catch(Exception $e)
	{
	//continue;
	}

        foreach( $items as $post_id => $item )
        {
            switch( get_post_type($post_id) )
            {
                case 'snack':
 		    $name = get_the_title($post_id);
                    $stmt = $dbh->prepare("INSERT INTO candybar_order_item VALUES (NULL, :order_id, :item_id, :item_name, :price, :quantity)");
                    $stmt->bindParam(":order_id", $order->id);
                    $stmt->bindParam(":item_id", $post_id);
                    $stmt->bindParam(":item_name", $name);
                    $stmt->bindParam(":price", $charge_object->metadata->{"item_price_".$post_id});
                    $stmt->bindParam(":quantity", $item);
                    $stmt->execute();
                    break;
                case 'collection':
                case 'country':
                    foreach( $item as $size => $quantity)
                    {
			$name = get_the_title($post_id) . ' ' . $size;
			$cost_meta = get_post_meta( $post_id, 'cost', true );

			if( $order->cost == 0.00  )
			{
				$price = 0;
			}
			elseif(!empty($cost_meta))
			{
				$price = $cost_meta;
			}
			else
			{
				switch($size)
				{
					case '4Snack':
						$price = 17.99;
					break;
					case '4SnackW':
						$price = 23.98;
					break;
					case '8Snack':
						$price = 29.99;
					break;
					case '8SnackW':
						$price = 35.98;
					break;
					case '16Snack':
						$price = 49.99;
					break;
					case '16SnackW':
						$price = 55.98;
					break;
					default:
						$price = 49.99;
				}
			}



                        $stmt = $dbh->prepare("INSERT INTO candybar_order_item VALUES (NULL, :order_id, :item_id, :item_name, :price, :quantity)");
                        $stmt->bindParam(":order_id", $order->id);
                        $stmt->bindParam(":item_id", $post_id);
                        $stmt->bindParam(":item_name", $name);
                        $stmt->bindParam(":price", $price);
                        $stmt->bindParam(":quantity", $quantity);
                        $stmt->execute();
                    }
                    break;
            }
        }
    }
}
catch(Exception $e)
{
	echo $e->getMessage();
    //die('<pre>'.print_r($e,1));
}

