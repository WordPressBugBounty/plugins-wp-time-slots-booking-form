<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'WPTS_Signature' ) )
{ 
    class WPTS_Signature extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-signature-20171025";
		protected $name = "Signature Fields";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#signature-addon';		

        function __construct()
        {
			$this->description = __("The add-on allows to replace form fields with \"Signature\" fields", 'wp-time-slots-booking-form');
		} // End __construct


	} // End Class

    // Main add-on code
    $Apphb_Signature_obj = new WPTS_Signature();

	// Add addon object to the objects list
	$cptslotsb_addons_objs_list[ $Apphb_Signature_obj->get_addon_id() ] = $Apphb_Signature_obj;
}
