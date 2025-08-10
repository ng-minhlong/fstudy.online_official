<?php
/**
 * Init class
 *
 * @package TutorPro\Payments\Integrations
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

namespace TutorVnpay;

use Tutor\Ecommerce\Ecommerce;

final class Init {

    public function __construct() {
        add_filter( 'tutor_gateways_with_class', __CLASS__ . '::payment_gateways_with_ref', 10, 2 );
        add_filter( 'tutor_payment_gateways_with_class', __CLASS__ . '::filter_payment_gateways' );
        add_filter( 'tutor_payment_gateways', array( $this, 'add_tutor_vnpay_payment_method' ), 100 );
        
        // Add hook to handle VNPay callback in success URL
        add_action( 'tutor_order_placement_success', array( $this, 'handle_vnpay_callback' ), 10, 1 );
    }

    public static function payment_gateways_with_ref( $value, $gateway ) {
        $arr = array(
            'vnpay' => array(
                'gateway_class' => VnpayGateway::class,
                'config_class'  => VnpayConfig::class,
            ),
        );

        if ( isset( $arr[ $gateway ] ) ) {
            $value[ $gateway ] = $arr[ $gateway ];
        }

        return $value;
    }

    public static function filter_payment_gateways( $gateways ) {
        $arr = array(
            'vnpay' => array(
                'gateway_class' => VnpayGateway::class,
                'config_class'  => VnpayConfig::class,
            ),
        );

        $gateways = $gateways + $arr;

        return $gateways;
    }

    public function add_tutor_vnpay_payment_method( $methods ) {
        $icon = '';
        if ( function_exists( 'plugins_url' ) ) {
            // Update the icon path if you upload a VNPAY logo.
            $icon = content_url( '/uploads/2025/02/VNPAY_Logo.jpg' );
        }

        $vnpay_method = array(
            'name' => 'vnpay',
            'label' => 'VNPAY',
            'is_installed' => true,
            'is_plugin_active' => true,
            'is_active' => true,
            'icon' => $icon,
            'support_subscription' => false,
            'fields' => array(
                array(
                    'name' => 'environment',
                    'type' => 'select',
                    'label' => 'Environment',
                    'options' => array(
                        'test' => 'Test',
                        'live' => 'Live',
                    ),
                    'value' => 'test',
                ),
                array(
                    'name' => 'tmn_code',
                    'type' => 'text',
                    'label' => 'VNPAY TmnCode',
                    'value' => '',
                ),
                array(
                    'name' => 'hash_secret',
                    'type' => 'secret_key',
                    'label' => 'Hash Secret',
                    'value' => '',
                ),
                array(
                    'name' => 'language',
                    'type' => 'select',
                    'label' => 'Language',
                    'options' => array(
                        'vn' => 'Vietnamese',
                        'en' => 'English',
                    ),
                    'value' => 'vn',
                ),
                array(
                    'name' => 'webhook_url',
                    'type' => 'webhook_url',
                    'label' => 'Webhook URL',
                    'value' => '',
                ),
            ),
        );

        $methods[] = $vnpay_method;
        return $methods;
    }

    /**
     * Handle VNPay callback when redirecting to success URL
     * 
     * @param int $order_id Order ID
     */
    public function handle_vnpay_callback( $order_id ) {
        // Check if this is a VNPay callback
        if ( ! isset( $_GET['vnp_ResponseCode'] ) || ! isset( $_GET['vnp_TxnRef'] ) ) {
            return;
        }

        // Verify that the order ID matches
        if ( $_GET['vnp_TxnRef'] != $order_id ) {
            error_log( 'VNPay callback: Order ID mismatch. Expected: ' . $order_id . ', Got: ' . $_GET['vnp_TxnRef'] );
            return;
        }

        error_log( 'VNPay callback: Processing order ' . $order_id . ' with response code: ' . $_GET['vnp_ResponseCode'] );

        // Create webhook data object
        $webhook_data = (object) array(
            'get'    => $_GET,
            'post'   => $_POST,
            'server' => $_SERVER,
            'stream' => file_get_contents( 'php://input' ),
        );

        try {
            // Get VNPay payment gateway
            $payment_gateways = apply_filters( 'tutor_gateways_with_class', array(), 'vnpay' );
            $payment_gateway_class = isset( $payment_gateways['vnpay'] ) 
                ? $payment_gateways['vnpay']['gateway_class'] 
                : null;

            if ( $payment_gateway_class ) {
                $payment = Ecommerce::get_payment_gateway_object( $payment_gateway_class );
                $res = $payment->verify_webhook_signature( $webhook_data );
                
                if ( is_object( $res ) && property_exists( $res, 'id' ) ) {
                    error_log( 'VNPay callback: Payment verification successful. Order: ' . $res->id . ', Status: ' . $res->payment_status );
                    // Trigger payment update action
                    do_action( 'tutor_order_payment_updated', $res );
                } else {
                    error_log( 'VNPay callback: Payment verification failed or returned invalid data' );
                }
            } else {
                error_log( 'VNPay callback: Could not find VNPay gateway class' );
            }
        } catch ( \Throwable $e ) {
            // Log error but don't break the page
            error_log( 'VNPay callback error: ' . $e->getMessage() );
        }
    }
}