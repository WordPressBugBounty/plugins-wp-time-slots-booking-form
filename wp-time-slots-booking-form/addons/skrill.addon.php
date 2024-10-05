<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Skrill' ) )
{
    class cptslotsb_Skrill extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Skrill-20170903";
		protected $name = "Skrill Payments Integration";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#skrill-addon';		

        function __construct()
        {
			$this->description = __("The add-on adds support for Skrill payments", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Skrill_obj = new cptslotsb_Skrill();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Skrill_obj->get_addon_id() ] = $cptslotsb_Skrill_obj;
}

