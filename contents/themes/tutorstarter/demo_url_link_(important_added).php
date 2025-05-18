// Rewrite rule cho test theme cac loai
function add_custom_query_vars($vars) {
    $vars[] = 'custom_number';
    $vars[] = 'custom_action';
    $vars[] = 'name';
    $vars[] = "testsavereadingnumber";
    $vars[] = "testsavedigitalsatnumber";
    $vars[] = "testsaveieltswriting";
    $vars[] = "testsaveieltsspeaking";
    $vars[] = "testsaveconversationai";

    $vars[] = "testsaveieltslistening";
    
    $vars[] = "custom_video_id";

    return $vars;
}
add_filter('query_vars', 'add_custom_query_vars');




function my_tests_rewrite_rules() {
    // Rewrite rule for each post type in the tests page
    add_rewrite_rule(
        '^tests/?$',
        'index.php?pagename=tests',
        'top'
    );
    
    add_rewrite_rule(
        '^tests/([^/]+)/?$',
        'index.php?pagename=tests&post_type=$matches[1]',
        'top'
    );
}
add_action('init', 'my_tests_rewrite_rules');

// Allow 'post_type' and 'term' as query variables
function my_custom_query_vars($vars) {
    $vars[] = 'post_type';
    $vars[] = 'term';
    return $vars;
}
add_filter('query_vars', 'my_custom_query_vars');


function my_tests_template_redirect() {
    if (get_query_var('pagename') === 'tests') {
        include locate_template('template/search-test-page/search_test_template.php');
        exit;
    }
}
add_action('template_redirect', 'my_tests_template_redirect');









// tạo rule cho hệ thống video trên web
function my_video_sys_rewrite_rules() {
    // Rewrite rule for each post type in the tests page
    add_rewrite_rule(
        '^video/?$',
        'index.php?pagename=video',
        'top'
    );
    
    add_rewrite_rule(
        '^video/([^/]+)/?$',
        'index.php?pagename=video&custom_video_id=$matches[1]',
        'top'
    );
}
add_action('init', 'my_video_sys_rewrite_rules');





function my_video_template_redirect() {
    if (get_query_var('pagename') === 'video') {
        include locate_template('template/video_system/template-play.php');
        exit;
    }
}
add_action('template_redirect', 'my_video_template_redirect');



// end tạo rule cho hệ thống video trên web






function analysis_tests_rewrite_rules() {
    // Rewrite rule for each post type in the tests page
    add_rewrite_rule(
        '^analysis/?$',
        'index.php?pagename=analysis',
        'top'
    );
    
    add_rewrite_rule(
        '^analysis/([^/]+)/?$',
        'index.php?pagename=analysis&post_type=$matches[1]',
        'top'
    );
}
add_action('init', 'analysis_tests_rewrite_rules');

// Allow 'post_type' and 'term' as query variables
function analysis_test_custom_query_vars($vars) {
    $vars[] = 'post_type';
    $vars[] = 'term';
    return $vars;
}
add_filter('query_vars', 'analysis_test_custom_query_vars');


function analysis_tests_template_redirect() {
    if (get_query_var('pagename') === 'analysis') {
        include locate_template('template/analysis-test-online/analysis-test.php');
        exit;
    }
}
add_action('template_redirect', 'analysis_tests_template_redirect');




function notification_rewrite_rules() {
    add_rewrite_rule(
        '^notification/?$',
        'index.php?pagename=notification',
        'top'
    );
}
add_action('init', 'notification_rewrite_rules');


function notification_template_redirect() {
    if (get_query_var('pagename') === 'notification') {
        include locate_template('template/notification.php');
        exit;
    }
}
add_action('template_redirect', 'notification_template_redirect');






function my_custom_rewrite_rules() {
    // Rewrite rule for /digitalsat/{custom_number}/{post-name}/start/
    add_rewrite_rule(
        '^digitalsat/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=digitalsat&custom_number=$matches[1]&name=$matches[2]&custom_action=doing',
        'top'
    );
    

  

    // Add rewrite rule for /digitalsat/{custom_number}/{post-name}/get-mark/...
    add_rewrite_rule(
        '^digitalsat/([0-9]+)/([^/]+)/result/([0-9]+)/?$',
        'index.php?post_type=digitalsat&custom_number=$matches[1]&name=$matches[2]&custom_action=result&testsavedigitalsatnumber=$matches[3]',
        'top'
    );

    add_rewrite_rule(
        '^digitalsat/([0-9]+)/([^/]+)/result/([0-9]+)/practice/?$',
        'index.php?post_type=digitalsat&custom_number=$matches[1]&name=$matches[2]&custom_action=practice&testsavedigitalsatnumber=$matches[3]',
        'top'
    );



    // Rewrite rule for /digitalsat/{custom_number}/{post-name}/explanation/
    add_rewrite_rule(
        '^digitalsat/([0-9]+)/([^/]+)/explanation/?$',
        'index.php?post_type=digitalsat&custom_number=$matches[1]&name=$matches[2]&custom_action=explanation',
        'top'
    );
    
    // Rewrite rule for /digitalsat/{custom_number}/{post-name}/
    add_rewrite_rule(
        '^digitalsat/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=digitalsat&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
}
add_action('init', 'my_custom_rewrite_rules');

function custom_template_redirect() {
    global $post;

    if (is_singular('digitalsat')) {
        $custom_number_in_url = get_query_var('custom_number');
        $actual_custom_number = get_post_meta($post->ID, '_digitalsat_custom_number', true);

        if ($custom_number_in_url !== $actual_custom_number) {
            // Redirect to a 404 page if the custom number does not match
            wp_redirect(home_url('/404'));
            exit;
        }

        $custom_action = get_query_var('custom_action');

        // Ensure only exact matches for 'doing' and 'result'
        if ($custom_action === 'doing') {
            include locate_template('template\digitalsat\template-doing.php');
            exit;
        } elseif ($custom_action === 'result') {
            include locate_template('template\digitalsat\template-result.php');
            exit;
        } 
        elseif ($custom_action === 'practice') {
            include locate_template('template\digitalsat\template-practice.php');
            exit;
        } 
        elseif ($custom_action === 'explanation') {
            include locate_template('template\digitalsat\template-explanation.php');
            exit;
        }
        elseif (!empty($custom_action)) {
            // Redirect to 404 for any other action (non-empty custom_action not 'doing' or 'result')
            wp_redirect(home_url('/404'));
            exit;
        }
       
    }
}
add_action('template_redirect', 'custom_template_redirect');



// Add meta box for Answer Box
function add_digitalsat_answer_meta_box() {
    add_meta_box(
        'digitalsat_answer', // Unique ID
        'Answer Box',              // Box title
        'display_digitalsat_answer_meta_box', // Callback function
        'digitalsat',       // Post type
        'side',                    // Context
        'default'                  // Priority
    );
}
add_action('add_meta_boxes', 'add_digitalsat_answer_meta_box');

// Display Answer Box meta box
function display_digitalsat_answer_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'digitalsat_answer_nonce');
    $additional_info = get_post_meta($post->ID, '_digitalsat_answer', true);
?>
    <label for="digitalsat_answer">Answer Box: </label>
    <input type="text" name="digitalsat_answer" id="digitalsat_answer" value="<?php echo esc_attr($additional_info); ?>" />
    <?php
}

