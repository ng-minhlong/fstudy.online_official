<?php
/*
Template Name: Plan and Target Template
*/

echo '<title>Plan and Target </title>';



// Database credentials (update with your own database details)
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

// Lấy giá trị custom_gift_id từ URL
global $wp_query;
//$custom_gift_id = $wp_query->get('custom_gift_id');
$siteurl = get_site_url();


  global $wpdb;
 // Get the current user
 $current_user = wp_get_current_user();
 $user_id = $current_user->ID; // Lấy user ID
 $username = $current_user->user_login;

 $sql = "SELECT target, plan FROM user_plan_and_target WHERE username = %s";
 $result = $wpdb->get_row($wpdb->prepare($sql, $username));
 $plans = $result ? json_decode($result->plan, true) : [];
 $targets = $result ? json_decode($result->target, true) : [];

 echo "<script> 
 const siteurl = '" . strval($siteurl) . "'; 
  const currentUsername = '" . strval($username) . "'; 

 console.log('siteurl: ' + siteurl);
 </script>";
 
?>
<style>
    /* Import Google font - Poppins */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
  
    .wrapper{
    width: 450px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    .wrapper header{
    display: flex;
    align-items: center;
    padding: 25px 30px 10px;
    justify-content: space-between;
    }
    header .icons{
    display: flex;
    }
    header .icons span{
    height: 38px;
    width: 38px;
    margin: 0 1px;
    cursor: pointer;
    color: #878787;
    text-align: center;
    line-height: 38px;
    font-size: 1.9rem;
    user-select: none;
    border-radius: 50%;
    }
    .icons span:last-child{
    margin-right: -10px;
    }
    header .icons span:hover{
    background: #f2f2f2;
    }
    header .current-date{
    font-size: 1.45rem;
    font-weight: 500;
    }
    .calendar{
    padding: 20px;
    }
    .calendar ul{
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    text-align: center;
    }
    .calendar .days{
    margin-bottom: 20px;
    }
    .calendar li{
    color: #333;
    width: calc(100% / 7);
    font-size: 1.07rem;
    }
    .calendar .weeks li{
    font-weight: 500;
    cursor: default;
    }
    .calendar .days li{
    z-index: 1;
    cursor: pointer;
    position: relative;
    margin-top: 30px;
    }
    .days li.inactive{
    color: #aaa;
    }
    .days li.active{
    color: #fff;
    }
    .days li.selected{
    color: #fff;
    }
    .days li::before{
    position: absolute;
    content: "";
    left: 50%;
    top: 50%;
    height: 40px;
    width: 40px;
    z-index: -1;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    }
    .days li.active::before{
    background: #9B59B6;
    }
    .days li:not(.active):hover::before{
    background:rgb(87, 76, 76);
    }
    .days li.selected::before{
    background:rgb(49, 198, 224);
    }
    .tabs {
        margin-bottom: 20px;
    }

    .tab-button {
        padding: 10px 20px;
        border: 1px solid #ccc;
        background: #f9f9f9;
        cursor: pointer;
        margin-right: 5px;
    }

    .tab-button.active {
        background: #0073aa;
        color: #fff;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
    .plan-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        color: #63E6BE;
        padding: 5px;
        border-radius: 50%;
        font-size: 16px;
    }
    .btn-plan{
        display: none;
    }
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
  }

  .popup-buttons {
    margin-top: 10px;
  }

  .popup-buttons button {
    margin: 5px;
  }

  .hidden {
    display: none;
  }
  .plan-input{
    width: 100%;
  height: 150px;
  padding: 12px 20px;
  box-sizing: border-box;
  border: 2px solid #ccc;
  border-radius: 4px;
  background-color: #f8f8f8;
  font-size: 16px;
  resize: none;
  }

</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

