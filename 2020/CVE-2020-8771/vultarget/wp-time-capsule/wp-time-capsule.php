<?php
/*
Plugin Name: Backup and Staging by WP Time Capsule
Plugin URI: https://wptimecapsule.com
Description: WP Time Capsule is an incremental automated backup plugin that backups up your website to Dropbox, Google Drive and Amazon S3 on a daily basis.
Author: Revmakx
Version: 1.21.15
Author URI: http://www.revmakx.com
Tested up to: 5.2.2
/************************************************************
 * This plugin was modified by Revmakx
 * Copyright (c) 2017 Revmakx
 * www.revmakx.com
 ************************************************************/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	echo 'Howdy! I am not of much use without MotherShip Dashboard.';
	// exit;
}

if (!function_exists('curl_init')) {
	echo 'WP Time Capsule must need curl to get work. Please install CURL in your server';
	// exit;
}

// add_action('init','wptc_init');

wptc_define_constants();

include_once ( ABSPATH . 'wp-admin/includes/file.php' );
include_once ( ABSPATH . 'wp-includes/capabilities.php' );

include_once ( WPTC_PLUGIN_DIR . 'common-functions.php' );
include_once ( WPTC_PLUGIN_DIR . 'Dropbox/Dropbox/API.php' );
include_once ( WPTC_PLUGIN_DIR . 'Dropbox/Dropbox/Exception.php' );
include_once ( WPTC_PLUGIN_DIR . 'Dropbox/Dropbox/OAuth/Consumer/ConsumerAbstract.php' );
include_once ( WPTC_PLUGIN_DIR . 'Dropbox/Dropbox/OAuth/Consumer/Curl.php' );
include_once ( WPTC_PLUGIN_DIR . 'utils/g-wrapper-utils.php' );

include_once ( WPTC_CLASSES_DIR . 'Extension/Base.php' );
include_once ( WPTC_CLASSES_DIR . 'Extension/Manager.php' );
include_once ( WPTC_CLASSES_DIR . 'Extension/DefaultOutput.php' );
include_once ( WPTC_CLASSES_DIR . 'Processed/Base.php' );
include_once ( WPTC_CLASSES_DIR . 'Processed/Files.php' );
include_once ( WPTC_CLASSES_DIR . 'Processed/Restoredfiles.php' );
include_once ( WPTC_CLASSES_DIR . 'Processed/iterator.php' );
include_once ( WPTC_CLASSES_DIR . 'DatabaseBackup.php' );
include_once ( WPTC_CLASSES_DIR . 'FileList.php' );
include_once ( WPTC_CLASSES_DIR . 'DropboxFacade.php' );
include_once ( WPTC_CLASSES_DIR . 'Config.php' );
include_once ( WPTC_CLASSES_DIR . 'BackupController.php' );
include_once ( WPTC_CLASSES_DIR . 'Logger.php' );
include_once ( WPTC_CLASSES_DIR . 'Factory.php' );
include_once ( WPTC_CLASSES_DIR . 'UploadTracker.php' );
include_once ( WPTC_CLASSES_DIR . 'ActivityLog.php' );
include_once ( WPTC_CLASSES_DIR . 'class-file-iterator.php' );
include_once ( WPTC_CLASSES_DIR . 'plugin.compatibility.class.php' );

wptc_process_request();

include_new_files_wptc();

include_primary_files_wptc();

if (is_php_version_compatible_for_g_drive_wptc()) {
	include_once ( WPTC_PLUGIN_DIR . 'Google/autoload.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Google/GoogleWPTCWrapper.php' );

	include_once ( WPTC_CLASSES_DIR . 'GdriveFacade.php' );
}

if (is_php_version_compatible_for_s3_wptc()) {
	include_once ( WPTC_PLUGIN_DIR . 'S3/autoload.php' );
	include_once ( WPTC_PLUGIN_DIR . 'S3/s3WPTCWrapper.php' );

	include_once ( WPTC_CLASSES_DIR . 'S3Facade.php' );
	include_once ( WPTC_CLASSES_DIR . 'WasabiFacade.php' );
}

wptc_load_files();

function wptc_process_request(){
	include_once ( WPTC_PLUGIN_DIR . 'wptc-cron-functions.php' );
	new Wptc_Init();
}

function wptc_define_constants() {
	include_once ( dirname(__FILE__).  DIRECTORY_SEPARATOR  .'wptc-constants.php' );
	$constants = new WPTC_Constants();
	$constants->init_live_plugin();
}

function include_new_files_wptc() {
	$old_files = array('Base.php', 'DefaultOutput.php', '.', '..');

	$cur_files = scandir(WPTC_EXTENSIONS_DIR);
	//wptc_log($cur_files, "--------cur_files--------");
	if (!empty($cur_files) && is_array($cur_files)) {
		foreach ($cur_files as $name => $file) {
			if (!in_array($file, $old_files) && file_exists(WPTC_EXTENSIONS_DIR . $file)) {
				include_once ( WPTC_EXTENSIONS_DIR . $file );
			}
		}
	}
}

function include_php_files_recursive_wptc($folder_name = '') {
	//TBC
	if (empty($folder_name)) {
		return false;
	}
}

function wptc_autoload($className) {
	$fileName = str_replace('_',  '/' , $className) . '.php';
	$temp = $fileName . " - ";
	if (preg_match('/^WPTC/', $fileName)) {
		$fileName = 'Classes' . str_replace('WPTC', '', $fileName);
	} elseif (preg_match('/^Dropbox/', $fileName)) {
		$fileName = 'Dropbox' .  '/'  . $fileName;
	} elseif (preg_match('/^Google/', $fileName)) {
		$fileName = 'Google' .  '/'  . $fileName;
	} elseif (preg_match('/^S3/', $fileName)) {
		$fileName = 'S3' .  '/'  . $fileName;
	} else {
		return false;
	}

	$path = dirname(__FILE__) .  '/'  . $fileName;
	if (file_exists($path)) {
		include_once ( $path );
	}
}

function include_primary_files_wptc() {

	include_once ( WPTC_PLUGIN_DIR . 'Base/Factory.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Base/init.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Base/Hooks.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Base/HooksHandler.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Base/Config.php' );
	include_once ( WPTC_PLUGIN_DIR . 'Base/CurlWrapper.php' );

	include_once ( WPTC_CLASSES_DIR . 'CronServer/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'CronServer/CurlWrapper.php' );

	include_once ( WPTC_CLASSES_DIR . 'WptcBackup/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'WptcBackup/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'WptcBackup/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'WptcBackup/Config.php' );

	include_once ( WPTC_CLASSES_DIR . 'Common/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'Common/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'Common/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'Common/Config.php' );

	include_once ( WPTC_CLASSES_DIR . 'Analytics/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'Analytics/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'Analytics/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'Analytics/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'Analytics/BackupAnalytics.php' );

	include_once ( WPTC_CLASSES_DIR . 'ExcludeOption/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'ExcludeOption/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'ExcludeOption/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'ExcludeOption/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'ExcludeOption/ExcludeOption.php' );

	include_once ( WPTC_CLASSES_DIR . 'Settings/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'Settings/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'Settings/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'Settings/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'Settings/Settings.php' );

	include_once ( WPTC_CLASSES_DIR . 'AppFunctions/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'AppFunctions/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'AppFunctions/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'AppFunctions/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'AppFunctions/AppFunctions.php' );

	include_once ( WPTC_CLASSES_DIR . 'InitialSetup/init.php' );
	include_once ( WPTC_CLASSES_DIR . 'InitialSetup/Hooks.php' );
	include_once ( WPTC_CLASSES_DIR . 'InitialSetup/HooksHandler.php' );
	include_once ( WPTC_CLASSES_DIR . 'InitialSetup/Config.php' );
	include_once ( WPTC_CLASSES_DIR . 'InitialSetup/InitialSetup.php' );

	include_once ( WPTC_CLASSES_DIR . 'Triggers/DeleteTrigger.php' );
	include_once ( WPTC_CLASSES_DIR . 'Triggers/InsertTrigger.php' );
	include_once ( WPTC_CLASSES_DIR . 'Triggers/TriggerCommon.php' );
	include_once ( WPTC_CLASSES_DIR . 'Triggers/TriggerInit.php' );
	include_once ( WPTC_CLASSES_DIR . 'Triggers/UpdateTrigger.php' );

	if(is_wptc_server_req() || is_admin()) {
		WPTC_Base_Factory::get('Wptc_Base')->init();
	}
}

function include_spl_files_wptc() {
	include_once ( WPTC_PRO_DIR . 'ProFactory.php' );
	include_once ( WPTC_PRO_DIR . 'Privileges.php' );
	include_once ( WPTC_PRO_DIR . 'init.php' );
	include_once ( WPTC_PRO_DIR . 'Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'HooksHandler.php' );

	include_once ( WPTC_PRO_DIR . 'AutoBackup/AutoBackup.php' );
	include_once ( WPTC_PRO_DIR . 'AutoBackup/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'AutoBackup/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'AutoBackup/Config.php' );

	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/TraceableUpdaterSkin.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/init.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/Config.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/AutoUpdate.php' );
	include_once ( WPTC_PRO_DIR . 'BackupBeforeUpdate/AutoUpdateSettings.php' );

	include_once ( WPTC_PRO_DIR . 'Staging/init.php' );
	include_once ( WPTC_PRO_DIR . 'Staging/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'Staging/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'Staging/Config.php' );
	include_once ( WPTC_PRO_DIR . 'Staging/class-stage-common.php' );
	include_once ( WPTC_PRO_DIR . 'Staging/class-update-in-staging.php' );

	include_once ( WPTC_PRO_DIR . 'RestoreToStaging/init.php' );
	include_once ( WPTC_PRO_DIR . 'RestoreToStaging/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'RestoreToStaging/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'RestoreToStaging/Config.php' );

	include_once ( WPTC_PRO_DIR . 'RevisionLimit/init.php' );
	include_once ( WPTC_PRO_DIR . 'RevisionLimit/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'RevisionLimit/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'RevisionLimit/Config.php' );

	include_once ( WPTC_PRO_DIR . 'WhiteLabel/init.php' );
	include_once ( WPTC_PRO_DIR . 'WhiteLabel/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'WhiteLabel/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'WhiteLabel/Config.php' );

	include_once ( WPTC_PRO_DIR . 'Vulns/init.php' );
	include_once ( WPTC_PRO_DIR . 'Vulns/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'Vulns/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'Vulns/Config.php' );

	include_once ( WPTC_PRO_DIR . 'Screenshot/init.php' );
	include_once ( WPTC_PRO_DIR . 'Screenshot/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'Screenshot/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'Screenshot/Config.php' );

	include_once ( WPTC_PRO_DIR . 'OnDemandBackup/init.php' );
	include_once ( WPTC_PRO_DIR . 'OnDemandBackup/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'OnDemandBackup/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'OnDemandBackup/Config.php' );

	include_once ( WPTC_PRO_DIR . 'Rollback/init.php' );
	include_once ( WPTC_PRO_DIR . 'Rollback/Hooks.php' );
	include_once ( WPTC_PRO_DIR . 'Rollback/HooksHandler.php' );
	include_once ( WPTC_PRO_DIR . 'Rollback/Config.php' );

	if (class_exists('WPTC_Pro_Factory') && (is_wptc_server_req() || is_admin())) {
		WPTC_Pro_Factory::get('WPTC_Pro')->init();
	}
}

function wptc_load_files(){
	add_action( 'wptc_trigger_truncate_cron_hook', 'wptc_trigger_truncate_cron' );

	if(!is_wptc_server_req() && !is_admin()) {
		return false;
	}

	include_primary_files_wptc();
	include_spl_files_wptc();
	store_bridge_compatibile_values_wptc();
	do_action('just_initialized_wptc_h', '');
	wptc_init_actions();
	wptc_set_fallback_db_search_1_14_0();
	define_default_repo_const_wptc();
	wptc_setlocale();
	initiate_check_and_truncate_trigger_tables_hook();

	add_action('init','initiate_check_and_truncate_trigger_tables_hook');
}

function wptc_style() {
	//Register stylesheet
	wp_register_style('wptc-style', plugins_url('wp-time-capsule.css', __FILE__));
	wp_enqueue_style('wptc-style', false, array(), WPTC_VERSION);
	wp_enqueue_style('dashicons', false, array(), WPTC_VERSION);
}

function define_default_repo_const_wptc(){
	$config = WPTC_Factory::get('config');

	// support for wasabi endpoints : start

	if( !empty($_POST) && !empty($_POST['action']) && $_POST['action'] == 'get_s3_authorize_url_wptc' ){
		$config->set_option('default_repo', 's3');
	}

	if( !empty($_POST) && !empty($_POST['action']) && $_POST['action'] == 'get_wasabi_authorize_url_wptc' ){
		$config->set_option('default_repo', 'wasabi');
	}

	// support for wasabi endpoints : end

	if (!defined('DEFAULT_REPO')) {
		define('DEFAULT_REPO', $config->get_option('default_repo'));
	}

	$repo_labels_arr = array(
		'g_drive' => 'Google Drive',
		's3' => 'Amazon S3',
		'dropbox' => 'Dropbox',
		'wasabi' => 'Wasabi',
	);

	if (defined('DEFAULT_REPO')) {
		$this_repo = DEFAULT_REPO;
	}

	if (!empty($this_repo) && !empty($repo_labels_arr[$this_repo])) {
		$supposed_repo_label = $repo_labels_arr[$this_repo];
	} else {
		$supposed_repo_label = 'Cloud';
	}

	if (!defined('DEFAULT_REPO_LABEL')) {
		define('DEFAULT_REPO_LABEL', $supposed_repo_label);
	}
}


/**
 * A wrapper function that adds an options page to setup Dropbox Backup.
 * @return void
 */
function wordpress_time_capsule_admin_menu($menus = array()) {

	if( is_wptc_filter_registered('is_whitelabling_active_wptc') 
		&& apply_filters('is_whitelabling_active_wptc', false) 
		&& !apply_filters('is_whitelabling_override_wptc', false) ){

		return do_action('add_pages_wl_wptc', time());
	}

	wptc_log(array(),'-----------athorized 4----------------');

	if (empty($menus)) {
		return ;
	}

	if ($menus['main']) {
		$text = __('WP Time Capsule', 'wptc');
		add_menu_page($text, $text, 'activate_plugins', 'wp-time-capsule-monitor', 'wordpress_time_capsule_monitor', 'dashicons-wptc', '80.0564');
	}

	if ($menus['backups']) {
		$text = __('Backups', 'wptc');
		add_submenu_page('wp-time-capsule-monitor', $text, $text, 'activate_plugins', 'wp-time-capsule-monitor', 'wordpress_time_capsule_monitor');
	}

	if ($menus['sub_menus']) {
		do_action('add_additional_sub_menus_wptc_h', '');
	}

	if ($menus['activity_log']) {
		$text = __('Activity Log', 'wptc');
		add_submenu_page('wp-time-capsule-monitor', $text, $text, 'activate_plugins', 'wp-time-capsule-activity', 'wordpress_time_capsule_activity');
	}

	if ($menus['settings']) {
		$text = __('Settings', 'wptc');
		add_submenu_page('wp-time-capsule-monitor', $text, $text, 'activate_plugins', 'wp-time-capsule-settings', 'wptimecapsule_settings_hook');
	}

	if ($menus['initial_setup']) {
		$text = __('Initial Setup', 'wptc');
		add_submenu_page(null, $text, $text, 'activate_plugins', 'wp-time-capsule', 'wordpress_time_capsule_admin_menu_contents');
	}


	if ($menus['dev_option']) {
		if (WPTC_ENV != 'production' || WPTC_DEBUG) {
			$text = __('Dev Options', 'wptc');
			add_submenu_page('wp-time-capsule-monitor', $text, $text, 'activate_plugins', 'wp-time-capsule-dev-options', 'wordpress_time_capsule_dev_options');
		}
	}

	download_recent_decrypted_file_wptc();
}

//Activity log page
function wordpress_time_capsule_activity() {
	include_once ( WPTC_PLUGIN_DIR.'Views/wptc-activity.php' );
}

//Settings page
function wptimecapsule_settings_hook(){

	if(!WPTC_Base_Factory::get('Wptc_App_Functions')->can_show_this_page()){
		return ;
	}

	if(!WPTC_Base_Factory::get('Wptc_App_Functions')->is_cloud_authorized()){
		return wordpress_time_capsule_admin_menu_contents();
	}

	include_once ( WPTC_PLUGIN_DIR . 'Views/wptc-plans.php' );
	include_once ( WPTC_PLUGIN_DIR.'Views/wptc-settings.php' );
}

//Dev options page
function wordpress_time_capsule_dev_options() {
	include_once ( WPTC_PLUGIN_DIR . 'Views/wptc-dev-options.php' );
}

//Staging page
function wordpress_time_capsule_staging_options() {

	if(!WPTC_Base_Factory::get('Wptc_App_Functions')->can_show_this_page()){
		return ;
	}

	include_once ( WPTC_PLUGIN_DIR . 'Pro/Staging/Views/wptc-staging-options.php' );
}

//Initial setup page
function wordpress_time_capsule_admin_menu_contents() {
	include_once( WPTC_PLUGIN_DIR . 'Views/wptc-options.php' );
}

//Monitor page
function wordpress_time_capsule_monitor() {
	if(!WPTC_Base_Factory::get('Wptc_App_Functions')->can_show_this_page()){
		return ;
	}

	if(!WPTC_Base_Factory::get('Wptc_App_Functions')->is_cloud_authorized()){
		return wordpress_time_capsule_admin_menu_contents();
	}

	$uri = rtrim(plugins_url('wp-time-capsule'), '/');
	include_once ( WPTC_PLUGIN_DIR.'Views/wptc-monitor.php' );
}

/**
 * A wrapper function for the progress AJAX request
 * @return void
 */
function tc_backup_progress_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	include_once ( WPTC_PLUGIN_DIR . 'Views/wptc-options-helper.php' );
	include_once ( WPTC_PLUGIN_DIR.'Views/wptc-progress.php' );
	die();
}

/**
 * A wrapper function for the progress AJAX request
 * @return void
 */
function get_this_day_backups_callback_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	//note that we are getting the ajax function data via $_POST.
	$backupIds = $_POST['data'];

	//getting the backups
	$processed_files = WPTC_Factory::get('processed-files');
	echo $processed_files->get_this_backups_html($backupIds);
}

function get_sibling_files_callback_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	//note that we are getting the ajax function data via $_POST.
	$file_name = $_POST['data']['file_name'];
	$file_name = wp_normalize_path($file_name);
	$backup_id = $_POST['data']['backup_id'];
	$recursive_count = $_POST['data']['recursive_count'];
	// //getting the backups
	$processed_files = WPTC_Factory::get('processed-files');
	echo $processed_files->get_this_backups_html($backup_id, $file_name, $type = 'sibling', (int) $recursive_count);
}

function get_in_progress_tcbackup_callback_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$in_progress_status = WPTC_Factory::get('config')->get_option('in_progress');
	echo $in_progress_status;
}

