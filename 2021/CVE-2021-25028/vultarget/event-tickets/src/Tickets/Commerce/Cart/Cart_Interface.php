<?php

namespace TEC\Tickets\Commerce\Cart;

/**
 * Interface Cart_Interface
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Cart
 */
interface Cart_Interface {

	/**
	 * Sets the cart hash.
	 *
	 * @since 5.1.9
	 * @since 5.2.0 Renamed to set_hash instead of set_id
	 *
	 * @param string $hash
	 */
	public function set_hash( $hash );

	/**
	 * Gets the cart hash.
	 *
	 * @since 5.2.0
	 *
	 * @return string
	 */
	public function get_hash();

	/**
	 * Gets the Cart mode based.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_mode();

	/**
	 * Gets the cart items from the cart.
	 *
	 * This method should include any persistence by the cart implementation.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_items();

	/**
	 * Saves the cart.
	 *
	 * This method should include any persistence, request and redirection required
	 * by the cart implementation.
	 *
	 * @since 5.1.9
	 */
	public function save();

	/**
	 * Clears the cart of its contents and persists its new state.
	 *
	 * This method should include any persistence, request and redirection required
	 * by the cart implementation.
	 */
	public function clear();

	/**
	 * Whether a cart exists meeting the specified criteria.
	 *
	 * @since 5.1.9
	 *
	 * @param array $criteria
	 */
	public function exists( array $criteria = [] );

	/**
	 * Whether the cart contains items or not.
	 *
	 * @since 5.1.9
	 *
	 * @return bool|int The number of products in the cart (regardless of the products quantity) or `false`
	 *
	 */
	public function has_items();

	/**
	 * Whether an item is in the cart or not.
	 *
	 * @since 5.1.9
	 *
	 * @param string $item_id
	 *
	 * @return bool|int Either the quantity in the cart for the item or `false`.
	 */
	public function has_item( $item_id );

	/**
	 * Adds a specified quantity of the item to the cart.
	 *
	 * @since 5.1.9
	 *
	 * @param int|string $item_id    The item ID.
	 * @param int        $quantity   The quantity to remove.
	 * @param array      $extra_data Extra data to save to the item.
	 */
	public function add_item( $item_id, $quantity, array $extra_data = [] );

	/**
	 * Determines if this instance of the cart has a public page.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function has_public_page();

	/**
	 * Removes an item from the cart.
	 *
	 * @since 5.1.9
	 *
	 * @param int|string $item_id  The item ID.
	 * @param null|int   $quantity The quantity to remove.
	 */
	public function remove_item( $item_id, $quantity = null );

	/**
	 * Removes an item from the cart.
	 *
	 * @since 5.1.10
	 *
	 * @param array $data to be processed by the cart.
	 *
	 * @return array
	 */
	public function process( array $data = [] );

	/**
	 * Prepare the data for cart processing.
	 *
	 * @since 5.1.10
	 *
	 * @param array $data To be processed by the cart.
	 *
	 * @return array
	 */
	public function prepare_data( array $data = [] );
}
