<?php

add_filter('document_title_parts', function ($title) {
 
        $title['title'] = sprintf('Practice Library');
    
    return $title;
});


get_header();

$site_url = get_site_url();
$current_user = wp_get_current_user();
$user_id = $current_user->ID; // Lấy user ID
$username = $current_user->user_login;


echo "<script>  const currentUsername = '" . strval($username) . "';  </script>";
echo '<script>  var siteUrl = "' . $site_url .'";  </script>';



// Define the post types with labels for the navigation
$test_tables = [
    
    'talk-with-edward' => 'conversation_with_ai_list',
    'studyvocabulary' => 'list_vocabulary_package',
    'dictation' => 'shadowing_dictation_question',
    'shadowing' => 'shadowing_dictation_question',



];

// Labels mapping
$test_labels = [
  
    'talk-with-edward' => 'Talk With Edward',
    'studyvocabulary' => 'Learn Vocabulary',
    'dictation' => 'Dictation',
    'shadowing' => 'Shadowing',


];

// Get the current post type and search term
$current_post_type = get_query_var('search_url', 'digitalsat');
$search_term = $_GET['term'] ?? '';
global $wpdb;

// Determine the table to query
// Determine the table to query
$table_name = $test_tables[$current_post_type] ?? '';
$search_column = ($table_name === 'list_vocabulary_package') ? 'package_name' : 'testname';


$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$limit = 12;
$offset = ($paged - 1) * $limit;

if (!empty($table_name)) {
    // Sort parameter: name_asc (default), takers_desc, created_desc
    $sort = $_GET['sort'] ?? 'name_asc';
    $allowed_sorts = ['name_asc', 'takers_desc', 'created_desc'];
    if (!in_array($sort, $allowed_sorts, true)) {
        $sort = 'name_asc';
    }

    $price_filter = $_GET['price'] ?? '';  
    if (!is_array($price_filter)) {
        $price_filter = [$price_filter]; // Chuyển về mảng nếu chỉ có một giá trị
    }

    $test_type_filter = $_GET['test_type'] ?? [];
    if (!is_array($test_type_filter)) {
        $test_type_filter = [$test_type_filter]; // Chuyển về mảng nếu chỉ có một giá trị
    }
    
    $conditions = [];
    $query_params = ['%' . $wpdb->esc_like($search_term) . '%', $limit, $offset];

    // Điều kiện lọc theo giá
    if (in_array('free', $price_filter) && in_array('premium', $price_filter)) {
        // Không cần điều kiện gì vì cả free và premium đều được chấp nhận
    } elseif (in_array('free', $price_filter)) {
        $conditions[] = "token_need = 0";
    } elseif (in_array('premium', $price_filter)) {
        $conditions[] = "token_need > 0";
    }
    
  
    

    if (in_array('Practice', $test_type_filter) && in_array('Full Test', $test_type_filter)) {
        // Không cần điều kiện vì chấp nhận cả hai
    } elseif (in_array('Practice', $test_type_filter)) {
        $conditions[] = "test_type = 'Practice'";
    } elseif (in_array('Full Test', $test_type_filter)) {
        $conditions[] = "test_type = 'Full Test'";
    }
    

    // Gộp các điều kiện thành câu SQL
    $where_clause = !empty($conditions) ? 'AND ' . implode(' AND ', $conditions) : '';

    // Xác định ORDER BY theo sort
    switch ($sort) {
        case 'takers_desc':
            $order_by_sql = 'ORDER BY test_taker_count DESC, created_at DESC';
            break;
        case 'created_desc':
            $order_by_sql = 'ORDER BY created_at DESC';
            break;
        case 'name_asc':
        default:
            $order_by_sql = "ORDER BY $search_column ASC";
            break;
    }

    $query = $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE $search_column LIKE %s $where_clause $order_by_sql LIMIT %d OFFSET %d",
        ...$query_params
    );

    $results = $wpdb->get_results($query);
} else {
    $results = [];
}


// Display the navigation buttons
?>
<div class="container-search">
    <div class="content">
        <!-- Existing content -->
    


<div class="post-type-navigation">
    <?php foreach ($test_tables as $type => $table) : ?>
        <a href="<?php echo esc_url(home_url("/practices/" . ($type === 'all' ? '' : $type))); ?>" 
           class="nav-button <?php echo $current_post_type === $type ? 'active' : ''; ?>">
           <?php echo esc_html($test_labels[$type] ?? ucfirst($type)); ?>
           </a>
    <?php endforeach; ?>
</div>

