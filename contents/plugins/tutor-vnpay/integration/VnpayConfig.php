<?php

namespace TutorVnpay;

use Tutor\Ecommerce\Settings;
use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;
use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;

class VnpayConfig extends BaseConfig implements ConfigContract {
    use PaymentUrlsTrait;

    private $environment;
    private $tmn_code;
    private $hash_secret;
    private $language;

    protected $name = 'vnpay';

    public function __construct() {
        parent::__construct();
        $settings = Settings::get_payment_gateway_settings( 'vnpay' );
        foreach ( [ 'environment', 'tmn_code', 'hash_secret', 'language' ] as $key ) {
            $this->$key = $this->get_field_value( $settings, $key );
        }
    }

    public function getMode(): string {
        return $this->environment ?: 'test';
    }

    public function getTmnCode(): string {
        return (string) $this->tmn_code;
    }

    public function getHashSecret(): string {
        return (string) $this->hash_secret;
    }

    public function getLanguage(): string {
        return $this->language ?: 'vn';
    }

    public function is_configured() {
        return true;
    }

    public function createConfig(): void {
        parent::createConfig();

        $pay_url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

        $this->updateConfig([
            'pay_url'      => $pay_url,
            'tmn_code'     => $this->getTmnCode(),
            'hash_secret'  => $this->getHashSecret(),
            'language'     => $this->getLanguage(),
        ]);
    }
}


