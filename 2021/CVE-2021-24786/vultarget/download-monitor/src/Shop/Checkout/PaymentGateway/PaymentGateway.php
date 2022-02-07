<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway;

use Never5\DownloadMonitor\Shop\Services\Services;

abstract class PaymentGateway {

	/** @var string */
	private $id;

	/** @var string */
	private $title;

	/** @var string */
	private $description;

	/** @var bool */
	private $enabled;

	/** @var array */
	private $settings = array();

	/**
	 * PaymentGateway constructor.
	 */
	public function __construct() {
		$this->setup_settings();

		$this->set_enabled( 1 == $this->get_option('enabled') );
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function set_settings( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @param bool $enabled
	 */
	public function set_enabled( $enabled ) {
		$this->enabled = $enabled;
	}

	/**
	 * This is the place to setup all things related to your gateway.
	 * Need to capture an event? Set up the listener here.
	 * Want to add an extra page? This is the place.
	 * Add an extra endpoint? Set it up here.
	 *
	 * This method is triggered for every *enabled* gateway, on init (should still be safe to redirect as well at this point)
	 */
	public function setup_gateway() {
		/** Override in gateway */
	}

	/**
	 * Get the success URL for given order
	 *
	 * @param int $order_id
	 * @param string $order_hash
	 *
	 * @return string
	 */
	public function get_success_url( $order_id, $order_hash ) {
		return add_query_arg( array( 'order_id' => $order_id, 'order_hash' => $order_hash ), Services::get()->service( 'page' )->get_checkout_url( 'complete' ) );
	}

	/**
	 * Get the failed URL for given order
	 *
	 * @param int $order_id
	 * @param string $order_hash
	 *
	 * @return string
	 */
	public function get_failed_url( $order_id, $order_hash ) {
		return add_query_arg( array( 'order_id' => $order_id, 'order_hash' => $order_hash ), Services::get()->service( 'page' )->get_checkout_url( 'failed' ) );
	}

	/**
	 * Get the success URL for given order
	 *
	 * @param int $order_id
	 * @param string $order_hash
	 *
	 * @return string
	 */
	public function get_cancel_url( $order_id, $order_hash ) {
		return add_query_arg( array( 'order_id' => $order_id, 'order_hash' => $order_hash ), Services::get()->service( 'page' )->get_checkout_url( 'cancelled' ) );
	}

	/**
	 * Setup settings for this payment gateway
	 * Default setting is if the gateway is enabled
	 */
	protected function setup_settings() {
		$this->set_settings( array() );
	}

	/**
	 * Get value for given payment option key
	 *
	 * @param $option
	 *
	 * @return string
	 */
	protected function get_option( $option ) {
		return download_monitor()->service( 'settings' )->get_option( 'gateway_' . $this->get_id() . '_' . $option );
	}

	/**
	 * @param $order_id
	 *
	 * @return Result
	 */
	abstract public function process( $order_id );
}