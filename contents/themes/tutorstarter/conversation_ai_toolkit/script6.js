// Speech Recognition Setup
// DOM Elements
const startRecordButton = document.getElementById("startRecordButton");
const endRecordButton = document.getElementById("endRecordButton");
const sendButton = document.getElementById("sendButton");
const userInput = document.getElementById("userInput");
const chatBox = document.getElementById("chatBox");
const remainingAttemptsEl = document.getElementById("remainingAttempts");
var selectedLanguage;

// Speech Synthesis
const synth = window.speechSynthesis;
let currentAudio = null;

// Audio Recording Variables
let mediaRecorder;
let audioChunks = [];
let currentRecording = null;

// Chat State
let isRecording = false;
let conversation = [];
let remainingAttempts = sentence_limit;
let targetsCompleted = {
    target1: false,
    target2: false,
    target3: false
};

// Event Listeners
startRecordButton.addEventListener("click", startRecording);
endRecordButton.addEventListener("click", stopRecording);
sendButton.addEventListener("click", handleSendMessage);
userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        handleSendMessage();
    }
});

async function setupAudioRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        
        mediaRecorder.ondataavailable = (e) => {
            audioChunks.push(e.data);
        };
        
        mediaRecorder.onstop = () => {
            currentRecording = new Blob(audioChunks, { type: 'audio/webm' });
            console.log('Recording stopped, audio saved', currentRecording);
        };
    } catch (error) {
        console.error("Error setting up audio recording:", error);
    }
}

let recordingStartTime = 0;
let recordingEndTime = 0;

async function startRecording() {
    if (!isRecording) {
        // Reset audio chunks
        audioChunks = [];
        
        // Start audio recording
        if (mediaRecorder) {
            mediaRecorder.start();
        }
        
        // Record start time
        recordingStartTime = Date.now();
        
        // Update UI
        startRecordButton.style.display = 'none';
        endRecordButton.style.display = 'block';
        isRecording = true;
        
        // Clear input field for new recording
        userInput.value = "";
        userInput.placeholder = "Recording...";
    }
}

async function stopRecording() {
    if (isRecording) {
        // Record end time
        recordingEndTime = Date.now();
        
        // Stop audio recording if available
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            await new Promise((resolve) => {
                mediaRecorder.onstop = () => {
                    currentRecording = new Blob(audioChunks, { type: 'audio/webm' });
                    console.log('Recording stopped, audio saved', currentRecording);
                    resolve();
                };
                mediaRecorder.stop();
            });
        }
        
        // Update UI
        endRecordButton.style.display = 'none';
        startRecordButton.style.display = 'block';
        isRecording = false;
        userInput.placeholder = "Type your message...";
        
        // Send recording to STT API
        if (currentRecording) {
            await transcribeRecording();
        }
    }
}

