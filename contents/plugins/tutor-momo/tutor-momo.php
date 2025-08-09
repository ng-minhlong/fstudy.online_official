<?php
/**
 * Plugin Name:     Tutor MoMo payment 
 * Plugin URI:      https://tutorlms.com
 * Description:     MoMo payment integration for Tutor LMS
 * Author:          Long Nguyen (Customized for MoMo)
 * Author URI:      https://tutorlms.com
 * Text Domain:     tutor-momo
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         TutorMomo
 */

require_once __DIR__ . '/vendor/autoload.php';

define( 'TUTOR_MOMO_VERSION', '1.0.0' );
define( 'TUTOR_MOMO_URL', plugin_dir_url( __FILE__ ) );
define( 'TUTOR_MOMO_PATH', plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action(
	'plugins_loaded',
	function() {
		// Initialize when Tutor LMS is active and payment gateway base is available
		if ( is_plugin_active( 'tutor/tutor.php' ) && class_exists( '\\Tutor\\PaymentGateways\\GatewayBase' ) ) {
			new TutorMomo\Init();
		}
	},
	100
);
