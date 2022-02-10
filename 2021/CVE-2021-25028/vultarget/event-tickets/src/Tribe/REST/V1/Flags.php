<?php

/**
 * Class Tribe__Tickets__REST__V1__Flags
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Flags {
	/**
	 * Filters posts REST response data for ticket-enabled custom post types to add ticket-related flags.
	 *
	 * @since 4.8
	 *
	 * @param WP_REST_Response $response
	 * @param WP_Post          $post
	 *
	 * @return WP_REST_Response
	 */
	public function flag_ticketed_post( WP_REST_Response $response, WP_Post $post ) {
		$id = $post->ID;

		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $id );

		$response->data['ticketed'] = count( $tickets ) > 0
			? $this->extract_providers_from_tickets( $tickets )
			: false;

		return $response;
	}

	/**
	 * Creates a list of ticket providers for a post.
	 *
	 * @since 4.8
	 *
	 * @param array $tickets
	 *
	 * @return array
	 */
	protected function extract_providers_from_tickets( array $tickets ) {
		$slugs = array();
		/** @var Tribe__Tickets__REST__Interfaces__Post_Repository $repository */
		$repository = tribe( 'tickets.rest-v1.repository' );

		/** @var Tribe__Tickets__Ticket_Object $ticket */
		foreach ( $tickets as $ticket ) {
			$slugs[] = $repository->get_provider_slug( $ticket->provider_class );
		}

		return array_unique( array_filter( $slugs ) );
	}

	/**
	 * Filters events REST response data to add ticket-related flags.
	 *
	 * @since 4.8
	 *
	 * @param array   $data
	 * @param WP_Post $event
	 *
	 * @return array
	 */
	public function flag_ticketed_event( array $data, WP_Post $event ) {
		$id = $event->ID;

		$data['ticketed'] = false;

		if ( ! in_array( $event->post_type, Tribe__Tickets__Main::instance()->post_types(), true ) ) {
			return $data;
		}

		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $id );

		if ( 0 < count( $tickets ) ) {
			$data['ticketed'] = $this->extract_providers_from_tickets( $tickets );
		}

		return $data;
	}
}
