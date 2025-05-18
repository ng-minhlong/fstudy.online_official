import devtools from 'https://cdn.jsdelivr.net/npm/devtools-detect/index.js';

// Function to update the display based on DevTools status
function updateDevToolsStatus() {
    const statusElement = document.getElementById('devtools-status');
    if (devtools.isOpen) {
        statusElement.textContent = 'Yes';  // DevTools is open
        window.location.href = 'https://example.com'; // Redirect to your URL
    } else {
        statusElement.textContent = 'No';   // DevTools is closed
    }
}

// Initial status check
updateDevToolsStatus();

// Listen for changes in DevTools state
window.addEventListener('devtoolschange', updateDevToolsStatus);

// Optional: Check status at regular intervals (e.g., every 500 milliseconds)
setInterval(updateDevToolsStatus, 500);