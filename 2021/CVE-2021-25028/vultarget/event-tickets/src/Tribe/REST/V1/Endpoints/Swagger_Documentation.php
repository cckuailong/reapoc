<?php

class Tribe__Tickets__REST__V1__Endpoints__Swagger_Documentation
	implements Tribe__REST__Endpoints__READ_Endpoint_Interface,
	Tribe__Documentation__Swagger__Provider_Interface,
	Tribe__Documentation__Swagger__Builder_Interface {

	/**
	 * @var string
	 */
	protected $open_api_version = '3.0.0';

	/**
	 * @var string
	 */
	protected $tec_rest_api_version;

	/**
	 * @var Tribe__Documentation__Swagger__Provider_Interface[]
	 */
	protected $documentation_providers = array();

	/**
	 * @var Tribe__Documentation__Swagger__Provider_Interface[]
	 */
	protected $definition_providers = array();

	/**
	 * Tribe__Events__REST__V1__Endpoints__Swagger_Documentation constructor.
	 *
	 * @since 4.7.5
	 *
	 * @param string $tec_rest_api_version
	 */
	public function __construct( $tec_rest_api_version ) {
		$this->tec_rest_api_version = $tec_rest_api_version;
	}

	/**
	 * Handles GET requests on the endpoint.
	 *
	 * @since 4.7.5
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function get( WP_REST_Request $request ) {
		$data = $this->get_documentation();

		return new WP_REST_Response( $data );
	}

	/**
	 * Returns an array in the format used by Swagger 2.0.
	 *
	 * @since 4.7.5
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
		/** @var Tribe__Tickets__REST__V1__Main $main */
		$main = tribe( 'tickets.rest-v1.main' );

		$documentation = array(
			'openapi'    => $this->open_api_version,
			'info'       => $this->get_api_info(),
			'servers'    => array(
				array(
					'url' => $main->get_url(),
				),
			),
			'paths'      => $this->get_paths(),
			'components' => array( 'schemas' => $this->get_definitions() ),
		);

		/**
		 * Filters the Swagger documentation generated for the TEC REST API.
		 *
		 * @since 4.7.5
		 *
		 * @param array                                                     $documentation An associative PHP array in the format supported by Swagger.
		 * @param Tribe__Tickets__REST__V1__Endpoints__Swagger_Documentation $this          This documentation endpoint instance.
		 *
		 * @link http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_rest_swagger_documentation', $documentation, $this );

		return $documentation;
	}

	/**
	 * Get Event Tickets REST API Info
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	protected function get_api_info() {
		return array(
			'title'       => __( 'Event Tickets REST API', 'event-tickets' ),
			'description' => __( 'Event Tickets REST API allows accessing ticket information easily and conveniently.', 'event-tickets' ),
			'version'     => $this->tec_rest_api_version,
		);
	}

	/**
	 * Get Event Tickets REST API Path
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	protected function get_paths() {
		$paths = array();
		foreach ( $this->documentation_providers as $path => $endpoint ) {
			if ( $endpoint !== $this ) {
				/** @var Tribe__Documentation__Swagger__Provider_Interface $endpoint */
				$documentation = $endpoint->get_documentation();
			} else {
				$documentation = $this->get_own_documentation();
			}
			$paths[ $path ] = $documentation;
		}

		return $paths;
	}

	/**
	 * Registers a documentation provider for a path.
	 *
	 * @since 4.7.5
	 *
	 * @param                                            $path
	 * @param Tribe__Documentation__Swagger__Provider_Interface $endpoint
	 */
	public function register_documentation_provider( $path, Tribe__Documentation__Swagger__Provider_Interface $endpoint ) {
		$this->documentation_providers[ $path ] = $endpoint;
	}

	/**
	 * Get REST API Documentation
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	protected function get_own_documentation() {
		return array(
			'get' => array(
				'responses' => array(
					'200' => array(
						'description' => __( 'Returns the documentation for Event Tickets REST API in Swagger consumable format.', 'event-tickets' ),
					),
				),
			),
		);
	}

	/**
	 * Get REST API Definitions
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	protected function get_definitions() {
		$definitions = array();
		/** @var Tribe__Documentation__Swagger__Provider_Interface $provider */
		foreach ( $this->definition_providers as $type => $provider ) {
			$definitions[ $type ] = $provider->get_documentation();
		}

		return $definitions;
	}

	/**
	 * Get REST API Registered Documentation Providers
	 *
	 * @since 4.7.5
	 *
	 * @return Tribe__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_documentation_providers() {
		return $this->documentation_providers;
	}

	/**
	 * Registers a documentation provider for a definition.
	 *
	 * @since 4.7.5
	 *
	 * @param                                                  string $type
	 * @param Tribe__Documentation__Swagger__Provider_Interface       $provider
	 */
	public function register_definition_provider( $type, Tribe__Documentation__Swagger__Provider_Interface $provider ) {
		$this->definition_providers[ $type ] = $provider;
	}

	/**
	 * Get Documentation Provider Interface
	 *
	 * @since 4.7.5
	 *
	 * @return Tribe__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_definition_providers() {
		return $this->definition_providers;
	}

	/**
	 * Returns the content of the `args` array that should be used to register the endpoint
	 * with the `register_rest_route` function.
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	public function READ_args() {
		return array();
	}
}
