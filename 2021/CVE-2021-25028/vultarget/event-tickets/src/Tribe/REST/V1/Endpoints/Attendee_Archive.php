<?php

class Tribe__Tickets__REST__V1__Endpoints__Attendee_Archive
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
						'description' => __( 'Returns all the attendees matching the search criteria', 'event-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
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
										'attendees'   => array(
											'type'  => 'array',
											'items' => array( '$ref' => '#/components/schemas/Attendee' ),
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
	 * @since 4.12.0 Returns 401 Unauthorized if Event Tickets Plus is not loaded.
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function get( WP_REST_Request $request ) {
		// Early bail: ET Plus must be active to use this endpoint.
		if ( ! class_exists( 'Tribe__Tickets_Plus__Main' ) ) {
			return new WP_REST_Response( __( 'Sorry, Event Tickets Plus must be active to use this endpoint.', 'event-tickets' ), 401 );
		}

		$query_args = $request->get_query_params();
		$page  = $request['page'];
		$per_page = $request['per_page'];

		$fetch_args = array();

		$supported_args = array(
			'provider'                       => 'provider',
			'search'                         => 's',
			'post_id'                        => 'event',
			'ticket_id'                      => 'ticket',
			'include_post'                   => 'event',
			'include_ticket'                 => 'ticket',
			'exclude_post'                   => 'event__not_in',
			'exclude_ticket'                 => 'ticket__not_in',
			'after'                          => 'after_date',
			'before'                         => 'before_date',
			'include'                        => 'post__in',
			'exclude'                        => 'post__not_in',
			'order'                          => 'order',
			'post_status'                    => 'event_status',
			'status'                         => 'post_status',
			'order_status'                   => 'order_status',
			'checkedin'                      => 'checkedin',
			'rsvp_going_status'              => 'rsvp_status__or_none',
			'price_min'                      => 'price_min',
			'price_max'                      => 'price_max',
			'attendee_information_available' => 'has_attendee_meta',
		);

		foreach ( $supported_args as $request_arg => $query_arg ) {
			if ( isset( $request[ $request_arg ] ) ) {
				$fetch_args[ $query_arg ] = $request[ $request_arg ];
			}
		}

		if ( current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' ) ) {
			$permission                 = Tribe__Tickets__REST__V1__Attendee_Repository::PERMISSION_EDITABLE;
			$fetch_args['post_status']  = Tribe__Utils__Array::get( $fetch_args, 'post_status', 'any' );
			$fetch_args['event_status'] = Tribe__Utils__Array::get( $fetch_args, 'event_status', 'any' );
			$fetch_args['order_status'] = Tribe__Utils__Array::get( $fetch_args, 'order_status', 'any' );
		} else {
			$permission                 = Tribe__Tickets__REST__V1__Attendee_Repository::PERMISSION_READABLE;
			$fetch_args['post_status']  = Tribe__Utils__Array::get( $fetch_args, 'post_status', 'publish' );
			$fetch_args['event_status'] = Tribe__Utils__Array::get( $fetch_args, 'event_status', 'publish' );
			$fetch_args['order_status'] = Tribe__Utils__Array::get( $fetch_args, 'order_status', 'public' );
		}

		$query = tribe_attendees( 'restv1' )
			->by_args( $fetch_args )
			->permission( $permission );

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
			$attendees = array();
		} elseif ( 1 !== $page && $page * $per_page > $found ) {
			return new WP_Error( 'invalid-page-number', $this->messages->get_message( 'invalid-page-number' ), array( 'status' => 400 ) );
		} else {
			$attendees = $query
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

		$data['rest_url']    = add_query_arg( $query_args, $main->get_url( '/attendees/' ) );
		$data['total']       = $found;
		$data['total_pages'] = (int) ceil( $found / $per_page );
		$data['attendees']   = $attendees;

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
			'page'     => array(
				'description'       => __( 'The page of results to return; defaults to 1', 'event-tickets' ),
				'type'              => 'integer',
				'required'          => false,
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'How many attendees to return per results page; defaults to posts_per_page.', 'event-tickets' ),
				'type'              => 'integer',
				'required'          => false,
				'default'           => get_option( 'posts_per_page' ),
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'provider' => array(
				'description'       => __( 'Limit results to attendees whose ticket is provided by one of the providers specified in the CSV list or array; defaults to all the available.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_string' ),
				'sanitize_callback' => array( $this->validator, 'trim' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'search'   => array(
				'description'       => __( 'Limit results to attendees containing the specified string in the title or description.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_string' ),
			),
			'offset'  => array(
				'description' => __( 'Offset the results by a specific number of items.', 'event-tickets' ),
				'type'        => 'integer',
				'required'    => false,
				'min'         => 0,
			),
			'order' => array(
				'description' => __( 'Sort results in ASC or DESC order. Defaults to ASC.', 'event-tickets' ),
				'type'        => 'string',
				'required'    => false,
				'enum'        => array(
					'ASC',
					'DESC',
				),
			),
			'orderby' => array(
				'description' => __( 'Order the results by one of date, relevance, id, include, title or slug. Defaults to id.', 'event-tickets' ),
				'type'        => 'string',
				'required'    => false,
				'enum'        => array(
					'id',
					'date',
					'include',
					'title',
					'slug',
				),
			),
			'post_id'  => array(
				'description'       => __( 'Limit results to attendees by post the ticket is associated with.', 'event-tickets' ),
				'type'              => 'integer',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_post_id' ),
			),
			'ticket_id' => array(
				'description'       => __( 'Limit results to attendees associated with a ticket.', 'event-tickets' ),
				'type'              => 'integer',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_ticket_id' ),
			),
			'after' => array(
				'description'       => __( 'Limit results to attendees created after or on the specified UTC date or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'before' => array(
				'description'       => __( 'Limit results to attendees created before or on the specified UTC date or timestamp.', 'event-tickets' ),
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_time' ),
			),
			'include' => array(
				'description'       => __( 'Limit results to a specific CSV list or array of attendee IDs.', 'event-tickets' ),
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
			'exclude' => array(
				'description'       => __( 'Exclude a specific CSV list or array of attendee IDs from the results.', 'event-tickets' ),
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
			'price_max' => array(
				'description' => __( 'Limit results to attendees that paid tickets a price equal or below the specified value; if not specified no maximum price limit will be used.', 'event-tickets' ),
				'type'        => 'integer',
				'min'         => 0,
				'required'    => false,
			),
			'price_min' => array(
				'description' => __( 'Limit results to attendees that paid tickets a price equal or above the specified value; if not specified no minimum price limit will be used.', 'event-tickets' ),
				'type'        => 'integer',
				'min'         => 0,
				'required'    => false,
			),
			'include_post'   => array(
				'description'       => __( 'Limit results to attendees whose ticket is assigned to one of the posts specified in the CSV list or array.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_post_id_list' ),
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'exclude_post'   => array(
				'description'       => __( 'Limit results to attendees whose tickets is not assigned to any of the posts specified in the CSV list or array..', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_post_id_list' ),
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'include_ticket' => array(
				'description'       => __( 'Limit results to a specific CSV list or array of ticket IDs.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_ticket_id_list' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'exclude_ticket' => array(
				'description'       => __( 'Exclude a specific CSV list or array of ticket IDs.', 'event-tickets' ),
				'required'          => false,
				'validate_callback' => array( $this->validator, 'is_ticket_id_list' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
						array( 'type' => 'string' ),
						array( 'type' => 'integer' ),
					),
				),
			),
			'post_status' => array(
				'description'       => __( 'Limit results to attendees for posts that are in one of the post statuses specified in the CSV list or array; defaults to publish.', 'event-tickets' ),
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
				'description'       => __( 'Limit results to attendees that are in one of post statuses specified in the CSV list or array; defaults to publish.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'order_status' => array(
				'description'       => __( 'Limit results to attendees whose order status is in one of post statuses specified in the CSV list or array; defaults to public.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'checkedin' => array(
				'description'       => __( 'Limit results to attendees that are or not checked-in.', 'event-tickets' ),
				'required'          => false,
				'type'           => 'boolean',
			),
			'rsvp_going_status' => array(
				'description'       => __( 'Limit results to RSVP Attendees that have one of the RSVP Going status specified in the CSV list or array.', 'event-tickets' ),
				'required'          => false,
				'sanitize_callback' => array( 'Tribe__Utils__Array', 'list_to_array' ),
				'swagger_type' => array(
					'oneOf' => array(
						array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
						array( 'type' => 'string' ),
					),
				),
			),
			'attendee_information_available' => array(
				'description'       => __( 'Limit results to attendees for tickets that provide attendees the possibility to fill in additional information or not; requires ET+.', 'event-tickets' ),
				'required'          => false,
				'type'           => 'boolean',
			),
		);
	}
}
