<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$course_id = wp_kses_post($_POST['course_id']);
$youtube_id = wp_kses_post($_POST['youtube_id']);
$youtube_status = wp_kses_post($_POST['youtube_status']);
$abyss_slug = wp_kses_post($_POST['abyss_slug']);
$abyss_status = wp_kses_post($_POST['abyss_status']);
$bunny_slug = wp_kses_post($_POST['bunny_slug']);
$bunny_status = wp_kses_post($_POST['bunny_status']);
$order = wp_kses_post($_POST['order']);

// Prepare the data for updating
$data = array(
    'course_id' => $course_id,
    'youtube_id' => $youtube_id,
    'youtube_status' => $youtube_status,
    'abyss_slug' => $abyss_slug,
    'abyss_status' => $abyss_status,
    'bunny_slug' => $bunny_slug,
    'bunny_status' => $bunny_status,
    'order' => $order

);

    // Insert the data into the database
    $inserted = $wpdb->insert('lessons_management', $data);

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
