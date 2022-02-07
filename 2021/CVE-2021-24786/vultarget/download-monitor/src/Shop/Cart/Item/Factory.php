<?php

namespace Never5\DownloadMonitor\Shop\Cart\Item;

use Never5\DownloadMonitor\Shop\Services\Services;

class Factory {


	/**
	 * Make Item for given Download ID
	 *
	 * @param int $product_id
	 *
	 * @return Item
	 * @throws \Exception
	 */
	public function make( $product_id ) {

		/**
		 * Fetch the download
		 *
		 * @var \Never5\DownloadMonitor\Shop\Product\Product $product
		 */
		$product = Services::get()->service( 'product_repository' )->retrieve_single( $product_id );

		if ( ! in_array( $product->get_status(), array( 'publish' ) ) ) {
			throw new \Exception( 'Product not purchasable' );
		}


		// build item
		$item = new Item();
		$item->set_product_id( $product_id );
		$item->set_qty( 1 );
		$item->set_label( $product->get_title() );
		$item->set_subtotal( $product->get_price() );
		$item->set_tax_total( 0 );
		/** @todo [TAX] Implement taxes */
		$item->set_total( $product->get_price() );

		return $item;
	}

}