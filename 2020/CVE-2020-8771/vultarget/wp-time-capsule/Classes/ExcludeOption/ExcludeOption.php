<?php

class Wptc_ExcludeOption extends Wptc_Exclude {
	protected $config;
	protected $logger;
	private $cron_server_curl;
	private $default_wp_folders;
	private $default_wp_files;
	private $db;
	private $default_exclude_files;
	private $processed_files;
	private $bulk_limit;
	private $default_wp_files_n_folders;
	private $excluded_files;
	private $included_files;
	private $excluded_tables;
	private $included_tables;
	private $max_table_size_allowed = 104857600; //100 MB
	private $max_file_size_allowed  = 52428800; //50 MB
	private $key_recursive_seek;
	private $file_list;
	private $app_functions;
	private $category;
	private $analyze_files_response = array();
	private $skip_tables = array(
						'blc_instances',
						'bwps_log',
						'Counterize',
						'Counterize_Referers',
						'Counterize_UserAgents',
						'et_bloom_stats',
						'itsec_log',
						'lbakut_activity_log',
						'redirection_404',
						'redirection_logs',
						'relevanssi_log',
						'simple_feed_stats',
						'slim_stats',
						'statpress',
						'svisitor_stat',
						'tts_referrer_stats',
						'tts_trafficstats',
						'wbz404_logs',
						'wbz404_redirects',
						'woocommerce_sessions',
						'wponlinebackup_generations',
						'wysija_email_user_stat',
						'bv_fw_requests',
						'bv_ip_store',
						'bv_lp_requests',
						'affiliate_wp_campaigns'
					);

	public function __construct($category = 'backup') {
		$this->category = $category;
		$this->db = WPTC_Factory::db();
		$this->bulk_limit = 500;
		$this->processed_files = WPTC_Factory::get('processed-files');
		$this->default_exclude_files = get_dirs_to_exculde_wptc();

		$this->default_wp_folders = array(
						WPTC_RELATIVE_ABSPATH . 'wp-admin',
						WPTC_RELATIVE_ABSPATH . 'wp-includes',
						WPTC_RELATIVE_WP_CONTENT_DIR,
					);
		$this->default_wp_files = array(
						WPTC_RELATIVE_ABSPATH . 'favicon.ico',
						WPTC_RELATIVE_ABSPATH . 'index.php',
						WPTC_RELATIVE_ABSPATH . 'license.txt',
						WPTC_RELATIVE_ABSPATH . 'readme.html',
						WPTC_RELATIVE_ABSPATH . 'robots.txt',
						WPTC_RELATIVE_ABSPATH . 'sitemap.xml',
						WPTC_RELATIVE_ABSPATH . 'wp-activate.php',
						WPTC_RELATIVE_ABSPATH . 'wp-blog-header.php',
						WPTC_RELATIVE_ABSPATH . 'wp-comments-post.php',
						WPTC_RELATIVE_ABSPATH . 'wp-config-sample.php',
						WPTC_RELATIVE_ABSPATH . 'wp-config.php',
						WPTC_RELATIVE_ABSPATH . 'wp-cron.php',
						WPTC_RELATIVE_ABSPATH . 'wp-links-opml.php',
						WPTC_RELATIVE_ABSPATH . 'wp-load.php',
						WPTC_RELATIVE_ABSPATH . 'wp-login.php',
						WPTC_RELATIVE_ABSPATH . 'wp-mail.php',
						WPTC_RELATIVE_ABSPATH . 'wp-settings.php',
						WPTC_RELATIVE_ABSPATH . 'wp-signup.php',
						WPTC_RELATIVE_ABSPATH . 'wp-trackback.php',
						WPTC_RELATIVE_ABSPATH . 'wp-salt.php',//some people added this file in wp-config.php
						WPTC_RELATIVE_ABSPATH . 'xmlrpc.php',
						WPTC_RELATIVE_ABSPATH . '.htaccess',
						WPTC_RELATIVE_ABSPATH . 'google',//google analytics files
						WPTC_RELATIVE_ABSPATH . 'gd-config.php',//go daddy configuration file
						WPTC_RELATIVE_ABSPATH . 'wp',//including all wp files on root
						WPTC_RELATIVE_ABSPATH . '.user.ini',//User custom settings / WordFence Files
						WPTC_RELATIVE_ABSPATH . 'wordfence-waf.php',//WordFence Files
					);
		$this->force_exclude_folders = array(
						WPTC_RELATIVE_ABSPATH . 'wp-tcapsule-bridge',
		);

		if($category == 'staging'){
			$extra_files = get_extra_files_to_exclude_for_staging();
			$this->force_exclude_folders = array_merge($this->force_exclude_folders, $extra_files);
		}
		
		$this->default_wp_files_n_folders = array_merge($this->default_wp_folders, $this->default_wp_files);
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->config = WPTC_Base_Factory::get('Wptc_Exclude_Config');
		$this->file_list = WPTC_Factory::get('fileList');
		$this->load_saved_keys($category);
	}

