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
        //const siteurl = '" . $siteurl . "'; 

    console.log('sessionID: ' + sessionID);
</script>";


?>


<!-- Khung hiển thị các gói -->
<div id="user-current-role" style="margin: 20px; font-size: 1.1rem; font-weight: bold;"></div>
<div id="account-packages" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px;"></div>
<!-- CSS tùy chỉnh cơ bản -->

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/fstudy/contents/checkout_gateway/upgrade_account/vnpay_php/config.php"); ?>             

  <div class="container" id="card-container"></div>

  <form action="/contents/checkout_gateway/upgrade_account/checkout_controller.php" id="frmCreateOrder" method="post" style="display:none;">
    <input type="hidden" name="bankCode" id="bankCode">
    <input type="hidden" name="amount" id="amount">
    <input type="hidden" name="orderCode" id="orderCode">
    <input type="hidden" name="typeItem" id="typeItem">
    <input type="hidden" name="accountCode" id="accountCode">

    <input type="hidden" name="item" id="item">
    <input type="hidden" name="language" id="language" value="vn">
  </form>

<style>
  .package-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #ddd;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
  }

  .package-card:hover {
    transform: translateY(-4px);
  }

  .upgrade-btn {
    background-color: #3B82F6;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    font-weight: bold;
  }

  .upgrade-btn:hover {
    background-color: #2563EB;
  }
</style>

<!-- Script gọi API và hiện gói -->
<script>
  let userRoleIds = []; // Mảng chứa tất cả id_role mà user đang sở hữu
  const userCurrentRoleDiv = document.getElementById('user-current-role');
  const container = document.getElementById('account-packages');


  // 1. Fetch role hiện tại của user
  fetch(`${siteurl}/api/v1/user/get_role`, {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ user_id: <?php echo json_encode($user_id); ?> })
})
.then(res => res.json())
.then(roleData => {
  if (roleData.status === 'success' && roleData.roles.length > 0) {
    // Lọc bỏ role "Default" nếu không muốn hiển thị
    const activeRoles = roleData.roles.filter(role => role.role !== "Default");
    
    if (activeRoles.length > 0) {
      let rolesHtml = activeRoles.map(role => {
        return `
          <div style="margin-bottom: 10px;">
            🎉 Bạn đang sở hữu gói <span style="color: #16a34a;">"${role.role}"</span> 
            (Hết hạn: <strong>${role.expired_date}</strong>)
          </div>
        `;
      }).join('');

      userCurrentRoleDiv.innerHTML = rolesHtml;
    } else {
      userCurrentRoleDiv.innerHTML = "🎉 Bạn đang sở hữu gói Default mặc định (Thời hạn: Không giới hạn) ";
    }
    
    // Lưu tất cả id_role mà user đang sở hữu (kể cả Default nếu cần)
    userRoleIds = roleData.roles.map(role => String(role.id_role));
  } else {
    userCurrentRoleDiv.innerHTML = "Không thể tải thông tin gói";
  
    
    // Lưu tất cả id_role mà user đang sở hữu vào mảng
  }
    userRoleIds = roleData.roles.map(role => String(role.id_role));

    // 2. Sau khi có userRoleId, fetch gói và đối chiếu
    return fetch(`${siteurl}/api/v1/web-store/package/account_package`, {
      method: 'POST'
    });
  })
  .then(res => res.json())
  .then(data => {
    if (Array.isArray(data)) {
      data.forEach(pkg => {
        const card = document.createElement('div');
        card.className = 'package-card';

        const isOwned = userRoleIds.includes(String(pkg.id));

        card.innerHTML = `
          <h3 style="font-size: 1.25rem; font-weight: bold;">${pkg.user_role}</h3>
          <p><strong>Giá:</strong> ${pkg.price_role} VNĐ</p>
          <p><strong>Thời hạn:</strong> ${pkg.time_expired}</p>
          <p style="color: #666;">${pkg.note_role}</p>
          <button class="upgrade-btn" ${isOwned ? 'disabled style="background-color: #ccc; cursor: not-allowed;"' : ''}>
            ${isOwned ? 'Đang sở hữu' : 'Nâng cấp'}
          </button>
        `;

        if (!isOwned) {
          card.querySelector('.upgrade-btn').addEventListener('click', () => {
            buyNow(pkg.user_role, pkg.price_role, '0', 'account_package', pkg.id);
          });
        }

        container.appendChild(card);
      });
    } else {
      console.error('Dữ liệu gói không phải mảng:', data);
    }
  })
  .catch(err => {
    console.error('Lỗi tải gói:', err);
  });

  var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

  function buyNow(title, price, tokens, type_item, accountCode) {
    Swal.fire({
      title: `Bạn muốn mua "${title}, giá ${price}"?`,
      input: 'select',
      inputOptions: {
        'vnpay': 'VNPay',
        'payUrl': 'Momo',
        'cod': 'Cod'
      },
      inputPlaceholder: 'Chọn phương thức thanh toán',
      showCancelButton: true,
      confirmButtonText: 'Thanh toán ngay',
      html: `
        <div class="form-group" style="display:none">
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
        document.getElementById('accountCode').value = `${accountCode}`;
        document.getElementById('language').value = selectedLanguage;
        document.getElementById('typeItem').value = type_item;
        const form = document.getElementById("frmCreateOrder");
        console.log({
          bankCode: form.bankCode.value,
          amount: form.amount.value,
          orderCode: form.orderCode.value,
          item: form.item.value,
          accountCode: form.accountCode.value,
          language: form.language.value,
          typeItem: form.typeItem.value
        });




        addTransactionToDB(selectedBankCode, price, tokens, title, type_item, accountCode)
          .then(() => {
            setTimeout(() => {
              document.getElementById("frmCreateOrder").submit();
            }, 2000);
          })
          .catch(() => {
            Swal.fire('Lỗi', 'Giao dịch thất bại. Vui lòng thử lại.', 'error');
          });
      }
    });
  }

  function addTransactionToDB(typeTransaction, amount, tokens, title, type_item, accountCode) {
      return new Promise((resolve, reject) => {
        const orderItem = JSON.stringify({ title: title, amount: amount, tokens: tokens, type_item: type_item , accountCode: accountCode});
        const orderTime = new Date().toISOString();
        console.log(`typeTransaction: ${typeTransaction},  amount: ${amount}, type_item: ${type_item}, orderTime: ${orderTime}, sessionID: ${sessionID}, orderItem: ${orderItem}`);

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