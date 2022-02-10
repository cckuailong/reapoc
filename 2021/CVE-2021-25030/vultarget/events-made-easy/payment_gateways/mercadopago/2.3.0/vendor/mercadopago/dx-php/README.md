# Mercado Pago SDK for PHP

[![Latest Stable Version](https://poser.pugx.org/mercadopago/dx-php/v/stable)](https://packagist.org/packages/mercadopago/dx-php)
[![Total Downloads](https://poser.pugx.org/mercadopago/dx-php/downloads)](https://packagist.org/packages/mercadopago/dx-php)
[![License](https://poser.pugx.org/mercadopago/dx-php/license)](https://packagist.org/packages/mercadopago/dx-php)

This library provides developers with a simple set of bindings to help you integrate Mercado Pago API to a website and start receiving payments.

## 💡 Requirements

PHP 5.6, 7.1 or higher

## 📲 Installation 

First time using Mercado Pago? Create your [Mercado Pago account](https://www.mercadopago.com), if you don’t have one already.

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) if not already installed

2. On your project directory run on the command line
`composer require "mercadopago/dx-php:2.3.0"` for PHP7 or `composer require "mercadopago/dx-php:1.11.0"` for PHP5.6.

3. Copy the access_token in the [credentials](https://www.mercadopago.com/mlb/account/credentials) section of the page and replace YOUR_ACCESS_TOKEN with it.

That's it! Mercado Pago SDK has been successfully installed.

## 🌟 Getting Started
  
  Simple usage looks like:
  
```php
  <?php
    require_once 'vendor/autoload.php'; // You have to require the library from your Composer vendor folder

    MercadoPago\SDK::setAccessToken("YOUR_ACCESS_TOKEN"); // Either Production or SandBox AccessToken

    $payment = new MercadoPago\Payment();
    
    $payment->transaction_amount = 141;
    $payment->token = "YOUR_CARD_TOKEN";
    $payment->description = "Ergonomic Silk Shirt";
    $payment->installments = 1;
    $payment->payment_method_id = "visa";
    $payment->payer = array(
      "email" => "larue.nienow@email.com"
    );

    $payment->save();

    echo $payment->status;
  ?>
```

## 📚 Documentation 

Visit our Dev Site for further information regarding:
 - Payments APIs: [Spanish](https://www.mercadopago.com.ar/developers/es/guides/payments/api/introduction/) / [Portuguese](https://www.mercadopago.com.br/developers/pt/guides/payments/api/introduction/)
 - Mercado Pago checkout: [Spanish](https://www.mercadopago.com.ar/developers/es/guides/payments/web-payment-checkout/introduction/) / [Portuguese](https://www.mercadopago.com.br/developers/pt/guides/payments/web-payment-checkout/introduction/)
 - Web Tokenize checkout: [Spanish](https://www.mercadopago.com.ar/developers/es/guides/payments/web-tokenize-checkout/introduction/) / [Portuguese](https://www.mercadopago.com.br/developers/pt/guides/payments/web-tokenize-checkout/introduction/)

Check [our official code reference](https://mercadopago.github.io/dx-php/) to explore all available functionalities.

## ❤️ Support 

If you require technical support, please contact our support team at [developers.mercadopago.com](https://developers.mercadopago.com)

## 🏻 License 

```
MIT license. Copyright (c) 2018 - Mercado Pago / Mercado Libre 
For more information, see the LICENSE file.
```
