<?php defined( 'ABSPATH' ) || exit;
/**
 * Handle a single request to MailChimp API v3
 *
 * This is based on the very simple v2 API wrapper from Drew McLellan. That
 * class, and this one, is licensed under the MIT.
 * @see https://github.com/drewm/mailchimp-api/
 *
 */
class mcrftbMailChimpRequest {

	/**
	 * MailChimp API key
	 *
	 * @param string
	 */
	private $api_key = '';

	/**
	 * API v3 endpoint URL
	 *
	 * @param string
	 */
	private $api_url = 'https://<dc>.api.mailchimp.com/3.0';

	/**
	 * Request details
	 *
	 * @param array {
	 *	string $method
	 *	string $endpoint
	 *	array $args
 	 * }
	 */
	private $request;

	/**
	 * Reponse to this request
	 *
	 * This stores the complete response from a wp_remote_get or wp_remote_post
	 * request. However, the helper method get_response() will retrieve just
	 * the body of the full response, which represents the data returned from
	 * the api request. The helper method get_response_complete() should be used
	 * if the metadata is desired.
	 *
	 * @param array
	 */
	private $response;

	/**
	 * Initialize a new request
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {

		$this->api_key = $api_key;
		$datacenter = explode('-', $this->api_key);
		$this->api_url = str_replace( '<dc>', $datacenter[1], $this->api_url );

	}

	/**
	 * Make a call to the API
	 *
	 * @param string $method HTTP method. Only GET and POST supported for now
	 * @param string $endpoint API endpoint to query, eg: /lists
	 * @param array $params Parameters to pass with the API request
	 */
	public function call( $endpoint = '',  $method = 'GET', $params = array() ) {

		$url = $this->api_url . $endpoint;

		$args = array(
			'headers' => array(
				'Authorization' => 'MailChimpForRestaurantReservations ' . $this->api_key,
			),
		);

		if ( !empty( $params ) ) {
			$args['body'] = json_encode( $params );
		}

		$args = apply_filters( 'mcfrtb_mailchimp_api_request_args', $args, $endpoint, $method, $params );

		$this->request = array(
			'method' => $method,
			'endpoint' => $endpoint,
			'args' => $args,
			'params' => $params,
		);

		$this->response = $method == 'GET' ? wp_remote_get( $url, $args ) : wp_remote_post( $url, $args );

		return $this;
	}

	/**
	 * Retrieve the cached response
	 */
	public function get_response() {

		if ( is_object( $this->response ) && get_class( $this->response ) == 'WP_Error' ) {
			return $this->response;
		}

		return isset( $this->response['body'] ) ? $this->response['body'] : null;
	}

	/**
	 * Retrieve the full details of the response array returned by wp_remote_get
	 * or wp_remote_post
	 */
	public function get_response_complete() {
		return $this->response;
	}

	/**
	 * Send the response for this request
	 *
	 * The response can be a successful or error response from MailChimp, or a
	 * WP_Error from wp_remote_get or wp_remote_post. This small wrapper around
	 * wp_send_json_* ensures any json is decoded
	 */
	public function send_json_response() {

		$response = $this->get_response();

		if ( empty( $response ) || ( is_object( $this->response ) && get_class( $response ) == 'WP_Error' ) ) {
			wp_send_json_error( $response );
		}

		wp_send_json_success( json_decode( $response ) );
	}

	/**
	 * Retrieve details about this request
	 */
	public function get_request() {
		return $this->request;
	}
}
