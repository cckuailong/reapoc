<?php

namespace Never5\DownloadMonitor\Shop\Session;

class Session {

	/** @var string */
	private $key;

	/** @var string */
	private $hash;

	/** @var \DateTimeImmutable */
	private $expiry;

	/** @var string[] */
	private $coupons;

	/** @var Item\Item[] */
	private $items;

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function set_key( $key ) {
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function get_hash() {
		return $this->hash;
	}

	/**
	 * @param string $hash
	 */
	public function set_hash( $hash ) {
		$this->hash = $hash;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function get_expiry() {
		return $this->expiry;
	}

	/**
	 * @param \DateTimeImmutable $expiry
	 */
	public function set_expiry( $expiry ) {
		$this->expiry = $expiry;
	}

	/**
	 * Reset the expiry date
	 *
	 * @return bool
	 */
	public function reset_expiry() {
		try {
			$expiry = new \DateTimeImmutable(current_time( 'mysql' ));
			$expiry = $expiry->modify( '+' . apply_filters( 'dlm_shop_session_expiry_days', 7 ) . 'days' );
			$this->set_expiry( $expiry );
			return true;
		} catch ( \Exception $e ) {

		}
		return false;
	}

	/**
	 * @return string[]
	 */
	public function get_coupons() {
		return $this->coupons;
	}

	/**
	 * @param string[] $coupons
	 */
	public function set_coupons( $coupons ) {
		$this->coupons = $coupons;
	}

	/**
	 * @param string $code
	 */
	public function add_coupon( $code ) {

		if ( ! is_array( $this->coupons ) ) {
			$this->coupons = array();
		}

		// add if not added already
		if ( ! in_array( $code, $this->coupons ) ) {
			$this->coupons[] = $code;
		}

	}

	/**
	 * @param string $code
	 */
	public function remove_coupon( $code ) {
		if ( in_array( $code, $this->coupons ) ) {
			$key = array_search( $code, $this->coupons );
			if ( false !== $key ) {
				unset( $this->coupons[ $key ] );
			}
		}
	}

	/**
	 * @return Item\Item[]
	 */
	public function get_items() {
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

		if ( ! is_array( $this->items ) ) {
			$this->items = array();
		}

		$this->items[ $item->get_key() ] = $item;
	}

	/**
	 * @param string $key
	 */
	public function remove_item( $key ) {
		if ( isset( $this->items[ $key ] ) ) {
			unset( $this->items[ $key ] );
		}
	}

}