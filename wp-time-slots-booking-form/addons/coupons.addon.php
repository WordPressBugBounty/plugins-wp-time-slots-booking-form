<?php
/*
Documentation: 
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Coupons' ) )
{
    class cptslotsb_Coupons extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Coupons-20151212";
		protected $name = "Coupons";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2019/05/27/coupon-codes-addon/';		
        protected $split_separator = '___*_';      
        protected $discount_label = 'Discount code';
        protected $discountapplied_label = 'Discount';
        protected $finalprice_label = 'Final price';        

        function __construct()
        {
			$this->description = __("The add-on adds support for coupons / discounts codes", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Coupons_obj = new cptslotsb_Coupons();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Coupons_obj->get_addon_id() ] = $cptslotsb_Coupons_obj;
}
