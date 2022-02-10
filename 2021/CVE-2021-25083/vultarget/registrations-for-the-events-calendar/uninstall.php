<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = get_option( 'rtec_options', array() );
global $wpdb;

if ( ! class_exists('Registrations_For_The_Events_Calendar_Pro' ) ) {
	//If the user is preserving the settings then don't delete them
	if ( !( isset( $options['preserve_db'] ) && $options['preserve_db'] == true ) && !( isset( $options['preserve_settings'] ) && $options['preserve_settings'] == true ) ) {
		// clean up options from the database
		delete_option( 'rtec_options' );
		delete_option( 'rtec_db_version' );
		delete_transient( 'rtec_new_registrations' );

		$table_name = esc_sql( $wpdb->prefix . "postmeta" );
		$result = $wpdb->query("
	    DELETE
	    FROM $table_name
	    WHERE `meta_key` LIKE ('%_RTEC%')
	    ");
	}

	if ( !( isset( $options['preserve_db'] ) && $options['preserve_db'] == true ) && !( isset( $options['preserve_registrations'] ) && $options['preserve_registrations'] == true ) ) {
		// delete the registrations table
		$wpdb->query( "DROP TABLE IF EXISTS " . esc_sql( $wpdb->prefix ) . "rtec_registrations" );

		delete_transient( 'rtec_new_registrations' );
	}

// reset WP_Query
	wp_reset_postdata();
}




