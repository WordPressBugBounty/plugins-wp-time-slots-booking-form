<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_PayPalPro' ) )
{
    class cptslotsb_PayPalPro extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-paypalpro-20151212";
		protected $name = "PayPal Pro";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/contact-us';
		
        function __construct()
        {
			$this->description = "The add-on adds support for PayPal Payment Pro payments to accept credit cars directly into the website";
        } // End __construct


    } // End Class

    // Main add-on code
    $apphbpaypalpro_obj = new cptslotsb_PayPalPro();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $apphbpaypalpro_obj->get_addon_id() ] = $apphbpaypalpro_obj;    
}
