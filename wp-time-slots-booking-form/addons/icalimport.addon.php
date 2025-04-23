<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_iCalImport' ) )
{
    class cptslotsb_iCalImport extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-iCalImport-20180607";
		protected $name = "iCal Automatic Import";
		protected $description;
        public $category = 'Integration with External Calendars';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2018/12/20/ical-import/';


        function __construct()
        {
			$this->description = "The add-on adds support for importing iCal files from external websites/services";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_iCalImport_obj = new cptslotsb_iCalImport();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_iCalImport_obj->get_addon_id() ] = $cptslotsb_iCalImport_obj;
}
