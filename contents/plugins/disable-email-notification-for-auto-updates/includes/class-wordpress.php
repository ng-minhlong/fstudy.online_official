<?php
class ITC_Disable_Update_Notifications_Wordpress{

   /*Disable WordPress core updates email */
function wp_updates_email( $send, $type, $core_update, $result ) {
	if ( ! empty( $type ) && $type == 'success' ) {
	return false;
	}
	return true;
	}
}