<?php

function get_video_data_by_external_link($data) {
    global $wpdb;

    // Lấy base64 từ param
    $encoded_url = $data['external_url'];

    // Nếu client không dùng rawurlencode thì dòng này có thể bỏ:
    $base64_url = rawurldecode($encoded_url);

    // Giải mã ra URL gốc
    $original_url = base64_decode($base64_url);

    if (!$original_url) {
        return new WP_Error('invalid_base64', 'Invalid base64 string', ['status' => 400]);
    }

    $table = 'lessons_management';

    // So sánh với URL gốc (vì DB lưu như vậy)
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE external_url = %s", $original_url),
        ARRAY_A
    );

    if (!$row) {
        error_log("❌ Not found for URL: $original_url");
        return new WP_Error('not_found', 'Video not found', ['status' => 404]);
    }

    error_log("✅ Found video for URL: $original_url");

    return $row;
}
