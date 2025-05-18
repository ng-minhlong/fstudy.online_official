<?php
function get_account_package() {
    global $wpdb;
    $table =   'admin_create';

    $results = $wpdb->get_results("
        SELECT id, type, user_role, date_created, price_role, note_role, time_expired
        FROM $table
        WHERE type = 'role'
    ", ARRAY_A);

    if (empty($results)) {
        return rest_ensure_response([]);
    }

    return rest_ensure_response($results);
}
