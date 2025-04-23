<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_PayPalStandard' ) )
{
    class cptslotsb_PayPalStandard extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-PayPalStandard-20170903";
		protected $name = "PayPal Standard Payments Integration";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://apphourbooking.dwbooster.com/customdownloads/paypal-add-ons-settings.png';		

        function __construct()
        {
			$this->description = "The add-on adds support for PayPal Standard payments";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_PayPalStandard_obj = new cptslotsb_PayPalStandard();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_PayPalStandard_obj->get_addon_id() ] = $cptslotsb_PayPalStandard_obj;
}

