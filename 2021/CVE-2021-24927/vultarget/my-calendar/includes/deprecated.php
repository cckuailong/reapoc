<?php
/**
 * This file holds functions that have been removed or deprecated,
 * but are kept in case 3rd party code is using the function independently.
 *
 * @category Utilities
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

// Define the table constants used in My Calendar in case anybody is still using them.
// These were eliminated some time ago.
if ( is_multisite() && get_site_option( 'mc_multisite_show' ) === '1' ) {
	define( 'MY_CALENDAR_TABLE', $wpdb->base_prefix . 'my_calendar' );
	define( 'MY_CALENDAR_EVENTS_TABLE', $wpdb->base_prefix . 'my_calendar_events' );
	define( 'MY_CALENDAR_CATEGORIES_TABLE', $wpdb->base_prefix . 'my_calendar_categories' );
	define( 'MY_CALENDAR_LOCATIONS_TABLE', $wpdb->base_prefix . 'my_calendar_locations' );
} else {
	define( 'MY_CALENDAR_TABLE', $wpdb->prefix . 'my_calendar' );
	define( 'MY_CALENDAR_EVENTS_TABLE', $wpdb->prefix . 'my_calendar_events' );
	define( 'MY_CALENDAR_CATEGORIES_TABLE', $wpdb->prefix . 'my_calendar_categories' );
	define( 'MY_CALENDAR_LOCATIONS_TABLE', $wpdb->prefix . 'my_calendar_locations' );
}

if ( is_multisite() ) {
	// Define the tables used in My Calendar.
	define( 'MY_CALENDAR_GLOBAL_TABLE', $wpdb->base_prefix . 'my_calendar' );
	define( 'MY_CALENDAR_GLOBAL_EVENT_TABLE', $wpdb->base_prefix . 'my_calendar_events' );
	define( 'MY_CALENDAR_GLOBAL_CATEGORIES_TABLE', $wpdb->base_prefix . 'my_calendar_categories' );
	define( 'MY_CALENDAR_GLOBAL_LOCATIONS_TABLE', $wpdb->base_prefix . 'my_calendar_locations' );
}

/**
 * Old support box function
 *
 * @see mc_show_sidebar()
 * @deprecated
 */
function jd_show_support_box() {
	$purchase_url = 'https://www.joedolson.com/awesome/my-calendar-pro/';
	$check_url    = 'https://www.joedolson.com/login/';
	$add          = array(
		// Translators: Purchase URL, account URL.
		'My Calendar Pro out of date!' => '<p>' . __( 'The version of My Calendar Pro (or My Calendar Submissions) you have installed is very out of date!', 'my-calendar' ) . '</p><p>' . __( 'The latest version of My Calendar Pro is the only version recommended for compatibility with My Calendar. Please <a href="%1$s">purchase an upgrade</a> or <a href="%2$s">login to check your license status</a>!', 'my-calendar' ) . '</p>',
	);
	mc_show_sidebar( '', $add, true );
}

/**
 * Get label for "forever" events (no longer exist.)
 *
 * @param string $recur Recurrence string (single character).
 * @param int    $repeats Number of occurrences to repeat.
 *
 * @deprecated 2.5.16. Last used 2.4.21.
 *
 * @return string label
 */
function mc_event_repeats_forever( $recur, $repeats ) {
	$repeats = absint( $repeats );
	if ( 'S' !== $recur && 0 === $repeats ) {
		return true;
	}
	switch ( $recur ) {
		case 'S': // single.
			return false;
			break;
		case 'D': // daily.
			return ( 500 === $repeats ) ? true : false;
			break;
		case 'W': // weekly.
			return ( 240 === $repeats ) ? true : false;
			break;
		case 'B': // biweekly.
			return ( 120 === $repeats ) ? true : false;
			break;
		case 'M': // monthly.
		case 'U':
			return ( 60 === $repeats ) ? true : false;
			break;
		case 'Y':
			return ( 5 === $repeats ) ? true : false;
			break;
		default:
			return false;
	}
}

/**
 * Old name of template drawing function. Deprecated 6/14/2018. Removed in Pro 3/31/2019.
 *
 * @see mc_draw_template()
 *
 * @param array  $array Associative Array of information.
 * @param string $template String containing tags.
 * @param string $type Type of display.
 *
 * @return string
 */
function jd_draw_template( $array, $template, $type = 'list' ) {

	return mc_draw_template( $array, $template, $type );
}
