<?php
/**
 * Init class
 *
 * @package TutorPro\Payments\Integrations
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

namespace TutorMomo;

final class Init {

    public function __construct() {
        add_filter( 'tutor_gateways_with_class', __CLASS__ . '::payment_gateways_with_ref', 10, 2 );
        add_filter( 'tutor_payment_gateways_with_class', __CLASS__ . '::filter_payment_gateways' );
        add_filter( 'tutor_payment_gateways', array( $this, 'add_tutor_momo_payment_method' ), 100 );
    }

    public static function payment_gateways_with_ref( $value, $gateway ) {
        $arr = array(
            'momo' => array(
                'gateway_class' => MomoGateway::class,
                'config_class'  => MomoConfig::class,
            ),
        );

        if ( isset( $arr[ $gateway ] ) ) {
            $value[ $gateway ] = $arr[ $gateway ];
        }

        return $value;
    }

    public static function filter_payment_gateways( $gateways ) {
        $arr = array(
            'momo' => array(
                'gateway_class' => MomoGateway::class,
                'config_class'  => MomoConfig::class,
            ),
        );

        $gateways = $gateways + $arr;

        return $gateways;
    }

    public function add_tutor_momo_payment_method( $methods ) {
        $icon = '';
        if ( function_exists( 'plugins_url' ) ) {
            $icon = content_url( '/uploads/2025/02/MoMo_Logo.png' );
        }

        $momo_method = array(
            'name' => 'momo',
            'label' => 'MoMo',
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
                    'name' => 'partner_code',
                    'type' => 'text',
                    'label' => 'Partner Code',
                    'value' => '',
                ),
                array(
                    'name' => 'access_key',
                    'type' => 'secret_key',
                    'label' => 'Access Key',
                    'value' => '',
                ),
                array(
                    'name' => 'secret_key',
                    'type' => 'secret_key',
                    'label' => 'Secret Key',
                    'value' => '',
                ),
                array(
                    'name' => 'request_type',
                    'type' => 'select',
                    'label' => 'Request Type',
                    'options' => array(
                        'captureWallet' => 'MoMo Wallet (qrCodeUrl)',
                        'payWithATM' => 'ATM',
                        'payWithCC' => 'Credit Card',
                    ),
                    'value' => 'captureWallet',
                ),
                array(
                    'name' => 'webhook_url',
                    'type' => 'webhook_url',
                    'label' => 'Webhook URL',
                    'value' => '',
                ),
            ),
        );

        $methods[] = $momo_method;
        return $methods;
    }
}