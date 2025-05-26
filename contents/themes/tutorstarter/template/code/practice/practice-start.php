<?php
/*
 * Template Name: Practice Code Start
 * Template Post Type: practice code start
 
 */

    if (!defined("ABSPATH")) {
        exit(); // Exit if accessed directly.
    }

if (is_user_logged_in()) {
    add_filter('document_title_parts', function ($title) {
            $title['title'] = "Code Playground";
            return $title;
        });
        $user_id = get_current_user_id();
        $current_user = wp_get_current_user();
        $current_username = $current_user->user_login;
        $username = $current_username;
        $current_user_id = $current_user->ID;
        echo '
        <script>
            var CurrentuserID = "' . $user_id . '";
            var Currentusername = "' . $username . '";
            console.log(CurrentuserID, Currentusername)
        </script>
        ';



    function generate_uuid_v4() {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),         // 32 bits
                mt_rand(0, 0xffff),                             // 16 bits
                mt_rand(0, 0x0fff) | 0x4000,                    // 16 bits, version 4
                mt_rand(0, 0x3fff) | 0x8000,                    // 16 bits, variant
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)  // 48 bits
            );
        }

    $id_test = get_query_var('id_test');
    $sessionID = get_query_var('sessionID');
    $sessionIDforAnotherTest = generate_uuid_v4();

    get_header();
    $site_url = get_site_url();
    echo "<script> 
        var siteUrl = '" .$site_url . "';   
        var currentTestId = '" . $id_test . "';
        var currensessionID = '" . $sessionID . "';
        var sessionIDforAnotherTest = '" . $sessionIDforAnotherTest . "';
        console.log(currensessionID);

        var status = '';
        var dataHistory = {};

        fetch(siteUrl + '/api/tests/practice/code/check-result-history', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                sessionID: currensessionID,
                id_problems: currentTestId
            })
        })
        .then(response => response.json())
        .then(data => {
            status = data.status;
            dataHistory = data.data || {};
            console.log('Check Result Status:', status);
            console.log('Data History:', dataHistory);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });

   
    </script>";
echo" <link rel='stylesheet' href='" . $site_url . "/contents/themes/tutorstarter/template/code/assets/style.css'>";

?>

