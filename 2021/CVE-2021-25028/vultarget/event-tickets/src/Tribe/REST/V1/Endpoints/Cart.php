<?php

class Tribe__Tickets__REST__V1__Endpoints__Cart
	extends Tribe__Tickets__REST__V1__Endpoints__Base
	implements Tribe__REST__Endpoints__READ_Endpoint_Interface,
	Tribe__REST__Endpoints__UPDATE_Endpoint_Interface,
	Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * @var bool Whether this endpoint is currently active.
	 */
	public $is_active = false;

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function get_documentation() {
		$get_defaults = [
			'in'      => 'query',
			'default' => '',
		];

		$post_defaults = [
			'in'      => 'formData',
			'default' => '',
			'type'    => 'string',
		];

		return [
			'get'  => [
				'parameters' => $this->swaggerize_args( $this->READ_args(), $get_defaults ),
				'responses'  => [
					'200' => [
						'description' => __( 'Returns the list of tickets in the cart', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type'       => 'object',
									'properties' => [
										'tickets'      => [
											'type'        => 'array',
											'description' => __( 'The list of tickets and their quantities in the cart', 'event-tickets' ),
										],
										'meta'         => [
											'type'        => 'array',
											'description' => __( 'The list of meta for each ticket item in the cart', 'event-tickets' ),
										],
										'cart_url'     => [
											'type'        => 'string',
											'description' => __( 'The provider cart URL', 'event-tickets' ),
										],
										'checkout_url' => [
											'type'        => 'string',
											'description' => __( 'The provider checkout URL', 'event-tickets' ),
										],
									],
								],
							],
						],
					],
					'403' => [
						'description' => __( 'The post does not have any tickets', 'event-tickets' ),
					],
				],
			],
			'post' => [
				'consumes'   => [ 'application/x-www-form-urlencoded' ],
				'parameters' => $this->swaggerize_args( $this->EDIT_args(), $post_defaults ),
				'responses'  => [
					'200' => [
						'description' => __( 'Returns the updated list of tickets in the cart and cart details', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type'       => 'object',
									'properties' => [
										'tickets'      => [
											'type'        => 'array',
											'description' => __( 'The list of tickets and their quantities in the cart', 'event-tickets' ),
										],
										'meta'         => [
											'type'        => 'array',
											'description' => __( 'The list of meta for each ticket item in the cart', 'event-tickets' ),
										],
										'cart_url'     => [
											'type'        => 'string',
											'description' => __( 'The provider cart URL', 'event-tickets' ),
										],
										'checkout_url' => [
											'type'        => 'string',
											'description' => __( 'The provider checkout URL', 'event-tickets' ),
										],
									],
								],
							],
						],
					],
					'400' => [
						'description' => __( 'The post ID is invalid.', 'ticket-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type' => 'object',
								],
							],
						],
					],
					'403' => [
						'description' => __( 'The post does not have any tickets', 'event-tickets' ),
					],
				],
			],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function get( WP_REST_Request $request ) {
		$this->is_active = true;

		$post_id   = $request->get_param( 'post_id' );
		$providers = $request->get_param( 'provider' );

		if ( 0 < $post_id ) {
			// Confirm post has tickets.
			$has_tickets = ! empty( Tribe__Tickets__Tickets::get_all_event_tickets( $post_id ) );

			if ( ! $has_tickets ) {
				$message = $this->messages->get_message( 'post-has-no-tickets' );

				return new WP_Error( 'post-has-no-tickets', $message, [ 'status' => 403 ] );
			}
		}

		if ( null === $providers ) {
			$providers = [];
		}

		$providers = (array) $providers;

		/** @var Tribe__Tickets__Commerce__Cart $cart */
		$cart = tribe( 'tickets.commerce.cart' );

		$response = $cart->get( [
			'post_id'   => $post_id,
			'providers' => $providers,
		] );

		if ( is_wp_error( $response ) ) {
			$error_code = $response->get_error_code();

			// Use message using error code if message is not yet set.
			if ( $error_code === $response->get_error_message() ) {
				$response->errors[ $error_code ] = $this->messages->get_message( $error_code );
			}

			return $response;
		}

		return new WP_REST_Response( $response );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function READ_args() {
		return [
			'provider' => [
				'required'          => false,
				'description'       => __( 'Limit results to tickets provided by one of the providers specified in the CSV list or array; defaults to all available.', 'event-tickets' ),
				'sanitize_callback' => [
					'Tribe__Utils__Array',
					'list_to_array',
				],
				'swagger_type'      => [
					'oneOf' => [
						[
							'type'  => 'array',
							'items' => [
								'type' => 'string',
							],
						],
						[
							'type' => 'string',
						],
					],
				],
			],
			'post_id'  => [
				'required'          => false,
				'type'              => 'integer',
				'description'       => __( 'The post ID', 'event-tickets' ),
				'validate_callback' => [ $this->validator, 'is_post_id' ],
			],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function update( WP_REST_Request $request ) {
		$this->is_active = true;

		$post_id  = $request->get_param( 'post_id' );
		$provider = $request->get_param( 'provider' );
		$tickets  = $request->get_param( 'tickets' );
		$meta     = $request->get_param( 'meta' );

		if ( 0 < $post_id ) {
			// Confirm post has tickets.
			$has_tickets = ! empty( Tribe__Tickets__Tickets::get_all_event_tickets( $post_id ) );

			if ( ! $has_tickets ) {
				$message = $this->messages->get_message( 'post-has-no-tickets' );

				return new WP_Error( 'post-has-no-tickets', $message, [ 'status' => 403 ] );
			}
		}

		/** @var Tribe__Tickets__Commerce__Cart $cart */
		$cart = tribe( 'tickets.commerce.cart' );

		$response = $cart->update( [
			'post_id'  => $post_id,
			'provider' => $provider,
			'tickets'  => $tickets,
			'meta'     => $meta,
			'additive' => false,
		] );

		if ( is_wp_error( $response ) ) {
			$error_code = $response->get_error_code();

			// Use message using error code if message is not yet set.
			if ( $error_code === $response->get_error_message() ) {
				$response->errors[ $error_code ] = $this->messages->get_message( $error_code );
			}

			return $response;
		}

		// Get the updated cart details.
		return $this->get( $request );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function EDIT_args() {
		return [
			'provider' => [
				'required'    => true,
				'type'        => 'string',
				'description' => __( 'The cart provider', 'event-tickets' ),
			],
			'tickets'  => [
				'required'     => false,
				'default'      => null,
				'swagger_type' => 'array',
				'description'  => __( 'List of tickets with their ID and quantity', 'event-tickets' ),
			],
			'meta'     => [
				'required'     => false,
				'default'      => null,
				'swagger_type' => 'array',
				'description'  => __( 'List of meta for each ticket to be saved for Attendee Registration', 'event-tickets' ),
			],
			'post_id'  => [
				'required'          => false,
				'type'              => 'integer',
				'description'       => __( 'The post ID', 'event-tickets' ),
				'validate_callback' => [ $this->validator, 'is_post_id' ],
			],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.11.0
	 */
	public function can_edit() {
		// Everyone can edit their own cart.
		return true;
	}
}
