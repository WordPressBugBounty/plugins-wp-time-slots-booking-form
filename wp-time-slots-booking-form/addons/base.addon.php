<?php
/*
....
*/

if( !class_exists( 'cptslotsb_BaseAddon' ) )
{
    class cptslotsb_BaseAddon 
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID;
		protected $name;
		protected $description;
        public $category = 'Misc';
        public $help = '';		
		
		public function get_addon_id()
		{
			return $this->addonID;
		}
		
		public function get_addon_name()
		{
			return $this->name;
		}
		
		public function get_addon_description()
		{
			return __($this->description, 'wp-time-slots-booking-form');
		}
		
		public function get_addon_form_settings( $form_id )
		{
			return '';
		}
		
		public function get_addon_settings()
		{
			return '';
		}
		
		public function addon_is_active()
		{
			global $cptslotsb_addons_active_list;
            if (!is_array($cptslotsb_addons_active_list)) $cptslotsb_addons_active_list = get_option( 'cptslotsb_addons_active_list', array() );
			return in_array( $this->get_addon_id(), $cptslotsb_addons_active_list );
		}
	} // End Class
}
