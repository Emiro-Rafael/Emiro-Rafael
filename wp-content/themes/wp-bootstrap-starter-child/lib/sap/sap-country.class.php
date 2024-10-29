<?php

class SAPCountry extends SAPMaster
{
    private static $mini = '4Snack';
    private static $original = '8Snack';
    private static $family = '16Snack';

    private $country_code;

    public function __construct($country_id, $sap_session = null)
    {
        parent::__construct($sap_session);

        $this->country_code = get_post_meta($country_id, 'country-code', true);
    }

    public function getItemCode($size)
    {
        return $this->country_code . $size;
    }

    public function determineBatches($quantity, $size)
    {
        $item_code = $this->getItemCode($size);
        $crate_item = new SAPItem($item_code, $this->sap);
        return $crate_item->determineBatches($quantity);
    }

    public function getSalesUnitCode($size)
    {
        $item_code = $this->getItemCode($size);
        $crate_item = new SAPItem($item_code, $this->sap);
        return $crate_item->getSalesUnitCode();
    }
}