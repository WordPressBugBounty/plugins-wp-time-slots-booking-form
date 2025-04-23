<?php
/*
Documentation: https://goo.gl/w3kKoH
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_iDealMollie' ) )
{
    class cptslotsb_iDealMollie extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-idealmollie-20151212";
		protected $name = "iDeal Mollie";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#mollie-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for iDeal via Mollie payments";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_iDealMollie_obj = new cptslotsb_iDealMollie();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_iDealMollie_obj->get_addon_id() ] = $cptslotsb_iDealMollie_obj;
}

