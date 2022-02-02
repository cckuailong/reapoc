<?php
/*
------time capsule------
1.this file is totally used to move the files from the tempFolder to the actual root folder of wp
2.this file uses files from wordpress and also plugins to perform the copying actions
 */
/**
* created by thamaraiselvam
* created on 28-06-2017
*/

class WPTC_Copy{
	private $common_include_files;
	private	$restore_app_functions;
	private	$config;
	private	$restore_id;
	private	$file_iterator;
	private	$processed_files;
	private	$copied_files_count;
	private	$state_files_count;
	private	$utils_base;
	private	$post_data;
	const SELECT_QUERY_LIMIT = 300;

	const WPTC_PLUGIN_URL = 'https://downloads.wordpress.org/plugin/wp-time-capsule.zip';

	function __construct(){

		//create object for restore app common
		$this->init_restore_app_functions();

		//accept only wptc requests
		$this->restore_app_functions->verify_request();

		$this->restore_app_functions->set_default_timezone();

		//enable PHP errors
		$this->restore_app_functions->enable_php_errors();

		//set global starting time
		$this->restore_app_functions->start_request_time();

		//define restore constant to override other functions
		$this->restore_app_functions->define_constants();

		//restore needed files
		$this->include_files();

		//start database connections
		$this->connect_db();

		$this->restore_app_functions->init_other_objects();

		$this->restore_app_functions->init_other_functions();

		//assume this is request from server
		set_server_req_wptc();

		//Init WP File system
		$this->set_fs();

		$this->config = WPTC_Factory::get('config');

		$this->log_recent_calls();

		$this->setup_restore();

		$this->check_request();

		$this->restore_app_functions->init_log_files();

		//start db dump and copy files
		$this->process_files();
	}

	private function delete_empty_folders($source){
		wptc_log(func_get_args(), __FUNCTION__);

		if (empty($source)) {
			return false;
		}

		$file_obj = $this->file_iterator->get_files_obj_by_path($source, false);

		foreach ($file_obj as $file_meta) {

			$path = $file_meta->getPathname();

			$path = wp_normalize_path($path);

			if (!wptc_is_dir($path)) {
				continue;
			}

			if(!$this->file_iterator->is_empty_folder($path)){
				wptc_log($path, '---------------Not empty-----------------');
				continue;
			}

			wptc_wait_for_sometime();

			wptc_log($path, '---------------Deleted-----------------');
			$this->fs->delete($path, true);
		}

	}

	public function include_files(){
		require_once dirname(__FILE__). '/' ."common_include_files.php";
		$this->common_include_files = new Common_Include_Files('wptc-copy');
		$this->common_include_files->init();
	}

	private function init_restore_app_functions(){
		//common app functions for both ajax and tc-init
		require_once dirname(__FILE__). '/' ."wptc-restore-app-functions.php";
		$this->restore_app_functions = new WPTC_Restore_App_Functions();
	}

	private function connect_db(){
		$this->wpdb = $this->restore_app_functions->init_db_connection();
	}

	private function set_fs(){
		$this->fs = $this->restore_app_functions->init_file_system();
	}

	private function log_recent_calls(){
		$this->config->set_option('recent_restore_ping', time());
	}

	private function setup_restore(){
		$this->restore_id = $this->config->get_option('restore_action_id');
		$this->restore_app_functions->define('WPTC_SITE_ABSPATH', $this->config->get_option('site_abspath'));
		$this->file_iterator = new WPTC_File_Iterator();
		wptc_set_fallback_db_search_1_14_0();
		wptc_setlocale();
	}

	private function check_request(){
		$this->post_data = $this->restore_app_functions->decode_request();
	}

