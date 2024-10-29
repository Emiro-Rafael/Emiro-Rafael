<?php

class SAPItem extends SAPMaster
{
    private $item_code;
    private $data;
    private $service;
    private $post_id;
    
    private $minimum_price; // minimum price that can be set at item level

    private const GLOBAL_MINIMUM_PRICE = 0.99; // nothing should ever cost less than this
    private const DRINK_MINIMUM_PRICE = 5.99;

    private static $sales_uom = 'iutSales';

    public function __construct( $item_code, $sap_session = null )
    {
        parent::__construct($sap_session);

        $this->item_code = $item_code;
        $this->service = $this->sap->getService("Items");
        $this->data = $this->service->queryBuilder()
                        ->select("*")
                        ->find($this->item_code);

        $this->_findPostId();
    }

    private function _findPostId()
    {
        $args = array(
            'post_type'   => 'snack',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query'  => array(
                array(
                    'key'   => 'internal-id-code',
                    'value' => $this->item_code
                )
            )
        );
        $posts = get_posts( $args );
        $this->post_id = $posts[0]->ID;
        $this->minimum_price = get_post_meta( $this->post_id, 'minimum-price', true );

        if( empty($this->minimum_price) )
            $this->minimum_price = 0;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getWarehouseStock($warehouse)
    {
        return current(
            array_filter(
                $this->data->ItemWarehouseInfoCollection,
                function($warehouse_collection) use($warehouse)
                {
                    return $warehouse_collection->WarehouseCode == $warehouse;
                }
            )
        )->InStock;
    }

    public function getItemPrice( $get_member_price = false )
    {
        $base_purchase_price = $this->_findBasePurchasePrice();

        $base_quantity = $this->_findPurchaseUnitBaseQuantity();

        $single_unit_cost = round(($base_purchase_price / $base_quantity), 2);

        $exchange_rate = $this->_getExchangeRate();

        $qty_per_sale = empty($this->data->SalesQtyPerPackUnit) ? 1 : $this->data->SalesQtyPerPackUnit;

        $usd_price = number_format( $qty_per_sale * $single_unit_cost / $exchange_rate, 2 );

        $member_price = $this->_calculateItemMemberPrice($usd_price);
        
        $types = array_map(
            function($type)
            {
                return $type->slug;
            },
            get_the_terms($this->post_id,'snack_types')
        );

        if( in_array('drinks', $types) )
        {
            $member_price = max( $member_price, $this->minimum_price, self::DRINK_MINIMUM_PRICE );
        }
        else
        {
            $member_price = max( $member_price, $this->minimum_price, self::GLOBAL_MINIMUM_PRICE );
        }

        if($get_member_price)
        {
            return $member_price;
        }
        else 
        {
            $sales_price = $this->_priceRounder( $member_price * 1.3 );
            return $sales_price;
        }
    }

    private function _getExchangeRate()
    {
        $bp_service = $this->sap->getService("BusinessPartners");
        $currency_obj = $bp_service->queryBuilder()
                        ->select('Currency')
                        ->find($this->data->Mainsupplier);

        $currency = $currency_obj->Currency;

        if($currency == '$')
        {
            return 1;
        }

        $exchange_rate_service = $this->sap->getService('SBOBobService_GetCurrencyRate');

        $data = array
        (
            "Currency" => $currency,
            "Date" => date("Ymd",strtotime("-1 days")),
        );
        $exchange_rate = $exchange_rate_service->create($data);

        return $exchange_rate;
    }

    public function getItemStock()
    {
        return $this->data->QuantityOnStock;

        /*
        return current(array_filter(
            $this->data->ItemWarehouseInfoCollection,
            function($warehouse)
            {
                return $warehouse->WarehouseCode == self::$shop_warehouse;
            }
        ))->InStock;
        */
    }

    public function getItemSku()
    {
        return $this->data->SerialNum;
    }

    private function _priceRounder($price)
    {
        $dollar_amt = floor($price);
        $cent_amt = 100 * ($price - $dollar_amt);
        
        if($cent_amt <= 9)
        {
            return $dollar_amt - .01;
        }
        elseif($cent_amt <= 19)
        {
            return $dollar_amt + .19;
        }
        else
        {
            $new_cent_amt = (round(($cent_amt) / 10) * 10) - 1;
            return $dollar_amt + ($new_cent_amt / 100);
        }
    }

    private function _calculateItemMemberPrice($cost)
    {
        $double_cost = ($cost * 2) + 0.1; // double price plus 10 cents
        $final_price = $this->_priceRounder( $double_cost );
        return $final_price;
    }

    private function _calculateItemPrice($cost)
    {
        $double_cost = 1.3 * (($cost * 2) + 0.1); // double price plus 10 cents
        $final_price = $this->_priceRounder( $double_cost );
        return $final_price;
    }

    private function _getCaseUomId()
    {
        $uom_service = $this->sap->getService('UnitOfMeasurements');
        $result = $uom_service->queryBuilder()
            ->select("AbsEntry")
            ->where(new \SAPb1\Filters\Equal("Code", "Case"))
            ->limit(1)
            ->findAll();

        return $result->value[0]->AbsEntry;
    }