<!DOCTYPE html>
<html lang="en">
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
        }

        .container1 {
            display: flex;
            height: 100vh;
            overflow: hidden;
            position: relative; /* Thêm để làm reference cho loader */
        }

        .loader-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; /* Đảm bảo loader hiển thị trên overlay */
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            margin: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            width: 300px;
            background-color: white;
            border-right: 1px solid #e0e0e0;
            height: 100vh;
            position: fixed;
            left: -300px;
            top: 0;
            transition: left 0.3s ease;
            overflow-y: auto;
            z-index: 1000;
        }
        #sidebar{
            margin-top: auto !important ;
        }
        .sidebar table {
            width: 100%;
        }

        .sidebar table tr:nth-child(even) {
            background-color: #3a3f4b;
        }

        .sidebar table tr:nth-child(odd) {
            background-color: #343a40;
        }

        .sidebar table tr.current-test {
            background-color: #4a5568 !important;
        }

        .sidebar table th {
            background-color: #2d3748 !important;
            color: white;
        }

        .sidebar table td, .sidebar table th {
            padding: 8px 12px;
            border: 1px solid #4a5568;
        }
        .sidebar td {
            padding: 10px;
            cursor: pointer;
        }

        .sidebar .title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #e2e8f0;
        }

        .sidebar .title:hover {
            color: #63b3ed;
            text-decoration: underline;
        }

        .sidebar .difficulty-level {
            font-size: 0.8em;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Màu sắc theo độ khó */
        .sidebar .easy {
            color: #68d391;
        }

        .sidebar .medium {
            color: #f6ad55;
        }

        .sidebar .hard {
            color: #fc8181;
        }

        .sidebar .difficulty-level.easy {
            background-color: rgba(104, 211, 145, 0.2);
        }

        .sidebar .difficulty-level.medium {
            background-color: rgba(246, 173, 85, 0.2);
        }

        .sidebar .difficulty-level.hard {
            background-color: rgba(252, 129, 129, 0.2);
        }
        
        .left-cols {
            width: 100%;
            padding: 20px;
            overflow-y: auto;
            background-color: white;
            border-right: 1px solid #e0e0e0;
        }

        .right-cols {
            width: 100%;
            padding: 20px;
            overflow-y: auto;
            background-color: white;
        }
        .cols-code {
            width: 100%;
            flex: auto !important;
        }
        .CodeMirror.cm-s-dracula {
            width: 100% !important;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: bold;
            font-size: 18px;
        }

        .sidebar-content {
            padding: 15px;
        }

        .problem-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .problem-item:hover {
            background-color: #f9f9f9;
        }

        .problem-title {
            font-weight: 500;
        }

        .problem-difficulty {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        .difficulty-easy {
            color: #28a745;
        }

        .difficulty-medium {
            color: #ffc107;
        }

        .difficulty-hard {
            color: #dc3545;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .problem-title-main {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .problem-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .example {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .example-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .button:hover {
            background-color: #0069d9;
        }

        .code-section {
            background-color: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-family: 'Courier New', Courier, monospace;
            overflow-x: auto;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        .tab-bar {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }

        .tab {
            padding: 8px 16px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .tab.active {
            border-bottom: 2px solid #007bff;
            color: #007bff;
            font-weight: 500;
        }
        .test-case {
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            margin: 16px 0;
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }

        .test-case-header {
            display: flex;
            justify-content: space-between;
            padding: 12px 16px;
            background-color: #f6f8fa;
            border-bottom: 1px solid #e1e4e8;
            font-weight: 600;
        }

        .test-case-tabs {
            display: flex;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 16px;
            background-color: #f6f8fa;
        }

        .test-case-tab {
            padding: 8px 16px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-right: 8px;
        }

        .test-case-tab.active {
            border-bottom-color: #f9826c;
            font-weight: 600;
        }

        .test-case-tab:hover {
            background-color: #eaecef;
        }

        .test-case-content {
            padding: 16px;
            background-color: white;
            min-height: 100px;
        }

        .test-case-param {
            margin-bottom: 8px;
        }

        .test-case-param-name {
            font-weight: 600;
            margin-right: 8px;
        }

        .test-case-param-value {
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
    <style>
        /* CSS cho tab buttons */
        .tab-buttons {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 16px;
            color: #555;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: #007bff;
        }

        .tab-button.active {
            color: #007bff;
            border-bottom-color: #007bff;
            font-weight: 500;
        }

        /* CSS cho tab content */
        .tab-content {
            display: none;
            padding: 15px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tab-content.active {
            display: block;
        }

        /* CSS cho solution content (tạm thời) */
        #solutionContent {
            font-size: 16px;
            line-height: 1.6;
        }
    </style>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
    <!-- CodeMirror core -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>

    <!-- Các ngôn ngữ -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/clike/clike.min.js"></script>

    <!-- Theme Dracula -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">

    <!-- Auto close brackets -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closebrackets.min.js"></script>

    <!-- Lint cho Python -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/lint/lint.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/lint/lint.min.css">

    <!-- Python Linting support (bên thứ 3) -->
    <script src="https://unpkg.com/codemirror-python-lint@latest/lib/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header > div {
            display: flex;
            gap: 10px; /* Tạo khoảng cách giữa các nút */
            align-items: center;
        }


        #colophon.site-footer { display: none !important; }
       
    </style>
</head>


<body>
    <div class="loader-container" id = "loader">
            <span class="loader-t1"></span>
        </div> 
    <div class ="container1" id = "ctn-container" style = "display: none">
               


        <div class="overlay" id="overlay"></div>
        <div class = "sidebar" id="sidebar">
            <div class="sidebar-header">Problem List</div>
            <label>Filter by Difficulty:
                <select id="difficulty">
                    <option value="">All</option>
                    <option value="Easy">Easy</option>
                    <option value="Medium">Medium</option>
                    <option value="Hard">Hard</option>
                </select>
            </label>

            <div id="loading">Loading...</div>

            <table id="practiceTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div id="pagination"></div>
        </div>


        <div class = "left-cols"> 
            <div class="header">
                <div>
                    <button class="button" id="problemListButton"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ebe8e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>Problem List</button>
                    <button class="button" id="retryButton" style = "display: none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ebe8e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>Retry</button>
                </div>

                <div>
                    <button class="button">Previous</button>
                    <button class="button">Next</button>
                </div>
            </div>
            <div class="tab-buttons">
                <button class="tab-button active" id="problemButton">Problem</button>
                <button class="tab-button" id="solutionButton">Solution</button>
                <button class="tab-button" id="compileButton">Compile & Result</button>
                <button class="tab-button" id="guideButton">Guide</button>
            </div>

            
                    <div id="problemContent" class="tab-content active">
                            <button id="translate-problem-btn" class="button">Dịch đề thi</button>

                            <div class="problem-content">
                                <h3 id="problem-title-main"></h3>
                                <div class="meta-info">
                                    <span id="problem-difficulty" class="difficulty-medium"></span>
                                    <span id="acceptance-rate"></span>
                                </div>
                                <div id="problem-description" class="problem-description"></div>
                                
                                <div class="examples">
                                    <!-- Các ví dụ sẽ được render từ content -->
                                </div>
                                
                                
                            </div>
                        </div>
                    
                    <div id="solutionContent" class="tab-content">
                        <button id="translate-solution-btn" class="button">Dịch lời giải</button>
                        <p>Solution - For review only !</p>
                        <div class="code-section">
                                    <pre id="python-code"></pre>
                                </div>
                                
                        <div id="problem-analysis" class="analysis-section"></div>
                    </div>

                    <div id="compileAndResultContent" class="tab-content">
                        <!-- Nội dung solution sẽ được thêm sau -->
                        <p>Compile And Result</p>
                        <div id = "compileAndResCtn">
                            <div id = "compileAndResAnnounce">
                                Submit xong thì kết quả sẽ hiện ở đây
                            </div>
                            <div id = "loading-render-result">Please wait...</div>
                            <canvas id="resultChart" width="600" height="300"></canvas>

                            <div id = "result-content"></div>
                        </div>
                    </div>

                    <div id="guideContent" class="tab-content">
                        <!-- Nội dung solution sẽ được thêm sau -->
                        <p>Video hướng dẫn</p>
                        
                    </div>
            </div>


           <div class = "right-cols">             
                <div class="row-code m-3" id = "row-code">
                    <div class="cols-code">
                        <div class="d-flex justify-content-between mb-2 bg-dark rounded p-2">
                            <div class="col-12 w-25">
                                <label class="visually-hidden" for="inlineFormSelectPref" style = "color: white">Chọn ngôn ngữ</label>
                                <select  class="dropdown" id="inlineFormSelectPref">
                                    <option selected>Choose...</option>
                                    <option value="Java">Java</option>
                                    <option value="Cpp">Cpp</option>
                                    <option value="Python">Python</option>
                                </select>
                            </div>
                            <div>
                                <button type="button" id="run" class="btn btn-success">Submit Code</button>
                            </div>
                        </div>
                        <textarea class = "textarea-code" placeholder = "test nha" type="text" id="editor" class="form-control" aria-label="First name"></textarea>
                    </div>


                    <div class="cols-code d-flex flex-column rounded bg-dark px-4">
                        <div class="test-case" id="test-case">
                            <div class="test-case-header">
                                <div class="test-case-title">Testcase</div>
                                <div class="test-case-result">Test Result
                                    <button onclick = "runTestCase()">Chạy test mẫu</button>
                                </div>
                            </div>
                            <div class="test-case-tabs" id="test-case-tabs"></div>
                            <div class="test-case-content" id="test-case-content"></div>
                        </div>


                        <div style = "display: none">
                            <div class="h-50">
                                <label for="Input" class="text-light mt-4 mb-2">Input</label>
                                <textarea class = "textarea-code" type="text" id="input" class="form-control h-75" aria-label="Last name"></textarea>
                            </div>
                            <div class="h-50">
                                <label for="Output" class="text-light mb-2">Output</label>
                                <textarea class = "textarea-code" type="text" id="output" class="form-control h-75" aria-label="Last name"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div id = "user-code" style = "display: none"></div>
            </div>
        </div>
</body>

<script>
    let runTimesNumber = 0;
    window.SampletestCases = [];


    // Khởi tạo CodeMirror với lint cho Python
var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
    mode: "text/x-python",
    theme: "dracula",
    lineNumbers: true,
    autoCloseBrackets: true,
    gutters: ["CodeMirror-lint-markers"],
    lint: true,
    indentUnit: 4,
    tabSize: 4,
    indentWithTabs: false,
});

var width = window.innerWidth;
var input = document.getElementById("input");
var output = document.getElementById("output");
var run = document.getElementById("run");
editor.setSize(0.7 * width, "500");

// Các mẫu code cho từng ngôn ngữ
const codeTemplates = {
    "Python": `def convert(s: str, numRows: int) -> str:
    # Your code here
    return "".join([])

if __name__ == "__main__":
    s = input().strip()
    #s = list(map(int, input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    numRows = int(input())
    result = convert(s, numRows)
    print(result)`,
    
    "Java": `import java.util.Scanner;

public class Main {
    public static String convert(String s, int numRows) {
        // Your code here
        return "";
    }
    
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        String s = scanner.nextLine().trim();
        int numRows = scanner.nextInt();
        String result = convert(s, numRows);
        System.out.println(result);
    }
}`,
    
    "Cpp": `#include <iostream>
#include <string>
using namespace std;

string convert(string s, int numRows) {
    // Your code here
    return "";
}

int main() {
    string s;
    getline(cin, s);
    int numRows;
    cin >> numRows;
    string result = convert(s, numRows);
    cout << result << endl;
    return 0;
}`
};

// Xử lý chuyển đổi ngôn ngữ
var option = document.getElementById("inlineFormSelectPref");
option.addEventListener("change", function () {
    let lang = option.value;
    if (lang === "Java") {
        editor.setOption("mode", "text/x-java");
        editor.setValue(codeTemplates["Java"]);
    } else if (lang === "Python") {
        editor.setOption("mode", "text/x-python");
        editor.setValue(codeTemplates["Python"]);
    } else if (lang === "Cpp") {
        editor.setOption("mode", "text/x-c++src");
        editor.setValue(codeTemplates["Cpp"]);
    } else {
        editor.setValue("");
    }
});

// Bắt đầu với editor rỗng
editor.setValue("");

// Gửi request đến server backend
var code;
run.addEventListener("click", async function () {
    async function submitTest() {
        processAfterSubmit();
        showCompileAndResultTab();
        const code = {
            code: editor.getValue(),
            input: input.value,
            id: currentTestId,
            type: "practice",
            lang: option.value
        };
        console.log(code);

        const oData = await fetch("http://localhost:8000/api/submit/", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(code)
        });
        const d = await oData.json();
        output.value = d.output;

        // Delay xóa file backend
        setTimeout(function () {
            fetch("http://localhost:8000/", {});
            console.log("Delete successfully!");
        }, 6000);
        renderCompileAndResult(d);

    }

    if (runTimesNumber === 0) {
        Swal.fire({
            title: "Chạy test case trước nhé?",
            text: "Bạn có muốn test case trước? Nếu đúng hết bạn mới nên submit kết quả.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Tiếp tục submit",
            cancelButtonText: "Hủy",
        }).then(async (result) => {
            if (result.isConfirmed) {
                await submitTest();
            }
        });
        return;
    }

    await submitTest();
});


</script>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    // Sửa lại hàm toggleSidebar
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        
        // Ngăn event propagation khi click overlay
        if (sidebar.classList.contains('open')) {
            overlay.style.display = 'block';
        } else {
            overlay.style.display = 'none';
        }
    }

    // Thêm event listener cho nút mở sidebar
    document.getElementById('problemListButton').addEventListener('click', function(e) {
        e.stopPropagation(); // Ngăn event bubbling
        sidebar.classList.add('open');
        overlay.classList.add('active');
        overlay.style.display = 'block';
    });

    // Sửa lại overlay click
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        overlay.style.display = 'none';
    });

    let currentPage = 1;

    function showLoading(show = true) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    function renderProblem() {
        fetch(`${siteUrl}/api/tests/practice/code/id`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_problem: currentTestId
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(response => {
            if (response.success && response.data) {
                const problem = response.data;
                //console.log('Problem data:', problem);
                
                // Cập nhật thông tin bài toán
                document.getElementById("problem-title-main").textContent = `${problem.id}. ${problem.title}`;
                
                // Parse Markdown trong problem.content → HTML
                const formattedContent = markdownToHtml(problem.content);
                document.getElementById("problem-description").innerHTML = formattedContent;
                
                // Hiển thị độ khó
                const difficultyElement = document.getElementById("problem-difficulty");
                difficultyElement.textContent = problem.difficulty;
                difficultyElement.className = `difficulty-${problem.difficulty.toLowerCase()}`;
                
                // Hiển thị acceptance rate nếu có
                if (problem.acceptance_rate) {
                    document.getElementById("acceptance-rate").textContent = `Acceptance: ${problem.acceptance_rate}%`;
                }
                
                // Hiển thị code mẫu (Python)
                const codeElement = document.getElementById("python-code");
                if (codeElement && problem.python_code) {
                    codeElement.textContent = problem.python_code.replace(/```python|```/g, '');
                }
                
                // Hiển thị phân tích
                if (problem.analysis) {
                    document.getElementById("problem-analysis").innerHTML = markdownToHtml(problem.analysis);
                }
                
                // Extract and render test cases from examples
                renderTestCases();
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error fetching problem:', error);
            alert('Failed to load problem. Please try again later.');
        });
        
}






async function runTestCase() {
    const allTestCases = (window.SampletestCases || []).map(tc => ({
        input: tc.input,
        expected_output: tc.expected_output  // ← đúng theo structure từ renderTestCases
    }));


    const code = {
        code: editor.getValue(),
        input: input.value,
        id: currentTestId,
        type: "practice",
        lang: option.value,
        SampletestCases: allTestCases
    };

    console.log(code);
    var sampleTestCase = await fetch("http://localhost:8000/api/compileTestCase/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(code)
    });
    var d = await sampleTestCase.json();
    // Gán kết quả vào SampletestCases để hiển thị sau
    if (d.results && Array.isArray(d.results)) {
        d.results.forEach((res, i) => {
            if (window.SampletestCases[i]) {
                window.SampletestCases[i].user_code_output = res.user_code_output;
                window.SampletestCases[i].passed = res.passed;
            }
        });
        // Thêm dòng này để cập nhật hiển thị ngay lập tức
        displayTestCase(window.SampletestCases[currentTestCaseIndex]);
    }


    output.value = d.output;
    runTimesNumber = runTimesNumber + 1;
    
}

let currentTestCaseIndex = 0;

async function renderTestCases() {
    await checkValidHistory();
    const tabsContainer = document.getElementById('test-case-tabs');
    const contentContainer = document.getElementById('test-case-content');

    fetch(`${siteUrl}/api/tests/practice/code/get-sample-test-case`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_problem: currentTestId
        })
    })
    .then(response => response.json())
    .then(json => {
        //console.log("API Response:", json);

        if (!json.success || !json.data || !json.data.test_cases) {
            throw new Error("Invalid structure");
        }

        let raw = json.data.test_cases;
        let cases;

        try {
            cases = Array.isArray(raw) ? raw : JSON.parse(raw);
            window.SampletestCases = cases;  // ← Gán testCases vào global scope để runTestCase() dùng được
        } catch (e) {
            console.error("Parsing error:", e, raw);
            alert("Failed to parse test cases.");
            return;
        }


        //console.log("Parsed cases:", cases);
        
        if (!Array.isArray(cases) || cases.length === 0) {
            tabsContainer.style.display = 'none';
            contentContainer.innerHTML = '<div class="test-case-param">No test cases provided</div>';
            return;
        }

        // Tạo tab
        tabsContainer.innerHTML = '';
        tabsContainer.style.display = 'flex';
        cases.forEach((caseData, index) => {
            const tab = document.createElement('div');
            tab.className = `test-case-tab ${index === 0 ? 'active' : ''}`;
            tab.textContent = `Case ${caseData.test_case}`;
            tab.onclick = () => switchTestCase(index, cases);
            tabsContainer.appendChild(tab);
        });
       


        // Hiển thị case đầu tiên
        displayTestCase(cases[0]);
    })
    .catch(error => {
        console.error('Error fetching problem:', error);
        alert('Failed to load problem. Please try again later.');
    });
    

    document.getElementById("loader").style.display = "none";
    document.getElementById("ctn-container").style.display = "flex";
}

