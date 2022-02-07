<?php

namespace Never5\DownloadMonitor\Shop\Shortcode;

use Never5\DownloadMonitor\Shop\Services\Services;

class Checkout {

	/**
	 * Register the shortcode
	 */
	public function register() {
		add_shortcode( 'dlm_checkout', array( $this, 'content' ) );
	}

	/**
	 * Shortcode content
	 *
	 * @param $atts array
	 *
	 * @return string
	 */
	public function content( $atts ) {

		/** @var \Never5\DownloadMonitor\Shop\Cart\Cart $cart */
		$cart = Services::get()->service( 'cart' )->get_cart();


		$endpoint = ( isset( $_GET['ep'] ) ? $_GET['ep'] : "" );

		ob_start();

		switch ( $endpoint ) {
			case "complete":

				// get order
				$order = $this->get_order_from_url();
				if ( $order !== null ) {
					// load the template
					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/order-complete', '', '', array(
						'order_id' => $order->get_id(),
						'order'    => $order
					) );
				}

				break;
			case "cancelled":
				// get order
				$order = $this->get_order_from_url();
				if ( $order !== null ) {

					// get simplified items array
					$items = $this->get_simplified_item_array( $order->get_items() );

					// create field values
					$customer     = $order->get_customer();
					$field_values = array(
						'first_name' => $customer->get_first_name(),
						'last_name'  => $customer->get_last_name(),
						'company'    => $customer->get_company(),
						'email'      => $customer->get_email(),
						'address_1'  => $customer->get_address_1(),
						'postcode'   => $customer->get_postcode(),
						'city'       => $customer->get_city(),
						'country'    => $customer->get_country(),

					);

					// set error
					$errors = array(
						__( "Your payment failed, please try again.", 'download-monitor' )
					);

					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout', '', '', array(
						'form_data_str' => sprintf( 'data-order_id="%s" data-order_hash="%s"', esc_attr( $order->get_id() ), esc_attr( $order->get_hash() ) ),
						'cart'          => $cart,
						'url_cart'      => Services::get()->service( 'page' )->get_cart_url(),
						'url_checkout'  => Services::get()->service( 'page' )->get_checkout_url(),
						'field_values'  => $field_values,
						'items'         => $items,
						'subtotal'      => dlm_format_money( $order->get_subtotal() ),
						'total'         => dlm_format_money( $order->get_total() ),
						'errors'        => $errors
					) );
				} else {
					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/empty', '', '', array() );
				}
				break;
			case "":
				if ( ! $cart->is_empty() ) {

					$items = $this->get_simplified_item_array( $cart->get_items() );

					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout', '', '', array(
						'form_data_str' => '',
						'cart'          => $cart,
						'url_cart'      => Services::get()->service( 'page' )->get_cart_url(),
						'url_checkout'  => Services::get()->service( 'page' )->get_checkout_url(),
						'field_values'  => array(),
						'items'         => $items,
						'subtotal'      => dlm_format_money( $cart->get_subtotal() ),
						'total'         => dlm_format_money( $cart->get_total() ),
						'errors'        => array()
					) );
				} else {
					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/empty', '', '', array() );
				}

				break;
		}

		return ob_get_clean();

	}

	/**
	 * Get order object based on data in GET
	 *
	 * @return \Never5\DownloadMonitor\Shop\Order\Order|null
	 */
	private function get_order_from_url() {
		// get order data
		$order_id   = absint( ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : 0 );
		$order_hash = ( isset( $_GET['order_hash'] ) ? $_GET['order_hash'] : '' );
		$order      = null;

		if ( $order_id > 0 ) {
			/** @var \Never5\DownloadMonitor\Shop\Order\WordPressRepository $op */
			try {
				$op    = Services::get()->service( 'order_repository' );
				$order = $op->retrieve_single( $order_id );

				// check order hashes
				if ( $order_hash !== $order->get_hash() ) {
					throw new \Exception( 'Order hash incorrect' );
				}
			} catch ( \Exception $e ) {
				return null;
			}
		}

		return $order;
	}

	/**
	 * Get a simplified array of item data for template usage
	 *
	 * @param \Never5\DownloadMonitor\Shop\Cart\Item\Item[] | \Never5\DownloadMonitor\Shop\Order\OrderItem[] $items
	 *
	 * @return array
	 */
	private function get_simplified_item_array( $items ) {
		$simple_items = array();
		if ( ! empty( $items ) ) {
			/** @var \Never5\DownloadMonitor\Shop\Cart\Item\Item $item */
			foreach ( $items as $item ) {
				$simple_items[] = array(
					'label'    => $item->get_label(),
					'subtotal' => dlm_format_money( $item->get_subtotal() )
				);
			}
		}

		return $simple_items;
	}

}