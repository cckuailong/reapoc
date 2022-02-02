<?php
if (!is_admin()) {
	die(json_encode(array('error' => 'Not Authorized')));
}

$config = WPTC_Factory::get('config');

if(!$config->get_option('is_user_logged_in')){
	die(json_encode(array('error' => 'Not Authorized')));
}

$processed_files = WPTC_Factory::get('processed-files');

$return_array = array();
$return_array['stored_backups'] = $processed_files->get_stored_backups();
$return_array['backup_progress'] = array();
$return_array['starting_first_backup'] = $config->get_option('starting_first_backup');
$return_array['meta_data_backup_process'] = $config->get_option('meta_data_backup_process');
$return_array['backup_before_update_progress'] = $config->get_option('backup_before_update_progress');
$return_array['is_staging_running'] = apply_filters('is_any_staging_process_going_on', '');

if( is_wptc_filter_registered('is_whitelabling_active_wptc') ){
	$return_array['is_whitelabel_active'] = apply_filters('is_whitelabling_active_wptc', '');
}

if( is_wptc_filter_registered('is_whitelabling_override_wptc') ){
	$return_array['is_whitelabling_override'] = apply_filters('is_whitelabling_override_wptc', '');
}

if( is_wptc_filter_registered('is_staging_tab_allowed_wptc') ){
	$return_array['is_whitelabling_staging_allowed'] = apply_filters('is_staging_tab_allowed_wptc', '');
} else {
	$return_array['is_whitelabling_staging_allowed'] = true;
}

if( is_wptc_filter_registered('is_backup_tab_allowed_wptc') ){
	$return_array['is_backup_tab_allowed_with_admin_user_check'] = apply_filters('is_backup_tab_allowed_wptc', '');
} else {
	$return_array['is_backup_tab_allowed_with_admin_user_check'] = true;
}

if( is_wptc_filter_registered('hide_this_option_wl_wptc') ){
	$return_array['hide_trigger_backup'] = apply_filters('hide_this_option_wl_wptc', 'trigger_backup');
}

$cron_status = $config->get_option('wptc_own_cron_status');
if (!empty($cron_status)) {
	$return_array['wptc_own_cron_status'] = unserialize($cron_status);
	$return_array['wptc_own_cron_status_notified'] = (int) $config->get_option('wptc_own_cron_status_notified');
}

// $start_backups_failed_server = $config->get_option('start_backups_failed_server');
// if (!empty($start_backups_failed_server)) {
// 	$return_array['start_backups_failed_server'] = unserialize($start_backups_failed_server);
// 	$config->set_option('start_backups_failed_server', false);
// }

//get current backup status
$processed_files->get_current_backup_progress($return_array);

$return_array['user_came_from_existing_ver'] = (int) $config->get_option('user_came_from_existing_ver');
$return_array['show_user_php_error'] = $config->get_option('show_user_php_error');
$return_array['bbu_setting_status'] = apply_filters('get_backup_before_update_setting_wptc', '');
$return_array['bbu_note_view'] = apply_filters('get_bbu_note_view', '');
$return_array['admin_notices_wptc'] = get_admin_notices_wptc();

// wptc_log($return_array, "--------return_array--------");

$return_array['is_multisite'] = is_multisite() ? true: false;

$return_array['first_backup_auto_refresh_msec'] = get_first_backup_auto_refresh_msec();

$options_helper = new Wptc_Options_Helper();

$processed_files = WPTC_Factory::get('processed-files');
$last_backup_time = $config->get_option('last_backup_time');
if (!empty($last_backup_time)) {
	$user_time = $config->cnvt_UTC_to_usrTime($last_backup_time);
	$processed_files->modify_schedule_backup_time($user_time);
	$formatted_date = date("M d @ g:i a", $user_time);
	$return_array['last_backup_time'] = $formatted_date;
} else {
	$return_array['last_backup_time']  = 'No Backup Taken';
}

echo '<wptc_head>' . json_encode($return_array) . '</wptc_head>';


function get_first_backup_auto_refresh_msec(){
	if(!wptc_function_exist('posix_uname')){
		return 1000 * 10; //10 secs
	}

	$server = posix_uname();

	if (empty($server) || empty($server['nodename'])) {
		return 1000 * 10; //10 secs
	}

	//If siteground sites then refresh will be 180 secs
	return strstr($server['nodename'], 'siteground') === false ? 1000 * 10 : 1000 * 180;
}
