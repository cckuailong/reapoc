<?php

namespace Never5\DownloadMonitor\Shop\Session\Item;

class Item {

	/** @var string */
	private $key;

	/** @var int */
	private $product_id;

	/** @var int */
	private $qty;

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
	 * @return int
	 */
	public function get_product_id() {
		return $this->product_id;
	}

	/**
	 * @param int $product_id
	 */
	public function set_product_id( $product_id ) {
		$this->product_id = $product_id;
	}

	/**
	 * @return int
	 */
	public function get_qty() {
		return $this->qty;
	}

	/**
	 * @param int $qty
	 */
	public function set_qty( $qty ) {
		$this->qty = $qty;
	}

	/**
	 * We're building these manually because implementing JsonSerializable is only available from PHP5.4+
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'key'        => $this->get_key(),
			'product_id' => $this->get_product_id(),
			'qty'        => $this->get_qty()
		);
	}
}