document.getElementById('logo').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default anchor behavior (navigation)
    window.location.href = 'https://onluyen247.net'; // Redirect to the desired URL
});
if (DoingTest = true){
    window.addEventListener('beforeunload', function(event) {
        // Display a confirmation dialog
        event.preventDefault(); // Some browsers require this to display the dialog
        event.returnValue = ''; // This triggers the confirmation dialog in most browsers

        // The message in the alert is typically ignored in modern browsers,
        // but setting returnValue still triggers the prompt.
        return "Bạn có chắc muốn thoát khỏi đề thi, bạn sẽ mất tiến độ làm bài của bạn. \n Bạn có thể lưu lại bài lại"; // Older browsers might show this message
    });

    
}