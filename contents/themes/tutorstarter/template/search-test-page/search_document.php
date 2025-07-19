<?php

add_filter('document_title_parts', function ($title) {
 
        $title['title'] = sprintf('Tài liệu');
    
    return $title;
});


get_header();

$site_url = get_site_url();
$current_user = wp_get_current_user();
$user_id = $current_user->ID; // Lấy user ID
$username = $current_user->user_login;


echo "<script>  const currentUsername = '" . strval($username) . "';  </script>";
echo '<script>  var siteUrl = "' . $site_url .'";  </script>';


 
// Define the document table name
$table_name = 'document';
$current_post_type  = 'document';
// Get the current post type and search term
$search_term = $_GET['term'] ?? '';
global $wpdb;

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$limit = 12;
$offset = ($paged - 1) * $limit;

$price_filter = $_GET['prices'] ?? '';  
if (!is_array($price_filter)) {
    $price_filter = [$price_filter];
}

$conditions = [];
$params = ['%' . $wpdb->esc_like($search_term) . '%', $limit, $offset];

// Điều kiện lọc theo giá
if (in_array('free', $price_filter) && in_array('premium', $price_filter)) {
    // Không cần điều kiện gì vì cả free và premium đều được chấp nhận
} elseif (in_array('free', $price_filter)) {
    $conditions[] = "prices = '0'";
} elseif (in_array('premium', $price_filter)) {
    $conditions[] = "prices > '0'";
}

// Gộp các điều kiện thành câu SQL
$where_clause = !empty($conditions) ? 'AND ' . implode(' AND ', $conditions) : '';

$query = $wpdb->prepare(
    "SELECT * FROM {$table_name} WHERE document_name LIKE %s $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d",
    ...$params
    );

    $results = $wpdb->get_results($query);



// Display the navigation buttons
?>
<div class="container-search">
    <div class="content">
        <!-- Existing content -->
    

<div class="search-feature">
    <!-- Search Form -->
    <form method="get" action="<?php echo esc_url(home_url( $current_post_type ? $current_post_type : '')); ?>" class="search-form">
        <input type="text" name="term" placeholder="Nhập từ khóa..." value="<?php echo esc_attr($search_term); ?>" />

        <div class="test-type-option">
            <label><input type="checkbox" name="price[]" value="free" <?php if (in_array('free', $price_filter)) echo 'checked'; ?>> Free</label>
            <label><input type="checkbox" name="price[]" value="premium" <?php if (in_array('premium', $price_filter)) echo 'checked'; ?>> Premium</label>

        </div>

        <button type="submit">Tìm kiếm & Lọc</button>
    </form>
    <div id = "content-token">
            <div class="loader" id = "token-loader"></div>
            <div id = "token-content"></div>
    </div>
</div>

<?php
// Set up the query arguments
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = [
    'search_url' => $current_post_type !== 'all' ? $current_post_type : 'any',
    's' => $search_term,
    'posts_per_page' => 12,
    'paged' => $paged,
    'order' => 'ASC',

];

// Run the query
$query = new WP_Query($args);
?>

<div class="test-library">
    <?php if (!empty($results)) : ?>
        <div class="test-grid">
    <?php foreach ($results as $document) : ?>
        <div class="test-item">
            <div class="price-icon">
                <?php
                    if ($document->prices > 0) {
                        echo esc_html($document->prices) . ' tokens';
                    } else {
                        echo 'Free';
                    }
                ?>
            </div>
            <div class="document-info">
                <h3><?php echo esc_html($document->document_name); ?></h3>
                <div class="document-meta">
                    <span class="category"><?php echo esc_html($document->category); ?></span>
                    <?php if (!empty($document->tag)) : ?>
                        <div class="tags">
                            <?php 
                            $tags = json_decode($document->tag);
                            if (is_array($tags)) {
                                foreach ($tags as $tag) {
                                    echo '<span class="tag">' . esc_html($tag) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

           
          
            <?php
              
               




                $base_path = "/{$current_post_type}/";
                $document_url = home_url("{$base_path}{$document->document_id}");
            ?>


                <a href="<?php echo esc_url($document_url); ?>" class="detail-button">Take Document</a>
        </div>
    <?php endforeach; ?>
</div>


        <!-- Pagination -->
        <div class="pagination">
            <?php
            $total_tests_query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE testname LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%'
            );
          
            $total_documents = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE document_name LIKE %s $where_clause",
                '%' . $wpdb->esc_like($search_term) . '%'
            ));
            $total_pages = ceil($total_documents / $limit);
            

            echo paginate_links([
                'total' => $total_pages,
                'current' => $paged,
                'format' => '?paged=%#%',
                'prev_text' => '&laquo; Prev',
                'next_text' => 'Next &raquo;',
                'add_args' => ['term' => $search_term],
            ]);
            ?>
        </div>
    <?php else : ?>
        <p>Không tìm thấy tài liệu nào với từ khóa "<?php echo esc_html($search_term); ?>"</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>
