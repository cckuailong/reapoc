<?php

/**
 *
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 */

// If plugin is not being uninstalled, exit (do nothing)
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Do something here if plugin is being uninstalled.

/*
Need to remove the options from the database;
*/

delete_option('fbc_flight_domain');
delete_option('fbc_app_id');
delete_option('fbc_app_secret');
delete_option('fbc_app_token');
delete_option('fbc_app_refresh_token');
delete_option('fbc_token_expire');
delete_option('fbc_flight_username');
delete_option('fbc_flight_password');
delete_option('fbc_refresh_token_expire');
delete_option('fbc_duplicates');
delete_option('fbc_cron');
delete_option('fbc_schedule');
delete_option('fbc_cron_start');
delete_option('fbc_cron_time_day');
delete_option('fbc_cron_time_hour');
wp_clear_scheduled_hook('fbc_scheduled_update');
