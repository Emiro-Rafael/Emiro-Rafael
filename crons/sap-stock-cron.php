<?php
require_once __DIR__ . "/../wp-load.php";
require_once __DIR__ . "/../../keys.php";
//equire_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

//$candybar_warehouse = 'SSW';
$candybar_warehouse = ['SSW','HBG'];
//$sap = \SAPb1\SAPClient::createSession();
//$item_service = $sap->getService("Items");
//$item_batch_service = $sap->getService("sml.svc/ITEM_BATCH_WAREHOUSE");
$mindate = strftime('%Y-%m-%d',strtotime("+7 day"));
try
{
    /**
     * Country box stock population
     */
    $posts = get_posts([
        'post_type' => array('collection','country'),
        'post_status' => ['publish','draft'],
	   //'exclude' => [18199],
        'numberposts' => -1,
    ]);
    //$sizes = array('4Snack', '8Snack', '16Snack', '4SnackW', '8SnackW', '16SnackW', 'Ultimate');
    $sizes = ['Ultimate'];
    foreach($posts as $post)
    {
        $country_code = get_post_meta($post->ID, 'country-code', true);

        if(!empty($country_code))
        {
            $candybar_stock = get_post_meta($post->ID, 'in-stock', true);

            if( !is_array($candybar_stock) )
            {
                $candybar_stock = array();
            }

            foreach($sizes as $size)
            {
                if( $post->post_type == 'country' && in_array($size, array('4Snack','4SnackW')) && $post->post_title != 'Hawaii' )
                    continue;

                $item_code = $country_code . $size;



              /*  try
                {
                    $batches = $item_batch_service->queryBuilder()
                        ->select("Quantity, IsCommited")
                        ->where(new \SAPb1\Filters\Equal("ItemCode", $item_code))
                        ->where(new \SAPb1\Filters\InArray("WhsCode", $candybar_warehouse))
                        ->where(new \SAPb1\Filters\MoreThanEqual("ExpDate", $mindate))
                        ->findAll();

                    $minimum_obj = $item_service->queryBuilder()
                        ->select("MinInventory, QuantityOrderedByCustomers")
                        ->find($item_code);

                    $minimum = $minimum_obj->MinInventory;

                }*/
                /*catch(Exception $e)
                {
                    continue;
                }*/
/*
                $candybar_stock[$size] = array_sum(
                    array_map(
                        function ($batch)
                        {
                            return $batch->Quantity - $batch->IsCommited;
                        },
                        $batches->value
                    )
                );



                $candybar_stock[$size] = (int)$candybar_stock[$size] - (int)$minimum; // - (int)$minimum_obj->QuantityOrderedByCustomers;
                
*/
                //OVERRIDE INVENTORY ON SELECT ITEM CODES USING ULTIMATE -  KYLE
                if($size == 'Ultimate'){
                    $post_meta_stock = get_post_meta($post->ID, 'stock', true);
                    $candybar_stock[$size] = empty($post_meta_stock) ? 0 : $post_meta_stock;
                    //$candybar_stock[$size] = max(0, $candybar_stock[$size]);
                }

                echo "{$country_code}{$size},{$candybar_stock[$size]}\n";
            }
            update_post_meta($post->ID, 'in-stock', $candybar_stock);
        }
    }

    /**
     * Snacks stock population
     */
    $posts = get_posts([
        'post_type' => 'snack',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    foreach($posts as $post)
    {
        $item_code = get_post_meta($post->ID, 'internal-id-code', true);

        $preorder_date = get_post_meta($post->ID, 'preorder-shipping-date', true);

        $is_preorder = false;

        if( !empty($preorder_date) && strtotime($preorder_date) > time() )
        {
            $is_preorder = true;
        }

        if(!empty($item_code))
        {
         /*   try
            {
                $batches = $item_batch_service->queryBuilder()
                ->select("Quantity")
                ->where(new \SAPb1\Filters\Equal("ItemCode", $item_code))
                ->where(new \SAPb1\Filters\InArray("WhsCode", $candybar_warehouse))
                ->where(new \SAPb1\Filters\MoreThanEqual("ExpDate", $mindate))
                ->findAll();

                $minimum_obj = $item_service->queryBuilder()
                ->select("MinInventory, QuantityOrderedFromVendors, QuantityOrderedByCustomers")
                ->find($item_code);

                $minimum = $minimum_obj->MinInventory;
            }
            catch(Exception $e)
            {
                continue;
            }
            $candybar_stock = array_sum(
                array_map(
                    function ($batch)
                    {
                        return $batch->Quantity;
                    },
                    $batches->value
                )
            );

            if( $is_preorder )
            {
                $candybar_stock += $minimum_obj->QuantityOrderedFromVendors;
            }

            $candybar_stock = (int)$candybar_stock - (int)$minimum - (int)$minimum_obj->QuantityOrderedByCustomers;
            $candybar_stock = max(0, $candybar_stock);
            update_post_meta( $post->ID, 'in-stock', $candybar_stock );
*/
            //OVERRIDE INVENTORY ON SELECT ITEM CODES USING TERMINATOR OF "BOM" -  KYLE
            //$lstTwo = substr($item_code, -3);
            //if($lstTwo == 'BOM'){
                $candybar_stock = get_post_meta($post->ID, 'stock', true);
                update_post_meta( $post->ID, 'in-stock', empty($candybar_stock) ? 0 : $candybar_stock );
            //}

            echo "{$item_code},{$candybar_stock}\n";

        }
    }
}
catch(Exception $e)
{
    error_log("Error occurred: " . $e->getMessage());
    $dbh = SCModel::getSnackCrateDB();

    $message = $e->getMessage();
    $trace = $e->getTrace();
    $trace = serialize($trace);
    $stmt = $dbh->prepare("INSERT INTO sap_error_log (message, trace) VALUES (:message, :trace)");
    $stmt->bindParam(":message", $message);
    $stmt->bindParam(":trace", $trace);
    $stmt->execute();
    $stmt = null;
}

