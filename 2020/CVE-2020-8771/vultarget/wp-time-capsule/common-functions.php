<?php

function is_php_version_compatible_for_s3_wptc() {
	if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
		return true;
	}
	return false;
}

function is_php_version_compatible_for_g_drive_wptc() {
	if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
		return true;
	}
	return false;
}

function is_php_version_compatible_for_dropbox_wptc() {
	if (version_compare(PHP_VERSION, '5.3.1') >= 0) {
		return true;
	}
	return false;
}

function reset_restore_related_settings_wptc($dont_delete_logs = false) {
	wptc_log(array(), "--------flashing restore flags--------");
	$config = WPTC_Factory::get('config');

	//resetting restore config-options
	$config->set_option('restore_action_id', false);
	$config->set_option('in_progress_restore', false);
	$config->set_option('is_running_restore', false);
	$config->set_option('cur_res_b_id', false);
	$config->set_option('is_bridge_process', false);
	$config->set_option('current_bridge_file_name', false);
	$config->set_option('is_bridge_restore', false);
	$config->set_option('last_backup_request', false);

	$config->set_option('got_files_list_for_restore_to_point', false);
	$config->set_option('live_files_to_restore_table', false);
	$config->set_option('recorded_files_to_restore_table', false);
	$config->set_option('is_deleted_all_future_files', false);
	$config->set_option('selected_files_temp_restore', false);
	$config->set_option('selected_backup_type_restore', false);
	$config->set_option('got_selected_files_to_restore', false);
	$config->set_option('not_safe_for_write_files', false);
	$config->set_option('recorded_this_selected_folder_restore', false);
	$config->set_option('download_failed_file_counter', false);
	$config->set_option('get_recorded_files_to_restore_table', false);

	$config->set_option('check_is_safe_for_write_restore', 1);

	$config->set_option('garbage_deleted', 0);
	$config->set_option('restore_db_index', 0);
	$config->set_option('restore_current_action', false);

	$config->set_option('sql_gz_uncompression', false);

	$config->set_option('copy_files_wp_content', false);
	$config->set_option('copy_files_abspath', false);
	$config->set_option('restore_copied_files_count', false);
	$config->set_option('restore_state_files_count', false);
	$config->set_option('restore_downloaded_files_count', false);
	$config->set_option('delete_future_files_offset', false);
	$config->set_option('is_realtime_partial_query_restore', false);
	$config->set_option('restore_partial_db_process', false);
	$config->set_option('is_latest_restore_point', false);
	$config->set_option('is_latest_restore_point_query_executed', false);
	$config->set_option('latest_restore_point_query_offset', false);
	$config->set_option('added_state_file_to_restore', false);
	$config->set_option('state_file_downloaded_in_restore', false);
	$config->set_option('deleted_state_files_from_download_list', false);
	$config->set_option('delete_state_files_from_download_list_offset', false);
	$config->set_option('refresh_triggers_on_restore', false);
	$config->set_option('tried_to_create_triggers_after_restore', false);
	$config->set_option('restore_database_decrypted', false);
	$config->set_option('fresh_wptc_plugin_downloaded', false);
	$config->set_option('migration_replaced_links', false);
	$config->set_option('migration_url', false);

	$config->set_option('restore_is_multisite', false);
	$config->set_option('restore_multisite_upload_dir', false);
	$config->set_option('restore_multisite_base_prefix', false);
	$config->set_option('restore_multisite_current_prefix', false);

	if ($dont_delete_logs === false) {
		$config->set_option('is_restore_failed_queries_file_created', false);
		$config->set_option('restore_failed_queries_file_path', false);
		$config->set_option('is_restore_failed_downloads_file_created', false);
		$config->set_option('restore_failed_downloads_file_path', false);
		$restore_logs_path = wptc_get_tmp_dir() . '/wptc_restore_logs/';
		$restore_logs_path = $config->wp_filesystem_safe_abspath_replace($restore_logs_path);
		$config->delete_files_of_this_folder($restore_logs_path, array());
	}

	global $wpdb;
	$wpdb->query("TRUNCATE TABLE `" . $wpdb->base_prefix . "wptc_current_process`");
	$wpdb->query("TRUNCATE TABLE `" . $wpdb->base_prefix . "wptc_processed_iterator`");
}

register_shutdown_function('wptc_fatal_error_hadler');
function wptc_fatal_error_hadler($return = null) {

	//reference http://php.net/manual/en/errorfunc.constants.php
	$log_error_types = array(
		1 => 'PHP Fatal error',
		2 => 'PHP Warning',
		4 => 'PHP Parse',
		8 => 'PHP Notice error',
		16 => 'PHP Core error',
		32 => 'PHP Core Warning',
		64 => 'PHP Core compile error',
		128 => 'PHP Core compile error',
		256 => 'PHP User error',
		512 => 'PHP User warning',
		1024 => 'PHP User notice',
		2048 => 'PHP Strict',
		4096 => 'PHP Recoverable error',
		8192 => 'PHP Deprecated error',
		16384 => 'PHP User deprecated',
		32767 => 'PHP All',
	);

	$last_error = error_get_last();

	if (empty($last_error) && empty($return)) {
		return ;
	}

	if ($return) {
		$config = WPTC_Factory::get('config');
		$recent_error = $config->get_option('plugin_recent_error');
		if (empty($recent_error)) {
			$recent_error = "Something went wrong ";
		}
		return $recent_error. ". \n Please contact us help@wptimecapsule.com";
	}

	if (WPTC_ENV === 'local') {
		if (strstr($last_error['file'], 'wp-time-capsule') === false ) {
			return ;
		}
	}

	if (strpos($last_error['message'], 'use the CURLFile class') !== false || strpos($last_error['message'], 'Automatically populating') !== false) {
		return ;
	}

	if (strpos($last_error['file'], 'iwp-client') !== false || !defined('WPTC_DEBUG') || !WPTC_DEBUG) {
		return ;
	}

	file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-logs.txt', $log_error_types[$last_error['type']] . ": " . $last_error['message'] . " in " . $last_error['file'] . " on " . " line " . $last_error['line'] . "\n", FILE_APPEND);

	if (strpos($last_error['file'], 'wp-time-capsule') === false && strpos($last_error['file'], 'wp-tcapsule-bridge') === false) {
		return ;
	}

	$config = WPTC_Factory::get('config');
	$error = $log_error_types[$last_error['type']] . ": " . $last_error['message'] . " in " . $last_error['file'] . " on " . " line " . $last_error['line'];
	$config->set_option('plugin_recent_error', $error);
}

function wptc_log_server_request($value, $type, $url = null) {
	if (!defined('WPTC_DEBUG') || !WPTC_DEBUG) {
		return ;
	}

	$usr_time = time();

	if (function_exists('user_formatted_time_wptc')) {
		$usr_time = user_formatted_time_wptc(time());
	}

	try {
		@file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-server-request-logs.txt', "\n -----$type-------$usr_time-------$url------- " . var_export($value, true) . "\n", FILE_APPEND);
	} catch (Exception $e) {
		@file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-server-request-logs.txt', "\n -----$type-------$usr_time------$url-------- " . var_export(serialize($value), true) . "\n", FILE_APPEND);
	}

}

function get_backtrace_string_wptc($limit = 7) {

	if (!WPTC_DEBUG) {
		return ;
	}

	$bactrace_arr = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
	$backtrace_str = '';

	if (!is_array($bactrace_arr)) {
		return false;
	}

	foreach ($bactrace_arr as $k => $v) {
		if ($k == 0) {
			continue;
		}

		$line = empty($v['line']) ? 0 : $v['line'];
		$backtrace_str .= '<-' . $v['function'] . '(line ' . $line . ')';
	}

	return $backtrace_str;
}

function store_bridge_compatibile_values_wptc() {
	$config = WPTC_Factory::get('config');

	$config->set_option('site_url_wptc', get_home_url());
	$config->set_option('child_site_specific_admin_url', admin_url());

	if (is_multisite()) {
		$config->set_option('network_admin_url', network_admin_url());
	} else {
		$config->set_option('network_admin_url', admin_url());
	}
}

