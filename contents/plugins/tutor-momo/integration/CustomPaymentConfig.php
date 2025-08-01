<?php

namespace MomoPayment;

use Tutor\Ecommerce\Settings;
use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;
use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;

/**
 * MomoPaymentConfig class.
 *
 * This class is used to manage the configuration settings for the "MomoPayment" gateway. It extends the `BaseConfig`
 * class and implements the `ConfigContract` interface. The class is designed to interact with the `Settings` class to
 * retrieve configuration data for the gateway and provide necessary methods for accessing and validating the configuration.
 *
 * @since 1.0.0
 */
class MomoPaymentConfig extends BaseConfig implements ConfigContract {

	/**
	 * This trait provides methods to retrieve the URLs used in the payment process for success, cancellation, and webhook 
	 * notifications. It includes functionality for retrieving dynamic URLs based on the current environment (e.g., 
	 * live or test) and allows for filterable URL customization.
	 */
	use PaymentUrlsTrait;

	/**
	 * Stores the environment setting for the payment gateway, such as 'test' or 'live'.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $environment;

	/**
	 * Stores the public key for the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $public_key;

	/**
	 * Stores the secret key for the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $secret_key;

	/**
	 * Stores the client ID for the payment gateway.
	 * @var   string
	 * @since 1.0.0
	 */
	private $client_id;

	/**
	 * The name of the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'custompayment';

	/**
	 * Constructor.
	 *
	 * Initializes the `MomoPaymentConfig` object by loading settings for the "momo_payment" gateway from the Settings
	 * class. It populates the object's properties based on the keys retrieved from the settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		
		parent::__construct();
		
		$settings    = Settings::get_payment_gateway_settings( 'custompayment' );
		$config_keys = array_keys( self::get_custompayment_config_keys() );
		
		foreach ( $config_keys as $key ) {
			if ( 'webhook_url' !== $key ) {
				$this->$key = $this->get_field_value( $settings, $key );
			}
		}
	}

	/**
	 * Retrieves the mode of the MomoPayment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @return string The mode of the payment gateway ('test' or 'live').
	 */
	public function getMode(): string {
		return $this->environment;
	}

	/**
	 * Retrieves the secret key for the payment gateway.
	 *
	 * @return string
	 */
	public function getSecretKey(): string {
		return $this->secret_key;
	}

	/**
	 * Retrieves the public key for the payment gateway.
	 *
	 * @return string
	 */
	public function getPublicKey(): string {
		return $this->public_key;
	}

	/**
	 * Retrieves the client ID for the payment gateway.
	 * @return string
	 */
	private function getClientID(): string {
		return $this->client_id;
	}


	/**
	 * Checks if the payment gateway is properly configured. The gateway is considered configured if the properties values 
	 * are all present.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_configured() {
		return $this->secret_key && $this->public_key && $this->client_id;
	}

	/**
	 * Returns an array of the configuration keys for the `custompayment` gateway.
	 * @return array
	 */
	private function get_custompayment_config_keys(): array
	{
		return array(
			'environment' => 'environment', // Payment Environment Type. `Test` or `Live`. Custom field created for Tutor LMS
			'secret_key'  => 'secret_key', // Field Type is secret.
			'public_key'  => 'secret_key',
			'client_id'   => 'text' // Field Type is Text
		);
	}

	/**
	 * Creates the configuration for the payment gateway. 
	 * This method extends the `createConfig` method from the parent class and updates the configuration if needed.
	 * @return void
	 */
	public function createConfig(): void
	{
		parent::createConfig();

		// Update the configuration if the gateway requires additional fields beyond the default ones.
		$config = ['client_id' => $this->getClientID()];
		$this->updateConfig($config);
	}
}
