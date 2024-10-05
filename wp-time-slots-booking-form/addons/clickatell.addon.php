<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Clickatell' ) )
{
    class cptslotsb_Clickatell extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-clickatell-20170403";
		protected $name = "Clickatell";
		protected $description;
        public $category = 'SMS Delivery / Text Messaging';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#clickatell-addon';		

        function __construct()
        {
			$this->description = __("The add-on allows to send notification messages (SMS) via Clickatell after submitting the form", 'wp-time-slots-booking-form');
        } // End __construct


    } // End Class

    // Main add-on code
    $apphbclickatell_obj = new cptslotsb_Clickatell();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $apphbclickatell_obj->get_addon_id() ] = $apphbclickatell_obj;
}
