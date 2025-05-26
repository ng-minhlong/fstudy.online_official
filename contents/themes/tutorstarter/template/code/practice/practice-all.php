<?php
/*
* Template Name: Practice Coding Template
* Template Post Type: practice template
*/
if (!defined("ABSPATH")) exit();

add_filter('document_title_parts', function ($title) {
    $title['title'] = "Happy Coding Practice";
    return $title;
});
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
$sessionId = generate_uuid_v4();

$current_user = wp_get_current_user();
$current_username = $current_user->user_login;
$username = $current_username;
get_header();
$site_url = get_site_url();
echo "<script>
    var Currentusername = '" . $username . "';
    var siteUrl = '" . $site_url . "';
    var sessionId = '" . $sessionId ."';
</script>";
?>

<head>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f8f8f8; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    button {
        padding: 6px 12px;
        margin: 2px;
        background-color: #0073aa;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button[disabled] {
        background-color: #aaa;
        cursor: default;
    }
    #loading {
        display: none;
        margin-top: 10px;
    }
</style>
</head>

<body>
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
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Difficulty</th>
                <th>Acceptance Rate</th>
                <th>Status</th>
                <th>Do Practice</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="pagination"></div>

    <script>
let currentPage = 1;
let completionData = []; // Store completion data globally

function showLoading(show = true) {
    document.getElementById('loading').style.display = show ? 'block' : 'none';
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

function fetchCompletionStatus() {
    const username = Currentusername; // Make sure this variable is defined
    
    return fetch(`${siteUrl}/api/get/all-result/code`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        // Store the completion data for later use
        if (data.success && Array.isArray(data.data)) {
            completionData = data.data;
        }
        return completionData;
    })
    .catch(error => {
        console.error('Error fetching completion status:', error);
        return [];
    });
}

function checkIfCompleted(practiceId) {
    // Check if this practice ID exists in completion data
    return completionData.some(item => 
        item.id_problems === practiceId.toString()  
    );
}

function fetchData(page = 1) {
    showLoading(true);
    const difficulty = document.getElementById('difficulty').value;
    
    // First fetch completion status, then fetch practice data
    Promise.all([
        fetchCompletionStatus(),
        fetch(`${siteUrl}/api/tests/practice/code/all`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ difficulty, page })
        }).then(res => res.json())
    ])
    .then(([completionData, json]) => {
        const tbody = document.querySelector('#practiceTable tbody');
        tbody.innerHTML = '';
        
        json.data.forEach(row => {
            const isCompleted = checkIfCompleted(row.id);
            
            tbody.innerHTML += 
                `<tr>
                    <td>${row.id}</td>
                    <td>${row.title}</td>
                    <td>${row.content.slice(0, 200)}...</td>
                    <td>${row.difficulty}</td>
                    <td>${row.acceptance_rate}%</td>
                    <td>${isCompleted ? '✅ Hoàn thành' : '❌ Chưa hoàn thành'}</td>
                    <td><button onclick="window.location.href='${siteUrl}/code/practice/id/${row.id}/${sessionId}'">Do Practice</button></td>
                </tr>`;
        });
        
        const totalPages = Math.ceil(json.total / json.limit);
        renderPagination(page, totalPages);
        currentPage = page;
        showLoading(false);
    })
    .catch(error => {
        console.error('Error:', error);
        showLoading(false);
    });
}

document.getElementById('difficulty').addEventListener('change', () => fetchData(1));
window.onload = () => fetchData();
</script>
</body>
