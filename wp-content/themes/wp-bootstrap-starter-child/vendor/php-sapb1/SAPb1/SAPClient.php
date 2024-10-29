<?php

namespace SAPb1;

/**
 * SAPClient manages access to SAP B1 Service Layer and provides methods to 
 * perform CRUD operations.
 */
class SAPClient{
    
    private $config = [];
    private $session = [];

    /**
     * Initializes SAPClient with configuration and session data.
     */
    public function __construct(array $configOptions, array $session){
        $this->config = new Config($configOptions);
        $this->session = $session;
    }
    
    /**
     * Returns a new instance of SAPb1\Service.
     */
    public function getService(string $serviceName) : Service{
        return new Service($this->config, $this->session, $serviceName);
    }
    
    /**
     * Returns the current SAP B1 session data.
     */
    public function getSession() : array{
        return $this->session;
    }

    /**
     * Returns a new instance of SAPb1\Query, which allows for cross joins.
     */
    public function query($join) : Query{
        return new Query($this->config, $this->session, '$crossjoin('. str_replace(' ', '', $join) . ')');
    }

    /**
     * Creates a new SAP B1 session and returns a new instance of SAPb1\Client.
     * Throws SAPb1\SAPException if an error occurred.
     */
    //public static function createSession(array $configOptions, string $username, string $password, string $company) : SAPClient{
    public static function createSession() : SAPClient {
        $params = $_ENV['sap_params'];
        $configOptions = [
            'https' => true,
            'host' => $_ENV['sap_host'],
            'port' => $_ENV['sap_port'],
            'sslOptions' => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
            'version' => 2
        ];


        $config = new Config($configOptions);

        $request = new Request($config->getServiceUrl('Login'), $config->getSSLOptions());
        $request->setMethod('POST');
        //$request->setPost(['UserName' => $username, 'Password' => $password, 'CompanyDB' => $company]);
        $request->setPost($params);
        $response = $request->getResponse();
        
        if($response->getStatusCode() === 200){
            return new SAPClient($config->toArray(), $response->getCookies());
        }
        
        throw new SAPException($response);
    }
}