<div class="search-feature">
    <!-- Search Form -->
    <form method="get" action="<?php echo esc_url(home_url('/practices/' . ($current_post_type ? $current_post_type : ''))); ?>" class="search-form">
        <input class="search-input" type="text" name="term" placeholder="Tìm kiếm bài practice..." value="<?php echo esc_attr($search_term); ?>" />

        <div class="filters-bar">
            <div class="test-type-option">
                <label class="chip"><input type="checkbox" name="price[]" value="free" <?php if (in_array('free', $price_filter)) echo 'checked'; ?>><span>Free</span></label>
                <label class="chip"><input type="checkbox" name="price[]" value="premium" <?php if (in_array('premium', $price_filter)) echo 'checked'; ?>><span>Premium</span></label>

                <label class="chip"><input type="checkbox" name="test_type[]" value="Practice" <?php if (in_array('Practice', $test_type_filter)) echo 'checked'; ?>><span>Practice</span></label>
                <label class="chip"><input type="checkbox" name="test_type[]" value="Full Test" <?php if (in_array('Full Test', $test_type_filter)) echo 'checked'; ?>><span>Full Length</span></label>
            </div>

            <div class="sort-group">
                <label for="sort" class="sort-label">Sắp xếp</label>
                <select id="sort" name="sort" class="sort-select">
                    <option value="name_asc" <?php if (($sort ?? 'name_asc') === 'name_asc') echo 'selected'; ?>>Tên (A–Z)</option>
                    <option value="takers_desc" <?php if (($sort ?? 'name_asc') === 'takers_desc') echo 'selected'; ?>>Nhiều người làm</option>
                    <option value="created_desc" <?php if (($sort ?? 'name_asc') === 'created_desc') echo 'selected'; ?>>Mới nhất</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-filter-submit">Tìm kiếm & Lọc</button>
    </form>



    <?php
        // Mảng quảng cáo cho từng loại test
        $ads_horizontal = [
            'talk-with-edward'        => 'https://your-site.com/path-to-ad/digitalsat-horizontal.jpg',
            'studyvocabulary'  => 'https://your-site.com/path-to-ad/ielts-reading-horizontal.jpg',
            'dictation'=> 'https://your-site.com/path-to-ad/ielts-speaking-horizontal.jpg',
            'shadowing'=> 'https://your-site.com/path-to-ad/ielts-listening-horizontal.jpg',


        ];

        // Nếu loại test hiện tại có quảng cáo thì hiển thị
        if (!empty($ads_horizontal[$current_post_type])) : ?>
            <div class="horizontal-ad">
                <img src="<?php echo esc_url($ads_horizontal[$current_post_type]); ?>" alt="Advertisement">
            </div>
    <?php endif; ?>

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
    <?php 
    // Lấy thông tin username hiện tại (ví dụ sử dụng hàm của WordPress)
    $current_user = wp_get_current_user();
    $username = $current_user->user_login;

    foreach ($results as $test) : 
        // Xác định bảng lưu kết quả và cột cần kiểm tra dựa trên loại bài kiểm tra
        $table_res_name = '';
        $check_column = '';
        if ($current_post_type === 'digitalsat') {
            $table_res_name = 'save_user_result_digital_sat';
            $check_column = 'resulttest'; // Cột cần kiểm tra
        } 
        
        
        
        
        elseif ($current_post_type === 'ieltsreadingtest') {
            $table_res_name = 'save_user_result_ielts_reading';
            $check_column = 'overallband'; // Cột cần kiểm tra
        }
        elseif ($current_post_type === 'ieltsspeakingtests') {
            $table_res_name = 'save_user_result_ielts_speaking';
            $check_column = 'resulttest'; // Cột cần kiểm tra
        }
        elseif ($current_post_type === 'ieltslisteningtest') {
            $table_res_name = 'save_user_result_ielts_listening';
            $check_column = 'overallband'; // Cột cần kiểm tra
        }
        elseif ($current_post_type === 'ieltswritingtests') {
            $table_res_name = 'save_user_result_ielts_writing';
            $check_column = 'resulttest'; // Cột cần kiểm tra
        }

        elseif ($current_post_type === 'thptqg') {
            $table_res_name = 'save_user_result_thptqg';
            $check_column = 'overallband'; // Cột cần kiểm tra
        }



        elseif ($current_post_type === 'topikreading') {
            $table_res_name = 'save_user_result_topik_reading';
            $check_column = 'overallband'; // Cột cần kiểm tra
        }
        elseif ($current_post_type === 'topiklistening') {
            $table_res_name = 'save_user_result_topik_listening';
            $check_column = 'overallband'; // Cột cần kiểm tra
        }

        

        // Kiểm tra kết quả trong bảng (dựa trên idtest và username)
        $completed = false;
        if (!empty($table_res_name) && !empty($username)) {
            $completed_query = $wpdb->prepare(
                "SELECT {$check_column} FROM {$table_res_name} 
                 WHERE {$check_column} IS NOT NULL AND idtest = %d AND username = %s",
                $test->id_test,
                $username
            );
            $completed = $wpdb->get_var($completed_query); // Kết quả trả về
        }
    ?>
        <div class="test-item">
            <div class="price-icon">
                   <!-- <i class="fa-solid fa-check" style="color: #63E6BE;"></i> -->
                    <?php
                        if (isset($test->token_need) && $test->token_need > 0) {
                            echo esc_html($test->token_need) . ' tokens';
                        } else {
                            echo 'Free';
                        }
                    ?>

            </div>

            <h2><?php echo esc_html($test->$search_column); ?></h2>
            <div class="test-meta">
                <p>⏱️ <?php echo esc_html($test->time ?? ''); ?> minutes</p>
                <p>📄 <?php echo esc_html($test->number_question ?? ''); ?> questions</p>
                <p><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <?php echo esc_html($test->test_taker_count ?? ''); ?></p>
                <p><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> <?php echo esc_html($test->created_at ?? ''); ?></p>
            </div>
            <?php if ($completed) : ?>
                <div class="completed-icon">
                    <i class="fa-solid fa-check" style="color: #63E6BE;"></i>
                </div>
            <?php endif; ?>
            <?php
                $custom_paths = [
                  

                    'talk-with-edward' => '/practice/talk-with-edward/',
                    'studyvocabulary' => '/practice/vocabulary/package/',
                    'dictation' => '/practice/dictation/',
                    'shadowing' => '/practice/shadowing/'
                    


                ];

               




                $base_path = $custom_paths[$current_post_type] ?? "/test/{$current_post_type}/";
                $test_url = home_url("{$base_path}{$test->id_test}");
            ?>


        <div class="test-actions <?php echo $completed ? 'completed' : 'not-completed'; ?>">
            <a href="<?php echo esc_url($test_url); ?>" class="btn-take-test">Take Test</a>

            <div class="btn-group">
                <?php if ($completed) : ?>
                    <!-- Result button -->
                    <button class="btn-icon result-icon" data-test-id="<?php echo $test->id_test; ?>" title="View Result">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </button>
                <?php endif; ?>

                <!-- Info button -->
                <button class="btn-icon info-icon" data-test-id="<?php echo $test->id_test; ?>" data-completed="<?php echo $completed ? '1' : '0'; ?>" title="Test Info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </button>
            </div>
        </div>

        </div>
    <?php endforeach; ?>