	private function process_files(){

		wptc_log(array(),'-----------COPY REQUESTED----------------');

		wptc_manual_debug('', 'start_wptc_copy');

		//update the options table to indicate that bridge process is going on , only on the first call
		$this->reset_flags();

		$this->is_restore_completed();

		$this->restore_app_functions->set_additional_flags();
		$this->restore_app_functions->set_old_prefix_restore_to_staging();

		$restore_temp_folder = $this->config->get_backup_dir(true) . '/' . WPTC_TEMP_DIR_BASENAME;

		$this->restore_full_db_file($restore_temp_folder);

		$this->restore_partial_db_query_files();

		$this->restore_app_functions->enable_maintenance_mode();

		$this->utils_base = new Utils_Base();

		$this->set_copied_files_count();
		// $this->processed_files = WPTC_Factory::get('processed-restoredfiles', true);

		if($this->config->get_option('is_bridge_restore') && !$this->config->get_option('fresh_wptc_plugin_downloaded')){
			$this->download_fresh_wptc_plugin($restore_temp_folder . '/' . WPTC_WP_CONTENT_BASENAME . '/plugins/' );
			$this->config->set_option('fresh_wptc_plugin_downloaded', true);
		}

		$full_copy_result = true;

		wptc_log($restore_temp_folder,'-----------$restore_temp_folder----------------');

		if(!$this->config->get_option('copy_files_wp_content')){
			wptc_manual_debug('', 'copy_files_wp_content');
			$full_copy_result = $this->move_dir($restore_temp_folder . '/' . basename(WPTC_WP_CONTENT_DIR) , WPTC_WP_CONTENT_DIR);
			wptc_manual_debug('', 'copy_files_wp_content');
			$this->config->set_option('copy_files_wp_content', true);
		}

		if(!$this->config->get_option('copy_files_abspath')){
			wptc_manual_debug('', 'copy_files_abspath');
			$full_copy_result = $this->move_dir($restore_temp_folder, ABSPATH);
			wptc_manual_debug('', 'copy_files_abspath');
			$this->config->set_option('copy_files_abspath', true);
		}


		$this->restore_app_functions->restore_to_staging_replace_links();
		$this->restore_app_functions->migration_replace_links();

		$this->restore_app_functions->recreate_triggers();

		$this->set_state_files_count();

		wptc_manual_debug('', 'start_deleting_state_files');
		$full_copy_result = $this->check_and_delete_state_files($restore_temp_folder);
		wptc_manual_debug('', 'end_deleting_state_files');

		$this->restore_app_functions->disable_maintenance_mode();

		if (!empty($full_copy_result) && is_array($full_copy_result) && array_key_exists('error', $full_copy_result)) {
			$this->restore_app_functions->die_with_msg($full_copy_result);
		} else {
			//if we set this value as false ; then the bridge process for copying is completed
			$this->config->set_option('is_bridge_process', false);
			$this->restore_complete();
		}

		$this->is_restore_completed();
	}

	private function restore_full_db_file($restore_temp_folder){

		$full_db_file = $this->get_full_db_file($restore_temp_folder);

		wptc_log($full_db_file,'----------restore_full_db_file--------------');

		//check if the db restore process is already completed
		if (!$this->config->get_option('restore_full_db_process')) {
			wptc_wait_for_sometime();
			if ($this->fs->exists($full_db_file)) {
				$this->fs->delete($full_db_file);

				wptc_log('', "--------full_db_file_deleted--------");

			}
			return ;
		}

		//check if the sql file is selected during restore process, if it doesnt exist then we dont need to do the restore db process
		if (!$this->fs->exists($full_db_file)) {
			wptc_log(array(), '-----------Full Sql db file not found in this restore-------------');

			$this->config->set_option('restore_full_db_process', false);
			$this->config->set_option('restore_db_index', 0);

			// exit;
			
			$this->restore_app_functions->die_with_msg('wptcs_callagain_wptce');
		}

		$this->restore_sql_file_common($full_db_file, $type = 'full');
	}

