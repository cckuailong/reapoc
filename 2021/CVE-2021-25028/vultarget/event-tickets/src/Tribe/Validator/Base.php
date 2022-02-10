<?php

/**
 * Class Tribe__Events__Validator__Base
 *
 * @since 4.7.5
 */
class Tribe__Tickets__Validator__Base extends Tribe__Validator__Base
	implements Tribe__Tickets__Validator__Interface {

	/**
	 * {@inheritdoc}
	 */
	public function is_ticket_id( $ticket_id ) {
		if ( empty( $ticket_id ) ) {
			return false;
		}

		/** @var Tribe__Tickets__Data_API $ticket_data */
		$ticket_data = tribe( 'tickets.data_api' );

		// get ticket provider
		$ticket_type = $ticket_data->detect_by_id( $ticket_id );

		return ! empty( $ticket_type ) && ! empty( $ticket_type['class'] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_ticket_id_list( $tickets, $sep = ',' ) {
		$sep     = is_string( $sep ) ? $sep : ',';
		$tickets = Tribe__Utils__Array::list_to_array( $tickets, $sep );

		$valid = array_filter( $tickets, array( $this, 'is_ticket_id' ) );

		return ! empty( $valid ) && count( $valid ) === count( $tickets );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_event_id( $event_id ) {
		if ( empty( $event_id ) ) {
			return false;
		}

		$event = get_post( $event_id );

		return ! empty( $event ) && 'tribe_event' === $event->post_type;
	}

	/**
	 * Whether a post ID exists.
	 *
	 * @since 4.7.5
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_post_id( $post_id ) {
		$post = get_post( $post_id );

		return ( $post instanceof WP_Post );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_post_id_list( $posts, $sep = ',' ) {
		$sep   = is_string( $sep ) ? $sep : ',';
		$posts = Tribe__Utils__Array::list_to_array( $posts, $sep );

		$valid = array_filter( $posts, array( $this, 'is_post_id' ) );

		return ! empty( $valid ) && count( $valid ) === count( $posts );
	}

	/**
	 * Whether the value is the post id of an existing attendee or not.
	 *
	 * @since 4.8.0
	 *
	 * @param int $attendee_id
	 *
	 * @return bool
	 */
	public function is_attendee_id( $attendee_id ) {
		if ( empty( $attendee_id ) ) {
			return false;
		}

		// get ticket provider
		$ticket_type = tribe( 'tickets.data_api' )->detect_by_id( $attendee_id );

		//get ticket
		$ticket = get_post( $attendee_id );

		return ! empty( $ticket_type['post_type'] ) && ! empty( $ticket ) &&  $ticket_type['post_type'] === $ticket->post_type;
	}

	/**
	 * Whether a csv list, or array, of post IDs only contains valid attendee IDs or not.
	 *
	 * @since 4.7.5
	 *
	 * @param        string|array $attendees
	 * @param string              $sep
	 *
	 * @return bool
	 */
	public function is_attendee_id_list( $attendees, $sep = ',' ) {
		$sep       = is_string( $sep ) ? $sep : ',';
		$attendees = Tribe__Utils__Array::list_to_array( $attendees, $sep );

		$valid = array_filter( $attendees, array( $this, 'is_attendee_id' ) );

		return ! empty( $valid ) && count( $valid ) === count( $attendees );
	}
}
