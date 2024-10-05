<?php
/*
    Appointment Limits Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_appLimit' ) )
{
    class cptslotsb_appLimit extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-appLimit-20180607";
		protected $name = "Limit the number of appointments per user";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#appointmentlimits-addon';		
        
        protected $default_label = 'Number of allowed appointments exceeded.';        

        function __construct()
        {
			$this->description = __("The add-on adds support for limiting the number of appointments per user", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_appLimit_obj = new cptslotsb_appLimit();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_appLimit_obj->get_addon_id() ] = $cptslotsb_appLimit_obj;
}
