<?php

class Wptc_Pro_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$this->hooks_handler_obj = WPTC_Pro_Factory::get('Wptc_Pro_Hooks_Hanlder');
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