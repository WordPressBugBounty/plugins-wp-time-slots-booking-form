<?php
/*
Documentation: https://stripe.com/docs/quickstart
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Stripe' ) )
{
    class cptslotsb_Stripe extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-stripe-20151212";
		protected $name = "Stripe";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#stripe-addon';		

        function __construct()
        {
			$this->description = "The add-on adds support for Stripe payments";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Stripe_obj = new cptslotsb_Stripe();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Stripe_obj->get_addon_id() ] = $cptslotsb_Stripe_obj;
}
