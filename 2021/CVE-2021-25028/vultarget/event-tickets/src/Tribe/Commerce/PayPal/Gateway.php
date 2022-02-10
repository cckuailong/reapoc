<?php

class Tribe__Tickets__Commerce__PayPal__Gateway {

	/**
	 * @var string
	 */
	public $base_url = 'https://www.paypal.com';
	/**
	 * @var string
	 */
	public $cart_url = 'https://www.paypal.com/cgi-bin/webscr';

	/**
	 * @var string
	 */
	public $sandbox_base_url = 'https://www.sandbox.paypal.com';

	/**
	 * @var string
	 */
	public $sandbox_cart_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	/**
	 * @var string
	 */
	public $identity_token;

	/**
	 * @var array
	 */
	protected $raw_transaction_data = [];

	/**
	 * @var array
	 */
	protected $transaction_data;

	/**
	 * @var array
	 */
	protected $optouts = [];

	/**
	 * @var string
	 */
	public static $invoice_cookie_name = 'event-tickets-tpp-invoice';

	/**
	 * @var Tribe__Tickets__Commerce__PayPal__Notices
	 */
	protected $notices;

	/**
	 * @var \Tribe__Tickets__Commerce__PayPal__Handler__Interface
	 */
	protected $handler;

	/**
	 * @var int The invoice number expiration time in seconds.
	 */
	protected $invoice_expiration_time;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Gateway constructor.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Commerce__PayPal__Notices $notices
	 */
	public function __construct( Tribe__Tickets__Commerce__PayPal__Notices $notices ) {
		$this->identity_token = tribe_get_option( 'ticket-paypal-identity-token' );
		$this->notices = $notices;

		$this->invoice_expiration_time = DAY_IN_SECONDS;
	}

	/**
	 * Set up hooks for the gateway
	 *
	 * @since 4.7
	 */
	public function hook() {
		add_action( 'template_redirect', [ $this, 'add_to_cart' ] );
	}

