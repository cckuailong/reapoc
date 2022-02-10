<?php

namespace TEC\Tickets\Commerce;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Utils\Price;
use \Tribe__Utils__Array as Arr;

/**
 * Class Cart
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Cart {

	/**
	 * Which URL param we use to identify a given page as the cart.
	 * Keep in mind this is not the only way, please use `is_current_page()` to determine that.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $url_query_arg = 'tec-tc-cart';

	/**
	 * Which URL param we use to tell the checkout page to set a cookie, since you cannot set a cookie on a 302
	 * redirect.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $cookie_query_arg = 'tec-tc-cookie';

	/**
	 * Redirect mode string, which will be used to determine which kind of cart the repository might be.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const REDIRECT_MODE = 'redirect';

	/**
	 * Which URL param we use to identify a given page as the cart.
	 * Keep in mind this is not the only way, please use `is_current_page()` to determine that.
	 *
	 * @since 5.1.9
	 *
	 * @var string[]
	 */
	protected $available_modes = [ self::REDIRECT_MODE ];

	/**
	 * Which cookie we will store the cart hash.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $cart_hash_cookie_name = 'tec-tickets-commerce-cart';

	/**
	 * Gets the current instance of cart handling that we are using.
	 * Most of the pieces should be handled in the Repository for the cart, only piece fully handled by the
	 * parent class is the cookie handling.
	 *
	 * @since 5.1.9
	 *
	 * @return Commerce\Cart\Cart_Interface
	 */
	public function get_repository() {
		$default_cart = tribe( Cart\Unmanaged_Cart::class );

		/**
		 * Filters the cart repository, by default we use Unmanaged Cart.
		 *
		 * @since 5.1.9
		 *
		 * @param Cart\Cart_Interface $cart Instance of the cart repository managing the cart.
		 */
		return apply_filters( 'tec_tickets_commerce_cart_repository', $default_cart );
	}

	/**
	 * From the current active cart repository we fetch it's mode.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_mode() {
		return $this->get_repository()->get_mode();
	}

	/**
	 * Gets the list of available modes we can use for the cart.
	 *
	 * @since 5.1.9
	 *
	 * @return string[]
	 */
	public function get_available_modes() {
		return $this->available_modes;
	}

	/**
	 * If a given string is a valid and available mode.
	 *
	 * @since 5.1.9
	 *
	 * @param string $mode Which mode we are testing.
	 *
	 * @return bool
	 */
	public function is_available_mode( $mode ) {
		return in_array( $mode, $this->get_available_modes(), true );
	}

	/**
	 * If the current page is the cart page or not.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function is_current_page() {
		$cart_mode = tribe_get_request_var( static::$url_query_arg, false );

		if ( ! $this->is_available_mode( $cart_mode ) ) {
			return false;
		}

		// When the current cart doesn't use this mode we fail the page check.
		if ( $this->get_mode() !== $cart_mode ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the name of the transient used by the cart.
	 *
	 * @since 5.1.9
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public static function get_transient_name( $id ) {
		return Commerce::ABBR . '-cart-' . md5( $id );
	}

	/**
	 * Determine the Current cart Transient Key based on invoice number.
	 *
	 * @since 5.1.9
	 *
	 * @return string|null
	 */
	public function get_current_cart_transient() {
		$cart_hash = $this->get_cart_hash();

		if ( empty( $cart_hash ) ) {
			return null;
		}

		return static::get_transient_name( $cart_hash );
	}

	/**
	 * Determine the Current cart URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_url() {
		$url = home_url( '/' );

		$url = add_query_arg( [ static::$url_query_arg => $this->get_mode() ], $url );

		/**
		 * Allows modifications to the cart url for Tickets Commerce.
		 *
		 * @since 5.1.9
		 *
		 * @param string $url URL for the cart.
		 */
		return (string) apply_filters( 'tec_tickets_commerce_cart_url', $url );
	}

	/**
	 * Reads the cart hash from the cookies.
	 *
	 * @since 5.1.9
	 *
	 * @return string|null The cart hash or `null` if not found.
	 */
	public function get_cart_hash( $generate = false ) {
		$cart_hash_length = 12;

		$cart_hash = $this->get_repository()->get_hash();

		if (
			! empty( $_COOKIE[ static::$cart_hash_cookie_name ] )
			&& strlen( $_COOKIE[ static::$cart_hash_cookie_name ] ) === $cart_hash_length
		) {
			$cart_hash = $_COOKIE[ static::$cart_hash_cookie_name ];

			$cart_hash_transient = get_transient( static::get_transient_name( $cart_hash ) );

			if ( empty( $cart_hash_transient ) ) {
				$cart_hash = null;
			}
		}

		if ( empty( $cart_hash ) && $generate ) {
			$tries     = 1;
			$max_tries = 20;

			$this->clear_cart();
			// While we dont find an empty transient to store this cart we loop, but avoid more than 20 tries.
			while (
				( ! empty( $cart_hash_transient ) || empty( $cart_hash ) )
				&& $max_tries >= $tries
			) {
				$cart_hash           = wp_generate_password( $cart_hash_length, false );
				$cart_hash_transient = get_transient( static::get_transient_name( $cart_hash ) );

				// Make sure we increment.
				$tries ++;
			}
		}

		$this->set_cart_hash( $cart_hash );

		return $this->get_repository()->get_hash();
	}

	/**
	 * Configures the Cart hash on the class object
	 *
	 * @since 5.2.0
	 *
	 * @param string $cart_hash Cart hash value.
	 *
	 */
	public function set_cart_hash( $cart_hash ) {
		$this->get_repository()->set_hash( $cart_hash );
	}

	/**
	 * Clear the cart.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function clear_cart() {
		$this->set_cart_hash_cookie( null );
		$this->get_repository()->clear();

		unset( $_COOKIE[ static::$cart_hash_cookie_name ] );

		return delete_transient( static::get_current_cart_transient() );
	}

	/**
	 * Sets the cart hash cookie or resets the cookie.
	 *
	 * @since 5.1.9
	 *
	 * @parem string $value Value used for the cookie or empty to purge the cookie.
	 *
	 * @return boolean
	 */
	public function set_cart_hash_cookie( $value = '' ) {
		if ( headers_sent() ) {
			return false;
		}

		/**
		 * Filters the life span of the Cart Cookie.
		 *
		 * @since 5.1.9
		 *
		 * @param int $expires The expiry time, as passed to setcookie().
		 */
		$expire  = apply_filters( 'tec_tickets_commerce_cart_expiration', time() + 1 * HOUR_IN_SECONDS );
		$referer = wp_get_referer();

		if ( $referer ) {
			$secure = ( 'https' === parse_url( $referer, PHP_URL_SCHEME ) );
		} else {
			$secure = false;
		}

		// When null means we are deleting.
		if ( null === $value ) {
			$expire = 1;
		}

		$is_cookie_set = setcookie( static::$cart_hash_cookie_name, $value, $expire, COOKIEPATH ?: '/', COOKIE_DOMAIN, $secure );

		// Overwrite local variable so we can use it right away.
		$_COOKIE[ static::$cart_hash_cookie_name ] = $value;

		return $is_cookie_set;
	}

	/**
	 * Get the tickets currently in the cart for a given provider.
	 *
	 * @since 5.1.9
	 *
	 * @param bool $full_item_params Determines all the item params, including event_id, sub_total, and obj.
	 *
	 * @return array List of items.
	 */
	public function get_items_in_cart( $full_item_params = false ) {
		$cart  = $this->get_repository();
		$items = $cart->get_items();

		// When Items is empty in any capacity return an empty array.
		if ( empty( $items ) ) {
			return [];
		}

		if ( $full_item_params ) {
			$items = array_map( static function ( $item ) {
				$item['obj']       = \Tribe__Tickets__Tickets::load_ticket_object( $item['ticket_id'] );
				// If it's an invalid ticket we just remove it.
				if ( ! $item['obj'] instanceof \Tribe__Tickets__Ticket_Object ) {
					return null;
				}

				$item['event_id']  = $item['obj']->get_event_id();
				$item['sub_total'] = Price::sub_total( $item['obj']->price, $item['quantity'] );

				return $item;
			}, $items );
		}

		return array_filter( $items );
	}

	/**
	 * Handles the process of adding a ticket product to the cart.
	 *
	 * If the cart contains a line item for the product, this will replace the previous quantity.
	 * If the quantity is zero and the cart contains a line item for the product, this will remove it.
	 *
	 * @since 5.1.9
	 *
	 * @param int   $ticket_id  Ticket ID.
	 * @param int   $quantity   Ticket quantity to add.
	 * @param array $extra_data Extra data to send to the cart item.
	 */
	public function add_ticket( $ticket_id, $quantity = 1, array $extra_data = [] ) {
		$cart = $this->get_repository();

		// Enforces that the min to add is 1.
		$quantity = max( 1, (int) $quantity );

		// Add to / update quantity in cart.
		$cart->add_item( $ticket_id, $quantity, $extra_data );
	}

	/**
	 * Handles the process of adding a ticket product to the cart.
	 *
	 * If the cart contains a line item for the product, this will replace the previous quantity.
	 * If the quantity is zero and the cart contains a line item for the product, this will remove it.
	 *
	 * @since 5.1.9
	 *
	 * @param int $ticket_id Ticket ID.
	 * @param int $quantity  Ticket quantity to remove.
	 */
	public function remove_ticket( $ticket_id, $quantity = 1 ) {
		$cart = $this->get_repository();

		// Enforces that the min to remove is 1.
		$quantity = max( 1, (int) $quantity );

		$cart->remove_item( $ticket_id, $quantity );
	}

	/**
	 * If product cache parameter is found, delete saved products from temporary cart.
	 *
	 * @filter wp_loaded 0
	 *
	 * @since  5.1.9
	 */
	public function maybe_delete_expired_products() {
		$delete = tribe_get_request_var( 'clear_product_cache', null );

		if ( empty( $delete ) ) {
			return;
		}

		$transient_key = $this->get_current_cart_transient();

		// Bail if we have no data key.
		if ( empty( $transient_key ) ) {
			return;
		}

		$transient = get_transient( $transient_key );

		// Bail if we have no data to delete.
		if ( empty( $transient ) ) {
			return;
		}

		// Bail if ET+ is not in place.
		if ( ! class_exists( 'Tribe__Tickets_Plus__Meta__Storage' ) ) {
			return;
		}
		$storage = new \Tribe__Tickets_Plus__Meta__Storage();

		foreach ( $transient as $ticket_id => $data ) {
			$storage->delete_cookie( $ticket_id );
		}
	}

	/**
	 * Prepare the data for cart processing.
	 *
	 * Note that most of the data that is processed here is legacy, so you will see very weird and wonky naming.
	 * Make sure when you are making modifications you consider:
	 * - Event Tickets without ET+ additional data
	 * - Event Ticket Plus IAC
	 * - Event Tickets Plus Attendee Registration
	 *
	 * @since 5.1.9
	 *
	 * @param array $request_data Request Data to be prepared.
	 *
	 * @return array
	 */
	public function prepare_data( $request_data ) {
		/**
		 * Filters the Cart data before sending to the prepare method.
		 *
		 * @since 5.1.9
		 *
		 * @param array $request_data The cart data before processing.
		 */
		$request_data = apply_filters( 'tec_tickets_commerce_cart_pre_prepare_data', $request_data );

		if ( empty( $request_data['tribe_tickets_ar_data'] ) ) {
			return [];
		}

		/** @var \Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		$raw_data = $request_data['tribe_tickets_ar_data'];

		// Attempt to JSON decode data if needed.
		if ( ! is_array( $raw_data ) ) {
			$raw_data = stripslashes( $raw_data );
			$raw_data = json_decode( $raw_data, true );
		}

		$raw_data = array_merge( $request_data, $raw_data );

		$data             = [];
		$data['post_id']  = absint( Arr::get( $raw_data, 'tribe_tickets_post_id' ) );
		$data['provider'] = sanitize_text_field( Arr::get( $raw_data, 'tribe_tickets_provider', Module::class ) );
		$data['tickets']  = Arr::get( $raw_data, 'tribe_tickets_tickets' );
		$data['meta']     = Arr::get( $raw_data, 'tribe_tickets_meta', [] );
		$tickets_meta     = Arr::get( $raw_data, 'tribe_tickets', [] );

		$default_ticket = [
			'ticket_id' => 0,
			'quantity'  => 0,
			'optout'    => false,
			'iac'       => 'none',
			'extra'     => [],
		];

		/**
		 * @todo Determine if this should be moved into the Ticket Controller.
		 */
		$data['tickets'] = array_map( static function ( $ticket ) use ( $default_ticket, $handler, $tickets_meta ) {
			if ( empty( $ticket['quantity'] ) ) {
				return false;
			}

			$ticket = array_merge( $default_ticket, $ticket );

			$ticket['quantity'] = (int) $ticket['quantity'];

			if ( $ticket['quantity'] < 0 ) {
				return false;
			}

			if ( ! empty( $tickets_meta[ $ticket['ticket_id'] ]['attendees'] ) ) {
				$ticket['extra']['attendees'] = $tickets_meta[ $ticket['ticket_id'] ]['attendees'];
			}

			$ticket['extra']['optout'] = tribe_is_truthy( $ticket['optout'] );
			unset( $ticket['optout'] );

			$ticket['extra']['iac'] = sanitize_text_field( $ticket['iac'] );
			unset( $ticket['iac'] );

			$ticket['obj'] = \Tribe__Tickets__Tickets::load_ticket_object( $ticket['ticket_id'] );

			if ( ! $handler->is_ticket_readable( $ticket['ticket_id'] ) ) {
				return false;
			}

			return $ticket;
		}, $data['tickets'] );

		// Remove empty items.
		$data['tickets'] = array_filter( $data['tickets'] );

		/**
		 * Filters the Meta on the Data before processing.
		 *
		 * @since 5.1.9
		 *
		 * @param array $meta Meta information on the cart.
		 * @param array $data Data used for the cart.w
		 */
		$data['meta'] = apply_filters( 'tec_tickets_commerce_cart_prepare_data_meta', $data['meta'], $data );

		/**
		 * Filters the Cart data before sending to to the Cart Repository.
		 *
		 * @since 5.1.9
		 *
		 * @param array $data The cart data after processing.
		 */
		return apply_filters( 'tec_tickets_commerce_cart_prepare_data', $this->get_repository()->prepare_data( $data ) );
	}

	/**
	 * Prepares the data from the Tickets form.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function parse_request() {
		// When it's not the current page we just bail.
		if ( ! $this->is_current_page() ) {
			return false;
		}

		$data = $this->prepare_data( $_POST );

		/**
		 * Hook to inject behavior before cart is processed, if you need to change the data that will be used, you
		 * should look into `tec_tickets_commerce_cart_prepare_data`.
		 *
		 * @since 5.1.9
		 *
		 * @param array $data Data used to process the cart.
		 */
		do_action( 'tec_tickets_commerce_cart_before_process', $data );

		$processed = $this->process( $data );

		/**
		 * Hook to inject behavior after cart is processed.
		 *
		 * @since 5.1.9
		 *
		 * @param array $data      Data used to process the cart.
		 * @param bool  $processed Whether or not we processed the data.
		 */
		do_action( 'tec_tickets_commerce_cart_after_process', $data, $processed );

		if ( static::REDIRECT_MODE === $this->get_mode() ) {
			$redirect_url = tribe( Checkout::class )->get_url();

			/**
			 * Filter the base redirect URL for cart to checkout.
			 *
			 * @since 5.2.0
			 *
			 * @param string $redirect_url Redirect URL.
			 * @param array  $data         Data that we just processed on the cart.
			 */
			$redirect_url = apply_filters( 'tec_tickets_commerce_cart_to_checkout_redirect_url_base', $redirect_url, $data );

			if (
				! isset( $_COOKIE[ $this->get_cart_hash() ] )
				|| ! $_COOKIE[ $this->get_cart_hash() ]
			) {
				$redirect_url = add_query_arg( [ static::$cookie_query_arg => $this->get_cart_hash() ], $redirect_url );
			}

			/**
			 * Which url it redirects after the processing of the cart.
			 *
			 * @since 5.1.9
			 *
			 * @param string $redirect_url Which url we will direct after processing the cart. Defaults to Checkout page.
			 * @param array  $data         Data that we just processed on the cart.
			 */
			$redirect_url = apply_filters( 'tec_tickets_commerce_cart_to_checkout_redirect_url', $redirect_url, $data );

			if ( null !== $redirect_url ) {
				wp_safe_redirect( $redirect_url );
				tribe_exit();
			}
		}

		return true;
	}

	/**
	 * Process a given cart data into this cart instance.
	 *
	 * @since 5.1.9
	 *
	 * @param array $data
	 *
	 * @return array|boolean Boolean true when it was a success or an array of errors.
	 */
	public function process( array $data = [] ) {
		if ( empty( $data ) ) {
			return false;
		}

		// Before we start we clear the existing cart.
		return $this->get_repository()->process( $data );
	}

}