	private function get_full_db_file($restore_temp_folder){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$site_db_name = $this->config->get_option('site_db_name');

		$tmp_path = $this->restore_app_functions->convert_abspath_dir_to_temp_dir_path($restore_temp_folder);

		$restore_db_dump_dir = $tmp_path . '/backups/';

		$restore_db_dump_file = $restore_db_dump_dir . $site_db_name . '-backup.sql';

		wptc_log($restore_db_dump_file,'-----------$restore_db_dump_file----------------');

		if (file_exists($restore_db_dump_file . '.gz.crypt')) {
			return $restore_db_dump_file . '.gz.crypt';
		}

		if (file_exists($restore_db_dump_file . '.gz')) {
			return $restore_db_dump_file . '.gz';
		}

		if (file_exists($restore_db_dump_file . '.crypt')) {
			return $restore_db_dump_file . '.crypt';
		}

		return $restore_db_dump_file;
	}

	private function restore_partial_db_query_files(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if( !$this->config->get_option('is_realtime_partial_query_restore') && !$this->config->get_option('is_latest_restore_point') ){
			wptc_log(array(),'-----------This is not a partial db query backups----------------');
			return ;
		}

		if($this->config->get_option('restore_partial_db_process')){
			wptc_log(array(),'-----------restore_partial_db_process done already----------------');
			return ;
		}

		$partial_db_file = $this->get_partial_db_file();

		wptc_log($partial_db_file,'-----------$partial_db_file----------------');

		if (empty($partial_db_file)) {
			return ;
		}

		$this->restore_sql_file_common($partial_db_file, $type = 'partial');
	}

	private function get_partial_db_file(){

		$realtime_temp_folder = $this->config->get_backup_dir(true) . '/' . WPTC_TEMP_DIR_BASENAME;;

		$realtime_temp_folder = $this->restore_app_functions->convert_abspath_dir_to_temp_dir_path($realtime_temp_folder);

		$realtime_temp_folder = substr($realtime_temp_folder, 0, -(strlen(WPTC_TEMP_DIR_BASENAME)));

		$realtime_temp_folder .= WPTC_REALTIME_DIR_BASENAME;

		$sql_files = $this->collect_all_partial_db_files($realtime_temp_folder);
		wptc_log($sql_files,'-----------$sql_files 1----------------');

		if (empty($sql_files)) {
			if ( $this->config->get_option('is_latest_restore_point') && !$this->config->get_option('is_latest_restore_point_query_executed') ) {
				$this->import_triggers_saved_queries();
			}
		}

		if (empty($sql_files)) {
			wptc_log(array(),'-----------SQL empty----------------');
			$this->config->set_option('restore_partial_db_process', true);
			return false;
		}

		//make query files in order
		natsort($sql_files);

		//reset the index
		$sql_files = array_values($sql_files);

		wptc_log($sql_files,'-----------$sql_files----------------');

		//send first file for restore
		return $sql_files[0];
	}

	private function get_live_site_realtime_tmp_dir(){
		$realtime_folder = $this->config->get_backup_dir(true);
		$realtime_folder .= '/' .WPTC_REALTIME_DIR_BASENAME;

		return $realtime_folder;
	}

	private function collect_all_partial_db_files($path){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$files_obj = $this->file_iterator->get_files_obj_by_path($path, $recursive = 1);

		if (empty($files_obj)) {
			return array();
		}

		$sql_files = array();

		foreach ($files_obj as $file_meta) {
			$file_path = $file_meta->getPathname();

			$file_path = wp_normalize_path($file_path);

			if ( !$file_meta->isReadable() || strstr($file_path, 'wptc_saved_queries') === false) {
				wptc_log($file_path,'-----------Not readable or not saved query----------------');
				continue;
			}

			//add at the end of array after sort on latest restore point
			// if ( strstr($file_path, 'wptc-secret') !== false) {
				// wptc_log($file_path,'-----------Not downloaded file so skip----------------');
				// continue;
			// }
			$sql_files[] = $file_path;
		}

		return $sql_files;
	}