	/**
	 * Handles adding tickets to cart.
	 *
	 * @since 4.7
	 */
	public function add_to_cart() {
		global $post;

		/**
		 * Action before adding to cart.
		 *
		 * @since 4.9
		 *
		 * @param array $post_data The $_POST superglobal.
		 */
		do_action( 'tribe_tickets_commerce_paypal_gateway_pre_add_to_cart', $_POST );

		// Bail if this isn't a Tribe Commerce PayPal ticket.
		if (
			(
				empty( $_POST['tribe_tickets'] )
				&& empty( $_POST['product_id'] )
			)
			|| empty( $_POST['provider'] )
			|| 'Tribe__Tickets__Commerce__PayPal__Main' !== $_POST['provider']
		) {
			return;
		}

		$cart_url      = $this->get_cart_url( '_cart' );
		$post_url      = get_permalink( $post );
		$currency_code = trim( tribe_get_option( 'ticket-commerce-currency-code' ) );

		if ( isset( $_POST['tribe_tickets'] ) ) {
			$product_ids = wp_list_pluck( $_POST['tribe_tickets'], 'ticket_id' );
		} elseif ( isset( $_POST['product_id'] ) ) {
			$product_ids = (array) $_POST['product_id'];
		}

		$notify_url = tribe_get_option( 'ticket-paypal-notify-url', home_url() );

		/**
		 * Filters the Notify URL.
		 *
		 * The `notify_url` argument is an IPN only argument specifying the URL PayPal should
		 * use to POST the payment information.
		 *
		 * @link  https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
		 *
		 * @see   Tribe__Tickets__Commerce__PayPal__Handler__IPN::check_response()
		 *
		 * @since 4.7
		 *
		 * @param string  $notify_url  The Notify URL.
		 * @param WP_Post $post        The post tickets are associated with.
		 * @param array   $product_ids An array of ticket post IDs that are being added to the cart.
		 */
		$notify_url = apply_filters( 'tribe_tickets_commerce_paypal_notify_url', $notify_url, $post, $product_ids );

		$custom_args = [
			'user_id'       => get_current_user_id(),
			'tribe_handler' => 'tpp',
			'pid'           => $post->ID,
		];

		$invoice_number = $this->set_invoice_number();

		$custom_args['invoice'] = $invoice_number;

		/**
		 * Filters the custom arguments that will be sent ot PayPal.
		 *
		 * @since 4.7
		 *
		 * @param array   $custom_args PayPal URL's `custom` argument.
		 * @param WP_Post $post        The post tickets are associated with.
		 * @param array   $product_ids An array of ticket post IDs that are being added to the cart.
		 */
		$custom_args = apply_filters( 'tribe_tickets_commerce_paypal_custom_args', $custom_args, $post, $product_ids );

		$custom = Tribe__Tickets__Commerce__PayPal__Custom_Argument::encode( $custom_args );

		$args = [
			'cmd'           => '_cart',
			'add'           => 1,
			'business'      => urlencode( trim( tribe_get_option( 'ticket-paypal-email' ) ) ),
			'bn'            => 'ModernTribe_SP',
			'notify_url'    => urlencode( trim( $notify_url ) ),
			'shopping_url'  => urlencode( $post_url ),
			'return'        => $this->get_success_page_url( $invoice_number ),
			'currency_code' => $currency_code ? $currency_code : 'USD',
			'custom'        => $custom,
			/*
			 * We're not sending an invoice anymore.
			 * It would mess up the cart cookies and we ended up not using it.
			 */
		];

		/** @var Tribe__Tickets__Commerce__PayPal__Cart__Interface $cart */
		$cart = tribe( 'tickets.commerce.paypal.cart' );
		$cart->set_id( $invoice_number );

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		foreach ( $product_ids as $ticket_id ) {
			$ticket = $paypal->get_ticket( $post->ID, $ticket_id );

			// Skip the product if the ticket no longer exists.
			if ( ! $ticket ) {
				continue;
			}

			$quantity = 0;

			if ( isset( $_POST['tribe_tickets'][ $ticket_id ]['quantity'] ) ) {
				$quantity = absint( $_POST['tribe_tickets'][ $ticket_id ]['quantity'] );
			} elseif ( isset( $_POST["quantity_{$ticket_id}"] ) ) {
				$quantity = absint( $_POST["quantity_{$ticket_id}"] );
			}

			// skip if the ticket in no longer in stock or is not sellable
			if (
				! $ticket->is_in_stock()
				|| ! $ticket->date_in_range()
			) {
				continue;
			}

			$inventory    = $ticket->inventory();
			$is_unlimited = $inventory === -1;

			// if the requested amount is greater than remaining, use remaining instead
			if ( ! $is_unlimited && $quantity > $inventory ) {
				$quantity = $inventory;
			}

			// if the ticket doesn't have a quantity, skip it
			if ( empty( $quantity ) ) {
				continue;
			}

			$args['quantity']    = $quantity;
			$args['amount']      = $ticket->price;
			$args['item_number'] = "{$post->ID}:{$ticket->ID}";
			$args['item_name']   = urlencode( wp_kses_decode_entities( $this->get_product_name( $ticket, $post ) ) );

			$cart->add_item( $ticket->ID, $quantity );

			// we can only submit one product at a time. Bail if we get to here because we have a product
			// with a requested quantity
			break;
		}

		// If there isn't a quantity at all, then there's nothing to purchase. Redirect with an error
		if ( empty( $args['quantity'] ) || ! is_numeric( $args['quantity'] ) || (int) $args['quantity'] < 1 ) {
			/**
			 * @see Tribe__Tickets__Commerce__PayPal__Errors::error_code_to_message for error codes
			 */
			wp_safe_redirect( add_query_arg( [ 'tpp_error' => 103 ], $post_url ) );
			die;
		}

		$cart->save();

		/**
		 * Filters the arguments passed to PayPal while adding items to the cart
		 *
		 * @since 4.7
		 *
		 * @param array   $args PayPal Add To Cart URL arguments.
		 * @param array   $data POST data from Buy Now submission.
		 * @param WP_Post $post Post object that has tickets attached to it.
		 */
		$args = apply_filters( 'tribe_tickets_commerce_paypal_add_to_cart_args', $args, $_POST, $post );

		$cart_url = add_query_arg( $args, $cart_url );

		/**
		 * To allow the Invoice cookie to apply we have to redirect to a page on the same domain
		 * first.
		 * The redirection is handled in the `Tribe__Tickets__Redirections::maybe_redirect` class
		 * on the `wp_loaded` action.
		 *
		 * @see Tribe__Tickets__Redirections::maybe_redirect
		 */
		$url = add_query_arg(
			[ 'tribe_tickets_redirect_to' => rawurlencode( $cart_url ) ],
			home_url()
		);

		/**
		 * Filters the add to cart redirect
		 *
		 * @since 4.9
		 *
		 * @param string $url
		 * @param string $cart_url
		 * @param array $post_data
		 */
		$url = apply_filters( 'tribe_tickets_commerce_paypal_gateway_add_to_cart_redirect', $url, $cart_url, $_POST );

		wp_redirect( $url );
		die;
	}

