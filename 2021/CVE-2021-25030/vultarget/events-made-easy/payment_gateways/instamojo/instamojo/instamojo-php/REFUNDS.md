## Refunds

**Note**: If you're using this wrapper with our sandbox environment `https://test.instamojo.com/` then you should pass `'https://test.instamojo.com/api/1.1/'` as third argument to the `Instamojo` class while initializing it. API key and Auth token for the same can be obtained from https://test.instamojo.com/developers/ (Details: [Test Or Sandbox Account](https://instamojo.zendesk.com/hc/en-us/articles/208485675-Test-or-Sandbox-Account)).


```php
$api = new Instamojo\Instamojo(API_KEY, AUTH_TOKEN, 'https://test.instamojo.com/api/1.1/');
```


## Installing via [Composer](https://getcomposer.org/)
```bash
$ php composer.phar require instamojo/instamojo-php
```

**Note**: If you're not using Composer then directly include the contents of `src` directory in your project.


## Usage

```php
$api = new Instamojo\Instamojo(API_KEY, AUTH_TOKEN);
```

### Create a new Refund

```php
try {
    $response = $api->refundCreate(array(
        'payment_id'=>'MOJO5c04000J30502939',
        'type'=>'QFL',
        'body'=>'Customer is not satified.'
        ));
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

This will give you JSON object containing details of the Refund that was just created.


### Get the details of a Refund

```php
try {
    $response = $api->refundDetail('[REFUND ID]');
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

This will give you JSON object containing details of the Refund.

Here `['REFUND ID']` is the value of `'id'` key returned by the `refundCreate()` query.


### Get a list of all Refunds

```php
try {
    $response = $api->refundsList();
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

This will give you an array containing Refunds created so far.

## Available Refund Functions

You have these functions to interact with the Refund API:

  * `refundCreate(array $refund)` Create a new Refund.
  * `refundDetail($id)` Get details of Refund specified by its unique id.
  * `refundsList()` Get a list of all Refunds.

## Refund Creation Parameters

### Required
  * `payment_id`: Payment ID for which Refund is being requested.
  * `type`: A three letter short-code to identify the type of the refund. Check the
            REST docs for more info on the allowed values.
  * `body`: Additional explanation related to why this refund is being requested.

### Optional
  * `refund_amount`: This field can be used to specify the refund amount. For instance, you
            may want to issue a refund for an amount lesser than what was paid. If
            this field is not provided then the total transaction amount is going to
            be used.

Further documentation is available at https://docs.instamojo.com/v1.1/docs
