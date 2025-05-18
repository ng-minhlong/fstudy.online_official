<?php
/**
 * Checkout Template.
 *
 * @package Tutor\Views
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Tutor\Ecommerce\CheckoutController;
use Tutor\Ecommerce\CartController;
use TUTOR\Input;

$user_id = get_current_user_id();
$site_url = get_site_url();

$tutor_toc_page_link     = tutor_utils()->get_toc_page_link();
$tutor_privacy_page_link = tutor_utils()->get_privacy_page_link();

$cart_controller = new CartController();
$get_cart        = $cart_controller->get_cart_items();
$courses         = $get_cart['courses'];
$total_count     = $courses['total_count'];
$course_list     = $courses['results'];
$subtotal        = 0;
$course_ids      = implode( ', ', array_values( array_column( $course_list, 'ID' ) ) );
$plan_id         = Input::get( 'plan', 0, Input::TYPE_INT );

$is_checkout_page = true;

?>
<!-- Thêm SweetAlert2 CDN -->

<div class="tutor-checkout-page">
<div class="tutor-container">
<div class="tutor-checkout-container">
	<?php
	$echo_before_return    = true;
	$user_has_subscription = apply_filters( 'tutor_checkout_user_has_subscription', false, $plan_id, $echo_before_return );
	if ( $user_has_subscription ) {
		return;
	}

	$current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;

    
    //$custom_number = get_post_meta($post_id, "_digitalsat_custom_number", true);
    $custom_number =intval(get_query_var('id_test'));
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = ""; // No password by default
    $dbname = "wordpress";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
   

    // Get current time (hour, minute, second)
    $minute = date("i"); // Phút
    $second = date("s"); // Giây

    // Generate random two-digit number
    $random_number = rand(10, 99);
    // Handle user_id and id_test error, set to "00" if invalid
    if (!$user_id) {
        $user_id = "00"; // Set user_id to "00" if invalid
    }

    $orderID = $minute . $second . $user_id . $random_number;
    echo "<script> 
        var orderID = '" .
        $orderID .
        "';
        console.log('orderID: ' + orderID);
    </script>";

	?>



	<form method="post" id="tutor-checkout-form">
		<?php tutor_nonce_field(); ?>
		<input type="hidden" name="tutor_action" value="tutor_pay_now">
		<input name="orderID" id = "orderID" value="<?php echo $orderID; ?>">

		<div class="tutor-row tutor-g-5">
			<div class="tutor-col-md-6" tutor-checkout-details>
				<?php
				$file = __DIR__ . '/checkout-details.php';
				if ( file_exists( $file ) ) {
					include $file;
				}
				?>
			</div>
			
			<div class="tutor-col-md-6">
				<div class="tutor-checkout-billing">
					<div class="tutor-checkout-billing-inner">
						<h5 class="tutor-fs-5 tutor-fw-medium tutor-color-black tutor-mb-24">
							<?php echo esc_html_e( 'Billing Address', 'tutor' ); ?>
						</h5>

						<div class="tutor-billing-fields">
							<?php require tutor()->path . 'templates/ecommerce/billing-form-fields.php'; ?>
						</div>

						<h5 class="tutor-fs-5 tutor-fw-medium tutor-color-black tutor-mb-24 tutor-mt-20">
							<?php esc_html_e( 'Payment Method', 'tutor' ); ?>
						</h5>
						<div class="tutor-checkout-payment-options tutor-mb-24">
							<input type="hidden" name="payment_type">
							<?php
							$payment_gateways = tutor_get_all_active_payment_gateways();
							if ( empty( $payment_gateways ) ) {
								?>
								<div class="tutor-alert tutor-warning">
									<?php esc_html_e( 'No payment method has been configured. Please contact the site administrator.', 'tutor' ); ?>
								</div>
								<?php
							} else {
								$supported_gateways = tutor_get_supported_payment_gateways( $plan_id );

								if ( empty( $supported_gateways ) ) {
									?>

									<div class="tutor-alert tutor-warning">
										<?php esc_html_e( 'No payment method found. Please contact the site administrator.', 'tutor' ); ?>
									</div>
									<?php
								} else {
									foreach ( $supported_gateways as $gateway ) {
										list( 'is_manual' => $is_manual, 'name' => $name, 'label' => $label, 'icon' => $icon) = $gateway;

										if ( $is_manual ) {
											?>
										<label class="tutor-checkout-payment-item" data-payment-method="<?php echo esc_attr( $name ); ?>" data-payment-type="manual" data-payment-details="<?php echo esc_attr( $gateway['additional_details'] ?? '' ); ?>" data-payment-instruction="<?php echo esc_attr( $gateway['payment_instructions'] ?? '' ); ?>">
											<input type="radio" value="<?php echo esc_attr( $name ); ?>" name="f" class="tutor-form-check-input" required>
											<div class="tutor-payment-item-content">
												<?php if ( ! empty( $icon ) ) : ?>
												<img src ="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $name ); ?>"/>
												
												<?php endif; ?>
												<?php echo esc_html( $label ); ?>
											</div>
										</label>
											<?php
										} else {
											?>
										<label class="tutor-checkout-payment-item" data-payment-type="automate">
											<input type="radio" name="payment_method" value="<?php echo esc_attr( $name ); ?>" class="tutor-form-check-input" required>
											<div class="tutor-payment-item-content">
												<?php if ( ! empty( $icon ) ) : ?>
												<img src = "<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $name ); ?>"/>
												
												<?php endif; ?>
												
												<?php echo esc_html( $label ); ?>
											</div>
										</label>
											<?php
										}
									}
								}
							}
							?>
						</div>

						<div class="tutor-payment-instructions tutor-mb-20 tutor-d-none"></div>

						<?php if ( null !== $tutor_toc_page_link ) : ?>
							<div class="tutor-mb-16">
								<div class="tutor-form-check tutor-d-flex">
									<input type="checkbox" id="tutor_checkout_agree_to_terms" name="agree_to_terms" class="tutor-form-check-input" required>
									<label for="tutor_checkout_agree_to_terms">
										<span class="tutor-color-subdued tutor-fw-normal">
											<?php esc_html_e( 'I agree with the website\'s', 'tutor' ); ?> 
											<a target="_blank" href="<?php echo esc_url( $tutor_toc_page_link ); ?>" class="tutor-color-primary"><?php esc_html_e( 'Terms of Use', 'tutor' ); ?></a> 
											<?php if ( null !== $tutor_privacy_page_link ) : ?>
												<?php esc_html_e( 'and', 'tutor' ); ?> 
												<a target="_blank" href="<?php echo esc_url( $tutor_privacy_page_link ); ?>" class="tutor-color-primary"><?php esc_html_e( 'Privacy Policy', 'tutor' ); ?></a>
											<?php endif; ?>
										</span>
									</label>
								</div>
							</div>
						<?php endif; ?>

						<!-- handle errors -->
						<?php
						$pay_now_errors    = get_transient( CheckoutController::PAY_NOW_ERROR_TRANSIENT_KEY . $user_id );
						$pay_now_alert_msg = get_transient( CheckoutController::PAY_NOW_ALERT_MSG_TRANSIENT_KEY . $user_id );

						delete_transient( CheckoutController::PAY_NOW_ALERT_MSG_TRANSIENT_KEY . $user_id );
						delete_transient( CheckoutController::PAY_NOW_ERROR_TRANSIENT_KEY . $user_id );
						if ( $pay_now_errors || $pay_now_alert_msg ) :
							?>
						<div class="tutor-break-word">
							<?php
							if ( ! empty( $pay_now_alert_msg ) ) :
								list( $alert, $message ) = array_values( $pay_now_alert_msg );
								?>
								<div class="tutor-alert tutor-<?php echo esc_attr( $alert ); ?>">
									<div class="tutor-color-<?php echo esc_attr( $alert ); ?>"><?php echo esc_html( $message ); ?></div>
								</div>
							<?php endif; ?>

							<?php if ( is_array( $pay_now_errors ) && count( $pay_now_errors ) ) : ?>
							<div class="tutor-alert tutor-danger">
								<ul class="tutor-mb-0">
									<?php foreach ( $pay_now_errors as $pay_now_err ) : ?>
										<li class="tutor-color-danger"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $pay_now_err ) ) ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<!-- handle errors end -->

						<button type="submit" id="tutor-checkout-pay-now-button" class="tutor-btn tutor-btn-primary tutor-btn-lg tutor-w-100 tutor-justify-center">
							<?php esc_html_e( 'Pay Now', 'tutor' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	
	document.addEventListener("DOMContentLoaded", function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const manualPaymentMethods = document.querySelectorAll('input[data-payment-type="manual"]');

    paymentMethods.forEach(paymentMethod => {
        paymentMethod.addEventListener('change', function() {
            if (this.value === 'paypal' || this.value === 'some_other_automated_gateway') {
                // Disable manual payment methods
                manualPaymentMethods.forEach(method => {
                    method.disabled = true;
                });
            } else {
                // Enable manual payment methods
                manualPaymentMethods.forEach(method => {
                    method.disabled = false;
                });
            }
        });
    });
});


	document.addEventListener('DOMContentLoaded', function () {
		const paymentItems = document.querySelectorAll('.tutor-checkout-payment-item input[type="radio"]');
		const payNowButton = document.getElementById('tutor-checkout-pay-now-button');
    
    let selectedPaymentMethod = null;

    paymentItems.forEach(function (item) {
        item.addEventListener('click', function () {
            const paymentItem = this.closest('.tutor-checkout-payment-item');
            selectedPaymentMethod = paymentItem.dataset.paymentMethod || 'N/A';
            const details = paymentItem.dataset.paymentDetails || 'No Details';
            const instruction = paymentItem.dataset.paymentInstruction || 'No Instruction';
			let qrCodeUrl = paymentItem.dataset.qrCode || ''; // Gán URL QR code nếu có

		



            if (selectedPaymentMethod === 'momo' || selectedPaymentMethod === 'bidv') {
				if (selectedPaymentMethod === 'momo' ){
					qrCodeUrl = 'https://fstudy.online/contents/uploads/2025/02/momo_qr.jpg'; // Sửa dấu "==" thành "="
				}
				else if (selectedPaymentMethod === 'bidv' ){
					qrCodeUrl = 'https://fstudy.online/contents/uploads/2025/02/bidv_qr.jpg'; // Sửa dấu "==" thành "="
				}
                // Hiển thị SweetAlert với các thông tin
                Swal.fire({
                    title: `Thanh toán qua ${selectedPaymentMethod.toUpperCase()}`,
                    html: `
                        <p><strong>Số tiền cần thanh toán:</strong> <?php tutor_print_formatted_price( $checkout_data->total_price ); ?></p>
                        <p><strong>Nội dung chuyển khoản:</strong> <i>${orderID} </i></p>
                        <img src = "${qrCodeUrl}" alt="QR Code" class="my-4 w-48 h-48 mx-auto" />
                        <p><strong>Hướng dẫn:</strong> ${instruction}</p>
                    `,
                    showCancelButton: false,
                    confirmButtonText: 'Tôi đã thanh toán',
                    focusConfirm: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Popup cảm ơn khi nhấn "Tôi đã thanh toán"
                        Swal.fire({
                            title: 'Cảm ơn bạn đã thanh toán!',
                            text: 'Chúc bạn một ngày tốt lành!',
                            icon: 'success',
                            confirmButtonText: 'Đóng',
                        }).then(() => {
                            // Bạn có thể thêm logic tiếp theo tại đây
                            // Ví dụ: tự động chuyển trang hoặc thực hiện hành động khác
                        });
                    }
                });
            }
        });
    });

    // Kiểm tra khi ấn nút "Pay Now"
   /* payNowButton.addEventListener('click', function (e) {
        if (selectedPaymentMethod === 'momo' || selectedPaymentMethod === 'bidv') {
            // Chặn nếu chưa nhấn "Tôi đã thanh toán"
            alert('Vui lòng nhấn "Tôi đã thanh toán" trước khi tiếp tục.');
            e.preventDefault(); // Chặn submit nếu chưa xác nhận thanh toán
        }
    });*/
});
</script>

	</form>
</div>
</div>
</div>