function escapeHtml(code) {
    return code
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

function checkValidHistory(){
    if(status == 'valid'){
        document.getElementById("retryButton").style.display = "block";
        document.getElementById("row-code").style.display = "none";
        const userCodeContainer = document.getElementById("user-code");
        userCodeContainer.style.display = "block";

        // Clear and set code block
        userCodeContainer.innerHTML = `
            <h3>Your code:</h3>
            <div class="code-header">Language: ${dataHistory.language}</div><br>
            <pre><code class="code-block">${escapeHtml(dataHistory.user_code)}</code></pre>
        `;

        console.log(dataHistory.user_code);

        
        renderCompileAndResultHistory(dataHistory.result);
    }

    
    
}



function switchTestCase(index, cases) {
    currentTestCaseIndex = index; // 
    // Update active tab
    const tabs = document.querySelectorAll('.test-case-tab');
    tabs.forEach((tab, i) => {
        if (i === index) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Display selected case
    displayTestCase(window.SampletestCases[index]);

}

function markdownToHtml(markdown) {
    if (!markdown) return '';
    
    // First remove escape characters before processing
    let html = markdown.replace(/\\/g, '');
    
    // **bold** → <strong>bold</strong>
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    
    // *italic* → <em>italic</em>
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    
    // * bullet point → <li>bullet</li>
    html = html.replace(/^\*\s(.*$)/gm, '<li>$1</li>');
    
    // Xuống dòng → <br> hoặc <p>
    html = html.replace(/\n/g, '<br>');
    
    // Code block ```...``` → <pre><code>...</code></pre>
    html = html.replace(/```(\w*)\n([\s\S]*?)\n```/g, '<pre><code class="$1">$2</code></pre>');
    
    return html;
}


function displayTestCase(caseData) {
    const contentContainer = document.getElementById('test-case-content');
    let html = '';

    let inputStr = "";
    if (typeof caseData.input === "object" && caseData.input !== null) {
        inputStr = Object.values(caseData.input).join("<br>");
    } else {
        inputStr = caseData.input;
    }

    const outputJson = JSON.stringify(caseData.expected_output);

    html += `<div class="test-case-param">
                <span class="test-case-param-name">Description</span>
                <span class="test-case-param-value">${caseData.description}</span>
             </div>`;

    html += `<div class="test-case-param">
                <span class="test-case-param-name">Input</span>
                <pre class="test-case-param-value">${inputStr}</pre>
             </div>`;

    html += `<div class="test-case-param">
                <span class="test-case-param-name">Expected Output</span>
                <pre class="test-case-param-value">${outputJson}</pre>
             </div>`;

    if ('user_code_output' in caseData) {
        let icon = caseData.passed
            ? '<span style="color:green">✔️ Passed</span>'
            : '<span style="color:red">❌ Failed</span>';

        html += `<div class="test-case-param">
                    <span class="test-case-param-name">Your Output</span>
                    <pre class="test-case-param-value">${caseData.user_code_output}</pre>
                </div>
                <div class="test-case-param">
                    <span class="test-case-param-name">Result</span>
                    <span class="test-case-param-value">${icon}</span>
                </div>`;
    }


    contentContainer.innerHTML = html;
}


    function renderPagination(current, total) {
        let html = '';

        if (current > 1) {
            html += `<button onclick="fetchData(${current - 1})">Previous</button>`;
        }

        const range = 2;
        for (let i = 1; i <= total; i++) {
            if (i === 1 || i === total || (i >= current - range && i <= current + range)) {
                html += `<button onclick="fetchData(${i})" ${i === current ? 'disabled' : ''}>${i}</button>`;
            } else if (
                i === current - range - 1 ||
                i === current + range + 1
            ) {
                html += `<span>...</span>`;
            }
        }

        if (current < total) {
            html += `<button onclick="fetchData(${current + 1})">Next</button>`;
        }

        document.getElementById('pagination').innerHTML = html;
    }

    function fetchData(page = 1) {
        showLoading(true);
        const difficulty = document.getElementById('difficulty').value;
        fetch(`${siteUrl}/api/tests/practice/code/all`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ difficulty, page })
        })
        .then(res => res.json())
        .then(json => {
            const tbody = document.querySelector('#practiceTable tbody');
            tbody.innerHTML = '';
            json.data.forEach(row => {
                const isCurrent = row.id == currentTestId;
                tbody.innerHTML += `
                    <tr ${isCurrent ? 'class="current-test"' : ''}>
                        <td>
                            <div onclick="window.location.href='${siteUrl}/code/practice/id/${row.id}/${sessionIDforAnotherTest}'" 
                                class="title ${row.difficulty.toLowerCase()}">
                                ${row.title}
                            </div>
                            <div class="difficulty-level ${row.difficulty.toLowerCase()}">
                                ${row.difficulty}
                            </div>
                        </td>                    
                    </tr>`;
            });
            const totalPages = Math.ceil(json.total / json.limit);
            renderPagination(page, totalPages);
            currentPage = page;
            showLoading(false);
        });
    }

     function hideAllContents() {
        problemContent.classList.remove('active');
        solutionContent.classList.remove('active');
        compileAndResultContent.classList.remove('active');
        guideContent.classList.remove('active');
    }

    // Hàm bỏ active ở tất cả button
    function deactivateAllButtons() {
        problemButton.classList.remove('active');
        solutionButton.classList.remove('active');
        compileButton.classList.remove('active');
        guideButton.classList.remove('active');
    }
    function processAfterSubmit(){
        document.getElementById("retryButton").style.display = "block";
        document.getElementById("run").style.display = "none";

    }
    const retryButton = document.getElementById('retryButton');
    retryButton.addEventListener('click', () => {      

        window.location.href = `${siteUrl}/code/practice/id/${currentTestId}/${sessionIDforAnotherTest}`;
    });


    function showCompileAndResultTab(){
        deactivateAllButtons();
        hideAllContents();
        //this.classList.add('active');
        compileButton.classList.add('active');
        compileAndResultContent.classList.add('active');
       
    }
        
    function renderCompileAndResultHistory(historydata) {
        // Parse JSON nếu cần
        if (typeof historydata === 'string') {
            try {
                historydata = JSON.parse(historydata);
            } catch (e) {
                console.error("Lỗi khi parse JSON:", e);
                return;
            }
        }

        // Kiểm tra dữ liệu hợp lệ
        if (!historydata || !Array.isArray(historydata.results)) {
            console.warn("Không có results hợp lệ.");
            return;
        }

        // Xác định container để hiển thị kết quả
        const container = document.getElementById("result-content");
        if (!container) {
            console.error("Không tìm thấy phần tử #resultContainer");
            return;
        }

        // Xóa kết quả cũ (nếu có)
        container.innerHTML = "";

        // Tạo div kết quả
        const resultHTML = document.createElement("div");

        // Render từng test case
        historydata.results.forEach((test, index) => {
            const div = document.createElement("div");
            div.innerHTML = `Test case ${test.test_case}: 
                ${test.passed ? '<span style="color:green;">Accepted ✔️</span>' : '<span style="color:red;">Wrong ❌</span>'}`;
            resultHTML.appendChild(div);
        });

        container.appendChild(resultHTML);

        // Vẽ biểu đồ
        const ctx = document.getElementById("resultChart").getContext("2d");
        const times = historydata.results.map(r => parseInt(r.task_finish_time.replace("ms", "")));
        const avg = times.reduce((a, b) => a + b, 0) / times.length;

        new Chart(ctx, {
            type: "line",
            data: {
                labels: historydata.results.map(r => `TC ${r.test_case}`),
                datasets: [
                    {
                        label: "Task Finish Time (ms)",
                        data: times,
                        borderColor: "blue",
                        tension: 0.3,
                    },
                    {
                        label: "Average Time",
                        data: new Array(times.length).fill(avg),
                        borderColor: "red",
                        borderDash: [5, 5],
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Test Case Execution Times'
                    }
                }
            }
        });
    }



    function renderCompileAndResult(data) {
        
        document.getElementById("loading-render-result").style.display = "none";
        const container = document.getElementById("result-content");
        container.innerHTML = ""; // Xóa cũ

        const resultHTML = document.createElement("div");

        // Dòng hiển thị kết quả từng test case
        data.results.forEach((test, index) => {
            const div = document.createElement("div");
            div.innerHTML = `Test case ${test.test_case}: 
                ${test.passed ? '<span style="color:green;">Accepted ✔️</span>' : '<span style="color:red;">Wrong ❌</span>'}`;
            resultHTML.appendChild(div);
        });

        container.appendChild(resultHTML);

        // Vẽ chart
        const ctx = document.getElementById("resultChart").getContext("2d");
        const times = data.results.map(r => parseInt(r.task_finish_time.replace("ms", "")));
        const avg = times.reduce((a, b) => a + b, 0) / times.length;

        new Chart(ctx, {
            type: "line",
            data: {
                labels: data.results.map(r => `TC ${r.test_case}`),
                datasets: [
                    {
                        label: "Task Finish Time (ms)",
                        data: times,
                        borderColor: "blue",
                        tension: 0.3,
                    },
                    {
                        label: "Average Time",
                        data: new Array(times.length).fill(avg),
                        borderColor: "red",
                        borderDash: [5, 5],
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Test Case Execution Times'
                    }
                }
            }
        });
        saveResult(data);
    }
    function saveResult(data) {
        const code = {
            user_id: CurrentuserID,
            username: Currentusername,
            code: editor.getValue(),
            input: input.value,
            id: currentTestId,
            type: "practice",
            lang: option.value,
            result: data,
            sessionID: currensessionID
        };

        fetch("<?php echo $site_url ?>/api/save/result/code", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(code)
        })
        .then(res => res.json())
        .then(res => console.log("Save response:", res))
        .catch(err => console.error("Save failed:", err));
    }


    // JavaScript để xử lý tab switching