function is_wptc_timeout_cut($start_time = false, $reduce_sec = 0) {
	if ($start_time === false) {
		global $wptc_ajax_start_time;
		$start_time = $wptc_ajax_start_time;
	}
	$time_diff = time() - $start_time;
	if (!defined('WPTC_TIMEOUT')) {
		define('WPTC_TIMEOUT', 21);
	}
	$max_execution_time = WPTC_TIMEOUT - $reduce_sec;
	if ($time_diff >= $max_execution_time) {
		wptc_log($time_diff, "--------cutin ya--------");
		return true;
	} else {
		// wptc_log($time_diff, "--------allow--------");
	}
	return false;
}

function wptc_get_sanitized_home_path() {
	//If site address and WordPress address differ but are not in a different directory
	//then get_home_path will return '/' and cause issues.
	$home_path = WPTC_ABSPATH;
	// if ($home_path == '/') {
	// 	$home_path = WPTC_ABSPATH;
	// }
	if (WPTC_DEBUG_SIMPLE) {
		$home_path = WPTC_ABSPATH . WPTC_WP_CONTENT_DIR .'/plugins/dark/';
	}
	return rtrim(wp_normalize_path($home_path), '/');
}

function wptc_get_upload_dir(){
	$options_obj = WPTC_Factory::get('config');
	if (defined('WPTC_BRIDGE')) {
		$uploadDir['basedir'] = WPTC_RELATIVE_WP_CONTENT_DIR . '/uploads';
	} else {
		$uploadDir = wp_upload_dir();
	}

	$upload_dir = str_replace(WPTC_ABSPATH, WPTC_RELATIVE_ABSPATH, $uploadDir['basedir']);

	return wp_normalize_path($upload_dir);
}

function get_dirs_to_exculde_wptc() {
	$upload_dir_path = wptc_get_upload_dir();
	$path = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/managewp/backups",
			WPTC_RELATIVE_WP_CONTENT_DIR   . "/" . md5('iwp_mmb-client') . "/iwp_backups",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/infinitewp",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/".md5('mmb-worker')."/mwp_backups",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/backupwordpress",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/contents/cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/content/cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/logs",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/old-cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/w3tc",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/cmscommander/backups",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/gt-cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wfcache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/widget_cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/bps-backup",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/old-cache",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/updraft",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/nfwlog",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/upgrade",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wflogs",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/tmp",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/backups",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/updraftplus",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wishlist-backup",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptouch-data/infinity-cache/",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/mysql.sql",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_clTimeTaken.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_clMemoryPeak.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_clMemoryUsage.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_clCalledTime.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_func_mem.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_func.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_server_call_log_wptc.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_dev_log_auto_update.php",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_dev_log_auto_update.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-server-request-logs.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-logs.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-memory-peak.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-memory-usage.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-time-taken.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-cpu-usage.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/debug.log",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/Dropbox_Backup",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/backup-db",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/updraft",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/w3tc-config",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/aiowps_backups",
			rtrim ( trim ( WPTC_RELATIVE_PLUGIN_DIR ) , '/' ), //WPTC plugin's file path
			$upload_dir_path . "/wp-clone",
			$upload_dir_path . "/db-backup",
			$upload_dir_path . "/ithemes-security",
			$upload_dir_path . "/mainwp/backup",
			$upload_dir_path . "/backupbuddy_backups",
			$upload_dir_path . "/vcf",
			$upload_dir_path . "/pb_backupbuddy",
			$upload_dir_path . "/sucuri",
			$upload_dir_path . "/aiowps_backups",
			$upload_dir_path . "/gravity_forms",
			$upload_dir_path . "/mainwp",
			$upload_dir_path . "/snapshots",
			$upload_dir_path . "/wp-clone",
			$upload_dir_path . "/wp_system",
			$upload_dir_path . "/wpcf7_captcha",
			$upload_dir_path . "/wc-logs",
			$upload_dir_path . "/siteorigin-widgets",
			$upload_dir_path . "/wp-hummingbird-cache",
			$upload_dir_path . "/wp-security-audit-log",
			$upload_dir_path . "/freshizer",
			$upload_dir_path . "/report-cache",
			$upload_dir_path . "/cache",
			$upload_dir_path . "/et_temp",
			$upload_dir_path . "/wptc_restore_logs",
			$upload_dir_path . "/bb-plugin",
			WPTC_RELATIVE_ABSPATH . "wp-admin/error_log",
			WPTC_RELATIVE_ABSPATH . "wp-admin/php_errorlog",
			WPTC_RELATIVE_ABSPATH . "error_log",
			WPTC_RELATIVE_ABSPATH . "error.log",
			WPTC_RELATIVE_ABSPATH . "debug.log",
			WPTC_RELATIVE_ABSPATH . "WS_FTP.LOG",
			WPTC_RELATIVE_ABSPATH . "security.log",
			WPTC_RELATIVE_ABSPATH . "wp-tcapsule-bridge.zip",
			WPTC_RELATIVE_ABSPATH . "dbcache",
			WPTC_RELATIVE_ABSPATH . "pgcache",
			WPTC_RELATIVE_ABSPATH . "objectcache",
		);
	return $path;
}

function get_extra_files_to_exclude_for_staging(){
	$upload_dir_path = wptc_get_upload_dir();
	$path = array(
		WPTC_RELATIVE_WP_CONTENT_DIR . "/wp-rocket-config",
		WPTC_RELATIVE_WP_CONTENT_DIR . "/mu-plugins/swift-performance-loader.php",
		WPTC_RELATIVE_WP_CONTENT_DIR . "/plugins/swift-performance",
		WPTC_RELATIVE_WP_CONTENT_DIR . "/plugins/wp-redis",
		WPTC_RELATIVE_WP_CONTENT_DIR . "/object-cache.php",
		WPTC_RELATIVE_ABSPATH . "user.ini",
	);
	
	return $path;
}

function wptc_set_cookie($name, $value = false) {
	$options_obj = WPTC_Factory::get('config');

	//Only for testing purpose
	if (WPTC_ENV !== 'production' && WPTC_BACKWARD_BACKUPS_CREATION) {
		if ($name === 'backupID') {
			$testing_current_date = $options_obj->get_option('testing_current_date');
			$value = strtotime(date('Y-m-d', strtotime('today - ' . $testing_current_date . ' days')));
			$options_obj->set_option('testing_current_date', $testing_current_date - 1);
		}
	}

	if (!$value) {
		$value = time();
	}

	$contents[$name] = $value;
	$_GLOBALS['this_cookie'] = $contents;
	$options_obj->set_option('this_cookie', serialize($contents));

	return true;
}

function wptc_get_cookie($name) {
	$options_obj = WPTC_Factory::get('config');
	if (!$options_obj->get_option('this_cookie')) {
		return false;
	} else {
		$contents = @unserialize($options_obj->get_option('this_cookie'));
		if (!isset($contents[$name])) {
			return false;
		}
		return $contents[$name];
	}
}

function wptc_delete_cookie() {

	$options_obj = WPTC_Factory::get('config');
	$options_obj->set_option('this_cookie', false);

	return true;
}

function is_wptc_restore_request_timed_out($need_return = NULL) {
	global $start_time_tc_bridge;

	if (!defined('WPTC_TIMEOUT')) {
		define('WPTC_TIMEOUT', 23);
	}

	if (!empty($start_time_tc_bridge) && (time() - $start_time_tc_bridge) >= WPTC_TIMEOUT) {
		if ($need_return == 1) {
			return true;
		}
		wptc_die_with_json_encode( "wptcs_callagain_wptce" );
	}
}

function wptc_temp_copy() {
	global $wp_filesystem;
	if (!$wp_filesystem) {
		initiate_filesystem_wptc();
		if (empty($wp_filesystem)) {
			send_response_wptc('FS_INIT_FAILED-016');
			return false;
		}
	}
}

function check_is_file_from_file_name_wptc($file_name) {
	$base_name_rseult = basename($file_name);

	$exploded = explode('.', $base_name_rseult);

	if (count($exploded) > 1) {
		return true;
	}

	return false;
}

