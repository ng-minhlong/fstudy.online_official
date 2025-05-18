<?php
/*
 * Template Name: Test LIST DIGITAL SAT
 */

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

// Get filter value from the form input
$id_test_filter = isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : '';

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM digital_sat_test_list";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM digital_sat_test_list";
if ($id_test_filter) {
    $sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to the SQL query
}
$sql .= " LIMIT $limit OFFSET $offset"; // Add pagination limits
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>List các đề Digital SAT</title>
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

<h1>List các đề Digital SAT Database</h1>


<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_test_filter" class="form-label">Filter by ID Test:</label>
            <input type="text" name="id_test_filter" id="id_test_filter" class="form-control" value="<?php echo isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>Number</th>
        <th>ID Test</th>
        <th>Test Name</th>
        <th>Number of Question</th>
        <th>Time (minutes)</th>
        <th>Test Type</th>
        <th>Question Choose</th>
        <th>Tag</th>
        <th>Book</th>
        
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Process "Sample" and "Important Add" columns
                $sample_words = explode(' ', $row['testname']);
                $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['testname'];
                $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Test Name\", \"{$row['testname']}\")'>View More</button>" : '';

                $important_words = explode(',', $row['question_choose']);
                if (count($important_words) > 3) {
                    $important_display = implode(',', array_slice($important_words, 0, 3)) . '...';
                    $important_view_more = "<button class='btn btn-link' onclick='showFullContent(\"Question Choose\", \"{$row['question_choose']}\")'>View More</button>";
                } else {
                    $important_display = $row['question_choose'];
                    $important_view_more = '';
                }

                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['id_test']}</td>
                        <td>{$row['testname']}</td>
                        <td>{$row['number_question']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['test_type']}</td>
                        <td>{$important_display} $important_view_more</td>          
                        <td>{$row['tag']}</td>
                        <td>{$row['book']}</td>
                        <td>
                            <button class='btn btn-primary btn-sm' onclick='openEditModal({$row['number']})'>Edit</button>
                            <button class='btn btn-danger btn-sm' onclick='deleteRecord({$row['number']})'>Delete</button>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No data found</td></tr>";
        }
        ?>




</table>
  <!-- Pagination buttons -->
  <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&id_test_filter=<?php echo $id_test_filter; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>">Next</a></li>
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
                    ID Test: <input type="text" id="edit_id_test" name="id_test" class="form-control" required><br>
                    Test Name: <input type="text" id="edit_testname" name="testname" class="form-control" required><br>
                    Number Question: <input type="text" id="edit_number_question" name="number_question" class="form-control" required><br>
                    Time: <input type="text" id="edit_time" name="time" class="form-control" required><br>
                    Test Type:<select id="edit_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>
                    
                    Question Choice: 
                    <p style = "color: red">Sử dụng lệnh verbal: __ - __ để thêm nhanh(Áp dụng với dãy liên tiếp) hoặc math: __ - __ Chấp nhận mỗi dòng 1 lệnh <br>Ví dụ:<br>verbal: 1 - 10 <br>math: 1 - 10 sẽ thêm verbal1, verbal2,... verbal10, math1,math2,...math10. </p>
                    <textarea id="edit_question_choose" name="question_choose" class="form-control" required></textarea><br>
                    Tag: <textarea id="edit_tag" name="tag" class="form-control"></textarea><br>
                   
                    Book:<select id="edit_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
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
                    <input type="hidden" id="add_number" name="number">
                    ID Test: <input type="text" id="add_id_test" name="id_test" class="form-control" required><br>
                    Test Name: <input type="text" id="add_testname" name="testname" class="form-control" required><br>
                    Number Question: <input type="text" id="add_number_question" name="number_question" class="form-control" required><br>
                    Time: <input type="text" id="add_time" name="time" class="form-control" required><br>

                    Test Type:<select id="add_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>                    
                    
                    Question Choice: 
                    <p style = "color: red">Sử dụng lệnh verbal: __ - __ để thêm nhanh(Áp dụng với dãy liên tiếp) hoặc math: __ - __ Chấp nhận mỗi dòng 1 lệnh <br>Ví dụ:<br>verbal: 1 - 10 <br>math: 1 - 10 sẽ thêm verbal1, verbal2,... verbal10, math1,math2,...math10. </p>

                    <textarea id="add_question_choose" name="question_choose" class="form-control" required></textarea><br>
                    Tag: <textarea id="add_tag" name="tag" class="form-control"></textarea><br>
                    Book:<select id="add_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
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
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/test-list/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_testname').val(data.testname);
            $('#edit_number_question').val(data.number_question);

            $('#edit_time').val(data.time);

            $('#edit_test_type').val(data.test_type);
            $('#edit_question_choose').val(data.question_choose);
            $('#edit_tag').val(data.tag);
            $('#edit_book').val(data.book);
            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/test-list/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload(); // Reload the page to reflect changes
        }
    });
}

function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data
    $('#addModal').modal('show'); // Show the modal
}

// Save the new question
function saveNew() {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/test-list/add_question.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload(); // Reload to show the new record
        }
    });
}

// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/digitalsat/test-list/delete_question.php',
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
