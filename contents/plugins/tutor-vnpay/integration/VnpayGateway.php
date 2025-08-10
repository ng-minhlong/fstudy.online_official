<?php

namespace TutorVnpay;

use Tutor\PaymentGateways\GatewayBase;

class VnpayGateway extends GatewayBase {
    private $dir_name = 'Vnpay';
    private $config_class = VnpayConfig::class;
    private $payment_class = \Ollyo\PaymentHub\Payments\Vnpay\Vnpay::class;

    public function get_root_dir_name(): string {
        return $this->dir_name;
    }

    public function get_payment_class(): string {
        return $this->payment_class;
    }

    public function get_config_class(): string {
        return $this->config_class;
    }

    public static function get_autoload_file() {
        return '';
    }
}


