<?php

namespace Never5\DownloadMonitor\Shop\Shortcode;

use Never5\DownloadMonitor\Shop\Services\Services;

class Cart {

	/**
	 * Register the shortcode
	 */
	public function register() {
		add_shortcode( 'dlm_cart', array( $this, 'content' ) );
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

		ob_start();

		if ( ! $cart->is_empty() ) {
			download_monitor()->service( 'template_handler' )->get_template_part( 'shop/cart', '', '', array(
				'cart'         => $cart,
				'url_cart'     => Services::get()->service( 'page' )->get_cart_url(),
				'url_checkout' => Services::get()->service( 'page' )->get_checkout_url()
			) );
		} else {
			download_monitor()->service( 'template_handler' )->get_template_part( 'shop/cart/empty', '', '', array() );
		}

		return ob_get_clean();

	}

}