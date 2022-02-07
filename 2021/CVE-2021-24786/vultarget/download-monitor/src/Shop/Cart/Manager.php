<?php

namespace Never5\DownloadMonitor\Shop\Cart;

use Never5\DownloadMonitor\Shop\Services\Services;

class Manager {

	/**
	 * @param \Never5\DownloadMonitor\Shop\Session\Session $session
	 *
	 * @return Cart
	 */
	private function build_cart_from_session( $session ) {
		$cart = new Cart();

		$subtotal      = 0;
		$tax_total     = 0;
		$coupons_total = 0;

		/**
		 * Set items
		 */
		$session_items = $session->get_items();
		$items         = array();
		if ( ! empty( $session_items ) ) {

			/** @var Item\Factory $item_factory */
			$item_factory = Services::get()->service( 'cart_item_factory' );

			/** @var \Never5\DownloadMonitor\Shop\Session\Item\Item $session_item */
			foreach ( $session_items as $session_item ) {

				try {
					$item = $item_factory->make( $session_item->get_product_id() );
					$item->set_qty( $session_item->get_qty() );

					// add item to items array
					$items[] = $item;

					// add this price to sub total
					$subtotal += $item->get_subtotal();
				} catch ( \Exception $exception ) {

				}

			}
		}
		$cart->set_items( $items );

		/**
		 * Set sub total
		 */
		$cart->set_subtotal( $subtotal );

		/**
		 * Set tax total
		 */
		$cart->set_tax_total( $tax_total );

		/**
		 * Set total
		 */
		$cart->set_total( ( $subtotal + $tax_total ) - $coupons_total );

		return $cart;
	}

	/**
	 * Build session from cart
	 *
	 * @param Cart $cart
	 *
	 * @return \Never5\DownloadMonitor\Shop\Session\Session
	 */
	private function build_session_from_cart( $cart ) {

		/** @var \Never5\DownloadMonitor\Shop\Session\Session $session */
		$session = Services::get()->service( 'session' )->get_session();

		// reset expiry date
		$session->reset_expiry();

		// convert cart items to session items
		$cart_items    = $cart->get_items();
		$session_items = array();
		if ( ! empty( $cart_items ) ) {

			$session_item_factory = Services::get()->service( 'session_item_factory' );

			/** @var Item\Item $cart_item */
			foreach ( $cart_items as $cart_item ) {
				$session_items[] = $session_item_factory->make( $cart_item->get_product_id(), $cart_item->get_qty() );
			}
		}
		$session->set_items( $session_items );

		// convert cart discounts to session discounts
		$cart_coupons    = $cart->get_coupons();
		$session_coupons = array();
		if ( ! empty( $cart_coupons ) ) {
			/** @var Coupon $cart_coupon */
			foreach ( $cart_coupons as $cart_coupon ) {
				$session_coupons[] = $cart_coupon->get_code();
			}
		}
		$session->set_coupons( $session_coupons );

		return $session;
	}


	/**
	 * Get current cart.
	 * This method builds the current cart based on the user session.
	 *
	 * @return Cart
	 */
	public function get_cart() {

		// get current session from cookie
		$session = Services::get()->service( 'session' )->get_session();

		// build a cart object from given session
		return $this->build_cart_from_session( $session );
	}

	/**
	 * Destroys the full cart.
	 * Session in DB as well as cookie reference will be removed.
	 */
	public function destroy_cart() {
		Services::get()->service( 'session' )->destroy_current_session();
	}

	/**
	 * Save cart.
	 * This will turn a cart object to a session, store session in DB and set cookie with reference to DB session.
	 *
	 * @param Cart $cart
	 *
	 * @return bool
	 */
	public function save_cart( $cart ) {

		// build new session from cart
		$session = $this->build_session_from_cart( $cart );

		// persist session
		Services::get()->service( 'session' )->persist_session( $session );

		return true;
	}

	/**
	 * Add a download to cart
	 *
	 * @param int $product_id
	 * @param int $qty
	 */
	public function add_to_cart( $product_id, $qty ) {

		try {

			/** @var Item\Item $item */
			$item = Services::get()->service( 'cart_item_factory' )->make( $product_id );
			$item->set_qty( $qty );

			// add item to cart
			$cart = $this->get_cart();
			if ( ! $cart->has_item( $item ) ) {
				$cart->add_item( $item );
			}

			// save cart
			$this->save_cart( $cart );


		} catch ( \Exception $exception ) {

		}

	}

	/**
	 * Remove a download from cart
	 *
	 * @param int $product_id
	 */
	public function remove_from_cart( $product_id ) {

		// get cart and items
		$cart  = $this->get_cart();
		$items = $cart->get_items();

		// search for given download ID in cart items, remove if found
		if ( ! empty( $items ) ) {

			/** @var int $ik */
			/** @var Item\Item $iv */
			foreach ( $items as $ik => $iv ) {
				if ( $iv->get_product_id() == $product_id ) {
					unset( $items[ $ik ] );
				}
			}

		}

		// set items back in cart
		$cart->set_items( $items );

		// save cart
		$this->save_cart( $cart );

	}

}