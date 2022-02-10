<?php

namespace TEC\Tickets\Commerce;

/**
 * Class Tickets_View
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Tickets_View extends \Tribe__Tickets__Tickets_View {
	/**
	 * Groups PayPal ticket attendees by purchaser name/email
	 *
	 * @since 5.1.9
	 *
	 * @param int      $post_id The post ID it relates to
	 * @param int|null $user_id An optional user ID
	 *
	 * @return array Array with the tickets attendees grouped by purchaser name/email
	 */
	public function get_post_attendees_by_purchaser( $post_id, $user_id ) {
		$attendees = $this->get_post_ticket_attendees( $post_id, $user_id );

		if ( ! $attendees ) {
			return array();
		}

		$attendee_groups = array();
		foreach ( $attendees as $attendee ) {
			$key = $attendee['purchaser_name'] . '::' . $attendee['purchaser_email'];

			if ( ! isset( $attendee_groups[ $key ] ) ) {
				$attendee_groups[ $key ] = array();
			}

			$attendee_groups[ $key ][] = $attendee;
		}

		return $attendee_groups;
	}

	/**
	 * Fetches from the Cached attendees list the ones that are relevant for this user and event
	 *
	 * Important to note that this method will bring the attendees from PayPal tickets
	 *
	 * @since 5.1.9
	 *
	 * @param int      $event_id The Event ID it relates to
	 * @param int|null $user_id  An Optional User ID
	 *
	 * @return array                   Array with the PayPal tickets attendees
	 */
	public function get_post_ticket_attendees( $event_id, $user_id ) {
		$module = tribe( Module::class );

		if ( $user_id ) {
			return $module->get_attendees_by_user_id( $user_id, $event_id );
		}

		return $module->get_attendees_by_id( $event_id );
	}

	/**
	 * Verifies if the Given Event has Ticket participation restricted
	 *
	 * @since 5.1.9
	 *
	 * @param int $event_id  The Event/Post ID (optional)
	 * @param int $ticket_id The Ticket/RSVP ID (optional)
	 * @param int $user_id   An User ID (optional)
	 *
	 * @return boolean
	 */
	public function is_ticket_restricted( $event_id = null, $ticket_id = null, $user_id = null ) {
		// By default we always pass the current User
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		/**
		 * Allow users to filter if this Event or Ticket has Restricted Tickets
		 *
		 * @since 4.7.1
		 *
		 * @param boolean $restricted Is this Event or Ticket Restricted?
		 * @param int     $event_id   The Event/Post ID (optional)
		 * @param int     $ticket_id  The Ticket/RSVP ID (optional)
		 * @param int     $user_id    An User ID (optional)
		 */
		return apply_filters( 'tec_tickets_commerce_is_ticket_restricted', false, $event_id, $ticket_id, $user_id );
	}

	/**
	 * Gets a HTML Attribute for input/select/textarea to be disabled
	 *
	 * @since 5.1.9
	 *
	 * @param int $event_id  The Event/Post ID (optional)
	 * @param int $ticket_id The Ticket/RSVP ID (optional)
	 *
	 * @return boolean
	 */
	public function get_restriction_attr( $event_id = null, $ticket_id = null ) {
		$is_disabled = '';
		if ( $this->is_ticket_restricted( $event_id, $ticket_id ) ) {
			$is_disabled = 'disabled title="' . esc_attr__( 'This ticket is no longer active.', 'event-tickets' ) . '"';
		}

		return $is_disabled;
	}

	/**
	 * Creates the HTML for the status of the PayPal ticket.
	 *
	 * @since 5.1.9
	 *
	 * @param string $status The ticket order status
	 *
	 * @return void
	 */
	public function render_ticket_status( $status = null ) {
		$ticket_status = $this->get_ticket_status( $status );

		echo sprintf( '<span>%s</span>', esc_html( $ticket_status ) );
	}

	/**
	 * Returns the ticket status corresponding to the ticket status slug.
	 *
	 * @since 5.1.9
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function get_ticket_status( $status ) {
		$ticket_status = __( 'unavailable', 'event-tickets' );

		if ( ! empty( $status ) ) {
			/** @var \Tribe__Tickets__Status__Manager $status_mgr */
			$status_mgr = tribe( 'tickets.status' );

			$statuses       = $status_mgr->get_all_provider_statuses( 'tpp' );
			$status_strings = [];
			foreach ( $statuses as $s ) {
				$status_strings[ $s->provider_name ] = _x( $s->name, 'a PayPal ticket order status', 'event-tickets' );
			}

			$ticket_status = \Tribe__Utils__Array::get( $status_strings, $status, reset( $status_strings ) );
		}

		return $ticket_status;
	}
}