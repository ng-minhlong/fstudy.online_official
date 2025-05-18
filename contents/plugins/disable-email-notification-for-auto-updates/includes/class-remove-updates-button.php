<?php
class ITC_Disable_Update_Notifications_WP_Update_Button {
    // Remove the updates menu item from the admin dashboard
    public function remove_update_menu_itc() {
        remove_submenu_page( 'index.php', 'update-core.php' );
    }
}
