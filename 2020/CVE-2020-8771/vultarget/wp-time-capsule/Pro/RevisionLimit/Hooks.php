<?php

class Wptc_Revision_Limit_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Hanlder';
		$this->hooks_handler_obj = WPTC_Pro_Factory::get($supposed_hooks_hanlder_class);
	}

	public function register_hooks() {
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	protected function register_wptc_actions() {
		add_action('update_eligible_revision_limit_wptc', array($this->hooks_handler_obj, 'update_eligible_revision_limit'), 10, 1);
		add_action('set_revision_limit_wptc', array($this->hooks_handler_obj, 'set_revision_limit'), 10, 2);
	}

	protected function register_wptc_filters() {
		add_filter('get_current_revision_limit_wptc', array($this->hooks_handler_obj, 'get_current_revision_limit'), 10);
		add_filter('get_eligible_revision_limit_wptc', array($this->hooks_handler_obj, 'get_eligible_revision_limit'), 10);
		add_filter('get_days_show_from_revision_limits_wptc', array($this->hooks_handler_obj, 'get_days_show_from_revision_limits'), 10);
	}

}