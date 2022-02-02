<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPTC_Stage_To_Live{
	private $file_iterator;
	private $config;
	private $current_iterator_table;
	private $filesystem;
	private $stage_path;
	private $live_path;
	private $app_functions;
	private $copy_file_offset;
	private $file_base;
	private $processed_files;
	private $wpdb;
	private $staging_common;
	private $staging_id;
	private $exclude_class_obj;
	private $replace_links_obj;

	private $allowed_roles = array('administrator', 'editor', 'author', 'contributor');

	public function __construct(){
		$this->file_iterator = new WPTC_File_Iterator();
		$this->config = WPTC_Factory::get('config');
		$this->current_iterator_table = new WPTC_Processed_iterator();
		$this->exclude_class_obj = new Wptc_ExcludeOption($category = 'staging');
		$this->stage_path = WPTC_ABSPATH;
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->file_base = new Utils_Base();
		$this->processed_files = WPTC_Factory::get('processed-files');
		$this->staging_common = new WPTC_Stage_Common();
		$this->init_fs();
		$this->init_db();
		$this->set_live_path();
		$this->init_replace_db_link_obj();
	}

	public function to_live(){
		$this->app_functions->set_start_time();
		$this->choose_action();
	}

	public function init_db(){
		$this->wpdb = $this->staging_common->init_db();
	}

	private function init_fs(){
		$this->filesystem = $this->staging_common->init_fs();
	}

	private function init_staging_id(){
		$this->staging_id = $this->staging_common->init_staging_id();
	}

	private function init_replace_db_link_obj(){
		include_once ( WPTC_CLASSES_DIR . 'class-replace-db-links.php' );
		$this->replace_links_obj = new WPTC_Replace_DB_Links();
	}

	private function choose_action(){

		wptc_manual_debug('', 'start_S2L');

		$this->init_staging_id();

		$current_action = $this->config->get_option('s2l_current_action', true);
		$current_action = empty($current_action)? 'first_action' : $current_action;

		wptc_log($current_action, '--------$current_action--------');

		$this->config->enable_maintenance_mode($this->live_path);

		switch ($current_action) {
			case 'first_action':
				$this->flush_all_flags();
				$this->setup();
				$this->clone_db();
				break;
			case 'replace_links':
				$this->replace_links();
				break;
			case 'get_folders':
				$this->get_folders();
				break;
			case 'get_hash_by_folders':
				$this->get_hash_by_folders();
				break;
			case 'copy_files_to_live':
				$this->copy_files_to_live();
				break;
			case 'final_things_to_do';
				$this->final_things_to_do();
				break;
			default:
				wptc_log(array(), '--------Something went wrong--------');
				break;
		}

	}

	private function clone_db(){
		wptc_manual_debug('', 'start_clone_db_S2L');

		$tables = $this->get_all_tables();

		$limit = $this->config->get_option('internal_staging_db_rows_copy_limit');

		if (empty($limit)) {
			$limit = WPTC_STAGING_DEFAULT_COPY_DB_ROWS_LIMIT; //fallback to default value
		}

		wptc_log($tables, '--------$tables--------');
		$live_prefix =  $this->config->get_option('s2l_live_db_prefix');

		foreach ($tables as $table) {
			$table_skip_status = $this->exclude_class_obj->is_excluded_table($table);

			if ($table_skip_status === 'table_excluded') {
				wptc_log($table, '---------------$table excluded from staging-----------------');
				continue;
			}

			wptc_log($table, '--------$table--------');
			$new_table = str_ireplace($this->wpdb->base_prefix, $live_prefix, $table);
			wptc_log($new_table, '--------$new_table--------');

			if ($this->current_iterator_table->is_complete($table)) {
				continue;
			}

			wptc_manual_debug('', 'during_clone_table_S2L_' . $table);

			$table_meta = $this->staging_common->get_table_data($table);

			//spawning var like $limit and $offset
			extract($table_meta);

			if ($is_new) {
				$this->staging_common->clone_table_structure($table, $new_table);
			}

			if( $table_skip_status === 'content_excluded'){
				$this->current_iterator_table->update_iterator($table, -1); //Done
				wptc_log($table,'-----------content excluded so skip now----------------');
				continue;
			}

			$this->staging_common->clone_table_content($table, $new_table, $limit, $offset);

			$this->current_iterator_table->update_iterator($table, -1); //Done
		}

		wptc_manual_debug('', 'end_clone_db_S2L');
		$this->config->set_option('s2l_current_action', 'replace_links');
		$this->app_functions->die_with_json_encode(array('status' => 'continue', 'msg' => 'Database is cloned', 'percentage' => 10));
	}

	private function get_all_tables(){
		$wp_tables = $this->processed_files->get_all_tables();
		$site_tables = array();
		foreach ($wp_tables as $table) {
			if(stripos($table, $this->wpdb->base_prefix) !== false){
				$site_tables[] = $table;
			}
		}

		return $site_tables;
	}

	private function replace_links(){
		wptc_manual_debug('', 'start_replace_links_S2L');

		$this->replace_deep_links();

		wptc_manual_debug('', 'start_replace_site_links_S2L');

		$this->replace_site_links();

		wptc_manual_debug('', 'end_replace_site_links_S2L');

		wptc_manual_debug('', 'end_replace_links_S2L');
		$this->config->set_option('s2l_current_action', 'get_folders');
		$this->app_functions->die_with_json_encode(array('status' => 'continue', 'msg' => 'Database links are replaced.', 'percentage' => 20));
	}

	private function replace_site_links(){
		$new_prefix =  $this->config->get_option('s2l_live_db_prefix');
		$new_url = $this->config->get_option('s2l_live_url');

		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				'update ' . $new_prefix . 'options set option_value = %s where option_name = \'siteurl\' or option_name = \'home\'',
				$new_url
			)
		);

		if (!$result) {
			$error = isset($this->wpdb->error) ? $this->wpdb->error : '';
			// $this->logger->log('Replacing site url has been failed.' . $error, 'staging', $this->staging_id);
			wptc_log('Replacing site url has been failed. ' . $error, '--------FAILED----------');
		} else {
			// $this->logger->log('Replacing siteurl has been done succesfully', 'staging', $this->staging_id);
			wptc_log('Replacing siteurl has been done succesfully', '--------SUCCESS----------');
		}

		//Update rewrite_rules in clone options table
		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				'update ' . $new_prefix . 'options set option_value = %s where option_name = \'rewrite_rules\'',
				''
			)
		);

		if (!$result) {
			wptc_log("Updating option[rewrite_rules] not successfull, likely the main site is not using permalinks", '--------FAILED-------------');
		} else {
			wptc_log("Updating option [rewrite_rules] has been done succesfully", '--------SUCCESS-------------');
		}

		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				"update  ". $new_prefix . "options set option_name = '" . $new_prefix . "user_roles' where option_name = '".$this->wpdb->prefix."user_roles' limit 1", ''
			)
		);

		if (!$result) {
			$error = isset($this->wpdb->error) ? $this->wpdb->error : '';
			// $this->logger->log('User roles modification has been failed.' . $error, 'staging', $this->staging_id);
			wptc_log("User roles modification has been failed", '--------FAILED-------------');
		} else {
			// $this->logger->log('User roles has been modified succesfully', 'staging', $this->staging_id);
			wptc_log("User roles has been modified succesfully", '--------SUCCESS-------------');
		}


		//replace table prefix in meta_keys
		$usermeta_sql = $this->wpdb->prepare(
				'update ' . $new_prefix . 'usermeta set meta_key = replace(meta_key, %s, %s) where meta_key like %s',
				$this->wpdb->base_prefix,
				$new_prefix,
				$this->wpdb->base_prefix . '_%'
			);
		wptc_log($usermeta_sql, '--------$usermeta_sql--------');

		$result_usermeta = $this->wpdb->query( $usermeta_sql );

		wptc_log($result_usermeta, '--------$result_usermeta--------');

		$options_sql = $this->wpdb->prepare(
				'update ' . $new_prefix . 'options set option_name = replace(option_name, %s, %s) where option_name like %s',
				$this->wpdb->base_prefix,
				$new_prefix,
				$this->wpdb->base_prefix . '_%'
			);
		$result_options = $this->wpdb->query( $options_sql );
		wptc_log($options_sql, '--------$options_sql--------');
		wptc_log($result_options, '--------$result_options--------');

		if ($result_options === false || $result_usermeta === false) {
			// $this->logger->log("Updating table $new_prefix has been failed.". $this->wpdb->last_error, 'staging', $this->staging_id);
			wptc_log("Updating db prefix $new_prefix has been failed.". $this->wpdb->last_error, '-----------FAILED----------');
		} else {
			// $this->logger->log('Updating db prefix "' . $this->wpdb->base_prefix . '" to  "' . $new_prefix . '" has been done succesfully.', 'staging', $this->staging_id);
			wptc_log('Updating db prefix "' . $this->wpdb->base_prefix . '" to  "' . $new_prefix . '" has been done succesfully.', '--SUCCESS-------------------');
		}

	}

	private function replace_deep_links(){
		$replace_deep_links = $this->config->get_option('s2l_deep_links_completed');

		if ($replace_deep_links) {
			return false;
		}

		$raw_result = $this->config->get_option('same_server_replace_old_url_data');
		$tables = false;
		if (!empty($raw_result)) {
			$tables = @unserialize($raw_result);
		}

		$old_url       = site_url();
		$new_url       = $this->config->get_option('s2l_live_url');
		$new_site_url  = $this->config->get_option('s2l_site_url');
		$old_file_path = $this->stage_path;
		$new_file_path = $this->live_path;

		wptc_log($old_url, '---------------$old_url-----------------');
		wptc_log($new_url, '---------------$new_url-----------------');
		wptc_log($old_file_path, '---------------$old_file_path-----------------');
		wptc_log($new_file_path, '---------------$new_file_path-----------------');
		$table_prefix =  $this->config->get_option('s2l_live_db_prefix');

		$this->replace_links_obj->replace_uri($old_url, $new_url, $old_file_path, $new_file_path, $table_prefix, $tables, $new_site_url, 'staging_to_live');

		$this->config->set_option('s2l_deep_links_completed', true);
	}

	private function get_folders(){
		wptc_manual_debug('', 'start_get_folders_S2L');

		$this->file_iterator->get_folders();

		wptc_manual_debug('', 'end_get_folders_S2L');
		$this->config->set_option('s2l_current_action', 'get_hash_by_folders');
		$this->app_functions->die_with_json_encode(array('status' => 'continue', 'msg' => 'Processing files...', 'percentage' => 25));
	}

	private function get_hash_by_folders(){
		wptc_manual_debug('', 'start_get_hash_by_folder_S2L');

		$break = false;
		$loop = $temp_counter = 0;

		while(!$break){
			$dir_meta = $this->current_iterator_table->get_unfnished_folder();
			// wptc_log($dir_meta, '--------$dir_meta--------');
			$deep_dirs = false;
			if (empty($dir_meta) || $dir_meta->offset === -1) {
				$break = true;
				continue;
			}

			$relative_path = wp_normalize_path($dir_meta->name);

			$path = wptc_add_fullpath($relative_path);


			if( array_search($path, $this->file_iterator->get_deep_dirs()) !== false ){
				$deep_dirs = true;
			}

			// wptc_log(array(), '--------Running--------');
			if(wptc_is_dir($path)){
				$this->get_hash_dir($relative_path, $dir_meta->offset, $temp_counter, $deep_dirs);
			} else {
				$this->get_hash_file($relative_path, $dir_meta->offset, $temp_counter);
			}
		}
		wptc_manual_debug('', 'end_get_hash_by_folder_S2L');
		$this->config->set_option('s2l_current_action', 'copy_files_to_live');
		$this->app_functions->die_with_json_encode(array('status' => 'continue', 'msg' => 'Copying files...', 'percentage' => 50));
	}

	public function process_file($iterator, $is_recursive, $path, &$counter, $iterator_loop_limit, &$query, $key) {
		wptc_manual_debug('', 'during_process_file_S2L', 500);

		$file = $iterator->getPathname();

		if (!$iterator->isReadable()) {
			return ;
		}

		$file = wp_normalize_path($file);

		if (!$is_recursive && wptc_is_dir($file)){
			wptc_log($file, '--------skip because of deep dir--------');
			return ;
		}

		if ($this->is_skip($file)) {
			$this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
			return ;
		}


		if(!$this->is_file_modified($file)){
			// wptc_log(array(), '--------SKIP FILE 2--------');
			$this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
			return ;
		}

		wptc_log($file, '--------File Modified--------');

		$relative_path = wptc_remove_fullpath( $file );

		$query .= empty($query) ? "(" : ",(" ;

		$query .= $this->wpdb->prepare("%s, 'Q', NULL)", $relative_path);

		$this->app_functions->check_timeout_iter_file($path, $counter, $iterator_loop_limit, $query, $key);
	}

	private function get_hash_dir($path, $offset, &$temp_counter, $deep_dirs){

		$seek_file_iterator = new WPTC_Seek_Iterator($this, $type = "STAGING_TO_LIVE", 500, $category = 'staging');

		$is_recursive = ($deep_dirs) ? false : true;

		try{
			$seek_file_iterator->process_iterator($path, $offset, $is_recursive);
		} catch(Exception $e){

			$exception_msg = $e->getMessage();
			wptc_log($exception_msg, '---------------Exception-----------------');

			if (!wptc_is_seeking_exception($exception_msg)) {
				$this->config->disable_maintenance_mode($this->live_path);
				$this->app_functions->die_with_json_encode( array('status' => 'error' , 'msg' => $exception_msg) );
			}

			wptc_log($path, '---------------Retry Seeking-----------------');
			$this->current_iterator_table->update_iterator($path, 0);
			$this->app_functions->die_with_json_encode( array( 'status' => 'continue', 'msg' => 'Seeking failed, Retrying...' ) );
		}

		$this->current_iterator_table->update_iterator($path, -1);
	}

	private function get_hash_file($file){
	}

	private function is_file_modified($stage_file){
		if(empty($stage_file)){
			return false;
		}

		$live_file = $this->convert_staging_to_live($stage_file);

		return $this->is_hash_modified($stage_file, $live_file);
	}

	private function convert_staging_to_live($path){
		if (wptc_is_wp_content_path($path) && wptc_is_wp_content_dir_moved_outside_root($this->config) ) {
			return wp_normalize_path(str_replace($this->stage_path, dirname($this->live_content_dir) . '/', $path));
		}

		return wp_normalize_path(str_replace($this->stage_path, $this->live_path, $path));
	}

	private function is_hash_modified($stage_file, $live_file){
		if (!file_exists($live_file)) {
			return true;
		}

		if(!wptc_is_hash_required($stage_file)){
			wptc_log(array(), '---------cannot find has so checking is_size_mismatch------------');
			return $this->is_size_mismatch($stage_file, $live_file);
		}

		$stage_file_hash = wptc_get_hash($stage_file);
		$live_file_hash  = wptc_get_hash($live_file);

		if ($stage_file_hash == $live_file_hash) {
			return false;
		}

		return true;
	}

	private function is_skip($file){
		if(wptc_is_dir($file)){
			return true;
		}

		if($this->is_wptc_dir($file)){
			return true;
		}

		if (strpos($file, '.htaccess') !== false) {
			return true;
		}

		if ($this->exclude_class_obj->is_excluded_file($file)) {
			return true;
		}

		return false;

	}

	private function is_wptc_dir($file){
		if(stripos($file, WPTC_PLUGIN_DIR) !== false){
			return true;
		}

		return false;
	}

	private function is_size_mismatch($stage_file, $live_file){

		if(!is_readable($stage_file) || !is_readable($live_file))
			return false;

		if(filesize($stage_file) == filesize($live_file))
			return false;

		return true;
	}

	private function set_live_path(){
		if (!empty($this->live_path)) {
			return false;
		}

		$this->live_content_dir = $this->config->get_option('s2l_wp_content_dir') . '/';
		$this->live_path        = $this->config->get_option('s2l_live_path');
	}

	private function get_queued_files(){

		$offset = $this->config->get_option('s2l_copy_files_offset');

		$this->copy_file_offset = empty($offset) ? 0 : $offset;

		$limit = $this->config->get_option('internal_staging_file_copy_limit');

		$limit = empty($limit) ? WPTC_STAGING_DEFAULT_FILE_COPY_LIMIT : $limit;

		$sql = "SELECT file_path FROM `".$this->wpdb->base_prefix."wptc_current_process` WHERE `status` = 'Q' ORDER BY `id` LIMIT " . $this->copy_file_offset . ", " . $limit . "";

		return $this->wpdb->get_results($sql, ARRAY_N);
	}

	private function copy_files_to_live(){
		wptc_manual_debug('', 'start_copy_files_S2L');

		$queue_files = $this->get_queued_files();

		if(empty($queue_files)){
			$this->config->set_option('s2l_current_action', 'final_things_to_do');
			$this->app_functions->die_with_json_encode(array('status' => 'continue', 'msg' => 'Files are copied successfully.', 'percentage' => 90));
		}

		wptc_log($queue_files, '--------$queue_files--------');

		$counter = 0;
		foreach ($queue_files as $key => $file) {

			$this->replace_links_obj->make_cpu_idle();

			wptc_manual_debug('', 'start_copy_files_S2L', 500);

			$counter++ ;
			$stage_file = $file[0];

			$stage_file = wptc_add_fullpath($stage_file);

			wptc_log($this->stage_path, '--------$this->stage_path--------');
			wptc_log($this->live_path, '--------$this->live_path--------');

			$live_file = $this->convert_staging_to_live($stage_file);

			wptc_log($stage_file, '--------$file--------');
			wptc_log($live_file, '--------$live_file--------');

			$live_dir = dirname($live_file);

			$this->make_dir_by_path($live_dir);

			if (!file_exists($live_dir)) {
				wptc_log($live_dir, '--------Could not create folder - --------');
					continue;
			}

			if(filesize($stage_file) < WPTC_STAGING_COPY_SIZE){

				$copy_status = $this->filesystem->copy($stage_file, $live_file, true, FS_CHMOD_FILE);
				wptc_log($copy_status, '--------$copy_status--------');
				if (!$copy_status){
					wptc_log(array(), '--------Could not copy this file--------');
					wptc_log(error_get_last(), '---------------error_get_last()-----------------');
				}

			} else {
				$copy_status = wptc_copy_large_file($stage_file, $live_file);
				wptc_log($copy_status, '--------$copy_status--------');
				if (!$copy_status){
					wptc_log(array(), '--------Could not copy this file--------');
					wptc_log(error_get_last(), '---------------error_get_last()-----------------');
				}
			}

			$break = is_wptc_timeout_cut();
			if ($break) {
				$this->config->set_option('s2l_copy_files_offset', $this->copy_file_offset + $counter);
				$this->app_functions->die_with_json_encode(array("status" => "continue", 'msg' => 'Copying files ('. ($this->copy_file_offset + $counter) .')', 'percentage' => 80));
			}
		}
		wptc_log($counter, '--------$counter--------');
		wptc_log($this->copy_file_offset, '--------$this->copy_file_offset--------');

		wptc_manual_debug('', 'end_copy_files_S2L');

		$this->config->set_option('s2l_copy_files_offset', $this->copy_file_offset + $counter);
		$this->app_functions->die_with_json_encode(array("status" => "continue", 'msg' => 'Copying files ('. ($this->copy_file_offset + $counter) .')', 'percentage' => 80));
	}

	private function make_dir_by_path($path, $recursive = true){
		$path = wp_normalize_path($path);
		$this->file_base->createRecursiveFileSystemFolder($path, false, false);
	}

	private function final_things_to_do(){

		wptc_manual_debug('', 'start_final_things_todo_S2L');
		$new_prefix = $this->config->get_option('s2l_live_db_prefix');
		$new_site_url = $this->config->get_option('s2l_live_url');

		//change wp-config.php
		$this->change_wp_config($new_prefix, $new_site_url);
		wptc_log(array(), '--------1--------');
		$this->remove_robot_txt();

		$this->encourage_search_engine($new_prefix);

		//multisite changes
		if (is_multisite()) {
			$this->replace_links_obj->multi_site_db_changes($new_prefix, $new_site_url, get_home_url());
		}

		$this->sub_dir_actions($new_prefix);

		wptc_manual_debug('', 'end_final_things_todo_S2L');

		$this->completed();

		wptc_manual_debug('', 'completed_S2L');

		$this->config->disable_maintenance_mode($this->live_path);

		$this->app_functions->die_with_json_encode(array("status" => "completed", 'msg' => 'Copied to live site successfully !', 'percentage' => 100, 'time' => $this->get_last_time_copy_to_live()));
	}

	private function sub_dir_actions($new_prefix){
		wptc_log(func_get_args(),'-----------sub_dir_actions func_get_args()----------------');
		if (!$this->config->get_option('s2l_is_subdir_installation')) {
			return ;
		}

		$table = $new_prefix . 'options';

		$site_url = $this->config->get_option('s2l_site_url');


		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				'update ' . $table . ' set option_value = %s where option_name = \'siteurl\'',
				$site_url
			)
		);

		wptc_log($result,'----------sub_dir_actions -$result----------------');

	}

	private function encourage_search_engine($new_prefix){

		if (!is_multisite()) {
			$table = $new_prefix . 'options';
			$this->revert_search_engine($table);
			$this->revert_permalink($table);
			return false;
		}

		$new_prefix = (string) $new_prefix;
		$wp_tables = $this->processed_files->get_all_tables();

		foreach ($wp_tables as $table) {
			if (stripos($table, 'options') === false || stripos($table, $new_prefix) !== 0) {
				continue;
			}

			$this->revert_search_engine($table);
			$this->revert_permalink($table);
		}
	}

	private function revert_permalink($table){
		$old_permalink_structure = $this->config->get_option('s2l_live_permalink_structure');
		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				'update ' . $table . ' set option_value = %s where option_name = \'permalink_structure\'',
				$old_permalink_structure
			)
		);
	}

	private function revert_search_engine($table){
		$result = $this->wpdb->query(
			$this->wpdb->prepare(
				'update ' . $table . ' set option_value = %s where option_name = \'blog_public\'',
				1
			)
		);
	}

	private function change_wp_config($new_prefix, $new_site_url){
		$this->replace_links_obj->modify_wp_config(
			array(
				'old_url' =>  site_url(),
				'new_url' =>  $new_site_url,
				'new_path' => $this->live_path,
				'old_path' => $this->stage_path,
				'new_prefix' =>  $new_prefix,
			), 'STAGING_TO_LIVE'
		);
	}

	private function remove_robot_txt(){
		$delete_status = $this->filesystem->delete($this->live_path.'robots.txt');
		wptc_log($delete_status, '--------$delete_status--------');
	}

	private function completed(){
		$live_prefix =  $this->config->get_option('s2l_live_db_prefix');

		//deactivate staging plugin in live site
		$this->app_functions->run_deactivate_plugin('wp-time-capsule-staging/wp-time-capsule-staging.php', $live_prefix);
		$this->app_functions->run_activate_plugin('wp-time-capsule/wp-time-capsule.php', $live_prefix);

		WPTC_Base_Factory::get('Wptc_Backup_Analytics')->send_report_data($this->staging_id, 'STAGING_TO_LIVE', 'SUCCESS');

		$this->config->set_option('s2l_last_copied_time', time());
		$this->config->set_option('s2l_current_action', false);
		$this->config->set_option('same_server_replace_old_url', false);
		$this->config->set_option('same_server_replace_old_url_data', false);
		$this->config->set_option('same_server_replace_url_multicall_status', false);
		$this->config->set_option('s2l_copy_files_offset', false);
		$this->config->set_option('s2l_deep_links_completed', false);
		$this->config->set_option('same_server_clear_flags', false);
		$this->config->set_option('same_server_setup', false);
		$this->wpdb->query("TRUNCATE TABLE `".$this->wpdb->base_prefix."wptc_current_process`;");
		$this->wpdb->query("TRUNCATE TABLE `".$this->wpdb->base_prefix."wptc_processed_iterator`;");
	}

	private function flush_all_flags(){
		if ($this->config->get_option('same_server_clear_flags')) {
			return false;
		}

		$this->config->set_option('same_server_replace_old_url', false);
		$this->config->set_option('same_server_replace_old_url_data', false);
		$this->config->set_option('same_server_replace_url_multicall_status', false);
		$this->config->set_option('same_server_clear_flags', true);
		wptc_manual_debug('', 'flush_flags_S2L');
	}

	private function setup(){
		if ($this->config->get_option('same_server_setup')) {
			return false;
		}

		wptc_log(array(), '--------SETUP STARTED--------');

		WPTC_Base_Factory::get('Wptc_Backup_Analytics')->send_report_data($this->staging_id, 'STAGING_TO_LIVE', 'STARTED');

		$this->config->set_option('same_server_setup', true);
	}

	public function check_permissions() {
		if (!$this->should_disable_login())
			return false;

		die( sprintf ( __('Access denied. You need to <a href="%1$s" target="_blank">Login</a> first','wptc'), get_admin_url() ) );
	}

	private function should_disable_login(){
		return (
			$this->is_staging_site() &&
			$this->staging_completed() &&
			$this->config->get_option('internal_staging_enable_admin_login') === 'yes' &&
			!$this->is_wptc_request() &&
			(!$this->is_allowed_role() && !$this->is_login_page() && !is_admin())
		);
	}

	private function restrictly_get_current_user_role() {
		if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$role = ( array ) $user->roles;
				return $role[0];
				wptc_log($role[0],'-----------$role[0]----------------');
		} else {
			return false;
		}
 	}

	private function is_allowed_role(){
		return in_array($this->restrictly_get_current_user_role(), $this->allowed_roles);
	}

	private function is_staging_site(){
		return true;
	}

	private function is_wptc_request(){

		if(empty($_REQUEST['data']) || !empty($_REQUEST['action'])){
			return false;
		}

		$is_wptc_request = @unserialize(base64_decode($_REQUEST['data']));

		wptc_log($is_wptc_request, '---------------$is_wptc_request-----------------');

		if (empty($is_wptc_request) || empty($is_wptc_request['action']) || $is_wptc_request['action'] !== 'update_in_staging' )  {
			return false;
		}

		return true;
	}

	private function staging_completed(){
		$live_prefix = $this->config->get_option('s2l_live_db_prefix');

		$sql = "SELECT value FROM `".$live_prefix."wptc_options` WHERE `name` = 'same_server_staging_status'";
		wptc_log($sql, '--------$sql--------');
		$status = $this->wpdb->get_var($sql);
		wptc_log($status, '--------$status--------');

		if ($status === 'staging_completed') {
			return true;
		}
	}

	private function is_login_page(){

		if (!$this->config->get_option('staging_login_custom_link')) {
			wptc_log($GLOBALS["_SERVER"]["REQUEST_URI"],'-----------$GLOBALS["_SERVER"]["REQUEST_URI"]----------------');
			if (strstr($GLOBALS["_SERVER"]["REQUEST_URI"], 'wp-admin')){
				return true;
			}
			return (in_array($GLOBALS["pagenow"], array("wp-login.php")));
		}

		return (strstr($GLOBALS["_SERVER"]["REQUEST_URI"], $this->config->get_option('staging_login_custom_link')));
	}

	public function get_last_time_copy_to_live(){
		$time = $this->config->get_option('s2l_last_copied_time');
		if(empty($time))
			return "none";

		return user_formatted_time_wptc($time);
	}

	public function change_sitename() {

		if(!$this->is_staging_site() ) 	return false;

		global $wp_admin_bar;
		// Main Title
		$wp_admin_bar->add_menu( array(
			'id' => 'site-name',
			'title' => is_admin() ? ('STAGING - ' . get_bloginfo( 'name' ) ) : ( 'STAGING - ' . get_bloginfo( 'name' ) . ' Dashboard' ),
			'href' => is_admin() ? home_url( '/' ) : admin_url(),
		) );
	}

	public function is_allowed_to_copy_site_to_live() {
		$live_prefix = $this->config->get_option('s2l_live_db_prefix');
		$sql = "SELECT value FROM `" . $live_prefix . "wptc_options` WHERE `name` = 'privileges_args'";
		$raw_result = $this->wpdb->get_var($sql);

		$object = json_decode($raw_result);
		$features = json_decode(json_encode($object), true);

		$is_allowed = isset($features['Wptc_Staging_To_Live']);

		wptc_log($is_allowed,'-----------$is_allowed----------------');

		return $is_allowed;
	}

	public function get_user_excluded_extenstions_s2l()
	{
		return $this->config->get_option('user_excluded_extenstions_staging');
	}
}
