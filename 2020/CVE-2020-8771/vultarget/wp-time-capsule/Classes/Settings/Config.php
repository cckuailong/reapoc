<?php

class Wptc_Settings_Config extends Wptc_Base_Config {
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
			'signed_in_repos' => 'retainable',
			'anonymous_datasent' => 'retainable',
			'wptc_timezone' => 'retainable',
			'schedule_time_str' => 'retainable',
			'revision_limit' => 'retainable',
			'user_excluded_extenstions' => 'retainable',
			'user_excluded_extenstions_staging' => 'retainable',
			'gdrive_old_token' => 'retainable',
			'backup_slot' => 'retainable',
			'eligible_revision_limit' => 'retainable',
			'default_repo' => 'retainable',
			'default_repo_history' => 'retainable',
			'database_encryption_key' => 'retainable',
			'dropbox_oauth_state' => 'retainable',
			'dropbox_access_token' => 'retainable',
			'oauth_state_g_drive' => 'retainable',
			'current_g_drive_email' => 'retainable',
			'as3_access_key' => 'retainable',
			'as3_secure_key' => 'retainable',
			'as3_bucket_name' => 'retainable',
			'as3_bucket_region' => 'retainable',
			'wasabi_access_key' => 'retainable',
			'wasabi_secure_key' => 'retainable',
			'wasabi_bucket_name' => 'retainable',
			'wasabi_bucket_region' => 'retainable',
		);
		$this->used_wp_options = array(
			//
		);
	}
}
