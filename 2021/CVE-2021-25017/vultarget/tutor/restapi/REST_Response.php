<?php
/*
@Reliable for give rest response
@author : themeum
*/

namespace TUTOR;
use WP_REST_Response;

if( ! defined('ABSPATH')) 
exit;

trait REST_Response {
	/*
		@send WP_REST_Response with 
		code, message along with data
	*/
	public static function send(array $response) {
		return new WP_REST_Response($response);
	} 
}
