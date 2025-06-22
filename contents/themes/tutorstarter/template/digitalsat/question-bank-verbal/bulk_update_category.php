<?php
require_once(__DIR__ . '/../../admin_panel/config-custom.php');
global $wpdb;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    $category = isset($_POST['category']) ? wp_unslash($_POST['category']) : '';

    if (empty($ids) || empty($category)) {
        echo 'Thiếu dữ liệu đầu vào.';
        exit;
    }

    // Sanitize and prepare
    $ids_sanitized = array_map('sanitize_text_field', $ids);
    $placeholders = implode(',', array_fill(0, count($ids_sanitized), '%s'));

    // Query string
    $sql = "UPDATE digital_sat_question_bank_verbal 
            SET category = %s 
            WHERE id_question IN ($placeholders)";

    // Combine params
    $params = array_merge([$category], $ids_sanitized);

    // Prepare and run
    $prepared = $wpdb->prepare($sql, ...$params);
    $result = $wpdb->query($prepared);

    if ($result !== false) {
        echo "Cập nhật thành công $result câu hỏi.";
    } else {
        echo "Lỗi: " . $wpdb->last_error;
    }
}
?>