function wptc_manual_debug($conditions = '', $printText = '', $forEvery = 0) {
	if (!defined('WPTC_DEBUG') || !WPTC_DEBUG) {
		return ;
	}

	global $debug_count;
	$debug_count++;
	$printText = '-' . $printText;

	global $every_count;
	//$conditions = 'printOnly';

	if (empty($forEvery)) {
		return wptc_print_memory_debug($debug_count, $conditions, $printText);
	}

	$every_count++;
	if ($every_count % $forEvery == 0) {
		return wptc_print_memory_debug($debug_count, $conditions, $printText);
	}

}

function wptc_print_memory_debug($debug_count, $conditions = '', $printText = '') {
	global $wptc_profiling_start;
	$config = WPTC_Factory::get('config');

	$this_memory_peak_in_mb = memory_get_peak_usage();
	$this_memory_peak_in_mb = $this_memory_peak_in_mb / 1048576;

	$this_memory_in_mb = memory_get_usage();
	$this_memory_in_mb = $this_memory_in_mb / 1048576;

	$current_cpu_load = 0;

	if (function_exists('sys_getloadavg')) {
		$cpu_load = sys_getloadavg();
		$current_cpu_load = $cpu_load[0];
	}

	if (empty($wptc_profiling_start)) {
		$wptc_profiling_start = time();
	}

	$this_time_taken = time() - $wptc_profiling_start;

	$human_readable_profile_start = date('H:i:s', $wptc_profiling_start);

	if ($conditions == 'printOnly') {
		if ($this_memory_peak_in_mb >= 34) {
			file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-memory-usage.txt', $debug_count . $printText . " " . round($this_memory_in_mb, 2) . "\n", FILE_APPEND);
			file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-time-taken.txt', $debug_count . $printText . " " . round($this_time_taken, 2) . "\n", FILE_APPEND);
			file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-cpu-usage.txt', $debug_count . $printText . " " . $current_cpu_load . "\n", FILE_APPEND);
			file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-memory-peak.txt', $debug_count . $printText . " " . round($this_memory_peak_in_mb, 2) . "\n", FILE_APPEND);
		}
		return ;
	}

	file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-memory-usage.txt', $debug_count . $printText . " " . round($this_memory_in_mb, 2) . "\n", FILE_APPEND);
	file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-time-taken.txt', $debug_count . $printText . " " . round($this_time_taken, 2) . "\n", FILE_APPEND);
	file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-cpu-usage.txt', $debug_count . $printText . " " . $current_cpu_load . "\n", FILE_APPEND);
	file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-memory-peak.txt', $debug_count . $printText . " " . round($this_memory_peak_in_mb, 2) . "\n", FILE_APPEND);
}


function wptc_log($value = null, $key = null, $is_print_all_time = true, $forEvery = 0) {
	if (!defined('WPTC_DEBUG') || !WPTC_DEBUG || !$is_print_all_time) {
		return ;
	}

	try {
		global $every_count;
		//$conditions = 'printOnly';

		$usr_time = time();

		if (function_exists('user_formatted_time_wptc') && class_exists('WPTC_Base_Factory')) {
			$usr_time = user_formatted_time_wptc(time());
		}

		if (empty($forEvery)) {
			return @file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-logs.txt', "\n -----$key------------$usr_time --- " . microtime(true) . "  ----- " . var_export($value, true) . "\n", FILE_APPEND);
		}

		$every_count++;
		if ($every_count % $forEvery == 0) {
			return @file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-logs.txt', "\n -----$key------- " . var_export($value, true) . "\n", FILE_APPEND);
		}

	} catch (Exception $e) {
		@file_put_contents(WPTC_WP_CONTENT_DIR . '/wptc-logs.txt', "\n -----$key----------$usr_time --- " . microtime(true) . "  ------ " . var_export(serialize($value), true) . "\n", FILE_APPEND);
	}
}

function wptc_get_home_page_url() {
	$config = WPTC_Factory::get('config');
	$site_url = $config->get_option('site_url_wptc');
	return $site_url;
}

function wptc_get_monitor_page_url() {
	// $monitor_url = network_admin_url('admin.php?page=wp-time-capsule-monitor');
	$monitor_url = WPTC_Factory::get('config')->get_option('child_site_specific_admin_url');
	return $monitor_url;
}

function wptc_get_options_page_url() {
	$options_url = network_admin_url('admin.php?page=wp-time-capsule');
	return $options_url;
}

function get_wptc_cron_url() {
	return trailingslashit(home_url());
}

function user_formatted_time_wptc($timestamp = '', $format = false) {
	if (empty($timestamp)) {
		return false;
	}
	if (empty($format)) {
		$usr_formated_time = WPTC_Factory::get('config')->get_wptc_user_today_date_time('g:i:s a Y-m-d', $timestamp);
	} else {
		$usr_formated_time = WPTC_Factory::get('config')->get_wptc_user_today_date_time($format, $timestamp);
	}
	return $usr_formated_time;
}

function send_response_wptc($status = null, $type = null, $data = null, $is_log = 0, $clear_request_time = true) {
	if (!is_wptc_server_req() && !is_wptc_node_server_req()) {
		return false;
	}

	wptc_log(get_backtrace_string_wptc(),'---------send_response_wptc-----------------');


	if (empty($is_log)) {
		$config = WPTC_Factory::get('config');
		$post_arr['status'] = $status;
		$post_arr['type'] = $type;
		$post_arr['version'] = WPTC_VERSION;
		$post_arr['source'] = 'WPTC';
		$post_arr['scheduled_time'] = $config->get_option('schedule_time_str');
		$post_arr['timezone'] = $config->get_option('wptc_timezone');
		$post_arr['last_backup_time'] = $config->get_option('last_backup_time');
		if (!empty($data)) {
			$post_arr['progress'] = $data;
		}
	} else {
		$post_arr = $data;
	}

	if ($clear_request_time) {
		reset_last_backup_request_wptc();
	}

	wptc_manual_debug('', 'send_response_wptc');

	die("<WPTC_START>".json_encode($post_arr)."<WPTC_END>");
}

function reset_last_backup_request_wptc(){
	if (!is_wptc_server_req()) {
		return ;
	}

	WPTC_Factory::get('config')->set_option('last_backup_request', false);
}


function search_array_wptc($array, $key, $val) {
	foreach ($array as $item){
		if (isset($item[$key]) && $item[$key] == $val){
			return true;
		}
	}
	return false;
}

function reset_backup_related_settings_wptc() {
	wptc_log(array(), '-----------reset_backup_related_settings_wptc-------------');
	$config = WPTC_Factory::get('config');
	//resetting backup config-options
	$config->set_option('gotfileslist', false);
	$config->set_option('in_progress', false);
	$config->set_option('is_running', false);
	$config->set_option('auto_backup_running', 0);
	$config->set_option('wptc_main_cycle_running', 0);
	$config->set_option('schedule_backup_running', false);
	$config->set_option('do_wptc_meta_data_backup', false);
	$config->set_option('allow_system_to_backup_meta_file', false);
	// $config->set_option('cached_wptc_g_drive_folder_id', 0);
	// $config->set_option('cached_g_drive_this_site_main_folder_id', 0);
	$config->set_option('is_meta_data_backup_failed', '');
	$config->set_option('meta_data_upload_offset', 0);
	$config->set_option('meta_data_upload_id', '');
	$config->set_option('meta_data_upload_s3_part_number', '');
	$config->set_option('meta_data_upload_s3_parts_array', '');
	$config->set_option('meta_data_backup_process', '');
	$config->set_option('backup_before_update_progress', false);
	$config->set_option('wptc_current_backup_type', 0);
	$config->set_option('recent_restore_ping', false);
	$config->set_option('wptc_update_progress', false);
	$config->set_option('reset_chunk_upload_on_failure_count', false);
	$config->set_option('backup_current_action', false);
	$config->set_option('sql_gz_compression_offset', false);
	$config->set_option('sql_gz_compression', false);
	$config->set_option('current_process_file_id', false);
	$config->set_option('last_backup_request', false);
	$config->set_option('db_encrypt_completed', false);

	//Clear realtime secret to avoid queries conflict during the backup
	$config->set_option('wptc_realtime_tmp_secret', false);

	$config->set_option('collected_tables_for_backups', false);
	$config->set_option('collected_tables_for_backups_offset', false);
	$config->set_option('collected_tables_for_backups_size', false);
	$config->set_option('iterator_file_size', false);

	global $wpdb;
	$wpdb->query("TRUNCATE TABLE `" . $wpdb->base_prefix . "wptc_current_process`");
}

