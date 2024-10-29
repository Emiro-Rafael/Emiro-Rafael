<?php

class Tax
{
    private $zipcode;
    private $request_url;
    private static $base_url = 'http://api.zip-tax.com/request/v40?key=XUMF9R948WDQ&postalcode=';

    public function __construct()
    {

    }

    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        $this->request_url = self::$base_url . $zipcode;
    }

    public function requestTaxRate()
    {
        try
        {
            if(empty($this->zipcode))
            {
                throw new Exception("Please set a valid zipcode to retrieve a tax rate.");
            }
            $result = file_get_contents($this->request_url);
            $r = json_decode($result);
            return $r;
        }
        catch(Exception $e)
        {
            return null;
        }
    }
}