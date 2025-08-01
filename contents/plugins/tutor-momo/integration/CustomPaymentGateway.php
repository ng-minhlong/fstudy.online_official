<?php
/**
 * Payment gateway concrete class
 *
 * @package Tutor\Ecommerce
 * @author Themeum
 * @link https://themeum.com
 * @since 1.0.0
 */

namespace CustomPayment;

use Payments\Custom\Custompayment;
use Tutor\PaymentGateways\GatewayBase;


/**
 * Custom payment gateway class
 * 
 * This class represents the Custom payment gateway, providing necessary methods for integrating and configuring the payment 
 * system. It extends the `GatewayBase` class and provides functionality to retrieve key configurations like the 
 * payment directory name, the associated payment and configuration classes, and the autoload file for the gateway.
 */
class MomoPaymentGateway extends GatewayBase {

	/**
	 * Payment gateway dir name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $dir_name = 'Custompayment';

	/**
	 * Payment gateway config class
	 * 
	 * Specifies the class responsible for handling configuration for the Custom payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $config_class = CustomPaymentConfig::class;

	/**
	 * Payment core class
	 * Defines the core payment class used to process payments for the Custom payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $payment_class = Custompayment::class;

	/**
	 * Root dir name of payment gateway src
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_root_dir_name():string {
		return $this->dir_name;
	}

	/**
	 * Payment class from payment hub
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_payment_class():string {
		return $this->payment_class;
	}

	/**
	 * Payment config class
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_config_class():string {
		return $this->config_class;
	}

	/**
	 * Returns the autoload file for the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_autoload_file() {
		return '';
	}
}