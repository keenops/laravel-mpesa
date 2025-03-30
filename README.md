# Vodacom M-Pesa API package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keenops/laravel-mpesa.svg?style=flat-square)](https://packagist.org/packages/keenops/laravel-mpesa)
[![Total Downloads](https://img.shields.io/packagist/dt/keenops/laravel-mpesa.svg?style=flat-square)](https://packagist.org/packages/keenops/laravel-mpesa)

Engineered for seamless integration with the Vodacom M-Pesa OpenApi, it offers Laravel developers a streamlined pathway to execute customer-to-business transactions with ease and efficiency.

## Installation

You can install the package via composer:

```bash
composer require keenops/laravel-mpesa:v0.0.1-beta.2
```

Runing the above command should also publish in a config directory a file named laravel-mpesa.php. If so didnt happen, publish the configuration files by running the command:

```bash
php artisan vendor:publish --tag=laravel-mpesa
```

## Usage

Add Vodacom M-Pesa API credentials to a .env file. The credentials can be obtained [here](https://openapiportal.m-pesa.com/)

```bash
    MPESA_API_KEY= // get this from your mpesa developer/bussiness account
    MPESA_PUBLIC_KEY= // get this from your mpesa developer/bussiness account
    MPESA_ENVIROMENT= // options are: sandbox, openapi
    MPESA_MARKET_COUNTRY= // options are: TZN, GHA, LES, DRC
    MPESA_MARKET_CURRENCY= // options are: TZS, GHS, SLS, USD
```

### Customer to Business Transaction

In your controller use like this

```php
namespace App\Http\Controllers;

use Keenops\Mpesa\Mpesa;


class CheckoutController extends Controller
{
    public function checkout()
    {
        return Mpesa::c2b(
            amount: '1000',
            customerNumber:'255746277553',
            serviceCode: '000000',
            reference: 'T12344Z',
            description: 'Three items',
            conversationId: '1e9b774d1da34af78412a498cbc28f43e'
        );

        //this returns json response from M-Pesa API. Refer the link https://openapiportal.m-pesa.com/ for error and success codes
    }
}
```

### Business to Customer Transaction

In your controller:

```php
namespace App\Http\Controllers;

use Keenops\Mpesa\Mpesa;

class PayoutController extends Controller
{
    public function sendMoney()
    {
        return Mpesa::b2c(
            amount: '1000',
            customerNumber: '255746277553',
            serviceCode: '000000',
            reference: 'REF123456',
            description: 'Payment for services',
            conversationId: 'b2c12345678xyz'
        );

        // Returns JSON response from M-Pesa API.
    }
}
```

### Business to Business Transaction

In your controller:

```php
namespace App\Http\Controllers;

use Keenops\Mpesa\Mpesa;

class TransferController extends Controller
{
    public function transfer()
    {
        return Mpesa::b2b(
            senderCode: '000001',
            receiverCode: '000002',
            amount: '50000',
            reference: 'B2BRef001',
            description: 'Supplier payment',
            conversationId: 'b2bconv112233'
        );

        // Returns JSON response from M-Pesa API.
    }
}
```

### Reverse a Transaction

In your controller:

```php
namespace App\Http\Controllers;

use Keenops\Mpesa\Mpesa;

class ReversalController extends Controller
{
    public function reverseTransaction()
    {
        return Mpesa::reverse(
            amount: '1000',
            serviceProviderCode: '000000',
            conversationId: 'revConv778899',
            transactionId: 'Trans123456789'
        );

        // Returns JSON response from M-Pesa API.
    }
}
```

### Query Transaction Status

In your controller:

```php
namespace App\Http\Controllers;

use Keenops\Mpesa\Mpesa;

class StatusController extends Controller
{
    public function checkStatus()
    {
        return Mpesa::transactionStatus(
            queryReference: 'REF123456',
            serviceProviderCode: '000000',
            conversationId: 'statusConv456789'
        );

        // Returns JSON response from M-Pesa API.
    }
}
```

### Errors

Specific error codes may be displayed within parenthesis when send or receive operations fail. The most common of these error codes are specified in API documetation found on [openapiportal.m-pesa.com](https://openapiportal.m-pesa.com)

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email git@kimwalu.com instead of using the issue tracker.

## Credits

- [Kee Nops](https://github.com/keenops)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
