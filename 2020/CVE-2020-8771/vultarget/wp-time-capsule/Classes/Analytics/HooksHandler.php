<?php

class Wptc_Analytics_Hooks_Handler extends Wptc_Base_Hooks_Handler {
	protected $config;

	public function __construct() {
		$this->backup_obj = WPTC_Base_Factory::get('Wptc_Backup');
		$this->backup_analytics = WPTC_Base_Factory::get('Wptc_Backup_Analytics');
	}

	public function just_starting_main_schedule_backup_wptc_h($args) {
		$this->backup_analytics->flush_backup_calls_record();
	}

	public function starting_fresh_new_backup_pre_wptc_h($data = null) {
		$this->backup_analytics->send_cloud_account_used();
	}

	public function just_completed_first_backup_wptc_h($data = null) {
		$this->backup_analytics->get_then_send_first_backup_completed_details();
	}

	public function send_basic_analytics($data = null) {
		$this->backup_analytics->send_basic_analytics();
	}

	public function send_database_size($data = null) {
		$this->backup_analytics->send_database_size();
	}

	public function reset_stats($data = null) {
		$this->backup_analytics->reset_stats();
	}

	public function send_backups_data_to_server($data = null) {
		$this->backup_analytics->send_backups_data_to_server();
	}

	public function inside_monitor_backup_pre_wptc_h($data = null) {
		$this->backup_analytics->update_backup_calls_record();
	}

	public function just_completed_not_first_backup_wptc_h($data = null) {
		$this->backup_analytics->get_then_send_any_backup_completed_details();
	}

	public function send_ptc_list_to_server() {
		$this->backup_analytics->send_ptc_list_to_server();
	}

	public function send_ptc_list_to_server_after_theme_delete() {
		$this->backup_analytics->send_ptc_list_to_server_after_theme_delete();
	}

	public function send_ip_address_to_server() {
		$this->backup_analytics->send_ip_address_to_server();
	}

	public function send_server_info() {
		$this->backup_analytics->send_server_info();
	}

	public function send_report_data($action_id, $type, $status, $additional = array()) {
		$this->backup_analytics->send_report_data($action_id, $type, $status, $additional);
	}

}