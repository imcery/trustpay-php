### Installation

The installation is simple as `composer install imcery/trustpay-php`.

### Integration in Laravel


TrustPay-PHP has an optional support for Laravel and comes with a **Service Provider and Facades** for easy integration.
The `vendor/autoload.php` is included by Laravel, so you don't have to require or autoload manually. Just see the instructions below.

After you have installed Imcery TrustPay, open your Laravel config file `config/app.php` and add the following lines.

In the `$providers` array add the service providers for this package.

```
Imcery\TrustPay\TrustPayServiceProvider::class
```

Add the facade of this package to the `$aliases` array.
```
'TrustPay' => Imcery\TrustPay\Facades\TrustPay::class
```

Now the TrustPay Class will be auto-loaded by Laravel.

### Configuration

By default Imcery TrustPay uses PHP's cURL library extension to process all http requests.

Publish configuration in Laravel 5

```
$ php artisan vendor:publish --provider="Imcery\TrustPay\TrustPayServiceProvider"
```

In Laravel 5 applications the configuration file is copied to `config/trustpay.php`.
With this copy you can alter application credentials for TrustPay.
