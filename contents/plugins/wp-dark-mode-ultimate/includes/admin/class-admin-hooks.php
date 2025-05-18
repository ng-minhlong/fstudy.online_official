<?php
/**
 * Handles all the custom hooks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Ultimate\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Hooks' ) ) {
	/**
	 * Handles all the custom hooks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Hooks extends \WP_Dark_Mode\Ultimate\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Register hooks.
		 *
		 * @since 5.0.0
		 */
		public function filters() {
			add_filter( 'wp_dark_mode_admin_json', array( $this, 'modify_wp_dark_mode_admin_json' ) );

			// Activation.
			register_activation_hook( WP_DARK_MODE_ULTIMATE_FILE, array( $this, 'activate' ) );

			// Admin init.
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		/**
		 * Modify the admin json.
		 *
		 * @since 5.0.0
		 *
		 * @param array $json The admin json.
		 * @return array
		 */
		public function modify_wp_dark_mode_admin_json( $json ) {

			$json['is_ultimate'] = $this->is_ultimate();
			$json['url']['ultimate'] = WP_DARK_MODE_ULTIMATE_URL;

			return $json;
		}

		/**
		 * Activate the plugin.
		 *
		 * @since 5.0.0
		 */
		public function activate() {
			
			// Set ultimate installed.
			delete_option( 'wp_dark_mode_ultimate_redirect' );
		}

		/**
		 * Redirect to license page on activation.
		 *
		 * @since 5.0.0
		 */
		public function admin_init() {
			// Bail, if redirect is set already.
			if ( get_option( 'wp_dark_mode_ultimate_redirect' ) ) {
				return;
			}

			// Set redirect option.
			update_option( 'wp_dark_mode_ultimate_redirect', true );

			// Bail, if not ultimate.
			if ( $this->is_ultimate() ) {
				return;
			}

			// Redirect to license page.
			wp_safe_redirect( admin_url( 'admin.php?page=wp-dark-mode-license' ) );
			exit;
		}

	}

	// Instantiate the class.
	Hooks::init();
}