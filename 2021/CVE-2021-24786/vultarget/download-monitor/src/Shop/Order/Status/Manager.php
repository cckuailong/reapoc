<?php

namespace Never5\DownloadMonitor\Shop\Order\Status;

use Never5\DownloadMonitor\Shop\Services\Services;

class Manager {

	/**
	 * Get available statuses
	 *
	 * @return array
	 */
	public function get_available_statuses() {

		/** @var \Never5\DownloadMonitor\Shop\Order\Status\Factory $factory */
		$factory = Services::get()->service( 'order_status_factory' );

		return apply_filters( 'dlm_shop_order_statuses', array(
			$factory->make( 'trash' ),
			$factory->make( 'pending-payment' ),
			$factory->make( 'completed' ),
			$factory->make( 'failed' ),
			$factory->make( 'refunded' ),
		) );
	}

	/**
	 * Get the default order status
	 *
	 * @return string
	 */
	public function get_default_status() {
		return apply_filters( 'dlm_shop_default_order_status', Services::get()->service( 'order_status_factory' )->make( 'pending-payment' ) );
	}

}