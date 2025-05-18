<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class ITC_Disable_Update_Notifications_i18n {

	public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'disable-email-notification-for-auto-updates' );
        $mofile = sprintf( 'languages/disable-email-notification-for-auto-updates-%s.mo', $locale );

        // Load the translation file for the specified locale
        load_textdomain( 'disable-email-notification-for-auto-updates', plugin_dir_path( __DIR__ ) . $mofile );

        // Load the translations directory relative to the plugin directory
        load_plugin_textdomain( 'disable-email-notification-for-auto-updates', false, 'disable-email-notification-for-auto-updates/languages' );
    }



}
