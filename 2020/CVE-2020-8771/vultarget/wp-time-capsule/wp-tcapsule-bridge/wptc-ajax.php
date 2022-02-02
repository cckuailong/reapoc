<?php
class WPTC_Restore_Download {

	const MAX_DOWNLOAD_FAILED_LIMIT = 500;

	private $config,
			$file_iterator,
			$processed_files,
			$output,
			$processed_file_count,
			$is_continue_from_email,
			$post_data,
			$common_include_files,
			$initialize,
			$get_cur_res_b_id,
			$cloud_repo,
			$current_iterator_table,
			$prepared_file_array,
			$backup_type,
			$restore_app_functions,
			$restore_id,
			$restore_downloaded_files_count,
			$utils,
			$processed_backup,
			$analyze_sql_files_iterator,
			$fs;

	private $mysql_buffer_freed = false;

	public function __construct($type = false) {
		//create object for restore app common
		$this->init_restore_app_functions();

		//accept only wptc requests
		$this->restore_app_functions->verify_request();

		$this->restore_app_functions->set_default_timezone();

		//enable PHP errors
		$this->restore_app_functions->enable_php_errors();

		//set global starting time
		$this->restore_app_functions->start_request_time($type);

		//define restore constant to override other functions
		$this->restore_app_functions->define_constants();

		//restore needed files
		$this->include_files();

		//assume this is request from server
		set_server_req_wptc();

		//start database connections
		$this->connect_db();

		$this->restore_app_functions->init_other_objects();

		$this->restore_app_functions->init_other_functions();

		//support for wp-content outside
		//set_basename_wp_content_dir();

		//check whether its init request
		$this->check_init_request();

		//Init WP File system
		$this->set_fs();

		$this->config = WPTC_Factory::get('config');

		//Check for other operations
		$this->check_for_other_operations();

		$this->restore_app_functions->init_log_files();

		//setup restore related data
		$this->setup_restore();

		//Init restore process
		$this->initiate_restore($type);
	}

	private function init_restore_app_functions(){
		//common app functions for both ajax and tc-init
		require_once dirname(__FILE__). '/' ."wptc-restore-app-functions.php";
		$this->restore_app_functions = new WPTC_Restore_App_Functions();
	}

	private function include_files(){
		require_once dirname(__FILE__). '/' ."common_include_files.php";
		$this->common_include_files = new Common_Include_Files('wptc-ajax');
		$this->common_include_files->init();
	}

	private function check_for_other_operations(){

		$this->restore_id = $this->config->get_option('restore_action_id');

		if (isset($this->post_data['action']) && $this->post_data['action'] == 'reset_restore_settings' ) {
			$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
			$this->restore_app_functions->disable_maintenance_mode();
			$this->config->remove_garbage_files(array('is_restore' => true), true);
			reset_restore_related_settings_wptc($dont_delete_logs = true);
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->die_with_msg("RESET_SUCCESSFULLY");
		}

		if (isset($this->post_data['action']) && $this->post_data['action'] == 'get_last_php_error_wptc' ) {
			$error = wptc_fatal_error_hadler(1);
			wptc_log($error, '---------$error------------');
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->die_with_msg($error);
		}

		if (!is_any_ongoing_wptc_restore_process()) {
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->die_with_msg(array('error' => 'This link has been expired, please initiate the fresh restore !'));
		}
	}

	private function connect_db(){
		$this->wpdb = $this->restore_app_functions->init_db_connection();
	}

	private function set_fs(){
		$this->fs = $this->restore_app_functions->init_file_system();
	}

	private function check_init_request(){
		$this->initialize = false;

		$this->post_data = $_POST;

		if (!empty($_POST['initialize'])) {
			$this->initialize = true;
		} else if (isset($_POST['data'])) {
			$this->post_data = $this->restore_app_functions->decode_request();
			if (!empty($_POST['initialize'])) {
				$this->initialize = true;
			}
		}
	}

	private function setup_restore($post_data = array()){
		$this->is_continue_from_email = false;
		if (!empty($this->post_data['continue'])) {
			$this->is_continue_from_email = true;
		}

		$this->file_iterator = new WPTC_File_Iterator();
		$this->current_iterator_table = new WPTC_Processed_iterator();
		$this->processed_files = WPTC_Factory::get('processed-restoredfiles', true);
		$this->processed_backup = new WPTC_Processed_Files();
		$this->config->set_option('recent_restore_ping', time());
		$this->cloud_repo = WPTC_Factory::get(DEFAULT_REPO);
		$this->output = $this->output ? $this->output : WPTC_Extension_Manager::construct()->get_output();
		$this->restore_app_functions->define('WPTC_SITE_ABSPATH', $this->config->get_option('site_abspath'));
		wptc_set_fallback_db_search_1_14_0();
		wptc_setlocale();
		$this->restore_app_functions->enable_maintenance_mode();
	}

