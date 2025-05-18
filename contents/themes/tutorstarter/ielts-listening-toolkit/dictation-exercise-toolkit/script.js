
function togglePopup() {
    var popup = document.getElementById("settingsPopup");
    var backdrop = document.getElementById("backdrop");
    if (popup.style.display === "none") {
        popup.style.display = "block";
        backdrop.style.display = "block";
    } else {
        popup.style.display = "none";
        backdrop.style.display = "none";
    }
}

// Function to initialize the sentences array from the paragraph
function initializeSentences() {
    const paragraphElement = document.getElementById('main-paragraph');
    const paragraphText = paragraphElement.innerText;
    let sentencesArray = paragraphText.split('. ').map(sentence => sentence.trim() + '.').filter(sentence => sentence.length > 1);

    const splitSentence = (sentence, parts) => {
        const words = sentence.split(' ');
        const partSize = Math.ceil(words.length / parts);
        let newSentences = [];
        for (let i = 0; i < words.length; i += partSize) {
            newSentences.push(words.slice(i, i + partSize).join(' ') + '.');
        }
        return newSentences;
    };

    sentencesArray = sentencesArray.flatMap(sentence => {
        const wordCount = sentence.split(' ').length;
        if (wordCount >= 8 && wordCount <= 12) {
            return splitSentence(sentence, 2);
        } else if (wordCount >= 13 && wordCount <= 15) {
            return splitSentence(sentence, 3);
        } else if (wordCount > 15) {
            return splitSentence(sentence, 4);
        } else {
            return sentence;
        }
    });

    return sentencesArray;
}



// code này thêm vào để chạy name voice đúng ngay lần 1
var speech_voices;
if ('speechSynthesis' in window) {
  speech_voices = window.speechSynthesis.getVoices();
  window.speechSynthesis.onvoiceschanged = function() {
    speech_voices = window.speechSynthesis.getVoices();
  };
}

// hết

const url = "https://api.dictionaryapi.dev/api/v2/entries/en/";
const result = document.getElementById("result");


let sentences = initializeSentences();
let currentIndex = 0;
let hintCount = 0;
let previousIndex = null; // Biến để theo dõi currentIndex trước đó

