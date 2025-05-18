<?php
/*
 * Template Name: Result Template Speaking 
 * Template Post Type: ieltsspeakingtests
 */

get_header();
$post_id = get_the_ID();

// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_ieltsspeakingtests_custom_number', true);
$custom_number =intval(get_query_var('id_test'));


  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;
$commentcount = get_comments_number( $post->ID );

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Prepare the SQL query to fetch question_content and stt where id_test matches the custom_number
$sql = "SELECT stt, question_content, topic, speaking_part, sample  FROM ielts_speaking_part_1_question WHERE id_test = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $custom_number); // 'i' is used for integer
$stmt->execute();
$result1 = $stmt->get_result();

// Prepare the SQL query to fetch question_content and stt where id_test matches the custom_number
$sql2 = "SELECT question_content, topic, speaking_part, sample  FROM ielts_speaking_part_2_question WHERE id_test = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $custom_number); // 'i' is used for integer
$stmt2->execute();
$result2 = $stmt2->get_result();


// Prepare the SQL query to fetch question_content and stt where id_test matches the custom_number
$sql3 = "SELECT stt, question_content, topic, speaking_part, sample  FROM ielts_speaking_part_3_question WHERE id_test = ?";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $custom_number); // 'i' is used for integer
$stmt3->execute();
$result3 = $stmt3->get_result();


global $wpdb;

// Get current user's username
$current_user = wp_get_current_user();
$current_username = $current_user->user_login;

// Get results for the current user and specific idtest (custom_number)
$results_query = $wpdb->prepare("
    SELECT * FROM save_user_result_ielts_speaking 
    WHERE username = %s 
    AND idtest = %d
    ORDER BY dateform DESC",
    $current_username,
    $custom_number
);
$results = $wpdb->get_results($results_query); ?>
 <?php     
            // Check if there are any results and display them
            if ($result1->num_rows > 0) {
                echo "<h3>Ielts Speaking Part 1 Questions:</h3>";
                echo "<p>Speaking Part 1</p>";
                echo "<table border='1'>
                        <tr>
                            <th>STT</th>
                            <th>Question Content</th>
                            <th>Title</th>
                            <th>Sample</th>
                            <th>Speaking part</th>
                        </tr>";
                while ($row = $result1->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['stt']) . "</td>
                            <td>" . htmlspecialchars($row['question_content']) . "</td>
                            <td>" . htmlspecialchars($row['topic']) . "</td>
                            <td>" . htmlspecialchars($row['sample']) . "</td>
                            <td>" . htmlspecialchars($row['speaking_part']) . "</td>
                        </tr>";
                }
                echo "</table>";
            }
            else if ($result2->num_rows > 0) {
                echo "<h3>Ielts Speaking Part 2 Questions:</h3>";
                echo "<p>Speaking Part 2</p>";
                echo "<table border='1'>
                        <tr>
                             <th>STT</th>
                            <th>Question Content</th>
                            <th>Title</th>
                            <th>Sample</th>
                            <th>Speaking part</th>
                        </tr>";
                while ($row = $result2->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['stt']) . "</td>
                            <td>" . htmlspecialchars($row['question_content']) . "</td>
                            <td>" . htmlspecialchars($row['topic']) . "</td>
                            <td>" . htmlspecialchars($row['sample']) . "</td>
                            <td>" . htmlspecialchars($row['speaking_part']) . "</td>
                            
                        </tr>";
                }
                echo "</table>";
            }
            else if ($result3->num_rows > 0) {
                echo "<h3>Ielts Speaking Part 3 Questions:</h3>";
                echo "<p>Speaking Part 3</p>";
                echo "<table border='1'>
                        <tr>
                            <th>STT</th>
                            <th>Question Content</th>
                            <th>Title</th>
                            <th>Sample</th>
                            <th>Speaking part</th>
                        </tr>";
                while ($row = $result3->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['stt']) . "</td>
                            <td>" . htmlspecialchars($row['question_content']) . "</td>
                            <td>" . htmlspecialchars($row['topic']) . "</td>
                            <td>" . htmlspecialchars($row['sample']) . "</td>
                            <td>" . htmlspecialchars($row['speaking_part']) . "</td>
                        </tr>";
                }
                echo "</table>";
            }
            else {
                echo "No questions found for this test.";
            }
            



echo "<script>console.log('ID TEST: " . esc_js($custom_number) . "');</script>";

if (is_user_logged_in()) {
   echo '<h3>Samle Speaking</h3>';
} else {
    echo '<p>Please log in to view your test results.</p>';
}

get_footer();