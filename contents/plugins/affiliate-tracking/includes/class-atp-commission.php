<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ATP_Commission {

    public static function maybe_create_commission( $order_id ) {
        
global $wpdb;

if ( empty( $order_id ) ) {
    // nếu không có $order_id ở template (trường hợp), thử lấy từ query param để test
    if ( ! empty( $_GET['order_id'] ) ) {
        $order_id = intval( $_GET['order_id'] );
    }
}
if ( empty( $order_id ) ) {
    error_log( 'Affiliate: no order_id on order-placement-success.php' );
    // tiếp tục hiển thị trang nhưng không tính hoa hồng
} else {
    $track_table =  'affiliate_user_tracking';
    $refs_table  =  'affiliate_refs';
    $comm_table  =  'affiliate_commissions';

    $user_id = get_current_user_id();

    // 1) lấy last_referrer_id từ tracking table
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT last_referrer_id FROM {$track_table} WHERE user_id = %d LIMIT 1",
        $user_id
    ) );
    $referrer_id = $row && ! empty( $row->last_referrer_id ) ? intval( $row->last_referrer_id ) : 0;

    // 2) fallback cookie
    if ( ! $referrer_id && ! empty( $_COOKIE['affiliate_ref'] ) ) {
        $data = json_decode( rawurldecode( $_COOKIE['affiliate_ref'] ), true );
        if ( ! empty( $data['referrer_id'] ) ) {
            $referrer_id = intval( $data['referrer_id'] );
        }
    }

    if ( $referrer_id ) {
        $ref = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$refs_table} WHERE id = %d AND status = 'active' LIMIT 1",
            $referrer_id
        ) );

        if ( $ref ) {

            // ----------------------------
            //  LẤY ORDER ITEMS CHUẨN TỪ TUTOR
            // ----------------------------
            $order_items = array();

            if ( class_exists( '\Tutor\Models\OrderModel' ) ) {

                $order_model = new \Tutor\Models\OrderModel();
                $order_items = $order_model->get_order_items_by_id( $order_id );
                // debug: error_log( 'Affiliate: order_items: ' . wp_json_encode($order_items) );
				echo '<script>console.log("order item là ", ' . wp_json_encode($order_items) . ');</script>';

            } else {
                // fallback: nếu bạn biết DB structure khác, có thể query thẳng
                error_log( 'Affiliate: Tutor OrderModel not found, cannot get order items.' );
            }


            // nếu có items thì insert commission per item
            if ( ! empty( $order_items ) && is_array( $order_items ) ) {

                foreach ( $order_items as $item ) {
    // object -> array
    $item = (array) $item;

    // Trong Tutor LMS, id ở đây chính là post_id của khóa học
    $course_id = isset( $item['id'] ) ? intval( $item['id'] ) : 0;
    $qty       = 1; // Tutor không trả qty trong order item

    // Giá bán: ưu tiên sale_price, fallback regular_price
    if ( ! empty( $item['sale_price'] ) && is_numeric( $item['sale_price'] ) ) {
        $price = floatval( $item['sale_price'] );
    } else {
        $price = floatval( $item['regular_price'] ?? 0 );
    }

    if ( $course_id <= 0 || $price <= 0 ) {
        continue;
    }

    $rate = (float) $ref->commission_rate; // % từ bảng ref
    $amount_per_unit = round( $price * ( $rate / 100 ), 2 );
    $amount = round( $amount_per_unit * $qty, 2 );

    $exists = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$comm_table} WHERE referrer_id = %d AND buyer_id = %d AND order_id = %d AND type = %s",
		$referrer_id,
		$user_id,
		$order_id,
		'ref_link'
	) );

	if ( $exists > 0 ) {
		error_log( "Affiliate: Commission already exists for order {$order_id}, referrer {$referrer_id}" );
		continue; // skip insert
	}

	$inserted = $wpdb->insert(
		$comm_table,
		[
			'type'        => 'ref_link',
			'referrer_id' => $referrer_id,
			'buyer_id'    => $user_id,
			'course_id'   => $course_id,
			'order_id'    => $order_id,
			'amount'      => $amount,
			'rate'        => $rate,
			'status'      => 'approved',
			'created_at'  => current_time( 'mysql' ),
		],
		[ '%s', '%d', '%d', '%d', '%d', '%f', '%f', '%s', '%s' ]
	);

    if ( false === $inserted ) {
        error_log( 'Affiliate: failed to insert commission. DB error: ' . $wpdb->last_error );
    }
}
// end foreach items
            } else {
                // không có items
                error_log( 'Affiliate: no order items found for order_id ' . $order_id );
            }
        } // end if $ref
    } // end if $referrer_id
} // end if order_id
    }
}