function start_backup_tc_callback_wptc($type = '') {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	//for backup during update
	$backup = new WPTC_BackupController();
	$backup->backup_now();

	// store_name_for_this_backup_callback_wptc("Updated on " . date('H-i', time()));
}

function start_fresh_backup_tc_callback_wptc($type = '', $args = null, $test_connection = true, $ajax_check = true, $is_iwp = false) {
	wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
	wptc_manual_debug('', 'start_backup');

	if ($ajax_check) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	}

	do_action('refresh_realtime_tmp_wptc', true);

	if (!apply_filters('is_realtime_partial_db_backup_wptc', '')) {
		WPTC_Base_Factory::get('Trigger_Init')->truncate_table();
	}

	wptc_reset_restore_if_long_time_no_ping();

	$config = WPTC_Factory::get('config');

	if($test_connection) {
		$result = is_wptc_cron_fine();

		wptc_log($result,'-----------is_wptc_cron_fine----------------');

		if($result == false){
			$config->set_option('in_progress', false);
			$config->set_option('wptc_main_cycle_running', false);

			wptc_log(array(),'-----------Cron not connected so backup aborted----------------');

			send_response_wptc('declined_by_wptc_cron_not_connected', 'SCHEDULE');
		}
	}

	if ($config->get_option('in_progress', true)) {
		if($is_iwp){
			
			return;
		}

		set_server_req_wptc(true);
		$config->set_option('recent_backup_ping', time());
		if ($type == 'daily_cycle') {
			send_response_wptc('already_daily_cycle_running', 'SCHEDULE');
		}
		wptc_set_backup_in_progress_server(true);
		send_response_wptc('already_backup_running_and_retried', $type);
	}

	wptc_log(array(), '-----------Backup set and ready for request from server-------------');

	$config->set_option('in_progress', true);
	$config->set_option('single_upgrade_details', false);

	if (empty($args)) {
		$args = $_POST;
	}

	do_action('just_initialized_fresh_backup_wptc_h', $args);

	$config->create_dump_dir(); //This will initialize wp_filesystem

	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'manual' || $type == 'manual') {
		$config->set_option('wptc_current_backup_type', 'M');
	}

	global $wpdb;
	$wpdb->query("TRUNCATE TABLE `" . $wpdb->base_prefix . "wptc_current_process`");

	$backup = new WPTC_BackupController();
	$backup->pre_check();
	//$config->remove_garbage_files();
	$backup->backup_now($type, $ajax_check);
}

function stop_restore_tc_callback_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$backup = new WPTC_BackupController();
	$backup->stop('restore');

	add_settings_error('wptc_monitor', 'restore_stopped', __('Restore stopped.', 'wptc'), 'updated');
}

function stop_fresh_backup_tc_callback_wptc($deactivated_plugin = null, $ajax_verify = true) {

	if($ajax_verify){
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	}

	do_action('send_report_data_wptc', wptc_get_cookie('backupID'), 'BACKUP', 'CANCELLED');

	//for backup during update
	$backup = new WPTC_BackupController();
	$backup->stop($deactivated_plugin);
	add_settings_error('wptc_monitor', 'backup_stopped', __('Backup stopped.', 'wptc'), 'updated');
}

function store_name_for_this_backup_callback_wptc($backup_name = null, $check_ajax = true) {

	if ($check_ajax) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	}

	if (empty($backup_name)) {
		$backup_name = $_POST['data'];
	}

	return store_backup_name_wptc(array('backup_name' => $backup_name));
}

function send_restore_initiated_email_wptc($dev_option = null) {
	$config = WPTC_Factory::get('config');

	$email = $config->get_option('main_account_email');
	if (empty($dev_option)) {
		$current_bridge_file_name = $config->get_option('current_bridge_file_name');
		$resume_restore_link = site_url() . "/" . $current_bridge_file_name . "/index.php?continue=true"; //the link to the bridge init file
	} else {
		$resume_restore_link = site_url() . "/wp-tcapsule-bridge-dev-test/index.php?continue=true";
	}

	$errors = array(
		'type' => 'restore_started',
		'resume_restore_link' => $resume_restore_link,
	);

	error_alert_wptc_server($errors);
}

function start_restore_tc_callback_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	if (apply_filters('is_restore_to_staging_wptc', '')) {
		$request = apply_filters('get_restore_to_staging_request_wptc', '');
	} else {
		$request = $_POST['data'];
	}

	$config = WPTC_Factory::get('config');
	$old_wasabi_us_east = $config->get_option('wasabi_bucket_region');
	if($old_wasabi_us_east == 'us-east-1'){
		$config->set_option('wasabi_bucket_region', 'us-east-2');
	}

	include_once ( WPTC_CLASSES_DIR . 'class-prepare-restore-bridge.php' );

	new WPTC_Prepare_Restore_Bridge($request);
}

function store_backup_name_wptc($backup_meta) {
	wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

	if (empty($backup_meta['backup_id'])) {
		$backup_meta['backup_id'] = wptc_get_cookie('backupID');
		if (empty($backup_meta['backup_id'])) {
			return false;
		}
	}

	global $wpdb;

	$sql = "SELECT count(*)
			FROM {$wpdb->base_prefix}wptc_backups WHERE backup_id = " . $backup_meta['backup_id']; //manual"

	$get_row = $wpdb->get_var($sql);

	wptc_log($get_row,'-----------$get_row----------------');

	if (empty($get_row)) {
		$result = $wpdb->insert("{$wpdb->base_prefix}wptc_backups", $backup_meta);
	} else {
		$result = $wpdb->update("{$wpdb->base_prefix}wptc_backups", $backup_meta , array('backup_id' => $backup_meta['backup_id']));
	}

	return ($result) ? true : false;
}

function is_wptc_cron_fine(){
	$config = WPTC_Factory::get('config');
	wptc_own_cron_status();
	$cron_status = $config->get_option('wptc_own_cron_status');
	if (!empty($cron_status)) {
		$cron_status = unserialize($cron_status);
		if ($cron_status['status'] == 'success') {
			return true;
		}
		return false;
	}
}

function execute_tcdropbox_backup_wptc($type = '') {

	$config = WPTC_Factory::get('config');
	wptc_set_backup_in_progress_server(true);

	$backup_id = wptc_get_cookie('backupID');
	$config->set_option('backup_action_id', $backup_id);

	$this_repo = $config->get_option('default_repo');

	WPTC_Factory::get('logger')->delete_log();
	WPTC_Factory::get('logger')->log(sprintf(__('Backup started on %s.', 'wptc'), date("l F j, Y", strtotime(current_time('mysql')))), 'backups', $backup_id);
	WPTC_Factory::get('logger')->log(sprintf(__('Connected Repo is %s.', 'wptc'), $this_repo), 'backups', $backup_id);
	$time = ini_get('max_execution_time');
	WPTC_Factory::get('logger')->log(sprintf(
		__('Your time limit is %s and your memory limit is %s'),
		$time ? $time . ' ' . __('seconds', 'wptc') : __('unlimited', 'wptc'),
		ini_get('memory_limit')
	), 'backups', $backup_id);
	if (ini_get('safe_mode')) {
		WPTC_Factory::get('logger')->log(__("Safe mode is enabled on your server so the PHP time and memory limit cannot be set by the backup process. So if your backup fails it's highly probable that these settings are too low.", 'wptc'), 'backups', $backup_id);
	}
	wptc_log(array(), '-----------in progress set again-------------');
	$config->set_option('in_progress', true);
	$config->set_option('mail_backup_errors', 0);
	$config->set_option('frequently_changed_files', false);

	$config->set_option('starting_backup_first_call_time', time());
}

function monitor_tcdropbox_backup_wptc($args = 0) {
	$config = WPTC_Factory::get('config');

	WPTC_Base_Factory::get('Wptc_Base')->init();

	wptc_log(array(), "----- monitor_tcdropbox_backup_wptc called--------");
	do_action('inside_monitor_backup_pre_wptc_h', '');

	if ($config->get_option('in_progress')) {
		$config->set_option('recent_backup_ping', time());

		wptc_log(date("g:i:s a l F j, Y"), "--------monitor bakcup is accepted --------");

		wptc_manual_debug('', 'continue_backup');
		$config->set_option('is_running', false);
		WPTC_Base_Factory::get('Wptc_App_Functions')->update_prev_backups();
		run_tc_backup_wptc();
		send_response_wptc('Backup completed.', 'SCHEDULE');
	} else {
		wptc_log(array(), "----- monitor_tcdropbox_backup_wptc rejected--------");
		wptc_set_backup_in_progress_server(false);
		reset_backup_related_settings_wptc();
		return 'declined';
	}
}



function run_tc_backup_wptc($type = '') {
	$options = WPTC_Factory::get('config');

	if (is_any_ongoing_wptc_restore_process()) {
		wptc_log(array(), "--------is_any_ongoing_wptc_restore_process--------");
		$options->set_option('recent_restore_ping', time());
		send_response_wptc('declined_by_running_restore', 'SCHEDULE');
		return false;
	}

	if (!$options->get_option('is_running')) {
		$options->create_dump_dir();
		$options->set_option('is_running', true);
		$contents = @unserialize($options->get_option('this_cookie'));
		$backup_id = $contents['backupID'];

		if (!empty($backup_id)) {
			WPTC_Factory::get('logger')->log(__('Resuming backup.', 'wptc'), 'backups', $backup_id);
		}

		$backup = new WPTC_BackupController();
		$backup->execute($type);
	}
}

function backup_tc_cron_schedules($schedules) {
	$new_schedules = array(
		'every_min' => array(
			'interval' => 60,
			'display' => 'WPTC - Every one minute',
		),
		'every_two_min' => array(
			'interval' => 120,
			'display' => 'WPTC - Every two minutes',
		),
		'every_ten' => array(
			'interval' => 600,
			'display' => 'WPTC - Every ten minutes',
		),
		'every_twenty' => array(
			'interval' => 1200,
			'display' => 'WPTC - Every twenty minutes',
		),
		'half_hour' => array(
			'interval' => 1800,
			'display' => 'WPTC - Every half hour',
		),
		'every_hour' => array(
			'interval' => 3600,
			'display' => 'WPTC - Every Hour',
		),
		'every_four' => array(
			'interval' => 14400,
			'display' => 'WPTC - Every Four Hours',
		),
		'every_six' => array(
			'interval' => 21600,
			'display' => 'WPTC - Every Six Hours',
		),
		'every_eight' => array(
			'interval' => 28800,
			'display' => 'WPTC - Every Eight Hours',
		),
		'daily' => array(
			'interval' => 86400,
			'display' => 'WPTC - Daily',
		),
		'weekly' => array(
			'interval' => 604800,
			'display' => 'WPTC - Weekly',
		),
		'fortnightly' => array(
			'interval' => 1209600,
			'display' => 'WPTC - Fortnightly',
		),
		'monthly' => array(
			'interval' => 2419200,
			'display' => 'WPTC - Once Every 4 weeks',
		),
		'two_monthly' => array(
			'interval' => 4838400,
			'display' => 'WPTC - Once Every 8 weeks',
		),
		'three_monthly' => array(
			'interval' => 7257600,
			'display' => 'WPTC - Once Every 12 weeks',
		),
	);

	return array_merge($schedules, $new_schedules);
}

