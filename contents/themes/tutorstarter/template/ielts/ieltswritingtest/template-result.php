<?php
/*
 * Template Name: Result Template Writing
 * Template Post Type: ieltswritingtests
 */


// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_ieltswritingtests_custom_number', true);
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

$testsavenumber = get_query_var('testsaveieltswriting');


    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM save_user_result_ielts_writing WHERE testsavenumber = %s",
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

    $sql = "SELECT testname, time, test_type, question_choose, tag, book FROM ielts_writing_test_list WHERE id_test = ?";
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
            "SELECT * FROM ielts_writing_test_list WHERE id_test = %d",
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
                
                
                echo "<script>console.log('Parts present: " . implode(', ', $parts_present) . "');</script>";
                
                // Now you can use this information to only query for the parts that exist
                $task1_data = [];
                $task2_data = [];
        
                // Loop through all question IDs in the questions array
                foreach ($questions as $question_id) {
                    // Only query for Part 1 if it exists
                    if (isset($user_answer_and_comment['1'])) {
                        $sql_question = "SELECT task, id_test, question_type, question_content, image_link, sample_writing FROM ielts_writing_task_1_question WHERE id_test = ?";
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
                        $sql_question_task2 = "SELECT task, id_test, question_type, question_content, topic, sample_writing FROM ielts_writing_task_2_question WHERE id_test = ?";
                        $stmt_question_task2 = $conn->prepare($sql_question_task2);
                        $stmt_question_task2->bind_param("i", $question_id);
                        $stmt_question_task2->execute();
                        $result_question_task_2 = $stmt_question_task2->get_result();
        
                        while ($row = $result_question_task_2->fetch_assoc()) {
                            $task2_data[] = $row;
                        }
                    }
        
                }
        
                // Output or process the data as needed
                error_log(print_r($task1_data, true));
                error_log(print_r($task2_data, true));



                
                

                    
              
            }
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <button class="tab-button" onclick="openTab('corrected_seperate')">Sửa bài</button>
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
                <div class="tab-content" id = "corrected_seperate"></div>



                <div class="tab-content active" id="youpass">
                    <div class="text-analysis" id="youpassContent"></div>
                </div>
                
               
                <div class="tab-content" id="suggestions">
                    <p id="suggestionsContent"></p>
                </div>
                <div id = "someElement" ></div>
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
                                <p id="task_achievement_score"></p>
                                <p id="grammatical_range_and_accuracy_score"></p>
                                <p id="coherence_and_cohesion_score"></p>
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
    <!--<script src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/process_result2.js"></script> 
    <script src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/submit_result.js"></script> -->

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

// Function to check which parts have data
function getAvailableParts() {
    const availableParts = [];
    if (userAnswerAndComment && userAnswerAndComment['1']) availableParts.push('1');
    if (userAnswerAndComment && userAnswerAndComment['2']) availableParts.push('2');
    return availableParts;
}

const availableParts = getAvailableParts();
console.log("Available parts:", availableParts);

// Initialize part buttons based on available data
function initializePartButtons() {
    const partButtons = {
        '1': document.getElementById("task1"),
        '2': document.getElementById("task2")
    };
    
    // Hide all part buttons first
    Object.values(partButtons).forEach(button => {
        if (button) button.style.display = 'none';
    });
    
    // Show only parts that have data
    availableParts.forEach(part => {
        if (partButtons[part]) {
            partButtons[part].style.display = 'block';
        }
    });
    
    // Always show overall button
    document.getElementById("overall").style.display = 'block';
}

// Initialize buttons when page loads
initializePartButtons();

// Simplified function to get answer for a task
function getAnswerForTask(part) {
    if (!userAnswerAndComment || !userAnswerAndComment[part]) {
        console.error("No data available for part:", part);
        return null;
    }

    const partData = userAnswerAndComment[part].data;
    const improvements = userAnswerAndComment[part]?.final_analysis?.improvement_words || [];
    
    return {
        question: partData.question || "",
        answer: partData.answer || "",
        wordCount: partData.wordCount || 0,
        sentenceCount: partData.sentenceCount || 0,
        paragraphCount: partData.paragraphCount || 0,
        taskAchievementComment: userAnswerAndComment[part]?.final_analysis?.detail_recommendation?.ta_tr || "",
        lexicalResourceComment: userAnswerAndComment[part]?.final_analysis?.detail_recommendation?.lr || "",
        grammarComment: userAnswerAndComment[part]?.final_analysis?.detail_recommendation?.gra || "",
        coherenceComment: userAnswerAndComment[part]?.final_analysis?.detail_recommendation?.cc || "",
        improvements: improvements,
        improvementSuggestions: userAnswerAndComment[part]?.final_analysis?.suggestion?.improvement_suggestions || "No improvement suggestions available"
    };
}

