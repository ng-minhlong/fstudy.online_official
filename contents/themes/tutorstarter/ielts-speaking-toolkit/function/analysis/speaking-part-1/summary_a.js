function ReviewPage(i) {
    // Count questions per part for STT
    const currentPart = quizData.questions[i].part;
    const currentIDs = quizData.questions[i].id;

    let partQuestionCount = 0;
    
    // Calculate STT by counting how many previous questions belong to the same part
    for (let j = 0; j <= i; j++) {
        if (quizData.questions[j].part === currentPart) {
            partQuestionCount++;
        }
    }
    
    let editAnsPage = document.getElementById('edit-ans-area');
    let answer = answers['answer' + (i + 1)] || "";
    let current_question = quizData.questions[i].question;
    let id_question = `q-p-${currentPart}-n-${i}`;

    let counter = counters['counter' + (i + 1)] || "";
    let reanswer = reanswers['reanswer' + (i + 1)] || "";
    let result = '';
    const link = answers['link_audio' + (i + 1)];

    let Timeused = counter;
    let wordCount = answer.split(/\s+/).length;
    let averageSpeakRate = wordCount / Timeused;

    let questionBlock = document.createElement('div');
    questionBlock.innerHTML = `
        <div id = '${id_question}'>
            <p style="color:red"><strong>Part ${currentPart} - Question ${partQuestionCount}:</strong> ${current_question}</p>
            <p><strong>Your Answer:</strong> <span id="answer${i + 1}"></span></p>
            <p><strong>Speak Rate:</strong> <span id="speakRate_${id_question}">${averageSpeakRate.toFixed(2)} words/sec</span></p>
            <p><strong>Time Spent:</strong> <span id="timeSpent_${id_question}">${Timeused} seconds</span></p>
            <p><strong>Word Count:</strong> <span id="wordCount_${id_question}">${wordCount} words</span></p>
            <strong>Audio:</strong> <audio controls src="${link}"></audio>

            <div id = "information-area" >
                <p><strong>STT:</strong> <span  id="stt_${id_question}">${partQuestionCount}</span></p>
                <p><strong>Part:</strong> <span  id="part_${id_question}">${currentPart}</span></p>
                <p><strong>ID Question:</strong> <span  id="ids_${id_question}">${currentIDs}</span></p>
                <p><strong>Audio Link:</strong> <span id="audioLink_${id_question}">${link}</span></p>
            </div>
        </div>
    `;
    editAnsPage.appendChild(questionBlock);

    let answerElement = document.getElementById(`answer${i + 1}`);
    let words = answer.split(' ');

    editCounts[`answer${i + 1}`] = 0; // Reset số lần sửa

    words.forEach((word, index) => {
        let wordElement = document.createElement('span');
        wordElement.textContent = word;
        wordElement.style.cursor = 'pointer';
        wordElement.style.padding = '2px';
        wordElement.style.margin = '2px';
        wordElement.style.borderBottom = '1px dashed blue';

        wordElement.addEventListener('click', () => handleEditWord(wordElement, i + 1));

        answerElement.appendChild(wordElement);
        answerElement.appendChild(document.createTextNode(' ')); // Khoảng trắng giữa từ
    });
}



function handleEditWord(wordElement, questionIndex) {
    let answerKey = `answer${questionIndex}`;

    if (editCounts[answerKey] >= 5) {
        alert("You can only edit up to 5 words per answer.");
        return;
    }

    // Hiện popup và đặt giá trị hiện tại vào input
    document.getElementById('word-edit-popup').style.display = 'block';
    let editInput = document.getElementById('edit-word-input');
    editInput.value = wordElement.textContent.trim();
    
    // Khi bấm "Save"
    document.getElementById('save-word-btn').onclick = function() {
        let newWord = editInput.value.trim();
        if (newWord && newWord !== wordElement.textContent.trim()) {
            wordElement.innerHTML = `<div class="origin_word" style = "display:none">${wordElement.textContent.trim()}</div>
                                     <div class="replace_word">${newWord}</div>`;
            editCounts[answerKey] += 1;
        }
        document.getElementById('word-edit-popup').style.display = 'none';
    };

    // Khi bấm "Cancel"
    document.getElementById('cancel-word-btn').onclick = function() {
        document.getElementById('word-edit-popup').style.display = 'none';
    };
}






