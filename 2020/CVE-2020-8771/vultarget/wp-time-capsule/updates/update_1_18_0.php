<?php

Class Wptc_Update_1_18_0{
	const DELETE_ROWS_LIMIT = 5000;
	private $wpdb;
	private $config;
	private $app_functions;

	public function __construct(&$app_functions, &$wpdb, &$config){
		$this->wpdb          = $wpdb;
		$this->config        = $config;
		$this->app_functions = $app_functions;
		$this->init();
	}

	public function init(){

		set_time_limit(0);

		$this->delete_tables('wptc_debug_log');
		$this->delete_tables('wptc_auto_backup_record');
		$this->merge_backups_meta_tables();
		$this->create_inc_exc_table();
		$this->transfer_content_to_inc_exc_table();
		$this->update_new_cache_files();

		$this->config->set_option('update_prev_backups_1_18_0', true);
	}

	private function delete_tables($table){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$result = $this->wpdb->query("DROP TABLE IF EXISTS `{$this->wpdb->base_prefix}$table`");
		wptc_log($result,'-----------$result----------------');
	}

	private function merge_backups_meta_tables(){
		if(!$this->config->get_option('backups_table_alterered_1_18_0')){
			$result = $this->wpdb->query("ALTER TABLE {$this->wpdb->base_prefix}wptc_backups ADD `backup_name` text NULL AFTER `backup_id`");
			wptc_log($result,'-----------$result backups_table_alterered_1_18_0----------------');
			$this->config->set_option('backups_table_alterered_1_18_0', true);
		}

		if ($this->config->get_option('backups_table_merged_1_18_0')) {
			return ;
		}

		$result = $this->wpdb->query("UPDATE {$this->wpdb->base_prefix}wptc_backups SET `backup_name` = (SELECT `backup_name` FROM {$this->wpdb->base_prefix}wptc_backup_names WHERE {$this->wpdb->base_prefix}wptc_backups.backup_id = {$this->wpdb->base_prefix}wptc_backup_names.backup_id )");

		wptc_log($result,'-----------$result merge_backups_meta_tables----------------');

		$this->config->set_option('backups_table_merged_1_18_0', true);

		$this->delete_tables('wptc_backup_names');
	}

	private function create_inc_exc_table(){

		if ( $this->config->get_option('backups_inc_exc_table_created_1_18_0') ) {
			return ;
		}

		$table 	= $this->wpdb->base_prefix . "wptc_inc_exc_contents";

		wptc_log($table,'-----------$table----------------');

		if( $this->app_functions->table_exist($table) ){
			wptc_log($table,'-----------Table exist----------------');
			return true;
		}

		include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$cachecollation = wptc_get_collation();

		dbDelta("CREATE TABLE IF NOT EXISTS $table (
			`id` int NOT NULL AUTO_INCREMENT,
			`key` text NOT NULL,
			`type` varchar(20) NOT NULL,
			`category` varchar(30) NOT NULL,
			`action` varchar(30) NOT NULL,
			`table_structure_only` int(1) NULL,
			`is_dir` int(1) NULL,
			PRIMARY KEY (`id`),
			INDEX `key` (`key`(191))
		) ENGINE=InnoDB " . $cachecollation . " ;");


		if( !$this->app_functions->table_exist($table) ){
			return WPTC_Factory::get('logger')->log('Error: v1.18.0 wptc_inc_exc_contents table creation failed.' . ( (string) $error['error_str'] ), 'others');
		}

		return $this->config->set_option('backups_inc_exc_table_created_1_18_0', true);;
	}

	private function transfer_content_to_inc_exc_table(){
		if ($this->config->get_option('transfer_content_to_inc_exc_table_1_18_0')) {
			return ;
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `is_dir`) SELECT `file`, 'file', 'backup','exclude', `isdir` from `{$this->wpdb->base_prefix}wptc_excluded_files`");
		wptc_log($result,'-----------backup  exlclude files rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `is_dir`) SELECT `file`, 'file', 'staging','exclude', `isdir` from `{$this->wpdb->base_prefix}wptc_excluded_files`");
		wptc_log($result,'-----------staging exlclude files rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `is_dir`) SELECT `file`, 'file', 'backup','include', `isdir` from `{$this->wpdb->base_prefix}wptc_included_files`");
		wptc_log($result,'-----------backup include files rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `is_dir`) SELECT `file`, 'file', 'staging','include', `isdir` from `{$this->wpdb->base_prefix}wptc_included_files`");
		wptc_log($result,'-----------staging include files rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`) SELECT `table_name`, 'table', 'backup','exclude' from `{$this->wpdb->base_prefix}wptc_excluded_tables`");
		wptc_log($result,'-----------backup exlclude tables rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`) SELECT `table_name`, 'table', 'staging','exclude' from `{$this->wpdb->base_prefix}wptc_excluded_tables`");
		wptc_log($result,'-----------staging exlclude tables rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `table_structure_only`) SELECT `table_name`, 'table', 'backup','include', `backup_structure_only` from `{$this->wpdb->base_prefix}wptc_included_tables`");
		wptc_log($result,'-----------backup include tables rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}

		$result = $this->wpdb->query("INSERT INTO {$this->wpdb->base_prefix}wptc_inc_exc_contents (`key`, `type`, `category`, `action`, `table_structure_only`) SELECT `table_name`, 'table', 'staging','include', `backup_structure_only` from `{$this->wpdb->base_prefix}wptc_included_tables`");
		wptc_log($result,'-----------staging include tables rows migrated----------------');

		if ($result === false) {
			wptc_log($this->wpdb->last_error,'-----------$this->wpdb->last_error----------------');
		}


		$this->delete_tables('wptc_excluded_files');
		$this->delete_tables('wptc_included_files');
		$this->delete_tables('wptc_excluded_tables');
		$this->delete_tables('wptc_included_tables');

		return $this->config->set_option('transfer_content_to_inc_exc_table_1_18_0', true);
	}

	private function update_new_cache_files(){
		if ($this->config->get_option('update_new_cache_files_1_18_0')) {
			return ;
		}

		$this->config->set_option('update_default_excluded_files', false);
		WPTC_Base_Factory::get('Wptc_ExcludeOption')->update_default_excluded_files();
		return $this->config->set_option('update_new_cache_files_1_18_0', true);
	}
}
