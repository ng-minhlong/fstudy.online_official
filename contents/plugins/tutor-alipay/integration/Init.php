<?php
/**
 * Init class
 *
 * @package TutorPro\Payments\Integrations
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

namespace TutorAlipay;

/**
 * Init class
 */
final class Init {

	/**
	 * Register hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'tutor_gateways_with_class', __CLASS__ . '::payment_gateways_with_ref', 10, 2 );
		add_filter( 'tutor_payment_gateways_with_class', __CLASS__ . '::filter_payment_gateways' );
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
	public static function payment_gateways_with_ref( $value, $gateway ) {
		$arr = array(
			'alipay' => array(
				'gateway_class' => AlipayGateway::class,
				'config_class'  => AlipayConfig::class,
			),
		);

		if ( isset( $arr[ $gateway ] ) ) {
			$value[ $gateway ] = $arr[ $gateway ];
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
	public static function filter_payment_gateways( $gateways ) {
		$arr = array(
			'alipay' => array(
				'gateway_class' => AlipayGateway::class,
				'config_class'  => AlipayConfig::class,
			),
		);

		$gateways = $gateways + $arr;

		return $gateways;
	}
}
