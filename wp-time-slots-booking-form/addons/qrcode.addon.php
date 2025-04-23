<?php
/*
    Appointment Limits Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_QRCode' ) )
{
    class cptslotsb_QRCode extends cptslotsb_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-QRCode-20180607";
		protected $name = "QRCode Image - Barcode";
		protected $description;
        public $category = 'Improvements';
        public $help = 'https://apphourbooking.dwbooster.com/blog/2018/01/15/qrcode-image-barcode-add-on/';        

        function __construct()
        {
			$this->description = "Generates a QRCode image for each booking.";
        } // End __construct


    } // End Class

    // Main add-on code
    $cptslotsb_QRCode_obj = new cptslotsb_QRCode();

	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_QRCode_obj->get_addon_id() ] = $cptslotsb_QRCode_obj;
}
