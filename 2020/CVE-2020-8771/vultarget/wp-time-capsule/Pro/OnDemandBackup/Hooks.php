<?php
class Wptc_On_Demand_Backup_Hooks extends Wptc_Base_Hooks{
	public $hooks_handler_obj,
			$autobackup;

	public function __construct() {
		$this->hooks_handler_obj = WPTC_Pro_Factory::get('Wptc_On_Demand_Backup_Hooks_Hanlder');
	}

	public function register_hooks() {
		$this->register_wptc_filters();
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_actions();
	}


	public function register_actions(){

	}

	public function register_filters() {

	}

	public function register_wptc_actions() {

	}

	public function register_wptc_filters() {
		add_filter('get_on_demand_backuo_option_wptc', array($this->hooks_handler_obj, 'get_html'), 10, 1);
	}
}