function wptc_install() {

	include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

	global $wpdb;
	$wpdb 				= WPTC_Factory::db();
	$cachecollation 	= wptc_get_collation();
	$is_wptc_installed 	= WPTC_Base_Factory::get('Wptc_App_Functions')->is_wptc_installed();

	$table_name = $wpdb->base_prefix . 'wptc_options';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		name varchar(50) NOT NULL,
		value text NOT NULL,
		UNIQUE KEY name (name)
	) " . $cachecollation . " ;");

	$table_name = $wpdb->base_prefix . 'wptc_current_process';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`file_path` text NOT NULL,
		`status` char(1) NOT NULL DEFAULT 'Q' COMMENT 'P=Processed, Q= In Queue, S- Skipped',
		`processed_time` varchar(30) NOT NULL,
		`file_hash` varchar(128) DEFAULT NULL,
		PRIMARY KEY (`id`),
		INDEX `file_path` (`file_path`(191))
		) ENGINE=InnoDB " . $cachecollation . ";"
	);

	$table_name = $wpdb->base_prefix . 'wptc_processed_files';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
	  `file` text DEFAULT NULL,
	  `offset` int(50) NULL DEFAULT '0',
	  `uploadid` text DEFAULT NULL,
	  `file_id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `backupID` double DEFAULT NULL,
	  `revision_number` text DEFAULT NULL,
	  `revision_id` text DEFAULT NULL,
	  `mtime_during_upload` varchar(22) DEFAULT NULL,
	  `uploaded_file_size` bigint(20) DEFAULT NULL,
	  `g_file_id` text DEFAULT NULL,
	  `s3_part_number` int(10) DEFAULT NULL,
	  `s3_parts_array` longtext DEFAULT NULL,
	  `cloud_type` varchar(50) DEFAULT NULL,
	  `parent_dir` TEXT DEFAULT NULL,
	  `is_dir` INT(1) DEFAULT NULL,
	  `file_hash` varchar(128) DEFAULT NULL,
	  `life_span` double DEFAULT NULL,
	  `filepath_md5` varchar(32) NULL,
	  PRIMARY KEY (`file_id`),
	  INDEX `uploaded_file_size` (`uploaded_file_size`),
	  INDEX `backupID` (`backupID`),
	  INDEX `filepath_md5` (`filepath_md5`),
	  INDEX `file` (`file`(191))
	) ENGINE=InnoDB  " . $cachecollation . ";");

	$table_name = $wpdb->base_prefix . 'wptc_processed_iterator';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` longtext NOT NULL,
		`offset` text DEFAULT NULL,
		PRIMARY KEY (`id`)
	) " . $cachecollation . " ;");

	$table_name = $wpdb->base_prefix . 'wptc_processed_restored_files';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
	  `file` text NOT NULL,
	  `offset` int(50) DEFAULT '0',
	  `uploadid` text DEFAULT NULL,
	  `file_id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `backupID` double DEFAULT NULL,
	  `revision_number` text DEFAULT NULL,
	  `revision_id` text DEFAULT NULL,
	  `mtime_during_upload` varchar(22) DEFAULT NULL,
	  `download_status` text DEFAULT NULL,
	  `uploaded_file_size` text DEFAULT NULL,
	  `process_type` text DEFAULT NULL,
	  `copy_status` text DEFAULT NULL,
	  `g_file_id` text DEFAULT NULL,
	  `file_hash` varchar(128) DEFAULT NULL,
	  `is_future_file` int(1) DEFAULT '0',
	  PRIMARY KEY (`file_id`),
	  INDEX `file` (`file`(191))
	) ENGINE=InnoDB  " . $cachecollation . ";");

	$table_name = $wpdb->base_prefix . 'wptc_inc_exc_contents';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
			`id` int NOT NULL AUTO_INCREMENT,
			`key` text NOT NULL,
			`type` varchar(20) NOT NULL,
			`category` varchar(30) NOT NULL,
			`action` varchar(30) NOT NULL,
			`table_structure_only` int(1) NULL,
			`is_dir` int(1) NULL,
			PRIMARY KEY (`id`),
			INDEX `key` (`key`(191))
		) ENGINE=InnoDB " . $cachecollation . ";");

	$table_name = $wpdb->base_prefix . 'wptc_activity_log';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`type` varchar(50) NOT NULL,
			`log_data` text NOT NULL,
			`parent` tinyint(1) NOT NULL DEFAULT '0',
			`parent_id` bigint(20) NOT NULL,
			`is_reported` tinyint(1) NOT NULL DEFAULT '0',
			`report_id` varchar(50) NOT NULL,
			`action_id` text NOT NULL,
			`show_user` ENUM('1','0') NOT NULL DEFAULT '1',
			PRIMARY KEY (`id`),
			UNIQUE KEY `id` (`id`),
			INDEX `action_id` (`action_id`(191)),
			INDEX `show_user` (`show_user`)
		  ) ENGINE=InnoDB  " . $cachecollation . ";");

	$table_name = $wpdb->base_prefix . 'wptc_backups';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`backup_id` varchar(100) NOT NULL,
			`backup_type` char(1) NOT NULL COMMENT 'M = Manual, D = Daily Main Cycle , S- Sub Cycle',
			`files_count` int(11) NOT NULL,
			`memory_usage` text NOT NULL,
			`update_details` text DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `id` (`id`)
		  ) ENGINE=InnoDB  " . $cachecollation . ";");

	//Ensure that there where no insert errors
	$errors = array();

	global $EZSQL_ERROR;
	if ($EZSQL_ERROR) {
		foreach ($EZSQL_ERROR as $error) {
			if (preg_match("/^CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}wptc_/", $error['query'])) {
				$errors[] = $error['error_str'];
			}
		}
	}


	//Only set the DB version if there are no errors
	if (!empty($errors)) {
		wptc_log($errors,'-----------$errors----------------');
		delete_option('wptc-init-errors');
		add_option('wptc-init-errors', implode($errors, '<br />'), false, 'no');
		return ;
	}

	//Only should execute on first time activation
	if (!$is_wptc_installed) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->set_fresh_install_flags();
	}

	WPTC_Base_Factory::get('Wptc_Settings')->auto_whitelist_ips();
	wptc_activation();
	wptc_log(array(), "--------installing finished--------");
}

function check_wptc_update() {

	// wptc_log('', "--------check_wptc_update--------");

	define_default_repo_const_wptc();

	$config = WPTC_Factory::get('config');

	$installed_db_version = $config->get_option('database_version');

	if (empty($installed_db_version)) {
		//Missing database_version means this is fresh install
		$installed_db_version = WPTC_DATABASE_VERSION;
	}

	if ($installed_db_version && (version_compare('3.0', $installed_db_version) > 0)) {
		// wptc_upgrade_db();
		set_default_repo_for_previous_update_wptc($config);
		$config->set_option('first_backup_started_atleast_once', true);
		process_wptc_logout();
		$config->set_option('database_version', '3.0');
	}
	if ($installed_db_version && (version_compare('4.0', $installed_db_version) > 0)) {
		create_auto_backup_db_wptc();
		$config->set_option('database_version', '4.0');
	}

	if ($installed_db_version && (version_compare('5.0', $installed_db_version) > 0)) {
		wptc_database_changes_5_0();
		$config->set_option('database_version', '5.0');
		$config->set_option('user_came_from_existing_ver', 1);
	}

	if ($installed_db_version && (version_compare('6.0', $installed_db_version) > 0)) {
		wptc_database_changes_6_0();
		$config->set_option('database_version', '6.0');
	}
	if ($installed_db_version && (version_compare('7.0', $installed_db_version) > 0)) {
		wptc_database_changes_7_0();
		$config->set_option('database_version', '7.0');
	}

	if ($installed_db_version && (version_compare('8.0', $installed_db_version) > 0)) {
		wptc_database_changes_8_0();
		$config->set_option('database_version', '8.0');
	}

	if ($installed_db_version && (version_compare('9.0', $installed_db_version) > 0)) {
		wptc_database_changes_9_0();
		$config->set_option('database_version', '9.0');
	}

	if ($installed_db_version && (version_compare('10.0', $installed_db_version) > 0)) {
		wptc_database_changes_10_0();
		$config->set_option('database_version', '10.0');
	}

	if ($installed_db_version && (version_compare('11.0', $installed_db_version) > 0)) {
		wptc_database_changes_11_0();
		$config->set_option('database_version', '11.0');
	}

	if ($installed_db_version && (version_compare('12.0', $installed_db_version) > 0)) {
		wptc_database_changes_12_0();
		$config->set_option('database_version', '12.0');
	}

	if ($installed_db_version && (version_compare('13.0', $installed_db_version) > 0)) {
		wptc_database_changes_13_0();
		$config->set_option('database_version', '13.0');
	}

	if ($installed_db_version && (version_compare('14.0', $installed_db_version) > 0)) {
		wptc_database_changes_14_0();
		$config->set_option('database_version', '14.0');
	}

	if ($installed_db_version && (version_compare('15.0', $installed_db_version) > 0)) {
		wptc_database_changes_15_0();
		$config->set_option('database_version', '15.0');
	}

	if ($installed_db_version && (version_compare('16.0', $installed_db_version) > 0)) {
		wptc_database_changes_16_0();
		$config->set_option('database_version', '16.0');
	}

	if ($installed_db_version && (version_compare('17.0', $installed_db_version) > 0)) {
		wptc_database_changes_17_0();
		$config->set_option('database_version', '17.0');
	}

	$installed_wptc_version = $config->get_option('wptc_version');

	if (empty($installed_wptc_version)) {
		//Missing wptc_version is unusual so setting it to one of the major version
		$config->set_option('wptc_version', WPTC_VERSION);
		$installed_wptc_version = WPTC_VERSION;
	}

	if (version_compare('1.0.0beta4.2', $installed_wptc_version) > 0) {
		process_wptc_logout();
	}

	if (version_compare('1.0.0RC1', $installed_wptc_version) > 0) {
		clear_gdrive_backup_data_wptc();
		$table_refactoring = $config->get_option('wptc_update_progress');
		if (empty($table_refactoring)) {
			$config->set_option('wptc_update_progress', 'start');
		}
	}

	if (version_compare('1.0.0', $installed_wptc_version) > 0) {
		$config->set_option('activity_log_lazy_load_limit', WPTC_ACTIVITY_LOG_LAZY_LOAD_LIMIT);
		signup_wptc_server_wptc();
		wptc_own_cron_status();
	}

	if (version_compare('1.1.1', $installed_wptc_version) > 0) {
		signup_wptc_server_wptc();
		wptc_own_cron_status();
	}

	if (version_compare('1.1.2', $installed_wptc_version) > 0) {
		$backup = new WPTC_BackupController();
		$backup->clear_current_backup();
	}

	if (version_compare('1.2.0', $installed_wptc_version) > 0) {
		reset_restore_related_settings_wptc();
	}

	if (version_compare('1.3.0', $installed_wptc_version) > 0) {
		$config->set_option('got_exclude_files', true);
		$config->set_option('user_came_from_existing_ver', 1);
		do_action("wptc_got_exclude_files", time());
	}

	if (version_compare('1.3.1', $installed_wptc_version) > 0) {
		$config->delete_option('user_excluded_files_and_folders');
	}

	if (version_compare('1.4.0', $installed_wptc_version) > 0) {
		if (is_any_ongoing_wptc_backup_process()) {
			$config->set_option('recent_backup_ping', time());
		}
		$config->request_service(
			array(
				'email'           => false,
				'pwd'             => false,
				'return_response' => false,
				'sub_action' 	  => false,
				'login_request'   => true,
			)
		);
	}

	if (version_compare('1.4.3', $installed_wptc_version) > 0) {
		$config->set_option('insert_default_excluded_files', false);
	}

	if (version_compare('1.4.4', $installed_wptc_version) > 0) {
		do_action('reset_stats', time());
	}


	if (version_compare('1.4.6', $installed_wptc_version) > 0) {
		// WPTC_Base_Factory::get('Wptc_ExcludeOption')->get_store_file_and_db_size();
	}

	if (version_compare('1.5.3', $installed_wptc_version) > 0) {
		$config->set_option('update_default_excluded_files', false);
	}

	if (version_compare('1.6.0', $installed_wptc_version) > 0) {
		$config->delete_option('backup_db_path');
		$config->set_option('user_came_from_existing_ver', true);
	}

	if (version_compare('1.7.2', $installed_wptc_version) > 0) {
		$config->set_option('update_prev_backups_1', true);
	}

	if (version_compare('1.8.0', $installed_wptc_version) > 0) {
		$config->set_option('existing_users_rev_limit_hold', time());
		$config->set_option('revision_limit', WPTC_FALLBACK_REVISION_LIMIT_DAYS);
		$config->set_option('run_init_setup_bbu', true);
	}

	if (version_compare('1.8.4', $installed_wptc_version) > 0) {
		$config->set_option('internal_staging_db_rows_copy_limit', 1000);
		$config->set_option('internal_staging_file_copy_limit', 1000);
	}

	if (version_compare('1.8.5', $installed_wptc_version) > 0) {
		wptc_clear_inc_exc_tables();
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_files_n_tables();
	}

	if (version_compare('1.9.0', $installed_wptc_version) > 0) {
		$config->set_option('internal_staging_file_copy_limit', 500);
		$config->set_option('run_staging_updates', '1.9.0');
		windows_machine_reset_backups_wptc();
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_files_n_tables();
	}

	if (version_compare('1.9.1', $installed_wptc_version) > 0) {
		wptc_log($installed_wptc_version, '--------$currnet installed version--------');
		$staging_type = $config->get_option('staging_type');
		wptc_log($staging_type, '--------$staging_type--------');
		if (empty($staging_type) && $installed_wptc_version == '1.9.0' && WPTC_Base_Factory::get('Wptc_App_Functions')->is_user_purchased_this_class('Wptc_Staging')) {
			update_option('blog_public', 1);
		}
	}

	if (version_compare('1.9.3', $installed_wptc_version) > 0) {
		$config->delete_option('update_prev_backups_1');
		$config->delete_option('update_prev_backups_1_pointer');
	}

	if (version_compare('1.9.4', $installed_wptc_version) > 0) {
		$config->delete_option('gotfileslist_multicall_count');
		if($config->get_option('default_repo') === 'dropbox'){
			$dropbox = WPTC_Factory::get('dropbox');
			$dropbox->migrate_to_v2();
			$config->set_option('dropbox_oauth_upgraded', true);
		}
		$config->set_option('update_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_excluded_files();
	}

	if (version_compare('1.10.0', $installed_wptc_version) > 0) {
		do_action('send_ptc_list_to_server_wptc', time());
		do_action('turn_off_themes_auto_updates_wptc', time());
	}

	if (version_compare('1.10.2', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->validate_dropbox_upgrade();
	}

	if (version_compare('1.11.0', $installed_wptc_version) > 0) {
		delete_option('wptc_installed');
		$config->set_option('update_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_excluded_files();
	}

	if (version_compare('1.11.1', $installed_wptc_version) > 0) {
		$config->set_option('update_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_excluded_files();
	}

	if (version_compare('1.12.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->update_default_vulns_settings();
		$config->delete_option('is_autoupdate_vulns_settings_enabled');
		$config->set_option('internal_staging_deep_link_limit', WPTC_STAGING_DEFAULT_DEEP_LINK_REPLACE_LIMIT);
		$config->set_option('internal_staging_enable_admin_login', true);
		$config->delete_option('file_list_point_restore');
		$config->delete_option('restore_child_pointer');
		$config->delete_option('restore_parent_pointer');
	}

	if (version_compare('1.12.2', $installed_wptc_version) > 0) {
		if(is_any_ongoing_wptc_backup_process()){
			stop_fresh_backup_tc_callback_wptc(null, false);
			start_fresh_backup_tc_callback_wptc('manual', null, true, false);
		}
	}

	if (version_compare('1.12.3', $installed_wptc_version) > 0) {
		if(is_any_ongoing_wptc_backup_process()){
			stop_fresh_backup_tc_callback_wptc(null, false);
			start_fresh_backup_tc_callback_wptc('manual', null, true, false);
		}
	}

	if (version_compare('1.13.0', $installed_wptc_version) > 0) {
		$config->set_option('backup_slot', 'daily');
		$config->delete_option('auto_backup_interval');
		$config->delete_option('auto_backup_switch');
		$config->delete_option('schedule_day');
	}

	if (version_compare('1.14.0', $installed_wptc_version) > 0) {
		//Renaming new staging key
		WPTC_Base_Factory::get('Wptc_App_Functions')->update_staging_enable_admin_key();

		//Updating exclude list full paths to relative paths
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_1_14_0();

		//Update new logs files in exlcude system
		$config->set_option('update_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_excluded_files();

		//Refresh login
		$config->request_service(
			array(
				'email'           => false,
				'pwd'             => false,
				'return_response' => false,
				'sub_action' 	  => 'sync_all_settings_to_node',
				'login_request'   => true,
			)
		);

		//Restart ongoing backup
		if(is_any_ongoing_wptc_backup_process()){
			stop_fresh_backup_tc_callback_wptc(null, false);
			start_fresh_backup_tc_callback_wptc('manual', null, true, false);
		}
	}

	if (version_compare('1.14.1', $installed_wptc_version) > 0) {
		//Restart ongoing backup
		if(is_any_ongoing_wptc_backup_process()){
			stop_fresh_backup_tc_callback_wptc(null, false);
			start_fresh_backup_tc_callback_wptc('manual', null, true, false);
		}
	}

	if (version_compare('1.14.3', $installed_wptc_version) > 0) {

	}

	if (version_compare('1.15.0', $installed_wptc_version) > 0) {
		//refresh temp locations
		WPTC_Base_Factory::get('Wptc_App_Functions')->refresh_cached_paths();
	}

	if (version_compare('1.15.1', $installed_wptc_version) > 0) {
		if($config->get_option('backup_before_update_setting') !== 'always'){
			$config->set_option('backup_before_update_setting', 'everytime');
		}
	}

	if (version_compare('1.15.5', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.15.5');
	}

	if (version_compare('1.15.6', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.15.6');
	}

	if (version_compare('1.15.7', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.15.7');
	}

	if (version_compare('1.16.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.16.0');
	}

	if (version_compare('1.16.3', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.16.3');
	}

	if (version_compare('1.17.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.17.0');
	}

	if (version_compare('1.19.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.19.0');
	}

	if (version_compare(WPTC_VERSION, $installed_wptc_version) > 0) {
		//This executes on every update
		$config->set_option('first_backup_started_atleast_once', true);
		$config->set_option('prev_installed_wptc_version', $installed_wptc_version);
		$config->set_option('wptc_version', WPTC_VERSION);
	}

	//Updates those use force_start_or_restart_backup should come after this.
	if (version_compare('1.14.10', $installed_wptc_version) > 0) {
		$config->set_option('insert_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->insert_default_excluded_files();
		WPTC_Base_Factory::get('Wptc_App_Functions')->force_start_or_restart_backup();
	}

	if (version_compare('1.15.8', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.15.8');
	}

	if (version_compare('1.15.10', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.15.10');
	}

	if (version_compare('1.16.1', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.16.1');
	}

	if (version_compare('1.16.2', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.16.2');
	}

	if (version_compare('1.18.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.18.0');
	}

	if (version_compare('1.20.0', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.20.0');
	}

	if (version_compare('1.20.6', $installed_wptc_version) > 0) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades('1.20.6');
	}

	//Send data to server
	if (version_compare(WPTC_VERSION, $installed_wptc_version) > 0) {
		add_action( 'plugins_loaded',  'wptc_send_upgrade_info_to_server' );
	}
}

function check_wptc_update_after_pro_loaded()
{
	$installed_wptc_pro_version = WPTC_Factory::get('config')->get_option('wptc_pro_version');
	if(empty($installed_wptc_pro_version)){
		$installed_wptc_pro_version = '0.0.0';
	}
	
	if (version_compare('1.21.0', $installed_wptc_pro_version) > 0) {
		WPTC_Factory::get('config')->set_option('wptc_pro_version', '1.21.0');
		WPTC_Base_Factory::get('Wptc_App_Functions')->plugin_upgrades_pro('1.21.0');
	}	
}

function wptc_send_upgrade_info_to_server(){
	$Wptc_Backup_Analytics = new Wptc_Backup_Analytics();
	$Wptc_Backup_Analytics->send_basic_analytics();
	$Wptc_Backup_Analytics->send_cloud_account_used();
	$Wptc_Backup_Analytics->send_backups_data_to_server();
	WPTC_Base_Factory::get('Wptc_Settings')->auto_whitelist_ips();

	wptc_modify_schedule_backup($dont_reactivate = true);

	if(is_any_ongoing_wptc_backup_process()){
		wptc_set_backup_in_progress_server(true, null, $dont_reactivate = true);
	}

	//This might take some times so adding at the end
	$Wptc_Backup_Analytics->send_server_info();
}

function clear_gdrive_backup_data_wptc() {
	$config = WPTC_Factory::get('config');
	if ($config->get_option('default_repo') == 'g_drive') {
		$backup = new WPTC_BackupController();
		$backup->clear_prev_repo_backup_files_record();
	}
}

function process_parent_dirs_wptc($result, $type) {
	if (empty($type)) {
		$result = json_decode(json_encode($result), True);
	}
	$wp_path = WPTC_RELATIVE_ABSPATH;
	$break = false;
	$path = $result['file'];
	$backup_id = $result['backupID'];
	// $path = str_replace($wp_path, '', $path);
	$dirs = explode('/', $path);
	$breadcrumb = '';
	$new_file = '';
	$parent_dir = '';
	// $path = str_replace($wp_path, '', $path);
	$dirs = explode('/', $path);
	$breadcrumb = '';
	$cacheArray = array();
	while (count($dirs) > 0) {
		$link = '/' . implode($dirs, '/');
		$text = array_pop($dirs);
		$link = ltrim($link, '/');
		if (empty($type)) {
			$new_file = wp_normalize_path($wp_path . $link);
		} else {
			$new_file = $wp_path . $link;
		}
		if (empty($type)) {
			$parent_dir = wp_normalize_path(get_parent_dir_from_path_wptc($link, $wp_path));
		} else {
			$parent_dir = get_parent_dir_from_path_wptc($link, $wp_path);
		}

		$parent_dir = !empty($parent_dir) ? $parent_dir : '/' ;

		$cacheCheck = $new_file . '/' . $backup_id . '/' . $parent_dir;
		if (is_array($cacheArray)) {
			if (in_array($cacheCheck, $cacheArray)) {
				break;
			}
		}
		if (empty($type)) {
			lazy_load_insert_or_update_row_wptc($new_file, $backup_id, $parent_dir);
		} else if ($type == 'process_files') {
			$processed_files = WPTC_Factory::get('processed-files');
			$full_path_new_file = wptc_add_fullpath($new_file);
			if (is_dir($full_path_new_file)) {
				$is_dir = 1;
				$processed_files->base_upsert(array(
					'file' => $new_file,
					'uploadid' => null,
					'offset' => 0,
					'backupID' => $result['backupID'],
					'revision_number' => null,
					'revision_id' => null,
					'mtime_during_upload' => null,
					'uploaded_file_size' => null,
					'cloud_type' => null,
					'parent_dir' => $parent_dir,
					'is_dir' => $is_dir,
					'file_hash' => '',
					'filepath_md5' => null,
				));
			} else {
				$is_dir = 0;
				$processed_files->base_upsert(array(
					'file' => $new_file,
					'uploadid' => $result['uploadid'],
					'offset' => $result['offset'],
					'backupID' => $result['backupID'],
					'revision_number' => $result['revision_number'],
					'revision_id' => $result['revision_id'],
					'mtime_during_upload' => $result['mtime_during_upload'],
					'uploaded_file_size' => $result['uploaded_file_size'],
					'g_file_id' => $result['g_file_id'],
					'cloud_type' => $result['cloud_type'],
					'parent_dir' => $parent_dir,
					'is_dir' => $is_dir,
					'file_hash' => $result['file_hash'],
					'filepath_md5' => md5($new_file),
				));
			}
		}
		$cacheArray[] = $new_file . '/' . $backup_id . '/' . $parent_dir;
	}
}

function get_parent_dir_from_path_wptc($link, $wp_path) {
	$breadcrumb = substr($link, 0, strrpos($link,  '/' ));
	if (empty($breadcrumb)) {
		$parent_dir = rtrim($wp_path,  '/' );
	} else {
		$parent_dir = $wp_path . $breadcrumb;
	}
	return $parent_dir;
}

function lazy_load_insert_or_update_row_wptc($new_file, $backup_id, $parent_dir) {
	global $wpdb;
	if (is_dir($new_file)) {

		$is_dir = 1;
	} else {
		$is_dir = 0;
	}
	$sqlTmp = "SELECT * FROM " . $wpdb->base_prefix . "wptc_processed_files WHERE backupID = '$backup_id' AND file = '$new_file' LIMIT 1";
	$row_exist = $wpdb->get_results($sqlTmp);
	if (count($row_exist) == 0) {
		$mysql_query = "INSERT INTO " . $wpdb->base_prefix . "wptc_processed_files (`file`, `offset`, `uploadid`, `file_id`, `backupID` ,`revision_number`, `revision_id`, `mtime_during_upload`, `uploaded_file_size`, `g_file_id`, `s3_part_number`, `s3_parts_array`, `cloud_type`, `parent_dir`, `is_dir`) VALUES ('$new_file', 0, NULL, NULL, $backup_id, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$parent_dir', '$is_dir' )";
		$result_temp = $wpdb->query($mysql_query);
		if ($wpdb->last_error !== '') {
			$wpdb->print_error();
		}

	} else {
		$wpdb->query("UPDATE " . $wpdb->base_prefix . "wptc_processed_files SET parent_dir = '$parent_dir', is_dir = '$is_dir' WHERE backupID = '$backup_id' AND file = '$new_file'");
	}
}

function create_auto_backup_db_wptc() {
	global $wpdb;

	if (method_exists($wpdb, 'get_charset_collate')) {
		$charset_collate = $wpdb->get_charset_collate();
	}

	if (!empty($charset_collate)) {
		$cachecollation = $charset_collate;
	} else {
		$cachecollation = ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ';
	}

	include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->base_prefix . 'wptc_auto_backup_record';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `timestamp` double NOT NULL,
		  `type` enum('upload','plugin-update','theme-update','core-update','other-update','bulk-plugin-update','bulk-theme-update','plugin-install','theme-install','core-install','other-install','file-edit','img-upload','video-upload','other-upload') NOT NULL,
		  `file` text,
		  `backup_status` enum('noted','queued','backed_up') DEFAULT 'noted',
		  `prev_backup_id` double DEFAULT '0',
		  `cur_backup_id` double DEFAULT '0',
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB " . $cachecollation . ";");
}

function wptc_database_changes_5_0() {
	global $wpdb;
	$wptc_processed_files = $wpdb->base_prefix . 'wptc_processed_files';
	$add_column_result = $wpdb->query('ALTER TABLE `' . $wptc_processed_files . '` ADD `parent_dir` TEXT DEFAULT NULL , ADD `is_dir` INT(1) DEFAULT NULL;');
	$add_index_key_result = $wpdb->query('ALTER TABLE `' . $wptc_processed_files . '` ADD KEY `file_backup_id` (`file`(191),`backupID`);');
	$modify_option_value = $wpdb->query('ALTER TABLE `' . $wptc_processed_files . '` MODIFY COLUMN `value` TEXT;');
	$add_index_revision_id = $wpdb->query('ALTER TABLE `' . $wptc_processed_files . '` ADD INDEX `revision_id` (`revision_id`(191))');
	$add_index_file = $wpdb->query('ALTER TABLE `' . $wptc_processed_files . '` ADD INDEX `file` (`file`(191))');

	$wptc_processed_restored_files = $wpdb->base_prefix . 'wptc_processed_restored_files';
	$add_index_revision_id_restore_table = $wpdb->query('ALTER TABLE `' . $wptc_processed_restored_files . '` ADD INDEX `revision_id` (`revision_id`(191))');
	$add_index_file_restore_table = $wpdb->query('ALTER TABLE `' . $wptc_processed_restored_files . '` ADD INDEX `file` (`file`(191))');
}

function wptc_database_changes_6_0() {
	global $wpdb;
	$wptc_options = $wpdb->base_prefix . 'wptc_options';

	$modify_option_value = $wpdb->query('ALTER TABLE `' . $wptc_options . '` MODIFY COLUMN `value` TEXT;');
}

function wptc_database_changes_7_0() {
	$config = WPTC_Factory::get('config');
	$config->set_option('backup_before_update_setting', 'everytime');

	global $wpdb;
	$wptc_activity_log = $wpdb->base_prefix . 'wptc_activity_log';
	$modify_activity_log = $wpdb->query("ALTER TABLE `" . $wptc_activity_log . "` ADD `show_user` ENUM('1','0') NOT NULL DEFAULT '1' ");
	$add_index_activity_id_activity_log = $wpdb->query('ALTER TABLE `' . $wptc_activity_log . '` ADD INDEX `action_id` (`action_id`(191))');
	$add_index_show_user_activity_log = $wpdb->query('ALTER TABLE `' . $wptc_activity_log . '` ADD INDEX `show_user` (`show_user`)');
}

function wptc_database_changes_8_0() {
	global $wpdb;

	if (method_exists($wpdb, 'get_charset_collate')) {
		$charset_collate = $wpdb->get_charset_collate();
	}

	if (!empty($charset_collate)) {
		$cachecollation = $charset_collate;
	} else {
		$cachecollation = ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ';
	}

	include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$table_name = $wpdb->base_prefix . 'wptc_excluded_tables';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
		table_name varchar(255) NOT NULL,
		UNIQUE KEY `table_name` (`table_name`(191))
	) ENGINE=InnoDB " . $cachecollation . " ;");
	$wptc_exc_tables = $wpdb->base_prefix . 'wptc_excluded_files';

	$table_name = $wpdb->base_prefix . 'wptc_included_tables';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
		table_name varchar(255) NOT NULL,
		UNIQUE KEY `table_name` (`table_name`(191))
	) ENGINE=InnoDB " . $cachecollation . " ;");

	$table_name = $wpdb->base_prefix . 'wptc_included_files';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
		file varchar(255) NOT NULL,
		isdir tinyint(1) NOT NULL,
		UNIQUE KEY `file` (`file`(191))
	) ENGINE=InnoDB " . $cachecollation . " ;");

	$wptc_exc_tables = $wpdb->base_prefix . 'wptc_excluded_files';
	$add_id_exc_files = $wpdb->query('ALTER TABLE `' . $wptc_exc_tables . '` ADD `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;');
}

