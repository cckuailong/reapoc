<?php

class Wptc_Screenshot_Hooks extends Wptc_Base_Hooks {
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
	}

	protected function register_filters() {
	}

	protected function register_wptc_actions() {
		add_action('take_screenshot_wptc', array($this->hooks_handler_obj, 'take_screenshot'), 10, 1);
		add_action('flush_screenshot_flags_wptc', array($this->hooks_handler_obj, 'flush'), 10, 1);
	}

	protected function register_wptc_filters() {
	}

}