document.addEventListener('DOMContentLoaded', function () {
    const problemButton = document.getElementById('problemButton');
    const solutionButton = document.getElementById('solutionButton');
    const compileButton = document.getElementById('compileButton');
    const guideButton = document.getElementById('guideButton');

    const problemContent = document.getElementById('problemContent');
    const solutionContent = document.getElementById('solutionContent');
    const compileAndResultContent = document.getElementById('compileAndResultContent');
    const guideContent = document.getElementById('guideContent');

    // Hàm ẩn tất cả content
   
    // Mặc định tab Problem
    problemButton.classList.add('active');
    problemContent.classList.add('active');

    // Các listener:
    problemButton.addEventListener('click', function () {
        deactivateAllButtons();
        hideAllContents();
        this.classList.add('active');
        problemContent.classList.add('active');
        //renderProblem(); // nếu có
    });

    solutionButton.addEventListener('click', function () {
        deactivateAllButtons();
        hideAllContents();
        this.classList.add('active');
        solutionContent.classList.add('active');
    });

    compileButton.addEventListener('click', function () {
        deactivateAllButtons();
        hideAllContents();
        this.classList.add('active');
        compileAndResultContent.classList.add('active');
    });

    guideButton.addEventListener('click', function () {
        deactivateAllButtons();
        hideAllContents();
        this.classList.add('active');
        guideContent.classList.add('active');
    });
});

    document.getElementById('difficulty').addEventListener('change', () => fetchData(1));
    window.onload = () => fetchData();renderProblem();