function wptc_database_changes_9_0() {
	global $wpdb;
	$wptc_current_process = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_current_process` ADD `file_hash` varchar(128) NULL");
	$wptc_processed_files = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files` ADD `file_hash` varchar(128) NULL");
}

function wptc_database_changes_10_0() {
	global $wpdb;
	$wptc_processed_files = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files` ADD `life_span` double NULL");
}

function wptc_database_changes_11_0() {
	global $wpdb;

	if (method_exists($wpdb, 'get_charset_collate')) {
		$charset_collate = $wpdb->get_charset_collate();
	}

	if (!empty($charset_collate)) {
		$cachecollation = $charset_collate;
	} else {
		$cachecollation = ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ';
	}

	include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->base_prefix . 'wptc_debug_log';
	dbDelta("CREATE TABLE IF NOT EXISTS $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`user_time` varchar(22) NOT NULL,
		`system_time` varchar(22) NOT NULL,
		`type` varchar(100) DEFAULT NULL,
		`log` text NOT NULL,
		`backup_id` varchar(22) DEFAULT NULL,
		`memory` varchar(20) DEFAULT NULL,
		`peak_memory` varchar(20) DEFAULT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB " . $cachecollation . " ;");
}

function wptc_database_changes_12_0() {
	global $wpdb;
	$processed_restored_files = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_restored_files` ADD `file_hash` varchar(128) NULL");
}

function wptc_database_changes_13_0() {
	global $wpdb;
	$wp_wptc_backups = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_backups` ADD `update_details` text DEFAULT NULL");
}

function wptc_database_changes_14_0() {
	global $wpdb;
	$drop_index = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_dbtables` DROP INDEX `name`");
	$add_id = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_dbtables` ADD `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;");
	$change_datatype = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_dbtables` CHANGE `name` `name` longtext NOT NULL;");
	$rename_table = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_dbtables` RENAME TO `" . $wpdb->base_prefix . "wptc_processed_iterator`");
}


function wptc_database_changes_15_0() {
	global $wpdb;

	$drop_index_keys_backup = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files`
		DROP INDEX `file_backup_id`,
		DROP INDEX `revision_id`,
		DROP INDEX `file`
		");

	$drop_index_keys_restore = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_restored_files`
		DROP INDEX `revision_id`,
		DROP INDEX `file`
		");

	$drop_index_keys_exc = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_excluded_files`
		DROP INDEX `file`
		");

	$drop_index_keys_inc = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_included_files`
		DROP INDEX `file`
		");

	$drop_index_keys_iterator = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_iterator`
		DROP INDEX `name`
		");
}

function wptc_database_changes_16_0() {
	global $wpdb;

	$add_uploaded_file_index = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files`
		ADD INDEX `uploaded_file_size` (`uploaded_file_size`)
		");


	$add_backup_index = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files`
		ADD INDEX `backupID` (`backupID`)
		");

	$change_index = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_iterator`
		CHANGE `count` `offset` text DEFAULT NULL AFTER `name`;
		");

	$add_filepath_md5 = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files`
		ADD `filepath_md5` varchar(32) NULL;
		");

	$add_index = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_files`
		ADD INDEX `filepath_md5` (`filepath_md5`)
		");

	$add_column = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_restored_files`
		ADD `is_future_file` int(1) DEFAULT '0'
		");

	$alter_current_process = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_current_process`
		CHANGE `file_path` `file_path` text NOT NULL AFTER `id`
		");

	$alter_current_process = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_current_process`
		ADD INDEX `file_path` (`file_path`(191))
		");

	$alter_current_process = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_processed_restored_files`
		ADD INDEX `file` (`file`(191))
		");
}


