<?php

namespace Imcery\TrustPay;

final class TrustPay
{
    /**
     *
     */
    const API_VERSION = 'v1';

    /**
     * @var string
     */
    private $testEndpoint = 'https://test.oppwa.com';

    /**
     * @var string
     */
    private $liveEndpoint = 'https://oppwa.com';

    /**
     * Config
     *
     * @var array
     */
    public $config = [
        'test_mode' => true,
        'user_id' => '',
        'entity_id' => '',
        'password' => '',
    ];

    /**
     * Creates new instance of Image Manager
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->checkRequirements();
        $this->configure($config);
    }

    /**
     * Overrides configuration settings
     *
     * @param array $config
     * @return self
     */
    public function configure(array $config = [])
    {
        $this->config = array_replace($this->config, $config);

        return $this;
    }

    /**
     * @param string $amount
     * @param string $currency
     * @param array $params
     * @return TrustPayResponse
     */
    public function purchase($amount, $currency, $params = [])
    {
        $this->validate($amount, $currency);

        $postData = array_merge([
            'paymentType' => 'DB',
            'amount' => $amount,
            'currency' => $currency
        ], $params);

        return $this->sendData('/checkouts', $postData);
    }

    /**
     * @param string $transactionReference
     * @param string $amount
     * @param string $currency
     * @param array $params
     * @return TrustPayResponse
     */
    public function refund($transactionReference, $amount, $currency, $params = [])
    {
        $this->validate($transactionReference, $amount, $currency);

        $postData = array_merge([
            'paymentType' => 'RF',
            'amount' => $amount,
            'currency' => $currency
        ], $params);

        return $this->sendData('/payments/'. $transactionReference, $postData);
    }

    /**
     * @param string $amount
     * @param string $currency
     * @param array $params
     * @return TrustPayResponse
     */
    public function initialRecurring($amount, $currency, $params = [])
    {
        $this->validate($amount, $currency);

        $postData = array_merge([
            'paymentType' => 'DB',
            'recurringType' => 'INITIAL',
            'amount' => $amount,
            'currency' => $currency,
            'createRegistration' => 'true',
        ], $params);

        return $this->sendData('/checkouts', $postData);
    }

    /**
     * @param string $transactionReference
     * @param string $amount
     * @param string $currency
     * @param array $params
     * @return TrustPayResponse
     */
    public function repeatedRecurring($transactionReference, $amount, $currency, $params = [])
    {
        $this->validate($transactionReference, $amount, $currency);

        $postData = array_merge([
            'paymentType' => 'DB',
            'recurringType' => 'REPEATED',
            'amount' => $amount,
            'currency' => $currency,
        ], $params);

        return $this->sendData('/registrations/' . $transactionReference . '/payments', $postData);
    }

    /**
     * @param $transactionReference
     * @return TrustPayResponse
     */
    public function status($transactionReference)
    {
        $this->validate($transactionReference);

        return $this->sendData('/checkouts/'. $transactionReference . '/payment' , [], 'GET');
    }

    /**
     * @param string $checkoutId
     * @param string $returnUrl
     * @return string
     */
    static public function renderForm($checkoutId, $returnUrl)
    {
        return '
            <script async src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId='.$checkoutId.'"></script>
            <form action="'.$returnUrl.'" class="paymentWidgets" data-brands="VISA MASTER"></form>
        ';
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        $base = $this->config['test_mode'] ? $this->testEndpoint : $this->liveEndpoint;
        return $base . '/' . self::API_VERSION;
    }

    /**
     * @param $resource
     * @param array $data
     * @param string $method
     * @return TrustPayResponse
     */
    protected function sendData($resource, $data = [], $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $authentication = [
            'authentication.userId' => $this->config['user_id'],
            'authentication.password' => $this->config['password'],
            'authentication.entityId' => $this->config['entity_id'],
        ];
        $data = array_merge($data, $authentication);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $this->getEndpoint() . $resource);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_URL, $this->getEndpoint() . $resource . '?' . http_build_query($authentication));
        }

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Imcery\TrustPay\Exceptions\RequestException(
                curl_error($ch)
            );
        }

        return new TrustPayResponse($result);
    }

    /**
     * @param array ...$params
     */
    private function validate(...$params)
    {
        foreach($params as $param) {
            if (!is_string($param)) {
                throw new \BadFunctionCallException($param . ' must be string type');
            }
        }
    }

    /**
     * Check if all requirements are available
     *
     * @return void
     */
    private function checkRequirements()
    {
        if ( ! function_exists('curl_init')) {
            throw new \Imcery\TrustPay\Exceptions\MissingDependencyException(
                "PHP cURL extension must be installed/enabled to use TrustPay."
            );
        }
    }
}
