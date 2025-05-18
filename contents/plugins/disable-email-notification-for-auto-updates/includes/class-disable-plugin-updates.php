<?php
class ITC_Disable_Update_Notifications_Wordpress_Plugin {
    private $settings;

    public function __construct( $settings ) {
        $this->settings = $settings;

    }

 // Add custom CSS and JS
public function add_custom_css_js_hide_plugins_itc() {
    if ( ! empty( $this->settings ) ) {
        echo '<style>';
        foreach ( $this->settings as $plugin_key => $enabled ) {
            if ( $enabled ) { // Only inject CSS if the plugin is enabled
                
                $plugin_slug = dirname( $plugin_key );

                // Output CSS 
               echo '#' . esc_attr( $plugin_slug ) . '-update .update-message.notice.inline.notice-warning.notice-alt { 
					visibility: hidden!important; 
					margin-bottom: -36px!important; 
				}';

            }
        }
        echo '</style>';

        // Add JavaScript fallback 
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {';
        foreach ( $this->settings as $plugin_key => $enabled ) {
            if ( $enabled ) {
                // Generate the plugin ID 
                $plugin_slug = dirname( $plugin_key );

                // Output JavaScript to hide the notice for this plugin
                echo '
                var notice = document.querySelector("#' . esc_js( $plugin_slug ) . '-update .update-message.notice.inline.notice-warning.notice-alt");
                if (notice) {
                    notice.style.visibility = "hidden";
					notice.style.marginBottom = "-36px";
                }';
            }
        }
        echo '});
        </script>';
    }
}

    // Disable plugin updates by removing the response for the selected plugins
    public function disable_plugin_updates_itc( $value ) {
        if ( $this->settings ) {
            foreach ( $this->settings as $plugin_key => $enabled ) {
                if ( $enabled && isset( $value->response[$plugin_key] ) ) {
                    unset( $value->response[$plugin_key] );
                }
            }
        }
        return $value;
    }

    // Disable auto-updates for the selected plugins
    public function filter_plugin_auto_update_itc( $update, $item ) {
        $plugin_key = $item->plugin;
        return isset( $this->settings[$plugin_key] ) && $this->settings[$plugin_key] == 1 ? false : $update;
    }
}