    private function _getUnitUomId( $uom_entry_name = "Unit" )
    {
        $uom_service = $this->sap->getService('UnitOfMeasurements');
        $result = $uom_service->queryBuilder()
            ->select("AbsEntry")
            ->where(new \SAPb1\Filters\Equal("Code", $uom_entry_name))
            ->limit(1)
            ->findAll();

        return $result->value[0]->AbsEntry;
    }

    private function _findPurchaseUnitBaseQuantity()
    {
        if( empty($this->data->DefaultPurchasingUoMEntry) )
        {
            $purchasing_uom_entry = $this->_getCaseUomId();
        }
        else
        {
            $purchasing_uom_entry = $this->data->DefaultPurchasingUoMEntry;
        }

        $uomg_service = $this->sap->getService("UnitOfMeasurementGroups");

        $result = $uomg_service->queryBuilder()
            ->select("UoMGroupDefinitionCollection")
            ->find($this->data->UoMGroupEntry);
        
        $purchasing_data = current(
            array_filter(
                $result->UoMGroupDefinitionCollection,
                function($element) use($purchasing_uom_entry)
                {
                    return $element->AlternateUoM == $purchasing_uom_entry;
                }
            )
        );
        
        return $purchasing_data->BaseQuantity;
    }

    private function _findBasePurchasePrice()
    {
        $pricing_unit = $this->data->PricingUnit;

        $relevant_price_object = current(
            array_filter(
                $this->data->ItemPrices,
                function($price) use($pricing_unit)
                {
                    return $price->Price > 0 && $price->Factor == 1;
                }
            )
        );

        return $relevant_price_object->Price;
    }

    public function getSalesUnitCode()
    {
        if(is_null($this->data->DefaultSalesUoMEntry))
        {
            $uom_entry_name = empty($this->data->InventoryUOM) ? "Unit" : $this->data->InventoryUOM;
            return $this->_getUnitUomId( $uom_entry_name );
        }
        else
        {
            return $this->data->DefaultSalesUoMEntry;
        }
    }

    /**
     * int $quantity
     * determine the batch numbers to take for a pending sale. Prioritizes batches which will expire sooner
     * returns array
     */
    public function determineBatches($quantity)
    {
        $mindate = strftime('%Y-%m-%d',strtotime("+7 day"));
        
        $batch_service = $this->sap->getService("sml.svc/ITEM_BATCH_WAREHOUSE");
        $batches = $batch_service->queryBuilder()
                    ->select("*")
                    ->where(new \SAPb1\Filters\Equal("ItemCode", $this->item_code))
                    ->where(new \SAPb1\Filters\Equal("WhsCode", 'SSW'))
                    ->where(new \SAPb1\Filters\MoreThan("Quantity", 0))
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

    public function determineBatchesByBin($quantity)
    {
        $mindate = strftime('%Y-%m-%d',strtotime("+7 day"));
        
        $batch_bin_service = $this->sap->getService("sml.svc/BIN_BATCH_QUANTITIES");
        $batches = $batch_bin_service->queryBuilder()
                    ->select("*")
                    ->where(new \SAPb1\Filters\Equal("ItemCode", $this->item_code))
                    ->where(new \SAPb1\Filters\NotEqual("BinCode", self::$system_bin))
                    ->where(new \SAPb1\Filters\Equal("WhsCode", self::$shop_warehouse))
                    ->where(new \SAPb1\Filters\MoreThan("OnHandQty", 0))
                    ->where(new \SAPb1\Filters\MoreThanEqual("ExpDate", $mindate))
                    ->orderBy("ExpDate, BatchNumber")
                    ->findAll();

        $items_needed = $quantity * $this->getSalesUnitsPerPackaging();
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

    public function getSalesUnits()
    {
        $sales_unit_collection = array_filter(
            $this->data->ItemUnitOfMeasurementCollection,
            function ($uom)
            {
                return $uom->UoMType == self::$sales_uom;
            }
        );

        $sales_unit_collection_ids = array_map(
            function ($sales_unit)
            {
                return $sales_unit->UoMEntry;
            },
            $sales_unit_collection
        );

        $uom_groups = $this->uom_service->queryBuilder()
                        ->select('AbsEntry, Name')
                        ->where(new \SAPb1\Filters\InArray("AbsEntry", $sales_unit_collection_ids))
                        ->findAll();

        $uoms = array_combine(
            array_map(
                function ($uom_group)
                {
                    return $uom_group->AbsEntry;
                },
                $uom_groups->value
            ),
            array_map(
                function ($uom_group)
                {
                    return $uom_group->Name;
                },
                $uom_groups->value
            )
        );

        return $uoms;
    }

    public function updateSalesUnits($post_data)
    {
        $update = $this->service->update(
            $this->item_code,
            array(
                'DefaultSalesUoMEntry' => $post_data['sales-uom']
            )
        );
    }

    public function getSalesUnitsPerPackaging()
    {
        return empty($this->data->SalesQtyPerPackUnit) ? 1 : $this->data->SalesQtyPerPackUnit;
    }

    public function updateUnitsPerPackage($post_data)
    {
        $package_code = $this->_findCandybarPackageCode();

        $update = $this->service->update(
            $this->item_code,
            array(
                'SalesPackagingUnit' => 'CandyBar',
                'SalesQtyPerPackUnit' => $post_data['package-size'],
            )
        );
    }
}