function wptc_remove_response_junk(&$response){
	$headerPos = stripos($response, '<WPTCHEADER');
	if($headerPos !== false){
		$response = substr($response, $headerPos);
		$response = substr($response, strlen('<WPTCHEADER>'), stripos($response, '</ENDWPTCHEADER')-strlen('<WPTCHEADER>'));
	}
}

function wptc_die_with_json_encode($msg = 'empty data', $escape = 0){
	reset_last_backup_request_wptc();

	switch ($escape) {
		case 1:
		die(json_encode($msg, JSON_UNESCAPED_SLASHES));
		case 2:
		die(json_encode($msg, JSON_UNESCAPED_UNICODE));
	}
	die(json_encode($msg));
}

function wptc_is_dir($good_path){
	$good_path = wp_normalize_path($good_path);

	if (is_dir($good_path)) {
		return true;
	}

	$ext = pathinfo($good_path, PATHINFO_EXTENSION);

	if (!empty($ext)) {
		return false;
	}

	if (is_file($good_path)) {
		return false;
	}

	return true;
}

function wptc_remove_secret($file, $basename = true) {

	if (stripos($file, 'wptc-secret') === false) {
		return ($basename) ? basename($file) : $file ;
	}

	if (stripos($file, 'sql') !== false) {
		$file = wptc_remove_secret_sql_file($file);
	} else {
		$file = substr($file, 0, strrpos($file, '.'));
	}

	return ($basename) ? basename($file) : $file ;
}

function wptc_remove_secret_sql_file($file){

	$result_file = substr($file, 0, strrpos($file, '.sql'));

	if (stripos($file, '.gz') !== false) {
		$result_file .= stripos($file, '.crypt') !== false ? '.sql.gz.crypt' : '.sql.gz' ;
	} else {
		$result_file .= stripos($file, '.crypt') !== false ? '.sql.crypt' : '.sql' ;
	}

	return $result_file;
}

function wptc_add_trailing_slash($string) {
	return wptc_remove_trailing_slash($string) . '/';
}

function wptc_remove_trailing_slash($string) {
	return rtrim($string, '/');
}

function wptc_decode_auth_token($token, $get_param = 'appId'){
	$tokenObj = explode('.', $token);
	$base64Url = $tokenObj[1];
	$base64Url = str_replace('-', '+', $base64Url);
	$base64Url = str_replace('_', '/', $base64Url);
	$decoded_data = base64_decode($base64Url);

	wptc_log($decoded_data, "--------decoded_data---wptc_decode_auth_token-----");

	if (empty($decoded_data)) {

		return false;
	}

	$auth_data = json_decode($decoded_data, true);

	wptc_log($auth_data, "--------auth_data---wptc_decode_auth_token-----");

	if (empty($auth_data)) {

		return false;
	}

	return $auth_data[$get_param];
}

function send_bulk_setup_status_to_server($status = 'pending') {

	wptc_log($status, "------trying--send_bulk_setup_status_to_server--------");

	$config = WPTC_Factory::get('config');
	$config->set_wptc_installed_true();
	$app_id = $config->get_option('appID');

	$email = trim($config->get_option('main_account_email', true));
	$emailhash = md5($email);
	$email_encoded = base64_encode($email);

	$post_arr = array(
		'app_id' => $app_id,
		'email' => $email_encoded,
		'bulk_setup_progress' => $status
	);

	do_cron_call_wptc('bulk-setup-progress', $post_arr, 'POST');
}

function initiate_filesystem_wptc() {
	$is_admin_call = false;
	if(is_admin()){
		$is_admin_call = true;
		global $initiate_filesystem_wptc_direct_load;
		if (empty($initiate_filesystem_wptc_direct_load)) {
			$initiate_filesystem_wptc_direct_load = true;
		} else{
			if (!is_wptc_server_req()) {
				return false;
			}
		}
	}

	if(!is_wptc_server_req() && $is_admin_call === false){
		return false;
	}

	if(!function_exists('request_filesystem_credentials')){
		include_once WPTC_ABSPATH . 'wp-admin/includes/file.php';
	}

	$creds = request_filesystem_credentials("", "", false, false, null);
	if (false === $creds) {
		return false;
	}

	if (!WP_Filesystem($creds)) {
		return false;
	}
}

function wptc_is_hash_required($file_path){
	if ( is_readable($file_path) && filesize($file_path) < WPTC_HASH_FILE_LIMIT) {
		return true;
	} else {
		return false;
	}
}

function get_checkbox_input_wptc($id, $value = '', $current_setting = '', $name = '') {
	$is_checked = '';
	if ($current_setting == $value) {
		$is_checked = 'checked';
	}

	$input = '';
	$input .= '<input name="'.$name.'" type="checkbox" id="' . $id . '"	' . $is_checked . ' value="' . $value . '">';

	return $input;
}

function error_alert_wptc_server($err_info_arr = array()) {
	$config = WPTC_Factory::get('config');

	$app_id = $config->get_option('appID');

	$email = trim($config->get_option('main_account_email', true));
	$email_encoded = base64_encode($email);

	$pwd = trim($config->get_option('main_account_pwd', true));
	$pwd_encoded = base64_encode($pwd);

	//$post_string = 'site_url=' . home_url() . "&pwd=" . $pwd_encoded . "&name=" . $name . "&email=" . $email . "&cloudAccount=" . $cloudAccount . "&connectedEmail" . $connectedEmail;

	$post_req = array(
		'app_id' => $app_id,
		'email' => $email_encoded,
		'site_url' => home_url(),
	);

	$post_arr = array_merge($post_req, $err_info_arr);
	$push_result = do_cron_call_wptc('users/alert', $post_arr);
}

function is_any_other_wptc_process_going_on() {
	if (apply_filters('is_any_staging_process_going_on', '')) {
		return true;
	}
	return false;
}

function stop_if_ongoing_backup_wptc(){
	if(is_any_ongoing_wptc_backup_process()){
		wptc_set_backup_in_progress_server(false);
	}
}

function purify_plugin_update_data_wptc($raw_upgrade_details) {
	$result = get_plugins();
	foreach ($raw_upgrade_details as $key => $value) {
		$upgrade_details[$value] = $result[$value]['Version'];
	}
	return $upgrade_details;
}

function purify_translation_update_data_wptc($raw_upgrade_details) {
	wptc_log($raw_upgrade_details, '---------purify_translation_update_data-------------');
	return $raw_upgrade_details;
}

function purify_theme_update_data_wptc($raw_upgrade_details) {
	wptc_log($raw_upgrade_details, '---------purify_theme_update_data-------------');
	return $raw_upgrade_details;
}

function purify_core_update_data_wptc($raw_upgrade_details) {
	wptc_log($raw_upgrade_details, '---------purify_core_update_data-------------');

	$is_multisite = is_multisite();
	$is_function_exist = function_exists('get_site_option');
	if( $is_multisite && $is_function_exist ){
		$transient = (array) get_site_option( '_site_transient_update_core' );
	} else {
		$transient = (array) wptc_mmb_get_transient('update_core');
	}

	$std_obj_data = $transient['updates'][0];
	$std_obj_into_array = (array) $transient['updates'][0];
	// $data = $data[0];
	if ($std_obj_into_array['version'] == $raw_upgrade_details[0]) {
		wptc_log($std_obj_data, '---------$std_obj_data-------------');
		return $std_obj_data;
	}

	$config = WPTC_Factory::get('config');
	$config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => 'WordPress upgrade version mismatch :(')));
	return false;
}

