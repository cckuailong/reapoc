<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Error\Notice\RestApiDisabledNotice;

/**
 * Checks for configuration errors about disabled REST API.
 */
class RestApiDisabledDetector implements ErrorDetector {

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		if ( ( apply_filters( 'rest_enabled', true ) === true )
			&& ( apply_filters( 'rest_jsonp_enabled', true ) === true )
			&& ( apply_filters( 'rest_authentication_errors', true ) === true ) ) {
			return null;
		}

		return new RestApiDisabledNotice();
	}
}
