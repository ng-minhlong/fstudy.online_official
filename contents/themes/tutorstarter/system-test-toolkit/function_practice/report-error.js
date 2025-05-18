

// Add this JavaScript code

// Open the draft popup when the Draft button is clicked
document.getElementById('report-error').addEventListener('click', openReportErrorPopup);

// Close the draft popup when the close button is clicked
function closeReportErorPopup() {
    document.getElementById('report-error-popup').style.display = 'none';
}

function openReportErrorPopup() {
    document.getElementById('report-error-popup').style.display = 'block';
    
}

const form= document.querySelector("form");
const fullName = document.getElementById("name");
const email = document.getElementById("email");
const testnamereport = document.getElementById("testnamereport");
const testnumberreport= document.getElementById("testnumberreport");
const descriptionreport =document.getElementById("descriptionreport");

function sendEmail()
{
    const bodyMessage = `Full Name: ${fullName.value} <br> Email: ${email.value} <br> 
Test Report: ${testnamereport.value}<br> Number error: ${testnumberreport.value} <br> More details: ${descriptionreport.value}`;

    Email.send({
    Host : "smtp.elasticemail.com",
    Username : "nguyenminhlong2811@gmail.com",
    Password : "A2A12CB373103778223EF8508869FDE3F7BA",
    To : 'nguyenminhlong2811@gmail.com',
    From : "nguyenminhlong2811@gmail.com",
    Subject : "Report Error on test",
    Body : bodyMessage
}).then(
  message => {
        if(message=="OK"){
            Swal.fire({
                title: "Thành công ",
                text: "Cảm ơn bạn đã phản hồi về bài thi/ kết quả ",
                icon:"success"
            });
        }
    }
);
}

function checkInputs()
{
    const items = document.querySelectorAll('.item');
    for(const item of items)
    {
        if (item.value == ""){
            item.classList.add("error");
            item.parentElement.classList.add("error");
        }
        if(items[1].value !=""){
            checkEmail();
        }
        items[1].addEventListener("keyup",() =>{
            checkEmail();
        });



        item.addEventListener("keyup",() => {
            if(item.value!= ""){
                item.classList.remove("error");
                item.parentElement.classList.remove("error");
            }
            else{
                item.classList.add("error");
                item.parentElement.classList.add("error");
            }

        });
    }
}


function checkEmail(){
    const emailRegex = /^([a-z\d\.-]+)@([a-z\d-]+)\.([a-z]{2,3})(\.[a-z]{2,3})?$/;
    const errorTxtEmail =document.querySelector(".error-txt.email");


    if(!email.value.match(emailRegex)){
        email.classList.add("error");
        email.parentElement.classList.add("error");
        if (email.value!=""){
            errorTxtEmail.innerText="Enter valid email address";
        }
        else{
            errorTxtEmail.innerText="Email Address cannot be blank";
        }

    }
    else{
        email.classList.remove("error");
        email.parentElement.classList.remove("error");
    }
}
form.addEventListener("submit", (e) =>{
    e.preventDefault();
    checkInputs();

    if(!fullName.classList.contains("error") && !email.classList.contains("error") &&  !testnamereport.classList.contains("error") && !testnumberreport.classList.contains("error") && !descriptionreport.classList.contains("error"))
    {
        sendEmail();
        e.target.reset();

    }

});




