<?php

class Wptc_Restore_To_Staging_Hooks extends Wptc_Base_Hooks {
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
		add_action('admin_enqueue_scripts', array($this->hooks_handler_obj, 'enque_js_files'));
		add_action('wp_ajax_init_restore_to_staging_wptc', array($this->hooks_handler_obj, 'init_restore'));
	}

	protected function register_filters() {

	}

	protected function register_wptc_actions() {

	}

	protected function register_wptc_filters() {
		add_filter('get_restore_to_staging_button_wptc', array($this->hooks_handler_obj, 'get_restore_to_staging_button'), 10);
		add_filter('is_restore_to_staging_wptc', array($this->hooks_handler_obj, 'is_restore_to_staging'), 10);
		add_action('get_restore_to_staging_request_wptc', array($this->hooks_handler_obj, 'get_request'));
	}

}