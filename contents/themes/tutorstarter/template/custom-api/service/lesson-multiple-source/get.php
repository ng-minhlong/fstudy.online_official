<?php
function get_video_data_by_youtube_id($data) {
    global $wpdb;
    $youtube_id = sanitize_text_field($data['youtube_id']);
    $table =  'lessons_management';

    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE youtube_id = %s", $youtube_id), ARRAY_A);

    if (!$row) {
        return new WP_Error('not_found', 'Video not found', ['status' => 404]);
    }

    return $row;
}