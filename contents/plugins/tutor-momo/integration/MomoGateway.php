<?php
namespace TutorMomo;

use Ollyo\PaymentHub\Payments\Momo\Momo;
use Tutor\PaymentGateways\GatewayBase;

class MomoGateway extends GatewayBase {
    private $dir_name = 'Momo';
    private $config_class = MomoConfig::class;
    private $payment_class = Momo::class;

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
        return null;
    }
}
