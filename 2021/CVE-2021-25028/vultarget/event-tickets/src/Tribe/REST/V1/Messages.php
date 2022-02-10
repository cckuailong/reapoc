<?php


class Tribe__Tickets__REST__V1__Messages implements Tribe__REST__Messages_Interface {

	/**
	 * @var string
	 */
	protected $message_prefix = 'rest-v1:';

	/**
	 * Tribe__Tickets__REST__V1__Messages constructor.
	 *
	 * @since 4.7.5
	 *
	 */
	public function __construct() {
		$this->messages = [
			'missing-attendee-id'           => __( 'The attendee ID is missing from the request', 'event-tickets' ),
			'attendee-not-found'            => __( 'The requested post ID does not exist or is not an attendee', 'event-tickets' ),
			'attendee-not-accessible'       => __( 'The requested attendee is not accessible', 'event-tickets' ),
			'attendee-check-in-not-found'   => __( 'The requested attendee check in is not available', 'event-tickets' ),
			'ticket-not-found'              => __( 'The requested ticket post could not be found', 'event-tickets' ),
			'ticket-provider-not-found'     => __( 'The ticket provider for the requested ticket is not available', 'event-tickets' ),
			'ticket-post-not-found'         => __( 'The post associated with the requested ticket was not found', 'event-tickets' ),
			'ticket-object-not-found'       => __( 'The requested ticket object could not be built or found', 'event-tickets' ),
			'ticket-not-accessible'         => __( 'The requested ticket is not accessible', 'event-tickets' ),
			'error-global-id-generation'    => __( 'The ticket global id could not be generated', 'event-tickets' ),
			'ticket-does-not-exist'         => __( 'The ticket #%d does not exist.', 'event-tickets' ),
			'ticket-capacity-not-available' => __( 'The ticket "%s" does not have that many available for purchase.', 'event-tickets' ),
			'webhook-not-processed'         => __( 'Webhook not processed.', 'event-tickets' ),
			// this is an internal error, not same as the `ticket-not-found` one
			'error-ticket-post'             => __( 'There was a problem while fetching the requested ticket post', 'event-tickets' ),
			'error-attendee-post'           => __( 'There was a problem while fetching the requested attendee post', 'event-tickets' ),
			// same as WordPress REST API
			'invalid-page-number'           => __( 'The page number requested is larger than the number of pages available.', 'default' ),
			'etplus-not-loaded'             => __( 'Event Tickets Plus must be loaded to get Attendee data.', 'default' ),
		];
	}

	/**
	 * Returns the localized message associated with the slug.
	 *
	 * @since 4.7.5
	 *
	 * @param string $message_slug
	 *
	 * @return string
	 */
	public function get_message( $message_slug ) {
		if ( isset( $this->messages[ $message_slug ] ) ) {
			return $this->messages[ $message_slug ];
		}

		return '';
	}

	/**
	 * Returns the associative array of all the messages handled by the class.
	 *
	 * @since 4.7.5
	 *
	 * @return array An associative array in the `[ <slug> => <localized message> ]` format.
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * Prefixes a message slug with a common root.
	 *
	 * @since 4.7.5
	 *
	 * Used to uniform the slug format to the one used by the `Tribe__Events__Aggregator__Service` class.
	 *
	 * @see Tribe__Events__Aggregator__Service::register_messages()
	 *
	 * @param string $message_slug
	 *
	 * @return string The prefixed message slug.
	 */
	public function prefix_message_slug( $message_slug ) {
		return $this->message_prefix . $message_slug;
	}

}
