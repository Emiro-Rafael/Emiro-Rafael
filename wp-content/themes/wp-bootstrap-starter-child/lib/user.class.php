<?php

class User extends General
{
    private $email;
    private $has_subscription;
    private $data;
    private $address_data;
    private $payments;

    // db fields to save to cookie for reference
    private static $cookie_fields = array(
        'id',
        'email',
        'sessionToken',
        'firstname',
        'lastname',
        'customer_ID'
    );

    public function __construct($email)
    {
        parent::__construct();

        $this->email = $email;
        $this->_checkForAccount();
    }

    public function addCustomUserMeta($meta_key, $meta_value)
    {
        $user_id = get_user_by( 'user_login', $this->email )->ID;
        if( empty($user_id) )
        {
            return false;
        }
        return add_user_meta($user_id, $meta_key, $meta_value);
    }

    private function setHasSubscription($count)
    {
        if($count > 0)
        {
            $this->has_subscription = 1;
        }
        else
        {
            $this->has_subscription = 0;
        }
    }

    private function setUserData($data)
    {
        $this->data = $data;
    }

    private function _checkForAccount()
    {
        $stmt = $this->dbh->prepare('SELECT * FROM `'.self::$user_table.'` WHERE email = :email');
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $this->setUserData($stmt->fetch(PDO::FETCH_OBJ));
    }

    private function _randomPassword($length = 16){
        //A list of characters that can be used in our
        //random password.
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()$';
        //Create a blank string.
        $password = '';
        //Get the index of the last character in our $characters string.
        $characterListLength = mb_strlen($characters, '8bit') - 1;
        //Loop from 1 to the $length that was specified.
        foreach(range(1, $length) as $i){
            $password .= $characters[random_int(0, $characterListLength)];
        }
        return $password;
    }

    private function _addAccount($pwd = null)
    {
        if( empty($pwd) )
        {
            $pwd = $this->_randomPassword();
        }
        return wp_create_user($this->email, $pwd, $this->email);
    }

    private function _checkForSubscription()
    {
        if(!empty($this->data->customer_ID))
        {
            $stripe_customer = \Stripe\Customer::retrieve( $this->data->customer_ID );
            $subscription_count = count($stripe_customer->subscriptions->data);
            $this->setHasSubscription($subscription_count);
        }
        else
        {
            $this->setHasSubscription(0);
        }
    }

    private function _saltPassword($password){
		//>Send raw password here and it will return a hashed password that you can store
		//password is a max of 72 characters I believe
		//>default encryption will always choose the latest method. current is bcrypt. salt is latest method.
		//http://php.net/manual/en/function.password-hash.php
		return password_hash($password, PASSWORD_DEFAULT);
	}

