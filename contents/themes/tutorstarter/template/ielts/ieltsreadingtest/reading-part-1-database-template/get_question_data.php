<?php
// Kết nối đến WordPress
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

if (isset($_GET['id_part'])) {
    global $wpdb;

    $id_part = sanitize_text_field($_GET['id_part']);

    // Truy vấn dữ liệu
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT group_question FROM ielts_reading_part_1_question WHERE id_part = %s",
        $id_part
    ), ARRAY_A);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
