

// Add this JavaScript code

// Open the draft popup when the Draft button is clicked
document.getElementById('print-exam-button').addEventListener('click', openPrintpopup);

// Close the draft popup when the close button is clicked
function closePrintpopup() {
    if (DoingTest == false){
                        document.getElementById("quiz-container").style.display = "none"; // Make quiz container visible
                       document.getElementsByClassName('explain-zone')[0].style.visibility = 'block';

                        document.getElementById('print-popup').style.display = 'none';

    }
    else if (DoingTest== true) {

    document.getElementById('print-popup').style.display = 'none';
}
}

function openPrintpopup() {

    document.getElementById('print-popup').style.display = 'block';
            document.getElementById("quiz-container").style.display = "block"; // Make quiz container visible


}


function printExam() {
        document.getElementById("quiz-container").style.display = "block"; // Make quiz container visible


        ReloadTestnosignal();
        window.print(); // Trigger print
    }


function printAnswer() {

        ReloadTestnosignal();
        window.print(); // Trigger print
    }



