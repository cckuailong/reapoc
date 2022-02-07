<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Logging class.
 */
class DLM_Logging {

	/**
	 * Check if logging is enabled
	 *
	 * @return bool
	 */
	public function is_logging_enabled() {
		return ( 1 == get_option( 'dlm_enable_logging', 0 ) );
	}

	/**
	 * Get the type of IP logging that is configured in settings
	 *
	 * @return string
	 */
	public function get_ip_logging_type() {
		$type = get_option( 'dlm_logging_ip_type', 'full' );
		if ( empty( $type ) ) {
			$type = 'full';
		}

		return $type;
	}

	public function is_ua_logging_enabled() {
		return (1==get_option('dlm_logging_ua', 1));
	}

	/**
	 * Check if 'dlm_count_unique_ips' is enabled
	 *
	 * @return bool
	 */
	public function is_count_unique_ips_only() {
		return ( '1' == get_option( 'dlm_count_unique_ips', 0 ) );
	}

	/**
	 * Check if visitor has downloaded version
	 *
	 * @param DLM_Download_Version $version
	 *
	 * @return bool
	 */
	public function has_ip_downloaded_version( $version ) {
		global $wpdb;

		return ( absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->download_log} WHERE `version_id` = %d AND `user_ip` = %s", $version->get_id(), DLM_Utils::get_visitor_ip() ) ) ) > 0 );
	}

}

