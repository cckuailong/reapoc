<?php
/**
 * Functionality to remove Wordpress Time Capsule from your WordPress installation
 *
 * @copyright Copyright (C) 2011-2014 Awesoft Pty. Ltd. All rights reserved.
 * @author Michael De Wildt (http://www.mikeyd.com.au/)
 * @license This program is free software; you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation; either version 2 of the License, or
 *          (at your option) any later version.
 *
 *          This program is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *
 *          You should have received a copy of the GNU General Public License
 *          along with this program; if not, write to the Free Software
 *          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
 */
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

drop_all_wptc_triggers();
wptc_mu_uninstall();

function drop_all_wptc_triggers() {
	global $wpdb;

	$triggers_meta = $wpdb->get_results("SHOW TRIGGERS WHERE `Trigger` LIKE '%_wptc%'");

	if (empty($triggers_meta)) {
		return ;
	}

	foreach ($triggers_meta as $trigger_meta) {

		if (empty($trigger_meta->Trigger)) {
			continue;
		}

		$wpdb->query('DROP TRIGGER IF EXISTS ' . $trigger_meta->Trigger);
	}
}

function wptc_mu_uninstall(){
	$loaderName = '0-mu-wp-time-capsule.php';
	try {
		$mustUsePluginDir = rtrim(WPMU_PLUGIN_DIR, '/');
		$loaderPath       = $mustUsePluginDir.'/'.$loaderName;

		if (!file_exists($loaderPath)) {
			return;
		}

		$removed = @unlink($loaderPath);

		if (!$removed) {
			$error = error_get_last();
			throw new Exception(sprintf('Unable to remove loader: %s', $error['message']));
		}
	} catch (Exception $e) {
		//unable to remove
	}
}
delete_option('wptc-init-errors');
delete_option('wptc-premium-extensions');
delete_option('wptc_disable_sentry_lib');

remove_action('admin_menu', 'wordpress_time_capsule_admin_menu');
remove_action('wp_ajax_progress_wptc', 'tc_backup_progress_wptc');
remove_action('wp_ajax_get_this_day_backups_wptc', 'get_this_day_backups_callback_wptc');
remove_action('wp_ajax_get_in_progress_backup_wptc', 'get_in_progress_tcbackup_callback_wptc');
remove_action('wp_ajax_start_backup_tc_wptc', 'start_backup_tc_callback_wptc');
remove_action('wp_ajax_store_name_for_this_backup_wptc', 'store_name_for_this_backup_callback_wptc');
remove_action('wp_ajax_start_fresh_backup_tc_wptc', 'start_fresh_backup_tc_callback_wptc');
remove_action('wp_ajax_stop_fresh_backup_tc_wptc', 'stop_fresh_backup_tc_callback_wptc');
remove_action('wp_ajax_get_check_to_show_dialog_wptc', 'get_check_to_show_dialog_wptc');
remove_action('wp_ajax_start_restore_tc_wptc', 'start_restore_tc_callback_wptc');
remove_action('wp_ajax_get_and_store_before_backup', 'get_and_store_before_backup_callback');
remove_action('wptc_sub_cycle_event', 'sub_cycle_event_func_wptc');
remove_action('wptc_schedule_cycle_event', 'sub_cycle_event_func_wptc');

global $wpdb;

$table_name = $wpdb->base_prefix . 'wptc_options';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_processed_files';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_premium_extensions';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_processed_iterator';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_processed_restored_files';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_current_process';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_activity_log';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_backups';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_auto_backup_record';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_inc_exc_contents';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

$table_name = $wpdb->base_prefix . 'wptc_query_recorder';
$wpdb->query("DROP TABLE IF EXISTS $table_name");
