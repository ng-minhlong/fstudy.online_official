<?php
/*
 * Template Name: Digital SAT Question Bank DATABASE
 */

require_once(__DIR__ . '/../../../config-custom.php');


// Kiểm tra nếu chưa load được WordPress
if (!defined('DB_HOST')) {
    die("Error: Unable to load WordPress configuration.");
}
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
$document_id_filter = isset($_GET['document_id_filter']) ? $_GET['document_id_filter'] : '';

// Pagination logic
$limit = 50; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM document";
if ($document_id_filter) {
    $total_sql .= " WHERE document_id LIKE '%$document_id_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM document";
if ($document_id_filter) {
    $sql .= " WHERE document_id LIKE '%$document_id_filter%'"; // Apply filter to the SQL query
}
$sql .= " LIMIT $limit OFFSET $offset"; // Add pagination limits
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

    <meta charset="UTF-8">
    <title>Document Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/contents/themes/tutorstarter/ielts-reading-tookit/script_database_1.js"></script>

    
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
<style>
        /* Ẩn popup mặc định */
        #popup-note {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            max-width: 90%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Hiệu ứng mở popup */
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -55%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        /* Overlay làm mờ nền */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        /* Nút đóng */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close-btn:hover {
            color: red;
        }

        /* Nút mở popup */
        #open-popup {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        #open-popup:hover {
            background: #0056b3;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

    <?php include('../../sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

            <?php include('../../topbar.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                <h1>Document Database</h1>


                <div id="overlay"></div>

                <div id="popup-note">
                    <button class="close-btn" onclick="closePopup()">✖</button>
                  

                    
                </div>
    

<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="document_id_filter" class="form-label">Filter by ID Question:</label>
            <input type="text" name="document_id_filter" id="document_id_filter" class="form-control" value="<?php echo isset($_GET['document_id_filter']) ? $_GET['document_id_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>
    <div class="d-flex mb-3">
        <select id="bulk-category" class="form-select me-2" style="width: 250px;">
            <option value="">Chọn loại để cập nhật</option>
          

            <option value="Boundaries">Boundaries</option>
            <option value="Central ideas and detail">Central ideas and detail</option>
            <option value="Form, Structure and Sense">Form, Structure and Sense</option>
            <option value="Inferences">Inferences</option>
            <option value="Cross Text Connections">Cross Text Connections</option>
            <option value="Words in context">Words in context</option>
            <option value="Text Structure and Purpose">Text Structure and Purpose</option>
            <option value="Transition">Transition</option>
            <option value="Rhetorical Analysis">Rhetorical Analysis</option>
            <option value="Command of Evidence">Command of Evidence</option>


        </select>
        <button class="btn btn-warning" onclick="bulkUpdateCategory()">Cập nhật loại</button>
    </div>


    <button id="open-popup">Xem ghi chú các câu/ các loại</button>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th><input type="checkbox" id="select-all"></th> <!-- Chọn tất cả -->
        <th>STT</th>
        <th>ID Document</th>
        <th>Document Name</th>
        <th>Category</th>
        <th>Tag</th>
        <th>Content</th>
        <th>File Link</th>
        <th>File Type</th>
        <th>Prices</th>
        <th>Created At</th>
        <th>Last update </th>
    </tr>

    <?php
        $stt = $offset + 1; // Initialize STT based on pagination offset

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                 // Process "Sample" and "Important Add" columns
                 $content_sanitized = htmlspecialchars(json_encode($row['content'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                 $content_words = explode(' ', $row['content']);
                 $content_display = count($content_words) > 20 ? implode(' ', array_slice($content_words, 0, 20)) . '...' : $row['content'];
                 $content_view_more = count($content_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Document Content\", $content_sanitized)'>View More</button>" : '';
                 

                 $tag_words = explode(' ', $row['tag']);
                 $tag_display = count($tag_words) > 20 ? implode(' ', array_slice($tag_words, 0, 20)) . '...' : $row['tag'];
                 $tag_view_more = count($tag_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Explanation Add\", \"{$row['tag']}\")'>View More</button>" : '';


                 $file_link = $row['file_link'];
                 $file_link_display = (strpos($file_link, 'https://') === 0) 
                     ? substr($file_link, 0, 5) . '...' 
                     : (strlen($file_link) > 20 ? substr($file_link, 0, 20) . '...' : $file_link);
                 $file_link_view_more = (strlen($file_link) > 20) 
                     ? "<button class='btn btn-link' onclick='showFullContent(\"File Link\", \"{$file_link}\")'>View More</button>" 
                     : '';
                 


                echo "<tr id='row_{$row['number']}'>
                        <td><input type='checkbox' class='select-question' value='{$row['document_id']}'></td>
                        <td>{$stt}</td> <!-- Display the STT here -->

                        <td>{$row['document_id']}</td>
                        <td>{$row['document_name']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['tag']}</td>
                        <td>{$row['content']}</td>
                        <td>{$row['file_link']}</td>
                        <td>{$row['file_type']}</td>
                        <td>{$row['prices']}</td>
                        <td>{$row['created_at']}</td>
                        <td>{$row['updated_at']}</td>
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
                <a class="page-link" href="?page=1&document_id_filter=<?php echo $document_id_filter; ?>">First</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&document_id_filter=<?php echo $document_id_filter; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php 
        // Define the range of page buttons to display
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&document_id_filter=<?php echo $document_id_filter; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&document_id_filter=<?php echo $document_id_filter; ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $total_pages; ?>&document_id_filter=<?php echo $document_id_filter; ?>">Last</a>
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

                    ID Document: <input id="edit_document_id" name="document_id" class="form-control" required><br>

                    Document Name: <input id="edit_document_name" name="document_name" class="form-control" required ></input><br>
                    Category: 
                    <select id="edit_category" name="category" class="form-control" required>
                        <option value=""></option>
                        <option value="thi_thpt">Thi THPTQG</option>
                        <option value="thi_10">Thi vào 10</option>
                        <option value="ielts">Ielts</option>
                        <option value="digital_sat">Digital SAT</option>
                        <option value="toeic">Toeic</option>

                    </select><br>

                    Tag: <textarea id="edit_tag" name="tag" class="form-control" required></textarea><br>
                    Content: <textarea id="edit_content" name="content" class="form-control" required></textarea><br>
                    File Link: <input type="text" id="edit_file_link" name="file_link" class="form-control" required></input><br>
                    File Type:
                    <select id="edit_file_type" name="file_type" class="form-control" required>
                        <option value=""></option>
                        <option value="pdf">PDF</option>
                        <option value="word">Word</option>
                        <option value="powerpoint">Powerpoint</option>

                    </select><br>
                    Price: <input  id="edit_prices" name="prices" class="form-control"   required><br>

                    

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

                    ID Document: <input id="add_document_id" name="document_id" class="form-control" required><br>
                    <button type="button" id="generate_id_btn" class="btn btn-primary">Generate ID</button><br>

                    Document Name: <input id="add_document_name" name="document_name" class="form-control" required ></input><br>
                    Category: 
                    <select id="add_category" name="category" class="form-control" required>
                        <option value=""></option>
                        <option value="thi_thpt">Thi THPTQG</option>
                        <option value="thi_10">Thi vào 10</option>
                        <option value="ielts">Ielts</option>
                        <option value="digital_sat">Digital SAT</option>
                        <option value="toeic">Toeic</option>

                    </select><br>

                    Tag: <textarea id="add_tag" name="tag" class="form-control" required></textarea><br>
                    Content: <textarea id="add_content" name="content" class="form-control" required></textarea><br>
                    File Link: <input type="text" id="add_file_link" name="file_link" class="form-control" required></input><br>
                    File Type:
                    <select id="add_file_type" name="file_type" class="form-control" required>
                        <option value=""></option>
                        <option value="pdf">PDF</option>
                        <option value="word">Word</option>
                        <option value="powerpoint">Powerpoint</option>

                    </select><br>
                    Price: <input  id="add_prices" name="prices" class="form-control"   required><br>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Question</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>

                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('../../footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <!-- Bootstrap core JavaScript-->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../../js/sb-admin-2.min.js"></script>

</body>

<!-- jQuery and JavaScript for AJAX -->

<script>
     document.getElementById("generate_id_btn").addEventListener("click", function() {
        let now = new Date();
        let timestamp = `${now.getSeconds()}${now.getMinutes()}${now.getHours()}${now.getDate()}${now.getMonth() + 1}`;
        let randomStr1 = Math.random().toString(36).substring(2, 4).toUpperCase(); // Random 2 ký tự
        let randomStr2 = Math.random().toString(36).substring(2, 6).toUpperCase(); // Random 4 ký tự
        let encoded = (timestamp + randomStr1 + randomStr2).toString(36).toUpperCase(); // Chuyển đổi base 36
        document.getElementById("add_document_id").value = encoded;
    });
    

        function openPopup() {
            document.getElementById("popup-note").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup-note").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

        document.getElementById("open-popup").addEventListener("click", openPopup);
        document.getElementById("overlay").addEventListener("click", closePopup);


// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/document/test-list/get.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_document_id').val(data.document_id);
            $('#edit_file_type').val(data.file_type);
            $('#edit_content').val(data.content);
            $('#edit_prices').val(data.prices);
            $('#edit_tag').val(data.tag);
            $('#edit_category').val(data.category);
            $('#edit_document_name').val(data.document_name);
            $('#edit_file_link').val(data.file_link);
            $('#editModal').modal('show');
        }
    });
}


function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/document/test-list/get_latest_id.php',
        type: 'GET',
        success: function(response) {
            $('#add_document_id').val(response);
        }
    });

    
    $('#addModal').modal('show'); // Show the modal
}

// Format text by replacing newlines with <br> tags
function formatTextWithLineBreaks(text) {
    return text.replace(/\n/g, '<br>');
}

// Modify saveEdit function to include formatted content
function saveEdit() {
    

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/document/test-list/update.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Modify saveNew function to include formatted content
function saveNew() {
    const idQuestionField = document.getElementById('add_document_id');

    let questionContent = $('#add_content').val();
    const tag = $('#add_tag').val();

    // Remove 'ID: [some_id]' from content
    questionContent = questionContent.replace(/ID:\s*\w+/g, '').trim();

    // Replace 'Text 1' and 'Text 2' with underlined versions
    questionContent = questionContent.replace(/Text 1/g, '<b>Text 1</b>');
    questionContent = questionContent.replace(/Text 2/g, '<b>Text 2</b>');

    // Apply formatting
    $('#add_content').val(formatTextWithLineBreaks(questionContent));
    $('#add_tag').val(formatTextWithLineBreaks(tag));

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/document/test-list/add.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}



// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this document?')) {
        $.ajax({
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/document/test-list/delete.php',
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

// Chọn tất cả checkbox
$('#select-all').on('click', function() {
    $('.select-question').prop('checked', this.checked);
});

function bulkUpdateCategory() {
    const selectedCategory = $('#bulk-category').val();
    if (!selectedCategory) {
        alert('Vui lòng chọn loại cần cập nhật.');
        return;
    }

    const selectedIds = $('.select-question:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất một câu hỏi.');
        return;
    }

    // Gửi AJAX
    $.ajax({
        url: '<?php echo get_site_url(); ?>/contents/themes/tutorstarter/template/document/test-list/bulk_update_category.php',
        type: 'POST',
        data: {
            ids: selectedIds,
            category: selectedCategory
        },
        success: function(response) {
            alert(response);
            location.reload(); // Cập nhật lại giao diện
        },
        error: function(xhr) {
            alert("Có lỗi xảy ra.");
        }
    });
}

</script>


</html>