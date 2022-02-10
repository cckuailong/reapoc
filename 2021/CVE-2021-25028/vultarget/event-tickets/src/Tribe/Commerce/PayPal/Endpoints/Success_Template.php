<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Endpoints__Success_Template
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Endpoints__Success_Template implements Tribe__Tickets__Commerce__PayPal__Endpoints__Template_Interface {

	/**
	 * @var string The PayPal order identification string.
	 */
	protected $order_number;

	/**
	 * Registers the resources this template will need to correctly render.
	 */
	public function register_resources() {
		// no-op
	}

	/**
	 * Enqueues the resources needed by this template to correctly render.
	 *
	 * @since 4.7
	 */
	public function enqueue_resources() {
		Tribe__Tickets__RSVP::get_instance()->enqueue_resources();
	}

	/**
	 * Renders and returns the template rendered contents.
	 *
	 * @since 4.7
	 *
	 * @param array $template_data
	 *
	 * @return string
	 */
	public function render( array $template_data = [] ) {
		$template_data = $this->get_template_data( $template_data );

		$is_just_visiting       = $template_data['is_just_visiting'];
		$order_is_valid         = $template_data['order_is_valid'];
		$order_is_not_completed = $template_data['order_is_not_completed'];

		if ( ! $is_just_visiting ) {
			/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
			$gateway = tribe( 'tickets.commerce.paypal.gateway' );
			$gateway->reset_invoice_number();

			if ( $order_is_not_completed ) {
				$order  = $template_data['order'];
				$status = $template_data['status'];
			} elseif ( $order_is_valid ) {
				$purchaser_name  = $template_data['purchaser_name'];
				$purchaser_email = $template_data['purchaser_email'];
				$tickets         = $template_data['tickets'];
				$order           = $template_data['order'];
			}
		}

		ob_start();
		include Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/tpp-success.php' );

		return ob_get_clean();
	}

	/**
	 * Builds and returns the date needed by this template.
	 *
	 * @since 4.7
	 *
	 * @param array $template_data
	 *
	 * @return array
	 */
	public function get_template_data( array $template_data = [] ) {
		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal                                  = tribe( 'tickets.commerce.paypal' );
		$template_data['is_just_visiting']       = false;
		$template_data['order_is_valid']         = true;
		$template_data['order_is_not_completed'] = false;
		$order_number                            = Tribe__Utils__Array::get( $_GET, 'tribe-tpp-order', false );
		$attendees                               = $paypal->get_attendees_by_id( $order_number, 'tpp_order_hash' );

		if ( empty( $attendees ) ) {
			// the order might have not been processed yet
			if ( ! isset( $_GET['tx'], $_GET['st'] ) && empty( $order_number ) ) {
				// this might just be someone visiting the page, all the pieces are missing
				$template_data['is_just_visiting'] = true;

				return $template_data;
			}

			if ( isset( $_GET['tx'], $_GET['st'] ) || ! empty( $order_number ) ) {
				$template_data['order_is_not_completed'] = true;
				$template_data['order'] = Tribe__Utils__Array::get( $_GET, 'tx', $order_number );
				$template_data['status'] = trim( strtolower( Tribe__Utils__Array::get( $_GET, 'st', __( 'pending', 'event-tickets' ) ) ) );

				return $template_data;
			}

			// we are missing one of the pieces...
			$template_data['order_is_valid'] = false;

			return $template_data;
		}

		// the purchaser details will be the same for all the attendees, so we fetch it from the first
		$first                            = reset( $attendees );
		$template_data['purchaser_name']  = Tribe__Utils__Array::get( $first, 'purchaser_name', '' );
		$template_data['purchaser_email'] = Tribe__Utils__Array::get( $first, 'purchaser_email', '' );

		$order_quantity = $order_total = 0;
		$tickets        = [];

		foreach ( $attendees as $attendee ) {
			$order_quantity ++;
			$ticket_id = Tribe__Utils__Array::get( $attendee, 'product_id', '' );
			$post_id = Tribe__Utils__Array::get( $attendee, 'event_id', '' );

			if ( empty( $ticket_id ) ) {
				continue;
			}

			$raw_ticket_price = get_post_meta( $ticket_id, '_price', true );
			$ticket_price     = (float) $raw_ticket_price;
			$order_total      += $ticket_price;

			/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
			$tickets_handler = tribe( 'tickets.handler' );

			if ( array_key_exists( $ticket_id, $tickets ) ) {
				$tickets[ $ticket_id ]['quantity'] += 1;
				$tickets[ $ticket_id ]['subtotal'] = $tickets[ $ticket_id ]['quantity'] * $ticket_price;
			} else {
				$header_image_id = ! empty( $post_id )
					? get_post_meta( $post_id, $tickets_handler->key_image_header, true )
					: false;

				$tickets[ $ticket_id ] = [
					'name'            => get_the_title( $ticket_id ),
					'price'           => $ticket_price,
					'quantity'        => 1,
					'subtotal'        => $ticket_price,
					'post_id'         => $post_id,
					'is_event'        => function_exists( 'tribe_is_event' ) && tribe_is_event( $post_id ),
					'header_image_id' => $header_image_id,
				];
			}
		}

		$template_data['order']   = [ 'quantity' => $order_quantity, 'total' => $order_total ];
		$template_data['tickets'] = $tickets;

		return $template_data;
	}
}