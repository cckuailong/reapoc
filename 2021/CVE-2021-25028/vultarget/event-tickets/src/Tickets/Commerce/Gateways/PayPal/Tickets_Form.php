<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Attendee;
use TEC\Tickets\Commerce\Module;

/**
 * Class Tickets_Form
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Tickets_Form {
	protected static $messages = [];

	/**
	 * Gets ticket messages
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function get_messages() {
		return static::$messages;
	}

	/**
	 * Adds a submission message
	 *
	 * @since 4.7
	 *
	 * @param        $message
	 * @param string $type
	 */
	public function add_message( $message, $type = 'update' ) {
		$message          = apply_filters( 'tribe_tpp_submission_message', $message, $type );
		static::$messages[] = (object) array( 'message' => $message, 'type' => $type );
	}


	/**
	 * Filters the post_updated_messages array for attendees
	 *
	 * @since 4.7
	 *
	 * @param array $messages Array of update messages
	 *
	 * @return array
	 */
	public function updated_messages( $messages ) {
		$ticket_post = get_post();

		if ( ! $ticket_post ) {
			return $messages;
		}

		$post_type = get_post_type( $ticket_post );

		if ( Attendee::POSTTYPE !== $post_type ) {
			return $messages;
		}

		$event = tribe( Module::class )->get_event_for_ticket( $ticket_post );

		$attendees_report_url = add_query_arg(
			array(
				'post_type' => $event->post_type,
				'page'      => \Tribe__Tickets__Tickets_Handler::$attendees_slug,
				'event_id'  => $event->ID,
			),
			admin_url( 'edit.php' )
		);

		$return_link = sprintf(
			esc_html__( 'Return to the %1$sAttendees Report%2$s.', 'event-tickets' ),
			"<a href='" . esc_url( $attendees_report_url ) . "'>",
			'</a>'
		);

		$messages[ Attendee::POSTTYPE ]     = $messages['post'];
		$messages[ Attendee::POSTTYPE ][1]  = sprintf(
			esc_html__( 'Post updated. %1$s', 'event-tickets' ),
			$return_link
		);
		$messages[ Attendee::POSTTYPE ][6]  = sprintf(
			esc_html__( 'Post published. %1$s', 'event-tickets' ),
			$return_link
		);
		$messages[ Attendee::POSTTYPE ][8]  = esc_html__( 'Post submitted.', 'event-tickets' );
		$messages[ Attendee::POSTTYPE ][9]  = esc_html__( 'Post scheduled.', 'event-tickets' );
		$messages[ Attendee::POSTTYPE ][10] = esc_html__( 'Post draft updated.', 'event-tickets' );

		return $messages;
	}

	/**
	 * Whether the form has rendered already or not
	 *
	 * @var bool
	 */
	protected $has_rendered = false;

	/**
	 * Modifies the passed content to inject the front-end tickets form.
	 *
	 * @todo @juanfra We need to move this to use a whole new set of templates. This is currently still using
	 *       Tribe Commerce templates and the old system.
	 *
	 * @since TBR
	 *
	 * @return void The method will echo in the context of a buffered output.
	 *
	 * @see   Tribe__Tickets__Tickets::front_end_tickets_form_in_content
	 */
	public function render() {
		if ( $this->has_rendered || ! tribe( Module::class )->is_active() ) {
			return;
		}

		$post = get_post();

		if ( empty( $post ) ) {
			return;
		}

		// For recurring events (child instances only), default to loading tickets for the parent event
		if ( ! empty( $post->post_parent ) && function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $post->ID ) ) {
			$post = get_post( $post->post_parent );
		}

		$tickets = tribe( Module::class )->get_tickets( $post->ID );

		foreach ( $tickets as $key => $ticket ) {
			/** @var \Tribe__Tickets__Ticket_Object $ticket */
			if ( ! $ticket->date_in_range() ) {
				unset( $tickets[ $key ] );
			}
		}

		if ( empty( $tickets ) ) {
			return;
		}

		$ticket_sent = empty( $_GET['tpp_sent'] ) ? false : true;

		if ( $ticket_sent ) {
			$this->add_message( esc_html( sprintf( __( 'Your PayPal %1$s has been received! Check your email for your PayPal %1$s confirmation.', 'event-tickets' ), tribe_get_ticket_label_singular( 'ticket_sent' ) ) ), 'success' );
		}

		$ticket_error = empty( $_GET['tpp_error'] ) ? false : (int) $_GET['tpp_error'];

		if ( $ticket_error ) {
			$this->add_message( \Tribe__Tickets__Commerce__PayPal__Errors::error_code_to_message( $ticket_error ), 'error' );
		}

		$ticket_message = empty( $_GET['tpp_message'] ) ? false : (int) $_GET['tpp_message'];

		if ( $ticket_message ) {
			$this->add_message( \Tribe__Tickets__Commerce__PayPal__Errors::error_code_to_message( $ticket_message ), 'update' );
		}

		$must_login = ! is_user_logged_in() && tribe( Module::class )->login_required();

		/**
		 * Controls the visibility of the "Log it before purchasing" link below the tickets form
		 * for TPP tickets
		 *
		 * @since 4.9.3
		 *
		 */
		$display_login_link = apply_filters( 'tribe_tickets_show_login_before_purchasing_link', true );

		ob_start();
		tribe( Module::class )->getTemplateHierarchy( 'tickets/tpp' );
		$form = ob_get_clean();

		$currently_available_tickets = array_filter( $tickets, array( $this, 'is_currently_available' ) );

		if ( count( $currently_available_tickets ) > 0 ) {
			// If we have available tickets there is generally no need to display a 'tickets unavailable' message
			// for this post
			tribe( Module::class )->do_not_show_tickets_unavailable_message();
		} else {
			// Indicate that there are not any tickets, so a 'tickets unavailable' message may be
			// appropriate (depending on whether other ticket providers are active and have a similar
			// result)
			tribe( Module::class )->maybe_show_tickets_unavailable_message( $tickets );
		}

		// It's only done when it's included
		$this->has_rendered = true;

		echo $form;
	}

	/**
	 * Sets whether the form rendered already or not.
	 *
	 * @since 4.7
	 *
	 * @param bool $has_rendered
	 */
	public function has_rendered( $has_rendered ) {
		$this->has_rendered = (bool) $has_rendered;
	}

	/**
	 * A utility method to filter the list of tickets by their currently available status.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return bool
	 */
	protected function is_currently_available( \Tribe__Tickets__Ticket_Object $ticket ) {
		return $ticket->date_in_range();
	}
}