document.getElementById('edit-ans-area').addEventListener('mouseover', (e) => {
    if (e.target.tagName === 'SPAN') {
    //    console.log("Mouse entered:", e.target.textContent);
        e.target.style.backgroundColor = '#ffff00';
    }
});
document.getElementById('edit-ans-area').addEventListener('mouseout', (e) => {
    if (e.target.tagName === 'SPAN') {
      //  console.log("Mouse left:", e.target.textContent);
        e.target.style.backgroundColor = '';
    }
});


document.getElementById('log-original-answer').addEventListener('click', () => {
    let originalAnswer = document.getElementById('edit-ans-area').innerHTML;
    console.log("Original Answer with Edits:", originalAnswer);
});

document.getElementById('log-edited-answer').addEventListener('click', async () => {
    let editedData = {
        total_answers: 0,
        detail: {}
    };

    let wordEdits = getWordEdits(); // Lấy dữ liệu chỉnh sửa từ log-edited-words

    document.querySelectorAll('div[id^="q-p-"]').forEach(questionBlock => {
        let id_question = questionBlock.id;
        let partMatch = id_question.match(/q-p-(\d+)-n-\d+/);

        if (partMatch) {
            let part = partMatch[1];
            let questionText = questionBlock.querySelector('p strong')?.nextSibling?.textContent.trim() || '';
            let wordCount = parseInt(document.getElementById(`wordCount_${id_question}`)?.textContent || '0', 10);
            let timeSpent = parseFloat(document.getElementById(`timeSpent_${id_question}`)?.textContent || '0');
            let averageSpeakRate = parseFloat(document.getElementById(`speakRate_${id_question}`)?.textContent || '0');
            let audioLink = document.getElementById(`audioLink_${id_question}`)?.textContent || 'null';
            let stt = document.getElementById(`stt_${id_question}`)?.textContent || 'null';
            let partID = document.getElementById(`part_${id_question}`)?.textContent || 'null';
            let ids = document.getElementById(`ids_${id_question}`)?.textContent || 'null';







            let answerSpans = questionBlock.querySelectorAll(`[id^="answer"] span`);
            let finalAnswer = Array.from(answerSpans).map(span => {
                let replacedWord = span.querySelector('.replace_word');
                return replacedWord ? replacedWord.textContent.trim() : span.textContent.trim();
            }).join(' ');

            // Lấy dữ liệu chỉnh sửa từ log-edited-words
            let editData = wordEdits[id_question] || { number_edit: 0, words: [] };

            if (!editedData.detail[part]) {
                editedData.detail[part] = [];
            }

            editedData.detail[part].push({
                id_question: id_question,
                question: questionText,
                answer: finalAnswer,
                audio: audioLink,
                stt: stt,
                part: partID,
                id: ids,
                length: wordCount,
                avarage_speak: averageSpeakRate,
                time_spent: timeSpent,
                edit_word: editData
            });

            editedData.total_answers++;
        }
    });

    console.log(JSON.stringify(editedData, null, 4));

    // Gọi hàm MarkTest để fetch API theo từng part
    await MarkTest(editedData);
});

 
// Hàm lấy dữ liệu từ log-edited-words
function getWordEdits() {
    let wordEdits = {};

    document.querySelectorAll('.replace_word').forEach(replaceWord => {
        let originWord = replaceWord.previousElementSibling.textContent.trim();
        let editedWord = replaceWord.textContent.trim();
        let parentDiv = replaceWord.closest('div[id^="q-p-"]');

        if (parentDiv) {
            let id_question = parentDiv.id;
            if (!wordEdits[id_question]) {
                wordEdits[id_question] = { number_edit: 0, words: [] };
            }

            wordEdits[id_question].number_edit++;
            wordEdits[id_question].words.push({
                origin: originWord,
                replace: editedWord
            });
        }
    });

    return wordEdits;
}

