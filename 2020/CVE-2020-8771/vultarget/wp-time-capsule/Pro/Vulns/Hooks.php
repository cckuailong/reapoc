<?php

class Wptc_Vulns_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Hanlder';
		$this->hooks_handler_obj = WPTC_Pro_Factory::get($supposed_hooks_hanlder_class);
	}

	public function register_hooks() {
		if (is_admin()) {
			$this->register_actions();
		}
		$this->register_filters();
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	protected function register_actions() {
		add_action('wp_ajax_save_vulns_settings_wptc', array($this->hooks_handler_obj, 'update_vulns_settings'));
		add_action('wp_ajax_get_installed_plugins_vulns_wptc', array($this->hooks_handler_obj, 'get_enabled_plugins'));
		add_action('wp_ajax_get_installed_themes_vulns_wptc', array($this->hooks_handler_obj, 'get_enabled_themes'));
		add_action('admin_enqueue_scripts', array($this->hooks_handler_obj, 'enque_js_files'));
		add_action('update_bulk_vulnerable_settings_wptc', array($this->hooks_handler_obj, 'update_bulk_settings'), 10, 1);
	}

	protected function register_filters() {
		add_filter('get_format_vulns_settings_to_send_server_wptc', array($this->hooks_handler_obj, 'get_format_vulns_settings_to_send_server'));
		add_filter('page_settings_tab_wptc', array($this->hooks_handler_obj, 'page_settings_tab'), 1);
		add_filter('page_settings_content_wptc', array($this->hooks_handler_obj, 'page_settings_content'), 1);
		add_filter('is_vulns_checker_request_wptc', array($this->hooks_handler_obj, 'is_vulns_checker_request'), 1);
	}

	protected function register_wptc_actions() {
		add_action('check_vulns_updates_wptc', array($this->hooks_handler_obj, 'check_vulns_updates'));
	}

	protected function register_wptc_filters() {
	}

}