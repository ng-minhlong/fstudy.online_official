const urlParams = new URLSearchParams(window.location.search);
//const option = urlParams.get('option');
const optiontrack = urlParams.get('optiontrack');

const trackingmodevalue = optiontrack;
var luotthoat  = 0;
function checktrackingmode(){

if(trackingmodevalue > 0){
      
  var abort = false;
  document.addEventListener("visibilitychange", function (event) {
    if (document.hidden) {
      
      if (abort) {
             return;
    }

    else { alert("Bạn đã thoát khỏi màn hình " + luotthoat +" lần");
      luotthoat = luotthoat + 1;
     }
   }
    
    });

    }




    else{
      return;
    }

}