	/**
	 * Parses PayPal transaction data into a more organized structure
	 *
	 * @since 4.7
	 *
	 * @link https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
	 *
	 * @param array $transaction Transaction data from PayPal in key/value pairs
	 *
	 * @return array|false The parsed transaction data or `false` if the transaction could not be processed for any reason.
	 */
	public function parse_transaction( array $transaction ) {
		if ( ! empty( $transaction['custom'] ) ) {
			$decoded_custom = Tribe__Tickets__Commerce__PayPal__Custom_Argument::decode( $transaction['custom'], true );

			if ( empty( $decoded_custom['tribe_handler'] ) || 'tpp' !== $decoded_custom['tribe_handler'] ) {
				return false;
			}
		}

		if ( $this->handler instanceof Tribe__Tickets__Commerce__PayPal__Handler__Invalid_PDT ) {
			$this->handler->save_transaction();

			return false;
		}

		$item_indexes = [
			'item_number',
			'item_name',
			'quantity',
			'mc_handling',
			'mc_shipping',
			'tax',
			'mc_gross_',
		];

		$item_indexes_regex = '/(' . implode( '|', $item_indexes ) . ')(\d)/';

		$data = [
			'items' => [],
		];

		foreach ( $transaction as $key => $value ) {
			if ( ! preg_match( $item_indexes_regex, $key, $matches ) ) {
				$data[ $key ] = $value;
				continue;
			}

			$index = $matches[2];
			$name  = trim( $matches[1], '_' );

			if ( ! isset( $data['items'][ $index ] ) ) {
				$data['items'][ $index ] = [];
			}

			$data['items'][ $index ][ $name ] = $value;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		foreach ( $data['items'] as &$item ) {
			if ( ! isset( $item['item_number'] ) ) {
				continue;
			}

			list( $item['post_id'], $item['ticket_id'] ) = explode( ':', $item['item_number'] );

			$item['ticket'] = $paypal->get_ticket( $item['post_id'], $item['ticket_id'] );
		}

		return $data;
	}

	/**
	 * Sets transaction data from PayPal as a class property
	 *
	 * @since 4.7
	 *
	 * @param array $data An array of parsed transaction data.
	 *
	 * @see \Tribe__Tickets__Commerce__PayPal__Gateway::parse_transaction()
	 * @see \Tribe__Tickets__Commerce__PayPal__Gateway::set_raw_transaction_data()
	 */
	public function set_transaction_data( $data ) {
		/**
		 * Filters the transaction data as it is being set
		 *
		 * @since 4.7
		 *
		 * @param array $data
		 */
		$this->transaction_data = apply_filters( 'tribe_tickets_commerce_paypal_set_transaction_data', $data );
	}

	/**
	 * Gets PayPal transaction data
	 *
	 * @since 4.7
	 *
	 * @param array $data
	 */
	public function get_transaction_data() {
		/**
		 * Filters the transaction data as it is being retrieved
		 *
		 * @since 4.7
		 *
		 * @param array $transaction_data
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_get_transaction_data', $this->transaction_data );
	}

	/**
	 * Gets the full PayPal product name
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket Ticket whose name is being generated
	 * @param WP_Post $post Post that the tickets are attached to
	 *
	 * @return string
	 */
	public function get_product_name( $ticket, $post ) {
		$title = get_the_title( $post->ID );
		$name  = $ticket->name;

		$product_name = "{$name} - {$title}";

		/**
		 * Filters the product name for PayPal's cart
		 *
		 * @since 4.7
		 *
		 * @param string $product_name
		 * @param Tribe__Tickets__Ticket_Object
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_product_name', $product_name, $ticket );
	}

	/**
	 * Sets an invoice number (generating it if one doesn't exist) in the cookies.
	 *
	 * @since 4.7
	 *
	 * @return string The invoice alpha-numeric identifier
	 */
	public function set_invoice_number() {
		$invoice = $this->get_invoice_number();

		// set the cookie (if it was already set, it'll extend the lifetime)
		$secure = 'https' === parse_url( home_url(), PHP_URL_SCHEME );
		setcookie( self::$invoice_cookie_name, $invoice, time() + $this->invoice_expiration_time, COOKIEPATH, COOKIE_DOMAIN, $secure );
		set_transient( $this->invoice_transient_name( $invoice ), '1', $this->invoice_expiration_time );

		return $invoice;
	}

	/**
	 * Purges an invoice cookie
	 *
	 * @since 4.7
	 */
	public function reset_invoice_number() {
		if ( empty( $_COOKIE[ self::$invoice_cookie_name ] ) ) {
			return;
		}

		$invoice_number = $_COOKIE[ self::$invoice_cookie_name ];
		unset( $_COOKIE[ self::$invoice_cookie_name ] );
		$deleted = delete_transient( $this->invoice_transient_name( $invoice_number ) );

		if ( ! headers_sent() ) {
			$secure = 'https' === parse_url( home_url(), PHP_URL_SCHEME );
			setcookie( self::$invoice_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure );
		}
	}

	/**
	 * Returns the PayPal cart URL
	 *
	 * @since 4.7
	 *
	 * @param string $path An optional path to append to the URL
	 *
	 * @return string
	 */
	public function get_cart_url( $path = '' ) {
		$path = '/' . ltrim( $path, '/' );

		return tribe_get_option( 'ticket-paypal-sandbox' )
			? $this->sandbox_cart_url . $path
			: $this->cart_url . $path;
	}

	/**
	 * Get the PayPal cart API URL.
	 *
	 * @since 4.11.0
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return string The PayPal cart API URL.
	 */
	public function get_paypal_cart_api_url( $post_id ) {
		if ( empty( $post_id ) || headers_sent() ) {
			return home_url( '/' );
		}

		$cart_url      = $this->get_cart_url( '_cart' );
		$post          = get_post( $post_id );
		$post_url      = get_permalink( $post_id );
		$currency_code = trim( tribe_get_option( 'ticket-commerce-currency-code' ) );
		$email         = trim( tribe_get_option( 'ticket-paypal-email' ) );
		$notify_url    = tribe_get_option( 'ticket-paypal-notify-url', home_url() );

		$custom_args = [
			'user_id'       => get_current_user_id(),
			'tribe_handler' => 'tpp',
			'pid'           => $post->ID,
			'oo'            => [],
		];

		$invoice_number = $this->set_invoice_number();

		$custom_args['invoice'] = $invoice_number;

		/** @var Tribe__Tickets__Commerce__PayPal__Cart__Unmanaged $cart */
		$cart = tribe( 'tickets.commerce.paypal.cart' );
		$cart->set_id( $invoice_number );

		$items = $cart->get_items();

		if ( empty( $items ) ) {
			return '';
		}

		$product_ids = array_keys( $items );

		/**
		 * Filters the notify URL.
		 *
		 * The `notify_url` argument is an IPN only argument specifying the URL PayPal should
		 * use to POST the payment information.
		 *
		 * @since 4.7
		 *
		 * @see  Tribe__Tickets__Commerce__PayPal__Handler__IPN::check_response()
		 * @link https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
		 *
		 * @param string $notify_url
		 * @param WP_Post $post The post tickets are associated with.
		 * @param array $product_ids An array of ticket post IDs that are being added to the cart.
		 */
		$notify_url = apply_filters( 'tribe_tickets_commerce_paypal_notify_url', $notify_url, $post, $product_ids );
		$notify_url = trim( $notify_url );

		/**
		 * Filters the custom arguments that will be sent ot PayPal.
		 *
		 * @since 4.7
		 *
		 * @param array   $custom_args
		 * @param WP_Post $post        The post tickets are associated with.
		 * @param array   $product_ids An array of ticket post IDs that are being added to the cart.
		 */
		$custom_args = apply_filters( 'tribe_tickets_commerce_paypal_custom_args', $custom_args, $post, $product_ids );

		$args = [
			'cmd'              => '_cart',
			'business'         => urlencode( $email ),
			'bn'               => 'ModernTribe_SP',
			'notify_url'       => urlencode( $notify_url ),
			'shopping_url'     => urlencode( $post_url ),
			'return'           => $this->get_success_page_url( $invoice_number ),
			'currency_code'    => $currency_code ?: 'USD',
			'custom'           => $custom_args,
			// tribe_redirected is needed because TEC will stop a redirect on the main events page.
			'tribe_redirected' => 1,
			/*
			 * We're not sending an invoice anymore.
			 * It would mess up the cart cookies and we ended up not using it.
			 */
		];

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		$cart_items = [];

		$optout_key = $paypal->attendee_optout_key;

		foreach ( $items as $ticket_id => $item ) {
			$optout = false;

			if ( is_array( $item ) ) {
				$quantity = $item['quantity'];

				if ( ! empty( $item[ $optout_key ] ) ) {
					$optout = $item[ $optout_key ];
				}
			} else {
				$quantity = $item;
			}

			$ticket = $paypal->get_ticket( $post_id, $ticket_id );

			// Skip the ticket if it no longer exists.
			if ( ! $ticket ) {
				continue;
			}

			// Skip if the ticket in no longer in stock or is not sellable.
			if (
				! $ticket->is_in_stock()
				|| ! $ticket->date_in_range()
			) {
				continue;
			}

			$inventory    = $ticket->inventory();
			$is_unlimited = -1 === $inventory;

			// If the requested amount is greater than remaining, use remaining instead.
			if ( ! $is_unlimited && $inventory < $quantity ) {
				$quantity = $inventory;
			}

			// If the ticket doesn't have a quantity, skip it.
			if ( empty( $quantity ) ) {
				continue;
			}

			// @todo Figure out logic for storing optout for PP.

			$args['custom']['oo'][ 'ticket_' . $ticket->ticket_id ] = $optout;

			$cart_items[] = [
				'quantity'    => $quantity,
				'amount'      => $ticket->price,
				'item_number' => "{$post_id}:{$ticket_id}",
				'item_name'   => urlencode( wp_kses_decode_entities( $this->get_product_name( $ticket, $post ) ) ),
			];
		}

		// If there isn't a quantity at all, then there's nothing to purchase. Redirect with an error.
		if ( empty( $cart_items ) ) {
			/**
			 * @see Tribe__Tickets__Commerce__PayPal__Errors::error_code_to_message for error codes.
			 */
			return add_query_arg( [ 'tpp_error' => 103 ], $post_url );
		}

		$item_counter = 1;

		$has_multiple_items = 1 < count( $cart_items );

		// Whether we use add or upload depends on if we have multiple items.
		if ( $has_multiple_items ) {
			$args['upload'] = 1;
		} else {
			$args['add'] = 1;
		}

		foreach ( $cart_items as $cart_item ) {
			$arg_modifier = '';

			if ( $has_multiple_items ) {
				$arg_modifier = '_' . $item_counter;
			}

			$args[ 'quantity' . $arg_modifier ]    = $cart_item['quantity'];
			$args[ 'amount' . $arg_modifier ]      = $cart_item['amount'];
			$args[ 'item_number' . $arg_modifier ] = $cart_item['item_number'];
			$args[ 'item_name' . $arg_modifier ]   = $cart_item['item_name'];

			$item_counter ++;
		}

		$args['custom'] = Tribe__Tickets__Commerce__PayPal__Custom_Argument::encode( $args['custom'] );

		/**
		 * Filters the arguments passed to PayPal while adding items to the cart.
		 *
		 * @since 4.7
		 *
		 * @param array   $args PayPal Add To Cart URL arguments.
		 * @param array   $data POST data from Buy Now submission.
		 * @param WP_Post $post Post object that has tickets attached to it.
		 */
		$args = apply_filters( 'tribe_tickets_commerce_paypal_add_to_cart_args', $args, [], $post );

		$cart_url = add_query_arg( $args, $cart_url );

		/**
		 * To allow the Invoice cookie to apply we have to redirect to a page on the same domain
		 * first.
		 * The redirection is handled in the `Tribe__Tickets__Redirections::maybe_redirect` class
		 * on the `wp_loaded` action.
		 *
		 * @see Tribe__Tickets__Redirections::maybe_redirect
		 */
		$url_args = [
			// tribe_redirected is needed because TEC will stop a redirect on the main events page.
			'tribe_redirected'          => 1,
			'tribe_tickets_post_id'     => $post_id,
			'tribe_tickets_redirect_to' => rawurlencode( $cart_url ),
		];

		$url = add_query_arg( $url_args, home_url( '/' ) );

		/**
		 * Filters the add to cart redirect.
		 *
		 * @since 4.9
		 *
		 * @param string $url
		 * @param string $cart_url
		 * @param array  $post_data
		 */
		$url = apply_filters( 'tribe_tickets_commerce_paypal_gateway_add_to_cart_redirect', $cart_url, $cart_url, [] );

		return $url;
	}

	/**
	 * Returns the PyaPal base URL
	 *
	 * @since 4.7
	 *
	 * @param string $path An optional path to append to the URL
	 *
	 * @return string
	 */
	public function get_base_url( $path = '' ) {
		$path = '/' . ltrim( $path, '/' );

		return tribe_get_option( 'ticket-paypal-sandbox' )
			? $this->sandbox_base_url . $path
			: $this->base_url . $path;
	}

	/**
	 * Returns the PayPal URL to a transaction details.
	 *
	 * @since 4.7
	 *
	 * @param string $transaction The transaction alpha-numeric identifier
	 *
	 * @return string
	 */
	public function get_transaction_url( $transaction ) {
		return $this->get_base_url( "activity/payment/{$transaction}" );
	}

	/**
	 * Builds the correct handler depending on the request type and options.
	 *
	 * @since 4.7
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Handler__Interface The handler instance.
	 */
	public function build_handler() {
		$handler = $this->get_handler_slug();

		if ( null === $this->handler ) {
			if ( $handler === 'pdt' && ! empty( $_GET['tx'] ) ) {
				// looks like a PDT request
				if ( ! empty( $this->identity_token ) ) {
					// if there's an identity token set we handle payment confirmation with PDT
					$this->handler = tribe( 'tickets.commerce.paypal.handler.pdt' );
				} else {
					// if there is not an identity token set we log a missed transaction and show a notice
					$this->notices->show_missing_identity_token_notice();
					$this->handler = new Tribe__Tickets__Commerce__PayPal__Handler__Invalid_PDT( $_GET['tx'] );
				}
			} else {
				// we use IPN otherwise
				$this->handler = tribe( 'tickets.commerce.paypal.handler.ipn' );
			}
		}

		return $this->handler;
	}

	/**
	 * Returns the invoice number reading it from the cookie or generating a new one.
	 *
	 * @since 4.7
	 *
	 * @param bool $generate Whether to generate a new invoice number if not found.
	 *
	 * @return string
	 */
	public function get_invoice_number( $generate = true ) {
		$invoice_length = 12;

		$invoice = null;

		if (
			! empty( $_COOKIE[ self::$invoice_cookie_name ] )
			&& strlen( $_COOKIE[ self::$invoice_cookie_name ] ) === $invoice_length
		) {
			$invoice = $_COOKIE[ self::$invoice_cookie_name ];

			$invoice_transient = get_transient( $this->invoice_transient_name( $invoice ) );

			if ( empty( $invoice_transient ) ) {
				$invoice = false;
			}
		}

		if ( empty( $invoice ) && $generate ) {
			$invoice = wp_generate_password( $invoice_length, false );
		}

		/**
		 * Filters the invoice number used for PayPal.
		 *
		 * @since 4.11.0
		 *
		 * @param string $invoice Invoice number.
		 */
		$invoice = apply_filters( 'tribe_tickets_commerce_paypal_invoice_number', $invoice );

		return $invoice;
	}

	/**
	 * Returns the slug of the default payment handler.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_handler_slug() {
		/**
		 * Filters which PayPal payment method should be used.
		 *
		 * @since 4.7
		 *
		 * @param string $handler One of `pdt` or `ipn`
		 */
		$handler = apply_filters( 'tribe_tickets_commerce_paypal_handler', 'ipn' );

		$handler = in_array( $handler, [ 'pdt', 'ipn' ] ) ? $handler : 'ipn';

		return $handler;
	}

	/**
	 * Returns the URL to the PayPal Settings page.
	 *
	 * @since 4.7
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function get_settings_url( $path = '' ) {
		return $this->get_base_url( '/customerprofileweb' . ltrim( $path, '/' ) );
	}

	/**
	 * Returns the success page URL.
	 *
	 * Will default to the `home_url` if the Success page is not set or wrong.
	 *
	 * @since 4.7
	 * @since 4.11.0 Added $invoice_number parameter to add to success page.
	 *
	 * @param string|null $invoice_number Invoice number.
	 *
	 * @return string
	 */
	public function get_success_page_url( $invoice_number = null ) {
		$success_page_id = tribe_get_option( 'ticket-paypal-success-page', false );

		$success_page_url = home_url();

		if ( ! empty( $success_page_id ) ) {
			$success_page = get_post( $success_page_id );

			if ( $success_page instanceof WP_Post && 'page' === $success_page->post_type ) {
				$success_page_url = get_permalink( $success_page->ID );
			}
		}

		$success_page_url = add_query_arg( 'tribe-tpp-order', $invoice_number, $success_page_url );

		return $success_page_url;
	}

	/**
	 * Sets the raw transaction data.
	 *
	 * @since 4.7
	 *
	 * @param array $data
	 */
	public function set_raw_transaction_data( array $data ) {
		$this->raw_transaction_data = $data;
	}

	/**
	 * Returns the raw transaction data.
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function get_raw_transaction_data() {
		return $this->raw_transaction_data;
	}

	/**
	 * Returns the name of the transient corresponding to an invoice number.
	 *
	 * @since 4.7.4
	 *
	 * @param string $invoice_number
	 *
	 * @return string
	 */
	protected function invoice_transient_name( $invoice_number ) {
		return 'tpp_invoice_' . md5( $invoice_number );
	}
}
