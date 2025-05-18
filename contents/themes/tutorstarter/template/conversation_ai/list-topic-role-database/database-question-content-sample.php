<?php
/*
 * Template Name: Reading Question DATABASE
 */

$servername = "localhost";
$username = "root";
$password = ""; // No password by default
$dbname = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

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

$total_sql = "SELECT COUNT(*) FROM conversation_with_ai_list";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM conversation_with_ai_list";
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
    <title>Conservation AI List </title>
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

<h1>Conservation AI List </h1>

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
        <th>testname</th>
        <th>instruction</th>
        <th>target_1</th>
        <th>target_2</th>
        <th>target_3</th> 
        <th>topic</th>
        <th>User Role</th>
        <th>AI Role</th>
        <th>difficulty</th>
        <th>time_limit</th>
        <th>sentence_limit</th>
        <th>cover_image</th>
        <th>Actions</th>
    </tr>
    <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
     

        echo "<tr id='row_{$row['number']}'>
                <td>{$row['number']}</td>
                <td>{$row['id_test']}</td>
                <td>{$row['testname']}</td>
                <td>{$row['instruction']}</td>
                <td>{$row['target_1']}</td>
                <td>{$row['target_2']}</td>
                <td>{$row['target_3']}</td>
                <td>{$row['topic']}</td>
                <td>{$row['user_role']}</td>
                <td>{$row['ai_role']}</td>
                <td>{$row['difficulty']}</td>
                <td>{$row['time_limit']}</td>
                <td>{$row['sentence_limit']}</td>
                <td>{$row['cover_image']}</td>
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
                    testname: <textarea type="text" id="edit_testname" name="conttestnameext_name" class="form-control" required></textarea><br>
                    instruction: <textarea type="number" id="edit_instruction" name="instruction" class="form-control" required></textarea><br>
                    target_1: <textarea id="edit_target_1" name="target_1" class="form-control" required></textarea><br>
                    target_2: <textarea id="edit_target_2" name="target_2" class="form-control"></textarea><br>
                    target_3: <textarea id="edit_target_3" name="target_3" class="form-control"></textarea><br>
                    topic: <input  id="edit_topic" name="topic" class="form-control" required><br>
                    ai_role: <input  id="edit_ai_role" name="ai_role" class="form-control" required><br>
                    user_role: <input  id="edit_user_role" name="user_role" class="form-control" required><br>
                    difficulty: <input  id="edit_difficulty" name="difficulty" class="form-control" required><br>
                    time_limit: <input  id="edit_time_limit" name="time_limit" class="form-control" required><br>
                    sentence_limit: <input  id="edit_sentence_limit" name="sentence_limit" class="form-control" required><br>
                    cover_image: <input  id="edit_cover_image" name="cover_image" class="form-control" required><br>

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
                    ID Test: <input type="text" id="add_id_test" name="id_test" class="form-control" required><br>
                    testname: <textarea type="text" id="add_testname" name="testname" class="form-control" required></textarea><br>
                    instruction: <textarea type="number" id="add_instruction" name="instruction" class="form-control" required></textarea><br>
                    target_1: <textarea id="add_target_1" name="target_1" class="form-control" required></textarea><br>
                    target_2: <textarea id="add_target_2" name="target_2" class="form-control"></textarea><br>
                    target_3: <textarea id="add_target_3" name="target_3" class="form-control"></textarea><br>
                    topic: <input  id="add_topic" name="topic" class="form-control" required><br>
                    ai_role: <input  id="add_ai_role" name="ai_role" class="form-control" required><br>
                    user_role: <input  id="add_user_role" name="user_role" class="form-control" required><br>
                    difficulty: <input  id="add_difficulty" name="difficulty" class="form-control" required><br>
                    time_limit: <input  id="add_time_limit" name="time_limit" class="form-control" required><br>
                    sentence_limit: <input  id="add_sentence_limit" name="sentence_limit" class="form-control" required><br>
                    cover_image: <input  id="add_cover_image" name="cover_image" class="form-control" required><br>
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
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/conversation_ai/list-topic-role-database/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_testname').val(data.testname);
            $('#edit_instruction').val(data.instruction);
            $('#edit_target_1').val(data.target_1);
            $('#edit_target_2').val(data.target_2);
            $('#edit_target_3').val(data.target_3);
            $('#edit_topic').val(data.topic);
            $('#edit_ai_role').val(data.ai_role);
            $('#edit_user_role').val(data.user_role);
            $('#edit_difficulty').val(data.difficulty);
            $('#edit_time_limit').val(data.time_limit);
            $('#edit_sentence_limit').val(data.sentence_limit);
            $('#edit_cover_image').val(data.cover_image);
            $('#editModal').modal('show');

        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/conversation_ai/list-topic-role-database/update_question.php',
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
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/conversation_ai/list-topic-role-database/add_question.php',
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
            url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/conversation_ai/list-topic-role-database/delete_question.php',
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
