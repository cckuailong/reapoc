<?php

namespace Never5\DownloadMonitor\Shop\Cart;

class Coupon {

	/** @var string */
	private $code;

	/** @var int */
	private $amount;

	/**
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}

	/**
	 * @return int
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * @param int $amount
	 */
	public function set_amount( $amount ) {
		$this->amount = $amount;
	}

}