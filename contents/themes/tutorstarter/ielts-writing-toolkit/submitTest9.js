
function updateWordCount(index) {

    const textarea = document.getElementById(`question-${index}-input`);
    const wordCountDiv = document.getElementById(`word-count-${index}`);


    let sentences = textarea.value.match(/[^.!?]+[.!?]+[\])'"`’”]*|.+$/g);
    let sentenceCount = sentences ? sentences.length : 0;

    let paragraphs = textarea.value.split('\n').filter(paragraph => paragraph.trim() !== '');
    let paragraphCount = paragraphs.length;

    const wordCount = textarea.value.trim().split(/\s+/).filter(word => word.length > 0).length;
    wordCountDiv.textContent = `Word count: ${wordCount} Sentence Count: ${sentenceCount} Paragraph Count: ${paragraphCount}`;

    



}




// bat dau cham diem
let testSubmitted = false;

async function preSubmitTest() {
    if (!checkValidSubmit()) {
        return; // Ngăn chặn việc nộp bài nếu có lỗi
    }
    //submitTest();

/*    await Swal.fire({
        title: "Đang nộp bài thi",
        html: "Vui lòng đợi trong giây lát.",
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
            Swal.showLoading();
        },
        didClose: () => {
            Swal.hideLoading();
        }
    });*/
}


function checkValidSubmit() {
    let underWordLimit = [];
    const questions = document.getElementsByClassName("questions");

    for (let i = 0; i < questions.length; i++) {
        const textarea = document.getElementById(`question-${i}-input`);
        if (!textarea) continue;

        const wordCount = textarea.value.trim().split(/\s+/).filter(word => word.length > 0).length;

        if (wordCount < 100) {
            underWordLimit.push(`Câu hỏi ${i + 1} của bạn dưới 100 từ, vui lòng bổ sung.`);
        }
    }

    if (underWordLimit.length > 0) {
        Swal.fire({
            title: "Cảnh báo!",
            html: underWordLimit.join("<br>"),
            icon: "warning",
            confirmButtonText: "OK"
        });
        return false;
    }
    else{
        submitTest();
    }

    return true;
}



