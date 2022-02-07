<?php

class DLM_Debug_Logger {

	const MSG_PREFIX = "DLM Notice: ";

	/**
	 * Returns if debugging is enabled
	 *
	 * @return bool
	 */
	private static function is_enabled() {
		return WP_DEBUG;
	}

	/**
	 * Log debug message
	 *
	 * @param string $message
	 */
	public static function log( $message ) {

		// Only continue if enabled
		if ( ! self::is_enabled() ) {
			return;
		}

		error_log( self::MSG_PREFIX . $message, 0 );

	}

	/**
	 * Log deprecated warning
	 *
	 * @param string $method
	 */
	public static function deprecated( $method ) {

		// Only continue if enabled
		if ( ! self::is_enabled() ) {
			return;
		}

		// Debug message
		$message = 'Deprecated method called: ' . $method;

		error_log( self::MSG_PREFIX . $message, 0 );
	}

}