<div class = "content-plan-and-target">
    <div class="tabs">
        <button class="tab-button active" data-tab="tab1">My Schedule</button>
        <button class="tab-button" data-tab="tab2">My Target</button>
    </div>

    <div class="tab-content">
        <div id="tab1" class="tab-pane active">
            <div class="plan-tab">
                <div class = "today-plan">
                    <p>Kế hoạch học hôm nay của bạn</p>
                </div>
                <div class = "upcomming-plan">
                    <p>Kế hoạch học sắp tới của bạn </p>
                <!--Set 3 ngày tiếp (Kế hoạch)-->
                </div>
                <div class = "calender-set">
                    <p>Set lịch học/kế hoạch học của bạn</p>
                    <div class="wrapper">
                        <header>
                            <p class="current-date"></p>
                            <div class="icons">
                            <span id="prev" class="material-symbols-rounded">chevron_left</span>
                            <span id="next" class="material-symbols-rounded">chevron_right</span>
                            </div>
                        </header>
                        <div class="calendar">
                            <ul class="weeks">
                            <li>Sun</li>
                            <li>Mon</li>
                            <li>Tue</li>
                            <li>Wed</li>
                            <li>Thu</li>
                            <li>Fri</li>
                            <li>Sat</li>
                            </ul>
                            <ul class="days"></ul>
                        </div>
                    </div>
                    <div id = "process-area">
                        <button id="addPlanEventBtn">Thêm sự kiện</button>
                        <button id="showPlanEventBtn" class = "btn-plan">Xem kế hoạch</button>
                        <button id="deletePlanEventBtn" class = "btn-plan">Xóa kế hoạch</button>

                        <div id = "information-area"><div>
                        </div>
                    </div>

                    <!-- Popup -->
                    <form id="eventPopup" style="display: none;">
                        <div class="popupContent">
                            <p id="selectedDate">Ngày: </p>
                            <textarea class ="plan-input" id="plan_context" placeholder="Nhập kế hoạch"></textarea>
                            <select id="level">
                                <option value="Important">Quan trọng</option>
                                <option value="Normal">Bình thường</option>
                            </select>
                            <button id="saveEventBtn">Lưu</button>
                            <button id="closePopupBtn">Đóng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <div id="tab2" class="tab-pane">
            <div class = "aim-set">
                <p>Mục tiêu của bạn</p>
                <div class = "target-area" id = "target-area">
                    <div class = "target-container" id = "target-container"></div>
                    <button class = "btn-add-target" id = "btn-add-target" >Thêm mục tiêu</button>
                </div>
                <!--Set môn thi, điểm target, ngày thi-->
            </div>
        </div>
        <!-- Popup Modal -->
        <div id="popup-add-target" class="popup-overlay hidden">
        <div class="popup-content">
            <h2>Thêm Mục Tiêu</h2>
            <label for="target-select">Chọn Target:</label>
            <select id="target-select">
            <option value="Digital SAT">Digital SAT</option>
            <option value="IELTS">IELTS</option>
            <option value="THPTQG">THPTQG</option>
            </select>
            
            <label for="aim-input">Nhập điểm mục tiêu:</label>
            <input type="number" id="aim-input" placeholder="Nhập điểm" min="0" />
            
            <div class="popup-buttons">
                
            <button id="saveTargetButton">Lưu</button>
            <button id="close-popup-btn">Đóng</button>
            </div>
        </div>
        </div>
        
    </div>
</div>
<script>
    const userPlans = <?php echo json_encode($plans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const userTargets = <?php echo json_encode($targets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
 
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Loại bỏ class active khỏi tất cả các nút và tab
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Thêm class active cho nút và tab được chọn
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    });
</script>

