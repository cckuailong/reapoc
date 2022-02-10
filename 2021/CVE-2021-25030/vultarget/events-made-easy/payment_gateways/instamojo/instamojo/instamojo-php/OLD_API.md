# Instamojo Old PHP API

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

### Create a product

```php
try {
    $response = $api->linkCreate(array(
        'title'=>'Hello API',
        'description'=>'Create a new product easily',
        'base_price'=>100,
        'cover_image'=>'/path/to/photo.jpg'
        ));
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

This will give you JSON object containing details of the product that was just created.

### Edit a product

```php
try {
    $response = $api->linkEdit(
        'hello-api', // You must specify the slug of the product
        array(
        'title'=>'A New Title',
        ));
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

### List all products

```php
try {
    $response = $api->linksList();
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

### List all Payments

```php
try {
    $response = $api->paymentsList();
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

### Get Details of a Payment using Payment ID

```php
try {
    $response = $api->paymentDetail('[PAYMENT ID]');
    print_r($response);
}
catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
```

## Available Functions

You have these functions to interact with the API:

  * `linksList()` List all products created by authenticated User.
  * `linkDetail($slug)` Get details of product specified by its unique slug.
  * `linkCreate(array $link)` Create a new product.
  * `linkEdit($slug, array $link)` Edit an existing product.
  * `linkDelete($slug)` Archive a product - Archived producrs cannot be generally accessed by the API. User can still view them on the Dashboard at instamojo.com.
  *  `paymentsList()` List all Payments linked to User's account.
  * `paymentDetail($payment_id)` Get details of a Payment specified by its unique Payment ID. You may receive the Payment ID via `paymentsList()` or via URL Redirect function or as a part of Webhook data.

## Product Creation Parameters

### Required

  * `title` - Title of the product, be concise.
  * `description` - Describe what your customers will get, you can add terms and conditions and any other relevant information here. Markdown is supported, popular media URLs like Youtube, Flickr are auto-embedded.
  * `base_price` - Price of the product. This may be 0, if you want to offer it for free. 

### File and Cover Image
  * `file_upload` - Full path to the file you want to sell. This file will be available only after successful payment.
  * `cover_image` - Full path to the IMAGE you want to upload as a cover image.

### Quantity
  * `quantity` - Set to 0 for unlimited sales. If you set it to say 10, a total of 10 sales will be allowed after which the product will be made unavailable.

### Post Purchase Note
  * `note` - A post-purchase note, will only displayed after successful payment. Will also be included in the ticket/ receipt that is sent as attachment to the email sent to buyer. This will not be shown if the payment fails.

### Event
  * `start_date` - Date-time when the event is beginning. Format: `YYYY-MM-DD HH:mm`
  * `end_date` - Date-time when the event is ending. Format: `YYYY-MM-DD HH:mm`
  * `venue` - Address of the place where the event will be held.
  * `timezone` - Timezone of the venue. Example: Asia/Kolkata

### Redirects and Webhooks
  * `redirect_url` - This can be a Thank-You page on your website. Buyers will be redirected to this page after successful payment.
  * `webhook_url` - Set this to a URL that can accept POST requests made by Instamojo server after successful payment.
  * `enable_pwyw` - set this to True, if you want to enable Pay What You Want. Default is False.
  * `enable_sign` - set this to True, if you want to enable Link Signing. Default is False. For more information regarding this, and to avail this feature write to support at instamojo.com.

---

## [Request a Payment](RAP.md)

---

## [Refunds](REFUNDS.md)

---

Further documentation is available at https://www.instamojo.com/developers/