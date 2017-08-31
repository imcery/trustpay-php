<?php

namespace Imcery\TrustPay;

/**
 * Class TrustPayResponse
 * @package Imcery\TrustPay
 */
class TrustPayResponse
{

    /** @var \stdClass */
    public $response;

    /** @var string */
    public $original;

    /**
     * TrustPayResponse constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = json_decode($response);
        $this->original = $response;
    }

    /**
     * Shortcut to obtain response parameters directly.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (strtolower($name) == 'errormessage') {
            return $this->getErrorMessage();
        }

        return $this->response->$name;
    }

    /**
     * Checks if response was successful or not
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if (preg_match('/^(000\.000\.|000\.100\.1|000\.[36])|^(000\.400\.0|000\.400\.100)|^(000\.200)/', $this->response->result->code)) {
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