<?php

class Wptc_App_Functions_Config extends Wptc_Base_Config {
	protected $used_options;
	protected $used_wp_options;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->set_used_options();
	}

	protected function set_used_options() {
		$this->used_options = array(
			'privileges_wptc' => 'retainable'
		);

		$this->used_wp_options = array(
		);
	}
}