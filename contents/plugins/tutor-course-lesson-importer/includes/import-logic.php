<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

function tutor_make_authenticated_request($url, $args = []) {
    $username = 'key_2a091f239b60855336d6959b85a00bb9';
    $password = 'secret_9c8cb32983f77ab885673fa01413bbc3f39858981761c8e8d93ce548ace218c8';
    
    $default_args = [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
        ],
        'timeout' => 15
    ];
    
    $args = wp_parse_args($args, $default_args);
    
    return wp_remote_post($url, $args);
}

function parse_duration($duration) {
    $duration = strtolower(trim($duration));
    
    // Handle formats: 1h:42m:39s or 1h 42m 39s
    if (preg_match_all('/(\d+)\s*([hms])/', $duration, $matches)) {
        $components = array_combine($matches[2], $matches[1]);
        $h = isset($components['h']) ? (int)$components['h'] : 0;
        $m = isset($components['m']) ? (int)$components['m'] : 0;
        $s = isset($components['s']) ? (int)$components['s'] : 0;
        
        return [
            'hours' => $h,
            'minutes' => $m,
            'seconds' => $s
        ];
    }
    // Handle colon-separated format: 1:42:39
    elseif (preg_match('/^(\d+):(\d+):(\d+)$/', $duration, $matches)) {
        return [
            'hours' => (int)$matches[1],
            'minutes' => (int)$matches[2],
            'seconds' => (int)$matches[3]
        ];
    }
    // Handle simple minutes format: 42
    elseif (is_numeric($duration)) {
        return [
            'hours' => 0,
            'minutes' => (int)$duration,
            'seconds' => 0
        ];
    }
    
    // Default to 0 if format not recognized
    return [
        'hours' => 0,
        'minutes' => 0,
        'seconds' => 0
    ];
}

function extract_youtube_id($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return $matches[1] ?? '';
}

function tutor_import_from_excel($course_id, $file_path) {
    global $wpdb;
    $table_name = 'lessons_management';
    
    try {
        $spreadsheet = IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $headers = array_map('strtolower', array_map('trim', $rows[0]));
        $data = array_slice($rows, 1);

        $results = [];
        $topic_cache = [];

        // Pre-cache existing topics
        $existing_topics = tutor_utils()->get_course_topics($course_id);
        foreach ($existing_topics as $topic) {
            $topic_cache[sanitize_title($topic->post_title)] = $topic->ID;
        }

        foreach ($data as $row_index => $row) {
            $row_number = $row_index + 2;
            if (count($row) < 5) continue;
            
            $row_data = array_combine($headers, $row);
            $topic_name = trim($row_data['topic_name']);
            $topic_key = sanitize_title($topic_name);

            // Create topic if doesn't exist
            if (!isset($topic_cache[$topic_key])) {
                $response = tutor_make_authenticated_request(
                    home_url('/wp-json/tutor/v1/topics'),
                    [
                        'body' => json_encode([
                            'topic_course_id' => $course_id,
                            'topic_title'     => $topic_name,
                            'topic_summary'   => '',
                            'topic_author'    => get_current_user_id()
                        ])
                    ]
                );

                if (is_wp_error($response)) {
                    $results[] = "ROW {$row_number}: Topic failed - " . $response->get_error_message();
                    continue;
                }

                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (empty($body['data'])) {
                    $results[] = "ROW {$row_number}: Topic failed - " . ($body['message'] ?? 'Unknown error');
                    continue;
                }

                $topic_cache[$topic_key] = $body['data'];
                $results[] = "ROW {$row_number}: Topic created - {$topic_name}";
            }

            // Parse duration using the robust parser
            $duration_components = parse_duration($row_data['duration']);
            $hours = str_pad($duration_components['hours'], 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($duration_components['minutes'], 2, '0', STR_PAD_LEFT);
            $seconds = str_pad($duration_components['seconds'], 2, '0', STR_PAD_LEFT);

            // Auto-detect video source type
            $source_url = trim($row_data['source']);
            $source_type = trim($row_data['source_type']);
            
            if (empty($source_type)) {
                if (strpos($source_url, 'youtube.com') !== false || 
                    strpos($source_url, 'youtu.be') !== false) {
                    $source_type = 'youtube';
                } elseif (strpos($source_url, 'vimeo.com') !== false) {
                    $source_type = 'vimeo';
                } else {
                    $source_type = 'external_url';
                }
            }

            // Create lesson
            $lesson_data = [
                'topic_id'       => $topic_cache[$topic_key],
                'lesson_title'   => $row_data['lesson_title'],
                'lesson_content' => $row_data['description'] ?? '',
                'thumbnail_id'   => 0,
                'lesson_author'  => get_current_user_id(),
                'video'          => [
                    'source_type' => $source_type,
                    'source'      => $source_url,
                    'runtime'     => [
                        'hours'   => $hours,
                        'minutes' => $minutes,
                        'seconds' => $seconds
                    ]
                ],
                'preview'        => strtolower($row_data['preview'] ?? '') === 'true'
            ];

            $lesson_response = tutor_make_authenticated_request(
                home_url('/wp-json/tutor/v1/lessons'),
                [
                    'body' => json_encode($lesson_data)
                ]
            );

            if (is_wp_error($lesson_response)) {
                $results[] = "ROW {$row_number}: Lesson failed - " . $lesson_response->get_error_message();
            } else {
                $lesson_body = json_decode(wp_remote_retrieve_body($lesson_response), true);
                if (!empty($lesson_body['data'])) {
                    $lesson_id = $lesson_body['data'];
                    $results[] = "ROW {$row_number}: Lesson created - {$row_data['lesson_title']} (ID: {$lesson_id})";
                    
                    // Prepare data for lessons_management table
                    $youtube_id = ($source_type === 'youtube') ? extract_youtube_id($source_url) : '';
                    $abyss_slug = $row_data['abyss slug'] ?? '';
                    $bunny_slug = $row_data['link bunny'] ?? '';
                    $external_url = ($source_type === 'external_url') ? $source_url : '';
                    
                    $management_data = [
                        'course_id'      => $course_id,
                        'youtube_id'     => $youtube_id,
                        'youtube_status' => $youtube_id ? 'Live' : 'Empty',
                        'abyss_slug'     => $abyss_slug,
                        'abyss_status'   => $abyss_slug ? 'Live' : 'Empty',
                        'bunny_slug'     => $bunny_slug,
                        'bunny_status'   => $bunny_slug ? 'Live' : 'Empty',
                        'external_url'        => $external_url,
                        'external_url_status' => $external_url ? 'Live' : 'Empty',
                        'order'          => $row_data['stt'] ?? ($row_index + 1),
                        'created_at'     => current_time('mysql'),
                        'updated_at'     => current_time('mysql')
                    ];
                    
                    // Insert into lessons_management table
                    $wpdb->insert($table_name, $management_data);
                    
                    if ($wpdb->last_error) {
                        $results[] = "ROW {$row_number}: DB insert failed - " . $wpdb->last_error;
                    } else {
                        $results[] = "ROW {$row_number}: Added to lessons_management table";
                    }
                    
                } else {
                    $results[] = "ROW {$row_number}: Lesson failed - " . ($lesson_body['message'] ?? 'Unknown error');
                }
            }
        }

        // Output results
        echo '<div class="notice notice-info"><p><strong>Import Results:</strong></p><ul>';
        foreach ($results as $result) {
            echo '<li>' . esc_html($result) . '</li>';
        }
        echo '</ul></div>';

    } catch (Exception $e) {
        echo '<div class="notice notice-error"><p>Error: ' . $e->getMessage() . '</p></div>';
    }
}