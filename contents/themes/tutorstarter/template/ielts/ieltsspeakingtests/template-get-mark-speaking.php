<?php
/*
 * Template Name: Result Template Writing
 * Template Post Type: ieltsspeakingtests
 */


// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_ieltsspeakingtests_custom_number', true);
global $wpdb; // Use global wpdb object to query the DB

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
$site_url = get_site_url();

echo "<script> 
      
       var siteUrl = '" .  $site_url .       "';
     

   </script>";


$testsavenumber = get_query_var('testsaveieltsspeaking');


    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM save_user_result_ielts_speaking WHERE testsavenumber = %s",
            $testsavenumber
        )
    );



    // Assign $custom_number using the id_test field from the query result if available
    $custom_number = 0; // Default value
    if (!empty($results)) {
        // Assuming you want the first result's id_test
        $custom_number = $results[0]->idtest;

    }



    // Set custom_number as id_test
    $id_test = $custom_number;

    // Prepare the SQL statement
    $sql = "SELECT testname, id_test, test_type, question_choose, tag, book FROM ielts_speaking_test_list WHERE id_test = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();



    echo "<script>console.log('Custom Number doing template: " . esc_js($custom_number) . "');</script>";


    if ($result->num_rows > 0) {
        // Fetch test data if available
        $data = $result->fetch_assoc();
        $testname = $data['testname'];
        add_filter('document_title_parts', function ($title) use ($testname) {
            $title['title'] = $testname; // Use the $testname variable from the outer scope
            return $title;
        });
        
        
    } else {
        echo "<script>console.log('No data found for the given id_test');</script>";
    }



    get_header();


    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    
    
    $review = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM ielts_speaking_test_list WHERE id_test = %d",
            $id_test // Replace with the correct variable holding the id_test
        )
    );




    if (!empty($results)) {
    
        $questions = !empty($data['question_choose']) 
    ? array_map(
        function($id) { return str_replace(' ', '', trim($id)); },
        explode(",", $data['question_choose'])
      )
    : [];
        
            // Display results
            foreach ($results as $result) {
                $user_answer_and_comment = json_decode($result->user_answer_and_comment, true);
                
                // Trong phần PHP, sau khi decode user_answer_and_comment
                $parts_present = [];
                if (isset($user_answer_and_comment['1'])) {
                    $parts_present[] = 1;
                }
                if (isset($user_answer_and_comment['2'])) {
                    $parts_present[] = 2;
                }
                if (isset($user_answer_and_comment['3'])) {
                    $parts_present[] = 3;
                }
                                
                echo "<script>console.log('Parts present: " . implode(', ', $parts_present) . "');</script>";
                
                // Now you can use this information to only query for the parts that exist
                $task1_data = [];
                $task2_data = [];
                $task3_data = [];
        
                // Loop through all question IDs in the questions array
                foreach ($questions as $question_id) {
                    // Only query for Part 1 if it exists
                    if (isset($user_answer_and_comment['1'])) {
                        $sql_question = "SELECT speaking_part, id_test, stt, question_content, sample FROM ielts_speaking_part_1_question WHERE id_test = ?";
                        $stmt_question = $conn->prepare($sql_question);
                        $stmt_question->bind_param("i", $question_id);
                        $stmt_question->execute();
                        $result_question = $stmt_question->get_result();
        
                        while ($row = $result_question->fetch_assoc()) {
                            $task1_data[] = $row;
                        }
                    }
        
                    // Only query for Part 2 if it exists
                    if (isset($user_answer_and_comment['2'])) {
                        $sql_question_task2 = "SELECT speaking_part, id_test, question_content, topic, sample FROM ielts_speaking_part_2_question WHERE id_test = ?";
                        $stmt_question_task2 = $conn->prepare($sql_question_task2);
                        $stmt_question_task2->bind_param("i", $question_id);
                        $stmt_question_task2->execute();
                        $result_question_task_2 = $stmt_question_task2->get_result();
        
                        while ($row = $result_question_task_2->fetch_assoc()) {
                            $task2_data[] = $row;
                        }
                    }
        
                    // Only query for Part 3 if it exists
                    if (isset($user_answer_and_comment['3'])) {
                        $sql_question_task3 = "SELECT speaking_part, stt, id_test, question_content, topic, sample FROM ielts_speaking_part_3_question WHERE id_test = ?";
                        $stmt_question_task3 = $conn->prepare($sql_question_task3);
                        $stmt_question_task3->bind_param("i", $question_id);
                        $stmt_question_task3->execute();
                        $result_question_task_3 = $stmt_question_task3->get_result();
        
                        while ($row = $result_question_task_3->fetch_assoc()) {
                            $task3_data[] = $row;
                        }
                    }
                }
        
                // Output or process the data as needed
                error_log(print_r($task1_data, true));
                error_log(print_r($task2_data, true));
                error_log(print_r($task3_data, true));



                
                

                    
              
            }
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Interface</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #fdf5ef;
        }

        .container1 {
            display: flex;
            flex-direction: column;
            margin: auto;
            padding: 20px;
        }

        audio{
            width: 100%;
        }
        .top-nav {
            display: flex;
            gap: 10px;
        }

        .top-nav button {
            padding: 10px 20px;
            background-color: #ffefd8;
            border: 1px solid #ff8f5a;
            border-radius: 5px;
            cursor: pointer;
        }

        .top-nav .active {
            background-color: #ff8f5a;
            color: white;
        }

        .timer {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 10px 0;
            font-size: 14px;
            color: #666;
        }
        .intro-result {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 10px 0;
            font-size: 14px;
            color: #666;
        }

        .submission-time {
            font-style: italic;
        }
        .time-Spent{
            font-style: italic;
        }
        .username{
            font-style: italic;
        }
        .testType .testName{
            font-style: italic;
        }


        .task p {
            margin-bottom: 10px;
        }

        .task-description {
            background-color: #f2f2f2;
            padding: 10px;
            border-radius: 5px;
        }

        .table-container img {
            width: 60%;
            justify-self: center;
            
            margin: 10px 0;
            border-radius: 8px;
        }

        .text-analysis p {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* Main Layout Container */
        .main-container-1 {
            display: flex;
            gap: 20px;
           /* margin: auto;*/
            height: 700px;
        }
        .task-buttons {
            display: flex;
            gap: 10px; /* Khoảng cách giữa các button */
            justify-content: flex-start; /* Căn các button sang góc trái */

        }

        .task-buttons button {
            padding: 10px 20px;
            background-color: #ffefd8;
            border: 1px solid #ff8f5a;
            border-radius: 5px;
            cursor: pointer;
        }

        .task-buttons .active {
            background-color: #ff8f5a;
            color: white;
        }


        .sidebar-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-sidebar {
            font-size: 20px;
            border-radius: 5px;
            background-color: transparent;
            border-color: white;
        }

        /* Adjust the active class directly */
        .btn-sidebar.active {
            background-color: white;
            border-color: white;
        }

        
        

        /* Content Section (Left Column) */
        .content {
            width: 100%; /* Adjust width as needed */
            background-color: white;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 20px;
            overflow: auto;
        }

        /* Right Column (Feedback and Sidebar) */
        .right-column {
            width: 30%; /* Adjust width as needed */
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Feedback Section */
        .feedback {
            background-color: #effbf2;
            padding: 20px;
            border-radius: 10px;
            overflow: auto;
            height: 100%;
        }

        .score {
            text-align: center;
        }

        .score-box h1 {
            color: #3bb75e;
            font-size: 2.5rem;
        }

        .feedback-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .feedback-buttons button {
            padding: 10px;
            border: none;
            background-color: #ff8f5a;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Sidebar Section */
        .sidebar {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            overflow: auto;
        }

        .comment {
            margin-bottom: 15px;
        }

        .tag {
            background-color: #ffef8f;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 0.8em;
        }

        .tab-content {
            /*display: none;*/
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .top-nav {
            margin-bottom: 20px;
            display:none;
        }


        .score-box {
            display: flex;
            align-items: center;
        }

        .band-score {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-right: 20px;
        }

        .band-score h1 {
            margin: 0;
            font-size: 24px;
        }

        .criteria {
            display: flex;
            gap: 10px;
        }

        .criteria p {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 5px 10px;
            margin: 0;
            font-weight: bold;
            text-align: center;
        }




    </style>
</head>
<script src="https://kit.fontawesome.com/acfb8e1879.js" crossorigin="anonymous"></script>
<body>
    <div class="container1">
    <div class="intro-result">
            <span class="username">Username: <span id="userName"><?php echo esc_html($result->username); ?></span></span><br>
            <span class="testName">Tên đề thi: <span id="testName"><?php echo esc_html($result->testname); ?></span></span><br>
            <span class="testType">Loại đề: <span id="categorytest"><?php echo esc_html($result->test_type); ?></span></span><br>

        </div>
        <!-- Top Navigation -->
        <div class="top-nav">
            <button class="tab-button active" onclick="openTab('question_seperate')">Bài gốc</button>
            <button class="tab-button" onclick="openTab('sample_seperate')">Sample Essay</button>
            <button class="tab-button" onclick="openTab('youpass')">Sửa bài</button>
            <button class="tab-button" onclick="openTab('suggestions')">Gợi ý nâng cấp</button>
        </div>

        <!-- Timer -->
        <div class="timer">
            <span class="submission-time">Nộp bài: <span id="dateSubmit"><?php echo esc_html($result->dateform); ?></span></span>
        </div>
        
        <div class="task-buttons">
            <button id="overall" class ="active button-10"  onclick="setActiveTask('overall')">Overall</button>
            <button id="task1"  class= "button-10" onclick="setActiveTask('task1')">Speaking Part 1</button>
            <button id="task2" class= "button-10" onclick="setActiveTask('task2')">Speaking Part 2</button>
            <button id="task3" class= "button-10" onclick="setActiveTask('task3')">Speaking Part 3</button>

        </div>

        <!-- Main Content -->
        <div class="main-container-1">
            <div class="content">
                <div class="task">
                    <p><strong>Word count:</strong> <span id="wordCount"></span></p>
                    <p id ="id_test_div">ID Test: </p>

                    <div class="tab-content" id = "question_seperate"></div>

                </div>

                <div class="table-container" id="taskImageContainer">
                <!-- Image will be dynamically inserted here -->
                </div>

                
                <div class="tab-content" id = "sample_seperate"></div>



                <div class="tab-content active" id="youpass">
                    <div class="text-analysis" id="youpassContent"></div>
                </div>
                
               
                <div class="tab-content" id="suggestions">
                    <p id="suggestionsContent"></p>
                </div>
            </div>

            <div class="right-column" style = "display:none">
                <!-- Feedback Section -->
                <div class="feedback">
                    <div class="score">
                        <div class="score-box">
                            <div class="band-score">
                                <p id="score"></p>
                            </div>
                            <div class="criteria">
                                <p id="lexical_resource_score"></p>
                                <p id="fluency_and_coherence_score"></p>
                                <p id="grammatical_range_and_accuracy_score"></p>
                                <p id="pronunciation_score"></p>
                            </div>
                        </div>
                        <p id = "user_level"></p>
                        <p id = "description_level"></p>
                    </div>

                    <div class="feedback-buttons">
                       <!-- <button onclick="addComment()">Thêm bình luận</button>
                        <button onclick="editFeedback()">Chỉnh sửa phản hồi</button>
                    </div> -->
                </div>

                <!-- Sidebar Section -->
                    <div class="sidebar-buttons">
                        <button class ="btn-sidebar active" id ="general-sidebar"><i class="fa-solid fa-circle" style="color: #74C0FC;"></i>General Comment</button>
                        <button class ="btn-sidebar" id ="details-sidebar"> <i class="fa-solid fa-circle" style="color: #B197FC;"></i> Detail Comment</button>
                        <button class ="btn-sidebar" id ="suggestion-sidebar"> <i class="fa-solid fa-circle" style="color: #B197FC;"></i> Suggestion</button>

                    </div>
                <div class="sidebar" id="sidebarContent"></div>

            </div>
        </div>
    </div>
    <!--<script src="http://localhost/contents/themes/tutorstarter/ielts-writing-toolkit/process_result.js"></script> 
    <script src="http://localhost/contents/themes/tutorstarter/ielts-writing-toolkit/submit_result.js"></script> -->

    <script>
// Decode HTML entities first
const decodeHTML = (html) => {
  const txt = document.createElement('textarea');
  txt.innerHTML = html;
  return txt.value;
};

// Decode the task1BreakdownForm string
const ResultTest = decodeHTML('<?php echo esc_js(wp_kses_post($result->resulttest)); ?>');

const bandDetails = <?php echo json_encode(json_decode($result->band_detail, true), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG); ?>;
const userAnswerAndComment = <?php echo json_encode(json_decode($result->user_answer_and_comment, true), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG); ?>;

console.log("User Answer and Comment:", userAnswerAndComment);
// Thêm hàm kiểm tra part nào có dữ liệu
function getAvailableParts() {
    const availableParts = [];
    if (userAnswerAndComment && userAnswerAndComment['1']) availableParts.push('1');
    if (userAnswerAndComment && userAnswerAndComment['2']) availableParts.push('2');
    if (userAnswerAndComment && userAnswerAndComment['3']) availableParts.push('3');
    return availableParts;
}

const availableParts = getAvailableParts();
console.log("Available parts:", availableParts);

// Hàm ẩn/hiện các nút part dựa trên dữ liệu có sẵn
function initializePartButtons() {
    const partButtons = {
        '1': document.getElementById("task1"),
        '2': document.getElementById("task2"),
        '3': document.getElementById("task3")
    };
    
    // Ẩn tất cả các nút part trước
    Object.values(partButtons).forEach(button => {
        if (button) button.style.display = 'none';
    });
    
    // Chỉ hiển thị các part có dữ liệu
    availableParts.forEach(part => {
        if (partButtons[part]) {
            partButtons[part].style.display = 'block';
        }
    });
    
    // Luôn hiển thị nút overall
    document.getElementById("overall").style.display = 'block';
}

// Gọi hàm này khi trang load
initializePartButtons();

function getAnswersForTask(taskData) {
    if (!taskData || !taskData.part) return [];
    
    let currentPart = taskData.part;
    const { id_tests, stts } = taskData;
    console.log("Checking part", userAnswerAndComment[currentPart]);

    // Check if the part exists in userAnswerAndComment
    if (!userAnswerAndComment || !userAnswerAndComment[currentPart] || !userAnswerAndComment[currentPart].data) {
        console.error("No data available for part:", currentPart);
        return [];
    }

    return id_tests.map((id_test, index) => {
        const stt = stts ? stts[index] : null;
        const currentStt = (stt > 0) ? stt - 1 : 0; 
        console.log("Đang check ", currentStt);

        if (!userAnswerAndComment[currentPart].data[currentStt]) {
            console.error("Invalid stt index:", currentStt);
            return null;
        }

        // Get data safely
        const answers = userAnswerAndComment[currentPart].data[currentStt]?.answer || "";
        const link_audios = userAnswerAndComment[currentPart].data[currentStt]?.audio || "";
        const speed_rate = userAnswerAndComment[currentPart].data[currentStt]?.avarage_speak || "";

        // Get comments if available
        const checkFluencyAndCoherenceComment = userAnswerAndComment[currentPart]?.final_analysis?.detail_recommendation?.flu || "";
        const checkLexicalResourceComment = userAnswerAndComment[currentPart]?.final_analysis?.detail_recommendation?.lr || "";
        const checkGrammarticalRangeAndAccuracyComment = userAnswerAndComment[currentPart]?.final_analysis?.detail_recommendation?.gra || "";
        const checkPronunciationComment = userAnswerAndComment[currentPart]?.final_analysis?.detail_recommendation?.pro || "";
        const pronunciationCorrection = userAnswerAndComment[currentPart]?.final_analysis?.analysis?.pronunciationBand || "";

        return {
            id_test: id_test,
            stt: stt,
            answers: answers,
            link_audios: link_audios,
            speed_rate: speed_rate,
            checkFluencyAndCoherenceComment: checkFluencyAndCoherenceComment,
            checkLexicalResourceComment: checkLexicalResourceComment,
            checkGrammarticalRangeAndAccuracyComment: checkGrammarticalRangeAndAccuracyComment,
            pronunciationCorrection: pronunciationCorrection,
            checkPronunciationComment: checkPronunciationComment,
        };
    }).filter(Boolean);
}

// Tạo sttAll tăng dần liên tục qua tất cả các part
let globalIndex = 0;

// Initialize task data only for available parts
const taskDataMap = {};

// Only initialize data for parts that exist in userAnswerAndComment
if (availableParts.includes('1')) {
    const task1IdTests = <?php echo isset($task1_data) ? json_encode(array_column($task1_data, 'id_test')) : '[]' ?>;
    const task1Stts = <?php echo isset($task1_data) ? json_encode(array_column($task1_data, 'stt')) : '[]' ?>;
    const task1QuestionContents = <?php echo isset($task1_data) ? json_encode(array_map('html_entity_decode', array_column($task1_data, 'question_content'))) : '[]' ?>;
    const task1Samples = <?php echo isset($task1_data) ? json_encode(array_map('html_entity_decode', array_column($task1_data, 'sample'))) : '[]' ?>;

    taskDataMap.task1 = {
        part: 1,
        id_tests: task1IdTests,
        question_contents: task1QuestionContents,
        samples: task1Samples,
        suggestions: [],
        stts: task1Stts,
        indexes: []
    };
}

if (availableParts.includes('2')) {
    const task2IdTests = <?php echo isset($task2_data) ? json_encode(array_column($task2_data, 'id_test')) : '[]' ?>;
    const task2Stts = <?php echo isset($task2_data) ? json_encode(array_column($task2_data, 'stt')) : '[]' ?>;
    const task2QuestionContents = <?php echo isset($task2_data) ? json_encode(array_map('html_entity_decode', array_column($task2_data, 'question_content'))) : '[]' ?>;
    const task2Samples = <?php echo isset($task2_data) ? json_encode(array_map('html_entity_decode', array_column($task2_data, 'sample'))) : '[]' ?>;

    taskDataMap.task2 = {
        part: 2,
        id_tests: task2IdTests,
        question_contents: task2QuestionContents,
        samples: task2Samples,
        suggestions: [],
        stts: task2Stts,
        indexes: []
    };
}

if (availableParts.includes('3')) {
    const task3IdTests = <?php echo isset($task3_data) ? json_encode(array_column($task3_data, 'id_test')) : '[]' ?>;
    const task3Stts = <?php echo isset($task3_data) ? json_encode(array_column($task3_data, 'stt')) : '[]' ?>;
    const task3QuestionContents = <?php echo isset($task3_data) ? json_encode(array_map('html_entity_decode', array_column($task3_data, 'question_content'))) : '[]' ?>;
    const task3Samples = <?php echo isset($task3_data) ? json_encode(array_map('html_entity_decode', array_column($task3_data, 'sample'))) : '[]' ?>;

    taskDataMap.task3 = {
        part: 3,
        id_tests: task3IdTests,
        question_contents: task3QuestionContents,
        samples: task3Samples,
        stts: task3Stts,
        suggestions: [],
        indexes: []
    };
}

// Initialize global indexes for available tasks
Object.values(taskDataMap).forEach(task => {
    if (task.stts && task.stts.length > 0) {
        task.indexes = task.stts.map(() => globalIndex++);
    } else {
        task.indexes = task.question_contents.map(() => globalIndex++);
    }
});

// Generic function to find suggestions
function findSuggestions(partNumber, globalIndex) {
    if (!userAnswerAndComment || !userAnswerAndComment[partNumber]?.final_analysis?.improvement_words) return [];
    
    const improvementWords = userAnswerAndComment[partNumber].final_analysis.improvement_words;
    const patterns = [
        `q-p-${partNumber}-n-${globalIndex}`,
        `q-p-${partNumber}-${globalIndex}`,
        `q-p-${partNumber}-n-${globalIndex+1}`,
        `q-p-${partNumber}-n-${globalIndex-1}`,
        `q-p-${partNumber}-${globalIndex+1}`,
        `q-p-${partNumber}-${globalIndex-1}`
    ];
    
    for (const pattern of patterns) {
        if (improvementWords[pattern]) {
            return improvementWords[pattern];
        }
    }
    return [];
}

// Assign suggestions to available tasks
Object.values(taskDataMap).forEach(task => {
    task.suggestions = task.indexes.map(index => 
        findSuggestions(task.part, index)
    );
});

// Generate improvement HTML
function generateImprovementHTML(improvementData, part, questionIndex) {
    if (!Array.isArray(improvementData) || improvementData.length === 0) {
        return "<p style='color: #999; font-style: italic;'>No improvement suggestions for this part.</p>";
    }

    return improvementData.map((item, improveIndex) => 
        `<div id="q-p-${part}-n-${questionIndex}-improve-${improveIndex}" style="border: 1px solid #ccc; border-radius: 8px; padding: 12px; margin-bottom: 10px; background-color: #f9f9f9; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);">
            <p><strong class="original-text">Original:</strong> ${item.origin_word_or_phrase}</p>
            <p><strong class="suggestion-text">Suggestion:</strong> ${item.replace_word_or_phrase}</p>
            <p><strong class="reason-text">Reason:</strong> ${item.reason}</p>
        </div>`
    ).join("");
}

// Apply replacements to content
function applyReplacements(text, suggestions) {
    if (!suggestions || !Array.isArray(suggestions)) return text;

    suggestions.forEach((item, index) => {
        const escaped = item.origin_word_or_phrase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(escaped, "g");
        text = text.replace(regex, 
            `<span class="need_replace" id="replace_text_${index}">${item.origin_word_or_phrase}</span>
             <span class="replaced_text" id="replace_${index}">${item.replace_word_or_phrase}</span>`
        );
    });
    return text;
}

// Process available tasks
Object.values(taskDataMap).forEach(task => {
    task.question_contents = task.question_contents.map((content, index) => 
        applyReplacements(content, task.suggestions[index] || [])
    );
});

// Generate recommendation bars for available tasks
const replaceRecommendBars = {};
Object.entries(taskDataMap).forEach(([taskName, taskData]) => {
    replaceRecommendBars[taskName] = taskData.suggestions.map((sugg, index) => 
        generateImprovementHTML(sugg, taskData.part, taskData.indexes[index])
    ).join("");
});

// Create a DOMParser to parse the decoded HTML string
const parser = new DOMParser();
const doc = parser.parseFromString(bandDetails, 'text/html');

const generalSidebarContentPart1 = `
Số từ: <br>
Task: <br> 
Loại essay: <br>
Số câu: <br>
Số lượng sai ngữ pháp/ từ vựng: <br>
Số linking Words: <br>
Độ mạch lạc: <br>
`;

const generalSidebarContentPart2 = `
Số từ: <br>
Task: <br> 
Loại essay: <br>
Số câu: <br>
Số lượng sai ngữ pháp/ từ vựng: <br>
Số linking Words: <br>
Độ mạch lạc: <br>
`;

const generalSidebarContentPart3 = `
Số từ: <br>
Task: <br> 
Loại essay: <br>
Số câu: <br>
Số lượng sai ngữ pháp/ từ vựng: <br>
Số linking Words: <br>
Độ mạch lạc: <br>
`;

// Generate detail comments only for available parts
const detailComments = {};

if (availableParts.includes('1')) {
    detailComments.task1 = `
    <strong style="color:red">Detail Feedback Speaking Part 1</strong>
    <p style="font-weight: bold">Fluency And Coherence: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.flu || "No feedback available"}<br>
    <p style="font-weight: bold">Lexical Resource: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.lr || "No feedback available"}<br>
    <p style="font-weight: bold">Grammar: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.gra || "No feedback available"}<br>
    <p style="font-weight: bold">Pronunciation: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.pro || "No feedback available"}<br>
    `;
}

if (availableParts.includes('2')) {
    detailComments.task2 = `
    <strong style="color:red">Detail Feedback Speaking Part 2</strong>
    <p style="font-weight: bold">Fluency And Coherence: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.flu || "No feedback available"}<br>
    <p style="font-weight: bold">Lexical Resource: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.lr || "No feedback available"}<br>
    <p style="font-weight: bold">Grammar: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.gra || "No feedback available"}<br>
    <p style="font-weight: bold">Pronunciation: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.pro || "No feedback available"}<br>
    `;
}

if (availableParts.includes('3')) {
    detailComments.task3 = `
    <strong style="color:red">Detail Feedback Speaking Part 3</strong>
    <p style="font-weight: bold">Fluency And Coherence: </p>${userAnswerAndComment[3]?.final_analysis?.detail_recommendation?.flu || "No feedback available"}<br>
    <p style="font-weight: bold">Lexical Resource: </p>${userAnswerAndComment[3]?.final_analysis?.detail_recommendation?.lr || "No feedback available"}<br>
    <p style="font-weight: bold">Grammar: </p>${userAnswerAndComment[3]?.final_analysis?.detail_recommendation?.gra || "No feedback available"}<br>
    <p style="font-weight: bold">Pronunciation: </p>${userAnswerAndComment[3]?.final_analysis?.detail_recommendation?.pro || "No feedback available"}<br>
    `;
}

const tasks = {
    overall: {
        description: "Overall feedback and analysis",
        user_level: "",
        wordCount: "", 
        description_level: "",
        replaceRecommendBar: "",
        generalSidebar: "Overall general content here.",
        detailSidebar: "",
        youpass: "Overall feedback will be shown here",
        question_seperate: [],
        sample: [],
        id_question: "Overall Questions",
        suggestions: "Overall suggestions will be shown here",
        sidebar: [
            "Comment 1: Overall performance",
            "Comment 2: General feedback",
            "Comment 3: Final thoughts"
        ],
        imageLink: ""
    }
};

// Add tasks only for available parts with proper data checking
if (availableParts.includes('1') && taskDataMap.task1) {
    const task1Desc = taskDataMap.task1.id_tests && taskDataMap.task1.stts && taskDataMap.task1.question_contents ?
        `${taskDataMap.task1.id_tests}: ${taskDataMap.task1.stts}: ${taskDataMap.task1.question_contents}` : 
        "Task 1 questions";
    
    tasks.task1 = {
        description: task1Desc,
        wordCount: "Word count for Task 1",    
        generalSidebar: generalSidebarContentPart1,
        detailSidebar: detailComments.task1 || "No detailed feedback available for Task 1",
        user_level: "User level for Task 1",
        replaceRecommendBar: replaceRecommendBars.task1 || "",
        description_level: "Description level for Task 1",
        youpass: userAnswerAndComment[1]?.final_analysis?.overall_recommendation || "YouPass feedback for Task 1",
        question_seperate: getAnswersForTask(taskDataMap.task1),
        sample: taskDataMap.task1.samples || [],
        id_question: "Task 1 Questions",
        suggestions: "Suggestions for Task 1",
        sidebar: [
            "Comment 1: Task 1 feedback",
            "Comment 2: Task 1 analysis",
            "Comment 3: Task 1 recommendations"
        ],
        imageLink: ""
    };
}

if (availableParts.includes('2') && taskDataMap.task2) {
    const task2Desc = taskDataMap.task2.id_tests && taskDataMap.task2.question_contents ?
        `${taskDataMap.task2.id_tests}: ${taskDataMap.task2.question_contents}` : 
        "Task 2 questions";
    
    tasks.task2 = {
        description: task2Desc,
        wordCount: "Word count for Task 2",
        generalSidebar: generalSidebarContentPart2,
        detailSidebar: detailComments.task2 || "No detailed feedback available for Task 2",
        user_level: "User level for Task 2",
        replaceRecommendBar: replaceRecommendBars.task2 || "",
        description_level: "Description level for Task 2",
        youpass: userAnswerAndComment[2]?.final_analysis?.overall_recommendation || "YouPass feedback for Task 2",
        question_seperate: getAnswersForTask(taskDataMap.task2),
        sample: taskDataMap.task2.samples || [],
        id_question: "Task 2 Questions",
        suggestions: "Suggestions for Task 2",
        sidebar: [
            "Comment 1: Task 2 feedback",
            "Comment 2: Task 2 analysis",
            "Comment 3: Task 2 recommendations"
        ],
        imageLink: ""
    };
}

if (availableParts.includes('3') && taskDataMap.task3) {
    const task3Desc = taskDataMap.task3.id_tests && taskDataMap.task3.stts && taskDataMap.task3.question_contents ?
        `${taskDataMap.task3.id_tests}: ${taskDataMap.task3.stts}: ${taskDataMap.task3.question_contents}` : 
        "Task 3 questions";
    
    tasks.task3 = {
        description: task3Desc,
        wordCount: "Word count for Task 3",
        generalSidebar: generalSidebarContentPart3,
        replaceRecommendBar: replaceRecommendBars.task3 || "",
        detailSidebar: detailComments.task3 || "No detailed feedback available for Task 3",
        user_level: "User level for Task 3",
        description_level: "Description level for Task 3",
        youpass: userAnswerAndComment[3]?.final_analysis?.overall_recommendation || "YouPass feedback for Task 3",
        question_seperate: getAnswersForTask(taskDataMap.task3),
        sample: taskDataMap.task3.samples || [],
        id_question: "Task 3 Questions",
        suggestions: "Suggestions for Task 3",
        sidebar: [
            "Comment 1: Task 3 feedback",
            "Comment 2: Task 3 analysis",
            "Comment 3: Task 3 recommendations"
        ],
        imageLink: ""
    };
}

console.log("Tasks data:", tasks);

// Function to set sidebar content based on the task and selected type (general/details)
function setSidebarContent(task, type) {
    const taskData = tasks[task];
    if (!taskData) return;
    
    let sidebarContent = '';
    let activeSidebar = '';

    if (type === 'general') {
        sidebarContent = taskData.generalSidebar || 'No general feedback available';
        activeSidebar = 'general-sidebar';
    } else if (type === 'details') {
        sidebarContent = taskData.detailSidebar || 'No detailed feedback available';
        activeSidebar = 'details-sidebar';
    } else if (type === 'suggestion') {
        sidebarContent = taskData.replaceRecommendBar || 'No improvement suggestions available';
        activeSidebar = 'suggestion-sidebar';
    }
    
    document.getElementById('sidebarContent').innerHTML = sidebarContent;
    document.getElementById('general-sidebar').classList.remove('active');
    document.getElementById('details-sidebar').classList.remove('active');
    document.getElementById('suggestion-sidebar').classList.remove('active');

    if (document.getElementById(activeSidebar)) {
        document.getElementById(activeSidebar).classList.add('active');
    }
}

// Add event listeners for switching between General and Details Sidebar
document.getElementById('general-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'general');
});

document.getElementById('suggestion-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'suggestion');
});

document.getElementById('details-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'details');
    document.getElementById('general-sidebar')?.classList.remove('active');
    document.getElementById('suggestion-sidebar')?.classList.remove('active');
    this.classList.add('active');
});

// Find the element by its ID
const final_lexical_resource_point = bandDetails?.bands?.lexicalResource || "N/A";
const final_fluency_and_coherence_point = bandDetails?.bands?.fluency || "N/A";
const final_grammatical_range_and_accuracy_point = bandDetails?.bands?.grammar || "N/A";
const final_pronunciation_point = bandDetails?.bands?.pronunciation || "N/A";

console.log('Lexical Resource:', final_lexical_resource_point);
console.log('Fluency and Coherence:', final_fluency_and_coherence_point);
console.log('Grammatical Range and Accuracy:', final_grammatical_range_and_accuracy_point);
console.log('Pronunciation:', final_pronunciation_point);

// Function to set active task and update content
function setActiveTask(task) {
    // Update active button state
    var buttons = document.querySelectorAll('.button-10');
    buttons.forEach(function(button) {
        button.classList.remove('active');
    });

    const activeButton = document.querySelector(`.button-10[onclick="setActiveTask('${task}')"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }

    // Show or hide the right-column
    const rightColumn = document.querySelector('.right-column');
    const topNav = document.querySelector('.top-nav'); 
    if (task === "overall") {
        rightColumn.style.display = 'none';
        topNav.style.display = 'none';
    } else {
        rightColumn.style.display = 'block';
        topNav.style.display = 'block';

    }

    currentTask = task;
    document.getElementById("question_seperate").innerHTML = "";
    document.getElementById("sample_seperate").innerHTML = "";

    const taskData = tasks[task];
    if (!taskData) {
        console.error("No data available for task:", task);
        return;
    }

    // Update content sections for the active task
    if (taskData.youpass) {
        document.getElementById('youpassContent').innerHTML = taskData.youpass;
    }
    
    if (taskData.suggestions) {
        document.getElementById('suggestionsContent').innerHTML = taskData.suggestions;
    }
    document.getElementById("wordCount").innerText = taskData.wordCount || "N/A"; 
    document.getElementById("user_level").innerText = taskData.user_level || "N/A";
    document.getElementById("description_level").innerText = taskData.description_level || "N/A";

    const sidebarContent = Array.isArray(taskData.sidebar) ? 
        taskData.sidebar.map(comment => `<div class="comment">${comment}</div>`).join('') :
        "No comments available";
    document.getElementById('sidebarContent').innerHTML = sidebarContent;

    if (taskData.imageLink && taskData.imageLink.trim() !== "") {
        document.getElementById('taskImageContainer').innerHTML = `<img src="${taskData.imageLink}" alt="Chart Image">`;
    } else {
        document.getElementById('taskImageContainer').innerHTML = "";
    }    

    document.getElementById('score').textContent = ResultTest || "N/A";
    document.getElementById('lexical_resource_score').textContent = final_lexical_resource_point;
    document.getElementById('fluency_and_coherence_score').textContent = final_fluency_and_coherence_point;
    document.getElementById('grammatical_range_and_accuracy_score').textContent = final_grammatical_range_and_accuracy_point;
    document.getElementById('pronunciation_score').textContent = final_pronunciation_point;
    document.getElementById('id_test_div').innerHTML = taskData.id_question || "N/A";

    switch (task) {
        case 'task1':
            if (taskDataMap.task1) {
                renderQuestionsWithAnswers(taskDataMap.task1, 1);
                getSampleForQuestion(taskDataMap.task1, 1);
            }
            break;
        case 'task2':
            if (taskDataMap.task2) {
                renderQuestionsWithAnswers(taskDataMap.task2, 2);
                getSampleForQuestion(taskDataMap.task2, 2);
            }
            break;
        case 'task3':
            if (taskDataMap.task3) {
                renderQuestionsWithAnswers(taskDataMap.task3, 3);
                getSampleForQuestion(taskDataMap.task3, 3);
            }
            break;
        case 'overall':
            renderOverall();
            break;
    }

    openTab('question_seperate');
    setSidebarContent(task, 'general');
}



function formatPronunciationCorrection(pronunciationCorrection, currentIndex) {
    // Check if we have valid pronunciation data
    if (!pronunciationCorrection || 
        !pronunciationCorrection.api_responses || 
        !Array.isArray(pronunciationCorrection.api_responses) ||
        !pronunciationCorrection.api_responses[currentIndex]) {
        return "<em>No pronunciation data available.</em>";
    }

    const response = pronunciationCorrection.api_responses[currentIndex];
    
    if (!response.words || !Array.isArray(response.words)) {
        return "";
    }

    const sentenceHtml = `<div style="margin-bottom: 10px;"><strong>Sentence:</strong> "${response.sentence}"</div>`;
    
    const wordsHtml = response.words.map(wordObj => {
        const word = wordObj.word;
        const comparison = wordObj.comparison || [];
        const stress = wordObj.stress_correct;
        const hasMistake = comparison.some(c => !c.match);

        const phoneDetails = comparison.map(c => {
            if (c.match) {
                return `<span style="color: green">${c.user || c.expected}</span>`;
            } else {
                return `<span style="color: red; font-weight: bold" title="Expected: ${c.expected}">${c.user || '?'}</span>`;
            }
        }).join(" ");

        const stressMark = stress ? "✅" : "⚠️";

        return `<div style="margin-bottom: 4px;">
                    <strong>${hasMistake ? "❌" : "✅"} ${word}</strong> 
                    <span style="font-size: 0.9em;">(${stressMark} stress)</span><br>
                    📣 <code>${phoneDetails}</code>
                </div>`;
    }).join("");

    return `<div style="margin-top: 8px; border-left: 3px solid #ddd; padding-left: 10px;">
                ${sentenceHtml}
                <div style="margin-left: 15px;">${wordsHtml}</div>
            </div>`;
}




function createAudioElement(link_audios, audioContainerId) {
    console.log(link_audios)
    if (!link_audios) return '';

    // Hiện loading trước
    setTimeout(() => {
        fetch(`${siteUrl}/api/v1/audio/service/get?url=${link_audios}`)
            .then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.blob();
            })
            .then(blob => {
                const audioUrl = URL.createObjectURL(blob);
                const container = document.getElementById(audioContainerId);
                if (container) {
                    container.innerHTML = `<strong>Audio:</strong> <audio controls src="${audioUrl}"></audio>`;
                }
            })
            .catch(error => {
                console.error('Error fetching audio:', error);
                const container = document.getElementById(audioContainerId);
                if (container) {
                    container.innerHTML = `<p style="color: red;">⚠️ Failed to load audio</p>`;
                }
            });
    }, 500); // delay nhẹ cho mượt

    return `<div id="${audioContainerId}"><strong>Loading audio...</strong></div>`;
}


function renderQuestionsWithAnswers(taskData, partIndex, hasStt = true) {
    if (!taskData || !taskData.id_tests || !taskData.question_contents) {
        console.error("Invalid task data for part", partIndex);
        return;
    }

    const idTests = taskData.id_tests;
    const stts = taskData.stts || [];
    const questionContents = taskData.question_contents;
    const taskAnswers = getAnswersForTask(taskData);
    const suggestionsArray = taskData.suggestions || [];

    if (!taskAnswers || taskAnswers.length === 0) {
        console.error("No answers found for part", partIndex);
        return;
    }

    let questionContainer = document.getElementById("question_seperate");
    if (!questionContainer) return;

    let htmlContent = '';

    idTests.forEach((id_test, index) => {
        if (!questionContents[index] || !taskAnswers[index]) {
            console.warn(`Missing data for question ${index} in part ${partIndex}`);
            return;
        }

        const stt = hasStt ? stts[index] : null;
        const questionContent = questionContents[index];
        const answers = taskAnswers[index]?.answers || "No answer available";
        const link_audios = taskAnswers[index]?.link_audios || "";
        const pronunciationCorrection = taskAnswers[index]?.pronunciationCorrection || "";
        const pronunciationHtml = formatPronunciationCorrection(pronunciationCorrection, index);

        const speed_rate = taskAnswers[index]?.speed_rate || "";

        const uniqueId = hasStt ?
            `part-${partIndex}-idTest-${id_test}-stt-${stt}` :
            `part-${partIndex}-idTest-${id_test}`;
        const audioContainerId = `audioContainer-${uniqueId}`;
        const speedRateId = `speedRate-${uniqueId}`;
        const suggestionId = `suggestion-${uniqueId}`;
        const pronunciationCorrectionId  = `pronunciationCorrectionId-${uniqueId}`;
        const suggestionItems = suggestionsArray[index] || [];
        let suggestionHtml = "";
        if (suggestionItems.length > 0) {
            suggestionHtml = `<ul>${suggestionItems.map(item =>
                `<li>
                    <strong>❌ ${item.origin_word_or_phrase}</strong><br>
                    ✅ <em>${item.replace_word_or_phrase}</em><br>
                    📝 ${item.reason}
                </li>`
            ).join('')}</ul>`;
        }

        console.log("Suggestions for index", index, suggestionItems);
        console.log("Suggestions for pronunciation", index, pronunciationHtml);


        htmlContent += `
            <div class="task-container" id="original">
                <p class="task-description" id="taskDescription-${uniqueId}">
                    ${questionContent}
                </p>
                <div>
                    <p id="originalContent-${uniqueId}">
                    

                        ${link_audios ? createAudioElement(link_audios, audioContainerId) : ''}

                        <strong>Your Answers:</strong> ${answers}<br>
                        <div id="${suggestionId}">${suggestionHtml}</div>
                        <div id="${speedRateId}"><strong>Speed Rate:</strong> ${speed_rate}</div><br>
                        <strong>Sample:</strong> ${getSampleForQuestion(taskData, index)}<br>

                        <strong id="${pronunciationCorrectionId}">Pronunciation:</strong> ${pronunciationHtml}
                    </p>
                </div>
            </div>
        `;
    });

    questionContainer.innerHTML = htmlContent || `<p>No questions available for this part</p>`;
}

function getSampleForQuestion(taskData, questionIndex) {
    if (!taskData || !taskData.samples) {
        console.error("Invalid sample data");
        return '';
    }
    return taskData.samples[questionIndex] || '';
}
function renderOverall() {
    let questionContainer = document.getElementById("question_seperate");
    if (!questionContainer) return;
    
    let overallContent = userAnswerAndComment?.overall_recommendation || "No overall feedback available";
    
    questionContainer.innerHTML = `
        <div class="task-container">
            <p class="task-description" id="overall-comment">
                Overall performance analysis
            </p>
            <div class="tab-content">
                <p id="originalContent-comment">
                    ${overallContent}
                </p>
            </div>
        </div>
    `;
}

// Function to open a specific tab
function openTab(tabName) {
    // Hide all tab content
    var contents = document.querySelectorAll('.tab-content');
    contents.forEach(function(content) {
        content.classList.remove('active');
    });

    // Show the selected tab content
    const tabContent = document.getElementById(tabName);
    if (tabContent) {
        tabContent.classList.add('active');
    }

    // Update active button state
    var buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(function(button) {
        button.classList.remove('active');
    });
    
    const activeButton = document.querySelector(`.tab-button[onclick="openTab('${tabName}')"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

// Set initial active task on page load
window.onload = function() {
    setActiveTask('overall');
    hidePreloader();
};
</script>
</body>
</html>

<?php
} else {
    // If no results with testsavenumber
    echo '<p>Không có kết quả tìm thấy cho đề thi này.</p>';
}
get_footer();