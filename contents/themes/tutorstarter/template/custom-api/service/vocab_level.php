<?php

function vocab_check_api(WP_REST_Request $request) {
    global $wpdb;

    // Lấy câu từ request và phân tách thành các từ
    $sentence = sanitize_text_field($request->get_param('sentence'));
    $words = explode(' ', $sentence); // Chia câu thành từng từ

    $table_name =  'vocab_table'; // Tên bảng của bạn
    $results = [];

    // Kiểm tra từng từ trong câu
    foreach ($words as $word) {
        $word = trim($word); // Loại bỏ khoảng trắng dư thừa
        if (empty($word)) continue;

        // Tìm từ trong database
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE word LIKE %s", $word);
        $result = $wpdb->get_results($sql);

        // Kiểm tra nếu không có kết quả, thử với đuôi từ như s, es, ed, ing
        if (empty($result)) {
            $suffixes = ['s', 'es', 'ed', 'ing'];
            foreach ($suffixes as $suffix) {
                $base_word = rtrim($word, $suffix);
                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE word LIKE %s", $base_word);
                $result = $wpdb->get_results($sql);
                if (!empty($result)) {
                    break;
                }
            }
        }

        // Nếu tìm thấy từ, thêm vào kết quả
        if (!empty($result)) {
            $results[] = array(
                'word' => $result[0]->word,
                'part_of_speech' => $result[0]->part_of_speech,
                'cefr_level' => $result[0]->cefr_level
            );
        } else {
            $results[] = array(
                'word' => $word,
                'part_of_speech' => 'Không tìm thấy',
                'cefr_level' => 'Không tìm thấy'
            );
        }
    }

    // Trả về kết quả dạng JSON
    return new WP_REST_Response(array(
        'status' => 'success',
        'results' => $results
    ), 200);
}