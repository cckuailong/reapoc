<?php

class Wptc_Backup_Before_Auto_Update {
	protected $config;
	protected $logger;
	protected $backup_before_update_obj;
	protected $backup_id;
	protected $update_stats;
	protected $settings;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update_Config');
		$this->logger = WPTC_Factory::get('logger');
		$this->backup_before_update_obj = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update');
		$this->backup_id = wptc_get_cookie('backupID');
		$this->settings = WPTC_Pro_Factory::get('Wptc_Backup_Before_Auto_Update_Settings');
	}

	public function simulate_fresh_backup_during_auto_update($update_details) {
		wptc_log('Function :','---------'.__FUNCTION__.'-----------------');

		//$update_args = $this->prepare_common_update_format($update_details);
		start_fresh_backup_tc_callback_wptc('manual', null, true, false);
	}

	private function prepare_common_update_format($update_details) {
		if (empty($update_details)) {
			return false;
		}

		if (!empty($update_details) && !empty($update_details->plugin)) {
			$backup_before_update = array(
				0 => $update_details->plugin,
			);
			$update_ptc_type = 'plugin';
		} elseif (!empty($update_details) && !empty($update_details->theme)) {
			$backup_before_update = array(
				0 => $update_details->theme,
			);
			$update_ptc_type = 'theme';
		} elseif (!empty($update_details) && !empty($update_details->response) && $update_details->response == 'autoupdate') {
			$backup_before_update = array(
				0 => $update_details,
			);
			$update_ptc_type = 'core';
		} elseif (!empty($update_details) && !empty($update_details->language)) {
			$backup_before_update = array(
			);
			$update_ptc_type = 'translation';
		}

		$backup_args = array(
			'action' => 'start_fresh_backup_tc_wptc',
			'type' => 'manual',
			'backup_before_update' => $backup_before_update,
			'update_ptc_type' => $update_ptc_type,
			'is_auto_update' => '1',
		);

		return $backup_args;
	}

	public function is_backup_required_before_auto_update() {
		if ($this->backup_before_update_obj->check_if_update_blocked_always_by_user_setting()) {
			return true;
		}
		return false;
	}

	public function get_current_version($type, $data){
		switch ($type) {
			case 'plugin':
				$recent_versions = array_values($data);
				return $recent_versions[0];
			case 'theme':
				$theme_data = array_values($data);
				$slug = $theme_data[0];
				wptc_log($slug, '---------------$slug-----------------');
				$theme_info = wp_get_theme( $slug );
				wptc_log($theme_info, '---------------$theme_info-----------------');
				return $theme_info->get( 'Version' );
			case 'translation':
				return false;
			case 'core':
				global $wp_version;
				return $wp_version;
			default:
				wptc_log(array(), '---------------Could not specify get_current_version-----------------');
				return false;
		}
	}

	private function init_get_updates_stats(){
		include_once ( WPTC_CLASSES_DIR . '/getUpdateStats.php' );
		$this->update_stats = new Wptc_Update_Stats();
	}

	public function set_updates(){

		$this->init_get_updates_stats();

		$stats = array();

		$this->get_upgradable_plugins( $stats );

		$this->get_upgradable_themes( $stats );

		$this->get_upgradable_translations( $stats );

		$this->get_core_update( $stats );

		wptc_log($stats,'-----------Update stats----------------');

		if (empty($stats)) {
			return false;
		}

		$this->backup_before_update_obj->bulk_update_request($stats);
		$this->config->set_option('is_bulk_update_request', true);
		$this->config->set_option('single_upgrade_details', false);
		$this->config->set_option('is_auto_update', true);
		start_fresh_backup_tc_callback_wptc('manual', null, true, false);
		send_response_wptc('Auto_update_found_new_updates_so_starting_new_backup', 'BACKUP');
	}

	public function get_upgradable_plugins(&$stats){
		$plugins_meta = $this->update_stats->get_upgradable_plugins();

		$plugins = array();

		foreach ($plugins_meta as $plugin_meta) {

			if(!$this->settings->check_if_included_plugin($plugin_meta)){
				continue;
			}

			$plugins[$plugin_meta->file] = $plugin_meta->new_version;
		}

		if (!empty($plugins)) {
			$stats['upgrade_plugins']['update_items'] = $plugins;
			$stats['upgrade_plugins']['updates_type'] = 'plugin';
			$stats['upgrade_plugins']['is_auto_update'] = '1';
		}
	}

	public function get_upgradable_themes(&$stats){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$themes_meta = $this->update_stats->get_upgradable_themes();

		wptc_log($themes_meta,'-----------$themes_meta----------------');

		$themes = array();

		foreach ($themes_meta as $theme_meta) {
			if(!$this->settings->check_if_included_theme($theme_meta['theme'])){
				continue;
			}

			$themes[$theme_meta['theme']] = $theme_meta['new_version'];
		}

		if (!empty($themes)) {
			$stats['upgrade_themes']['update_items'] = $themes;
			$stats['upgrade_themes']['updates_type'] = 'theme';
			$stats['upgrade_themes']['is_auto_update'] = '1';
		}
	}

	public function get_core_update(&$stats){
		$core_updates = $this->update_stats->get_core_update();

		$core_update = $this->settings->check_if_included_core($core_updates);

		if (!empty($core_update)) {
			$stats['wp_upgrade']['update_items'] = $core_updates;
			$stats['wp_upgrade']['updates_type'] = 'core';
			$stats['wp_upgrade']['is_auto_update'] = '1';
		}
	}

	public function get_upgradable_translations(&$stats){
		$translation_meta = $this->update_stats->get_upgradable_translations();

		$translations = false;

		if ($translation_meta) {
			$translations = $this->settings->check_if_included_translation($translation_meta);
		}

		if (!empty($translations)) {
			$stats['upgrade_translations']['update_items'] = $translations;
			$stats['upgrade_translations']['updates_type'] = 'translation';
			$stats['upgrade_translations']['is_auto_update'] = '1';
		}
	}

	public function auto_update_failed_email_user(){
		$excluded_list = $this->config->get_option('temp_auto_excluded_auto_updates_lists');

		if (empty($excluded_list)) {
			return ;
		}

		$excluded_list = unserialize($excluded_list);

		$this->config->set_option('temp_auto_excluded_auto_updates_lists', false);

		$post_arr = array(
			'type' => 'auto_update_failed',
			'update_info' => $excluded_list,
		);

		WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper')->do_call('users/alert', $post_arr);
	}

	public function check_auto_updates(){

		$post_arr = array(
			'cron_url' => $this->config->get_option('site_url_wptc'),
			'timezone' => $this->config->get_option('wptc_timezone'),
		);

		WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper')->do_call('check-auto-updates', $post_arr);
	}
}
