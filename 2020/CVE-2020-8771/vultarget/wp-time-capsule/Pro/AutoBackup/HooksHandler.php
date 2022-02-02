<?php

class Wptc_Auto_Backup_Hooks_Hanlder extends Wptc_Base_Hooks_Handler{
	protected $autobackup;
	protected $config;

	public function __construct() {
		$this->autobackup = WPTC_Pro_Factory::get('Wptc_Auto_Backup');
		$this->config = WPTC_Pro_Factory::get('Wptc_Auto_Backup_Config');
	}

	public function start_auto_backup() {
		return $this->autobackup->start_auto_backup();
	}

	public function is_auto_backup_running() {
		return $this->autobackup->is_auto_backup_running();
	}

	public function is_partial_db_backup() {
		return $this->autobackup->is_partial_db_backup();
	}

	public function get_partial_db_file() {
		return $this->autobackup->get_partial_db_file();
	}

	public function add_hide_dirs($dirs) {
		return $this->autobackup->add_hide_dirs($dirs);
	}

	public function get_realtime_dir() {
		return $this->autobackup->get_realtime_dir();
	}

	public function get_backup_slots($backup_timing) {
		return $this->autobackup->get_backup_slots($backup_timing);
	}

	public function check_requirements() {
		return $this->autobackup->check_requirements();
	}

	public function validate_auto_backup($die = false) {
		return $this->autobackup->validate_auto_backup($die);
	}

	public function is_realtime_enabled($check_partial_db = false) {
		return $this->autobackup->is_realtime_enabled($check_partial_db);
	}

	public function is_realtime_valid_query_file($file) {
		return $this->autobackup->is_realtime_valid_query_file($file);
	}

	public function refresh_realtime_tmp($refresh_secret) {
		return $this->autobackup->refresh_realtime_tmp($refresh_secret);
	}

	public function add_trigger($table) {
		return $this->autobackup->add_trigger($table);
	}

	public function remove_trigger($table) {
		return $this->autobackup->remove_trigger($table);
	}

	public function full_backup_needed_tables($table) {
		return $this->autobackup->full_backup_needed_tables($table);
	}

	public function get_realtime_restore_to_latest_button($backup_html, $additional_html, $backup_id, $current_time) {

		return $backup_html;

		//Show latest backup only today's calender view and is_partial_db_backup enabled
		if(!$this->is_today($backup_id) || !$this->autobackup->is_partial_db_backup_enabled() ){
			return $backup_html;
		}

		return '<li class="single_group_backup_content bu_list" this_backup_id="' . $backup_id .'"><div class="single_backup_head bu_meta"><div class="time">' .  date('g:i a', $current_time) . '</div><div class="bu_name" title="latest" id="wptc_restore_latest_point">Latest Point</div><a class="this_restore disabled btn_wptc" style="display:none">Restore Selected</a><div class="changed_files_count" style="display:none"></div>' . $additional_html . '</div><div class="wptc-clear"></div><div class="bu_files_list_cont"><div class="item_label">Files</div><ul class="bu_files_list"></ul></div><div class="wptc-clear"></div></li>' . $backup_html;
	}

	private function is_today($timestamp){

		if (date("d-m-Y", $timestamp) ===  date("d-m-Y", time())) {
			return true;
		}

		wptc_log(date("d-m-Y", $timestamp),'-----------requested date----------------');
		wptc_log(date("d-m-Y", (time())),'-----------Current date----------------');
	}
}