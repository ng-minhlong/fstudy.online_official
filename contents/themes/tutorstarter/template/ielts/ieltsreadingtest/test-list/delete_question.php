<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = intval($_POST['number']); // Sanitize the input

    // Prepare the SQL statement for deletion
    $deleted = $wpdb->delete('ielts_reading_test_list', array('number' => $number));

    if ($deleted) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $wpdb->last_error; // Fetch last error if any
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit; // Always call exit after redirecting
}
?>
