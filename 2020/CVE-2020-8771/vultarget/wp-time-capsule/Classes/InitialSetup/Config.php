<?php

class Wptc_InitialSetup_Config extends Wptc_Base_Config {
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
			'main_account_email' => 'retainable',
			'as3_access_key' => 'retainable',
			'as3_secure_key' => 'retainable',
			'as3_bucket_region' => 'retainable',
			'as3_bucket_name' => 'retainable',
			'wasabi_access_key' => 'retainable',
			'wasabi_secure_key' => 'retainable',
			'wasabi_bucket_region' => 'retainable',
			'wasabi_bucket_name' => 'retainable',
			'oauth_state_g_drive' => 'retainable',
			'gdrive_old_token' => 'retainable',
			'last_cloud_error' => 'retainable',
			'wptc_main_acc_email_temp' => 'retainable',
			'wptc_main_acc_pwd_temp' => 'retainable',
			'wptc_token' => 'retainable',
			'privileges_wptc' => 'retainable',
			'signed_in_repos' => 'retainable',
			'default_repo' => 'retainable',
			'default_repo_history' => 'retainable',
			'connected_sites_count' => 'retainable',
			'dropbox_access_token' => 'retainable',
			'dropbox_oauth_state' => 'retainable',
			'current_g_drive_email' => 'retainable',
			's3_NoncurrentVersionExpiration_days' => 'retainable',
		);
		$this->used_wp_options = array(
			//
		);
	}
}
