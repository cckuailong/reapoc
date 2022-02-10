# IPSP PHP-SDK-v2

<p align="center">
  <img width="200" height="200" src="https://avatars0.githubusercontent.com/u/15383021?s=200&v=4">
</p>

## Payment service provider
A payment service provider (PSP) offers shops online services for accepting electronic payments by a variety of payment methods including credit card, bank-based payments such as direct debit, bank transfer, and real-time bank transfer based on online banking. Typically, they use a software as a service model and form a single payment gateway for their clients (merchants) to multiple payment methods. 
[read more](https://en.wikipedia.org/wiki/Payment_service_provider)

## Installation

This SDK uses composer.

Composer is a tool for dependency management in PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.

For more information on how to use/install composer, please visit https://github.com/composer/composer

#### Composer installation
```cmd
composer require cloudipsp/php-sdk-v2
```
#### Manual installation
```cmd
git clone -b master https://github.com/cloudipsp/php-sdk-v2.git
```

```php
<?php
require '/path-to-sdk/autoload.php';
```
## Simple Start
```php
require 'vendor/autoload.php';
\Cloudipsp\Configuration::setMerchantId(1396424);
\Cloudipsp\Configuration::setSecretKey('test');

$checkoutData = [
    'currency' => 'USD',
    'amount' => 1000
];
$data = \Cloudipsp\Checkout::url($data);
$url = $data->getUrl();
//$data->toCheckout() - redirect to checkout
```
# Api

See [php-docs](https://cloudipsp.github.io/php-docs/)
## Examples
To check it you can use build-in php server
```cmd
cd ~/php-sdk-v2
php -S localhost:8000
```
[Checkout examples](https://github.com/cloudipsp/php-sdk-v2/tree/master/examples)

## Author

D.M.