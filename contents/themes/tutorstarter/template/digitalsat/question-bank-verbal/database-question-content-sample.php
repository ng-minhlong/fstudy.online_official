<?php
/*
 * Template Name: Digital SAT Question Bank DATABASE
 */

 $servername = DB_HOST;
 $username = DB_USER;
 $password = DB_PASSWORD;
 $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter value from the form input
$id_question_filter = isset($_GET['id_question_filter']) ? $_GET['id_question_filter'] : '';

// Pagination logic
$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM digital_sat_question_bank_verbal";
if ($id_question_filter) {
    $total_sql .= " WHERE id_question LIKE '%$id_question_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM digital_sat_question_bank_verbal";
if ($id_question_filter) {
    $sql .= " WHERE id_question LIKE '%$id_question_filter%'"; // Apply filter to the SQL query
}
$sql .= " LIMIT $limit OFFSET $offset"; // Add pagination limits
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Digital Practice SAT Questions Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Digital Practice SAT Questions Database - VERBAL (Reading - Writing) only</h1>
Practice:
<p>Verbal100 - Verbal206: Boundaries</p>
<p>Verbal207 - Verbal272: Central ideas and detail</p>
<p>Verbal273 - Verbal376: Form, Structure and Sense</p>
<p>Verbal377 - Verbal450: Inferences </p>
<p>Verbal451 - verbal493: Cross Text Connections</p>
<p>Verbal494 - verbal623: Words in context</p>
<p>Verbal624 - verbal695: Text Structure and Purpose</p>
<p>Verbal696 - verbal781: Transition</p>
<p>Verbal782 - verbal884: Rhetorical Analysis</p>
<p>Verbal885 - verbal1035: Command of Evidence	</p>

Full test:
<p>Verbal1036 - : Practice Test 1 - (verbal: M1: verbal1036-verbal1067)</p>

<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_question_filter" class="form-label">Filter by ID Question:</label>
            <input type="text" name="id_question_filter" id="id_question_filter" class="form-control" value="<?php echo isset($_GET['id_question_filter']) ? $_GET['id_question_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>STT</th>
        
        <th>ID Question</th>
        <th>Type</th>
        <th>Question</th>
        <th>answer_1</th>
        <th>answer_2</th>
        <th>answer_3</th>
        <th>answer_4</th>
        <th>Correct Answer</th>
        <th>Explanation</th>
        <th>Image Link (if have)</th>
        <th>Phân loại câu hỏi</th>

    </tr>

    <?php
        $stt = $offset + 1; // Initialize STT based on pagination offset

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                 // Process "Sample" and "Important Add" columns
                 $question_content_words = explode(' ', $row['question_content']);
                 $question_content_display = count($question_content_words) > 20 ? implode(' ', array_slice($question_content_words, 0, 20)) . '...' : $row['question_content'];
                 $question_content_view_more = count($question_content_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Question Content\", \"{$row['question_content']}\")'>View More</button>" : '';
 
                 $explanation_words = explode(' ', $row['explanation']);
                 $explanation_display = count($explanation_words) > 20 ? implode(' ', array_slice($explanation_words, 0, 20)) . '...' : $row['explanation'];
                 $explanation_view_more = count($explanation_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Explanation Add\", \"{$row['explanation']}\")'>View More</button>" : '';


                 $image_link = $row['image_link'];
                 $image_link_display = (strpos($image_link, 'https://') === 0) 
                     ? substr($image_link, 0, 5) . '...' 
                     : (strlen($image_link) > 20 ? substr($image_link, 0, 20) . '...' : $image_link);
                 $image_link_view_more = (strlen($image_link) > 20) 
                     ? "<button class='btn btn-link' onclick='showFullContent(\"Image Link\", \"{$image_link}\")'>View More</button>" 
                     : '';
                 


                echo "<tr id='row_{$row['number']}'>
                        <td>{$stt}</td> <!-- Display the STT here -->

                        <td>{$row['id_question']}</td>
                        <td>{$row['type_question']}</td>
                        <td>{$question_content_display} $question_content_view_more</td>
                        <td>{$row['answer_1']}</td>
                        <td>{$row['answer_2']}</td>
                        <td>{$row['answer_3']}</td>
                        <td>{$row['answer_4']}</td>
                        <td>{$row['correct_answer']}</td>
                        <td>{$explanation_display} $explanation_view_more</td>
                        <td>{$image_link_display} $image_link_view_more</td>
                        <td>{$row['category']}</td>
                        <td>
                            <button class='btn btn-primary btn-sm' onclick='openEditModal({$row['number']})'>Edit</button>
                            <button class='btn btn-danger btn-sm' onclick='deleteRecord({$row['number']})'>Delete</button>
                        </td>
                      </tr>";
                      $stt++; // Increment STT for each row
            }
        } else {
            echo "<tr><td colspan='9'>No data found</td></tr>";
        }
        ?>




</table>
  <!-- Pagination buttons -->
  <!-- Pagination buttons -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1&id_question_filter=<?php echo $id_question_filter; ?>">First</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php 
        // Define the range of page buttons to display
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&id_question_filter=<?php echo $id_question_filter; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $total_pages; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Last</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Button to Add New Record -->
<button class="btn btn-success" onclick="openAddModal()">Add New Question</button>
<!-- View More Modal -->
<div class="modal" id="viewMoreModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMoreTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewMoreContent">
                <!-- Full content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form id="editForm">
                    <input type="hidden" id="edit_number" name="number">

                    ID Question: <textarea id="edit_id_question" name="id_question" class="form-control" required></textarea><br>

                    Type: <input id="edit_type_question" name="type_question" class="form-control" required value="multiple-choice" readonly ></input><br>

                    Question: <textarea id="edit_question_content" name="question_content" class="form-control" required></textarea><br>
                    Answer_1: <input type="text" id="edit_answer_1" name="answer_1" class="form-control" required></input><br>
                    Answer_2: <input type="text" id="edit_answer_2" name="answer_2" class="form-control" required></input><br>
                    Answer_3: <input type="text" id="edit_answer_3" name="answer_3" class="form-control"></input><br>
                    Answer_4: <input type="text" id="edit_answer_4" name="answer_4" class="form-control"   required></input><br>
                    Choose Correct Answer:
                    <select id="edit_correct_answer" name="correct_answer" class="form-control" required>
                        <option value=""></option>
                        <option value="answer_1">Answer A</option>
                        <option value="answer_2">Answer B</option>
                        <option value="answer_3">Answer C</option>
                        <option value="answer_4">Answer D</option>

                    </select><br>
                    explanation: <textarea  id="edit_explanation" name="explanation" class="form-control"   required></textarea><br>
                    image_link: <input type="text" id="edit_image_link" name="image_link" class="form-control"  ></input><br>

                    Phân loại câu hỏi:
                    <select id="edit_category" name="category" class="form-control" required>
                        <option value=""></option>
                        <option value="Boundaries">Boundaries</option>
                        <option value="Central ideas and detail">Central ideas and detail</option>
                        <option value="Form, Structure and Sense">Form, Structure and Sense</option>
                        <option value="Inferences">Inferences</option>
                        <option value="Cross Text Connections">Cross Text Connections</option>
                        <option value="Words in context">Words in context</option>
                        <option value="Text Structure and Purpose">Text Structure and Purpose</option>
                        <option value="Transition">Transition</option>
                        <option value="Rhetorical Analysis">Rhetorical Analysis</option>

                    </select><br>

                    

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form id="addForm">
                    ID Question: 
                    <div class="input-group">
                        <span class="input-group-text">verbal</span>
                        <input type="text" id="add_id_question" name="id_question" class="form-control" required>
                    </div>
                    <br>
                    type_question: <input  id="add_type_question" name="type_question" class="form-control" value="multiple-choice" readonly  required></input><br>

                    question_content: <textarea  id="add_question_content" name="question_content" class="form-control" required></textarea><br>
                    Answer_1: <input type="text" id="add_answer_1" name="answer_1" class="form-control" required></input><br>
                    Answer_2: <input type="text" id="add_answer_2" name="answer_2" class="form-control" required></input><br>
                    Answer_3: <input type="text" id="add_answer_3" name="answer_3" class="form-control" required></input><br>
                    Answer_4: <input type="text" id="add_answer_4" name="answer_4" class="form-control"   required></input><br>
                    Choose Correct Answer:
                    <select id="add_correct_answer" name="correct_answer" class="form-control" required>
                        <option value=""></option>
                        <option value="answer_1">Answer A</option>
                        <option value="answer_2">Answer B</option>
                        <option value="answer_3">Answer C</option>
                        <option value="answer_4">Answer D</option>

                    </select><br>                   
                    explanation: <textarea id="add_explanation" name="explanation" class="form-control"   required></textarea><br>
                    image_link: <input type="text" id="add_image_link" name="image_link" class="form-control"  ></input><br>
                    Phân loại câu hỏi:
                    <select id="add_category" name="category" class="form-control" required>
                        <option value=""></option>
                        <option value="Boundaries">Boundaries</option>
                        <option value="Central ideas and detail">Central ideas and detail</option>
                        <option value="Form, Structure and Sense">Form, Structure and Sense</option>
                        <option value="Inferences">Inferences</option>
                        <option value="Cross Text Connections">Cross Text Connections</option>
                        <option value="Words in context">Words in context</option>
                        <option value="Text Structure and Purpose">Text Structure and Purpose</option>
                        <option value="Transition">Transition</option>
                        <option value="Rhetorical Analysis">Rhetorical Analysis</option>

                    </select><br>
                
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Question</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- jQuery and JavaScript for AJAX -->
<script>
// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_question').val(data.id_question);
            $('#edit_type_question').val(data.type_question);
            $('#edit_question_content').val(data.question_content);
            $('#edit_answer_1').val(data.answer_1);
            $('#edit_answer_2').val(data.answer_2);
            $('#edit_answer_3').val(data.answer_3);
            $('#edit_answer_4').val(data.answer_4);
            $('#edit_correct_answer').val(data.correct_answer);
            $('#edit_explanation').val(data.explanation);
            $('#edit_image_link').val(data.image_link);
            $('#editModal').modal('show');
        }
    });
}


