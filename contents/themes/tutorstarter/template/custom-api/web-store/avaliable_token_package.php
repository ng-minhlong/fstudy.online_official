<?php
function get_token_package() {
    global $wpdb;
    $table =  'admin_create';

    $results = $wpdb->get_results("
        SELECT id, type, user_token, date_created, price_token, note_token
        FROM $table
        WHERE type = 'token'
    ", ARRAY_A);

    return rest_ensure_response($results);
}
