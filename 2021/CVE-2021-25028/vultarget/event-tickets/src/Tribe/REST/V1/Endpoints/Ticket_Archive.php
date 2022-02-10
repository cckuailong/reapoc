<?php

class Tribe__Tickets__REST__V1__Endpoints__Ticket_Archive
	extends Tribe__Tickets__REST__V1__Endpoints__Base
	implements Tribe__REST__Endpoints__READ_Endpoint_Interface,
	Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * Returns an array in the format used by Swagger 2.0.
	 *
	 * While the structure must conform to that used by v2.0 of Swagger the structure can be that of a full document
	 * or that of a document part.
	 * The intelligence lies in the "gatherer" of informations rather than in the single "providers" implementing this
	 * interface.
	 *
	 * @link http://swagger.io/
	 *
	 * @return array An array description of a Swagger supported component.
	 */
	public function get_documentation() {
		return array(
			'get' => array(
				'parameters' => $this->swaggerize_args( $this->READ_args(), array( 'in' => 'query', 'default' => '' ) ),
				'responses'  => array(
					'200' => array(
						'description' => __( 'Returns all the tickets matching the search criteria', 'event-tickets' ),
						'content' => array(
							'application/json' => array(
								'schema'      => array(
									'type'       => 'object',
									'properties' => array(
										'rest_url'    => array(
											'type'        => 'string',
											'format'      => 'uri',
											'description' => __( 'This results page REST URL', 'event-tickets' ),
										),
										'total'       => array(
											'type'       => 'integer',
											'description' => __( 'The total number of results across all pages', 'event-tickets' ),
										),
										'total_pages' => array(
											'type'       => 'integer',
											'description' => __( 'The total number of result pages matching the search criteria', 'event-tickets' ),
										),
										'tickets'   => array(
											'type'  => 'array',
											'items' => array( '$ref' => '#/components/schemas/Ticket' ),
										),
									),
								),
							),
						),
					),
					'400' => array(
						'description' => __( 'One or more of the specified query variables has a bad format', 'event-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
									'type' => 'object',
								),
							),
						),
					),
					'404' => array(
						'description' => __( 'The requested page was not found.', 'event-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
									'type' => 'object',
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Handles GET requests on the endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function get( WP_REST_Request $request ) {
		$query_args = $request->get_query_params();
		$per_page   = (int) $request->get_param( 'per_page' );
		$page       = (int) $request->get_param( 'page' );

		$fetch_args = array();

		$supported_args = array(
			'search'                         => 's',
			'include_post'                   => 'event',
			'exclude_post'                   => 'event_not_in',
			'is_available'                   => 'is_available',
			'provider'                       => 'provider',
			'after'                          => 'after_date',
			'before'                         => 'before_date',
			'include'                        => 'post__in',
			'exclude'                        => 'post__not_in',
			'available_from'                 => 'available_from',
			'available_until'                => 'available_until',
			'post_status'                    => 'event_status',
			'status'                         => 'post_status',
			'attendee_information_available' => 'has_attendee_meta',
			'currency'                       => 'currency_code',
		);

		$private_args = array(
			'attendees_min' => 'attendees_min',
			'attendees_max' => 'attendees_max',
			'checkedin_min' => 'checkedin_min',
			'checkedin_max' => 'checkedin_max',
			'capacity_min' => 'capacity_min',
			'capacity_max' => 'capacity_max',
		);

		foreach ( $supported_args as $request_arg => $query_arg ) {
			if ( isset( $request[ $request_arg ] ) ) {
				$fetch_args[ $query_arg ] = $request[ $request_arg ];
			}
		}

		$has_manage_access = current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' );

		$attendess_btwn = $checkedin_btwn = $capacity_btwn = null;

		if ( $has_manage_access ) {
			foreach ( $private_args as $request_arg => $query_arg ) {
				if ( isset( $request[ $request_arg ] ) ) {
					$fetch_args[ $query_arg ] = $request[ $request_arg ];
				}
			}

			if ( isset( $fetch_args['attendees_min'], $fetch_args['attendees_max'] ) ) {
				$attendess_btwn = array( $fetch_args['attendees_min'], $fetch_args['attendees_max'] );
				unset( $fetch_args['attendees_min'], $fetch_args['attendees_max'] );
			}

			if ( isset( $fetch_args['checkedin_min'], $fetch_args['checkedin_max'] ) ) {
				$checkedin_btwn = array( $fetch_args['checkedin_min'], $fetch_args['checkedin_max'] );
				unset( $fetch_args['checkedin_min'], $fetch_args['checkedin_max'] );
			}

			if ( isset( $fetch_args['capacity_min'], $fetch_args['capacity_max'] ) ) {
				$capacity_btwn = array( $fetch_args['capacity_min'], $fetch_args['capacity_max'] );
				unset( $fetch_args['capacity_min'], $fetch_args['capacity_max'] );
			}
		}

		if ( $has_manage_access ) {
			$permission                 = Tribe__Tickets__REST__V1__Ticket_Repository::PERMISSION_EDITABLE;
			$fetch_args['post_status']  = Tribe__Utils__Array::get( $fetch_args, 'post_status', 'any' );
			$fetch_args['event_status'] = Tribe__Utils__Array::get( $fetch_args, 'event_status', 'any' );
		} else {
			$permission                 = Tribe__Tickets__REST__V1__Ticket_Repository::PERMISSION_READABLE;
			$fetch_args['post_status']  = Tribe__Utils__Array::get( $fetch_args, 'post_status', 'publish' );
			$fetch_args['event_status'] = Tribe__Utils__Array::get( $fetch_args, 'event_status', 'publish' );
		}

		$query = tribe_tickets( 'restv1' )
			->by_args( $fetch_args )
			->permission( $permission );

		if ( null !== $attendess_btwn ) {
			$query->by( 'attendees_between', $attendess_btwn[0], $attendess_btwn[1] );
		}

		if ( null !== $checkedin_btwn ) {
			$query->by( 'checkedin_between', $checkedin_btwn[0], $checkedin_btwn[1] );
		}

		if ( null !== $capacity_btwn ) {
			$query->by( 'capacity_between', $capacity_btwn[0], $capacity_btwn[1] );
		}

		if ( $request['order'] ) {
			$query->order( $request['order'] );
		}

		if ( $request['orderby'] ) {
			$query->order_by( $request['orderby'] );
		}

		if ( $request['offset'] ) {
			$query->offset( $request['offset'] );
		}

		$query_args = array_intersect_key( $query_args, $this->READ_args() );

		$found = $query->found();

		if ( 0 === $found && 1 === $page ) {
			$tickets = array();
		} elseif ( 1 !== $page && $page * $per_page > $found ) {
			return new WP_Error( 'invalid-page-number', $this->messages->get_message( 'invalid-page-number' ), array( 'status' => 400 ) );
		} else {
			$tickets = $query
				->per_page( $per_page )
				->page( $page )
				->all();
		}

		/** @var Tribe__Tickets__REST__V1__Main $main */
		$main = tribe( 'tickets.rest-v1.main' );

		// make sure all arrays are formatted to by CSV lists

		foreach ( $query_args as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = Tribe__Utils__Array::to_list( $value );
			}
		}

		$data['rest_url']    = add_query_arg( $query_args, $main->get_url( '/tickets/' ) );
		$data['total']       = $found;
		$data['total_pages'] = (int) ceil( $found / $per_page );
		$data['tickets']     = $tickets;

		$headers = array(
			'X-ET-TOTAL'       => $data['total'],
			'X-ET-TOTAL-PAGES' => $data['total_pages'],
		);

		return new WP_REST_Response( $data, 200, $headers );
	}

	/**
	 * Returns the content of the `args` array that should be used to register the endpoint
	 * with the `register_rest_route` function.
	 *
	 * @return array
	 */
	public function READ_args() {
		return array(
			'page'          => array(
				'description'       => __( 'The page of results to return; defaults to 1', 'event-tickets' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'minimum'           => 1,
			),
			'per_page'      => array(
				'description'       => __( 'How many tickets to return per results page; defaults to posts_per_page.', 'event-tickets' ),
				'type'              => 'integer',
				'default'           => get_option( 'posts_per_page' ),
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'search'        => array(
				'description'       => __( 'Limit results to tickets containing the specified string in the title or description.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_string' ),
			),
			'offset'        => array(
				'description' => __( 'Offset the results by a specific number of items.', 'event-tickets' ),
				'type'        => 'integer',
				'required'    => false,
				'min'         => 0,
			),
			'order'         => array(
				'description' => __( 'Sort results in ASC or DESC order. Defaults to ASC.', 'event-tickets' ),
				'type'        => 'string',
				'required'    => false,
				'enum'        => array(
					'ASC',
					'DESC',
				),
			),
			'orderby'       => array(
				'description' => __( 'Order the results by one of date, relevance, id, include, title, or slug; defaults to title.', 'event-tickets' ),
				'type'        => 'string',
				'required'    => false,
				'enum'        => array(
					'id',
					'include',
					'title',
					'slug',
				),
			),
			'is_available'  => array(
				'description' => __( 'Limit results to tickets that have or do not have capacity currently available.', 'event-tickets' ),
				'type'        => 'boolean',
				'required'    => false,
			),
			'provider'      => array(
				'description'       => __( 'Limit results to tickets provided by one of the providers specified in the CSV list or array; defaults to all available.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'after'         => array(
				'description'       => __( 'Limit results to tickets created after or on the specified UTC date or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'before'        => array(
				'description'       => __( 'Limit results to tickets created before or on the specified UTC date or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'include'       => array(
				'description'       => __( 'Limit results to a specific CSV list or array of ticket IDs.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_positive_int_list' ),
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'exclude'       => array(
				'description'       => __( 'Exclude a specific CSV list or array of ticket IDs from the results.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_positive_int_list' ),
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'include_post'  => array(
				'description'       => __( 'Limit results to tickets that are assigned to one of the posts specified in the CSV list or array.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_post_id_list' ),
				'sanitize_callback' => array( $this->validator, 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'exclude_post'  => array(
				'description'       => __( 'Limit results to tickets that are not assigned to any of the posts specified in the CSV list or array.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_post_id_list' ),
				'sanitize_callback' => array( $this->validator, 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'attendees_min' => array(
				'description' => __( 'Limit results to tickets that have at least this number or attendees.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'attendees_max' => array(
				'description' => __( 'Limit results to tickets that have at most this number of attendees.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'checkedin_min' => array(
				'description' => __( 'Limit results to tickets that have at most this number of checked-in attendee.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'checkedin_max' => array(
				'description' => __( 'Limit results to tickets that have at least this number of checked-in attendees.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'capacity_min' => array(
				'description' => __( 'Limit results to tickets that have at least this capacity.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'capacity_max' => array(
				'description' => __( 'Limit results to tickets that have at most this capacity.', 'event-tickets' ),
				'required'    => false,
				'type'        => 'integer',
				'min'         => 0,
			),
			'available_from'  => array(
				'description'       => __( 'Limit results to tickets that will be available at or after the specified UTC date (parseable by strtotime) or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'available_until' => array(
				'description'       => __( 'Limit results to tickets that will be available up to the specified UTC date (parseable by strtotime) or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'post_status' => array(
				'description'       => __( 'Limit results to tickets assigned to posts that are in one of the post statuses specified in the CSV list or array; defaults to publish.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'status' => array(
				'description'       => __( 'Limit results to tickets that are in one of post statuses specified in the CSV list or array; defaults to publish.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'currency' => array(
				'description'       => __( 'Limit results to tickets priced in one of the 3-letter currency codes specified in the CSV list or array.', 'event-tickets' ),
				'required'          => false,
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'attendee_information_available' => array(
				'description'       => __( 'Limit results to tickets that provide attendees the possibility to fill in additional information or not; requires ET+.', 'event-tickets' ),
				'required'          => false,
				'type'           => 'boolean',
			),
		);
	}
}
