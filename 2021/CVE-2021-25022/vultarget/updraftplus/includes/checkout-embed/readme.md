# Embed the plugin's checkout page

## To use in a new plugin:

- Include and instanciate `Updraft_Checkout_Embed`

```php
if (!class_exists('Updraft_Checkout_Embed')) include_once (UPDRAFTPLUS_DIR.'/includes/checkout-embed/class-udp-checkout-embed.php');
global $udp_checkout_embed;
$udp_checkout_embed = new Updraft_Checkout_Embed(
	'updraftplus'
	$data_url, 
	$load_in_pages
);
```

### Params:
- $plugin_name: (string) Current plugin using the class
- $proructs_data_url: (string) url of the merchand website (eg: https://https://updraftplus.com)
- $load_in_pages: (array) pages on which the script + css will be loaded

### Cache:
The products data is cached and expires after 7 days. To force fetching it, add `udp-force-product-list-refresh=1` to the admin page url

## Using in the admin

- Once the php is setup, you can configure the links / buttons in the admin.

Add `data-embed-checkout="{$url}"` to any link. eg: 

```php
global $updraftplus_checkout_embed;

$link_data_attr = $updraftplus_checkout_embed->get_product('updraftpremium') ? 'data-embed-checkout="'.apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftpremium')).'"' : '';

<a target="_blank" title="Upgrade to Updraft Premium" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/shop/updraftplus-premium/");?>" <?php echo $link_data_attr; ?>><?php _e('get it here', 'updraftplus');?></a>
```

- On completion (when the order is complete), the event 'udp/checkout/done' is triggered. 
- The event 'udp/checkout/close' is triggered when the user closes the modal, regardless of success.

Use this to do something with the data received:

```javascript
$(document).on('udp/checkout/done', function(event, data, $element) {
	// ... do something with data, currently data.email and data.order_number
	// $element clicked to open the modal.
});
```
