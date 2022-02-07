<?php

namespace Never5\DownloadMonitor\Shop\Order\Status;

class OrderStatus {
	/** @var string */
	private $key;

	/** @var string */
	private $label;

	/**
	 * OrderStatus constructor.
	 *
	 * @param string $key
	 * @param string $label
	 */
	public function __construct( $key, $label ) {
		$this->key   = $key;
		$this->label = $label;
	}

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
	public function get_label() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function set_label( $label ) {
		$this->label = $label;
	}

}