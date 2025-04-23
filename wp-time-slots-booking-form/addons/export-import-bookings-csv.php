<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_EIBookingsCSV' ) )
{
    class cptslotsb_EIBookingsCSV extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-EIBookingsCSV-20221014";
		protected $name = "Import raw bookings (CSV format)";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/images/articles/csv-import.png';
		
        function __construct()
        {
			$this->description = "The add-on allows to import bookings in raw CSV format";
        } // End __construct

		
    } // End Class
    
    // Main add-on code
    $cptslotsb_EIBookingsCSV_obj = new cptslotsb_EIBookingsCSV();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_EIBookingsCSV_obj->get_addon_id() ] = $cptslotsb_EIBookingsCSV_obj;
}
