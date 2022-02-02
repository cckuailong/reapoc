<?php

class Wptc_Backup_Analytics extends Wptc_Analytics {
	protected $config;
	protected $logger;
	private $cron_server_curl;
	private $app_functions;
	private $big_table_size_in_bytes = 104857600; //100MB
	private $wpdb;

	public function __construct() {
		$this->config           = WPTC_Factory::get('config');
		$this->cron_server_curl = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
		$this->backup_obj       = WPTC_Base_Factory::get('Wptc_Backup');
		$this->app_functions    = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->init_wpdb();
	}

	private function init_wpdb(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function get_then_send_any_backup_completed_details() {
		$post_arr = array(
			'event'           => 'update_site_meta',
			'size'            => $this->get_total_size_of_processsed_files_during_current_backup(),
			'noOfFiles'       => $this->get_recent_backup_processsed_files(),
			'noOfTables'      => $this->get_recent_backup_processsed_tables(),
			'no_of_calls'     => $this->get_last_value_of_backup_calls_record(),
			'plugin_version'  => WPTC_VERSION,
			'backup_duration' => $this->get_backup_duration(),
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function get_then_send_first_backup_completed_details() {
		$post_arr = array(
			'event'           => 'update_complete_backup',
			'size'            => $this->get_total_size_of_processsed_files_during_current_backup(),
			'noOfFiles'       => $this->get_recent_backup_processsed_files(),
			'noOfTables'      => $this->get_recent_backup_processsed_tables(),
			'no_of_calls'     => $this->get_last_value_of_backup_calls_record(),
			'backup_duration' => $this->get_backup_duration(),
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_backups_data_to_server(){
		wptc_set_time_limit(300);
		$backups_data = array();
		$backups_data['event']          = 'update_full_backup_data';
		$backups_data['app_id']         = $this->config->get_option('appID');
		$backups_data['timeZone']       = $this->config->get_option('wptc_timezone');
		$backups_data['cloud_account']  = $this->config->get_option('default_repo');
		$backups_data['revision_limit'] =  apply_filters('get_current_revision_limit_wptc', '');
		$backups_data['plugin_version'] = WPTC_VERSION;

		$processed_files = WPTC_Factory::get('processed-files', true);
		$backups_data['no_of_backups'] = $processed_files->get_no_of_backups();

		$backups_data['backups'] = $processed_files->get_backups_meta();

		$this->cron_server_curl->do_call('users/stats', $backups_data);
	}

	public function send_basic_analytics(){
		$post_arr = array(
			'event'          => 'update_site_meta',
			'size'           => '0',
			'noOfFiles'      => '0',
			'no_of_calls'    => '0',
			'plugin_version' => WPTC_VERSION,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);

		$this->send_ip_address_to_server();
	}

	public function send_database_size(){

		if (apply_filters('is_realtime_partial_db_backup_wptc', '')) {
			return ;
		}

		$post_arr = array(
			'event'   => 'update_database_info',
			'db_size' => $this->get_recent_database_size(),
		);
		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_report_data($action_id, $type, $status, $additional = array()){
		$post_arr = array(
			'action_id' => $action_id,
			'type'      => $type,
			'status'    => $status,
		);

		$post_arr = array_merge($post_arr, $additional);

		$this->cron_server_curl->do_call('status-meta', $post_arr);
	}

	private function get_last_value_of_backup_calls_record() {
		$backup_calls_record = $this->get_backup_calls_record_arr();

		if (!$backup_calls_record || !is_array($backup_calls_record)) {
			return 0;
		}
		$last_record_val = $this->get_last_value_of_array($backup_calls_record);

		return $last_record_val;
	}

	private function get_last_value_of_array($array = array()) {
		if (!is_array($array)) {
			return 0;
		}

		$last_record_val = '';
		foreach ($array as $v) {
			$last_record_val = $v;
		}

		return $last_record_val;
	}

	public function get_total_size_of_processsed_files_during_current_backup() {
		$current_backup_id = wptc_get_cookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}
		// $current_backup_id = '1467191833.5798';
		// $prepared_query = $this->wpdb->prepare('SELECT SUM(`uploaded_file_size`) FROM ' . $this->wpdb->base_prefix . 'wptc_processed_files WHERE backupID = %0.4f', $current_backup_id);
		$get_size = "SELECT SUM(uploaded_file_size) FROM " . $this->wpdb->base_prefix . "wptc_processed_files WHERE backupID = $current_backup_id";
		$total_size = $this->wpdb->get_var($get_size);
		return $total_size;
	}

	public function get_total_size_of_processsed_files_during_all_backup() {
		$get_size = "SELECT SUM(uploaded_file_size) FROM " . $this->wpdb->base_prefix . "wptc_processed_files";
		$total_size = $this->wpdb->get_var($get_size);
		return $total_size;
	}

	public function get_total_backup_processsed_files() {
		$total_files = "SELECT COUNT(*) FROM " . $this->wpdb->base_prefix . "wptc_processed_files WHERE is_dir != 1";
		return $this->wpdb->get_var($total_files);
	}
	public function get_recent_database_size(){
		$sql = "SELECT uploaded_file_size FROM {$this->wpdb->base_prefix}wptc_processed_files WHERE file LIKE '%-backup.sql%' ORDER BY  file_id DESC LIMIT 1";
		$size = $this->wpdb->get_var($sql);
		if (empty($size)) {
			return 7;
		}
		return $size;
	}

	public function get_recent_backup_processsed_files() {
		$current_backup_id = wptc_get_cookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}
		$prepared_query = $this->wpdb->prepare('SELECT COUNT(*) FROM ' . $this->wpdb->base_prefix . 'wptc_processed_files WHERE is_dir != %d AND backupID = %s', 1, $current_backup_id);
		return $this->wpdb->get_var($prepared_query);
	}

	public function get_recent_backup_processsed_tables() {
		$tables  = WPTC_Factory::get('processed-files')->get_all_tables();
		$counter = 0;

		$exclude_class_obj = WPTC_Base_Factory::get('Wptc_ExcludeOption');

		foreach ($tables as $table) {
			$table_status  =  $exclude_class_obj->is_excluded_table($table);
			if ($table_status !== 'table_included') {
				continue;
			}

			$counter++;
		}

		return $counter;
	}

	public function get_backup_calls_record_arr() {
		$call_records = $this->config->get_option('backup_calls_record');
		if ($call_records) {
			return json_decode($call_records, true);
		}
		return array();
	}

	public function update_backup_calls_record() {
		$current_backup_id = wptc_get_cookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}

		$call_records = $this->get_backup_calls_record_arr();

		if (empty($call_records[$current_backup_id])) {
			$call_records[$current_backup_id] = 0;
		}

		$call_records[$current_backup_id] += 1;

		$this->config->set_option('backup_calls_record', json_encode($call_records, JSON_UNESCAPED_SLASHES));
	}

	public function flush_backup_calls_record() {
		$this->config->set_option('backup_calls_record', false);
	}

	public function send_cloud_account_used() {
		$post_arr = array(
			'event' => 'update_cloud_account',

			'cloud_account' => $this->config->get_option('default_repo'),
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_server_info() {
		$post_arr = $this->app_functions->get_server_info();
		$post_arr['big_tables'] = $this->get_big_tables();
		$post_arr['cloud_account'] = $this->config->get_option('default_repo');
		$post_arr['event'] ='update_server_info';

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function reset_stats(){
		$post_arr = array(
			'event' => 'update_complete_backup',
			'size' => 0,
			'noOfFiles' => 0,
			'no_of_calls' => 0,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);

		sleep(2);

		$post_arr = array(
			'event' => 'update_complete_backup',
			'size' =>  $this->get_total_size_of_processsed_files_during_all_backup(),
			'noOfFiles' =>  $this->get_total_backup_processsed_files(),
			'no_of_calls' => $this->get_last_value_of_backup_calls_record(),
		);
		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_ptc_list_to_server() {
		$post_arr =  $this->get_ptc_meta();
		$this->cron_server_curl->do_call('update-ptc-meta', $post_arr);
	}

	public function send_ip_address_to_server(){
		$post_arr = array(
			'event' => 'update_ip_address',
			'ip_address' => gethostbyname($_SERVER['HTTP_HOST'])
		);
		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_ptc_list_to_server_after_theme_delete() {
		if ((!isset($_POST['slug']) || !isset($_POST['action']) || $_POST['action'] != 'delete-theme')){
			return false;
		}

		$this->send_ptc_list_to_server();
	}

	public function get_ptc_meta(){
		$ptc_meta['ptc_meta']['plugins_meta'] = $this->get_plugins_meta();
		$ptc_meta['ptc_meta']['themes_meta'] = $this->get_themes_meta();
		$ptc_meta['ptc_meta']['core_meta'][0] = array( 'version' => $this->get_core_meta() );

		$vulns_setting = apply_filters('get_format_vulns_settings_to_send_server_wptc', '');

		if (empty($vulns_setting)) {
			$ptc_meta['ptc_meta']['plugins_to_exclude'] = array();
			$ptc_meta['ptc_meta']['themes_to_exclude'] = array();
			$ptc_meta['ptc_meta']['is_core_exclude'] = false;
			$ptc_meta['vulns_setting'] = false;
			return $ptc_meta;
		}

		$plugins_to_exclude_slugs = ( empty($vulns_setting['plugins_to_exclude']) || !is_array($vulns_setting['plugins_to_exclude']) ) ? array() : array_values($vulns_setting['plugins_to_exclude']) ;

		$plugins_to_exclude = array();

		foreach ($plugins_to_exclude_slugs as $plugin) {
			$plugins_to_exclude[] = $this->app_functions->shortern_plugin_slug($plugin);
		}

		$ptc_meta['ptc_meta']['plugins_to_exclude'] = $plugins_to_exclude;
		$ptc_meta['ptc_meta']['themes_to_exclude'] = ( empty($vulns_setting['themes_to_exclude']) || !is_array($vulns_setting['themes_to_exclude']) ) ? array() : array_values($vulns_setting['themes_to_exclude']);
		$ptc_meta['ptc_meta']['is_core_exclude'] = empty($vulns_setting['is_core_exclude']) ? false : true;
		$ptc_meta['vulns_setting'] = empty($vulns_setting['status']) ? false : true;
		return $ptc_meta;;
	}

	public function get_plugins_meta(){
		if (!function_exists('get_plugins')) {
			include_once ABSPATH.'/wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		$plugins_meta = array();
		foreach ($plugins as $key => $plugin) {
			$plugins_meta[] = array(
					'version' => $this->remove_unwanted_str_from_ver($plugin['Version']),
					'slug' => $this->app_functions->shortern_plugin_slug($key),
					);
		}
		return $plugins_meta;
	}

	private function remove_unwanted_str_from_ver($version){
		//remove v from the beginning
		return ltrim($version, 'v');
	}

	public function get_themes_meta(){
		if (!function_exists('wp_get_themes')) {
			include_once ABSPATH.'/wp-includes/theme.php';
		}
		$themes = wp_get_themes();
		$themes_meta = array();
		foreach ($themes as $key => $theme_info) {
			$themes_meta[] = array(
					'version' => $theme_info->get( 'Version' ),
					'slug' => $key,
					);
		}
		return $themes_meta;
	}

	public function get_core_meta(){
		return $this->app_functions->get_wp_core_version();
	}

	private function get_big_tables(){
		$processed_files = WPTC_Factory::get('processed-files', true);

		$tables = $processed_files->get_all_tables();

		$big_tables = array();


		if (empty($tables)) {
			return $big_tables;
		}

		foreach ($tables as $table) {
			$size = $processed_files->get_table_size($table, false);
			if ($size < $this->big_table_size_in_bytes ) {
				continue;
			}
			$table = str_replace($this->wpdb->base_prefix, '', $table);
			$big_tables[$table] = $size;
		}

		return $big_tables;
	}

	private function get_backup_duration(){
		return time() - $this->config->get_option('starting_backup_first_call_time');
	}

}
