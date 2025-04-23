<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_StatusUpdateEmails' ) )
{
    class cptslotsb_StatusUpdateEmails extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-StatusUpdateEmails-20170903";
		protected $name = "Status Modification Emails";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2018/11/28/status-update-emails/';		

        function __construct()
        {
			$this->description = "The add-on allows to define emails to be sent when the booking status is changed from the bookings list";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_StatusUpdateEmails_obj = new cptslotsb_StatusUpdateEmails();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_StatusUpdateEmails_obj->get_addon_id() ] = $cptslotsb_StatusUpdateEmails_obj;
}

