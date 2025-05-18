<?php
class ITC_Disable_Update_Notifications extends ITC_Disable_Update_Notifications_BaseController {
	protected $loader;
	
	public function __construct() {
		parent::__construct();// call parent constructor
		
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_common_hooks();

	}

	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-public.php';

		$this->loader = new ITC_Disable_Update_Notifications_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new ITC_Disable_Update_Notifications_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_common_hooks() {
		$settings = $this->get_option();

		if ( isset( $settings['plugin'] ) && $settings['plugin'] === 1 ) {
			add_filter( 'auto_plugin_update_send_email', '__return_false' );
		}

		if ( isset( $settings['themes'] ) && $settings['themes'] === 1 ) {
			add_filter( 'auto_theme_update_send_email', '__return_false' );
		}

		if ( isset( $settings['wordpress'] ) && $settings['wordpress'] === 1 ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress.php';
			$plugin_obj = new ITC_Disable_Update_Notifications_Wordpress();
			$this->loader->add_filter( 'auto_core_update_send_email', $plugin_obj, 'wp_updates_email', 10, 4 );
		}
				
		 // Remove updates button
		if ( isset( $settings['wp_update_button'] ) && $settings['wp_update_button'] === 1 ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-remove-updates-button.php';
			$plugin_wp_update_button = new ITC_Disable_Update_Notifications_WP_Update_Button();
			$this->loader->add_action( 'admin_menu', $plugin_wp_update_button, 'remove_update_menu_itc', 102 );
		}

		// Remove WordPress Core Updates
		if ( isset( $settings['wp_core'] ) && $settings['wp_core'] === 1 ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-disable-wp-core-updates.php';
			$plugin_wp_core = new ITC_Disable_Update_Notifications_WP_Core();
			$this->loader->add_action( 'init', $plugin_wp_core, 'wp_core_updates_itc', 1 );
			$this->loader->add_filter( 'pre_site_transient_update_core', $plugin_wp_core, 'disable_wp_core_last_checked_itc' );
			$this->loader->add_filter( 'site_status_tests', $plugin_wp_core, 'wp_health_check_tests_itc' );
}

		// Remove WordPress theme Updates
		if ( isset( $settings['wp_themes'] ) && $settings['wp_themes'] === 1 ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-disable-theme-updates.php';
			$plugin_wp_themes = new ITC_Disable_Update_Notifications_WP_Themes();
			$this->loader->add_filter( 'pre_site_transient_update_themes', $plugin_wp_themes, 'disable_theme_last_checked_itc' );
			$this->loader->add_filter( 'site_status_tests', $plugin_wp_themes, 'theme_health_check_tests_itc' );
			$this->loader->add_action( 'init', $plugin_wp_themes, 'wp_theme_updates_itc', 1 );
		} 
		
		// for block plugin updates
		$disable_plugin_updates_settings = get_option( $this->get_settings() . '_disable_plugin_updates' );
		if ( ! empty( $disable_plugin_updates_settings ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-disable-plugin-updates.php';
			$plugin_update_manager = new ITC_Disable_Update_Notifications_Wordpress_Plugin( $disable_plugin_updates_settings );
			add_filter( 'site_transient_update_plugins', [ $plugin_update_manager, 'disable_plugin_updates_itc' ] );
			add_filter( 'auto_update_plugin', [ $plugin_update_manager, 'filter_plugin_auto_update_itc' ], 10, 2 );
			add_action( 'admin_head', [ $plugin_update_manager, 'add_custom_css_js_hide_plugins_itc' ] );
	
		}
		// Hide plugin
		$hide_plugin_from_dashboard_settings = get_option( $this->get_settings() . '_hide_plugin_from_dashboard' );
		if ( ! empty( $hide_plugin_from_dashboard_settings ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hide-wp-plugin.php';
			$plugin_hide_manager = new ITC_Disable_Update_Notifications_Wordpress_Plugin_hide( $hide_plugin_from_dashboard_settings );
			add_filter( 'all_plugins', [ $plugin_hide_manager, 'hide_plugins_from_dashboard_itc' ] );
		}
	}

	private function define_admin_hooks() {

		$plugin_admin = new ITC_Disable_Update_Notifications_Admin();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action("admin_menu", $plugin_admin, 'add_options_page');
		$this->loader->add_action("admin_init", $plugin_admin, 'register_setting');
		$this->loader->add_filter("plugin_action_links_".ITC_DISABLE_UPDATE_NOTIFICATIONS_BASENAME , $plugin_admin, 'itc_disable_update_notifications_action_links');
		$this->loader->add_action("wp_ajax_itc_disable_update_notifications_dismissed", $plugin_admin, 'itc_disable_update_notifications_dismissed');

		$this->loader->add_action("admin_notices", $plugin_admin, 'general_admin_notice');

	}

	private function define_public_hooks() {

		$plugin_public = new ITC_Disable_Update_Notifications_Public();
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_loader() {
		return $this->loader;
	}
}
