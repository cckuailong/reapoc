[![Latest Stable Version](https://poser.pugx.org/underdev/utils/v/stable)](https://packagist.org/packages/underdev/utils) [![Total Downloads](https://poser.pugx.org/underdev/utils/downloads)](https://packagist.org/packages/underdev/utils) [![Latest Unstable Version](https://poser.pugx.org/underdev/utils/v/unstable)](https://packagist.org/packages/underdev/utils)

WordPress utilities classes for plugin development

# Usage example

Include the Composer's autoloader first.

```php
require_once( 'vendor/autoload.php' );
```

## Singleton

```php
use underDEV\Utils\Singleton;

class Example extends Singleton {}

Example::get();
```

## AJAX

Helper for AJAX requests.

```php
use underDEV\Utils\Ajax;

function ajax_callback() {

	$ajax = new Ajax();

	// verify nonce
	// you can pass the $_REQUEST array key for nonce as the second argument
	$ajax->verify_nonce( 'key_for_nonce' );

	// do stuff
	// ...

	// send output
	// if errors array will not be empty, it's considered as an error respose
	$ajax->response( $success = 'success message', $errors = array() );

}
```

## Files

Helper for plugin's files.

```php
use underDEV\Utils\Files;

// argument should be the main plugin file
$files = new Files( __FILE__ );

// get asset url
// will return: your-plugin/assets/dist/css/style.css
$files->asset_url( 'css', 'style.css' )

// get vendor asset url
// will return: your-plugin/assets/vendor/vendor_name/asset.css
$files->vendor_asset_url( 'vendor_name', 'asset.css' )
```

For all methods please check the class source.

## View

Helper for loading views. Uses the Files class.

```php
use underDEV\Utils\Files;
use underDEV\Utils\View;

// argument should be the main plugin file
$files = new Files( __FILE__ );
$view  = new View( $files );

// set some view var
$view->set_var( 'var_name', 'value' );

// load view
// this will load ./views/parts/menu.php
$view->get_view( 'parts/menu' );
```

In template file you can get vars

```html
<div><?php echo $this->get_var( 'var_name' ); ?></div>
```

To have different scopes in templates you have to instantinate different classes.

## Cache

Interface for cache. Has two implementations:
* Object Cache - if WordPress cache is not set it will not persist
* Transient Cache

### Basic usage

```php
use underDEV\Utils\Cache\ObjectCache;
use underDEV\Utils\Cache\Transient;

// create new cache object giving it a key and group
$cached_object = new ObjectCache( 'object_key', 'object_group' );
var_dump( $cached_object->get() ); // inspect cached value

// create new transient cache giving it a key and expiration in seconds
$transient_cache = new Transient( 'transient_key', 3600 );
var_dump( $transient_cache->get() ); // inspect cached value
```

### Injecting cached element into a class

```php
use underDEV\Utils\Interfaces\Cacheable;
use underDEV\Utils\Cache\ObjectCache;
use underDEV\Utils\Cache\Transient;

class MyClass {

	/**
	 * Cached object
	 * @var mixed
	 */
	protected $cached_element;

	/**
	 * Constructor
	 * @param Cacheable $cached_element
	 */
	public function __construct( Cacheable $cached_element ) {
		$this->cached_element = $cached_element;
	}

	public function inspect_element() {
		var_dump( $this->cached_element->get() );
	}

}

$myclass = new MyClass( new ObjectCache( 'object_key', 'object_group' ) );
$myclass->inspect_element(); // dumps object cached variable

// you can substitute MyClass constructor argument with
// any object of class that implements Cacheable
$myclass = new MyClass( new Transient( 'transient_key', 3600 ) );
$myclass->inspect_element(); // dumps cached transient variable
```

See Cacheable interface for all available methods.

## Dice

Dependency injection container. Forked from Tom Butler's Dice lib. Compatible with PHP 5.4

[Dice usage](https://r.je/dice.html)
