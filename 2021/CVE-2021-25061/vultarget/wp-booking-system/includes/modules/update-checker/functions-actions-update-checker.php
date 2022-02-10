<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the registering of the current website the plugin is running on
 *
 */
function wpbs_action_register_website() {

	// Verify for nonce
	if( empty( $_GET['wpbs_token'] ) || ! wp_verify_nonce( $_GET['wpbs_token'], 'wpbs_register_website' ) )
		return;

	if( empty( $_GET['serial_key'] ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_serial_key_missing' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	// Set serial key
	$serial_key = sanitize_text_field( $_GET['serial_key'] );

	// Make the call to the website
	$response = wp_remote_get( add_query_arg( array( 'request' => 'register_website', 'serial_key' => $serial_key, 'website_url' => site_url() ), 'https://www.wpbookingsystem.com/u/' ), array( 'timeout' => 30 ) );

	if( is_wp_error( $response ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_response_error' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	// Set the body of the response
	$website_id = ( ! empty( $response['body'] ) ? (int)$response['body'] : 0 );

	// Check for general errors
	if( $website_id == 0 ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_general_error' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	// Check if serial is expired
	if( $website_id == -1 ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_serial_expired' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	// If maximum websites has been reached
	if( $website_id == -3 ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_maximum_websites' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	if( is_int( $website_id ) ) {

		delete_option( 'wpbs_serial_key' );
		delete_option( 'wpbs_registered_website_id' );

		update_option( 'wpbs_serial_key', $serial_key );
		update_option( 'wpbs_registered_website_id', $website_id );


		// Redirect to the settings page with a success message
		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'register_website_success' ), admin_url( 'admin.php' ) ) );
		exit;

	}

}
add_action( 'wpbs_action_register_website', 'wpbs_action_register_website' );


/**
 * Handles the deregistering of the current website the plugin is running on
 *
 */
function wpbs_action_deregister_website() {

	// Verify for nonce
	if( empty( $_GET['wpbs_token'] ) || ! wp_verify_nonce( $_GET['wpbs_token'], 'wpbs_deregister_website' ) )
		return;

	if( empty( $_GET['serial_key'] ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'deregister_website', 'wpbs_message' => 'deregister_website_serial_key_missing' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	// Set serial key
	$serial_key = sanitize_text_field( $_GET['serial_key'] );

	// Make the call to the website
	$response = wp_remote_get( add_query_arg( array( 'request' => 'deregister_website', 'serial_key' => $serial_key, 'website_url' => site_url() ), 'https://www.wpbookingsystem.com/u/' ), array( 'timeout' => 30 ) );

	if( is_wp_error( $response ) ) {

		wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'deregister_website_response_error' ), admin_url( 'admin.php' ) ) );
		exit;

	}

	delete_option( 'wpbs_serial_key' );
	delete_option( 'wpbs_registered_website_id' );

	// Redirect to the settings page with a success message
	wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'deregister_website_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'wpbs_action_deregister_website', 'wpbs_action_deregister_website' );


/**
 * Handles the "check for updates" action, where the updates checker cron is executed
 *
 */
function wpbs_action_check_for_updates() {

	// Verify for nonce
	if( empty( $_GET['wpbs_token'] ) || ! wp_verify_nonce( $_GET['wpbs_token'], 'wpbs_check_for_updates' ) )
		return;

	do_action_ref_array( 'check_plugin_updates-wp-booking-system-premium', array() );

	// Check for addon updates.
	do_action('wpbs_check_addon_updates');

	delete_transient( 'wpbs_subscription_type' );

	// Redirect to the settings page with a success message
	wp_redirect( add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => 'register_website', 'wpbs_message' => 'check_for_updates_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'wpbs_action_check_for_updates', 'wpbs_action_check_for_updates' );


/**
 * Makes a call to the website to check the serial key status and saves it into a transient
 *
 */
function wpbs_action_get_serial_status() {

	if( ! current_user_can( 'manage_options' ) )
		return;

	$serial_key = get_option( 'wpbs_serial_key', '' );

	if( empty( $serial_key ) )
		return;

	// Check to see if the notice should be shown
	$transient = get_transient( 'wpbs_serial_status' );

	if( false !== $transient )
		return;

	// Make the call to the website for the serial key status
	$response = wp_remote_get( add_query_arg( array( 'request' => 'get_serial_status', 'serial_key' => $serial_key ), 'https://www.wpbookingsystem.com/u/' ), array( 'timeout' => 30 ) );

	if( is_wp_error( $response ) )
		return;

	// Set transient to make sure not to do checks next time
	set_transient( 'wpbs_serial_status', (int)$response['body'], 36 * HOUR_IN_SECONDS );

}
add_action( 'admin_init', 'wpbs_action_get_serial_status' );

/**
 * Makes a call to the website to check the serial key status and saves it into a transient
 *
 */
function wpbs_action_get_subscription_type() {

	if( ! current_user_can( 'manage_options' ) )
		return;

	$serial_key = get_option( 'wpbs_serial_key', '' );

	if( empty( $serial_key ) )
		return;

	// Check to see if the notice should be shown
	$transient = get_transient( 'wpbs_subscription_type' );

	if( false !== $transient )
		return;

	// Save Subscription name
	$response = wp_remote_get( add_query_arg( array( 'request' => 'get_subscription_type', 'serial_key' => $serial_key ), 'https://www.wpbookingsystem.com/u/' ), array( 'timeout' => 30 ) );

	if( is_wp_error( $response ) )
		return;
	
	// Set transient to make sure not to do checks next time
	set_transient( 'wpbs_subscription_type', $response['body'], 36 * HOUR_IN_SECONDS );

}
add_action( 'admin_init', 'wpbs_action_get_subscription_type' );