function wptc_database_changes_17_0() {
	global $wpdb;

	$change_column_type_included_tables = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_included_tables`
		CHANGE `table_name` `table_name` text NOT NULL
		");

	$change_column_type_excluded_tables = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_excluded_tables`
		CHANGE `table_name` `table_name` text NOT NULL
		");

	$change_column_type_included_files = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_included_files`
		CHANGE `file` `file` text NOT NULL
		");

	$change_column_type_excluded_files = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_excluded_files`
		CHANGE `file` `file` text NOT NULL
		");

	$add_column_included_tables = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_included_tables`
		ADD `backup_structure_only` int(1) NOT NULL DEFAULT '0'
		");

	$drop_index_included_tables = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_included_tables`
		DROP INDEX `table_name`;
		");

	$drop_index_excluded_tables = $wpdb->query("ALTER TABLE `" . $wpdb->base_prefix . "wptc_excluded_tables`
		DROP INDEX `table_name`;
		");
}


function process_wptc_logout($deactivated_plugin = null) {

	stop_fresh_backup_tc_callback_wptc($deactivated_plugin, false);

	stop_wptc_server();

	$config = WPTC_Factory::get('config');

	$config->set_option('is_user_logged_in', false);
	$config->set_option('wptc_server_connected', false);
	$config->set_option('signup', false);
	$config->set_option('appID', false);
	$config->set_option('main_account_email', 0);
	$config->set_option('main_account_pwd', 0);
	$config->set_option('privileges_wptc', false);

	$config->set_option('wptc_token', false);

	reset_restore_related_settings_wptc();

	reset_backup_related_settings_wptc();
}

function set_default_repo_for_previous_update_wptc(&$config) {
	$config->set_option('default_repo', 'dropbox');

	$signed_in_arr['dropbox'] = 'Dropbox';
	$config->set_option('signed_in_repos', serialize($signed_in_arr));
}

function wptc_init_flags() {
	try {
		check_wptc_update();
		if (defined('FS_METHOD') && FS_METHOD === 'direct') {
			global $wp_filesystem;
			if (!$wp_filesystem) {
				initiate_filesystem_wptc();
				if (empty($wp_filesystem)) {
					send_response_wptc('FS_INIT_FAILED-034');
					return false;
				}
			}
		}
		if(is_wptc_server_req() || is_admin()){
			WPTC_Factory::get('config')->choose_db_backup_path();
		}

		if (!get_option('wptc-premium-extensions')) {
			add_option('wptc-premium-extensions', array(), false, 'no');
		}

		if (!WPTC_Factory::get('config')->get_option('before_backup')) {
			WPTC_Factory::get('config')->set_option('before_backup', 'yes_no');
		}

		if (!WPTC_Factory::get('config')->get_option('anonymous_datasent')) {
			WPTC_Factory::get('config')->set_option('anonymous_datasent', 'no');
		}

		if (!WPTC_Factory::get('config')->get_option('schedule_backup')) {
			WPTC_Factory::get('config')->set_option('schedule_backup', 'off');
		}

		if (!WPTC_Factory::get('config')->get_option('wptc_timezone')) {
			if (get_option('timezone_string') != "") {
				WPTC_Factory::get('config')->set_option('wptc_timezone', get_option('timezone_string'));
			} else {
				WPTC_Factory::get('config')->set_option('wptc_timezone', 'UTC');
			}
		}

		$redirect = true;

		if(defined('WPTC_DONT_REDIRECT_ON_ACTIVATE') && WPTC_DONT_REDIRECT_ON_ACTIVATE){
			$redirect = false;
		}

		if($redirect){
			add_action('init','wptc_redirect_to_login');
		}

		if (!WPTC_Factory::get('config')->get_option('wptc_service_request')) {
			WPTC_Factory::get('config')->set_option('wptc_service_request', 'no');
		}
	} catch (Exception $e) {
		error_log($e->getMessage());
	}
}


function wptc_redirect_to_login(){
	if (get_option('is_wptc_activation_redirected', false)) {
		return ;
	}

	add_option('is_wptc_activation_redirected', true);

	if (!function_exists('wp_safe_redirect')) {
		include_once ABSPATH.'wp-includes/pluggable.php';
	}

	wptc_log('', "--------wptc_redirect_to_login--here------");

	wp_safe_redirect(network_admin_url() . '?page=wp-time-capsule-monitor');
}

function my_tcadmin_notice_wptc() {
	$options_obj = WPTC_Factory::get('config');
	if (!$options_obj->get_option('restore_completed_notice')) {
		//do nothing
	} else {
		$options_obj->set_option('restore_completed_notice', false);
		/* $notice_message = "<div class='updated'> <p> "._e( 'Restored Successfully', 'my-text-domain' )."</p> </div>";
	echo $notice_message; */
	}
}

function send_issue_report_wptc() {
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	WPTC_Base_Factory::get('Wptc_App_Functions')->send_report();
}

function clear_wptc_logs() {
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	WPTC_Base_Factory::get('Wptc_App_Functions')->truncate_activity_log();
}

function wptc_clear_inc_exc_tables(){
	global $wpdb;
	$wpdb->query("TRUNCATE TABLE `" . $wpdb->base_prefix . "wptc_inc_exc_contents`");
}

function dropbox_auth_check_wptc($return = true) {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	//Dropbox auth checking for continue process
	wptc_log(DEFAULT_REPO, "--------dropbox_auth_check_wptc--------");
	$dropbox = WPTC_Factory::get(DEFAULT_REPO);
	if ( !empty($dropbox)  && $dropbox->is_authorized()) {
		reset_backup_related_settings_wptc();
		WPTC_Factory::get('config')->set_option('last_cloud_error', false);
		return true;
	} else {
		$err_msg = WPTC_Factory::get('config')->get_option('last_cloud_error');
		return false;
	}

}

function wptc_main_cycle() {
	//wptc_main_cycle is for daily full backup

	$config = new WPTC_Config();
	$timestamp_plus_secs = time() + ADVANCED_SECONDS_FOR_TIME_CALCULATION;
	$usertime = $config->get_wptc_user_today_date_time('Y-m-d', $timestamp_plus_secs);

	//weekly backup validate
	// if (( wptc_is_weekly_backup_eligible($config)  || ($config->get_option('wptc_today_main_cycle') != $usertime) ) && ($config->get_option('wptc_main_cycle_running') != true) && $config->get_option('first_backup_started_atleast_once') ) {
	if ($config->get_option('wptc_today_main_cycle') != $usertime && ($config->get_option('wptc_main_cycle_running') != true) && $config->get_option('first_backup_started_atleast_once') ) {
		$config->set_option('wptc_main_cycle_running', true);
		wptc_main_cycle_event();
	} else {
		if ($config->get_option('wptc_main_cycle_running') && $config->get_option('first_backup_started_atleast_once') && !is_any_ongoing_wptc_backup_process()) {
				$config->set_option('wptc_main_cycle_running', true);
				$config->get_option('wptc_main_cycle_running', false);
				wptc_log(array(), '---------declined_by_wptc_main_cycle RARE case------------');
		}
		wptc_log(array(), '---------declined_by_wptc_main_cycle------------');
		send_response_wptc('declined_by_wptc_main_cycle', 'SCHEDULE');
	}
}

function wptc_main_cycle_event() {
	$options = WPTC_Factory::get('config');
	if (!$options->get_option('in_progress_restore') && !$options->get_option('is_running') && !$options->get_option('is_bridge_process') && !$options->get_option('is_running_restore') && !$options->get_option('is_staging_running')) {
		do_action('just_starting_main_schedule_backup_wptc_h', '');
		$options->set_option('wptc_current_backup_type', 'D');
		start_fresh_backup_tc_callback_wptc('daily_cycle', $args = null,  $test_connection = false);
	} else {
		if (!$options->get_option('in_progress_restore')) {
			$options->set_option('recent_restore_ping', time());
			send_response_wptc('declined_by_progress_restore', 'SCHEDULE');
		}
		if (!$options->get_option('is_running')) {
			send_response_wptc('declined_by_is_running', 'SCHEDULE');
		}
		if (!$options->get_option('is_bridge_process')) {
			$options->set_option('recent_restore_ping', time());
			send_response_wptc('declined_by_bridge_process', 'SCHEDULE');
		}
		if (!$options->get_option('is_running_restore')) {
			$options->set_option('recent_restore_ping', time());
			send_response_wptc('declined_by_running_restore', 'SCHEDULE');
		}
	}
}

function wptc_reset_restore_if_long_time_no_ping(){
	$options = WPTC_Factory::get('config');
	$recent_restore_ping = $options->get_option('recent_restore_ping');

	if (empty($recent_restore_ping)) {
		return false;
	}

	$current_time = time();
	$min_idle_time = $recent_restore_ping + (30 * 60); // 30 mins

	if ($min_idle_time < $current_time) {
		wptc_log(array(), '-----------there is no chance just reverrt it-------------');
		reset_restore_related_settings_wptc();
	} else {
		wptc_log(array(), '-----------thre is chance retry-------------');
	}
}

function wptc_reset_backup_if_long_time_no_ping($restart = 0, $return = 0, $type = null){
	$options = WPTC_Factory::get('config');
	$recent_backup_ping = $options->get_option('recent_backup_ping');

	if (empty($recent_backup_ping)) {
		return false;
	}
	$current_time = time();
	$min_idle_time = $recent_backup_ping + (60 * 60); // 80 mins
	wptc_log($current_time,'--------------$current_time-------------');
	wptc_log($min_idle_time,'--------------$min_idle_time-------------');
	if ($min_idle_time < $current_time) {
		wptc_log(array(), '-----------there is no chance just reverrt it-------------');
		if ($return) {
			return true;
		}
		$backup = new WPTC_BackupController();
		$backup->stop();
	} else {
		wptc_log(array(), '-----------thre is chance retry-------------');
		if ($return) {
			return false;
		}
	}
	if ($restart) {
		if (empty($type)) {
			$type = wptc_get_recent_backup_type();
		}
		wptc_log($type, '---------$type restart ------------');
		start_fresh_backup_tc_callback_wptc($type, null, true, false);
	}
}

function wptc_get_recent_backup_type(){
	$config = WPTC_Factory::get('config');
	if ($config->get_option('schedule_backup_running')) {
		return 'daily_cycle';
	} else if ($config->get_option('wptc_main_cycle_running') || $config->get_option('auto_backup_running')) {
		return 'sub_cycle';
	} else {
		return 'manual';
	}

}

function storage_quota_check_wptc() {
	$default_repo = WPTC_Factory::get('config')->get_option('default_repo');

	if($default_repo == 'dropbox' || $default_repo == 'gdrive'){
		$cloud = WPTC_Factory::get($default_repo);
		$cloud->ping_server_if_storage_quota_low();
	}
}

// Sub cycle event trigger the backup (file and DB incremental process)
function sub_cycle_event_func_wptc($request_type = null) {

	$options = WPTC_Factory::get('config');

	wptc_reset_restore_if_long_time_no_ping();

	$tt = time();
	$usertime_full_stamp = $options->cnvt_UTC_to_usrTime($tt);
	$usertime_full = date('j M, g:ia', $usertime_full_stamp);

	$cur_time = date('Y-m-d H:i:s');

	$options->set_option('last_cron_triggered_time', $usertime_full);
	$first_backup_started_atleast_once = $options->get_option('first_backup_started_atleast_once');


	if (is_any_ongoing_wptc_restore_process() || is_any_ongoing_wptc_backup_process() || is_any_other_wptc_process_going_on() || !$options->get_option('default_repo') || !$options->get_option('main_account_email') || !$options->get_option('is_user_logged_in')) {

		if (is_any_ongoing_wptc_restore_process()) {
			$options->set_option('recent_restore_ping', time());
			send_response_wptc('declined_restore_in_progress', WPTC_DEFAULT_CRON_TYPE);
		} else if (is_any_ongoing_wptc_backup_process()) {
			$options->set_option('recent_backup_ping', time());
			wptc_set_backup_in_progress_server(true);
			send_response_wptc('declined_backup_in_progress_and_retried', WPTC_DEFAULT_CRON_TYPE);
		} else if (is_any_other_wptc_process_going_on()) {
			// do_action('init_staging_wptc_h', time());
			send_response_wptc('declined_staging_processes_in_progress', WPTC_DEFAULT_CRON_TYPE);
		} else if(!$options->get_option('default_repo')) {
			send_response_wptc('declined_default_repo_empty', WPTC_DEFAULT_CRON_TYPE);
		} else if(!$options->get_option('main_account_email')) {
			send_response_wptc('declined_main_account_email_empty', WPTC_DEFAULT_CRON_TYPE);
		} else if(!$options->get_option('is_user_logged_in')) {
			send_response_wptc('declined_user_logged_out', WPTC_DEFAULT_CRON_TYPE);
		}

		return false;
	}

	$first_backup_started_but_not_completed = $options->get_option('starting_first_backup'); // true first backup started but not completed

	$timestamp_plus_secs = time() + ADVANCED_SECONDS_FOR_TIME_CALCULATION;
	$usertime = $options->get_wptc_user_today_date_time('Y-m-d', $timestamp_plus_secs);
	$wptc_today_main_cycle = $options->get_option('wptc_today_main_cycle');

	storage_quota_check_wptc();

	if ($wptc_today_main_cycle == $usertime && !$first_backup_started_but_not_completed) {

		if( !apply_filters('validate_auto_backup_wptc', true) ){

			wptc_log('', "--------missed_backup_3--------");

			send_response_wptc('Scheduled backup is completed ', WPTC_DEFAULT_CRON_TYPE);
		}

		do_action('start_auto_backup_wptc', time());

	} else {

		if ($wptc_today_main_cycle == $usertime) {

			wptc_log('', "--------missed_backup_4--------");

			send_response_wptc('Scheduled backup is completed', WPTC_DEFAULT_CRON_TYPE);
		}

		wptc_main_cycle();
	}
}

register_activation_hook(__FILE__, 'wptc_install');

function register_the_js_events_wptc($hook) {
	wp_enqueue_style('wptc-tc-ui', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/tc-ui.css', array(), WPTC_VERSION);
	wp_enqueue_style('wptc-opentip', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/css/opentip.css', array(), WPTC_VERSION);
	wp_enqueue_script('wptc-jquery', false, array(), WPTC_VERSION);
	wp_enqueue_script('wptc-actions', plugins_url() . '/' . basename(dirname(__FILE__)) . '/time-capsule-update-actions.js', array(), WPTC_VERSION);
	wp_enqueue_script('wptc-pro-common-listener', plugins_url() . '/' . basename(dirname(__FILE__)) . '/js/ProCommonListener.js', array(), WPTC_VERSION);

	wptc_init_nonce();

	if (!wptc_can_load_third_party_scripts()) {

		wptc_log('', "--------wptc_cannot_load_other_plugin_scripts--------");

		return ;
	}

	wp_enqueue_script('wptc-opentip-jquery', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/js/opentip-jquery.js', array(), WPTC_VERSION);
	wp_enqueue_script('wptc-clipboard-js', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/js/clipboard.min.js', array(), WPTC_VERSION);
	wp_enqueue_style('wptc-sweetalert-css', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/lib/sweetalert.css', array(), WPTC_VERSION);
	wp_enqueue_script('wptc-sweetalert-js', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/lib/sweetalert.min.js', array(), WPTC_VERSION);
	wp_enqueue_style('wptc-jquery-ui-css', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/css/jquery-ui.css', array(), WPTC_VERSION);
	wp_enqueue_script('wptc-monitor-js', plugins_url() 	. '/' . basename(dirname(__FILE__)) . '/Views/wptc-monitor.js', array(), WPTC_VERSION);
	wptc_init_monitor_js_keys();
}

wptc_init_screenshot_script();

function wptc_init_screenshot_script(){
	include_once ( WPTC_PRO_DIR . 'Screenshot/Screenshot.php' );
	new Wptc_Screenshot_Loader();
}

add_action('admin_enqueue_scripts', 'register_the_js_events_wptc');

add_action('admin_enqueue_scripts', 'deregister_other_plugin_scripts_wptc', 200);

function deregister_other_plugin_scripts_wptc($hook){
	if ( stripos($hook, 'wp-time-capsule') !== false 
		 || stripos($hook, 'plugins.php') !== false
		 || strpos($hook, 'plugin-install.php') !== false
		 || stripos($hook, 'themes.php') !== false
		 || stripos($hook, 'update-core.php') !== false ){

		wptc_log($hook, "--------deregistering_other_plugin_scripts_wptc--------");

		wp_dequeue_script('sweetalert');
	}
}

function wptc_init_nonce(){
	$params = array(
		'ajax_nonce' => wp_create_nonce('wptc_nonce'),
		'admin_url' => network_admin_url(),
	);

	wp_localize_script( 'wptc-actions', 'wptc_ajax_object', $params );
}

function wptc_init_actions(){
	if (!is_wptc_server_req() && !is_admin()) {
		return ;
	}

	//Custom filters and actions
	if(is_multisite()){
		add_action('network_admin_notices', 'my_tcadmin_notice_wptc');
	} else {
		add_action('admin_notices', 'my_tcadmin_notice_wptc');
	}

	add_action('admin_enqueue_scripts', 'wptc_style');

	do_action('add_query_filter_wptc', time());
	add_action('load-index.php', 'admin_notice_on_dashboard_wptc');

	//WordPress filters and actions
	add_action('wp_ajax_progress_wptc', 'tc_backup_progress_wptc');
	add_action('wp_ajax_get_this_day_backups_wptc', 'get_this_day_backups_callback_wptc');
	add_action('wp_ajax_get_sibling_files_wptc', 'get_sibling_files_callback_wptc');
	add_action('wp_ajax_get_in_progress_backup_wptc', 'get_in_progress_tcbackup_callback_wptc');
	add_action('wp_ajax_start_backup_tc_wptc', 'start_backup_tc_callback_wptc');
	add_action('wp_ajax_store_name_for_this_backup_wptc', 'store_name_for_this_backup_callback_wptc');
	add_action('wp_ajax_start_fresh_backup_tc_wptc', 'start_fresh_backup_tc_callback_wptc');
	add_action('wp_ajax_stop_fresh_backup_tc_wptc', 'stop_fresh_backup_tc_callback_wptc');
	add_action('wp_ajax_stop_restore_tc_wptc', 'stop_restore_tc_callback_wptc');
	add_action('wp_ajax_start_restore_tc_wptc', 'start_restore_tc_callback_wptc');
	add_action('wp_ajax_send_issue_report_wptc', 'send_issue_report_wptc');
	add_action('wp_ajax_clear_wptc_logs', 'clear_wptc_logs');
	add_action('wp_ajax_continue_with_wtc', 'dropbox_auth_check_wptc');
	add_action('wp_ajax_get_dropbox_authorize_url_wptc', 'get_dropbox_authorize_url_wptc');
	add_action('wp_ajax_get_g_drive_authorize_url_wptc', 'get_g_drive_authorize_url_wptc');
	add_action('wp_ajax_get_s3_authorize_url_wptc', 'get_s3_authorize_url_wptc');
	add_action('wp_ajax_get_wasabi_authorize_url_wptc', 'get_wasabi_authorize_url_wptc');
	add_action('wp_ajax_change_wptc_default_repo', 'change_wptc_default_repo');
	add_action('wp_ajax_plugin_update_notice_wptc', 'plugin_update_notice_wptc');
	add_action('wp_ajax_lazy_load_activity_log_wptc', 'lazy_load_activity_log_wptc');
	add_action('wp_ajax_update_sycn_db_view_wptc', 'update_sycn_db_view_wptc');
	add_action('wp_ajax_save_initial_setup_data_wptc', 'save_initial_setup_data_wptc');
	add_action('wp_ajax_test_connection_wptc_cron', 'test_connection_wptc_cron');
	add_action('wp_ajax_save_general_settings_wptc', 'save_general_settings_wptc');
	add_action('wp_ajax_save_advanced_settings_wptc', 'save_advanced_settings_wptc');
	add_action('wp_ajax_save_backup_settings_wptc', 'save_backup_settings_wptc');
	add_action('wp_ajax_resume_backup_wptc', 'resume_backup_wptc');
	add_action('wp_ajax_proceed_to_pay_wptc', 'proceed_to_pay_wptc');
	add_action('wp_ajax_save_manual_backup_name_wptc', 'save_manual_backup_name_wptc');
	add_action('wp_ajax_clear_show_users_backend_errors_wptc', 'clear_show_users_backend_errors_wptc');
	add_action('wp_ajax_make_this_fresh_site_wptc', 'make_this_fresh_site_wptc');
	add_action('wp_ajax_make_this_original_site_wptc', 'make_this_original_site_wptc');
	add_action('wp_ajax_login_request_wptc', 'login_request_wptc');
	add_action('wp_ajax_wptc_sync_purchase', 'wptc_sync_purchase');
	add_action('wp_ajax_decrypt_file_wptc', 'decrypt_file_wptc');
	add_action('wp_ajax_clear_all_decrypt_files_wptc', 'clear_all_decrypt_files_wptc');
	add_action('wp_ajax_get_check_to_show_dialog_wptc', 'get_check_to_show_dialog_wptc');
	add_action('wp_ajax_prepare_file_upload_index_file_wptc', 'prepare_file_upload_index_file_wptc');
	add_action('wp_ajax_delete_file_upload_index_file_wptc', 'delete_file_upload_index_file_wptc');
	add_action('wp_ajax_clear_upgrade_after_backup_flags_wptc', 'clear_upgrade_after_backup_flags_wptc');

	if ( is_multisite() ) {
		add_action('network_admin_menu', 'wptc_add_main_admin_menu');
		add_action('admin_menu', 'wptc_add_child_admin_menu');
	} else {
		add_action('admin_menu', 'wptc_add_main_admin_menu');
	}
}

function wptc_add_main_admin_menu(){
	wordpress_time_capsule_admin_menu(array(
				'main'          => true,
				'initial_setup' => true,
				'backups'       => true,
				'sub_menus'     => true,
				'activity_log'  => true,
				'settings'      => true,
				'dev_option'    => true,
			)
	);
}

function wptc_add_child_admin_menu(){
	wordpress_time_capsule_admin_menu(array(
				'main'          => true,
				'initial_setup' => false,
				'backups'       => true,
				'sub_menus'     => false,
				'activity_log'  => false,
				'settings'      => false,
				'dev_option'    => false,
			)
	);
}

function wptc_activation() {
	WPTC_Factory::get('logger')->log('WP Time Capsule Activated', 'others');
	$config = WPTC_Factory::get('config');

	if (empty($config)) {
		return ;
	}

	if(!$config->get_option('is_user_logged_in')){
		return ;
	}

	wptc_modify_schedule_backup();

	if(is_any_ongoing_wptc_backup_process()){
		wptc_set_backup_in_progress_server(true, null, $dont_reactivate = true);
	}
}

function wptc_deactivation() {

	WPTC_Factory::get('config')->set_option('backup_slot', 'daily');
	wptc_modify_schedule_backup();

	stop_wptc_server();

	WPTC_Base_Factory::get('Trigger_Init')->drop_trigger_for_all_tables();

	WPTC_Factory::get('logger')->log('WP Time Capsule Deactivated', 'others');
	delete_option('is_wptc_activation_redirected');
}

register_deactivation_hook(__FILE__, 'wptc_deactivation');

function init_auto_backup_settings_wptc(&$config) {
	$config->set_option('wptc_service_request', 'yes');

	$scheduled_time_string = $config->get_option('schedule_time_str');
	if (!$scheduled_time_string) {
		$scheduled_time_string = WPTC_DEFAULT_SCHEDULE_TIME_STR;
	}
	$config->set_option('schedule_time_str', $scheduled_time_string);
}

function initial_setup_notices_wptc() {
	global $wpdb;

	$fcount = $wpdb->get_results('SELECT COUNT(*) as files FROM ' . $wpdb->base_prefix . 'wptc_processed_files');

	if ( !apply_filters('is_whitelabling_active_wptc', false) && !empty($fcount) && !($fcount[0]->files > 0)) {
		?>
			<div class="updated">
				<p>WP Time Capsule is ready to use. <a href="<?php echo network_admin_url() . 'admin.php?page=wp-time-capsule-monitor&action=initial_setup' ?>">Take your first backup now</a>.</p>
			</div>
		<?php
	}
}

function admin_notice_on_dashboard_wptc() {
	if(is_multisite()){
		add_action('network_admin_notices', 'initial_setup_notices_wptc');
	} else {
		add_action('admin_notices', 'initial_setup_notices_wptc');
	}
}

function get_dropbox_authorize_url_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');

	$config->set_option('default_repo', 'dropbox');
	$config->set_option('dropbox_oauth_state', 'request');
	$config->set_option('dropbox_access_token', false);
	$dropbox = WPTC_Factory::get('dropbox');

	$result['authorize_url'] = $dropbox->get_authorize_url();
	wptc_log($result, '--------$dauthorize_url--------');
	wptc_die_with_json_encode( $result );
}

function get_g_drive_authorize_url_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	set_server_req_wptc();
	$config = WPTC_Factory::get('config');
	if(set_refresh_token_g_drive($config) !== false){
		die(json_encode(array('status' => 'connected')));
	}
	$g_drive_status = $config->get_option('oauth_state_g_drive');
	if ($g_drive_status == 'access') {
		$config->set_option('oauth_state_g_drive', 'revoke');
		$cloud_obj = WPTC_Factory::get('g_drive');
		$cloud_obj->reset_oauth_config()->init();
	}
	$config->set_option('default_repo', 'g_drive');
	$email = trim($config->get_option('main_account_email', true));
	$wptc_redirect_url = urlencode(base64_encode(network_admin_url() . 'admin.php?page=wp-time-capsule&wptc_account_email='.$email));
	$dauthorize_url = WPTC_G_DRIVE_AUTHORIZE_URL . '?wptc_redirect_url=' . $wptc_redirect_url .'&WPTC_ENV='.WPTC_ENV;
	$result['authorize_url'] = $dauthorize_url;
	wptc_die_with_json_encode( $result );
}

function set_refresh_token_g_drive(&$config){
	if (empty($_POST['credsData'])) {
		return false;
	}

	if (empty($_POST['credsData']['g_drive_refresh_token'])) {
		return false;
	}
	wptc_log($_POST, '---------------$_POST-----------------');
	wptc_log(wp_unslash($_POST['credsData']['g_drive_refresh_token']), '---------------wp_unslash($_POST[credsData g_drive_refresh_token])-----------------');
	$config->set_option('default_repo', 'g_drive');
	$config->set_option('oauth_state_g_drive', 'access');
	$config->set_option('gdrive_old_token', wp_unslash($_POST['credsData']['g_drive_refresh_token']));
	$connected_obj = WPTC_Factory::get('g_drive');
	$email = trim($config->get_option('main_account_email', true));
	$refresh_token_arr = unserialize(wp_unslash($_POST['credsData']['g_drive_refresh_token']));
	$result['authorize_url'] = network_admin_url() . 'admin.php?page=wp-time-capsule&wptc_account_email='.$email. '&cloud_auth_action=g_drive&code='.$refresh_token_arr['refresh_token'];
	die(json_encode($result));
	return true;
}

function get_s3_authorize_url_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	if (empty($_POST['credsData'])) {
		wptc_die_with_json_encode( array('error' => 'please enter credentials.') );
	}

	if(!wptc_function_exist('curl_multi_exec')){
		wptc_die_with_json_encode( array('error' => 'curl_multi_exec() is not available, it is required by Amazon S3, Please contact your hosting to enable it.') );
	}

	$as3_access_key    = $_POST['credsData']['as3_access_key'];
	$as3_secure_key    = $_POST['credsData']['as3_secure_key'];
	$as3_bucket_region = $_POST['credsData']['as3_bucket_region'];
	$as3_bucket_name   = $_POST['credsData']['as3_bucket_name'];
	$as3_iam_user_status   = $_POST['credsData']['as3_iam_user_status'];

	if (empty($as3_access_key) || empty($as3_secure_key) || empty($as3_bucket_name) || empty($as3_iam_user_status)) {
		wptc_die_with_json_encode( array('error' => 'please enter credentials.') );
	}

	$config = WPTC_Factory::get('config');
	$config->set_option('as3_access_key', $as3_access_key);
	$config->set_option('as3_secure_key', $as3_secure_key);
	$config->set_option('as3_bucket_region', $as3_bucket_region);
	$config->set_option('as3_bucket_name', $as3_bucket_name);
	$config->set_option('default_repo', 's3');

	$result['authorize_url'] = network_admin_url() . 'admin.php?page=wp-time-capsule&cloud_auth_action=s3&as3_access_key=' . $as3_access_key . '&as3_secure_key=' . $as3_secure_key . '&as3_bucket_region=' . $as3_bucket_region . '&as3_bucket_name=' . $as3_bucket_name . '';

	include_once WPTC_PLUGIN_DIR . 'S3/class.iam.php';
	$iam = new WPTC_IAM_S3();

	if ($as3_iam_user_status === 'full_access') {
		$iam->authorize_full_access();
		$response = $iam->process_full_access();
	} else {
		$iam->authorize_restricted_access();
		$response = $iam->process_restricted_access();
		$config->set_option('is_auto_generated_iam', true);
	}

	wptc_log($response,'-----------$response-----get_s3_authorize_url_wptc-----------');

	if (!empty($response['error'])) {
		$config->set_option('as3_access_key', false);
		$config->set_option('as3_secure_key', false);
		$config->set_option('as3_bucket_region', false);
		$config->set_option('as3_bucket_name', false);
		$config->set_option('default_repo', false);
		$config->set_option('s3_NoncurrentVersionExpiration_days', false);
		wptc_die_with_json_encode( $response );
	}

	WPTC_Factory::get('S3Facade');
	wptc_die_with_json_encode( $result );
}

function get_wasabi_authorize_url_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	if (empty($_POST['credsData'])) {
		wptc_die_with_json_encode( array('error' => 'please enter credentials.') );
	}

	if(!wptc_function_exist('curl_multi_exec')){
		wptc_die_with_json_encode( array('error' => 'curl_multi_exec() is not available, it is required by Wasabi, Please contact your hosting to enable it.') );
	}

	$wasabi_access_key    = $_POST['credsData']['wasabi_access_key'];
	$wasabi_secure_key    = $_POST['credsData']['wasabi_secure_key'];
	$wasabi_bucket_region = $_POST['credsData']['wasabi_bucket_region'];
	$wasabi_bucket_name   = $_POST['credsData']['wasabi_bucket_name'];

	$config = WPTC_Factory::get('config');
	$config->set_option('wasabi_access_key', $wasabi_access_key);
	$config->set_option('wasabi_secure_key', $wasabi_secure_key);
	$config->set_option('wasabi_bucket_region', $wasabi_bucket_region);
	$config->set_option('wasabi_bucket_name', $wasabi_bucket_name);
	$config->set_option('default_repo', 'wasabi');

	$result['authorize_url'] = network_admin_url() . 'admin.php?page=wp-time-capsule&cloud_auth_action=wasabi&wasabi_access_key=' . $wasabi_access_key . '&wasabi_secure_key=' . $wasabi_secure_key . '&wasabi_bucket_region=' . $wasabi_bucket_region . '&wasabi_bucket_name=' . $wasabi_bucket_name . '';


	// if (!empty($response['error'])) {
	// 	$config->set_option('wasabi_access_key', false);
	// 	$config->set_option('wasabi_secure_key', false);
	// 	$config->set_option('wasabi_bucket_region', false);
	// 	$config->set_option('wasabi_bucket_name', false);
	// 	$config->set_option('default_repo', false);
	// 	$config->set_option('wasabi_NoncurrentVersionExpiration_days', false);
	// 	wptc_die_with_json_encode( $response );
	// }

	$obj = WPTC_Factory::get('WasabiFacade');
	$obj->init();
	$response = WPTC_Factory::get('WasabiFacade')->is_authorized_during_initial_setup();

	$config->set_option('show_user_php_error', false);

	wptc_die_with_json_encode( $result );
}

function change_wptc_default_repo() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$new_default_repo = $_POST['new_default_repo'];
	if (empty($new_default_repo)) {
		wptc_die_with_json_encode( array('error' => 'Cannot not assign new repo.') );
	}

	$config = WPTC_Factory::get('config');
	$config->set_option('default_repo', $new_default_repo);
	wptc_die_with_json_encode( array('success' => $new_default_repo) );
}

//Function for wptc cron service signup
function signup_wptc_server_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');

	$email = trim($config->get_option('main_account_email', true));
	$emailhash = md5($email);
	$email_encoded = base64_encode($email);

	$pwd = trim($config->get_option('main_account_pwd', true));
	$pwd_encoded = base64_encode($pwd);

	if (empty($email) || empty($pwd)) {
		return false;
	}

	wptc_log($email, "--------email--------");

	$name = trim($config->get_option('main_account_name'));
	// $cron_url = site_url('wp-cron.php'); //wp cron commented because of new cron
	$cron_url = get_wptc_cron_url();

	$app_id = 0;
	if ($config->get_option('appID')) {
		$app_id = $config->get_option('appID');
	}

	//$post_string = "name=" . $name . "&emailhash=" . $emailhash . "&cron_url=" . $cron_url . "&email=" . $email_encoded . "&pwd=" . $pwd_encoded . "&site_url=" . home_url();

	$post_arr = array(
		'email' => $email_encoded,
		'pwd' => $pwd_encoded,
		'cron_url' => $cron_url,
		'site_url' => home_url(),
		'name' => $name,
		'emailhash' => $emailhash,
		'app_id' => $app_id,
	);

	$result = do_cron_call_wptc('signup', $post_arr);

	$resarr = json_decode($result);

	wptc_log($resarr, "--------resarr-node reply--------");

	if (!empty($resarr) && $resarr->status == 'success') {
		$config->set_option('wptc_server_connected', true);
		$config->set_option('signup', 'done');
		$config->set_option('appID', $resarr->appID);

		init_auto_backup_settings_wptc($config);
		$set = push_settings_wptc_server($resarr->appID, 'signup');
		if (WPTC_ENV !== 'production') {
			// echo $set;
		}

		$to_url = network_admin_url() . 'admin.php?page=wp-time-capsule';
		return true;
	} else {
		$config->set_option('last_service_error', $result);
		$config->set_option('appID', false);

		if (WPTC_ENV !== 'production') {
			echo "Creating Cron service failed";
		}

		return false;
	}
}

//Push the wptc (Auto/Scheduled) backup settings to wptc-server
function push_settings_wptc_server($app_id = "", $type = "", $dont_reactivate = false, $backup_db_query_limit = 0) {

	//Send ptc list to server in all save changes
	do_action('send_ptc_list_to_server_wptc', time());

	$config = WPTC_Factory::get('config');
	if ($config->get_option('wptc_service_request') == 'yes' || $type == 'signup') {
		if ($app_id == "") {
			$app_id = $config->get_option('appID');
		}

		$email = trim($config->get_option('main_account_email', true));
		$emailhash = md5($email);
		$email_encoded = base64_encode($email);

		$pwd = trim($config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		$scheduled_time_string = $config->get_option('schedule_time_str');
		if (!$scheduled_time_string) {
			$config->set_option('schedule_time_str', WPTC_DEFAULT_SCHEDULE_TIME_STR);
		}

		$time_zone      = $config->get_option('wptc_timezone');
		$backup_slot    = $config->get_option('backup_slot');
		$revision_limit = $config->get_option('revision_limit');

		if(empty($backup_db_query_limit)){
			$backup_db_query_limit = $config->get_option('backup_db_query_limit');
		}

		if(empty($backup_db_query_limit) || $backup_db_query_limit < 5){
			$backup_db_query_limit = WPTC_DEFAULT_DB_ROWS_BACKUP_LIMIT;
		}

		$cron_url = get_wptc_cron_url();

		$post_arr = array(
			'app_id'             => $app_id,
			'email'              => $email_encoded,
			'schedule'           => $scheduled_time_string,
			'frequency'          => $backup_slot,
			'revision_limit'     => $revision_limit,
			'timeZone'           => $time_zone,
			'emailhash'          => $emailhash, //below 5 settings are used only for old cron
			'cron_url'           => $cron_url, // wptc own cron
			'backup_db_query_limit' => $backup_db_query_limit,
			'schedulebackup'     => 0,
			'scheduled_unixtime' => 0,
			'scheduled_interval' => 0,
		);

		if ($dont_reactivate) {
			$post_arr['dont_reactivate'] = true;
		}

		WPTC_Base_Factory::get('Wptc_App_Functions')->add_wpengine_cookie($post_arr);

		$post_arr = apply_filters('modify_settings_to_server_wptc', $post_arr);

		wptc_log($post_arr, "--------post_string--------");

		$push_result = do_cron_call_wptc('push-settings', $post_arr);

		$is_error = process_cron_error_wptc($push_result, $no_reset = 1);
		if ($is_error) {
			return "push_failed";
		}

		$push_arr = json_decode($push_result);
		if ($push_arr->status == 'success') {
			return "success";
		} else {
			return "push_failed";
		}
	}
}

function wptc_own_cron_status() {
	$config = WPTC_Factory::get('config');
	$config->set_option('wptc_own_cron_status_notified', '0');

	if ($config->get_option('wptc_service_request') != 'yes') {
		return false;
	}

	$app_id = $config->get_option('appID');

	$email = trim($config->get_option('main_account_email', true));
	$emailhash = md5($email);
	$email_encoded = base64_encode($email);

	$post_arr = array(
		'app_id' => $app_id,
		'email' => $email_encoded,
	);

	WPTC_Base_Factory::get('Wptc_App_Functions')->add_wpengine_cookie($post_arr);

	$push_result = do_cron_call_wptc('status', $post_arr, 'GET');

	$push_arr = json_decode($push_result);

	WPTC_Base_Factory::get('Wptc_App_Functions')->save_server_response($push_arr);

	if (!empty($push_arr) && !empty($push_arr->msg) && $push_arr->msg == 'success') {
		$test_connection_status = array('status' => 'success');
		$config->set_option('wptc_own_cron_status', serialize($test_connection_status));
		return "success";
	}

	$status_code = (empty($push_arr->res_desc->statusCode)) ? 7 : $push_arr->res_desc->statusCode;
	$body = (empty($push_arr->res_desc->response->body)) ? 'Connection failed' : $push_arr->res_desc->response->body;
	$ips = (empty($push_arr->res_desc->ips)) ? '' : $push_arr->res_desc->ips;
	$new_url = (empty($push_arr->new_url)) ? '' : $push_arr->new_url;
	$old_url = (empty($push_arr->old_url)) ? '' : $push_arr->old_url;
	$is_different_url = (empty($push_arr->is_different_url)) ? false : $push_arr->is_different_url;

	$test_connection_status = array('status' => 'error',
									'statusCode' => $status_code,
									'body'=> $body,
									'ips' => $ips,
									'new_url' => $new_url,
									'old_url' => $old_url,
									'is_different_url' => $is_different_url,
									'cron_url' => get_wptc_cron_url());

	wptc_log($test_connection_status,'--------------$test_connection_status-------------');

	$config->set_option('wptc_own_cron_status', serialize($test_connection_status));
	return "push_failed";
}

//notify to the wptc server -currently backup process is running (For fast and successful backup)
function wptc_set_backup_in_progress_server($flag, $cron_type = null, $dont_reactivate = false) {

	wptc_log(get_backtrace_string_wptc(),'---------set_backup_in_progress_server------------------');
	$config = WPTC_Factory::get('config');
	$app_id = $config->get_option('appID');
	if ($config->get_option('wptc_server_connected') && $config->get_option('wptc_service_request') == 'yes' && !empty($app_id)) {
		$email = trim($config->get_option('main_account_email', true));
		$emailhash = md5($email);
		$email_encoded = base64_encode($email);

		$pwd = trim($config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		if (empty($cron_type)) {
			$cron_type = ($flag) ? 'BACKUP' : WPTC_DEFAULT_CRON_TYPE;
		}

		if ($cron_type == 'BACKUP') {
			$config->set_option('recent_backup_ping', time());
		}

		$post_arr = array(
			'app_id'           => $app_id,
			'email'            => $email_encoded,
			'cronType'         => $cron_type,
			'last_backup_time' => $config->get_option('last_backup_time'),
		);

		if ($dont_reactivate) {
			$post_arr['dont_reactivate'] = true;
		}

		wptc_log($post_arr, "--------post_string-set_backup_in_progress_server-------");

		$push_result = do_cron_call_wptc('process-backup', $post_arr);

		wptc_log($push_result, "--------pushresultset_backup_in_progress_server--------");

		process_cron_error_wptc($push_result);
		process_cron_backup_response_wptc($push_result);

	} else {
		// $config->set_option('is_user_logged_in', false);
		// $config->set_option('wptc_server_connected', false);
	}
}

function stop_wptc_server() {
	$config = WPTC_Factory::get('config');
	if ($config->get_option('wptc_server_connected')) {
		$app_id = $config->get_option('appID');

		$email = trim($config->get_option('main_account_email', true));
		$email_encoded = base64_encode($email);

		$pwd = trim($config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		$post_arr = array(
			'app_id' => $app_id,
			'email' => $email_encoded,
		);
		$push_result = do_cron_call_wptc('stop-service', $post_arr);
	}
}

function remove_wptc_server() {
	$config = WPTC_Factory::get('config');
	if ($config->get_option('wptc_server_connected')) {

		$email = trim($config->get_option('main_account_email', true));
		$email_encoded = base64_encode($email);

		$pwd = trim($config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		$post_arr = array(
			'email' => $email_encoded,
			'site_url' => home_url(),
		);

		$push_result = do_cron_call_wptc('remove-site', $post_arr);
	}
}

function do_cron_call_wptc($route_path, $post_arr, $type = 'POST') {
	$post_arr['version'] = WPTC_VERSION;
	$post_arr['source'] = 'WPTC';
	$site_url = WPTC_Factory::get('config')->get_option('site_url_wptc');
	$post_arr['site_url'] = $site_url;
	$post_arr['cron_url'] = wptc_add_trailing_slash($site_url) ;
	$post_arr['home_url'] = wptc_add_trailing_slash(get_home_url());

	// $post_arr['home_url'] = 'http://example.com/';

	$wptc_token = WPTC_Factory::get('config')->get_option('wptc_token');
	if (WPTC_DEBUG) {
		wptc_log_server_request($post_arr, '----REQUEST-----', WPTC_CRSERVER_URL . "/" . $route_path);
	}

	$chb = curl_init();

	curl_setopt($chb, CURLOPT_URL, WPTC_CRSERVER_URL . "/" . $route_path);
	curl_setopt($chb, CURLOPT_CUSTOMREQUEST, $type);
	// curl_setopt($chb, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($chb, CURLOPT_POSTFIELDS, htmlspecialchars_decode(http_build_query($post_arr, '', '&')));
	curl_setopt($chb, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($chb, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($chb, CURLOPT_SSL_VERIFYHOST, FALSE);

	$headers[] = WPTC_DEFAULT_CURL_CONTENT_TYPE;

	if(!empty($wptc_token)){
		$headers[] = "Authorization: $wptc_token";
	}

	curl_setopt($chb, CURLOPT_HTTPHEADER, $headers );

	if (!defined('WPTC_CURL_TIMEOUT')) {
		define('WPTC_CURL_TIMEOUT', 20);
	}
	curl_setopt($chb, CURLOPT_TIMEOUT, WPTC_CURL_TIMEOUT);

	$pushresult = curl_exec($chb);
	if (WPTC_DEBUG) {
		wptc_log_server_request($pushresult, '-----RESPONSE-----');
	}
	return $pushresult;
}

function process_cron_backup_response_wptc($push_result_raw = null){
	wptc_log($push_result_raw,'--------------$push_result_raw-------------');
	$config = WPTC_Factory::get('config');
	$cron_error = true;
	$push_result = json_decode($push_result_raw, true);
	wptc_log($push_result,'--------------$push_result-------------');
	if (isset($push_result['status']) && $push_result['status'] == 'success') {
		$cron_error = false;
	}
	wptc_log($cron_error,'--------------$cron_error-------------');
	if ($cron_error) {
		$time = user_formatted_time_wptc(time());
		wptc_log($time,'--------------$time-------------');
		reset_restore_related_settings_wptc();
		reset_backup_related_settings_wptc();
		send_response_wptc(array('status' => 'Backup failed to notify, Try again after sometimes.', 'error' => $push_result));
	}
}

function process_cron_error_wptc($push_result = null, $no_reset = null) {
	$config = WPTC_Factory::get('config');
	$cron_error = false;
	$full_push_result = array();
	
	if (!$push_result) {
		$cron_error = true;
	} else {
		$full_push_result = json_decode($push_result, true);
		if (isset($full_push_result) && (!empty($full_push_result['error']) || ( !empty($full_push_result['status']) &&  $full_push_result['status'] == 'error') ) ) {
			$cron_error = true;
		}
	}

	if ($cron_error) {
		if (empty($no_reset)) {
			reset_restore_related_settings_wptc();
			reset_backup_related_settings_wptc();
			send_response_wptc(array('status' => 'Cron server is failed, Try after sometime.', 'error' => $full_push_result));
		}
	}
	return $cron_error;
}

function wptc_admin_bar_icons(WP_Admin_Bar $bar) {
	return false; //disabled from 1.7.3
	$parse_url = parse_url(network_admin_url());
	if (!is_admin()) {
		return false;
	}
	$bar->add_node(array(
		'id' => 'wptc-dash-icons',
		'title' => '<span class="wptc-dash-status dashicons-before dashicons-image-rotate rotate"></span><span class="wptc-dash-text">Checking backup status...</span>',
		'href' => network_admin_url() . 'admin.php?page=wp-time-capsule-monitor',
		'meta' => array(
			'target' => '',
			// 'class' => 'wptc-dash-main wptc_logo_status_bar', //status bar wptc logo remove for now
			'class' => 'wptc-dash-main',
			// 'title' => __('Backup Completed', 'some-textdomain'),
			'html' => '',
		),
	));
}

function check_timeout_cut_and_exit_wptc($current_process_file_id = null) {
	if (is_wptc_timeout_cut()) {
		backup_proper_exit_wptc('', $current_process_file_id);
	}
}

function backup_proper_exit_wptc($msg = '', $current_process_file_id = null) {
	$config = WPTC_Factory::get('config');

	if ($config->get_option('in_progress')) {
		global $wpdb;

		if (DEFAULT_REPO === 'g_drive') {
			WPTC_Factory::get('processed-restoredfiles')->insert_gdrive_caches();
		}

		if (!empty($current_process_file_id)) {
			WPTC_Factory::get('config')->set_option('current_process_file_id', $current_process_file_id);
		}

		$backup_id = wptc_get_cookie('backupID');

		WPTC_Factory::get('logger')->log(__("Preparing for next call from server.", 'wptc'), 'backups', $backup_id);

		$config->set_option('is_running', false);

	}

	wptc_manual_debug('', 'end_cron_request');

	if (empty($msg)) {
		wptc_send_current_backup_response_to_server();
	}

	if(is_wptc_server_req()){
		exit($msg);
	} else {
		$config->set_option('show_user_php_error', $msg);
	}
}


function wptc_send_current_backup_response_to_server(){
	$return_array = array();
	$processed_files = WPTC_Factory::get('processed-files');
	wptc_manual_debug('', 'start_get_current_backup_progress');
	$processed_files->get_current_backup_progress($return_array);
	wptc_manual_debug('', 'end_get_current_backup_progress');
	send_response_wptc('progress', WPTC_DEFAULT_CRON_TYPE, $return_array);
}

function plugin_update_notice_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');
	$config->set_option('user_came_from_existing_ver', 0);
}

function update_sycn_db_view_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');
	$config->set_option('show_sycn_db_view_wptc', false);
}

function show_processing_files_view_wptc() {
	$config = WPTC_Factory::get('config');
	$config->set_option('show_processing_files_view_wptc', false);
}

// function update_test_connection_err_shown() {
// 	$config = WPTC_Factory::get('config');
// 	wptc_log(array(), '-----------cupdate_test_connection_err_shown-------------');
// 	$config->set_option('wptc_own_cron_status_notified', '1');
// }

function test_connection_wptc_cron() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	wptc_cron_status();
}

function wptc_cron_status($return = false){

	$config = WPTC_Factory::get('config');
	wptc_own_cron_status();
	$status = array();
	$cron_status = $config->get_option('wptc_own_cron_status');
	if (empty($cron_status)) {
		return false;
	}

	$cron_status = unserialize($cron_status);

	if ($cron_status['status'] == 'success') {
		$config->set_option('admin_notices', false);
		if ($return === 2) {
			return true;
		}
		$status['status'] = 'success';
	} else if(!empty($cron_status['is_different_url'])) {
		$config->set_option('stop_all_requests_to_node', true);
		$head = "<div> WPTC : Previously connected site url (<span id='wptc_old_connected_site_url'>" . $cron_status['old_url'] . "</span>) mismatches  with current site url (<span id='wptc_new_connected_site_url'>" . $cron_status['new_url'] . "</span>) in WP Time Capsule - ";
		$original_site = "<a  style ='cursor: pointer;' id='wptc_make_this_original_site'> Replace the original site </a>";
		$connector = "or";
		$fresh_site = "<a style ='cursor: pointer;' id='wptc_make_this_fresh_site' class='button-link-delete' > Sign up as a new site.</a>";
		$note = "<br> Note: Backup is paused on this site until you make an action <br> If you are not sure what went wrong, please email us at <a href='mailto:help@wptimecapsule.com?Subject=Contact' target='_top'>help@wptimecapsule.com</a> </div>";
		if (!WPTC_BACKWARD_DB_SEARCH) {
			$msg = $head . $fresh_site . $note;
		} else{
			$msg = $head . $fresh_site . $note;
		}
		set_admin_notices_wptc($msg, 'error', $strict_wptc_page = false, $do_not_delete = true);
		$status['status'] = 'success';
	} else {
		wptc_log($cron_status, '--------$cron_status--------');
		$config->set_option('admin_notices', false);
		if ($return === 2) {
			return false;
		}
		$status['status'] = 'failed';
		$status['status_code'] = $cron_status['statusCode'];
		$status['err_msg'] = $cron_status['body'];
		$status['cron_url'] = $cron_status['cron_url'];
		$status['ips'] = $cron_status['ips'];
	}
	if ($return == 1) {
		return $status;
	}

	wptc_die_with_json_encode( $status );
}

function save_initial_setup_data_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	if (!isset($_POST) && !isset($_POST['data'])) {
		return false;
	}
	$data = $_POST['data'];

	WPTC_Base_Factory::get('Wptc_ExcludeOption')->save_settings($data);

	$config = WPTC_Factory::get('config');

	$backup_slot 	 	= (isset($data['backup_slot'])) ? $data['backup_slot'] : WPTC_DEFAULT_BACKUP_SLOT;
	$schedule_time 		= (isset($data['schedule_time'])) ? $data['schedule_time'] : false;
	$timezone 			= (isset($data['timezone'])) ? $data['timezone'] : false;

	if (!empty($backup_slot)) {
		$config->set_option('backup_slot', $backup_slot);
	}

	if (!empty($schedule_time)) {
		$config->set_option('schedule_time_str', $schedule_time);
	}

	if (!empty($timezone)) {
		$config->set_option('wptc_timezone', $timezone);
	}

	if (!empty($exclude_extensions)) {
		$config->set_option('user_excluded_extenstions', strtolower($exclude_extensions) );
	}

	$config->set_option('backup_before_update_setting', 'always');

	if (!empty($data['database_encryption_settings']) && !empty($data['database_encryption_settings']['key'])) {
		$data['database_encryption_settings']['key'] = base64_encode($data['database_encryption_settings']['key']);
	}

	$config->set_option('database_encrypt_settings', serialize($data['database_encryption_settings']));

	$default_repo = $config->get_option('default_repo');
	$cloud_repo = WPTC_Factory::get($default_repo);
	$max_possible_rev_limit = 365;
	if($default_repo != 's3'){
		$max_possible_rev_limit = $cloud_repo->validate_max_revision_limit(365);
	}

	wptc_log($max_possible_rev_limit, "--------max_possible_rev_limit--during intital setup------");

	apply_filters('save_settings_revision_limit_wptc', $max_possible_rev_limit);

	wptc_modify_schedule_backup();
	dropbox_auth_check_wptc($return = true);

	$notice = apply_filters('check_requirements_auto_backup_wptc', '');

	if ($notice) {
		wptc_die_with_json_encode( array('status' => 'success', 'notice' => $notice ) );
	}

	wptc_die_with_json_encode( array('status' => 'success') );
}

function save_general_settings_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');
	$data = $_POST['data'];
	$config->set_option('anonymous_datasent', $data['anonymouse']);
	wptc_die_with_json_encode( array('status' => 'success') );
}

function save_advanced_settings_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');

	wptc_die_with_json_encode( array('status' => 'success') );
}

function save_backup_settings_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');
	$data = $_POST['data'];

	WPTC_Base_Factory::get('Wptc_ExcludeOption')->save_settings($data);

	$backup_slot = (isset($data['backup_slot'])) ? $data['backup_slot'] : WPTC_DEFAULT_BACKUP_SLOT;

	if (!empty($backup_slot)) {
		$config->set_option('old_backup_slot', $config->get_option('backup_slot'));
		$config->set_option('backup_slot', $backup_slot);
	}

	$backup_db_query_limit = (!empty($data['backup_db_query_limit'])) ? $data['backup_db_query_limit'] : WPTC_DEFAULT_DB_ROWS_BACKUP_LIMIT;

	if($backup_db_query_limit < 5){
		$backup_db_query_limit = WPTC_DEFAULT_DB_ROWS_BACKUP_LIMIT;
	}

	if (!empty($backup_db_query_limit)) {
		$config->set_option('backup_db_query_limit', $backup_db_query_limit);
	}

	if (!empty($data['database_encryption_settings']) && !empty($data['database_encryption_settings']['key'])) {
		$data['database_encryption_settings']['key'] = base64_encode($data['database_encryption_settings']['key']);
	}

	$config->set_option('database_encrypt_settings', serialize($data['database_encryption_settings']));

	$notice = apply_filters('check_requirements_auto_backup_wptc', '');

	if (!empty($data['revision_limit']) && !$notice ) {
		$notice = apply_filters('save_settings_revision_limit_wptc', $data['revision_limit']);
	}

	if(!empty($data['scheduled_time']) && !empty($data['timezone']) ){
		$config->set_option('wptc_timezone', $data['timezone']);
		$config->set_option('schedule_time_str', $data['scheduled_time']);
		wptc_modify_schedule_backup();
	}

	if ($notice) {
		wptc_die_with_json_encode( array('status' => 'success', 'notice' => $notice ) );
	}

	wptc_die_with_json_encode( array('status' => 'success') );
}

function resume_backup_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$status = wptc_cron_status(2);
	if ($status) {
		$push_result =wptc_resume_backup_call_to_server();
		wptc_log($push_result, '---------$push_result decoded------------');
		if(isset($push_result['msg']) && $push_result['msg'] === 'success'){
			wptc_log(array(), '---------come in------------');
			$options = WPTC_Factory::get('config');
			$options->set_option('recent_backup_ping', time());
			$response_arr['status'] = 'success';
		} else {
			wptc_cron_status();
		}
	} else {
		wptc_log(array(), '---------comes in else------------');
		wptc_cron_status();
	}
	die(json_encode($response_arr));
}


