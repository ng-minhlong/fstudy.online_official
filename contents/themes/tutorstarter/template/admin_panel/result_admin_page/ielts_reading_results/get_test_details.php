<?php
global $wpdb;
// Database credentials
$servername = "localhost";
$username = "root";
$password = ""; // No password by default
$dbname = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_test'])) {
    $id_test = $_POST['id_test'];
    $sql = "SELECT overallband, test_type, username, dateform, testsavenumber FROM save_user_result_ielts_reading WHERE idtest = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id_test);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['test_type']}</td>
                    <td>{$row['overallband']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['dateform']}</td>
                    <td>{$row['testsavenumber']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No details found for this test.</td></tr>";
    }

    $stmt->close();
    $conn->close();
}
?>
