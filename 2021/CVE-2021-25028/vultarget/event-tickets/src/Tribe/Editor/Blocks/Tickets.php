<?php
/**
 * Tickets block Setup
 */
class Tribe__Tickets__Editor__Blocks__Tickets
extends Tribe__Editor__Blocks__Abstract {

	public function hook() {
		add_action( 'wp_ajax_ticket_availability_check', [ $this, 'ticket_availability' ] );
		add_action( 'wp_ajax_nopriv_ticket_availability_check', [ $this, 'ticket_availability' ] );
	}

	/**
	 * Which is the name/slug of this block
	 *
	 * @since 4.9
	 *
	 * @return string
	 */
	public function slug() {
		return 'tickets';
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since 4.9
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = [] ) {
		/** @var Tribe__Tickets__Editor__Template $template */
		$template     = tribe( 'tickets.editor.template' );
		$post_id      = $template->get( 'post_id', null, false );
		$tickets_view = Tribe__Tickets__Tickets_View::instance();

		return $tickets_view->get_tickets_block( $post_id, false );
	}

	/**
	 * Register block assets
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function assets() {
		global $wp_version;
		$plugin = Tribe__Tickets__Main::instance();

		wp_register_script(
			'wp-util-not-in-footer',
			includes_url( '/js/wp-util.js' ),
			[ 'jquery', 'underscore' ],
			false,
			false
		);

		wp_enqueue_script( 'wp-util-not-in-footer' );

		$tickets_block_dependencies = [
			'jquery',
			'wp-util-not-in-footer',
			'tribe-common',
		];

		if ( version_compare( $wp_version, '5.0', '>=' ) ) {
			$tickets_block_dependencies[] = 'wp-i18n';
		}

		// Check whether we use v1 or v2. We need to update this when we deprecate tickets v1.
		$tickets_js = tribe_tickets_new_views_is_enabled() ? 'v2/tickets-block.js' : 'tickets-block.js';

		tribe_asset(
			$plugin,
			'tribe-tickets-block',
			$tickets_js,
			$tickets_block_dependencies,
			null,
			[
				'type'     => 'js',
				'groups'   => [ 'tribe-tickets-block-assets' ],
				'localize' => [
					[
						'name' => 'TribeTicketOptions',
						'data' => [ 'Tribe__Tickets__Tickets', 'get_asset_localize_data_for_ticket_options' ],
					],
					[
						'name' => 'TribeCurrency',
						'data' => [ 'Tribe__Tickets__Tickets', 'get_asset_localize_data_for_currencies' ],
					],
					[
						'name' => 'TribeCartEndpoint',
						'data' => [
							'url' => tribe_tickets_rest_url( '/cart/' ),
						],
					],
					[
						'name' => 'TribeMessages',
						'data' => $this->set_messages(),
					],
					[
						'name' => 'TribeTicketsURLs',
						'data' => [ 'Tribe__Tickets__Tickets', 'get_asset_localize_data_for_cart_checkout_urls' ],
					],
				],
			]
		);

		Tribe__Tickets__Tickets::$frontend_script_enqueued = true;
	}

	/**
	 * Check for ticket availability
	 *
	 * @since 4.9
	 *
	 * @param  array $tickets (IDs of tickets to check)
	 *
	 * @return void
	 */
	public function ticket_availability( $tickets = [] ) {

		$response = [ 'html' => '' ];
		$tickets  = tribe_get_request_var( 'tickets', [] );

		// Bail if we receive no tickets.
		if ( empty( $tickets ) ) {
			wp_send_json_error( $response );
		}

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		/** @var Tribe__Tickets__Editor__Template $tickets_editor */
		$tickets_editor = tribe( 'tickets.editor.template' );

		// Parse the tickets and create the array for the response.
		foreach ( $tickets as $ticket_id ) {
			$ticket = Tribe__Tickets__Tickets::load_ticket_object( $ticket_id );

			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| empty( $ticket->ID )
			) {
				continue;
			}

			$available     = $ticket->available();
			$max_at_a_time = $tickets_handler->get_ticket_max_purchase( $ticket->ID );

			$response['tickets'][ $ticket_id ]['available']    = $available;
			$response['tickets'][ $ticket_id ]['max_purchase'] = $max_at_a_time;

			// If there are no more available we will send the template part HTML to update the DOM.
			if ( 0 === $available ) {
				$response['tickets'][ $ticket_id ]['unavailable_html'] = $tickets_editor->template( 'blocks/tickets/quantity-unavailable', $ticket, false );
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Get all tickets for event/post, other than RSVP type because they're presented in a separate block.
	 *
	 * @since 4.9
	 *
	 * @param  int $post_id Post ID.
	 *
	 * @return array
	 */
	public function get_tickets( $post_id ) {
		$all_tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post_id );

		if ( ! $all_tickets ) {
			return [];
		}

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		$tickets = [];

		// We only want RSVP tickets.
		foreach ( $all_tickets as $ticket ) {
			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| $rsvp->class_name === $ticket->provider_class
			) {
				continue;
			}

			$tickets[] = $ticket;
		}

		return $tickets;
	}

	/**
	 * Get provider ID/slug.
	 *
	 * @since 4.9
	 * @since 4.12.3 Retrieve slug from updated Ticktes Status Manager method.
	 *
	 * @param  Tribe__Tickets__Tickets $provider Provider class instance.
	 *
	 * @return string
	 */
	public function get_provider_id( $provider ) {
		/** @var Tribe__Tickets__Status__Manager $status */
		$status = tribe( 'tickets.status' );

		$slug = $status->get_provider_slug( $provider );

		if (
			empty( $slug )
			|| 'rsvp' === $slug
		) {
			$slug = 'tpp';
		}

		return $slug;
	}

	/**
	 * Get all tickets on sale
	 *
	 * @since 4.9
	 *
	 * @param  array $tickets Array of all tickets.
	 *
	 * @return array
	 */
	public function get_tickets_on_sale( $tickets ) {
		$tickets_on_sale = [];

		foreach ( $tickets as $ticket ) {
			if ( tribe_events_ticket_is_on_sale( $ticket ) ) {
				$tickets_on_sale[] = $ticket;
			}
		}

		return $tickets_on_sale;
	}

	/**
	 * Get whether all ticket sales have passed or not
	 *
	 * @since 4.9
	 *
	 * @param  array $tickets Array of all tickets.
	 *
	 * @return bool
	 */
	public function get_is_sale_past( $tickets ) {
		$is_sale_past = ! empty( $tickets );

		foreach ( $tickets as $ticket ) {
			$is_sale_past = ( $is_sale_past && $ticket->date_is_later() );
		}

		return $is_sale_past;
	}

	/**
	 * Get whether no ticket sales have started yet
	 *
	 * @since 4.11.0
	 *
	 * @param  array $tickets Array of all tickets.
	 *
	 * @return bool
	 */
	public function get_is_sale_future( $tickets ) {
		$is_sale_future = ! empty( $tickets );

		foreach ( $tickets as $ticket ) {
			$is_sale_future = ( $is_sale_future && $ticket->date_is_earlier() );
		}

		return $is_sale_future;
	}

	/**
	 * Localized messages for errors, etc in javascript. Added in assets() above.
	 * Set up this way to amke it easier to add messages as needed.
	 *
	 * @since 4.11.0
	 *
	 * @return void
	 */
	public function set_messages() {
		$messages = [
			'api_error_title'        => _x( 'API Error', 'Error message title, will be followed by the error code.', 'event-tickets' ),
			'connection_error'       => __( 'Refresh this page or wait a few minutes before trying again. If this happens repeatedly, please contact the Site Admin.', 'event-tickets' ),
			'capacity_error'         => __( 'The ticket for this event has sold out and has been removed from your cart.', 'event-tickets'),
			'validation_error_title' => __( 'Whoops!', 'event-tickets' ),
			'validation_error'       => '<p>' . sprintf( _x( 'You have %s ticket(s) with a field that requires information.', 'The %s will change based on the error produced.', 'event-tickets' ), '<span class="tribe-tickets__notice--error__count">0</span>' ) . '</p>',
		];

		return $messages;
	}
}
