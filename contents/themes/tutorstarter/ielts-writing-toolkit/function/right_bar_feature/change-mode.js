//2C4243





function DarkMode() {
    var body = document.body;
    var elems = document.getElementsByClassName('header');
    var elems2 = document.getElementsByClassName('exam_area');

    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        for (var i = 0; i < elems.length; i++) {
            elems[i].classList.remove('dark-mode');
            elems[i].classList.add('light-mode');
            elems2[i].classList.remove('dark-mode');
            elems2[i].classList.add('light-mode');
        }
    } else {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        for (var i = 0; i < elems.length; i++) {
            elems[i].classList.remove('light-mode');
            elems[i].classList.add('dark-mode');
            elems2[i].classList.remove('light-mode');
            elems2[i].classList.add('dark-mode');
        }
    } 
        /*if (body.classList.contains('light-mode')) {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            for (var i = 0; i < elems.length; i++) {
                elems[i].classList.remove('light-mode');
                elems[i].classList.add('dark-mode');
                elems2[i].classList.remove('light-mode');
                elems2[i].classList.add('dark-mode');
            }
        } 
        
        else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            for (var i = 0; i < elems.length; i++) {
                elems[i].classList.remove('dark-mode');
                elems[i].classList.add('light-mode');
                elems2[i].classList.remove('dark-mode');
                elems2[i].classList.add('light-mode');
            }
        }*/
}