	private function process_safe_for_write_check_options() {
		if (empty($this->post_data)){
			return ;
		}

		if(!isset($this->post_data['ignore_file_write_check'])) {
			return ;
		}

		if($this->post_data['ignore_file_write_check'] == 0) { //Skip only selected files so continue from where we left.

			$this->config->set_option('live_files_to_restore_table', false);
			$this->config->set_option('not_safe_for_write_files', false);
			$this->config->set_option('check_is_safe_for_write_restore', 1);

		} else if($this->post_data['ignore_file_write_check'] == 1) { //Skip all the files and start restore.
			$this->config->set_option('live_files_to_restore_table', 0);
			$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
			$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
			$this->config->set_option('check_is_safe_for_write_restore', 0);
			$this->config->set_option('restore_current_action', false);

		} else if($this->post_data['ignore_file_write_check'] == 2) { //Restart file checking once again because user may fixed those issues
			$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_current_process`");
			$this->wpdb->query("TRUNCATE TABLE `" . $this->wpdb->base_prefix . "wptc_processed_iterator`");
			$this->config->set_option('restore_current_action', false);
			$this->config->set_option('live_files_to_restore_table', 0);
			$this->config->set_option('check_is_safe_for_write_restore', 1);
			$this->config->set_option('not_safe_for_write_files', false);
		}
	}

	public function initiate_restore($type) {

		//Just object creation request
		if (isset($type) && $type === 'iterator') {
			return ;
		}

		wptc_manual_debug('', 'continue_wptc_ajax');

		wptc_log(array(), "--------Initiate restore--");

		$this->config->set_option('wptc_profiling_start', time());

		$this->config->set_option('in_progress_restore', true);

		$this->restore_app_functions->set_additional_flags();

		$this->process_safe_for_write_check_options();

		$this->continue_copy_from_bridge_if_already_started();

		$data = array();

		if (!empty($this->initialize) && empty($data)) {
			//get requested restore meta data
			$data = $this->config->get_option('restore_post_data');
			if (empty($data)) {
				$this->restore_app_functions->disable_maintenance_mode();
				$this->proper_restore_complete_exit(array('error' => 'Didnt get Files to Restore'));
			}
			$data = unserialize($data);
		}

		$files_to_restore = array();

		if (isset($data['files_to_restore'])) {
			$files_to_restore = $data['files_to_restore'];
		}

			if (!empty($data['cur_res_b_id']) && $data['cur_res_b_id'] != 'false') {
			//This is restore to point
			$cur_res_b_id = $data['cur_res_b_id'];

			if (empty($files_to_restore)) {
				$files_to_restore = array('0' => 1); //dummy
			}
		}

		if (!empty($files_to_restore)) {
			if (!empty($cur_res_b_id)) {
				//Restore to point
				//the current b_id will be used to determine the future old files which are to be restored to the prev restore point
				$this->config->set_option('cur_res_b_id', $cur_res_b_id);
				$this->config->set_option('in_restore_deletion', false);
				$this->config->set_option('unknown_files_delete', true);
				$this->config->set_option('selected_id_for_restore', $cur_res_b_id);
			} else {
				//Restore to selected files
				$this->config->set_option('cur_res_b_id', '');
				$this->config->set_option('selected_id_for_restore', $data['selectedID']);
				$this->config->set_option('unknown_files_delete', false);
			}

			//set some flags and get sql files if its restore to point and then continue the restore process
			$this->set_flags_and_add_database_file($files_to_restore);

		} else {

			//if there is a bridge process going on ; then dont do restore-download
			if ($this->config->get_option('is_bridge_process')) {

				wptc_log('', "--------die_with_msg--here --1----");

				$this->restore_app_functions->die_with_msg("wptcs_over_wptce");
			}

			$restore_result = $this->start_restore();
		}
	}

	private function continue_copy_from_bridge_if_already_started() {
		if ($this->config->get_option('is_bridge_process')) {
			if ($this->is_continue_from_email) {
				$this->restore_app_functions->die_with_msg(array('continue_from_email' => true));
			}
		}
	}

	public function set_flags_and_add_database_file($files_to_restore) {

		//This never be empty because we add some dummy data even its empty
		if (empty($files_to_restore)) {
			return true;
		}

		$this->config->set_option('in_progress_restore', true);

		$this->config->set_option('got_files_list_for_restore_to_point', 0);
		$this->config->set_option('live_files_to_restore_table', 0);
		$this->config->set_option('recorded_files_to_restore_table', 0);
		$this->config->set_option('is_deleted_all_future_files', 0);
		$this->config->set_option('selected_files_temp_restore', 0);
		$this->config->set_option('selected_backup_type_restore', 0);
		$this->config->set_option('got_selected_files_to_restore', 0);
		$this->config->set_option('not_safe_for_write_files', 0);
		$this->config->set_option('recorded_this_selected_folder_restore', 0);
		$this->config->set_option('is_bridge_process', 0);

		WPTC_Factory::get('logger')->log(sprintf(__('Restore started on %s.', 'wptc'), date("l F j, Y", strtotime(current_time('mysql')))), 'restores', $this->restore_id);
		$time = ini_get('max_execution_time');

		WPTC_Factory::get('logger')->log(sprintf(__('Your time limit is %s and your memory limit is %s'),
			$time ? $time . ' ' . __('seconds', 'wptc') : __('unlimited', 'wptc'),
			ini_get('memory_limit')
		), 'restores', $this->restore_id);

		if (ini_get('safe_mode')) {
			WPTC_Factory::get('logger')->log(__("Safe mode is enabled on your server so the PHP time and memory limit cannot be set by the Restore process. So if your Restore fails it's highly probable that these settings are too low.", 'wptc'), 'restores', $this->restore_id);
		}

		$cur_res_b_id = $this->get_cur_res_b_id();

		//this truncates the processed_restored_files table
		$this->processed_files->truncate();

		if (empty($cur_res_b_id)) {
			//This conditional loop is for restoring single or selected files.

			$selected_restore_id = $this->config->get_option("selected_id_for_restore");

			//get backup type such as D(Scheduled backup) or M(Manual backup)
			$backup_type = $this->processed_backup->backup_type_check($selected_restore_id);

			$this->config->set_option('selected_files_temp_restore', serialize($files_to_restore));
			$this->config->set_option('selected_backup_type_restore', $backup_type);

			$this->config->set_option('got_files_list_for_restore_to_point', 1); //resetting flag indicating no need of restore_to_point

			WPTC_Factory::get('logger')->log(__("Files prepared for Restoring.", 'wptc'), 'restores', $this->restore_id);
			wptc_manual_debug('', 'stored_selected_restore_flags');

		} else {
			//This conditional loop is for restoring to a point. This will have only the sql file.
			$this->config->set_option('got_selected_files_to_restore', 1); //resetting flag indicating no need of restore_single

			$backup_type = $this->processed_backup->backup_type_check($cur_res_b_id);

			//during restore to point we need to prepare and send the sql files separately by the following function.
			$files_to_restore = $this->processed_files->get_formatted_sql_file_for_restore_to_point_id($cur_res_b_id, $backup_type);

			wptc_log($files_to_restore, "--------got sql files--------");

			//Add only the sql files to queue to download.
			$this->processed_files->add_files_for_restoring($files_to_restore, $this->restore_app_functions);
			wptc_manual_debug('', 'got_sql_file_restore');

			//get current state file if its restore to point
			$this->add_state_file($cur_res_b_id);

		}

		$this->restore_pre_check(); //since we are using manual ajax function
	}

	public function add_state_file($backup_id) {

		if ($this->config->get_option('added_state_file_to_restore')) {
			return true;
		}

		$state_file = $this->processed_files->get_state_file($backup_id);

		wptc_log($state_file, "--------got state_file--------");

		$this->processed_files->add_files_for_restoring($state_file, $this->restore_app_functions);

		$this->config->set_option('added_state_file_to_restore', true);

		wptc_manual_debug('', 'got_state_file_restore');
	}

	public function restore_pre_check() {
		if (!$this->config->get_option('is_running_restore')) {
			$this->config->set_option('is_running_restore', true);
			//After all setups here we start the restore process.
			$this->start_restore();
		} else {

			wptc_log('', "--------die_with_msg--here --2----");

			$this->restore_app_functions->die_with_msg("wptcs_over_wptce");
		}
	}

	public function start_restore() {

		$this->config->set_memory_limit();

		$logger = WPTC_Factory::get('logger');

		try {
			if (!$this->cloud_repo || !$this->cloud_repo->is_authorized($is_restore = true)) {
				$logger->log(__('Your '.DEFAULT_REPO.' account is not authorized yet.', 'wptc'), 'restores', $this->restore_id);
				$this->restore_app_functions->disable_maintenance_mode();
				$this->proper_restore_complete_exit(array('error' => 'Your '.DEFAULT_REPO.' account is not authorized yet.'));
			}

			$this->delete_state_files_from_download_list();

			//Adding files to queue and start dowloading the files
			$result = $this->add_files_and_start_download();

			wptc_log($result, "--------result add_files_and_start_download--------");

			if (is_array($result) && isset($result['error'])) {
				$this->config->set_option('last_process_restore', false);
				$this->proper_restore_complete_exit($result);
			}

			$logger->log(__('Restore complete.', 'wptc'), 'restores', $this->restore_id);
			$logger->log(sprintf(__('A total of %s files were processed.', 'wptc'), $this->processed_file_count), 'restores', $this->restore_id);
			$logger->log(sprintf(
				__('A total of %dMB of memory was used to complete this restore.', 'wptc'),
				(memory_get_usage(true) / 1048576)
			), 'restores', $this->restore_id);

			$root = false;
			if (get_class($this->output) != 'WPTC_Extension_DefaultOutput') {
				$this->output = new WPTC_Extension_DefaultOutput();
				$root = true;
			}

			if (!$this->chunked_download_check()) {

				wptc_log('', "--------die_with_msg--here --3----");

				//if chunked download is not going ; or if bridge copy is not going do this completion step.
				$this->proper_restore_complete_exit('wptcs_over_wptce');
			} else {
				wptc_log(array(), "--------is_chunk_alive--------");
				$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
			}
		} catch (Exception $e) {
			if ($e->getMessage() == 'Unauthorized') {
				$logger->log(__('The plugin is no longer authorized with Cloud Repo.', 'wptc'), 'restores', $this->restore_id);
			} else {
				$logger->log(__('A fatal error occured: ', 'wptc') . $e->getMessage(), 'restores', $this->restore_id);
			}

			$this->proper_restore_complete_exit(array('error' => $e->getMessage()));
		}
	}

	//Checking the chunked download is in progress or completed
	public function chunked_download_check() {
		$unfinished_downloads = $this->wpdb->get_var(' SELECT COUNT(*) FROM `' . $this->wpdb->base_prefix . 'wptc_processed_restored_files` WHERE `offset` > 0 ');
		if ($unfinished_downloads > 0) {
			return true;
		}

		return false;
	}

	public function proper_restore_complete_exit($message) {
		$this->config->complete('restore');

		if (!empty($message) && is_array($message)) {
			$this->config->remove_garbage_files(array('is_restore' => true));
			$this->restore_app_functions->die_with_msg($message);
		}

		$this->restore_app_functions->die_with_msg($message);
	}

	public function add_files_and_start_download($file = null, $version = null, $site_abspath = null) {

		if (!$this->config->get_option('in_progress_restore') || $this->config->get_option('in_progress')) {
			//this means restore got completed so returning empty response will end the restore
			return;
		}

		//just normalized abspath
		$site_abspath = wptc_get_sanitized_home_path();

		//this variable holds all the files which are already restored along with some info.
		$this->processed_files = WPTC_Factory::get('processed-restoredfiles', true);

		$this->processed_file_count = $this->processed_files->get_file_count();

		//this is true for restore to point because we have made it true when we add sql files to the queue eariler
		$got_selected_files_to_restore = $this->config->get_option("got_selected_files_to_restore");

		if (!$got_selected_files_to_restore) {
			//This conditional loop is only for restoring selected files
			$this->process_add_selected_files_to_restore();
			WPTC_Factory::get('logger')->log(__("Filelist prepared for Restoring Selected Files.", 'wptc'), 'restores', $this->restore_id);
		}

		//if we didnt store all the restore to point files to restore table, then continue it
		$got_restore_file_list = $this->config->get_option('got_files_list_for_restore_to_point');
		if (!$got_restore_file_list) {
			//This conditional loop is only for restore to point
			WPTC_Factory::get('logger')->log(__("Starting to prepare the Filelist for Restoring.", 'wptc'), 'restores', $this->restore_id);

			$cur_res_b_id = $this->get_cur_res_b_id();

			if (!empty($cur_res_b_id)) {
				//This conditional loop is for Restore to point.

				$live_files_to_restore_table = $this->config->get_option('live_files_to_restore_table'); //flag for is live_files added to db

				$backup_type = $this->processed_backup->backup_type_check($cur_res_b_id);

				if (empty($live_files_to_restore_table)) {
					wptc_log(array(), '---------------Checking feature files-----------------');

					wptc_manual_debug('', 'start_iterator_files_restore');

					$this->iterator_files();

					wptc_manual_debug('', 'end_iterator_files_restore');
				}

				$this->restore_app_functions->maybe_call_again_tc();

				$is_deleted_all_future_files = $this->config->get_option('is_deleted_all_future_files');

				if (empty($is_deleted_all_future_files)) {
					wptc_log(array(), '---------------Deleting feature files-----------------');

					wptc_manual_debug('', 'start_delete_future_files');
					if ($this->config->get_option('is_latest_restore_point')) {
						wptc_log(array(),'-----------SKIP delete_future_files because of is_latest_restore_point----------------');
						$this->config->set_option('is_deleted_all_future_files', true);
					} else {
						$this->delete_future_files();
					}

					wptc_manual_debug('', 'end_delete_future_files');
				}

				$this->restore_app_functions->maybe_call_again_tc();

				$recorded_files_to_restore_table = $this->config->get_option('recorded_files_to_restore_table');

				if (empty($recorded_files_to_restore_table)) {
					wptc_log(array(), '---------------Checking files changes-----------------');
					wptc_manual_debug('', 'start_get_restorable_files');

					$this->get_recorded_files_to_restore_table();

					wptc_manual_debug('', 'end_get_restorable_files');
				}

				WPTC_Factory::get('logger')->log(__("Filelist prepared for Restoring.", 'wptc'), 'restores', $this->restore_id);

				$this->config->set_option('got_files_list_for_restore_to_point', 1);

			}
		}

		$this->restore_app_functions->maybe_call_again_tc();

		$this->set_downloaded_files_count();

		$restore_queue = $this->processed_files->get_limited_restore_queue_from_base($this->restore_downloaded_files_count);

		wptc_manual_debug('', 'start_download_files');

		static $is_queue = 0;
		$is_queue = count($restore_queue);

		while ($is_queue) {
			foreach ($restore_queue as $key => $value) {
				wptc_manual_debug('', 'during_download_files', 5);

				$additional_data = array();
				$additional_data = (array) $value;

				// wptc_log($value,'-----------$value----------------');

				$this->restore_downloaded_files_count = $additional_data['file_id'];

				$relative_file = $additional_data['file'];

				$file = wptc_add_fullpath($relative_file);

				$version = $additional_data['revision_id'];

				$current_processed_files = $uploaded_files = array();

				// wptc_log($file, '---------------dowloading file-----------------');

				$cloud_response = $this->output->drop_download($site_abspath, $file, $version, $value, (array) $value);

				// wptc_log($cloud_response,'-----------$cloud_response----------------');

				if (is_array($cloud_response) && isset($cloud_response['error']) || empty($cloud_response)) {

					$this->delete_download_failed_file_cache($file);

					if (!empty($cloud_response['error']) && $this->is_retry_required($cloud_response['error'])) {
						wptc_log(array(),'-----------404 ERROR so getting nearest revision----------------');
						$retry_status = $this->output->retry_revision_failed_file($site_abspath, $file, $value);

						if ($retry_status !== false) {
							$this->config->set_option('restore_downloaded_files_count', $this->restore_downloaded_files_count - 1);
							$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
						}
					}

					if (empty($cloud_response)) {
						$cloud_response['error'] = 'Unknown error';
					}
					$cloud_response['file'] = $relative_file;

					wptc_log($cloud_response,'-----------$cloud_response---------error-------');

					$this->restore_app_functions->log_data('files', $cloud_response);

					$counter = $this->get_download_failed_file_count();
					$counter++;

					if (self::MAX_DOWNLOAD_FAILED_LIMIT <= $counter) {
						$this->hard_reset_restore();
						$err_msg['failure_data'] = $this->restore_app_functions->get_failure_data();
						$err_msg['error'] = 'Error: Maximum download failed limit crossed!';
						wptc_log($err_msg, '--------$err_msg--------');
						$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
						$this->restore_app_functions->die_with_msg($err_msg);
					}

					$this->update_download_failed_file_count($counter);

				} else if (!empty($result) && is_array($result) && isset($result['too_many_requests'])) {

					//Temporary fails in google drive
					wptc_log($result, "--------too_many_requests--------");
					WPTC_Factory::get('logger')->log(__("Limit reached during download : .", 'wptc'), 'restores', $this->restore_id);

				}

				if ($this->is_state_file($relative_file)) {
					$this->config->set_option('restore_downloaded_files_count', $this->restore_downloaded_files_count);
					$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
				}

				//check timeout and echo "wptcs_callagain_wptce`" for each download files
				if ($this->restore_app_functions->maybe_call_again_tc($return = true)) {
					$this->config->set_option('restore_downloaded_files_count', $this->restore_downloaded_files_count);
					$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
				}
			}
			$restore_queue = $this->processed_files->get_limited_restore_queue_from_base($this->restore_downloaded_files_count);
			$is_queue = count($restore_queue);
		}

