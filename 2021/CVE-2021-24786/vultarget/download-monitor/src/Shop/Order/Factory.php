<?php

namespace Never5\DownloadMonitor\Shop\Order;

use Never5\DownloadMonitor\Shop\Services\Services;

class Factory {

	/**
	 * Make new order with default values (like status and currency)
	 *
	 * @return Order
	 */
	public function make() {
		$order = new Order();

		$order->set_status( Services::get()->service( 'order_status' )->get_default_status() );

		$order->set_currency( Services::get()->service( 'currency' )->get_shop_currency() );

		$order->set_hash( $this->generate_order_hash( $order ) );

		try {
			$order->set_date_created( new \DateTimeImmutable( current_time( 'mysql' ) ) );
		} catch ( \Exception $e ) {

		}

		$order->set_coupons( array() );
		$order->set_items( array() );
		$order->set_transactions( array() );


		return $order;
	}

	/**
	 * Generate order hash
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function generate_order_hash( $order ) {

		$hash = apply_filters( 'dlm_order_hash', sha1( $order->get_id() . time() . uniqid( 'dlm' . time() ) ), $order );

		return $hash;
	}

}