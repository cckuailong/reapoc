# Table Of Contents
<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [MAILCHIMP API 3.0 PHP](#mailchimp-api-3.0-php)
  - [Installation](#installation)
  - [Instantiation](#instantiation)
  - [Oauth](#oauth)
  - [Constructing a Request](#constructing-a-request)
    - [GET](#get)
    - [POST](#post)
    - [PATCH &amp; PUT](#patch-put-heading)
    - [DELETE](#delete)
  - [Handling A Response](https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Handling-A-Response)
  - [Callbacks](https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Callbacks)
  - [Method Chart (\*excluding verbs)](#method-chart-heading)
  - [Wiki](https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki) - See for more in depth documentation

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# MAILCHIMP API 3.0 PHP 
[![GitHub license](https://img.shields.io/github/license/Jhut89/Mailchimp-API-3.0-PHP.svg)](https://github.com/Jhut89/Mailchimp-API-3.0-PHP/blob/master/LICENSE) 
[![Build Status](https://travis-ci.com/Jhut89/Mailchimp-API-3.0-PHP.svg?branch=master)](https://travis-ci.com/Jhut89/Mailchimp-API-3.0-PHP)
[![Latest Stable Version](https://poser.pugx.org/jhut89/mailchimp3php/v/stable?format=flat)](https://packagist.org/packages/jhut89/mailchimp3php)
[![Total Downloads](https://poser.pugx.org/jhut89/mailchimp3php/downloads?format=flat)](https://packagist.org/packages/jhut89/mailchimp3php) 
[![Slack Workspace](https://img.shields.io/badge/slack%20workspace-join-blue.svg?style=flat)](https://join.slack.com/t/mailchimp-api-30-php/shared_invite/enQtMzkwNTg3Mzc5NzI5LTdhOWE2ZGE1NzJmZGRjNDg2Mzg1MDYwZmVhNGM0MGJmMDY3NGVkOWQyZTI1Zjg1YTA2YjdkMDMxNjIyZjg5MGM)
[![Trello Board](https://img.shields.io/badge/trello-view%20board-blue.svg?style=flat)](https://trello.com/b/N5DRSTgL/mailchimp-api-30-sdk)


This is a PHP library for interacting with [version 3.0 of MailChimp's API](https://developer.mailchimp.com)

This library assumes a basic understanding of the MailChimp application and its associated functions. 

## Installation

For Composer run:

```php
composer require jhut89/mailchimp3php
```

Alternatively you may add a require line to your projects `composer.json` for the package `jhut89/mailchimp3php`.

Then run `composer update` and add the composer autoloader to your project with:

```php
require "vendor/autoload.php";
```

You can then use a use statement to pull in the Mailchimp class:
```php
use MailchimpAPI\Mailchimp;
```
## Instantiation

```php
$mailchimp = new Mailchimp('123abc123abc123abc123abc-us0');
```

To instantiate you will need a new instance of the `Mailchimp` class with your MailChimp account's API key as its only argument.

## Oauth

If you are using [Oauth](http://developer.mailchimp.com/documentation/mailchimp/guides/how-to-use-oauth2/) to obtain an access token, this library can handle the "handshake" for you.
 
You must first send the user to your applications `authorize_uri`. You can get this url by calling the `Mailchimp::getAuthUrl()` statically:

```php 
$client_id =   '12345676543';
$redirect_url =  'https://www.some-domain.com/callback_file.php';

Mailchimp::getAuthUrl($client_id, $redirect_url);
```

From there the user will input their username and password to approve your application and will be redirected to the `redirect_uri` you set along with a `code`.

With that `code` you can now request an access token from mailchimp. For this you will need to call the `Mailchimp::oauthExchange()` method statically like this:

```php
$code = 'abc123abc123abc123abc123';
$client_id =   '12345676543';
$client_secret =  '789xyz789xyz789xyz789xyz';
$redirect_url =  'https://www.some-domain.com/callback_file.php';

Mailchimp::oauthExchange($code, $client_id, $client_secret, $redirect_url);
```

If the handshake is successful, then this method will return a string containing your API key like this: `123abc123abc123abc123abc123abc-us0`. This API key can now be used to instantiate your `Mailchimp` class as we have above.


## Constructing a Request

Once you have instantiated the `Mailchimp` class you can start constructing requests. Constructing requests is done by 'chaining' methods to the `$mailchimp` instance. In most cases this 'chain' will end with the HTTP verb for your request.

### GET

An Example of how to retrieve a list collection:
```php
$mailchimp
    ->lists()
    ->get();
```

Retrieving an instance can be accomplished by giving a unique identifier for the instance you want as an argument to the appropriate method. For example if I wanted to retrieve a list instance from the above example I would simply pass a `list_id`, as the only argument for the `lists()` method. Like this:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->get();
```

Methods available for each position in the chain depend on what the prior method returns. For example if I wanted to retrieve subscribers from a list in my account I would:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members()
    ->get();
```

Notice that I provided a `list_id` to the `lists()` method, as there would be no way to retrieve a list of subscribers from a lists collection. The above request however will only return 10 subscriber instances from the members collection. This is because MailChimp's API uses pagination (documented [HERE](http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#parameters)) that defaults to `count=10` and `offset=0`. This library allows you to alter query string parameters by passing them as an argument to the `GET()` method. We do this by providing an array of key-value pairs where the keys are the query parameter you wish to provide/alter and its value is the parameter's value. As an example if I wanted to retrieve the second 100 subscribers from my list I could:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members()
    ->get([
        "count" => "100", 
        "offset" => "100"
    ]);
```

This would be equivalent to making a get request against:

```
Https://us0.api.mailchimp.com/3.0/lists/1a2b3c4d/members?count=100&offset=100
```

Going a little further we can retrieve a single list member by giving the `members_hash` (md5 hash of lower-case address) to the `members()` method. Like this:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members('8bdbf060209f35b52087992a3cbdf4d7')
    ->get();
```

Alternatively, in place of providing an md5 hash as the identifier to the `members()` function you can provide a contact's email address as a string and this library will do the hashing for you. Like this:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members('example@domain.com')
    ->get();
```

> You can read about `GET` requests in depth here: https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Get-Requests
### POST

While being able to retrieve data from your account is great we also need to be able to post new data. This can be done by calling the `POST()` method at the end of a chain. As an example subscribing an address to a list would look like this:

```php
$post_params = [
    'email_address'=>'example@domain.com', 
    'status'=>'subscribed'
];

$mailchimp
    ->lists('1a2b3c4d')
    ->members()
    ->post($post_params);
```

In this case I would not provide `members()` with an identifier as I want to post to its collection. Also notice that the post data is an array of key-value pairs representing what parameters I want to pass to the MailChimp API. Be sure that you provide all required fields for the endpoint you are posting to. Check [MailChimp's documentation](http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#create-post_lists_list_id_members) for what parameters are required. Non-required parameters can just be added to the post data, and MailChimp will ignore any that are unusable. To illustrate here is an example of adding a subscriber to a list with some non-required parameters:

```php
$merge_values = [
    "FNAME" => "John",
    "LNAME" => "Doe"
];

$post_params = [
    "email_address" => "example@domain.com", 
    "status" => "subscribed", 
    "email_type" => "html", 
    "merge_fields" => $merge_values
]

$mailchimp
    ->lists('1a2b3c4d')
    ->members()
    ->post($post_params);
```
> You can read about `POST` requests in depth here: https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Post-Requests

### <a name="patch-put-heading"></a> PATCH &amp; PUT

This library handles PUT and PATCH request similar to that of POST requests. Meaning that `PUT()` & `PATCH()` both accept an array of key-value pairs that represent the data you wish altered/provided to MailChimp. As an example if I was patching the subscriber that we subscribed above, to have a new first name, that would look like this.

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members('a1167f5be2df7113beb69c95ebcdb2fd')
    ->patch([
        "merge_fields" => ["FNAME" => "Jane"]
    ]);
```
> You can read about `PATCH` & `PUT` requests in depth here: https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Patch-&-Put-Requests

### DELETE

Deleting a record from MailChimp is performed with the `DELETE()` method and is constructed similar to GET requests. If I wanted to delete the above subscriber I would:

```php
$mailchimp
    ->lists('1a2b3c4d')
    ->members('a1167f5be2df7113beb69c95ebcdb2fd')
    ->delete();
```
> You can read about `DELETE` requests in depth here: https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Delete-Requests
## Handling A Response

Methods named for http verbs such as `get()` ,`post()`, `patch()`, `put()`, or `delete()` kick off an over the wire request to MailChimp's A.P.I. Given a successful request these methods return an instance of a `MailchimpResponse`.
 I suggest you become familiar with this class as there are a number of modifiers and getters for different pieces of a response.

There are a number of getters we can use to interact with pieces our `MailchimpResponse` instance. Some of the more commonly used ones are:

```php
$response->deserialize(); // returns a deserialized (to php object) resource returned by API
$response->getHttpCode(); // returns an integer representation of the HTTP response code
$response->getHeaders(); // returns response headers as an array of key => value pairs
$response->getBody(); // return the raw text body of the response
```

As an example, if I posses an API key but want the contact email associated with its account:

```php
$mailchimp = new Mailchimp('123abc123abc123abc123abc-us0');
$account = $mailchimp
    ->account()
    ->get()
    
$contact_email = $account
    ->deserialize()
    ->email
    
print $contact_email; // outputs something like "example@domain.com"
```

> You can read about how to work with responses in depth here: https://github.com/Jhut89/Mailchimp-API-3.0-PHP/wiki/Handling-A-Response

## <a name="method-chart-heading"></a>Method Chart (\*excluding verbs)

                                    
      mailchimp()                       
      |                                 
      |----account()                    
      |                                 
      |----apps()                       
      |                                 
      |----automations()                
      |    |                            
      |    |----removedSubscribers()    
      |    |----emails()                
      |         |                       
      |         |---queue()*             
      |         |---pauseAll()*         
      |         |---startAll()*        
      |                                 
      |----batches()                    
      |                                 
      |----batchWebhooks()              
      |                                 
      |----campaignFolders()            
      |                                 
      |----campaigns()                  
      |    |                            
      |    |----cancel()*                
      |    |----pause()*                 
      |    |----replicate()*             
      |    |----resume()*                
      |    |----scedule()*              
      |    |----send()*                  
      |    |----test()*                  
      |    |----unschedule()*            
      |    |----checklist()             
      |    |----feedback()              
      |    |----content()
      |
      |----connectedSites()
      |    |
      |    |----verifyScriptInstallation()*               
      |                                 
      |----conversations()              
      |    |                            
      |    |----messages()              
      |                                 
      |----ecommStores()                
      |    |                            
      |    |----customers()             
      |    |----products()              
      |    |    |                       
      |    |    |----variants()         
      |    |    |----images()           
      |    |     
      |    |----promoRules()
      |    |    |
      |    |    |----promoCodes()
      |    |                   
      |    |----orders()                
      |    |    |                       
      |    |    |----lines()            
      |    |                            
      |    |----carts()                 
      |         |                       
      |         |----lines()
      |
      |----facebookAds()            
      |                                 
      |----fileManagerFiles()           
      |                                 
      |----fileManagerFolders()
      |
      |----googleAds()
      |
      |----landingPages()
      |    |
      |    |----publish()*
      |    |----unpublish()*
      |    |----content()         
      |                                 
      |----lists()                      
      |    |                            
      |    |----batchSubscribe()*             
      |    |----webhooks()              
      |    |----signupForms()           
      |    |----mergeFields()           
      |    |----growthHistory()         
      |    |----clients()               
      |    |----activity()              
      |    |----abuseReports()          
      |    |----segments()              
      |    |    |                       
      |    |    |----batch()*            
      |    |    |----members()          
      |    |                            
      |    |----members()               
      |    |    |                       
      |    |    |---notes()             
      |    |    |---goals()             
      |    |    |---activity()
      |    |    |---tags()          
      |    |                            
      |    |----interestCategories()    
      |         |                       
      |         |----interests()        
      |    
      |----ping()
      |                             
      |----reports()                    
      |    |                            
      |    |----unsubscribes()          
      |    |----subReports()            
      |    |----sentTo()                
      |    |----locations()             
      |    |----emailActivity() 
      |    |----googleAnalytics()
      |    |----openDetails()        
      |    |----eepurl()                
      |    |----domainPerformance()     
      |    |----advice()                
      |    |----abuse()                 
      |    |----clickReports()          
      |         |                       
      |         |----members()          
      |                                 
      |----searchCampaigns()            
      |                                 
      |----searchMembers()              
      |                                 
      |----templateFolders()            
      |                                 
      |----templates()                  
      |    |                            
      |    |----defaultContent()        
      |                             
      |----verifiedDomains()
           |
           |----verify()                            
                                    
                                    

\*Please see [MailChimp's API Documentation](http://developer.mailchimp.com/documentation/mailchimp/reference/overview/) for what verbs are appropriate where.

\** Methods marked with a `*` make a network request 

\*\*Please watch for updates, and feel free to Fork or Pull Request. Check out the Wiki for a little more info on contributing.