		$this->config->set_option('restore_downloaded_files_count', $this->restore_downloaded_files_count);

		wptc_manual_debug('', 'end_download_files');

		return true;
	}

	private function init_utils(){
		if (!empty($this->utils)) {
			return ;
		}

		$this->utils = new Gdrive_Utils();
	}

	private function is_state_file($file){

		if(strstr($file, 'wptc_current_files_state.txt') === false){
			return false;
		}
		wptc_log(array(),'-----------Yes its state file.----------------');

		$sql = "SELECT offset FROM {$this->wpdb->base_prefix}wptc_processed_restored_files WHERE file = '". $file ."'";

		wptc_log($sql,'-----------$sql----------------');

		$offset = $this->wpdb->get_var($sql);

		wptc_log($offset,'--------$offset-------------------');

		if (!empty($offset)) {
			wptc_log(array(),'-----------Offset not empty so its chunk dowload continue the process----------------');
			return false;
		}

		wptc_log(array(),'-----------offset empty state_file completely downloaded so breaking the request to process state file----------------');

		$this->config->set_option('state_file_downloaded_in_restore', true);

		return true;
	}

	private function delete_state_files_from_download_list(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if (!$this->config->get_option('state_file_downloaded_in_restore')) {
			wptc_log(array(),'-------------state_file_downloaded_in_restore not set--------------');
			return ;
		}

		if ($this->config->get_option('deleted_state_files_from_download_list')) {
			wptc_log(array(),'-------------delete_state_files_from_download_list aleady deleted--------------');
			return ;
		}

		if(!$this->restore_app_functions->delete_state_files_from_download_list($this->processed_files)){
			wptc_log(array(),'----------delete_state_files_from_download_list FAILED----------------');
			$this->restore_app_functions->disable_maintenance_mode();
			$this->restore_app_functions->send_report_data($this->restore_id, 'FAILED');
			$this->restore_app_functions->die_with_msg(array('error' => 'Cannot state open database file'));
		}

		wptc_log(array(),'----------delete_state_files_from_download_list success----------------');

		$this->config->set_option('state_file_downloaded_in_restore', false);

		//this is for double check
		$this->config->set_option('deleted_state_files_from_download_list', true);
	}

	private function is_retry_required($error){
		if (empty($error)) {
			return false;
		}

		switch (DEFAULT_REPO) {
			case 'dropbox':
				return stripos($error, '409 path/not_found/' ) === false ? false : true;
			case 'g_drive':
				return stripos($error, '(404) Revision not found' ) === false ? false : true;
			case 's3':
				return false;
		}

	}

	private function delete_download_failed_file_cache($file){
		$this->init_utils();

		$temp_folder_file = $this->utils->getTempFolderFromOutFile(wp_normalize_path($file));

		wptc_wait_for_sometime();

		$this->fs->delete($temp_folder_file);

		if ( file_exists($temp_folder_file) ) {
			@unlink($temp_folder_file);
		}

	}

	private function set_downloaded_files_count(){
		$count = $this->config->get_option('restore_downloaded_files_count');
		$this->restore_downloaded_files_count = ($count) ? $count : 0 ;
		wptc_log($this->restore_downloaded_files_count, '---------------$this->restore_downloaded_files_count-----------------');
	}

	private function get_download_failed_file_count(){
		$count = $this->config->get_option('download_failed_file_counter');
		return empty($count) ? 0 : $count;
	}

	private function update_download_failed_file_count($counter){
		$this->config->set_option('download_failed_file_counter', $counter);
	}

	private function hard_reset_restore(){
		$this->config->set_option('in_progress_restore', false);
		$this->config->set_option('in_progress_restore', false);
		$this->config->set_option('is_running_restore', false);

		reset_restore_related_settings_wptc($dont_delete_logs = true);
	}

	public function process_add_selected_files_to_restore() {

		$selected_files_temp_restore = $this->config->get_option('selected_files_temp_restore');

		$backup_type = $this->config->get_option('selected_backup_type_restore');

		if (!$selected_files_temp_restore && !$backup_type) {
			$this->config->set_option('got_selected_files_to_restore', 1);
			return true;
		}

		$files_to_restore_tmp = unserialize($selected_files_temp_restore);

		wptc_log($files_to_restore_tmp, "--------files_to_restore_tmp--------");

		foreach ($files_to_restore_tmp as $files_or_folders => $v) {
			if ($files_or_folders == 'files') {
				foreach ($v as $file_dets) {
					$this->restore_app_functions->check_and_record_not_safe_for_write($file_dets['file']);
				}
				$this->check_and_exit_if_safe_for_write_limit_reached();

				$this->processed_files->add_files_for_restoring($v, $this->restore_app_functions, $check_hash = true);
			} else if ($files_or_folders == 'folders') {
				foreach ($v as $file_dets) {
					$recorded_this_selected_folder_restore = $this->config->get_option_arr_bool_compat('recorded_this_selected_folder_restore');
					if (is_array($recorded_this_selected_folder_restore) && in_array($file_dets['file'], $recorded_this_selected_folder_restore)) {
						continue;
					}
					$this->get_recorded_files_of_this_folder_to_restore_table($file_dets['file'], $file_dets['backup_id']);
				}
			}
		}
		$this->config->set_option('got_selected_files_to_restore', 1);
	}

	public function check_and_exit_if_safe_for_write_limit_reached($path = false, $offset = false) {
		//return true; //off for now

		$check_is_safe_for_write = $this->config->get_option('check_is_safe_for_write_restore');
		$not_safe_for_write_files = $this->config->get_encoded_not_safe_for_write_files();

		if (!$check_is_safe_for_write || empty($not_safe_for_write_files)) {
			return true;
		}

		if (defined('WPTC_RESTORE_FILES_NOT_WRITABLE_COUNT') && (count($not_safe_for_write_files) >= WPTC_RESTORE_FILES_NOT_WRITABLE_COUNT)) {
			if ($path !== false && $offset !== false) {
				$this->current_iterator_table->update_iterator($path, $offset);
			}
			$this->restore_app_functions->die_with_msg((array('not_safe_for_write_limit_reached' => $not_safe_for_write_files)));
		}
	}

	public function iterator_files() {

		$current_action = $this->config->get_option('restore_current_action');
		$current_action = empty($current_action) ? false : $current_action ;

		switch ($current_action) {
			case false:
				$this->get_folders();
				break;
			case 'get_files_by_folders':
				$this->get_files_by_folders();
				break;
			case 'start_restore':
			default:
				break;
		}

		$this->save_future_files();
		$this->config->set_option('live_files_to_restore_table', 1);

	}

	private function get_folders(){
		$this->file_iterator->get_folders();
		$this->config->set_option('restore_current_action', 'get_files_by_folders');
		$this->get_files_by_folders();
	}

	private function get_files_by_folders(){

		$break = false;
		$loop = $temp_counter = 0;

		while(!$break){
			$dir_meta = $this->current_iterator_table->get_unfnished_folder();

			$deep_dirs = false;

			if (empty($dir_meta) || $dir_meta->offset === -1) {
				$break = true;
				continue;
			}

			$relative_path = wp_normalize_path($dir_meta->name);

			$path = wptc_add_fullpath($relative_path);

			if( array_search($relative_path, $this->file_iterator->get_deep_dirs()) !== false ){
				$deep_dirs = true;
			}


			if ($deep_dirs === false && $this->is_skip($path)) {
				$this->current_iterator_table->update_iterator($relative_path, -1);
				continue;
			}

			if(wptc_is_dir($path)){
				$this->process_dir($relative_path, $dir_meta->offset, $temp_counter, $deep_dirs);
			} else {
				// $this->check_file($dir_meta->name);
			}
		}
		$this->config->set_option('restore_current_action', 'start_restore');
		return;
	}

	private function process_dir($path, $offset, &$temp_counter, $deep_dirs){

		$seek_file_iterator = new WPTC_Seek_Iterator($this, $type = "RESTORE", 30 );

		$is_recursive = ($deep_dirs) ? false : true;

		try{
			$seek_file_iterator->process_iterator($path, $offset, $is_recursive);
		} catch(Exception $e){

			$exception_msg = $e->getMessage();
			wptc_log($exception_msg, '---------------Exception-----------------');

			if (wptc_is_file_iterator_allowed_exception($exception_msg)) {
				WPTC_Factory::get('logger')->log($exception_msg , 'restores', $this->restore_id);
				WPTC_Factory::get('logger')->log($path . ' This folder has been excluded since it is not readable.', 'restores', $this->restore_id);
				WPTC_Base_Factory::get('Wptc_ExcludeOption')->exclude_file_list(array('file' => $path, 'isdir' => true, 'category' => 'backup'), true);
				$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
			}

			if (!wptc_is_seeking_exception($exception_msg)) {
				$this->restore_app_functions->die_with_msg(array('error' => $exception_msg));
			}

			wptc_log($path, '---------------Retry Seeking-----------------');
			$this->current_iterator_table->update_iterator($path, 0);
			$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
		}

		$this->current_iterator_table->update_iterator($path, -1);

		$this->save_future_files();

		$this->restore_app_functions->maybe_call_again_tc();

	}

	public function process_file($iterator, $is_recursive, $path, &$counter, $key){
		wptc_manual_debug('', 'during_iterator_files_restore', 1000);

		$file = $iterator->getPathname();

		if (!$iterator->isReadable()) {
			return ;
		}

		$file = wp_normalize_path($file);

		if (!$is_recursive && wptc_is_dir($file)){
			// wptc_log(array(), '---------------SKIP 1-----------------');
			return ;
		}

		if ($this->is_skip($file)) {
			$this->check_timeout_iter_file($path, $counter, $key);
			// wptc_log($file, '---------------SKIP 2-----------------');
			return ;
		}

		$this->check_file($file, $key);

		$this->check_timeout_iter_file($path, $counter, $key);
	}

	private function check_file($file, &$offset = 0){

		//Check file is writable if not then change the mode , if cannot change then add it to unable restore list
		$this->restore_app_functions->check_and_record_not_safe_for_write($file);

		//delete all the new files which comes after current restore point
		$this->add_future_files_and_get_restorable_files($file, $offset);

		// if ($write_on_complete) {
		// 	$this->current_iterator_table->update_iterator($file, -1);
		// }
	}


	private function check_timeout_iter_file($path, &$temp_counter, $offset){
		// wptc_log(func_get_args(), "-------". __FUNCTION__ ."---------");
		// if ($temp_counter++ < 30) {
		// 	return ;
		// }

		// $temp_counter = 0;


		$this->check_and_exit_if_safe_for_write_limit_reached($path, $offset);

		if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
			return ;
		}

		$this->save_future_files();

		$this->current_iterator_table->update_iterator($path, $offset);

		$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
	}

	private function save_future_files(){
		if(empty($this->prepared_file_array)){
			return ;
		}

		$this->processed_files->add_future_files($this->prepared_file_array);
		$this->prepared_file_array = array();
	}

	private function is_skip($file){
		$basename = basename($file);

		if ($basename == '.' || $basename == '..') {
			return true;
		}


		if (stripos($file, 'wp-time-capsule') !== false || (stripos($file, WPTC_TEMP_DIR_BASENAME) !== false && stripos($file, 'wptc_current_files_state') === false) || strpos($file, 'error_log') !== false) {
			return true;
		}


		if (stripos($file, 'wptc-secret') !== false) {
			return true;
		}

		if (false !== stripos($file, '/'. WPTC_TEMP_DIR_BASENAME .'/backups/') && false === stripos($file, 'wptc_current_files_state') ) {
			return true;
		}

		if (false !== stripos($file, 'DE_cl')) {
			return true;
		}

		if (false !== stripos($file, 'wp-tcapsule-bridge')) {
			return true;
		}

		if (false !== stripos($file, 'wptc_staging_controller')) {
			return true;
		}

		if (WPTC_Base_Factory::get('Wptc_ExcludeOption')->is_excluded_file($file)) {
			return true;
		}

		return false;
	}

	public function add_future_files_and_get_restorable_files($file, &$offset) {

		if (is_dir($file)) {
			return true;
		}

		$file = $this->config->wp_filesystem_safe_abspath_replace($file);
		$file = $this->config->replace_to_original_abspath($file);
		$file = rtrim($file, '/');

		$cur_res_b_id = $this->get_cur_res_b_id();

		$recent_revision = $this->processed_files->get_most_recent_revision($file, $cur_res_b_id);

		if ( !empty($recent_revision) ){
			return ;
		}

		$future_file['file'] = wptc_remove_fullpath($file);
		$future_file['offset'] = 0;
		$future_file['backupID'] = $cur_res_b_id;
		$future_file['revision_number'] = '';
		$future_file['revision_id'] = '';
		$future_file['mtime_during_upload'] = 0;
		$future_file['download_status'] = '';
		$future_file['uploaded_file_size'] = '';
		$future_file['g_file_id'] = '';
		$future_file['file_hash'] = '';
		$future_file['is_future_file'] = 1;
		$this->prepared_file_array[] = $future_file;
	}

	private function delete_future_files(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$offset = $this->config->get_option('delete_future_files_offset');
		$offset = empty($offset) ? 0 : $offset;

		wptc_log($offset, '--------$offset--------');

		$break = false;

		while(!$break){

			wptc_manual_debug('', 'during_delete_future_files', 100);

			$files = $this->processed_files->get_future_files($offset);

			if(empty($files)){
				break;
			}

			foreach ($files as $file) {
				$file_path = $file->file;
				$file_path = wptc_add_fullpath($file_path);
				$this->safe_unlink_file($file_path);
			}

			$offset += WPTC_RESTORE_ADDING_FILES_LIMIT;

			if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->config->set_option('delete_future_files_offset', $offset);
			$break = true;
			$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
		}

		$this->config->set_option('is_deleted_all_future_files', true);
	}

	private function get_cur_res_b_id(){

		if ($this->get_cur_res_b_id) {
			return $this->get_cur_res_b_id;
		}

		$this->get_cur_res_b_id = $this->config->get_option("cur_res_b_id");
		return $this->get_cur_res_b_id;
	}

	public function get_recorded_files_of_this_folder_to_restore_table($folder_name, $folder_res_id) {

		$result = $this->processed_files->get_sql_files_iterator_of_folder($folder_res_id, $folder_name);

		$offset = $this->config->get_option('get_recorded_files_to_restore_table');

		$offset = empty($offset) ? 0 : $offset;

		$this->prepared_file_array = array();

		$total_counter = 0;

		if($this->wpdb->use_mysqli){
			while ( $recent_revision = @mysqli_fetch_object($result)){
				$this->select_downloaded_needed_files($recent_revision, $total_counter, $offset, $folder_res_id);
			}
		} else {
			while ( $recent_revision = @mysql_fetch_object($result)){
				$this->select_downloaded_needed_files($recent_revision, $total_counter, $offset, $folder_res_id);
			}
		}

		if (!empty($this->prepared_file_array)) {
			$this->free_mysql_buffer();
			//add files to restore processing table
			$this->processed_files->add_files_for_restoring($this->prepared_file_array, $this->restore_app_functions);
		}

		$this->free_mysql_buffer();
		$this->config->set_option('get_recorded_files_to_restore_table', 0);
		$this->config->append_option_arr_bool_compat('recorded_this_selected_folder_restore', $folder_name);
	}

	public function safe_unlink_file($val) {

		$fs_safe_dir = $this->config->wp_filesystem_safe_abspath_replace(dirname($val));
		$fs_safe_file_name = $fs_safe_dir . basename($val);

		wptc_log($fs_safe_file_name, '---------------Delete file-----------------');

		wptc_log($this->wpdb->base_prefix, "--------base base_prefix--------");
		wptc_log($this->wpdb->prefix, "--------base prefix--------");

		if( is_multisite() 
			&& !$this->restore_app_functions->is_parent_multisite_site() 
			&& !$this->restore_app_functions->is_ms_and_ms_upload_dir($fs_safe_file_name) ) {
			wptc_log(array(),'-----------Skipped files from the future files----------------');

			return ;
		}

		wptc_wait_for_sometime();

		$result = $this->fs->delete($fs_safe_file_name);

		if ($fs_safe_dir == trailingslashit($this->fs->abspath()) || $fs_safe_dir == trailingslashit(WPTC_ABSPATH)) {
			return false;
		}

		$this->delete_dir_if_empty($fs_safe_dir);

		//Double check if parent folder empty then delete them too
		$this->delete_dir_if_empty(dirname($fs_safe_dir));
	}

	private function delete_dir_if_empty($fs_safe_dir){

		if(!$this->file_iterator->is_empty_folder($fs_safe_dir)){
			wptc_log(array(), '---------------Folder not empty-----------------');
			return ;
		}

		wptc_log($fs_safe_dir, '-----fs_safe_dir-----------');

		$fs_safe_dir = wptc_add_trailing_slash($fs_safe_dir);

		if ( stripos($fs_safe_dir, ABSPATH) === false || $fs_safe_dir === ABSPATH || $fs_safe_dir === WP_CONTENT_DIR) {
			wptc_log(array(), '---------------sorry, I cannot destroy home with people inside.-----------------');
			return ; //sorry, I cannot destroy home with people inside.
		}

		wptc_wait_for_sometime();

		$this->fs->delete($fs_safe_dir, true);
	}

	public function get_recorded_files_to_restore_table() {
		wptc_manual_debug('', 'get_recorded_files_to_restore_table');

		$cur_res_b_id = $this->get_cur_res_b_id();

		$offset = $this->config->get_option('get_recorded_files_to_restore_table');

		$offset = empty($offset) ? 0 : $offset;
		wptc_log($offset, '--------$offset--------');

		//get restore required files from the processed tables
		$this->analyze_sql_files_iterator = $this->processed_files->get_sql_files_iterator_for_site($cur_res_b_id, $offset);

		$this->prepared_file_array = array();

		$total_counter = 0;

		if($this->wpdb->use_mysqli){
			while ( $recent_revision = @mysqli_fetch_object($this->analyze_sql_files_iterator)){
				$this->select_downloaded_needed_files($recent_revision, $total_counter, $offset, $cur_res_b_id);
			}
		} else {
			while ( $recent_revision = @mysql_fetch_object($this->analyze_sql_files_iterator)){
				$this->select_downloaded_needed_files($recent_revision, $total_counter, $offset, $cur_res_b_id);
			}
		}

		if (!empty($this->prepared_file_array)) {
			$this->free_mysql_buffer();
			//add files to restore processing table
			$this->processed_files->add_files_for_restoring($this->prepared_file_array, $this->restore_app_functions);
			$this->prepared_file_array = array();
		}

		$this->free_mysql_buffer();
		$this->config->set_option('recorded_files_to_restore_table', 1);
	}

	private function select_downloaded_needed_files($recent_revision, &$total_counter, $offset, $cur_res_b_id){

			wptc_manual_debug('', 'select_downloaded_needed_files', 500);

			//Skip already processed files
			if ($total_counter++ < $offset) {
				return ;
			}

			$path = wptc_add_fullpath($recent_revision->file);

			$is_same_hash = $this->restore_app_functions->is_file_hash_same($path, $recent_revision->file_hash ,$recent_revision->uploaded_file_size, $recent_revision->mtime_during_upload);

			if ($is_same_hash) {
				$this->is_checking_restorable_files_timedout($total_counter);
				return ;
			}

			if (WPTC_Base_Factory::get('Wptc_ExcludeOption')->is_excluded_file($path)) {
				$this->is_checking_restorable_files_timedout($total_counter);
				return ;
			}

			// wptc_log($recent_revision, '-----file changed-----------');

			$this->prepared_file_array[$recent_revision->revision_id] 							= array();
			$this->prepared_file_array[$recent_revision->revision_id]['file'] 					= wp_normalize_path($recent_revision->file);
			$this->prepared_file_array[$recent_revision->revision_id]['uploaded_file_size'] 	= $recent_revision->uploaded_file_size;
			$this->prepared_file_array[$recent_revision->revision_id]['mtime_during_upload'] 	= $recent_revision->mtime_during_upload;
			$this->prepared_file_array[$recent_revision->revision_id]['g_file_id'] 				= $recent_revision->g_file_id;
			$this->prepared_file_array[$recent_revision->revision_id]['file_hash'] 				= $recent_revision->file_hash;
			$this->prepared_file_array[$recent_revision->revision_id]['revision_number'] 		= $recent_revision->revision_number;
			$this->prepared_file_array[$recent_revision->revision_id]['backup_id'] 				= $recent_revision->backupID;

			$this->is_checking_restorable_files_timedout($total_counter);

	}

	private function is_checking_restorable_files_timedout($offset){

		if(!$this->restore_app_functions->maybe_call_again_tc($return = true)){
			return ;
		}

		if (!empty($this->prepared_file_array)) {
			$this->free_mysql_buffer();
			//add files to restore processing table
			$this->processed_files->add_files_for_restoring($this->prepared_file_array, $this->restore_app_functions);
			$this->prepared_file_array = array();
		}

		$this->free_mysql_buffer();

		$this->config->set_option('get_recorded_files_to_restore_table', $offset);
		$this->restore_app_functions->die_with_msg("wptcs_callagain_wptce");
	}

	private function free_mysql_buffer(){

		if ($this->mysql_buffer_freed === false && !empty($this->analyze_sql_files_iterator) && method_exists($this->analyze_sql_files_iterator, 'free')) {
			$this->analyze_sql_files_iterator->free();
		}

		$this->mysql_buffer_freed = true;
	}
}

new WPTC_Restore_Download();
