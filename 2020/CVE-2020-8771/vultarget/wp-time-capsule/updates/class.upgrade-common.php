<?php

Class Wptc_Upgrade_Common{
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
			case '1.15.5':
				$this->upgrade_1_15_5();
				break;
			case '1.15.6':
				$this->upgrade_1_15_6();
				break;
			case '1.15.7':
				$this->upgrade_1_15_7();
				break;
			case '1.15.8':
				$this->upgrade_1_15_8();
				break;
			case '1.15.10':
				$this->upgrade_1_15_10();
				break;
			case '1.16.0':
				$this->upgrade_1_16_0();
				break;
			case '1.16.1':
				$this->upgrade_1_16_1();
				break;
			case '1.16.2':
				$this->upgrade_1_16_2();
				break;
			case '1.16.3':
				$this->upgrade_1_16_3();
				break;
			case '1.17.0':
				$this->upgrade_1_17_0();
				break;
			case '1.18.0':
				$this->upgrade_1_18_0();
				break;
			case '1.19.0':
				$this->upgrade_1_19_0();
				break;
			case '1.20.0':
				$this->upgrade_1_20_0();
				break;
			case '1.20.6':
				$this->upgrade_1_20_6();
				break;
		}
	}

	private function upgrade_1_15_5(){
		$new_items 	= array(
			'.zip',
			'.log',
			'.DS_Store',
			'.git',
			'.gitignore',
			'.gitmodules',
			'.wpress',
			'.db',
			'.tmp',
		);

		$this->update_new_extensions($new_items);

		//Request to service to sync new classes
		$this->config->request_service(
				array(
					'email'           => false,
					'pwd'             => false,
					'return_response' => false,
					'sub_action' 	  => 'sync_all_settings_to_node',
					'login_request'   => true,
				)
			);
	}

	private function update_new_extensions($new_items){
		$raw_saved  = $this->config->get_option('user_excluded_extenstions');
		$saved = array_map('trim', explode(',', $raw_saved));

		foreach ($new_items as $new_item) {
			if (!in_array($new_item, $saved)) {
				$raw_saved .= ', '. $new_item;
			}
		}

		$this->config->set_option('user_excluded_extenstions', strtolower($raw_saved) );
	}

	private function upgrade_1_15_6(){

		if ($this->config->get_option('default_repo') !== 's3') {
			wptc_log(array(),'-----------This is not s3 so no upgrade required----------------');
			return ;
		}
		$current_region = $this->config->get_option('as3_bucket_region');
		if (!empty($current_region)) {
			wptc_log(array(),'-----------This user already set set region----------------');
			return ;
		}

		$access_key = $this->config->get_option('as3_access_key');
		$secure_key = $this->config->get_option('as3_secure_key');
		$bucket  	= $this->config->get_option('as3_bucket_name');

		include_once ( WPTC_PLUGIN_DIR . 'updates/update_1_15_6.php' );
		new Wptc_Update_1_15_6($access_key, $secure_key, $bucket, $this->config);
	}

	private function upgrade_1_15_7(){
		$this->config->request_service(
				array(
					'email'           => false,
					'pwd'             => false,
					'return_response' => false,
					'sub_action' 	  => 'sync_all_settings_to_node',
					'login_request'   => true,
				)
			);
	}

	private function upgrade_1_15_8(){

		if ($this->config->get_option('default_repo') === 'g_drive') {
			$this->config->set_option('cached_g_drive_this_site_main_folder_id', false);
			$this->config->set_option('cached_wptc_g_drive_folder_id', false);
		}

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$slot = $this->config->get_option('backup_slot');
		wptc_log($slot,'-----------$slot----------------');

		if ($slot != 'every_1_hour' && $slot != 'every_6_hours' ) {
			return ;
		}

		$this->config->set_option('backup_slot', 'daily');
		wptc_modify_schedule_backup();
		$this->app_functions->force_start_or_restart_backup();
	}

	private function upgrade_1_15_10(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->config->set_option('backup_db_query_limit', WPTC_DEFAULT_DB_ROWS_BACKUP_LIMIT);
		$this->config->delete_option('backup_type_setting');
	}

	private function upgrade_1_16_0(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->config->delete_option('bbu_upgrade_process_running');
	}

	private function upgrade_1_16_1(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		//Delete wrong skip tables
		$delete_qry = "DELETE FROM `{$this->wpdb->base_prefix}wptc_included_tables` WHERE
				table_name = '{$this->wpdb->base_prefix}adrotate_stats'
			OR	table_name = '{$this->wpdb->base_prefix}dmsguestbook'
			OR	table_name = '{$this->wpdb->base_prefix}icl_languages_translations'
			OR	table_name = '{$this->wpdb->base_prefix}icl_string_positions'
			OR	table_name = '{$this->wpdb->base_prefix}icl_string_translations'
			OR	table_name = '{$this->wpdb->base_prefix}icl_strings'
			OR	table_name = '{$this->wpdb->base_prefix}login_security_solution_fail'
			OR	table_name = '{$this->wpdb->base_prefix}relevanssi'
			OR	table_name = '{$this->wpdb->base_prefix}wfHits'";

		$result = $this->wpdb->query($delete_qry);

		wptc_log($result,'-----------upgrade_1_16_1 delete skip tables----------------');

		$this->add_new_content_skip_tables(array(
				$this->wpdb->base_prefix . 'blc_instances',
				$this->wpdb->base_prefix . 'bwps_log',
				$this->wpdb->base_prefix . 'Counterize',
				$this->wpdb->base_prefix . 'Counterize_Referers',
				$this->wpdb->base_prefix . 'Counterize_UserAgents',
				$this->wpdb->base_prefix . 'et_bloom_stats',
				$this->wpdb->base_prefix . 'itsec_log',
				$this->wpdb->base_prefix . 'lbakut_activity_log',
				$this->wpdb->base_prefix . 'redirection_404',
				$this->wpdb->base_prefix . 'redirection_logs',
				$this->wpdb->base_prefix . 'relevanssi_log',
				$this->wpdb->base_prefix . 'simple_feed_stats',
				$this->wpdb->base_prefix . 'slim_stats',
				$this->wpdb->base_prefix . 'statpress',
				$this->wpdb->base_prefix . 'svisitor_stat',
				$this->wpdb->base_prefix . 'tts_referrer_stats',
				$this->wpdb->base_prefix . 'tts_trafficstats',
				$this->wpdb->base_prefix . 'wbz404_logs',
				$this->wpdb->base_prefix . 'wbz404_redirects',
				$this->wpdb->base_prefix . 'woocommerce_sessions',
				$this->wpdb->base_prefix . 'wponlinebackup_generations',
				$this->wpdb->base_prefix . 'wysija_email_user_stat',
			));

		$this->app_functions->force_start_or_restart_backup();
	}

	private function add_new_content_skip_tables($tables){
		$insert_qry = '';

		foreach ($tables as $table) {
			$response = $this->wpdb->get_var("SELECT table_name from `{$this->wpdb->base_prefix}wptc_included_tables` WHERE table_name = '$table' ");
			if (!empty($response)) {
				continue;
			}

			$insert_qry .= empty($insert_qry) ? '( "' . $table . '", "1")' : ',("' . $table . '", "1")' ;
		}

		if (empty($insert_qry)) {
			return ;
		}

		$insert_qry = "INSERT INTO `{$this->wpdb->base_prefix}wptc_included_tables` (`table_name`, `backup_structure_only`) VALUES " . $insert_qry . ";";
		$result = $this->wpdb->query($insert_qry);
	}

	private function upgrade_1_16_2(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		//Delete wrong skip tables
		$delete_qry = "DELETE FROM `{$this->wpdb->base_prefix}wptc_included_tables` WHERE
				table_name = '{$this->wpdb->base_prefix}wfFileMods'
			OR	table_name = '{$this->wpdb->base_prefix}rp_tags'";

		$result = $this->wpdb->query($delete_qry);

		$this->app_functions->force_start_or_restart_backup();
	}

	private function upgrade_1_16_3(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		delete_option('wptc_disable_sentry_lib');
		$mu_error = $this->app_functions->register_Must_Use();
		wptc_log($mu_error,'-----------$mu_error on updating----------------');
	}

	private function upgrade_1_17_0(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ($this->config->get_option('default_repo') !== 's3') {
			wptc_log(array(),'-----------This is not s3 so no upgrade required----------------');
			return ;
		}

		$settings_revision_limit = $this->config->get_option('settings_revision_limit');
		$this->config->delete_option('settings_revision_limit');

		if(empty($settings_revision_limit)){
			$settings_revision_limit = $this->config->get_option('revision_limit');
		}

		$revision_limit = new Wptc_Revision_Limit();
		$revision_limit->set_revision_limit($settings_revision_limit, $cross_check_failed = true);
	}

	private function upgrade_1_18_0(){
		$this->config->set_option('site_db_prefix', $this->wpdb->base_prefix);

		include_once ( WPTC_PLUGIN_DIR . 'updates/update_1_18_0.php' );
		new Wptc_Update_1_18_0($this->app_functions, $this->wpdb, $this->config);

		$this->app_functions->force_start_or_restart_backup();

	}

	private function upgrade_1_19_0(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$updateSettings = array(
			'status' => 'yes',
			'size' => $this->config->get_option('user_excluded_files_more_than_size'),
		);

		$this->config->set_option('user_excluded_files_more_than_size_settings', serialize($updateSettings));
		$this->config->delete_option('user_excluded_files_more_than_size');

		$enable_admin_login = $this->config->get_option('internal_staging_enable_admin_login');

		if ($enable_admin_login) {
			$this->config->set_option('internal_staging_enable_admin_login', 'yes');
		} else {
			$this->config->set_option('internal_staging_enable_admin_login', 'no');
		}
	}

	private function upgrade_1_20_0(){

		$this->config->set_option('white_lable_details', false);

		$file_copy_limit = $this->config->get_option('internal_staging_file_copy_limit');

		if ($file_copy_limit > 200) {
			$this->config->set_option('internal_staging_file_copy_limit', 200);
		}

		//Request to service to sync new whitelabing format
		$this->config->request_service(
				array(
					'email'           => false,
					'pwd'             => false,
					'return_response' => false,
					'sub_action' 	  => 'sync_all_settings_to_node',
					'login_request'   => true,
				)
			);
	}

	private function upgrade_1_20_6()
	{
		wptc_log('', "--------trying update--upgrade_1_20_6------");

		$backup_slot = $this->config->get_option('backup_slot');

		if( $backup_slot == 'daily' || $backup_slot == 'every_12_hours' ){
			WPTC_Base_Factory::get('Trigger_Init')->drop_trigger_for_all_tables();
			WPTC_Base_Factory::get('Trigger_Init')->drop_table();
		} else {
			if( function_exists('allowed_to_create_triggers_common') && 
				allowed_to_create_triggers_common() ){

				wptc_log($variable, "--------triggers are altered-during upgrade_1_20_6----and backup restarted---");

				WPTC_Base_Factory::get('Trigger_Init')->create_trigger_for_all_tables($dont_create_table = true);

				$this->app_functions->force_start_or_restart_backup();
			} else {

				wptc_log('', "--------not allowed to create triggers during upgrade-1_20_6-------");

			}
		}


	}

}
