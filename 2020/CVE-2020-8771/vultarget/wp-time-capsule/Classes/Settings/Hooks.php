<?php

class Wptc_Settings_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Handler';
		$this->hooks_handler_obj = WPTC_Base_Factory::get($supposed_hooks_hanlder_class);
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

	}

	protected function register_filters() {
		add_filter( 'admin_footer_text', array( $this->hooks_handler_obj, 'admin_footer_text' ), 1000);
		add_filter( 'update_footer', array( $this->hooks_handler_obj, 'update_footer' ), 1000);
	}

	protected function register_wptc_actions() {
	}

	protected function register_wptc_filters() {
		add_filter( 'save_settings_revision_limit_wptc', array( $this->hooks_handler_obj, 'save_settings_revision_limit' ), 10, 1);
	}

}