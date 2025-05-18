<?php
    require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

    global $wpdb;

    // Get the data from the POST request
    $number = wp_kses_post($_POST['number']);
    $id_test = wp_kses_post($_POST['id_test']);
    $type_test = wp_kses_post($_POST['type_test']);
    $testname = wp_unslash($_POST['testname']);
    $id_video = wp_kses_post($_POST['id_video']);
    $transcript = wp_unslash($_POST['transcript']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);
    // Prepare the data for updating
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

    // Update the record in the database
    $wpdb->update('shadowing_question', $data, array('number' => $number));

    // Return a response
    echo json_encode(array('status' => 'success'));
?>
