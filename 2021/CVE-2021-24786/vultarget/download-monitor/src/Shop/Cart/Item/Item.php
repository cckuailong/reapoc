<?php

namespace Never5\DownloadMonitor\Shop\Cart\Item;

class Item {

	/** @var int */
	private $product_id;

	/** @var string */
	private $label;

	/** @var int */
	private $qty;

	/** @var int */
	private $tax_total;

	/** @var int */
	private $subtotal;

	/** @var int */
	private $total;

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
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function set_label( $label ) {
		$this->label = $label;
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

}