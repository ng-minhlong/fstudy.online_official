<?php
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    global $wpdb;

    $number = intval($_POST['number']);
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM lessons_management WHERE number = %d", $number));

    if ($result) {
        // Return JSON response
        echo json_encode($result);
    } else {
        // Handle error case
        echo json_encode(array('error' => 'Record not found.'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request.'));
}
?>
