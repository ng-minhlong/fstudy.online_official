<?php

namespace TutorAlipay;

use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;
use Ollyo\PaymentHub\Payments\Alipay\Config;
use Tutor\Ecommerce\Settings;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;
use TutorPro\Ecommerce\Config as EcommerceConfig;

/**
 * AlipayConfig class.
 *
 * This class handles the configuration for the Alipay payment gateway.
 * It extends the BaseConfig class and implements the ConfigContract interface.
 *
 * @since 1.0.0
 */
class AlipayConfig extends Config implements ConfigContract {

	use PaymentUrlsTrait;

	/**
	 * Environment setting.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $environment;

	/**
	 * Client ID for authentication
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Private key for authentication
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $private_key;

	/**
	 * Public key for authentication
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $public_key;

	/**
	 * Region
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $region;

	/**
	 * The name of the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'alipay';

	/**
	 * Constructor.
	 *
	 * Initializes the AlipayConfig object.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$settings    = Settings::get_payment_gateway_settings( 'alipay' );
		$config_keys = array_keys( EcommerceConfig::get_alipay_config_keys() );
		foreach ( $config_keys as $key ) {
			if ( 'webhook_url' !== $key ) {
				$this->$key = $this->get_field_value( $settings, $key );
			}
		}
	}

	/**
	 * Retrieves the mode of the Alipay payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @return string The mode of the payment gateway ('test' or 'live').
	 */
	public function getMode(): string {
		return $this->environment ?? 'test';
	}

	/**
	 * Get client id.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getClientId() {
		return $this->client_id;
	}

	/**
	 * Get public key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getPublicKey() : string {
		return $this->public_key;
	}


	/**
	 * Get private key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getPrivateKey() : string {
		return $this->private_key;
	}

	/**
	 * Get region.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getRegion() {
		return $this->region ?? 'asia';
	}

	/**
	 * Determine whether payment gateway configured properly
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_configured() {
		return $this->public_key && $this->private_key && $this->client_id;
	}

}