	private function load_saved_keys($category){
		if (!$this->app_functions->table_exist($this->db->base_prefix . 'wptc_inc_exc_contents')) {
			return ;
		}

		$this->load_exc_inc_files($category);
		$this->load_exc_inc_tables($category);
	}

	private function load_saved_keys_manually(){

		if (empty($_GET['category'])) {
			return ;
		}

		$this->load_exc_inc_files($_GET['category']);
		$this->load_exc_inc_tables($_GET['category']);
	}

	private function load_exc_inc_files($category){
		$this->excluded_files = $this->get_keys($type = 'file' , $action = 'exclude', $category);
		$this->included_files = $this->get_keys($type = 'file' , $action = 'include', $category);
	}

	private function load_exc_inc_tables($category){
		$this->excluded_tables = $this->get_keys($type = 'table' , $action = 'exclude', $category);
		$this->included_tables = $this->get_keys($type = 'table' , $action = 'include', $category);
	}

	public function insert_default_excluded_files(){
		$status = $this->config->get_option('insert_default_excluded_files');

		if ($status) {
			return false;
		}

		$files = $this->format_excluded_files($this->default_exclude_files);

		foreach ($files as $file) {
			$file['category'] = 'backup';
			$this->exclude_file_list($file, true);

			$file['category'] = 'staging';
			$this->exclude_file_list($file, true);
		}

		$this->config->set_option('insert_default_excluded_files', true);
	}

	private function format_excluded_files($files){

		if (empty($files)) {
			return false;
		}

		$selected_files = array();

		foreach ($files as $file) {
				$selected_files[] = array(
							"id"    => NULL,
							"file"  => $file,
							"isdir" => wptc_is_dir($file) ? 1 : 0 ,
						);
		}
		return $selected_files;
	}

	public function update_default_excluded_files_list(){
		$upload_dir_path = wptc_get_upload_dir();

		$files_index = array(
			'1.5.3'  => 'wptc_1_5_3',
			'1.8.0'  => 'wptc_1_8_0',
			'1.8.2'  => 'wptc_1_8_2',
			'1.9.0'  => 'wptc_1_9_0',
			'1.9.4'  => 'wptc_1_9_4',
			'1.11.1' => 'wptc_1_11_1',
			'1.14.0' => 'wptc_1_14_0',
			'1.18.0' => 'wptc_1_18_0',
			);

		$wptc_1_5_3 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/nfwlog",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/debug.log",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wflogs",
			$upload_dir_path . "/siteorigin-widgets",
			$upload_dir_path . "/wp-hummingbird-cache",
			$upload_dir_path . "/wp-security-audit-log",
			$upload_dir_path . "/freshizer",
			$upload_dir_path . "/db-backup",
			$upload_dir_path . "/backupbuddy_backups",
			$upload_dir_path . "/vcf",
			$upload_dir_path . "/pb_backupbuddy",
			WPTC_RELATIVE_ABSPATH . "wp-admin/error_log",
			WPTC_RELATIVE_ABSPATH . "wp-admin/php_errorlog",
			);

