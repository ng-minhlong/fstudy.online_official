<?php
/*
 * Template Name: Doing Template Shadowing
 * Template Post Type: shadowing
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    echo '
    <script>
           
        var Currentusername = "' . $username . '";
    
    </script>
    ';

$custom_number = get_query_var('id_test');
//$custom_number = get_post_meta($post_id, '_shadowing_custom_number', true);
// Database credentials (update with your own database details)
  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_test = "SELECT * FROM shadowing_question WHERE id_test = ?";


$stmt_test = $conn->prepare($sql_test);
$stmt_test->bind_param("i", $custom_number); // 'i' is used for integer
$stmt_test->execute();
$result_test = $stmt_test->get_result();




// Query to fetch token details for the current username
$sql_user = "SELECT token, token_use_history, token_practice
         FROM user_token 
         WHERE username = ?";
$site_url = get_site_url();

echo '
<script>

var siteUrl = "' . $site_url .'";
var id_test = "' . $id_test . '";
</script>
';



if ($result_test->num_rows > 0) {
    // Lấy các ID từ question_choose (ví dụ: "1001,2001,3001")
    $data = $result_test ->fetch_assoc();
    $testname = $data['testname'];
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];

    $id_video = $data['id_video'];
    $transcript = $data['transcript'];
    $type_test = $data['type_test'];



    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    get_header();
    $stmt_user = $conn->prepare($sql_user);
    if (!$stmt_user) {
        die("Error preparing statement 2: " . $conn->error);
    }

    $stmt_user->bind_param("s", $current_username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $token_data = $result_user->fetch_assoc();
        $token = $token_data['token'];
        $token_practice = $token_data['token_practice'];

        $token_use_history = $token_data['token_use_history'];

        echo "<script>//console.log('Token: $token_practice, Token Use History: $token_use_history, Mày tên: $current_username');</script>";
       

    } else {
        echo "Lỗi đề thi";
        
    }


      
            $permissiveManagement = json_decode($permissive_management, true);
            
            // Chuyển mảng PHP thành JSON string để có thể in trong console.log
            echo "<script> 
                    console.log('$permissive_management');
                </script>";
            
            
            $foundUser = null;
            if (!empty($permissiveManagement)) {
                foreach ($permissiveManagement as $entry) {
                    if ($entry['username'] === $current_username) {
                        $foundUser = $entry;
                        break;
                    }
                }
            }
        
            $premium_test = "False"; // Default value
            if ($foundUser != null && $foundUser['time_left'] > 0 || $token_need == 0) {
                if ($token_need > 0) {
                    $premium_test = "True";
                }
            
            
                echo '<script>
                let premium_test = "' . $premium_test . '";
                let token_need = "' . $token_need . '";
                let change_content = "' . $testname . '";
                let testname = "' . $testname . '";
                let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
            </script>';
            






echo '
<script>
const rawTranscript = [' . $transcript . '];


console.log(rawTranscript);
</script>
';


// Đóng kết nối
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shadowing Exercise</title>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  </head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        #transcript {
            font-size: 18px;
            margin-top: 20px;
        }
        .controls {
            margin-top: 20px;
        }
        /* Import Google Font - Poppins */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
} */
body {
    
    top: 0px !important; 
    margin: 0;


 font-family: sf pro text, -apple-system, BlinkMacSystemFont, Roboto,
     segoe ui, Helvetica, Arial, sans-serif, apple color emoji,
     segoe ui emoji, segoe ui symbol;
     text-align: center;
     justify-content: center;
}


.container1{
    width: 70%;
    height: 600px;
    display: contents;
    align-items: center;
}

#pronunciation-div, #translate-div, #speaking-check-div{
  display: none;
}
#tips, #refreshButton, #important-warning {
  display: inline-block;
  vertical-align: middle;
}

#warning{
  background-color: rgba(188, 204, 151, 0.726);
}

#refreshButton {
  cursor: pointer;
  margin-left: 10px; /* Optional: Add some spacing between the div and the button */
}



