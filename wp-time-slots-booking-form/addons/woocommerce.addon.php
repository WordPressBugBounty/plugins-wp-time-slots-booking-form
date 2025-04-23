<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_WooCommerce' ) )
{
    class cptslotsb_WooCommerce extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-woocommerce-20150309";
		protected $name = "WooCommerce";
		protected $description;
        public $category = 'Integration with other plugins';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2018/11/01/woocommerce-integration/';		
		
		
        function __construct()
        {
			$this->description = "The add-on allows integrate the forms with WooCommerce products";
        } // End __construct


    } // End Class
    
    // Main add-on code
    $cptslotsb_woocommerce_obj = new cptslotsb_WooCommerce();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_woocommerce_obj->get_addon_id() ] = $cptslotsb_woocommerce_obj;
}
