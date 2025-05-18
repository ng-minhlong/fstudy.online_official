window.myApp = window.myApp || {};
window.myApp.urlEncode = window.myApp.urlEncode || {};

function fetchImageAsBase64() {
    for(let i = 0; i < quizData.questions.length; i++){
        let url = quizData.questions[i].image 
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const base64String = reader.result;
                    console.log(base64String); // In ra chuỗi Base64
                    //document.getElementById('imageDisplay').src = base64String;
                };
                reader.readAsDataURL(blob);
            })
            .catch(error => console.error('Error fetching image:', error));


            window.myApp.urlEncode[i] = url;
            sessionStorage.setItem(`url_encode_${i}`, url);
        }
}