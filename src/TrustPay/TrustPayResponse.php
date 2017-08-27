<?php

namespace Imcery\TrustPay;

/**
 * Class TrustPayResponse
 * @package Imcery\TrustPay
 */
class TrustPayResponse
{

    /** @var \stdClass */
    private $response;

    /**
     * TrustPayResponse constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = json_decode($response);
    }

    /**
     * Checks if response was successful or not
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if (preg_match('/^(000\.000\.|000\.100\.1|000\.[36])|^(000\.400\.0|000\.400\.100)/', $this->response->result->code)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->response->result->description;
    }

}