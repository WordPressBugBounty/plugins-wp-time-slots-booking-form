<?php
/*
   Automatically cancel pending bookings Addon
*/

require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_AutoCancelStatusManagement' ) )
{    
    class cptslotsb_AutoCancelStatusManagement extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-AutoCancelStatusaddon-20220314";
		protected $name = "Automatically cancel pending bookings";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/customdownloads/pending-status-expiration.png';
        public $pay_later_status = 'Pending';
        public $pay_later_expired_status = 'Pending Booking Expired';

        function __construct()
        {
			$this->description = __("Automatically cancel pending bookings, expiration time for bookings with pending status.", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_AutoCancelStatusManagement_obj = new cptslotsb_AutoCancelStatusManagement();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_AutoCancelStatusManagement_obj->get_addon_id() ] = $cptslotsb_AutoCancelStatusManagement_obj;
    
}