	private function restore_sql_file_common($restore_db_dump_file, $type ){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->restore_app_functions->enable_maintenance_mode();

		wptc_manual_debug('', "start_" . basename($restore_db_dump_file) . "_db_restore");

		$db_restore_result = $this->database_restore($restore_db_dump_file);

		wptc_log($db_restore_result,'-----------$db_restore_result----------------');

		wptc_manual_debug('', "end_" . basename($restore_db_dump_file) . "_db_restore");

		if (!$db_restore_result) {
			$this->handle_restore_error_wptc($this->config);
			$err_obj = array();
			$err_obj['restore_db_dump_file'] = $restore_db_dump_file;
			$err_obj['mysql_error'] = $this->wpdb->last_error;
			$err = array('error' => $err_obj);
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
			$this->restore_app_functions->die_with_msg($err);
		}

		//on db restore completion - set the following values
		if ($type === 'full') {
			$this->config->set_option('restore_full_db_process', false);
		}

		$this->config->set_option('restore_db_index', 0);
		$this->config->set_option('sql_gz_uncompression', false);
		$this->config->set_option('restore_database_decrypted', false);

		$restore_db_dump_file = $this->restore_app_functions->remove_gz_ext_from_file($restore_db_dump_file);
		wptc_wait_for_sometime();
		//delete the sql file then carryout the copying files process
		if ($this->fs->exists($restore_db_dump_file)) {
			@unlink($restore_db_dump_file);
			if ($this->fs->exists($restore_db_dump_file)) {
				$this->fs->delete($restore_db_dump_file);
			}
		}

		$this->restore_app_functions->die_with_msg('wptcs_callagain_wptce');
	}

	private function check_and_delete_state_files($restore_temp_folder){

		$state_file = $restore_temp_folder . '/backups/wptc_current_files_state.txt';

		wptc_log($state_file, '---------------$state_file--check_and_delete_state_files---------------');

		if (!file_exists($state_file)) {
			$state_file = ABSPATH . 'backups/wptc_current_files_state.txt';

			wptc_log($state_file, '---------------new_state_file--check_and_delete_state_files---------------');

			wptc_log(array(), '--------------state--File not exists----one------------');
		}

		if (!file_exists($state_file)) {

			wptc_log(array(), '-------------state---File not exists----two------------');

			return ;
		}

		$handle = fopen($state_file, "rb");

		if (empty($handle)) {
			wptc_log(array(), '----------------cannot state open file----------------');

			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
			$this->restore_app_functions->die_with_msg(array('error' => 'Cannot state open database file'));
		}

		$loop_iteration = 0;

		while (($file = fgets($handle)) !== false) {

			$loop_iteration++;

			if ($loop_iteration <= $this->state_files_count ) {
				continue; //check index; if it already processed ; then continue;
			}

			wptc_manual_debug('', 'during_deleting_state_files', 100);

			$file = str_replace("\n", '', $file);

			if (empty($file)) {

				wptc_log('', "--------empty file-list---check_and_delete_state_files----");

				continue;
			}

			$file = wptc_add_fullpath($file);

			if (!$this->fs->exists($file)) {
				wptc_log(array(), '----------------File not found----------------');
				continue;
			}

			wptc_wait_for_sometime();

			wptc_log($file, "--------deleting state file--------");

			$result = $this->fs->delete($file);

			if (!$result) {
				wptc_log(error_get_last(), '---------------error_get_last()-----------------');
			}

			if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->config->set_option('restore_state_files_count', $loop_iteration);
			$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
		}

	}

	private function set_state_files_count(){
		$count = $this->config->get_option('restore_state_files_count');
		$this->state_files_count = ($count) ? $count : 0 ;
		wptc_log($this->state_files_count, '---------------$this->state_files_count-----------------');
	}

	private function set_copied_files_count(){
		$count = $this->config->get_option('restore_copied_files_count');
		$this->copied_files_count = ($count) ? $count : 0 ;
	}

