<?php

class Wptc_Vulns_Config extends Wptc_Base_Config {
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
			'single_upgrade_details' => 'retainable',
			'bulk_update_request' => 'retainable',
			'is_bulk_update_request' => 'retainable',
			'is_vulns_updates' => 'retainable',
			'vulns_settings' => 'retainable',
			'run_vulns_checker' => 'flushable',
		);
		$this->used_wp_options = array(
		);
	}
}