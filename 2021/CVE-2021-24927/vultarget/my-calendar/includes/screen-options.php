<?php
/**
 * Implement screen options on pages where needed.
 *
 * @category Utilities
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implement show on page selection fields
 */
function mc_event_editing() {
	$option = 'mc_show_on_page';
	$args   = array(
		'label'   => 'Show these fields',
		'default' => get_option( 'mc_input_options' ),
		'option'  => 'mc_show_on_page',
	);
	add_screen_option( $option, $args );
}

add_filter( 'screen_settings', 'mc_show_event_editing', 10, 2 );
/**
 * Show event editing options for user
 *
 * @param string $status string.
 * @param array  $args array Arguments.
 *
 * @return string
 */
function mc_show_event_editing( $status, $args ) {
	$return = $status;
	if ( 'toplevel_page_my-calendar' === $args->base ) {
		$input_options    = get_user_meta( get_current_user_id(), 'mc_show_on_page', true );
		$settings_options = get_option( 'mc_input_options' );
		if ( ! is_array( $input_options ) ) {
			$input_options = $settings_options;
		}
		$defaults = array(
			'event_location_dropdown' => 'on',
			'event_short'             => 'on',
			'event_desc'              => 'on',
			'event_category'          => 'on',
			'event_image'             => 'on',
			'event_link'              => 'on',
			'event_recurs'            => 'on',
			'event_open'              => 'on',
			'event_location'          => 'off',
			'event_specials'          => 'on',
			'event_access'            => 'on',
			'event_host'              => 'on',
		);

		$input_options = array_merge( $defaults, $input_options );
		// cannot change these keys.
		$input_labels = array(
			'event_location_dropdown' => __( 'Event Location Dropdown Menu', 'my-calendar' ),
			'event_short'             => __( 'Event Short Description', 'my-calendar' ),
			'event_desc'              => __( 'Event Description', 'my-calendar' ),
			'event_category'          => __( 'Event Category', 'my-calendar' ),
			'event_image'             => __( 'Event Image', 'my-calendar' ),
			'event_link'              => __( 'Event Link', 'my-calendar' ),
			'event_recurs'            => __( 'Event Recurrence Options', 'my-calendar' ),
			'event_open'              => __( 'Event Registration options', 'my-calendar' ),
			'event_location'          => __( 'Event Location fields', 'my-calendar' ),
			'event_specials'          => __( 'Set Special Scheduling options', 'my-calendar' ),
			'event_access'            => __( 'Event Accessibility', 'my-calendar' ),
			'event_host'              => __( 'Event Host', 'my-calendar' ),
		);

		$output = '';
		foreach ( $input_options as $key => $value ) {
			$checked = ( 'on' === $value ) ? "checked='checked'" : '';
			$allowed = ( isset( $settings_options[ $key ] ) && 'on' === $settings_options[ $key ] ) ? true : false;
			if ( ! ( current_user_can( 'manage_options' ) && 'true' === get_option( 'mc_input_options_administrators' ) ) && ! $allowed ) {
				// don't display options if this user can't use them.
				$output .= "<input type='hidden' name='mc_show_on_page[$key]' value='off' />";
			} else {
				if ( isset( $input_labels[ $key ] ) ) {
					// don't show if label doesn't exist. That means I removed the option.
					$output .= "<label for='mci_$key'><input type='checkbox' id='mci_$key' name='mc_show_on_page[$key]' value='on' $checked /> $input_labels[$key]</label>";
				}
			}
		}
		$button  = get_submit_button( __( 'Apply' ), 'button', 'screen-options-apply', false );
		$return .= '
	<fieldset>
	<legend>' . __( 'Event editing fields to show', 'my-calendar' ) . "</legend>
	<div class='metabox-prefs'>
		<div><input type='hidden' name='wp_screen_options[option]' value='mc_show_on_page' /></div>
		<div><input type='hidden' name='wp_screen_options[value]' value='yes' /></div>
		$output
	</div>
	</fieldset>
	<br class='clear'>
	$button";
	}

	return $return;
}

add_filter( 'set-screen-option', 'mc_set_event_editing', 11, 3 );
/**
 * Save settings for screen options
 *
 * @param string $status string.
 * @param string $option option name.
 * @param string $value rows to use.
 *
 * @return value
 */
function mc_set_event_editing( $status, $option, $value ) {
	if ( 'mc_show_on_page' === $option ) {
		$defaults = array(
			'event_location_dropdown' => 'on',
			'event_short'             => 'on',
			'event_desc'              => 'on',
			'event_category'          => 'on',
			'event_image'             => 'on',
			'event_link'              => 'on',
			'event_recurs'            => 'on',
			'event_open'              => 'on',
			'event_location'          => 'off',
			'event_specials'          => 'on',
			'event_access'            => 'on',
			'event_host'              => 'on',
		);
		$value    = array();
		foreach ( $defaults as $k => $v ) {
			if ( isset( $_POST['mc_show_on_page'][ $k ] ) ) {
				$value[ $k ] = 'on';
			} else {
				$value[ $k ] = 'off';
			}
		}
		update_user_meta( get_current_user_ID(), 'mc_show_on_page', $value );
	}

	return $value;
}

/**
 * Add the screen option for num per page
 */
function mc_add_screen_option() {
	$items_per_page = ( get_option( 'mc_num_per_page' ) ) ? get_option( 'mc_num_per_page' ) : 50;
	$option         = 'per_page';
	$args           = array(
		'label'   => 'Events',
		'default' => $items_per_page,
		'option'  => 'mc_num_per_page',
	);
	add_screen_option( $option, $args );
}

add_filter( 'set-screen-option', 'mc_set_screen_option', 10, 3 );
/**
 * Set the num per page value
 *
 * @param string $status Status.
 * @param string $option Option name.
 * @param string $value New value.
 *
 * @return string $value
 */
function mc_set_screen_option( $status, $option, $value ) {
	if ( 'mc_num_per_page' === $option ) {
		update_user_meta( get_current_user_ID(), 'mc_num_per_page', $value );
	}

	return $value;
}