    private function _checkGuestTable()
    {
        $stmt = $this->dbh->prepare("SELECT `user_id` FROM " . self::$guest_table . " WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getWordpressUserId()
    {
        $wp_user = get_user_by('email', $this->email);
        
        if( !$wp_user )
        {
            $wp_user_id = $this->_addAccount();
            return $wp_user_id;
        }

        return $wp_user->ID;
    }

    public function login($options)
    {
        if(!email_exists($this->email))
        {
            $check = get_user_by( 'user_login', $this->email );
            if( $check !== false )
            {
                $user_id = $check->ID;
            }
            else
            {
                $user_id = $this->_addAccount($options['pwd']);
            }
        }
        else
        {
            $user_id = get_user_by('email', $this->email)->ID;
        }

        if( is_wp_error($user_id) )
        {
            throw new Exception($user_id->get_error_message());
        }

        $this->_checkForSubscription();

        update_user_meta( $user_id, 'has_subscription', $this->has_subscription );

        update_user_meta( $user_id, 'full_name', $this->data->firstname . ' ' . $this->data->lastname );
        update_user_meta( $user_id, 'first_name', $this->data->firstname );

        if(!empty($this->data->customer_ID))
            update_user_meta( $user_id, 'stripe_customer_id', $this->data->customer_ID );

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    }

    public function setPaymentsInfo()
    {
        $this->payments = array();
        if(!empty($this->data->customer_ID))
        {
            $stripe_customer = \Stripe\Customer::retrieve( $this->data->customer_ID );
            
            foreach($stripe_customer->sources->data as $source)
            {
                $card = $source;
                $card->is_default = ($card->id === $stripe_customer->default_source);
                $card->exp_month = str_pad($card->exp_month, 2, "0", STR_PAD_LEFT);

                array_push($this->payments, $card);
            }
        }
    }

    public function getPaymentsInfo()
    {
        return $this->payments;
    }

    public function getDefaultPaymentId()
    {
        if( !empty($this->data->customer_ID) )
        {
            $stripe_customer = $this->stripe->customers->retrieve(
                $this->data->customer_ID,
                []
            );

            return $stripe_customer->default_source;
        }
        return '';
    }

    public function setAddressData()
    {
        $stmt = $this->dbh->prepare("SELECT *, zipcode as zip FROM " . self::$address_table . " WHERE customer_id = :customer_id ORDER BY is_default DESC LIMIT 1");
        $stmt->bindParam(":customer_id", $this->data->customer_ID);
        $stmt->execute();
        $address_data = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;
        
        if( empty($address_data) )
        {
            if(!empty($this->data->customer_ID))
            {
                $customer_obj = $this->stripe->customers->retrieve(
                    $this->data->customer_ID,
                    []
                );
        
                $customer_info = $customer_obj->cards->data[0];
            }

            if( !empty($customer_info) && !empty($customer_info->address_line1) )
            {
                //check stripe first
                $this->address_data = new stdClass();
                $this->address_data->shipping_name = htmlspecialchars($customer_info->name);
                $this->address_data->address_1 = htmlspecialchars($customer_info->address_line1);
                $this->address_data->address_2 = htmlspecialchars($customer_info->address_line2);
                $this->address_data->city = htmlspecialchars($customer_info->address_city);
                $this->address_data->state = htmlspecialchars($customer_info->address_state);
                $this->address_data->zip = htmlspecialchars($customer_info->address_zip);
                $this->address_data->country = htmlspecialchars($customer_info->address_country);
            }
            else
            {
                $stmt = $this->dbh->prepare("SELECT Shipping_Name as shipping_name, email, `Address` as address_1, suite as address_2, City as city, `State` as state, Zip as zip, Country as country FROM " . self::$order_history_table . " WHERE email = :email AND Address != '' ORDER BY Order_Date DESC LIMIT 1");
                $stmt->bindParam(":email", $this->email);
                $stmt->execute();
        
                $address_data = $stmt->fetch(PDO::FETCH_OBJ);
                $stmt = null;

                $this->address_data = $address_data;
            }
        }
        else
        {
            $this->address_data = $address_data;
        }
    }

    public function checkCredentials($pwd)
    {
        //>send user input password, and password from database
        //run hash from db across password
        //returns true/false
        return password_verify($pwd, $this->data->password);
    }

    public function getHasSubscription()
    {
        return $this->has_subscription;
    }

    public function getUserData()
    {
        return $this->data;
    }

    public function getStripeCustomerId()
    {
        return $this->data->customer_ID;
    }

    public function getAddressData()
    {
        return $this->address_data;
    }

    public function isStripeCustomer()
    {
        return !empty($this->data->customer_ID);
    }

    public function addToUsers($data, $pwd)
    {
        $customer_id = !empty($data['customer_id']) ? $data['customer_id'] : NULL;
        $password = $this->_saltPassword($pwd);
        $marketing_unsubscribe = $_SESSION['checkout']['user_info']['optin'] == 0 ? 1 : 0; // do the opposite...
        $stmt = $this->dbh->prepare("INSERT INTO " . self::$user_table . " (email, signupMethod, customer_ID, firstname, lastname, password, unsubscribe_marketing) VALUES (:email, 'CandyBar', :customer_id, :first_name, :last_name, :pwd, :unsubscribe_marketing)");
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":first_name", $_SESSION['checkout']['user_info']['first_name']);
        $stmt->bindParam(":last_name", $_SESSION['checkout']['user_info']['last_name']);
        $stmt->bindParam(":pwd", $password);
        $stmt->bindParam(":unsubscribe_marketing", $marketing_unsubscribe);
        $stmt->execute();
        $stmt = null;

        $wp_user_id = $this->_addAccount($pwd);
        update_user_meta( $wp_user_id, 'first_name', $_SESSION['checkout']['user_info']['first_name'] );

        $new_user_id = $this->dbh->lastInsertId();

        $guest_ids = $this->_checkGuestTable();

        if( !empty($guest_ids) )
        {
            foreach($guest_ids as $guest_id)
            {
                $stmt = $this->dbh->prepare("UPDATE " . self::$candybar_order_table . " SET user_id = :user_id WHERE user_id = :guest_id");
                $stmt->bindParam(":user_id", $new_user_id);
                $stmt->bindParam(":guest_id", $guest_id);
                $stmt->execute();
                $stmt = null;

                // not sure if we want to delete past guest entries when a user creates an account or not...
                /*
                $stmt = $this->dbh->prepare("DELETE FROM " . self::$guest_table . " WHERE user_id = :guest_id");
                $stmt->bindParam(":guest_id", $guest_id);
                $stmt->execute();
                $stmt = null;
                */
            }
        }

        $stmt = $this->dbh->prepare("INSERT INTO " . self::$notification_table . " (id, user_id, email, text, marketing_email) VALUES (NULL, :user_id, 1, 1, :optin)");
        $stmt->bindParam(":user_id", $new_user_id);
        $stmt->bindParam(":optin", $_SESSION['checkout']['user_info']['optin']);
        $stmt->execute();
        $stmt = null;

        return $new_user_id;
    }

    public function createStripeCustomer()
    {
        $new_customer = $this->stripe->customers->create([
            'email' => $this->email,
        ]);

        $user_id = get_user_by_email( $this->email )->ID;

        update_user_meta( $user_id, 'stripe_customer_id', $new_customer->id );

        return $new_customer->id;
    }

    private function _checkUnsubscribedUser()
    {
        return empty($this->data);
    }

    public function addToGuests( $customer_id )
    {

        /*
        // this would rectify a user account if they started signing up in the past but didn't finish, instead of adding to the guest user table. 
        // Right now we're just going to add to guest_user
        if(!$this->_checkUnsubscribedUser())
        {
            $stmt = $this->dbh->prepare("UPDATE " . self::$user_table . " SET customer_ID = :customer_id WHERE id = :id");
            $stmt->bindParam(":customer_id", $customer_id);
            $stmt->bindParam(":id", $this->data->id);
            $stmt->execute();
            $stmt = null;
            return $this->data->id;
        }
        */

        $address = $_SESSION['checkout']['address']['shipping'];

        $serialized_address = serialize( $address );

        $marketing_optin = empty($_SESSION['checkout']['user_info']['optin']) ? 0 : $_SESSION['checkout']['user_info']['optin'];

        if( empty($address['country']) )
        {
            $address['country'] = 'United States of America';
        }
        $stmt = $this->dbh->prepare("INSERT INTO " . self::$guest_table . " (email, first_name, last_name, address, country, stripe_customer_id, marketing_optin)
                                    VALUES (:email, :first_name, :last_name, :address, :country, :stripe_customer_id, :marketing_optin)");
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":first_name", $_SESSION['checkout']['user_info']['first_name']);
        $stmt->bindParam(":last_name", $_SESSION['checkout']['user_info']['last_name']);
        $stmt->bindParam(":address", $serialized_address);
        $stmt->bindParam(":country", $address['country']);
        $stmt->bindParam(":stripe_customer_id", $customer_id);
        $stmt->bindParam(":marketing_optin", $marketing_optin);
        $stmt->execute();
        $stmt = null;

        $last_insert_id = $this->dbh->lastInsertId();
        $new_user_id = "g{$last_insert_id}";
        $stmt = $this->dbh->prepare("UPDATE " . self::$guest_table . " SET user_id = :user_id WHERE id = :id");
        $stmt->bindParam(":id", $last_insert_id);
        $stmt->bindParam(":user_id", $new_user_id);
        $stmt->execute();
        $stmt = null;

        return $new_user_id;
    }

    public function updatePaymentMethod( $data )
    {
        if(!empty($this->data->customer_ID))
        {
            $card_address = empty( $_SESSION['checkout']['address']['billing'] ) ? $_SESSION['checkout']['address']['shipping'] : $_SESSION['checkout']['address']['billing'];
            
            if( !empty($data['card_id']) && $data['card_id'] !== "0" )
            {
                $delete_response = $this->stripe->customers->deleteSource(
                    $this->data->customer_ID, 
                    $data['card_id'],
                    []
                );
            }
            
            try
            {
                $new_card_response = $this->stripe->customers->createSource(
                    $this->data->customer_ID,
                    array
                    (
                        'source' => $data['token'],
                    )
                );

                $new_card_response->name = $data['name'];
                $new_card_response->address_city =  $card_address['city'];
                $new_card_response->address_country = $card_address['country'];
                $new_card_response->address_line1 = $card_address['address_1'];
                $new_card_response->address_line2 = empty($card_address['address_2']) ? null : $card_address['address_2'];
                $new_card_response->address_state = $card_address['state'];
                $new_card_response->address_zip = $card_address['zip'];

                $new_card_response->save();
                
                return $new_card_response->id;
            }
            catch(Stripe_CardError $e)
            {
                // the card has been declined
                throw new Exception ($e->getMessage());
            }
        }
        else
        {
            throw new Exception('No Stripe Customer found.');
        }
    }

    public function optInAccountBeta()
    {
        $stmt = $this->dbh->prepare( "UPDATE " . self::$user_table . " SET account_beta = 1 WHERE email = :email");
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAllAddresses( $default_address_id = null )
    {
        $stmt = $this->dbh->prepare('SELECT * FROM `Address` WHERE customer_id = :customer_id');
        $stmt->bindParam(':customer_id', $this->data->customer_ID);
        $stmt->execute();
        $addresses = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = null;

        if( !empty($default_address_id) )
        {
            // move default to beginning of array
            $default_address = array_filter(
                $addresses,
                function( $address ) use( $default_address_id )
                {
                    return $address->id == $default_address_id;
                }
            );
            
            $other_addresses = array_filter(
                $addresses,
                function( $address ) use( $default_address_id )
                {
                    return $address->id != $default_address_id;
                }
            );

            $addresses = array_merge( $default_address, $other_addresses );
        }

        return $addresses;
    }

    public function marketingOptin( $optin = 1 )
    {
        if( !empty($this->data->id) )
        {
            $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM " . self::$notification_table . " WHERE user_id = :user_id");
            $stmt->bindParam(":user_id", $this->data->id);
            $stmt->execute();

            $count = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt = null;

            if( $count >= 1 )
            {
                $stmt = $this->dbh->prepare("UPDATE " . self::$notification_table . " SET marketing_email = :optin WHERE user_id = :user_id");
                $stmt->bindParam(":user_id", $this->data->id);
                $stmt->bindParam(":optin", $optin);
                $stmt->execute();
                $stmt = null;
            }
            else
            {
                $stmt = $this->dbh->prepare("INSERT INTO " . self::$notification_table . " (id, user_id, email, text, marketing_email) VALUES (NULL, :user_id, 1, 1, :optin)");
                $stmt->bindParam(":user_id", $this->data->id);
                $stmt->bindParam(":optin", $optin);
                $stmt->execute();
                $stmt = null;
            }
        }
    }

    public function getUserNotificationPreferences()
    {
        $stmt = $this->dbh->prepare("SELECT * FROM " . self::$notification_table . " WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $this->data->id);
        $stmt->execute();

        $preferences = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;

        return $preferences;
    }

    private static function setCookieData($user, $support = false)
    {
        $cookie_info = array();
        foreach(self::$cookie_fields as $field)
        {
            $cookie_info[$field] = $user->{$field};
        }

        if( $support )
        {
            $cookie_info['impersonation_mode'] = 1;
        }

        $new_token = bin2hex(openssl_random_pseudo_bytes(20));
        $cookie_info['sessionToken'] = $new_token;

        $dbh = SCModel::getSnackCrateDB();
        $stmt = $dbh->prepare("UPDATE `" . self::$user_table . "` SET sessionToken = :token WHERE email = :email");
        $stmt->bindParam(":token", $new_token);
        $stmt->bindParam(":email", $data['email']);
        $stmt->execute();
        $stmt = null;

        setcookie( 'snackcrate_user', json_encode($cookie_info), time() + 3600, "/", SCModel::getDomain() ); // set to last 1 hour

        return $cookie_info;
    }

    private static function matchPassword($password, $hashfromdb)
    {
        return password_verify($password, $hashfromdb);
    }

    public function accountLogin($data)
    {
        self::logout(); // log out user, they shouldn't be here if they were already logged in however

        $stmt = $this->dbh->prepare("SELECT * FROM `" . self::$user_table . "` WHERE email = :email");
        $stmt->bindParam(':email', $data['email']);
		$stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;

        if($user === false)
        {
            throw new Exception("User not found.");
        }

        switch($data['method'])
        {
            case 'AppleID':
                $confirmed = true; // if we make it here, apple has verified the user's identity, and we know the user exists
                break;

            default:
                if(!empty($user->password))
                {
                    $confirmed = self::matchPassword($data['password'], $user->password);
                }
        }

        if(!$confirmed)
        {
            throw new Exception("Invalid Username or Password");
        }

        if(empty($user->customer_ID))
        {
            throw new Exception("No subscription.");
        }

        return self::setCookieData($user);
    }

    public static function getUserById( $user_id )
    {
        $dbh = self::_getDbh();

        if( substr($user_id, 0, 1) == 'g')
        {
            $stmt = $dbh->prepare("SELECT email, stripe_customer_id as customer_id, first_name, last_name FROM " . self::$guest_table . " WHERE user_id = :user_id");
        }
        else
        {
            $stmt = $dbh->prepare("SELECT email, customer_ID as customer_id, firstname as first_name, lastname as last_name FROM " . self::$user_table . " WHERE id = :user_id");
        }
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt = null;
        return $data;
    }

    public static function logout()
    {
        unset($_COOKIE['snackcrate_user']);
        setcookie( 'snackcrate_user', "", -1, "/", SCModel::getDomain() ); // unset
        setcookie( 'snackcrate_user', "", time(), "/", SCModel::getDomain() ); // unset

        unset($_SESSION['csrf_token']);
    }

    public static function checkLoggedIn()
    {
        return isset($_COOKIE['snackcrate_user']);
    }

    public static function checkHasSubscription()
    {
        if( self::checkLoggedIn() )
        {
            $subscription_count = get_user_meta( get_current_user_id(), 'has_subscription', true );
            if($subscription_count >= 1) return true;
        }

        return false;
    }

    public static function getSnackCrateUserData()
    {
        if( empty($_COOKIE['snackcrate_user']) )
        {
            return null;
        }
        else
        {
            return json_decode( stripslashes($_COOKIE['snackcrate_user']) );
        }
    }

    public static function getDrinklessSubscriptionData()
    {
        if( self::checkLoggedIn() )
        {
            global $user_data;

            $dbh = self::_getDbh();

            $stmt = $dbh->prepare("SELECT * FROM Subscriptions WHERE RIGHT(plan, 1) != 'W' AND customer_id = :customer_id AND is_active = 1 ORDER BY created_at ASC LIMIT 1");
            $stmt->bindParam(":customer_id", $user_data->customer_ID);
            $stmt->execute();

            $subscription = $stmt->fetch(PDO::FETCH_OBJ);

            $stmt = null;

            return $subscription;
        }

        return false;
    }

    public static function getCurrencyData()
    {
        if( self::checkLoggedIn() )
        {
            global $user_data;

            $directus = SCModel::getDirectus();

            $stripe_customer = \Stripe\Customer::retrieve( $user_data->customer_ID );

            $stmt = $directus->prepare("SELECT unit, drink_cost, currency_code FROM currencies WHERE currency_code = :currency_code LIMIT 1");
            $stmt->bindParam(":currency_code", $stripe_customer->currency);
            $stmt->execute();
            
            $return_data = $stmt->fetch(PDO::FETCH_OBJ);

            return $return_data;
        }

        return false;
    }

    public static function addDrinkToSubscription( $data )
    {
        $directus = SCModel::getDirectus();
        $dbh = self::_getDbh();

        $stmt = $dbh->prepare("SELECT term FROM Subscriptions WHERE id = :id");
        $stmt->bindParam(":id", $data['subscription_id']);
        $stmt->execute();
        $term = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;

        // get Stripe plan name that will be removed
        $stmt = $directus->prepare('SELECT end_plan, one_month_plan, six_month_plan, twelve_month_plan FROM `plan_funnel` WHERE base_plan = :base_plan AND drink = 0 AND currency = (SELECT code FROM `currencies` WHERE currency_code = :currency_code LIMIT 1)');
        $stmt->bindParam(":base_plan", $data['old_plan']);
        $stmt->bindParam(":currency_code", $data['currency_code']);
        $stmt->execute();

        $no_drink_plan = $stmt->fetch(PDO::FETCH_OBJ);

        $stmt = null;

        // get Stripe plan name that will be replacing the old one
        $stmt = $directus->prepare('SELECT end_plan, one_month_plan, six_month_plan, twelve_month_plan FROM `plan_funnel` WHERE base_plan = :base_plan AND drink = 1 AND currency = (SELECT code FROM `currencies` WHERE currency_code = :currency_code LIMIT 1)');
        $stmt->bindParam(":base_plan", $data['old_plan']);
        $stmt->bindParam(":currency_code", $data['currency_code']);
        $stmt->execute();

        $drink_plan = $stmt->fetch(PDO::FETCH_OBJ);

        $stmt = null;

        // send update to stripe
        $stripe_helper = new StripeHelper();
        $stripe_helper->updateSubscription( $data['external_id'], $no_drink_plan, $drink_plan, $term );

        // update our user table
        $new_plan = $data['old_plan'].'W';
        $update = $dbh->prepare("UPDATE Subscriptions SET plan = :new_plan WHERE id = :id");
        $update->bindParam(":new_plan", $new_plan);
        $update->bindParam(":id", $data['subscription_id']);
        $update->execute();
        $update = null;
    }
}