</div>


        <!-- Pagination -->
        <div class="pagination">
            <?php
            $total_tests = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE $search_column LIKE %s $where_clause",
                '%' . $wpdb->esc_like($search_term) . '%'
            ));
            $total_pages = ceil($total_tests / $limit);
            

            echo paginate_links([
                'total' => $total_pages, // Số trang tính toán chính xác
                'current' => $paged,
                'format' => '?paged=%#%',
                'prev_text' => '&laquo; Prev',
                'next_text' => 'Next &raquo;',
                'add_args' => [
                    'term' => $search_term,
                    'sort' => $sort ?? 'name_asc',
                    'price' => $price_filter,
                    'test_type' => $test_type_filter,
                ],
            ]);
            ?>
        </div>
    <?php else : ?>
        <p>Không tìm thấy bài thi nào với từ khóa "<?php echo esc_html($search_term); ?>" cho loại bài thi "<?php echo ucfirst($current_post_type); ?>"</p>
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
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.innerHTML = `
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Loading...</span>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Vui lòng chờ...</p>
            </div>
        </div>`;
    document.body.appendChild(overlay);

    const modalTitle = overlay.querySelector('.modal-title');
    const modalBody = overlay.querySelector('.modal-body');

    function openModal(title, content) {
        modalTitle.textContent = title;
        modalBody.innerHTML = content;
        overlay.style.display = 'flex';
    }

    function closeModal() {
        overlay.style.display = 'none';
    }

    overlay.querySelector('.modal-close').addEventListener('click', closeModal);
    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeModal();
    });

    document.querySelectorAll('.info-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const id = this.dataset.testId;
            const completed = this.dataset.completed === '1';
            if (completed) {
                openModal("Kết quả bài test", `<p>Hiển thị kết quả cho test ID: <b>${id}</b></p>`);
            } else {
                openModal("Thông tin bài test", `<p>Hiển thị thông tin cho test ID: <b>${id}</b></p>`);
            }
        });
    });

    document.querySelectorAll('.result-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const id = this.dataset.testId;
            openModal("Kết quả bài test", `<p>Hiển thị kết quả cho test ID: <b>${id}</b></p>`);
        });
    });
});
</script>

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
    
    .test-actions {
    display: flex;
    gap: 6px;
    width: 100%;
    margin-top: 10px;
}

.btn-take-test {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 14px;
    background-color: #0073aa;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    height: 44px;
    transition: background 0.2s ease;
}

