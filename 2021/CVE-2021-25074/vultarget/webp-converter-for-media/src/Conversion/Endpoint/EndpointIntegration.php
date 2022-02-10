<?php

namespace WebpConverter\Conversion\Endpoint;

use WebpConverter\HookableInterface;

/**
 * Integrates endpoint class by registering REST API route.
 */
class EndpointIntegration implements HookableInterface {

	const ROUTE_NAMESPACE = 'webp-converter/v1';

	/**
	 * Objects of supported REST API endpoints.
	 *
	 * @var EndpointInterface
	 */
	private $endpoint_object;

	public function __construct( EndpointInterface $endpoint_object ) {
		$this->endpoint_object = $endpoint_object;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
	}

	/**
	 * Registers new endpoint in REST API.
	 *
	 * @return void
	 * @internal
	 */
	public function register_rest_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			$this->endpoint_object->get_route_name(),
			[
				'methods'             => \WP_REST_Server::ALLMETHODS,
				'permission_callback' => function () {
					return ( wp_verify_nonce( $_REQUEST['_wpnonce'] ?? '', 'wp_rest' ) // phpcs:ignore
						&& current_user_can( 'manage_options' ) );
				},
				'callback'            => [ $this->endpoint_object, 'get_route_response' ],
				'args'                => $this->endpoint_object->get_route_args(),
			]
		);
	}
}
