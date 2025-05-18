
// Tính trung bình các tiêu chí
const avgTA = (scoreTATask1 + scoreTATask2) / 2;
const avgCC = (scoreCCTask1 + scoreCCTask2) / 2;
const avgGra = (scoreGraTask1 + scoreGraTask2) / 2;
const avgLr = (scoreLrTask1 + scoreLrTask2) / 2;

// Xác định tiêu chí tốt nhất và kém nhất
const criteria = {
    TA: avgTA,
    CC: avgCC,
    Gra: avgGra,
    Lr: avgLr
};
const bestCriteria = Object.keys(criteria).reduce((a, b) => criteria[a] > criteria[b] ? a : b);
const worstCriteria = Object.keys(criteria).reduce((a, b) => criteria[a] < criteria[b] ? a : b);

// Hiển thị kết luận
const conclusion = `
    <p><strong>Kết luận:</strong></p>
    <p>Bạn tốt nhất ở <strong>${bestCriteria}</strong> (${criteria[bestCriteria].toFixed(1)})</p>
    <p>Bạn kém nhất ở <strong>${worstCriteria}</strong> (${criteria[worstCriteria].toFixed(1)})</p>
`;

// Tạo nội dung overallUserTest
var overallUserTest = `
    <p><strong>Band score overall:</strong> ${bandScoreOverall}</p>
    <p><strong>Your overall Test:</strong></p>
    <div>
        <div>
            <h2>Band Scores</h2>
            <canvas id="bandScoreChart"></canvas>
        </div>

        <div>
            <h2>Task 1 Scores</h2>
            <canvas id="task1PieChart"></canvas>
        </div>

        <div>
            <h2>Task 2 Scores</h2>
            <canvas id="task2PieChart"></canvas>
        </div>

        <div>
            <h2>Average Scores by Criteria</h2>
            <canvas id="averageLineChart"></canvas>
        </div>

        <h3>Task 1</h3>
        <p><strong>Overall Band:</strong> ${overallbandTask1}</p>
        <p><strong>TA:</strong> ${scoreTATask1}</p>
        <p><strong>CC:</strong> ${scoreCCTask1}</p>
        <p><strong>Gra:</strong> ${scoreGraTask1}</p>
        <p><strong>Lr:</strong> ${scoreLrTask1}</p>
    </div>
    <div>
        <h3>Task 2</h3>
        <p><strong>Overall Band:</strong> ${overallbandTask2}</p>
        <p><strong>TA:</strong> ${scoreTATask2}</p>
        <p><strong>CC:</strong> ${scoreCCTask2}</p>
        <p><strong>Gra:</strong> ${scoreGraTask2}</p>
        <p><strong>Lr:</strong> ${scoreLrTask2}</p>
    </div>
    ${conclusion}
`;

// Hiển thị overallUserTest (ví dụ: gán vào một phần tử khác)

// Vẽ biểu đồ (sau khi overallUserTest được thêm vào DOM)
function drawCharts() {
    // Vẽ biểu đồ Band Scores (Bar Chart)
    const bandScoreCtx = document.getElementById('bandScoreChart').getContext('2d');
    new Chart(bandScoreCtx, {
        type: 'bar',
        data: {
            labels: ['Overall Band', 'Task 1', 'Task 2'],
            datasets: [{
                label: 'Band Scores',
                data: [bandScoreOverall, overallbandTask1, overallbandTask2],
                backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 9.0
                }
            }
        }
    });

    // Vẽ biểu đồ Task 1 (Pie Chart)
    const task1PieCtx = document.getElementById('task1PieChart').getContext('2d');
    new Chart(task1PieCtx, {
        type: 'pie',
        data: {
            labels: ['TA', 'CC', 'Gra', 'Lr'],
            datasets: [{
                label: 'Task 1 Scores',
                data: [scoreTATask1, scoreCCTask1, scoreGraTask1, scoreLrTask1],
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0'],
                borderWidth: 1
            }]
        }
    });

    // Vẽ biểu đồ Task 2 (Pie Chart)
    const task2PieCtx = document.getElementById('task2PieChart').getContext('2d');
    new Chart(task2PieCtx, {
        type: 'pie',
        data: {
            labels: ['TA', 'CC', 'Gra', 'Lr'],
            datasets: [{
                label: 'Task 2 Scores',
                data: [scoreTATask2, scoreCCTask2, scoreGraTask2, scoreLrTask2],
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0'],
                borderWidth: 1
            }]
        }
    });

    // Vẽ biểu đồ trung bình (Line Chart)
    const averageLineCtx = document.getElementById('averageLineChart').getContext('2d');
    new Chart(averageLineCtx, {
        type: 'line',
        data: {
            labels: ['TA', 'CC', 'Gra', 'Lr'],
            datasets: [{
                label: 'Average Scores',
                data: [avgTA, avgCC, avgGra, avgLr],
                borderColor: '#36a2eb',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 9.0
                }
            }
        }
    });
}

// Gọi hàm vẽ biểu đồ sau khi overallUserTest được thêm vào DOM
// Ví dụ: nếu bạn gán overallUserTest vào một phần tử khác, hãy gọi drawCharts() sau đó
document.getElementById('someElement').innerHTML = overallUserTest;
 drawCharts();
