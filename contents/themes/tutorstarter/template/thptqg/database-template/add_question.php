<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_test = wp_kses_post($_POST['id_test']);
    $subject = wp_kses_post($_POST['subject']);
    $year = wp_kses_post($_POST['year']);
    $testname = wp_kses_post($_POST['testname']);
    $link_file = wp_kses_post($_POST['link_file']);
    $time = wp_kses_post($_POST['time']);
    $number_question = wp_kses_post($_POST['number_question']);
    $testcode = wp_unslash(wp_kses_post($_POST['testcode']));
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);
    // Prepare the data for insertion
    $data = array(
        'id_test' => $id_test,
        'subject' => $subject,
        'year' => $year,
        'testname' => $testname,
        'link_file' => $link_file,
        'time' => $time,
        'number_question' => $number_question,
        'testcode' => $testcode,
        'token_need' => $token_need,
        'role_access' => $role_access,
        'time_allow' => $time_allow
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('thptqg_question', $data);

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
