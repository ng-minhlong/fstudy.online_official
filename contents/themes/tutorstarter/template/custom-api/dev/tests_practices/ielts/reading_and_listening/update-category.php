<?php
    function update_category_ielts_reading_listening( WP_REST_Request $request ) {
        global $wpdb;

        $params = $request->get_json_params();
        if ( empty($params) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Empty request body'), 400 );
        }

        $idTest = isset($params['idTest']) ? intval($params['idTest']) : 0;
        $partIndex = isset($params['partIndex']) ? intval($params['partIndex']) : 0;
        $typeRequest = isset($params['typeRequest']) ? sanitize_text_field($params['typeRequest']) : '';
        $categoryParam = isset($params['category']) ? $params['category'] : null;

        if ( ! $idTest || ! in_array($partIndex, array(1,2,3), true) || $typeRequest !== 'ielts_reading' ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Invalid parameters'), 400 );
        }

        // Normalize incoming category to PHP array of assoc arrays
        if ( is_string($categoryParam) ) {
            $decoded = json_decode($categoryParam, true);
            if ( json_last_error() === JSON_ERROR_NONE ) {
                $incoming = $decoded;
            } else {
                return new WP_REST_Response( array('success' => false, 'message' => 'Invalid category JSON'), 400 );
            }
        } elseif ( is_array($categoryParam) ) {
            $incoming = $categoryParam;
        } else {
            return new WP_REST_Response( array('success' => false, 'message' => 'Missing category'), 400 );
        }

        // Validate incoming items
        $clean_incoming = array();
        foreach ( $incoming as $item ) {
            if ( ! is_array($item) ) continue;
            if ( ! isset($item['start']) || ! isset($item['end']) || ! isset($item['type']) ) {
                return new WP_REST_Response( array('success' => false, 'message' => 'Category items must have start,end,type'), 400 );
            }
            $s = trim((string)$item['start']);
            $e = trim((string)$item['end']);
            $t = trim((string)$item['type']);
            if ($s === '' || $e === '' || $t === '') {
                return new WP_REST_Response( array('success' => false, 'message' => 'Empty start/end/type not allowed'), 400 );
            }
            // Optionally ensure numeric starts/ends
            if (!ctype_digit($s) || !ctype_digit($e)) {
                return new WP_REST_Response( array('success' => false, 'message' => 'start and end must be integers (as strings)'), 400 );
            }
            $clean_incoming[] = array('start' => $s, 'end' => $e, 'type' => $t);
        }

        if ( empty($clean_incoming) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'No valid category items provided'), 400 );
        }

        // 1) get question_choose from ielts_reading_test_list where id_test = $idTest
        $table_tests =  'ielts_reading_test_list';
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT question_choose FROM {$table_tests} WHERE id_test = %d", $idTest ), ARRAY_A );

        if ( ! $row || empty($row['question_choose']) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Test not found or question_choose empty'), 404 );
        }

        // assume question_choose like "1011,2011,3011"
        $parts_raw = array_map('trim', explode(',', $row['question_choose']));
        if ( ! isset($parts_raw[$partIndex - 1]) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'PartIndex not found in question_choose'), 400 );
        }

        $id_part = intval($parts_raw[$partIndex - 1]);
        if ( ! $id_part ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Invalid id_part parsed'), 400 );
        }

        // choose table by partIndex
        $table_part = 'ielts_reading_part_' . $partIndex . '_question';

        // fetch existing category for this id_part
        $existing_row = $wpdb->get_row( $wpdb->prepare( "SELECT category FROM {$table_part} WHERE id_part = %d LIMIT 1", $id_part ), ARRAY_A );
        if ( $existing_row === null ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'No row found for id_part ' . $id_part), 404 );
        }

        $existing_category = array();
        if ( ! empty($existing_row['category']) ) {
            $decoded_existing = json_decode($existing_row['category'], true);
            if ( json_last_error() === JSON_ERROR_NONE && is_array($decoded_existing) ) {
                // normalize existing items
                foreach ( $decoded_existing as $it ) {
                    if ( is_array($it) && isset($it['start']) && isset($it['end']) && isset($it['type']) ) {
                        $existing_category[] = array(
                            'start' => trim((string)$it['start']),
                            'end'   => trim((string)$it['end']),
                            'type'  => trim((string)$it['type'])
                        );
                    }
                }
            }
        }

        // Merge logic: for each incoming item, if any existing item has same start OR same end -> replace that existing item;
        // otherwise append incoming item.
        foreach ( $clean_incoming as $inc ) {
            $matched_index = null;
            foreach ( $existing_category as $k => $ex ) {
                if ( $ex['start'] === $inc['start'] || $ex['end'] === $inc['end'] ) {
                    $matched_index = $k;
                    break;
                }
            }
            if ( $matched_index !== null ) {
                // replace existing item at matched_index with incoming (preserve order)
                $existing_category[$matched_index] = $inc;
            } else {
                // append new incoming item
                $existing_category[] = $inc;
            }
        }

        // encode and update DB
        $category_json = json_encode( $existing_category, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

        $updated = $wpdb->update(
            $table_part,
            array( 'category' => $category_json ),
            array( 'id_part' => $id_part ),
            array( '%s' ),
            array( '%d' )
        );

        if ( $updated === false ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'DB update error: ' . $wpdb->last_error), 500 );
        }

        // Return merged category for verification
        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Category merged and saved',
            'category_merged' => $existing_category,
            'rows_updated' => ($updated === 0 ? 0 : intval($updated))
        ), 200 );
    }

