<?php

abstract class Wptc_Base_Config {
	protected $config;
	protected $used_options;
	protected $used_wp_options;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->used_options = array();
		$this->used_wp_options = array();
	}

	protected abstract function set_used_options();

	public function get_option($option_name = null) {
		if (array_key_exists($option_name, $this->used_options)) {
			return WPTC_Factory::get('config')->get_option($option_name);
		}
		wptc_log($option_name, "--------not registered Wptc_Base_Config--------");
	}

	public function set_option($option_name, $val = null) {
		if (array_key_exists($option_name, $this->used_options)) {
			return WPTC_Factory::get('config')->set_option($option_name, $val);
		}
		wptc_log($option_name, "--------not registered Wptc_Base_Config--------");
	}

	public function flush() {
		foreach ($this->used_options as $option_name => $v) {
			if ($v == 'flushable') {
				WPTC_Factory::get('config')->set_option($option_name, false);
			}
		}

		$this->flush_wp_options();
	}

	public function flush_wp_options() {
		if (empty($this->used_wp_options)) {
			return true;
		}

		foreach ($this->used_wp_options as $option_name => $v) {
			if ($v == 'flushable') {
				delete_option($option_name, false);
			}
		}
	}
}