<?php

namespace Never5\DownloadMonitor\Shop\Access;

use Never5\DownloadMonitor\Shop\Admin\DownloadOption;
use Never5\DownloadMonitor\Shop\Services\Services;

class Manager {

	/**
	 * Setup Access related things
	 */
	public function setup() {
		add_filter( 'dlm_can_download', array( $this, 'check_access' ), 30, 3 );
	}

	/**
	 * Check if requester has access to download
	 *
	 * @param bool $has_access
	 * @param \DLM_Download $download
	 * @param \DLM_Download_Version $version
	 *
	 * @return bool
	 */
	public function check_access( $has_access, $download, $version ) {

		// check if request still has access at this point this is a purchasable download
		if ( true === $has_access && 1 == get_post_meta( $download->get_id(), DownloadOption::OPTION_KEY, true ) ) {

			/**
			 * This is a download that requires a purchase.
			 */
			$has_access = false;

			$order_id   = ( isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : "" );
			$order_hash = ( isset( $_GET['order_hash'] ) ? $_GET['order_hash'] : "" );

			// if id or hash are empty, no access for you
			if ( empty( $order_id ) || empty( $order_hash ) ) {
				return $has_access;
			}

			/** @var \Never5\DownloadMonitor\Shop\Order\Repository $order_repo */
			$order_repo = Services::get()->service( 'order_repository' );

			/** @var \Never5\DownloadMonitor\Shop\Product\Repository $product_repo */
			$product_repo = Services::get()->service( 'product_repository' );

			// try to fetch order with given order ID
			try {
				$order = $order_repo->retrieve_single( $order_id );
			} catch ( \Exception $exception ) {
				// can't find your order? no access for you
				return $has_access;
			}

			// check if the given hash matches the hash we know the order has
			if ( $order_hash !== $order->get_hash() ) {
				return $has_access;
			}

			// check if the order has the complete status
			if ( $order->get_status()->get_key() !== 'completed' ) {
				return $has_access;
			}

			// check if this download id exists in one of the products that's purchased in this order
			$order_items = $order->get_items();

			foreach ( $order_items as $order_item ) {

				/**
				 * Fetch product
				 */
				try {

					$product = $product_repo->retrieve_single( $order_item->get_product_id() );

					$download_ids = $product->get_download_ids();

					if ( ! empty( $download_ids ) ) {
						foreach ( $download_ids as $download_id ) {
							if ( intval( $download_id ) === intval( $download->get_id() ) ) {
								$has_access = true;
								break;
							}

						}
					}

				} catch ( \Exception $exception ) {
				}

				// if we have access as this point, we stop checking the other items
				if ( $has_access ) {
					break;
				}

			}

		}

		return $has_access;
	}
}