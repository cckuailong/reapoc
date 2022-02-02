<?php

class Wptc_Analytics_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Handler';
		$this->hooks_handler_obj = WPTC_Base_Factory::get($supposed_hooks_hanlder_class);
	}

	public function register_hooks() {
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	protected function register_actions() {
	}

	protected function register_filters() {
	}

	protected function register_wptc_actions() {
		add_action('starting_fresh_new_backup_pre_wptc_h', array($this->hooks_handler_obj, 'starting_fresh_new_backup_pre_wptc_h'));
		add_action('just_starting_main_schedule_backup_wptc_h', array($this->hooks_handler_obj, 'just_starting_main_schedule_backup_wptc_h'));
		add_action('inside_monitor_backup_pre_wptc_h', array($this->hooks_handler_obj, 'inside_monitor_backup_pre_wptc_h'));
		add_action('just_completed_first_backup_wptc_h', array($this->hooks_handler_obj, 'just_completed_first_backup_wptc_h'));
		add_action('just_completed_not_first_backup_wptc_h', array($this->hooks_handler_obj, 'just_completed_not_first_backup_wptc_h'));
		add_action('send_basic_analytics', array($this->hooks_handler_obj, 'send_basic_analytics'));
		add_action('send_database_size', array($this->hooks_handler_obj, 'send_database_size'));
		add_action('reset_stats', array($this->hooks_handler_obj, 'reset_stats'));
		add_action('send_backups_data_to_server_wptc', array($this->hooks_handler_obj, 'send_backups_data_to_server'));
		add_action('send_ptc_list_to_server_wptc', array($this->hooks_handler_obj, 'send_ptc_list_to_server'));
		add_action('send_server_info_wptc', array($this->hooks_handler_obj, 'send_server_info'));

		//After every new plugin/theme installed or plugin/theme gets updated
		add_action('upgrader_process_complete', array($this->hooks_handler_obj, 'send_ptc_list_to_server'));
		//after deleted a plugin
		add_action('deleted_plugin', array($this->hooks_handler_obj, 'send_ptc_list_to_server'));
		//after delete theme
		add_action('delete_site_transient_update_themes', array($this->hooks_handler_obj, 'send_ptc_list_to_server_after_theme_delete'));
		add_action('send_ip_address_to_server_wptc', array($this->hooks_handler_obj, 'send_ip_address_to_server'));
		add_action('send_report_data_wptc', array($this->hooks_handler_obj, 'send_report_data'), 10, 4);
	}

	protected function register_wptc_filters() {
	}

}