<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_SchedudeCalContents' ) )
{
    class cptslotsb_SchedudeCalContents extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-SchedudeCalContents-20210714";
		protected $name = "Schedule & Status Contents Customization";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2018/11/01/schedule-calendar-contents-customization/';
        protected $default_colors = array("#77dd77","#836953","#89cff0","#99c5c4","#9adedb","#aa9499","#aaf0d1","#b2fba5","#b39eb5","#bdb0d0","#bee7a5","#befd73","#c1c6fc","#c6a4a4","#c8ffb0","#cb99c9","#cef0cc","#cfcfc4","#d6ffe","#d8a1c4","#dea5a4","#deece1","#dfd8e1","#e5d9d3","#e9d1bf","#f49ac2","#f4bfff","#fdfd96","#ff6961","#ff964f","#ff9899","#ffb7ce","#ca9bf7");
        
        protected $default_colors_bkorders = array("#000000","#006600","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000","#ff0000",);
        
        protected $cached_colors = array();
        protected $cached_statuses = array();

        function __construct()
        {
			$this->description = __("Customize the contents displayed on the schedule calendar and the status colors for each form.", 'wp-time-slots-booking-form' );
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_SchedudeCalContents_obj = new cptslotsb_SchedudeCalContents();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_SchedudeCalContents_obj->get_addon_id() ] = $cptslotsb_SchedudeCalContents_obj;
}

