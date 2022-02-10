<?php

/**
 * Class Tribe__Tickets__Commerce__Cart
 */
class Tribe__Tickets__Commerce__Cart {

	/**
	 * Add hooks needed for cart to function.
	 *
	 * @since 4.11.0
	 */
	public function hook() {
		add_action( 'wp', [ $this, 'process_cart' ] );
	}

	/**
	 * Process cart on any given (non-admin) page.
	 *
	 * @since 4.11.0
	 */
	public function process_cart() {
		if ( empty( $_POST['tribe_tickets_ar'] ) || is_admin() ) {
			return;
		}

		$data = $_POST;

		if ( ! empty( $data['tribe_tickets_ar_data'] ) ) {
			$data = $data['tribe_tickets_ar_data'];

			// Attempt to JSON decode data if needed.
			if ( ! is_array( $data ) ) {
				$data = stripslashes( $data );
				$data = json_decode( $data, true );
			}

			$data = array_merge( $_POST, $data );
		}

		$post_id  = isset( $data['tribe_tickets_post_id'] ) ? absint( $data['tribe_tickets_post_id'] ) : null;
		$provider = isset( $data['tribe_tickets_provider'] ) ? sanitize_text_field( $data['tribe_tickets_provider'] ) : tribe_get_request_var( tribe_tickets_get_provider_query_slug() );
		$tickets  = isset( $data['tribe_tickets_tickets'] ) ? $data['tribe_tickets_tickets'] : null;
		$meta     = isset( $data['tribe_tickets_meta'] ) ? $data['tribe_tickets_meta'] : null;

		$tribe_commerce_providers = [
			'tpp',
			'tribe-commerce',
			'tribe_tpp_attendees',
			'Tribe__Tickets__Commerce__PayPal__Main',
		];

		$is_tribe_commerce = in_array( $provider, $tribe_commerce_providers, true );

		// Simplify provider for Tribe Commerce.
		if ( $is_tribe_commerce ) {
			$provider = 'tribe-commerce';
		}

		// On AR Page, we use replace logic, not additive.
		$is_ar_modal = empty( $_POST['tribe_tickets_ar_page'] );
		$additive    = $is_ar_modal && ! $is_tribe_commerce;

		if ( $is_tribe_commerce ) {
			if ( null === $post_id ) {
				if ( ! empty( $_GET['tribe_tickets_post_id'] ) ) {
					// Get post ID from current URL parameter.
					$post_id = absint( $_GET['tribe_tickets_post_id'] );
				} else {
					// Detect post ID for Tribe Commerce from the first ticket (no cart, we only support one post at a time).
					$ticket_ids = wp_list_pluck( $tickets, 'ticket_id' );
					$ticket_ids = array_filter( array_unique( $ticket_ids ) );

					if ( ! empty( $ticket_ids ) ) {
						$ticket_id = current( $ticket_ids );

						$ticket = Tribe__Tickets__Tickets::load_ticket_object( $ticket_id );

						if ( $ticket ) {
							$post_id = $ticket->get_event_id();
						}
					}
				}
			}

			if ( null === $meta && $is_ar_modal ) {
				$meta = [];
			}
		}

		// We only update tickets from the modal, not the AR page right now.
		if ( ! $is_ar_modal ) {
			$tickets = null;
		}

		$response = $this->update( [
			'post_id'  => $post_id,
			'provider' => $provider,
			'tickets'  => $tickets,
			'meta'     => $meta,
			'additive' => $additive,
		] );

		// Tribe Commerce needs to be redirected to the checkout URL from here.
		if ( $is_tribe_commerce ) {
			$data = $this->get( [
				'post_id'  => $post_id,
				'provider' => $provider,
			] );

			// Redirect to AR page if we need to.
			if (
				isset( $data['is_stored_meta_up_to_date'] )
				&& empty( $data['is_stored_meta_up_to_date'] )
				&& ! empty( $data['attendee_registration_url'] )
			) {
				wp_redirect( $data['attendee_registration_url'] );
				die();
			}

			// Redirect to Tribe Commerce checkout URL.
			if ( ! empty( $data['checkout_url'] ) ) {
				wp_redirect( $data['checkout_url'] );
				die();
			}
		}
	}

