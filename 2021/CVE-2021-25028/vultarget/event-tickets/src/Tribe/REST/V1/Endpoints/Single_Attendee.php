<?php

class Tribe__Tickets__REST__V1__Endpoints__Single_Attendee
	extends Tribe__Tickets__REST__V1__Endpoints__Base
	implements Tribe__REST__Endpoints__READ_Endpoint_Interface,
	Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$GET_defaults = array( 'in' => 'query', 'default' => '', 'type' => 'string' );

		return array(
			'get' => array(
				'parameters' => $this->swaggerize_args( $this->READ_args(), $GET_defaults ),
				'responses'  => array(
					'200' => array(
						'description' => __( 'Returns the data of the attendee with the specified post ID', 'ticket-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
									'$ref' => '#/components/schemas/Attendee',
								),
							),
						),
					),
					'400' => array(
						'description' => __( 'The attendee post ID is invalid.', 'ticket-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
									'type' => 'object',
								),
							),
						),
					),
					'401' => array(
						'description' => __( 'The attendee with the specified ID is not accessible.', 'ticket-tickets' ),
						'content'     => array(
							'application/json' => array(
								'schema' => array(
									'type' => 'object',
								),
							),
						),
					),
					'404' => array(
						'description' => __( 'An attendee with the specified ID does not exist.', 'ticket-tickets' ),
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
	 * {@inheritdoc}
	 */
	public function READ_args() {
		return array(
			'id' => array(
				'type'              => 'integer',
				'in'                => 'path',
				'description'       => __( 'The attendee post ID', 'event-tickets' ),
				'required'          => true,
				/**
				 * Here we check for a positive int, not an attendee ID to properly
				 * return 404 for missing post in place of 400.
				 */
				'validate_callback' => array( $this->validator, 'is_positive_int' ),
			),
		);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since 4.12.0 Returns 401 Unauthorized if Event Tickets Plus is not loaded.
	 */
	public function get( WP_REST_Request $request ) {
		// Early bail: ET Plus must be active to use this endpoint.
		if ( ! class_exists( 'Tribe__Tickets_Plus__Main' ) ) {
			return new WP_REST_Response( __( 'Sorry, Event Tickets Plus must be active to use this endpoint.', 'event-tickets' ), 401 );
		}

		return tribe_attendees( 'restv1' )->by_primary_key( $request['id'] );
	}
}
