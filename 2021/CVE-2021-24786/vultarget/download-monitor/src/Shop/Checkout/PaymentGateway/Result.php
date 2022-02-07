<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway;

class Result {

	/** @var bool */
	private $success;

	/** @var string */
	private $redirect = null;

	/** @var string $error_message */
	private $error_message = '';

	/**
	 * Result constructor.
	 *
	 * @param bool $success
	 * @param string $redirect
	 */
	public function __construct( $success, $redirect, $error_message = '' ) {
		$this->success       = $success;
		$this->redirect      = $redirect;
		$this->error_message = $error_message;
	}

	/**
	 * @return bool
	 */
	public function is_success() {
		return $this->success;
	}

	/**
	 * @param bool $success
	 */
	public function set_success( $success ) {
		$this->success = $success;
	}

	/**
	 * @return string
	 */
	public function get_redirect() {
		return $this->redirect;
	}

	/**
	 * @param string $redirect
	 */
	public function set_redirect( $redirect ) {
		$this->redirect = $redirect;
	}

	/**
	 * @return string
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * @param string $error_message
	 */
	public function set_error_message( $error_message ) {
		$this->error_message = $error_message;
	}

}