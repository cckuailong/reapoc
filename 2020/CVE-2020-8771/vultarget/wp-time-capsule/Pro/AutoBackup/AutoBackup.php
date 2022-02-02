<?php

class Wptc_Auto_Backup extends WPTC_Privileges {
	private $config,
			$db,
			$extra_mins,
			$fs,
			$app_functions,
			$auto_backup_slots,
			$exclude_class_obj,
			$trigger,
			$default_dir;

	public function __construct(){
		$this->init_db_files();
		$this->extra_mins = 55;
		$this->config = WPTC_Pro_Factory::get('Wptc_Auto_Backup_Config');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->exclude_class_obj = WPTC_Base_Factory::get('Wptc_ExcludeOption');
		$this->trigger   = WPTC_Base_Factory::get('Trigger_Init');
		$this->set_slots();
		$this->init_realtime_dir();
		define('FALL_BACK_TO_6_HOUR_MSG_WPTC', 'And Backup Schedule set to 6 hours.');
	}

	private function init_realtime_dir(){
		$this->default_dir = $this->config->get_option('backup_db_path') . '/' . WPTC_REALTIME_DIR_BASENAME .'/';
	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on() {
		return true;
	}

	private function init_db_files() {
		global $wpdb;
		$this->db = $wpdb;

		global $wp_filesystem;

		if ($wp_filesystem) {
			$this->fs = $wp_filesystem;
		}
	}

	private function set_slots(){
		$this->auto_backup_slots = wptc_get_auto_backup_slots();
	}

	public function start_auto_backup() {
		$this->config->set_option('auto_backup_running', true);
		$this->config->set_option('wptc_current_backup_type', 'S');
		$this->config->set_option('last_auto_backup_started', time());
		start_fresh_backup_tc_callback_wptc('sub_cycle', null, true, false);
	}

	public function is_auto_backup_running(){
		$is_running = $this->config->get_option('auto_backup_running');
		return empty($is_running) ? false : true;
	}

	public function is_partial_db_backup(){
		if (!$this->is_auto_backup_enabled()) {
			return false;
		}

		if (!$this->is_auto_backup_running()) {
			return false;
		}

		if ( $this->config->get_option('take_full_backup_once') ) {
			return false;
		}

		if ( $this->config->get_option('is_not_eligible_for_partial_backup') ) {
			return false;
		}

		$backup_slot = $this->get_backup_slot();

		return $this->auto_backup_slots[$backup_slot]['partial_db_backup'];
	}

	public function is_partial_db_backup_enabled($recheck = false){
		if (!$this->is_auto_backup_enabled()) {
			return false;
		}

		if (!$recheck && $this->config->get_option('is_not_eligible_for_partial_backup') ) {
			return false;
		}

		$backup_slot = $this->get_backup_slot();

		return $this->auto_backup_slots[$backup_slot]['partial_db_backup'];
	}

	public function get_backup_slots($default_slots){

		$auto_backup_slots = array();

		foreach ($this->auto_backup_slots as $key => $slot) {
			$auto_backup_slots[$key] = $slot['name'];
		}

		$backup_slots = array_merge($default_slots, $auto_backup_slots);
		return $backup_slots;
	}

	public function add_trigger($table){
		if(!$this->is_auto_backup_enabled()){
			return false;
		}

		if ( !$this->is_partial_db_backup_enabled() ) {
			return false;
		}

		if ( !$this->trigger->enabled() ) {
			return false;
		}

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->trigger->create_trigger($table, $is_modified = true, $add_to_tmp_memory = false);
	}

	public function remove_trigger($table){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->trigger->drop_tiggers($table);
	}

	public function printTriggerTakenTime($trigger_start_time)
	{
		$trigger_creation_time = time() - $trigger_start_time;

		wptc_log($trigger_creation_time,'-----------trigger_creation_time----------------');
	}

	public function check_requirements(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if( !$this->is_auto_backup_enabled() || !$this->is_partial_db_backup_enabled(true) ){
			$this->delete_tmp_folder();
			$this->trigger->drop_trigger_for_all_tables();
			$this->trigger->drop_table();
			return false;
		}

		if ( $this->is_partial_db_backup_enabled($recheck = true) ) {

			$error = $this->can_create_query_file();

			if (!empty($error)) {
				$this->set_not_eligible_for_partial_backup();
				return $error;
			}

			$trigger_start_time = time();

			$error = $this->trigger->create_trigger_for_all_tables();

			wptc_log($error,'-----------$error----------------');

			$this->printTriggerTakenTime($trigger_start_time);

			if (!empty($error)) {
				$this->set_not_eligible_for_partial_backup();
				return $error;
			}

			$this->set_eligible_for_partial_backup();

			$msg = $this->start_manual_backup_if_come_from_non_partial_db_backup();
			if (!empty($msg)) {
				return $msg;
			}

			return false;
		}

		$response = $this->check_database_requirement();

		if (!empty($response)) {
			return $response;
		}

		return false;
	}

	private function start_manual_backup_if_come_from_non_partial_db_backup(){
		$old_backup_slot = $this->config->get_option('old_backup_slot');
		$current_backup_slot = $this->get_backup_slot();

		if (empty($old_backup_slot)) {
			return false;
		}

		wptc_log($old_backup_slot,'-----------$old_backup_slot----------------');
		wptc_log($current_backup_slot,'-----------$current_backup_slot----------------');

		if (!$this->is_partial_db_backup_enabled()) {
			return false;
		}

		if (!empty($this->auto_backup_slots[$old_backup_slot]) && $this->auto_backup_slots[$old_backup_slot]['partial_db_backup']) {
			return false;
		}

		if (!$this->config->get_option('first_backup_started_atleast_once')) {
			return false;
		}

		$this->app_functions->force_start_or_restart_backup();

		return array(
			'title' => 'Success',
			'message' => 'Realtime backup enabled and we have initiated the full database backup now. Thereafter site will be backed up as changes happen.' ,
			'type' => 'success'
			);
	}

	private function set_not_eligible_for_partial_backup(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->config->set_option('is_not_eligible_for_partial_backup', true);
		if ($this->get_backup_slot() == 'every_1_hour') {
			wptc_log(array(),'-----------1----------------');
			$this->config->set_option('backup_slot', 'every_6_hours');
			wptc_modify_schedule_backup();
		}

		$this->trigger->drop_trigger_for_all_tables();
		$this->trigger->drop_table();
		$this->delete_tmp_folder();
	}

	private function set_eligible_for_partial_backup(){
		$this->config->set_option('is_not_eligible_for_partial_backup', false);
	}

	private function check_database_requirement(){

		$database = new WPTC_DatabaseBackup();

		if ($database->is_shell_exec_available()) {
			return false;
		}

		$processed_files = WPTC_Factory::get('processed-files');

		$tables = $processed_files->get_all_tables();
		$total_size = 0;

		foreach ($tables as $table) {
			if($this->exclude_class_obj->is_excluded_table($table) != 'table_included'){
				continue;
			}

			$total_size += $processed_files->get_table_size($table, $convert_human_readable = false); //convert to human readable
		}

		if (WPTC_REAL_TIME_BACKUP_MAX_PHP_DUMP_DB_SIZE > $total_size) {
			return false;
		}

		return array(
			'title' => 'mysqldump not available',
			'message' => 'Real time backups may take more time to complete as your DB size (' . $processed_files->convert_bytes_to_hr_format($total_size) . ') is greater than (' . $processed_files->convert_bytes_to_hr_format(WPTC_REAL_TIME_BACKUP_MAX_PHP_DUMP_DB_SIZE) . ')  and also mysql dump is not available on your server.' ,
			'type' => 'warning'
			);
	}

	private function get_backup_slot(){
		return $this->config->get_option('backup_slot');
	}

	public function validate_auto_backup($die){

		if( !$this->is_auto_backup_enabled() ){
			if($die){

				wptc_log('', "---------missed_backup_2----------");

				send_response_wptc('Scheduled backup is completed ', WPTC_DEFAULT_CRON_TYPE);
			}

			return false;
		}

		$last_auto_backup_started = $this->config->get_option('last_auto_backup_started');

		//assume this is first auto backup
		if (empty($last_auto_backup_started)) {
			return true;
		}

		$current_backup_slot = $this->get_backup_slot();

		$interval_sec = $this->auto_backup_slots[$current_backup_slot]['interval_sec'];

		//Interval seconds not found so slot in the auto backup list
		if (empty($interval_sec)) {
			return ($die) ? send_response_wptc('Backup slot not in the list ', WPTC_DEFAULT_CRON_TYPE) : false ;
		}

		wptc_log(time(), '--------time() Current Time--------');
		wptc_log($last_auto_backup_started, '--------last_auto_backup_started--------');

		//last auto backup time exceeds next schedule so start now. (Time tolerence respects constant's value)
		if ( ( time() + WPTC_AUTO_BACKUP_CHECK_TIME_TOLERENCE ) > ( $last_auto_backup_started + $interval_sec ) || ( time() - WPTC_AUTO_BACKUP_CHECK_TIME_TOLERENCE ) > ( $last_auto_backup_started + $interval_sec )  ) {
			return true;
		}

		//last auto backup time not exceeds next schedule so do not start now.
		return  ($die) ? send_response_wptc('Auto backup is completed', WPTC_DEFAULT_CRON_TYPE) : false ;
	}

	public function is_auto_backup_enabled(){
		$current_backup_slot = $this->get_backup_slot();

		//Current slot not in the slot of auto backup
		if( !isset($this->auto_backup_slots[$current_backup_slot]) ){

			return false;
		}

		return true;
	}

	public function is_realtime_enabled($check_partial_db){
		if (!$this->is_auto_backup_enabled()) {
			return false;
		}

		if (!$check_partial_db) {
			return true;
		}

		if ( $this->is_partial_db_backup_enabled() ) {
			return true;
		}

		return false;
	}

	public function can_create_query_file(){
		$query_file = $this->get_query_file();

		if (file_exists($query_file)) {
			return false;
		}

		return array(
			'title' => 'Failed to create temp dir',
			'message' => 'WPTC cannot create the temp dir to backup the queries, Please create the this file <i><strong>' . $query_file. ' </strong></i> and make it writable by PHP. ' . FALL_BACK_TO_6_HOUR_MSG_WPTC ,
			'type' => 'error'
			);
	}

	public function get_query_file(){
		$this->app_functions->mkdir_by_path( $this->default_dir, true );

		$secret = $this->config->get_option('wptc_realtime_tmp_secret');

		if (empty($secret)) {
			$secret = WPTC_Factory::secret('wptc_saved_queries');
			$this->config->set_option('wptc_realtime_tmp_secret', $secret);
		}

		$query_file = $this->default_dir . 'wptc_saved_queries.sql.' . $secret;

		$query_file = wptc_add_fullpath($query_file);

		$query_file = $this->create_file($query_file);

		return $query_file;

	}

	public function get_partial_db_file(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if (!$this->is_partial_db_backup()) {
			wptc_log(array(),'-----------not partial_db_backup----------------');
			return false;
		}

		return $this->get_query_file();
	}

	private function get_backup_query_file(){
		$file_iterator = $this->get_realtime_dir_iterator();

		if (empty($file_iterator)) {
			return false;
		}

		$secret = $this->config->get_option('wptc_realtime_tmp_secret');

		wptc_log($secret,'-----------$current_secret----------------');
		wptc_log($file_iterator,'-----------$current_file_iterator----------------');

		foreach ($file_iterator as $file_meta) {
			$file = $file_meta->getPathname();

			$file = wp_normalize_path($file);

			$basename = basename($file);
			
			// wptc_log($file,'-----------$file----------------');

			if ($basename === 'index.php') {
				continue;
			}

			if (strstr($basename, 'wptc-secret.gz') !== false) {
				continue;
			}

			if (strstr($file, $secret) !== false) {
				continue;
			}

			wptc_log($file,'----------- backing up file----------------');

			return $file;
		}
	}

	private function create_file($query_file, $refresh_file = false){
		if (!$refresh_file && file_exists($query_file . '.gz') ) {
			return $query_file . '.gz';
		}

		if (!$refresh_file && file_exists($query_file)) {
			return $query_file;
		}

		file_put_contents($query_file, '', FILE_APPEND);

		return $query_file;
	}

	public function add_hide_dirs($dirs){
		array_push($dirs, $this->default_dir);
		return $dirs;
	}

	public function get_realtime_dir(){
		return $this->default_dir;
	}

	private function get_realtime_dir_iterator(){
		$file_iterator = new WPTC_File_Iterator();
		wptc_log($this->default_dir,'-----------$this->default_dir----------------');
		return $file_iterator->get_files_obj_by_path($this->default_dir, true);
	}

	public function refresh_realtime_tmp($refresh_secret = false){

		$file_iterator = $this->get_realtime_dir_iterator();

		if (empty($file_iterator)) {
			return ;
		}

		foreach ($file_iterator as $file_meta) {
			$file = $file_meta->getPathname();

			$file = wp_normalize_path($file);

			if (basename($file) === 'index.php') {
				continue;
			}

			@unlink($file);
		}

		if ($refresh_secret) {
			$this->config->set_option('wptc_realtime_tmp_secret', false);
		}
	}

	public function is_realtime_valid_query_file($file){

		//skip auto backup files if its not an auto backup
		if (!$this->is_partial_db_backup()) {
			return false;
		}

		if (strstr($file, '.gz') !== false) {
			return true;
		}

		$temp_file = $file . '.gz';

		//Assume if gz found then this new queries file and should not be added to backup
		if ( file_exists($temp_file) ) {
			return false;
		}

		return filesize( $file ) > 5 ? true : false;
	}

	public function full_backup_needed_tables(){
		return $this->trigger->full_backup_needed_tables();
	}

	public function delete_tmp_folder(){
		$file_iterator = $this->get_realtime_dir_iterator();

		if (empty($file_iterator)) {
			return ;
		}

		foreach ($file_iterator as $file_meta) {
			$file = $file_meta->getPathname();
			$file = wp_normalize_path($file);
			@unlink($file);
		}

		$path = wptc_add_fullpath($this->default_dir);

		@rmdir($path);
	}
}
