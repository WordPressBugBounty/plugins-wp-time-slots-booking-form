<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_WebHook' ) )
{
    class cptslotsb_WebHook extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-webhook-20150403";
		protected $name = "WebHook";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = 'https://wptimeslot.dwbooster.com/add-ons/webhook';		

        function __construct()
        {
			$this->description = __("The add-on allows put the submitted information to a webhook URL, and integrate the forms with the Zapier service", 'wp-time-slots-booking-form');
        } // End __construct
   
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_webhook_obj = new cptslotsb_WebHook();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_webhook_obj->get_addon_id() ] = $cptslotsb_webhook_obj;
}
