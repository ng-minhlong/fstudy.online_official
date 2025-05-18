function showLoadingPopup() {
  let timerInterval;
  
  // Show the loader when the popup opens

  Swal.fire({
      title: "<p>Bạn sẵn sàng làm bài chưa</p>", 
      icon: "question",
      //showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Bắt đầu làm bài",
  }).then((result) => {
      if (result.isConfirmed) {
          Swal.fire({
              title: "Đang tải bài thi",
              html: "Vui lòng đợi trong giây lát",
              timer: 2000,
              allowOutsideClick: false,
              showCloseButton: false,
              timerProgressBar: true,
              didOpen: () => {
                  Swal.showLoading();
              },
              willClose: () => {
                  clearInterval(timerInterval);
                  
              }
          }).then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                  console.log("Test displayed");
                  startTest();
                  DoingTest = true;
                  MathJax.Hub.Queue(["Typeset", MathJax.Hub]);

                  // Hide the loader after loading completes
              }
          });
      } else {
          // Hide the loader if the user cancels the test
      }
  });
}

function closePopup() {
  document.getElementById("loading-popup-remember").style.display = "none";
}

function prestartTest()
{
    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
    if(premium_test == "False"){
        console.log("Cho phép làm bài")
    }
    else{
    console.log(premium_test);
    console.log(token_need);
    console.log(change_content);
    console.log(time_left);
    // Giảm time_left tại frontend
    time_left--;
    console.log("Updated time_left:", time_left);

    // Gửi request AJAX đến admin-ajax.php
    jQuery.ajax({
        url: `${siteUrl}/wp-admin/admin-ajax.php`,
        type: "POST",
        data: {
            action: "update_time_left",
           // username: change_content,
            time_left: time_left,
            table_test: 'digital_sat_test_list',
            id_test: id_test
        },
        success: function (response) {
            console.log("Server response:", response);
        },
        error: function (error) {
            console.error("Error updating time_left:", error);
        }
    });
}

    startTest();
}



function startTest() {
    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

    document.getElementById("test-prepare").style.display = "none";
    document.getElementById("user-button").style.display = 'block';

    document.getElementById("title").style.display = "none";
    document.getElementById("navi-button").style.display = 'block';
    document.getElementById("checkbox-button").style.display = 'block';
    document.getElementById("change_appearance").style.display = "block";
    document.getElementById("start-test").style.display = 'none';
    document.getElementById("basic-info").style.display = 'none';
    document.getElementById("quiz-container").style.display = 'block';
    document.getElementById("current_module").style.display = "block";
    var explain_zone = document.getElementsByClassName("explain-zone");
    for (var i = 0; i < explain_zone.length; i++)
        explain_zone[i].style.display = 'none';
    document.getElementById("center-block").style.display = 'block';

    
    showQuestion(currentQuestionIndex);
    submitButton();
}
