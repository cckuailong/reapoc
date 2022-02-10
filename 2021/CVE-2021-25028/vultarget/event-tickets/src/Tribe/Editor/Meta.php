<?php
/**
 * Initialize Gutenberg Event Meta fields.
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor__Meta extends Tribe__Editor__Meta {
	/**
	 * Register the required Meta fields for good Gutenberg saving.
	 *
	 * @since 4.9
	 *
	 * @return void
	 */
	public function register() {

		// That comes from Woo, that is why it's static string.
		register_meta(
			'post',
			'_price',
			$this->text()
		);

		register_meta(
			'post',
			'_stock',
			$this->text()
		);

		/** @var Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		register_meta(
			'post',
			$handler->key_image_header,
			$this->text()
		);

		register_meta(
			'post',
			$handler->key_provider_field,
			$this->text()
		);

		register_meta(
			'post',
			$handler->key_capacity,
			$this->text_or_null()
		);

		register_meta(
			'post',
			$handler->key_start_date,
			$this->text()
		);

		register_meta(
			'post',
			$handler->key_end_date,
			$this->text()
		);

		register_meta(
			'post',
			$handler->key_show_description,
			$this->text()
		);

		/**
		 * @todo  move this into the `tickets.handler` class
		 */
		register_meta(
			'post',
			'_tribe_ticket_show_not_going',
			$this->boolean_or_null()
		);

		// Global Stock.
		register_meta(
			'post',
			Tribe__Tickets__Global_Stock::GLOBAL_STOCK_ENABLED,
			$this->text()
		);

		register_meta(
			'post',
			Tribe__Tickets__Global_Stock::GLOBAL_STOCK_LEVEL,
			$this->text()
		);

		register_meta(
			'post',
			Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE,
			$this->text()
		);

		register_meta(
			'post',
			Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP,
			$this->text()
		);

		// Fetch RSVP keys.
		$rsvp = tribe( 'tickets.rsvp' );

		register_meta(
			'post',
			$rsvp->get_event_key(),
			$this->text()
		);

		// "Ghost" Meta fields
		register_meta(
			'post',
			'_tribe_ticket_going_count',
			$this->text()
		);

		register_meta(
			'post',
			'_tribe_ticket_not_going_count',
			$this->text()
		);

		register_meta(
			'post',
			'_tribe_tickets_list',
			$this->numeric_array()
		);

		register_meta(
			'post',
			'_tribe_ticket_has_attendee_info_fields',
			$this->boolean_or_null()
		);
	}

	/**
	 * Default definition for an attribute of type text
	 *
	 * @since 4.10.11.1
	 *
	 * @return array
	 */
	protected function text_or_null() {
		$args = $this->text();

		if ( ! function_exists( 'is_wp_version_compatible' ) || ! is_wp_version_compatible( '5.3' ) ) {
			return $args;
		}

		// REST API needs more help because it doesn't like 'type' being an array (yet).
		$args['show_in_rest'] = [
			'schema' => [
				'type' => $args['type'],
			],
		];

		$args['type'] = [
			$args['type'],
			'null',
		];

		return $args;
	}

	/**
	 * Default definition for an attribute of boolean text
	 *
	 * @since 4.10.11.1
	 *
	 * @return array
	 */
	protected function boolean_or_null() {
		$args = $this->boolean();

		if ( ! function_exists( 'is_wp_version_compatible' ) || ! is_wp_version_compatible( '5.3' ) ) {
			return $args;
		}

		// REST API needs more help because it doesn't like 'type' being an array (yet).
		$args['show_in_rest'] = [
			// Especially for boolean, if 'type' isn't reasserted on this next line it throws 500 errors.
			'type'   => $args['type'],
			'schema' => [
				'type' => $args['type'],
			],
		];

		$args['type'] = [
			$args['type'],
			'null',
		];

		return $args;
	}

	/**
	 * Removes `_edd_button_behavior` key from the REST API where tickets blocks is used
	 *
	 * @since 4.9
	 *
	 * @param array  $args
	 * @param string $defaults
	 * @param string $object_type
	 * @param string $meta_key
	 *
	 * @return array
	 */
	public function register_meta_args( $args = [], $defaults = '', $object_type = '', $meta_key = '' ) {
		if ( '_edd_button_behavior' === $meta_key ) {
			$args['show_in_rest'] = false;
		}

		return $args;
	}

	/**
	 * Hook into the REST API request dispatch process (before REST endpoint runs) for custom overrides.
	 *
	 * @since 4.11.5
	 *
	 * @param mixed           $dispatch_result Dispatch result, will be used if not empty.
	 * @param WP_REST_Request $request         Request used to generate the response.
	 * @param string          $route           Route matched for the request.
	 *
	 * @return mixed Unmodified dispatch result.
	 */
	public function filter_rest_dispatch_request( $dispatch_result, $request, $route ) {
		// Only disable meta updates from the normal WP endpoints for post/meta.
		if ( 0 !== strpos( $route, '/wp/' ) ) {
			return $dispatch_result;
		}

		// Don't get virtual meta.
		add_filter(
			'get_post_metadata',
			[ $this, 'register_tickets_list_in_rest' ],
			15,
			4
		);

		// Don't delete virtual meta.
		add_filter(
			'delete_post_metadata',
			[ $this, 'delete_tickets_list_in_rest' ],
			15,
			3
		);

		// Don't update virtual meta.
		add_filter(
			'update_post_metadata',
			[ $this, 'update_tickets_list_in_rest' ],
			15,
			3
		);

		// Don't update global stock meta.
		add_filter(
			'update_post_metadata',
			[ $this, 'update_global_stock_meta_in_rest' ],
			15,
			3
		);

		return $dispatch_result;
	}

	/**
	 * Make sure the value of the "virtual" meta is up to date with the correct ticket values
	 * as can be modified by removing or adding a plugin outside of the blocks editor the ticket
	 * can be added by React if is part of the diff of non created blocks
	 *
	 * @since 4.9
	 *
	 * @param mixed $value
	 * @param int $post_id
	 * @param string $meta_key
	 * @param bool $single
	 *
	 * @return array
	 */
	public function register_tickets_list_in_rest( $value, $post_id, $meta_key, $single ) {
		if ( '_tribe_tickets_list' !== $meta_key ) {
			return $value;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		$list_of_tickets = [];

		foreach ( $tickets as $ticket ) {
			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| 'Tribe__Tickets__RSVP' === $ticket->provider_class
			) {
				continue;
			}

			$list_of_tickets[] = $ticket->ID;
		}

		return $list_of_tickets;
	}

	/**
	 * Don't delete virtual meta.
	 *
	 * @since 4.10.11.1
	 *
	 * @param null|bool $delete            Whether to allow metadata deletion of the given type.
	 * @param int       $unused_object_id  Object ID.
	 * @param string    $meta_key          Meta key.
	 *
	 * @return bool
	 */
	public function delete_tickets_list_in_rest( $delete, $unused_object_id, $meta_key ) {
		$ghost_meta_fields = $this->get_ghost_meta_fields();

		if ( isset( $ghost_meta_fields[ $meta_key ] ) ) {
			return true;
		}

		return $delete;
	}

	/**
	 * Don't update virtual meta.
	 *
	 * @since 4.10.11.1
	 *
	 * @param null|bool $check             Whether to allow updating metadata for the given type.
	 * @param int       $unused_object_id  Object ID.
	 * @param string    $meta_key          Meta key.
	 *
	 * @return bool
	 */
	public function update_tickets_list_in_rest( $check, $unused_object_id, $meta_key ) {
		$ghost_meta_fields = $this->get_ghost_meta_fields();

		if ( isset( $ghost_meta_fields[ $meta_key ] ) ) {
			return true;
		}

		return $check;
	}

	/**
	 * Don't update global stock meta that's handled elsewhere.
	 *
	 * @since 4.11.5
	 *
	 * @param null|bool $check             Whether to allow updating metadata for the given type.
	 * @param int       $unused_object_id  Object ID.
	 * @param string    $meta_key          Meta key.
	 *
	 * @return bool
	 */
	public function update_global_stock_meta_in_rest( $check, $unused_object_id, $meta_key ) {
		$global_stock_meta_fields = $this->get_global_stock_meta_fields();

		if ( isset( $global_stock_meta_fields[ $meta_key ] ) ) {
			return true;
		}

		return $check;
	}

	/**
	 * Get ghost meta fields that we don't actually store/update/delete.
	 *
	 * @since 4.10.11.1
	 *
	 * @return array List of ghost meta fields.
	 */
	public function get_ghost_meta_fields() {
		return [
			'_tribe_tickets_list'                    => 1,
			'_tribe_ticket_going_count'              => 1,
			'_tribe_ticket_not_going_count'          => 1,
			'_tribe_ticket_has_attendee_info_fields' => 1,
		];
	}

	/**
	 * Get global stock meta fields that we don't actually want to update/delete from normal WP REST routes.
	 *
	 * @since 4.11.5
	 *
	 * @return array List of global stock meta fields.
	 */
	public function get_global_stock_meta_fields() {
		return [
			Tribe__Tickets__Global_Stock::GLOBAL_STOCK_ENABLED => 1,
			Tribe__Tickets__Global_Stock::GLOBAL_STOCK_LEVEL   => 1,
			Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE    => 1,
			Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP     => 1,
		];
	}
}
