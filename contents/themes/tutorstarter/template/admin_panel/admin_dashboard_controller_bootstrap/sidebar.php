<?php
require_once(__DIR__ . '/../config-custom.php');
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center"  href="<?php echo MAIN_PATH; ?>index.php">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">FStudy <sup>Admin</sup></div>
</a>
<style>
    #caution {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }
        
        #caution:hover {
            color: darkblue;
        }
        
        .modal2 {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content2 {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            position: relative;
        }
        
        .close2 {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
</style>
<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>index.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
        <span class="nav-link" id = "caution">Lưu ý chung</span>
        <div id="myModal" class="modal2">

        <div class="modal-content2">
            <span class="close2">&times;</span>
            <h2>Lưu ý chung</h2>
            * ID các Test
            - Template header cho giao diện website (user) có file path: \contents\themes\tutorstarter\inc\Traits\Header_Components.php<br>
            - Chỉ generate ID được mã hóa Base24 cho các đề thi trong thư viện đề thi, KHÔNG mã hóa từng phần, để dễ quản lý<br>
            - Ví dụ generate id cho 1 test ielts reading, KHÔNG generate id cho từng ielts reading part 1,2,3.<br>
        </div>
    </div>
    <script>
        // Lấy các phần tử cần thiết
        const modal = document.getElementById("myModal");
        const btn = document.getElementById("caution");
        const span = document.getElementsByClassName("close2")[0];
        
        // Khi nhấn vào "Lưu ý chung", hiển thị modal
        btn.onclick = function() {
            modal.style.display = "block";
        }
        
        // Khi nhấn vào nút đóng (x), ẩn modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // Khi nhấn vào bất kỳ đâu bên ngoài modal, ẩn modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading" style = "font-size:20px">
    Đề thi, câu hỏi và thống kê
</div>




<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIeltsReading"
        aria-expanded="true" aria-controls="collapseIeltsReading">
        <i class="fas fa-fw fa-cog"></i>
        <span> Ielts Reading Test</span>
    </a>
    <div id="collapseIeltsReading" class="collapse" aria-labelledby="headingIeltsReading" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Sửa/Thêm đề thi, câu hỏi</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_reading/add_ielts_reading_tests.php">Thêm đề thi</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_reading/add_ielts_reading_part_1.php">Thêm Reading Part 1</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_reading/add_ielts_reading_part_2.php">Thêm Reading Part 2</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_reading/add_ielts_reading_part_3.php">Thêm Reading Part 3</a>

            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_reading/all_result_reading.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>

        </div>
    </div>
</li>

<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIeltsListening"
        aria-expanded="true" aria-controls="collapseIeltsListening">
        <i class="fas fa-fw fa-wrench"></i>
        <span>Ielts Listening Test</span>
    </a>
    <div id="collapseIeltsListening" class="collapse" aria-labelledby="headingIeltsListening"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Ielts Listening:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/add_ielts_listening_tests.php"> Thêm đề thi</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/add_ielts_listening_part_1.php">Thêm Listening Part 1</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/add_ielts_listening_part_2.php">Thêm Listening Part 2</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/add_ielts_listening_part_3.php">Thêm Listening Part 3</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/add_ielts_listening_part_4.php">Thêm Listening Part 4</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_listening/all_result_listening.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIeltsWriting"
        aria-expanded="true" aria-controls="collapseIeltsWriting">
        <i class="fas fa-fw fa-cog"></i>
        <span> Ielts Writing Test</span>
    </a>
    <div id="collapseIeltsWriting" class="collapse" aria-labelledby="headingIeltsWriting" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Ielts Writing:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_writing/add_ielts_writing_tests.php"> Thêm đề thi</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_writing/add_ielts_writing_task_1.php">Thêm Writing Task 1</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_writing/add_ielts_writing_task_2.php">Thêm Writing Task 2</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_writing/all_result_ielts_writing.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>

        </div>
    </div>
</li>


<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIeltsSpeaking"
        aria-expanded="true" aria-controls="collapseIeltsSpeaking">
        <i class="fas fa-fw fa-cog"></i>
        <span>Ielts Speaking Test</span>
    </a>
    <div id="collapseIeltsSpeaking" class="collapse" aria-labelledby="headingIeltsSpeaking" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Ielts Speaking:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_speaking/add_ielts_speaking_tests.php">Thêm đề thi</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_speaking/add_ielts_speaking_part_1.php">Thêm Speaking Part 1</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_speaking/add_ielts_speaking_part_2.php">Thêm Speaking Part 2</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_speaking/add_ielts_speaking_part_3.php">Thêm Speaking Part 3</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/ielts_speaking/all_result_ielts_speaking.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>
        </div>
    </div>
</li>



<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTopikReading"
        aria-expanded="true" aria-controls="collapseTopikReading">
        <i class="fas fa-fw fa-cog"></i>
        <span>Topik Reading Tests</span>
    </a>
    <div id="collapseTopikReading" class="collapse" aria-labelledby="headingTopikReading" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Topik Reading:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/topik_reading/add_topik_reading_tests.php">Thêm đề thi</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/topik_reading/all_result_topik_reading.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>
        </div>
    </div>
</li>


<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTopikListening"
        aria-expanded="true" aria-controls="collapseTopikListening">
        <i class="fas fa-fw fa-cog"></i>
        <span>Topik Listening Tests</span>
    </a>
    <div id="collapseTopikListening" class="collapse" aria-labelledby="headingTopikListening" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Topik Listening:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/topik_listening/add_topik_listening_tests.php">Thêm đề thi</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/topik_listening/all_result_topik_listening.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>
        </div>
    </div>
</li>


<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDigitalSat"
        aria-expanded="true" aria-controls="collapseDigitalSat">
        <i class="fas fa-fw fa-cog"></i>
        <span>Digital Sat</span>
    </a>
    <div id="collapseDigitalSat" class="collapse" aria-labelledby="headingDigitalSat" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Digital Sat:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/digital_sat/add_digital_sat_tests.php"> Thêm đề thi Digital Sat</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/digital_sat/add_digital_sat_verbal_question.php">Thêm câu hỏi Verbal</a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/digital_sat/add_digital_sat_math_question.php">Thêm câu hỏi Math</a>
            <h6 class="collapse-header">Chi tiết</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/digital_sat/all_result_digital_sat.php">Bảng kết quả bài thi</a>
            <a class="collapse-item" href="blank.html">Phân tích chuyên sâu</a>
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudyVocabulary"
        aria-expanded="true" aria-controls="collapseStudyVocabulary">
        <i class="fas fa-fw fa-cog"></i>
        <span>Study Vocabulary</span>
    </a>
    <div id="collapseStudyVocabulary" class="collapse" aria-labelledby="headingStudyVocabulary" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Study Vocabulary:</h6>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/study_vocabulary/add_vocabulary_packages.php">
                Danh sách package test lớn
            </a>

            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/study_vocabulary/add_vocabulary_tests.php">
                Danh sách đề thi (Các phần của package)
            </a>
            <a class="collapse-item" href="<?php echo MAIN_PATH; ?>exam_questions_database/study_vocabulary/add_vocabulary.php">
                Danh sách từ vựng
            </a>
        </div>
    </div>
</li>





<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>exam_questions_database/conversation_ai/add_conversation_ai.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Conversation AI</span></a>
</li>


<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>exam_questions_database/dictation_excercise/add_dictation_tests.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Dictation Excercise</span></a>
</li>


<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>exam_questions_database/shadowing/add_shadowing_tests.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Shadowing</span></a>
</li>


<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>exam_questions_database/thptqg/add_thptqg_tests.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>THPTQG</span></a>
</li>



<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading" style = "font-size:20px">
    Chức năng khác
</div>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-folder"></i>
        <span>Quản lý API</span>
    </a>
    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item"  href="<?php echo MAIN_PATH; ?>other_function_page/api/api_key.php">API Route and API Key</a>
            <a class="collapse-item"  href="<?php echo MAIN_PATH; ?>other_function_page/api/route_and_prompt.php">Order and Prompt</a>
        </div>
    </div>
</li>
<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link"  href="<?php echo MAIN_PATH; ?>other_function_page/video_export_system/admin_video_export.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Hệ thống xuất video</span></a>
</li>


<li class="nav-item">
    <a class="nav-link"  href="<?php echo MAIN_PATH; ?>other_function_page/admin_create/admin_create.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Hệ thống quản lý role, token và tạo thêm</span></a>
</li>

<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link"  href="<?php echo MAIN_PATH; ?>other_function_page/gift_database/admin_gift.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Hệ thống gửi quà</span></a>
</li>


<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2"
        aria-expanded="true" aria-controls="collapsePages2">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Quản lý Token</span>
    </a>
    <div id="collapsePages2" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item"  href="<?php echo MAIN_PATH; ?>other_function_page/create_token/admin_token.php">Tạo Token</a>
            <a class="collapse-item"  href="<?php echo MAIN_PATH; ?>other_function_page/create_token/transaction.php">Lịch sử mua Token</a>
        </div>
    </div>
</li>



<li class="nav-item">
    <a class="nav-link"  href="<?php echo MAIN_PATH; ?>other_function_page/notation/admin_notation_page.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Quản lý Notation</span></a>
</li>


<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>other_function_page/notification/admin_notification_page.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Tạo thông báo</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="charts.html">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Report lỗi đề</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo MAIN_PATH; ?>other_function_page/user_control_and_blacklist/admin.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Quản lý user/Blacklist</span></a>
</li>


<!-- Nav Item - Tables -->
<li class="nav-item">
    <a class="nav-link" href="tables.html">
        <i class="fas fa-fw fa-table"></i>
        <span>Tables</span></a>
</li>







<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>


</ul>
<!-- End of Sidebar -->
