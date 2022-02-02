<?php
/**
* A class with functions the perform a backup of WordPress
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

class WPTC_BackupController {
	private $dropbox,
			$config,
			$output,
			$processed_file_count,
			$WptcAutoBackupHooksObj,
			$exclude_class_obj,
			$file_iterator,
			$seek_file_iterator,
			$is_auto_backup,
			$processed_files,
			$app_functions,
			$current_iterator_table,
			$tcapsule_path,
			$logger,
			$db_backup,
			$file_list,
			$wpdb;

	public static function construct() {
		return new self();
	}

	public function __construct($output = null) {
		$this->config = WPTC_Factory::get('config');
		$this->dropbox = WPTC_Factory::get(DEFAULT_REPO);
		$this->output = $output ? $output : WPTC_Extension_Manager::construct()->get_output();
		$this->exclude_class_obj = WPTC_Base_Factory::get('Wptc_ExcludeOption');
		$this->current_iterator_table = new WPTC_Processed_iterator();
		$this->processed_files = WPTC_Factory::get('processed-files');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->logger = WPTC_Factory::get('logger');
		$this->tcapsule_path = 	wptc_get_tmp_dir() . '/' . WPTC_TEMP_DIR_BASENAME;
		$this->db_backup = WPTC_Factory::get('databaseBackup');
		$this->file_list = WPTC_Factory::get('fileList');
		$this->init_db();
	}

	public function init_file_iterator(){
		$this->file_iterator = new WPTC_File_Iterator();
		$this->seek_file_iterator = new WPTC_Seek_Iterator($this, $type = 'BACKUP', 100);
	}

	public function init_db(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	private function set_is_auto_backup(){
		$this->is_auto_backup = apply_filters( 'is_auto_backup_running_wptc', '' );
	}

	public function backup_path($path, $always_include_file = null, $backup_id = null) {

		// wptc_log('', "--------backup_path()--coming------");

		if (!$this->config->get_option('in_progress')) {
			wptc_log($account_info, "--------exiting by !in_progress--------");
			return 'ignored';
		}

		if ($this->config->get_option('in_progress_restore')) {
			wptc_log($account_info, "--------exiting by !in_restore_progress--------");
			return 'ignored';
		}

		$dropbox_path = wptc_get_sanitized_home_path();
		$dropbox_path = wp_normalize_path($dropbox_path);
		$current_processed_files = array();

		$this->processed_file_count = $this->processed_files->get_processed_files_count();

		if (!file_exists($path)) {
			return false;
		}

		if (!$this->config->get_option('gotfileslist', true)) {

			wptc_log(array(), '---------Got list false------------');

			global $iterator_files_count_this_call;
			$iterator_files_count_this_call = 0;

			$this->logger->log(__("Starting File List Iterator.", 'wptc'), 'backups', $backup_id);

			$this->wpdb->query("SET @@auto_increment_increment=1");

			wptc_manual_debug('', 'start_backup_file_iterator');

			$this->iterate_files();

			wptc_manual_debug('', 'end_backup_file_iterator');

			$this->logger->log(__("Ending File List Iterator.", 'wptc'), 'backups', $backup_id);

		}

		wptc_manual_debug('', 'start_upload_files');

		global $current_process_file_id;
		$current_process_file_id = $this->config->get_option('current_process_file_id');

		wptc_log($current_process_file_id, '---------$current_process_file_id------------');

		if (empty($current_process_file_id)) {
			$current_process_file_id = 1;
		}

		$Qfiles = $this->wpdb->get_results("SELECT * FROM " . $this->wpdb->base_prefix . "wptc_current_process WHERE id = " . $current_process_file_id);

		if (empty($Qfiles)) {
			wptc_log(array(), "--------Qfiles empty--------");
			return false;
		}

		static $is_queue = 0;
		$is_queue = count($Qfiles);

		//while loop is for memory optimization
		while ($is_queue) {

			foreach ($Qfiles as $file_info) {

				// wptc_log($file_info, '---------------$file_info-----------------');

				wptc_manual_debug('', 'during_upload_files', 100);

				$current_processed_files = array();
				$file = wp_normalize_path($file_info->file_path);

				$file = wptc_add_fullpath($file);

				if(!file_exists($file)){
					wptc_log($file, '--------File Does not exist--------');
					continue;
				}

				if (stripos($file, $this->tcapsule_path) !== FALSE) {
					$always_backup = 1;
				} else {
					$always_backup = 0;
				}

				if (!is_readable($file)) {

					//Do not email anything inside wptc temp dir
					if (stripos($file, WPTC_TEMP_DIR_BASENAME . '/backups') !== false) {
						continue;
					}

					$error_array = array('file_name' => wp_normalize_path($file));
					$this->config->append_option_arr_bool_compat('mail_backup_errors', $error_array, 'unable_to_read');
					continue;
				}

				//Cannot read filesize so skip this file
				if (filesize($file) === false) {
					continue;
				}

				$is_zero_bytes_file = false;

				if(wptc_is_zero_bytes_file($file)){
					wptc_log($file, '---------$file yes zero bytes------------');
					$is_zero_bytes_file = true;
				} else if (!$always_backup && $this->app_functions->is_bigger_than_allowed_file_size($file)) {
					wptc_log($file, '---------$file more than allowed size and not included------------');
					continue;
				}

				if (!is_file($file)) {
					continue;
				}

				$processed_file = $this->processed_files->get_file($file);

				//File inside wptc temp folder but not in the always included list then ignore it
				if (dirname($file) == $this->config->get_backup_dir() && $file != $always_include_file && !wptc_is_always_include_file($file) ) {
					continue;
				}

				$cloud_response = $this->output->out($dropbox_path, $file, $processed_file);

				// wptc_log($cloud_response, '---------$cloud_response------------');

				if ($cloud_response == 'exist') {
					wptc_log(array(), "--------exists--------");
					continue;
				} else if (isset($cloud_response['error'])) {
					// wptc_log($cloud_response, '--------$cloud_response--------');
					$this->logger->log(__($cloud_response['error'], 'wptc') , 'backups', $backup_id);

					wptc_log($cloud_response['error'], '---------GOT ERROR ON THIS------------');

					if($cloud_response['error'] == 'invalid_account_type'){
						$this->proper_backup_force_complete_exit('dropbox_api_limit_error');
					}

					continue;
				}

				if (isset($cloud_response['too_many_requests'])) {
					wptc_log(date('g:i:s a'), '---------too_many_requests called -------------');
					continue;
				} else if ($cloud_response) {
					// wptc_log($cloud_response, '---------$cloud_response------------');

					$response_arr = (array) $cloud_response['body'];
					$version_meta = array();

					if (DEFAULT_REPO === 'dropbox') {
						$version_meta['revision_number'] = $response_arr['id'];
						$version_meta['uploaded_file_size'] = $response_arr['size'];
					} else {
						$version_meta['revision_number'] = $response_arr['revision'];
						$version_meta['uploaded_file_size'] = $response_arr['bytes'];
					}

					$version_meta['revision_id'] = $response_arr['rev'];
					$version_meta['g_file_id'] = (empty($response_arr['g_file_id'])) ? '' : $response_arr['g_file_id'];

					//refreshing the processed file obj ; this is necessary only for chunked upload
					$processed_file = $this->processed_files->get_file($file);

					if ($processed_file && $processed_file->offset > 0) {
						$this->processed_files->file_complete($file);
					}

					$file_with_version = !empty($version_meta) ? $version_meta : array();

					$file_with_version['filename'] = $file;
					$file_with_version['mtime_during_upload'] = filemtime($file);
					$file_with_version['file_hash'] = $file_info->file_hash;

					$current_processed_files[] = $file_with_version; //manual

					$this->processed_file_count++;

					$in_progress = $this->config->get_option('in_progress', true);

					if(empty($in_progress)){
						wptc_log(array(), '-----------Break in middle because force stop 1-------------');
						send_response_wptc('backup_stopped_manually', 'BACKUP');
					}

					// wptc_log($current_processed_files, '---------$currentfile------------');

					$this->processed_files->add_files($current_processed_files); //manual

					wptc_save_files_zero_bytes($is_zero_bytes_file, $file);
				}
			}

			check_timeout_cut_and_exit_wptc($current_process_file_id + 1);

			// reload the while loop condition below
			global $current_process_file_id;
			$Qfiles = $this->wpdb->get_results("SELECT * FROM " . $this->wpdb->base_prefix . "wptc_current_process WHERE id = " . ++$current_process_file_id);
			$is_queue = count($Qfiles);
		}

		wptc_manual_debug('', 'end_upload_files');

		$this->config->set_option('current_process_file_id', $current_process_file_id);

		return true;
	}

	public function execute($type = '') {

		$this->set_if_meta_data_backup();

		wptc_log(wptc_is_meta_data_backup(), '---------------wptc_is_meta_data_backup()-----------------');

		$this->config->set_option('wptc_profiling_start', time());
		wptc_manual_debug('', 'backup_execute');
		$contents = @unserialize($this->config->get_option('this_cookie'));
		$backup_id = $contents['backupID'];

		wptc_set_time_limit(60);

		$this->config->set_memory_limit();

		try {
			if ((defined('DEFAULT_REPO') && DEFAULT_REPO) && !$this->dropbox->is_authorized()) {
				$this->logger->log(__('Your ' . DEFAULT_REPO . ' account is not authorized yet.', 'wptc'), 'backups', $backup_id);
				$this->proper_backup_force_complete_exit($msg = DEFAULT_REPO . ' account Auth failed so backup stopped');
			}

			//Database backup
			if (!$this->config->get_option('wptc_db_backup_completed')) {
				wptc_manual_debug('', 'start_db_backup');
				$this->backup_database($backup_id);
				wptc_manual_debug('', 'end_db_backup');
			}

			$this->app_functions->is_backup_request_timeout();

			//Compress backed up file
			if(!$this->config->get_option('sql_gz_compression')){
				wptc_manual_debug('', 'start_compress_db');
				$this->db_backup->compress();
				wptc_manual_debug('', 'end_compress_db');
			}

			if (!$this->config->get_option('db_encrypt_completed')) {
				wptc_manual_debug('', 'start_db_encrypt');
				$this->db_backup->encrypt();
				$this->config->set_option('db_encrypt_completed', true);
				wptc_manual_debug('', 'end_db_encrypt');
			}

			$this->app_functions->is_backup_request_timeout();

			//Check current files state
			if(!$this->config->get_option('get_current_files_state')){
				wptc_manual_debug('', 'start_get_current_files_state');
				$this->get_current_files_state();
				wptc_manual_debug('', 'end_get_current_files_state');
			}

			$this->allow_system_to_backup_meta_file();

			//Backup files
			wptc_manual_debug('', 'start_file_backup');
			$result = $this->backup_path(wptc_get_sanitized_home_path(), $this->db_backup->get_file(), $backup_id);
			wptc_manual_debug('', 'end_file_backup');

			$in_progress = $this->config->get_option('in_progress', true);

			if(empty($in_progress)){
				send_response_wptc('backup_stopped_manually', 'BACKUP');
			}

			$this->add_backup_general_data($backup_id);

			$this->do_meta_data_backup_related_works();

			$ignored_backup = ( $result === 'ignored' ) ? true : false;

			//Update all auto backup dirs to backedup
			do_action('record_auto_backup_complete_wptc', $backup_id);

			wptc_manual_debug('', 'start_backup_complete');
			$this->complete($ignored_backup, $backup_id);
			wptc_manual_debug('', 'end_backup_complete');

		} catch (Exception $e) {
			$msg = $e->getMessage();
			$this->logger->log(__('A error occured on backup ', 'wptc') . $msg, 'backups', $backup_id);
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => 'Backup failed, please check the activity logs for more info!')));
			$this->proper_backup_force_complete_exit($msg);
		}
	}

	private function do_meta_data_backup_related_works(){
		if ( !$this->config->get_option('wptc_main_cycle_running') && !$this->config->get_option('starting_first_backup')  ) {
			wptc_log('' , '---------------Dont take meta data its not scheduled backup-----------------');
			return ;
		}

		if ( $this->config->get_option('do_wptc_meta_data_backup') ) {
			wptc_log('' , '---------------meta already set-----------------');
			return ;
		}

		$this->config->set_option('do_wptc_meta_data_backup', true);

		//reset these flags to do db backup again
		$this->config->set_option('wptc_db_backup_completed', false);
		$this->config->set_option('sql_gz_compression', false);
		$this->config->set_option('sql_gz_compression_offset', false);

		$this->config->set_option('collected_tables_for_backups', false);
		$this->config->set_option('collected_tables_for_backups_offset', 0);

		if ($this->config->get_option('shell_db_dump_status') == 'completed') {
			$this->config->set_option('shell_db_dump_status', false);
		}

		send_response_wptc('Starting meta data backup', 'BACKUP');
	}

	private function set_if_meta_data_backup(){
		if ( !$this->config->get_option('do_wptc_meta_data_backup') ) {
			return define('IS_META_DATA_BACKUP', false);
		}

		return define('IS_META_DATA_BACKUP', true);
	}

	private function allow_system_to_backup_meta_file(){

		if (!wptc_is_meta_data_backup()) {
			return ;
		}

		if ( $this->config->get_option( 'allow_system_to_backup_meta_file' ) ) {
			wptc_log('' , '---------------allow_system_to_backup_meta_file already added to current table-----------------');
			return ;
		}

		$current_process_file_id = $this->config->get_option('current_process_file_id');
		wptc_log($current_process_file_id, '---------------$current_process_file_id allow_system_to_backup_meta_file-----------------');

		$file = $this->db_backup->get_file();

		if (file_exists($file . '.gz')) {
			$file = $file . '.gz';
		} else if (!file_exists($file)){
			wptc_log('' , '---------------meta file not exist-----------------');
			return ;
		}

		$file = wptc_remove_fullpath($file);

		$file_hash = wptc_get_hash($file);

		$query = "('" . $file . "', 'Q', '" . $file_hash . "')";

		wptc_log($query, '---------------allow_system_to_backup_meta_file--query---------------');

		$this->app_functions->insert_into_current_process($query);

		$this->config->set_option('allow_system_to_backup_meta_file' , true);

	}

	private function backup_database($backup_id){

		$bef_time = time();

		$this->db_backup->collect_tables_for_backup();

		$collect_tables_time_taken = time() - $bef_time;

		wptc_log($collect_tables_time_taken, "--------collect_tables_time_taken--------");

		$dbStatus = $this->db_backup->get_status();

		if (($dbStatus != WPTC_DatabaseBackup::NOT_STARTED) && ($dbStatus != WPTC_DatabaseBackup::IN_PROGRESS)) {
			return ;
		}

		if(defined('WPTC_SHELL_DB') && !WPTC_SHELL_DB){
			$status = 'failed';
		} else {
			$status = $this->db_backup->shell_db_dump();
		}

		wptc_log($status, '---------------$status------backup_database_shell_status-----------');

		if ($status === 'failed') {

			wptc_log('', "--------php sql dump only running--------");

			if ($dbStatus == WPTC_DatabaseBackup::IN_PROGRESS) {
				$this->logger->log(__('Resuming SQL backup.', 'wptc'), 'backups', $backup_id);
			} else {
				$this->logger->log(__('Starting SQL backup.', 'wptc'), 'backups', $backup_id);
			}

			$this->db_backup->execute();
			$this->logger->log(__('SQL backup complete. Starting file backup.', 'wptc'), 'backups', $backup_id);
			$this->config->set_option('wptc_db_backup_completed', true);

		}  else if ($status === 'running') {

			wptc_log(array(), '---------------database dump is running-----------------');
			send_response_wptc('Shell DB dump is running, wait for next request');

		} else if($status === 'do_not_continue'){

			$this->logger->log(__('SQL backup complete. Starting file backup.', 'wptc'), 'backups', $backup_id);
			$this->config->set_option('wptc_db_backup_completed', true);
			$this->db_backup->complete_all_tables();
			wptc_log(array(), '---------------database dump completed but wait for next call-----------------');
			// send_response_wptc('Shell DB dump is completed, continue from next request');
		}
	}

	private function add_completed_log($backup_id){

		$starting_backup_first_call_time = $this->config->get_option('starting_backup_first_call_time');
		$backup_all_time_taken = time() - $starting_backup_first_call_time;

		$this->logger->log(__('Backup complete.', 'wptc'), 'backups', $backup_id);
		$this->logger->log(sprintf(__('Total time taken to complete the full backup process is %d secs.', 'wptc'), $backup_all_time_taken), 'backups', $backup_id);
		$this->logger->log(sprintf(__('A total of %s files were processed.', 'wptc'), $this->processed_file_count), 'backups', $backup_id);
		$this->logger->log(sprintf(
			__('A total of %dMB of memory was used to complete this backup.', 'wptc'),
			(memory_get_usage(true) / 1048576)
		), 'backups', $backup_id);
	}

	private function complete($ignored_backup, $backup_id){

		$this->config->set_option('last_process_restore', false);

		$this->complete_reset_backup_flags();
		$this->add_completed_log($backup_id);

		$this->config->set_option('staging_backup_id', $backup_id);
		$this->config->complete(null, $ignored_backup);
		$this->clean_up();
	}

	public function  proper_backup_force_complete_exit($msg = false){
		wptc_set_backup_in_progress_server(false);
		$backup_id = wptc_get_cookie('backupID');
		$msg = empty($msg) ? 'Backup stopped' : $msg;
		$this->logger->log(__( $msg , 'wptc'), 'backups', $backup_id);
		$this->config->force_complete(null);
		$this->config->set_main_cycle_time();
		$this->clean_up($force_stop = true);
		if (empty($msg)) {
			send_response_wptc('Backup stopped forcefully');
		} else {
			send_response_wptc($msg);
		}
	}

	public function backup_now($type, $check_ajax = true) {

		wptc_log($type, '---------backup_now $type------------');
		
		$old_cookie = wptc_get_cookie('backupID');
		wptc_delete_cookie();

		wptc_set_cookie("backupID");

		if ($this->config->get_option('schedule_backup_running') || $type == 'daily_cycle') {
			store_name_for_this_backup_callback_wptc("Schedule Backup", $check_ajax);
		} else if ($this->config->get_option('wptc_main_cycle_running') || $this->config->get_option('auto_backup_running') || $type == 'sub_cycle') {
			store_name_for_this_backup_callback_wptc("Auto Backup", $check_ajax);
		} else {
			store_name_for_this_backup_callback_wptc("Updated on " .user_formatted_time_wptc(time(), $format = 'g:i a'), $check_ajax); //time
		}

		do_action('send_report_data_wptc', wptc_get_cookie('backupID'), 'BACKUP', 'STARTED');

		execute_tcdropbox_backup_wptc($type);
		wptc_manual_debug('', 'requested_to_server');
	}

	public function pre_check() {
		$this->exclude_class_obj->exclude_content_for_default_log_tables();
		$this->processed_files->delete_expired_life_span_backups();
		$this->processed_files->delete_old_backups(); //delete old data to free up database
		$this->processed_files->delete_incompleted_chunks(); //delete incompleted backups files
	}

	public function stop($process = null) {
		wptc_log($process,'--------------$stop process data-------------');
		if ($process == 'restore' || $process == 'logout' ) {
			$this->config->complete($process);
		} else {
			$in_progress = $this->config->get_option('in_progress', true);
			if (empty($in_progress)) {
				wptc_log(array(), '-----------Progress is not set-------------');
				return false;
			}
			$cur_backup_id = wptc_get_cookie('backupID');
			$this->logger->log(__('Backup is stopped', 'wptc'), 'backups', $cur_backup_id);
			$this->config->force_complete();
		}

		$this->clean_up($force_stop = true);
	}

	private function clean_up($force_stop = false) {
		return ;
	}

	public function unlink_current_acc_and_backups() {

		//delete backup history and data
		$this->logger->log(__("Current account unlinked and backups data were deleted", 'wptc'), 'others');

		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_files`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_restored_files`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_activity_log`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_options`");

		$this->config->set_option('in_progress', false);
		$this->config->set_option('is_running', false);
		$this->config->set_option('cached_g_drive_this_site_main_folder_id', false);
		$this->config->set_option('cached_wptc_g_drive_folder_id', false);
	}

	public function clear_prev_repo_backup_files_record($reset_inc_exc = false) {

		$this->config->set_option('cached_g_drive_this_site_main_folder_id', false);
		$this->config->set_option('cached_wptc_g_drive_folder_id', false);

		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_files`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_restored_files`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_backups`");

		if(!$reset_inc_exc){
			return false;
		}
		//reset inc exc
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_inc_exc_contents`");
	}

	public function clear_current_backup(){
		reset_backup_related_settings_wptc();
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_restored_files`");
		$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
	}
	public function write_status_of_file($id, $status) {
		$in = $this->wpdb->query("UPDATE `" . $this->wpdb->base_prefix . "wptc_current_process`
				SET status = '$status'
				WHERE id = $id");
	}

	public function iterate_files(){

		$this->init_file_iterator();

		$this->set_is_auto_backup();

		$current_action = $this->config->get_option('backup_current_action');
		$current_action = empty($current_action) ? false : $current_action ;

		switch ($current_action) {
			case false:
				$this->get_folders();
				break;
			case 'get_hash_by_folders':
				$this->get_hash_by_folders();
				break;
			case 'start_backup':
			default:
				break;
		}

		//Iterator is completed
		$this->config->set_option('gotfileslist', 1);

		//Update auto backup dirs status to queued
		// do_action('update_auto_backup_record_db_wptc', time());
	}

	private function get_folders(){
		$this->file_iterator->get_folders();
		$this->config->set_option('backup_current_action', 'get_hash_by_folders');
		$this->get_hash_by_folders();
	}

	private function get_hash_by_folders(){
		$break = false;
		$loop = $temp_counter = 0;

		while(!$break){
			$dir_meta = $this->current_iterator_table->get_unfnished_folder();
			$deep_dirs = false;

			if (empty($dir_meta) || $dir_meta->offset === -1) {
				$break = true;
				continue;
			}

			// wptc_manual_debug('', 'after_get_unfnished_folder_hash');

			$relative_path = wp_normalize_path($dir_meta->name);

			$path = wptc_add_fullpath($relative_path);

			if( array_search($relative_path, $this->file_iterator->get_deep_dirs()) !== false ){
				$deep_dirs = true;
			}

			if ($deep_dirs === false && $this->is_skip($path, true)) {
				$this->current_iterator_table->update_iterator($relative_path, -1);
				continue;
			}

			if(wptc_is_dir($path)){
				$this->get_hash_dir($relative_path, $dir_meta->offset, $temp_counter, $deep_dirs);
			}
		}
		$this->config->set_option('backup_current_action', 'start_backup');
		return;
	}

	private function get_hash_dir($path, $offset, &$temp_counter, $deep_dirs){

		$is_recursive = empty($deep_dirs) ? true : false;

		wptc_log($offset, "--------dir_meta_offset----get_hash_dir----");

		try{
			$this->seek_file_iterator->process_iterator($path, $offset, $is_recursive);
		} catch(Exception $e){

			$exception_msg = $e->getMessage();
			wptc_log($exception_msg, '---------------Exception-----------------');

			if (wptc_is_file_iterator_allowed_exception($exception_msg)) {
				// $this->current_iterator_table->update_iterator($path, -1);
				$this->logger->log(__($exception_msg , 'wptc'), 'backups', wptc_get_cookie('backupID') );
				$this->logger->log(__($path . ' This folder has been excluded since it is not readable.', 'wptc'), 'backups', wptc_get_cookie('backupID') );
				$this->exclude_class_obj->exclude_file_list(array('file' => $path, 'isdir' => true, 'category' => 'backup'), true);
				send_response_wptc('progress', 'BACKUP', 'Seeking Error :' . $exception_msg . 'So exlcuded this dir automatically');
			}

			if (!wptc_is_seeking_exception($exception_msg)) {
				$this->proper_backup_force_complete_exit($exception_msg);
			}

			wptc_log($path, '---------------Retry Seeking-----------------');
			$this->current_iterator_table->update_iterator($path, 0);
			send_response_wptc('progress', 'BACKUP', 'Seeking Error :' . $exception_msg);
		}

		$this->current_iterator_table->update_iterator($path, -1);
	}

	public function process_file($iterator, $path, &$counter, $iterator_loop_limit, &$query, $key){

		wptc_manual_debug('', 'during_backup_file_iterator', 1000);

		global $iterator_files_count_this_call;
		$iterator_files_count_this_call++;

		$this->set_iterator_file_size();

		$file = $iterator->getPathname();

		$file = wp_normalize_path($file);

		if (!$iterator->isReadable()) {
			return ;
		}

		$size = $iterator->getSize();

		$file = wp_normalize_path( $file );

		$relative_file = wptc_remove_fullpath( $file );

		if($this->is_skip($file)){
			return $this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
		}

		//wptc_manual_debug('', 'before_hash_check_iterator');

		$file_hash = wptc_get_hash($file);

		//wptc_manual_debug('', 'after_hash_get_iterator');

		if(!$this->processed_files->is_file_modified_from_before_backup($relative_file, $size, $file_hash)){

			//wptc_manual_debug('', 'after_hash_check_iterator');

			return $this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
		}


		$query .= empty($query) ? "(" : ",(" ;

		$query .= $this->wpdb->prepare("%s, 'Q', %s)", $relative_file, $file_hash);

		$this->add_iterator_file_size($size);

		$this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
	}

	private function set_iterator_file_size(){
		global $wptc_iterator_file_size;

		if (!empty($wptc_iterator_file_size)) {
			return ;
		}

		$iterator_file_size = $this->config->get_option('iterator_file_size');

		$wptc_iterator_file_size = empty($iterator_file_size) ? 0 : $iterator_file_size;
	}

	private function add_iterator_file_size($size){
		global $wptc_iterator_file_size;
		$wptc_iterator_file_size += $size;
	}

	public function is_skip($file, $is_dir = false){

		$basename = basename($file);

		if ($basename == '.' || $basename == '..') {
			return true;
		}

		if (!is_readable($file)) {
			return true;
		}

		if($is_dir === false && wptc_is_dir($file)){
			return true;
		}

		if (is_wptc_file($file)) {
			return true;
		}

		$always_backup = wptc_is_always_include_file($file);


		if ($this->exclude_class_obj->is_excluded_file($file) && $always_backup === false) {
			return true;
		}

		if (strstr($file, 'wptc_saved_queries.sql') !== false) {
			if (!apply_filters('is_realtime_valid_query_file_wptc', $file)) {
				return true;
			}
		}

		return false;
	}

	//Reset the config values after completing backup
	public function complete_reset_backup_flags() {
		$userTimenow = $this->config->get_wptc_user_today_date_time('Y-m-d');

		//Daily Main cycle complete process
		if ($this->config->get_option('wptc_main_cycle_running')) {
			$this->config->set_option('wptc_today_main_cycle', $userTimenow);
			$this->config->set_option('wptc_main_cycle_running', false);
		}
	}

	private function add_backup_general_data($backup_id){

		if($this->config->get_option('add_backup_general_data')){
			return ;
		}

		if ($this->processed_file_count < 1) {
			return ;
		}

		$complete_data['memory_usage'] = (memory_get_usage(true) / 1048576);
		$complete_data['backup_id'] = $backup_id;
		$complete_data['files_count'] = $this->processed_file_count;

		$backup_type = $this->config->get_option('wptc_current_backup_type');

		$backup_type = empty($backup_type) ? 'M' : $backup_type;

		store_backup_name_wptc( array(
						'backup_id'    => $complete_data['backup_id'],
						'backup_type'  => $backup_type,
						'files_count'  => $complete_data['files_count'],
						'memory_usage' => $complete_data['memory_usage']
					)
		);

		$this->config->set_option('add_backup_general_data', true);
	}

	public function get_current_files_state(){

		$offset = $this->config->get_option('check_current_files_state_offset');
		$offset = empty($offset) ? 0 : $offset;

		$break = false;

		while (!$break) {
			$files = $this->processed_files->get_all_distinct_files($offset);

			if (empty($files)) {
				break;
			}

			$deleted_files = $this->check_current_files_state($files);

			$this->save_current_files_state($deleted_files);

			$offset += WPTC_CHECK_CURRENT_STATE_FILE_LIMIT;

			if(!$this->app_functions->is_backup_request_timeout($return = true)){
				continue;
			}

			$break = true;
			$this->config->set_option('check_current_files_state_offset', $offset);
			send_response_wptc('Checking Current Files state ' . $offset , 'BACKUP');
		}

		$this->config->set_option('get_current_files_state', true);
	}

	private function save_current_files_state($data){

		$file = $this->create_current_state_file_if_not_exist();

		if (empty($data)) {
			return ;
		}

		$result = @file_put_contents($file , $data, FILE_APPEND);

		wptc_log($result, '---------------$result-----------------');

		if ($result === false) {
			wptc_log(array(), '----------------Saving current files state failed----------------');
		}
	}

	private function create_current_state_file_if_not_exist(){

		$backup_dir = $this->config->get_backup_dir();

		$file = $backup_dir . '/wptc_current_files_state.txt';

		if (file_exists($file) && filesize($file) > 0) {
			return $file;
		}

		@file_put_contents($file , wptc_remove_fullpath($backup_dir) . "/wptc_dummy_file.txt\n", FILE_APPEND);

		return $file;
	}

	private function check_current_files_state($files){
		if (empty($files)) {
			return ;
		}

		$deleted_files = '';

		foreach ($files as $file_meta) {

			$file = $file_meta->file;

			if(empty($file)){
				continue;
			}

			$file = wptc_add_fullpath($file);

			if (file_exists($file)) {
				continue;
			}

			if($this->current_state_skip_file($file)){
				continue;
			}

			$file = wptc_remove_fullpath($file);

			$deleted_files .= $file . "\n";
		}

		return $deleted_files;
	}

	private function current_state_skip_file($file){
		if (empty($file)) {
			return true;
		}

		if (strpos($file, 'wptc-secret') !== false || strpos($file, 'wptc_current_files_state.txt') !== false || strpos($file, 'wp-tcapsule-bridge') !== false) {
			return true;
		}

		return false;
	}
}
