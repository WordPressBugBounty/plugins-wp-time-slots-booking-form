<?php
/*
Documentation: https://www.targetpay.com/docs/TargetPay_iDEAL_V3.0_en.pdf
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_iDealTargetPay' ) )
{
    class cptslotsb_iDealTargetPay extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-idealtargetpay-20151212";
		protected $name = "iDeal TargetPay";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#targetpay-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for iDeal via TargetPay payments";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_iDealTargetPay_obj = new cptslotsb_iDealTargetPay();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_iDealTargetPay_obj->get_addon_id() ] = $cptslotsb_iDealTargetPay_obj;
}
