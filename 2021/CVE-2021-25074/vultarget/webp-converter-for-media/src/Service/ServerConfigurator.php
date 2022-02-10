<?php

namespace WebpConverter\Service;

/**
 * Manages required server configuration.
 */
class ServerConfigurator {

	/**
	 * @return void
	 */
	public function set_memory_limit() {
		ini_set( 'memory_limit', '1G' ); // phpcs:ignore
	}

	/**
	 * @param int $seconds .
	 *
	 * @return void
	 */
	public function set_execution_time( int $seconds = 120 ) {
		if ( strpos( ini_get( 'disable_functions' ) ?: '', 'set_time_limit' ) === false ) {
			set_time_limit( $seconds );
		}
	}
}
