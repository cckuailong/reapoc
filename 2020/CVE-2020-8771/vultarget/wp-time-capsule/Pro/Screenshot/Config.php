<?php

class Wptc_Screenshot_Config extends Wptc_Base_Config {
	protected $config;
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
			'appID' => 'retainable',
			'single_upgrade_details' => 'retainable',
			'bulk_update_request' => 'retainable',
			'do_screenshot_compare' => 'retainable',
			'is_before_update_screenshot_taken' => 'flushable',
			'is_after_update_screenshot_taken' => 'flushable',
			'screenshot_group_updates_start' => 'flushable',
		);
		$this->used_wp_options = array(
		);
	}
}