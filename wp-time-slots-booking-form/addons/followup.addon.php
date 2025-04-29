<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_FollowUp' ) )
{
    class cptslotsb_FollowUp extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-FollowUp-20170903";
		protected $name = "FollowUp notifications for bookings";
		protected $description;
        public $help = 'http://wptimeslot.dwbooster.com/contact-us';
        public $category = 'Improvements';


        function __construct()
        {
			$this->description = "The add-on adds support for followup notifications";
        } // End __construct

    } // End Class

    // Main add-on code
    $cptslotsb_FollowUp_obj = new cptslotsb_FollowUp();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_FollowUp_obj->get_addon_id() ] = $cptslotsb_FollowUp_obj;
}