let rememberSettings = false;
let selectedVoiceName = "Microsoft Mark - English (United States)";
let selectedSpeed = 1;
function showSentence(index) {
    const contentDiv = document.getElementById('left-side');
    const sentenceWords = sentences[index].split(' ').map(word => `<span class="word">${word}</span>`).join(' ');

    const rightDiv = document.getElementById('right-side');
    const headContent = document.getElementById('bf-content-setting');
    headContent.innerHTML = `
    <div class="left-group">
        <svg id="previous" onclick="navigate('prev')" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
        </svg>
        <div class="question-number" id="question-number">Question ${index + 1} / ${sentences.length}</div>
        <svg id="next" onclick="navigate('next')" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
        </svg>
    </div>
    
    <div id="setting-area">
        <div id="backdrop"></div>
        <div id="settingsPopup">
            <h2>Settings</h2>
            <label for="replayKey">Replay Key</label>
            <select id="replayKey">
                <option value="Ctrl">Ctrl</option>
                <option value="Alt">Alt</option>
                <option value="Shift">Shift</option>
            </select>
            <br><br>
            <label for="autoReplay">Auto Replay (times)</label>
            <select id="autoReplay">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <br><br>
            <label for="speedVoice">Change Speed</label>
            <select id="speedVoice">
                <option value="0.5">0.5x</option>
                <option value="0.75">0.75x</option>
                <option value="1">1x</option>
                <option value="1.25">1.25x</option>
                <option value="1.75">1.75x</option>
                <option value="2">2x</option>
            </select>
            <br><br>
            <label for="changeVoice">Change Voice</label>
            <select id="changeVoice">
                <option value="male1">Microsoft Mark - English (United States)</option>
                <option value="female1">Google UK English Female</option>
                <option value="male2">Google UK English Male</option>
                <option value="female2">Google US English</option>
                <option value="female3">Microsoft Zira - English (United States)</option>
            </select>
            <br><br>
            <label for="timeBetweenReplays">Time between replays</label>
            <select id="timeBetweenReplays">
                <option value="0.5">0.5 seconds</option>
                <option value="1">1 second</option>
                <option value="2">2 seconds</option>
            </select>
            <br><br>
            <label for="alwaysShowExplanation">Always show explanation</label>
            <select id="alwaysShowExplanation">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
            <button id="applyButton">Apply for all</button>
        </div>

    <div class="right-group" id="settingsButton">
    

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
        </svg><p style = 'font-weight:bold'>Settings</p>

    </div>
    </div>
    
`;
loadSettings();

    contentDiv.innerHTML = `
<div id ='warning'> <svg xmlns="http://www.w3.org/2000/svg" id = 'important-warning' width="20" height="20" fill="currentColor" class="bi bi-exclamation-lg" viewBox="0 0 16 16">
  <path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
</svg>  If website don't automatically run script, please click button .<br> You can change speed, voice by clicking <br>For more information and guideline, please click button </div>



</div>

        
       

        <input class="input-ans" id ='input-sentence-${index}' type="text" placeholder="Your answer..."></input>
        <button id ="check-ans" class = "button-1" onclick ="Check_Answer()" >Check Answer</button>
        <button id ="skip-ans" class = "button-4" onclick ="Skip_Answer()" >Skip</button>
        <button id ="next-question" style = "display:none" class = "button-4" onclick="navigate('next')">Next</button>

        <p id ="check-correct"></p>
        <br>
        <button class = "button-1" onclick ="listen_record()" id ="record-number-${index}">Listen again</button>
        <p style ="color:red; font-style:italic" > You can change speed, voice, replay times by clicking icon settings</p>
        
       <!-- <div id="option-control-speak">
            <label for="speedOptions">Speed:</label>
            <select id="speedOptions">
                <option value="0.5">0.5x</option>
                <option value="0.75">0.75x</option>
                <option value="1" selected="selected">1x</option>
                <option value="1.25">1.25x</option>
                <option value="1.5">1.5x</option>
                <option value="1.75">1.75x</option>
                <option value="2">2x</option>
            </select><br>
            <label for="voiceOptions">Voice:</label>
            <select id="voiceOptions" >
                <option value="voice-1">Voice 1</option>
                <option value="voice-2">Voice 2</option>
                <option value="voice-3">Voice 3</option>
                <option value="voice-4">Voice 4</option>
                <option value="voice-5">Voice 5</option>
            </select><br>
            <input style = "display:none" type="checkbox" id="rememberSettings" onclick="handleRememberSettings()" />
            <label style = "display:none" for="rememberSettings">Remember this setting for all questions</label>
        </div> -->
        
        <button class = "button-1" onclick="showHint(${index})">Show Hint</button>

        <p style="display:none" id ="sentence-${index}">${sentences[index]}</p>
        <p style="display:none" id ="show-hint-${index}"></p>
        <br>
        <div id="tips"> </div>
         <svg xmlns="http://www.w3.org/2000/svg" id ='refreshButton' width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
  <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/> 
</svg>

        `;

    rightDiv.innerHTML =`

    <div id ="pronunciation-zone"><h3>Pronunciation
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-soundwave" viewBox="0 0 16 16">
   <path fill-rule="evenodd" d="M8.5 2a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-1 0v-11a.5.5 0 0 1 .5-.5m-2 2a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5m4 0a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5m-6 1.5A.5.5 0 0 1 5 6v4a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m8 0a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m-10 1A.5.5 0 0 1 3 7v2a.5.5 0 0 1-1 0V7a.5.5 0 0 1 .5-.5m12 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0V7a.5.5 0 0 1 .5-.5"/>
</svg></h3>

    <button class="button-10"  onclick="showPronunciation()">Show Pronun</button>

        <div id ="pronunciation-div">
        <p style = "color:red; font-style: italic;">Click to any words below to see spelling, pronounciation, definition... </p>
            <span id="sentence-${index}">${sentenceWords}</span>
            <span  id="myPopup"></span>
        </div>


    </div>

    <div id = "translate-sentence">
    <h3> Translation
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-translate" viewBox="0 0 16 16">
  <path d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286zm1.634-.736L5.5 3.956h-.049l-.679 2.022z"/>
  <path d="M0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zm7.138 9.995q.289.451.63.846c-.748.575-1.673 1.001-2.768 1.292.178.217.451.635.555.867 1.125-.359 2.08-.844 2.886-1.494.777.665 1.739 1.165 2.93 1.472.133-.254.414-.673.629-.89-1.125-.253-2.057-.694-2.82-1.284.681-.747 1.222-1.651 1.621-2.757H14V8h-3v1.047h.765c-.318.844-.74 1.546-1.272 2.13a6 6 0 0 1-.415-.492 2 2 0 0 1-.94.31"/>
</svg></h3> 
    <button class="button-10" onclick="showTranslate()">Show Translation</button>

    <div id ="translate-div">
      <div class="wrapper">
         <div class="text-input">
          <textarea spellcheck="false" readonly disabled class="from-text"></textarea>
          <textarea spellcheck="false" readonly disabled class="to-text" placeholder="Translation"></textarea>
        </div>
        <ul class="controls">
          <li class="row from">
            
            <select id="select1"></select>
          </li>
          <li class="row to">
            <select id="select2" ></select>
           
          </li>
        </ul>
      </div>
      <button id ='button-translate'>Translate Text</button>
    
    </div>


    </div>

     <div id = "speaking-check">
     <h3> Speaking Check <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-spellcheck" viewBox="0 0 16 16">
  <path d="M8.217 11.068c1.216 0 1.948-.869 1.948-2.31v-.702c0-1.44-.727-2.305-1.929-2.305-.742 0-1.328.347-1.499.889h-.063V3.983h-1.29V11h1.27v-.791h.064c.21.532.776.86 1.499.86zm-.43-1.025c-.66 0-1.113-.518-1.113-1.28V8.12c0-.825.42-1.343 1.098-1.343.684 0 1.075.518 1.075 1.416v.45c0 .888-.386 1.401-1.06 1.401zm-5.583 1.035c.767 0 1.201-.356 1.406-.737h.059V11h1.216V7.519c0-1.314-.947-1.783-2.11-1.783C1.355 5.736.75 6.42.69 7.27h1.216c.064-.323.313-.552.84-.552s.864.249.864.771v.464H2.346C1.145 7.953.5 8.568.5 9.496c0 .977.693 1.582 1.704 1.582m.42-.947c-.44 0-.845-.235-.845-.718 0-.395.269-.684.84-.684h.991v.538c0 .503-.444.864-.986.864m8.897.567c-.577-.4-.9-1.088-.9-1.983v-.65c0-1.42.894-2.338 2.305-2.338 1.352 0 2.119.82 2.139 1.806h-1.187c-.04-.351-.283-.776-.918-.776-.674 0-1.045.517-1.045 1.328v.625c0 .468.121.834.343 1.067z"/>
  <path d="M14.469 9.414a.75.75 0 0 1 .117 1.055l-4 5a.75.75 0 0 1-1.116.061l-2.5-2.5a.75.75 0 1 1 1.06-1.06l1.908 1.907 3.476-4.346a.75.75 0 0 1 1.055-.117"/>
</svg></h3>
    <button class="button-10" onclick="showSpeakingCheck()">Show Speaking Check</button>

    
    <div id = 'speaking-check-div'>
    <p>Click button and read the current sentence, we will check your pronunciation</p>
        <button onclick="startRecording()">Record</button>
        <button onclick="stopRecording()">Stop Record</button>
        <p id="record-result"></p>
        </div>
    </div>
    
    `;
    
// Attach event listeners
document.getElementById("settingsButton").addEventListener("click", togglePopup);
document.getElementById("backdrop").addEventListener("click", togglePopup);


document.getElementById('applyButton').addEventListener('click', function() {
    const replayKey = document.getElementById('replayKey').value;
    const autoReplay = document.getElementById('autoReplay').value;
    const speedVoice = document.getElementById('speedVoice').value;
    const changeVoice = document.getElementById('changeVoice').value;
    const timeBetweenReplays = document.getElementById('timeBetweenReplays').value;
    const alwaysShowExplanation = document.getElementById('alwaysShowExplanation').value;

    // Save settings to local storage
    localStorage.setItem('replayKey', replayKey);
    localStorage.setItem('autoReplay', autoReplay);
    localStorage.setItem('speedVoice', speedVoice);
    localStorage.setItem('changeVoice', changeVoice);
    localStorage.setItem('timeBetweenReplays', timeBetweenReplays);
    localStorage.setItem('alwaysShowExplanation', alwaysShowExplanation);

    console.log('Replay Key:', replayKey);
    console.log('Auto Replay:', autoReplay);
    console.log('Change Speed:', speedVoice);
    console.log('Change Voice:', changeVoice);
    console.log('Time between replays:', timeBetweenReplays);
    console.log('Always show explanation:', alwaysShowExplanation);
    
    var popup = document.getElementById("settingsPopup");
    var backdrop = document.getElementById("backdrop");
    if (popup.style.display === "block") {
        popup.style.display = "none";
        backdrop.style.display = "none";
    } else {
        popup.style.display = "block";
        backdrop.style.display = "block";
    }
});


document.getElementById('tips').innerHTML=`<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="yellow" class="bi bi-lightbulb-fill" viewBox="0 0 16 16"><path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.46-.302l-.761-1.77a2 2 0 0 0-.453-.618A5.98 5.98 0 0 1 2 6m3 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1-.5-.5"/></svg> Practice at least 30 minutes every day to improve faster!`;
   
    const tips = [
        "Practice at least 30 minutes every day to improve faster!",
        "Repeat each exercise at least 3 times to improve faster!.",
        "If possible, read out loud the sentence after completing it!",
        "You'll need about 600 hours of practice to achieve a good level of listening skills.",
    ];
    
    function refreshTip() {
        const randomTip = tips[Math.floor(Math.random() * tips.length)];
        document.getElementById('tips').innerHTML=`<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="yellow" class="bi bi-lightbulb-fill" viewBox="0 0 16 16"><path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.46-.302l-.761-1.77a2 2 0 0 0-.453-.618A5.98 5.98 0 0 1 2 6m3 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1-.5-.5"/></svg> ${randomTip}  `;

    }
    
    document.getElementById('refreshButton').addEventListener('click', refreshTip);



    if (index === 0) {
        document.getElementById('previous').disabled = true;
    } else if (index === sentences.length - 1) {
        document.getElementById('next').disabled = true;
    }

    if (rememberSettings) {
        document.getElementById('voiceOptions').value = selectedVoiceName;
        document.getElementById('speedOptions').value = selectedSpeed;
    }



// Create a popup div and add it to the body
const popup = document.createElement('div');
popup.id = 'word-popup';
popup.class= 'popuptext';
popup.style.position = 'absolute';
popup.style.display = 'none';
popup.style.padding = '10px';
popup.style.zIndex ='';
popup.style.backgroundColor = 'white';
popup.style.border = '1px solid black';
popup.style.zIndex = '1000';
document.body.appendChild(popup);

// translation start code
const fromText = document.querySelector(".from-text");
toText = document.querySelector(".to-text");
var selectTag = document.getElementById("select1");
var selectTag2 = document.getElementById("select2");
icons = document.querySelectorAll(".row i");
translateBtn = document.getElementById("button-translate");

let currentSentence = document.getElementById("sentence-"+ index).innerText;

fromText.innerText = currentSentence;
console.log(currentSentence);
console.log(index);

// Clear the existing options (if needed)
selectTag.innerHTML == '';
selectTag2.innerHTML = '';
Object.keys(countries).forEach((country_code, id) => {
    // Set default selected options
    let selected = id === 0 ? country_code === "en-GB" ? "selected" : "" : country_code === "en-GB" ? "selected" : "";
    let option = `<option ${selected} value="${country_code}">${countries[country_code]}</option>`;
    selectTag.insertAdjacentHTML("beforeend", option);
});

Object.keys(countries).forEach((country_code, id) => {
    // Set default selected options
    let selected = id === 0 ? country_code === "en-GB" ? "selected" : "" : country_code === "vi-VN" ? "selected" : "";
    let option = `<option ${selected} value="${country_code}">${countries[country_code]}</option>`;
    selectTag2.insertAdjacentHTML("beforeend", option);
});




fromText.addEventListener("keyup", () => {
    if(!fromText.value) {
        toText.value = "";
    }
}); 

translateBtn.addEventListener("click", () => {
    let text = fromText.value.trim(),
    translateFrom = selectTag.value,
    translateTo = selectTag2.value;
    if(!text) return;
    toText.setAttribute("placeholder", "Translating...");
    let apiUrl = `https://api.mymemory.translated.net/get?q=${text}&langpair=${translateFrom}|${translateTo}`;
    fetch(apiUrl).then(res => res.json()).then(data => {
        toText.value = data.responseData.translatedText;
        data.matches.forEach(data => {
            if(data.id === 0) {
                toText.value = data.translation;
            }
        });
        toText.setAttribute("placeholder", "Translation");
    });
});

icons.forEach(icon => {
    icon.addEventListener("click", ({target}) => {
        if(!fromText.value || !toText.value) return;
        if(target.classList.contains("fa-copy")) {
            if(target.id == "from") {
                navigator.clipboard.writeText(fromText.value);
            } else {
                navigator.clipboard.writeText(toText.value);
            }
        } else {
            let utterance;
            if(target.id == "from") {
                utterance = new SpeechSynthesisUtterance(fromText.value);
                utterance.lang = selectTag[0].value;
            } else {
                utterance = new SpeechSynthesisUtterance(toText.value);
                utterance.lang = selectTag[1].value;
            }
            speechSynthesis.speak(utterance);
        }
    });
});


//end translation code
document.querySelectorAll(`#sentence-${index} .word`).forEach(wordElement => {
    wordElement.addEventListener('click', (event) => {
        let word = wordElement.innerText;
        word = word.replace(/[.,;]/g, ''); 
        
        fetch(`${url}${word}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            console.log(`Data pronunciation cho từ ${word} \n Phát âm: ${data[0].phonetic}`)
        

        popup.innerHTML = `
            <p>${word}</p>
            <p>${data[0].meanings[0].partOfSpeech}</p>
            <p>/${data[0].phonetic}/</p>
             <p>
                   ${data[0].meanings[0].definitions[0].definition}
                </p>
                <p class="word-example">
                    ${data[0].meanings[0].definitions[0].example || ""}
                </p>
            <button onclick="speakText('${word}')">Speak</button>
        `;
        popup.style.left = `${event.pageX}px`;
        popup.style.top = `${event.pageY}px`;
        popup.style.display = 'block';
    });
})
    });
    document.addEventListener('click', (event) => {
        if (!popup.contains(event.target) && !event.target.classList.contains('word')) {
            popup.style.display = 'none';
        }
    });

}
function loadSettings() {
    const replayKey = localStorage.getItem('replayKey') || 'Ctrl';
    const autoReplay = localStorage.getItem('autoReplay') || '2';
    const speedVoice = localStorage.getItem('speedVoice') || '1';
    const changeVoice = localStorage.getItem('changeVoice') || 'male1';
    const timeBetweenReplays = localStorage.getItem('timeBetweenReplays') || '0.5';
    const alwaysShowExplanation = localStorage.getItem('alwaysShowExplanation') || 'yes';

    document.getElementById('replayKey').value = replayKey;
    document.getElementById('autoReplay').value = autoReplay;
    document.getElementById('speedVoice').value = speedVoice;
    document.getElementById('changeVoice').value = changeVoice;
    document.getElementById('timeBetweenReplays').value = timeBetweenReplays;
    document.getElementById('alwaysShowExplanation').value = alwaysShowExplanation;
}

function listen_record() {
    const autoReplayValue = document.getElementById('autoReplay').value;

    const questionCurrent = document.getElementById("sentence-" + currentIndex).innerHTML;
    console.log(`Số replay: ${autoReplayValue}`)
    speakText(questionCurrent);
        
    
}

function Check_Answer() {
    const questionCurrent = document.getElementById("sentence-" + currentIndex).innerHTML;
    let ansUserCurrent = document.getElementById("input-sentence-" + currentIndex).value;
    const checkAns = document.getElementById("check-correct");

    // Remove commas and periods from the user's answer
    ansUserCurrent = ansUserCurrent.replace(/[,.]/g, '').toLowerCase();
    const formattedQuestionCurrent = questionCurrent.replace(/[,.]/g, '').toLowerCase();

    if (ansUserCurrent !== '') {
        if (ansUserCurrent === formattedQuestionCurrent) {
            checkAns.innerText = `You are correct!`;
            checkAns.style.color = "green";
            document.getElementById("check-ans").style.display = 'none';
            document.getElementById("skip-ans").style.display = 'none';
            document.getElementById("next-question").style.display = 'block';
            
        } else {
            checkAns.innerText = `Incorrect! You made an mistake, try again !`;
            checkAns.style.color = "red";
        }
    } else {
        checkAns.innerText = `Please enter your answer`;
        checkAns.style.color = "red";
    }
}

function Skip_Answer() {
    let ansUserCurrent = document.getElementById("input-sentence-" + currentIndex).value;
    const questionCurrent = document.getElementById("sentence-" + currentIndex).innerHTML;
    document.getElementById("input-sentence-" + currentIndex).value = questionCurrent;

}

let recognition;
function startRecording() {
   

    recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    const resultElement = document.getElementById("record-result");

    resultElement.innerHTML = `Recording... Read the sentence`;
    resultElement.style.color = "green";

    recognition.onresult = function(event) {
        const speechResult = event.results[0][0].transcript.toLowerCase();
        const confidence = event.results[0][0].confidence * 100; // Convert to percentage
        const questionCurrent = document.getElementById("sentence-" + currentIndex).innerText.toLowerCase();

        if (confidence > 70 && speechResult === questionCurrent.replace(/[,.]/g, '')) {
            resultElement.innerHTML = `You are correct!<br>You said: ${speechResult}<br>Confidence level (Correction Percentage): ${confidence.toFixed(2)}% `;
            resultElement.style.color = "green";
        } else {
            resultElement.innerHTML = `You are incorrect.<br>You said: ${speechResult}<br> Confidence level(Correction Percentage): ${confidence.toFixed(2)}%. Try again.`;
            //resultElement.innerHTML = `<div> 
        //<i class="far fa-question-circle"></i> MORE INFO
        //<span class="tooltiptext plus">Here you would put additional info that will displayed on mouse hover</span>
   // </div>`;

            resultElement.style.color = "red";
        }
    };

    recognition.onspeechend = function() {
        recognition.stop();
    };

    recognition.onerror = function(event) {
        const resultElement = document.getElementById("record-result");
        resultElement.innerText = `Error occurred in recognition: ${event.error}`;
        resultElement.style.color = "red";
    };

    recognition.start();
}

function stopRecording() {
    if (recognition) {
        recognition.stop();
    }
}


//sentence-${index}
function showHint(currentIndex) {
    // Kiểm tra nếu currentIndex đã thay đổi, reset hintCount
    if (previousIndex !== currentIndex) {
        hintCount = 0;
    }

    // Hiển thị các phần tử
    document.getElementById("show-hint-" + currentIndex).style.display = "block";

    // Lấy câu đáp án và tách thành mảng từ
    let sentence = document.getElementById("sentence-" + currentIndex).innerHTML;
    let words = sentence.split(' ');

    // Tạo chuỗi gợi ý dựa trên số lần bấm nút
    let hint = words.map((word, index) => {
        return index <= hintCount ? word : '_'.repeat(word.length);
    }).join(' ');

    // Tăng số lần bấm nút
    hintCount++;

    // Hiển thị gợi ý
    document.getElementById("show-hint-" + currentIndex).innerHTML = hint;

    // Cập nhật previousIndex
    previousIndex = currentIndex;
}

function handleRememberSettings() {
    rememberSettings = document.getElementById('rememberSettings').checked;
}

function handleSettingsChange() {
    if (rememberSettings) {
        const voiceDropdown = document.getElementById('voiceOptions');
        const speedDropdown = document.getElementById('speedOptions');

        selectedVoiceName = voiceDropdown.value;
        selectedSpeed = parseFloat(speedDropdown.value);
    }
}


function speakText(text) {
    const autoReplayValue = document.getElementById('autoReplay').value;

    const changeVoiceValue = document.getElementById('changeVoice');
    const changeSpeedValue = document.getElementById('speedVoice').value;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US';
    speechSynthesis.cancel();

    let voiceName = "Microsoft Mark - English (United States)"; // default voice
    //if (!rememberSettings) {
    const voiceDropdown = document.getElementById('voiceOptions');
    /*if (voiceDropdown) {
        if (voiceDropdown.value === "voice-2") {
            voiceName = "Google UK English Female";
        } else if (voiceDropdown.value === "voice-3") {
            voiceName = "Google UK English Male";
        } else if (voiceDropdown.value === "voice-4") {
            voiceName = "Google US English";
        }
        else if (voiceDropdown.value === "voice-5") {
            voiceName = "Microsoft Zira - English (United States)";
        } */
    
    
        if (changeVoiceValue.value === "female1") {
            voiceName = "Google UK English Female";
        } else if (changeVoiceValue.value === "male2") {
            voiceName = "Google UK English Male";
        } else if (changeVoiceValue.value === "female2") {
            voiceName = "Google US English";
        }
        else if (changeVoiceValue.value === "female3") {
            voiceName = "Microsoft Zira - English (United States)";
        }
        else if (changeVoiceValue.value === "male1") {
            voiceName = "Microsoft Mark - English (United States)";
        }


    const voices = speechSynthesis.getVoices();
    const selectedVoice = voices.find(voice => voice.name === voiceName);
    if (selectedVoice) {
        utterance.voice = selectedVoice;
    }

    //if (!rememberSettings) {
        const speedDropdown = document.getElementById('speedOptions');
        if (speedDropdown) {
            //utterance.rate = parseFloat(speedDropdown.value);
            utterance.rate = changeSpeedValue;

        }
    


    

    else {

        voiceName = selectedVoiceName;
        utterance.rate = changeSpeedValue;
    }
    
    for (i = 1; i <= autoReplayValue; i++)
        speechSynthesis.speak(utterance);
}





function navigate(direction) {
    let i;
    speechSynthesis.cancel();
    if (direction === 'next' && currentIndex < sentences.length - 1) {
        currentIndex++;
        showSentence(currentIndex);
        } else if (direction === 'prev' && currentIndex > 0) {
        currentIndex--;
        showSentence(currentIndex);
        }   
     //renderQuestion(currentIndex);
    
     const autoReplayValue = document.getElementById('autoReplay').value;
    
        listen_record();
    
    
     
}

function getStart() {
    document.getElementById("intro").style.display = "none";
    document.getElementById("start-dictation").style.display = "block";
    showSentence(currentIndex);
}


function showPronunciation() {
    var x = document.getElementById("pronunciation-div");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }


function showTranslate() {
    var x = document.getElementById("translate-div");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

function showSpeakingCheck() {
    var x = document.getElementById("speaking-check-div");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }



  //renderQuestion(currentIndex);
