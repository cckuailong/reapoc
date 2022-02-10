<?php
if (!defined('ABSPATH')) {
	exit;
}

class TINVWL_Trigger_Wishlist_Item_Purchased extends AutomateWoo\Trigger
{

	public $supplied_data_items = array('customer', 'product', 'wishlist');

	function load_admin_details()
	{
		$this->title = __('Product Purchased From Wishlist (TI WooCommerce Wishlist)', 'ti-woocommerce-wishlist');
		$this->group = __('Wishlists', 'ti-woocommerce-wishlist');
	}

	function load_fields()
	{
		$this->add_field_user_pause_period();
	}

	function register_hooks()
	{
		add_action('tinvwl_product_purchased', array($this, 'catch_hooks'), 10, 3);
	}

	/**
	 * Route hooks through here
	 *
	 * @param WC_order $order Order object.
	 * @param WC_Order_Item_Product $item Order item product object.
	 * @param array $wishlist_data A wishlist data where product added from.
	 */
	function catch_hooks($order, $item, $wishlist_data)
	{

		if (!$this->has_workflows()) {
			return;
		}

		$wishlist = new TINVWL_AutomateWoo_Wishlist();
		$wishlist->id = $wishlist_data['ID'];
		$wishlist->owner_id = $wishlist_data['author'];

		$this->maybe_run(array(
			'customer' => AutomateWoo\Customer_Factory::get_by_user_id($wishlist_data['author']),
			'wishlist' => $wishlist,
			'product' => wc_get_product($item->get_product_id()),
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