async function MarkTest(editedData) {
    const results = {};
    const allDataResponses = {};
    let finalResult = null;

    // Show loading popup (not blocking)
    Swal.fire({
        title: 'Đang chấm bài',
        html: 'Bắt đầu quá trình chấm bài IELTS Speaking...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    for (let part in editedData.detail) {
        try {
            // Show loading for each part (non-blocking)
            Swal.fire({
                title: 'Đang chấm bài',
                html: `Đang chấm phần <strong>${part}</strong>...`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log(`Start mark part ${part}...`);
            console.log(JSON.stringify({ [part]: editedData.detail[part] }, null, 4));

            let response = await fetch(`${siteUrl}/api/public/test/v1/ielts/speaking/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ part: part, data: editedData.detail[part] })
            });

            let dataResponse = await response.json();
            console.log(`Part ${part} response:`, dataResponse);
            console.log(`Successfully mark for ${part}...`);

            await Swal.fire({
                title: 'Thành công!',
                html: `Đã hoàn thành chấm phần <strong>${part}</strong>!`,
                icon: 'success',
                timer: 2000,
                timerProgressBar: true
            });

            results[part] = dataResponse;
            allDataResponses[part] = dataResponse;

        } catch (error) {
            console.error(`Error sending part ${part}:`, error);
            await Swal.fire({
                title: 'Lỗi!',
                html: `Có lỗi xảy ra khi chấm phần <strong>${part}</strong>!`,
                icon: 'error'
            });
        }
    }

    // Tổng hợp kết quả
    Swal.fire({
        title: 'Đang tổng hợp kết quả',
        html: 'Bắt đầu tổng hợp kết quả từ các phần...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let responsefinal = await fetch(`${siteUrl}/api/public/test/v1/ielts/final_speaking/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ results: results })
        });
        
        console.log("Status:", responsefinal.status);
        
        if (!responsefinal.ok) {
            throw new Error(`Server returned ${responsefinal.status}`);
        }

        

        finalResult = await responsefinal.json();
        console.log('Final result:', finalResult);

        Swal.update({
            title: 'Hoàn thành!',
            html: 'Đã chấm điểm xong toàn bộ bài thi!',
            icon: 'success',
            allowOutsideClick: false,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error getting final result:', error);
        Swal.update({
            title: 'Lỗi!',
            html: 'Có lỗi xảy ra khi tổng hợp kết quả cuối cùng!',
            icon: 'error',
            allowOutsideClick: false,
            showConfirmButton: false
        });
    }

    if (finalResult) {
        document.getElementById("band_detail").value = JSON.stringify(finalResult, null, 2);
        if (finalResult.bands && finalResult.bands.overallBand !== undefined) {
            document.getElementById("resulttest").value = finalResult.bands.overallBand;
        }
    }

    document.getElementById("user_answer_and_comment").value = JSON.stringify(allDataResponses, null, 2);

    ResultInput();



    const resultUrl = `${siteUrl}/ielts/s/result/${resultId}`;

    await Swal.fire({
        title: 'Kết quả đã sẵn sàng!',
        html: `Bạn có thể xem kết quả tại: <a href="${resultUrl}" target="_blank">${resultUrl}</a>`,
        allowOutsideClick: false,
        showConfirmButton: false,
        icon: 'info',
    });

//    document.getElementById("result-full-page").style.display = "block";

    
}

document.getElementById('log-edited-words').addEventListener('click', () => {
    let editedData = {
        total_edit: 0,
        detail: {}
    };
    
    document.querySelectorAll('.replace_word').forEach(replaceWord => {
        let originWord = replaceWord.previousElementSibling.textContent.trim();
        let editedWord = replaceWord.textContent.trim();
        let parentDiv = replaceWord.closest('div[id^="q-p-"]');
        
        if (parentDiv) {
            let id_question = parentDiv.id;
            let partMatch = id_question.match(/q-p-(\d+)-n-\d+/);
            if (partMatch) {
                let part = partMatch[1];
                
                if (!editedData.detail[part]) {
                    editedData.detail[part] = [];
                }
                
                editedData.detail[part].push({
                    id_question: id_question,
                    origin: originWord,
                    edited: editedWord
                });
                
                editedData.total_edit++;
            }
        }
    });
    
    console.log(JSON.stringify(editedData, null, 4));
});




async function GetSummaryPart1(i) {
    console.log(`Số câu hỏi: ${number_of_question}, Số câu part 1 ${part1Count}, Số câu part 2: ${part2Count}, Số câu part 3: ${part3Count}`)
    console.log("Start Summary - Show Result");
    document.getElementById("speaking-part").style.display = "none";

    let resultColumn = document.getElementById('resultColumn');
    let DataSaveTask = document.getElementById(`data-save-full-speaking`);

    if (!DataSaveTask.innerHTML.trim()) {
        DataSaveTask.innerHTML = "[";
    }
    
    let answer = answers['answer' + (i + 1)] || "";
    let current_question = quizData.questions[i].question;
    let counter = counters['counter' + (i + 1)] || "";

    let reanswer = reanswers['reanswer' + (i + 1)] || "";
    let result = '';
    const link = answers['link_audio' + (i + 1)];

    let Timeused = counter;
    let wordCount = answer.split(/\s+/).length;
    let averageSpeakRate = wordCount / Timeused;
    
    let lexical_resource_comment_part1 =``;
    let grammatical_range_and_accuracy_comment_part1 = ``;
    let fluency_and_coherence_comment_part1 = ``;
    let pronunciaton_comment_part1 = ``;

    let lexical_resource_comment_part2 =``;
    let grammatical_range_and_accuracy_comment_part2 = ``;
    let fluency_and_coherence_comment_part2 = ``;
    let pronunciaton_comment_part2 = ``;

    let lexical_resource_comment_part3 =``;
    let grammatical_range_and_accuracy_comment_part3 = ``;
    let fluency_and_coherence_comment_part3 = ``;
    let pronunciaton_comment_part3 = ``;


    // Initialize highlightedAnswer outside the try block
    let highlightedAnswer = answer;

    try {
        const response = await fetch('https://fstudy-speaking-ielts-check.onrender.com/check-speaking', {
       // const response = await fetch('http://localhost:3000/check-speaking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ question: current_question, link_audio: link, answer: answer, part: quizData.questions[i].part,sample: quizData.questions[i].sample, averageSpeakRate:averageSpeakRate, 
                time:Timeused, wordCount:  wordCount, number_of_question:number_of_question, number_of_part_1: part1Count, number_of_part_2: part2Count, number_of_part_3: part3Count, stt: quizData.questions[i].stt, id_question: quizData.questions[i].id }),
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        /*console.log('Result:', data.result);
        console.log('Synonym Groups:', data.synonym_groups);
        console.log('Grammar Result:', data.grammar_result);
        console.log('Grammar Result GPT V2:', data.grammar_result_gpt);*/
        console.log(data)
        
        DataSaveTask.innerHTML += `<pre>${JSON.stringify(data, null, 2)}</pre><br`;
        DataSaveTask.innerHTML += `,<br>`;

        if(data.part == "1"){
                fluency_and_coherence_comment_part1 = data.checkFluencyAndCoherenceSend.checkFluencyAndCoherenceComment;
                lexical_resource_comment_part1 = data.checkLexicalResourceSend.checkLexicalResourceComment;
                fluency_and_coherence_all_point_part1 += data.checkFluencyAndCoherenceSend.checkFluencyAndCoherencePoint;
                lexical_resource_all_point_part1 += data.checkLexicalResourceSend.checkLexicalResourcePoint;
                grammatical_range_and_accuracy_comment_part1 = data.checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyComment;
                grammatical_range_and_accuracy_all_point_part1 += data. checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyPoint;
        }



else if(data.part == '2'){
        fluency_and_coherence_comment_part2 = data.checkFluencyAndCoherenceSend.checkFluencyAndCoherenceComment;
        lexical_resource_comment_part2 = data.checkLexicalResourceSend.checkLexicalResourceComment;
        grammatical_range_and_accuracy_comment_part2 = data.checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyComment;
        fluency_and_coherence_all_point_part2 += data.checkFluencyAndCoherenceSend.checkFluencyAndCoherencePoint;
        lexical_resource_all_point_part2 += data.checkLexicalResourceSend.checkLexicalResourcePoint;
        grammatical_range_and_accuracy_all_point_part2 += data. checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyPoint;
}
else{

        fluency_and_coherence_comment_part3 = data.checkFluencyAndCoherenceSend.checkFluencyAndCoherenceComment;
        lexical_resource_comment_part3 = data.checkLexicalResourceSend.checkLexicalResourceComment;
        grammatical_range_and_accuracy_comment_part3 = data.checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyComment;
        fluency_and_coherence_all_point_part3 += data.checkFluencyAndCoherenceSend.checkFluencyAndCoherencePoint;
        lexical_resource_all_point_part3 += data.checkLexicalResourceSend.checkLexicalResourcePoint;
        grammatical_range_and_accuracy_all_point_part3 += data. checkGrammarticalRangeAndAccuracySend.checkGrammarticalRangeAndAccuracyPoint;
}
        //pronunciation_all_point += 

        /*if (data.checkGrammarSpelling && data.checkGrammarSpelling.suggestions) {
            // Iterate through each suggestion in the suggestions array
            data.checkGrammarSpelling.suggestions.forEach(suggestion => {
            console.log('Message:', suggestion.message); // Display message
            console.log('Wrong Word:', suggestion.wrongWord); // Display wrong word
        
            if (suggestion.replacements && suggestion.replacements.length > 0) {
                console.log('Replacements:', suggestion.replacements); // Display replacements if any
            } else {
                console.log('No replacements available for this suggestion.');
            }
            });
        } else {
            console.log('Suggestions not found in data.');
        }*/
        // Highlight grammar errors in red
        data.checkGrammarSpelling.suggestions.forEach(error => {
            highlightedAnswer = highlightedAnswer.replace(
                error.wrongWord,
                `<span style="color:red">${error.wrongWord}</span>`
            );
        });

      /*  data.grammar_result.grammar_errors.forEach(error => {
            highlightedAnswer = highlightedAnswer.replace(
                error.error,
                `<span style="color:red">${error.error}</span>`
            );
        });*/

        // Highlight synonyms in blue
       /* data.synonym_groups.forEach(group => {
            group.forEach(word => {
                const regex = new RegExp(`\\b${word}\\b`, 'gi');
                highlightedAnswer = highlightedAnswer.replace(
                    regex,
                    `<span style="color:blue">${word}</span>`
                );
            });
        });*/
        getOverallBand();
        addSampletoTab();

    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
    if (i === quizData.questions.length - 1) {
        // Remove the last comma
        DataSaveTask.innerHTML = DataSaveTask.innerHTML.replace(/,<br>$/, '');
        DataSaveTask.innerHTML += "]";
    }

    
    let userAnswerAndComment = document.getElementById('userAnswerAndComment');
  


 
       

    resultColumn.innerHTML += `<p style = "color:red"><strong>Question ${i + 1}  (Part: ${quizData.questions[i].part}):</strong> ${current_question}</p>`;
    resultColumn.innerHTML += `<p><strong>Your Answer:</strong> ${highlightedAnswer}</p>`;

    if(part == 1){
        resultColumn.innerHTML += `<p><strong>Fluency and Coherence:</strong> ${fluency_and_coherence_comment_part1} </p>`;
        resultColumn.innerHTML += `<p><strong>Lexical Resource:</strong> ${lexical_resource_comment_part1}  </p>`;
        resultColumn.innerHTML += `<p><strong>Grammatical range and accuracy:</strong> ${grammatical_range_and_accuracy_comment_part1}</p>`;
        resultColumn.innerHTML += `<p><strong>Pronounciation: </strong> ${pronunciaton_comment_part1} </p>`;
    }
    else if(part == 2){
        resultColumn.innerHTML += `<p><strong>Fluency and Coherence (part 2):</strong> ${fluency_and_coherence_comment_part2} </p>`;
        resultColumn.innerHTML += `<p><strong>Lexical Resource (part 2):</strong> ${lexical_resource_comment_part2}  </p>`;
        resultColumn.innerHTML += `<p><strong>Grammatical range and accuracy (part 2):</strong> ${grammatical_range_and_accuracy_comment_part2}</p>`;
        resultColumn.innerHTML += `<p><strong>Pronounciation (part 2): </strong> ${pronunciaton_comment_part2} </p>`;
    }
    else if(part == 3){
        resultColumn.innerHTML += `<p><strong>Fluency and Coherence (part 3):</strong> ${fluency_and_coherence_comment_part3} </p>`;
        resultColumn.innerHTML += `<p><strong>Lexical Resource (part 3):</strong> ${lexical_resource_comment_part3}  </p>`;
        resultColumn.innerHTML += `<p><strong>Grammatical range and accuracy (part 3):</strong> ${grammatical_range_and_accuracy_comment_part3}</p>`;
        resultColumn.innerHTML += `<p><strong>Pronounciation (part 2): </strong> ${pronunciaton_comment_part3} </p>`;
    }

    //resultColumn.innerHTML += `<p><strong>Result:</strong> ${result}</p>`;
    resultColumn.innerHTML += `<p><strong>Time used:</strong> ${Timeused} second and <strong>Word Count: </strong>${wordCount}. Average Rate: ${averageSpeakRate} word per second</p>`;
    userAnswerAndComment.innerHTML = `${resultColumn.innerHTML}`;

    console.log("Done Summary - Show Result - DONE ALL");
}
