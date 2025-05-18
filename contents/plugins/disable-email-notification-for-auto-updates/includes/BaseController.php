<?php
if( ! class_exists( 'ITC_Disable_Update_Notifications_BaseController' ) ) {
	class ITC_Disable_Update_Notifications_BaseController{
		private $plugin_name_parent = 'disable_email_notification_for_auto_updates_module_settings';
		private $plugin_detail;

		function __construct() {
			$this->plugin_detail = array(
				'name'      => 'itc_disable_update_notifications',
				'title'     => esc_html__( 'Disable Email Notifications and Block Plugin, WP Core, and Theme Updates', 'disable-email-notification-for-auto-updates' ),
				'slug'      => 'itc-disable_update_notifications',
				'version'   => ( defined( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_VERSION' ) ) ? ITC_DISABLE_UPDATE_NOTIFICATIONS_VERSION : '1.0.5',
				'settings'  => 'itc_disable_update_notifications_settings',
			);
		}

		public function register_Module($plugin_name, $plugin_details){
			$plugins = get_option($this->plugin_name_parent, []);  // Default to an empty array if not set

			if($plugin_name !== "" && isset($plugins[$plugin_name])){
				$finalModuleDetails = array_merge($plugins[$plugin_name], $plugin_details);
			} else {
				$finalModuleDetails = $plugin_details;
			}
			$plugins[$plugin_name] = $finalModuleDetails;
			update_option($this->plugin_name_parent, $plugins);
		}

		public function unregister_Module($plugin_name){
			$plugins = get_option($this->plugin_name_parent, []);  // Default to an empty array if not set
			if($plugin_name !== "" && isset($plugins[$plugin_name])){
				unset($plugins[$plugin_name]);
				update_option($this->plugin_name_parent, $plugins);
			}
		}

		public function uninstall_Module($plugin_name){
			$this->unregister_Module($plugin_name);
			// Also, if we want to delete any module settings, add them here.
		}

		public function get_Module($plugin_name = ""){
			$plugins = get_option($this->plugin_name_parent, []);  // Default to an empty array if not set
			if($plugin_name !== "" && isset($plugins[$plugin_name])){
				return $plugins[$plugin_name];
			}
			return [];
		}

		public function get_plugin_detail() {
			return $this->plugin_detail;
		}

		public function get_plugin_name() {
			$detail = $this->get_plugin_detail();
			return $detail['name'];
		}

		public function get_plugin_title() {
			$detail = $this->get_plugin_detail();
			return $detail['title'];
		}
		
		public function get_plugin_slug() {
			$detail = $this->get_plugin_detail();
			return $detail['slug'];
		}

		public function get_version() {
			$detail = $this->get_plugin_detail();
			return $detail['version'];
		}
		
		public function get_settings() {
			$detail = $this->get_plugin_detail();
			return $detail['settings'];
		}

		public function get_option_default($key=""){
			$default = array(
				"plugin" => 1,
				"themes" => 1,
				"wordpress" => 1,
				"wp_update_button" => 0,
				"wp_core" => 0,
				"wp_themes" => 0,
			);
			if($key !== "" && isset($default[$key])){
				$result = ($default[$key] == "1") ? 1 : 0;
				return $result;
			}
			return $default;
		}

		public function get_option($key=""){
			$defaultSettings = $this->get_option_default();
			$settings = get_option($this->get_settings(), $defaultSettings);  // Ensure it's always an array
			if(isset($settings[$key])){
				return $settings[$key];
			}
			return $settings;
		}

		//This is to display msg after plugin activation.
		public function set_transient(){
			$notice_dismiss_index = $this->get_settings().'_notice_dismiss';
			set_transient( 'disable-email-notification-for-auto-updates' . $notice_dismiss_index, true, 5 ); // Prefixed transient name
		}
		
		public function get_transient(){
			$notice_dismiss_index = $this->get_settings().'_notice_dismiss';
			return get_transient( 'disable-email-notification-for-auto-updates' . $notice_dismiss_index );
		}
		
		public function remove_transient(){
			$notice_dismiss_index = $this->get_settings().'_notice_dismiss';
			delete_transient( 'disable-email-notification-for-auto-updates' . $notice_dismiss_index );
		}
	}
}