// Initialize task data only for available parts
const taskDataMap = {};

if (availableParts.includes('1')) {
    taskDataMap.task1 = {
        part: 1,
        answerData: getAnswerForTask(1),
        sample: <?php echo isset($task1_data[0]) ? json_encode(html_entity_decode($task1_data[0]['sample_writing'])) : '""' ?>
    };
}

if (availableParts.includes('2')) {
    taskDataMap.task2 = {
        part: 2,
        answerData: getAnswerForTask(2),
        sample: <?php echo isset($task2_data[0]) ? json_encode(html_entity_decode($task2_data[0]['sample_writing'])) : '""' ?>
    };
}

// Generate improvement HTML with styling for original and suggested text
function generateImprovementHTML(improvementData, part) {
    if (!Array.isArray(improvementData) || improvementData.length === 0) {
        return "<p style='color: #999; font-style: italic;'>No improvement suggestions for this part.</p>";
    }

    return improvementData.map((item, index) => 
        `<div id="q-p-${part}-improve-${index}" style="border: 1px solid #ccc; border-radius: 8px; padding: 12px; margin-bottom: 10px; background-color: #f9f9f9; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);">
            <p><strong class="original-text">Original:</strong> <span style="text-decoration: line-through; color: red;">${item.original}</span></p>
            <p><strong class="suggestion-text">Suggestion:</strong> <span style="color: green;">${item.suggestion}</span></p>
            <p><strong class="reason-text">Reason:</strong> ${item.reason}</p>
        </div>`
    ).join("");
}

// Apply replacements to content with styling
function applyReplacements(text, improvements) {
    if (!improvements || !Array.isArray(improvements)) return text;

    improvements.forEach((item) => {
        const escaped = item.original.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(escaped, "g");
        text = text.replace(regex, 
            `<span style="text-decoration: line-through; color: red;">${item.original}</span> 
             <span style="color: green;">${item.suggestion}</span>`
        );
    });
    return text;
}

// Process available tasks to apply improvements to answers
Object.values(taskDataMap).forEach(task => {
    if (task.answerData.answer && task.answerData.improvements) {
        task.answerData.correctedAnswer = applyReplacements(task.answerData.answer, task.answerData.improvements);
    }
});

// Generate recommendation bars for available tasks
const replaceRecommendBars = {};
Object.entries(taskDataMap).forEach(([taskName, taskData]) => {
    replaceRecommendBars[taskName] = generateImprovementHTML(taskData.answerData.improvements, taskData.part);
});

// General sidebar template
const generalSidebarContent = (taskData) => `
Số từ: ${taskData.answerData.wordCount}<br>
Task: Part ${taskData.part}<br> 
Loại essay: ${taskData.part === 1 ? 'Academic Writing' : 'Essay Writing'}<br>
Số câu: ${taskData.answerData.sentenceCount}<br>
Số đoạn văn: ${taskData.answerData.paragraphCount}<br>
`;

// Generate detail comments only for available parts
const detailComments = {};

if (availableParts.includes('1')) {
    detailComments.task1 = `
    <strong style="color:red">Detail Feedback Writing Part 1</strong>
    <p style="font-weight: bold">Task Achievement: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.ta_tr || "No feedback available"}<br>
    <p style="font-weight: bold">Lexical Resource: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.lr || "No feedback available"}<br>
    <p style="font-weight: bold">Grammar: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.gra || "No feedback available"}<br>
    <p style="font-weight: bold">Coherence and Cohesion: </p>${userAnswerAndComment[1]?.final_analysis?.detail_recommendation?.cc || "No feedback available"}<br>
    `;
}

