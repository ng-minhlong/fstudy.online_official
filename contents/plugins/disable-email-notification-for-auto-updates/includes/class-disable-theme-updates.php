<?php
class ITC_Disable_Update_Notifications_WP_Themes {
	 public function __construct() {
		 
      // add_filter('automatic_updater_disabled', '__return_true');
		 
    }
	
	// Disable WordPress theme updates
	public function wp_theme_updates_itc() {
		remove_action('load-update-core.php', 'wp_update_themes');
	}

	// Hide Last Checked for themes
	public function disable_theme_last_checked_itc() {
		return (object) array(
			'last_checked' => time(),
			'updates' => array()
		);
	}
	
	// Health check
    public function theme_health_check_tests_itc($tests) {
        unset($tests['async']['background_updates']);
        unset($tests['direct']['plugin_theme_auto_updates']);
        return $tests;
    }

}