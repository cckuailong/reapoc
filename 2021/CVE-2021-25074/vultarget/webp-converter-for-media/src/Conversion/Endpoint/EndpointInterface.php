<?php

namespace WebpConverter\Conversion\Endpoint;

/**
 * Interface for class that supports endpoint.
 */
interface EndpointInterface {

	/**
	 * Returns route of endpoint.
	 *
	 * @return string Endpoint route.
	 */
	public function get_route_name(): string;

	/**
	 * Returns list of params for endpoint.
	 *
	 * @return array[] Params of endpoint.
	 */
	public function get_route_args(): array;

	/**
	 * Returns URL of endpoint.
	 *
	 * @return string Endpoint URL.
	 */
	public function get_route_url(): string;

	/**
	 * Returns response to endpoint.
	 *
	 * @param \WP_REST_Request $request REST request object.
	 *
	 * @return \WP_REST_Response|\WP_Error REST response object or WordPress Error object.
	 * @internal
	 */
	public function get_route_response( \WP_REST_Request $request );
}
