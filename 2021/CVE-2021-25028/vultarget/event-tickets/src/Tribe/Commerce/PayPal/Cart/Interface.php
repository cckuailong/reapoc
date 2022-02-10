<?php

/**
 * Interface Tribe__Tickets__Commerce__PayPal__Cart__Interface
 *
 * @since 4.7.3
 */
interface Tribe__Tickets__Commerce__PayPal__Cart__Interface {

	/**
	 * Sets the cart id.
	 *
	 * @since 4.7.3
	 *
	 * @param string $id
	 */
	public function set_id( $id );

	/**
	 * Gets the cart items from the cart.
	 *
	 * This method should include any persistence by the cart implementation.
	 *
	 * @since 4.11.0
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
	 * @since 4.7.3
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
	 * @since 4.7.3
	 *
	 * @param array $criteria
	 */
	public function exists( array $criteria = [] );

	/**
	 * Whether the cart contains items or not.
	 *
	 * @since 4.7.3
	 *
	 * @return bool|int The number of products in the cart (regardless of the products quantity) or `false`
	 *
	 */
	public function has_items();

	/**
	 * Whether an item is in the cart or not.
	 *
	 * @param string $item_id
	 *
	 * @return bool|int Either the quantity in the cart for the item or `false`.
	 */
	public function has_item( $item_id );

	/**
	 * Adds a specified quantity of the item to the cart.
	 *
	 * @since 4.7.3
	 *
	 * @param int|string $item_id   The item ID.
	 * @param int        $quantity  The quantity to remove.
	 * @param array      $extra_dat Extra data to save to the item.
	 */
	public function add_item( $item_id, $quantity, array $extra_data = [] );

	/**
	 * Removes an item from the cart.
	 *
	 * @since 4.7.3
	 * @since 4.11.0 Added null default for $quantity
	 *
	 * @param int|string $item_id  The item ID.
	 * @param null|int   $quantity The quantity to remove.
	 */
	public function remove_item( $item_id, $quantity = null );
}
