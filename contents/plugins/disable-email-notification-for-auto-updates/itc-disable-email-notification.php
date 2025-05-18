<?php

/**
 * @link              https://ideastocode.com
 * @since             1.0.1
 * @package           Disable Auto Update Emails and Block Updates for Plugins, WP Core, and Themes
 *
 * @wordpress-plugin
 * Plugin Name:       Disable Auto Update Emails and Block Updates for Plugins, WP Core, and Themes
 * Plugin URI:        https://ideastocode.com/plugins/disable-automatic-update-email-notification-in-wordpress/
 * Description:       Key Features: Disable Auto-Update Emails, Block Specific Plugins, Core, or Theme Updates.
 * Version:           1.0.5
 * Author:            ideasToCode
 * Author URI:        http://ideastocode.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       disable-email-notification-for-auto-updates
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_VERSION', '1.0.5' );
if ( ! defined( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_BASENAME' ) ) {
	define( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH' ) ) {
	define( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_URL' ) ) {
	define( 'ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

require_once ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH . 'includes/BaseController.php';

function initiate_itc_disable_update_notifications() {
	require_once ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH . 'includes/class-activator.php';
	$objActivator = new ITC_Disable_Update_Notifications_Activator();
	$objActivator->activate();
}

function disconnect_itc_disable_update_notifications() {
	require_once ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH . 'includes/class-deactivator.php';
	$objDeactivator = new ITC_Disable_Update_Notifications_Deactivator();
	$objDeactivator->deactivate();
}

function start_itc_disable_update_notifications() {
	require_once ITC_DISABLE_UPDATE_NOTIFICATIONS_PLUGIN_DIR_PATH . 'includes/class-itc.php';
	$plugin = new ITC_Disable_Update_Notifications();
	$plugin->run();
}

register_activation_hook( __FILE__, 'initiate_itc_disable_update_notifications' );
register_deactivation_hook( __FILE__, 'disconnect_itc_disable_update_notifications' );
start_itc_disable_update_notifications();