</div>
    <aside class="sidebar">
        <b><svg  class="icon_tests" version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <circle fill="#F76D57" cx="32" cy="32" r="6"></circle> <path fill="#F9EBB2" d="M32,20c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S38.627,20,32,20z M32,40 c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,40,32,40z"></path> <path fill="#45AAB8" d="M32,14c-9.941,0-18,8.059-18,18s8.059,18,18,18s18-8.059,18-18S41.941,14,32,14z M32,46 c-7.732,0-14-6.268-14-14s6.268-14,14-14s14,6.268,14,14S39.732,46,32,46z"></path> <path fill="#F9EBB2" d="M32,8C18.745,8,8,18.745,8,32s10.745,24,24,24s24-10.745,24-24S45.255,8,32,8z M32,52 c-11.046,0-20-8.954-20-20s8.954-20,20-20s20,8.954,20,20S43.046,52,32,52z"></path> <path fill="#45AAB8" d="M32,2C15.432,2,2,15.432,2,32s13.432,30,30,30s30-13.432,30-30S48.568,2,32,2z M32,58 C17.641,58,6,46.359,6,32S17.641,6,32,6s26,11.641,26,26S46.359,58,32,58z"></path> <path fill="#394240" d="M32,0c-8.477,0-16.178,3.302-21.903,8.683L9,7.586V5c0-0.168-0.051-0.318-0.124-0.457 C8.862,4.48,8.827,4.412,8.747,4.332L4.708,0.293H4.707C4.526,0.111,4.276,0,4,0C3.447,0,3,0.447,3,1v2H1C0.447,3,0,3.447,0,4 c0,0.276,0.112,0.526,0.293,0.707v0.001l3.999,3.998c0.001,0.001,0.001,0.001,0.001,0.001l0.041,0.041 c0.08,0.08,0.147,0.115,0.21,0.129C4.682,8.949,4.833,9,5,9h2.586l1.097,1.097C3.302,15.822,0,23.523,0,32 c0,17.673,14.327,32,32,32s32-14.327,32-32S49.673,0,32,0z M7,7H5.415L3.418,5.004L5,5l0.012-1.574L7,5.414V7z M32,62 C15.432,62,2,48.568,2,32c0-7.924,3.078-15.127,8.097-20.489l2.828,2.828C8.629,18.977,6,25.18,6,32c0,14.359,11.641,26,26,26 s26-11.641,26-26S46.359,6,32,6c-6.82,0-13.023,2.629-17.661,6.925l-2.828-2.828C16.874,5.078,24.075,2,32,2 c16.568,0,30,13.432,30,30S48.568,62,32,62z M17.185,18.599C13.973,22.146,12,26.837,12,32c0,11.046,8.954,20,20,20s20-8.954,20-20 s-8.954-20-20-20c-5.163,0-9.854,1.973-13.401,5.185l-2.846-2.845C20.028,10.404,25.732,8,32,8c13.255,0,24,10.745,24,24 S45.255,56,32,56S8,45.255,8,32c0-6.268,2.405-11.973,6.339-16.246L17.185,18.599z M21.427,22.841C19.298,25.297,18,28.494,18,32 c0,7.732,6.268,14,14,14s14-6.268,14-14s-6.268-14-14-14c-3.506,0-6.703,1.298-9.159,3.427l-2.828-2.828 C23.197,15.748,27.39,14,32,14c9.941,0,18,8.059,18,18s-8.059,18-18,18s-18-8.059-18-18c0-4.61,1.748-8.803,4.599-11.987 L21.427,22.841z M25.686,27.1C24.633,28.455,24,30.151,24,32c0,4.418,3.582,8,8,8s8-3.582,8-8s-3.582-8-8-8 c-1.849,0-3.545,0.633-4.9,1.686l-2.844-2.844C26.347,21.072,29.047,20,32,20c6.627,0,12,5.373,12,12s-5.373,12-12,12 s-12-5.373-12-12c0-2.953,1.072-5.653,2.842-7.744L25.686,27.1z M31.293,32.707c0.391,0.391,1.023,0.391,1.414,0 s0.391-1.023,0-1.414l-4.18-4.18C29.508,26.415,30.704,26,32,26c3.313,0,6,2.687,6,6s-2.687,6-6,6s-6-2.687-6-6 c0-1.295,0.415-2.492,1.113-3.473L31.293,32.707z"></path> <polygon fill="#B4CCB9" points="7,7 5.415,7 3.418,5.004 5,5 5.012,3.426 7,5.414 "></polygon> </g> </g></svg>Mục tiêu của bạn</b>
        <div class="loader" id = "target-loader"></div>
        <div id="target-list" class="target-list"></div>

        <b><svg  class="icon_tests" version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve" fill="#ffffff" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .sharpcorners_een{fill:#62c0b5;} .st0{fill:#62c0b5;} </style> <path class="sharpcorners_een" d="M4,2v28h24V2H4z M13.354,23.646l-0.707,0.707L11.5,23.207l-1.146,1.146l-0.707-0.707l1.146-1.146 l-1.146-1.146l0.707-0.707l1.146,1.146l1.146-1.146l0.707,0.707L12.207,22.5L13.354,23.646z M13.354,16.646l-0.707,0.707 L11.5,16.207l-1.146,1.146l-0.707-0.707l1.146-1.146l-1.146-1.146l0.707-0.707l1.146,1.146l1.146-1.146l0.707,0.707L12.207,15.5 L13.354,16.646z M13.354,9.646l-0.707,0.707L11.5,9.207l-1.146,1.146L9.646,9.646L10.793,8.5L9.646,7.354l0.707-0.707L11.5,7.793 l1.146-1.146l0.707,0.707L12.207,8.5L13.354,9.646z M22,24h-7v-1h7V24z M22,22h-7v-1h7V22z M22,17h-7v-1h7V17z M22,15h-7v-1h7V15z M22,10h-7V9h7V10z M22,8h-7V7h7V8z"></path> </g></svg>Kế hoạch hôm nay</b>
        <div class="loader" id = "planlist-loader"></div>
        <div id="plan-list" class="plan-list"></div>
       

    </aside>