function is_wptc_table($tableName) {
	global $wpdb;
	//ignoring tables of wptc plugin
	$wp_prefix_with_tc_prefix = $wpdb->base_prefix . WPTC_TC_PLUGIN_NAME;
	$wptc_strpos = stripos($tableName, $wp_prefix_with_tc_prefix);

	if (false !== $wptc_strpos && $wptc_strpos === 0) {
		return true;
	}
	return false;
}


function is_wptc_file($file){
	if(stripos($file, WPTC_TC_PLUGIN_NAME) === FALSE){
		return false;
	}

	if(stripos($file, 'imagify') !== FALSE){
		return false;
	}

	return true;
}

function wptc_mmb_get_transient($option_name) {
	if (trim($option_name) == '') {
		return FALSE;
	}
	global $wp_version;
	$transient = array();
	if (version_compare($wp_version, '2.7.9', '<=')) {
		return get_option($option_name);
	} else if (version_compare($wp_version, '2.9.9', '<=')) {
		$transient = get_option('_transient_' . $option_name);
		return apply_filters("transient_" . $option_name, $transient);
	} else {
		$transient = get_option('_site_transient_' . $option_name);
		return apply_filters("site_transient_" . $option_name, $transient);
	}
}

function is_wptc_server_req(){
	global $wptc_server_req;
	if (isset($wptc_server_req) && $wptc_server_req === true) {
		return true;
	}
	return false;
}

function is_wptc_node_server_req(){
	global $wptc_node_server_req;
	if (isset($wptc_node_server_req) && $wptc_node_server_req === true) {
		return true;
	}
	return false;
}

function trim_value_wptc(&$v){
	$v = trim($v);
}

function wptc_get_file_size($file) {
	clearstatcache();

	if ( !file_exists($file) || !is_file($file) ) {
		return false;
	}

	$normal_file_size = filesize($file);

	if(($normal_file_size !== false)&&($normal_file_size >= 0)) {
		return $normal_file_size;
	}

	$file = realPath($file);

	if(!$file) {
		return false;
	}

	$ch = curl_init("file://" . $file);
	curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_FILE);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	$data = curl_exec($ch);
	$curl_error = curl_error($ch);
	curl_close($ch);

	if ($data !== false && preg_match('/Content-Length: (\d+)/', $data, $matches)) {
		return (string) $matches[1];
	}

	return $normal_file_size;
}

if (!function_exists('json_encode')) {
	function json_encode($a=false) {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_string($a)) {
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			} else{
				return $a;
			}
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))	{
			if (key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) $result[] = wptc_json_encoder($v);
			return '[' . join(',', $result) . ']';
		}
		else {
			foreach ($a as $k => $v) $result[] = wptc_json_encoder($k).':'.wptc_json_encoder($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

function wptc_json_encoder( $data, $options = 0, $depth = 512 ) {
	if ( version_compare( PHP_VERSION, '5.5', '>=' ) ) {
		$args = array( $data, $options, $depth );
	} elseif ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
		$args = array( $data, $options );
	} else {
		$args = array( $data );
	}
	$json = @call_user_func_array( 'json_encode', $args );

	if ( false !== $json && ( version_compare( PHP_VERSION, '5.5', '>=' ) || false === strpos( $json, 'null' ) ) )  {
		return $json;
	}

	$args[0] = jsonCompatibleCheck( $data, $depth );
	return @call_user_func_array( 'json_encode', $args );
}

function wptc_is_chunk_hash_required($file_path){
	return (filesize($file_path) > HASH_CHUNK_LIMIT) ? true : false;
}

function wptc_get_hash($file_path, $limit = 0, $offset = 0) {
	// wptc_log(func_get_args(), '---------func_get_args()------------');
	$is_hash_required = wptc_is_hash_required($file_path);
	// wptc_log($is_hash_required, '---------$is_hash_required------------');
	if (!$is_hash_required) {
		return null;
	}
	$chunk_hash = wptc_is_chunk_hash_required($file_path);
	// wptc_log($chunk_hash, '---------$chunk_hash------------');
	if ($chunk_hash === false) {

		if ( !file_exists($file_path) || !is_file($file_path) ) {
			return null;
		}

		// md5_file is always faster if we don't chunk the file
		$hash = md5_file($file_path);

		return $hash !== false ? $hash : null;
	}
	$ctx = hash_init('md5');
	if (!$ctx) {
		// Fail to initialize file hashing
		return null;
	}

	$limit = filesize($file_path) - $offset;

	$handle = @fopen($file_path, "rb");
	if ($handle === false) {
		// Failed opening file, cleanup hash context
		hash_final($ctx);

		return null;
	}

	fseek($handle, $offset);

	while ($limit > 0) {
		// Limit chunk size to either our remaining chunk or max chunk size
		$chunkSize = $limit < HASH_CHUNK_LIMIT ? $limit : HASH_CHUNK_LIMIT;
		$limit -= $chunkSize;

		$chunk = fread($handle, $chunkSize);
		hash_update($ctx, $chunk);
	}

	fclose($handle);

	return hash_final($ctx);
}

function wptc_dupx_array_rtrim(&$value) {
	$value = rtrim($value, '\/');
}

function wptc_is_zero_bytes_file($file){
	if (!file_exists($file)) {
		return false;
	}

	return (filesize($file) === 0) ? true : false;
}

function wptc_save_files_zero_bytes($is_zero_bytes_file, $file){
	if (!$is_zero_bytes_file) {
		return false;
	}

	$config = WPTC_Factory::get('config');
	$raw = $config->get_option('zero_bytes_files_list', true);
	if (empty($raw)) {
		return $config->set_option('zero_bytes_files_list', serialize(array($file)));
	}
	wptc_log($raw, '---------------$raw-----------------');
	$unserialized = unserialize($raw);
	wptc_log($unserialized, '---------------$unserialize1-----------------');
	if (empty($unserialized)) {
		return $config->set_option('zero_bytes_files_list', serialize(array($file)));
	}
	wptc_log($unserialized, '---------------$unserialize-----------------');
	$unserialized[] = $file;
	wptc_log($unserialized, '---------------$unserialize new-----------------');
	$unserialized = array_unique($unserialized);
	wptc_log($unserialized, '---------------$unserialized-----------------');
	return $config->set_option('zero_bytes_files_list', serialize($unserialized));
}

function wptc_is_file_in_zero_bytes_list($file){
	wptc_log(array(), '---------------wptc_is_file_in_zero_bytes_list-----------------');
	$config = WPTC_Factory::get('config');
	$raw = $config->get_option('zero_bytes_files_list', true);
	if (empty($raw)) {
		return false;
	}
	wptc_log($raw, '---------------$raw-----------------');
	$unserialized = unserialize($raw);
	wptc_log($unserialized, '---------------$unserialize1-----------------');
	if (empty($unserialized)) {
		return false;
	}

	if (in_array($file, $unserialized)) {
		return true;
	}

	return false;
}

function get_home_path_wptc(){
	$override_script_filename = wp_normalize_path(WPTC_ABSPATH . 'wp-admin/admin.php'); // assume all cron calls like admin calls

	$home    = set_url_scheme( get_option( 'home' ), 'http' );
	$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */

		$pos = strripos( $override_script_filename, trailingslashit( $wp_path_rel_to_home ) );
		$pos += strlen($wp_path_rel_to_home);

		$home_path = trailingslashit( substr( $override_script_filename, 0, $pos ) );
	} else {
		$home_path = WPTC_ABSPATH;
	}

	return wp_normalize_path($home_path);
}

function set_server_req_wptc($node_server_req = false){
	global $wptc_server_req;
	$wptc_server_req = true;
	if ($node_server_req) {
		global $wptc_node_server_req;
		$wptc_node_server_req = true;
	}
}

function is_windows_machine_wptc(){
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return true;
	}
	return false;
}

function wptc_wait_for_sometime(){
	//Windows filesyetem slower so wait for sometime
	if (!is_windows_machine_wptc()) {
		return ;
	}

	@usleep(250000);
    // Maybe a concurrent script has deleted the file in the meantime
    @clearstatcache();
}

