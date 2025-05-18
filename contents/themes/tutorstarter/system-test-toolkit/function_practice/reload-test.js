function reloadTest(){
  
    newTypeset();
    }

    
function newTypeset(){
    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

    Swal.fire({
  title: "Thành công",
  text: "Đã tải lại câu hỏi và đáp án đề thi. Nếu đề thi còn lỗi, hãy báo lỗi để chúng tôi fix nhanh nhất !",
  icon: "success"
});

}

  function ReloadTestnosignal(){
 
    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

  }