if (availableParts.includes('2')) {
    detailComments.task2 = `
    <strong style="color:red">Detail Feedback Writing Part 2</strong>
    <p style="font-weight: bold">Task Achievement: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.ta_tr || "No feedback available"}<br>
    <p style="font-weight: bold">Lexical Resource: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.lr || "No feedback available"}<br>
    <p style="font-weight: bold">Grammar: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.gra || "No feedback available"}<br>
    <p style="font-weight: bold">Coherence and Cohesion: </p>${userAnswerAndComment[2]?.final_analysis?.detail_recommendation?.cc || "No feedback available"}<br>
    `;
}

// Tasks data structure
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
        id_question: "",
        suggestions: "Overall suggestions will be shown here",
        sidebar: [
            "Comment 1: Overall performance",
            "Comment 2: General feedback",
            "Comment 3: Final thoughts"
        ],
        imageLink: ""
    }
};

// Add tasks only for available parts
if (availableParts.includes('1') && taskDataMap.task1) {
    tasks.task1 = {
        description: "Writing Task 1",
        wordCount: taskDataMap.task1.answerData.wordCount,    
        generalSidebar: generalSidebarContent(taskDataMap.task1),
        detailSidebar: detailComments.task1 || "No detailed feedback available for Task 1",
        user_level: "User level for Task 1",
        replaceRecommendBar: replaceRecommendBars.task1 || "",
        description_level: "Description level for Task 1",
        youpass: userAnswerAndComment[1]?.final_analysis?.overall_recommendation || "YouPass feedback for Task 1",
        answerData: taskDataMap.task1.answerData,
        sample: taskDataMap.task1.sample,
        id_question: "Task 1 Question",
        suggestions: taskDataMap.task1.answerData.improvementSuggestions,
        sidebar: [
            "Comment 1: Task 1 feedback",
            "Comment 2: Task 1 analysis",
            "Comment 3: Task 1 recommendations"
        ],
        imageLink: ""
    };
}

if (availableParts.includes('2') && taskDataMap.task2) {
    tasks.task2 = {
        description: "Writing Task 2",
        wordCount: taskDataMap.task2.answerData.wordCount,
        generalSidebar: generalSidebarContent(taskDataMap.task2),
        detailSidebar: detailComments.task2 || "No detailed feedback available for Task 2",
        user_level: "User level for Task 2",
        replaceRecommendBar: replaceRecommendBars.task2 || "",
        description_level: "Description level for Task 2",
        youpass: userAnswerAndComment[2]?.final_analysis?.overall_recommendation || "YouPass feedback for Task 2",
        answerData: taskDataMap.task2.answerData,
        sample: taskDataMap.task2.sample,
        id_question: "Task 2 Question",
        suggestions: taskDataMap.task2.answerData.improvementSuggestions,
        sidebar: [
            "Comment 1: Task 2 feedback",
            "Comment 2: Task 2 analysis",
            "Comment 3: Task 2 recommendations"
        ],
        imageLink: ""
    };
}

console.log("Tasks data:", tasks);

// Function to set sidebar content based on the task and selected type
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

// Add event listeners for sidebar tabs
document.getElementById('general-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'general');
});

document.getElementById('suggestion-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'suggestion');
});

document.getElementById('details-sidebar')?.addEventListener('click', function() {
    setSidebarContent(currentTask, 'details');
});

// Get band scores
const final_lexical_resource_point = bandDetails?.bands?.lexicalResource || "N/A";
const final_task_achievement_point = bandDetails?.bands?.taskAchievement || "N/A";
const final_grammatical_range_and_accuracy_point = bandDetails?.bands?.grammar || "N/A";
const final_coherence_and_cohesion_point = bandDetails?.bands?.coherenceAndCohesion || "N/A";

console.log('Lexical Resource:', final_lexical_resource_point);
console.log('Task Achievement:', final_task_achievement_point);
console.log('Grammatical Range and Accuracy:', final_grammatical_range_and_accuracy_point);
console.log('Coherence and Cohesion:', final_coherence_and_cohesion_point);