/* CSS */
.button-1 {
  appearance: none;
  background-color: #2ea44f;
  border: 1px solid rgba(27, 31, 35, .15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
  box-sizing: border-box;
  font-size: 16px;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
  font-weight: 600;
  line-height: 20px;
  padding: 6px 16px;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
}

.button-1:focus:not(:focus-visible):not(.focus-visible) {
  box-shadow: none;
  outline: none;
}

.button-1:hover {
  background-color: #2c974b;
}

.button-1:focus {
  box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
  outline: none;
}

.button-1:disabled {
  background-color: #94d3a2;
  border-color: rgba(27, 31, 35, .1);
  color: rgba(255, 255, 255, .8);
  cursor: default;
}

.button-1:active {
  background-color: #298e46;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}

/* CSS */
.button-10 {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 6px 14px;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
  border-radius: 6px;
  border: none;

  color: #fff;
  background: linear-gradient(180deg, #4B91F7 0%, #367AF6 100%);
   background-origin: border-box;
  box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-10:focus {
  box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
  outline: 0;
}


/* CSS */
.button-4 {
  appearance: none;
  background-color: #FAFBFC;
  border: 1px solid rgba(27, 31, 35, 0.15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
  box-sizing: border-box;
  color: #24292E;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system, system-ui, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
  font-size: 14px;
  font-weight: 500;
  line-height: 20px;
  list-style: none;
  padding: 6px 16px;
  position: relative;
  transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
  word-wrap: break-word;
}

.button-4:hover {
  background-color: #F3F4F6;
  text-decoration: none;
  transition-duration: 0.1s;
}

.button-4:disabled {
  background-color: #FAFBFC;
  border-color: rgba(27, 31, 35, 0.15);
  color: #959DA5;
  cursor: default;
}

.button-4:active {
  background-color: #EDEFF2;
  box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
  transition: none 0s;
}

.button-4:focus {
  outline: 1px transparent;
}

.button-4:before {
  display: none;
}

.button-4:-webkit-details-marker {
  display: none;
}

#content1
{
  height: 100%;
  box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.2);
  margin: auto;
  padding: 10px;
  position: relative;
  flex-direction: row;
}

.content-side {
  display:none;
}


.video-div{
}
.app-div{
  text-align: center;
  justify-content: center;
}

#before-content {
   
    border: #f0e6e6;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
   height: 40px;
    background-color: #dfd7d7;

    margin: auto;
    padding: 10px;
    

}


.left-group, .right-group {
  display: flex;
  align-items: center;
}
.question-number {
  margin: 0 10px;
}
.tooltip {
  position: relative;
  display: inline-block;
  cursor: pointer; /* Optional: Changes cursor to pointer to indicate it's interactive */
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 160px; /* Increased width to better fit the content */
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%; /* Positioning above the tooltip parent */
  left: 50%;
  margin-left: -80px; /* Centering the tooltip */
  opacity: 0;
  transition: opacity 0.3s;
}

.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}

.far {
  margin-right: 5px;
  color: #00F;
  padding: 7px;
}

/* The actual popup */
.popup .popuptext {
  width: 160px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 8px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -80px;
}

/* Popup arrow */
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

/* Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
  from {opacity: 0;} 
  to {opacity: 1;}
}

@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity:1 ;}
}

@media (max-width: 768px) {
    #content1 {
        flex-direction: column;
    }
    .left-side .right-side {
        width: 100%;
    }
}



#main-paragraph {
    font-size: 1.2em;
    margin-bottom: 20px;
}

#navigation-buttons {
    margin-top: 20px;
}

.input-ans {
    height: 50px;
    width: 100%;
    border-radius: 8px;
    background: transparent;
    border-bottom: 1px solid #000000;
    padding: 2px 5px;

}


button {
    padding: 10px 20px;
    margin: 0 10px;
    font-size: 1em;
    cursor: pointer;
}



/* Header */
.header {
    overflow: hidden;
    background-color: #f1f1f1;
    padding: 5px 10px;
  }
  
  .header a {
    float: left;
    color: black;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    font-size: 18px; 
    line-height: 25px;
    border-radius: 4px;
  }
  
  .header a.logo {
    font-size: 25px;
    font-weight: bold;
  }
  
  .header a:hover {
    background-color: #ddd;
    color: black;
  }
  
  .header a.active {
    background-color: dodgerblue;
    color: white;
  }
  
  .header-right {
    float: right;
  }


  @media screen and (max-width: 500px) {
    .header a {
      float: none;
      /*display: block; */
      text-align: left;
    }
    
    .header-right {
      float: none;
    }
  }
  

  /* Translation api */

  .wrapper{
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  .wrapper .text-input{
    display: flex;
    border-bottom: 1px solid #ccc;
  }
  .text-input .to-text{
    border-radius: 0px;
    border-left: 1px solid #ccc;
  }
  .text-input {
    height: 100px;
    width: 100%;
    border: none;
    outline: none;
    resize: none;
    background: none;
    font-size: 18px;
    padding: 10px 15px;
    border-radius: 5px;
  }
.textarea{
  
  display: block;
    width: 100%;
    font-weight: 400;
    appearance: none;
    border-radius: var(--bs-border-radius);
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    padding: 10px !important;
    margin-bottom: 4px !important;
    resize: none;
    z-index: 1;
    position: relative;
    font-family: sans-serif;
    font-size: 100%;
    line-height: 1.15;
    margin: 0;
}
  .controls, li, .icons, .icons i{
    display: flex;
    align-items: center;
  }
  .controls{
    list-style: none;
    padding: 12px 15px;
  }
  .controls .row .icons{
    width: 38%;
  }
  .controls .row .icons i{
    width: 50px;
    color: #adadad;
    font-size: 14px;
    cursor: pointer;
    transition: transform 0.2s ease;
    justify-content: center;
  }
  .controls .row.from .icons{
    padding-right: 15px;
    border-right: 1px solid #ccc;
  }
  .controls .row.to .icons{
    padding-left: 15px;
    border-left: 1px solid #ccc;
  }
  .controls .row select{
    color: #333;
    border: none;
    outline: none;
    font-size: 18px;
    background: none;
    padding-left: 5px;
  }
  .text-input textarea::-webkit-scrollbar{
    width: 4px;
  }
  .controls .row select::-webkit-scrollbar{
    width: 8px;
  }
  .text-input textarea::-webkit-scrollbar-track,
  .controls .row select::-webkit-scrollbar-track{
    background: #fff;
  }
  .text-input textarea::-webkit-scrollbar-thumb{
    background: #ddd;
    border-radius: 8px;
  }
  .controls .row select::-webkit-scrollbar-thumb{
    background: #999;
    border-radius: 8px;
    border-right: 2px solid #ffffff;
  }
  .controls .exchange{
    color: #adadad;
    cursor: pointer;
    font-size: 16px;
    transition: transform 0.2s ease;
  }
  .controls i:active{
    transform: scale(0.9);
  }
  #pronunciation-zone, #translate-sentence, #speaking-check {
    border: 1px solid #ccc;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    
  }
  @media (max-width: 660px){
    .container{
      padding: 20px;
    }
    .wrapper .text-input{
      flex-direction: column;
    }
    .text-input .to-text{
      border-left: 0px;
      border-top: 1px solid #ccc;
    }
    .text-input textarea{
      height: 200px;
    }
    .controls .row .icons{
      display: none;
    }
    .container button{
      padding: 13px;
      font-size: 16px;
    }
    .controls .row select{
      font-size: 16px;
    }
    .controls .exchange{
      font-size: 14px;
    }
  }



  #settingsButton {
    /*padding: 10px 20px;
    /*background-color: #007BFF; 
    color: white;*/
    right: 10;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
/* Style for the popup */
#settingsPopup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 20px;
    width: 300px;
    z-index: 1000;
}
/* Style for the close button */
.closeButton {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}
/* Style for the backdrop */
#backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
}


