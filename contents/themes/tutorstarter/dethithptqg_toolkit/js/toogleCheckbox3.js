let index,specificQuestionID;
function createCheckboxElement(index, specificQuestionID) {
    const checkboxContainer = document.createElement("div");
    checkboxContainer.classList.add("checkbox-container");
    checkboxContainer.id = `checkbox-${specificQuestionID}`;
  
    const checkbox = document.createElement("div");
    checkbox.classList.add("checkbox-number");
    checkbox.textContent = index;
    checkboxContainer.appendChild(checkbox);
  
  
     
      document.getElementById("checkboxes").appendChild(checkboxContainer);
  }
  
  
  
    
  
  
  function updateCheckboxColor(inputId,questionNumber) {
     let checked = false;
      const inputs = document.querySelectorAll(`input[name='${questionNumber}']`);
      
  
    if (input.checked) {
      checkboxContainer.classList.add('checkbox-checked');
    } else {
      checkboxContainer.classList.remove('checkbox-checked');
    }
  }
  


function toggleCheckboxColor(checkboxContainer, questionNumber) {
    let checked = false;
      const inputs = document.querySelectorAll(`input[name='${questionNumber}']`);
      
  
      inputs.forEach(input => {
          if (input.checked) {
              
              checkboxContainer.classList.add('checkbox-checked');
              checked = true;
          }
      });
  
      if (!checked) {
          if (checkboxContainer.classList.contains('checkbox-yellow')) {
              checkboxContainer.classList.remove('checkbox-yellow');
          } else {
              checkboxContainer.classList.add('checkbox-yellow');
          }
      }
  }


  

  function rememberQuestion(i) {
    const checkboxContainer = document.getElementById(`checkbox-${i}`);
    const bookmarkQuestionElement = document.getElementById("bookmark-question-" + i);

    if (!checkboxContainer) {
        console.error(`Element with id 'checkbox-${i}' not found.`);
        return;
    }

    if (!bookmarkQuestionElement) {
        console.error(`Element with id 'bookmark-question-${i}' not found.`);
        return;
    }

    console.log("Bookmark: bookmark-question-" + i);

    // Toggle the background color of the question checkbox container and change the image src
    if (checkboxContainer.style.backgroundColor === 'yellow') {
        checkboxContainer.style.backgroundColor = '';
        bookmarkQuestionElement.src = '/contents/themes/tutorstarter/dethithptqg_toolkit/assets/bookmark_empty.png';
    } else {
        checkboxContainer.style.backgroundColor = 'yellow';
        bookmarkQuestionElement.src = '/contents/themes/tutorstarter/dethithptqg_toolkit/assets/bookmark_filled.png';
    }
}