function wptc_resume_backup_call_to_server() {
	$config = WPTC_Factory::get('config');
	if ($config->get_option('wptc_server_connected')) {
		$app_id = $config->get_option('appID');

		$email = trim($config->get_option('main_account_email', true));
		$email_encoded = base64_encode($email);

		$pwd = trim($config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		$post_arr = array(
			'app_id' => $app_id,
			'email' => $email_encoded,
			'pwd' => $pwd_encoded,
		);

		$push_result = do_cron_call_wptc('users/resume', $post_arr);
		wptc_log($push_result, '---------$push_result------------');
		return json_decode($push_result, true);
	}
}

function is_backup_paused_wptc(){
	$current_status = wptc_reset_backup_if_long_time_no_ping(0, 1);
	return $current_status;
}

function proceed_to_pay_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$data = array();
	if (!empty($_POST['data'])) {
		$data = $_POST['data'];
	} else {
		wptc_die_with_json_encode( array('error' => 'Post Data is missing.') );
	}

	if (!empty($data['is_change_plan'])) {
		$data['sub_action'] = "process_update_subscription_from_plugin";
	} else {
		$data['sub_action'] = "process_subscription_from_plugin";
	}

	$data['current_site_url'] = WPTC_Factory::get('config')->get_option('site_url_wptc');
	$data['site_url'] = WPTC_Factory::get('config')->get_option('site_url_wptc');
	$data['email'] = WPTC_Factory::get('config')->get_option('main_account_email');
	$data['pwd'] = WPTC_Factory::get('config')->get_option('main_account_pwd');
	$data['password'] = WPTC_Factory::get('config')->get_option('main_account_pwd');
	$data['version'] = WPTC_VERSION;

	wptc_log($data, "--------post data----proceed_to_pay_wptc----");

	$rawResponseData = WPTC_Factory::get('config')->doCall(WPTC_USER_SERVICE_URL, $data, 20, array('normalPost' => 1));
	wptc_log($rawResponseData,'-----------$rawResponseData----------------');
	die($rawResponseData);
}

