<?php

class Restore_Progress{
	private $config,
			$restore_app_functions,
			$app_functions,
			$wpdb;

	public function __construct(){
		//allow ajax calls
		header('Access-Control-Allow-Origin: *');

		//create object for restore app common
		$this->init_restore_app_functions();

		//accept only wptc requests
		$this->restore_app_functions->verify_request();

		//define restore constant to override other functions
		$this->restore_app_functions->define_constants($enable_bridge_alone = true);

		//restore needed files
		$this->include_files();

		//start database connections
		$this->restore_app_functions->init_db_connection();

		$this->config = WPTC_Factory::get('config');

		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');

		$this->init_wpdb();

		$this->get_progress();
	}

	private function init_restore_app_functions(){
		//common app functions for restore
		require_once dirname(__FILE__). '/' ."wptc-restore-app-functions.php";
		$this->restore_app_functions = new WPTC_Restore_App_Functions();
	}

	private function include_files(){
		require_once dirname(__FILE__). '/' ."common_include_files.php";
		$this->common_include_files = new Common_Include_Files('restore-progress');
		$this->common_include_files->init();
	}

	private function init_wpdb(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	private function get_progress(){

		if (!$this->config->get_option('live_files_to_restore_table')) {
			$this->get_processing_files_status();
		}

		if (!$this->config->get_option('is_deleted_all_future_files')) {
			$this->get_future_files_deleting_status();
		}

		if (!$this->config->get_option('recorded_files_to_restore_table')) {
			$this->get_dead_processing_files_count();
		}

		if (!$this->config->get_option('is_bridge_process')) {
			$this->get_download_status();
		}

		if ($this->config->get_option('restore_full_db_process')) {
			$this->get_db_restore_status();
		}

		$this->get_copy_status();
	}

	private function get_total_files_count(){
		$total_files = $this->wpdb->get_var("SELECT count(*) FROM {$this->wpdb->base_prefix}wptc_processed_restored_files");
		return ($total_files) ? $total_files : 0 ;
	}

	private function get_alive_processing_files_count(){
		$this->app_functions->get_processing_files_count('restore');
	}

	private function get_analyze_processing_files_count(){
		$count = $this->config->get_option('get_recorded_files_to_restore_table');
		return empty($count) ? 0 : $count;
	}

	private function get_delete_future_files_offset(){
		$count = $this->config->get_option('delete_future_files_offset');
		$count = empty($count) ? 0 : $count;
		return $count * WPTC_RESTORE_ADDING_FILES_LIMIT;
	}

	private function is_deleted_all_future_files(){
		return !$this->config->get_option('is_deleted_all_future_files');
	}

	private function get_downloaded_files_count(){
		$current_restore_file_id = $this->config->get_option('restore_downloaded_files_count');
		$current_restore_file_id = empty($current_restore_file_id) ? 0 : $current_restore_file_id;

		if (empty($current_restore_file_id)) {
			return 0;
		}

		$downloaded_files_count = $this->wpdb->get_var("SELECT count(*) FROM {$this->wpdb->base_prefix}wptc_processed_restored_files WHERE file_id <= " . $current_restore_file_id);
		return empty($downloaded_files_count) ? 0 : $downloaded_files_count;
	}

	private function get_restore_db_index(){
		$count = $this->config->get_option('restore_db_index');
		return empty($count) ? 0 : $count;
	}

	private function get_copied_files_count(){
		$count = $this->config->get_option('restore_copied_files_count');
		return empty($count) ? 0 : $count;
	}

	private function get_percentage($number, $total){
		if (empty($total)) {
			return 0;
		}

		$percentage = ($number / $total) * 100;

		return round($percentage, 2);
	}

	private function get_download_status(){
		$response = array();

		if (!$this->config->get_option('in_progress_restore')) {
			$this->restore_app_functions->die_with_msg($response);
		}

		$total_files_count 			= $this->get_total_files_count();
		$total_files_count 			= empty($total_files_count) ? 0 : $total_files_count;

		$downloaded_files_count 	= $this->get_downloaded_files_count();

		$downloaded_files_percent 	= $this->get_percentage($downloaded_files_count, $total_files_count);

		// if ($downloaded_files_percent > 100) {
		// 	$downloaded_files_percent = 100;
		// }

		$response['status'] = 'download';
		$response['msg'] = empty($downloaded_files_percent) ? 'Warping back in time... Hold on tight!' : 'Warping back in time... Hold on tight! ('.$downloaded_files_percent.'%)';
		$response['percentage'] = $downloaded_files_percent;

		$this->restore_app_functions->die_with_msg($response);
	}

	private function get_copy_status(){
		$response = array();

		$total_files_count 		= $this->get_total_files_count();

		$copied_files_count 	= $this->get_copied_files_count();

		$copied_files_percent 	= $this->get_percentage($copied_files_count, $total_files_count);

		$response['status'] = 'copy';
		$response['msg'] = empty($copied_files_percent) ? 'Copying Files... Hold on tight!' : 'Copying Files... Hold on tight! ('.$copied_files_percent.'%)' ;
		$response['percentage'] = $copied_files_percent;

		$this->restore_app_functions->die_with_msg($response);
	}

	private function get_processing_files_status(){
		$msg = $this->app_functions->get_processing_files_count('restore');
		$msg = empty($msg) ? 'Preparing files to restore...' : $msg;
		$response['status'] = 'process';
		$response['msg'] = $msg;
		$this->restore_app_functions->die_with_msg($response);
	}

	private function get_dead_processing_files_count(){
		$analyzing_files_count = $this->get_analyze_processing_files_count();
		$response['status'] = 'analyze';
		$response['msg'] = 'Analyzing files to restore ('. $analyzing_files_count .')';
		$this->restore_app_functions->die_with_msg($response);
	}

	private function get_future_files_deleting_status(){
		$analyzing_files_count = $this->get_delete_future_files_offset();
		$response['status'] = 'delete';
		$response['msg'] = 'Deleting untracked files ('. $analyzing_files_count .')';
		$this->restore_app_functions->die_with_msg($response);
	}

	private function get_db_restore_status(){
		$db_row_index = $this->get_restore_db_index();
		$response['status'] = 'restore';
		$response['msg'] =  $db_row_index .' rows imported';
		$this->restore_app_functions->die_with_msg($response);
	}
}

new Restore_Progress();