// Current active task
let currentTask = 'overall';

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
    document.getElementById("corrected_seperate").innerHTML = "";

    const taskData = tasks[task];
    if (!taskData) {
        console.error("No data available for task:", task);
        return;
    }

    // Update content sections
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

    // Update scores based on current task
    if (task === "overall") {
        document.getElementById('score').textContent = bandDetails?.bands?.overallBand || "N/A";
        document.getElementById('lexical_resource_score').textContent = bandDetails?.bands?.lexicalResource || "N/A";
        document.getElementById('task_achievement_score').textContent = bandDetails?.bands?.taskAchievement || "N/A";
        document.getElementById('grammatical_range_and_accuracy_score').textContent = bandDetails?.bands?.grammar || "N/A";
        document.getElementById('coherence_and_cohesion_score').textContent = bandDetails?.bands?.coherenceAndCohesion || "N/A";
    } 
    else if (task === "task1") {
        document.getElementById('score').textContent = bandDetails?.details?.["1"]?.overallBand || "N/A";
        document.getElementById('lexical_resource_score').textContent = bandDetails?.details?.["1"]?.lr || "N/A";
        document.getElementById('task_achievement_score').textContent = bandDetails?.details?.["1"]?.ta || "N/A";
        document.getElementById('grammatical_range_and_accuracy_score').textContent = bandDetails?.details?.["1"]?.gra || "N/A";
        document.getElementById('coherence_and_cohesion_score').textContent = bandDetails?.details?.["1"]?.cc || "N/A";
    } 
    else if (task === "task2") {
        document.getElementById('score').textContent = bandDetails?.details?.["2"]?.overallBand || "N/A";
        document.getElementById('lexical_resource_score').textContent = bandDetails?.details?.["2"]?.lr || "N/A";
        document.getElementById('task_achievement_score').textContent = bandDetails?.details?.["2"]?.ta || "N/A";
        document.getElementById('grammatical_range_and_accuracy_score').textContent = bandDetails?.details?.["2"]?.gra || "N/A";
        document.getElementById('coherence_and_cohesion_score').textContent = bandDetails?.details?.["2"]?.cc || "N/A";
    }

    document.getElementById('id_test_div').innerHTML = taskData.id_question || "N/A";

    // Render content based on task
    switch (task) {
        case 'task1':
            renderTaskContent(tasks.task1, 1);
            break;
        case 'task2':
            renderTaskContent(tasks.task2, 2);
            break;
        case 'overall':
            renderOverall();
            break;
    }

    openTab('question_seperate');
    setSidebarContent(task, 'general');
}

// Function to render task content
function renderTaskContent(taskData, partIndex) {
    if (!taskData || !taskData.answerData) {
        console.error("Invalid task data for part", partIndex);
        return;
    }
    
    const questionContainer = document.getElementById("question_seperate");
    const sampleContainer = document.getElementById("sample_seperate");
    const correctedContainer = document.getElementById("corrected_seperate");
    
    if (!questionContainer || !sampleContainer || !correctedContainer) return;

    // Render question and original answer
    questionContainer.innerHTML = `
        <div class="task-container" id="original">
            <p class="task-description">
                ${taskData.answerData.question}
            </p>
            <div>
                <p>
                    <strong>Your Original Answer:</strong> ${taskData.answerData.answer || "No answer available"}<br>
                </p>
            </div>
        </div>
    `;

    // Render sample answer
    sampleContainer.innerHTML = `
        <div class="task-container" id="sample">
            <p class="task-description">
                ${taskData.answerData.question}
            </p>
            <p>
                <strong>Sample Answer:</strong> ${taskData.sample || "No sample answer available"}
            </p>
        </div>
    `;

    // Render corrected answer with improvements
    correctedContainer.innerHTML = `
        <div class="task-container" id="corrected">
            <p class="task-description">
                ${taskData.answerData.question}
            </p>
            <div>
                <p>
                    <strong>Your Corrected Answer:</strong> 
                    ${taskData.answerData.correctedAnswer || taskData.answerData.answer || "No answer available"}
                </p>
            </div>
        </div>
    `;
}


