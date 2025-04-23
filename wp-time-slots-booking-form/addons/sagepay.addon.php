<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SagePay' ) )
{
    class cptslotsb_SagePay extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-SagePay-20160706";
		protected $name = "SagePay Payment Gateway";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#sagepay-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for SagePay payments";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_SagePay_obj = new cptslotsb_SagePay();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_SagePay_obj->get_addon_id() ] = $cptslotsb_SagePay_obj;
}

