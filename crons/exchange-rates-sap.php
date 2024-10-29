<?php
require_once __DIR__ . "/../wp-load.php";
require_once __DIR__ . "/../../keys.php";
require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

try
{
    // ea065d33d3ed44420d6b9073
    $exchange_url = "https://v6.exchangerate-api.com/v6/" . $_ENV['exchangerate_api_key'] . "/latest/USD";

    $params = $_ENV['sap_params'];

    $config = [
        'https' => true,
        'host' => $_ENV['sap_host'],
        'port' => $_ENV['sap_port'],
        'sslOptions' => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
        'version' => 2
    ];

    print "Connecting to SAP...\n";
    $sap = SAPb1\SAPClient::createSession($config, $params['UserName'], $params['Password'], $params['CompanyDB']);

    print "Fetching SAP session...\n";
    $session = $sap->getSession();

    print "Fetching currencies...\n";
    $customers = $sap->getService("Currencies");
    $currencies = $customers->queryBuilder()
        ->select("Code,DocumentsCode")
        ->findAll();

    $currencies = array_map(function($e){
        if($e->DocumentsCode=='CAN') 
            return 'CAD';
        elseif($e->DocumentsCode=='$') 
            return 'USD';
        return $e->DocumentsCode;
    }, $currencies->value);


    print "Fetching exchange rates... ($exchange_url)\n";
    $rates = json_decode(file_get_contents($exchange_url));

    $rate_date = date('Ymd', strtotime($rates->time_last_update_utc));

    $rates = (array)$rates->conversion_rates;

    $service = $sap->getService("SBOBobService_SetCurrencyRate");
    foreach($rates as $code => $rate)
    {
        if(!in_array($code, $currencies) || $code == "USD") continue;
        
        switch($code)
        {
            case 'CAD':
                $code = 'CAN';
                break;
            case 'JPY':
                $code = 'YEN';
                break;
            default:
                //do nothing
        }

        $data = [
            "Currency"=> $code,
            "Rate"=> $rate,
            "RateDate"=> $rate_date
        ];

        $create = $service->create($data);
    }
    echo 'success';
}
catch(Exception $e)
{
    echo $e->getMessage();
}
