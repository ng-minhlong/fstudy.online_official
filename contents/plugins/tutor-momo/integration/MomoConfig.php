<?php

namespace TutorMomo;

use Tutor\Ecommerce\Settings;
use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;
use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;

class MomoConfig extends BaseConfig implements ConfigContract {
	use PaymentUrlsTrait;

	private $environment;
	private $partner_code;
	private $access_key;
	private $secret_key;
	private $request_type;

	protected $name = 'momo';

	public function __construct() {
		parent::__construct();
		$settings = Settings::get_payment_gateway_settings( 'momo' );
		foreach ( [ 'environment', 'partner_code', 'access_key', 'secret_key', 'request_type' ] as $key ) {
			$this->$key = $this->get_field_value( $settings, $key );
		}
	}

	public function getMode(): string {
		return $this->environment ?: 'test';
	}

	public function getPartnerCode(): string {
		return (string) $this->partner_code;
	}

	public function getAccessKey(): string {
		return (string) $this->access_key;
	}

	public function getSecretKey(): string {
		return (string) $this->secret_key;
	}

    public function getRequestType(): string {
        // Valid request types for MoMo create API; 'qrCodeUrl' is a response field, not a request type
        $type = $this->request_type ?: 'captureWallet';
        if ($type === 'qrCodeUrl') {
            $type = 'captureWallet';
        }
        return $type;
    }

	public function is_configured() {
		return true;
	}

	public function createConfig(): void {
		parent::createConfig();
		$create_url = $this->getMode() === 'live'
			? 'https://payment.momo.vn/v2/gateway/api/create'
			: 'https://test-payment.momo.vn/v2/gateway/api/create';

		$this->updateConfig([
			'create_url'   => $create_url,
			'partner_code' => $this->getPartnerCode(),
			'access_key'   => $this->getAccessKey(),
			'secret_key'   => $this->getSecretKey(),
			'request_type' => $this->getRequestType(),
		]);
	}
}
