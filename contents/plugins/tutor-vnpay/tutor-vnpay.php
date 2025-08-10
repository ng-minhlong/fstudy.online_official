<?php
/**
 * Plugin Name:     Tutor VNPAY payment
 * Plugin URI:      https://tutorlms.com
 * Description:     VNPAY payment integration for Tutor LMS
 * Author:          Custom
 * Author URI:      https://tutorlms.com
 * Text Domain:     tutor-vnpay
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         TutorVnpay
 */

// Avoid relying on outdated composer autoload in this plugin.
// We'll register our own lightweight autoloaders instead.
// Ensure Tutor's PaymentHub vendor autoload is available early (safe include)
$tutorPaymentHubAutoload = dirname(__DIR__) . '/tutor/ecommerce/PaymentGateways/Paypal/vendor/autoload.php';
if (file_exists($tutorPaymentHubAutoload)) {
    require_once $tutorPaymentHubAutoload;
}

// We don't additionally autoload Ollyo\PaymentHub here, vendor autoload above handles it.

// Register lightweight autoloaders for this plugin's namespaces
spl_autoload_register(function ($class) {
    $prefix = 'TutorVnpay\\';
    if (strpos($class, $prefix) === 0) {
        $relative = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', '/', $relative) . '.php';
        $path = __DIR__ . '/integration/' . $relativePath;
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

spl_autoload_register(function ($class) {
    $prefix = 'Ollyo\\PaymentHub\\Payments\\Vnpay\\';
    if (strpos($class, $prefix) === 0) {
        $relative = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', '/', $relative) . '.php';
        $path = __DIR__ . '/payments/' . $relativePath;
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

// Load Init early to attach filters
require_once __DIR__ . '/integration/Init.php';

define( 'TUTOR_VNPAY_VERSION', '1.0.0' );
define( 'TUTOR_VNPAY_URL', plugin_dir_url( __FILE__ ) );
define( 'TUTOR_VNPAY_PATH', plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action(
    'plugins_loaded',
    function() {
        if ( is_plugin_active( 'tutor/tutor.php' ) && class_exists( '\\Tutor\\PaymentGateways\\GatewayBase' ) ) {
            new \TutorVnpay\Init();
        }
    },
    100
);


