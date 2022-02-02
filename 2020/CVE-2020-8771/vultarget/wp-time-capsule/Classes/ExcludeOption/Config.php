<?php

class Wptc_Exclude_Config extends Wptc_Base_Config {
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
			'insert_exclude_file_list' => 'flushable',//remove
			'got_exclude_files' => 'flushable',//remove
			'insert_default_excluded_files' => 'flushable',//remove
			'got_exclude_tables' => 'flushable', //remove
			'included_db_size' => 'flushable', //remove
			'included_file_size' => 'flushable', //remove
			'exclude_already_excluded_folders' => 'flushable',//remove
			'done_all_exclude_files_tables' => 'flushable',//remove
			'recent_total_files_count' => 'flushable',//remove
			'recent_total_files_size' => 'flushable',//remove
			'user_excluded_files_and_folders' => 'flushable',//remove
			'user_include_files_and_folders' => 'flushable',//remove
			'user_included_tables' => 'flushable',//remove
			'user_excluded_tables' => 'flushable',//remove
			'inc_exc_multicall_insert_status' => 'flushable',//remove
			'this_backup_exclude_files_done' => 'flushable',//remove
			'get_size_of_folder_multicall_status' => 'flushable',//remove
			'update_default_excluded_files' => 'flushable',
			'non_wp_files_excluded' => '',
			'non_wp_tables_excluded' => '',
			'prev_installed_wptc_version' => '',
			'signed_in_repos' => '',
			'default_repo' => '',
			'cached_g_drive_this_site_main_folder_id' => '',
			'cached_wptc_g_drive_folder_id' => '',
			'suggest_files_offset' => 'flushable',
			'user_excluded_files_more_than_size_settings' => '',
			'user_excluded_extenstions' => '',
			'user_excluded_extenstions_staging' => '',
			'exclude_content_for_default_log_tables' => '',
		);
		$this->used_wp_options = array(
		);
	}
}
