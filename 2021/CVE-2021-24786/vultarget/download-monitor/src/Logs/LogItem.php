<?php

class DLM_Log_Item {

	/** @var int */
	private $id = 0;

	/** @var int */
	private $user_id;

	/** @var string */
	private $user_ip;

	/** @var string */
	private $user_agent;

	/** @var int */
	private $download_id;

	/** @var int */
	private $version_id;

	/** @var string */
	private $version;

	/** @var \DateTime */
	private $download_date;

	/** @var string */
	private $download_status;

	/** @var string */
	private $download_status_message;

	/** @var array */
	private $meta_data = array();

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
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * @param int $user_id
	 */
	public function set_user_id( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * @return string
	 */
	public function get_user_ip() {
		return $this->user_ip;
	}

	/**
	 * @param string $user_ip
	 */
	public function set_user_ip( $user_ip ) {
		$this->user_ip = $user_ip;
	}

	/**
	 * @return string
	 */
	public function get_user_agent() {
		return $this->user_agent;
	}

	/**
	 * @param string $user_agent
	 */
	public function set_user_agent( $user_agent ) {
		$this->user_agent = $user_agent;
	}

	/**
	 * @return int
	 */
	public function get_download_id() {
		return $this->download_id;
	}

	/**
	 * @param int $download_id
	 */
	public function set_download_id( $download_id ) {
		$this->download_id = $download_id;
	}

	/**
	 * @return int
	 */
	public function get_version_id() {
		return $this->version_id;
	}

	/**
	 * @param int $version_id
	 */
	public function set_version_id( $version_id ) {
		$this->version_id = $version_id;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

	/**
	 * @return DateTime
	 */
	public function get_download_date() {
		return $this->download_date;
	}

	/**
	 * @param DateTime $download_date
	 */
	public function set_download_date( $download_date ) {
		$this->download_date = $download_date;
	}

	/**
	 * @return string
	 */
	public function get_download_status() {
		return $this->download_status;
	}

	/**
	 * @param string $download_status
	 */
	public function set_download_status( $download_status ) {
		$this->download_status = $download_status;
	}

	/**
	 * @return string
	 */
	public function get_download_status_message() {
		return $this->download_status_message;
	}

	/**
	 * @param string $download_status_message
	 */
	public function set_download_status_message( $download_status_message ) {
		$this->download_status_message = $download_status_message;
	}

	/**
	 * @return array
	 */
	public function get_meta_data() {
		return $this->meta_data;
	}

	/**
	 * @param array $meta_data
	 */
	public function set_meta_data( $meta_data ) {
		$this->meta_data = $meta_data;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function add_meta_data_item( $key, $value ) {

		// get meta
		$meta = $this->get_meta_data();

		// just to be sure we have an array
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		// set new meta. We're not checking if it exists, this means we override by default. Check in your code if exists before adding!
		$meta[ $key ] = $value;

		// set meta
		$this->set_meta_data( $meta );
	}

	/**
	 * Checks if meta data exists for given key
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function meta_data_exist( $key ) {
		$meta = $this->get_meta_data();

		return ( is_array( $meta ) && isset( $meta[ $key ] ) );
	}

}