/*      Change scrollbar style   */

#right-side::-webkit-scrollbar {
  width: 13px;
}

#right-side::-webkit-scrollbar-track {
  border-radius: 8px;
  background-color: #e7e7e7;
  border: 1px solid #cacaca;
}

#right-side::-webkit-scrollbar-thumb {
  border-radius: 8px;
border: 3px solid transparent;
 background-clip: content-box;
  background-color: #685757;
}

#left-side::-webkit-scrollbar {
  width: 13px;
}

#left-side::-webkit-scrollbar-track {
  border-radius: 8px;
  background-color: #e7e7e7;
  border: 1px solid #cacaca;
}

#left-side::-webkit-scrollbar-thumb {
  border-radius: 8px;
border: 3px solid transparent;
 background-clip: content-box;
  background-color: #685757;
}

.bf-content-setting-class{
  display: flex;
  align-items: center;
}
.opt-player{
  display: flex;
  align-items: center;
}



/* CSS */
.button-4 {
  appearance: none;
  background-color: #FAFBFC;
  border: 1px solid rgba(27, 31, 35, 0.15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
  box-sizing: border-box;
  color: #24292E;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system, system-ui, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
  font-size: 14px;
  font-weight: 500;
  line-height: 20px;
  list-style: none;
  padding: 6px 16px;
  position: relative;
  transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
  word-wrap: break-word;
}

.button-4:hover {
  background-color: #F3F4F6;
  text-decoration: none;
  transition-duration: 0.1s;
}

.button-4:disabled {
  background-color: #FAFBFC;
  border-color: rgba(27, 31, 35, 0.15);
  color: #959DA5;
  cursor: default;
}

.button-4:active {
  background-color: #EDEFF2;
  box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
  transition: none 0s;
}

.button-4:focus {
  outline: 1px transparent;
}

.button-4:before {
  display: none;
}

.button-4:-webkit-details-marker {
  display: none;
}

<!-- HTML !-->

/* CSS */
.button-11 {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 6px 14px;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
  border-radius: 6px;
  border: none;

  color: #fff;
  background: linear-gradient(180deg,rgb(143, 150, 160) 0%, #367AF6 100%);
   background-origin: border-box;
  box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-11:focus {
  box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
  outline: 0;
}



.button-11 {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 6px 14px;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
  border-radius: 6px;
  border: none;

  color: #fff;
  background: gray;
   background-origin: border-box;
  box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-11:focus {
  box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
  outline: 0;
}



#player {
  position: relative;

  height: 400px;
  width: 100%;
}
#hide-player{
  height: 400px;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Hoặc đặt hình ảnh */
  z-index: 10;
}
#video-container {
  position: relative;
}

#test-prepare {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: fixed; /* Giữ loader cố định giữa màn hình */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* Căn giữa theo cả chiều ngang và dọc */
    height: 200px;
    z-index: 1001; /* Đảm bảo loader ở trên các phần tử khác */
}
/* HTML: <div class="loader"></div> */
.loader {
  width: 70px;
  aspect-ratio: 1;
  border-radius: 50%;
  border: 8px solid #514b82;
  animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
}

.start_test {
  appearance: none;
  background-color: #2ea44f;
  border: 1px solid rgba(27, 31, 35, .15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
  font-size: 20px;
  font-weight: 600;
  line-height: 20px;
  padding: 6px 16px;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
}

.start_test:focus:not(:focus-visible):not(.focus-visible) {
  box-shadow: none;
  outline: none;
}
.icon {
    width: 20px;
    height: 20px;
    vertical-align: middle;
}

.start_test:hover {
  background-color: #2c974b;
}

.start_test:focus {
  box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
  outline: none;
}

.start_test:disabled {
  background-color: #94d3a2;
  border-color: rgba(27, 31, 35, .1);
  color: rgba(255, 255, 255, .8);
  cursor: default;
}

.start_test:active {
  background-color: #298e46;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}
/* Thêm style mới cho thông báo trình duyệt không hỗ trợ */
.browser-warning {
      text-align: center;
      padding: 20px;
      background-color: #fff3cd;
      border: 1px solid #ffeeba;
      border-radius: 8px;
      max-width: 600px;
      margin: 50px auto;
      color: #856404;
    }
    
    .browser-warning h2 {
      color: #856404;
      margin-top: 0;
    }
    
    .supported-browsers {
      margin-top: 15px;
      text-align: left;
      display: inline-block;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
    }
    
.modal-overlay-progress {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-progress {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
    }
    .modal-nav {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .modal-nav button {
        padding: 8px 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .modal-nav button:hover {
        background-color: #45a049;
    }
    
    .modal-nav button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    .close-modal-2 {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
    #colophon.site-footer { display: none !important; }

.word-correct {
    color: green;
}
.word-incorrect {
    color: red;
}
.pronunciation-display {
    font-family: monospace;
    margin: 10px 0;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 5px;
}
.speed-display {
    margin: 10px 0;
    padding: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    font-weight: bold;
}

/* Custom Audio Player Styling */
#audioPlayback {
    width: 100%;
    max-width: 500px;
    height: 50px;
    margin: 20px auto;
    border-radius: 30px;
    background: #f0f0f0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

#audioPlayback::-webkit-media-controls-panel {
    background: linear-gradient(145deg, #f0f0f0, #ffffff);
    border-radius: 30px;
}

#audioPlayback::-webkit-media-controls-current-time-display,
#audioPlayback::-webkit-media-controls-time-remaining-display {
    color: #333;
    font-family: 'Arial', sans-serif;
    font-weight: bold;
}

#audioPlayback::-webkit-media-controls-play-button {
    background-color: #4CAF50;
    border-radius: 50%;
    transform: scale(1.5);
}

#audioPlayback::-webkit-media-controls-play-button:hover {
    background-color: #45a049;
}

#audioPlayback::-webkit-media-controls-timeline {
    background-color: #ddd;
    border-radius: 10px;
    margin: 0 15px;
}

#audioPlayback::-webkit-media-controls-volume-slider {
    background-color: #ddd;
    border-radius: 10px;
    padding: 0 5px;
}

#audioPlayback::-webkit-media-controls-mute-button {
    transform: scale(1.2);
}