</div>

<script>
    console.log('<?php echo esc_js($username); ?>');

    fetch(`${siteUrl}/wp-json/api/v1/get-user-token?username=${currentUsername}`)
  .then(response => {
    if (!response.ok) {
      throw new Error('Không thể lấy dữ liệu token của người dùng');
    }
    return response.json();
  })
  .then(data => {
    document.getElementById("token-loader").style.display = 'none';
    
    // Lấy phần tử token-content
    const tokenContentElement = document.getElementById("token-content");
    
    // Cập nhật nội dung HTML (thêm vào nội dung hiện có)
    tokenContentElement.innerHTML += `
      <div>Số token bạn đang có: ${data.token}</div>
      <div>Số token practice bạn đang có: ${data.token_practice}</div>
    `;
    
    console.log('Số token bạn đang có:', data.token);
    console.log('Số token practice bạn đang có:', data.token_practice);
  })
  .catch(error => {
    console.error(error);
    document.getElementById("token-loader").style.display = 'none';
    document.getElementById("token-content").innerHTML = `
      <div style="color: red;">Lỗi khi tải thông tin token: ${error.message}</div>
    `;
  });


  
    fetch(`${siteUrl}/wp-json/api/v1/get-plan-today?username=${currentUsername}`)
  .then(response => {
    if (!response.ok) {
      throw new Error('Không thể lấy dữ liệu kế hoạch cho ngày hôm nay');
    }
    return response.json();
  })
  .then(data => {
    document.getElementById("planlist-loader").style.display ='none';
    console.log('Kế hoạch hôm nay:', data.plan_today);
    renderPlanToday(data.plan_today);
  })
  .catch(error => console.error(error));


// Lấy dữ liệu từ API cho Target
fetch(`${siteUrl}/wp-json/api/v1/get-target?username=${currentUsername}`)
  .then(response => {
    if (!response.ok) {
      throw new Error('Không thể lấy dữ liệu mục tiêu');
    }
    return response.json();
  })
  .then(data => {
    document.getElementById("target-loader").style.display ='none';

    const targetData = JSON.parse(data.target); // Parse JSON nếu backend trả về chuỗi
    console.log('Target:', targetData);
    renderTargetList(targetData);
    hidePreloader();
  })
  .catch(error => console.error(error));

  
function renderPlanToday(planData) {
  const planList = document.getElementById('plan-list');
  planList.innerHTML = '';

  if (planData.length === 0) {
    planList.innerHTML = 'Không có kế hoạch cho ngày hôm nay!<br> <a href="<?php echo $site_url ?>/dashboard/my-plan-and-target/">Thêm kế hoạch </a>';
    return;
  }

  planData.forEach(plan => {
    const planCard = document.createElement('div');
    planCard.classList.add('card');

    // ✅ FIX: thay \\n thành \n
    const fixedContext = plan.plan_context.replace(/\\n/g, '\n');

    planCard.innerHTML = `
      <p><strong>Nội dung:</strong></p>
      <div style="white-space: pre-line;">${fixedContext}</div>
      <p><strong>Mức độ:</strong> ${plan.level}</p>
    `;
    planList.appendChild(planCard);
  });
}



