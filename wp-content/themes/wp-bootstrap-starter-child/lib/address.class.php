<?php

class Address extends General
{
    private $id;
    private $data;

    private static $us_states = array
    (
        "AA" => "Armed Forces Americas",
        "AE" => "Armed Forces Europe",
        "AP" => "Armed Forces Pacific", 
        "AL" => "Alabama",
        "AK" => "Alaska",
        "American Samoa" => "American Samoa",
        "AR" => "Arkansas",
        "AZ" => "Arizona",
        "CA" => "California",
        "CO" => "Colorado",
        "CT" => "Connecticut",
        "DC" => "District of Columbia",
        "DE" => "Delaware",
        "FL" => "Florida",
        "GA" => "Georgia",
        "Guam" => "Guam",
        "HI" => "Hawaii",
        "IA" => "Iowa",
        "ID" => "Idaho",
        "IL" => "Illinois",
        "IN" => "Indiana",
        "KS" => "Kansas",
        "KY" => "Kentucky",
        "LA" => "Louisiana",
        "Marshall Islands" => "Marshall Islands",
        "MA" => "Massachusetts",
        "MD" => "Maryland",
        "ME" => "Maine",
        "MI" => "Michigan",
        "MN" => "Minnesota",
        "MO" => "Missouri",
        "MS" => "Mississippi",
        "MT" => "Montana",
        "NC" => "North Carolina",
        "ND" => "North Dakota",
        "Northern Mariana Islands" => "Northern Mariana Islands",
        "NE" => "Nebraska",
        "NH" => "New Hampshire",
        "NJ" => "New Jersey",
        "NM" => "New Mexico",
        "NV" => "Nevada",
        "NY" => "New York",
        "OH" => "Ohio",
        "OK" => "Oklahoma",
        "OR" => "Oregon",
        "Palau" => "Palau",
        "PA" => "Pennsylvania",
        "Puerto Rico" => "Puerto Rico",
        "RI" => "Rhode Island",
        "SC" => "South Carolina",
        "SD" => "South Dakota",
        "TN" => "Tennessee",
        "TX" => "Texas",
        "US Virgin Islands" => "US Virgin Islands",
        "UT" => "Utah",
        "VT" => "Vermont",
        "VA" => "Virginia",
        "WA" => "Washington",
        "WI" => "Wisconsin",
        "WV" => "West Virginia",
        "WY" => "Wyoming"
    );

    public function __construct($id)
    {
        parent::__construct();
        
        $this->id = $id;
        $this->findAddress();
    }

    private function findAddress()
    {
        $stmt = $this->dbh->prepare("SELECT * FROM " . self::$address_table . " WHERE id = :id");
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $this->data = $stmt->fetch(PDO::FETCH_OBJ);

        $stmt = null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAddress()
    {
        return $this->data->address_1;
    }

    public function getSuite()
    {
        return $this->data->address_2;
    }

    public function getCity()
    {
        return $this->data->city;
    }

    public function getState()
    {
        return $this->data->state;
    }

    public function getCountry()
    {
        return $this->data->country;
    }

    public function getZipcode()
    {
        return $this->data->zipcode;
    }

    public function update($data)
    {
        $shipping_name = $data['first_name'] . ' ' . $data['last_name'];
        $stmt = $this->dbh->prepare("UPDATE " . self::$address_table . " 
                                    SET shipping_name = :shipping_name,
                                        address_1 = :address_1,
                                        address_2 = :address_2,
                                        city = :city,
                                        state = :state,
                                        zipcode = :zipcode,
                                        phone = :phone
                                    WHERE id = :id
                                    ");
        $stmt->bindParam(":shipping_name", $shipping_name);
        $stmt->bindParam(":address_1", $data['address_1']);
        $stmt->bindParam(":address_2", $data['address_2']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":state", $data['state']);
        $stmt->bindParam(":zipcode", $data['zip']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":id", $this->id);
        
        $stmt->execute();
        $stmt = null;
    }

    public static function getStates()
    {
        return self::$us_states;
    }

    private static function checkAddressExists($data, $customer_id)
    {
        $dbh = SCModel::getSnackCrateDB();

        $stmt = $dbh->prepare("SELECT id FROM " . self::$address_table . " 
            WHERE address_1 = :address_1
            AND address_2 = :address_2
            AND city = :city
            AND state = :state
            AND country = :country
            AND zipcode = :zipcode
            AND customer_id = :customer_id");
        $stmt->bindParam(":address_1", $data['address_1']);
        $stmt->bindParam(":address_2", $data['address_2']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":state", $data['state']);
        $stmt->bindParam(":country", $data['country']);
        $stmt->bindParam(":zipcode", $data['zip']);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->execute();
        
        $id = $stmt->fetch(PDO::FETCH_COLUMN);
        
        $stmt = null;
        
        return $id;
    }

    public static function addAddress($data, $customer_id, $phone = '', $is_default = 0)
    {
        if( is_object($data) )
        {
            $data = (array)$data;
        }

        $existing_address = self::checkAddressExists($data, $customer_id);

        if( !empty($existing_address) )
        {
            return $existing_address;
        }
        
        $dbh = SCModel::getSnackCrateDB();

        $stmt = $dbh->prepare("INSERT INTO " . self::$address_table . " 
                                (id, shipping_name, address_1, address_2, city, state, country, zipcode, customer_id, phone, is_default)
                                VALUES (NULL, :shipping_name, :address_1, :address_2, :city, :state, :country, :zipcode, :customer_id, :phone, :is_default)");
        $stmt->bindParam(":shipping_name", $data['shipping_name']);
        $stmt->bindParam(":address_1", $data['address_1']);
        $stmt->bindParam(":address_2", $data['address_2']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":state", $data['state']);
        $stmt->bindParam(":country", $data['country']);
        $stmt->bindParam(":zipcode", $data['zip']);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":is_default", $is_default);
        $stmt->execute();
        $stmt = null;

        return $dbh->lastInsertId();
    }
}