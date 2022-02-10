<?php
/**
 * Class CFF_Response
 *
 * Sends back ajax response to client end
 *
 * @since 4.0
 */
namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CFF_Response {
	/**
	 * Response constructor.
	 *
	 * @param $is_success
	 * @param $data
	 *
	 * @throws \Exception
	 */
	public function __construct( $is_success, $data ) {
		$is_success ? wp_send_json_success( $data ) : wp_send_json_error( $data );
		wp_die();
	}
}