<script>
    // Hàm lấy kế hoạch học cho ngày hiện tại và 3 ngày tiếp theo
    function displayPlans(planData) {
        const todayPlanContainer = document.querySelector(".today-plan");
        const upcomingPlanContainer = document.querySelector(".upcomming-plan");

        // Lấy ngày hôm nay và chuẩn hóa định dạng bỏ số 0 trước
        const today = new Date();
        const formattedToday = `${today.getDate()}/${today.getMonth() + 1}/${today.getFullYear()}`;

        // Lọc kế hoạch cho hôm nay
        const todayPlan = planData.find(plan => plan.date === formattedToday);
        console.log("Hôm nay:", formattedToday);






        // Lọc kế hoạch cho 3 ngày tiếp theo
        const upcomingPlans = planData
            .filter(plan => {
                const planDate = new Date(plan.date.split("/").reverse().join("-"));
                const diffDays = (planDate - new Date()) / (1000 * 60 * 60 * 24);
                return diffDays > 0 && diffDays <= 3;
            })
            .sort((a, b) => new Date(a.date.split("/").reverse().join("-")) - new Date(b.date.split("/").reverse().join("-")));

        // Hiển thị kế hoạch hôm nay
        if (todayPlan) {
            const formattedPlanContext = todayPlan.plan_context.replaceAll("\\n", "<br>");

            todayPlanContainer.innerHTML += `
                <div>
                    <p><strong>Ngày:</strong> ${todayPlan.date}</p>
                    <p><strong>Nội dung:</strong> ${formattedPlanContext}</p>
                    <p><strong>Mức độ:</strong> ${todayPlan.level}</p>
                </div>
            `;

        } else {
            todayPlanContainer.innerHTML += `<p>Không có kế hoạch nào cho hôm nay.</p>`;
        }

        // Hiển thị kế hoạch sắp tới
        if (upcomingPlans.length > 0) {
            const formattedUpcomingContext = plan.plan_context.replaceAll("\\n", "<br>");

            upcomingPlans.forEach(plan => {
                upcomingPlanContainer.innerHTML += `
                    <div>
                        <p><strong>Ngày:</strong> ${plan.date}</p>
                        <p><strong>Nội dung:</strong> ${formattedUpcomingContext}</p>
                        <p><strong>Mức độ:</strong> ${plan.level}</p>
                    </div>
                `;

            });
        } else {
            upcomingPlanContainer.innerHTML += `<p>Không có kế hoạch sắp tới.</p>`;
        }
    }


    displayPlans(userPlans);

   const daysTag = document.querySelector(".days"),
    currentDate = document.querySelector(".current-date"),
    addPlanEventBtn = document.getElementById("addPlanEventBtn"),
    eventPopup = document.getElementById("eventPopup"),
    saveEventBtn = document.getElementById("saveEventBtn"),
    closePopupBtn = document.getElementById("closePopupBtn");
    selectedDateText = document.getElementById("selectedDate");

    const showPlanEventBtn = document.getElementById("showPlanEventBtn");
    const deletePlanEventBtn = document.getElementById("deletePlanEventBtn");
    const informationArea = document.getElementById("information-area");

    // Xóa nội dung cũ trong thông tin kế hoạch
    informationArea.innerHTML = "";


    // getting new date, current year and month
    let date = new Date(),
    currYear = date.getFullYear(),
    currMonth = date.getMonth();

    const months = ["January", "February", "March", "April", "May", "June", "July",
    "August", "September", "October", "November", "December"];
    let selectedDate = null; // To store the selected date

    // Render Calendar Function (giống như phần trước, có thêm logic chọn ngày)
    const renderCalendar = () => {
        let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
            lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
            lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
            lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();

        let liTag = "";
        for (let i = firstDayofMonth; i > 0; i--) {
            liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
        }
        
        for (let i = 1; i <= lastDateofMonth; i++) {
            let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                        && currYear === new Date().getFullYear() ? "active" : "";
            
            let formattedDate = `${i}/${currMonth + 1}/${currYear}`;
            let hasPlan = userPlans.some(plan => plan.date === formattedDate);

            let planClass = hasPlan ? "has-plan" : "";  

            // Nếu có kế hoạch, thêm biểu tượng icon
            let planIcon = hasPlan ? `
                <div class="plan-icon">
                   *
                </div>
            ` : ""; 

            liTag += `
                <li class="${isToday}" data-day="${i}" data-month="${currMonth}" data-year="${currYear}">
                    ${i}
                    ${planIcon}
                </li>
            `;
        }

        for (let i = lastDayofMonth; i < 6; i++) {
            liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`;
        }

        currentDate.innerText = `${months[currMonth]} ${currYear}`;
        daysTag.innerHTML = liTag;

        // Add click event for each day
        const days = document.querySelectorAll(".days li");
        days.forEach(day => {
            day.addEventListener("click", function() {
                if (!day.classList.contains("inactive")) {
                    // Change the color of selected day
                    if (selectedDate) {
                        selectedDate.classList.remove("selected");
                    }
                    selectedDate = day;
                    day.classList.add("selected");
                    const daySelected = day.getAttribute("data-day");
                    const monthSelected = day.getAttribute("data-month");
                    const yearSelected = day.getAttribute("data-year");

                    // Show the selected date in the popup
                    document.getElementById("selectedDate").textContent = `Ngày: ${daySelected}/${parseInt(monthSelected) + 1}/${yearSelected}`;

                    let daySelectedOrgin = `${daySelected}/${parseInt(monthSelected) + 1}/${yearSelected}`;
                    
                    const planForDate = userPlans.find(plan => plan.date === daySelectedOrgin);
                    console.log("selectedDate", daySelectedOrgin);
                    console.log("planForDate", userPlans);


                    if (planForDate) {
                        // Nếu có kế hoạch, hiển thị nút và thông tin kế hoạch
                        showPlanEventBtn.style.display = "inline-block";
                        deletePlanEventBtn.style.display = "inline-block";
                        informationArea.innerHTML = `
                            <p>Ngày: ${planForDate.date}</p>
                            <p>Nội dung: ${planForDate.plan_context}</p>
                            <p>Mức độ: ${planForDate.level}</p>
                        `;
                    } else {
                        // Không có kế hoạch, ẩn nút và xóa thông tin
                        showPlanEventBtn.style.display = "none";
                        deletePlanEventBtn.style.display = "none";
                        informationArea.innerHTML = "";
                    }
                }
            });
        });
    };

    // Open Popup when clicking "Thêm sự kiện"
    addPlanEventBtn.addEventListener("click", () => {
        if (!selectedDate) {
            alert("Vui lòng chọn một ngày.");
            return;
        }
        eventPopup.style.display = "block";
    });

    // Close Popup
    closePopupBtn.addEventListener("click", () => {
        eventPopup.style.display = "none";
    });

    

    // Render the calendar initially
    renderCalendar();
    console.log(userTargets)

    const userPlanAndTarget = userTargets;

    function renderTargetTable(data) {
      if (data.length === 0) {
        document.getElementById("target-container").innerHTML = "<p>Không có mục tiêu nào.</p>";
        return;
      }

      let tableHTML = `
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr>
              <th>Mục tiêu</th>
              <th>Điểm Mục Tiêu</th>
            </tr>
          </thead>
          <tbody>
      `;

      data.forEach(item => {
        tableHTML += `
          <tr>
            <td>${item.target}</td>
            <td>${item.aim_point}</td>
          </tr>
        `;
      });

      tableHTML += `</tbody></table>`;
      document.getElementById("target-container").innerHTML = tableHTML;
    }

    // Initial render
    renderTargetTable(userPlanAndTarget);
    
    prevNextIcon = document.querySelectorAll(".icons span");

    prevNextIcon.forEach(icon => { // getting prev and next icons
        icon.addEventListener("click", () => { // adding click event on both icons
            // if clicked icon is previous icon then decrement current month by 1 else increment it by 1
            currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;
            if(currMonth < 0 || currMonth > 11) { // if current month is less than 0 or greater than 11
                // creating a new date of current year & month and pass it as date value
                date = new Date(currYear, currMonth, new Date().getDate());
                currYear = date.getFullYear(); // updating current year with new date year
                currMonth = date.getMonth(); // updating current month with new date month
            } else {
                date = new Date(); // pass the current date as date value
            }
            renderCalendar(); // calling renderCalendar function
        });
    });


    document.querySelector("#saveEventBtn").addEventListener("click", function (e) {
    e.preventDefault();

    const planContext = document.querySelector("#plan_context").value.replace(/\n/g, "\\n");

    const level = document.querySelector("#level").value;
    const selectedDate = document.querySelector("#selectedDate").innerText.replace('Ngày: ', ''); 

    
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    jQuery.ajax({
    url: ajaxurl,
    type: "POST",
    data: {
        action: "save_user_plan", // Tên action phải khớp với tên trong add_action ở backend
        username: currentUsername,
        date: selectedDate,
        plan_context: planContext,
        level: level
    },
    success: function (response) {
        if (response.success) {
        console.log(response.data.message);
        alert("Kế hoạch đã được lưu!");
        } else {
        console.error(response.data.message);
        alert("Lưu kế hoạch thất bại!");
        }
    },
    error: function () {
        console.error("AJAX error");
        alert("Có lỗi xảy ra trong quá trình gửi AJAX.");
    }
    });

    });






    // DOM Elements
    const addTargetButton = document.getElementById("btn-add-target");
    const popup = document.getElementById("popup-add-target");
    const closePopupButton = document.getElementById("close-popup-btn");
    const saveTargetButton = document.getElementById("saveTargetButton");
    const targetSelect = document.getElementById("target-select");
    const aimInput = document.getElementById("aim-input");

    // Show popup
    addTargetButton.addEventListener("click", () => {
    popup.classList.remove("hidden");
    });

    // Close popup
    closePopupButton.addEventListener("click", () => {
    popup.classList.add("hidden");
    });



  
    

    document.querySelector("#saveTargetButton").addEventListener("click", function (e) {
    e.preventDefault();

    const aim_pointContext = targetSelect.value; // Điểm mục tiêu
    const targetSelected = aimInput.value; // Mục tiêu đã chọn

    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
            action: "save_user_target", // Tên action cần khớp với add_action ở backend
            username: currentUsername,
            target: aim_pointContext,  // Lưu target
            aim_point: targetSelected, // Lưu điểm mục tiêu
        },
        success: function (response) {
            if (response.success) {
                console.log(response.data.message);
                alert("Đã thiết lập target");
            } else {
                console.error(response.data.message);
                alert("Lưu target thất bại!");
            }
        },
        error: function () {
            console.error("AJAX error");
            alert("Có lỗi xảy ra trong quá trình gửi AJAX.");
        }
    });
});

</script>
<?php
?>
