

 //Translate tool (Google trans)
// open translate

document.getElementById('translate-button').addEventListener('click', openTranslatePopup);

// Close the draft popup when the close button is clicked
function closeTranslatePopup() {
    document.getElementById('translate-popup').style.display = 'none';
}

function openTranslatePopup() {
    document.getElementById('translate-popup').style.display = 'block';
    
}


// close translate popup



//translate
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'en,vi,zh-CN,ru,ja,ko,es,th,de,fr,nl',
    
  }, 'google_translate_element');
}

// end translate

 //end gg translate 