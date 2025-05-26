<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_test = wp_unslash($_POST['id_test']);
    $testname = wp_unslash($_POST['testname']);
    $target_1 = wp_unslash($_POST['target_1']);
    $target_2 = wp_unslash($_POST['target_2']);
    $target_3 = wp_unslash($_POST['target_3']);
    $topic = wp_unslash($_POST['topic']);
    $ai_role = wp_unslash($_POST['ai_role']);
    $user_role = wp_unslash($_POST['user_role']);
    $language = wp_unslash($_POST['language']);
    $time_limit = wp_unslash($_POST['time_limit']);
    $sentence_limit = wp_unslash($_POST['sentence_limit']);
    $cover_image = wp_unslash($_POST['cover_image']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);

    // Prepare the data for insertion
    $data = array(
        'testname' => $testname,
        'target_1' => $target_1,
        'target_2' => $target_2,
        'target_3' => $target_3,
        'topic' => $topic,
        'ai_role' => $ai_role,
        'user_role' => $user_role,
        'language' => $language,
        'time_limit' => $time_limit,
        'sentence_limit' => $sentence_limit,
        'cover_image' => $cover_image,
        'id_test' => $id_test,
        'token_need' => $token_need,
        'role_access' => $role_access,
        'time_allow' => $time_allow,

    );

    // Insert the data into the database
    $inserted = $wpdb->insert('conversation_with_ai_list', $data);

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
