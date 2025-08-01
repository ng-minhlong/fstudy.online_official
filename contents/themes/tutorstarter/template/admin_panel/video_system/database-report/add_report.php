<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$report_id = wp_kses_post($_POST['report_id']);
$date_created = wp_kses_post($_POST['date_created']);
$report_by_username = wp_kses_post($_POST['report_by_username']);
$report_by_user_id = wp_kses_post($_POST['report_by_user_id']);
$course_id = wp_kses_post($_POST['course_id']);
$source_type = wp_kses_post($_POST['source_type']);
$source = wp_kses_post($_POST['source']);
$error_log = wp_kses_post($_POST['error_log']);

// Prepare the data for updating
$data = array(
    'report_id' => $report_id,
    'date_created' => $date_created,
    'report_by_username' => $report_by_username,
    'report_by_user_id' => $report_by_user_id,
    'course_id' => $course_id,
    'source_type' => $source_type,
    'source' => $source,
    'error_log' => $error_log
);

    // Insert the data into the database
    $inserted = $wpdb->insert('report_lessons_link', $data);

    if ($inserted) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $wpdb->last_error; // Fetch last error if any
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit; // Always call exit after redirecting
}
?>
