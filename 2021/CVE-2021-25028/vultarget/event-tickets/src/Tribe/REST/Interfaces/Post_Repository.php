<?php


interface Tribe__Tickets__REST__Interfaces__Post_Repository {

	/**
	 * Returns the array representation of a ticket.
	 *
	 * Mind that this method will take user capabilities into account when providing
	 * the data.
	 *
	 * @since 4.8
	 *
	 * @param int|WP_Post|array|Tribe__Tickets__Ticket_Object $ticket_id A ticket post, data, post ID or object.
	 * @param string                                          $context   The context in which the data will be shown;
	 *                                                                   this is about format, not permissions.
	 *
	 * @return array|WP_Error ticket data or a `WP_Error` detailing the issue on failure.
	 */
	public function get_ticket_data( $ticket_id, $context = 'default' );

	/**
	 * Returns an attendee data.
	 *
	 * Mind that this method will take user capabilities into account when providing
	 * the data.
	 *
	 * @since  4.8
	 *
	 * @param int|WP_Post $attendee_id An attendee post or post ID.
	 * @param string      $context     The context in which the data will be shown;
	 *                                 this is about format, not permissions.
	 *
	 * @return array|WP_Error The attendee data or a `WP_Error` detailing the issue on failure.
	 */
	public function get_attendee_data( $attendee_id, $context = 'default' );

	/**
	 * Returns the slug for provider.
	 *
	 * @since 4.7.5
	 *
	 * @param string|object $provider_class The provider object or class.
	 *
	 * @return string
	 */
	public function get_provider_slug( $provider_class );
}
