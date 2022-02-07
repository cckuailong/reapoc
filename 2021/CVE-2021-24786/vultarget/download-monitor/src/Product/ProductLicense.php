<?php

class DLM_Product_License {

	/**
	 * @var String
	 */
	private $product_id;

	/**
	 * @var String
	 */
	private $key;

	/**
	 * @var String
	 */
	private $email;

	/**
	 * @var String (active or inactive)
	 */
	private $status;

	/**
	 * Constructor
	 *
	 * @param String $product_id
	 */
	public function __construct( $product_id ) {

		// Set Product ID
		$this->product_id = $product_id;

		// Load license data from DB
		$db_license = wp_parse_args( get_option( $this->product_id . '-license', array() ), array(
			'key'    => '',
			'email'  => get_option( 'admin_email', '' ),
			'status' => 'inactive'
		) );

		// Set properties
		$this->key    = $db_license['key'];
		$this->email  = $db_license['email'];
		$this->status = $db_license['status'];
	}

	/**
	 * @return String
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @param String $key
	 */
	public function set_key( $key ) {
		$this->key = $key;
	}

	/**
	 * @return String
	 */
	public function get_email() {
		return $this->email;
	}

	/**
	 * @param String $email
	 */
	public function set_email( $email ) {
		$this->email = $email;
	}

	/**
	 * @return String
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @param String $status
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Return if license is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return ( 'active' === $this->status );
	}

	/**
	 * Store license data in DB
	 */
	public function store() {
		update_option( $this->product_id . '-license', array(
			'key'    => $this->get_key(),
			'email'  => $this->get_email(),
			'status' => $this->get_status()
		) );
	}

}