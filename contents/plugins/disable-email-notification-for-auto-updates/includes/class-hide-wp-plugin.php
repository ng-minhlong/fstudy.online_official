<?php
class ITC_Disable_Update_Notifications_Wordpress_Plugin_hide {
    private $settings;

    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    // Hide plugins from the plugin list
    public function hide_plugins_from_dashboard_itc( $all_plugins ) {
        // Check if settings exist
        if ( $this->settings ) {
            foreach ( $this->settings as $plugin_key_hide => $enabled ) {
                /* Skip hiding this specific plugin
                if ( $plugin_key_hide === 'disable-email-notification-for-auto-updates/itc-disable-email-notification.php' ) {
                    continue; // Skip this plugin and do not hide it
                } */

                // Check if the plugin exists in the all_plugins array
                if ( isset( $all_plugins[$plugin_key_hide] ) ) {
                    if ($enabled) {
                        // If the setting is enabled, remove the plugin from the list
                        unset( $all_plugins[$plugin_key_hide] );
                    }
                }
            }
        }
        return $all_plugins;
    }
}
