<?php

class Wptc_Base_Hooks {
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
		add_action('just_initialized_wptc_h', array($this->hooks_handler_obj, 'just_initialized_wptc_h'));
	}

	protected function register_wptc_filters() {
	}

}