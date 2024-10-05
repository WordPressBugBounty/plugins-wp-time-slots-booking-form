<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_paymentoptionorder' ) )
{
    class cptslotsb_paymentoptionorder extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-paymentoptionorder-20210706";
		protected $name = "Set order of payment options";
		protected $description;
        public $category = 'Payment Gateways Integration';
        public $help = 'https://apphourbooking.dwbooster.com/customdownloads/order-payment-options.png';
        
        public $defaultoptions = "paypal\nstripe\npaylater";

        function __construct()
        {
			$this->description = __("Tool for setting the order in which the payment options appear in the booking form (if multiple payment options are enabled).", 'wp-time-slots-booking-form');
			
            
        } // End __construct

		
      
		
    } // End Class
    
    // Main add-on code
    $cptslotsb_paymentoptionorder_obj = new cptslotsb_paymentoptionorder();
    
	// Add addon object to the objects list
	global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $cptslotsb_paymentoptionorder_obj->get_addon_id() ] = $cptslotsb_paymentoptionorder_obj;
}
?>