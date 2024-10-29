<?php
/**
 * Helper class for klaviyo implementation
 * view github project (with README) here: https://github.com/klaviyo/php-klaviyo
 * I suspect this will expand as we figure out everything we want to do with Klaviyo
 * @author Brian Hackett
 */
include_once( get_stylesheet_directory() . '/vendor/php-klaviyo/vendor/autoload.php' );
use Klaviyo\Klaviyo as Klaviyo;
use Klaviyo\Model\EventModel as KlaviyoEvent;
use Klaviyo\Model\ProfileModel as KlaviyoProfile;

class SCKlaviyoHelper
{
    private static $instance = null;
    private $client;

    public function __construct()
    {
        $this->client = new Klaviyo( $_ENV['klaviyo_pvt'], $_ENV['klaviyo_pub'] );
    }

    private static function _forceArray($parameter)
    {
        if( !is_array($parameter) )
        {
            $parameter = array( $parameter );
        }
        return $parameter;
    }

    /**
     * send tracking event to klaviyo
     * @param str $event_name
     * @param str $customer_email
     * @param arr $customer_properties
     * @param arr $properties
     */
    public function sendEvent( $event_name, $customer_email, $customer_properties = array(), $properties = array() )
    {
        if( empty($event_name) || empty($customer_email) )
        {
            return false; // do nothing
        }

        $pass_array = array(
            'event' => $event_name,
            'customer_properties' => array_merge(
                array('$email' => $customer_email),
                $customer_properties
            )
        );

        if( !empty($properties) )
        {
            $pass_array['properties'] = $properties;
        }

        $event = new KlaviyoEvent( $pass_array );

        $this->client->publicAPI->track( $event, true );
    }

    /**
     * update a customer's klaviyo profile
     * @param str $customer_email
     * @param arr $customer_properties
     */
    public function updateProfile( $customer_email, $customer_properties = array() )
    {
        if( empty($customer_email) )
        {
            return false;
        }

        $pass_array = array_merge(
            array('$email' => $customer_email),
            $customer_properties
        );

        $profile = new KlaviyoProfile( $pass_array );

        $this->client->publicAPI->identify( $profile, true );
    }

    /**
     * add list of profiles to a list
     * @param str $list_id
     * @param str $email
     * @param bool $change_consent_status -- default false
     */
    public function addToList( $list_id, $email, $change_consent_status = false )
    {
        if( empty($list_id) || empty($email) )
        {
            return false; // do nothing
        }

        $profile = new KlaviyoProfile( array('$email' => $email) );

        if( $change_consent_status )
        {
            $this->client->lists->addSubscribersToList( $list_id, array($profile) );
        } 
        else
        {
            $this->client->lists->addMembersToList( $list_id, array($profile) );
        }
    }

    public function removeFromList( $list_id, $emails, $change_consent_status = false )
    {
        if( empty($list_id) || empty($emails) )
        {
            return false; // do nothing
        }

        if( $change_consent_status )
        {
            $this->client->lists->deleteSubscribersFromList( $list_id, $emails );
        } 
        else
        {
            $this->client->lists->removeMembersFromList( $list_id, $emails );
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new SCKlaviyoHelper();
        }

        return self::$instance;
    }
}