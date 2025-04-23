<?php
/*
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_FrontendLists' ) )
{
    class cptslotsb_FrontendLists extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-FrontendLists-20181221";
		protected $name = "Frontend List: Grouped by Date Add-on";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2018/11/21/grouped-frontend-lists/';		
        protected $max_image_width = 250;

        function __construct()
        {
			$this->description = "The add-on allows to displays list (schedule) of bookings grouped by date in the frontend";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_FrontendLists_obj = new cptslotsb_FrontendLists();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_FrontendLists_obj->get_addon_id() ] = $cptslotsb_FrontendLists_obj;
}

