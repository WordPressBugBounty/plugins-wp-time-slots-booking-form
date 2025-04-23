<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Reminder' ) )
{
    class cptslotsb_Reminder extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Reminder-20170903";
		protected $name = "Reminder notifications for bookings";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#reminder-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for reminder notifications";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Reminder_obj = new cptslotsb_Reminder();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Reminder_obj->get_addon_id() ] = $cptslotsb_Reminder_obj;
}

