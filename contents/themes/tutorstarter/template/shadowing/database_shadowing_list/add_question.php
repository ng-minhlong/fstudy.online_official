<?php
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_test = wp_kses_post($_POST['id_test']);
    $type_test = wp_kses_post($_POST['type_test']);
    $testname = wp_unslash($_POST['testname']);
    $id_video = wp_kses_post($_POST['id_video']);
    $transcript = wp_unslash($_POST['transcript']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);


    // Prepare the data for insertion
    $data = array(
        'id_test' => $id_test,
        'type_test' => $type_test,
        'testname' => $testname,
        'id_video' => $id_video,
        'transcript' => $transcript,
        'token_need' => $token_need,
        'role_access' => $role_access,
        'time_allow' => $time_allow,

    );

    // Insert the data into the database
    $inserted = $wpdb->insert('shadowing_question', $data);

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