.btn-take-test:hover {
    background-color: #005a8c;
}

/* Kích thước theo trạng thái */
.test-actions.completed .btn-take-test {
    flex: 0 0 50%;
}
.test-actions.completed .btn-group {
    flex: 0 0 50%;
}

.test-actions.not-completed .btn-take-test {
    flex: 0 0 75%;
}
.test-actions.not-completed .btn-group {
    flex: 0 0 25%;
}

/* Group chứa icon */
.btn-group {
    display: flex;
    gap: 6px;
    height: 44px;
}

.btn-icon {
    flex: 1;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: #f5f5f5;
    transform: translateY(-1px);
}

.btn-icon svg {
    width: 20px;
    height: 20px;
    stroke: #333;
}

/* Overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(4px);
}

/* Modal container */
.modal {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 520px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: modalFadeIn 0.25s ease;
}

/* Header */
.modal-header {
    background: linear-gradient(135deg, #0073aa, #005a8c);
    color: white;
    padding: 14px 20px;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    font-size: 22px;
    cursor: pointer;
    transition: 0.2s;
    user-select: none;
}
.modal-close:hover {
    transform: rotate(90deg);
}

/* Body */
.modal-body {
    padding: 20px;
    font-size: 15px;
    color: #333;
    line-height: 1.5;
    max-height: 60vh;
    overflow-y: auto;
}

/* Animation */
@keyframes modalFadeIn {
    from { transform: translateY(-20px) scale(0.98); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}


@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-10px);}
    to {opacity: 1; transform: translateY(0);}
}



 .horizontal-ad {
    margin: 20px 0;
    text-align: center;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 10px;
}

.horizontal-ad img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: auto;
    border-radius: 6px;
    transition: transform 0.3s ease;
}

.horizontal-ad img:hover {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .horizontal-ad {
        margin: 15px 0;
        padding: 5px;
    }
}
   
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
        align-items: flex-start;
        min-height: 100vh;
    }
    .content {
        flex: 1;
        min-width: 0;
    }
    .sidebar {
        width: 280px;
        background-color: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow: auto;
        box-shadow: 0 6px 24px rgba(0,0,0,0.06);
        border: 1px solid #eef2f7;
        z-index: 5;
    }
@media (max-width: 1024px) {
    .sidebar { top: 12px; max-height: calc(100vh - 24px); }
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
            height: auto;
            max-height: none;
            position: static;
        }
    }
.token-display {
    display: flex;
    gap: 20px;
    margin: 10px 0;
}

.token-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
}

.token-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
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
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}

.search-input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #dde3ea;
    border-radius: 10px;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.search-input:focus { border-color: #0073aa; box-shadow: 0 0 0 4px rgba(0,115,170,.08); }

.filters-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.test-type-option { display: flex; gap: 8px; flex-wrap: wrap; }
.chip { display: inline-flex; align-items: center; gap: 8px; background: #f3f6f9; border: 1px solid #e5ebf2; padding: 6px 10px; border-radius: 999px; cursor: pointer; user-select: none; }
.chip input { accent-color: #0073aa; }
.chip span { font-size: 13px; color: #334155; }

.sort-group { margin-left: auto; display: flex; align-items: center; gap: 8px; }
.sort-label { font-size: 13px; color: #64748b; }
.sort-select { padding: 8px 10px; border: 1px solid #dde3ea; border-radius: 10px; background: #fff; }

.btn-filter-submit { align-self: flex-start; padding: 10px 16px; background: linear-gradient(135deg, #0073aa, #005a8c); color: #fff; border: none; border-radius: 10px; cursor: pointer; transition: transform .15s ease, box-shadow .2s ease; box-shadow: 0 6px 16px rgba(0, 115, 170, 0.18); }
.btn-filter-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0, 115, 170, 0.22); }
/* Test grid */
.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}
.test-item {
    position: relative;
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 6px 24px rgba(2, 8, 20, 0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}
.test-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(2, 8, 20, 0.1);
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
    background-color: #eef0f3;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 13px;
}

.detail-button {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
    padding: 8px 14px;
    background-color: #0073aa;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    min-height: 40px; /* đảm bảo chiều cao nút */
    text-align: center;
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

/* Ensure sticky works well with very tall content */
.content .test-library { min-height: 50vh; }

/* Responsive adjustments */
@media (max-width: 1024px) { .sidebar { top: 12px; max-height: calc(100vh - 24px); } }
@media (max-width: 768px) {
    .container-search { flex-direction: column; width: 100%; height: 100%; }
    .sidebar { order: -1; width: 100%; height: auto; max-height: none; position: static; }
    .filters-bar { flex-direction: column; align-items: flex-start; }
    .sort-group { margin-left: 0; }
}

</style>
<?php  get_footer() ?>