async function submitTest() {

    if (testSubmitted) return; // Avoid double submission

    testSubmitted = true; 
    clearInterval(countdownInterval); // Stop the countdown

    

    // Calculate time used correctly
    let timeUsed = duration - countdownElement;
    let formattedTimeUsed = formatTime(timeUsed);

    console.log("Time used: ", formattedTimeUsed);
    
    let timeSpent = document.getElementById("time-result");
    //document.getElementById('submit-button').style.display = 'none';
    //document.getElementById('countdown').style.display = 'none';

    
    timeSpent.innerText += `${formattedTimeUsed}`;
      //  document.getElementById("result-full-page").style.display="block";

        //document.getElementById('overall_band_test_container').style.display = 'block';
        //document.getElementById("container").style.display = "none"

        const questions = document.getElementsByClassName("questions");

        const buttonsContainer = document.getElementById("question-buttons-container"); // To hold the navigation buttons

        let overallBandsSummary = ''; // Initialize the summary string

        let totalPart1 = 0;
        let totalPart2 = 0;
        let countPart1 = 0;
        let countPart2 = 0;
        let xValues = [];
        let yValues = [];

//let overallband_2 = 0;
    
let userEssayTask1 = document.getElementById("user-essay-task-1");
let userEssayTask2 = document.getElementById("user-essay-task-2");
let userSummaryTask1 = document.getElementById("summary-essay-task-1");
let userSummaryTask2 = document.getElementById("summary-essay-task-2");
let userBreakdownTask1 = document.getElementById("breakdown-task-1");
let userBreakdownTask2 = document.getElementById("breakdown-task-2");
let userdetailsCommentTask1 = document.getElementById("details-comment-task-1");
let userdetailsCommentTask2 = document.getElementById("details-comment-task-2");


    for (let i = 0; i < questions.length; i++) {
   
        
    /*await checkGrammarErrors(i);
    await Check_Structure_Part_1(i);

    await Check_Vocab_Range_Part_1(i); */
    /*console.log("Grammr eerrror check trong submit:",spelling_grammar_error_essay)
    
    console.log("Length essay check trong submit:",length_essay)
    
    console.log("Paragraphg check trong submit:",paragraph_essay)

    console.log(`Linking word checking trong submit: Số linking Words: ${total_linking_word_count}, Các linking words ${linking_word_array_essay}, Số linking word khác nhau ${unique_linking_word_count}`)
    */
    //console.log("Intro checking trong submit:",point_for_intro_cheking_part_1_essay)
    //console.log("Number of data:",count_common_number)
    let userEssayInput = document.getElementById(`question-${i}-input`).value;
    // Set the content as a paragraph in the corresponding div

   
    

    document.getElementById(`userEssayCheck-${i+1}`).innerHTML = `<p>${userEssayInput}</p>`;

   // document.getElementById(`userEssayCheckContainer${i+1}`).style.display='block';
    //document.getElementById(`question-${i}-input`).style.display='none';

    let userEssayCheckContainer = document.getElementById(`userEssayCheckContainer${i+1}`).innerHTML;
    let userMarkEssay = document.getElementById("tab2-user-essay-container").innerHTML;
    let sampleEssay =  document.getElementById("sample-tab-container").innerHTML;
    let questionContextArea = document.getElementById(`questionContextArea-${i}`).innerHTML;
    userEssayTab2 = document.getElementById(`userEssayTab2-${i}`).innerHTML;
    
    
    await processEssay(i);

   
    

    let textarea = document.getElementById(`question-${i}-input`);
    let explanation = document.getElementById(`explanation-${quizData.questions[i].id_question}`);
    let overallBandDiv = document.getElementById(`overall-band-${quizData.questions[i].id_question}`);
    let sampleEassayContainer = document.getElementById(`sample-essay-area-${i}`);
    let current_band_detail = document.getElementById(`current_band_detail_div-${quizData.questions[i].id_question}`);

    let summarizeUserEssay = document.getElementById(`summarize-${i}`);
    let detailCretariaEssay = document.getElementById(`detail-cretaria-${i}`);
    let breakDownEssay = document.getElementById(`breakdown-${i}`);

    let recommendToBoostBand = document.getElementById(`recommendation-${i}`);
   
    

    userMarkEssay += `${questionContextArea}<br> ${userEssayTab2}`;
    sampleEssay += `${sampleEassayContainer.innerHTML}`;

    
     // Create a navigation button for each question
     const button = document.createElement("button");
     button.innerHTML = `Question ${i + 1}`;
     button.addEventListener('click', function() {
         displayQuestion(i); // Function to display respective question
     });
     buttonsContainer.appendChild(button);


    document.getElementById("tab2-user-essay-container").innerHTML = userMarkEssay;
    document.getElementById("sample-tab-container").innerHTML = sampleEssay;
    
    


    let part = quizData.questions[i].part;


    // Disable the textarea
    textarea.disabled = true;
    //sampleEassayContainer.style.display='block';
    //summarizeUserEssay.style.display = 'block';
    //detailCretariaEssay.style.display = 'block';
    //recommendToBoostBand.style.display = 'block';
    //breakDownEssay.style.display = 'block';

    let symbol_check_length_essay_summarize;
    let symbol_check_paragraph_essay_summarize;
    let symbol_check_spelling_grammar_summarize;
    let symbol_check_linking_word_summarize;
    let symbol_check_essay_requirement_summarize; 

    if(count_common_number >= 2 && length_essay > 100){
        relation_point_essay_and_question = (point_for_second_paragraph_cheking_part_1_essay + point_for_intro_cheking_part_1_essay)/2
    }else{
        relation_point_essay_and_question = 0;
    }

    // Define the green checkmark and red tick symbols
    let green_check = `<span style='color:green'>&#10004;</span>`;
    let red_tick = `<span style='color:red'>&#x2716;</span>`;

    // Determine which symbol to use based on the essay length

    if (length_essay >= 150 && length_essay < 225 && part == 1) {
        symbol_check_length_essay_summarize = `${green_check}`;
    } else if ((length_essay < 150 && part == 1) || (length_essay >= 210 && part == 1)) {
        symbol_check_length_essay_summarize = `${red_tick}`;
    }
    else if ((length_essay >= 250 && length_essay <= 300 && part == 2)) {
        symbol_check_length_essay_summarize = `${green_check}`;
    }
    else if ((length_essay < 250 && part == 2) || (length_essay > 300 && part == 2)) {
        symbol_check_length_essay_summarize = `${red_tick}`;
    }


    if (paragraph_essay == 3 || paragraph_essay == 4){
          symbol_check_paragraph_essay_summarize = green_check;
    }else {
        symbol_check_paragraph_essay_summarize = red_tick;
    }


    if (spelling_grammar_error_count <= 2) symbol_check_spelling_grammar_summarize = green_check;
    else symbol_check_spelling_grammar_summarize = red_tick;


    if(total_linking_word_count > 4)  symbol_check_linking_word_summarize = green_check;
    else symbol_check_linking_word_summarize = red_tick;


    if(relation_point_essay_and_question > 0.9)  symbol_check_essay_requirement_summarize = green_check;
    else symbol_check_essay_requirement_summarize = red_tick;
    


    

    /*summarizeUserEssay.innerHTML += `
        <h3 style="color:red">A. Tổng quát bài làm (Summarize your essay):</h3>
        
        <td>
        <p>* Current Part: ${part}. Type: ${type_of_essay}</p></td>
    
        <p>* Số từ: ${length_essay} ${symbol_check_length_essay_summarize}</p>
        <p>* Số đoạn: ${paragraph_essay} ${symbol_check_paragraph_essay_summarize}</p>
    
        <p>* Số lỗi ngữ pháp/ từ vựng: ${spelling_grammar_error_count} ${symbol_check_spelling_grammar_summarize}</p>
        <p>* Số từ nối: ${total_linking_word_count} ${symbol_check_linking_word_summarize}</p>
        <p>* Độ liên quan đến câu hỏi: ${relation_point_essay_and_question.toFixed(3)}${symbol_check_essay_requirement_summarize}</p>
        <h5 style="color:red" >Tip nhỏ: Để đạt được band 6+ Ielts Writing, các bạn cần đạt ${green_check} ở tất cả yếu tố trên </h5>
        
    `;*/

    summarizeUserEssay.innerHTML += `
        <h3 style="color:red">A. Tổng quát bài làm (Summarize your essay):</h3>
        
       
        <p id = "current_essay_task_${i+1}">${part}</p>
        <p id = "type_of_essay_task_${i+1}"> ${type_of_essay}</p>

        <p id ="word_count_task_${i+1}">${length_essay}</p>
        <p id ="paragraph_count_task_${i+1}">${paragraph_essay}</p>
    
        <p id ="number_of_spelling_grammar_error_task_${i+1}">${spelling_grammar_error_count}</p>
        <p id ="total_linking_word_count_task_${i+1}">${total_linking_word_count}</p>
        <p id ="relation_point_essay_and_question_task_${i+1}"> ${relation_point_essay_and_question.toFixed(3)}</p>
        
    `;
    


    

    
    let task_achievement1_1; //check độ dài user essay - (length_essay) //dạng point
    let task_achievement1_2; // check thông tin trong user essay và sample (count_common_number) //dạng point
    let task_achievement1_3; //dạng point
    let task_achievement1_4; //dạng point
    let task_achievement1_1_comment = ``; 
    let task_achievement1_2_comment = ``; 
    let task_achievement1_3_comment = ``;
    let task_achievement1_4_comment = ``;

    let task_achievement2_1; //check độ dài user essay - (length_essay) //dạng point
    let task_achievement2_2; // check thông tin trong user essay và sample (count_common_number) //dạng point
    let task_achievement2_3; //dạng point
    let task_achievement2_4; //dạng point
    let task_achievement2_1_comment = ``; 
    let task_achievement2_2_comment = ``; 
    let task_achievement2_3_comment = ``;
    let task_achievement2_4_comment = ``;
    
    let coherenceandcohesion1_1;  //check xem user có viết đúng theo thứ tự introduction -> Overall -> body(detail) không ? //dạng point
    let coherenceandcohesion1_2;  //dạng point
    let coherenceandcohesion1_3; //dạng point
    let coherenceandcohesion1_4; //dạng point
    let coherenceandcohesion1_1_comment = ``; 
    let coherenceandcohesion1_2_comment = ``; 
    let coherenceandcohesion1_3_comment = ``;
    let coherenceandcohesion1_4_comment = ``;
    let coherenceandcohesion2_1;  //check xem user có viết đúng theo thứ tự introduction -> Overall -> body(detail) không ? //dạng point
    let coherenceandcohesion2_2;  //dạng point
    let coherenceandcohesion2_3; //dạng point
    let coherenceandcohesion2_4; //dạng point
    let coherenceandcohesion2_1_comment = ``; 
    let coherenceandcohesion2_2_comment = ``; 
    let coherenceandcohesion2_3_comment = ``;
    let coherenceandcohesion2_4_comment = ``;

    let lexical_resource1_1; //dạng point
    let lexical_resource1_2; //dạng point
    let lexical_resource1_3; //dạng point
    let lexical_resource1_4; //dạng point
    let lexical_resource1_1_comment = ``;
    let lexical_resource1_2_comment = ``;
    let lexical_resource1_3_comment = ``;
    let lexical_resource1_4_comment = ``;
    let lexical_resource2_1; //dạng point
    let lexical_resource2_2; //dạng point
    let lexical_resource2_3; //dạng point
    let lexical_resource2_4; //dạng point
    let lexical_resource2_1_comment = ``;
    let lexical_resource2_2_comment = ``;
    let lexical_resource2_3_comment = ``;
    let lexical_resource2_4_comment = ``;

    

    let grammatical_range_and_accuracy1_1; //dạng point
    let grammatical_range_and_accuracy1_2; //dạng point
    let grammatical_range_and_accuracy1_3; //dạng point
    let grammatical_range_and_accuracy1_4; //dạng point
    let grammatical_range_and_accuracy1_1_comment = ``;
    let grammatical_range_and_accuracy1_2_comment = ``;
    let grammatical_range_and_accuracy1_3_comment = ``;
    let grammatical_range_and_accuracy1_4_comment = ``;
    let grammatical_range_and_accuracy2_1; //dạng point
    let grammatical_range_and_accuracy2_2; //dạng point
    let grammatical_range_and_accuracy2_3; //dạng point
    let grammatical_range_and_accuracy2_4; //dạng point
    let grammatical_range_and_accuracy2_1_comment = ``;
    let grammatical_range_and_accuracy2_2_comment = ``;
    let grammatical_range_and_accuracy2_3_comment = ``;
    let grammatical_range_and_accuracy2_4_comment = ``;


    if (part == 1 && length_essay > 30)
    {
        // 

        if (length_essay < 130){
            task_achievement1_1 = 0;
            task_achievement1_1_comment = `Độ dài bài viết của bạn quá ngắn. Đối với Task 1 độ dài tối thiểu là 150 từ. Bạn nên viết trong khoảng 160 - 190 từ đối với dạng bài có 1 biểu đồ và khoảng 170 - 210 từ đối với dạng bài nhiều biểu đồ (mixed chart/graph). Việc viết thiếu sẽ ảnh hưởng rất nhiều đến kết quả của bạn và thông thường sẽ nhận được < 5.0 band `
        
        }
        else{
            if (length_essay >= 130 && length_essay < 150 ){
                task_achievement1_1 = 0.5;
                task_achievement1_1_comment = `Có vẻ như độ dài của bài viết bạn chưa đạt yêu cầu. Bạn nên cố gắng để độ dài Task 1 kéo dài lên > 150 từ để cải thiện điểm cho phần Task Achievement. Nếu bạn chưa đủ độ dài do vấn đề thời gian, hãy cải thiện bằng cách luyện tập nhiều, tập trung cao độ cho bài viết của mình. Đối với task 1 bạn nên dùng nhiều nhất 20 phút để hoàn thiện (5 phút lên dàn ý, 10 phút viết bài và 5 phút dành cho việc soát lỗi). `
            }
            else if(length_essay >= 150 && length_essay < 160 && type_of_essay != 'Mixed'){
                task_achievement1_1 = 1;
                task_achievement1_1_comment =`Độ dài bài viết đã đạt yêu cầu ! (> 150 từ). Tuy vậy, bạn nên mở rộng ra từ 160 - 180 từ nhé, để bài viết đầy đủ và giám khảo sẽ nhận thấy bài viết của bạn chỉnh chu và đủ ý. Về độ dài bạn vẫn sẽ được 75 - 80% số điểm cho phần độ dài nhưng bạn nên lưu ý thêm nhé ^^. `
            }
            else if(length_essay >= 150 && length_essay < 170 && type_of_essay == 'Mixed'){
                task_achievement1_1 = 1;
                task_achievement1_1_comment = `Độ dài bài viết đủ yêu cầu (> 150 từ). Nhưng đối với dạng bài mixed graph/chart như này, bạn nên viết trong khoảng 170 - 200 từ để đạt tối đa điểm cho độ dài nhé. Về độ dài bạn vẫn sẽ được 60 - 75% số điểm cho phần độ dài nhưng bạn nên lưu ý thêm nhé ^^. `;
            }
            else if(length_essay >= 170 && length_essay < 225 && type_of_essay == 'Mixed'){
                task_achievement1_1 = 2;
                task_achievement1_1_comment = `Tuyệt vời ! Độ dài bài viết của bạn cho task 1(dạng mixed) là hoàn hảo rồi. Bạn sẽ đạt tối đa điểm cho phần độ dài trong bài thi lần này lẫn bài thi thật (real exam) khi bạn thi Ielts. Hãy tiếp tục phát huy nhé ^^. `;
            }
            else if(length_essay >= 160 && length_essay < 225){
                task_achievement1_1 = 2;
                task_achievement1_1_comment = `Tuyệt vời ! Độ dài bài viết của bạn cho task 1 là hoàn hảo rồi. Bạn sẽ đạt tối đa điểm cho phần độ dài trong bài thi lần này lẫn bài thi thật (real exam) khi bạn thi Ielts. Hãy tiếp tục phát huy nhé ^^. `
            }
            else if(length_essay >= 225 && length_essay < 250){
                task_achievement1_1 = 1;
                task_achievement1_1_comment = `Độ dài bài viết của bạn là quá dài. Việc này sẽ ảnh hưởng tới thời gian làm bài của bạn là rất lớn. Bạn nên nhớ đây mới chỉ là Writing Task 1 và ở bài thi thật bạn còn task 2 (sẽ chiếm phần lớn thời gian và điểm). Hãy giới hạn bài viết của bạn lại trong khoảng 160 - 180 từ để đạt điểm tối đa của phần length(độ dài)`
            }
            else if (length_essay >= 250){
                task_achievement1_1 = 1;
                task_achievement1_1_comment = `No comment -.- Sao bạn viết dài thế !? Bạn đang trêu tôi hay sao, bạn nên viết trong khoảng 160 - 180 từ thôi để đạt yêu cầu đề bài và cũng vừa thời gian làm bài. Bạn sẽ KHÔNG đạt điểm cho phần độ dài !`
            
            }

            if(paragraph_essay < 3){
                task_achievement1_1 += 0.25;
                task_achievement1_1_comment += `Ngoài ra, bạn nên chia bài làm của bạn thành 3 - 4 phần (đoạn). Phần 1: Introduction, phần 2: Overall, phần 3: Detail information (phân tích các dữ liệu trong đề), phần 4 (optional/ không bắt buộc): Tổng kết lại bài làm`
            }
            else if(paragraph_essay == 3 || paragraph_essay == 4){
                task_achievement1_1 += 1;
                task_achievement1_1_comment += `Ngoài ra, bạn chia bố cục bài làm hợp lý rồi (3 - 4 đoạn) để phù hợp logic, cấu trúc bài làm và phù hợp với các tiêu chí khác như Coherence and Cohesion`
            }
            else{
                task_achievement1_1 += 0.5;
                task_achievement1_1_comment += `Bạn đang chia bố cục không hợp lý cho lắm. Thông thường ở task 1, bạn nên chia làm 3 - 4 phần tương ứng Introduction, Overall, Detail Information, Summary(không bắt buộc). Việc chia thành nhiều đoạn sẽ gây ra nhiễu thông tin và ảnh hưởng tính mạch lạc và gắn kết của bài làm `
            }
        }

        if (count_common_number == 0){
            task_achievement1_2 = 0;
            task_achievement1_2_comment = `Bạn chưa sử dụng bất cứ thông tin và số liệu nào có trong bài. Điều này có nghĩa là bạn đang không viết đúng và lạc đề hoàn toàn. Bạn nên nhớ rằng writing task 1 sẽ kiểm tra khả năng sử dụng các dữ kiện trong các chart/ process/ table/ map nên bạn cần phân tích các số liệu có trong đó. Chính vì vậy điểm cho tiêu chí task achievement của bạn sẽ rất thấp nếu không bao gồm các dữ liệu / thông tin đề bài cho` 
        }
        else{
            if (count_common_number >= 1 && count_common_number <= 3){
                task_achievement1_2 = 0.5;
                task_achievement1_2_comment = `Bạn cần phải đề cập mọi thông tin QUAN TRỌNG có trong hình vẽ, việc chỉ bao gồm ${count_common_number} thông tin (bao gồm các đối tượng trong bài và các dữ liệu) là tương đối ít và nhiều khả năng bạn đã thiếu nhiều thông tin quan trọng`;
            }
            else if(count_common_number >= 4 && count_common_number <= 7){
                task_achievement1_2 = 2;
                task_achievement1_2_comment= `Bạn đã tương đối sử dụng đủ các thông tin có trong bài, bao gồm các số liệu và các đối tượng cần được so sánh. Cụ thể là ${count_common_number} lần áp dụng số liệu vào để phân tích và so sánh. Bạn nên tiếp tục phát huy điều này nhé ! `
            }
            else if (count_common_number >= 8 && count_common_number <= 11){
                task_achievement1_2 = 2.5;
                task_achievement1_2_comment= `Bạn đã sử dụng đầy đủ các thông tin có trong bài và sử dụng nó chi tiết. Điều này sẽ giúp bạn đạt điểm cao cho tiêu chí Task Achievement đấy. Hãy giữ vững phong độ nhé ! `
            }
            else{
                task_achievement1_2 = 1.5;
                task_achievement1_2_comment = `Bạn đang hơi lạm dụng thêm các thông tin có trong hình ảnh và đề bài. Việc sử dụng đầy đủ thông tin là tốt và sẽ tăng điểm cho bạn tiêu chí Task Achievment nhưng bạn nên chọn lọc cô đọng, chọn lọc những thông tin tiêu biểu và quan trọng nhất chứ đừng lạm dụng quá vì nó sẽ không chú trọng trong việc phân tích, so sánh các thông tin đó.`
            }
        }

        if(relation_point_essay_and_question < 0.9){
            if(point_for_intro_cheking_part_1_essay >= 0.9 && point_for_second_paragraph_cheking_part_1_essay < 0.9){
                task_achievement1_3 = 1;
                task_achievement1_3_comment += ` Hệ thống tự động nhận xét về bài của bạn như sau: Phần giới thiệu (Introduction - đoạn 1) của bạn đã đủ thông tin nhưng phần Overall- đoạn 2 của bạn đang chưa tốt. Câu Overall Writing Task 1 là câu quan trọng nhất của câu Writing Task 1, đó là một câu chứa tóm tắt ý tưởng tổng quát. Nói cho dễ hiểu, câu OVERALL chính là cái mà khi nhìn sơ qua biểu đồ, bảng biểu hoặc hình vẽ những đặc điểm nào mà mình thấy đầu tiên, nổi bật nhất chính là câu overall nhé. Tức là đập vào mắt mình những điểm nào (thường là 2 điểm) nổi bật nhất sẽ lấy đó làm câu overall `
            }
            else if(point_for_intro_cheking_part_1_essay < 0.9 && point_for_second_paragraph_cheking_part_1_essay <= 0.9){
                task_achievement1_3 = 1;
                task_achievement1_3_comment += ` Hệ thống tự động nhận xét về bài của bạn như sau: Phần Introduction - đoạn 1 của bạn viết chưa tốt, tuy vậy phần 2 (Overall của bạn viết ổn rồi nhé). Phần mở bài trong IELTS Writing Task 1 thường chỉ gói gọn trong một câu, nhằm mục đích giới thiệu về biểu đồ hoặc bảng số liệu được mô tả. Cách nhanh và đơn giản nhất để viết phần này là diễn đạt lại đề bài theo văn phong của chính bạn, sử dụng các từ đồng nghĩa. `
            }else{
                task_achievement1_3 = 0;
                task_achievement1_3_comment +=` Sau khi hệ thống check bài của bạn thì dường như bài viết của bạn không đề cập đến những gì đề bài yêu cầu, bài viết của bạn đang lạc đề hoàn toàn.<br> Phần mở bài trong IELTS Writing Task 1 thường chỉ gói gọn trong một câu, nhằm mục đích giới thiệu về biểu đồ hoặc bảng số liệu được mô tả. Cách nhanh và đơn giản nhất để viết phần này là diễn đạt lại đề bài theo văn phong của chính bạn, sử dụng các từ đồng nghĩa. Câu Overall Writing Task 1 là câu quan trọng nhất của câu Writing Task 1, đó là một câu chứa tóm tắt ý tưởng tổng quát. Nói cho dễ hiểu, câu OVERALL chính là cái mà khi nhìn sơ qua biểu đồ, bảng biểu hoặc hình vẽ những đặc điểm nào mà mình thấy đầu tiên, nổi bật nhất chính là câu overall nhé. Tức là đập vào mắt mình những điểm nào (thường là 2 điểm) nổi bật nhất sẽ lấy đó làm câu overall  `
            }
        }
        else{
            if(point_for_intro_cheking_part_1_essay > 0.95){
                task_achievement1_3 = 1;
                task_achievement1_3_comment +=`  Có vẻ như introduction - câu mở đầu của bạn viết không ổn rồi !. Thông thường introduction sẽ phải được paraphrase bằng cách sử dụng các từ đồng nghĩa hoặc sử dụng cấu trúc câu khác nhé. Câu mở đầu (introduction) của bạn có vẻ đã bao gồm ý chính của bài tuy nhiên chưa paraphrase dẫn đến việc không được đánh giá cao về khả năng sử dụng từ, sử dụng cấu trúc câu ! `
            }
            else{
                task_achievement1_3 = 2.5;
                task_achievement1_3_comment += `Câu mở đầu (Introduction) và câu tóm gọn (Overall) của bạn viết tốt rồi, đã bao gồm các ý chính/ quan trọng mà đề bài này yêu cầu. Đồng thời bạn cũng đã paraphrase lại và sử dụng hợp lý nó. Hãy tiếp tục phát huy điều này nhé ^^. ` 
            }
        }
    


        if ((position_introduction_task_1 < position_overall_task_1) && (position_overall_task_1 < position_body_task_1)){
            coherenceandcohesion1_1 = 2.5;
            coherenceandcohesion1_1_comment = ` Đoạn văn này đã được viết đúng theo trình tự introduction -> overall -> body rồi. Hãy nhớ viết đúng trình tự này ở mọi bài writing task 1 để phù hợp logic và tạo nên sự liên kết nhất nhé`

        }
        else{
            coherenceandcohesion1_1 = 1;
            coherenceandcohesion1_1_comment = ` Trình tự đoạn văn của bạn đang có vấn đề. 1 bài wiritng task 1 cần được trình bày theo trình tự introduction -> overall rồi đến body. <br> - Introduction nên là 1 câu ngắn gọn ở đoạn 1 bao quát cả bài làm (1 tip nhỏ cho bạn là bạn nên paraphrase lại đề bài)<br>- Overall (hay còn là overview) cần được trình bài ở đoạn số 2 và chúng ta cần nhìn vào “bức tranh tổng thể”, chứ đừng nhìn vào ” số liệu chi tiết”. Bạn cũng nên sử dụng 1 số cụm từ để bắt đầu đoạn overall như: Overall, it is obvious/apparent/clear that… (Nhìn chung, rõ ràng là…); It can easily be noticed/seen from the graph/table that… (Có thể dễ dàng nhận thấy/nhìn thấy từ biểu đồ, bảng rằng…); As is shown/illustrated by the graph…(Như được trình bày trong biểu đồ…)<br>- Body (Detail Information): Bạn có thể dùng đoạn 3 hoặc đoạn 4(nếu muốn chia body làm 2 đoạn) để viết phần này. Body sẽ bắt đầu phân tích chi tiết các số liệu, đưa ra so sánh các số liệu đó. Ở phần này, giám khảo sẽ đánh giá bạn qua các phân tích, sử dụng các thông tin hợp lý nhưng vẫn chi tiết và đầy đủ thông tin. `
        }

        if(point_for_intro_cheking_part_1_essay < 0.9){
            coherenceandcohesion1_2 = 0.5;
            coherenceandcohesion1_2_comment =`Phần introduction của bạn có vẻ chưa hợp lý (Lạc đề/ Chưa đủ thông tin cần thiết). Ngoài ra introduction nên là 1 câu và được pharaphrase lại từ đề bài. Ví dụ nhé:<br> Đề bài: Đề bài: “The line graph below shows the consumption of fish and different kinds of meat in a European country between 1979 and 2004”. <br> Introduction sẽ là: The line graph illustrates the consumption of fish and different kinds of meat in a European country between 1979 and 2004. ` 
        }
        else{
            if(point_for_intro_cheking_part_1_essay >= 0.9 && point_for_intro_cheking_part_1_essay <= 0.94){
                coherenceandcohesion1_2 = 1.25;
                coherenceandcohesion1_2_comment =`Phần introduction này đã ổn rồi nhé. Nó đã bao gồm các ý chính của bài và bạn cũng làm tốt trong việc paraphrase nó ! ` 
            }
            else{
                coherenceandcohesion1_2 = 0.5;
                coherenceandcohesion1_2_comment = ` Ôi bạn ơi ! Có vẻ bạn chưa paraphrase lại đề bài hoặc bạn chỉ paraphrase mỗi 1 từ/cụm từ nhỏ. Bạn nên thử thay đổi cấu trúc câu xem. Ví dụ nhé:<br> Đề bài: Đề bài: “The line graph below shows the consumption of fish and different kinds of meat in a European country between 1979 and 2004”. <br> Introduction sẽ là: The line graph illustrates the consumption of fish and different kinds of meat in a European country between 1979 and 2004.`
            }
        }

        if (point_for_second_paragraph_cheking_part_1_essay < 0.9){
            coherenceandcohesion1_2 += 0.25;
            coherenceandcohesion1_2_comment += `<br><br> - Tiếp đến phần overall(Thông thường ở đoạn 2): Bạn chưa làm tốt điều đó. Overall của bạn chưa chỉ ra những điểm đặc biệt / quan trọng của yêu cầu. Overall nên viết ở đoạn 2 và nên viết trong khoảng 2 - 3 câu thể hiện quan điểm của bạn về key informaion. main trends (Những thay đổi rõ rệt nhất). Ta xét ví dụ sau: <br> <p style ="font-style: italic">Overall, petrol and oil are the primary power sources in this country throughout the period shown, while the least used power sources are nuclear and renewable energy. It is also noticeable that the consumption of petrol and oil and coal experiences the greatest increases over the period given.</p><br> Như bạn thấy ở mẫu overview này, nó đã chỉ rõ cao nhất/thấp nhất hay dao động đáng chú ý nhất ("petrol and oil are the primary power sources in this country throughout the period shown, while the least used power sources are nuclear and renewable energy." - Petrol and oil cao nhất, Nuclear, solar/wind và hydropower là thấp nhất.) và xu hướng chung của biểu đồ ("It is also noticeable that the consumption of petrol and oil and coal experiences the greatest increases over the period given." - Petrol and oi và coal là những đường nổi bật nhất vì nó có sự tăng trưởng mạnh nhất.) `
        }
        else{
            coherenceandcohesion1_2 += 1.25;
            coherenceandcohesion1_2_comment +=`<br><br> - Tiếp đến phần overall(Thông thường ở đoạn 2): Phần này bạn viết ổn rồi ! Đã bao gồm các main trend/ key information và cũng đã paraphrase. Làm tốt nha ! ` 

        }

        if(total_linking_word_count == 0){ //xem lại: linking words:   https://yourielts.net/prepare-for-ielts/ielts-writing/academic-task-1/ielts-writing-task-1-linking-words-for-describing-a-graph
            coherenceandcohesion1_3 = 0;
            coherenceandcohesion1_3_comment = `Bạn không sử dụng bất cứ linking words (Từ nối) nào cả. Việc này sẽ làm mất tính liên kết, liên quan giữa các câu, các đoạn với nhau và ảnh hưởng đến tiêu chí cohesion and coherence. Một số linking words phổ biến cần phải được sử dụng như: and, also, but,...`;
        }
        else{
            //if(total_linking_word_count > 0 && total_linking_word_count < 6){
             if(total_linking_word_count > 0){
                coherenceandcohesion1_3 = 0.5;
                coherenceandcohesion1_3_comment = `Các linking words đã được bạn sử dụng trong bài viết là: ${linking_word_to_accumulate}. Bạn đang sử dụng quá ít linking words, hãy sử dụng nhiều hơn để tăng tính liên kết cho tiêu chí cohestion and coherence. Nhớ là nên sử dụng nhiều và phân bổ đều ở các đoạn nha. Bạn có thể tham khảo các từ nối sau: <br> Dùng để liên kết các câu/ thêm thông tin : furthermore, additionally, in addition to, also, moreover, and, as well as,... <br> Để thêm thời gian: during, while, until, before, afterward, in the end, at the same time, meanwhile, subsequently, simultaneously,...<br> Để liệt kê: firstly, secondly, thirdly, fourthly, lastly,... <br> Dùng để cung cấp ví dụ: for instance, for example, to cite an example, to illustrate, namely <br> Dùng để nhấn mạnh: obviously, particularly, in particular, especially, specifically, clearly,... <br> Dùng để chỉ hậu quả: as a result, therefore, thus, consequently, for this reason, so, hence,...` 
            }
            

            if(unique_linking_word_count <= 2){ //check
                coherenceandcohesion1_3 += 0.5
                coherenceandcohesion1_3_comment += ` Các linking words đã được bạn sử dụng trong bài viết là: ${linking_word_to_accumulate}. Bạn có sử dụng linking words để nối các câu lại với nhau. Tuy nhiên các từ nối ấy vẫn không đa dạng. Bạn nên sử dụng ít nhất 6 từ nối trong bài và ít nhất 3 từ nối trong đó là có sự khác biệt. Bạn có thể tham khảo các từ nối sau: <br> Dùng để liên kết các câu/ thêm thông tin : furthermore, additionally, in addition to, also, moreover, and, as well as,... <br> Để thêm thời gian: during, while, until, before, afterward, in the end, at the same time, meanwhile, subsequently, simultaneously,...<br> Để liệt kê: firstly, secondly, thirdly, fourthly, lastly,... <br> Dùng để cung cấp ví dụ: for instance, for example, to cite an example, to illustrate, namely <br> Dùng để nhấn mạnh: obviously, particularly, in particular, especially, specifically, clearly,... <br> Dùng để chỉ hậu quả: as a result, therefore, thus, consequently, for this reason, so, hence,...`
            }
            else { //check
                coherenceandcohesion1_3 = 1;
                coherenceandcohesion1_3_comment = `Các linking words đã được bạn sử dụng trong bài viết là: ${linking_word_to_accumulate}. Số lượng linking words cho task 1 như này là đã ổn và có vẻ đầy đủ, phòng phú nha. Bạn có thể bổ sung thêm kiến thúc về 1 số từ nối như additionally, in addition to, also, moreover, and, as well as, ` 
            }
        }
        
        if(increase_word_count > 1){
            if(unique_increase_word_count <= 2){
                lexical_resource1_1 = 1;
                lexical_resource1_1_comment = `Một số động từ để chỉ sự tăng trường đã được bạn sử dụng như " ${increase_word_array} " Tuy vậy, bạn cũng nên sử dụng thêm các động từ để chỉ sự tăng trưởng khác như grow, increase, rise, climb, go up,... để thể hiện khả năng linh hoạt trong cách sử dụng từ vựng của bạn` 
            }
            else{
                lexical_resource1_1 = 1.5
                lexical_resource1_1_comment = `Bạn đã sử dụng các động từ/ cụm từ thể hiện xu hướng tăng trưởng khi so sánh giữa nhiều đối tượng khác nhau như " ${increase_word_array}" và những cụm từ đó cũng linh hoạt trong cách sử dụng`
            }
        }else{
            lexical_resource1_1 = 0.5;
            lexical_resource1_1_comment = `Trong bài viết, bạn chưa sử dụng bất cứ từ ngữ/ động từ nào để thể hiện sự tăng trưởng. Do vậy bạn không làm rõ được quan điểm của mình khi so sánh các đối tượng trong bài. Hãy sử dụng các cụm từ chỉ sự tăng trưởng như increase, soar, rise, grow up,... nhiều hơn nhé`
        }


        if(decrease_word_count > 1){
            if(unique_decrease_word_count <= 2){
                lexical_resource1_1 += 1;
                lexical_resource1_1_comment += ` Tương tự vậy ta xét đến các động từ/ cụm từ chỉ sự suy giảm. Trong bài viết bạn đã sử dụng " ${decrease_word_array}" , tuy vậy vẫn còn hạn chế trong cách sử dụng. Bạn nên sử dụng thêm các động từ để chỉ sự giảm như downturn, drop, collapse, decline, decrease, fall, reduce,... ` 
            }
            else{
                lexical_resource1_1 += 1.5
                lexical_resource1_1_comment += `Tương tự vậy ta xét đến các động từ/ cụm từ chỉ sự suy giảm. Bạn đã sử dụng các động từ/ cụm từ thể hiện xu hướng giảm khi so sánh giữa nhiều đối tượng khác nhau như " ${decrease_word_array} "và những cụm từ đó cũng linh hoạt trong cách sử dụng`
            }
        }else{
            lexical_resource1_1 += 0.5;
            lexical_resource1_1_comment += `Tương tự vậy ta xét đến các động từ/ cụm từ chỉ sự suy giảm. Bạn chưa sử dụng bất cứ từ ngữ/ động từ nào để thể hiện sự giảm sút. Do vậy bạn không làm rõ được quan điểm của mình khi so sánh các đối tượng trong bài. Hãy sử dụng các cụm từ chỉ sự giảm như downturn, drop, collapse, decline, decrease, fall, reduce,..`
        }
        
        if(unchange_word_count > 0 && unchange_word_count <= 5){
            lexical_resource1_1 += 1;
            if(unique_unchange_word_count < 2){
                lexical_resource1_1_comment += ` Bạn nên sử dụng thêm các động từ hoặc các cụm từ để chỉ sự không giảm (không đổi) - xu hướng ổn định khác như: a leveling off, show stability, plateau, stability, keep constant, stabilize,... thay vì chỉ sử dụng mỗi ${unchange_word_array}`
            }
            else{
                lexical_resource1_1 += 1.5
                lexical_resource1_1_comment += ` Bạn cũng đã sử dụng thêm các động từ/ cụm từ diễn tả xu hướng không đổi, giữ nguyên và cũng sử dụng chúng 1 cách linh hoạt, không trùng lặp như ${unchange_word_array}`
            }
        }
        else{
            lexical_resource1_1+= 0.75;
            lexical_resource1_1_comment += `Không nên sử dụng quá nhiều cụm từ so sánh vì nó sẽ làm sao nhãng việc phân tích. Cụ thể ở bài viết này, khi mô tả xu hướng ổn định, không thay đổi, bạn đã sử dụng ${unchange_word_count} cụm/động từ: ${unchange_word_array}. Hãy hạn chế nó nhé, nên nhớ bạn chỉ có 20 phút để viết task 1 và bạn nên giới hạn trong 190 từ !` 
        }



        if (goodVerb_word_count > 0 ){
            lexical_resource1_1 += 0.5;
            lexical_resource1_1_comment += `<br> - ${goodVerb_word_array} là những động từ hay, nên sử dụng để diễn tả sự thay đổi về số liệu/ xu hướng của các đối tượng so sánh. Và bạn đã thể hiện trong bài viết, đây sẽ là 1 điểm cộng cho bạn!`
        }
        else{
            lexical_resource1_1 += 0;
        }

        if(well_adjective_and_adverb_word_count  == 1){
            lexical_resource1_1 += 0.5;
            lexical_resource1_1_comment += `<br> - ${well_adjective_and_adverb_word_array} là tính từ/ trạng từ cũng khá hay nếu bạn muốn nhấn mạnh xu hướng tăng/giảm/giữ nguyên và nó cũng khiến động từ trước nó tăng thêm 1 cấp độ. Ví dụ: increase drammatically (tăng mạnh),... Tuy nhiên bạn đang sử dụng hơi ít nó, hãy thêm tầm 2,3 trạng từ/ tính từ nữa để nhấn mạnh xu hướng nhé. Bạn có thể tham khảo 1 số trạng từ/ tính từ sau: <br>Tính từ: <br> - Chỉ tốc độ nhanh: dramatic, tremendous, significant, rapid, sharp, suddent, steep, substantial, remarkable, notable, swift,... <br> - Chỉ mức độ trung bình: noticeable, marked, moderate, marked, moderate, steady, gradual, consistent, constant,... <br>- Chỉ mức độ chậm: minimal, slight, slow, marginal <br><b>Trạng từ: </b> <br> - Chỉ mức độ nhanh: dramatically, tremendously, significantly, rapidly, sharply, suddenly, steeply, substantially, remarkably, notably, swiftly, quickly <br> - Chỉ mức độ trung bình: noticeably, markedly, moderately, steadily, gradually, consistently, constantly<br> - Chỉ mức độ chậm: minimally, slightly, slowly, marginally`
            if(well_adjective_and_adverb_word_count >= 2 && well_adjective_and_adverb_word_count <= 6){
                lexical_resource1_1 += 1;
                lexical_resource1_1_comment += `<br> -Trong bài viết, chúng tôi nhận thấy có sử dụng ${well_adjective_and_adverb_word_array} (là tính từ/ trạng từ cũng khá hay nếu bạn muốn nhấn mạnh xu hướng tăng/giảm/giữ nguyên) và có vẻ cũng được sử dụng nhuần nhuẫn, linh hoạt rồi đó. Good job !. Bạn có thể tham khảo thêm 1 số tính từ/ trạng từ kiểu này nữa như <br>Tính từ: <br> - Chỉ tốc độ nhanh: dramatic, tremendous, significant, rapid, sharp, suddent, steep, substantial, remarkable, notable, swift,... <br> - Chỉ mức độ trung bình: noticeable, marked, moderate, marked, moderate, steady, gradual, consistent, constant,... <br>- Chỉ mức độ chậm: minimal, slight, slow, marginal <br>Trạng từ: <br> - Chỉ mức độ nhanh: dramatically, tremendously, significantly, rapidly, sharply, suddenly, steeply, substantially, remarkably, notably, swiftly, quickly <br> - Chỉ mức độ trung bình: noticeably, markedly, moderately, steadily, gradually, consistently, constantly<br> - Chỉ mức độ chậm: minimally, slightly, slowly, marginally`
            }
        }
        else{
            lexical_resource1_1 += 0.5;
            lexical_resource1_1_comment += `<br> - Nếu bạn muốn tăng band điểm writing, bạn có thể làm writing task 1 của bạn trở nên hay hơn bằng các từ vựng miêu tả tốc độ thay đổi. Ví dụ: increase slightly (tăng nhẹ), decrease significantly (giảm sâu),...Bạn có thể tham khảo thêm 1 số tính từ/ trạng từ kiểu này nữa như <br>Tính từ: <br> - Chỉ tốc độ nhanh: dramatic, tremendous, significant, rapid, sharp, suddent, steep, substantial, remarkable, notable, swift,... <br> - Chỉ mức độ trung bình: noticeable, marked, moderate, marked, moderate, steady, gradual, consistent, constant,... <br>- Chỉ mức độ chậm: minimal, slight, slow, marginal <br>Trạng từ: <br> - Chỉ mức độ nhanh: dramatically, tremendously, significantly, rapidly, sharply, suddenly, steeply, substantially, remarkably, notably, swiftly, quickly <br> - Chỉ mức độ trung bình: noticeably, markedly, moderately, steadily, gradually, consistently, constantly<br> - Chỉ mức độ chậm: minimally, slightly, slowly, marginally ` 
        }


        if(spelling_grammar_error_count == 0){
            lexical_resource1_2 = 3;
            lexical_resource1_2_comment  = ` Hệ thống nhận thấy bạn không có bất kì lỗi chính tả nào. Bạn làm tốt lắm, hãy cố gắng luôn dành 3-5 phút cuối để kiểm tra lại lỗi sai ngữ pháp, chính tả để tránh đánh mất điểm số không đáng nhé !<br> Ngoài ra bạn cũng phải chú ý để thì của bài viết xem ở hiện tại/ quá khứ hay tương lai.`

        }
        else if(spelling_grammar_error_count == 1){
            lexical_resource1_2 = 2.5;
            lexical_resource1_2_comment  = `Trong bài viết bạn đã có ${spelling_grammar_error_count} lỗi ngữ pháp, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }
        else if(spelling_grammar_error_count >= 2 && spelling_grammar_error_count <= 3){
            lexical_resource1_2 = 2;
            lexical_resource1_2_comment  = ` Trong bài viết bạn đã có ${spelling_grammar_error_count} lỗi ngữ pháp, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }
        else{
            lexical_resource1_2 = 1;
            lexical_resource1_2_comment  = ` Trong bài viết bạn đã có ${spelling_grammar_error_count} lỗi ngữ pháp, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }
        
        if (simple_sentences_count >= 2 && complex_sentences_count >= 2 && compound_sentences_count >= 2){
            grammatical_range_and_accuracy1_1 = 3.5;
            grammatical_range_and_accuracy1_1_comment = `Bạn đã sử dụng tương đối ổn và linh hoạt các cấu trúc ngữ pháp. Cụ thể, có ${simple_sentences_count} câu đơn (${simple_sentences}), có ${compound_sentences_count} câu ghép ở các câu ${position_compound_sentences} và ${complex_sentences_count} câu phức (${complex_sentences})` 
        }
        
        else{
            if(simple_sentences_count < 2 && sentence_count > 4){
                grammatical_range_and_accuracy1_1 = 1.5;
                grammatical_range_and_accuracy1_1_comment += `Bạn đang sử dụng hơi ít câu đơn. Cụ thể, số câu đơn có trong bài là ${simple_sentences_count} ${simple_sentences}. Hãy lưu ý thêm những câu đơn (Cấu tạo gồm 1 mệnh đề : S + V) ` 
            }
            else if(compound_sentences_count < 2 && sentence_count > 4){
                grammatical_range_and_accuracy1_1 = 1;
                grammatical_range_and_accuracy1_1_comment += `Về câu ghép, số câu ghép có trong bài là ${compound_sentences_count} "${compound_sentences}". Bạn nên thêm ít nhất 2 câu ghép (Cấu trúc: Mệnh đề 1; trạng từ liên kết, mệnh đề 2) vào mỗi bài writing task 1. 1 tip cho bạn là dùng các liên từ (FANBOYS) hoặc dấu ;` 
            }
            else if(complex_sentences_count < 2 && sentence_count > 4){
                grammatical_range_and_accuracy1_1 = 1;
                grammatical_range_and_accuracy1_1_comment += `Số lượng câu phức có trong bài là ${complex_sentences_count} ${complex_sentences}. Để tăng tính liên kết cho tiêu chí Cohesion and Coherence (CC) và sự linh hoạt trong cách sử dụng ngữ pháp, bạn nên sử dụng nhiều câu phức (nên ít nhất là 2)(Câu phức là loại câu được tạo thành từ hai hay nhiều mệnh đề, trong đó phải có một mệnh đề độc lập (mệnh đề chính) và ít nhất một mệnh đề phụ thuộc (mệnh đề phụ)). <br> - Sử dụng liên từ chỉ nguyên nhân, kết quả như: as, since, before hoặc cấu trúc Because of/Due to/Owing to,... <br> - Sử dụng liên từ chỉ quan hệ nhượng bộ: Although/Though/Even though, Despite/In spite of,... <br> - Liên từ chỉ quan hệ tương phản: While/Whereas  (trong khi) <br> Sử dụng Liên từ chỉ mục đích: In order that/so that (để mà) , vv Đây là 1 số các liên từ dùng để liên kết tạo thành câu phức` 
            }
            else if (sentence_count <= 4){
                grammatical_range_and_accuracy1_1 = 0;
                grammatical_range_and_accuracy1_1_comment += `Số lượng câu có trong bài là quá ít. Cụ thể ${sentence_count} câu. Điều này sẽ ảnh hưởng đến điểm và tất cả tiêu chí đi kèm. Nếu như bạn đang sử dụng quá nhiều câu phức hoặc câu ghép dẫn đến không đủ số câu, hãy cố gắng sử dụng thêm câu đơn để khắc phục. Chúng tôi khuyên bạn nên viết trong khoảng 8 - 12 câu cho task 1 là vừa đẹp !` 
            }
            grammatical_range_and_accuracy1_1 = 1;
        }
        grammatical_range_and_accuracy1_1_comment += `<br> Ở phần Sentence Structure của tiêu chí Grammaticall Range and Accuracy, bạn nên bao gồm nhiều cấu trúc câu khác nhau như simple sentence (câu đơn), complex sentence (câu phức) và compound sentence (câu ghép). Và nên sử dụng nhiều hơn hoặc bằng 2 câu đối với mỗi loại, điều này sẽ khiến giám khảo đánh giá bài viết của bạn cao hơn trong kho tàng ngữ pháp của bạn, cũng như làm tăng tính liên kết của bài viết qua các từ nối của complex, compound sentences. Trong bài viết của bạn có ${simple_sentences_count} câu đơn, ${compound_sentences_count} câu ghép và ${complex_sentences_count} câu phức <br> Ví dụ về câu đơn: Copyright laws are necessary for society. (chỉ có 1 mệnh đề) <br> Ví dụ về câu ghép:  Copyright laws are necessary for society, as they provide rewards and protection to original artwork creators.(nhiều hơn 1 mệnh đề) <br> Ví dụ về câu ghép: Because they provide rewards and protection, copyright laws are necessary for society.(có một mệnh đề chính và một hoặc nhiều hơn một mệnh đề phụ thuộc) ` 

        if(spelling_grammar_error_count == 0){
            grammatical_range_and_accuracy1_2 = 4;
            grammatical_range_and_accuracy1_2_comment  = ` Hệ thống nhận thấy bạn không có bất kì lỗi ngữ pháp nào. Bạn làm tốt lắm, hãy cố gắng luôn dành 3-5 phút cuối để kiểm tra lại lỗi sai ngữ pháp, chính tả để tránh đánh mất điểm số không đáng nhé !<br> Bạn cũng nên lưu ý một số lỗi ngữ pháp cơ bản dễ sai như vị trí dấu câu, thì của bài viết.`

        }
        else if(spelling_grammar_error_count == 1){
            grammatical_range_and_accuracy1_2 = 2.75;
            grammatical_range_and_accuracy1_2_comment  = `Số lỗi ngữ pháp: ${spelling_grammar_error_count}, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }
        else if(spelling_grammar_error_count >= 2 && spelling_grammar_error_count <= 3){
            grammatical_range_and_accuracy1_2 = 2;
            grammatical_range_and_accuracy1_2_comment  = ` Số lỗi ngữ pháp: ${spelling_grammar_error_count} lỗi ngữ pháp, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }
        else{
            grammatical_range_and_accuracy1_2 = 1;
            grammatical_range_and_accuracy1_2_comment  = ` Số lỗi ngữ pháp: ${spelling_grammar_error_count} lỗi ngữ pháp, chính tả. Cụ thể ở đoạn ${spelling_grammar_error_essay} (đã được bôi đỏ trong phần bài làm). Hãy lưu ý điều này vì lỗi sai chính tả sẽ làm giảm số điểm bài làm của bạn`
        }

      //  sau này cần phân loại rõ cho các type và comment  của nó. Ví dụ coherenceandcohesion1_1_comment cụ thể cho dạng line thì overall nên viết gì...
        task_achievement_part_1 = task_achievement1_1 + task_achievement1_2 + task_achievement1_3;
        coherence_and_cohesion_part_1 = coherenceandcohesion1_1 + coherenceandcohesion1_2 + coherenceandcohesion1_3
        lexical_resource_part_1 = lexical_resource1_1 + lexical_resource1_2
        grammatical_range_and_accuracy_part_1 = grammatical_range_and_accuracy1_1 + grammatical_range_and_accuracy1_2;

    }
    
    
    else{
        task_achievement_part_1 = coherence_and_cohesion_part_1 = lexical_resource_part_1 = grammatical_range_and_accuracy_part_1 = 2;
        task_achievement1_1_comment = task_achievement1_2_comment = task_achievement1_3_comment = coherenceandcohesion1_1_comment = coherenceandcohesion1_2_comment = coherenceandcohesion1_3_comment = lexical_resource1_1_comment = lexical_resource1_2_comment = grammatical_range_and_accuracy1_1_comment = grammatical_range_and_accuracy1_2_comment = `Độ dài bài viết quá ngắn hoặc chưa viết. Hệ thống không chấm được bài này, hãy thử lại bằng bài viết khác. Đọc kĩ yêu cầu đề bài (Ít nhất 150 từ cho task 1 và 250 từ cho task 2)`
    }




    if (part == 2 && length_essay > 40){

        if (length_essay < 200){
            task_achievement2_1 = 0;
            task_achievement2_1_comment = `Độ dài bài viết của bạn quá ngắn. Đối với Task 2 độ dài tối thiểu là 250 từ. Bạn nên viết trong khoảng 270 - 290 từ. Việc viết thiếu sẽ ảnh hưởng rất nhiều đến kết quả của bạn và thông thường sẽ nhận được < 5.0 band cho task 2 `
        
        }
        else{
            if (length_essay >= 200 && length_essay < 250 ){
                task_achievement2_1 = 0.5;
                task_achievement2_1_comment = `Có vẻ như độ dài của bài viết bạn chưa đạt yêu cầu. Bạn nên cố gắng để độ dài Task 2 kéo dài lên > 250 từ để cải thiện điểm cho phần Task Achievement. Nếu bạn chưa đủ độ dài do vấn đề thời gian, hãy cải thiện bằng cách luyện tập nhiều, tập trung cao độ cho bài viết của mình. Đối với task 2 bạn nên dùng 40 phút để hoàn thiện (5 - 7 phút lên dàn ý, 25 - 30 phút viết bài và 5 phút dành cho việc soát lỗi). `
            }
            
            else if(length_essay >= 250 && length_essay < 260 ){
                task_achievement2_1 = 2;
                task_achievement2_1_comment = `Độ dài cho phần writing task 2 của bạn đã đạt yêu cầu. Tuy vậy, bạn nên mở rộng hơn 1 chút nữa và cố gắng viết trong khoảng 260 - 280 từ. Bạn vẫn được điểm cho phần này nhé !`;
            }
            else if(length_essay >= 260 && length_essay < 300){
                task_achievement2_1 = 2;
                task_achievement2_1_comment = `Tuyệt vời ! Độ dài bài viết của bạn cho task 2 là hoàn hảo rồi. Bạn sẽ đạt tối đa điểm cho phần độ dài trong bài thi lần này lẫn bài thi thật (real exam) khi bạn thi Ielts. Hãy tiếp tục phát huy nhé ^^. `
            }
            else if(length_essay >= 300 && length_essay < 320){
                task_achievement2_1 = 1;
                task_achievement2_1_comment = `Độ dài bài viết của bạn là quá dài. Việc này sẽ ảnh hưởng tới thời gian làm bài của bạn là rất lớn. Hãy giới hạn bài viết của bạn lại trong khoảng 260 - 280 từ để đạt điểm tối đa của phần length(độ dài)`
            }
            else if (length_essay >= 320){
                task_achievement2_1 = 1;
                task_achievement2_1_comment = `No comment -.- Sao bạn viết dài thế !? Bạn nên viết trong khoảng 260 - 280 từ thôi để đạt yêu cầu đề bài và cũng vừa thời gian làm bài. Bạn sẽ KHÔNG đạt điểm cho phần độ dài !`
            
            }

            if(paragraph_essay < 3){
                task_achievement2_1 += 0.25;
                task_achievement2_1_comment += `Ngoài ra, bạn nên chia bài làm của bạn thành 3 - 4 phần (đoạn). Đối với writing task 2, Phần 1: Introduction, phần 2: Supporting Paragraph 1: Thân bài 1, phần 3: Supporting Paragraph 2: Thân bài 2, phần 4: Conclusion: Kết luận <br> Phần thân bài thường bao gồm:<br> - Topic sentence: Câu chủ đề<br> - Explanation: Giải thích <br> - Example: Ví dụ cụ thể `
            }
            else if(paragraph_essay == 3 || paragraph_essay == 4){
                task_achievement2_1 += 1;
                task_achievement2_1_comment += `Ngoài ra, bạn chia bố cục bài làm hợp lý rồi (3 - 4 đoạn) để phù hợp logic, cấu trúc bài làm và phù hợp với các tiêu chí khác như Coherence and Cohesion`
            }
            else{
                task_achievement2_1 += 0.5;
                task_achievement2_1_comment += `Bạn đang chia bố cục không hợp lý cho lắm. Thông thường ở task 2, bạn nên chia làm 4 phần tương ứng Introduction, Supporting Paragraph 1,  Supporting Paragraph 2, Conclusion. Việc chia thành nhiều đoạn sẽ gây ra nhiễu thông tin và ảnh hưởng tính mạch lạc và gắn kết của bài làm `
            }
        }

        
    }
    else{
        task_achievement_part_2 = coherence_and_cohesion_part_2 = lexical_resource_part_2 = grammatical_range_and_accuracy_part_2 = 2;
        task_achievement2_1_comment = task_achievement2_2_comment = task_achievement2_3_comment = coherenceandcohesion2_1_comment = coherenceandcohesion2_2_comment = coherenceandcohesion2_3_comment = lexical_resource2_1_comment = lexical_resource2_2_comment = grammatical_range_and_accuracy2_1_comment = grammatical_range_and_accuracy2_2_comment = `Độ dài bài viết quá ngắn hoặc chưa viết. Hệ thống không chấm được bài này, hãy thử lại bằng bài viết khác. Đọc kĩ yêu cầu đề bài (Ít nhất 150 từ cho task 1 và 250 từ cho task 2)`
    }


    
    overallband = (task_achievement_part_1 + coherence_and_cohesion_part_1 + lexical_resource_part_1 + grammatical_range_and_accuracy_part_1)/4
    MarkDescription();


    breakDownEssay.innerHTML +=`<h3 style="color:red">B. Tổng kết điểm của bạn và nhận xét (Overall Band and Comment)</h3>
        <h4 style = "font-weight:bold" id = "task_achievement_score_task_${i+1}">${task_achievement_part_1}</h4>
        <p id = "task_achievement_comment_task_${i+1}">${task_achievement_comment}</p>
        <h4 style = "font-weight:bold"  id = "coherence_and_cohesion_score_task_${i+1}">${coherence_and_cohesion_part_1}</h4>
        <p id = "coherence_and_cohesion_comment_task_${i+1}" >${coherence_and_cohesion_comment}</p>


        <h4 style = "font-weight:bold"  id = "lexical_resource_score_task_${i+1}">${lexical_resource_part_1}</h4>
        <p id = "lexical_resource_comment_task_${i+1}">${lexical_resource_comment}</p>

        <h4 style = "font-weight:bold"  id = "grammatical_range_and_accuracy_score_task_${i+1}">${grammatical_range_and_accuracy_part_1}</h4>
        <p id ="grammatical_range_and_accuracy_comment_task_${i+1} ">${grammatical_range_and_accuracy_comment}</p>
 
        <h4 style = "font-weight:bold" id = "overall_band_score_task_${i+1}"> ${overallband}</h4>
        <p id = "user_level_task_${i+1}">${userLevel}</p>
        <p id = "description_user_essay_task_${i+1}">${band_description}</p>
        `;
    
    detailCretariaEssay.innerHTML += `
    <h3 style="color:red">C. Phân tích từng phần bài làm của bạn (Analysis your test)</h3>
        <h4>Task Achievement (TA) ${task_achievement_part_1}</h4>
        <h5 style="font-weight:bold">Completeness</h5>
            - ${task_achievement1_1_comment}<br>
            - ${task_achievement1_2_comment}<br>
        <h5 style="font-weight:bold">Accuracy</h5>
            - ${task_achievement1_3_comment}<br>
        
        <h4>Coherence and Cohesion (CC) ${coherence_and_cohesion_part_1}</h4>
        <h5 style="font-weight:bold">Logical Organization</h5>
            - ${coherenceandcohesion1_1_comment}<br>
        <h5 style="font-weight:bold">Paraphrasing</h5>
            - ${coherenceandcohesion1_2_comment}<br>
        <h5 style="font-weight:bold">Linking Words</h5>
            - ${coherenceandcohesion1_3_comment}<br>

        <h4>Lexical Resource (LR) ${lexical_resource_part_1}</h4>
        <h5 style="font-weight:bold">Vocabulary Range and Complexity</h5>
            - ${lexical_resource1_1_comment}<br>
            - ${lexical_resource1_2_comment}<br>
        
        <h4>Grammatical Range and Accuracy (GRA) ${grammatical_range_and_accuracy_part_1}</h4>
        <h5  style="font-weight:bold">Sentence Structure</h5>
            - ${grammatical_range_and_accuracy1_1_comment}<br>
        <h5 style="font-weight:bold">Grammar </h5>
            - ${grammatical_range_and_accuracy1_2_comment}<br>
    `;


    
    detailCretariaEssay.innerHTML += `
    <p>Một số tính năng mới như: ... đang chuẩn bị ra mắt</p>
    `




        // Determine part of the question
        let questionId = quizData.questions[i].id;


        let currentquestion = quizData.questions[i].question;// choose current question
        let lengthBonus = 0;
        let lengthBonusDetail = '';

        countlinkingword = 0;
        uniqueLinkingWordsCount=0;


        
       




    xValues.push(`Question ${i + 1}` );
     
    yValues.push(overallband);

        // Show the feedback and overallband in the overall band div

    /*    overallBandDiv.innerHTML = `<h2 style="color: red">Overall band: ${overallband} </h2>
        <p>     <b>Skill level:</b> ${userLevel}</p><p><b>Description: </b>${band_description}</p><p><b></b></p>`;
*/




        let band_score_expand = document.getElementById('band-score-expand');
        overallBandDiv.style.display = 'block';

        // Show the explanation
        explanation.style.display = 'block';
        


         overallBandsSummary += `Question ${i + 1} - Part ${part}: ${overallband} points<br>`;
         if (part == 1) {
            totalPart1 += overallband;
            countPart1++;
        } else if (part == 2) {
            totalPart2 += overallband;
            countPart2++;
        }

        let finalOverallScoreExpand =   (totalPart1+totalPart2*2)/(countPart1+countPart2*2);

        band_score_expand.innerText +=`Task ${part}: ${overallband}. `



    }

    userEssayTask1.innerHTML = document.getElementById(`userEssayCheck-${1}`).innerHTML;
    userEssayTask2.innerHTML = document.getElementById(`userEssayCheck-${2}`).innerHTML;

    userSummaryTask1.innerHTML = document.getElementById(`summarize-${0}`).innerHTML;

    userSummaryTask2.innerHTML = document.getElementById(`summarize-${1}`).innerHTML;

    userBreakdownTask1.innerHTML = document.getElementById(`breakdown-${0}`).innerHTML;
    userBreakdownTask2.innerHTML = document.getElementById(`breakdown-${1}`).innerHTML;
    userdetailsCommentTask1.innerHTML = document.getElementById(`detail-cretaria-${0}`).innerHTML;
    userdetailsCommentTask2.innerHTML = document.getElementById(`detail-cretaria-${1}`).innerHTML;

    displayQuestion(0);
        //let finalOverallScore =     ((totalPart1 / countPart1) + 2 * (totalPart2 / countPart2)) / 3;
        let finalOverallScore =   (totalPart1+totalPart2*2)/(countPart1+countPart2*2);

        xValues.push(`Final Score` );
        yValues.push(`${finalOverallScore}` );

       /* document.getElementById("overall-band-form").textContent = finalOverallScore;
*/
       let detailScoreDiv = document.getElementById('detail_score');
    detailScoreDiv.innerHTML = `<h3>Detail Score: </h3><p>${overallBandsSummary}</p><h3>Final Overall Score: ${finalOverallScore.toFixed(2)}</h3>`;
   


   
    let band_score = document.getElementById('band-score');

        band_score.innerText += `${finalOverallScore.toFixed(2)}`;
    


    draw_full_overall_band(xValues, yValues);
    showQuestion(currentQuestionIndex);
    
}
function displayQuestion(index) {
    const questions = document.getElementsByClassName("questions");
    
    // Hide all question result containers
    for (let i = 0; i < questions.length; i++) {
        document.getElementById(`userEssayCheckContainer${i+1}`).style.display = 'none';
    }

    // Display the result for the selected question
    document.getElementById(`userEssayCheckContainer${index+1}`).style.display = 'block';

    // Update the essay content for the selected question
    let questionContextArea = document.getElementById(`questionContextArea-${index}`).innerHTML;
    let userEssayTab2 = document.getElementById(`userEssayTab2-${index}`).innerHTML;
    //let sampleEssayContent = document.getElementById(`sample-essay-area-${index}`).innerHTML;

    document.getElementById("tab2-user-essay-container").innerHTML = `${questionContextArea}<br> ${userEssayTab2}`;
    //document.getElementById("sample-tab-container").innerHTML = `${sampleEssayContent}`;
}

async function ResultInput() {
    console.log("Result Input")
    

    
    // Copy the content to the form fields
   // var contentToCopy1 = document.getElementById("overall-band-form").textContent;
    var contentToCopy2 = document.getElementById("date-div").textContent;
    var contentToCopy3 = document.getElementById("user-essay-task-1").innerHTML;

    var contentToCopy4 = document.getElementById("user-essay-task-2").innerHTML;
    var contentToCopy5 = document.getElementById("summary-essay-task-1").innerHTML;
    var contentToCopy6 = document.getElementById("summary-essay-task-2").innerHTML;

    var contentToCopy7 = document.getElementById("breakdown-task-1").innerHTML;
    var contentToCopy8 = document.getElementById("breakdown-task-2").innerHTML;

   /* var contentToCopy9 = document.getElementById("details-comment-task-1").innerHTML;
    var contentToCopy10 = document.getElementById("details-comment-task-2").innerHTML;*/

    var contentToCopy11 = document.getElementById("band-score").innerHTML;
    var contentToCopy13 = document.getElementById("id_test").textContent;
    var contentToCopy14 = document.getElementById("title").textContent;
    var contentToCopy15 = document.getElementById("type_test").textContent;
    var contentToCopy16 = document.getElementById("time-result").textContent;
    var contentToCopy17 = document.getElementById("data-save-task-1").textContent;
    var contentToCopy18 = document.getElementById("data-save-task-2").textContent;

    /*    var contentToCopy3 = document.getElementById("time-result").textContent;
    var contentToCopy4 = document.getElementById("title").textContent;
    var contentToCopy5 = document.getElementById("id_category").textContent;
    var contentToCopy6 = document.getElementById("id_test").textContent;
    var contentToCopy7 = document.getElementById("correctanswerdiv").textContent;
    var contentToCopy8 = document.getElementById("useranswerdiv").textContent;*/


 //   document.getElementById("resulttest").value = contentToCopy1;
     document.getElementById("testname").value = contentToCopy14;

    document.getElementById("dateform").value = contentToCopy2;
    document.getElementById("idtest").value = contentToCopy13;
    document.getElementById("test_type").value = contentToCopy15;

    
    document.getElementById("task1userform").value = contentToCopy3;


    document.getElementById("task2userform").value = contentToCopy4;
    document.getElementById("task1summaryuserform").value = contentToCopy5;
    document.getElementById("task2summaryuserform").value = contentToCopy6;

    document.getElementById("task1breakdownform").value = contentToCopy7;
    document.getElementById("task2breakdownform").value = contentToCopy8;
    
    /*document.getElementById("task1detailscommentform").value = contentToCopy9;
    document.getElementById("task2detailscommentform").value = contentToCopy10;*/

    document.getElementById("timedotest").value = contentToCopy16;
    document.getElementById("datasaveformtask1").value = contentToCopy17;
    document.getElementById("datasaveformtask2").value = contentToCopy18;
    document.getElementById("testsavenumber").value = resultId;

    return new Promise((resolve) => {
        // Thêm event listener trước khi submit
        jQuery('#saveUserWritingTest').on('submit', function(e) {
            e.preventDefault(); // Ngăn submit mặc định
            
            jQuery.ajax({
                url: this.action,
                type: this.method,
                data: jQuery(this).serialize(),
                success: function() {
                    resolve();
                },
                error: function() {
                    resolve(); // Vẫn resolve dù có lỗi (nếu muốn)
                }
            });
        });
        
        // Kích hoạt submit
        jQuery('#saveUserWritingTest').submit();
    });

}