function save_manual_backup_name_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$processed_files = WPTC_Factory::get('processed-files');
	$processed_files->save_manual_backup_name_wptc($_POST['name']);
}

function wptc_check_cloud_in_auth_state(){
	$config = WPTC_Factory::get('config');
	$state = $config->get_option('oauth_state');
	$default_repo = $config->get_option('default_repo');
	if ($state === 'request' && $default_repo === 'dropbox') {
		send_response_wptc('CLOUD_IN_REQUEST_STATE');
	}
}

function clear_show_users_backend_errors_wptc(){

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$config = WPTC_Factory::get('config');
	$result = $config->set_option('show_user_php_error', false);
	if ($result) {
		die(json_encode(array('status' => 'success' )));
	}
	die(json_encode(array('status' => 'failed' )));
}

function windows_machine_reset_backups_wptc(){
	if(!is_windows_machine_wptc()){
		return false;
	}
	wptc_log(array(), '--------Yes windows machine--------');
	$backup = new WPTC_BackupController();
	if (is_any_ongoing_wptc_backup_process()) {
		$backup->stop();
	}
	reset_backup_related_settings_wptc();
	$backup->clear_prev_repo_backup_files_record($reset_inc_exc = true);
	$config = WPTC_Factory::get('config');
	$prev_date = $config->get_wptc_user_today_date_time('Y-m-d', (time() - 259200));
	wptc_log($prev_date, '--------$prev_date--------');
	$config->set_option('wptc_today_main_cycle', $prev_date);
}

