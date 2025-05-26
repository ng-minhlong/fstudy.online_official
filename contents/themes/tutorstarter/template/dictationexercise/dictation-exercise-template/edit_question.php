<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = intval($_POST['number']);
    
    // Fetch the record from the database
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM dictation_question WHERE number = %d", $number), ARRAY_A);

    if ($row) {
?>
        <form method="post" action="update_question.php">
            <input type="hidden" name="number" value="<?php echo esc_attr($row['number']); ?>">
            ID Test: <input type="text" name="id_test" value="<?php echo esc_attr($row['id_test']); ?>" required><br>
            Type Test: <input type="text" name="type_test" value="<?php echo esc_attr($row['type_test']); ?>" required><br>
            Test Name: <input type="number" name="testname" value="<?php echo esc_attr($row['testname']); ?>" required><br>
            Script Paragraph: <textarea name="script_paragraph"><?php echo esc_textarea($row['script_paragraph']); ?></textarea><br>
            <input type="submit" value="Update">
        </form>
<?php
    } else {
        echo "No record found.";
    }
}
?>
