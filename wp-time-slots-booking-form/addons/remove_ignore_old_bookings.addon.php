<?php
/*
    Shared Availability Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_RemoveIgnoreOld' ) )
{
    class cptslotsb_RemoveIgnoreOld extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-RemoveIgnoreOld-20210417";
		protected $name = "Remove or Ignore Old Bookings";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2018/09/15/remove-or-ignore-old-bookings/';		

        function __construct()
        {            
			$this->description = __("The add-on allows to automatically remove or ignore old bookings to increase the booking form speed", 'appointment-hour-booking' );
          
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_RemoveIgnoreOld_obj = new cptslotsb_RemoveIgnoreOld();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_RemoveIgnoreOld_obj->get_addon_id() ] = $cptslotsb_RemoveIgnoreOld_obj;
}

