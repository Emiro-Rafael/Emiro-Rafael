<?php

class EasypostHelper
{
    const INSULAR_AREAS_AND_NON_CONTIGUIOUS_STATES = [
        'AE',
        'Armed Forces Europe',
        'AA',
        'Armed Forces Americas',
        'AP',
        'Armed Forces Pacific', 
        'Guam',
        'GU',
        'Palau',
        'PW',
        'Puerto Rico',
        'PR',
        'US Virgin Islands',
        'U.S. Virgin Islands',
        'VI',
        'American Samoa',
        'AS',
        'Marshall Islands',
        'MH',
        'Alaska',
        'AK',
        'Hawaii',
        'HI',
        'Federated States of Micronesia',
        'FM',
        'Northern Mariana Islands',
        'MP',
    ];

    public function __construct()
    {

    }

    public function createParcel($details)
    {
        return \EasyPost\Parcel::create($details);
    }

    public function createAddress($data)
    {
        $result = array
        (
            'name' => $data['shipping_name'],
            'verify' => 'delivery',
            'street1' => $data['address_1'],
            'street2' => $data['address_2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'email' => $data['email']
        );

        $result['phone'] = !empty($data['phone']) ? $data['phone'] : '9999999999';

        return $result;
    }

    private function _getFromAddress()
    {
        return array(
            'company' => 'The Snack Squad',
            'street1' => '890 East Heinberg St.',
            'city' => 'Pensacola',
            'state' => 'FL',
            'zip' => '32502',
            'phone' => '844-427-6225'
        );
    }

    private function _getCarrier($parcel, $state)
    {
        $volume = ($parcel->length * $parcel->width * $parcel->height) / 1728;
        
        if(in_array($state, self::INSULAR_AREAS_AND_NON_CONTIGUIOUS_STATES))
        {
            return 'USPS';
        }

        return 'OSMWorldwide';
        //return 'USPS'; - Makes boxes ship faster for days immediately leading to Christmas. @KyleRoarke
    }

    private function _getServiceLevel( $carrier, $parcel, $state )
    {
        if(in_array($state, self::INSULAR_AREAS_AND_NON_CONTIGUIOUS_STATES) && $carrier == 'USPS')
        {
            return 'Priority';
        } elseif( $parcel->weight >= 16)
        {
            return 'ParcelSelect';
            //return 'Priority'; - Makes boxes ship faster for days immediately leading to Christmas. @KyleRoarke
        }
        else
        {
            switch($carrier)
            {
                case 'OSMWorldwide':
                    //return 'ParcelSelectLightweight';
                    return 'ParcelSelect';
                break;
                
                case 'USPS':
                    return 'First';
                break;
            }
        }
    }

    public function getLabel($shipment_id)
    {
        $label = '';
        while( empty($label) )
        {
            $shipment = \EasyPost\Shipment::retrieve($shipment_id);
            $label = $shipment->postage_label->label_url;
            if( empty($label) )
            {
                sleep(5);
            }
        }
        return $label;
    }

    public function createShipment($to_address, $parcel, $reference, $quantity = 1, $value = 5, $has_cold_pack = false)
    {
        $from_address = $this->_getFromAddress();

        $shipment_arr = array
        (
            'to_address' => $to_address,
            'from_address' => $from_address,
            'parcel' => $parcel,
            'reference' => $reference,
            'options' => array
            (
                'address_validation_level' => 0,
                'label_format' => 'ZPL'
            )
        );

        if(in_array($to_address['state'], self::INSULAR_AREAS_AND_NON_CONTIGUIOUS_STATES))
        {
            $customs_item1 = array(
                "description" => 'Sweets',
                "quantity" => $quantity,
                "weight" => $parcel['weight'],
                "value" => $value,
                "hs_tariff_number" => 180632, 
                "origin_country" => "US"
            );

            $customs_info = array(
                "eel_pfc" => 'NOEEI 30.37(a)',
                "customs_certify" => true,
                "customs_signer" => 'Kyle Roarke',
                "contents_type" => 'merchandise',
                "customs_items" => array($customs_item1)
            );

            $shipment_arr['customs_info'] = $customs_info;
        }

        $shipment = \EasyPost\Shipment::create($shipment_arr);

        $this->_buyShipmentLabel($shipment, $to_address['state'], $value, $has_cold_pack);

        return $shipment;
    }

    private function _buyShipmentLabel($shipment, $state, $value = 5, $has_cold_pack = false)
    {
        $rate = $this->_findRate($shipment, $state, $value, $has_cold_pack);
        try
        {
            $shipment->buy(
                array
                (
                    'rate' => $rate
                )
            );
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    private function _findRate($shipment, $state, $value = 5, $has_cold_pack = false)
    {
        $possible_rates = array_filter(
            $shipment->rates,
            function($rate) use($state, $value)
            {
                if($rate->service == 'Express') return false;
                if($rate->carrier == 'OSMWorldwide') return false;
                
                if(in_array($state, self::INSULAR_AREAS_AND_NON_CONTIGUIOUS_STATES)) {
                    if($has_cold_pack || $value > 69) {
                        return !empty($rate->delivery_days) && $rate->delivery_days <= 3;
                    } else {
                        return true;
                    }
                    //if($value >= 50) {
                    //    return ($rate->carrier == 'USPS' && $rate->service == 'Priority' && $rate->delivery_days <= 3);
                    //} else {
                    //    return ($rate->carrier == 'USPS' && $rate->service == 'Priority');
                    //}
                } else {
                    if($has_cold_pack || $value > 69) {
                        return !empty($rate->delivery_days) && $rate->delivery_days <= 3;
                    } else {
                        return true;
                    }
                }
            }
        );

        if(empty($possible_rates)) {
            $possible_rates = array_filter(
                $shipment->rates,
                function($rate) {
                    if($rate->service == 'Express') return false;
                    if($rate->carrier == 'OSMWorldwide') return false;

                    return true;
                }
            );
        }

        foreach($possible_rates as $rate)
        {
            if( empty($lowest_rate) || $rate->rate < $lowest_rate->rate )
            {
                $lowest_rate = $rate;
            } elseif($rate->rate == $lowest_rate->rate && $rate->delivery_days < $lowest_rate->delivery_days) {
                $lowest_rate = $rate;
            }
        }

        return $lowest_rate;
    }
}