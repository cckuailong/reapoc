<?php

namespace Never5\DownloadMonitor\Shop\Order;

class OrderCustomer {

	/** @var string */
	private $first_name;

	/** @var string */
	private $last_name;

	/** @var string */
	private $company;

	/** @var string */
	private $address_1;

	/** @var string */
	private $address_2;

	/** @var string */
	private $city;

	/** @var string */
	private $state;

	/** @var string */
	private $postcode;

	/** @var string */
	private $country;

	/** @var string */
	private $email;

	/** @var string */
	private $phone;

	/** @var string */
	private $ip_address;

	/**
	 * OrderCustomer constructor.
	 *
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $company
	 * @param string $address_1
	 * @param string $address_2
	 * @param string $city
	 * @param string $state
	 * @param string $postcode
	 * @param string $country
	 * @param string $email
	 * @param string $phone
	 * @param string $ip_address
	 */
	public function __construct( $first_name, $last_name, $company, $address_1, $address_2, $city, $state, $postcode, $country, $email, $phone, $ip_address ) {
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->company    = $company;
		$this->address_1  = $address_1;
		$this->address_2  = $address_2;
		$this->city       = $city;
		$this->state      = $state;
		$this->postcode   = $postcode;
		$this->country    = $country;
		$this->email      = $email;
		$this->phone      = $phone;
		$this->ip_address = $ip_address;
	}

	/**
	 * @return string
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * @param string $first_name
	 */
	public function set_first_name( $first_name ) {
		$this->first_name = $first_name;
	}

	/**
	 * @return string
	 */
	public function get_last_name() {
		return $this->last_name;
	}

	/**
	 * @param string $last_name
	 */
	public function set_last_name( $last_name ) {
		$this->last_name = $last_name;
	}

	/**
	 * @return string
	 */
	public function get_company() {
		return $this->company;
	}

	/**
	 * @param string $company
	 */
	public function set_company( $company ) {
		$this->company = $company;
	}

	/**
	 * @return string
	 */
	public function get_address_1() {
		return $this->address_1;
	}

	/**
	 * @param string $address_1
	 */
	public function set_address_1( $address_1 ) {
		$this->address_1 = $address_1;
	}

	/**
	 * @return string
	 */
	public function get_address_2() {
		return $this->address_2;
	}

	/**
	 * @param string $address_2
	 */
	public function set_address_2( $address_2 ) {
		$this->address_2 = $address_2;
	}

	/**
	 * @return string
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function set_city( $city ) {
		$this->city = $city;
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
	 * @return string
	 */
	public function get_postcode() {
		return $this->postcode;
	}

	/**
	 * @param string $postcode
	 */
	public function set_postcode( $postcode ) {
		$this->postcode = $postcode;
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
	public function get_email() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function set_email( $email ) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function set_phone( $phone ) {
		$this->phone = $phone;
	}

	/**
	 * @return string
	 */
	public function get_ip_address() {
		return $this->ip_address;
	}

	/**
	 * @param string $ip_address
	 */
	public function set_ip_address( $ip_address ) {
		$this->ip_address = $ip_address;
	}

}