function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data
    $('#addModal').modal('show'); // Show the modal
}

// Format text by replacing newlines with <br> tags
function formatTextWithLineBreaks(text) {
    return text.replace(/\n/g, '<br>');
}

// Modify saveEdit function to include formatted content
function saveEdit() {
    const idQuestionField = document.getElementById('edit_id_question');
    if (!idQuestionField.value.startsWith('verbal')) {
        idQuestionField.value = 'verbal' + idQuestionField.value;
    }

    let questionContent = $('#edit_question_content').val();
    const explanation = $('#edit_explanation').val();

    // Remove 'ID: [some_id]' from question_content
    questionContent = questionContent.replace(/ID:\s*\w+/g, '').trim();

    // Replace 'Text 1' and 'Text 2' with underlined versions
    questionContent = questionContent.replace(/Text 1/g, '<b>Text 1</b>');
    questionContent = questionContent.replace(/Text 2/g, '<b>Text 2</b>');

    // Remove prefixes 'A. ', 'B. ', 'C. ', 'D. ' from answer fields
    $('#edit_answer_1').val($('#edit_answer_1').val().replace(/^A\. /, '').trim());
    $('#edit_answer_2').val($('#edit_answer_2').val().replace(/^B\. /, '').trim());
    $('#edit_answer_3').val($('#edit_answer_3').val().replace(/^C\. /, '').trim());
    $('#edit_answer_4').val($('#edit_answer_4').val().replace(/^D\. /, '').trim());

    // Apply formatting
    $('#edit_question_content').val(formatTextWithLineBreaks(questionContent));
    $('#edit_explanation').val(formatTextWithLineBreaks(explanation));

    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Modify saveNew function to include formatted content
function saveNew() {
    const idQuestionField = document.getElementById('add_id_question');
    idQuestionField.value = 'verbal' + idQuestionField.value;

    let questionContent = $('#add_question_content').val();
    const explanation = $('#add_explanation').val();

    // Remove 'ID: [some_id]' from question_content
    questionContent = questionContent.replace(/ID:\s*\w+/g, '').trim();

    // Replace 'Text 1' and 'Text 2' with underlined versions
    questionContent = questionContent.replace(/Text 1/g, '<b>Text 1</b>');
    questionContent = questionContent.replace(/Text 2/g, '<b>Text 2</b>');

    // Remove prefixes 'A. ', 'B. ', 'C. ', 'D. ' from answer fields
    $('#add_answer_1').val($('#add_answer_1').val().replace(/^A\. /, '').trim());
    $('#add_answer_2').val($('#add_answer_2').val().replace(/^B\. /, '').trim());
    $('#add_answer_3').val($('#add_answer_3').val().replace(/^C\. /, '').trim());
    $('#add_answer_4').val($('#add_answer_4').val().replace(/^D\. /, '').trim());

    // Apply formatting
    $('#add_question_content').val(formatTextWithLineBreaks(questionContent));
    $('#add_explanation').val(formatTextWithLineBreaks(explanation));

    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/add_question.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/delete_question.php',
            type: 'POST',
            data: { number: number },
            success: function(response) {
                location.reload(); // Reload after deletion
            }
        });
    }
}
function showFullContent(title, content) {
    // Set the title of the modal
    $('#viewMoreTitle').text(title);

    // Set the content with HTML allowed
    $('#viewMoreContent').html(content);

    // Show the modal
    $('#viewMoreModal').modal('show');
}


</script>


</body>
</html>
