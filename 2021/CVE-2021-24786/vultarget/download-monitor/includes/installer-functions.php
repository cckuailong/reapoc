<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Plugin activation hook.
 * When site is multisite and plugin is network activated, installer will run for each blog
 *
 * @param bool $network_wide
 */
function _download_monitor_install( $network_wide = false ) {

	// DLM Installer
	$installer = new DLM_Installer();

	// check if
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	// check if it's multisite
	if ( is_multisite() && true == $network_wide ) {

		// get websites
		$sites = wp_get_sites();

		// loop
		if ( count( $sites ) > 0 ) {
			foreach ( $sites as $site ) {

				// switch to blog
				switch_to_blog( $site['blog_id'] );

				// run installer on blog
				$installer->install();

				// restore current blog
				restore_current_blog();
			}
		}

	} else {
		// no multisite so do normal install
		$installer->install();
	}

}


/**
 * Run installer for new blogs on multisite when plugin is network activated
 *
 * @param $blog_id
 * @param $user_id
 * @param $domain
 * @param $path
 * @param $site_id
 * @param $meta
 */
function _download_monitor_mu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	// check if plugin is network activated
	if ( is_plugin_active_for_network( 'download-monitor/download-monitor.php' ) ) {

		// DLM Installer
		$installer = new DLM_Installer();

		// switch to new blog
		switch_to_blog( $blog_id );

		// run installer on blog
		$installer->install();

		// restore current blog
		restore_current_blog();
	}
}
/**
 * Delete DLM log table on multisite when blog is deleted
 *
 * @param $tables
 *
 * @return array
 */
function _download_monitor_mu_delete_blog( $tables ) {
	global $wpdb;
	$tables[] = $wpdb->prefix . 'download_log';

	return $tables;
}