function renderOverall() {
    let questionContainer = document.getElementById("question_seperate");
    if (!questionContainer) return;

    // Kiểm tra part nào có dữ liệu
    const hasPart1 = bandDetails.details && bandDetails.details["1"];
    const hasPart2 = bandDetails.details && bandDetails.details["2"];

    // Tạo HTML
    questionContainer.innerHTML = `
        <div class="task-container">
            <p class="task-description">Overall performance analysis</p>
            
            <!-- Chart 1: Band Scores Comparison -->
            <div class="chart-row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                <div style="flex: 1; min-width: 300px;">
                    <h3>Band Scores Comparison</h3>
                    <canvas id="bandComparisonChart" height="200"></canvas>
                    <div id="bandComparisonTable" style="margin-top: 20px;"></div>
                </div>
                
                <!-- Chart 2: Criteria Analysis -->
                <div style="flex: 1; min-width: 300px;">
                    <h3>Criteria Analysis</h3>
                    <canvas id="criteriaChart" height="200"></canvas>
                    <div id="criteriaAnalysisInfo" style="margin-top: 20px;"></div>
                </div>
            </div>
            
            <!-- Chart 3: Part Details (nếu có part) -->
            ${hasPart1 || hasPart2 ? `
            <div class="chart-row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                ${hasPart1 ? `
                <div style="flex: 1; min-width: 300px;">
                    <h3>Task 1 Detailed Analysis</h3>
                    <canvas id="part1Chart" height="200"></canvas>
                    <div id="part1Table" style="margin-top: 20px;"></div>
                </div>` : ''}
                
                ${hasPart2 ? `
                <div style="flex: 1; min-width: 300px;">
                    <h3>Task 2 Detailed Analysis</h3>
                    <canvas id="part2Chart" height="200"></canvas>
                    <div id="part2Table" style="margin-top: 20px;"></div>
                </div>` : ''}
            </div>
            ` : ''}
            
            <div class="tab-content">
                ${userAnswerAndComment?.overall_recommendation || "No overall feedback available"}
            </div>
        </div>
    `;

    // Render charts sau khi HTML được tạo
    setTimeout(() => {
        // 1. Band Comparison Chart
        renderBandComparisonChart();
        
        // 2. Criteria Analysis Chart
        renderCriteriaChart();
        
        // 3. Part Details Charts
        if (hasPart1) renderPartChart('1');
        if (hasPart2) renderPartChart('2');
    }, 100);

    // Hàm render Band Comparison Chart
    function renderBandComparisonChart() {
        const bandLabels = ['Overall'];
        const bandData = [bandDetails.bands.overallBand];
        let tableHTML = `
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; border: 1px solid #ddd;">Category</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Overall Band</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${bandDetails.bands.overallBand}</td>
                    </tr>`;

        if (hasPart1) {
            bandLabels.push('Task 1');
            bandData.push(bandDetails.details["1"].overallBand);
            tableHTML += `
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">Task 1 Band</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${bandDetails.details["1"].overallBand}</td>
                </tr>`;
        }

        if (hasPart2) {
            bandLabels.push('Task 2');
            bandData.push(bandDetails.details["2"].overallBand);
            tableHTML += `
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">Task 2 Band</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${bandDetails.details["2"].overallBand}</td>
                </tr>`;
        }

        tableHTML += `</tbody></table>`;
        document.getElementById('bandComparisonTable').innerHTML = tableHTML;

        const bandCtx = document.getElementById('bandComparisonChart').getContext('2d');
        new Chart(bandCtx, {
            type: 'line',
            data: {
                labels: bandLabels,
                datasets: [{
                    label: 'Band Scores',
                    data: bandData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 9,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Hàm render Criteria Chart
    function renderCriteriaChart() {
        const criteriaDatasets = [];
        let infoHTML = '';

        if (hasPart1) {
            criteriaDatasets.push({
                label: 'Task 1',
                data: [
                    bandDetails.details["1"].ta,
                    bandDetails.details["1"].lr,
                    bandDetails.details["1"].gra,
                    bandDetails.details["1"].cc
                ],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2,
                tension: 0.1
            });
        }

        if (hasPart2) {
            criteriaDatasets.push({
                label: 'Task 2',
                data: [
                    bandDetails.details["2"].ta,
                    bandDetails.details["2"].lr,
                    bandDetails.details["2"].gra,
                    bandDetails.details["2"].cc
                ],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                tension: 0.1
            });
        }

        // Tính average nếu có ít nhất 1 part
        if (hasPart1 || hasPart2) {
            const totalParts = (hasPart1 ? 1 : 0) + (hasPart2 ? 1 : 0);
            const avgScores = {
                ta: ((hasPart1 ? bandDetails.details["1"].ta : 0) + (hasPart2 ? bandDetails.details["2"].ta : 0)) / totalParts,
                lr: ((hasPart1 ? bandDetails.details["1"].lr : 0) + (hasPart2 ? bandDetails.details["2"].lr : 0)) / totalParts,
                gra: ((hasPart1 ? bandDetails.details["1"].gra : 0) + (hasPart2 ? bandDetails.details["2"].gra : 0)) / totalParts,
                cc: ((hasPart1 ? bandDetails.details["1"].cc : 0) + (hasPart2 ? bandDetails.details["2"].cc : 0)) / totalParts
            };

            criteriaDatasets.push({
                label: 'Average',
                data: [avgScores.ta, avgScores.lr, avgScores.gra, avgScores.cc],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                tension: 0.1,
                borderDash: [5, 5]
            });

            // Tìm criteria mạnh nhất/yếu nhất
            const criteria = [
                { name: "Task Achievement", value: avgScores.ta, code: "ta" },
                { name: "Lexical Resource", value: avgScores.lr, code: "lr" },
                { name: "Grammar", value: avgScores.gra, code: "gra" },
                { name: "Coherence & Cohesion", value: avgScores.cc, code: "cc" }
            ].sort((a, b) => a.value - b.value);

            const weakest = criteria[0];
            const strongest = criteria[criteria.length - 1];

            infoHTML = `
                <div style="background-color: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Weakest Criteria:</strong> ${weakest.name} (${weakest.value.toFixed(1)})
                </div>
                <div style="background-color: #e8f5e9; padding: 10px; border-radius: 5px;">
                    <strong>Strongest Criteria:</strong> ${strongest.name} (${strongest.value.toFixed(1)})
                </div>
            `;
        }

        document.getElementById('criteriaAnalysisInfo').innerHTML = infoHTML;

        const criteriaCtx = document.getElementById('criteriaChart').getContext('2d');
        new Chart(criteriaCtx, {
            type: 'line',
            data: {
                labels: ['Task Achievement', 'Lexical Resource', 'Grammar', 'Coherence & Cohesion'],
                datasets: criteriaDatasets
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 9,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Hàm render Part Chart chi tiết
    function renderPartChart(partNum) {
        const partData = bandDetails.details[partNum];
        const ctx = document.getElementById(`part${partNum}Chart`).getContext('2d');
        const container = document.getElementById(`part${partNum}Table`);
        
        // Tìm criteria mạnh/yếu cho part này
        const partCriteria = [
            { name: "Task Achievement", value: partData.ta, code: "ta" },
            { name: "Lexical Resource", value: partData.lr, code: "lr" },
            { name: "Grammar", value: partData.gra, code: "gra" },
            { name: "Coherence & Cohesion", value: partData.cc, code: "cc" }
        ].sort((a, b) => a.value - b.value);

        const weakest = partCriteria[0];
        const strongest = partCriteria[partCriteria.length - 1];

        // Tạo bảng
        const tableHTML = `
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; border: 1px solid #ddd;">Criteria</th>
                        <th style="padding: 8px; border: 1px solid #ddd;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Task Achievement</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${partData.ta}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Lexical Resource</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${partData.lr}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Grammar</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${partData.gra}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">Coherence & Cohesion</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${partData.cc}</td>
                    </tr>
                    <tr style="background-color: #fffde7;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Overall Band</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>${partData.overallBand}</strong></td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top: 15px;">
                <div style="background-color: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Weakest Point:</strong> ${weakest.name} (${weakest.value})
                </div>
                <div style="background-color: #e8f5e9; padding: 10px; border-radius: 5px;">
                    <strong>Strongest Point:</strong> ${strongest.name} (${strongest.value})
                </div>
            </div>
        `;

        container.innerHTML = tableHTML;

        // Vẽ chart
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Task Achievement', 'Lexical Resource', 'Grammar', 'Coherence & Cohesion'],
                datasets: [{
                    label: `Task ${partNum} Criteria`,
                    data: [partData.ta, partData.lr, partData.gra, partData.cc],
                    backgroundColor: partNum === '1' ? 'rgba(255, 99, 132, 0.2)' : 'rgba(54, 162, 235, 0.2)',
                    borderColor: partNum === '1' ? 'rgba(255, 99, 132, 1)' : 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 9,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
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