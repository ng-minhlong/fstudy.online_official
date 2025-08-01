<?php

namespace Ollyo\PaymentHub\Payments\Alipay;

use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;

class Config extends BaseConfig implements ConfigContract
{
	protected $name = 'alipay';

    /**
     * Constant values for Alipay
     */
    const NORTH_AMERICA_REGION_URL = 'https://open-na-global.alipay.com';
    const ASIA_REGION_URL          = 'https://open-sea-global.alipay.com';
    const CONSULT_API_URL_TEST     = '/ams/sandbox/api/v1/payments/consult';
    const CONSULT_API_URL_LIVE     = '/ams/api/v1/payments/consult';
    const HTTP_METHOD              = 'POST';
    const DEFAULT_KEY_VERSION      = 1;
    const PAY_API_URL_TEST         = '/ams/sandbox/api/v1/payments/pay';
    const PAY_API_URL_LIVE         = '/ams/api/v1/payments/pay';
    const INQUIRY_API_URL_TEST     = '/ams/sandbox/api/v1/payments/inquiryPayment';
    const INQUIRY_API_URL_LIVE     = '/ams/api/v1/payments/inquiryPayment';
	
    
	public function __construct()
	{
		parent::__construct();
	}

    public function getMode(): string
	{
		return 'test';
	}

	public function getPublicKey(): string
	{
		return 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjr2yTEh2Hl2Pdsd4c/9Imz4yQ+zbfZf8bCi3L+bQwOXSJMr4D+D5RctCo5PkaRwv94KaJXp+gqS1glRjS7KTL7iLX8rsyCrxxIaejvLlKlLXEDPkRgq7+SPKrm/QqzDg+9m84yVknBd+X8l6zlTePMgh6QafCKy0mJc2uXhmdl0Z/1c3TJcdLUBu0em3iVxJje54WcppEe+cuNRsbGgikBl5dIspipw25X56y9pd8Uh/tXqfCQ5IJkYaLcH6rwH3uXauObQnmkmlG86A3FMMi3UTD7hdCVKf1cvnl7EQ8miP1/1I9889sTiQILf2a9BNjvr9V6wsPFYUWmQRm1OyBQIDAQAB';
	}

	public function getSuccessUrl(): string
	{
		return 'https://example.com/success';
	}

	public function getCancelUrl(): string
	{
		return 'https://example.com/cancel';
	}

	public function getWebhookUrl(): string
	{
        return 'https://example.com/webhook';
	}

	public function getAdditionalInformation(): string
	{
		return '';
	}

	public function getTitle(): string
	{
		return '';
	}

	public function getName(): string
	{
		return $this->name;
	}

