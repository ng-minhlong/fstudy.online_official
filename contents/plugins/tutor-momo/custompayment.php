<?php
/**
 * Plugin Name:     Tutor Momo Payment
 * Plugin URI:      https://tutorlms.com
 * Description:     Momo payment integration for Tutor LMS
 * Author:          Themeum
 * Author URI:      https://tutorlms.com
 * Text Domain:     tutor-momo-payment
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         CustomPayment
 */

// Your code starts here.
require_once __DIR__ . '/vendor/autoload.php';

// Define plugin meta info.
define( 'CUSTOM_PAYMENT_VERSION', '1.0.0' );
define( 'CUSTOM_PAYMENT_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOM_PAYMENT_PATH', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_PAYMENT_PAYMENTS_DIR', trailingslashit( CUSTOM_PAYMENT_PATH . 'src/Payments' ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action(
	'plugins_loaded',
	function() {
		if ( is_plugin_active( 'tutor/tutor.php' ) && is_plugin_active( 'tutor-pro/tutor-pro.php' ) ) {
			new CustomPayment\Init();
		}
	},
	100
);