function is_server_writable_wptc(){

	if (!function_exists('get_filesystem_method')) {
		include_once ABSPATH.'wp-admin/includes/file.php';
	}

	if ((!defined('FTP_HOST') || !defined('FTP_USER')) && (get_filesystem_method(array(), false) != 'direct')) {
		return false;
	} else {
		return true;
	}
}

function wptc_mmb_delete_transient($option_name) {
	if (trim($option_name) == '') {
		return FALSE;
	}

	global $wp_version;
	if (version_compare($wp_version, '2.7.9', '<=')) {
		delete_option($option_name);
	} else if (version_compare($wp_version, '2.9.9', '<=')) {
		delete_option('_transient_' . $option_name);
	} else {
		delete_option('_site_transient_' . $option_name);
	}
}

function is_any_ongoing_wptc_restore_process() {
	$options = WPTC_Factory::get('config');
	if ( $options->get_option('in_progress_restore') 
		|| $options->get_option('is_bridge_process') 
		|| $options->get_option('is_running_restore') 
		|| $options->get_option('wptc_update_progress') == 'start' ) {
		// wptc_log(array(), "--------On going restore process--------");
		return true;
	}
	return false;
}

function is_any_ongoing_wptc_backup_process() {
	$options = WPTC_Factory::get('config');
	if (($options) && ($options->get_option('in_progress') || $options->get_option('is_running') || $options->get_option('wptc_main_cycle_running') || $options->get_option('auto_backup_running') || $options->get_option('wptc_update_progress') == 'start')) {
			//wptc_log(array(), "--------on going backup process--------");
			return true;
		}
	return false;
}

function wptc_copy_large_file($src, $dst) {
	wptc_manual_debug('', 'start_large_copy_files');

	$src = fopen($src, 'r');
	$dest = fopen($dst, 'w');

	// Try first method:
	while (! feof($src)){
		if (false === fwrite($dest, fread($src, WPTC_STAGING_COPY_SIZE))){
			$error = true;
		}
	}
	// Try second method if first one failed
	if (isset($error) && ($error === true)){
		while(!feof($src)){
			stream_copy_to_stream($src, $dest, 1024 );
		}
		fclose($src);
		fclose($dest);
		return true;
	}
	fclose($src);
	fclose($dest);
	wptc_manual_debug('', 'start_large_copy_files');
	return true;
}

function wptc_check_folder_exist($dir){
	if (is_dir($dir)) {
		return true;
	}

	return false;
}

function wptc_function_exist($function){

	if (empty($function)) {
		return false;
	}

	if ( !function_exists($function) ) {
		return false;
	}

	$disabled_functions = explode(',', ini_get('disable_functions'));
	$function_enabled = !in_array($function, $disabled_functions);
	return ($function_enabled) ? true : false;
}

function wptc_set_time_limit($seconds){

	if(!wptc_function_exist('set_time_limit')){
		return false;
	}

	@set_time_limit($seconds);
}

function wptc_setlocale(){
	if(function_exists('get_locale')){
		$locale = get_locale();
	} else {
		$locale = 'en_US';
	}

	setlocale(LC_CTYPE, $locale);
}

function wptc_add_fullpath($file){
	$file = wp_normalize_path($file);

	if (wptc_is_wp_content_path($file)) {
		//Special patch for wp-content dir to support common functions of paths.

		$temp_file = $file;

		if(stripos($file, WPTC_RELATIVE_WP_CONTENT_DIR ) === 0 ){
			$temp_file = substr_replace($file, '', 0, strlen(WPTC_RELATIVE_WP_CONTENT_DIR));
			if($temp_file === '' || $temp_file === '/'){
				$temp_file = WPTC_WP_CONTENT_DIR;
			}
		}

		return wptc_add_custom_path($temp_file, $custom_path = WPTC_WP_CONTENT_DIR . '/');
	}

	return wptc_add_custom_path($file, $custom_path = WPTC_ABSPATH);
}

function wptc_remove_fullpath($file){
	$file = wp_normalize_path($file);

	if (wptc_is_wp_content_path($file)) {

		$temp_file = $file;

		if(stripos($file, WPTC_RELATIVE_WP_CONTENT_DIR ) === 0 ){
			$temp_file = substr_replace($file, '', 0, strlen(WPTC_RELATIVE_WP_CONTENT_DIR));
			if($temp_file === '' || $temp_file === '/'){
				$temp_file = WPTC_WP_CONTENT_DIR;
			}
		}

		if(wptc_remove_trailing_slash($file) === wptc_remove_trailing_slash(WPTC_WP_CONTENT_DIR)  ){
			$temp_file = wptc_remove_trailing_slash($temp_file);
		}


		return wptc_remove_custom_path($temp_file, $custom_path = WPTC_WP_CONTENT_DIR , $relative_path = WPTC_RELATIVE_WP_CONTENT_DIR );
	}

	return wptc_remove_custom_path($file, $custom_path = WPTC_ABSPATH, $relative_path = WPTC_RELATIVE_ABSPATH);
}

function wptc_is_wp_content_path($file){
	if (stripos($file, '/' . WPTC_WP_CONTENT_BASENAME) === 0 || stripos($file, WPTC_WP_CONTENT_DIR) === 0) {
		return true;
	}

	return false;
}

function wptc_add_custom_path($file, $custom_path){

	$temp_file = wptc_add_trailing_slash($file);

	if (stripos($temp_file, $custom_path) !== false) {
		return $file;
	}

	return $custom_path . ltrim($file, '/');
}

function wptc_remove_custom_path($file, $custom_path, $relative_path){
		// wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

	if (stripos($file, $custom_path) === false) {
		if(substr($relative_path, -1) === '/'){
			return $relative_path . ltrim($file, '/');
		}

		return $relative_path . '/' . ltrim($file, '/');
	}

	return str_replace($custom_path, $relative_path, $file);
}

function wptc_is_wp_content_dir_moved_outside_root($config = false){
	if (empty($config)) {
		return WPTC_Factory::get('config')->get_option('is_wp_content_dir_moved_outside_root');
	}

	return $config->get_option('is_wp_content_dir_moved_outside_root');
}

function wptc_replace_abspath(&$file, $change_reference = true){

	if(!defined('WPTC_BRIDGE')){
		return $file;
	}

	$file = wp_normalize_path($file);

	if (!defined('WPTC_SITE_ABSPATH')) {
		return $file;
	}

	if (WPTC_SITE_ABSPATH === WPTC_ABSPATH) {
		return $file;
	}

	if (stripos($file, WPTC_SITE_ABSPATH) === false) {
		return $file;
	}

	if ($change_reference) {
		$file = str_replace(WPTC_SITE_ABSPATH, WPTC_ABSPATH, $file);
		return $file;
	}

	return str_replace(WPTC_SITE_ABSPATH, WPTC_ABSPATH, $file);
}

function wptc_init_monitor_js_keys(){
?>

<script type="text/javascript" language="javascript">
	//initiating Global Variables here

	var sitenameWPTC = '<?php echo addslashes(get_bloginfo('name')); ?>';
	var bp_in_progress = false;
	var wp_base_prefix_wptc = '<?php global $wpdb; echo $wpdb->base_prefix;?>';		//am sending the prefix ; since it is a bridge
	var this_home_url_wptc = '<?php echo network_site_url(); ?>' ;
	var defaultDateWPTC = '<?php echo date('Y-m-d', time()) ?>' ;
	var wptcOptionsPageURl = '<?php echo plugins_url('wp-time-capsule'); ?>' ;
	var this_plugin_url_wptc = '<?php echo plugins_url(); ?>' ;
	var wptcMonitorPageURl = '<?php echo admin_url('admin.php?page=wp-time-capsule-monitor'); ?>';
	var wptcPluginURl = '<?php echo plugins_url() . '/' . WPTC_TC_PLUGIN_NAME; ?>';
	var on_going_restore_process = false;
	var cuurent_bridge_file_name = seperate_bridge_call = '';
	var TRIGGER_PREVENT_TABLES_COUNT_WPTC = 180;
</script>

<?php
}

