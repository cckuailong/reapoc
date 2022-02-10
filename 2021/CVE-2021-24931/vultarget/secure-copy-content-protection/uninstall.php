<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Secure_Copy_Content_Protection
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

if (get_option('sccp_upgrade_plugin') == 'false') {
	global $wpdb;

	$table = $wpdb->prefix . 'ays_sccp';
	$block_content = $wpdb->prefix . 'ays_sccp_block_content';
	$block_subscribe = $wpdb->prefix . 'ays_sccp_block_subscribe';
	$report_table = $wpdb->prefix . 'ays_sccp_reports';
	$settings_table = $wpdb->prefix . 'ays_sccp_settings';
	$wpdb->query("DROP TABLE IF EXISTS `{$table}`");
	$wpdb->query("DROP TABLE IF EXISTS `{$block_content}`");
	$wpdb->query("DROP TABLE IF EXISTS `{$block_subscribe}`");
	$wpdb->query("DROP TABLE IF EXISTS `{$report_table}`");
	$wpdb->query("DROP TABLE IF EXISTS `$settings_table`");
	delete_option("sccp_db_version");
	delete_option("sccp_upgrade_plugin");
}
