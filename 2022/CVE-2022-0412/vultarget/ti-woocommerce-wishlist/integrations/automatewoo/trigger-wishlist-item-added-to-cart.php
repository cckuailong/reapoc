<?php
if (!defined('ABSPATH')) {
	exit;
}

class TINVWL_Trigger_Wishlist_Item_Added_To_Cart extends AutomateWoo\Trigger
{

	public $supplied_data_items = array('customer', 'product', 'wishlist');

	function load_admin_details()
	{
		$this->title = __('Customer Added Product From Wishlist To Cart (TI WooCommerce Wishlist)', 'ti-woocommerce-wishlist');
		$this->group = __('Wishlists', 'ti-woocommerce-wishlist');
	}

	function load_fields()
	{
		$this->add_field_user_pause_period();
	}

	function register_hooks()
	{
		add_action('tinvwl_product_added_to_cart', array($this, 'catch_hooks'), 10, 3);
	}

	/**
	 * Route hooks through here
	 *
	 * @param string $cart_item_key cart product unique key.
	 * @param integer $quantity Product quantity.
	 * @param array $product product data.
	 */
	function catch_hooks($cart_item_key, $quantity, $product)
	{

		if (!$this->has_workflows()) {
			return;
		}

		$wishlist = new TINVWL_AutomateWoo_Wishlist();
		$wishlist->id = $product['wishlist_id'];
		$wishlist->owner_id = $product['author'];

		$this->maybe_run(array(
			'customer' => AutomateWoo\Customer_Factory::get_by_user_id($product['author']),
			'wishlist' => $wishlist,
			'product' => wc_get_product($product['product_id']),
		));

	}

	/**
	 * @param $workflow Workflow
	 *
	 * @return bool
	 */
	function validate_workflow($workflow)
	{
		if (!$this->validate_field_user_pause_period($workflow)) {
			return false;
		}


		return true;
	}

	/**
	 * @param Workflow $workflow
	 *
	 * @return bool
	 */
	function validate_before_queued_event($workflow)
	{
		$product = $workflow->data_layer()->get_product();

		if (!$product) {
			return false;
		}

		return true;
	}
}