		$wptc_1_8_0 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/DE_cl_dev_log_auto_update.txt",
			);

		$wptc_1_8_2 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/Dropbox_Backup",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/backup-db",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/updraft",
			$upload_dir_path . "/report-cache",
			);

		$wptc_1_9_0 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/w3tc-config",
			$upload_dir_path . "/ithemes-security",
			$upload_dir_path . "/cache",
			$upload_dir_path . "/et_temp",
			);

		$wptc_1_9_4 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/aiowps_backups",
			);

		$wptc_1_11_1 = array(
			$upload_dir_path."/wptc_restore_logs",
			);

		$wptc_1_14_0 = array(
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-server-request-logs.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-logs.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-memory-peak.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-memory-usage.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-time-taken.txt",
			WPTC_RELATIVE_WP_CONTENT_DIR . "/wptc-cpu-usage.txt",
			);

		$wptc_1_18_0 = array(
			$upload_dir_path . "/bb-plugin",
		);

		$prev_wptc_version =  $this->config->get_option('prev_installed_wptc_version');

		if (empty($prev_wptc_version)) {
			return false;
		}

		$required_files = array();
		foreach ($files_index as $key => $value) {
			if (version_compare($prev_wptc_version, $key, '<') && version_compare(WPTC_VERSION, $key, '>=')) {
				$required_files = array_merge($required_files, ${$files_index[$key]});
			}
		}
		return $required_files;
	}

	public function update_default_excluded_files(){
		$status = $this->config->get_option('update_default_excluded_files');

		if ($status) {
			return false;
		}

		$new_default_exclude_files = $this->update_default_excluded_files_list();

		if (empty($new_default_exclude_files)) {
			$this->config->set_option('update_default_excluded_files', true);
			return false;
		}

		$files = $this->format_excluded_files($new_default_exclude_files);

		foreach ($files as $file) {
			$file['category'] = 'backup';
			$this->exclude_file_list($file, true);

			$file['category'] = 'staging';
			$this->exclude_file_list($file, true);
		}

		$this->config->set_option('update_default_excluded_files', true);
	}

	public function get_tables($exc_wp_tables = false) {
		$this->load_saved_keys_manually();

		$tables = $this->processed_files->get_all_tables();

		if ($exc_wp_tables && !$this->config->get_option('non_wp_tables_excluded')) {
			$this->exclude_non_wp_tabes($tables);
			$this->exclude_content_for_default_log_tables($tables);
			$this->config->set_option('non_wp_tables_excluded', true);
			$this->load_exc_inc_tables('backup');
		}

		$tables_arr = array();

		foreach ($tables as $table) {

			if (!$this->show_this_tables_in_staging_site($table)) {
				continue;
			}

			$table_status = $this->is_excluded_table($table);

			if ($table_status === 'table_included') {
				$temp = array(
					'title'            => $table,
					'key'              => $table,
					'content_excluded' => 0,
					'size'             => $this->processed_files->get_table_size($table),
					'preselected'      => true,
				);
			} else if ($table_status === 'content_excluded') {
				$temp = array(
					'title'            => $table,
					'key'              => $table,
					'content_excluded' => 1,
					'size'             => $this->processed_files->get_table_size($table),
					'preselected'      => true,
				);
			} else  {
				$temp = array(
					'title'       => $table,
					'key'         => $table,
					'size'        => $this->processed_files->get_table_size($table),
					'preselected' => false,
				);
			}
			$temp['size_in_bytes'] = $this->processed_files->get_table_size($table, 0);
			$tables_arr[] = $temp;
		}
		die(json_encode($tables_arr));
	}

	private function show_this_tables_in_staging_site($table){
		if (!defined('WPTC_IS_STAGING_SITE') || !WPTC_IS_STAGING_SITE) {
			return true;
		}

		return stripos($table, $this->db->base_prefix) === 0 ? true : false;
	}

	public function get_root_files($exc_wp_files = false) {

		$this->load_saved_keys_manually();


		$root_files    = $this->get_wp_content_files();
		$root_files    = $this->get_abspath_files($exc_wp_files, array($root_files));

		die(json_encode($root_files));
	}

	private function get_abspath_files($exc_wp_files, $root_files){
		$files_object = WPTC_Factory::get('File_Iterator')->get_files_obj_by_path(WPTC_ABSPATH);

		if ($exc_wp_files && !$this->config->get_option('non_wp_files_excluded')) {
			$this->exclude_non_wp_files($files_object);
			$this->config->set_option('non_wp_files_excluded', true);
		}

		return $this->format_result_data($files_object, $root_files, $skip_wp_content = true);
	}

	private function get_wp_content_files(){

		$is_excluded = $this->is_excluded_file(WPTC_WP_CONTENT_DIR, true);

		return array(
			'folder'        => true,
			'lazy'          => true,
			'size'          => '',
			'title'         => basename(WPTC_WP_CONTENT_DIR),
			'key'           => WPTC_WP_CONTENT_DIR,
			'size_in_bytes' => '0',
			'partial'       => $is_excluded ? false : true,
			'preselected'   => $is_excluded ? false : true,
		);
	}

	public function update_default_files_n_tables(){
		$this->config->set_option('insert_default_excluded_files', false);

		$this->insert_default_excluded_files();
	}

	public function exclude_non_wp_files($file_obj){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$selected_files = array();
		foreach ($file_obj as $Ofiles) {
			$file_path = $Ofiles->getPathname();
			$file_path = wp_normalize_path($file_path);
			$file_name = basename($file_path);
			if ($file_name == '.' || $file_name == '..') {
				continue;
			}
			wptc_log($file_path,'-----------$file_path----------------');
			if(!$this->is_wp_file($file_path)){
				$isdir = wptc_is_dir($file_path);
				$this->exclude_file_list(array('file'=> $file_path, 'isdir' => $isdir, 'category' => 'backup' ), true);
				$this->exclude_file_list(array('file'=> $file_path, 'isdir' => $isdir, 'category' => 'staging' ), true);
			}
		}
	}

	private function exclude_non_wp_tabes($tables){
		foreach ($tables as $table) {
			if (!$this->is_wp_table($table)) {
				$this->exclude_table_list(array('file' => $table, 'category' => 'backup'), true);
				$this->exclude_table_list(array('file' => $table, 'category' => 'staging'), true);
			}
		}
	}

	public function get_files_by_key($path) {
		$this->load_saved_keys_manually();
		$result_obj = WPTC_Factory::get('File_Iterator')->get_files_obj_by_path($path);
		$result = $this->format_result_data($result_obj);
		die(json_encode($result));
	}

	private function format_result_data($file_obj, $files_arr = array(), $skip_wp_content = false){

		if (empty($file_obj)) {
			return false;
		}

		foreach ($file_obj as $Ofiles) {

			$file_path = $Ofiles->getPathname();

			$file_path = wp_normalize_path($file_path);

			$file_name = basename($file_path);

			if ($file_name == '.' || $file_name == '..') {
				continue;
			}

			if (!$Ofiles->isReadable()) {
				continue;
			}

			$file_size = $Ofiles->getSize();

			$temp = array(
					'title' => basename($file_name),
					'key'   => $file_path,
					'size'  => $this->processed_files->convert_bytes_to_hr_format($file_size),
				);

			$is_dir = wptc_is_dir($file_path);


			if ($is_dir) {
				if ($skip_wp_content) {
					if ($file_path === WPTC_WP_CONTENT_DIR) {
						continue;
					}
				}
				$is_excluded    = $this->is_excluded_file($file_path, true);
				$temp['folder'] = true;
				$temp['lazy']   = true;
				$temp['size']   = '';
			} else {
				$is_excluded = $this->is_excluded_file($file_path, false);

				if (!$is_excluded) {
					$is_excluded = ( $this->file_list->in_ignore_list($file_path) && !$this->is_included_file($file_path) ) ? true : false;
				}

				if (!$is_excluded) {
					$is_excluded = $this->app_functions->is_bigger_than_allowed_file_size($file_path) ? true : false;
				}

				$temp['folder']        = false;
				$temp['size_in_bytes'] = $Ofiles->getSize();
			}

			if($is_excluded){
				$temp['partial']     = false;
				$temp['preselected'] = false;
			} else {
				$temp['preselected'] = true;
			}

			$files_arr[] = $temp;
		}

		$this->sort_by_folders($files_arr);

		return $files_arr;
	}

	private function sort_by_folders(&$files_arr) {
		if (empty($files_arr) || !is_array($files_arr)) {
			return false;
		}
		foreach ($files_arr as $key => $row) {
			$volume[$key]  = $row['folder'];
		}
		array_multisort($volume, SORT_DESC, $files_arr);
	}

	public function exclude_file_list($data, $do_not_die = false){

		$data = stripslashes_deep($data);

		if (empty($data['file']) || WPTC_ABSPATH ===  wptc_add_trailing_slash($data['file'])) {
			wptc_log(array(), '--------Matches abspath--------');
			return false;
		}

		$data['file'] = wp_normalize_path($data['file']);

		if ($data['isdir']) {
			$this->delete($data['file'], $data['category'], $force = true);
		} else {
			$this->delete($data['file'], $data['category'], $force = false );
		}

		$data['file'] = wptc_remove_fullpath($data['file']);

		$result = $this->insert( array(
					'key'      => $data['file'],
					'type'     => 'file',
					'category' => $data['category'],
					'action'   => 'exclude',
					'is_dir'   => $data['isdir'],
				));

		if($do_not_die){
			return true;
		}

		if ($result) {
			wptc_die_with_json_encode( array('status' => 'success') );
		}
		wptc_die_with_json_encode( array('status' => 'error') );
	}

	public function include_file_list($data, $force_insert = false){

		$data = stripslashes_deep($data);

		if (empty($data['file'])) {
			return false;
		}

		$data['file'] = wp_normalize_path($data['file']);

		if ($data['isdir']) {
			$this->delete($data['file'], $data['category'], $force = true );
		} else {
			$this->delete($data['file'], $data['category'], $force = false );
		}

		if ( $this->is_wp_file($data['file'] ) && !$this->file_list->in_ignore_list( $data['file'] ) && !$this->app_functions->is_bigger_than_allowed_file_size( $data['file'] ) ) {
			wptc_log(array(), '---------------wordpress folder so no need to inserted ----------------');
			wptc_die_with_json_encode( array('status' => 'success') );
			return false;
		}

		$data['file'] = wptc_remove_fullpath($data['file']);

		$result = $this->insert( array(
					'key'      => $data['file'],
					'type'     => 'file',
					'category' => $data['category'],
					'action'   => 'include',
					'is_dir'   => $data['isdir'],
				));

		if ($result) {
			wptc_die_with_json_encode( array('status' => 'success') );
		}
		wptc_die_with_json_encode( array('status' => 'error') );
	}

	private function is_wp_file($file){
		if (empty($file)) {
			return false;
		}
		$file = wp_normalize_path($file);
		foreach ($this->default_wp_files_n_folders as $path) {

			$path = wptc_add_fullpath($path);
			$path = wptc_remove_trailing_slash($path);
			if(stripos($file, $path) !== false){
				return true;
			}
		}

		return false;
	}

	public function is_excluded_file($file, $is_dir = false){

		if (empty($file)) {
			return true;
		}

		if( !$is_dir 
			&& $this->file_list->in_ignore_list( $file, $this->category ) 
			&& !$this->is_included_file( $file ) ) {

			wptc_log($file, '---------------skip, file in ignore list-----------------');
			
			return true;
		}

		$file = wp_normalize_path($file);

		if ($this->froce_exclude_files($file)) {
			return true;
		}

		$found = false;
		if ($this->is_wp_file($file)) {
			return $this->exclude_file_check_deep($file);
		}
		if (!$this->is_included_file($file)) {
			return true;
		} else {
			return $this->exclude_file_check_deep($file);
		}
	}

	private function exclude_file_check_deep($file){

		if (empty($this->excluded_files)) {
			return false;
		}

		foreach ($this->excluded_files as $key_meta) {
			$value = str_replace('(', '-', $key_meta->key);
			$value = str_replace(')', '-', $value);
			$file = str_replace('(', '-', $file);
			$file = str_replace(')', '-', $file);
			if(stripos($file.'/', $value.'/') === 0){

				return true;
			}
		}
		return false;
	}

	private function get_keys($type = 'file' , $action, $category = 'backup'){

		$sql = "SELECT * FROM {$this->db->base_prefix}wptc_inc_exc_contents WHERE `type` = '$type' AND `action` = '$action' AND `category` = '$category'";
		$raw_data = $this->db->get_results($sql);

		if (empty($raw_data)) {
			return array();
		}

		$result = array();

		foreach ($raw_data as $value) {
			if ($type === 'file') {
				$value->key = wptc_add_fullpath($value->key);
			}

			$result[] = $value;
		}

		return empty($result) ? array() : $result;
	}

	public function is_included_file($file, $is_dir = false){
		$found = false;
		$file = wp_normalize_path($file);

		foreach ($this->included_files as $key_meta) {
			$value = str_replace('(', '-', $key_meta->key);
			$value = str_replace(')', '-', $value);
			$file = str_replace('(', '-', $file);
			$file = str_replace(')', '-', $file);
			if(stripos($file.'/', $value.'/') === 0){
				$found = true;
				break;
			}
		}
		return $found;
	}

	private function is_included_file_deep($file, $is_dir = false){
		$found = false;
		foreach ($this->included_files as $value->key) {
			if ($value->key === $file) {
				$found = true;
				break;
			}
		}
		return $found;
	}

	//table related functions
	public function exclude_table_list($data, $do_not_die = false){
		if (empty($data['file'])) {
			return false;
		}

		$this->delete($data['file'], $data['category'], $force = false, $is_table = true);

		do_action('remove_realtime_trigger_wptc', $data['file']);

		$result = $this->insert( array(
					'key'      => $data['file'],
					'type'     => 'table',
					'category' => $data['category'],
					'action'   => 'exclude',
				));

		if ($do_not_die) {
			return false;
		}
		if ($result) {
			wptc_die_with_json_encode( array('status' => 'success') );
		}
		wptc_die_with_json_encode( array('status' => 'error') );
	}

	private function insert($data){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$result = $this->db->insert("{$this->db->base_prefix}wptc_inc_exc_contents", $data);

		if ($result === false) {
			wptc_log($this->db->last_error,'-----------$this->db->last_error----------------');
		}

		return $result;
	}

	public function include_table_list($data){
		if (empty($data['file'])) {
			return false;
		}

		if(is_wptc_filter_registered('add_realtime_trigger_wptc')){
			do_action('add_realtime_trigger_wptc', $data['file']);
		}

		$this->delete($data['file'], $data['category'], $force = false, $is_table = true );

		if ($this->is_wp_table($data['file'])) {
			wptc_log($data['file'], '---------------Wordpress table so no need to insert-----------------');
			wptc_die_with_json_encode( array('status' => 'success') );
		}

		$result = $this->insert( array(
				'key'                  => $data['file'],
				'type'                 => 'table',
				'category'             => $data['category'],
				'action'               => 'include',
				'table_structure_only' => 0,
			));

		if ($result) {
			wptc_die_with_json_encode( array('status' => 'success') );
		}

		wptc_die_with_json_encode( array('status' => 'error') );
	}

	public function include_table_structure_only($data, $do_not_die = false){

		if (empty($data['file'])) {
			return false;
		}

		do_action('remove_realtime_trigger_wptc', $data['file']);

		$this->delete($data['file'], $data['category'], $force = false );

		$result = $this->insert( array(
				'key'                  => $data['file'],
				'type'                 => 'table',
				'category'             => $data['category'],
				'action'               => 'include',
				'table_structure_only' => 1,
			));

		if ($do_not_die) {
			return ;
		}

		if ($result) {
			wptc_die_with_json_encode( array('status' => 'success') );
		}

		wptc_die_with_json_encode( array('status' => 'error') );
	}

	private function delete($key, $category = 'backup', $force = false, $is_table = false){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (empty($key)) {
			return false;
		}

		if(!$is_table){
			$key = wptc_remove_fullpath($key);
		}

		if ($force) {
			$re_sql = $this->db->prepare(" DELETE FROM {$this->db->base_prefix}wptc_inc_exc_contents WHERE `key` LIKE  '%%%s%%' AND `category` = '%s' ", $key, $category);
		} else {
			$re_sql = $this->db->prepare(" DELETE FROM {$this->db->base_prefix}wptc_inc_exc_contents WHERE `key` = '%s' AND `category` = '%s' ", $key, $category);
		}

		$result = $this->db->query($re_sql);

		if ($result === false) {
			wptc_log($this->db->last_error,'-----------$this->db->last_error----------------');
		}
	}

	private function is_wp_table($table){
		$case_i_prefix = strtolower($this->db->base_prefix);
		if (preg_match('#^' . $case_i_prefix . '#i', $table) === 1) {

			return true;
		}

		return false;
	}

	public function is_excluded_table($table){
		if (empty($table)) {
			return 'table_excluded';
		}

		if (wptc_is_meta_data_backup()) {
			return $this->app_functions->is_meta_table_excluded($table);
		}

		$is_wp_table = false;

		if($this->is_wp_table($table) ){
			if($this->exclude_table_check_deep($table)){
				return 'table_excluded';
			}

			$is_wp_table = true;
		}

		return $this->is_included_table($table, $is_wp_table);
	}

	private function exclude_table_check_deep($table){
		foreach ($this->excluded_tables as $key_meta) {
			if (preg_match('#^' . $key_meta->key . '#i', $table) === 1 ) {
				return true;
			}
		}

		return false;
	}

	private function is_included_table($table, $is_wp_table){
		if(is_array($this->included_tables)){
			foreach ($this->included_tables as $key_meta) {
				if (preg_match('#^' . $key_meta->key . '#i', $table) === 1) {
					return $key_meta->table_structure_only == 1 ? 'content_excluded' : 'table_included';
				}
			}
		}

		return $is_wp_table === true ? 'table_included' : 'table_excluded';
	}

	public function update_1_14_0(){
		$this->update_1_14_0_replace_path_to_relative('wptc_excluded_files');
		$this->update_1_14_0_replace_path_to_relative('wptc_included_files');
	}

	public function update_1_14_0_replace_path_to_relative($table){

		$result = $this->db->get_results("SELECT * FROM {$this->db->base_prefix}" . $table . "");

		$query = '';

		foreach ($result as $value) {
			$query .= empty($query) ? "(" : ",(" ;
			$file = wptc_remove_fullpath( $value->file);

			if ( empty($file) || $file == WPTC_RELATIVE_ABSPATH) {
				continue;
			}

			$query .= "NULL, '" . $file . "', " . $value->isdir . ")";
		}

		wptc_log($query, '--------$query--------');

		if (empty($query)) {
			return false;
		}

		$this->db->query("TRUNCATE TABLE `" . $this->db->base_prefix . $table . "`");

		$result = $this->db->query("INSERT INTO " . $this->db->base_prefix . $table . " (id, file, isdir) VALUES $query");
	}

	private function froce_exclude_files($file){
		if (empty($file)) {
			return false;
		}

		$file = wp_normalize_path($file);

		foreach ($this->force_exclude_folders as $path) {

			$path = wptc_add_fullpath($path);

			if(stripos($file, $path) !== false){
				return true;
			}
		}

		return false;
	}

	public function analyze_inc_exc(){
		$this->app_functions->set_start_time();

		$excluded_tables = $this->analyze_tables();

		wptc_die_with_json_encode( array('status' => 'completed', 'files' => $this->analyze_files_response, 'tables' => $excluded_tables));
	}

	public function analyze_tables(){
		$tables = $this->processed_files->get_all_tables();
		$exclude_tables = array();
		$counter = 0;
		foreach ($tables as $table) {
			$table_status = $this->is_excluded_table($table);
			if ($table_status !== 'table_included') {
				continue;
			}

			$size = $this->processed_files->get_table_size($table, false);

			if ($size < $this->max_table_size_allowed) {
				continue;
			}

			$exclude_tables[$counter]['title']         = $table;
			$exclude_tables[$counter]['key']           = $table;
			$exclude_tables[$counter]['size_in_bytes'] = $size;
			$exclude_tables[$counter]['size']          = $this->processed_files->convert_bytes_to_hr_format($size);
			$exclude_tables[$counter]['preselected']   = true;
			$counter++;
		}

		return $exclude_tables;
	}

	public function is_log_table($table){
		foreach ($this->skip_tables as $skip_table) {
			if (stripos($table, $skip_table) !== false) {
				return true;
			}
		}

		return false;
	}

	public function analyze_files(){
		$seekable_iterator = new WPTC_Seek_Iterator();
		$iterator = $seekable_iterator->get_seekable_files_obj(WPTC_RELATIVE_ABSPATH);

		$offset = $this->config->get_option('suggest_files_offset');
		$offset = empty($offset) ? false : $offset;

		$this->key_recursive_seek = empty($offset) ? array() : explode('-', $offset);

		$this->recursive_iterator($iterator, false);
	}

	public function recursive_iterator($iterator, $key_recursive) {

		$this->seek_offset($iterator);

		while ($iterator->valid()) {

			//Forming current path from iterator
			$recursive_path = $iterator->getPathname();

			$recursive_path = wp_normalize_path($recursive_path);

			//Mapping keys
			$key = ($key_recursive !== false ) ? $key_recursive . '-' . $iterator->key() : $iterator->key() ;

			//Do recursive iterator if its a dir
			if (!$iterator->isDot() && $iterator->isReadable() && $iterator->isDir() ) {

				//create new object for new dir
				$sub_iterator = new DirectoryIterator($recursive_path);

				$this->recursive_iterator($sub_iterator, $key);
			}

			//Ignore dots paths
			if(!$iterator->isDot()){
				$this->process_file( $iterator, $key );
			}

			//move to next file
			$iterator->next();
		}
	}

	private function seek_offset(&$iterator){

		if(!count($this->key_recursive_seek)){
			return false;
		}

		//Moving satelite into position.
		$iterator->seek($this->key_recursive_seek[0]);

		//remove positions from the array after moved satelite
		unset($this->key_recursive_seek[0]);

		//reset array index
		$this->key_recursive_seek = array_values($this->key_recursive_seek);

	}

	private function process_file($iterator, $key){

		if(is_wptc_timeout_cut()){
			$this->config->set_option('suggest_files_offset', $key);
			wptc_die_with_json_encode( array('status' => 'continue', 'files' => $this->analyze_files_response) );
		}

		if (!$iterator->isReadable()) {
			return ;
		}

		$file = $iterator->getPathname();

		$file = wp_normalize_path($file);

		if ($this->is_skip($file)) {
			return ;
		}


		$size = $iterator->getSize();
		// $extension = $iterator->getExtension();

		if ($size < $this->max_file_size_allowed) {
			return ;
		}

		$suggested_file['title']         = wptc_remove_fullpath($file);
		$suggested_file['key']           = $file;
		$suggested_file['size_in_bytes'] = $size;
		$suggested_file['size']          = $this->processed_files->convert_bytes_to_hr_format($size);
		$suggested_file['preselected']   = true;

		$this->analyze_files_response[]  = $suggested_file;
	}

	private function is_skip($file){

		$basename = basename($file);

		if ($basename == '.' || $basename == '..') {
			return true;
		}

		if (!is_readable($file)) {
			return true;
		}

		if(wptc_is_dir($file)){
			return true;
		}

		if (is_wptc_file($file)) {
			return true;
		}

		//always include backup and backup-meta files
		if ( stripos($file, WPTC_WP_CONTENT_DIR) !== false && ( stripos($file, 'backup.sql') !== false || stripos($file, 'meta-data') !== false ) ) {
			return true;
		}

		if ($this->is_excluded_file($file)) {
			return true;
		}

		if (stripos($file, 'wptc_saved_queries.sql') !== false) {
			return true;
		}

		if (stripos($file, WPTC_REALTIME_DIR_BASENAME) !== false) {
			return true;
		}

		return false;
	}

	public function exclude_all_suggested_items($request, $category = 'backup'){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (empty($request['data'])) {
			wptc_die_with_json_encode( array('status' => 'success' ) );
		}

		if (!empty($request['data']['tables']) || !is_array($request['data']['tables'])) {
			$query = '';
			foreach ($request['data']['tables'] as $table) {
				$query .= empty($query) ? "(" : ",(" ;
				$query .= $this->wpdb->prepare("NULL, %s, 'table', %s, 'include', '1')", $table, $category);
				$this->delete($data['file'], $category, $force = false );
			}
			if (!empty($query)) {
				$query = "insert into " . $this->db->base_prefix . "wptc_inc_exc_contents (`id`, `key`, `type`, `category`, `action` ,`table_structure_only`) values $query";
				$this->db->query($query);
			}
		}


		if (empty($request['data']['files']) || !is_array($request['data']['files'])) {
			wptc_die_with_json_encode( array('status' => 'success' ) );
		}

		$query = '';
		foreach ($request['data']['files'] as $file) {
			$query .= empty($query) ? "(" : ",(" ;
			$query .= $this->wpdb->prepare("NULL, %s, 'file', %s, 'exclude', '0')",  wptc_remove_fullpath($file), $category);
			$this->delete($file, $category, $force = false );
		}

		if (empty($query)) {
			wptc_die_with_json_encode( array('status' => 'success' ) );
		}

		$query = "insert into " . $this->db->base_prefix . "wptc_inc_exc_contents (`id`, `key`, `type`, `category`, `action` ,`is_dir`) values $query";
		$this->db->query($query);
		wptc_die_with_json_encode( array('status' => 'success' ) );
	}

	public function get_all_excluded_files($category = 'backup'){
		$files = $this->get_keys($type = 'file' , $action = 'exclude', $category);

		if (empty($files)) {
			wptc_die_with_json_encode( array('status' => 'success', 'files' => array() ) );
		}

		$analyze_files_response = array();

		foreach ($files as $file) {

			if (!file_exists($file)) {
				continue;
			}

			$size = is_readable($file) ? filesize($file) : '-' ;

			$suggested_file['title']         = wptc_remove_fullpath($file);
			$suggested_file['key'] 	         = wptc_add_fullpath($file);;
			$suggested_file['size_in_bytes'] = $size;
			$suggested_file['size']          = is_numeric($size) ? $this->processed_files->convert_bytes_to_hr_format($size) : $size;
			$suggested_file['preselected']   = false;
			$analyze_files_response[]        = $suggested_file;
		}

		wptc_die_with_json_encode( array('status' => 'success', 'files' => $analyze_files_response) );
	}

	public function exclude_content_for_default_log_tables($tables = false){

		if($this->config->get_option('exclude_content_for_default_log_tables')){
			return ;
		}

		if (empty($tables)) {
			$tables = $this->processed_files->get_all_tables();
		}

		if (empty($tables)) {
			return $this->config->set_option('exclude_content_for_default_log_tables', true);
		}

		foreach ($tables as $table) {
			if(!$this->is_log_table($table)){
				continue;
			}

			$this->include_table_structure_only(array('file' => $table, 'category' => 'backup'), $do_not_die = true);
			$this->include_table_structure_only(array('file' => $table, 'category' => 'staging'), $do_not_die = true);
		}

		$this->config->set_option('exclude_content_for_default_log_tables', true);
	}

	public function get_user_excluded_files_more_than_size(){
		$raw_settings = $this->config->get_option('user_excluded_files_more_than_size_settings');

		if (empty($raw_settings)) {
			return array(
				'status' => 'no',
				'size' => 50 * 1024 * 1024,
				'hr' => 50,
			);
		}

		$settings       = unserialize($raw_settings);

		if($settings['size'] < (10 * 1024 * 1024)){
			$settings['size'] = 10 * 1024 * 1024;
		}
		
		$settings['hr'] = $this->app_functions->convert_bytes_to_mb($settings['size']);

		return $settings;
	}

	public function save_settings($data){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (empty($data)) {
			return ;
		}

		if (!empty($data['user_excluded_extenstions'])) {
			$this->config->set_option('user_excluded_extenstions', strtolower($data['user_excluded_extenstions']) );
		} else {
			$this->config->set_option('user_excluded_extenstions', false);
		}

		if (empty($data['user_excluded_files_more_than_size_settings'])) {
			return ;
		}

		$updateSettings = array(
			'status' => $data['user_excluded_files_more_than_size_settings']['status'],
			'size' => $this->app_functions->convert_mb_to_bytes($data['user_excluded_files_more_than_size_settings']['size']),
		);

		wptc_log($updateSettings, "--------updateSettings--------");

		if($updateSettings['size'] < (10 * 1024 * 1024)){
			$updateSettings['size'] = 10 * 1024 * 1024;
		}

		$this->config->set_option('user_excluded_files_more_than_size_settings', serialize($updateSettings));
	}
}
