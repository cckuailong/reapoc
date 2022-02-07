<?php

namespace Never5\DownloadMonitor\Shop\Cart;

class Cart {

	/** @var Item\Item[] */
	private $items;

	/** @var Coupon[] */
	private $coupons;

	/** @var int */
	private $subtotal;

	/** @var int */
	private $total;

	/** @var int */
	private $tax_total;

	/**
	 * @return Item\Item[]
	 */
	public function get_items() {
		if ( ! is_array( $this->items ) ) {
			$this->items = array();
		}

		return $this->items;
	}

	/**
	 * @param Item\Item[] $items
	 */
	public function set_items( $items ) {
		$this->items = $items;
	}

	/**
	 * @param Item\Item $item
	 */
	public function add_item( $item ) {
		$items   = $this->get_items();
		$items[] = $item;
		$this->set_items( $items );
	}

	/**
	 * @param Item\Item $item
	 *
	 * @return bool
	 */
	public function has_item( $item ) {
		$items = $this->get_items();
		if ( count( $items ) > 0 ) {
			/** @var Item\Item $item */
			foreach ( $items as $i ) {
				if ( $i->get_product_id() === $item->get_product_id() ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if there are items in the cart.
	 * Returns true if there are no items in the cart, false if there are items.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->items );
	}

	/**
	 * @return Coupon[]
	 */
	public function get_coupons() {
		return $this->coupons;
	}

	/**
	 * @param Coupon[] $coupons
	 */
	public function set_coupons( $coupons ) {
		$this->coupons = $coupons;
	}

	/**
	 * @param Coupon $coupon
	 */
	public function add_coupon( $coupon ) {
		if ( ! is_array( $this->coupons ) ) {
			$this->coupons = array();
		}
		$this->coupons[] = $coupon;
	}

	/**
	 * @return int
	 */
	public function get_subtotal() {
		return $this->subtotal;
	}

	/**
	 * @param int $subtotal
	 */
	public function set_subtotal( $subtotal ) {
		$this->subtotal = $subtotal;
	}

	/**
	 * @return int
	 */
	public function get_total() {
		return $this->total;
	}

	/**
	 * @param int $total
	 */
	public function set_total( $total ) {
		$this->total = $total;
	}

	/**
	 * @return int
	 */
	public function get_tax_total() {
		return $this->tax_total;
	}

	/**
	 * @param int $tax_total
	 */
	public function set_tax_total( $tax_total ) {
		$this->tax_total = $tax_total;
	}

}