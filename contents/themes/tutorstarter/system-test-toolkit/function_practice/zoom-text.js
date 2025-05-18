// Increase font size
function zoomIn() {
    var elements = document.querySelectorAll('.question, .answer-options');
    var canZoomIn = true;
    elements.forEach(element => {
        var fontSize = parseInt(window.getComputedStyle(element).fontSize);
        if (fontSize < 26) {
            element.style.fontSize = (fontSize + 2) + 'px';
        } else {
            canZoomIn = false;
        }
    });
    if (!canZoomIn) {
        alert("Cannot zoom in anymore");
    }
}

// Decrease font size
function zoomOut() {
    var elements = document.querySelectorAll('.question, .answer-options');
    var canZoomOut = true;
    elements.forEach(element => {
        var fontSize = parseInt(window.getComputedStyle(element).fontSize);
        if (fontSize > 12) {
            element.style.fontSize = (fontSize - 2) + 'px';
        } else {
            canZoomOut = false;
        }
    });
    if (!canZoomOut) {
        alert("Cannot zoom out anymore");
    }
}

// Add event listeners to the zoom in and zoom out buttons
document.getElementById('zoom-in').addEventListener('click', zoomIn);
document.getElementById('zoom-out').addEventListener('click', zoomOut);

// End increase, decrease font size
