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
$id_question_filter = isset($_GET['id_question_filter']) ? $_GET['id_question_filter'] : '';

// Pagination logic
$limit = 50; // Number of records per page
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
    <title>Digital Practice SAT Questions Database</title>
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

                <h1>Digital Practice SAT Questions Database - VERBAL (Reading - Writing) only</h1>


                <div id="overlay"></div>

                <div id="popup-note">
                    <button class="close-btn" onclick="closePopup()">✖</button>
                    <b>Old version SSQB</b>
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
                                        

                    <h3>Full test:</h3>
                    <p>Verbal1036 - : Practice Test 1 - (verbal(verbal1036-verbal1100): M1: verbal1036-verbal1067; M2: verbal1068-verbal1100)</p>
                    <p>Verbal1101 - : Practice Test 2 - (verbal(verbal1101-verbal1166): M1: verbal1101-verbal1133; M2: verbal1134-verbal1166)</p>
                    <p>Verbal1167 - : Practice Test 3 - (verbal(verbal1167-verbal1232): M1: verbal1167-verbal1199; M2: verbal1200-verbal1232)</p>
                    <p>Verbal1233 - : Practice Test 4 - (verbal(verbal1233-verbal1296): M1: verbal1233-verbal1264; M2: verbal1265-verbal1296)</p>
                    <p>Verbal1297 - : Practice Test 5 - (verbal(verbal1297-verbal1361): M1: verbal1297-verbal1329; M2: verbal1330-verbal1361)</p>
                    <p>Verbal1297 - : Practice Test 6 - (verbal(verbal1693-verbal1757): M1: verbal1693-verbal1725; M2: verbal1726-verbal1757)</p>
                    <b>New version SSQB</b>
                    Boundaries (verbal1362 - verbal1402)
                    Central ideas and detail(verbal1403  - verbal1430)
                    Command of Evidence( verbal1431   - verbal1475)
                    Cross Text Connections ( verbal1476 - verbal1481)
                    Form, Structure and Sense (verbal1482   - verbal1518)
                    Inferences (verbal1519  -   verbal1537)
                    Rhetorical Analysis  (verbal1538  -  verbal1574)
                    Text Structure and Purpose (verbal1575 - verbal1599)
                    Transition (verbal1600 - verbal1641)
                    Words in context (verbal1642  - verbal1692)


                    <h3>Real test</h3>
                    - 2024年5月美国A卷 Module 1: verbal2075 - verbal2101; Module 2: verbal2102 - verbal2128

                    <b>Sample Img</b>: /contents/themes/tutorstarter/template/media_img_intest/digital_sat/verbal1143.png


                    
                </div>
    

<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_question_filter" class="form-label">Filter by ID Question:</label>
            <input type="text" name="id_question_filter" id="id_question_filter" class="form-control" value="<?php echo isset($_GET['id_question_filter']) ? $_GET['id_question_filter'] : ''; ?>">
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
                 $question_content_sanitized = htmlspecialchars(json_encode($row['question_content'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                 $question_content_words = explode(' ', $row['question_content']);
                 $question_content_display = count($question_content_words) > 20 ? implode(' ', array_slice($question_content_words, 0, 20)) . '...' : $row['question_content'];
                 $question_content_view_more = count($question_content_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Question Content\", $question_content_sanitized)'>View More</button>" : '';
                 

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
                        <td><input type='checkbox' class='select-question' value='{$row['id_question']}'></td>
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
                   <!-- explanation: <textarea  id="edit_explanation" name="explanation" class="form-control"   required></textarea><br> -->
                    Explanation: <input  id="edit_explanation" name="explanation" class="form-control"   required><br>
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
                        <option value="Command of Evidence">Command of Evidence</option>

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
                    <!--explanation: <textarea id="add_explanation" name="explanation" class="form-control"   required></textarea><br> -->
                    Explanation: <input id="add_explanation" name="explanation" class="form-control"   required><br>
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/get_question.php', // Fetch the question details
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
            $('#edit_category').val(data.category);

            $('#edit_image_link').val(data.image_link);
            $('#editModal').modal('show');
        }
    });
}


function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/get_latest_id.php',
        type: 'GET',
        success: function(response) {
            $('#add_id_question').val(response);
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

    // Remove prefixes 'A. ', 'B. ', 'C. ', 'D. ' and 'A) ', 'B) ', 'C) ', 'D) '
    $('#edit_answer_1').val($('#edit_answer_1').val().replace(/^(A\. |A\))/, '').trim());
    $('#edit_answer_2').val($('#edit_answer_2').val().replace(/^(B\. |B\))/, '').trim());
    $('#edit_answer_3').val($('#edit_answer_3').val().replace(/^(C\. |C\))/, '').trim());
    $('#edit_answer_4').val($('#edit_answer_4').val().replace(/^(D\. |D\))/, '').trim());

    // Apply formatting
    $('#edit_question_content').val(formatTextWithLineBreaks(questionContent));
    $('#edit_explanation').val(formatTextWithLineBreaks(explanation));

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/update_question.php',
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/add_question.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/delete_question.php',
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
        url: '<?php echo get_site_url(); ?>/contents/themes/tutorstarter/template/digitalsat/question-bank-verbal/bulk_update_category.php',
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