<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Turnstile' ) )
{
    class cptslotsb_Turnstile extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-Turnstile-20151106";
		protected $name = "Cloudflare Turnstile";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = '';
		
        
        function __construct()
        {
			$this->description = "The add-on allows to protect the forms with Cloudflare Turnstile";
        } // End __construct
      
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_Turnstile_obj = new cptslotsb_Turnstile();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Turnstile_obj->get_addon_id() ] = $cptslotsb_Turnstile_obj;
}

