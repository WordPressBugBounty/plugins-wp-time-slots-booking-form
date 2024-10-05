<?php
/*
    Reference: https://www.twilio.com/docs/quickstart/php/sms/sending-via-rest#send-sms-via-rest
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_TwilioSMS' ) )
{
    class cptslotsb_TwilioSMS extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-TwilioSMS-20170903";
		protected $name = "Twilio SMS notifications for bookings";
		protected $description;
        public $category = 'SMS Delivery / Text Messaging';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#twilio-addon';		

        function __construct()
        {
			$this->description = __("The add-on adds support for Twilio SMS notifications", 'wp-time-slots-booking-form' );
        } // End __construct  


    } // End Class

    // Main add-on code
    $cptslotsb_TwilioSMS_obj = new cptslotsb_TwilioSMS();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_TwilioSMS_obj->get_addon_id() ] = $cptslotsb_TwilioSMS_obj;
}