#audioPlayback::-webkit-media-controls-volume-slider-container {
    width: 100px;
}

#audioPlayback::-webkit-media-controls-seek-back-button,
#audioPlayback::-webkit-media-controls-seek-forward-button {
    display: none;
}

#audioPlayback::-webkit-media-controls-timeline::-webkit-slider-thumb {
    background-color: #4CAF50;
    border: 2px solid #fff;
    border-radius: 50%;
    width: 15px;
    height: 15px;
}

#audioPlayback::-webkit-media-controls-volume-slider::-webkit-slider-thumb {
    background-color: #4CAF50;
    border: 2px solid #fff;
    border-radius: 50%;
    width: 12px;
    height: 12px;
}

/* Audio player container styling */
.audio-player-container {
    background: #ffffff;
    padding: 15px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    margin: 20px auto;
    max-width: 550px;
    transition: all 0.3s ease;
}

.audio-player-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}
</style>
</head>
<body onload = "main()">



<div class="container1">
   
    <div id = "test-prepare">
        <div class="loader"></div>
        <h3>Your test will begin shortly</h3>
        <div id = "checkpoint" class = "checkpoint">
                <?php
                    if($premium_test == "True"){
                        echo "<script >console.log('Thông báo. Bạn còn {$foundUser['time_left']} lượt làm bài. success ');</script>";
                        echo " <p style = 'color:green'> Bạn còn {$foundUser['time_left']} lượt làm bài này <svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#7ed321' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg> </p> ";
                        echo "<script>console.log('This is premium test');</script>";
                    }
                    else{
                        echo "<script>console.log('This is free test');</script>"; 
                    }
                        ?>
        </div>    
        <div id = "quick-instruction">
            <i>Quick Instruction:<br>
            - If you find any errors from test (image,display,text,...), please let us know by clicking icon <i class="fa-solid fa-bug"></i><br> 
            - Incon <i class="fa-solid fa-circle-info"></i> will give you a guide tour, in which you can understand the structure of test, include test's type, formation and how to answer questions<br>
            - All these two icons are at the right-above side of test.
        </i>

        </div>
        <div style="display: none;" id="date" style="visibility:hidden;"></div>
        <div style="display: none;" id="title-test"><?php echo esc_html($testname);?></div>
        <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html($custom_number);?></div>
        <button  style="display: none;" class ="start_test" id="start_test"  onclick = "pregetStart()">Start test</button>
        <i id = "welcome" style = "display:none">Click Start Test button to start the test now. Good luck</i>


    </div>




    <div id="content1">
        <!--Save Progress Popup-->
        <div class="modal-overlay" id="questionModal">
                    <div class="modal-content">
                        <span class="close-modal-2">&times;</span>
                        <div id="modalQuestionContent"></div>
                        
                    </div>
                </div>


        <div id = 'content-side' class="content-side">


        <div id = "checkpoint" class = "checkpoint">
                <?php
                    if($premium_test == "True"){
                        echo "<script >console.log('Thông báo. Bạn còn {$foundUser['time_left']} lượt làm bài. success ');</script>";
                        echo " <p style = 'color:green'> Bạn còn {$foundUser['time_left']} lượt làm bài này <svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#7ed321' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg> </p> ";
                        echo "<script>console.log('This is premium test');</script>";
                    }
                    else{
                        echo "<script>console.log('This is free test');</script>"; 
                    }
                        ?>
        </div>    
       
        <div class = "video-div">
            <div id ="start-dictation" style="display: none;">
                <div id="video-container">
                  <image id="hide-player" class = "hide-player" src = "http://localhost/contents/uploads/2025/01/StudyAI.com_-1.png"></image>
                  <div id="player" class = "player"></div>
                </div>

              <div class ="opt-player">
                <select name="video-width" id="video-width">
                  <option value="small">Video Size: Small</option>
                  <option value="normal" selected>Video Size: Normal</option>
                  <option value="large">Video Size: Large</option>
                  <option value="extra-large">Video Size: Extra Large</option>
                </select>
                <br>
                <button id="toggle-video-btn">Hide Video</button>
            </div>
            
          </div>
      </div>

      <div class="app-div">
        <div id="transcript"></div>
        <div class = "result">
          <div id= "checkSpeed">Tốc độ</div>
          <div id = "checkPronunciation"></div>
        </div>
          <div class = "bf-content-setting-class" >
                  <svg id="previous"  xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                  </svg>
                  <div class="question-number" id="question-number"></div>
                  <svg id="next" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                  </svg>
          </div>
                <button id="listenAgain" class="button-4" role="button"><i class="fa-solid fa-play"></i> Listen Again</button><br>
                <button id="saveProgress" class="button-4" role="button" onclick = "saveProgress()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> Save Progress</button><br>

                <textarea class = "textarea" type="text" id="userInput" style = "display:none" placeholder="Enter transcript text here"></textarea>

                
              <div class="controls">
                <button id="checkAnswer" class="button-10" role="button" style = "display:none" >Check Answer</button>
                <button id="listenAgain" class="button-11" role="button"  style = "display:none" >Listen Again</button>
            </div>

            <div class="record-controls">
              <button id="startRecord"><svg  class="icon" version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .isometric_zeven{fill:#FF7344;} .isometric_tien{fill:#7BD6C4;} .isometric_elf{fill:#72C0AB;} .isometric_twaalf{fill:#569080;} .isometric_dertien{fill:#225B49;} .st0{fill:#F28103;} .st1{fill:#BE1E2D;} .st2{fill:#F05A28;} .st3{fill:#F29227;} .st4{fill:#F8F7C5;} .st5{fill:#F5DF89;} .st6{fill:#AD9A74;} .st7{fill:none;} .st8{fill:#F2D76C;} .st9{fill:#72C0AB;} .st10{fill:#7BD6C4;} .st11{fill:#569080;} </style> <g> <path class="isometric_dertien" d="M21.496,11.298l-3.174-3.174l-2.161-2.161c-1.562-1.562-4.095-1.562-5.657,0 c-1.562,1.562-1.562,4.095,0,5.657l2.179,2.179l3.156,3.156c1.562,1.562,4.095,1.562,5.657,0 C23.058,15.392,23.058,12.86,21.496,11.298z"></path> <path class="isometric_tien" d="M18.987,20.926c-2.306-0.004-3.994,1.11-3.987,2.302c0.016,3.027,8.016,3.036,8,0.014 C22.993,22.053,21.307,20.931,18.987,20.926z"></path> <path class="isometric_twaalf" d="M20.83,11.727c-0.29-0.186-0.84-0.471-0.991-0.555c-0.703-0.391-2.563,0.833-2.563,2.75 c0,0.469,0.137,0.801,0.374,0.968c0.168,0.118,0.771,0.425,0.995,0.558c0.263,0.216,0.664,0.21,1.15-0.07 c0.933-0.539,1.513-1.616,1.513-2.607C21.308,12.221,21.131,11.862,20.83,11.727z"></path> <path class="isometric_elf" d="M18.308,14.503c0-1.019,0.63-2.098,1.513-2.608c0.883-0.51,1.487-0.143,1.487,0.876 c0,0.991-0.58,2.068-1.513,2.607C18.913,15.887,18.308,15.494,18.308,14.503z M19.044,10.439c-0.085,0.323-0.218,0.648-0.387,0.965 c0.451-0.29,0.906-0.386,1.183-0.232c0.109,0.061,0.43,0.228,0.708,0.386c0.101-0.624-0.016-1.131-0.286-1.496l-1.436-1.436 C19.139,9.052,19.246,9.668,19.044,10.439z M17.65,14.891c-0.237-0.167-0.374-0.499-0.374-0.968c0-0.316,0.051-0.612,0.138-0.886 c-0.707,0.688-1.554,1.225-2.396,1.455c-0.775,0.212-1.4,0.117-1.833-0.19l1.436,1.436c0.432,0.307,1.058,0.402,1.833,0.19 c0.546-0.149,1.089-0.436,1.597-0.805C17.88,15.03,17.724,14.942,17.65,14.891z"></path> <path class="st11" d="M19.011,22.448v-0.056C19.01,22.4,19,22.407,19,22.415C19,22.426,19.009,22.437,19.011,22.448z"></path> <path class="isometric_twaalf" d="M23,23.243v1.388h-0.011c0.171,3.089-8.085,3.106-7.98,0H15v-1.403 C15.016,26.257,23.016,26.263,23,23.243z M18.048,22.241v0.509c-0.038,1.128,2.962,1.121,2.9,0l0.004-0.504 C20.958,23.343,18.054,23.34,18.048,22.241z M21.896,14.185l-1.913-1.111c-0.299,0.044-0.62,0.49-0.525,0.852l1.434,0.833 c-0.005,1,0.235,4.641-2.121,4.641c-1.682,0-3.461-1.943-4.45-3.963c-1.066-1.066-1.293-1.293-1.706-1.706 c0.981,3.574,3.638,6.67,6.156,6.67c0.082,0,0.162-0.003,0.24-0.009v2c0,0.448,1.044,0.403,0.988,0.003v-2.241 C22.06,19.206,21.888,15.78,21.896,14.185z"></path> <path class="isometric_zeven" d="M16.44,23.728c-0.28-0.28-0.195-0.591,0.19-0.694c0.385-0.103,0.925,0.04,1.205,0.321 c0.28,0.28,0.195,0.591-0.19,0.694C17.259,24.152,16.72,24.008,16.44,23.728z"></path> </g> </g></svg>Start Record</button>
              <button id="stopRecord" disabled> <svg  class="icon" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M512 1024C229.7 1024 0 794.3 0 512S229.7 0 512 0s512 229.7 512 512-229.7 512-512 512z m0-938.7C276.7 85.3 85.3 276.7 85.3 512S276.7 938.7 512 938.7 938.7 747.3 938.7 512 747.3 85.3 512 85.3z" fill="#3688FF"></path><path d="M640 682.7H384c-23.6 0-42.7-19.1-42.7-42.7V384c0-23.6 19.1-42.7 42.7-42.7h256c23.6 0 42.7 19.1 42.7 42.7v256c0 23.6-19.1 42.7-42.7 42.7z m-213.3-85.4h170.7V426.7H426.7v170.6z" fill="#5F6379"></path></g></svg>Stop Record</button>
            </div>
            <div id="recordedText"></div>
            <div id="resultFeedback"></div>

            <div id="confidentialLevel"></div>
            <div class="audio-player-container">
                <audio id="audioPlayback" controls></audio>
            </div>





            
              <button id="hintButton"  style = "display:none" >Show Hint</button>
              <div id="hint"  style = "display:none" ></div>


          </div>
      </div>
  </div>

<!--<div id ="ads" style="display:  flex;justify-content: center; align-items: center;">
    DEV TAG: Powered by Nguyen Minh Long
</div> -->
</div>


   
    

  
<script>
  
  
  document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggle-video-btn");
    const player = document.getElementById("player");
    const hidePlayer = document.getElementById("hide-player");

    // Ban đầu, ẩn hide-player
    hidePlayer.style.display = "none";

    toggleButton.addEventListener("click", function () {
      if (hidePlayer.style.display === "none") {
        // Hiện hide-player (che player), đổi nội dung nút
        hidePlayer.style.display = "block";
        toggleButton.textContent = "Show Video";
      } else {
        // Ẩn hide-player (bỏ che), đổi nội dung nút
        hidePlayer.style.display = "none";
        toggleButton.textContent = "Hide Video";
      }
    });
  });
  var modal = document.getElementById("questionModal");
  var closeBtn = document.querySelector(".close-modal-2");
                        
  closeBtn.onclick = function() {
    modal.style.display = "none";
  }
                                      
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
     }
  }
  
  const currentDate = new Date();

  const day = currentDate.getDate();
  const month = currentDate.getMonth() + 1; // Adding 1 because getMonth() returns zero-based month index
  const year = currentDate.getFullYear();
  const dateElement = `${year}-${month}-${day}`;

  function saveProgress() {
    const modal = document.getElementById("questionModal");
    const modalContent = document.getElementById("modalQuestionContent");
    
    modalContent.innerHTML = `
        <div>
            <p><strong>Loại test:</strong> shadowing</p>
            <p><strong>Progress:</strong> ${currentQuestion}</p>
            <p><strong>ID Test:</strong> ${id_test}</p>
            <p><strong>Date:</strong> ${dateElement.value || new Date().toLocaleString()}</p>
        </div>
        <button id="saveProgressBtn">Lưu progress</button>
        <div id="saveResult" style="margin-top: 10px;"></div>
        <div id="deleteProgressSection" style="display: none; margin-top: 20px;">
            <button id="showDeleteProgressBtn" style="background-color: #f44336; color: white;">Xóa bớt record</button>
            <div id="progressList" style="margin-top: 10px; display: none;"></div>
        </div>
    `;
    modal.style.display = "flex";

    document.getElementById('saveProgressBtn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('saveResult');
        const deleteSection = document.getElementById('deleteProgressSection');

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        resultDiv.innerHTML = '<p>Đang xử lý...</p>';
        var percentCompleted = Math.floor(currentQuestion / totalQuestions * 100);

        const data = {
            username: Currentusername,
            id_test: id_test,
            testname: testname,
            progress: currentQuestion,
            percent_completed: percentCompleted,
            type_test: "shadowing",
            date: dateElement
        };

        fetch(`${siteUrl}/api/v1/update-progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                resultDiv.innerHTML = '<p style="color: green;">Lưu thành công!</p>';
            } else {
                if (result.message.includes('tối đa (20)')) {
                    deleteSection.style.display = 'block';
                    loadProgressList();
                }
                throw new Error(result.message || 'Lỗi khi lưu progress');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = `<p style="color: red;">Lỗi: ${error.message}</p>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Lưu progress';
        });
    });

    // Xử lý hiển thị danh sách progress để xóa
    document.getElementById('showDeleteProgressBtn')?.addEventListener('click', function() {
        const progressList = document.getElementById('progressList');
        progressList.style.display = progressList.style.display === 'none' ? 'block' : 'none';
    });
}

