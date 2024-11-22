<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Uploads' ) )
{
    class cptslotsb_Uploads extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-uploads-20160330";
		protected $name = "Uploads";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#uploads-addon';		

        function __construct()
        {
			$this->description = __("The add-on allows to add the uploaded files to the Media Library, and the support for new mime types", 'wp-time-slots-booking-form');
		} // End __construct
        
      
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_uploads_obj = new cptslotsb_Uploads();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_uploads_obj->get_addon_id() ] = $cptslotsb_uploads_obj;
}