function wptc_set_fallback_db_search_1_14_0(){
	if (defined('WPTC_BACKWARD_DB_SEARCH')) {
		return ;
	}

	$config = WPTC_Factory::get('config');
	$wptc_backward_db_search = $config->get_option('wptc_backward_db_search');

	if ($wptc_backward_db_search == 'no') {
		return define('WPTC_BACKWARD_DB_SEARCH', false);
	} else if($wptc_backward_db_search == 'yes'){
		return define('WPTC_BACKWARD_DB_SEARCH', true);
	}

	$prev_version = $config->get_option('prev_installed_wptc_version');

	if (!empty($prev_version) && version_compare($prev_version, '1.14.0', '<')) {
		$config->set_option('wptc_backward_db_search', 'yes');
		return define('WPTC_BACKWARD_DB_SEARCH', true);
	}

	$config->set_option('wptc_backward_db_search', 'no');
	return define('WPTC_BACKWARD_DB_SEARCH', false);
}

function wptc_is_seeking_exception($exception_msg){
	//Eg: Seek position 29 is out of range
	return ( stripos($exception_msg, 'Seek position') !== false || stripos($exception_msg, 'out of range') !== false );
}

function wptc_is_file_iterator_allowed_exception($exception_msg){
	//Eg: Seek position 29 is out of range
	return stripos($exception_msg, 'open_basedir restriction in effect') !== false ;
}

function wptc_can_load_third_party_scripts(){

	if (!empty($_SERVER['REQUEST_URI']) &&
		strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false ||
		strpos($_SERVER['REQUEST_URI'], 'plugin-install.php') !== false ||
		strpos($_SERVER['REQUEST_URI'], 'themes.php') !== false ||
		strpos($_SERVER['REQUEST_URI'], 'update-core.php') !== false ||
		strpos($_SERVER['REQUEST_URI'], 'page=wp-time-capsule') !== false
		) {
		return true;
	}

	return false;
}

function wptc_is_abspath($path){
	$path = trailingslashit($path);

	if (trailingslashit(WPTC_ABSPATH) == $path) {
		return true;
	}

	if (trailingslashit(wp_normalize_path(get_home_path_wptc())) == $path){
		return true;
	}

	return false;
}

function wptc_is_always_include_file($file){

	$file = wptc_add_fullpath($file);

	if ( stripos($file, WPTC_WP_CONTENT_DIR) === false){
		return false;
	}

	if ( stripos($file, WPTC_TEMP_DIR_BASENAME) === false && stripos($file, WPTC_REALTIME_DIR_BASENAME) === false ){
		return false;
	}

	if ( strpos($file, 'backup.sql') !== false ||  strpos($file, 'wptc_current_files_state.txt') !== false || strpos($file, 'wptc_saved_queries.sql') !== false ) {
		return true;
	} else {
		return false;
	}
}


function wptc_is_meta_data_backup(){
	if (!defined('IS_META_DATA_BACKUP') ) {
		return false;
	}

	if (!IS_META_DATA_BACKUP) {
		return false;
	}

	return true;
}

function wptc_get_live_url(){
	if(is_multisite()){
		return get_home_url();
	}

	return site_url();
}

function wptc_get_tmp_dir($create = true){
	$backup_dir_path = WPTC_Factory::get('config')->get_option('backup_db_path');

	if ( empty($backup_dir_path) ) {
		if ($create) {
			WPTC_Factory::get('config')->choose_db_backup_path();
		} else {
			return false;
		}
	}

	$backup_dir_path = WPTC_Factory::get('config')->get_option('backup_db_path');

	if (empty($backup_dir_path)) {
		return false;
	}

	$path = wptc_add_fullpath($backup_dir_path);

	return $path;
}

function wptc_get_table_from_query( $query ) {
	// Remove characters that can legally trail the table name.
	$query = rtrim( $query, ';/-#' );

	// Allow (select...) union [...] style queries. Use the first query's table name.
	$query = ltrim( $query, "\r\n\t (" );

	// Strip everything between parentheses except nested selects.
	$query = preg_replace( '/\((?!\s*select)[^(]*?\)/is', '()', $query );

	// Quickly match most common queries.
	if ( preg_match( '/^\s*(?:'
			. 'SELECT.*?\s+FROM'
			. '|INSERT(?:\s+LOW_PRIORITY|\s+DELAYED|\s+HIGH_PRIORITY)?(?:\s+IGNORE)?(?:\s+INTO)?'
			. '|REPLACE(?:\s+LOW_PRIORITY|\s+DELAYED)?(?:\s+INTO)?'
			. '|UPDATE(?:\s+LOW_PRIORITY)?(?:\s+IGNORE)?'
			. '|DELETE(?:\s+LOW_PRIORITY|\s+QUICK|\s+IGNORE)*(?:.+?FROM)?'
			. ')\s+((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)/is', $query, $maybe ) ) {
		return str_replace( '`', '', $maybe[1] );
	}

	// SHOW TABLE STATUS and SHOW TABLES WHERE Name = 'wp_posts'
	if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES).+WHERE\s+Name\s*=\s*("|\')((?:[0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)\\1/is', $query, $maybe ) ) {
		return $maybe[2];
	}

	// SHOW TABLE STATUS LIKE and SHOW TABLES LIKE 'wp\_123\_%'
	// This quoted LIKE operand seldom holds a full table name.
	// It is usually a pattern for matching a prefix so we just
	// strip the trailing % and unescape the _ to get 'wp_123_'
	// which drop-ins can use for routing these SQL statements.
	if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES)\s+(?:WHERE\s+Name\s+)?LIKE\s*("|\')((?:[\\\\0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)%?\\1/is', $query, $maybe ) ) {
		return str_replace( '\\_', '_', $maybe[2] );
	}

	// Big pattern for the rest of the table-related queries.
	if ( preg_match( '/^\s*(?:'
			. '(?:EXPLAIN\s+(?:EXTENDED\s+)?)?SELECT.*?\s+FROM'
			. '|DESCRIBE|DESC|EXPLAIN|HANDLER'
			. '|(?:LOCK|UNLOCK)\s+TABLE(?:S)?'
			. '|(?:RENAME|OPTIMIZE|BACKUP|RESTORE|CHECK|CHECKSUM|ANALYZE|REPAIR).*\s+TABLE'
			. '|TRUNCATE(?:\s+TABLE)?'
			. '|CREATE(?:\s+TEMPORARY)?\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?'
			. '|ALTER(?:\s+IGNORE)?\s+TABLE'
			. '|DROP\s+TABLE(?:\s+IF\s+EXISTS)?'
			. '|CREATE(?:\s+\w+)?\s+INDEX.*\s+ON'
			. '|DROP\s+INDEX.*\s+ON'
			. '|LOAD\s+DATA.*INFILE.*INTO\s+TABLE'
			. '|(?:GRANT|REVOKE).*ON\s+TABLE'
			. '|SHOW\s+(?:.*FROM|.*TABLE)'
			. ')\s+\(*\s*((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)\s*\)*/is', $query, $maybe ) ) {
		return str_replace( '`', '', $maybe[1] );
	}

	return false;
}

function wptc_get_collation(){

	global $wpdb;

	if (method_exists( $wpdb, 'get_charset_collate')) {
		$charset_collate =  $wpdb->get_charset_collate();
	}

	return !empty($charset_collate) ?  $charset_collate : ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ' ;
}

function wptc_get_auto_backup_slots(){
	$auto_backup_slots = array();

	$privileges_args = WPTC_Factory::get('config')->get_option('privileges_args');

	if (empty($privileges_args)) {
		wptc_reset_privileges_args();
	}

	$features = json_decode($privileges_args);

	if (empty($features)) {
		wptc_reset_privileges_args();
		return ;
	}

	foreach ($features as $key => $feature) {
		if ($key === 'Wptc_Auto_Backup') {
			$auto_backup_slots =  json_decode(json_encode($feature), true);
			if (empty($auto_backup_slots)) {
				wptc_reset_privileges_args();
			}
			break;
		}
	}

	return $auto_backup_slots;
}

