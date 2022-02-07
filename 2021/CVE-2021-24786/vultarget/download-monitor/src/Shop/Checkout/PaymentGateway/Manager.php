<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway;

class Manager {

	/** @var PaymentGateway[] */
	private $gateways = array();

	/**
	 * Manager constructor.
	 */
	public function __construct() {

		// add gateways
		$this->gateways = apply_filters( 'dlm_shop_payment_gateways', array(
			new PayPal\PayPalGateway(),
			new Test\TestGateway()
		) );

	}

	/**
	 * Returns all payment gateways
	 *
	 * @return PaymentGateway[]
	 */
	public function get_all_gateways() {
		$gateways = array();
		if ( ! empty( $this->gateways ) ) {
			/** @var PaymentGateway $gateway */
			foreach ( $this->gateways as $gateway ) {
				$gateways[ $gateway->get_id() ] = $gateway;
			}
		}

		return $gateways;
	}

	/**
	 * Returns all enabled payment gateways
	 *
	 * @return PaymentGateway[]
	 */
	public function get_enabled_gateways() {
		$eg = array();
		if ( ! empty( $this->gateways ) ) {
			/** @var PaymentGateway $gateway */
			foreach ( $this->gateways as $gateway ) {
				if ( $gateway->is_enabled() ) {
					$eg[ $gateway->get_id() ] = $gateway;
				}
			}
		}

		return $eg;
	}

	/**
	 * ****** DO NOT CALL THIS METHOD YOURSELF *****
	 * Setup all enabled gateways. This is automatically called in bootstrap.
	 */
	public function setup_gateways() {

		/** @var PaymentGateway[] $gateways */
		$gateways = $this->get_enabled_gateways();
		if ( count( $gateways ) > 0 ) {
			foreach ( $gateways as $gateway ) {
				$gateway->setup_gateway();
			}
		}
	}

}