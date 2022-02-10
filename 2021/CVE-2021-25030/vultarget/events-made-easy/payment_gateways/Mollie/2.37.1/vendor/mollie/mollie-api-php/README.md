<p align="center">
  <img src="https://info.mollie.com/hubfs/github/php/logo.png" width="128" height="128"/>
</p>
<h1 align="center">Mollie API client for PHP</h1>

<img src="https://info.mollie.com/hubfs/github/php/editor.png" />

Accepting [iDEAL](https://www.mollie.com/payments/ideal/), [Apple Pay](https://www.mollie.com/payments/apple-pay), [Bancontact](https://www.mollie.com/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/payments/sofort/), [Creditcard](https://www.mollie.com/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/payments/direct-debit/), [PayPal](https://www.mollie.com/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/payments/belfius/), [KBC/CBC](https://www.mollie.com/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/payments/ing-homepay/), [Giftcards](https://www.mollie.com/payments/gift-cards/), [Giropay](https://www.mollie.com/payments/giropay/), [EPS](https://www.mollie.com/payments/eps/) and [Przelewy24](https://www.mollie.com/payments/przelewy24/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

[![Build Status](https://github.com/mollie/mollie-api-php/workflows/tests/badge.svg)](https://github.com/mollie/mollie-api-php/actions)
[![Latest Stable Version](https://poser.pugx.org/mollie/mollie-api-php/v/stable)](https://packagist.org/packages/mollie/mollie-api-php)
[![Total Downloads](https://poser.pugx.org/mollie/mollie-api-php/downloads)](https://packagist.org/packages/mollie/mollie-api-php)

## Requirements ##
To use the Mollie API client, the following things are required:

+ Get yourself a free [Mollie account](https://www.mollie.com/signup). No sign up costs.
+ Now you're ready to use the Mollie API client in test mode.
+ Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
+ PHP >= 5.6
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)

For leveraging [Mollie Connect](https://docs.mollie.com/oauth/overview) (advanced use cases only), we recommend also installing our [OAuth2 client](https://github.com/mollie/oauth2-mollie-php).

## Composer Installation ##

By far the easiest way to install the Mollie API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require mollie/mollie-api-php:^2.0

    {
        "require": {
            "mollie/mollie-api-php": "^2.0"
        }
    }

The version of the API client corresponds to the version of the API it implements. Check the [notes on migration](https://docs.mollie.com/migrating-v1-to-v2) to see what changes you need to make if you want to start using a newer API version.


## Manual Installation ##
If you're not familiar with using composer we've added a ZIP file to the releases containing the API client and all the packages normally installed by composer.
Download the ``mollie-api-php.zip`` from the [releases page](https://github.com/mollie/mollie-api-php/releases).

Include the ``vendor/autoload.php`` as shown in [Initialize example](https://github.com/mollie/mollie-api-php/blob/master/examples/initialize.php).

## How to receive payments ##

To successfully receive a payment, these steps should be implemented:

1. Use the Mollie API client to create a payment with the requested amount, currency, description and optionally, a payment method. It is important to specify a unique redirect URL where the customer is supposed to return to after the payment is completed.

2. Immediately after the payment is completed, our platform will send an asynchronous request to the configured webhook to allow the payment details to be retrieved, so you know when exactly to start processing the customer's order.

3. The customer returns, and should be satisfied to see that the order was paid and is now being processed.

Find our full documentation online on [docs.mollie.com](https://docs.mollie.com).

## Getting started ##

Initializing the Mollie API client, and setting your API key.

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
``` 

Creating a new payment.

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
]);
```
_After creation, the payment id is available in the `$payment->id` property. You should store this id with your order._

After storing the payment id you can send the customer to the checkout using the `$payment->getCheckoutUrl()`.  

```php
header("Location: " . $payment->getCheckoutUrl(), true, 303);
```
_This header location should always be a GET, thus we enforce 303 http response code_

For a payment create example, see [Example - New Payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/create-payment.php).

## Retrieving payments ##
We can use the `$payment->id` to retrieve a payment and check if the payment `isPaid`.

```php
$payment = $mollie->payments->get($payment->id);

if ($payment->isPaid())
{
    echo "Payment received.";
}
```

Or retrieve a collection of payments.

```php
$payments = $mollie->payments->page(); 
```

For an extensive example of listing payments with the details and status, see [Example - List Payments](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/list-payments.php).

## Payment webhook ##

When the status of a payment changes the `webhookUrl` we specified in the creation of the payment will be called.  
There we can use the `id` from our POST parameters to check te status and act upon that, see [Example - Webhook](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/webhook.php).


## Multicurrency ##
Since 2.0 it is now possible to create non-EUR payments for your customers.
A full list of available currencies can be found [in our documentation](https://docs.mollie.com/guides/multicurrency).

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "USD",
        "value" => "10.00"
    ],
    "description" => "Order #12345",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
]);
```
_After creation, the `settlementAmount` will contain the EUR amount that will be settled on your account._


### Fully integrated iDEAL payments ###

If you want to fully integrate iDEAL payments in your web site, some additional steps are required. First, you need to
retrieve the list of issuers (banks) that support iDEAL and have your customer pick the issuer he/she wants to use for
the payment.

Retrieve the iDEAL method and include the issuers

```php
$method = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"]);
```

_`$method->issuers` will be a list of objects. Use the property `$id` of this object in the
 API call, and the property `$name` for displaying the issuer to your customer. For a more in-depth example, see [Example - iDEAL payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/create-ideal-payment.php)._

Create a payment with the selected issuer:

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
    "method"      => \Mollie\Api\Types\PaymentMethod::IDEAL,
    "issuer"      => $selectedIssuerId, // e.g. "ideal_INGBNL2A"
]);
```

_The `_links` property of the `$payment` object will contain an object `checkout` with a `href` property, which is a URL that points directly to the online banking environment of the selected issuer.
A short way of retrieving this URL can be achieved by using the `$payment->getCheckoutUrl()`._

### Refunding payments ###

The API also supports refunding payments. Note that there is no confirmation and that all refunds are immediate and
definitive. refunds are supported for all methods except for paysafecard and gift cards.

```php
$payment = $mollie->payments->get($payment->id);

// Refund € 2 of this payment
$refund = $payment->refund([
    "amount" => [
        "currency" => "EUR",
        "value" => "2.00"
    ]
]);
```

For a working example, see [Example - Refund payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/refund-payment.php).

## API documentation ##
If you wish to learn more about our API, please visit the [Mollie Developer Portal](https://www.mollie.com/developers). API Documentation is available in English.

## Want to help us make our API client even better? ##

Want to help us make our API client even better? We take [pull requests](https://github.com/mollie/mollie-api-php/pulls?utf8=%E2%9C%93&q=is%3Apr), sure. But how would you like to contribute to a technology oriented organization? Mollie is hiring developers and system engineers. [Check out our vacancies](https://jobs.mollie.com/) or [get in touch](mailto:personeel@mollie.com).

## License ##
[BSD (Berkeley Software Distribution) License](https://opensource.org/licenses/bsd-license.php).
Copyright (c) 2013-2018, Mollie B.V.

## Support ##
Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20 820 20 70
