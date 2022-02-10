<?php

class Tribe__Tickets__Editor__Blocks__Rsvp extends Tribe__Editor__Blocks__Abstract {

	/**
	 * Init class
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function hook() {
		// Add AJAX calls.
		add_action( 'wp_ajax_rsvp-form', [ $this, 'rsvp_form' ] );
		add_action( 'wp_ajax_nopriv_rsvp-form', [ $this, 'rsvp_form' ] );
		add_action( 'wp_ajax_rsvp-process', [ $this, 'rsvp_process' ] );
		add_action( 'wp_ajax_nopriv_rsvp-process', [ $this, 'rsvp_process' ] );
	}

	/**
	 * Which is the name/slug of this block
	 *
	 * @since 4.9
	 *
	 * @return string
	 */
	public function slug() {
		return 'rsvp';
	}

	/**
	 * Set the default attributes of this block
	 *
	 * @since 4.9
	 *
	 * @return array
	 */
	public function default_attributes() {
		return [];
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since 4.9
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return string
	 */
	public function render( $attributes = [] ) {
		/** @var Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		$post_id = $template->get( 'post_id', null, false );

		$tickets_view = Tribe__Tickets__Tickets_View::instance();

		return $tickets_view->get_rsvp_block( $post_id, false );
	}

	/**
	 * Method to get all RSVP tickets
	 *
	 * @since 4.9
	 *
	 * @return array
	 */
	public function get_tickets( $post_id ) {
		$tickets = [];

		// Bail if there's no event id.
		if ( ! $post_id ) {
			return $tickets;
		}

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		// Get the tickets IDs for this event.
		$ticket_ids = $rsvp->get_tickets_ids( $post_id );

		// Bail if we don't have tickets.
		if ( ! $ticket_ids ) {
			return $tickets;
		}

		// We only want RSVP tickets.
		foreach ( $ticket_ids as $post ) {
			// Get the ticket
			$ticket = $rsvp->get_ticket( $post_id, $post );

			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| $rsvp->class_name !== $ticket->provider_class
			) {
				continue;
			}

			$tickets[] = $ticket;
		}

		return $tickets;
	}

	/**
	 * Method to get the active RSVP tickets
	 *
	 * @since 4.9
	 *
	 * @return array
	 */
	public function get_active_tickets( $tickets ) {
		$active_tickets = [];

		foreach ( $tickets as $ticket ) {
			// Continue if it's not in date range.
			if ( ! $ticket->date_in_range() ) {
				continue;
			}

			$active_tickets[] = $ticket;
		}

		return $active_tickets;
	}

	/**
	 * Method to get the all RSVPs past flag
	 * All RSVPs past flag is true if all RSVPs end date is earlier than current date
	 * If there are no RSVPs, false is returned
	 *
	 * @since 4.9
	 *
	 * @return bool
	 */
	public function get_all_tickets_past( $tickets ) {
		if ( empty( $tickets ) ) {
			return false;
		}

		$all_past = true;

		foreach ( $tickets as $ticket ) {
			$all_past = $all_past && $ticket->date_is_later();
		}

		return $all_past;
	}

	/**
	 * Get the threshold.
	 *
	 * @since 4.12.3
	 *
	 * @param int $post_id
	 *
	 * @return int
	 */
	public function get_threshold( $post_id = 0 ) {
		/** @var Tribe__Settings_Manager $settings_manager */
		$settings_manager = tribe( 'settings.manager' );
		$threshold        = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

		/**
		 * Overwrites the threshold to display "# tickets left".
		 *
		 * @since 4.11.1
		 *
		 * @param int $threshold Stock threshold to trigger display of "# tickets left" text.
		 * @param int $post_id   Event ID.
		 */
		$threshold = absint( apply_filters( 'tribe_display_rsvp_block_tickets_left_threshold', $threshold, $post_id ) );

		return $threshold;
	}

	/**
	 * Show unlimited?
	 *
	 * @since 4.12.3
	 *
	 * @param bool $is_unlimited
	 *
	 * @return bool
	 */
	public function show_unlimited( $is_unlimited ) {
		/**
		 * Allows hiding of "unlimited" to be toggled on/off conditionally.
		 *
		 * @since 4.11.1
		 *
		 * @param int $show_unlimited allow showing of "unlimited".
		 */
		return (bool) apply_filters( 'tribe_rsvp_block_show_unlimited_availability', false, $is_unlimited );
	}

	/**
	 * Register block assets
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function assets() {
		$plugin = Tribe__Tickets__Main::instance();

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-rsvp',
			'rsvp-block.js',
			[ 'jquery' ],
			null,
			[
				'localize' => [
					'name' => 'TribeRsvp',
					'data' => [
						'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
					],
				],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-block-rsvp-style',
			'app/rsvp/frontend.css',
			[],
			null
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-ari',
			'v2/rsvp-ari.js',
			[ 'jquery', 'wp-util' ],
			null,
			[
				'groups'       => 'tribe-tickets-rsvp',
				'conditionals' => [ $this, 'should_enqueue_ari' ],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-manager',
			'v2/rsvp-manager.js',
			[
				'jquery',
				'tribe-common',
				'tribe-tickets-rsvp-block',
				'tribe-tickets-rsvp-tooltip',
				'tribe-tickets-rsvp-ari',
			],
			null,
			[
				'localize' => [
					'name' => 'TribeRsvp',
					'data' => [
						'ajaxurl'    => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
						'cancelText' => __( 'Are you sure you want to cancel?', 'event-tickets' ),
					],
				],
				'groups'   => 'tribe-tickets-rsvp',
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-block',
			'v2/rsvp-block.js',
			[ 'jquery' ],
			null,
			[ 'groups' => 'tribe-tickets-rsvp' ]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-tooltip',
			'v2/rsvp-tooltip.js',
			[
				'jquery',
				'tribe-common',
				'tribe-tooltipster',
			],
			null,
			[
				'groups' => 'tribe-tickets-rsvp',
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-style',
			'rsvp.css',
			[ 'tribe-common-skeleton-style', 'tribe-common-responsive' ],
			null
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-rsvp-style-override',
			Tribe__Templates::locate_stylesheet( 'tribe-events/tickets/rsvp.css' ),
			[],
			null
		);
	}

	/**
	 * Determine whether we should enqueue the ARI assets.
	 *
	 * @since 5.0.0
	 *
	 * @return bool Whether we should enqueue the ARI assets.
	 */
	public function should_enqueue_ari() {
		return class_exists( 'Tribe__Tickets_Plus__Main' );
	}

	/**
	 * Function that returns the RSVP form from an AJAX call
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function rsvp_form() {
		$response  = [
			'html' => '',
			'view' => 'rsvp-form',
		];
		$ticket_id = absint( tribe_get_request_var( 'ticket_id', 0 ) );
		$going     = tribe_get_request_var( 'going', 'yes' );

		if ( 0 === $ticket_id ) {
			wp_send_json_error( $response );
		}

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		$ticket = $rsvp->get_ticket( get_the_id(), $ticket_id );

		if ( ! $ticket instanceof Tribe__Tickets__Ticket_Object ) {
			wp_send_json_error( $response );
		}

		$args = [
			'post_id' => $ticket->get_event_id(),
			'ticket'  => $ticket,
			'going'   => $going,
		];

		/** @var Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		$html = $template->template( 'blocks/rsvp/form/form', $args, false );

		$response['html'] = $html;

		wp_send_json_success( $response );
	}

	/**
	 * Function that process the RSVP
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function rsvp_process() {
		$response = [
			'html' => '',
			'view' => 'rsvp-process',
		];

		$ticket_id = absint( tribe_get_request_var( 'ticket_id', 0 ) );

		if ( 0 === $ticket_id ) {
			wp_send_json_error( $response );
		}

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp        = tribe( 'tickets.rsvp' );
		$has_tickets = false;
		$event       = $rsvp->get_event_for_ticket( $ticket_id );
		$post_id     = $event->ID;
		$ticket      = $rsvp->get_ticket( $post_id, $ticket_id );

		/**
		 * RSVP specific action fired just before a RSVP-driven attendee tickets for an order are generated
		 *
		 * @param $data $_POST Parameters comes from RSVP Form
		 */
		do_action( 'tribe_tickets_rsvp_before_order_processing' );

		$attendee_details = $rsvp->parse_attendee_details();

		if ( false === $attendee_details ) {
			wp_send_json_error( $response );
		}

		$products = [];

		if ( isset( $_POST['tribe_tickets'] ) ) {
			$products = wp_list_pluck( $_POST['tribe_tickets'], 'ticket_id' );
		} elseif ( isset( $_POST['product_id'] ) ) {
			$products = (array) $_POST['product_id'];
		}

		// Iterate over each product.
		foreach ( $products as $product_id ) {
			if ( ! $ticket_qty = $rsvp->parse_ticket_quantity( $product_id ) ) {
				// if there were no RSVP tickets for the product added to the cart, continue.
				continue;
			}

			$has_tickets |= $rsvp->generate_tickets_for( $product_id, $ticket_qty, $attendee_details );
		}

		$order_id              = $attendee_details['order_id'];
		$attendee_order_status = $attendee_details['order_status'];

		/**
		 * Fires when an RSVP attendee tickets have been generated.
		 *
		 * @param int    $order_id              ID of the RSVP order
		 * @param int    $post_id               ID of the post the order was placed for
		 * @param string $attendee_order_status 'yes' if the user indicated they will attend
		 */
		do_action( 'event_tickets_rsvp_tickets_generated', $order_id, $post_id, $attendee_order_status );

		$send_mail_stati = [ 'yes' ];

		/**
		 * Filters whether a confirmation email should be sent or not for RSVP tickets.
		 *
		 * This applies to attendance and non attendance emails.
		 *
		 * @param bool $send_mail Defaults to `true`.
		 */
		$send_mail = apply_filters( 'tribe_tickets_rsvp_send_mail', true );

		if ( $send_mail && $has_tickets ) {
			/**
			 * Filters the attendee order stati that should trigger an attendance confirmation.
			 *
			 * Any attendee order status not listed here will trigger a non attendance email.
			 *
			 * @param array  $send_mail_stati       An array of default stati triggering an attendance email.
			 * @param int    $order_id              ID of the RSVP order
			 * @param int    $post_id               ID of the post the order was placed for
			 * @param string $attendee_order_status 'yes' if the user indicated they will attend
			 */
			$send_mail_stati = apply_filters(
				'tribe_tickets_rsvp_send_mail_stati',
				$send_mail_stati,
				$order_id,
				$post_id,
				$attendee_order_status
			);

			// No point sending tickets if their current intention is not to attend
			if ( in_array( $attendee_order_status, $send_mail_stati, true ) ) {
				$rsvp->send_tickets_email( $order_id, $post_id );
			} else {
				$rsvp->send_non_attendance_confirmation( $order_id, $post_id );
			}
		}

		$args = [
			'ticket' => $ticket,
		];

		$remaining = $ticket->remaining();

		/** @var Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		if ( ! $remaining ) {
			$response['status_html'] = $template->template( 'blocks/rsvp/status', $args, false );
		}

		$response['remaining']      = $ticket->remaining();
		$response['remaining_html'] = $template->template( 'blocks/rsvp/details/availability', $args, false );
		$response['html']           = $template->template( 'blocks/rsvp/messages/success', $args, false );

		wp_send_json_success( $response );
	}
}
