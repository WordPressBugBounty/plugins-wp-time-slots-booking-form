<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_AuthNetSIM' ) )
{
    class cptslotsb_AuthNetSIM extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-AuthNetSIM-20160910";
		protected $name = "Authorize.net Server Integration Method";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#authorize-addon';		

        function __construct()
        {
			$this->description = __("The add-on adds support for Authorize.net Server Integration Method payments", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_AuthNetSIM_obj = new cptslotsb_AuthNetSIM();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_AuthNetSIM_obj->get_addon_id() ] = $cptslotsb_AuthNetSIM_obj;
}

