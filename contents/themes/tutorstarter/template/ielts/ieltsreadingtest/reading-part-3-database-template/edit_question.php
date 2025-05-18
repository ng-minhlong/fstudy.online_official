<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = intval($_POST['number']);
    
    // Fetch the record from the database
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ielts_reading_part_3_question WHERE number = %d", $number), ARRAY_A);

    if ($row) {
?>
        <form method="post" action="update_question.php">
            <input type="hidden" name="number" value="<?php echo esc_attr($row['number']); ?>">
            ID Test: <input type="text" name="id_part" value="<?php echo esc_attr($row['id_part']); ?>" required><br>
            Part: <input type="text" name="part" value="<?php echo esc_attr($row['part']); ?>" required><br>
            Duration: <input type="number" name="duration" value="<?php echo esc_attr($row['duration']); ?>" required><br>
            Number questions: <textarea name="number_question_of_this_part" required><?php echo esc_textarea($row['number_question_of_this_part']); ?></textarea><br>
            Paragraph: <textarea name="paragraph"><?php echo esc_textarea($row['paragraph']); ?></textarea><br>
            Group Question: <textarea name="group_question"><?php echo esc_textarea($row['group_question']); ?></textarea><br>
            Category: <input name="category" value="<?php echo esc_attr($row['category']); ?>" required><br>
            <input type="submit" value="Update">
        </form>
<?php
    } else {
        echo "No record found.";
    }
}
?>
