<?php
use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;

defined( 'ABSPATH' ) or exit;

// load date/time functions for older WC versions
if ( WCX::is_wc_version_lt_3_0() ) {
	if ( !function_exists( 'wc_timezone_offset' ) ) {
		/**
		 * Get timezone offset in seconds.
		 *
		 * @since  3.0.0
		 * @return float
		 */
		function wc_timezone_offset() {
			if ( $timezone = get_option( 'timezone_string' ) ) {
				$timezone_object = new DateTimeZone( $timezone );
				return $timezone_object->getOffset( new DateTime( 'now' ) );
			} else {
				return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
			}
		}
	}
	
	if ( !function_exists( 'wc_string_to_timestamp' ) ) {
		/**
		 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
		 *
		 * Based on wcs_strtotime_dark_knight() from WC Subscriptions by Prospress.
		 *
		 * @since  3.0.0
		 * @return int
		 */
		function wc_string_to_timestamp( $time_string, $from_timestamp = null ) {
			$original_timezone = date_default_timezone_get();
			// @codingStandardsIgnoreStart
			date_default_timezone_set( 'UTC' );
			if ( null === $from_timestamp ) {
				$next_timestamp = strtotime( $time_string );
			} else {
				$next_timestamp = strtotime( $time_string, $from_timestamp );
			}
			date_default_timezone_set( $original_timezone );
			// @codingStandardsIgnoreEnd
			return $next_timestamp;
		}
	}
}
