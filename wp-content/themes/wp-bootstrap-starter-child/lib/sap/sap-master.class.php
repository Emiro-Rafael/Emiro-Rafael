<?php

class SAPMaster
{
    protected $sap;
    
    protected $customer_service;
    protected $uom_service;
    protected $package_service;
    protected $user_data;

    protected static $warehouses = ['HBG','SSW'];
    protected static $shop_warehouse = 'HBG';
    protected static $crate_warehouse = 'HBG';
    protected static $tax_code = 'Exempt';
    protected static $minimum_item_price = 1.49;
    protected static $system_bin = 'HBG-SYSTEM-BIN-LOCATION';

    public function __construct( $sap_session = null )
    {
        if( empty($sap_session) )
        {
            $this->sap = \SAPb1\SAPClient::createSession();
        }
        else
        {
            $this->sap = $sap_session;
        }

        $this->customer_service = $this->sap->getService("BusinessPartners");
        $this->uom_service = $this->sap->getService("UnitOfMeasurements");
        $this->package_service = $this->sap->getService("PackagesTypes");

        global $user_data;
        $this->user_data = $user_data;
    }

    /**
     * creates new SAP Customer from email
     * returns CardCode
     * only use when you know user already has stripe customer id set
     */
    private function _createNewCustomer($user)
    {
        $create = $this->customer_service->create([
            "CardCode" => empty($user->customer_ID) ? bin2hex(random_bytes(6)) : substr($user->customer_ID, 4),
            "CardType" => 'cCustomer',
            "CardName" => get_user_meta( get_current_user_id(), 'full_name', true ),
            //"AdditionalID" => $customer_id,
            "EmailAddress" => $email,
        ]);
        
        return $create->CardCode;
    }

    /**
     * creates new SAP Customer from email and stripe customer id
     * returns CardCode
     */
    public static function createCustomer($email, $customer_id, $first_name, $last_name, $sap = null)
    {
        if( empty($sap) )
        {
            $sap = \SAPb1\SAPClient::createSession();
        }
        $customer_service = $sap->getService("BusinessPartners");

        try
        {
            $create = $customer_service->create([
                "CardCode" => empty($customer_id) ? bin2hex(random_bytes(6)) : substr($customer_id, 4),
                "CardType" => 'cCustomer',
                "CardName" => "{$first_name} {$last_name}",
                "AdditionalID" => $customer_id,
                "EmailAddress" => $email,
            ]);
            
            return $create->CardCode;
        }
        catch(Exception $e)
        {
            // card code already exists so just return it
            return substr($customer_id, 4);
        }
    }

    /**
     * checks if Customer is already in SAP
     * calls function to create the Customer if not
     * returns CardCode
     */
    protected function _getCustomerCode($user)
    {
        $result = $this->customer_service->queryBuilder()
            ->select("CardCode")
            ->where(new \SAPb1\Filters\Equal("EmailAddress", $user->email))
            ->findAll();

        if(count($result->value) > 0){
            return $result->value[0]->CardCode;
        }
        else
        {
            return $this->_createNewCustomer($user);
        }
    }

    protected function _findCandybarPackageCode()
    {
        $package_service = $this->sap->getService("PackagesTypes");
        $result = $package_service->queryBuilder()
            ->select("Code")
            ->where(new \SAPb1\Filters\Equal("Type", "CandyBar"))
            ->findAll();

        if(count($result->value))
        {
            return $result->value[0]->Code;
        }
        else
        {
            return false;
        }
    }
}