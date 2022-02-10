PAYMILL-PHP
===========

[![Build Status](https://travis-ci.org/paymill/paymill-php.png)](https://travis-ci.org/paymill/paymill-php)[![Latest Stable Version](https://poser.pugx.org/paymill/paymill/v/stable.png)](https://packagist.org/packages/paymill/paymill)[![Total Downloads](https://poser.pugx.org/paymill/paymill/downloads.png)](https://packagist.org/packages/paymill/paymill)

VERSIONING
----------

This wrapper is using the api v2.1 launched in June 2014. If you wish to use the old api v2.0 please use the wrapper in branch v2: https://github.com/paymill/paymill-php/tree/v2.

How to test cards and errors
----------------------------

There are different credit card numbers, frontend and backend error codes, which can be used for testing. For more information, please read our testing reference. https://www.paymill.com/en-gb/documentation-3/reference/testing/

How to run unit and integration tests
-------------------------------------
Just run:

```
ant test
```


Getting started with PAYMILL
----------------------------

If you don't already use Composer, then you probably should read the installation guide http://getcomposer.org/download/.

Please include this library via Composer in your composer.json and execute **composer update** to refresh the autoload.php.

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/paymill/paymill-php"
    }
  ],
  "require": {
    "paymill/paymill": "dev-master"
  }
}
```

If you don't want to use composer, paymill-php library provides its own **autoload** script. You have to include the autoload script in all files, in which you are going to use the PAYMILL library.

Lets say you have two files, which are going to use the PAYMILL lib. First one is located in the project root, the other one is in the app folder. You have downloaded the PAYMILL library in your project root folder under the name **paymill-php**.

To load the PAYMILL library from the file, which is located in *your project root folder*, you need to **require** PAYMILL's **autoload** script like this:

```php
require './paymill-php/autoload.php';
```

To load the PAYMILL library from the file, which is located in *the app folder*, you need to **require** PAYMILL's **autoload** script like this:

```php
require '../paymill-php/autoload.php';
```

1. Instantiate the request class with the following parameters: $apiKey: First parameter is always your private API (test) Key

 ```php
 $request = new Paymill\Request($apiKey);
 ```

1. Instantiate the model class with the parameters described in the API-reference:

 ```php
 $payment = new \Paymill\Models\Request\Payment();
 $payment->setToken("098f6bcd4621d373cade4e832627b4f6");
 ```

1. Use your desired function:

 ```php
 $response  = $request->create($payment);
 $paymentId = $response->getId();
 ```

It recommend to wrap it into a "try/catch" to handle exceptions like this:

```php
try {
  $response  = $request->create($payment);
  $paymentId = $response->getId();
} catch(\Paymill\Services\PaymillException $e){
  //Do something with the error informations below
  $e->getResponseCode();
  $e->getStatusCode();
  $e->getErrorMessage();
  $e->getRawError();
}
```

Receiving Response
------------------

This section shows diffrent ways how to receive a response. The followings examples show how to get the Id for a transaction.

1.	The default response is one of the response-models.

```php
$response  = $request->create($payment);
$response->getId();
```

1.	getLastResponse() returns the unconverted response from the API.

```php
$request->create($payment);
$response = $request->getLastResponse();
$response['body']['data']['id'];
```

1.	getJSONObject returns the response as stdClass-Object.

```php
$request->create($payment);
$response = $request->getJSONObject();
$response->data->id;
```

Using Root certificate
----------------------

If the error below occurres on your system please follow the steps below to configure curl.

```php
Paymill\Services\PaymillException: SSL certificate problem, verify that the CA cert is OK. Details:
error:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed
```

Windows / OS X / Linux

1.	Download http://curl.haxx.se/ca/cacert.pem and save it on your server.
2.	Open php.ini with an editor and add the line `curl.cainfo=PathToYourCACertFile`
3.	Restart your Webserver

Update Root certificate on Linux(ubuntu)

1.	Run `sudo update-ca-certificates`
2.	Restart your Webserver

Changelog
---------

#### 3.2.1

-	bugfix: [#92](https://github.com/paymill/paymill-php/pull/92) remove typecheck for http response code

#### 4.0.0

- Added shipping and billing address
- Added shopping cart (items)
-	Added PayPal functionality
-	Possible [BC break in ResponseHandler.php](https://github.com/paymill/paymill-php/pull/102#discussion_r32232137)

Documentation
-------------

For further information, please refer to our official PHP library reference: https://developers.paymill.com/API/index
