<?php
/*
    Shared Availability Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SharedAvailability' ) )
{
    class cptslotsb_SharedAvailability extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-SharedAvailability-20201201";
		protected $name = "Shared Availability between Calendars";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2018/01/20/sharing-booked-times-between-calendars/';		

        function __construct()
        {
			$this->description = __("The add-on allow to share the booked times between calendars (for blocking booked times)", 'appointment-hour-booking' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_SharedAvailability_obj = new cptslotsb_SharedAvailability();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_SharedAvailability_obj->get_addon_id() ] = $cptslotsb_SharedAvailability_obj;
}

