<?php
/**
 * Template Name: Test Result Template
 */
add_filter('document_title_parts', function ($title) {
    if (get_query_var('pagename') === 'analysis') {
        $post_type = get_query_var('post_type', 'Tất cả các bài kiểm tra');
        $title['title'] = sprintf('Test Analysis', ucfirst($post_type));
    }
    return $title;
});
/*
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\analysis_test.php 
 


*/
$post_types = [
    'all' => 'Tất cả',
    'thptqg' => 'THPTQG',
    'digitalsat' => 'Digital SAT',

    'ieltslisteningtest' => 'Ielts Listening',
    'ieltsspeakingtests' => 'Ielts Speaking',
    'ieltsreadingtest' => 'Ielts Reading',
    'ieltswritingtests' => 'Ielts Writing',

    'topikreading' => 'Topik Reading',
    'topiklistening' => 'Topik Listening',
    'topikwriting' => 'Topik Writing',
    'topikspeaking' => 'Topik Speaking',

    'dictationexercise' => 'Dictation Exercise',
    'studyvocabulary' => 'Study Vocabulary',
];

// Mapping specific post types to custom database tables
$table_mappings = [
    'thptqg' => 'save_user_result_thptqg',
    'digitalsat' => 'save_user_result_digital_sat',


    'ieltsspeakingtests' => 'save_user_result_ielts_speaking',
    'ieltslisteningtest' => 'save_user_result_ielts_listening',
    'ieltsreadingtest' => 'save_user_result_ielts_reading',
    'ieltswritingtests' => 'save_user_result_ielts_writing',
    

    'topikreading' => 'save_user_result_topik_reading',
    'topiklistening' => 'save_user_result_topik_listening',
    'topikwriting' => 'save_user_result_topik_writing',
    'topikspeaking' => 'save_user_result_topik_speaking',

    'dictationexercise' => 'save_user_result_dictation',
    'studyvocabulary' => 'save_user_result_vocab',
];

$current_post_type = get_query_var('post_type', 'all');
$search_term = get_query_var('term', '');
$current_user = wp_get_current_user();
$username = $current_user->user_login;

// Fetch test counts for each post type
$test_counts = [];
foreach ($post_types as $key => $label) {
    if ($key === 'all') {
        $test_counts[$key] = '-'; // Placeholder for "All" option
        continue;
    }

    global $wpdb;
    $table_name = $table_mappings[$key] ?? ''; // Use the mapped table or leave empty

    if (!empty($table_name) && $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
        $test_counts[$key] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE username = %s",
            $username
        ));
    } else {
        $test_counts[$key] = 0;
    }
}

get_header();
?>
<!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\analysis_test.php 
 


-->
<div class="result-template">
    <div class="tabs">
        <?php foreach ($post_types as $key => $label): ?>
            <a href="<?php echo add_query_arg(['post_type' => $key, 'term' => $search_term], home_url('/analysis/')); ?>"
               class="tab <?php echo $key === $current_post_type ? 'active' : ''; ?>">
                <?php echo esc_html($label); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="filter-bar">
        <form method="get">
            <select name="post_type">
                <?php foreach ($post_types as $key => $label): ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $current_post_type); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="term" placeholder="Search..." value="<?php echo esc_attr($search_term); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
<!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\analysis_test.php 
 


-->
    <div class="stats">
        <?php foreach ($test_counts as $key => $count): ?>
            <div class="stat-item">
                <span class="stat-label"><?php echo esc_html($post_types[$key]); ?>:</span>
                <span class="stat-value"><?php echo esc_html($count); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\analysis_test.php 
 


-->
    <?php if ($current_post_type !== 'all'): ?>
        <div class="results">
            <h2>Results for <?php echo esc_html($post_types[$current_post_type]); ?></h2>
            <?php
            $table_name = $table_mappings[$current_post_type] ?? ''; // Get the mapped table for current post type
            if (!empty($table_name) && $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE username = %s ORDER BY dateform DESC",
                    $username
                ));
                if ($results):
                    ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Final Result</th>
                            <th>Date</th>
                            <th>Time Spent</th>
                            <th>Xem chi tiết</th>
                        </tr>
                        </thead>
                        <tbody>
                            <!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\analysis_test.php 
 


-->
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo esc_html($result->testname); ?></td>
                                <td><?php echo esc_html($result->finalresult); ?></td>
                                <td><?php echo esc_html($result->dateform); ?></td>
                                <td><?php echo esc_html($result->timedotest); ?></td>
                                <td><?php echo esc_html($result->testsavenumber); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php
                else:
                    echo '<p>No results found.</p>';
                endif;
            } else {
                echo '<p>Invalid post type selected.</p>';
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .result-template { font-family: Arial, sans-serif; margin: 20px; }
    .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
    .tab { padding: 10px; border: 1px solid #ccc; text-decoration: none; color: #000; }
    .tab.active { background: #0073aa; color: #fff; }
    .filter-bar { margin-bottom: 20px; }
    .filter-bar select, .filter-bar input { margin-right: 10px; padding: 5px; }
    .stats { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; }
    .stat-item { border: 1px solid #ccc; padding: 10px; width: 200px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
</style>

<?php
get_footer();
