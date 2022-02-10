<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the export of the backup file
 *
 */
function wpbs_action_backup_export() {

	// Let only admins do this procedure
	if( ! current_user_can( 'manage_options' ) )
		return;

	// Verify for nonce
	if( empty( $_POST['wpbs_token'] ) || ! wp_verify_nonce( $_POST['wpbs_token'], 'wpbs_backup_export' ) )
		return;

	// Set export data variable
	$export_data = array();

	// Get wpdb global
	global $wpdb;

	/**
	 * Go through each custom db table and save the values
	 *
	 */
	foreach( wp_booking_system()->db as $table_slug => $db_object ) {

		$results = $wpdb->get_results( "SELECT * FROM {$db_object->table_name}" );

		$export_data[$table_slug] = $results;

	}

	/**
	 * General settings and plugin version number
	 *
	 */
	$export_data['settings'] 	   = get_option( 'wpbs_settings', array() );
	$export_data['plugin_version'] = WPBS_VERSION;

	header( 'Content-disposition: attachment; filename=wpbs-export-' . current_time('Y') . '-' . current_time('m') . '-' . current_time('d') . '-' . current_time('H') . '-' . current_time('i') . '-' . current_time('s') . '.wpbs' );
	header( 'Content-type: application/json' );

	echo( json_encode( $export_data ) );
	exit;

}
add_action( 'wpbs_action_backup_export', 'wpbs_action_backup_export' );


/**
 * Handles the export of the backup file
 *
 */
function wpbs_action_backup_import() {

	// Let only admins do this procedure
	if( ! current_user_can( 'manage_options' ) )
		return;

	// Verify for nonce
	if( empty( $_POST['wpbs_token'] ) || ! wp_verify_nonce( $_POST['wpbs_token'], 'wpbs_backup_import' ) )
		return;

	// Check if file has been selected
	if( empty( $_FILES['import_file']['tmp_name'] ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_missing', '<p>' . __( 'Please select a file.', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_missing' );

		return;

	}

	// Check for proper extension
	if( false === strpos( $_FILES['import_file']['name'], '.wpbs' ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_extension_fail', '<p>' . __( 'The file you are trying to import does not have a valid extension. The extension of the file should be ".wpbs".', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_extension_fail' );

		return;

	}
		
	// Decode the file
	$file_contents = json_decode( file_get_contents( $_FILES['import_file']['tmp_name'] ), true );

	// Check for proper plugin version
	if( empty( $file_contents['plugin_version'] ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_version_fail', '<p>' . __( 'The file you are trying to import is not compatible with this version of the plugin.', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_version_fail' );

		return;

	}


	// Get wpdb global
	global $wpdb;


	/**
	 * Empty all registered tables
	 *
	 */
	foreach( wp_booking_system()->db as $table_slug => $db_object ) {

		$wpdb->query( "TRUNCATE {$db_object->table_name}" );

	}


	/**
	 * Go through each custom db table and insert the values from the file
	 *
	 */
	foreach( wp_booking_system()->db as $table_slug => $db_object ) {

		if( empty( $file_contents[$table_slug] ) || ! is_array( $file_contents[$table_slug] ) )
			continue;

		foreach( $file_contents[$table_slug] as $key => $values ) {

			$wpdb->insert( $db_object->table_name, $values );

		}

	}

	/**
	 * Add general settings
	 *
	 */
	if( ! empty( $file_contents['settings'] ) )
		update_option( 'wpbs_settings', $file_contents['settings'] );

	// Redirect with a success message
	wp_redirect( add_query_arg( array( 'page' => 'wpbs-backup', 'wpbs_message' => 'import_file_success' ), admin_url( 'admin.php' ) ) );
	exit;

}
add_action( 'wpbs_action_backup_import', 'wpbs_action_backup_import' );