	/**
	 * Get cart data.
	 *
	 * @since 4.11.0
	 *
	 * @param array $args {
	 *      List of arguments for getting cart.
	 *
	 *      @type int|null    $post_id   Post ID.
	 *      @type string|null $provider  Provider to get cart for.
	 *      @type array|null  $providers List of providers to get cart for.
	 * }
	 *
	 * @return array Cart data.
	 */
	public function get( $args ) {
		$post_id   = isset( $args['post_id'] ) ? $args['post_id'] : null;
		$provider  = isset( $args['provider'] ) ? $args['provider'] : null;
		$providers = isset( $args['providers'] ) ? $args['providers'] : [];

		if ( [] === $providers && null !== $provider ) {
			$providers = (array) $provider;
		}

		$data = [
			'tickets' => [],
			'meta'    => [],
		];

		/** @var Tribe__Tickets__Editor__Configuration $editor_config */
		$editor_config = tribe( 'tickets.editor.configuration' );

		// Get list of providers.
		$all_providers = $editor_config->get_providers();

		$found_providers = [];

		/** @var Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		// Fetch tickets for cart providers.
		foreach ( $all_providers as $provider_data ) {
			/** @var Tribe__Tickets__Tickets $provider_object */
			$provider_object = call_user_func( [ $provider_data['class'], 'get_instance' ] );

			$provider_key             = $provider_object->orm_provider;
			$provider_attendee_object = $provider_object->attendee_object;

			// Skip provider if we only want specific ones.
			if (
				[] !== $providers
				&& ! in_array( $provider_key, $providers, true )
				&& ! in_array( $provider_attendee_object, $providers, true )
				&& ! in_array( $provider_data['class'], $providers, true )
			) {
				// Backcompat for tpp usage.
				if ( 'tribe-commerce' !== $provider_key || ! in_array( 'tpp', $providers, true ) ) {
					continue;
				}
			}

			// Fetch tickets for provider cart.
			$cart_tickets = [];

			/**
			 * Get list of tickets in the cart for provider.
			 *
			 * The dynamic portion of the hook name, `$provider_key`, refers to the cart provider.
			 *
			 * @since 4.11.0
			 *
			 * @param array $cart_tickets List of tickets in the cart.
			 */
			$cart_tickets = apply_filters( 'tribe_tickets_commerce_cart_get_tickets_' . $provider_key, $cart_tickets );

			$default_ticket = [
				'ticket_id' => 0,
				'quantity'  => 0,
				'post_id'   => 0,
				'optout'    => 0,
				'iac'       => 'none',
			];

			foreach ( $cart_tickets as $ticket ) {
				$ticket = array_merge( $default_ticket, $ticket );

				// Enforce types.
				$ticket['ticket_id'] = absint( $ticket['ticket_id'] );
				$ticket['quantity']  = absint( $ticket['quantity'] );
				$ticket['post_id']   = absint( $ticket['post_id'] );
				$ticket['optout']    = (int) filter_var( $ticket['optout'], FILTER_VALIDATE_BOOLEAN );

				$ticket_id = $ticket['ticket_id'];
				$quantity  = $ticket['quantity'];

				// Skip ticket if it has no quantity or is not accessible.
				if ( $quantity < 1 || ! $handler->is_ticket_readable( $ticket_id ) ) {
					continue;
				}

				$data['tickets'][] = $ticket;

				if ( ! in_array( $provider_key, $found_providers, true ) ) {
					$found_providers[] = $provider_key;
				}
			}
		}

		// Set providers as the ones we found tickets for.
		if ( [] === $providers ) {
			$providers = $found_providers;
		}

		// Fetch meta for cart.
		$cart_meta = [];

		/**
		 * Get list of ticket meta in the cart.
		 *
		 * @since 4.11.0
		 *
		 * @param array $cart_meta List of ticket meta in the cart.
		 * @param array $tickets   List of tickets in the cart.
		 */
		$cart_meta = apply_filters( 'tribe_tickets_commerce_cart_get_ticket_meta', $cart_meta, $data['tickets'] );

		$data['meta']         = $cart_meta;
		$data['cart_url']     = '';
		$data['checkout_url'] = '';

		if ( ! empty( $data['tickets'] ) ) {
			foreach ( $providers as $cart_provider ) {
				/**
				 * Get cart URL for provider.
				 *
				 * The dynamic portion of the hook name, `$cart_provider`, refers to the cart provider.
				 *
				 * @since 4.11.0
				 *
				 * @param string $cart_url Cart URL.
				 * @param array  $data     Commerce response data to be sent.
				 * @param int    $post_id  Post ID for the cart.
				 */
				$data['cart_url'] = apply_filters( 'tribe_tickets_commerce_cart_get_cart_url_' . $cart_provider, '', $data, $post_id );

				/**
				 * Get checkout URL for provider.
				 *
				 * The dynamic portion of the hook name, `$cart_provider`, refers to the cart provider.
				 *
				 * @since 4.11.0
				 *
				 * @param string $checkout_url Checkout URL.
				 * @param array  $data         Commerce response data to be sent.
				 * @param int    $post_id      Post ID for the cart.
				 */
				$data['checkout_url'] = apply_filters( 'tribe_tickets_commerce_cart_get_checkout_url_' . $cart_provider, '', $data, $post_id );

				// Stop after first provider URLs are set.
				if ( '' !== $data['cart_url'] || '' !== $data['checkout_url'] ) {
					break;
				}
			}
		}

