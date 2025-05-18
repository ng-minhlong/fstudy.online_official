
const fullScreenButton = document.getElementById('full-screen-button');

fullScreenButton.addEventListener('click', toggleFullScreen);

function toggleFullScreen() {
    const doc = window.document;
    const docEl = doc.documentElement;

    const requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
    const exitFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

    if (!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
        // Enter full screen
        if (requestFullScreen) {
            requestFullScreen.call(docEl);
            fullScreenButton.textContent = '⛶'; // Change button label
        }
    } else {
        // Exit full screen
        if (exitFullScreen) {
            exitFullScreen.call(doc);
            fullScreenButton.textContent = '⛶'; // Change button label back
        }
    }
}
