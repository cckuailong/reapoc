<?php

class Wptc_Auto_Backup extends WPTC_Privileges {
	protected $config;
	protected $logger;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->logger = WPTC_Factory::get('logger');
	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			WPTC_Pro_Factory::get('WptcAutoBackupHooks')->register_hooks();
		}
	}

	private function is_switch_on()
	{
		return false;
	}
}