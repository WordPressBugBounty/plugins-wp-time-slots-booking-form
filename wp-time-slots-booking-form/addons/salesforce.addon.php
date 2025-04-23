<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SalesForce' ) )
{
    class cptslotsb_SalesForce extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-salesforce-20150311";
		protected $name = "SalesForce";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = 'https://wptimeslot.dwbooster.com/add-ons/salesforce';		

        function __construct()
        {
			$this->description = "The add-on allows create SalesForce leads with the submitted information";
        } // End __construct
        
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_salesforce_obj = new cptslotsb_SalesForce();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_salesforce_obj->get_addon_id() ] = $cptslotsb_salesforce_obj;
}
