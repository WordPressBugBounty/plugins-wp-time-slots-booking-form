<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SagePayments' ) )
{
    class cptslotsb_SagePayments extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-SagePayments-20160706";
		protected $name = "SagePayments Payment Gateway";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#sagepayment-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for SagePayments payments";
        } // End __construct

    } // End Class

    // Main add-on code
    $cptslotsb_SagePayments_obj = new cptslotsb_SagePayments();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_SagePayments_obj->get_addon_id() ] = $cptslotsb_SagePayments_obj;
}

