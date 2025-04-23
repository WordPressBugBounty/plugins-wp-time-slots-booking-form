<?php
/*

*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_DoubleOptIn' ) )
{
    class cptslotsb_DoubleOptIn extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-DoubleOptIn-20200805";
		protected $name = "Double opt-in email verification";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/blog/2020/09/12/double-opt-in-addon/';		
        protected $approved_by_customer_indicator = '';

        function __construct()
        {
			$this->description = "Double opt-in email verification link to mark the booking as approved";

        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_DoubleOptIn_obj = new cptslotsb_DoubleOptIn();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_DoubleOptIn_obj->get_addon_id() ] = $cptslotsb_DoubleOptIn_obj;
}

