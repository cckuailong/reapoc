<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name AutomateWoo
 *
 * @version 5.3.0
 *
 * @slug automatewoo
 *
 * @url https://automatewoo.com
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "automatewoo";

$name = "AutomateWoo";

$available = class_exists('AutomateWoo');

$tinvwl_integrations = is_array($tinvwl_integrations) ? $tinvwl_integrations : [];

$tinvwl_integrations[$slug] = array(
	'name' => $name,
	'available' => $available,
);

if (!tinv_get_option('integrations', $slug)) {
	return;
}

if (!$available) {
	return;
}

add_filter('automatewoo/triggers', 'tinvwl_automatewoo_triggers');

/**
 * @param array $triggers
 *
 * @return array
 */
function tinvwl_automatewoo_triggers($triggers)
{
	try {
		// AutomateWoo Wishlist class
		include_once 'automatewoo/wishlist.class.php';

		add_filter('automatewoo/preview_data_layer', 'tinvwl_automatewoo_preview', 10, 2);
		add_filter('automatewoo/variables', 'tinvwl_automatewoo_preview_variables');
		add_filter('automatewoo_validate_data_item', 'tinvwl_automatewoo_validate_data_item', 10, 3);

		// Trigger wishlist item added.
		include_once 'automatewoo/trigger-wishlist-item-added.php';
		$triggers['tinvwl_wishlist_item_added'] = 'TINVWL_Trigger_Wishlist_Item_Added';

		// Trigger wishlist reminder.
		include_once 'automatewoo/trigger-wishlist-reminder.php';
		$triggers['tinvwl_wishlist_reminder'] = 'TINVWL_Trigger_Wishlist_Reminder';

		// Trigger wishlist item added to cart.
		include_once 'automatewoo/trigger-wishlist-item-added-to-cart.php';
		$triggers['tinvwl_wishlist_item_added_to_cart'] = 'TINVWL_Trigger_Wishlist_Item_Added_To_Cart';

		// Trigger wishlist item purchased.
		include_once 'automatewoo/trigger-wishlist-item-purchased.php';
		$triggers['tinvwl_wishlist_item_purchased'] = 'TINVWL_Trigger_Wishlist_Item_Purchased';

		// Trigger wishlist item removed.
		include_once 'automatewoo/trigger-wishlist-item-removed.php';
		$triggers['tinvwl_wishlist_item_removed'] = 'TINVWL_Trigger_Wishlist_Item_Removed';
	} catch (Exception $e) {
		error_log(print_r($e->getMessage(), true));
	}
	return $triggers;
}

function tinvwl_automatewoo_preview($data_layer, $data_items)
{
	/**
	 * Wishlist
	 */
	if (in_array('wishlist', $data_items)) {
		$wishlist = new TINVWL_AutomateWoo_Wishlist();
		$wl = new TInvWL_Wishlist();
		$items = false;
		$current_wl = $wl->get_by_user_default();
		if ($current_wl && isset($current_wl[0]) && isset($current_wl[0]['ID'])) {
			$wishlist->id = $current_wl[0]['ID'];
			$wishlist->owner_id = $current_wl[0]['author'];
			$wishlist->date = DateTime::createFromFormat("Y-m-d H:i:s", $current_wl[0]['date']);
			$wishlist->get_items();
			if ($wishlist->items) {
				$items = true;
			}
		}

		if (!$items) {
			$product_query = new \WP_Query([
				'post_type' => 'product',
				'posts_per_page' => 4,
				'fields' => 'ids'
			]);
			$wishlist->items = $product_query->posts;
		}

		$data_layer['wishlist'] = $wishlist;
	}


	return $data_layer;
}


function tinvwl_automatewoo_preview_variables($variables)
{
	return $variables;
}

function tinvwl_automatewoo_validate_data_item($valid, $type, $item)
{
	if ('wishlist' === $type) {
		return true;
	}

	return $valid;
}
