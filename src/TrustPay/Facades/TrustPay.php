<?php

namespace Imcery\TrustPay\Facades;

use Illuminate\Support\Facades\Facade;

class TrustPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Don't use this. Just... don't.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'trustpay';
    }
}
