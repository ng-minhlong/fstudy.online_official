//2C4243





function DarkMode() {
    var body = document.body;
    var elems = document.getElementsByClassName('header');
    var answer_box =  document.getElementsByClassName('answer-box');  

    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        for (var i = 0; i < elems.length; i++) {
            elems[i].classList.remove('dark-mode');
            elems[i].classList.add('light-mode');
           
        }

        for (var i = 0; i < quizData.questions.length; i++) {
            answer_box[i].classList.add('light-mode');
            answer_box[i].classList.remove('dark-mode');

        }


} else {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        for (var i = 0; i < elems.length; i++) {
            elems[i].classList.remove('light-mode');
            elems[i].classList.add('dark-mode');
            
        }
        for (var i = 0; i < quizData.questions.length; i++) {
            answer_box[i].classList.remove('light-mode');
            answer_box[i].classList.add('dark-mode');
        }
    }
}