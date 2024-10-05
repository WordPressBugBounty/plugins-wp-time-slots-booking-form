<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_UserCalendarCreation' ) )
{
    class cptslotsb_UserCalendarCreation extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-UserCalendarCreation-20180607";
		protected $name = "User Calendar Creation";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#usercreation-addon';		

        function __construct()
        {
			$this->description = __("The add-on creates and assign a calendar for each new registered user", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_UserCalendarCreation_obj = new cptslotsb_UserCalendarCreation();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_UserCalendarCreation_obj->get_addon_id() ] = $cptslotsb_UserCalendarCreation_obj;
}