<?php

namespace Never5\DownloadMonitor\Shop\Session\Item;

class Factory {

	/**
	 * Generate key
	 *
	 * @return string
	 */
	private function generate_key() {
		return md5( uniqid( 'dlm_shop_session_item_key', true ) . mt_rand( 0, 99 ) );
	}

	/**
	 * @param int $product_id
	 * @param int $qty
	 *
	 * @return Item
	 */
	public function make( $product_id, $qty ) {
		$item = new Item();

		$item->set_key( $this->generate_key() );
		$item->set_product_id( $product_id );
		$item->set_qty( $qty );

		return $item;
	}
}