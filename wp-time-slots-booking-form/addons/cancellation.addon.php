<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Cancellation' ) )
{
    class cptslotsb_Cancellation extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Cancellation-20170903";
		protected $name = "Cancellation link for bookings";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#cancellation-addon';		
        protected $cancelled_by_customer_indicator = 'Cancelled by customer';

        function __construct()
        {
			$this->description = __("The add-on adds support for cancellation links for bookings", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Cancellation_obj = new cptslotsb_Cancellation();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Cancellation_obj->get_addon_id() ] = $cptslotsb_Cancellation_obj;
}

