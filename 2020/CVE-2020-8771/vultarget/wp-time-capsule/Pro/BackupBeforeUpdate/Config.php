<?php

class Wptc_Backup_Before_Update_Config extends Wptc_Base_Config {
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
			'single_upgrade_details'                => 'flushable',
			'backup_before_update_progress'         => 'flushable',
			'started_backup_before_auto_update'     => 'flushable',
			'bbu_note_view'                         => 'retainable',
			'backup_before_update_setting'          => 'retainable',
			'backup_action_id'                      => 'retainable',
			'privileges_wptc'                       => 'retainable',
			'wptc_auto_update_settings'             => 'retainable',
			'auto_update_queue'                     => 'flushable',
			'run_init_setup_bbu'                    => 'retainable',
			'update_response_details'               => 'flushable',
			'upgrade_process_running'               => 'flushable',
			'backup_and_update_request'             => 'flushable',
			'is_bulk_update_request'                => 'flushable',
			'bulk_update_request'                   => 'flushable',
			'is_vulns_updates'                      => 'flushable',
			'start_upgrade_process'                 => 'flushable',
			'site_url_wptc'                         => '',
			'wptc_timezone'                         => '',
			'retry-upgrade-list'                    => 'flushable',
			'temp_auto_excluded_auto_updates_lists' => '',
			'is_auto_update'                        => 'flushable',
		);
		$this->used_wp_options = array(
			'auto_updater.lock'       => 'flushable', //remove to trigger auto update instantly
			'core_updater.lock'       => 'flushable', //remove to trigger auto update even after failures
			'auto_core_update_failed' => 'flushable', //remove to trigger auto update update even after failures
		);
	}

	public function get_all_as_str() {
		$str = '' . PHP_EOL;
		foreach ($this->used_options as $option_name => $v) {
			$str .= $option_name . ' -> ' . $this->get_option($option_name) . PHP_EOL;
		}
		$str .= 'Lock -> ' . get_option('auto_updater.lock');
		return $str;
	}

}