function loadProgressList() {
    fetch(`${siteUrl}/api/v1/get-all-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const progressList = document.getElementById('progressList');
            progressList.innerHTML = '<h4>Danh sách progress (đã lưu ' + result.progress_number + '/20):</h4>';
            
            if (result.data.length === 0) {
                progressList.innerHTML += '<p>Không có progress nào được lưu</p>';
                return;
            }

            // Sắp xếp theo date mới nhất trước
            const sortedProgress = result.data.sort((a, b) => {
                return new Date(b.date) - new Date(a.date);
            });

            sortedProgress.forEach(item => {
                const progressItem = document.createElement('div');
                progressItem.className = 'progress-item';
                progressItem.style.display = 'flex';
                progressItem.style.justifyContent = 'space-between';
                progressItem.style.alignItems = 'center';
                progressItem.style.margin = '5px 0';
                progressItem.style.padding = '10px';
                progressItem.style.backgroundColor = '#f5f5f5';
                progressItem.style.borderRadius = '5px';
                
                progressItem.innerHTML = `
                    <div style="flex: 1;">
                        <div><strong>ID Test:</strong> ${item.id_test}</div>
                        <div><strong>Loại test:</strong> ${item.type_test || 'N/A'}</div>
                        <div><strong>Tiến độ:</strong> ${item.progress} (${item.percent_completed || '0'}%)</div>
                        <div><strong>Ngày lưu:</strong> ${item.date}</div>
                    </div>
                    <button class="delete-progress-btn" data-id="${item.id_test}" 
                            style="background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                        Xóa
                    </button>
                `;
                
                progressList.appendChild(progressItem);
            });

            // Thêm sự kiện click cho các nút xóa
            document.querySelectorAll('.delete-progress-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idToDelete = this.getAttribute('data-id');
                    deleteProgressRecord(idToDelete);
                });
            });
        } else {
            throw new Error(result.message || 'Lỗi khi tải danh sách progress');
        }
    })
    .catch(error => {
        console.error('Error loading progress list:', error);
        const progressList = document.getElementById('progressList');
        progressList.innerHTML = `<p style="color: red;">Lỗi: ${error.message}</p>`;
    });
}
function deleteProgressRecord(idToDelete) {
    if (!confirm('Bạn có chắc chắn muốn xóa progress này?')) return;

    fetch(`${siteUrl}/api/v1/delete-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername,
            id_test: idToDelete
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Hiển thị thông báo thành công
            const notification = document.createElement('div');
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.backgroundColor = '#4CAF50';
            notification.style.color = 'white';
            notification.style.padding = '15px';
            notification.style.borderRadius = '5px';
            notification.style.zIndex = '1000';
            notification.textContent = 'Xóa thành công!';
            
            document.body.appendChild(notification);
            
            // Tự động ẩn thông báo sau 3 giây
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => document.body.removeChild(notification), 500);
            }, 3000);

            // Load lại danh sách
            loadProgressList();
        } else {
            throw new Error(result.message || 'Lỗi khi xóa progress');
        }
    })
    .catch(error => {
        console.error('Error deleting progress:', error);
        alert('Lỗi khi xóa: ' + error.message);
    });
}

  // Hàm kiểm tra trình duyệt Opera
