<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_MailChimp' ) )
{
    class cptslotsb_MailChimp extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-mailchimp-20170504";
		protected $name = "MailChimp";
		protected $description;
        public $category = 'Integration with third party services';
        public $help = 'https://wptimeslot.dwbooster.com/documentation#mailchimp-addon';		

        function __construct()
        {
			$this->description = "The add-on creates MailChimp List members with the submitted information";
        } // End __construct


    } // End Class

    // Main add-on code
    $apphbmailchimp_obj = new cptslotsb_MailChimp();

    global $cptslotsb_addons_objs_list;
	$cptslotsb_addons_objs_list[ $apphbmailchimp_obj->get_addon_id() ] = $apphbmailchimp_obj;
}
