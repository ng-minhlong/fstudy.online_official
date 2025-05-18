
// Open the color choice popup when the Colors button is clicked
function openColorPopup() {
    document.getElementById('color-popup').style.display = 'block';
}

// Apply selected color from the color choice popup
function applyColor() {
    var selectedColor = document.querySelector('input[name="color"]:checked').value;
    document.body.style.backgroundColor = selectedColor;
    closeColorPopup();
}

// Close the color choice popup
function closeColorPopup() {
    document.getElementById('color-popup').style.display = 'none';
}

// end color background option

