<?php
    // Include WordPress functions
    require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

    global $wpdb;

    $number = wp_kses_post($_POST['number']);
    $id_test = wp_kses_post($_POST['id_test']);
    $package_name = wp_kses_post($_POST['package_name']);
    $package_category = wp_kses_post($_POST['package_category']);
    $package_detail = wp_unslash($_POST['package_detail']);
    $status = wp_kses_post($_POST['status']);
    $number_of_vocab = wp_kses_post($_POST['number_of_vocab']);
    $package_note = wp_kses_post($_POST['package_note']);



    $data = array(
        'id_test' => $id_test,
        'package_name' => $package_name,
        'package_category' => $package_category,
        'package_detail' => $package_detail,
        'status' => $status,
        'package_note' => $package_note,
        'number_of_vocab' => $number_of_vocab,
      
    );

// Update the record in the database
$wpdb->update('list_vocabulary_package', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
