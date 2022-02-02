<?php

class Wptc_Common {
	protected $config;
	protected $logger;
	protected $cron_curl;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->logger = WPTC_Factory::get('logger');

		$this->cron_curl = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
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