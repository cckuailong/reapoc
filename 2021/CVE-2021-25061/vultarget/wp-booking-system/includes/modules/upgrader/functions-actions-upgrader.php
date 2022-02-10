<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the skipping of the upgrade process
 *
 */
function wpbs_action_skip_upgrade_process() {

	// Verify for nonce
	if( empty( $_GET['wpbs_token'] ) || ! wp_verify_nonce( $_GET['wpbs_token'], 'wpbs_skip_upgrade_process' ) )
		return;

	// Add the option that the upgrader has been skipped
	update_option( 'wpbs_upgrade_5_0_0_skipped', 1 );

	// Redirect to the edit page of the calendar with a success message
	wp_redirect( add_query_arg( array( 'page' => 'wpbs-calendars' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'wpbs_action_skip_upgrade_process', 'wpbs_action_skip_upgrade_process', 50 );