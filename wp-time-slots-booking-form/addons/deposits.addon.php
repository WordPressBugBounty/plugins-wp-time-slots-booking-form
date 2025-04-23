<?php
/*
Documentation: https://wptimeslot.dwbooster.com/
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_Deposits' ) )
{
    class cptslotsb_Deposits extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-deposits-20151212";
		protected $name = "Deposit Payments";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/customdownloads/deposits-payments.png';
        
        public $ahbdepoptionallabel = 'Deposit option';
        public $ahbdepoptionaloptionyes = 'Pay deposit only';
        public $ahbdepoptionaloptionno = 'Pay full amount';

        function __construct()
        {
			$this->description = "The add-on enables the option to accept payment deposit as fixed amount or percent of the booking cost";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_Deposits_obj = new cptslotsb_Deposits();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_Deposits_obj->get_addon_id() ] = $cptslotsb_Deposits_obj;
}


