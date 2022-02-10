<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Adds a new tab to the Settings page of the plugin
 *
 * @param array $tabs
 *
 * @return $tabs
 *
 */
function wpbs_submenu_page_settings_tabs_uninstaller( $tabs ) {

	$tabs['uninstaller'] = __( 'Uninstaller', 'wp-booking-system' );

	return $tabs;

}
add_filter( 'wpbs_submenu_page_settings_tabs', 'wpbs_submenu_page_settings_tabs_uninstaller', 100 );


/**
 * Adds the HTML for the Uninstaller tab
 *
 */
function wpbs_submenu_page_settings_tab_uninstaller() {

	include 'views/view-uninstaller.php';

}
add_action( 'wpbs_submenu_page_settings_tab_uninstaller', 'wpbs_submenu_page_settings_tab_uninstaller' );


/**
 * Action that uninstalls the plugin
 *
 */
function wpbs_action_uninstall_plugin() {

	// Verify for nonce
	if( empty( $_GET['wpbs_token'] ) || ! wp_verify_nonce( $_GET['wpbs_token'], 'wpbs_uninstall_plugin' ) )
		return;

	/**
	 * Drop db tables
	 *
	 */
	global $wpdb;

	$registered_tables = wp_booking_system()->db;

	foreach( $registered_tables as $table )
		$wpdb->query( "DROP TABLE IF EXISTS {$table->table_name}" );

	/**
	 * Remove options
	 *
	 */
	delete_option( 'wpbs_version' );
	delete_option( 'wpbs_first_activation' );
	delete_option( 'wpbs_upgrade_5_0_0' );
	delete_option( 'wpbs_upgrade_5_0_0_skipped' );
	delete_option( 'wpbs_serial_key' );
	delete_option( 'wpbs_registered_website_id' );


	/**
	 * Deactivate the plugin and redirect to Plugins
	 *
	 */
    deactivate_plugins( WPBS_BASENAME );
    
    wp_redirect( admin_url( 'plugins.php' ) );
    exit;

}
add_action( 'wpbs_action_uninstall_plugin', 'wpbs_action_uninstall_plugin' );