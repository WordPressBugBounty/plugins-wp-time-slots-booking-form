<?php
/*
Documentation: https://apphourbooking.dwbooster.com/documentation
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_EmailBlacklistaddon' ) )
{
    class cptslotsb_EmailBlacklistaddon extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-EmailBlacklistaddon-20230328";
		protected $name = "Email Blacklist";
		protected $description;
        public $category = 'Improvements';
        public $help = 'http://wptimeslot.dwbooster.com/contact-us';

        function __construct()
        {

			$this->description = "The add-on is for preventing bookings from blacklisted emails.";
            

        } // End __construct



    } // End Class

    // Main add-on code
    $cptslotsb_EmailBlacklistaddon_obj = new cptslotsb_EmailBlacklistaddon();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_EmailBlacklistaddon_obj->get_addon_id() ] = $cptslotsb_EmailBlacklistaddon_obj;
}
