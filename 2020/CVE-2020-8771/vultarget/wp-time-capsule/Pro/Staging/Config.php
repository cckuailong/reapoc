<?php

Class Wptc_staging_Config extends Wptc_Base_Config {
	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->set_used_options();
	}

	protected function set_used_options() {
		$this->used_options = array(
			'is_staging_running' => 'flushable',
			'is_staging_completed' => 'flushable',
			'staging_id' => 'flushable',
			'staging_completed' => '',
			'same_server_staging_status' => 'retainable',
			'same_server_staging_path' => 'flushable',
			'same_server_staging_running' => 'flushable',
			'same_server_staging_db_prefix' => 'flushable',
			'same_server_staging_full_db_prefix' => 'flushable',
			'same_server_clone_db_total_tables' => 'flushable',
			'same_server_clone_db_completed_tables' => 'flushable',
			'same_server_copy_staging' => 'flushable',
			'same_server_replace_old_url' => 'flushable',
			'same_server_replace_old_url_data' => 'flushable',
			'same_server_staging_details' => 'retainable',
			'same_server_replace_url_multicall_status' => 'flushable',
			'staging_type' => 'retainable',
			'internal_staging_db_rows_copy_limit' => 'retainable',
			'internal_staging_file_copy_limit' => 'retainable',
			'internal_staging_deep_link_limit' => 'retainable',
			'run_staging_updates' => 'retainable',
			'internal_staging_enable_admin_login' => 'retainable',
			'same_server_get_folders' => 'flushable',
			'last_staging_ping' => 'flushable',
			'site_url_wptc' => 'retainable',
			'network_admin_url' => 'retainable',
			'staging_is_reset_permalink' => 'retainable',
			'staging_login_custom_link' => 'retainable',
			'user_excluded_extenstions_staging' => 'retainable',
		);
	}

}
