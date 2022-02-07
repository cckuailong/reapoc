<?php

namespace Never5\DownloadMonitor\Shop\Cart;

use Never5\DownloadMonitor\Shop\Services\Services;

class Hooks {

	/**
	 * Setup cart hooks
	 */
	public function setup() {
		add_action( 'init', array( $this, 'catch_add_to_cart' ), 1 );
		add_action( 'init', array( $this, 'catch_remove_from_cart' ), 1 );

		add_action( 'template_redirect', array( $this, 'check_for_cart_redirect' ) );
	}

	/**
	 * Catch add to cart request
	 */
	public function catch_add_to_cart() {
		if ( ! empty( $_GET['dlm-add-to-cart'] ) ) {
			$atc_id = absint( $_GET['dlm-add-to-cart'] );

			if ( $atc_id > 0 ) {
				Services::get()->service( 'cart' )->add_to_cart( $atc_id, 1 );
				Services::get()->service( 'page' )->to_cart();
			}
		}
	}

	/**
	 * Catch remove from cart request
	 */
	public function catch_remove_from_cart() {
		if ( ! empty( $_GET['dlm-remove-from-cart'] ) ) {
			$atc_id = absint( $_GET['dlm-remove-from-cart'] );

			if ( $atc_id > 0 ) {
				Services::get()->service( 'cart' )->remove_from_cart( $atc_id );
				Services::get()->service( 'page' )->to_cart();
			}
		}
	}

	/**
	 * Check if the 'disable_cart' setting is enabled. If so, redirect customer to checkout on cart pages
	 */
	public function check_for_cart_redirect() {
		if ( download_monitor()->service( 'settings' )->get_option( 'disable_cart' ) ) {
			if ( Services::get()->service( 'page' )->is_cart() ) {
				wp_redirect( Services::get()->service( 'page' )->get_checkout_url(), 302 );
				exit;
			}
		}
	}


}