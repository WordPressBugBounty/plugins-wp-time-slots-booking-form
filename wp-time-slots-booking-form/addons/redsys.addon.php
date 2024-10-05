<?php
/*
Documentation: https://goo.gl/w3kKoH
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SabTPV' ) )
{
    class cptslotsb_SabTPV extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-sabtpv-20151212";
		protected $name = "RedSys TPV";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#redsys-addon';
		
        function __construct()
        {
			
			$this->description = __("The add-on adds support for RedSys TPV payments", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $apphbsabtpv_obj = new cptslotsb_SabTPV();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $apphbsabtpv_obj->get_addon_id() ] = $apphbsabtpv_obj;
}

