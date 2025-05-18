<?php
/*
 * Template Name: Reading Question DATABASE
 */

 $wp_load_paths = [
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php', // Local (có thư mục wordpress)
];

foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

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
$id_part_filter = isset($_GET['id_part_filter']) ? $_GET['id_part_filter'] : '';

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM ielts_reading_part_3_question";
if ($id_part_filter) {
    $total_sql .= " WHERE id_part LIKE '%$id_part_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM ielts_reading_part_3_question";
if ($id_part_filter) {
    $sql .= " WHERE id_part LIKE '%$id_part_filter%'"; // Apply filter to the SQL query
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
    <title>IELTS Reading Part 3 Questions Database</title>
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

                    <!-- 404 Error Text -->
       
<h1>IELTS Reading Part 3 Questions Database</h1>
<b>Question Content (Các câu hỏi) up trực tiếp trong database. Không up ở đây</b>
<b>Note 2: LƯU Ý: THÊM TESTNAME CHO Reading - dễ nhận biết</b>

<b>Check Valid json cho group_question tại: https://jsonlint.com/
</b>
<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_part_filter" class="form-label">Filter by ID Test:</label>
            <input type="text" name="id_part_filter" id="id_part_filter" class="form-control" value="<?php echo isset($_GET['id_part_filter']) ? $_GET['id_part_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>Number</th>
        <th>ID Test</th>
        <th>Reading Part</th>
        <th>Thời gian</th>
        <th>Số câu</th>
        <th>Paragraph</th>
        <th>Question Content</th> 
        <th>Category</th>
        <th>Key Answer</th>
        <th>Note</th>
        <th>Actions</th>
    </tr>
    <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Process "Sample" and "Important Add" columns
        $sample_words = explode(' ', $row['paragraph']);
        $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['paragraph'];
        $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Sample\", \"" . addslashes($row['paragraph']) . "\")'>View More</button>" : '';

        $important_words = explode(' ', $row['group_question']);
        $important_display = count($important_words) > 20 ? implode(' ', array_slice($important_words, 0, 20)) . '...' : $row['group_question'];
        $important_view_more = count($important_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Important Add\", \"" . addslashes($row['group_question']) . "\")'>View More</button>" : '';

        echo "<tr id='row_{$row['number']}'>
                <td>{$row['number']}</td>
                <td>{$row['id_part']}</td>
                <td>{$row['part']}</td>
                <td>{$row['duration']}</td>
                <td>{$row['number_question_of_this_part']}</td>
                <td>{$sample_display} $sample_view_more</td>
                <td>Ấn Edit để Xem chi tiết</td>
                <!--<td>{$important_display} $important_view_more</td> -->
                <td>{$row['category']}</td>
                <td id=useranswerdiv_{$row['number']}></td>
                <td>{$row['note']}</td>

                <td>
                    <button class='btn btn-primary btn-sm' onclick='openEditModal({$row['number']})'>Edit</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteRecord({$row['number']})'>Delete</button>
                    <button class='btn btn-info btn-sm' onclick='openPreviewModal(\"{$row['id_part']}\")'>Preview</button>

                    </td>
              </tr>";

        echo "<script>
            const quizData_{$row['number']} = {
                part: [
                    {
                        part_number: {$row['number']},
                        paragraph: '" . addslashes($row['paragraph']) . "',
                        number_question_of_this_part: '{$row['number_question_of_this_part']}',
                        duration: {$row['duration']},
                        group_question: " . $row['group_question'] . "
                    }
                ]
            };
            console.log('Quiz Data:', quizData_{$row['number']});
            logUserAnswers(0, quizData_{$row['number']}, {$row['number']})

        </script>";
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
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&id_part_filter=<?php echo $id_part_filter; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&id_part_filter=<?php echo $id_part_filter; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&id_part_filter=<?php echo $id_part_filter; ?>">Next</a></li>
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
                    
                    ID Test: <input type="text" id="edit_id_part" name="id_part" class="form-control" required><br>
                    Part: <input type="text" id="edit_part" name="part" class="form-control" required><br>
                    Duration: <input type="number" id="edit_duration" name="duration" class="form-control" required><br>
                    Number questions: <textarea id="edit_number_question_of_this_part" name="number_question_of_this_part" class="form-control" required></textarea><br>
                    Paragraph: <textarea id="edit_paragraph" name="paragraph" class="form-control"></textarea><br>
                    Question Group: <textarea id="edit_group_question" name="group_question" class="form-control"></textarea><br>

                    <!-- Dynamic Ranges -->
                    <label>Number of Ranges:</label>
                    <input type="number" id="edit_num_ranges" name="num_ranges" class="form-control" min="1" max="10" onchange="generateRangeFields('edit')">
                    <div id="edit_range_fields"></div>


                    Note:<select id="edit_note" name="note" class="form-control" required>
                        <option value=""></option>
                        <option value="Hoàn thiện">Hoàn thiện</option>
                        <option value="Đáp án sai">Đáp án sai</option>
                        <option value="Group Question lỗi">Group Question lỗi</option>
                        <option value="Đoạn văn sai">Đoạn văn sai</option>
                        <option value="Cả đoạn văn và đáp án sai">Cả đoạn văn và đáp án sai/thiếu</option>
                        <option value="Cả ĐV, Đ/á và G_Q sai/thiếu">Cả đoạn văn và đáp án và group question sai/thiếu</option>

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
                    ID Test: <input type="text" id="add_id_part" name="id_part" class="form-control" required><br>
                    Part: <input type="text" id="add_part" name="part" class="form-control" required><br>
                    Duration: <input type="number" id="add_duration" name="duration" class="form-control" required><br>
                    Number questions: <textarea id="add_number_question_of_this_part" name="number_question_of_this_part" class="form-control" required></textarea><br>
                    Paragraph: <textarea id="add_paragraph" name="paragraph" class="form-control"></textarea><br>
                    Group Question: <textarea id="add_group_question" name="group_question" class="form-control"></textarea><br>
                    
                    
                    <!-- Dynamic Ranges -->
                    <label>Number of Ranges:</label>
                    <input type="number" id="add_num_ranges" name="num_ranges" class="form-control" min="1" max="10" value="1" onchange="generateRangeFields('add')">
                    <div id="add_range_fields"></div>




                    Note:<select id="add_note" name="note" class="form-control" required>
                        <option value=""></option>
                        <option value="Hoàn thiện">Hoàn thiện</option>
                        <option value="Đáp án sai">Đáp án sai</option>
                        <option value="Group Question lỗi">Group Question lỗi</option>
                        <option value="Đoạn văn sai">Đoạn văn sai</option>
                        <option value="Cả đoạn văn và đáp án sai">Cả đoạn văn và đáp án sai/thiếu</option>
                        <option value="Cả ĐV, Đ/á và G_Q sai/thiếu">Cả đoạn văn và đáp án và group question sai/thiếu</option>

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


<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview ID Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Content will be inserted dynamically -->
            </div>
            <div class="modal-footer">
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
let quizData = {};

async function openPreviewModal(idPart) {
    // Chọn phần tử modal body
    const previewContent = document.getElementById('previewContent');
    
    // Hiển thị trạng thái đang tải
    previewContent.innerHTML = '<p>Loading content...</p>';
    
    try {
        // Gửi yêu cầu AJAX để lấy dữ liệu
        const response = await fetch(`http://localhost/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/reading-part-3-database-template/get_question_data.php?id_part=${idPart}`);
        const data = await response.json();
        
        // Cập nhật dữ liệu cho quizData
        quizData = {
            part: [
                {
                    questionID: idPart,
                    part_number: "2",
                    paragraph:"ok",
                    number_question_of_this_part:"10",
                    duration:"10",
                    group_question: data.group_question
                }
            ]
        };
        



        // Hiển thị nội dung trong modal
        previewContent.innerHTML = `
            <p><strong>ID Part:</strong> ${idPart}</p>
            <p><strong>Group Question:</strong> ${data.group_question}</p>
        `;
        
        // Đợi dữ liệu load xong và thêm nội dung mới
        
        previewContent.innerHTML += `
            <div id="area-${idPart}">
            <div class="quiz-container">
            <div class = "group-control-part-btn">
                <button id="prev-btn" class= "control-part-btn" ><i class="fa-solid fa-arrow-left fa-xl"></i></button>
                <button id="next-btn" class= "control-part-btn" ><i class="fa-solid fa-arrow-right  fa-xl"></i></button>
            </div>

            <div class="content-left">

                <div id="paragraph-container-${idPart}">
                    <!-- Paragraph will be loaded dynamically -->
                </div>
            </div>
            <div class="content-right">
                <div id="questions-container-${idPart}">
                    <!-- Questions will be loaded dynamically -->
                </div>


             <div class="pagination-container">
                   

                    <h5  id="time-result"></h5>

                    <h5 id ="useranswerdiv"></h5>
                     <!-- giấu form send kết quả bài thi -->


                    </div> 
                    <div id="results-container"></div>
                </div>       
    
            </div>
            
            </div>
        `;
        loadPart(idPart, 0);
    } catch (error) {
        previewContent.innerHTML = `<p>Error loading content: ${error.message}</p>`;
    }
    
    // Hiển thị modal Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}




function generateRangeFields(context) {
    const numRanges = document.getElementById(`${context}_num_ranges`).value;
    const rangeFieldsContainer = document.getElementById(`${context}_range_fields`);
    rangeFieldsContainer.innerHTML = '';

    for (let i = 0; i < numRanges; i++) {
        rangeFieldsContainer.innerHTML += `
            <div class="range-group mb-3">
                <label>Range ${i + 1} Start:</label>
                <input type="number" name="${context}_range_start_${i}" class="form-control" required>
                <label>Range ${i + 1} End:</label>
                <input type="number" name="${context}_range_end_${i}" class="form-control" required>
                <label>Type:</label>
                <select name="${context}_range_type_${i}" class="form-control" required>
                    <option value="multiple_choice">Multiple Choice Questions (MCQs) </option>   
                    <option value="matching_heading">Matching Heading</option>    
                    <option value="short-answer_question">Short-Answer Questions</option>    
                    <option value="completion">Completion</option>
                    <option value="true/false/notgiven_or_yes/no/notgiven">True/False/Not Given or Yes/No/Not Given</option>
                    <option value="matching_information/features">Matching Information/Features</option>
                </select>
            </div>
        `;
    }
}



// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/reading-part-3-database-template/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_part').val(data.id_part);
            $('#edit_part').val(data.part);
            $('#edit_duration').val(data.duration);
            $('#edit_number_question_of_this_part').val(data.number_question_of_this_part);
            $('#edit_paragraph').val(data.paragraph);
            $('#edit_group_question').val(data.group_question);
            $('#edit_category').val(data.category);
            $('#edit_note').val(data.note);

            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    const formData = new FormData(document.getElementById('editForm'));
    const ranges = [];
    const numRanges = formData.get('num_ranges');

    // Loop to collect range data
    for (let i = 0; i < numRanges; i++) {
        ranges.push({
            start: formData.get(`edit_range_start_${i}`),
            end: formData.get(`edit_range_end_${i}`),
            type: formData.get(`edit_range_type_${i}`),
        });
    }

    // Add the serialized ranges to the category field
    formData.set('category', JSON.stringify(ranges));


    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/reading-part-3-database-template/update_question.php',
        type: 'POST',
        data: formData,
        processData: false, // Required for FormData
        contentType: false, // Required for FormData
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
    const formData = new FormData(document.getElementById('addForm'));
    const ranges = [];
    const numRanges = formData.get('num_ranges');

    // Loop to collect range data
    for (let i = 0; i < numRanges; i++) {
        ranges.push({
            start: formData.get(`add_range_start_${i}`),
            end: formData.get(`add_range_end_${i}`),
            type: formData.get(`add_range_type_${i}`),
        });
    }

    // Add the serialized ranges to the category field
    formData.set('category', JSON.stringify(ranges));


    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/reading-part-3-database-template/add_question.php',
        type: 'POST',
        data: formData,
        processData: false, // Required for FormData
        contentType: false, // Required for FormData
        success: function(response) {
          location.reload(); // Reload to show the new record
        }
    });
}

// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: 'http://localhost/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/reading-part-3-database-template/delete_question.php',
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



</html>