<?php
/*
* Add-on: Activity Logs for WP Time Slots Booking Form
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'cptslotsb_ActivityLogsWidget' ) )
{
    class cptslotsb_ActivityLogsWidget extends cptslotsb_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
        protected $addonID = "addon-ActivityLogsWidget-20260707";
        protected $name = 'Activity Logs';
        protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/images/articles/logs-addon.png';
        private $table_name;

        public function __construct()
        {
            global $wpdb;
            $this->table_name = $wpdb->prefix . 'cptslotsb_activity_logs';
            $this->description = "The add-on logs actions made in the plugin (additions, updates, deletions) and displays them on a dedicated dashboard page.";
          
        }

    } // End Class

    // Main add-on code
    $cptslotsb_ActivityLogsWidget_obj = new cptslotsb_ActivityLogsWidget();

    // Add addon object to the objects list
    global $cptslotsb_addons_objs_list;
    $cptslotsb_addons_objs_list[ $cptslotsb_ActivityLogsWidget_obj->get_addon_id() ] = $cptslotsb_ActivityLogsWidget_obj;
}