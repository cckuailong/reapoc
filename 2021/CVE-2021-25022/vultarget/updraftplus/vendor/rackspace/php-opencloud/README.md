**php-opencloud**
=============
PHP SDK for OpenStack/Rackspace APIs

[![Latest Stable Version](https://poser.pugx.org/rackspace/php-opencloud/v/stable.png)](https://packagist.org/packages/rackspace/php-opencloud) [![Travis CI](https://secure.travis-ci.org/rackspace/php-opencloud.png)](https://travis-ci.org/rackspace/php-opencloud) [![Total Downloads](https://poser.pugx.org/rackspace/php-opencloud/downloads.png)](https://packagist.org/packages/rackspace/php-opencloud)

Our official documentation is now available on http://docs.php-opencloud.com. For SDKs in different languages, see http://developer.rackspace.com.

The PHP SDK should work with most OpenStack-based cloud deployments,
though it specifically targets the Rackspace public cloud. In
general, whenever a Rackspace deployment is substantially different
than a pure OpenStack one, a separate Rackspace subclass is provided
so that you can still use the SDK with a pure OpenStack instance
(for example, see the `OpenStack` class (for OpenStack) and the
`Rackspace` subclass).

Requirements
------------
* PHP >=5.4
* cURL extension for PHP

**Note**: Since PHP 5.3 has reached [end of life](http://php.net/eol.php) and is no longer officially supported, we are moving to 5.4 as a minimum requirement. If upgrading is not an option and you still need a stable version of the SDK for 5.3, please follow [this guide](http://docs.php-opencloud.com/en/latest/using-php-5.3.html).

Installation
------------
You must install this library through Composer:

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Require php-opencloud as a dependency
php composer.phar require rackspace/php-opencloud
```

Once you have installed the library, you will need to load Composer's autoloader (which registers all the required
namespaces). To do this, place the following line of PHP code at the top of your application's PHP files:

```php
require 'vendor/autoload.php';
```

**Note**: this assumes your application's PHP files are located in the same folder as `vendor/`. If your files are located
elsewhere, please supply the path to `vendor/autoload.php` in the `require` statement above.

And you're ready to go!

You can also check out the [Getting Started guide](docs/getting-started.md) for a quick tutorial.

- - -

Alternatively, if you would like to fork or clone the repository into a directory (to work and submit pull requests),
you will need to execute:

```bash
php composer.phar install
```

Instead of the `require` command. You can also specify the `--no-dev` option if you do not want to install phpDocumentor
(which has lots of vendor folders).

Support and Feedback
--------------------
Your feedback is appreciated! If you have specific problems or bugs with this SDK, please file an issue on Github. We
also have a [mailing list](https://groups.google.com/forum/#!forum/php-opencloud), so feel free to join to keep up to
date with all the latest changes and announcements to the library.

For general feedback and support requests, contact us at https://developer.rackspace.com/support/

You can also find assistance via IRC on #rackspace at freenode.net.

Contributing
------------
If you'd like to contribute to the project, or require help running the unit/acceptance tests, please view the
[contributing guidelines](https://github.com/rackspace/php-opencloud/blob/master/CONTRIBUTING.md).
