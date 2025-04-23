<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_CustomStatuses' ) )
{
    class cptslotsb_CustomStatuses extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-customstatuses-20210714";
		protected $name = "Additional Booking Statuses";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2018/01/05/custom-statuses/';

        function __construct()
        {
			$this->description = "The add-on allows to add new statuses to the bookings.";
        } // End __construct
    
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_CustomStatuses_obj = new cptslotsb_CustomStatuses();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_CustomStatuses_obj->get_addon_id() ] = $cptslotsb_CustomStatuses_obj;
}