</script>
<script>
const translateAPI = async (text) => {
    const res = await fetch('http://localhost:5000/translate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            text: text,
            source: 'en',
            target: 'vi'
        })
    });
    const data = await res.json();
    return data.translatedText;
};

// Đề thi
const problemBtn = document.getElementById('translate-problem-btn');
const problemContentDiv = document.querySelector('#problemContent .problem-content');
let problemOriginalHTML = '';

problemBtn.addEventListener('click', async () => {
    if (problemBtn.innerText === 'Dịch đề thi') {
        problemOriginalHTML = problemContentDiv.innerHTML;
        problemBtn.disabled = true;
        problemBtn.innerText = 'Đang dịch...';

        const translated = await translateAPI(problemOriginalHTML);
        problemContentDiv.innerHTML = translated;
        problemBtn.innerText = 'Hiển thị đề thi gốc';
        problemBtn.disabled = false;
    } else {
        problemContentDiv.innerHTML = problemOriginalHTML;
        problemBtn.innerText = 'Dịch đề thi';
    }
});

// Lời giải
const solutionBtn = document.getElementById('translate-solution-btn');
const analysisDiv = document.getElementById('problem-analysis');
let solutionOriginalHTML = '';

solutionBtn.addEventListener('click', async () => {
    if (solutionBtn.innerText === 'Dịch lời giải') {
        solutionOriginalHTML = analysisDiv.innerHTML;
        solutionBtn.disabled = true;
        solutionBtn.innerText = 'Đang dịch...';

        const translated = await translateAPI(solutionOriginalHTML);
        analysisDiv.innerHTML = translated;
        solutionBtn.innerText = 'Hiển thị lời giải gốc';
        solutionBtn.disabled = false;
    } else {
        analysisDiv.innerHTML = solutionOriginalHTML;
        solutionBtn.innerText = 'Dịch lời giải';
    }
});
</script>

</html>
<?php
}


else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";
    //get_footer();
}
get_footer();

