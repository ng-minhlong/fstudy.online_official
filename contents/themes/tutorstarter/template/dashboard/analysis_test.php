<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}?>





<!-- Add thêm 1 file ở plugin -> tutor -> templates -> dashboard-->








	<div>
		<main>
          <?php
// Check if user is logged in
if (is_user_logged_in()) {
    global $wpdb;

    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;

    // Get distinct dates for the current user's data
    $dates_query = $wpdb->prepare("SELECT DISTINCT dateform FROM save_user_result_digital_sat WHERE username = %s ORDER BY dateform DESC", $current_username);
    $distinct_dates = $wpdb->get_col($dates_query);

    // Loop through each distinct date
    foreach ($distinct_dates as $date) {
        // Get table data for the current user and date
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM save_user_result_digital_sat WHERE username = %s AND dateform = %s", $current_username, $date));

        if ($result) { // If there are results for the current user and date
?>
            <h3><?php echo $date; ?></h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tài khoản</th>
                        <th>Ngày làm bài</th>
                        <th>Đề thi</th>
                        <th>Phân loại</th>
						<th>ID Đề thi</th>
                        <th>Kết quả</th>
                        <th>Thời gian hoàn thành</th>
                    </tr>
                </thead>
                <tbody>
<?php
            foreach ($result as $book) { ?>
                <tr>
                    <td><?php echo $book->username; ?></td>
                    <td><?php echo $book->dateform; ?></td>
                    <td><?php echo $book->testname ?></td>
                    <td><?php echo $book->idcategory; ?></td>
                    <td><?php echo $book->idtest; ?></td>
					<td><?php echo $book->resulttest; ?></td>
                    <td><?php echo $book->timedotest ?></td>
                </tr>
<?php
            } ?>
                </tbody>
            </table>
<?php
        } else { // If no results for the current user and date
            echo 'Không có kết quả nào bài thi nào của bạn ';
        }
    }
} else { // If user is not logged in
    echo 'Vui lòng đăng nhập để xem kết quả làm bài.';
}
?>
