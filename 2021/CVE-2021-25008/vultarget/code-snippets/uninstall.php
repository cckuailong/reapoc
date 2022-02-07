<?php

/**
 * Cleans up data created by this plugin
 * @package Code_Snippets
 * @since 2.0
 */

/* Ensure this plugin is actually being uninstalled */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/* Fetch the Complete Uninstall option from the database settings */
$unified = false;
if ( is_multisite() ) {
	$menu_perms = get_site_option( 'menu_items', array() );
	$unified = empty( $menu_perms['snippets_settings'] );
}

$settings = $unified ? get_site_option( 'code_snippets_settings' ) : get_option( 'code_snippets_settings' );

/* Short circuit the uninstall cleanup process if the option is not enabled */
if ( ! isset( $settings['general']['complete_uninstall'] ) || ! $settings['general']['complete_uninstall'] ) {
	return;
}

/**
 * Clean up data created by this plugin for a single site
 * @since 2.0
 */
function code_snippets_uninstall_site() {
	global $wpdb;

	/* Remove snippets database table */
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}snippets" );

	/* Remove saved options */
	delete_option( 'code_snippets_version' );
	delete_option( 'recently_activated_snippets' );
	delete_option( 'code_snippets_settings' );
}


global $wpdb;

/* Multisite uninstall */

if ( is_multisite() ) {

	/* Loop through sites */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

	if ( $blog_ids ) {

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			code_snippets_uninstall_site();
		}

		restore_current_blog();
	}

	/* Remove multisite snippets database table */
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ms_snippets" );

	/* Remove saved options */
	delete_site_option( 'code_snippets_version' );
	delete_site_option( 'recently_activated_snippets' );
} else {
	code_snippets_uninstall_site();
}