	private function move_dir($source, $destination = '') {

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$source 	 = trailingslashit($source);
		$destination = trailingslashit($destination);

		$file_obj = $this->file_iterator->get_files_obj_by_path($source, true);

		foreach ($file_obj as $file) {

			$source_file = $file->getPathname();

			$source_file = wp_normalize_path($source_file);

			// wptc_log($source_file,'-----------$source_file----------------');

			if (wptc_is_dir($source_file)) {
				continue;
			}

			$this->copied_files_count++;

			$destination_file = str_replace($source, $destination, $source_file);

			if (!$this->move_file($source_file, $destination_file, true)) {

				$this->fs->chmod($destination_file, 0644);

				if (!$this->move_file($source_file, $destination_file, true)) {
					$file_err['error'] = 'cannot move file';
					$file_err['file'] = $destination_file;
					$this->restore_app_functions->log_data('files', $file_err);
				}
			}

			if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->config->set_option('restore_copied_files_count', $this->copied_files_count);
			$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
		}

		return true;
	}

	private function move_file($source, $destination, $overwrite = true) {

		wptc_manual_debug('', 'during_copy_files', 100);

		$this->utils_base->createRecursiveFileSystemFolder(dirname($destination));

		if (!file_exists($source)) {
			wptc_log($source, '--------------Source not found------------------');
			return false;
		}

		$result = $this->fs->move($source, $destination, $overwrite);

		if (!$result) {
			wptc_log(error_get_last(), '---------------error_get_last()-----------------');
			return false;
		}

		return true;
	}

	private function reset_flags(){
		if (empty($this->post_data) || empty($this->post_data['initialize']) || $this->post_data['initialize'] != true) {
			return false;
		}

		$this->config->set_option('is_bridge_process', true);
		$this->config->set_option('restore_db_index', 0);
		$this->config->set_option('restore_saved_index', 0);
		$this->config->set_option('restore_full_db_process', true);
		$this->config->set_option('restore_partial_db_process', false);
	}

	private function is_restore_completed(){
		if (!$this->config->get_option('is_bridge_process') && !$this->config->get_option('garbage_deleted')) {
			$this->restore_app_functions->send_report_data($this->restore_id, 'SUCCESS');

			wptc_log('', "--------die_with_msg--here --4----");

			$this->restore_app_functions->die_with_msg('wptcs_over_wptce');
		}
	}