    public function getPrivateKey() : string
    {
        return 'MIIEugIBADANBgkqhkiG9w0BAQEFAASCBKQwggSgAgEAAoIBAQCWmtrjYc3mEuB8brXXAZfu7xeyUUTOJ/3LsxHb9YRLYUCjPAXaQOK2gFlbC7AyauTort+t10Q5jiaqmkiT548hEaF7tQeJ3J1d4A3+FaknG+pYSzbfbs5HpULGEvbmSH9ZrFYWTSwsaTkS/2nQgEyMR8tGeHQ5hoIzxLGqZIplfee2EjUFWPFI0qG/Q840S4QGfAHtGxlo6qmvJmFW6bGcKSZdF1V5JXgLaP8JGGPUxClb58PeQvL1GLkYiq2si8+GavgtNiNLHzCKGGAOc4LQlA8pdPJ8LpJwFoC0kZ8BHw1pv5al+A+M5RFKT9y2sq7nXY9jbe1VK5iaFHv6bi2NAgMBAAECggEAbpukjsLR+Vt7y2dz3UiqUSz/9lKBefcdCnGleCpE0yfF1RzMH5Lv7qEs/xUCfsTLAakNVht3W93uv/U3wIicMelE9BnsQ2/nk35uSGGYLcTuw5HZ5xb2IOBaviZHdrFf9nf/cbmT67oL1MwI5ryTe6Nuw37LvUPwdBzNbxsQGaDQ7ArhceaQX7+QHidx5n9/b1EksndMRrKnEjkmjIlUfXJf260aunSXDQ7+fi2jx4c7F7wyYac03AzqpAIUj7cdq1+WV1Xq8j+YMV23g65T36yfc4OUgr5HW/R2NHW8iaAbBaCW0SJ+P8gh3DF9lmMpkhj7bs18XKViBtJ6M8OcAQKBgQDWyCIC3oR9SaFDtCA32gzVw15iBwpADWmyQ6HgcHJRwn96J2lgMHOhwKYlM9NN15ZCAl6N6mJRN54lIjPyFsIvmQwKChwADOieDbaxtWWpBOTdCj4ovKWAO+fn6Oa6rtXKiatJrVbqFyej8PHANC9mFyM0STdtoc/aTmIF+yZ3QQKBgQCzgdIViGrgFPqFhTzRqAoZr2FXoVOuEuYDM5stqgZxMhu4WMGwOmxCqEDM3tfD8nDXdoQdZEORUediaIRXy+8KLodoN8IyWOZKD62mFfb7kcWJ/Ho1N1yYdResISZt86GMXuEcbyUCz/mgTPJS2KwBMhQd6slSI87N/tQXSn+PTQJ/bef7f3xeT75dx0dlNS9fYgOXSJJRrHUwoQf6zd68oRnmIZwJUC96/EqYunDKsAqh2SVdPUGzDdxoEJhv/HGo7LJkdr61lVEFt9tMoaMxYkwe0P1s0Wu3ROdixElpyB2vSPUpsYoB78fpgPrKuo/5sUQlv5u3wWIL+qohfu4FwQKBgHpRskh+BuXfJb8qRYFQEmHiQmCmTBFIS3hPCCUbwPwNexopUQK3LOJXKKDyEPEzGXWZCvnEQs6ZkyRZuCTjkGXNwvNfW3NXZMV+3YNZYE2YzZTzeIseyHgTAiiHBlrSM3klhhwMGJpMAfwbstyFTOAbsd2ZCcpxIBD63qdoWnfZAoGAa6s16fjUZl88ZVzJtK44Yt6dgRKAawINY/FIaFXyUgUx/OzOqj16halSZMIHBI7N3q1vulZakvhcDUT+m31gTMAnt7kq5zdffLuG4FX0yyThrFPl/+BhlUOw0Dlzz/D/bQZ7UqPOjGcUJotzy4H5x9U4otoup5KU3IjAsaaHXpQ=';
    }

    /**
     * Gets the region code for the current context.
     *
     * This function determines the region code based on predefined logic.
     * - North America is represented by 'na'.
     * - Asia is represented by 'asia'.
     *
     * @return string The region code.
     * @since  1.0.0
     */
    public function getRegion()
    {
        return 'asia';
    }

    public function getRegionUrl()
    {
        return $this->getRegion() === 'asia' ? self::ASIA_REGION_URL : self::NORTH_AMERICA_REGION_URL;
    }

    public function getConsultApiUrl()
    {
        return $this->getMode() === 'test' ? self::CONSULT_API_URL_TEST : self::CONSULT_API_URL_LIVE;
    }

    public function getPayApiUrl()
    {
        return $this->getMode() === 'test' ? self::PAY_API_URL_TEST : self::PAY_API_URL_LIVE;
    }

    public function getInquiryApiUrl()
    {
        return $this->getMode() === 'test' ? self::INQUIRY_API_URL_TEST : self::INQUIRY_API_URL_LIVE;
    }

    public function getClientId()
    {
        return 'SANDBOX_5YBW8J2ZFU5H01871';
    }

    public function createConfig(): void
    {
        parent::createConfig();
        
        $config = [
            'public_key'          => $this->getPublicKey(),
            'private_key'         => $this->getPrivateKey(),
            'client_id'           => $this->getClientId(),
            'region_url'          => $this->getRegionUrl(),
            'consult_url'         =>$this->getConsultApiUrl(),
            'pay_url'             => $this->getPayApiUrl(),
            'inquiry_url'         => $this->getInquiryApiUrl(),
            'http_method'         => self::HTTP_METHOD,
            'default_key_version' => self::DEFAULT_KEY_VERSION
        ];

        $this->updateConfig($config);
        
    }
}