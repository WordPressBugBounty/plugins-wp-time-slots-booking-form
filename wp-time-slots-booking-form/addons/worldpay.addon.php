<?php
/*
Documentation: http://support.worldpay.com/support/bg/index.php?page=development&sub=integration&subsub=examples
               http://support.worldpay.com/support/kb/bg/paymentresponse/pr0000.html
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_WorldPay' ) )
{
    class cptslotsb_WorldPay extends cptslotsb_BaseAddon
    {
       
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-WorldPay-20200705";
		protected $name = "WorldPay Payment Gateway";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/contact-us';
		
        function __construct()
        {
			$this->description = __("The add-on adds support for WorldPay payments", 'cpabc' );
        } // End __construct
        		
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_WorldPay_obj = new cptslotsb_WorldPay();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_WorldPay_obj->get_addon_id() ] = $cptslotsb_WorldPay_obj;
}

