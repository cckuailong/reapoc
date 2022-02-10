<?php


/**
 * Class Tribe__Tickets__REST__V1__Main
 *
 * The main entry point for ET REST API.
 *
 * This class should not contain business logic and merely set up and start the ET REST API support.
 */
class Tribe__Tickets__REST__V1__Main extends Tribe__REST__Main {

	/**
	 * Event Tickets REST API URL prefix.
	 *
	 * This prefx is appended to the Modern Tribe REST API URL ones.
	 *
	 * @var string
	 */
	protected $url_prefix = '/tickets/v1';

	/**
	 * @var array
	 */
	protected $registered_endpoints = array();

	/**
	 * Hooks the filters and actions required for the REST API support to kick in.
	 *
	 * @since 4.7.5
	 *
	 */
	public function hook() {
		$this->hook_headers();
		$this->hook_settings();

		/** @var Tribe__Tickets__REST__V1__System $system */
		$system = tribe( 'tickets.rest-v1.system' );

		if ( ! $system->supports_et_rest_api() || ! $system->et_rest_api_is_enabled() ) {
			return;
		}

	}

	/**
	 * Hooks the additional headers and meta tags related to the REST API.
	 *
	 * @since 4.7.5
	 *
	 */
	protected function hook_headers() {
		/** @var Tribe__Tickets__REST__V1__System $system */
		$system = tribe( 'tickets.rest-v1.system' );
		/** @var Tribe__REST__Headers__Base_Interface $headers_base */
		$headers_base = tribe( 'tickets.rest-v1.headers-base' );

		if ( ! $system->et_rest_api_is_enabled() ) {
			if ( ! $system->supports_et_rest_api() ) {
				tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Unsupported( $headers_base, $this ) );
			} else {
				tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Disabled( $headers_base ) );
			}
		} else {
			tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Supported( $headers_base, $this ) );
		}

		add_action( 'wp_head', tribe_callback( 'tickets.rest-v1.headers', 'add_header' ) );
		add_action( 'template_redirect', tribe_callback( 'tickets.rest-v1.headers', 'send_header' ), 11 );
	}

	/**
	 * Hooks the additional Event Tickets Settings related to the REST API.
	 *
	 * @since 4.7.5
	 *
	 */
	protected function hook_settings() {
		add_filter( 'tribe_addons_tab_fields', tribe_callback( 'tickets.rest-v1.settings', 'filter_tribe_addons_tab_fields' ) );
	}

	/**
	 * Returns the URL where the API users will find the API documentation.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_reference_url() {
		return esc_url( 'https://theeventscalendar.com/' );
	}

	/**
	 * Returns the semantic version for REST API
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_semantic_version() {
		return '1.0.0';
	}

	/**
	 * Returns the events REST API namespace string that should be used to register a route.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_events_route_namespace() {
		return $this->get_namespace() . '/tickets/' . $this->get_version();
	}

	/**
	 * Returns the string indicating the REST API version.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_version() {
		return 'v1';
	}


	/**
	 * Returns the REST API URL prefix that will be appended to the namespace.
	 *
	 * The prefix should be in the `/some/path` format.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	protected function url_prefix() {
		return $this->url_prefix;
	}

}