// Hiển thị danh sách mục tiêu
function renderTargetList(targetData) {
  const targetList = document.getElementById('target-list');
  targetList.innerHTML = '';

  targetData.forEach(target => {
    const targetCard = document.createElement('div');
    targetCard.classList.add('card');
    targetCard.innerHTML = `
        ${target.target} - ${target.aim_point}`;

    targetList.appendChild(targetCard);
  });
}


</script>
<style>
    
/* HTML: <div class="loader"></div> */
.loader {
  width: 50px;
  aspect-ratio: 1;
  display: grid;
  border: 4px solid #0000;
  border-radius: 50%;
  border-right-color: #25b09b;
  animation: l15 1s infinite linear;
}
.loader::before,
.loader::after {    
  content: "";
  grid-area: 1/1;
  margin: 2px;
  border: inherit;
  border-radius: 50%;
  animation: l15 2s infinite;
}
.content{
    max-width: 1350px;
}
.loader::after {
  margin: 8px;
  animation-duration: 3s;
}
@keyframes l15{ 
  100%{transform: rotate(1turn)}
}

    .container-search {
        display: flex;
        gap: 20px;
    }
    .content {
        flex: 1;
    }
    .sidebar {
        width: 250px;
        background-color: #f4f4f4;
        padding: 20px;
        border-radius: 8px;
    }
    @media (max-width: 768px) {
        .container-search {
            flex-direction: column;
            width: 100%;
            height: 100%;
        }
        .sidebar {
            order: -1;
            width: 100%;
            height: 100%;
        }
    }

.post-type-navigation {
    overflow-x: auto; /* Cho phép cuộn ngang */
    white-space: nowrap; /* Ngăn chữ xuống dòng */
    display: flex;
    gap: 10px;
    padding-bottom: 10px; /* Tránh che thanh cuộn */
    scrollbar-width: thin; /* Tạo thanh cuộn mảnh trên Firefox */
    -ms-overflow-style: none; /* Ẩn thanh cuộn trên IE/Edge cũ */
}

/* Ẩn thanh cuộn trên Chrome, Safari */
.post-type-navigation::-webkit-scrollbar {
    height: 6px; /* Độ dày của thanh cuộn */
}

.post-type-navigation::-webkit-scrollbar-thumb {
    background-color: #bbb;
    border-radius: 5px;
}
.nav-button {
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    background-color: #f1f1f1;
    transition: background-color 0.3s;
    flex-shrink: 0; /* Ngăn co lại */
    display: inline-block; /* Đảm bảo không bị thu nhỏ */
}

.nav-button.active {
    color: #fff;
    background-color: #0073aa;
}

.nav-button:hover {
    background-color: #ddd;
}

    .test-library {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

.search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-form input[type="text"] {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.search-form button {
    padding: 8px 16px;
    background-color: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.test-item {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
}

.test-item h2 {
    font-size: 18px;
    margin: 10px 0;
}

.test-meta p {
    margin: 5px 0;
    color: #666;
    font-size: 14px;
}

.test-tags span {
    display: inline-block;
    margin: 5px 5px 0 0;
    background-color: #e0e0e0;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 13px;
}

.detail-button {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 12px;
    background-color: #0073aa;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}

.detail-button:hover {
    background-color: #005a8c;
}

.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination a {
    padding: 5px 10px;
    color: #0073aa;
    text-decoration: none;
    margin: 0 5px;
}

.pagination a:hover {
    background-color: #0073aa;
    color: white;
    border-radius: 4px;
}
.post-id {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: #f1f1f1;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    color: #333;
}
.test-item {
    position: relative;
    padding-top: 40px;
}
.custom-number {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #0073aa;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
}

.test-item {
    position: relative; /* Để custom number nằm trong item */
    padding-top: 40px; /* Dành không gian cho custom number */
}
.icon_tests {
    width: 20px;
    height: 20px;
    vertical-align: middle;
}

.completed-icon {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: #fff;
    color: #63E6BE;
    padding: 5px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 16px;
}
.price-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #fff;
    color: #63E6BE;
    padding: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 16px;
}
.test-item {
    position: relative; /* Giúp biểu tượng nằm trong item */
    padding-top: 40px; /* Dành không gian cho biểu tượng */
}

</style>
<?php  get_footer() ?>