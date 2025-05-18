let essayResponses = {}; // Lưu tất cả final_analysis theo ID câu hỏi
let essayAnswers = {};   // Lưu answer của user
let scoreOverallAndBand = {}; // Lưu điểm số tổng kết


async function processEssay(i) {
    let userEssay = document.getElementById(`question-${i}-input`).value;

    let countText = document.getElementById(`word-count-${i}`).textContent;
    let counts = countText.match(/\d+/g);
    let wordCount = counts ? parseInt(counts[0]) : 0;
    let sentenceCount = counts ? parseInt(counts[1]) : 0;
    let paragraphCount = counts ? parseInt(counts[2]) : 0;

    let sampleEssay = quizData.questions[i].sample_essay.replace(/<br>/g, '\n');
    let currentPart = quizData.questions[i].part;
    let currentQuestion = quizData.questions[i].question;
    let currentIDQuestion = quizData.questions[i].id_question;

    // Show progress with SweetAlert
    Swal.fire({
        title: 'Đang chấm điểm',
        html: `Bắt đầu chấm phần: <b>${currentPart}</b>`,
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    console.log(`Start mark for part: ${currentPart}`);

    const dataAns = {
        id_question: currentIDQuestion,
        part: currentPart,
        question: currentQuestion,
        wordCount: wordCount,
        sentenceCount: sentenceCount,
        paragraphCount: paragraphCount,
        answer: userEssay
    };

    try {
        let response = await fetch(`${siteUrl}/api/public/test/v1/ielts/writing/`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                question: currentQuestion,
                answer: userEssay,
                part: currentPart,
                sample: sampleEssay,
                idquestion: currentIDQuestion,
                type: quizData.questions[i].question_type,
                data: dataAns
            })
        });

        let data = await response.json();
        console.log(`Part ${currentPart} response:`, data);
        console.log(`Successfully mark for : ${currentPart}`);

        // Update success notification
        Swal.fire({
            title: 'Hoàn thành',
            html: `Đã chấm xong phần: <b>${currentPart}</b>`,
            icon: 'success',
            timer: 2000,
            allowOutsideClick: false,
            showConfirmButton: false
        });

        essayResponses[currentPart] = data;
        document.getElementById("user_band_score_and_suggestion").value = JSON.stringify(essayResponses, null, 2);

        if (data.answer) {
            essayAnswers[currentPart] = {
                answer: data.answer,
                dataAns: dataAns
            };
            console.log("Updated answers by part:", essayAnswers);
            document.getElementById("user_essay").value = JSON.stringify(essayAnswers);
        }

        // ✅ Chỉ gọi final result khi đã có đủ kết quả cho tất cả câu hỏi
        if (Object.keys(essayResponses).length === quizData.questions.length) {
            await processFinalResult();
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Lỗi',
            allowOutsideClick: false,
            text: `Có lỗi xảy ra khi chấm phần ${currentPart}`,
            icon: 'error'
        });
    }
}
async function processFinalResult() {
    // Show processing final result
    Swal.fire({
        title: 'Đang xử lý',
        html: 'Đang tổng hợp kết quả cuối cùng...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    console.log("Start combinning all tests and get final result");
    try {
        let responsefinal = await fetch(`${siteUrl}/api/public/test/v1/ielts/final_writing/`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ results: essayResponses })
        });

        finalResult = await responsefinal.json();
        console.log('Final result:', finalResult);

        if (finalResult) {
            document.getElementById("band-score-expand-form").value = JSON.stringify(finalResult, null, 2);

            if (finalResult.bands?.overallBand !== undefined) {
                document.getElementById("band-score-form").value = finalResult.bands.overallBand;
            }
        }

        // Đảm bảo ResultInput() hoàn thành trước khi hiển thị thông báo
        await ResultInput(); // Thêm await ở đây
        console.log(`Succesfully get mark, click this link to get report: ${siteUrl}/ielts/w/result/${resultId}`);

        // Đóng loading trước khi hiển thị kết quả
        Swal.close();

        // Show final result with link
        Swal.fire({
            title: 'Hoàn thành!',
            html: `Đã chấm điểm xong!<br><br>
                  <a href="${siteUrl}/ielts/w/result/${resultId}" class="btn btn-primary">
                    Xem báo cáo chi tiết
                  </a>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            icon: 'success',
        });

    } catch (error) {
        console.error(`Error getting final result:`, error);
        Swal.fire({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi tổng hợp kết quả cuối cùng',
            icon: 'error'
        });
    }
}

// Hàm làm tròn số về .0 hoặc .5
function roundToNearestHalf(score) {
    const floor = Math.floor(score); // Phần nguyên
    const decimal = score - floor; // Phần thập phân

    if (decimal < 0.25) {
        return floor; // Làm tròn xuống .0
    } else if (decimal >= 0.25 && decimal < 0.75) {
        return floor + 0.5; // Làm tròn lên .5
    } else {
        return floor + 1; // Làm tròn lên .0 của số nguyên tiếp theo
    }
}