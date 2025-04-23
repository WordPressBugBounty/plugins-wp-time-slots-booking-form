<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_reCAPTCHA' ) )
{
    class cptslotsb_reCAPTCHA extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-recaptcha-20151106";
		protected $name = "reCAPTCHA";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = 'https://wptimeslot.dwbooster.com/add-ons/recaptcha';		
		
	
        function __construct()
        {
			$this->description = "The add-on allows to protect the forms with reCAPTCHA service of Google";
        } // End __construct
        
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_recaptcha_obj = new cptslotsb_reCAPTCHA();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_recaptcha_obj->get_addon_id() ] = $cptslotsb_recaptcha_obj;
}
?>