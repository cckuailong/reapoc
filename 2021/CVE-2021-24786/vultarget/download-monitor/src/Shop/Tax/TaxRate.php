<?php

namespace Never5\DownloadMonitor\Shop\Tax;

class TaxRate {

	/** @var int */
	private $id;

	/** @var string */
	private $class;

	/** @var string */
	private $country;

	/** @var string */
	private $state;

	/** @var int */
	private $rate; /** @todo look into if float is reliable enough for this. */

	/** @var string  */
	private $label;

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
	public function get_class() {
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function set_class( $class ) {
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function get_country() {
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function set_country( $country ) {
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function get_state() {
		return $this->state;
	}

	/**
	 * @param string $state
	 */
	public function set_state( $state ) {
		$this->state = $state;
	}

	/**
	 * @return int
	 */
	public function get_rate() {
		return $this->rate;
	}

	/**
	 * @param int $rate
	 */
	public function set_rate( $rate ) {
		$this->rate = $rate;
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