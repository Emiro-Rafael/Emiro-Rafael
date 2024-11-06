<?php

class ZebraPrint
{
    private static $instance = null;
    
    private $zebraprint_url = 'https://api.zebra.com/v2/devices/printers/send';

    private $zebraprint_key;
    
    private $zebraprint_tenant;
    

    function __construct() 
    {
        $this->zebraprint_key = $_ENV['zebraprint_key'];
        $this->zebraprint_tenant = $_ENV['zebraprint_tenant'];
    }

    function sendFileToPrinter($printerId, $fileUrl)
    {
        $data = file_get_contents($fileUrl);

        $filename = basename($fileUrl);

        $tmph = tmpfile();
        fwrite($tmph, $data);
        $tmpf = stream_get_meta_data($tmph)['uri'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->zebraprint_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
            'Content-Type: multipart/form-data',
            'Origin: https://developer.zebra.com',
            'Referer: https://developer.zebra.com/',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-site',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'accept: text/plain',
            'apikey: ' . $this->zebraprint_key,
            'sec-ch-ua: "Google Chrome";v="119", "Chromium";v="119", "Not?A_Brand";v="24"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"',
            'tenant: ' . $this->zebraprint_tenant,
            'Accept-Encoding: gzip',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'sn' => $printerId,
            'zpl_file' => curl_file_create($tmpf, 'application/octet-stream', $filename),
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        fclose($tmph);
        unset($tmph, $tmpf);

        $json = json_decode($response);

        return $json;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new ZebraPrint();
        }

        return self::$instance;
    }
}