function wptc_json_format_bulk_exit_response($msg) {
	$response_data = array(
		'bulk_setup_response' => $msg
	);
	die("<WPTC_START>".json_encode($response_data)."<WPTC_END>");
}

function wptc_reset_privileges_args(){
	WPTC_Factory::get('config')->set_option('privileges_args', false);
}

function set_admin_notices_wptc($msg, $status, $strict_wptc_page, $do_not_delete = false){
	$notice = array(
		'msg'              => $msg,
		'status'           => $status,
		'strict_wptc_page' => $strict_wptc_page,
		'do_not_delete'    => $do_not_delete,
	);
	WPTC_Factory::get('config')->set_option('admin_notices', serialize($notice));
}

//Add or modify the schedule backup in wptc
function wptc_modify_schedule_backup($dont_reactivate = false) {
	$config = WPTC_Factory::get('config');

	$backup_db_query_limit = $config->get_option('backup_db_query_limit');

	if (!$config->get_option('schedule_time_str')) {
		$config->set_option('schedule_time_str', WPTC_DEFAULT_SCHEDULE_TIME_STR);
	}
	if ($config->get_option('wptc_server_connected') && $config->get_option('wptc_service_request') == 'yes') {
		push_settings_wptc_server("", "", $dont_reactivate, $backup_db_query_limit);
	}
}

function first_backup_basics_wptc() {
	do_action('starting_fresh_new_backup_pre_wptc_h', '');
	do_action('send_basic_analytics', time());

	$eligible_revision_limit = apply_filters('get_eligible_revision_limit_wptc', '');

	if (!empty($eligible_revision_limit)) {
		do_action('set_revision_limit_wptc', $eligible_revision_limit, $cross_check = true);
	}

	$dropbox = WPTC_Factory::get(DEFAULT_REPO);
	WPTC_Base_Factory::get('Wptc_InitialSetup')->record_signed_in_repos($dropbox);
	do_action('send_backups_data_to_server_wptc', time());
}

function wptc_ensure_phpseclib($classes = array(), $class_paths = array()) {

	wptc_no_deprecation_warnings_on_php7();

	if (!empty($classes)) {
		$any_missing = false;
		if (is_string($classes)) $classes = array($classes);
		foreach ($classes as $cl) {
			if (!class_exists($cl)) $any_missing = true;
		}
		if (!$any_missing) return true;
	}

	$ret = true;

	// From phpseclib/phpseclib/phpseclib/bootstrap.php - we nullify it there, but log here instead
	if (extension_loaded('mbstring')) {
		// 2 - MB_OVERLOAD_STRING
		// @codingStandardsIgnoreLine
		if (ini_get('mbstring.func_overload') & 2) {
			// We go on to try anyway, in case the caller wasn't using an affected part of phpseclib
			// @codingStandardsIgnoreLine
			$ret = new WP_Error('mbstring_func_overload', 'Overloading of string functions using mbstring.func_overload is not supported by phpseclib.');
		}
	}

	if (!empty($class_paths)) {
		if (defined('WPTC_BRIDGE')) {
			$phpseclib_dir = dirname(__FILE__) . '/lib/phpseclib';
		} else {
			$phpseclib_dir = WPTC_PLUGIN_DIR . 'lib/phpseclib';
		}

		if (false === stripos( get_include_path(), $phpseclib_dir) ){
			set_include_path( get_include_path() . PATH_SEPARATOR . $phpseclib_dir );
		}

		if (is_string($class_paths)){
			$class_paths = array($class_paths);
		}

		foreach ($class_paths as $cp) {
			wptc_log($phpseclib_dir . '/' . $cp . '.php','-----------$phpseclib_dir ----------------');
			include_once($phpseclib_dir . '/' . $cp . '.php');
		}
	}

	return $ret;
}

function wptc_no_deprecation_warnings_on_php7() {
	// PHP_MAJOR_VERSION is defined in PHP 5.2.7+
	// We don't test for PHP > 7 because the specific deprecated element will be removed in PHP 8 - and so no warning should come anyway (and we shouldn't suppress other stuff until we know we need to).
	// @codingStandardsIgnoreLine
	if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION == 7) {
		$old_level = error_reporting();
		// @codingStandardsIgnoreLine
		$new_level = $old_level & ~E_DEPRECATED;
		if ($old_level != $new_level) error_reporting($new_level);
	}
}

function wptc_is_auto_generated_iam() {
	return WPTC_Factory::get('config')->get_option('is_auto_generated_iam');
}

function is_wptc_filter_registered($filter_name) {
	global $wp_filter;

	if(isset($wp_filter[$filter_name])){

		return true;
	}

	return false;
}

function wptc_get_result_array_s3($result) {
	if(empty($result)){
		return false;
	}

	if(is_array($result)){
		return $result;
	}

	$no_std = iterator_to_array($result);

	return $no_std;
}

function allowed_to_create_triggers_common(){
	$config = WPTC_Factory::get('config');
	
	if ($config->get_option('is_not_eligible_for_partial_backup') ) {
		$config->set_option('tried_to_create_triggers_after_restore', true);
		return false;
	}

	$current_backup_slot = $config->get_option('backup_slot');
	$auto_backup_slots 	 = wptc_get_auto_backup_slots();

	if (empty($auto_backup_slots)) {

		return false;
	}

	//Current slot not in the slot of auto backup
	if(empty( $auto_backup_slots[$current_backup_slot] )){

		return false;
	}

	return $auto_backup_slots[$current_backup_slot]['partial_db_backup'];
}

function admin_wp_loaded_wptc(){

	wptc_define_admin_constants();

	define('WP_BLOG_ADMIN', true);

	require_once ABSPATH.'wp-admin/includes/admin.php';

	do_action('admin_init');

	if (function_exists('wp_clean_update_cache')) {
		/** @handled function */
		wp_clean_update_cache();
	}

	/** @handled function */
	wp_update_plugins();

	/** @handled function */
	set_current_screen();
	do_action('load-update-core.php');

	/** @handled function */
	wp_version_check();

	/** @handled function */
	wp_version_check(array(), true);

	wptc_log(array(),'-----------admin_wp_loaded done----------------');
}

function wptc_define_admin_constants(){

	if (!defined('WP_ADMIN')) {
		define('WP_ADMIN', true);
	}

	if (!defined('WP_NETWORK_ADMIN')) {
		is_multisite() ? define('WP_NETWORK_ADMIN', true) : define('WP_NETWORK_ADMIN', false);
	}
}

function wptc_login_as_admin(){

	wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

	wptc_define_admin_constants();

	if( !function_exists('is_user_logged_in') ){
		include_once( ABSPATH . 'wp-includes/pluggable.php' );
	}

	if(is_user_logged_in()){
		return ;
	}

	$admins = get_users(array('role' => 'administrator'));

	foreach ($admins as $admin) {
		$user = $admin;
		break;
	}

	if (isset($user) && isset($user->ID)) {
		wp_set_current_user($user->ID);
		// Compatibility with All In One Security
		update_user_meta($user->ID, 'last_login_time', current_time('mysql'));
	}

	if(!defined('SECURE_AUTH_COOKIE')){
		
		return;
	}

	$isHTTPS = (bool)is_ssl();

	if($isHTTPS){
		wp_set_auth_cookie($user->ID);
	} else{
		wp_set_auth_cookie($user->ID, false, false);
		wp_set_auth_cookie($user->ID, false, true);
	}
}

function wptc_remove_protocal_from_url($url){
	$url = preg_replace("(^https?://?www.)", "", $url );
	return preg_replace("(^https?://)", "", $url );
}

function wptc_add_protocal_to_url($url, $protocal, $add_www){
	$trimmed_url = wptc_remove_protocal_from_url($url);
	$protocal = $protocal . '://';
	return $add_www ? $protocal . 'www.' . $trimmed_url : $protocal . $trimmed_url ;
}


//Fix for php v5.5 > installed version
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
