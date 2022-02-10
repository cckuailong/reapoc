<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Ajax callback to refresh the calendar
 *
 */
function wpbs_refresh_calendar() {

	if( empty( $_POST['action'] ) || $_POST['action'] != 'wpbs_refresh_calendar' ) {
		echo __( '', 'wp-booking-system' );
		wp_die();
	}

	if( empty( $_POST['id'] ) ) {
		wp_die();
	}

	$calendar_id   = absint( $_POST['id'] );
	$calendar      = wpbs_get_calendar( $calendar_id );
	$calendar_args = array();

	foreach( $_POST as $key => $val ) {
		if( in_array( $key, array_keys(wpbs_get_calendar_output_default_args()) ) ){
			$calendar_args[$key] = sanitize_text_field( $val );
		}
	}

	$calendar_outputter = new WPBS_Calendar_Outputter( $calendar, $calendar_args );
	
	echo $calendar_outputter->get_display();
	wp_die();

}
add_action( 'wp_ajax_nopriv_wpbs_refresh_calendar', 'wpbs_refresh_calendar' );
add_action( 'wp_ajax_wpbs_refresh_calendar', 'wpbs_refresh_calendar' );