		/**
		 * Get response data for the cart.
		 *
		 * @since 4.11.0
		 *
		 * @param array $data      Cart response data.
		 * @param array $providers List of cart providers.
		 * @param int   $post_id   Post ID for cart.
		 */
		$data = apply_filters( 'tribe_tickets_commerce_cart_get_data', $data, $providers, $post_id );

		return $data;
	}

	/**
	 * Update cart data.
	 *
	 * @since 4.11.0
	 *
	 * @param array $args {
	 *      List of arguments for updating cart.
	 *
	 *      @type int|null    $post_id  Post ID.
	 *      @type string|null $provider Provider to update cart for.
	 *      @type array|null  $tickets  List of tickets to add to cart.
	 *      @type array|null  $meta     List of meta to set.
	 * }
	 *
	 * @return true|WP_Error Successful updates return true and errors are returned as WP_Error.
	 */
	public function update( $args ) {
		$post_id  = isset( $args['post_id'] ) ? $args['post_id'] : null;
		$provider = isset( $args['provider'] ) ? $args['provider'] : null;
		$tickets  = isset( $args['tickets'] ) ? $args['tickets'] : null;
		$meta     = isset( $args['meta'] ) ? $args['meta'] : null;
		$additive = isset( $args['additive'] ) ? (bool) $args['additive'] : true;

		// Update cart quantities.
		if ( null !== $tickets ) {
			$providers = [];
			$defaults  = [
				'ticket_id' => 0,
				'quantity'  => 0,
				'optout'    => 0,
				'provider'  => $provider,
			];

			// Setup tickets.
			foreach ( $tickets as $k => $ticket ) {
				$ticket = array_merge( $defaults, $ticket );

				$ticket['ticket_id'] = absint( $ticket['ticket_id'] );
				$ticket['quantity']  = absint( $ticket['quantity'] );

				// Skip ticket if ticket_id is not set.
				if ( 0 === $ticket['ticket_id'] ) {
					unset( $tickets[ $k ] );

					continue;
				}

				// Update ticket in array for use later.
				$tickets[ $k ] = $ticket;

				// Add provider if not yet added.
				if ( ! isset( $providers[ $ticket['provider'] ] ) ) {
					$providers[ $ticket['provider'] ] = [];
				}

				// Add ticket to provider.
				$providers[ $ticket['provider'] ][] = $ticket;
			}

			try {
				foreach ( $providers as $ticket_provider => $provider_tickets ) {
					/**
					 * Update tickets in cart for provider.
					 *
					 * The dynamic portion of the hook name, `$ticket_provider`, refers to the ticket provider.
					 *
					 * @since 4.11.0
					 *
					 * @param array   $provider_tickets List of tickets with their ID and quantity.
					 * @param int     $post_id          Post ID for the cart.
					 * @param boolean $additive         Whether to add or replace tickets.
					 */
					do_action( 'tribe_tickets_commerce_cart_update_tickets_' . $ticket_provider, $provider_tickets, $post_id, $additive );
				}

				/**
				 * Update tickets in cart.
				 *
				 * @since 4.11.0
				 *
				 * @param array   $tickets  List of tickets with their ID and quantity.
				 * @param string  $provider The cart provider.
				 * @param int     $post_id  Post ID for the cart.
				 * @param boolean $additive Whether to add or replace tickets.
				 */
				do_action( 'tribe_tickets_commerce_cart_update_tickets', $tickets, $provider, $post_id, $additive );
			} catch ( Tribe__REST__Exceptions__Exception $exception ) {
				return new WP_Error( $exception->getCode(), esc_html( $exception->getMessage() ), [ 'status' => $exception->getStatus() ] );
			}
		}

		// Update ticket meta.
		if ( null !== $meta ) {
			// Setup meta.
			$defaults = [
				'ticket_id' => 0,
				'provider'  => $provider,
				'items'     => [],
			];

			foreach ( $meta as $k => $ticket_meta ) {
				$ticket_meta = array_merge( $defaults, $ticket_meta );

				$ticket_meta['ticket_id'] = absint( $ticket_meta['ticket_id'] );

				$meta[ $k ] = $ticket_meta;
			}

			try {
				/**
				 * Update ticket meta from Attendee Registration.
				 *
				 * @since 4.11.0
				 *
				 * @param array   $meta     List of meta for each ticket to be saved for Attendee Registration.
				 * @param array   $tickets  List of tickets with their ID and quantity.
				 * @param string  $provider The cart provider.
				 * @param int     $post_id  Post ID for the cart.
				 * @param boolean $additive Whether to add or replace meta.
				 */
				do_action( 'tribe_tickets_commerce_cart_update_ticket_meta', $meta, $tickets, $provider, $post_id, $additive );
			} catch ( Tribe__REST__Exceptions__Exception $exception ) {
				return new WP_Error( $exception->getCode(), esc_html( $exception->getMessage() ), [ 'status' => $exception->getStatus() ] );
			}
		}

		return true;
	}

}
