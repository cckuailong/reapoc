<?php

class Wptc_Backup {
	protected $config;
	protected $logger;
	protected $cron_curl;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->logger = WPTC_Factory::get('logger');
		$this->backup_controller = WPTC_Factory::get('WPTC_BackupController');
	}

	public function init() {
		if ($this->is_privileged()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Base_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	public function is_privileged() {
		return true;
	}

}