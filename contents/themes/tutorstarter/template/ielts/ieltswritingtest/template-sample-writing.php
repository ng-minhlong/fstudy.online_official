<?php
/*
 * Template Name: Result Sample Writing
 * Template Post Type: ieltswritingtests
 */

get_header();
$post_id = get_the_ID();

// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_ieltswritingtests_custom_number', true);
$custom_number =intval(get_query_var('id_test'));

echo "<script>console.log('Custom Number doing template: " . esc_js($custom_number) . "');</script>";
echo"<h3>Sample Writing</h3>";

get_footer();