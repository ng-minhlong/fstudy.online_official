<?php
/**
 * Init class
 *
 * @package TutorPro\Payments\Integrations
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

namespace CustomPayment;

/**
 * Init class
 * 
 * This class initializes the Custom Payment Gateway by registering hooks and filters for integrating with Tutor's payment 
 * system. It adds the Custom Payment method to Tutor's list of payment gateways, allows customization of gateway settingsand 
 * manages the gateway's configuration and functionality.
 */
final class Init
{
    /**
     * Register hooks
     * 
     * This method registers filters that allow Custom Payment Gateway to be added to Tutor's available payment gateway
     * filter payment methods, and add the gateway's configuration options in Tutor settings.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_filter('tutor_gateways_with_class', __CLASS__ . '::payment_gateways_with_ref', 10, 2); // For Webhook Integration
        add_filter('tutor_payment_gateways_with_class', __CLASS__ . '::filter_payment_gateways'); // For Checkout Integration
        add_filter('tutor_payment_gateways', array($this, 'add_tutor_custom_payment_method'), 100); // Add Settings Options
    }

    /**
     * Get payment gateways with reference class
     *
     * @since 1.0.0
     *
     * @param array  $value Gateway with ref class.
     * @param string $gateway Payment gateway name.
     *
     * @return array|null
     */
    public static function payment_gateways_with_ref($value, $gateway)
    {
        $arr = array(
            'momopayment' => array(
                'gateway_class' => MomoPaymentGateway::class,
                'config_class' => MomoPaymentConfig::class,
            ),
        );

        if (isset($arr[$gateway])) {
            $value[$gateway] = $arr[$gateway];
        }

        return $value;
    }

    /**
     * Get payment gateways with reference class
     *
     * @since 1.0.0
     *
     * @param array $gateways Tutor payment gateways.
     *
     * @return array|null
     */
    public static function filter_payment_gateways($gateways)
    {
        $arr = array(
            'momopayment' => array(
                'gateway_class' => MomoPaymentGateway::class,
                'config_class' => MomoPaymentConfig::class,
            ),
        );

        $gateways = $gateways + $arr;

        return $gateways;
    }

    /**
     * Add custom payment method.
     *
     * This method defines the configuration fields for the Custom Payment method and adds it to Tutor's payment options.
     *
     * @since 1.0.0
     *
     * @param array $methods Tutor existing payment methods.
     *
     * @return array
     */
    public function add_tutor_custom_payment_method($methods)
    {
        $custom_payment_method = array(
            'name' => 'momopayment',
            'label' => 'Momo Payment Automation',
            'is_installed' => true,
            'is_active' => true,
            'icon' => 'https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png?20201011055544', // Icon url.
            'support_subscription' => false,
            'fields' => array(
                array(
                    'name' => 'environment',
                    'type' => 'select',
                    'label' => 'Environment',
                    'options' => array(
                        'sandbox' => 'Sandbox',
                        'live' => 'Live',
                    ),
                    'value' => 'sandbox',
                ),
                array(
                    'name' => 'secret_key',
                    'type' => 'secret_key',
                    'label' => 'Secret Key',
                    'value' => '',
                ),
                array(
                    'name' => 'public_key',
                    'type' => 'secret_key',
                    'label' => 'Public Key',
                    'value' => '',
                ),
                array(
                    'name' => 'client_id',
                    'type' => 'text',
                    'label' => 'Client ID',
                    'value' => '',
                ),
                array(
                    'name' => 'webhook_url',
                    'type' => 'webhook_url',
                    'label' => 'Webhook URL',
                    'value' => '',
                ),
            ),
        );

        $methods[] = $custom_payment_method;
        return $methods;
    }

}