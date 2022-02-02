<?php
class Wptc_Auto_Backup_Hooks extends Wptc_Base_Hooks{
	public $hooks_handler_obj,
			$config,
			$autobackup;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Auto_Backup_Config');
		$this->hooks_handler_obj = WPTC_Pro_Factory::get('Wptc_Auto_Backup_Hooks_Hanlder');
		$this->autobackup = WPTC_Pro_Factory::get('Wptc_Auto_Backup');
	}

	public function register_hooks() {
		if (!$this->autobackup->is_auto_backup_enabled()) {
			$this->register_wptc_filters();
			$this->register_wptc_actions();
			return false;
		}

		$this->register_wptc_filters();
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_actions();
	}


	public function register_actions() { }

	public function register_filters() { }

	public function add_query_filter_wptc(){
		//Do not enable this until we need to store all the executed queries this may slow down the site.
		if ( !$this->autobackup->is_partial_db_backup_enabled() ) {
			return ;
		}

		return false;
	}

	public function register_wptc_actions() {
		add_action('add_query_filter_wptc', array($this, 'add_query_filter_wptc'));
		add_action('start_auto_backup_wptc', array($this->hooks_handler_obj, 'start_auto_backup'));
		add_action('refresh_realtime_tmp_wptc', array($this->hooks_handler_obj, 'refresh_realtime_tmp'), 10, 1);
		add_action('add_realtime_trigger_wptc', array($this->hooks_handler_obj, 'add_trigger'), 10, 1);
		add_action('remove_realtime_trigger_wptc', array($this->hooks_handler_obj, 'remove_trigger'), 10, 1);
	}

	public function register_wptc_filters() {
		add_filter('is_auto_backup_running_wptc', array($this->hooks_handler_obj, 'is_auto_backup_running'), 10);
		add_filter('is_realtime_partial_db_backup_wptc', array($this->hooks_handler_obj, 'is_partial_db_backup'), 10);
		add_filter('get_realtime_partial_db_file_wptc', array($this->hooks_handler_obj, 'get_partial_db_file'), 10);
		add_filter('get_backup_slots_wptc', array($this->hooks_handler_obj, 'get_backup_slots'), 10, 1);
		add_filter('check_requirements_auto_backup_wptc', array($this->hooks_handler_obj, 'check_requirements'), 10);
		add_filter('validate_auto_backup_wptc', array($this->hooks_handler_obj, 'validate_auto_backup'), 10, 1);
		add_filter('add_hide_dirs_wptc', array($this->hooks_handler_obj, 'add_hide_dirs'), 10, 1);
		add_filter('get_realtime_dir_wptc', array($this->hooks_handler_obj, 'get_realtime_dir'), 10, 1);
		add_filter('get_realtime_restore_to_latest_button_wptc', array($this->hooks_handler_obj, 'get_realtime_restore_to_latest_button'), 10, 4);
		add_filter('is_realtime_valid_query_file_wptc', array($this->hooks_handler_obj, 'is_realtime_valid_query_file'), 10, 1);
		add_filter('is_realtime_enabled_wptc', array($this->hooks_handler_obj, 'is_realtime_enabled'), 10, 1);
		add_filter('get_realtime_full_backup_needed_tables_wptc', array($this->hooks_handler_obj, 'full_backup_needed_tables'), 10, 1);
	}
}