// Save Answer Box meta field
function save_digitalsat_answer_field($post_id) {
    if (!isset($_POST['digitalsat_answer_nonce']) || !wp_verify_nonce($_POST['digitalsat_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('digitalsat' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['digitalsat_answer'])) {
        $new_additional_info = sanitize_text_field($_POST['digitalsat_answer']);
        update_post_meta($post_id, '_digitalsat_answer', $new_additional_info);
    }
}
add_action('save_post', 'save_digitalsat_answer_field');








function add_digitalsat_additional_info_meta_box() {
    add_meta_box(
        'digitalsat_additional_info', // Unique ID
        'Additional Info',                  // Box title
        'display_digitalsat_additional_info_meta_box', // Callback function
        'digitalsat',                // Post type
        'side',                             // Context (where on the screen)
        'default'                           // Priority
    );
}
add_action('add_meta_boxes', 'add_digitalsat_additional_info_meta_box');

function display_digitalsat_additional_info_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'digitalsat_additional_info_nonce');
    $additional_info = get_post_meta($post->ID, '_digitalsat_additional_info', true);
    ?>
    <label for="digitalsat_additional_info">Additional Info:</label>
    <textarea name="digitalsat_additional_info" id="digitalsat_additional_info" rows="5" style="width:100%;"><?php echo esc_textarea($additional_info); ?></textarea>
    <?php
}



