<?php
class Wptc_White_Label_Config extends Wptc_Base_Config {
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
			'white_lable_details' => 'retainable',
			'uuid' => 'retainable',
			'wl_allowed_user_id' => 'retainable',
			'wl_installed_admin_username' => 'retainable'
		);
		$this->used_wp_options = array(
		);
	}
}