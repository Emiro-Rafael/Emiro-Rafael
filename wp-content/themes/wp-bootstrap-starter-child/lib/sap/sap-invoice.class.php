<?php

class SAPInvoice extends SAPMaster
{
    private $line_items; // array where the keys represent the snack's post id and values are quantity
    private $document_lines; // line_items rearranged in format needed to create invoice
    private $service;

    public function __construct( $sap_session = null )
    {
        parent::__construct($sap_session);
        $this->service = $this->sap->getService("Invoices");
    }

    public function setLineItems($items)
    {
        $this->line_items = $items;
    }

    public function create($email = null, $card_code = null, $is_reserve = false, $reference = null)
    {
        if(!empty($this->line_items))
        {
            if( is_null($email) )
            {
                global $user_data;
                
                if( !empty($user_data) )
                {
                    $email = $user_data->email;
                }
            }
            
            if(!is_null($email))
            {
                $user = new User($email);
                $this->_generateDocumentLines();
                $invoice_document = $this->service->create([
                    "CardCode" => empty($card_code) ? $this->_getCustomerCode($user) : $card_code,
                    "DocDueDate" => date("Y-m-d", strtotime("+1 week")),
                    "ReserveInvoice" => $is_reserve ? 'Y' : 'N',
                    "DocumentLines" => $this->document_lines,
                    "Comments" => $reference
                ]);
                return $invoice_document;
            }
            else
            {
                throw new Exception("Unable to generate sales data.");
            }
        }
        else
        {
            throw new Exception("No items in cart.");
        }
    }

    private function _generateDocumentLines()
    {
        $this->document_lines = array();
        $discount = (get_user_meta( get_current_user_id(), 'has_subscription', true ) === 1) ? 30 : 0;

        foreach($this->line_items as $item_id => $item)
        {
            switch( get_post_type($item_id) )
            {
                case 'snack':
                    $item_code = get_post_meta($item_id, 'internal-id-code', true);
                    $sap_item = new SAPItem($item_code, $this->sap);
        
                    $batches_to_take = $sap_item->determineBatches($item);

                    if( empty($batches_to_take) )
                    {
                        $batches_to_take = $sap_item->determineBatchesByBin($item);

                        
        
                        $document_line = array(
                            "ItemCode" => $item_code,
                            "Quantity" => $item * $sap_item->getSalesUnitsPerPackaging(),
                            "PackageQuantity" => $sap_item->getSalesUnitsPerPackaging(),
                            "DiscountPercent" => $discount,
                            "TaxCode" => self::$tax_code,
                            "WarehouseCode" => self::$shop_warehouse,
                            "UoMEntry" => $sap_item->getSalesUnitCode(),
                            "BatchNumbers" => $batches_to_take->batch_numbers,
                            "DocumentLinesBinAllocations" => $batches_to_take->bin_allocations
                        );
                    }
                    else
                    {   
                        $whs_code = "SSW";
                        if( empty( $sap_item->getWarehouseStock("SSW") ) )
                        {
                            $whs_code = "HBG";
                        }

                        $document_line = array(
                            "ItemCode" => $item_code,
                            "Quantity" => $item * $sap_item->getSalesUnitsPerPackaging(),
                            "PackageQuantity" => $sap_item->getSalesUnitsPerPackaging(),
                            "DiscountPercent" => $discount,
                            "TaxCode" => self::$tax_code,
                            "WarehouseCode" => $whs_code,
                            "UoMEntry" => $sap_item->getSalesUnitCode(),
                            "BatchNumbers" => $batches_to_take
                        );
                    }
                    array_push($this->document_lines, $document_line);
                    break;

                case 'collection':
                case 'country':
                    $sap_country = new SAPCountry($item_id, $this->sap);
                    foreach($item as $size => $quantity)
                    {
                        $item_code = $sap_country->getItemCode($size);
                        $batches_to_take = $sap_country->determineBatches($quantity, $size);

                        $sap_item = new SAPItem($item_code, $this->sap);
                        $whs_code = "SSW";
                        if( empty( $sap_item->getWarehouseStock("SSW") ) )
                        {
                            $whs_code = "HBG";
                        }

                        $document_line = array(
                            "ItemCode" => $item_code,
                            "Quantity" => $quantity,
                            "DiscountPercent" => $discount,
                            "TaxCode" => self::$tax_code,
                            "WarehouseCode" => $whs_code,
                            "UoMEntry" => $sap_country->getSalesUnitCode($size),
                            "BatchNumbers" => $batches_to_take
                        );
                        array_push($this->document_lines, $document_line);
                    }
                    break;
            }
        }
    }

    public function reverseInvoice($invoice_document)
    {
        $credit_notes_service = $this->sap->getService("CreditNotes");

        $document_lines = array_map(
            function($line) use($invoice_document)
            {
                return array
                (
                    "BaseType" => 13,
                    "BaseEntry" => $invoice_document->DocEntry,
                    "BaseLine" => $line->LineNum,
                );
            },
            $invoice_document->DocumentLines
        );
        
        $credit_memo = $credit_notes_service->create(
            array
            (
                "CardCode" => $invoice_document->CardCode,
                "DocumentLines" => $document_lines,
            )
        );

        return $credit_memo;
    }

    public function createDelivery($sap_invoice_id, $shipping_cost)
    {
        $delivery_service = $this->sap->getService("DeliveryNotes");

        $result = $this->service->queryBuilder()
            ->select("DocEntry, DocNum, DocumentLines, CardCode")
            ->where(new \SAPb1\Filters\Equal('DocNum', (int)$sap_invoice_id))
            ->limit(1)
            ->findAll();

        $original_document = $result->value[0];

        $document_lines = $original_document->DocumentLines;

        foreach($original_document->DocumentLines as $key => $line)
        {
            $document_lines[$key]->BaseEntry = $original_document->DocEntry;
            $document_lines[$key]->BaseLine = $key;
            $document_lines[$key]->BaseType = 13;
        }

        $new_delivery = $delivery_service->create(
            array(
                "CardCode" => $original_document->CardCode,
                "Comments"=> "Based on Invoices {$original_document->DocNum}.",
                "DocumentLines" => $document_lines,
                "DocumentAdditionalExpenses" => 
                    array(
                        "ExpenseCode" => 1,
                        "Remarks"=> "Freight Charge",
                        "LineTotal" => $shipping_cost,
                    ),
            )
        );

        return $new_delivery->DocNum;
    }

    public function getFromSAP($sap_invoice_id)
    {
        $invoice_data = $this->service->queryBuilder()
            ->select("*")
            ->where(new \SAPb1\Filters\Equal('DocNum', (int)$sap_invoice_id))
            ->findAll();

        return $invoice_data->value[0];
    }
}