function isOperaBrowser() {
    const userAgent = navigator.userAgent.toLowerCase();
    return userAgent.includes('opera') || userAgent.includes('opr/');
}



  function main() {  
    document.getElementById("test-prepare").style.display = "block";

        console.log("Passed Main");
        
        setTimeout(function() {
            console.log("Show Test!");
            
            document.getElementById("start_test").style.display = "block";
            document.getElementById("welcome").style.display = "block";
        }, 1000);
    }

    function pregetStart() {
        if(premium_test == "False") {
            console.log("Cho phép làm bài")
        }
        else {
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
                    time_left: time_left,
                    id_test: id_test,
                    table_test: 'shadowing_question',
                },
                success: function(response) {
                    console.log("Server response:", response);
                },
                error: function(error) {
                    console.error("Error updating time_left:", error);
                }
            });
        }
        getStart();
    }

    function getStart() {    
        document.getElementById("test-prepare").style.display = "none";
        document.getElementById("content-side").style.display = "block";
        document.getElementById("start-dictation").style.display = "block";
        navigateTranscript(0);
    }

    // JSON transcript data (replaced with the correct format)
    const transcript = rawTranscript.map(item => {
        return { 
            ...item, 
            end: item.start + item.duration 
        };
    });
    
    const totalQuestions = transcript.length;
    let currentQuestion = 1;
    let player;
    let currentTranscriptIndex = 0;
    let timeoutId;
    let mediaRecorder;
    let audioChunks = [];

    // Update the question number display
    function updateQuestionNumber() {
        document.getElementById('question-number').textContent = `${currentQuestion}/${totalQuestions}`;
    }

    function showHint() {
        const currentItem = transcript[currentTranscriptIndex];
        const text = currentItem.text;
        let hint = text.split(' ').map(word => {
            return word.split('').map((char, index) => {
                return index === 0 ? char : '_';
            }).join('');
        }).join('  ');

        document.getElementById('hint').textContent = hint;
    }

    document.getElementById('hintButton').addEventListener('click', showHint);

    // YouTube Player Setup
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '315',
            width: '560',
            videoId: '<?php echo esc_html($id_video);?>',
            playerVars: { 
                enablejsapi: 1, 
                cc_load_policy: 0
            },
            events: {
                onReady: onPlayerReady
            }
        });
    }

    
    function updateTranscript() {
        const currentItem = transcript[currentTranscriptIndex];
        if (currentItem) {
            clearTimeout(timeoutId);
            document.getElementById('transcript').innerText = currentItem.text;
            player.seekTo(currentItem.start, true);
            player.playVideo();
            const duration = currentItem.duration * 1000;
            timeoutId = setTimeout(() => player.pauseVideo(), duration);
            updateQuestionNumber();
        }
    }
    function navigateTranscript(direction) {
        currentQuestion += direction;
        if (currentQuestion < 1) currentQuestion = 1;
        if (currentQuestion > totalQuestions) currentQuestion = totalQuestions;
        currentTranscriptIndex = currentQuestion - 1;
        updateTranscript();
    }

    document.getElementById('listenAgain').addEventListener('click', () => {
        const currentItem = transcript[currentTranscriptIndex];
        if (currentItem) {
            clearTimeout(timeoutId);
            player.seekTo(currentItem.start, true);
            player.playVideo();
            const duration = currentItem.duration * 1000;
            timeoutId = setTimeout(() => player.pauseVideo(), duration);
        }
    });

    function sanitizeInput(input) {
        if (input === undefined || input === null) {
            return '';
        }
        return input.replace(/[^a-zA-Z0-9\s]/g, "").toLowerCase().trim();
    }

    function onPlayerReady() {
        updateTranscript();
    }

    function checkAnswer() {
        const userInput = document.getElementById('userInput').value;
        const currentItem = transcript[currentTranscriptIndex];
        if (sanitizeInput(userInput) === sanitizeInput(currentItem.text)) {
            alert("Correct!");
        } else {
            alert("Incorrect. Try again.");
        }
    }

    // Speech Recognition and Recording Setup
    document.getElementById('startRecord').addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);

        audioChunks = [];
        mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = async () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioURL = URL.createObjectURL(audioBlob);
            const recordingStartTime = Date.now();

            const audioElement = document.getElementById('audioPlayback');
            audioElement.src = audioURL;
            audioElement.style.display = 'block';
            const transcriptData = document.getElementById("transcript").innerText;

            // Send to STT API
            const formData = new FormData();
            formData.append("audio", audioBlob, "recording.webm");
            formData.append("lang", "en");
            formData.append("text", `${transcriptData}`);

            try {
                document.getElementById('recordedText').textContent = "Processing...";
                const response = await fetch("<?php echo URL_PYTHON_API; ?>/shadowingResult", {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();
                console.log("Speech-to-text result:", result);
                
                if (result.pronunciation_analysis) {
                    // Calculate speaking rate
                    const recordingEndTime = Date.now();
                    const durationInSeconds = (recordingEndTime - recordingStartTime) / 1000;
                    const totalCharacters = result.input_text.replace(/[^a-zA-Z]/g, '').length;
                    const speakingRate = (totalCharacters / durationInSeconds).toFixed(2);
                    
                    // Display speaking rate
                    document.getElementById('checkSpeed').innerHTML = `
                        <div class="speed-display">
                            Speaking Rate: ${speakingRate} characters per second
                        </div>
                    `;

                    // Process pronunciation analysis
                    const words = result.pronunciation_analysis.words;
                    let transcriptHtml = '';
                    let pronunciationHtml = '';

                    words.forEach(word => {
                        const isWordCorrect = word.comparison.every(phone => phone.match);
                        const wordClass = isWordCorrect ? 'word-correct' : 'word-incorrect';
                        transcriptHtml += `<span class="${wordClass}">${word.word}</span> `;
                        
                        // Add pronunciation for each word
                        pronunciationHtml += `<span class="${wordClass}">[${word.user_phones.join(' ')}]</span> `;
                    });

                    // Update transcript with colored words
                    document.getElementById('transcript').innerHTML = transcriptHtml;
                    
                    // Display pronunciation
                    document.getElementById('checkPronunciation').innerHTML = `
                        <div class="pronunciation-display">
                            Pronunciation: ${pronunciationHtml}
                        </div>
                    `;

                    // Display recognized text
                    document.getElementById('recordedText').textContent = `Recognized: ${result.recognized_text}`;
                    
                    // Display confidence score
                    document.getElementById('confidentialLevel').textContent = `Confidence: ${(result.confidence * 100).toFixed(2)}%`;
                } else {
                    document.getElementById('recordedText').textContent = "Could not analyze pronunciation";
                }
            } catch (error) {
                console.error('Error with STT API:', error);
                document.getElementById('recordedText').textContent = "Error processing speech";
            }
        };

        mediaRecorder.start();
        document.getElementById('startRecord').disabled = true;
        document.getElementById('stopRecord').disabled = false;
        document.getElementById('recordedText').textContent = "Recording...";

    } catch (error) {
        console.error('Error accessing microphone', error);
    }
});