	private function restore_complete($error = false) {

		wptc_manual_debug('', 'start_delete_empty_folders');

		$this->delete_empty_folders(WP_CONTENT_DIR . '/plugins'); //Remove invalid plugins
		$this->delete_empty_folders(WP_CONTENT_DIR . '/themes'); //Remove invalid themes

		wptc_manual_debug('', 'end_delete_empty_folders');

		$this->restore_app_functions->disable_maintenance_mode();

		$this->config->set_option('restore_completed_notice', 'yes');


		//delete the bridge files on completion
		$this->delete_bridge_folder();

		$this->config->set_option('in_progress_restore', false);
		$this->config->set_option('is_running_restore', false);
		$this->config->set_option('cur_res_b_id', false);
		$this->config->set_option('start_renaming_sql', false);
		$this->config->set_option('restore_db_index', false);
		$this->config->set_option('got_files_list_for_restore_to_point', false);
		$this->config->set_option('live_files_to_restore_table', false);
		$this->config->set_option('recorded_files_to_restore_table', false);
		$this->config->set_option('is_realtime_partial_query_restore', false);
		$this->config->set_option('restore_partial_db_process', false);
		$this->config->set_option('is_deleted_all_future_files', false);
		$this->config->set_option('selected_files_temp_restore', false);
		$this->config->set_option('selected_backup_type_restore', false);
		$this->config->set_option('got_selected_files_to_restore', false);
		$this->config->set_option('not_safe_for_write_files', false);
		$this->config->set_option('recorded_this_selected_folder_restore', false);
		$this->config->set_option('recent_restore_ping', false);
		$this->config->set_option('is_bridge_process', false);
		$this->config->set_option('get_recorded_files_to_restore_table', false);
		$this->config->set_option('restore_current_action', false);
		$this->config->set_option('sql_gz_uncompression', false);
		$this->config->set_option('restore_copied_files_count', false);
		$this->config->set_option('restore_state_files_count', false);
		$this->config->set_option('copy_files_wp_content', false);
		$this->config->set_option('copy_files_abspath', false);
		$this->config->set_option('restore_downloaded_files_count', false);
		$this->config->set_option('delete_future_files_offset', false);
		$this->config->set_option('is_restore_to_staging', false);
		$this->config->set_option('replace_collation_for_this_restore', false);
		$this->config->set_option('restore_to_staging_details', false);
		$this->config->set_option('R2S_replace_links', false);
		$this->config->set_option('restore_deep_links_completed', false);
		$this->config->set_option('is_latest_restore_point', false);
		$this->config->set_option('is_bridge_restore', false);
		$this->config->set_option('latest_restore_point_query_offset', false);
		$this->config->set_option('is_latest_restore_point_query_executed', false);
		$this->config->set_option('added_state_file_to_restore', false);
		$this->config->set_option('state_file_downloaded_in_restore', false);
		$this->config->set_option('deleted_state_files_from_download_list', false);
		$this->config->set_option('delete_state_files_from_download_list_offset', false);
		$this->config->set_option('refresh_triggers_on_restore', false);
		$this->config->set_option('tried_to_create_triggers_after_restore', false);
		$this->config->set_option('restore_database_decrypted', false);
		$this->config->set_option('fresh_wptc_plugin_downloaded', false);
		$this->config->set_option('migration_replaced_links', false);
		$this->config->set_option('migration_url', false);
		$this->config->set_option('same_server_replace_old_url_data', false);
		$this->config->set_option('same_server_replace_url_multicall_status', false);

		$this->config->set_option('restore_is_multisite', false);
		$this->config->set_option('restore_multisite_upload_dir', false);
		$this->config->set_option('restore_multisite_base_prefix', false);
		$this->config->set_option('restore_multisite_current_prefix', false);


		$this->config->reset_complete_flags();

		//Set restore is completed reference for next backup
		$this->config->set_option('take_full_backup_once', true);

		$processed_restore = new WPTC_Processed_Restoredfiles();
		$processed_restore->truncate();
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");

		$this->config->remove_garbage_files(array('is_restore' => true));
		wptc_manual_debug('', 'remove_garbage_files');

		if (!empty($error)) {
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->die_with_msg($error);
		}

		$setup_fresh_site_coz_migration = $this->config->get_option('setup_fresh_site_coz_migration');
		if($setup_fresh_site_coz_migration){
			WPTC_Base_Factory::get('Wptc_App_Functions')->run_deactivate_plugin('wp-time-capsule/wp-time-capsule.php', $this->wpdb->base_prefix);
			$this->delete_all_wptc_related_tables_bridge();
		}

		$this->restore_app_functions->send_report_data($this->restore_id, 'SUCCESS');

		$failure_data = $this->restore_app_functions->get_failure_data();

		wptc_manual_debug('', 'restore_complete');

		if (empty($failure_data)) {

			wptc_log('', "--------die_with_msg--here --5----");

			$this->restore_app_functions->die_with_msg('wptcs_over_wptce');
		} else {

			wptc_log('', "--------die_with_msg--here --6----");

			$this->restore_app_functions->die_with_msg(array('status' => 'wptcs_over_wptce', 'failure_data' => $failure_data) );
		}
	}

	public function delete_all_wptc_related_tables_bridge(){
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_activity_log`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_backups`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_current_process`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_options`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_inc_exc_contents`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_processed_files`");
		$this->wpdb->query("DROP TABLE IF EXISTS `" . $this->wpdb->base_prefix . "wptc_processed_restored_files`");
	}

	private function delete_bridge_folder(){

		$backup_db_path = wptc_get_tmp_dir();
		$backup_db_path = $this->config->wp_filesystem_safe_abspath_replace($backup_db_path. WPTC_REALTIME_DIR_BASENAME .'/wptc_saved_queries_restore.sql');

		wptc_wait_for_sometime();

		if ($this->fs->exists($backup_db_path)) {
			$this->fs->delete($backup_db_path);
		}
	}

