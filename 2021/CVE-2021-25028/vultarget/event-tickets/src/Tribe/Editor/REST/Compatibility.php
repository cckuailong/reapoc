<?php

/**
 * Initialize Gutenberg Rest Compatibility layers for WP api and Tickets
 *
 * @todo  Remove this on class when we move into using our own API for RSVP
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor__REST__Compatibility {
	/**
	 * Register the required Rest filters fields for good Gutenberg saving
	 *
	 * @since 4.9
	 *
	 * @return boolean
	 */
	public function hook() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}

		add_filter( 'updated_post_meta', [ $this, 'trigger_update_capacity' ], 15, 4 );
		add_filter( 'rest_prepare_tribe_rsvp_tickets', [ $this, 'filter_rest_hook' ], 10, 3 );

		return true;
	}

	/**
	 * When updating the Value of capacity for a RSVP we update Stock and some other Meta values
	 *
	 * @since 4.9
	 *
	 * @param  int    $meta_id
	 * @param  int    $object_id
	 * @param  string $meta_key
	 * @param  mixed  $capacity
	 *
	 * @return null
	 */
	public function trigger_update_capacity( $meta_id, $object_id, $meta_key, $capacity ) {
		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		// Bail if not capacity
		if ( $tickets_handler->key_capacity !== $meta_key ) {
			return;
		}

		$object = get_post( $object_id );

		// Bail on wrong post type
		if ( tribe( 'tickets.rsvp' )->ticket_object !== $object->post_type ) {
			return;
		}

		// Fetch capacity field, if we don't have it use default (defined above)
		$capacity = trim( $capacity );

		// If empty we need to modify to the default
		if ( '' === $capacity ) {
			$capacity = -1;
		}

		// The only available value lower than zero is -1 which is unlimited
		if ( 0 > $capacity ) {
			$capacity = -1;
		}

		if ( -1 !== $capacity ) {
			$totals = $tickets_handler->get_ticket_totals( $object_id );

			// update stock by taking capacity - pending and sold tickets
			$stock  = $capacity - ( $totals['pending'] + $totals['sold'] );

			// set stock to zero if a negative number
			if ( $stock < 0 ) {
				$stock = 0;
			}

			update_post_meta( $object_id, '_manage_stock', 'yes' );
			update_post_meta( $object_id, '_stock', $stock );
		} else {
			// unlimited stock
			delete_post_meta( $object_id, '_stock_status' );
			update_post_meta( $object_id, '_manage_stock', 'no' );
			delete_post_meta( $object_id, '_stock' );
			delete_post_meta( $object_id, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE );
			delete_post_meta( $object_id, Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP );
		}
	}

	/**
	 * Filter rest response prior to returning via API.
	 * Add new functions here so that they pass the response along and we can force order
	 *
	 * Hooked on rest_prepare_tribe_rsvp_tickets.
	 * @since 4.10
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post $post The post (RSVP)
	 * @param WP_REST_Request $unused_request The request object.
	 *
	 * @return WP_REST_Response $response The modified response object.
	 */
	public function filter_rest_hook( $response, $post, $unused_request ) {
		// Filter the rest request to add meta for if the RSVP has attendees going/not going
		$response = $this->filter_rest_going_fields( $response, $post );

		// Filter the rest request to add meta for if the RSVP has attendee meta
		$response = $this->filter_rest_has_attendee_info_fields( $response, $post );

		return $response;
	}

	/**
	 * Filter the rest request to add meta for if the RSVP has attendee meta
	 * @since 4.10
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post $post The post (RSVP)
	 *
	 * @return WP_REST_Response $response The modified response object.
	 */
	public function filter_rest_has_attendee_info_fields( $response, $post ) {
		if ( 'tribe_rsvp_tickets' !== $post->post_type ) {
			return $response;
		}

		$key        = '_tribe_ticket_has_attendee_info_fields';
		$repository = tribe( 'tickets.data_api' );

		$response->data['meta'][ $key ] = $repository->ticket_has_meta_fields( $post->ID );

		return $response;
	}

	/**
	 * Filter the rest request to add meta for if the RSVP has attendees going/not going
	 * @since 4.10
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post $post The post (RSVP)
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response $response The modified response object.
	 */
	public function filter_rest_going_fields( $response, $post ) {
		if ( 'tribe_rsvp_tickets' !== $post->post_type ) {
			return $response;
		}

		$repository = tribe( 'tickets.rest-v1.repository' );
		$attendees  = $repository->get_ticket_attendees( $post->ID );

		if ( false === $attendees ) {
			return $response;
		}

		$going     = 0;
		$not_going = 0;

		foreach ( $attendees as $attendee ) {
			if ( isset( $attendee['rsvp_going'] ) && true === $attendee['rsvp_going'] ) {
				$going++;
			} else {
				$not_going++;
			}
		}

		$response->data['meta']['_tribe_ticket_going_count']     = (string) $going;
		$response->data['meta']['_tribe_ticket_not_going_count'] = (string) $not_going;

		return $response;
	}
}