function get_admin_notices_wptc(){

	if(apply_filters('is_whitelabling_enabled_wptc', '')){
		return false;
	}

	$config = WPTC_Factory::get('config');

	$notice = $config->get_option('admin_notices');

	if (empty($notice)) {
		
		return false;
	}

	$notice = unserialize($notice);

	if ($notice['do_not_delete']) {
		return $notice;
	}

	if(!$notice['strict_wptc_page']){
		$config->delete_option('admin_notices');

		return $notice;
	}

	if($_POST['is_wptc_page']){
		$config->delete_option('admin_notices');
	} else {
		$notice = array();
	}

	return $notice;
}

function make_this_fresh_site_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	WPTC_Base_Factory::get('Wptc_App_Functions')->make_this_fresh_site();
}

function make_this_original_site_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	WPTC_Base_Factory::get('Wptc_App_Functions')->make_this_original_site();
}


function login_request_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	$initial_setup  = WPTC_Base_Factory::get('Wptc_InitialSetup');
	$initial_setup->process_wptc_login();
}

function login_request_for_bulk_support_wptc($post_data){

	$initial_setup  = WPTC_Base_Factory::get('Wptc_InitialSetup');
	$initial_setup->process_bulk_setup_wptc_login(false, $post_data);
}

function update_bulk_settings_default_flags_wptc($post_data){
	if(!empty($post_data['cloud_creds']['schedule_time_str'])){
		WPTC_Factory::get('config')->set_option('schedule_time_str', $post_data['cloud_creds']['schedule_time_str']);
	}
	if(!empty($post_data['cloud_creds']['revision_limit'])){
		WPTC_Factory::get('config')->set_option('revision_limit', $post_data['cloud_creds']['revision_limit']);
	}
	if(!empty($post_data['cloud_creds']['wptc_timezone'])){
		WPTC_Factory::get('config')->set_option('wptc_timezone', $post_data['cloud_creds']['wptc_timezone']);
	}
}

function lazy_load_activity_log_wptc(){
	require_once ( WP_PLUGIN_DIR . '/wp-time-capsule/Classes/ActivityLog.php' );
	$list_table = new WPTC_List_Table();
	$list_table->lazy_load_activity_log();
}

function wptc_sync_purchase(){
	WPTC_Factory::get('config')->request_service(
				array(
					'email'           => false,
					'pwd'             => false,
					'return_response' => false,
					'sub_action' 	  => 'sync_all_settings_to_node',
					'login_request'   => true,
				)
			);
}

function decrypt_file_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	if (empty($_POST['data']['file'])) {
		wptc_die_with_json_encode( array('error' => 'File path is empty.') );
	}

	if (empty($_POST['data']['key'])) {
		wptc_die_with_json_encode( array('error' => 'Decryption key is empty.') );
	}

	$result = WPTC_Factory::get('config')->decrypt( WP_PLUGIN_DIR . '/' . WPTC_TC_PLUGIN_NAME . '/wp-tcapsule-bridge/upload/php/files/' . $_POST['data']['file'], $_POST['data']['key']);

	wptc_log($result,'-----------$result----------------');

	if (empty($result)) {
		wptc_die_with_json_encode(array('error' => 'Cannot not decrypt, Please try again.'));
	}

	WPTC_Factory::get('config')->set_option('recent_decrypted_file', $result['fullpath']);

	$result['message'] = "Decryption Completed. <a href=" . network_admin_url() . "?page=wp-time-capsule-settings&download=1#wp-time-capsule-tab-advanced>Download your file here</a>. After downloaded <a href='#' id='wptc-clear-all-decrypt-files'>click here</a> to delete the file for security reason.";

	wptc_die_with_json_encode($result);
}

function clear_all_decrypt_files_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	WPTC_Base_Factory::get('Wptc_App_Functions')->make_folders_empty(WP_PLUGIN_DIR . '/' . WPTC_TC_PLUGIN_NAME . '/wp-tcapsule-bridge/upload/php/files/');
	WPTC_Factory::get('config')->set_option('recent_decrypted_file', false);
	wptc_die_with_json_encode(array('status' => 'success'));
}

function prepare_file_upload_index_file_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	
	global $wp_filesystem;

	if (!$wp_filesystem) {
		initiate_filesystem_wptc();
		if (!$wp_filesystem) {
			wptc_die_with_json_encode( array('error' => 'Cannot initiate WordPress file system.') );
		}
	}

	$fs = $wp_filesystem;

	$contents_to_be_written = '
	<?php
	error_reporting(E_ALL | E_STRICT);
	require("UploadHandler.php");
	$upload_handler = new UploadHandler();
	';

	$index_like_file = WPTC_PLUGIN_DIR . 'wp-tcapsule-bridge/upload/php/index.php';

	$result = $fs->put_contents($index_like_file, $contents_to_be_written, 0644);

	if(empty($result)){
		wptc_die_with_json_encode( array('error' => 'Cannot perform upload. Write permissions required for "' . WPTC_PLUGIN_DIR . 'wp-tcapsule-bridge/upload/php/".') );

		return;
	}

	wptc_die_with_json_encode(array('status' => 'success'));
}

function delete_file_upload_index_file_wptc(){
	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
	
	global $wp_filesystem;

	if (!$wp_filesystem) {
		initiate_filesystem_wptc();
		if (!$wp_filesystem) {
			wptc_die_with_json_encode( array('error' => 'Cannot initiate WordPress file system.') );
		}
	}

	$fs = $wp_filesystem;

	$index_like_file = WPTC_PLUGIN_DIR . 'wp-tcapsule-bridge/upload/php/index.php';

	if ($fs->exists($index_like_file)) {
		$fs->delete($index_like_file);
	}

	wptc_die_with_json_encode(array('status' => 'success'));
}

function clear_upgrade_after_backup_flags_wptc(){
	WPTC_Factory::get('config')->set_option('start_upgrade_process', false);
	WPTC_Factory::get('config')->set_option('single_upgrade_details', false);
}

function download_recent_decrypted_file_wptc(){

	if (empty($_GET) || empty($_GET['page']) || empty($_GET['download']) || $_GET['page'] != 'wp-time-capsule-settings' || $_GET['download'] != 1) {
		return ;
	}

	$wptc_file_path = WPTC_Factory::get('config')->get_option('recent_decrypted_file');

	wptc_log($wptc_file_path,'-----------$wptc_file_path----------------');

	if (empty($wptc_file_path)) {
		return ;
	}

	include_once ( WPTC_PLUGIN_DIR . 'Views/wptc-download-file.php' );
}

function get_check_to_show_dialog_wptc() {

	WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

	$current_setting = WPTC_Factory::get('config')->get_option('backup_before_update_setting');

	if ($current_setting == 'always') {
		$backup_status['backup_before_update_setting'] = 'always';
	} else {
		$backup_status['backup_before_update_setting'] = 'everytime';
	}

	if (is_any_ongoing_wptc_restore_process() || is_any_ongoing_wptc_backup_process() || is_any_other_wptc_process_going_on()) {
		$backup_status['is_backup_running'] = 'yes';
	} else {
		$backup_status['is_backup_running'] = 'no';
	}

	wptc_die_with_json_encode( $backup_status );
}

function wptc_trigger_truncate_cron(){
	global $wpdb;
	
	$last_time = WPTC_Factory::get('config')->get_option('last_backup_time');
	$cur_time_adjusted = time();

	$timeThreshold = 86400;
	if(defined('TRIGGER_TABLE_CRON_DELETE_FREQUENCY')){
		$timeThreshold = TRIGGER_TABLE_CRON_DELETE_FREQUENCY;
	}

	if(defined('SHOW_QUERY_RECORDER_TABLE_SIZE_EXCEED_WARNING') && SHOW_QUERY_RECORDER_TABLE_SIZE_EXCEED_WARNING){
		$table_size = $wpdb->get_var("SELECT
			  (TABLE_ROWS * AVG_ROW_LENGTH)
			FROM
			  information_schema.TABLES
			WHERE
			    TABLE_SCHEMA='".DB_NAME."'
			  AND
			    TABLE_NAME='" . $wpdb->base_prefix . "wptc_query_recorder'
			ORDER BY
			  (DATA_LENGTH + INDEX_LENGTH)
			DESC;");

		wptc_log($table_size, "--------table_size--------");

		if($table_size > 419430400){
			set_admin_notices_wptc('Realtime query recorder table is big in size. Kindly ask your site administrator to check the query_recorder table. ', 'warning', false);
		}
	}

	if( $last_time && ($cur_time_adjusted - $last_time) > $timeThreshold ){

		wptc_log($timeThreshold, "--------must be deleteing trigger table--------");
		wptc_log($last_time, "--------last_time--------");

		$wpdb->query("DROP TABLE `" . $wpdb->base_prefix . "wptc_query_recorder`");

		$cachecollation = wptc_get_collation();

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->base_prefix . "wptc_query_recorder` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`query` longtext NOT NULL,
			`table_name` text  NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB " . $cachecollation . " ;");
	}
}

// wptc_trigger_truncate_cron();

function initiate_check_and_truncate_trigger_tables_hook(){
	if ( ! wp_next_scheduled( 'wptc_trigger_truncate_cron_hook' ) ) {
		wptc_log('', "--------initiate_check_and_truncate_trigger_tables_hook---hourly-----");
	    wp_schedule_event( time(), 'hourly', 'wptc_trigger_truncate_cron_hook' );
	}
}