	private function handle_restore_error_wptc() {
		$this->config->remove_garbage_files(array('is_restore' => true));
		$this->config->set_option('restore_full_db_process', false);
		$this->config->set_option('is_bridge_process', false);
		$this->config->set_option('restore_db_index', 0);
		$this->restore_complete('Restoring DB error.');
	}

	private	function database_restore($file_name) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$file_name = $this->decrypt($file_name);

		wptc_log($file_name,'-----------$file_name after decrypt----------------');

		$file_name = $this->uncompress($file_name);

		$prev_index = $this->config->get_option('restore_db_index');

		$this->restore_app_functions->set_wptc_sql_mode_variables();
		$response = $this->restore_app_functions->import_sql_file($file_name, $prev_index);
		$this->restore_app_functions->reset_wptc_sql_mode_variables();

		// exit;

		wptc_log($response, '--------database_restore response--------');

		if (empty( $response ) || empty($response['status']) || $response['status'] === 'error') {
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
			$err = $response['status'] === 'error' ? $response['msg'] : 'Unknown error during database import';
			$this->restore_app_functions->die_with_msg(array('error' => $err));
		}

		if ($response['status'] === 'continue') {
			$this->config->set_option('restore_db_index', $response['offset']); //updating the status in db for each 10 lines
			$this->restore_app_functions->die_with_msg('wptcs_callagain_wptce');
		}

