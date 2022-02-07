<?php

namespace Never5\DownloadMonitor\Shop\Order;

class OrderCoupon {

	/** @var int */
	private $id;

	/** @var string */
	private $code;

	/** @var int */
	private $amount;

	/** @var \DateTimeImmutable */
	private $date_created;

	/** @var \DateTimeImmutable */
	private $date_modified;

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

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

	/**
	 * @return \DateTimeImmutable
	 */
	public function get_date_created() {
		return $this->date_created;
	}

	/**
	 * @param \DateTimeImmutable $date_created
	 */
	public function set_date_created( $date_created ) {
		$this->date_created = $date_created;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function get_date_modified() {
		return $this->date_modified;
	}

	/**
	 * @param \DateTimeImmutable $date_modified
	 */
	public function set_date_modified( $date_modified ) {
		$this->date_modified = $date_modified;
	}

}