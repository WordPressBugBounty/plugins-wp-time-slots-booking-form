<?php
/*
    Appointment Limits Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_serverSidePriceCheck' ) )
{
    class cptslotsb_serverSidePriceCheck extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-serverSidePriceCheck-20221102";
		protected $name = "Checks price on the server side";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/images/articles/server-side-price-verification.png';	
        
        protected $default_label = 'Price value cannot be verified. Please <a href="javascript:window.history.back();">go back and try again</a> or contact us if this is an error.';        

        function __construct()
        {
			$this->description = "The add-on adds server side verification of the booking price";
        } // End __construct      


    } // End Class

    // Main add-on code
    $cptslotsb_serverSidePriceCheck_obj = new cptslotsb_serverSidePriceCheck();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_serverSidePriceCheck_obj->get_addon_id() ] = $cptslotsb_serverSidePriceCheck_obj;
}
