<?php
class ITC_Disable_Update_Notifications_WP_Core {
	
	// Disable WordPress core updates

	public function __construct() {

    }
	
	public function wp_core_updates_itc() {
		remove_action('load-update-core.php', 'wp_update_core');
	}

	// Hide Last Checked
	public function disable_wp_core_last_checked_itc() {
		global $wp_version;
		return (object) array(
			'last_checked' => time(),
			'version_checked' => $wp_version,
			'updates' => array()
		);
	}
	
	// Health check
    public function wp_health_check_tests_itc($tests) {
        unset($tests['async']['background_updates']);
        unset($tests['direct']['plugin_theme_auto_updates']);
        return $tests;
    }
}