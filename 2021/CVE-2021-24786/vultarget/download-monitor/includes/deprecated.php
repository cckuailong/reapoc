<?php
/**
 * Deprecated code, avoid using anything that's in this file
 */

/**
 * dlm_create_log function.
 *
 * @access public
 *
 * @deprecated 1.6.0
 *
 * @param string $type (default: '')
 * @param string $status (default: '')
 * @param string $message (default: '')
 * @param mixed $download
 * @param mixed $version
 *
 * @return void
 */
function dlm_create_log( $type = '', $status = '', $message = '', $download, $version ) {

	// Deprecated notice
	_deprecated_function( __FUNCTION__, '1.6.0', 'DLM_Logging->create_log()' );

	// Logging object
	$logging = new DLM_Logging();

	// Check if logging is enabled
	if( $logging->is_logging_enabled() ) {

		// Create log
		$logging->create_log( $type, $status, $message, $download, $version );

	}

}