function save_digitalsat_additional_info_field($post_id) {
    // Check the nonce for security
    if (!isset($_POST['digitalsat_additional_info_nonce']) || !wp_verify_nonce($_POST['digitalsat_additional_info_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // Check if it's an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check user permissions
    if ('digitalsat' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Sanitize and save the additional info field
    if (isset($_POST['digitalsat_additional_info'])) {
        $new_additional_info = sanitize_textarea_field($_POST['digitalsat_additional_info']);
        update_post_meta($post_id, '_digitalsat_additional_info', $new_additional_info);
    }
}

add_action('save_post', 'save_digitalsat_additional_info_field');



// ADD THIS FOR ALL TYPE 
function custom_number_permalink($post_link, $post) {
    if ($post->post_type == 'digitalsat') {
        $custom_number = get_post_meta($post->ID, '_digitalsat_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'ieltswritingtests') {
        $custom_number = get_post_meta($post->ID, '_ieltswritingtests_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'ieltsspeakingtests') {
        $custom_number = get_post_meta($post->ID, '_ieltsspeakingtests_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'conversation_ai') {
        $custom_number = get_post_meta($post->ID, '_conversation_ai_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
   
  
    else if ($post->post_type == 'thptqg') {
        $custom_number = get_post_meta($post->ID, '_thptqg_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'ieltscollection') {
        $custom_number = get_post_meta($post->ID, '_ieltscollection_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'dictationexercise') {
        $custom_number = get_post_meta($post->ID, '_dictationexercise_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    else if ($post->post_type == 'studyvocabulary') {
        $custom_number = get_post_meta($post->ID, '_studyvocabulary_custom_number', true);
        if ($custom_number) {
            $post_link = str_replace('%custom_number%', $custom_number, $post_link);
        }
    }
    
    return $post_link;
}
add_filter('post_type_link', 'custom_number_permalink', 10, 2);




function my_theme_activation() {
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'my_theme_activation');

function add_trash_link_to_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=digitalsat',
        __('Trash', 'textdomain'),
        __('Trash', 'textdomain'),
        'edit_posts',
        'edit.php?post_status=trash&post_type=digitalsat'
    );
}
add_action('admin_menu', 'add_trash_link_to_admin_menu');

// Template redirection based on custom query variable



function add_digitalsat_meta_box() {
    add_meta_box(
        'digitalsat_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_digitalsat_meta_box', // Callback function
        'digitalsat',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_digitalsat_meta_box');

function display_digitalsat_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'digitalsat_nonce');
    $custom_number = get_post_meta($post->ID, '_digitalsat_custom_number', true);
    ?>
    <label for="digitalsat_custom_number">Custom Number:</label>
    <input type="number" name="digitalsat_custom_number" id="digitalsat_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_digitalsat_custom_field($post_id) {
    // Check the nonce for security
    if (!isset($_POST['digitalsat_nonce']) || !wp_verify_nonce($_POST['digitalsat_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // Check if it's an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check user permissions
    if ('digitalsat' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Sanitize and save the number field
    if (isset($_POST['digitalsat_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_digitalsat_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['digitalsat_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_digitalsat_custom_number', $new_custom_number);
            flush_rewrite_rules(); // Flush rewrite rules to update permalinks
        }
    }
}
add_action('save_post', 'save_digitalsat_custom_field');


// Add the Custom Number column to the digitalsat post type
function add_custom_number_column($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_digitalsat_posts_columns', 'add_custom_number_column');

// Populate the Custom Number column with data
function custom_number_column_content($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_digitalsat_custom_number', true);
        echo esc_html($custom_number);
    }
}
add_action('manage_digitalsat_posts_custom_column', 'custom_number_column_content', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-digitalsat_sortable_columns', 'custom_number_column_sortable');
// Display the Custom Number field in Quick Edit
function quick_edit_custom_box_combined($column_name, $post_type) {
    // Check if the column is 'custom_number' and if the post type matches either 'digitalsat' or 'ieltswritingtests or ieltsspeakingtests'
    if ($column_name == 'custom_number' && ($post_type == 'digitalsat' || $post_type == 'ieltswritingtests'|| $post_type == 'ieltsspeakingtests'|| $post_type == 'ieltsreadingtest'|| $post_type == 'ieltscollection'|| $post_type == 'dictationexercise'|| $post_type == 'thptqg' || $post_type == 'studyvocabulary')) {

        // Determine the name and title attributes based on the post type
        if ($post_type == 'digitalsat') {
            $name_attribute = 'digitalsat_custom_number';
            $title_text = 'Custom Number Test';
        } else if ($post_type == 'ieltswritingtests'){
            $name_attribute = 'ieltswritingtests_custom_number';
            $title_text = 'Custom Number Writing';
        }else if ($post_type == 'ieltsspeakingtests'){
            $name_attribute = 'ieltsspeakingtests_custom_number';
            $title_text = 'Custom Number Speaking';
        }
        else if ($post_type == 'conversation_ai'){
            $name_attribute = 'conversation_ai_custom_number';
            $title_text = 'Custom Number Conversation AI';
        }
       
        else if ($post_type == 'ieltscollection'){
            $name_attribute = 'ieltscollection_custom_number';
            $title_text = 'Custom Number Collection Ielts';
        }
        else if ($post_type == 'dictationexercise'){
            $name_attribute = 'dictationexercise_custom_number';
            $title_text = 'Custom Number Dictation Exercise';
        }
        else if ($post_type == 'studyvocabulary'){
            $name_attribute = 'studyvocabulary_custom_number';
            $title_text = 'Custom Vocabulary Test Number';
        }
        
        ?>

        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php echo esc_html($title_text); ?></span>
                    <span class="input-text-wrap">
                        <input type="text" name="<?php echo esc_attr($name_attribute); ?>" value="">
                    </span>
                </label>
            </div>
        </fieldset>

        <?php
    }
}
add_action('quick_edit_custom_box', 'quick_edit_custom_box_combined', 10, 2);

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number($post_id) {
    if (isset($_POST['digitalsat_custom_number'])) {
        update_post_meta($post_id, '_digitalsat_custom_number', sanitize_text_field($_POST['digitalsat_custom_number']));
    }
}
add_action('save_post', 'save_quick_edit_custom_number');



function add_custom_testonlinesystem_column($columns) {
    $columns['custom_action'] = 'All Page';  // Add your custom column
    return $columns;
}
add_filter('manage_digitalsat_posts_columns', 'add_custom_testonlinesystem_column');

// Populate the custom column with the button or action
function custom_column_content($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro Test Page</a>';
        echo '<a href="' . $view_link . 'doing" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'result" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'explanation" class="button">Answer and Explanation Page</a>';

    }
}
add_action('manage_digitalsat_posts_custom_column', 'custom_column_content', 10, 2);






// new add for ieltswritingtests
// Rewrite rule for the 'ieltswritingtests' post type
function my_ieltswritingtests_rewrite_rules() {
    // Rewrite rule for /ieltswritingtests/{custom_number}/{post-name}/testing/
    add_rewrite_rule(
        '^ieltswritingtests/([a-zA-Z0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=ieltswritingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=testing',
        'top'
    );

    // Rewrite rule for /ieltswritingtests/{custom_number}/{post-name}/get-mark/
   

    // Rewrite rule for /ieltswritingtests/{custom_number}/{post-name}/sample-and-explanation/
    add_rewrite_rule(
        '^ieltswritingtests/([a-zA-Z0-9]+)/([^/]+)/sample-and-explanation/?$',
        'index.php?post_type=ieltswritingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=sample-and-explanation',
        'top'
    );

    // Add rewrite rule for /ieltswritingtests/{custom_number}/{post-name}/get-mark/...
    add_rewrite_rule(
        '^ieltswritingtests/([0-9]+)/([^/]+)/result/([0-9]+)/?$',
        'index.php?post_type=ieltswritingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=get-mark&testsaveieltswriting=$matches[3]',
        'top'
    );

    // Rewrite rule for /ieltswritingtests/{custom_number}/{post-name}/
    add_rewrite_rule(
        '^ieltswritingtests/([a-zA-Z0-9]+)/([^/]+)/?$',
        'index.php?post_type=ieltswritingtests&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
}
add_action('init', 'my_ieltswritingtests_rewrite_rules');


function ieltswritingtests_template_redirect() {
    global $post;

    if (is_singular('ieltswritingtests')) {
        $custom_number_in_url = get_query_var('custom_number');
        $actual_custom_number = get_post_meta($post->ID, '_ieltswritingtests_custom_number', true);

        if ($custom_number_in_url !== $actual_custom_number) {
            // Redirect to a 404 page if the custom number does not match
            wp_redirect(home_url('/404'));
            exit;
        }
        $custom_action = get_query_var('custom_action');

        // Ensure only exact matches for 'testing' and 'get-mark'
        if ($custom_action === 'testing') {
            include locate_template('template\ieltswritingtest\template-testing.php');
            exit;
        } elseif ($custom_action === 'get-mark') {
            include locate_template('template/ieltswritingtest/template-get-mark.php');
            exit;
        } 
        elseif ($custom_action === 'sample-and-explanation') {
            include locate_template('template/ieltswritingtest/template-sample-writing.php');
            exit;
        }
        elseif (!empty($custom_action)) {
            // Redirect to 404 for any other action (non-empty custom_action not 'testing' or 'get-mark')
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'ieltswritingtests_template_redirect');


// Save Answer Box meta field
function save_ieltswritingtests_answer_field($post_id) {
    if (!isset($_POST['ieltswritingtests_answer_nonce']) || !wp_verify_nonce($_POST['ieltswritingtests_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('ieltswritingtests' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    
}
add_action('save_post', 'save_ieltswritingtests_answer_field');


// Add meta box for Custom Number
function add_ieltswritingtests_meta_box() {
    add_meta_box(
        'ieltswritingtests_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_ieltswritingtests_meta_box', // Callback function
        'ieltswritingtests',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_ieltswritingtests_meta_box');

function display_ieltswritingtests_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'ieltswritingtests_nonce');
    $custom_number = get_post_meta($post->ID, '_ieltswritingtests_custom_number', true);
    ?>
    <label for="ieltswritingtests_custom_number">Custom Number:</label>
    <input type="text" name="ieltswritingtests_custom_number" id="ieltswritingtests_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_ieltswritingtests_custom_field($post_id) {
    if (!isset($_POST['ieltswritingtests_nonce']) || !wp_verify_nonce($_POST['ieltswritingtests_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('ieltswritingtests' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['ieltswritingtests_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_ieltswritingtests_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['ieltswritingtests_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_ieltswritingtests_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_ieltswritingtests_custom_field');

// Add the Custom Number column to the ieltswritingtests post type
function add_custom_number_column_ieltswritingtests($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_ieltswritingtests_posts_columns', 'add_custom_number_column_ieltswritingtests');

// Populate the Custom Number column with data
function custom_number_column_content_ieltswritingtests($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_ieltswritingtests_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_ieltswritingtests_posts_custom_column', 'custom_number_column_content_ieltswritingtests', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_ieltswritingtests($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-ieltswritingtests_sortable_columns', 'custom_number_column_sortable_ieltswritingtests');


// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_ieltswritingtests($post_id) {
    if (isset($_POST['ieltswritingtests_custom_number'])) {
        update_post_meta($post_id, '_ieltswritingtests_custom_number', sanitize_text_field($_POST['ieltswritingtests_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_ieltswritingtests');

// Add a new column to the list of custom post type items
function add_custom_column_ieltswriting($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_ieltswritingtests_posts_columns', 'add_custom_column_ieltswriting');

// Populate the custom column with the button or action
function custom_column_content_ieltswriting($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">View Intro Test Page</a>';
        echo '<a href="' . $view_link . 'start/" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'result/" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_ieltswritingtests_posts_custom_column', 'custom_column_content_ieltswriting', 10, 2);














// add new for speaking


// new add for ieltsspeakingtests

// Add the new custom rewrite rule for the "choose-speaking-ielts-question" action without custom_number
function my_ieltsspeakingtests_rewrite_rules() {
    // Rewrite rule for /ieltsspeakingtests/choose-question/
    add_rewrite_rule(
        '^ieltsspeakingtests/choose-question/?$',
        'index.php?post_type=ieltsspeakingtests&custom_action=choose-question',
        'top'
    );

    // Rewrite rule for /ieltsspeakingtests/full-ielts-speaking-test/
    add_rewrite_rule(
        '^ieltsspeakingtests/full-ielts-speaking-test/?$',
        'index.php?post_type=ieltsspeakingtests&custom_action=do-full-ielts-speaking-test',
        'top'
    );
    // Existing rewrite rules for other actions
    add_rewrite_rule(
        '^ieltsspeakingtests/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=ieltsspeakingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=testing-speaking',
        'top'
    );
    

    add_rewrite_rule(
        '^ieltsspeakingtests/([0-9]+)/([^/]+)/result/([0-9]+)/?$',
        'index.php?post_type=ieltsspeakingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=get-mark-speaking&testsaveieltsspeaking=$matches[3]',
        'top'
    );


    add_rewrite_rule(
        '^ieltsspeakingtests/([0-9]+)/([^/]+)/sample-and-explanation/?$',
        'index.php?post_type=ieltsspeakingtests&custom_number=$matches[1]&name=$matches[2]&custom_action=sample-answer-speaking',
        'top'
    );
    add_rewrite_rule(
        '^ieltsspeakingtests/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=ieltsspeakingtests&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
}
add_action('init', 'my_ieltsspeakingtests_rewrite_rules');

function ieltsspeakingtests_template_redirect() {
    if (is_singular('ieltsspeakingtests')) {
        global $wp_query;

        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');

        // If the custom action is 'choose-ielts-speaking-question', load the custom template
        if ($custom_action === 'choose-question') {
            include locate_template('template/ieltsspeakingtests/template-choose-question.php');
            exit;
        }
        else if ($custom_action === 'do-full-ielts-speaking-test') {
            include locate_template('template/ieltsspeakingtests/template-full-ielts-speaking-test-custom.php');
            exit;
        }
        

        // Handle other custom actions like 'testing-speaking', 'get-mark-speaking', etc.
        else if ($custom_action === 'testing-speaking') {
            include locate_template('template/ieltsspeakingtests/template-testing-speaking.php');
            exit;
        } else if ($custom_action === 'get-mark-speaking') {
            include locate_template('template/ieltsspeakingtests/template-get-mark-speaking.php');
            exit;
        } else if ($custom_action === 'sample-answer-speaking') {
            include locate_template('template/ieltsspeakingtests/template-explanation-speaking.php');
            exit;
        }
        

        if (!empty($custom_action)) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'ieltsspeakingtests_template_redirect');

// Save Answer Box meta field
function save_ieltsspeakingtests_answer_field($post_id) {
    if (!isset($_POST['ieltsspeakingtests_answer_nonce']) || !wp_verify_nonce($_POST['ieltsspeakingtests_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('ieltsspeakingtests' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

   
}
add_action('save_post', 'save_ieltsspeakingtests_answer_field');

// Add meta box for Custom Number
function add_ieltsspeakingtests_meta_box() {
    add_meta_box(
        'ieltsspeakingtests_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_ieltsspeakingtests_meta_box', // Callback function
        'ieltsspeakingtests',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_ieltsspeakingtests_meta_box');

function display_ieltsspeakingtests_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'ieltsspeakingtests_nonce');
    $custom_number = get_post_meta($post->ID, '_ieltsspeakingtests_custom_number', true);
    ?>
    <label for="ieltsspeakingtests_custom_number">Custom Number:</label>
    <input type="number" name="ieltsspeakingtests_custom_number" id="ieltsspeakingtests_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_ieltsspeakingtests_custom_field($post_id) {
    if (!isset($_POST['ieltsspeakingtests_nonce']) || !wp_verify_nonce($_POST['ieltsspeakingtests_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('ieltsspeakingtests' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['ieltsspeakingtests_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_ieltsspeakingtests_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['ieltsspeakingtests_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_ieltsspeakingtests_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_ieltsspeakingtests_custom_field');

// Add the Custom Number column to the ieltsspeakingtests post type
function add_custom_number_column_ieltsspeakingtests($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_ieltsspeakingtests_posts_columns', 'add_custom_number_column_ieltsspeakingtests');

// Populate the Custom Number column with data
function custom_number_column_content_ieltsspeakingtests($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_ieltsspeakingtests_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_ieltsspeakingtests_posts_custom_column', 'custom_number_column_content_ieltsspeakingtests', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_ieltsspeakingtests($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-ieltsspeakingtests_sortable_columns', 'custom_number_column_sortable_ieltsspeakingtests');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_ieltsspeakingtests($post_id) {
    if (isset($_POST['ieltsspeakingtests_custom_number'])) {
        update_post_meta($post_id, '_ieltsspeakingtests_custom_number', sanitize_text_field($_POST['ieltsspeakingtests_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_ieltsspeakingtests');


// Add a new column to the list of custom post type items
function add_custom_column_ieltsspeaking($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_ieltsspeakingtests_posts_columns', 'add_custom_column_ieltsspeaking');

// Populate the custom column with the button or action
function custom_column_content_ieltsspeaking($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'start/" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'result/" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_ieltsspeakingtests_posts_custom_column', 'custom_column_content_ieltsspeaking', 10, 2);













////// BA CÁI ĐẦU ĐÃ ỔN



/// check từ đây



// add new for speaking

// new add for ieltscollection

// Add the new custom rewrite rule for the "choose-speaking-ielts-question" action without custom_number
function my_ieltscollection_rewrite_rules() {
    // Rewrite rule for /ieltscollection/choose-ielts-speaking-question/
    add_rewrite_rule(
        '^ieltscollection/choose-ielts-speaking-question/?$',
        'index.php?post_type=ieltscollection&custom_action=choose-ielts-speaking-question',
        'top'
    );

    // Rewrite rule for /ieltscollection/full-ielts-speaking-test/
    add_rewrite_rule(
        '^ieltscollection/full-ielts-speaking-test/?$',
        'index.php?post_type=ieltscollection&custom_action=do-full-ielts-speaking-test',
        'top'
    );

    // Existing rewrite rules for other actions
    add_rewrite_rule(
        '^ieltscollection/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=ieltscollection&custom_number=$matches[1]&name=$matches[2]&custom_action=start',
        'top'
    );
    add_rewrite_rule(
        '^ieltscollection/([0-9]+)/([^/]+)/get-mark-speaking/?$',
        'index.php?post_type=ieltscollection&custom_number=$matches[1]&name=$matches[2]&custom_action=get-mark-speaking',
        'top'
    );
    add_rewrite_rule(
        '^ieltscollection/([0-9]+)/([^/]+)/sample-and-explanation/?$',
        'index.php?post_type=ieltscollection&custom_number=$matches[1]&name=$matches[2]&custom_action=sample-answer-speaking',
        'top'
    );
    add_rewrite_rule(
        '^ieltscollection/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=ieltscollection&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
    flush_rewrite_rules();  // Thêm tạm thời ở đây

}
add_action('init', 'my_ieltscollection_rewrite_rules');

function ieltscollection_template_redirect() {
    if (is_singular('ieltscollection')) {
        global $wp_query;
        global $post;

        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');

        // If the custom action is 'choose-ielts-speaking-question', load the custom template
        if ($custom_action === 'choose-ielts-speaking-question') {
            include locate_template('template/ieltsspeakingtest/choose-question-speaking-full-part.php');
            exit;
        }
        else if ($custom_action === 'do-full-ielts-speaking-test') {
            include locate_template('template/ieltsspeakingtest/template-full-ielts-speaking-test-custom.php');
            exit;
        }
    

        else if ($custom_action === 'start') {
            if (has_term('speaking-collection', 'test_category', $post)) {
                include locate_template('template/ieltsspeakingtests/template-testing-speaking.php');
                exit;
        } elseif (has_term('writing-collection', 'test_category', $post)) {
                include locate_template('template\ieltswritingtest\template-testing.php');
                exit;
        }
            echo 'Template path: ' . locate_template('template/ieltsspeakingtest/template-testing-speaking.php');
            exit;
        }


        else if ($custom_action === 'get-mark-speaking') {
            include locate_template('template/ieltsspeakingtest/template-get-mark-speaking.php');
            exit;
        } else if ($custom_action === 'sample-answer-speaking') {
            include locate_template('template/ieltsspeakingtest/template-explanation-speaking.php');
            exit;
        }
        

        // Optionally, redirect to a 404 page if the custom action doesn't match any known values
        if (!empty($custom_action)) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'ieltscollection_template_redirect');



// Add meta box for Custom Number
function add_ieltscollection_meta_box() {
    add_meta_box(
        'ieltscollection_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_ieltscollection_meta_box', // Callback function
        'ieltscollection',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_ieltscollection_meta_box');

function display_ieltscollection_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'ieltscollection_nonce');
    $custom_number = get_post_meta($post->ID, '_ieltscollection_custom_number', true);
    ?>
    <label for="ieltscollection_custom_number">Custom Number:</label>
    <input type="number" name="ieltscollection_custom_number" id="ieltscollection_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_ieltscollection_custom_field($post_id) {
    if (!isset($_POST['ieltscollection_nonce']) || !wp_verify_nonce($_POST['ieltscollection_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('ieltscollection' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['ieltscollection_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_ieltscollection_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['ieltscollection_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_ieltscollection_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_ieltscollection_custom_field');

// Add the Custom Number column to the ieltscollection post type
function add_custom_number_column_ieltscollection($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_ieltscollection_posts_columns', 'add_custom_number_column_ieltscollection');

// Populate the Custom Number column with data
function custom_number_column_content_ieltscollection($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_ieltscollection_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_ieltscollection_posts_custom_column', 'custom_number_column_content_ieltscollection', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_ieltscollection($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-ieltscollection_sortable_columns', 'custom_number_column_sortable_ieltscollection');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_ieltscollection($post_id) {
    if (isset($_POST['ieltscollection_custom_number'])) {
        update_post_meta($post_id, '_ieltscollection_custom_number', sanitize_text_field($_POST['ieltscollection_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_ieltscollection');


// Add a new column to the list of custom post type items
function add_custom_column_ieltsexam ($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_ieltscollection_posts_columns', 'add_custom_column_ieltsexam');

// Populate the custom column with the button or action
function custom_column_content_ieltsexam ($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'start" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'get-mark-speaking" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_ieltscollection_posts_custom_column', 'custom_column_content_ieltsexam', 10, 2);














// new add for dictationexercise

// Add the new custom rewrite rule for the "choose-speaking-ielts-question" action without custom_number
function my_dictationexercise_rewrite_rules() {
    // Rewrite rule for /dictationexercise/choose-ielts-speaking-question/
    

    // Existing rewrite rules for other actions
    add_rewrite_rule(
        '^dictationexercise/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=dictationexercise&custom_number=$matches[1]&name=$matches[2]&custom_action=start',
        'top'
    );
    add_rewrite_rule(
        '^dictationexercise/([0-9]+)/([^/]+)/get-mark-speaking/?$',
        'index.php?post_type=dictationexercise&custom_number=$matches[1]&name=$matches[2]&custom_action=get-mark-speaking',
        'top'
    );
    add_rewrite_rule(
        '^dictationexercise/([0-9]+)/([^/]+)/sample-and-explanation/?$',
        'index.php?post_type=dictationexercise&custom_number=$matches[1]&name=$matches[2]&custom_action=sample-answer-speaking',
        'top'
    );
    add_rewrite_rule(
        '^dictationexercise/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=dictationexercise&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
    flush_rewrite_rules();  // Thêm tạm thời ở đây

}
add_action('init', 'my_dictationexercise_rewrite_rules');

function dictationexercise_template_redirect() {
    if (is_singular('dictationexercise')) {
        global $wp_query;
        global $post;

        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');

        

        if ($custom_action === 'start') {
                include locate_template('template\dictationexercise\template-start.php');
                exit;
        }
        else if ($custom_action === 'get-result') {
            include locate_template('template/dictationexercise/template-get-mark-speaking.php');
            exit;
        } else if ($custom_action === 'sample-answer-speaking') {
            include locate_template('template/dictationexercise/template-explanation-speaking.php');
            exit;
        }
        

        // Optionally, redirect to a 404 page if the custom action doesn't match any known values
        if (!empty($custom_action)) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'dictationexercise_template_redirect');



/*
// Add meta box for Answer Box
function add_dictationexercise_answer_meta_box() {
    add_meta_box(
        'dictationexercise_answer', // Unique ID
        'Answer Box',              // Box title
        'display_dictationexercise_answer_meta_box', // Callback function
        'dictationexercise',       // Post type
        'side',                    // Context
        'default'                  // Priority
    );
}
add_action('add_meta_boxes', 'add_dictationexercise_answer_meta_box');

// Display Answer Box meta box
function display_dictationexercise_answer_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'dictationexercise_answer_nonce');
    $additional_info = get_post_meta($post->ID, '_dictationexercise_answer', true);
    ?>
    <label for="dictationexercise_answer">Answer Box: </label>
    <input type="text" name="dictationexercise_answer" id="dictationexercise_answer" value="<?php echo esc_attr($additional_info); ?>" />
    <?php
}

// Save Answer Box meta field
function save_dictationexercise_answer_field($post_id) {
    if (!isset($_POST['dictationexercise_answer_nonce']) || !wp_verify_nonce($_POST['dictationexercise_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('dictationexercise' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['dictationexercise_answer'])) {
        $new_additional_info = sanitize_text_field($_POST['dictationexercise_answer']);
        update_post_meta($post_id, '_dictationexercise_answer', $new_additional_info);
    }
}
add_action('save_post', 'save_dictationexercise_answer_field');

// The rest of the code follows a similar pattern, replacing 'ieltswritingtests' with 'dictationexercise'.

// Add meta box for Additional Info
function add_dictationexercise_additional_info_meta_box() {
    add_meta_box(
        'dictationexercise_additional_info', // Unique ID
        'Additional Info',                  // Box title
        'display_dictationexercise_additional_info_meta_box', // Callback function
        'dictationexercise',                // Post type
        'side',                             // Context (where on the screen)
        'default'                           // Priority
    );
}
add_action('add_meta_boxes', 'add_dictationexercise_additional_info_meta_box');

// Display Additional Info meta box
function display_dictationexercise_additional_info_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'dictationexercise_additional_info_nonce');
    $additional_info = get_post_meta($post->ID, '_dictationexercise_additional_info', true);
    ?>
    <label for="dictationexercise_additional_info">Additional Info:</label>
    <textarea name="dictationexercise_additional_info" id="dictationexercise_additional_info" rows="5" style="width:100%;"><?php echo esc_textarea($additional_info); ?></textarea>
    <?php
}

// Continue the rest of the script similarly...

// Save Additional Info meta field
function save_dictationexercise_additional_info_field($post_id) {
    if (!isset($_POST['dictationexercise_additional_info_nonce']) || !wp_verify_nonce($_POST['dictationexercise_additional_info_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('dictationexercise' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['dictationexercise_additional_info'])) {
        $new_additional_info = sanitize_textarea_field($_POST['dictationexercise_additional_info']);
        update_post_meta($post_id, '_dictationexercise_additional_info', $new_additional_info);
    }
}
add_action('save_post', 'save_dictationexercise_additional_info_field');
*/
// Add meta box for Custom Number
function add_dictationexercise_meta_box() {
    add_meta_box(
        'dictationexercise_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_dictationexercise_meta_box', // Callback function
        'dictationexercise',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_dictationexercise_meta_box');

function display_dictationexercise_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'dictationexercise_nonce');
    $custom_number = get_post_meta($post->ID, '_dictationexercise_custom_number', true);
    ?>
    <label for="dictationexercise_custom_number">Custom Number:</label>
    <input type="number" name="dictationexercise_custom_number" id="dictationexercise_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_dictationexercise_custom_field($post_id) {
    if (!isset($_POST['dictationexercise_nonce']) || !wp_verify_nonce($_POST['dictationexercise_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('dictationexercise' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['dictationexercise_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_dictationexercise_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['dictationexercise_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_dictationexercise_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_dictationexercise_custom_field');

// Add the Custom Number column to the dictationexercise post type
function add_custom_number_column_dictationexercise($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_dictationexercise_posts_columns', 'add_custom_number_column_dictationexercise');

// Populate the Custom Number column with data
function custom_number_column_content_dictationexercise($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_dictationexercise_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_dictationexercise_posts_custom_column', 'custom_number_column_content_dictationexercise', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_dictationexercise($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-dictationexercise_sortable_columns', 'custom_number_column_sortable_dictationexercise');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_dictationexercise($post_id) {
    if (isset($_POST['dictationexercise_custom_number'])) {
        update_post_meta($post_id, '_dictationexercise_custom_number', sanitize_text_field($_POST['dictationexercise_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_dictationexercise');


// Add a new column to the list of custom post type items
function add_custom_column_dictation ($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_dictationexercise_posts_columns', 'add_custom_column_dictation');

// Populate the custom column with the button or action
function custom_column_content_dictation ($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'start" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'get-mark-speaking" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_dictationexercise_posts_custom_column', 'custom_column_content_dictation', 10, 2);










// Add the new custom rewrite rule for the "choose-thptqg-ielts-question" action without custom_number
function my_thptqg_rewrite_rules() {
    // Add a basic rewrite rule for /thptqg/{custom_number}/{post-name}/
    add_rewrite_rule(
        '^thptqg/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=thptqg&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );


    // Add rewrite rule for /thptqg/{custom_number}/{post-name}/testingreading/
    add_rewrite_rule(
        '^thptqg/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=thptqg&custom_number=$matches[1]&name=$matches[2]&custom_action=start',
        'top'
    );
    
    
    

    // Add rewrite rule for /thptqg/{custom_number}/{post-name}/get-mark/...
    add_rewrite_rule(
        '^thptqg/([0-9]+)/([^/]+)/result/([0-9]+)/?$',
        'index.php?post_type=thptqg&custom_number=$matches[1]&name=$matches[2]&custom_action=result&testsavereadingnumber=$matches[3]',
        'top'
    );
    
  
}
add_action('init', 'my_thptqg_rewrite_rules');


function thptqg_template_redirect() {
     if (is_singular('thptqg')) {
        global $post;
        global $wp_query;
        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');
        if ($custom_action === 'start') {
            include locate_template('template\thptqg\template-testing-thptqg.php');
                exit;
        } else if ($custom_action === 'result') {
            include locate_template('template\thptqg\template-get-mark-thptqg.php');
            exit;
        } 
        if (!empty($custom_action)) {
                wp_redirect(home_url('/404'));
                exit;
            
        }
    }
}
add_action('template_redirect', 'thptqg_template_redirect');


/*
// Add meta box for Answer Box
function add_thptqg_answer_meta_box() {
    add_meta_box(
        'thptqg_answer', // Unique ID
        'Answer Box',              // Box title
        'display_thptqg_answer_meta_box', // Callback function
        'thptqg',       // Post type
        'side',                    // Context
        'default'                  // Priority
    );
}
add_action('add_meta_boxes', 'add_thptqg_answer_meta_box');

// Display Answer Box meta box
function display_thptqg_answer_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'thptqg_answer_nonce');
    $additional_info = get_post_meta($post->ID, '_thptqg_answer', true);
    ?>
    <label for="thptqg_answer">Answer Box: </label>
    <input type="text" name="thptqg_answer" id="thptqg_answer" value="<?php echo esc_attr($additional_info); ?>" />
    <?php
}
*/
// Save Answer Box meta field
function save_thptqg_answer_field($post_id) {
    if (!isset($_POST['thptqg_answer_nonce']) || !wp_verify_nonce($_POST['thptqg_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('thptqg' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

   /* if (isset($_POST['thptqg_answer'])) {
        $new_additional_info = sanitize_text_field($_POST['thptqg_answer']);
        update_post_meta($post_id, '_thptqg_answer', $new_additional_info);
    }*/
}
add_action('save_post', 'save_thptqg_answer_field');

// Add meta box for Custom Number
function add_thptqg_meta_box() {
    add_meta_box(
        'thptqg_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_thptqg_meta_box', // Callback function
        'thptqg',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_thptqg_meta_box');

function display_thptqg_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'thptqg_nonce');
    $custom_number = get_post_meta($post->ID, '_thptqg_custom_number', true);
    ?>
    <label for="thptqg_custom_number">Custom Number:</label>
    <input type="number" name="thptqg_custom_number" id="thptqg_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_thptqg_custom_field($post_id) {
    if (!isset($_POST['thptqg_nonce']) || !wp_verify_nonce($_POST['thptqg_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('thptqg' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['thptqg_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_thptqg_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['thptqg_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_thptqg_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_thptqg_custom_field');

// Add the Custom Number column to the thptqg post type
function add_custom_number_column_thptqg($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_thptqg_posts_columns', 'add_custom_number_column_thptqg');

// Populate the Custom Number column with data
function custom_number_column_content_thptqg($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_thptqg_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_thptqg_posts_custom_column', 'custom_number_column_content_thptqg', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_thptqg($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-thptqg_sortable_columns', 'custom_number_column_sortable_thptqg');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_thptqg($post_id) {
    if (isset($_POST['thptqg_custom_number'])) {
        update_post_meta($post_id, '_thptqg_custom_number', sanitize_text_field($_POST['thptqg_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_thptqg');


// Add a new column to the list of custom post type items
function add_custom_column_thptqg($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_thptqg_posts_columns', 'add_custom_column_thptqg');

// Populate the custom column with the button or action
function custom_column_content_thptqg($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'start" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'result" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_thptqg_posts_custom_column', 'custom_column_content_thptqg', 10, 2);










// new add for studyvocabulary

// Add the new custom rewrite rule for the "choose-speaking-ielts-question" action without custom_number
function my_studyvocabulary_rewrite_rules() {
    // Rewrite rule for /studyvocabulary/choose-ielts-speaking-question/
    

    // Existing rewrite rules for other actions
    add_rewrite_rule(
        '^studyvocabulary/([0-9]+)/([^/]+)/flashcard/?$',
        'index.php?post_type=studyvocabulary&custom_number=$matches[1]&name=$matches[2]&custom_action=flashcard',
        'top'
    );
    add_rewrite_rule(
        '^studyvocabulary/([0-9]+)/([^/]+)/test/?$',
        'index.php?post_type=studyvocabulary&custom_number=$matches[1]&name=$matches[2]&custom_action=test',
        'top'
    );
    add_rewrite_rule(
        '^studyvocabulary/([0-9]+)/([^/]+)/sample-and-explanation/?$',
        'index.php?post_type=studyvocabulary&custom_number=$matches[1]&name=$matches[2]&custom_action=sample-answer-speaking',
        'top'
    );
    add_rewrite_rule(
        '^studyvocabulary/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=studyvocabulary&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
    flush_rewrite_rules();  // Thêm tạm thời ở đây

}
add_action('init', 'my_studyvocabulary_rewrite_rules');

function studyvocabulary_template_redirect() {
    if (is_singular('studyvocabulary')) {
        global $wp_query;
        global $post;

        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');

        

        if ($custom_action === 'flashcard') {
                include locate_template('template\studyvocabulary\template-flashcard.php');
                exit;
        }
        else if ($custom_action === 'test') {
            include locate_template('template/studyvocabulary/template-test.php');
            exit;
        } else if ($custom_action === 'sample-answer-speaking') {
            include locate_template('template/studyvocabulary/template-explanation-speaking.php');
            exit;
        }
        

        // Optionally, redirect to a 404 page if the custom action doesn't match any known values
        if (!empty($custom_action)) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'studyvocabulary_template_redirect');


// Add meta box for Custom Number
function add_studyvocabulary_meta_box() {
    add_meta_box(
        'studyvocabulary_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_studyvocabulary_meta_box', // Callback function
        'studyvocabulary',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_studyvocabulary_meta_box');

function display_studyvocabulary_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'studyvocabulary_nonce');
    $custom_number = get_post_meta($post->ID, '_studyvocabulary_custom_number', true);
    ?>
    <label for="studyvocabulary_custom_number">Custom Number:</label>
    <input type="number" name="studyvocabulary_custom_number" id="studyvocabulary_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_studyvocabulary_custom_field($post_id) {
    if (!isset($_POST['studyvocabulary_nonce']) || !wp_verify_nonce($_POST['studyvocabulary_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('studyvocabulary' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['studyvocabulary_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_studyvocabulary_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['studyvocabulary_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_studyvocabulary_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_studyvocabulary_custom_field');

// Add the Custom Number column to the studyvocabulary post type
function add_custom_number_column_studyvocabulary($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_studyvocabulary_posts_columns', 'add_custom_number_column_studyvocabulary');

// Populate the Custom Number column with data
function custom_number_column_content_studyvocabulary($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_studyvocabulary_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_studyvocabulary_posts_custom_column', 'custom_number_column_content_studyvocabulary', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_studyvocabulary($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-studyvocabulary_sortable_columns', 'custom_number_column_sortable_studyvocabulary');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_studyvocabulary($post_id) {
    if (isset($_POST['studyvocabulary_custom_number'])) {
        update_post_meta($post_id, '_studyvocabulary_custom_number', sanitize_text_field($_POST['studyvocabulary_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_studyvocabulary');


// Add a new column to the list of custom post type items
function add_custom_column_studyvocabulary ($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_studyvocabulary_posts_columns', 'add_custom_column_studyvocabulary');

// Populate the custom column with the button or action
function custom_column_content_studyvocabulary ($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'flashcard" class="button">Flash Card (Learn Page)</a>';
        echo '<a href="' . $view_link . 'test" class="button">Exam (Test Page) </a>';
    }
}
add_action('manage_studyvocabulary_posts_custom_column', 'custom_column_content_studyvocabulary', 10, 2);








// new add for conversation_ai

// Add the new custom rewrite rule for the "choose-speaking-ielts-question" action without custom_number
function my_conversation_ai_rewrite_rules() {
    // Rewrite rule for /conversation_ai/choose-question/
   
    // Existing rewrite rules for other actions
    add_rewrite_rule(
        '^conversation_ai/([0-9]+)/([^/]+)/start/?$',
        'index.php?post_type=conversation_ai&custom_number=$matches[1]&name=$matches[2]&custom_action=start',
        'top'
    );
    

    add_rewrite_rule(
        '^conversation_ai/([0-9]+)/([^/]+)/result/([0-9]+)/?$',
        'index.php?post_type=conversation_ai&custom_number=$matches[1]&name=$matches[2]&custom_action=result&testsaveconversationai=$matches[3]',
        'top'
    );


  
    add_rewrite_rule(
        '^conversation_ai/([0-9]+)/([^/]+)/?$',
        'index.php?post_type=conversation_ai&custom_number=$matches[1]&name=$matches[2]',
        'top'
    );
}
add_action('init', 'my_conversation_ai_rewrite_rules');

function conversation_ai_template_redirect() {
    if (is_singular('conversation_ai')) {
        global $wp_query;

        // Get the custom action from the query variable
        $custom_action = get_query_var('custom_action');

        // Handle other custom actions like 'testing-speaking', 'get-mark-speaking', etc.
        if ($custom_action === 'start') {
            include locate_template('template/conversation_ai/template-conversation.php');
            exit;
        } else if ($custom_action === 'result') {
            include locate_template('template/conversation_ai/template-result.php');
            exit;
        } 
        

        if (!empty($custom_action)) {
            wp_redirect(home_url('/404'));
            exit;
        }
    }
}
add_action('template_redirect', 'conversation_ai_template_redirect');

// Save Answer Box meta field
function save_conversation_ai_answer_field($post_id) {
    if (!isset($_POST['conversation_ai_answer_nonce']) || !wp_verify_nonce($_POST['conversation_ai_answer_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('conversation_ai' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    /*if (isset($_POST['conversation_ai_answer'])) {
        $new_additional_info = sanitize_text_field($_POST['conversation_ai_answer']);
        update_post_meta($post_id, '_conversation_ai_answer', $new_additional_info);
    }*/
}
add_action('save_post', 'save_conversation_ai_answer_field');

function add_conversation_ai_meta_box() {
    add_meta_box(
        'conversation_ai_custom_field',  // Unique ID
        'Custom Number Field',              // Box title
        'display_conversation_ai_meta_box', // Callback function
        'conversation_ai',               // Post type
        'side',                            // Context (where on the screen)
        'default'                          // Priority
    );
}
add_action('add_meta_boxes', 'add_conversation_ai_meta_box');

function display_conversation_ai_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'conversation_ai_nonce');
    $custom_number = get_post_meta($post->ID, '_conversation_ai_custom_number', true);
    ?>
    <label for="conversation_ai_custom_number">Custom Number:</label>
    <input type="number" name="conversation_ai_custom_number" id="conversation_ai_custom_number" value="<?php echo esc_attr($custom_number); ?>" required>
    <?php
}

function save_conversation_ai_custom_field($post_id) {
    if (!isset($_POST['conversation_ai_nonce']) || !wp_verify_nonce($_POST['conversation_ai_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('conversation_ai' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    if (isset($_POST['conversation_ai_custom_number'])) {
        $old_custom_number = get_post_meta($post_id, '_conversation_ai_custom_number', true);
        $new_custom_number = sanitize_text_field($_POST['conversation_ai_custom_number']);

        if ($old_custom_number !== $new_custom_number) {
            update_post_meta($post_id, '_conversation_ai_custom_number', $new_custom_number);
            flush_rewrite_rules();
        }
    }
}
add_action('save_post', 'save_conversation_ai_custom_field');

// Add the Custom Number column to the conversation_ai post type
function add_custom_number_column_conversation_ai($columns) {
    $columns['custom_number'] = __('Custom Number');
    return $columns;
}
add_filter('manage_conversation_ai_posts_columns', 'add_custom_number_column_conversation_ai');

// Populate the Custom Number column with data
function custom_number_column_content_conversation_ai($column, $post_id) {
    if ($column == 'custom_number') {
        $custom_number = get_post_meta($post_id, '_conversation_ai_custom_number', true); // Adjust meta key
        echo esc_html($custom_number);
    }
}
add_action('manage_conversation_ai_posts_custom_column', 'custom_number_column_content_conversation_ai', 10, 2);

// Make the Custom Number column sortable
function custom_number_column_sortable_conversation_ai($columns) {
    $columns['custom_number'] = 'custom_number';
    return $columns;
}
add_filter('manage_edit-conversation_ai_sortable_columns', 'custom_number_column_sortable_conversation_ai');

// Save the Custom Number field from Quick Edit
function save_quick_edit_custom_number_conversation_ai($post_id) {
    if (isset($_POST['conversation_ai_custom_number'])) {
        update_post_meta($post_id, '_conversation_ai_custom_number', sanitize_text_field($_POST['conversation_ai_custom_number'])); // Adjust meta key
    }
}
add_action('save_post', 'save_quick_edit_custom_number_conversation_ai');


// Add a new column to the list of custom post type items
function add_custom_column_conversationai($columns) {
    $columns['custom_action'] = 'Custom Action';  // Add your custom column
    return $columns;
}
add_filter('manage_conversation_ai_posts_columns', 'add_custom_column_conversationai');

// Populate the custom column with the button or action
function custom_column_content_conversation_ai ($column, $post_id) {
    if ($column == 'custom_action') {
        $view_link = get_permalink($post_id); // or custom URL
        echo '<a href="' . $view_link . '" class="button">Intro speaking test</a>';
        echo '<a href="' . $view_link . 'testing-speaking" class="button">Doing Test page</a>';
        echo '<a href="' . $view_link . 'get-mark-speaking" class="button">Result Test Page</a>';
        echo '<a href="' . $view_link . 'sample-and-explanation" class="button">Answer and Explanation Page</a>';
    }
}
add_action('manage_conversation_ai_posts_custom_column', 'custom_column_content_conversation_ai', 10, 2);



add_action('init', function() {
    // Add rewrite rule for /shadowing/id_test/start
    add_rewrite_rule(
        '^shadowing/([0-9]+)/start/?$', // Regex pattern
        'index.php?pagename=shadowing-start&id_test=$matches[1]', // Internal query vars
        'top'
    );

    // Add rewrite rule for /shadowing/id_test/
    add_rewrite_rule(
        '^shadowing/([0-9]+)/?$', // Regex pattern
        'index.php?pagename=shadowing&id_test=$matches[1]', // Internal query vars
        'top'
    );
});

add_filter('query_vars', function($vars) {
    // Allow id_test as a query variable
    $vars[] = 'id_test';
    return $vars;
});

// Template redirection for shadowing pages
add_action('template_redirect', function() {
    global $wp_query;

    if (get_query_var('pagename') === 'shadowing-start' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/start
        $id_test = intval(get_query_var('id_test'));
        include locate_template('\template\shadowing\shadowing-start-template.php');
        exit;
    }

    if (get_query_var('pagename') === 'shadowing' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/
        $id_test = intval(get_query_var('id_test'));
        include locate_template('single-shadowing.php');
        exit;
    }
});






add_action('init', function() {
    add_rewrite_rule(
        '^ieltsreadingtest/([0-9]+)/?$',
        'index.php?pagename=ieltsreadingtest&id_test=$matches[1]',
        'top'
    );


    add_rewrite_rule(
        '^ieltsreadingtest/([0-9]+)/start/?$',
        'index.php?pagename=ieltsreadingtest-start&id_test=$matches[1]&custom_action=start',
        'top'
    );
    

    // Add rewrite rule for /ieltsreadingtest/{custom_number}/{post-name}/get-mark/...
    add_rewrite_rule(
        '^ieltsreadingtest/([0-9]+)/result/([0-9]+)/?$',
        'index.php?pagename=ieltsreadingtest-result&id_test=$matches[1]&custom_action=result&testsavereadingnumber=$matches[2]',
        'top'
    );
});


// Template redirection for shadowing pages
add_action('template_redirect', function() {
    global $wp_query;

    if (get_query_var('pagename') === 'ieltsreadingtest' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/
        $id_test = intval(get_query_var('id_test'));
        include locate_template('single-ieltsreadingtest.php');
        exit;
    }

    if (get_query_var('pagename') === 'ieltsreadingtest-start' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/start
        $id_test = intval(get_query_var('id_test'));
        include locate_template('template\ieltsreadingtest\template-testing-reading-ielts.php');
        exit;
    }

   
    if (get_query_var('pagename') === 'ieltsreadingtest-result' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/
        $id_test = intval(get_query_var('id_test'));
        include locate_template('template\ieltsreadingtest\template-get-mark-ielts-reading.php');
        exit;
    }
});







add_action('init', function() {
    add_rewrite_rule(
        '^ieltslisteningtest/([0-9]+)/?$',
        'index.php?pagename=ieltsreadingtest&id_test=$matches[1]',
        'top'
    );


    add_rewrite_rule(
        '^ieltslisteningtest/([0-9]+)/start/?$',
        'index.php?pagename=ieltslisteningtest-start&id_test=$matches[1]&custom_action=start',
        'top'
    );
    

    // Add rewrite rule for /ieltslisteningtest/{custom_number}/{post-name}/get-mark/...
    add_rewrite_rule(
        '^ieltslisteningtest/([0-9]+)/result/([0-9]+)/?$',
        'index.php?pagename=ieltslisteningtest-result&id_test=$matches[1]&custom_action=result&testsavereadingnumber=$matches[2]',
        'top'
    );
});


// Template redirection for shadowing pages
add_action('template_redirect', function() {
    global $wp_query;

    if (get_query_var('pagename') === 'ieltslisteningtest' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/
        $id_test = intval(get_query_var('id_test'));
        include locate_template('single-ieltslisteningtest.php');
        exit;
    }

    if (get_query_var('pagename') === 'ieltslisteningtest-start' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/start
        $id_test = intval(get_query_var('id_test'));
        include locate_template('template\ieltslisteningtest\template-testing-listening-ielts.php');
        exit;
    }

   
    if (get_query_var('pagename') === 'ieltslisteningtest-result' && isset($wp_query->query_vars['id_test'])) {
        // Load custom template for /shadowing/id_test/
        $id_test = intval(get_query_var('id_test'));
        include locate_template('template\ieltslisteningtest\template-get-mark-ielts-listening.php');
        exit;
    }
});



// Temporary: Flush rewrite rules (remove after rules are flushed)
add_action('init', function() {
    flush_rewrite_rules();
});


