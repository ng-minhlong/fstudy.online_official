<?php
/**
 * Plugin Name: Affiliate Tracking Pro
 * Description: Affiliate tracking plugin (last-click wins). Creates DB tables, handles ?ref=..., tracks clicks, creates commissions on WooCommerce orders and provides an admin overview.
 * Version: 1.0.0
 * Author: You
 * Text Domain: affiliate-tracking-pro
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ATP_PLUGIN_FILE', __FILE__);
define('ATP_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Includes
require_once ATP_PLUGIN_DIR . 'includes/class-atp-activator.php';
require_once ATP_PLUGIN_DIR . 'includes/class-atp-tracker.php';
require_once ATP_PLUGIN_DIR . 'includes/class-atp-commission.php';
require_once ATP_PLUGIN_DIR . 'includes/class-atp-admin.php';
require_once ATP_PLUGIN_DIR . 'includes/helpers.php';

// Activation / Deactivation
register_activation_hook(__FILE__, ['ATP_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['ATP_Activator', 'deactivate']);

// Initialize main classes
add_action('plugins_loaded', function() {
    // Start tracker early
    global $atp_tracker, $atp_commission, $atp_admin;
    $atp_tracker = new ATP_Tracker();
    $atp_commission = new ATP_Commission();
    $atp_admin = new ATP_Admin();
});
