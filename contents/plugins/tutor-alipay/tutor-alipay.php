<?php
/**
 * Plugin Name:     Tutor Alipay
 * Plugin URI:      https://tutorlms.com
 * Description:     Alipay payment integration for Tutor LMS
 * Author:          Themeum
 * Author URI:      https://tutorlms.com
 * Text Domain:     tutor-alipay
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         TutorAlipay
 */

// Your code starts here.
require_once __DIR__ . '/vendor/autoload.php';

// Define plugin meta info.
define( 'TUTOR_ALIPAY_VERSION', '1.0.0' );
define( 'TUTOR_ALIPAY_URL', plugin_dir_url( __FILE__ ) );
define( 'TUTOR_ALIPAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'TUTOR_ALIPAY_PAYMENTS_DIR', trailingslashit( TUTOR_ALIPAY_PATH . 'src/Payments' ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action(
	'plugins_loaded',
	function() {
		if ( is_plugin_active( 'tutor/tutor.php' ) && is_plugin_active( 'tutor-pro/tutor-pro.php' ) ) {
			new TutorAlipay\Init();
		}
	},
	100
);
