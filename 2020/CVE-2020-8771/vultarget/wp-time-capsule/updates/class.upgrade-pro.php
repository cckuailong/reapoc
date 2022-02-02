<?php

Class Wptc_Upgrade_Pro{
	private $app_functions;
	private $wpdb;
	private $config;

	public function __construct(&$app_functions, &$wpdb, &$config, $version){
		$this->app_functions = $app_functions;
		$this->wpdb = $wpdb;
		$this->config = $config;
		$this->init($version);
	}

	private function init($version){
		switch ($version) {
			case '1.21.0':
				$this->upgrade_1_21_0();
				break;
		}
	}

	private function upgrade_1_21_0()
	{
		wptc_log('', "--------trying update--upgrade_1_21_0--pro----");

		if(is_wptc_filter_registered('upgrade_our_staging_plugin_wptc')){
			do_action('upgrade_our_staging_plugin_wptc', '');
		}
	}
}