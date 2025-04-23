<?php
/*
Documentation: https://googlecalapi.com/docs/quickstart
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_GoogleCalendarAPI' ) )
{
    class cptslotsb_GoogleCalendarAPI extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-googlecalapi-20200105";
		protected $name = "Google Calendar API";
		protected $description;
        protected $default_pay_label = "Integration with Google Calendar via API";
        protected $default_duration = 60;
        public $category = 'Integration with External Calendars';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2020/04/18/google-calendar-api-connection/';		

        function __construct()
        {
			$this->description = "The add-on adds support for Google Calendar API integration";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_GoogleCalendarAPI_obj = new cptslotsb_GoogleCalendarAPI();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_GoogleCalendarAPI_obj->get_addon_id() ] = $cptslotsb_GoogleCalendarAPI_obj;
}
