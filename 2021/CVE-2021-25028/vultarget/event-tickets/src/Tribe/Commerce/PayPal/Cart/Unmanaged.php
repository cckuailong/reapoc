<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Cart__Unmanaged
 *
 * Models a transitional, not managed, cart implementation; cart management functionality
 * is offloaded to PayPal.
 *
 * @since 4.7.3
 */
class Tribe__Tickets__Commerce__PayPal__Cart__Unmanaged implements Tribe__Tickets__Commerce__PayPal__Cart__Interface {

	/**
	 * @var string The invoice number for this cart.
	 */
	protected $invoice_number;

	/**
	 * @var array|null The list of items, null if not retrieved from transient yet.
	 */
	protected $items = null;

	/**
	 * {@inheritdoc}
	 */
	public function set_id( $id ) {
		$this->invoice_number = $id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save() {
		if ( empty( $this->invoice_number ) ) {
			return;
		}

		if ( ! $this->has_items() ) {
			$this->clear();

			return;
		}

		set_transient( self::get_transient_name( $this->invoice_number ), $this->items, DAY_IN_SECONDS );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_items() {
		if ( null !== $this->items ) {
			return $this->items;
		}

		if ( ! $this->exists() ) {
			return false;
		}

		$this->items = [];

		$invoice_number = $this->read_invoice_number();

		$items = get_transient( self::get_transient_name( $invoice_number ) );

		if ( is_array( $items ) && ! empty( $items ) ) {
			$this->items = $items;
		}

		return $this->items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear() {
		$invoice_number = $this->invoice_number;

		if ( null === $invoice_number ) {
			if ( ! $this->exists() ) {
				return;
			}

			$invoice_number = $this->read_invoice_number();
		}

		delete_transient( self::get_transient_name( $this->invoice_number ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists( array $criteria = [] ) {
		if ( null !== $this->invoice_number ) {
			$invoice_number = $this->invoice_number;
		} else {
			$invoice_number = $this->read_invoice_number();
		}

		if ( false === $invoice_number ) {
			return false;
		}

		return (bool) get_transient( self::get_transient_name( $invoice_number ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_items() {
		$items = $this->get_items();

		return count( $items );
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_item( $item_id ) {
		$items = $this->get_items();

		return ! empty( $items[ $item_id ] ) ? (int) $items[ $item_id ]['quantity'] : false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_item( $item_id, $quantity, array $extra_data = [] ) {
		$current_quantity = $this->has_item( $item_id );

		$new_quantity = (int) $quantity;

		if ( 0 < $current_quantity ) {
			$new_quantity += (int) $current_quantity;
		}

		$new_quantity = max( $new_quantity, 0 );

		if ( 0 < $new_quantity ) {
			$item = $extra_data;

			$item['quantity'] = $new_quantity;

			$this->items[ $item_id ] = $item;
		} else {
			$this->remove_item( $item_id );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove_item( $item_id, $quantity = null ) {
		if ( null !== $quantity ) {
			$this->add_item( $item_id, - abs( (int) $quantity ) );

			return;
		}

		if ( $this->has_item( $item_id ) ) {
			unset( $this->items[ $item_id ] );
		}
	}

	/**
	 * Reads the invoice number from the invoice cookie.
	 *
	 * @since 4.7.3
	 *
	 * @return string|bool The invoice number or `false` if not found.
	 *
	 * @see   Tribe__Tickets__Commerce__PayPal__Gateway::set_invoice_number()
	 */
	public function read_invoice_number() {
		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		return $gateway->get_invoice_number( false );
	}

	/**
	 * Returns the name of the transient used by the cart.
	 *
	 * @since 4.7.3
	 *
	 * @param string $invoice_number
	 *
	 * @return string
	 */
	public static function get_transient_name( $invoice_number ) {
		return 'tpp_cart_' . md5( $invoice_number );
	}
}
