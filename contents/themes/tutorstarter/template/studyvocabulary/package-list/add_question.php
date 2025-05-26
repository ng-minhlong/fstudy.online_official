<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input data
    $id_test = wp_kses_post($_POST['id_test']);
    $package_name = wp_kses_post($_POST['package_name']);
    $package_category = wp_kses_post($_POST['package_category']);
    $package_detail = wp_unslash($_POST['package_detail']);
    $package_note = wp_kses_post($_POST['package_note']);
    $status = wp_kses_post($_POST['status']);
    $number_of_vocab = wp_kses_post($_POST['number_of_vocab']);


    // Prepare data for insertion
    $data = array(
        'id_test' => $id_test,
        'package_name' => $package_name,
        'package_category' => $package_category,
        'package_detail' => $package_detail,
        'package_note' => $package_note,

        'status' => $status,
        'number_of_vocab' => $number_of_vocab,

        'date_created' => current_time('mysql')
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('list_vocabulary_package', $data);

    if ($inserted) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $wpdb->last_error;
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit;
}
?>
