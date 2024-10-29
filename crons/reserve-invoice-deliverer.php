<?php
require_once __DIR__ . "/../wp-load.php";
require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

try
{
    $candybar_warehouse = 'SSW';
    $sap = \SAPb1\SAPClient::createSession();
    $invoice_service = $sap->getService('Invoices');
    $invoice_batch_service = $sap->getService('sml.svc/INVOICE_BATCH');
    $delivery_service = $sap->getService('DeliveryNotes');

    $dbh = SCModel::getSnackCrateDB();
    $stmt = $dbh->prepare("SELECT * 
        FROM candybar_order 
        WHERE `status` = 'fulfilled' 
            AND purchased like '%9443%'
            AND sap_invoice_id IN (SELECT sap_invoice_id FROM candybar_reserve_invoices)
            AND sap_delivery_id IS NULL");
    $stmt->execute();
    $reserves = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;

    foreach($reserves as $order)
    {
        try
        {
            echo "$order->sap_invoice_id, ";


            $invoice_items = $invoice_batch_service->queryBuilder()
                ->select("*")
                ->where( new \SAPb1\Filters\Equal("BaseNum", (int)$order->sap_invoice_id) )
                ->where( new \SAPb1\Filters\Equal("BaseType", 13) )
                ->findAll();

            if(count($invoice_items->value) == 0)
                continue;

            $items = array();

            foreach($invoice_items->value as $doc_line)
            {
                if($doc_line->WhsCode == 'HBG')
                    break;

                if( array_key_exists($doc_line->ItemCode, $items) )
                {
                    $items[$doc_line->ItemCode]["Quantity"] += $doc_line->Quantity;
                    array_push($items[$doc_line->ItemCode]["BatchNumbers"], array(
                        "BatchNumber" => $doc_line->BatchNum,
                        "Quantity" => $doc_line->Quantity
                    ));
                }
                else
                {
                    $document_line = array(
                        "ItemCode" => $doc_line->ItemCode,
                        "Quantity" => $doc_line->Quantity,
                        "WarehouseCode" => $doc_line->WhsCode,
                        "BaseEntry" => (int)$doc_line->BaseEntry,
                        "BaseLine" => (int)$doc_line->BaseLinNum,
                        "TaxCode" => "Exempt",
                        "BaseType" => 13,
                        "BatchNumbers" => array(
                            array(
                                "BatchNumber" => $doc_line->BatchNum,
                                "Quantity" => $doc_line->Quantity,
                            )
                        )
                    );
                    $items[$doc_line->ItemCode] = $document_line;
                }
                
                //array_push($items, $document_line);
            }
	
            $delivery = $delivery_service->create(
                array(
                    "CardCode" => $invoice_items->value[0]->CardCode,
                    "DocumentLines" => array_values($items),
                    "Reference2" => strval($doc_line->BaseEntry), // refer to base document
                    "Comments"=> "Based on Reserve Invoice {$doc_line->BaseNum}.",
                )
            );
            echo "$delivery->DocNum \n";

            $update = $dbh->prepare("UPDATE candybar_order SET sap_delivery_id = :sap_delivery_id WHERE id = :id");
            $update->bindParam(":sap_delivery_id", $delivery->DocNum);
            $update->bindParam(":id", $order->id);
            $update->execute();
            $update = null;

            $delete = $dbh->prepare("DELETE FROM candybar_reserve_invoices WHERE sap_invoice_id = :sap_invoice_id");
            $delete->bindParam(":sap_invoice_id", $order->sap_invoice_id);
            $delete->execute();
            $delete = null;

            //die;
        }
        catch(Exception $e)
        {
            //die('<pre>'.print_r($e,1));
            echo $e->getMessage() . "\n";
        }
    }

}
catch(Exception $e)
{
    die('<pre>'.print_r($e,1));
    echo $e->getMessage() . "\n";
}







function determineBatches($sap, $item_code, $quantity, $mindate)
{   
    $batch_service = $sap->getService("sml.svc/ITEM_BATCH_WAREHOUSE");
    $batches = $batch_service->queryBuilder()
                ->select("*")
                ->where(new \SAPb1\Filters\Equal("ItemCode", $item_code))
                ->where(new \SAPb1\Filters\Equal("WhsCode", 'SSW'))
                ->where(new \SAPb1\Filters\MoreThan("Quantity", 0))
                ->where(new \SAPb1\Filters\MoreThan("IsCommited", 0))
                ->where(new \SAPb1\Filters\MoreThanEqual("ExpDate", $mindate))
                ->orderBy("ExpDate, BatchNum")
                ->findAll();

    $items_needed = $quantity;
    $return = array();
    foreach($batches->value as $batch)
    {
        $items_to_take = min($items_needed, $batch->Quantity - $batch->IsCommited);

        if($items_to_take == 0)
        {
            continue;
        }

        $items_needed -= $items_to_take;

        array_push($return, array(
            "BatchNumber" => $batch->BatchNum,
            "Quantity" => $items_to_take
        ));

        if($items_needed <= 0)
        {
            break;
        }
    }

    return $return;
}


function determineBatchesByBin($sap, $item_code, $quantity, $mindate)
{
    $batch_bin_service = $sap->getService("sml.svc/BIN_BATCH_QUANTITIES");
    $batches = $batch_bin_service->queryBuilder()
                ->select("*")
                ->where(new \SAPb1\Filters\Equal("ItemCode", $item_code))
                ->where(new \SAPb1\Filters\NotEqual("BinCode", 'HBG-SYSTEM-BIN-LOCATION'))
                ->where(new \SAPb1\Filters\Equal("WhsCode", 'HBG'))
                ->where(new \SAPb1\Filters\MoreThan("OnHandQty", 0))
                ->where(new \SAPb1\Filters\MoreThanEqual("ExpDate", $mindate))
                ->orderBy("ExpDate, BatchNumber")
                ->findAll();

    $items_needed = $quantity;
    $return = new stdClass();
    $return->batch_numbers = array();
    $return->bin_allocations = array();

    foreach($batches->value as $batch)
    {
        $items_to_take = min($items_needed, $batch->OnHandQty);

        if($items_to_take == 0)
        {
            continue;
        }

        $items_needed -= $items_to_take;

        array_push(
            $return->bin_allocations, 
            array(
                "SerialAndBatchNumbersBaseLine" => count($return->batch_numbers), // correspond with the batch number that gets added next, zero indexed
                "Quantity" => $items_to_take,
                "BinAbsEntry" => $batch->BinAbs
            )
        );

        array_push(
            $return->batch_numbers, 
            array(
                "BatchNumber" => $batch->BatchNumber,
                "Quantity" => $items_to_take
            )
        );

        if($items_needed <= 0)
        {
            break;
        }
    }

    return $return;
}
