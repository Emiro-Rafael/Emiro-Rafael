<?php
/*
$usps_rate = array(
    1 => array(
        1 => 6.9,
        2 => 6.9,
        3 => 7.24,
        4 => 7.36,
        5 => 7.51,
        6 => 7.77,
        7 => 7.87,
        8 => 8.14,
    ),
    2 => array(
        1 => 7.38,
        2 => 7.38,
        3 => 7.74,
        4 => 7.9,
        5 => 8.51,
        6 => 8.87,
        7 => 9.15,
        8 => 9.45,
    ),
    3 => array(
        1 => 7.88,
        2 => 7.88,
        3 => 8,
        4 => 8.15,
        5 => 9.36,
        6 => 10.68,
        7 => 11.41,
        8 => 11.87,
    ),
);
*/

$usps_rate = array(
    1 => array(
        1 => 6.5,
        2 => 6.5,
        3 => 6.82,
        4 => 6.93,
        5 => 7.07,
        6 => 7.32,
        7 => 7.41,
        8 => 7.67,
    ),
    2 => array(
        1 => 6.95,
        2 => 6.95,
        3 => 7.29,
        4 => 7.44,
        5 => 8.02,
        6 => 8.36,
        7 => 8.62,
        8 => 8.9,
    ),
    3 => array(
        1 => 7.42,
        2 => 7.42,
        3 => 7.54,
        4 => 7.68,
        5 => 8.82,
        6 => 10.06,
        7 => 10.75,
        8 => 11.18,
    ),
);

if (($handle = fopen("shipments.csv", "r")) !== FALSE) 
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
    {
        if( $data[0] == 'created_at' )
        {
            $headers = $data;
            continue;
        }
        else
        {
            $shipment = array_combine( $headers, $data );
        }

        $we_paid = $shipment['rate'];

        $zone = $shipment['usps_zone'];

        $volume = $shipment['length'] * $shipment['width'] * $shipment['height']; // cubic inches
        $cubic_feet = $volume / 1728;

        if( $cubic_feet < .1 )
        {
            $tier = 1;
        }   
        elseif( $cubic_feet < .2 )
        {
            $tier = 2;
        }
        else
        {
            $tier = 3;
        }

        $should_paid = $usps_rate[$tier][$zone];

        $created = date('Y-m-d', strtotime($shipment['created_at']));

        $difference = number_format($we_paid - $should_paid, 2);

        $we_paid = number_format($we_paid,2);

        $should_paid = number_format($should_paid,2);

        echo "{$created}, {$shipment['id']}, {$zone}, {$tier}, {$we_paid}, {$should_paid}, {$difference}\n";
    }
}
