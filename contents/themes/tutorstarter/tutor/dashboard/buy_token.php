<?php
/*
 * Template Name: Gift User Token Template
 * Template Post Type: user token page
 
 */


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
 $current_username = $current_user->user_login;
 $user_id = $current_user->ID; // Lấy user ID
 
 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây

 // Generate random two-digit number
 $random_number = rand(10, 99);

 // Handle user_id and id_test error, set to "00" if invalid
 if (!$user_id) {
    $user_id = '00'; // Set user_id to "00" if invalid
}




 // Create result_id
 $ss_id = $hour . $minute . $second . $user_id . $random_number;

 echo "<script> 
 const sessionID = '" . strval($ss_id) . "'; 
 console.log('sessionID: ' + sessionID);
</script>";





  // Thực hiện truy vấn để lấy id_video_orginal 
  $sql = "SELECT username, updated_time, token, token_practice, token_use_history FROM user_token WHERE username = %s";
  $result = $wpdb->get_row($wpdb->prepare($sql, $current_username));

  if ($result) {
   /* echo "Username: " . ($result->username ?? 'Không có dữ liệu');
    echo "Time updated " . ($result->updated_time ?? 'Không có dữ liệu');
    echo "Token: " . ($result->token ?? 'Không có dữ liệu');
    echo "Lịch sử sử dụng Token: " . ($result->token_use_history ?? 'Không có dữ liệu');*/

  // Đóng kết nối
  $conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Token</title>
  <style>
    .token-toggle {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }
    
    .token-toggle button {
      padding: 10px 20px;
      background-color: #f0f0f0;
      border: none;
      cursor: pointer;
      font-size: 16px;
      transition: all 0.3s;
    }
    
    .token-toggle button.active {
      background-color: #007bff;
      color: white;
    }
    
    .token-toggle button:first-child {
      border-radius: 8px 0 0 8px;
    }
    
    .token-toggle button:last-child {
      border-radius: 0 8px 8px 0;
    }
   
    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .card-content {
      font-size: 14px;
      color: #666;
      margin-bottom: 10px;
    }

    .card-info {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    button {
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    button.buy-now {
      background-color: #28a745;
    }

    button.buy-now:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <i>Lưu ý: Đây là trang mua token để sử dụng các dịch vụ như chấm ielts writing, chấm ielts speaking,...</i>
  
  <!-- Thêm toggle button -->
  <div class="token-toggle">
    <button id="tokenTab" class="active" onclick="switchTab('token')">Token</button>
    <button id="tokenPracticeTab" onclick="switchTab('token_practice')">Token Practice</button>
  </div>
  
  <div id="coint-shop" class="coint-shop"></div>
  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/fstudy/contents/checkout_gateway/token/vnpay_php/config.php"); ?>             

  <div class="container" id="card-container"></div>

  <form action="/contents/checkout_gateway/token/checkout_controller.php" id="frmCreateOrder" method="post" style="display:none;">
    <input type="hidden" name="bankCode" id="bankCode">
    <input type="hidden" name="amount" id="amount">
    <input type="hidden" name="orderCode" id="orderCode">
    <input type="hidden" name="typeItem" id="typeItem">
    <input type="hidden" name="item" id="item">
    <input type="hidden" name="language" id="language" value="vn">
  </form>

  <script>
    // Các gói token thông thường
    const tokenPackages = [
      { title: "Token Package 1", content: "Best for small tasks.", tokens: 100, price: 20000, type_item: "token" },
      { title: "Token Package 2", content: "Ideal for daily users.", tokens: 200, price: 30000, type_item: "token" },
      { title: "Token Package 3", content: "Most popular package.", tokens: 500, price: 50000, type_item: "token" },
      { title: "Token Package 4", content: "Professional use.", tokens: 1000, price: 100000, type_item: "token" },
      { title: "Token Package 5", content: "Best value deal.", tokens: 2000, price: 200000, type_item: "token" },
      { title: "Token Package 6", content: "Corporate package.", tokens: 5000, price: 300000, type_item: "token" },
      { title: "Token Package 7", content: "Massive savings.", tokens: 10000, price: 2000000, type_item: "token" },
      { title: "Token Package 8", content: "Ultimate business plan.", tokens: 20000, price: 100000000, type_item: "token" }
    ];

    // Các gói token practice
    const tokenPracticePackages = [
      { title: "Practice Token Package 1", content: "For basic practice.", tokens: 50, price: 10000, type_item: "token_practice" },
      { title: "Practice Token Package 2", content: "For regular practice.", tokens: 100, price: 15000, type_item: "token_practice" },
      { title: "Practice Token Package 3", content: "For intensive practice.", tokens: 200, price: 25000, type_item: "token_practice" },
      { title: "Practice Token Package 4", content: "For professional practice.", tokens: 500, price: 50000, type_item: "token_practice" }
    ];

    let currentTab = 'token';
    const container = document.getElementById("card-container");

    // Hàm chuyển đổi tab
    function switchTab(tab) {
      currentTab = tab;
      
      // Cập nhật trạng thái active của button
      document.getElementById('tokenTab').classList.toggle('active', tab === 'token');
      document.getElementById('tokenPracticeTab').classList.toggle('active', tab === 'token_practice');
      
      // Hiển thị các gói phù hợp
      renderPackages();
    }

    // Hàm hiển thị các gói
    function renderPackages() {
      container.innerHTML = '';
      
      const packages = currentTab === 'token' ? tokenPackages : tokenPracticePackages;
      
      packages.forEach((pkg, index) => {
        const card = document.createElement("div");
        card.classList.add("card");

        card.innerHTML = `
          <div class="card-title">${pkg.title}</div>
          <div class="card-content">${pkg.content}</div>
          <div class="card-info">Tokens: ${pkg.tokens}</div>
          <div class="card-info">Price: $${pkg.price}</div>
          <div class="buttons">
            <button onclick="showDetails('${pkg.title}', '${pkg.content}')">View Details</button>
            <button class="buy-now" onclick="buyNow('${pkg.title}', ${pkg.price}, ${pkg.tokens}, '${pkg.type_item}')">Buy Now</button>
          </div>
        `;

        container.appendChild(card);
      });
    }

    // Khởi tạo ban đầu
    renderPackages();

    function showDetails(title, content) {
      Swal.fire({
        title: title,
        text: content,
        icon: 'info',
        confirmButtonText: 'Close'
      });
    }

    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    function buyNow(title, price, tokens, type_item) {
      Swal.fire({
        title: `Do you want to buy ${title}?`,
        input: 'select',
        inputOptions: {
          'vnpay': 'VNPay',
          'payUrl': 'Momo',
          'cod': 'Cod'
        },
        inputPlaceholder: 'Select payment method',
        showCancelButton: true,
        confirmButtonText: 'Pay Now',
        html: `
          <div class="form-group" style = "display:none">
            <h5>Chọn ngôn ngữ giao diện thanh toán:</h5>
            <input type="radio" id="language-vn" name="language" value="vn" checked>
            <label for="language-vn">Tiếng Việt</label><br>
            <input type="radio" id="language-en" name="language" value="en">
            <label for="language-en">Tiếng Anh</label><br>
          </div>
        `,
        preConfirm: () => {
          const selectedBankCode = Swal.getPopup().querySelector('select').value;
          const selectedLanguage = Swal.getPopup().querySelector('input[name="language"]:checked').value;
          return { selectedBankCode, selectedLanguage };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const { selectedBankCode, selectedLanguage } = result.value;

          document.getElementById('bankCode').value = selectedBankCode;
          document.getElementById('amount').value = price;
          document.getElementById('orderCode').value = sessionID;
          document.getElementById('item').value = `${title}`;
          document.getElementById('language').value = selectedLanguage;
          document.getElementById('typeItem').value = type_item;

          addTransactionToDB(selectedBankCode, price, tokens, title, type_item)
            .then(() => {
              setTimeout(() => {
                document.getElementById("frmCreateOrder").submit();
              }, 2000);
            })
            .catch(() => {
              Swal.fire('Error', 'Transaction failed. Please try again.', 'error');
            });
        }
      });
    }

    function addTransactionToDB(typeTransaction, amount, tokens, title, type_item) {
      return new Promise((resolve, reject) => {
        const orderItem = JSON.stringify({ title: title, amount: amount, tokens: tokens, type_item: type_item });
        const orderTime = new Date().toISOString();

        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            action: "handle_token_transaction",
            type_transaction: typeTransaction,
            amount: amount,
            type_item: type_item,
            order_time: orderTime,
            order_code: sessionID,
            order_item: orderItem,
            order_status: "pending"
          },
          success: function (response) {
            if (response.success) {
              console.log(response.data);
              resolve(response.data);
            } else {
              console.error("Failed to add transaction");
              reject();
            }
          },
          error: function () {
            console.error("AJAX error");
            reject();
          }
        });
      });
    }
  </script>
</body>
</html>


<?php
}
else {
  echo 'Lỗi: Không tìm thấy Token nào';
}
