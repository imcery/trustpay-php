<?php

namespace Imcery\TrustPay;

use Illuminate\Support\ServiceProvider;

class TrustPayServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(array(
            __DIR__.'/../config/trustpay.php' => config_path('trustpay.php'),
        ));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // merge default config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'trustpay');

        // create image
        $app->singleton('trustpay', function ($app) {
            return new TrustPay($app['config']->get('trustpay'));
        });

        $app->alias('trustpay', 'Imcery\TrustPay\TrustPay');
    }

}
