<?php
/*
* Add-on: Waiting List for WP Time Slots Booking Form (Backend + Frontend)
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once dirname( __FILE__ ) . '/base.addon.php';

if ( ! class_exists( 'cptslotsb_WaitingListAddon' ) ) {

    class cptslotsb_WaitingListAddon extends cptslotsb_BaseAddon {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
        protected $addonID = "addon-WaitingList-20261022";
        protected $name = 'Waiting List';
        protected $description;
        public $category = 'Improvements';
        public $help = 'https://wptimeslot.dwbooster.com/images/articles/waiting-list-addon.png';

        private $table_name;
        private $option_name = 'cptslotsb_waiting_list_settings';
        private $cached_bookings = array();
        private $frontend_assets_printed = false;

        public function __construct() {
            global $wpdb;
            $this->table_name = $wpdb->prefix . 'cptslotsb_waiting_list';
            $this->description = __(
                "Manages a waiting list for booked time slots on the frontend and backend, and sends automatic or manual email notifications when a spot opens up.",
                'wp-time-slots-booking-form'
            );

            
        }


    } // End Class

    // Instantiate and register add-on in the global add-ons list
    $cptslotsb_WaitingListAddon_obj = new cptslotsb_WaitingListAddon();

    global $cptslotsb_addons_objs_list;
    $cptslotsb_addons_objs_list[ $cptslotsb_WaitingListAddon_obj->get_addon_id() ] = $cptslotsb_WaitingListAddon_obj;
}