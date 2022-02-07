<?php

namespace Never5\DownloadMonitor\Shop\Order\Transaction;

class OrderTransaction {

	/** @var int */
	private $id;

	/** @var \DateTimeImmutable */
	private $date_created;

	/** @var \DateTimeImmutable */
	private $date_modified;

	/** @var int */
	private $amount;

	/** @var OrderTransactionStatus */
	private $status;

	/** @var string */
	private $processor;

	/** @var string */
	private $processor_nice_name;

	/** @var string */
	private $processor_transaction_id;

	/** @var string */
	private $processor_status;

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
	 * @return OrderTransactionStatus
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @param OrderTransactionStatus $status
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function get_processor() {
		return $this->processor;
	}

	/**
	 * @param string $processor
	 */
	public function set_processor( $processor ) {
		$this->processor = $processor;
	}

	/**
	 * @return string
	 */
	public function get_processor_nice_name() {
		return $this->processor_nice_name;
	}

	/**
	 * @param string $processor_nice_name
	 */
	public function set_processor_nice_name( $processor_nice_name ) {
		$this->processor_nice_name = $processor_nice_name;
	}

	/**
	 * @return string
	 */
	public function get_processor_transaction_id() {
		return $this->processor_transaction_id;
	}

	/**
	 * @param string $processor_transaction_id
	 */
	public function set_processor_transaction_id( $processor_transaction_id ) {
		$this->processor_transaction_id = $processor_transaction_id;
	}

	/**
	 * @return string
	 */
	public function get_processor_status() {
		return $this->processor_status;
	}

	/**
	 * @param string $processor_status
	 */
	public function set_processor_status( $processor_status ) {
		$this->processor_status = $processor_status;
	}

}