<?php
/*
 * Template Name: Content Question DATABASE
 * Template Post Type: ieltsspeakingtests
 */
get_header();

require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM ielts_speaking_part_1_question";
$sql2 = "SELECT * FROM ielts_speaking_part_2_question";
$sql3 = "SELECT * FROM ielts_speaking_part_3_question";

$result = $conn->query($sql);
$result2 = $conn->query($sql2);
$result3 = $conn->query($sql3);

function display_questions_by_topic($result, $button_class, $part_number) {
    $id_tests = [];
    $topics = [];

      // Group questions by topic and id_test
  while ($row = $result->fetch_assoc()) {
    $id_test = $row['id_test'];
    $topic = $row['topic']; // Get the correct topic for each row

    // Store topic and questions grouped by id_test
    if (!isset($id_tests[$id_test])) {
        $id_tests[$id_test] = [
            'topic' => $topic,
            'questions' => []
        ];
    }
    $id_tests[$id_test]['questions'][] = $row['question_content'];
}

   

    foreach ($id_tests as $id_test => $data) {
      $topic = $data['topic']; // Correctly retrieve the topic
      $questions = $data['questions'];

      echo "<tr>
              <td>$topic</td>
              <td>$id_test</td>
              <td>" . count($questions) . " questions</td>
                <td>
                    <input type='checkbox' class='part-checkbox' data-part='$part_number' data-id-test='$id_test'>
                </td>
                <td>
                    <button class='btn $button_class' onclick='showModal(\"" . addslashes($id_test) . "\", " . json_encode($questions) . ")'>View</button>
                </td>
            </tr>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IELTS Speaking Questions</title>

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .selected-button {
        display: none;
    }
    
    .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

table {
    width: 100%;
}

  
</style>
<body>

<div class="container-fluid mt-5">
    <h2 class="text-center mb-4">Choose all 3 parts of Speaking Test</h2>
    
    <div class="row">
        <!-- Speaking Part 1 -->
        <div class="col-12 col-sm-6 col-md-4">
        <div class="card">
                <div class="card-header bg-primary text-white">Speaking Part 1</div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>ID Test</th>
                                <th>Number of Questions</th>
                                <th>Select</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php display_questions_by_topic($result, 'btn-primary', 1); ?>
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
        </div>

        <!-- Speaking Part 2 -->
        <div class="col-12 col-sm-6 col-md-4">
        <div class="card">
                <div class="card-header bg-secondary text-white">Speaking Part 2</div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>ID Test</th>
                                <th>Number of Questions</th>
                                <th>Select</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php display_questions_by_topic($result2, 'btn-secondary', 2); ?>
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>

        <!-- Speaking Part 3 -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="card">
                  <div class="card-header bg-dark text-white">Speaking Part 3</div>
                  <div class="card-body">
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                              <tr>
                                  <th>Topic</th>
                                  <th>ID Test</th>
                                  <th>Number of Questions</th>
                                  <th>Select</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                          <?php display_questions_by_topic($result3, 'btn-dark', 3); ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
        </div>
    </div>

    <button id="submitBtn" class="btn btn-success selected-button" onclick="submitSelection()">Submit</button>
</div>

<!-- Modal for displaying the question content -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Question Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <!-- Question content will be inserted here via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let selectedParts = {1: null, 2: null, 3: null};

    // Function to show questions modal
    function showModal(id_test, questions) {
        let questionContent = `<strong>ID Test:</strong> ${id_test}<br>`;
        questionContent += `<strong>Total Questions:</strong> ${questions.length}<br><br>`;
        
        questions.forEach((question, index) => {
            questionContent += `<strong>Q${index + 1}:</strong> ${question}<br>`;
        });

        document.getElementById('modal-body-content').innerHTML = questionContent;
        var myModal = new bootstrap.Modal(document.getElementById('questionModal'));
        myModal.show();
    }

    // Function to handle checkbox changes
    document.querySelectorAll('.part-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            let part = this.getAttribute('data-part');
            let idTest = this.getAttribute('data-id-test');

            // Allow only one checkbox per part
            document.querySelectorAll(`.part-checkbox[data-part="${part}"]`).forEach(cb => {
                if (cb !== this) cb.checked = false;
            });

            // Store the selected checkbox's ID Test
            if (this.checked) {
                selectedParts[part] = idTest;
            } else {
                selectedParts[part] = null;
            }

            // Check if all 3 parts are selected
            if (selectedParts[1] && selectedParts[2] && selectedParts[3]) {
                document.getElementById('submitBtn').style.display = 'block';
            } else {
                document.getElementById('submitBtn').style.display = 'none';
            }
        });
    });

    // Function to handle submit button click
    function submitSelection() {
    if (selectedParts[1] && selectedParts[2] && selectedParts[3]) {
        // Gather the selected id_test values
        const part1Id = selectedParts[1];
        const part2Id = selectedParts[2];
        const part3Id = selectedParts[3];

        // Construct the URL with the selected IDs using unique parameter names
        const redirectUrl = `/wordpress/ieltsspeakingtests/full-ielts-speaking-test/?part1_id=${part1Id}&part2_id=${part2Id}&part3_id=${part3Id}`;

        // Redirect to the constructed URL
        window.location.href = redirectUrl;
    } else {
        alert('Please select 1 ID Test from each part.');
    }
}

</script>

</body>
</html>
