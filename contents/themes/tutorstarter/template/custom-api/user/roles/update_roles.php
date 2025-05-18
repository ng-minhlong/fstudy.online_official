<?php

function update_roles(WP_REST_Request $request) {
    global $wpdb;
    
    // Lấy thông tin từ request
    $parameters = $request->get_params();
    $user_id = isset($parameters['user_id']) ? intval($parameters['user_id']) : 0;
    $user_name = isset($parameters['user_name']) ? sanitize_text_field($parameters['user_name']) : '';
    $id_role = isset($parameters['id_role']) ? intval($parameters['id_role']) : 0;
    
    // Kiểm tra các tham số bắt buộc
    if (empty($user_id) || empty($user_name) || empty($id_role)) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'Missing required parameters'
        ), 400);
    }
    
    // Lấy thông tin role từ bảng admin_create
    $admin_create_table = 'admin_create';
    $role_info = $wpdb->get_row($wpdb->prepare(
        "SELECT user_role, time_expired FROM $admin_create_table WHERE id = %d AND type = 'role'",
        $id_role
    ));
    
    if (!$role_info) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'Role not found'
        ), 404);
    }
    
    // Chuẩn bị dữ liệu role
    $role_data = array(
        'role' => $role_info->user_role,
        'id_role' => $id_role,
        'duration' => $role_info->time_expired
    );
    
    // Xử lý expired_date
    if ($role_info->time_expired === 'Unlimited') {
        $role_data['expired_date'] = 'Unlimited';
    } else {
        $months = intval($role_info->time_expired);
        $current_date = new DateTime();
        $current_date->add(new DateInterval('P' . $months . 'M'));
        $role_data['expired_date'] = $current_date->format('Y-m-d H:i:s');
    }
    
    // Chuyển thành JSON
    $roles_json = json_encode(array($role_data));
    
    // Kiểm tra xem user đã tồn tại trong bảng chưa
    $user_roles_table =  'user_role';
    $existing_user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $user_roles_table WHERE user_id = %d",
        $user_id
    ));
    
    // Xử lý insert hoặc update
    if ($existing_user) {
        // Lấy danh sách roles hiện tại và giải mã JSON
        $existing_roles = json_decode($existing_user->roles, true);
        if (!is_array($existing_roles)) {
            $existing_roles = [];
        }

        // Thêm role mới vào mảng
        $existing_roles[] = $role_data;

        // Mã hóa lại thành JSON
        $roles_json = json_encode($existing_roles);

        // Update lại user
        $wpdb->update(
            $user_roles_table,
            array(
                'user_name' => $user_name,
                'roles' => $roles_json,
                'last_updated' => current_time('mysql')
            ),
            array('user_id' => $user_id),
            array('%s', '%s', '%s'),
            array('%d')
        );
    }
    else {
        // Insert new record
        $wpdb->insert(
            $user_roles_table,
            array(
                'user_name' => $user_name,
                'user_id' => $user_id,
                'roles' => $roles_json,
                'last_updated' => current_time('mysql')
            ),
            array('%s', '%d', '%s', '%s')
        );
    }
    
    return new WP_REST_Response(array(
        'status' => 'success',
        'message' => 'Role updated successfully'
    ), 200);
}

