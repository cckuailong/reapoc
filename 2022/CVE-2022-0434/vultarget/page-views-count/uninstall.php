<?php
/**
 * Page Views Count Uninstall
 *
 * Uninstalling deletes options, tables, and pages.
 *
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

$plugin_key = 'a3_page_view_count';

// Delete Google Font
delete_option( $plugin_key . '_google_api_key' . '_enable' );
delete_transient( $plugin_key . '_google_api_key' . '_status' );
delete_option( $plugin_key . '_google_font_list' );

if ( get_option( $plugin_key . '_clean_on_deletion' ) == 1 ) {
	delete_option( $plugin_key . '_google_api_key' );
	delete_option( $plugin_key . '_toggle_box_open' );
	delete_option( $plugin_key . '-custom-boxes' );

	delete_metadata( 'user', 0,  $plugin_key . '-' . 'plugin_framework_global_box' . '-' . 'opened', '', true );

	delete_option('pvc_settings');
	delete_option('a3_pvc_version');
	delete_option('a3rev_pvc_plugin');
	delete_option('a3rev_auth_pvc');
	delete_option($plugin_key . '_clean_on_deletion');

	global $wpdb;
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'pvc_total');
	$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'pvc_daily');

	$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_a3_pvc_activated' " );
}