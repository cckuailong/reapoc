<?php

class Wptc_Rollback_Hooks extends Wptc_Base_Hooks {
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
		add_action('wp_ajax_get_previous_versions_wptc', array($this->hooks_handler_obj, 'get_previous_versions'));
	}

	protected function register_filters() {
		add_filter( 'plugin_action_links', array( $this->hooks_handler_obj, 'plugin_action_links' ), 20, 4 );
	}

	protected function register_wptc_actions() {

	}

	protected function register_wptc_filters() {
	}

}