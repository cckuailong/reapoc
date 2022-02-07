<?php

// What is happening?
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// get option
$clean_up = absint( get_option( 'dlm_clean_on_uninstall', 0 ) );

// check if we need to clean up
if ( 1 === $clean_up ) {

	global $wpdb;

	/**
	 * Fetch all Download ID's
	 */
	$ids = get_posts(
		array(
			'post_type'      => 'dlm_download',
			'fields'         => 'ids',
			'post_status'    => 'any',
			'posts_per_page' => - 1
		)
	);

	/**
	 * Remove all download meta data
	 */
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE `post_id` IN (" . implode( ",", $ids ) . ");" );

	/**
	 * Remove all downloads
	 */
	$wpdb->query( "DELETE FROM $wpdb->posts WHERE `ID` IN (" . implode( ",", $ids ) . ");" );

	/**
	 * Remove all options
	 */
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE 'dlm_%';" );

	/**
	 * Remove all DLM transients
	 */
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_timeout_dlm_%';" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_dlm_%';" );

	/**
	 * Drop logs table
	 */
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}download_log ;" );

}