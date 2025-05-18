<?php
/*
 * Template Name: Vocabulary Bank DATABASE
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
$id_filter = isset($_GET['id_filter']) ? $_GET['id_filter'] : '';
$vocabulary_filter = isset($_GET['vocabulary_filter']) ? $_GET['vocabulary_filter'] : '';

// Pagination logic
$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM list_vocabulary";
if ($id_filter) {
    $total_sql .= " WHERE id LIKE '%$id_filter%'"; // Apply filter to total count
}

if ($vocabulary_filter) {
    $total_sql .= " WHERE new_word LIKE '%$vocabulary_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM list_vocabulary";
if ($id_filter) {
    $sql .= " WHERE id LIKE '%$id_filter%'"; // Apply filter to the SQL query
}
if ($vocabulary_filter) {
    $sql .= " WHERE new_word LIKE '%$vocabulary_filter%'"; // Apply filter to the SQL query
}
$sql .= " LIMIT $limit OFFSET $offset"; // Add pagination limits
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vocabulary List</title>
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

<h1>Vocabulary List</h1>

<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_filter" class="form-label">Filter by ID :</label>
            <input type="text" name="id_filter" id="id_filter" class="form-control" value="<?php echo isset($_GET['id_filter']) ? $_GET['id_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>


        <div class="mb-3">
            <label for="vocabulary_filter" class="form-label">Filter by Vocab :</label>
            <input type="text" name="vocabulary_filter" id="vocabulary_filter" class="form-control" value="<?php echo isset($_GET['vocabulary_filter']) ? $_GET['vocabulary_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>

        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>STT</th>
        
        <th>ID</th>
        <th>New Word</th>
        <th>Language Vocabulary</th>
        <th>Vietnamese Meaning</th>
        <th>Explanation</th>
        <th>Example</th>
        <th>Image Link (if have)</th>
    </tr>

    <?php
        $stt = $offset + 1; // Initialize STT based on pagination offset

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                 // Process "Sample" and "Important Add" columns
              
                 $english_explanation_words = explode(' ', $row['english_explanation']);
                 $english_explanation_display = count($english_explanation_words) > 20 ? implode(' ', array_slice($english_explanation_words, 0, 20)) . '...' : $row['english_explanation'];
                 $english_explanation_view_more = count($english_explanation_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Explanation Add\", \"{$row['english_explanation']}\")'>View More</button>" : '';


                 $image_link = $row['image_link'];
                 $image_link_display = (strpos($image_link, 'https://') === 0) 
                     ? substr($image_link, 0, 5) . '...' 
                     : (strlen($image_link) > 20 ? substr($image_link, 0, 20) . '...' : $image_link);
                 $image_link_view_more = (strlen($image_link) > 20) 
                     ? "<button class='btn btn-link' onclick='showFullContent(\"Image Link\", \"{$image_link}\")'>View More</button>" 
                     : '';
                 


                echo "<tr id='row_{$row['number']}'>
                        <td>{$stt}</td> <!-- Display the STT here -->

                        <td>{$row['id']}</td>
                        <td>{$row['new_word']}</td>
                        <td>{$row['language_new_word']}</td>
                        <td>{$row['vietnamese_meaning']}</td>
                        <td>{$english_explanation_display} $english_explanation_view_more</td>
                        <td>{$row['example']}</td>
                        <td>{$image_link_display} $image_link_view_more</td>
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
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1&id_filter=<?php echo $id_filter; ?>">First</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&id_filter=<?php echo $id_filter; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php 
        // Define the range of page buttons to display
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&id_filter=<?php echo $id_filter; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&id_filter=<?php echo $id_filter; ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $total_pages; ?>&id_filter=<?php echo $id_filter; ?>">Last</a>
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

                    ID: <textarea id="edit_id" name="id" class="form-control" required></textarea><br>

                    New Word: <input id="edit_new_word" name="new_word" class="form-control" required></input><br>

                    Choose Language of New Word:
                    <select id="edit_language_new_word" name="language_new_word" class="form-control" required>
                        <option value=""></option>
                        <option value="English">English</option>
                        <option value="Chinese">Chinese</option>
                        <option value="Korean">Korean</option>
                        <option value="French">French</option>
                        <option value="Japanese">Japanese</option>
                        <option value="Russian">Russian</option>
                        <option value="German">German</option>
                        <option value="Vietnamese">Vietnamese</option>
                        <option value="Spanish">Spanish</option>
                    </select><br>     
                    
                    
                    Vietnamese Meaning: <input type="text" id="edit_vietnamese_meaning" name="vietnamese_meaning" class="form-control" required></input><br>
                   
                    Explanation: <textarea  id="edit_english_explanation" name="english_explanation" class="form-control"   required></textarea><br>
                    Example: <textarea  id="edit_example" name="example" class="form-control"   required></textarea><br>

                    Image Link: <input type="text" id="edit_image_link" name="image_link" class="form-control"  ></input><br>

                    

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
                    ID: 
                    <div class="input-group">
                        <span class="input-group-text">vocabulary</span>
                        <input type="text" id="add_id" name="id" class="form-control" required>
                    </div>
                    <br>
                    New Word: <input  id="add_new_word" name="new_word" class="form-control" required></input><br>

                    Choose Language of New Word:
                    <select id="add_language_new_word" name="language_new_word" class="form-control" required>
                        <option value=""></option>
                        <option value="English">English</option>
                        <option value="Chinese">Chinese</option>
                        <option value="Korean">Korean</option>
                        <option value="French">French</option>
                        <option value="Japanese">Japanese</option>
                        <option value="Russian">Russian</option>
                        <option value="German">German</option>
                        <option value="Vietnamese">Vietnamese</option>
                        <option value="Spanish">Spanish</option>
                    </select><br> 

                    Vietnamese Meaning: <input type="text" id="add_vietnamese_meaning" name="vietnamese_meaning" class="form-control" required></input><br>
                
                    Explanation: <textarea id="add_english_explanation" name="english_explanation" class="form-control"   required></textarea><br>
                    Example: <textarea id="add_example" name="example" class="form-control"   required></textarea><br>

                    Image Link: <input type="text" id="add_image_link" name="image_link" class="form-control"  ></input><br>
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
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/studyvocabulary/study-vocabulary-database-template/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id').val(data.id);
            $('#edit_new_word').val(data.new_word);
            $('#edit_language_new_word').val(data.language_new_word);
            $('#edit_vietnamese_meaning').val(data.vietnamese_meaning);
            $('#edit_english_explanation').val(data.english_explanation);
            $('#edit_example').val(data.example);
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
    const idQuestionField = document.getElementById('edit_id');
    if (!idQuestionField.value.startsWith('vocabulary')) {
        idQuestionField.value = 'vocabulary' + idQuestionField.value;
    }

    const english_explanation = $('#edit_english_explanation').val();

  
    // Apply formatting
    $('#edit_english_explanation').val(formatTextWithLineBreaks(english_explanation));

    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/studyvocabulary/study-vocabulary-database-template/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Modify saveNew function to include formatted content
function saveNew() {
    const idQuestionField = document.getElementById('add_id');
    idQuestionField.value = 'vocabulary' + idQuestionField.value;

    const english_explanation = $('#add_english_explanation').val();

    // Apply formatting
    $('#add_english_explanation').val(formatTextWithLineBreaks(english_explanation));

    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/studyvocabulary/study-vocabulary-database-template/add_question.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this vocabulary?')) {
        $.ajax({
            url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/studyvocabulary/study-vocabulary-database-template/delete_question.php',
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
