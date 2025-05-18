<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = intval($_POST['number']);
    
    // Fetch the record from the database
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM digital_sat_question_bank_math WHERE number = %d", $number), ARRAY_A);

    if ($row) {
?>
        <form method="post" action="update_question.php">
            <input type="hidden" name="number" value="<?php echo esc_attr($row['number']); ?>">
            ID Question: <input type="text" name="id_question" value="<?php echo esc_attr($row['id_question']); ?>" required><br>
            type_question: <input type="text" name="type_question" value="<?php echo esc_attr($row['type_question']); ?>" required><br>

            question_content: <input type="text" name="question_content" value="<?php echo esc_attr($row['question_content']); ?>" required><br>
            answer_1: <input type="text" name="answer_1" value="<?php echo esc_attr($row['answer_1']); ?>" required><br>
            answer_2: <textarea name="answer_2" required><?php echo esc_textarea($row['answer_2']); ?></textarea><br>
            answer_3: <textarea name="answer_3"><?php echo esc_textarea($row['answer_3']); ?></textarea><br>
            answer_4: <textarea name="answer_4"><?php echo esc_textarea($row['answer_4']); ?></textarea><br>
            correct_answer: <input name="correct_answer" value="<?php echo esc_attr($row['correct_answer']); ?>" required><br>
            explanation: <textarea name="explanation"><?php echo esc_textarea($row['explanation']); ?></textarea><br>
            image_link: <input name="image_link" value="<?php echo esc_attr($row['image_link']); ?>"><br>
            
            <input type="submit" value="Update">
        </form>
<?php
    } else {
        echo "No record found.";
    }
}
?>
