<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = wp_kses_post($_POST['number']);
    
    // Fetch the record from the database
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ielts_speaking_part_2_question WHERE number = %d", $number), ARRAY_A);

    if ($row) {
?>
        <form method="post" action="update_question.php">
            <input type="hidden" name="number" value="<?php echo esc_attr($row['number']); ?>">
            ID Test: <input type="text" name="id_test" value="<?php echo esc_attr($row['id_test']); ?>" required><br>
            Topic: <input type="text" name="topic" value="<?php echo esc_attr($row['topic']); ?>" required><br>
            Question Content: <textarea name="question_content" required><?php echo esc_textarea($row['question_content']); ?></textarea><br>
            Sample: <textarea name="sample"><?php echo esc_textarea($row['sample']); ?></textarea><br>
            Important Add: <textarea name="important_add"><?php echo esc_textarea($row['important_add']); ?></textarea><br>
            Speaking Part: <input type="number" name="speaking_part" value="<?php echo esc_attr($row['speaking_part']); ?>" required><br>
            <input type="submit" value="Update">
        </form>
<?php
    } else {
        echo "No record found.";
    }
}
?>
