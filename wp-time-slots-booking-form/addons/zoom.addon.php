<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Zoom' ) )
{
    class cptslotsb_Zoom extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Zoom-20170903";
		protected $name = "Zoom.us Meetings Integration";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#zoom-addon';			

        function __construct()
        {
			$this->description = __("Automatically creates a Zoom.us meeting for the booked time", 'appointment-hour-booking' );
        } // End __construct
		

    } // End Class

    // Main add-on code
    $cptslotsb_Zoom_obj = new cptslotsb_Zoom();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Zoom_obj->get_addon_id() ] = $cptslotsb_Zoom_obj;
}
