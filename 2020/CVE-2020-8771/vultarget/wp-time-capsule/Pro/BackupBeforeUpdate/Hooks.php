<?php

class Wptc_Backup_Before_Update_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;
	public $wp_filter_id;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Hanlder';
		$this->hooks_handler_obj = WPTC_Pro_Factory::get($supposed_hooks_hanlder_class);
	}

	public function register_hooks() {
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	protected function register_actions() {
		add_action('wp_ajax_clear_bbu_notes_wptc', array($this->hooks_handler_obj, 'clear_bbu_notes'));
		add_action('wp_ajax_get_installed_plugins_wptc', array($this->hooks_handler_obj, 'get_installed_plugins'));
		add_action('wp_ajax_get_installed_themes_wptc', array($this->hooks_handler_obj, 'get_installed_themes'));
		add_action('wp_ajax_save_bbu_settings_wptc', array($this->hooks_handler_obj, 'save_bbu_settings'));
	}

	protected function register_filters() {
		add_filter('send_core_update_notification_email', array($this->hooks_handler_obj, 'filter_hanlder'), 1, 2);

		$this->register_filters_may_be_prevent_auto_update();
	}

	protected function register_filters_may_be_prevent_auto_update() {
		//wptc_log(array(), "--------trying to register_filters_may_be_prevent_auto_update --------");

		add_filter('auto_update_core', array($this->hooks_handler_obj, 'may_be_prevent_auto_update'), 1, 3);
		add_filter('auto_update_theme', array($this->hooks_handler_obj, 'may_be_prevent_auto_update'), 1, 3);
		add_filter('auto_update_plugin', array($this->hooks_handler_obj, 'may_be_prevent_auto_update'), 1, 3);
		add_filter('auto_update_translation', array($this->hooks_handler_obj, 'may_be_prevent_auto_update'), 1, 3);
	}

	protected function register_wptc_actions() {
		add_action('just_initialized_fresh_backup_wptc_h', array($this->hooks_handler_obj, 'just_initialized_fresh_backup_wptc_h'));
		add_action('do_ptc_upgrades_wptc', array($this->hooks_handler_obj, 'do_ptc_upgrades'));
		add_action('admin_enqueue_scripts', array($this->hooks_handler_obj, 'enque_js_files'));
		add_action('install_actions_wptc', array($this->hooks_handler_obj, 'install_actions_wptc'));
		add_action('process_auto_updates_wptc', array($this->hooks_handler_obj, 'process_auto_updates'));
		add_action('turn_off_auto_update_wptc', array($this->hooks_handler_obj, 'turn_off_auto_update'));
		add_action('auto_update_failed_email_user_wptc', array($this->hooks_handler_obj, 'auto_update_failed_email_user'));
		add_action('turn_off_themes_auto_updates_wptc', array($this->hooks_handler_obj, 'turn_off_themes_auto_updates'));
		add_action('check_auto_updates_wptc', array($this->hooks_handler_obj, 'check_auto_updates'));
		add_action('check_any_upgrades_available_wptc', array($this->hooks_handler_obj, 'check_any_upgrades_available'));

		//After every new plugin/theme installed or plugin/theme gets updated
		add_action('upgrader_process_complete', array($this->hooks_handler_obj, 'included_new_items'), 10, 2);
		add_action('update_bulk_auto_update_settings_wptc', array($this->hooks_handler_obj, 'update_bulk_settings'), 10, 1);
	}

	protected function register_wptc_filters() {
		add_filter('page_settings_content_wptc', array($this->hooks_handler_obj, 'page_settings_content'), 1);
		add_filter('http_request_args', array($this->hooks_handler_obj, 'site_transient_update_plugins_h'), 10, 2);
		// add_filter('upgrader_pre_download', array($this->hooks_handler_obj, 'site_transient_update_plugins_h'), 10, 2);
		add_filter('upgrader_package_options', array($this->hooks_handler_obj, 'upgrader_pre_download'), 10, 1);
		add_filter('get_backup_before_update_setting_wptc', array($this->hooks_handler_obj, 'get_backup_before_update_setting_wptc'), 10, 2);
		add_filter('get_bbu_note_view', array($this->hooks_handler_obj, 'get_bbu_note_view'), 10, 2);
		add_filter('is_upgrade_in_progress_wptc', array($this->hooks_handler_obj, 'is_upgrade_in_progress'), 10, 2);
		add_filter('backup_and_update_wptc', array($this->hooks_handler_obj, 'backup_and_update'), 999, 2);
		add_filter('page_settings_tab_wptc', array($this->hooks_handler_obj, 'page_settings_tab'), 1);
		add_filter('modify_settings_to_server_wptc', array($this->hooks_handler_obj, 'modify_settings'), 1, 1);
		add_filter('get_auto_updates_wptc', array($this->hooks_handler_obj, 'get_auto_updates'), 1);
		add_filter('is_upgrade_request_wptc', array($this->hooks_handler_obj, 'is_upgrade_request'), 1);
	}
}