async function transcribeRecording() {
    try {
        userInput.value = "Processing speech...";
        
        const formData = new FormData();
        formData.append("audio", currentRecording, "recording.webm");
        formData.append("lang", selectedLanguage);

        const response = await fetch("http://127.0.0.1:5000/stt", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        
        if (result.text) {
            userInput.value = result.text;
        } else {
            userInput.value = "";
            console.error("No transcription returned from API");
        }
    } catch (error) {
        console.error("Error transcribing recording:", error);
        userInput.value = "";
    }
}


// Handle speech recognition results
function handleSpeechResult(event) {
    const transcript = Array.from(event.results)
        .map(result => result[0].transcript)
        .join("");
    userInput.value = transcript;
}

function handleSpeechError(event) {
    console.error("Speech recognition error:", event.error);
    stopRecording();
}



// Initialize audio recording when page loads
window.addEventListener('load', setupAudioRecording);
// Append message to chat box with audio elements


function appendMessage(role, message, audioUrl = null) {
    const messageDiv = document.createElement("div");
    messageDiv.className = `message ${role === 'user' ? 'user-message' : 'assistant-message'}`;
    
    const messageContent = document.createElement("div");
    messageContent.className = "message-content";
    messageContent.textContent = message;
    
    messageDiv.appendChild(messageContent);
    
    // Hiển thị audio cho user message
    if (role === 'user' && audioUrl) {
        const audioContainer = document.createElement("div");
        audioContainer.style.marginTop = "10px";
        
        const audioLabel = document.createElement("div");
        audioLabel.textContent = "Your recording:";
        audioLabel.style.fontSize = "0.8em";
        audioLabel.style.marginBottom = "5px";
        audioContainer.appendChild(audioLabel);
        
        const audioPlayer = document.createElement("audio");
        audioPlayer.src = audioUrl;
        audioPlayer.controls = true;
        audioPlayer.style.width = "100%";
        audioContainer.appendChild(audioPlayer);
        
        messageDiv.appendChild(audioContainer);
    }
    
    // Thêm nút play cho assistant message
    if (role === 'assistant') {
        const playButton = document.createElement("button");
        playButton.className = "btn btn-sm btn-outline-secondary";
        playButton.innerHTML = "🔊 Play Voice";
        playButton.style.marginLeft = "10px";
        playButton.onclick = () => speakText(message);
        messageDiv.appendChild(playButton);
    }
    
    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}




async function speakText(text) {
            try {
                const apiUrl = `http://127.0.0.1:5000/tts?text=${encodeURIComponent(text)}&lang=${selectedLanguage}`;
                
                const audio = new Audio(apiUrl);
                
                // Đặt tốc độ phát
                const speedDropdown = document.getElementById('speedOptions');
                if (speedDropdown) {
                    audio.playbackRate = parseFloat(speedDropdown.value);
                }
                
                await audio.play();
                console.log('Playback started');
            } catch (error) {
                console.error('Playback error:', error);
                alert('Could not play audio: ' + error.message);
            }
        }


// Get last 3 messages for context
function getLastThreeMessages() {
    return conversation.slice(-3);
}

// Check targets in response and update checkboxes
function checkTargetsInResponse(response) {
    // Tạo các biến thể có thể có của mỗi target
    const target1Variations = ['{target_1}', '{target 1}', '{Target_1}', '{Target 1}'];
    const target2Variations = ['{target_2}', '{target 2}', '{Target_2}', '{Target 2}'];
    const target3Variations = ['{target_3}', '{target 3}', '{Target_3}', '{Target 3}'];
    
    // Chuẩn hóa response về lowercase để so sánh không phân biệt hoa thường
    const lowerResponse = response.toLowerCase();
    
    // Kiểm tra target 1
    if (!targetsCompleted.target1) {
        const found = target1Variations.some(variation => 
            lowerResponse.includes(variation.toLowerCase())
        );
        if (found) {
            document.getElementById('target1-checkbox').checked = true;
            targetsCompleted.target1 = true;
        }
    }
    
    // Kiểm tra target 2
    if (!targetsCompleted.target2) {
        const found = target2Variations.some(variation => 
            lowerResponse.includes(variation.toLowerCase())
        );
        if (found) {
            document.getElementById('target2-checkbox').checked = true;
            targetsCompleted.target2 = true;
        }
    }
    
    // Kiểm tra target 3
    if (!targetsCompleted.target3) {
        const found = target3Variations.some(variation => 
            lowerResponse.includes(variation.toLowerCase())
        );
        if (found) {
            document.getElementById('target3-checkbox').checked = true;
            targetsCompleted.target3 = true;
        }
    }
}

// Disable recording and sending when attempts are exhausted
function disableInputsIfNeeded() {
    if (remainingAttempts <= 0) {
        startRecordButton.disabled = true;
        endRecordButton.disabled = true;
        sendButton.disabled = true;
        userInput.disabled = true;
        
        // Thêm CSS để làm rõ trạng thái disabled
        startRecordButton.style.opacity = '0.5';
        sendButton.style.opacity = '0.5';
        userInput.style.opacity = '0.5';
    } else {
        startRecordButton.disabled = false;
        sendButton.disabled = false;
        userInput.disabled = false;
        
        startRecordButton.style.opacity = '1';
        sendButton.style.opacity = '1';
        userInput.style.opacity = '1';
    }
}

// Fetch conversation response from API
async function fetchConversationResponse(messages) {
    try {
        const response = await fetch(`${siteUrl}/api/public/test/v1/conversation/completions`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id_test: id_test,
                lang: selectedLanguage,
                messages: messages
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("Error fetching conversation response:", error);
        return { error: error.message };
    }
}async function handleSendMessage() {
    // Always stop recording before processing the message
    if (isRecording) {
        await stopRecording();
    }

    sendButton.disabled = true;
    endRecordButton.style.display = "none";
    startRecordButton.style.display = "block";

    const userMessage = userInput.value.trim();
    
    if (!userMessage || remainingAttempts <= 0) {
        sendButton.disabled = false;
        return;
    }

    remainingAttempts--;
    remainingAttemptsEl.textContent = remainingAttempts;
    disableInputsIfNeeded();

    // Check for duplicate user message
    const lastUserMessage = conversation.filter(m => m.role === "user").pop();
    if (lastUserMessage?.content === userMessage) {
        remainingAttempts++;
        remainingAttemptsEl.textContent = remainingAttempts;
        disableInputsIfNeeded();
        sendButton.disabled = false;
        return;
    }

    // Calculate speaking metrics
    const recordingDuration = (recordingEndTime - recordingStartTime) / 1000; // in seconds
    const wordCount = userMessage.split(/\s+/).filter(word => word.length > 0).length;
    const speakRate = wordCount / (recordingDuration || 1); // words per second

    // Upload audio recording if available
    let audioUrl = null;
    if (currentRecording) {
        try {
            const formData = new FormData();
            formData.append('file', currentRecording, 'recording-conversation.webm');
            
            const uploadResponse = await fetch(`${siteUrl}/api/v1/audio/service/upload`, {
                method: 'POST',
                body: formData
            });
            
            if (uploadResponse.ok) {
                const uploadResult = await uploadResponse.json();
                console.log('Full upload response:', uploadResult);
                audioUrl = uploadResult.link;
                console.log('Audio uploaded successfully:', audioUrl);
            } else {
                const errorResponse = await uploadResponse.text();
                console.error('Audio upload failed:', uploadResponse.status, errorResponse);
            }
        } catch (uploadError) {
            console.error('Error uploading audio:', uploadError);
        }
    }

    // Display message with audio
    appendMessage("user", userMessage, audioUrl);
    conversation.push({ 
        role: "user", 
        content: userMessage,
        audioUrl: audioUrl,
        recordingMetrics: {
            duration: recordingDuration,
            wordCount: wordCount,
            speakRate: speakRate
        }
    });

    // Reset recording
    currentRecording = null;
    audioChunks = [];

    // Call API and handle response
    try {
        const lastThreeMessages = getLastThreeMessages();
        const { response, error } = await fetchConversationResponse(lastThreeMessages);
        
        if (error) {
            appendMessage("assistant", `Error: ${error}`);
        } else {
            appendMessage("assistant", response);
            conversation.push({ role: "assistant", content: response });
            checkTargetsInResponse(response);
            speakText(response);
        }
    } catch (apiError) {
        console.error('API call failed:', apiError);
        appendMessage("assistant", "Sorry, I encountered an error processing your request.");
    }
    
    userInput.value = "";
    userInput.focus();
    sendButton.disabled = false;
}

// Add CSS styles for the chat interface
const style = document.createElement('style');
style.textContent = `
  
 
    .assistant-message {
        background-color: #f1f1f1;
        margin-right: auto;
    }
    .message-content {
        display: inline-block;
    }
    .audio-play-button {
        vertical-align: middle;
        padding: 2px 8px;
    }
    .input-group {
        margin-top: 15px;
    }
`;
document.head.appendChild(style);
// Initialize audio recording when page loads
window.addEventListener('load', () => {
    setupAudioRecording();
    disableInputsIfNeeded(); // In case page loads with 0 attempts
});


// Function to handle submit button click
async function handleSubmit() {
    // Check if attempts are exhausted
    if (remainingAttempts <= 0) {
        submitButton();
        return;
    }
    
    // Check if all targets are completed
    const allTargetsCompleted = targetsCompleted.target1 && targetsCompleted.target2 && targetsCompleted.target3;
    
    if (allTargetsCompleted) {
        // Show confirmation for completed targets
        Swal.fire({
            title: 'Submit Confirmation',
            text: `You have completed all targets. You have ${remainingAttempts} attempts remaining. Do you want to submit?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitButton();
            }
        });
    } else {
        // Show warning for incomplete targets
        const incompleteTargets = [];
        if (!targetsCompleted.target1) incompleteTargets.push('Target 1');
        if (!targetsCompleted.target2) incompleteTargets.push('Target 2');
        if (!targetsCompleted.target3) incompleteTargets.push('Target 3');
        
        Swal.fire({
            title: 'Incomplete Targets',
            html: `You haven't completed: <b>${incompleteTargets.join(', ')}</b>. Do you still want to submit?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit anyway',
            cancelButtonText: 'No, continue'
        }).then((result) => {
            if (result.isConfirmed) {
                submitButton();
                
            }
        });
    }
}

async function submitButton() {
    // Create result array containing conversation pairs with metrics
    const conversationPairs = [];
    
    // Process conversation to create pairs
    for (let i = 0; i < conversation.length; i++) {
        const userMsg = conversation[i];
        if (userMsg.role === 'user') {
            const assistantMsg = conversation[i + 1]; // Next message should be assistant
            if (assistantMsg && assistantMsg.role === 'assistant') {
                conversationPairs.push({
                    questionNumber: conversationPairs.length + 1,
                    userAnswer: userMsg.content,
                    assistantResponse: assistantMsg.content,
                    recording: {
                        duration: userMsg.recordingMetrics?.duration || 0,
                        wordCount: userMsg.recordingMetrics?.wordCount || 0,
                        speakRate: userMsg.recordingMetrics?.speakRate || 0,
                        audioUrl: userMsg.audioUrl || null
                    }
                });
            }
        }
    }
    
    try {
        // Send data to server
        const response = await fetch(`${siteUrl}/api/public/test/v1/conversation/final/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                conversationPairs: conversationPairs,
                testId: id_test
            })
        });

        if (!response.ok) {
            throw new Error('Failed to submit data');
        }

        // Disable submit button after successful submission
        document.getElementById('submitButton').disabled = true;
        const responseData = await response.json();

        // Show confirmation
        Swal.fire({
            title: 'Submitted!',
            text: 'Your answers have been submitted successfully.',
            icon: 'success'
        });
        document.getElementById("conversation").value = JSON.stringify(responseData, null, 2);
        ResultInput();


    } catch (error) {
        console.error("Submission error:", error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to submit answers. Please try again.',
            icon: 'error'
        });
    }
}

// Add event listener for submit button
document.getElementById('submitButton').addEventListener('click', handleSubmit);

// Check remaining attempts periodically and auto-submit if needed
setInterval(() => {
    if (parseInt(remainingAttemptsEl.textContent) === 0) {
        submitButton();
    }
}, 1000);



function ResultInput() {
    document.getElementById("dateform").value = dateElement;
    document.getElementById("testname").value = testname;
    document.getElementById("idtest").value = id_test;
    document.getElementById("testsavenumber").value = resultId;

    // Add a delay before submitting the form
    setTimeout(function() {
    // Automatically submit the form
   // jQuery('#saveConversationAI').submit();
    }); 

}




// Hàm kiểm tra trình duyệt Opera
function isOperaBrowser() {
    const userAgent = navigator.userAgent.toLowerCase();
    return userAgent.includes('opera') || userAgent.includes('opr/');
}

// Hàm main chính
function main() {
    console.log("Passed Main");
    
    
    
    document.getElementById("personalize").style.display = "block";
    
    
    setTimeout(() => {
        console.log("Show Test!");
        document.getElementById("start_test").style.display = "block";
        document.getElementById("welcome").style.display = "block";
    }, 1000);
}


function prestartTest() {
    if (premium_test == "False") {
        console.log("Cho phép làm bài");
    } else {
        console.log(premium_test, token_need, change_content, time_left);
        time_left--;
        console.log("Updated time_left:", time_left);

        jQuery.ajax({
            url: `${siteUrl}/wp-admin/admin-ajax.php`,
            type: "POST",
            data: {
                action: "update_time_left",
                time_left: time_left,
                id_test: id_test,
                table_test: 'conversation_with_ai_list',
            },
            success: response => console.log("Server response:", response),
            error: error => console.error("Error updating time_left:", error)
        });
    }
    startTest();
}

function startTest() {
    document.getElementById("test-prepare").style.display = "none";
    document.getElementById("test_screen").style.display = "block";
}

    const languageSelect = document.getElementById("languageSelect");
    const scenarioOptions = document.getElementById("scenarioOptions");
    const userLevel = document.getElementById("userLevel");
    const startBtn = document.getElementById("startBtn");

    function checkEnableButton() {
        const languageSelected = languageSelect.value !== "";
        const scenarioChecked = scenarioOptions.querySelectorAll("input[type='checkbox']:checked").length > 0;
        const levelSelected = userLevel.value !== "";

        if (languageSelected && scenarioChecked && levelSelected) {
            startBtn.disabled = false;
            startBtn.classList.add("enabled");
        } else {
            startBtn.disabled = true;
            startBtn.classList.remove("enabled");
        }
    }

    languageSelect.addEventListener("change", checkEnableButton);
    userLevel.addEventListener("change", checkEnableButton);
    scenarioOptions.querySelectorAll("input[type='checkbox']").forEach(cb => {
        cb.addEventListener("change", checkEnableButton);
    });

function processingPersonalize() {
        selectedLanguage = document.getElementById("languageSelect").value;
        const selectedLevel = document.getElementById("userLevel").value;
        const selectedScenarios = Array.from(
            document.querySelectorAll("#scenarioOptions input[type='checkbox']:checked")
        ).map(cb => cb.value);

        console.log("Language:", selectedLanguage);
        console.log("Scenarios:", selectedScenarios);
        console.log("Level:", selectedLevel);

        //alert("Đã bắt đầu luyện tập!");
        document.getElementById("test-prepare").style.display = "block";
        document.getElementById("personalize").style.display = "none";

        // Thêm xử lý cá nhân hóa tại đây
}
    