document.getElementById('stopRecord').addEventListener('click', () => {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        document.getElementById('startRecord').disabled = false;
        document.getElementById('stopRecord').disabled = true;
    }

            
            if (recognition) {
                recognition.stop();
            }

            // Automatically check the answer after stopping recording
            const spokenText = document.getElementById('recordedText').textContent;
            const currentItem = transcript[currentTranscriptIndex];
            
            if (spokenText && currentItem) {
                const sanitizedSpoken = sanitizeInput(spokenText);
                const sanitizedCorrect = sanitizeInput(currentItem.text);
                
                const resultDiv = document.getElementById('resultFeedback');
                if (sanitizedSpoken === sanitizedCorrect) {
                    resultDiv.textContent = "Correct!";
                    resultDiv.style.color = "green";
                } else {
                    resultDiv.textContent = "Incorrect. The correct answer is: " + currentItem.text;
                    resultDiv.style.color = "red";
                }
            }
        }
    );

    // Navigation buttons
    document.getElementById('previous').addEventListener('click', () => navigateTranscript(-1));
    document.getElementById('next').addEventListener('click', () => navigateTranscript(1));
    document.getElementById('checkAnswer').addEventListener('click', checkAnswer);
</script>
</body>
</html>


<?php


}
else{
    get_header();
    if (!$foundUser) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3>Bạn chưa mua đề thi này</h3>";     
        } 

    else if ($foundUser['time_left'] <= 0) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3> Bạn đã từng mua test này nhưng số lượt làm test này đã hết rồi, vui lòng mua thêm token<i class='fa-solid fa-face-sad-tear'></i></h3>";
    }

    echo"
            <p> Bạn đang có: $token_practice token</p>
            <p> Để làm test này bạn cần $token_need token. Bạn sẽ được làm test này $time_allow lần </p>
            <p class = 'info-buy'>Bạn có muốn mua $time_allow lượt làm test này với $token_need không ?</button>
                <div class='button-group'>
                    <button class='process-token' onclick='preProcessToken()'>Mua ngay</button>
                    <button style = 'display:none' class='close-modal'>Hủy</button>
                </div>  
            </div>
        </div>
        
        <script>
    
    function preProcessToken() {
        if ($token_practice < $token_need) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Bạn không đủ token để mua test này',
                footer: `<a href='${site_url}/dashboard/buy_token/'>Nạp token vào tài khoản ngay</a>`
            });
        } else {
            console.log(`Allow to next step`);
            jQuery.ajax({
                url: `${site_url}/wp-admin/admin-ajax.php`,
                type: 'POST',
                data: {
                    action: 'update_buy_test',
                    type_transaction: 'paid',
                    table: 'shadowing_question',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token_practice',
                    title: 'Buy test $testname with $id_test (Shadowing Test) with $token_need token (Buy $time_allow time do this test)',
                    id_test: id_test
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mua test thành công!',
                        text: 'Trang sẽ được làm mới sau 2 giây.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => location.reload()
                    });
                },
                error: function (error) {
                    console.error('Error updating time_left:', error);
                }
            });
        }
    }
        </script>
        <style>
.checkout-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkout-modal {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    width: 400px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.checkout-modal h3 {
    font-size: 18px;
    color: #333;
}

.checkout-modal p {
    margin: 10px 0;
    color: #555;
}

.checkout-modal .button-group {
    margin-top: 20px;
}

.process-token {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 10px;
    font-size: 14px;
}

.process-token:hover {
    background-color: #0056b3;
}

.close-modal {
    background-color: #ccc;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

.close-modal:hover {
    background-color: #aaa;
}
</style>

<script>
    document.querySelector('.close-modal')?.addEventListener('click', function() {
        document.querySelector('.checkout-modal-overlay').style.display = 'none';
    });
</script>
        ";
        } 
    }
    
 else {
        get_header();
            echo "<p>Không tìm thấy đề thi.</p>";
            exit();
    }

} else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";

}
get_footer();