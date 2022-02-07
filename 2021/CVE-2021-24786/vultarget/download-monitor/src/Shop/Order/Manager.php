<?php

namespace Never5\DownloadMonitor\Shop\Order;

use Never5\DownloadMonitor\Shop\Services\Services;

class Manager {

	/**
	 * Build an array with OrderItem objects based on items in current cart
	 *
	 * @return OrderItem[]
	 */
	public function build_order_items_from_cart() {

		$order_items = array();

		/** @var \Never5\DownloadMonitor\Shop\Cart\Cart $cart */
		$cart = Services::get()->service( 'cart' )->get_cart();

		$cart_items = $cart->get_items();

		if ( ! empty( $cart_items ) ) {
			/** @var \Never5\DownloadMonitor\Shop\Cart\Item\Item $cart_item */
			foreach ( $cart_items as $cart_item ) {
				$order_item = new OrderItem();

				$order_item->set_label( $cart_item->get_label() );
				$order_item->set_qty( $cart_item->get_qty() );
				$order_item->set_product_id( $cart_item->get_product_id() );
				$order_item->set_subtotal( $cart_item->get_subtotal() );
				$order_item->set_tax_total( $cart_item->get_tax_total() );
				/** @todo set tax class */
				$order_item->set_total( $cart_item->get_total() );

				$order_items[] = $order_item;
			}
		}

		return $order_items;

	}

}