		if ($response['status'] === 'completed') {
			return true;
		}

	}

	private function is_unwanted_query_staging($req_query){
		$queries = array('CREATE DATABASE IF NOT EXISTS ', 'USE ');
		foreach ($queries as $query) {
			if (stripos($req_query, $query) !== FALSE) {
				return true;
			}
		}
		return false;
	}

	public function uncompress($file){


		//Return original sql file for normal sql file or compression completed file.
		if( strpos($file, '.gz') === false 
			|| $this->config->get_option('sql_gz_uncompression') ){

			wptc_log(array(), '--------Either compression done or file is not compressed--------');

			return $this->restore_app_functions->remove_gz_ext_from_file($file);
		}

		wptc_log(array(), '---------------Uncompressing file-----------------');

		if ( !$this->restore_app_functions->is_gzip_available() ) {
			$this->config->set_option('sql_gz_uncompression', true);
			$this->restore_app_functions->die_with_msg(array('error' => 'gzip not installed on this server so could not uncompress the sql file'));
		}

		wptc_manual_debug('', 'start_uncompress_db');

		$this->restore_app_functions->gz_uncompress_file($file, $offset = 0);
		$this->config->set_option('sql_gz_uncompression', true);
		return $this->restore_app_functions->remove_gz_ext_from_file($file);
	}

	private function download_fresh_wptc_plugin($destination){
		if (WPTC_ENV !== 'production') {
			return ;
		}

		$this->utils_base->createRecursiveFileSystemFolder($destination);
		$file_path = $destination . 'wp-time-capsule.zip';
		$result = $this->download_URL( self::WPTC_PLUGIN_URL, $file_path );
		$this->extract_zip($file_path, $destination);

		wptc_wait_for_sometime();

		if (file_exists($file_path)) {
			unlink($file_path);
		}
	}

	private function download_URL($URL, $filePath){
		return ($this->curl_download_URL($URL, $filePath) || $this->fopen_download_URL($URL, $filePath));
	}

	private	function extract_zip($backup_file, $temp_unzip_dir){
		$archive   = new WPTCPclZip($backup_file);
		$extracted = $archive->extract(WPTC_PCLZIP_OPT_PATH, $temp_unzip_dir, WPTC_PCLZIP_OPT_TEMP_FILE_THRESHOLD, 1);

		// wptc_log($extracted,'-----------$extracted----------------');

		if (!$extracted || $archive->error_code) {
			wptc_log('Error: Failed to extract fresh wptc plugin (' . $archive->error_string . ')' ,'---------- Failed to extract fresh wptc plugin-----------------');
			return false;
		}

		return true;
	}

	private function curl_download_URL($URL, $filePath){

		$fp = fopen ($filePath, 'w');

		if ($fp === false) {
			return false;
		}

		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 180);
		curl_setopt($ch, CURLOPT_FILE, $fp);

		if (!ini_get('safe_mode') && !ini_get('open_basedir')){
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}

		$callResponse = curl_exec($ch);

		curl_close($ch);
		fclose($fp);

		if($callResponse == 1){
			return true;
		}

		return false;
	}

	private function fopen_download_URL($URL, $filePath){

		if (!function_exists('ini_get') || ini_get('allow_url_fopen') != 1) {
			return false;
		}

		$src = @fopen($URL, "r");
		$dest = @fopen($filePath, 'wb');
		if(!$src || !$dest){
			return false;
		}

		while ($content = @fread($src, 1024 * 1024)) {
			@fwrite($dest, $content);
		}

		@fclose($src);
		@fclose($dest);
		return true;
	}

	private function import_triggers_saved_queries(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ($this->config->get_option('is_latest_restore_point_query_executed')) {
			wptc_log(array(),'-----------import_triggers_saved_queries alreay completed----------------');
			return true;
		}

		$table_backup_stat = $this->config->get_option('latest_restore_point_query_offset');
		$offset            = empty($table_backup_stat) ? 0 : $table_backup_stat ;
		$loop_iteration    = $offset;

		wptc_log($offset,'-----------$offset----------------');

		$table  = WPTC_Base_Factory::get('Trigger_Init')->get_trigger_query_tablename();;
		$column = 'query';

		$table_count = $this->wpdb->get_var("SELECT COUNT(*) FROM $table");

		wptc_log($table_count,'-----------$table_count----------------');

		if ($table_count == 0) {
			$this->config->set_option('is_latest_restore_point_query_executed', true);
			return true;
		}

		for ($i = $offset; $i < $table_count; $i = $i + self::SELECT_QUERY_LIMIT) {

			$table_data = $this->wpdb->get_results("SELECT $column FROM $table LIMIT " . self::SELECT_QUERY_LIMIT . " OFFSET $i");

			if ($table_data === false || empty($table_data)) {
				$this->restore_app_functions->log_data('queries', "#Cannot restore latest trigger queries\n");
				$this->config->set_option('is_latest_restore_point_query_executed', true);
				return true;
			}

			foreach ($table_data as $row) {
				$loop_iteration++;
				$result = $this->wpdb->query($row->$column);
				if ($result === false) {
					wptc_log($row->$column,'-----------$row->$column----------------');
					wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
					$this->restore_app_functions->log_data('queries', $row->$column);
				}
			}

			if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->wpdb->query('UNLOCK TABLES;');
			$this->config->get_option('latest_restore_point_query_offset', $loop_iteration);
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->die_with_msg('wptcs_callagain_wptce');
		}

		$this->config->set_option('is_latest_restore_point_query_executed', true);
		return true;
	}

	private function decrypt($file){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (strstr($file, '.gz.crypt') === false) {
			$this->config->set_option('restore_database_decrypted', true);
			return $file;
		}

		if($this->config->get_option('restore_database_decrypted')){
			return str_replace('.crypt', '', $file);
		}

		$key = $this->config->get_database_encryption_settings('key');

		wptc_log($key,'-----------$key----------------');

		if (empty($key)) {
			WPTC_Factory::get('logger')->log("Database Encryption Phrase is empty, cannot decrypt without key so database restore failed.", 'restores', $this->restore_id);
			$this->restore_app_functions->die_with_msg(array('error' => 'Database Encryption Phrase is empty, cannot decrypt without key so database restore failed.'));
		}

		$response = $this->config->decrypt($file, $key);

		if (empty($response) || empty($response['fullpath']) ) {
			$this->restore_app_functions->die_with_msg(array('error' => 'Database Encryption failed.'));
		}

		return $response['fullpath'];
	}

}

new WPTC_Copy();
