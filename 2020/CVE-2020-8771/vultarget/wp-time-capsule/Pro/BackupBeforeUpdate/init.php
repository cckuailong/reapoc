<?php

include_once 'utils.php';

class Wptc_Backup_Before_Update extends WPTC_Privileges {
	protected $db;
	protected $config;
	protected $logger;
	protected $cron_server_curl;
	protected $upgrade_error_keys;
	protected $upgrade_wp_flow_keys;
	protected $upgrade_success_keys;
	protected $is_bulk_update_request;
	protected $upgrade_ptc_types;
	protected $update_common;
	protected $backup_id;
	protected $process_upgrade_extra_note;
	protected $process_upgrade_type;
	protected $process_upgrade_type_prefix;
	protected $process_upgrade_response;
	protected $process_upgrade_autoupdate;
	protected $process_upgrade_version;
	protected $process_upgrade_item_type;
	protected $auto_update_settings;
	const MAX_RETRY = 2;
	protected $retry_failed_upgrades = array(
		'manual' => array(
				'plugin'      => true,
				'theme'       => true,
				'core'        => false, //Not tested
				'translation' => false, //Not tested
			),
		'auto'   => array(
				'plugin'      => true,
				'theme'       => true,
				'core'        => false, //Not tested
				'translation' => false, //Not tested
			),
	) ;

	public function __construct() {
		$this->config                 = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update_Config');
		$this->logger                 = WPTC_Factory::get('logger');
		$this->update_common          = WPTC_Base_Factory::get('Wptc_Update_Common');
		$this->cron_server_curl       = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
		$this->auto_update_settings   = WPTC_Pro_Factory::get('Wptc_Backup_Before_Auto_Update_Settings');
		$this->is_bulk_update_request = false;
		$this->upgrade_ptc_types      = array('plugin' => 'Plugins', 'theme' => 'Themes', 'core' => 'WordPress', 'translation' => 'Translations');
		$this->upgrade_error_keys     = array
		(
			'bad_request',
			'fs_unavailable',
			'fs_error',
			'fs_no_root_dir',
			'fs_no_content_dir',
			'fs_no_plugins_dir',
			'fs_no_themes_dir',
			'fs_no_folder',
			'download_failed',
			'no_package',
			'no_files',
			'folder_exists',
			'mkdir_failed',
			'incompatible_archive',
			'files_not_writable',
			'process_failed',
			'remove_old_failed'
		);
		$this->upgrade_wp_flow_keys = array
		(
			'installing_package',
			'maintenance_start',
			'maintenance_end',
			'downloading_package',
			'unpack_package',
			'remove_old'
		);
		$this->upgrade_success_keys = array(
			'up_to_date',
			'process_success'
		);

	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on()
	{
		return true;
	}

	public function check_and_initiate_if_update_required_after_backup_wptc($args) {
		if ( isset($args['backup_before_update']) 
			&& isset($args['update_ptc_type']) ) {
			$update_needed = $args['backup_before_update'];
			$update_ptc_type = $args['update_ptc_type'];
			$is_auto_update = $args['is_auto_update'];
			$this->purify_update_req_data_wptc($update_needed, $update_ptc_type, $is_auto_update);
		}
	}


	public function check_site_alive_after_update(){
		$post_arr = array(
			'event' => 'test_connection_send_email',
			'plugin_version' => WPTC_VERSION,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	private function purify_update_req_data_wptc($raw_upgrade_details, $update_ptc_type, $is_auto_update) {
		if ($update_ptc_type == 'plugin') {
			$upgrade_details = purify_plugin_update_data_wptc($raw_upgrade_details);
		} else if ($update_ptc_type == 'theme') {
			$upgrade_details = purify_theme_update_data_wptc($raw_upgrade_details);
		} else if ($update_ptc_type == 'core') {
			if (!$is_auto_update) {
				$upgrade_details = purify_core_update_data_wptc($raw_upgrade_details);
			} else {
				$upgrade_details = $raw_upgrade_details;
			}
		} else if ($update_ptc_type == 'translation') {
			$upgrade_details = purify_translation_update_data_wptc($raw_upgrade_details);
		}

		$this->update_formated_single_upgrade_details($update_ptc_type, $upgrade_details, $is_auto_update);
	}

	private function update_formated_single_upgrade_details($update_ptc_type, $upgrade_details, $is_auto_update) {
		wptc_log($upgrade_details, '---------upgrade_details-------------');

		$upgrade_details_data['update_items'] = $upgrade_details;
		$upgrade_details_data['updates_type'] = $update_ptc_type;
		$upgrade_details_data['is_auto_update'] = $is_auto_update;
		wptc_log($upgrade_details_data, '---------bacup_before_upgrade_details_data-------------');
		if (!$upgrade_details_data) {
			return false;
		}
		$this->config->set_option('single_upgrade_details', serialize($upgrade_details_data));
	}

	public function check_if_update_blocked_always_by_user_setting() {
		if ($this->config->get_option('backup_before_update_setting') && ($this->config->get_option('backup_before_update_setting') == 'always')) {
			wptc_log($this->config->get_option('backup_before_update_setting'), "--------check_if_update_blocked_always_by_user_setting--------");
			return true;
		}
		return false;
	}

	public function do_single_upgrades() {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$raw_upgrade_details = $this->config->get_option('single_upgrade_details');

		if (empty($raw_upgrade_details)) {
			return false;
		}

		$upgrade_details = unserialize($raw_upgrade_details);

		if (empty($upgrade_details) || !is_array($upgrade_details)) {
			return false;
		}

		wptc_log($upgrade_details, '---------$upgrade_details i want-------------');

		$type_of_update = $upgrade_details['updates_type'];
		$update_items   = $upgrade_details['update_items'];
		$is_auto_update = !empty($upgrade_details['is_auto_update']) ? true : false;

		if (empty($type_of_update) || empty($update_items)) {
			if($type_of_update != 'translation'){
				return false;
			}
		}

		$this->do_upgrade($type_of_update, $update_items, $upgrade_details, $is_auto_update);
	}

	private function do_upgrade($type_of_update, $update_items, $upgrade_details, $is_auto_update = false){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->config->set_option('backup_before_update_progress', $type_of_update);

		wptc_set_time_limit(0);

		$more_updates_available = $this->remove_a_item_from_update_list($upgrade_details, $type_of_update);

		wptc_log($more_updates_available,'-----------$more_updates_available----------------');

		switch ($type_of_update) {
			case 'plugin':
				$response = $this->do_plugin_upgrade($update_items);
				break;
			case 'theme':
				$response = $this->do_theme_upgrade($update_items);
				break;
			case 'core':
				$response = $this->upgrade_core($update_items);
				break;
			case 'translation':
				$response = $this->upgrade_translation($update_items);
				break;
		}

		wptc_log($response, '---------$response------------');

		$this->process_upgrade_response($type_of_update, $response, $is_auto_update);

		wptc_log($more_updates_available, '--------$more_updates_available--------');

		if ( $more_updates_available && is_wptc_server_req() ) {
			$this->config->set_option('upgrade_process_running', false);
			send_response_wptc('SEND_ANOTHER_REQUESTS_TO_PERFORM_' . strtoupper($type_of_update) . 'S_UPDATE', 'BACKUP');
		}

		$this->parse_bulk_upgrade_response();
		$this->config->set_option('backup_before_update_progress', false);
		// $this->config->set_option('is_it_fresh_request', false);
		$this->finish_upgrade_process();
	}

	private function finish_upgrade_process(){

		wptc_log('', "--------finish_upgrade_process--------");

		$this->config->set_option('start_upgrade_process', false);
	}

	private function do_plugin_upgrade($update_items){
		$this->ithemes_updater_compatiblity();

		$first_plugin_value = reset($update_items);
		$first_plugin_key   = key($update_items);

		$update_items       = array(
			$first_plugin_key => $first_plugin_value
		);

		wptc_log($update_items, '--------do_plugin_upgrade--------');

		$values = array_values($update_items);

		wptc_log($values,'-----------$values----------------');

		$this->process_upgrade_version = $values[0];

		wptc_log($this->process_upgrade_version,'-----------$this->process_upgrade_version----------------');

		$response = $this->upgrade_plugin($update_items);

		$this->ithemes_updater_compatiblity();

		return $response;
	}

	private function do_theme_upgrade($update_items){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		reset($update_items);

		$first_theme   = key($update_items);

		wptc_log($first_theme,'-----------$first_theme 1----------------');

		if (!is_string($first_theme)) {
			$first_theme = $update_items[$first_theme];
		}

		wptc_log($first_theme,'-----------$first_theme 2----------------');

		$update_items 		= array($first_theme);

		wptc_log($update_items, '-------update_items first_theme--------');

		return $this->upgrade_theme($update_items);
	}

	private function remove_a_item_from_update_list($upgrade_details, $type_of_update){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		switch ($type_of_update) {
			case 'plugin':
			case 'theme':
				return $this->unset_a_plugin_theme_from_upgrade_list($upgrade_details);
				break;
			case 'core':
				$this->config->set_option('single_upgrade_details', false);
				break;
			case 'translation':
				$this->config->set_option('single_upgrade_details', false);
				break;
		}

		return false;
	}

	private function unset_a_plugin_theme_from_upgrade_list($upgrade_details){
		if(count($upgrade_details['update_items']) <= 1){
			$this->config->set_option('single_upgrade_details', false);
			return false;
		}

		//reset keys
		reset($upgrade_details['update_items']);

		$first_key = key($upgrade_details['update_items']);

		//unset first key
		unset($upgrade_details['update_items'][$first_key]);

		wptc_log($upgrade_details, '--------Remaining updates--------');

		$this->config->set_option('single_upgrade_details', serialize($upgrade_details));
		return true;
	}

	public function parse_bulk_upgrade_response(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ( $this->config->get_option('is_bulk_update_request') 
			&& $this->is_bulk_update_request === false ) {

			wptc_log(array(), '--------Comes 1--------');

			$this->config->set_option('upgrade_process_running', false);
			
			send_response_wptc('I_HAVE_SOME_MORE_BULK_UPDATES', 'BACKUP');
		}

		$raw_data = $this->config->get_option('update_response_details');
		if (empty($raw_data)) {
			return false;
		}

		$upgrade_responses = unserialize($raw_data);

		wptc_log($upgrade_responses, '--------$upgrade_responses update_response_details--------');
		$update_type = $upgrade_responses['update_type'];

		//set notice content
		$this->set_bbu_notice_view($upgrade_responses, $update_type);

		//set backup name
		$this->set_bbu_backup_name($upgrade_responses, $update_type);

		$this->send_report_data($upgrade_responses, $update_type);

		//Map update resulst with current backup
		$this->map_update_results_with_backup($upgrade_responses);

		wptc_log($this->config->get_option('is_auto_update'),'-----------$this->config->set_option(is_auto_update)----------------');
		
		if ($this->config->get_option('is_auto_update')) {

			wptc_log(array(),'-----------Yes----------------');

			$this->check_site_alive_after_update();
		}

	}


	private function send_report_data($upgrade_responses, $update_type){

		$type = ($update_type === 'manual') ? 'MANUAL_UPDATE' : 'AUTO_UPDATE';

		$extra_data = array();
		$total_upgrades = $success = $failed = 0;

		if ( !empty($upgrade_responses['plugin']) ) {
			foreach ($upgrade_responses['plugin'] as $response) {
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$extra_data['passed_plugins'][] = $response['name'];
					$success++;
				} else {
					$extra_data['failed_plugins'][] = $response['name'];
					$failed++;
				}
			}
		}

		if ( !empty($upgrade_responses['theme']) ) {
			foreach ($upgrade_responses['theme'] as $response) {
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$extra_data['passed_themes'][] = $response['name'];
					$success++;
				} else {
					$extra_data['failed_themes'][] = $response['name'];
					$failed++;
				}
			}
		}

		if ( !empty($upgrade_responses['core']) ) {
			foreach ($upgrade_responses['core'] as $response) {
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$extra_data['passed_core'] = $response['version'];
					$success++;
				} else {
					$extra_data['failed_core'] = $response['version'];
					$failed++;
				}
			}
		}

		if ( !empty($upgrade_responses['translation']) ) {
			foreach ($upgrade_responses['translation'] as $response) {
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$extra_data['passed_translations'] = true;
					$success++;
				} else {
					$extra_data['failed_translations'] = true;
					$failed++;
				}
			}
		}

		do_action('send_report_data_wptc', wptc_get_cookie('backupID'), $type, 'STARTED');

		sleep(1);

		wptc_log($extra_data, '--------$extra_data--------');
		wptc_log($total_upgrades, '--------$total_upgrades--------');
		wptc_log($success, '--------$success--------');

		if ($success === $total_upgrades) {
			do_action('send_report_data_wptc', wptc_get_cookie('backupID'), $type, 'SUCCESS', $extra_data);
		} else {
			do_action('send_report_data_wptc', wptc_get_cookie('backupID'), $type, 'FAILED', $extra_data);
		}

	}

	private function set_bbu_notice_view($upgrade_responses, $update_type){

		if ($this->is_bulk_update_request === true) { //IWP requested updates never need notice
			return false;
		}

		if ($update_type === 'auto') { //Auto updates never needs specific notice views.
			return false;
		}

		$total_upgrades = $success = $failed = 0;
		$ptc_type = '';

		if (!empty($upgrade_responses['plugin'])) {
			foreach ($upgrade_responses['plugin'] as $response) {
				$ptc_type = $this->upgrade_ptc_types['plugin'];
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$success++;
				} else {
					$failed++;
				}
			}
		} else if (!empty($upgrade_responses['theme'])) {
			foreach ($upgrade_responses['theme'] as $response) {
				$ptc_type = $this->upgrade_ptc_types['theme'];
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$success++;
				} else {
					$failed++;
				}
			}
		} else if (!empty($upgrade_responses['core'])) {
			foreach ($upgrade_responses['core'] as $response) {
				$ptc_type = $this->upgrade_ptc_types['core'];
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$success++;
				} else {
					$failed++;
				}
			}
		} else if (!empty($upgrade_responses['translation'])) {
			foreach ($upgrade_responses['translation'] as $response) {
				$ptc_type = $this->upgrade_ptc_types['translation'];
				$total_upgrades++;
				if ($response['status'] === 'success') {
					$success++;
				} else {
					$failed++;
				}
			}
		}

		wptc_log($total_upgrades, '--------$total_upgrades--------');
		wptc_log($success, '--------$success--------');
		wptc_log($failed, '--------$failed--------');
		wptc_log($ptc_type, '--------$ptc_type--------');
		if ($total_upgrades > 1) {
				if ($success === $total_upgrades) {
					$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $total_upgrades .' '.$ptc_type.' updated successfully :)')));
				} else if ($failed === $total_upgrades) {
					$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $total_upgrades .' '.$ptc_type.' updates failed.')));
				} else {
					$this->config->set_option('bbu_note_view', serialize(array('type' => 'warning', 'note' => $success.' '.$ptc_type.' updated successfully and '.$failed.' plugin updates failed.')));
				}
		} else {
			if ($success) {
				if ($ptc_type === 'Plugins' || $ptc_type ===  'Themes') {
					$ptc_type = $response['name'];
				}
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $ptc_type.' updated successfully :)')));
			} else {
				if ($ptc_type === 'Plugins' || $ptc_type ===  'Themes') {
					$ptc_type = $response['name'];
				}
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $ptc_type.' upgrade failed.')));;
			}
		}
	}

	private function set_bbu_backup_name($upgrade_responses, $update_type){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$total_upgrades = 0;
		$name  = $backup_name = '';
		if (!empty($upgrade_responses['plugin'])) {
			foreach ($upgrade_responses['plugin'] as $response) {

				//Skip failed items
				if ($response['status'] === 'failed') {
					continue;
				}

				$total_upgrades++;
				if ($name) {
					if ($response['version']) {
						$name .= ', '.$response['name']. '-'.$response['version'];
					} else {
						$name .= ', '.$response['name'];
					}
				} else {
					if ($response['version']) {
						$name .= $response['name']. '-'.$response['version'];
					} else {
						$name .= $response['name'];;
					}
				}
			}
		}

		if ($name) {
			if ($total_upgrades > 1) {
				$backup_name = $total_upgrades. ' '. $this->upgrade_ptc_types['plugin'] . ' ( '.$name.' ) ';
			} else {
				$backup_name = $total_upgrades. ' Plugin ( '.$name.' ) ';
			}
		}

		$total_upgrades = 0;
		$name = '';
		if (!empty($upgrade_responses['theme'])) {
			foreach ($upgrade_responses['theme'] as $response) {

				//Skip failed items
				if ($response['status'] === 'failed') {
					continue;
				}

				$total_upgrades++;
				if ($name) {
					if ($response['version']) {
						$name .= ', '.$response['name']. '-'.$response['version'];
					} else {
						$name .= ', '.$response['name'];
					}
				} else {
					if ($response['version']) {
						$name .= $response['name']. '-'.$response['version'];
					} else {
						$name .= $response['name'];
					}
				}
			}
		}
		wptc_log($total_upgrades, '--------$total_upgrades--------');
		if ($name) {
			if ($backup_name) {
				if ($total_upgrades > 1) {
					$backup_name .= ', ' . $total_upgrades. ' '. $this->upgrade_ptc_types['theme'] . ' ( '.$name.' ) ';
				} else {
					$backup_name .= ', ' . $total_upgrades. ' Theme ( '.$name.' ) ';
				}
			} else {
				if ($total_upgrades > 1) {
					$backup_name = $total_upgrades. ' '. $this->upgrade_ptc_types['theme'] . ' ( '.$name.' ) ';
				} else {
					$backup_name .= $total_upgrades. ' Theme ( '.$name.' ) ';
				}
			}
		}


		if (!empty($upgrade_responses['core'] ) ) {

			if ($backup_name) {
				if ($upgrade_responses['core'][0]['version']) {
					$backup_name .= ', ' . $this->upgrade_ptc_types['core'] . '-'.$upgrade_responses['core'][0]['version'];
				} else {
					$backup_name .= ', ' . $this->upgrade_ptc_types['core'];
				}
			} else {
				if ($upgrade_responses['core'][0]['version']) {
					$backup_name = $this->upgrade_ptc_types['core'] . '-'.$upgrade_responses['core'][0]['version'];
				} else {
					$backup_name = $this->upgrade_ptc_types['core'];
				}
			}
		}

		if (!empty($upgrade_responses['translation'] ) ) {
			if ($backup_name) {
				$backup_name .= ', ' . $this->upgrade_ptc_types['translation'];
			} else {
				$backup_name = $this->upgrade_ptc_types['translation'];
			}
		}

		wptc_log($backup_name, '--------$backup_name--------');

		if (empty($backup_name)) {
			wptc_log(array(),'-----------Empty name skipped----------------');
			return ;
		}

		if($update_type === 'manual'){
			if ($this->config->get_option('is_vulns_updates')) {
				store_backup_name_wptc(array('backup_name' => $backup_name .' updated via vulnerable updates.'));
			} else {
				store_backup_name_wptc(array('backup_name' => $backup_name .' updated manually.'));
			}
		} else{
			store_backup_name_wptc(array('backup_name' => $backup_name .' auto updated.'));
		}
	}

	private function map_update_results_with_backup($upgrade_responses){
		$processed_files = WPTC_Factory::get('processed-files', true);
		$processed_files->save_PTC_update_response($upgrade_responses);
	}

	public function process_upgrade_response($type_of_update, $response, $autoupdate = false){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->check_site_alive_after_update();

		$this->backup_id                          = wptc_get_cookie('backupID');
		$this->process_upgrade_autoupdate         = $autoupdate;
		$this->process_upgrade_type_prefix        = ($this->process_upgrade_autoupdate) ? 'auto'        : 'manual';
		$this->process_upgrade_extra_note         = ($this->process_upgrade_autoupdate) ? ' (AU) - '    : '';
		$this->process_upgrade_type               = ($this->process_upgrade_autoupdate) ? 'auto_update' : 'backup_and_update';
		$this->process_upgrade_item_type          = $type_of_update;
		$this->process_upgrade_response           = $response;

		if($this->process_upgrade_autoupdate){
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'message', 'note' => 'WPTC performed some auto-updates, check Activity log for more info.')));
		}

		//Test any error from the response like hard error so we cannot proceed.
		if ($this->is_upgrade_response_contains_errors()) {
			wptc_log(array(),'-----------Update contains error so skipping further checks----------------');
			return ;
		}

		switch ($this->process_upgrade_item_type) {
			case 'plugin':
				return $this->process_plugin_upgrade_response();
			case 'theme':
				return $this->process_theme_upgrade_response();
			case 'core':
				return $this->process_core_upgrade_response();
			case 'translation':
				return $this->process_translation_upgrade_response();
		}
	}

	private function is_upgrade_response_contains_errors(){

		wptc_log($this->process_upgrade_response, "--------is_upgrade_response_contains_errors--------");

		if (empty($this->process_upgrade_response)) {
			if($this->process_upgrade_autoupdate){
				$this->check_auto_update_failed_and_exclude_update('Empty response: Upgrade Failed');
			} else{
				$this->update_backup_name_wptc('', false, $this->process_upgrade_type_prefix, false, 'Upgrade failed.');
			}

			$this->logger->log(__($this->process_upgrade_extra_note . ' ' . $this->upgrade_ptc_types[$this->process_upgrade_item_type] . ' ' . "Upgrade failed .", 'wptc') , $this->process_upgrade_type, $this->backup_id);

			return true;

		} else if( isset($this->process_upgrade_response['error']) ){

			if($this->process_upgrade_autoupdate){
				$this->check_auto_update_failed_and_exclude_update( $this->process_upgrade_response['error']);
			} else{
				$this->update_backup_name_wptc('', false, $this->process_upgrade_type_prefix, false , $this->process_upgrade_response['error']);
			}

			$this->logger->log(__($this->process_upgrade_extra_note . ' ' . $this->upgrade_ptc_types[$this->process_upgrade_item_type] . ' ' . " Upgrade failed - Reason - " .  $this->process_upgrade_response['error'], 'wptc'), $this->process_upgrade_type, $this->backup_id);

			return true;
		}

		return false;
	}

	private function is_upgrade_failed(){
		if (!isset($this->process_upgrade_response['upgraded'])) {
			$this->logger->log(__($this->process_upgrade_extra_note . "Updating " . $this->process_upgrade_item_type . " failed - response is broken", 'wptc'), $this->process_upgrade_type, $this->backup_id);
			return true;
		}

		return false;
	}

	private function process_plugin_upgrade_response(){
		if ($this->is_upgrade_failed($this->process_upgrade_response, $this->backup_id, $this->process_upgrade_item_type)) {
			wptc_log(array(),'-----------Upgrade failed ----------------');
			return ;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins_data  = get_plugins();

		$update_status = $err_msg =  false;

		foreach ($this->process_upgrade_response['upgraded'] as $key => $value) {

			$name    = $plugins_data[$key]['Name'];
			$version = $plugins_data[$key]['Version'];

			if ($value === 1) {
				$this->logger->log(__($this->process_upgrade_extra_note . "Plugin " . $name . " updated successfully", 'wptc'), $this->process_upgrade_type, $this->backup_id);
				$update_status = true;
			} else {
				$this->logger->log(__($this->process_upgrade_extra_note . "Plugin " . $name . ' update failed - ' . $value['error'], 'wptc'), $this->process_upgrade_type, $this->backup_id);
				$err_msg = $value['error'];
				$update_status = false;
				$this->retry_failed_upgrades($key, $name);
			}

			//save response data
			$this->update_backup_name_wptc($name, $update_status, $version, $err_msg);
		}
	}

	private function process_theme_upgrade_response(){
		if ($this->is_upgrade_failed($this->process_upgrade_response)) {
			wptc_log(array(),'-----------Upgrade failed ----------------');
			return ;
		}

		$update_status =  $err_msg =  false;
		foreach ($this->process_upgrade_response['upgraded'] as $key => $value) {

			$theme_info = wp_get_theme($key);

			if (!empty($theme_info)) {
				$name = $theme_info->get( 'Name' );
				$version = $theme_info->get( 'Version' );
			} else {
				$name = 'theme';
				$version = 0;
			}

			if ($value === 1) {
				$this->logger->log(__($this->process_upgrade_extra_note . "Theme " . $name . " updated successfully", 'wptc'), $this->process_upgrade_type, $this->backup_id);
				$update_status = true;
			} else{
				$this->logger->log(__($this->process_upgrade_extra_note . "Theme " . $name . ' update failed - ' . $value['error'], 'wptc'), $this->process_upgrade_type, $this->backup_id);
				$update_status = false;
				$err_msg = $value['error'];
				$this->retry_failed_upgrades($key, $name);
			}

			//save response data
			$this->update_backup_name_wptc($name, $update_status, $version, $err_msg);
		}
	}

	private function process_core_upgrade_response(){
		$update_status = $err_msg = false;

		$current_core_version = WPTC_Base_Factory::get('Wptc_App_Functions')->get_wp_core_version($hard_refresh = true );

		if (isset($this->process_upgrade_response['upgraded'])) {
			$update_status = true;
			$this->logger->log(__($this->process_upgrade_extra_note . "WordPress v" . $current_core_version . " updated successfully", 'wptc'), $this->process_upgrade_type, $this->backup_id);
		} else {
			$this->logger->log(__($this->process_upgrade_extra_note . "Updating WordPress v " . $this->process_upgrade_version . " failed - " . $this->process_upgrade_response['error'], 'wptc'), $this->process_upgrade_type, $this->backup_id);
			$update_status = false;
			$err_msg = $this->process_upgrade_response['error'];
			$this->retry_failed_upgrades(false, 'WordPress');
		}


		//save response data
		$this->update_backup_name_wptc('WordPress', $update_status, $current_core_version, $err_msg);
	}

	private function process_translation_upgrade_response(){

		$update_status = $err_msg = false;
		if (isset($this->process_upgrade_response['upgraded']) && !isset($this->process_upgrade_response['upgraded']['error'])) {
			$update_status = true;
			$this->logger->log(__($this->process_upgrade_extra_note . "Translations updated successfully", 'wptc'), $this->process_upgrade_type, $this->backup_id);
		} else {
			$err_msg = ($this->process_upgrade_response['error']) ? $this->process_upgrade_response['error'] : $this->process_upgrade_response['upgraded']['error'] ;
			$this->logger->log(__($this->process_upgrade_extra_note . "Updating translation failed - " . $err_msg, 'wptc'), $this->process_upgrade_type, $this->backup_id);
			$update_status = false;
			$this->retry_failed_upgrades(false, 'Translations');
		}

		//save response data
		$this->update_backup_name_wptc('Translations', $update_status, false, $err_msg);
	}

	private function check_auto_update_failed_and_exclude_update($err_msg){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if(!$this->process_upgrade_autoupdate){
			return false;
		}

		if ($this->process_upgrade_item_type === 'core') {

			$current_core_version = WPTC_Base_Factory::get('Wptc_App_Functions')->get_wp_core_version($hard_refresh = true );

			if($this->verify_auto_update_plugin($this->process_upgrade_version, $current_core_version)){
				return $this->logger->log("WordPress v" . $this->process_upgrade_version . " updated successfully." , $this->process_upgrade_type, $this->backup_id);
			}

			$name = 'WordPress';
			$err_msg = !empty($err_msg) ? $err_msg : "WordPress update failed. No errors found.";
		} else if ($this->process_upgrade_item_type === 'translation') {
			$name = 'Translations';
			$err_msg = !empty($err_msg) ? $err_msg : "Translations update failed. No errors found.";
		} else {
			return ;
			// return $this->logger->log($this->process_upgrade_item_type  . " Failed to update to v" . $this->process_upgrade_version , $this->process_upgrade_type, $this->backup_id);
		}

		// $this->logger->log($err_msg , $this->process_upgrade_item_type, $this->backup_id);
		$this->auto_update_settings->exclude_item_from_auto_update(false ,$name, $this->process_upgrade_version, $this->process_upgrade_item_type, $this->backup_id);
	}

	private function retry_failed_upgrades($key, $name, $exclude_from_settings = true){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (!$this->retry_failed_upgrades[$this->process_upgrade_type_prefix][$this->process_upgrade_item_type]) {

			wptc_log($this->process_upgrade_type_prefix . '-' . $this->process_upgrade_item_type ,'-----------Retry disabled for this type of updates----------------');

			if ($exclude_from_settings) {
				$this->auto_update_settings->exclude_item_from_auto_update($key, $name, $this->process_upgrade_version, $this->process_upgrade_item_type, $this->backup_id);
			}

			return false;
		}

		if (!$this->check_retry_limit($key)) {

			wptc_log(array(),'-----------Limit execeeds----------------');

			if ($this->process_upgrade_autoupdate) {

				wptc_log(array(),'-----------AUTo update so exclude from list----------------');

				if ($exclude_from_settings) {
					$this->auto_update_settings->exclude_item_from_auto_update($key, $name, $this->process_upgrade_version, $this->process_upgrade_item_type, $this->backup_id);
				}

			}

			return false;
		}

		$this->logger->log(__('Retrying update : ' . $name  , 'wptc'), $this->process_upgrade_type, $this->backup_id);

		$this->add_into_retry_list($key);

		$this->config->set_option('upgrade_process_running', false);

		send_response_wptc('Retrying_failed_'. $this->process_upgrade_item_type .' '. $key , 'BACKUP');

	}

	private function check_retry_limit($key){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$raw_list = $this->config->get_option('retry-upgrade-list');

		wptc_log($raw_list,'-----------$raw_list----------------');

		$list = array();

		if (!empty($raw_list)) {
			$list = unserialize($raw_list);
		}

		if (!empty( $list[$this->process_upgrade_item_type][$key] ) ) {
			if ($list[$this->process_upgrade_item_type][$key] < self::MAX_RETRY) {
				return false;
			}

			$list[$this->process_upgrade_item_type][$key] += 1;
		} else {
			$list[$this->process_upgrade_item_type][$key] = 1;
		}

		wptc_log($list,'-----------$list----------------');

		$this->config->set_option('retry-upgrade-list', serialize($list));
		return true;
	}

	private function add_into_retry_list($key){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$raw_upgrade_details = $this->config->get_option('single_upgrade_details');

		$upgrade_details = array();
		if (!empty( $raw_upgrade_details )) {
			$upgrade_details = unserialize($raw_upgrade_details);
		}

		wptc_log($upgrade_details,'-----------$upgrade_details----------------');

		if ($this->process_upgrade_item_type === 'plugin') {
			if (empty($upgrade_details)) {
				$upgrade_details = array (
					'update_items'   => array ( $key => $this->process_upgrade_version ),
					'updates_type'   => 'plugin',
					'is_auto_update' => $this->process_upgrade_autoupdate,
				);
			} else {
				$upgrade_details['update_items'] = array ( $key => $this->process_upgrade_version ) + $upgrade_details['update_items'];
			}
		} else if ($this->process_upgrade_item_type === 'theme') {
			if (empty($upgrade_details)) {
				$upgrade_details = array (
					'update_items'   => array ( $key ),
					'updates_type'   => 'theme',
					'is_auto_update' => $this->process_upgrade_autoupdate,
				);
			} else {
				$upgrade_details['update_items'] =  array ( $key ) + $upgrade_details['update_items'];
			}
		}

		wptc_log($upgrade_details,'-----------$upgrade_details final----------------');
		$this->config->set_option('single_upgrade_details', serialize($upgrade_details));

		return true;
	}

	private function update_backup_name_wptc( $ptc_name, $update_result, $version = false, $err_msg = false){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		//perform a structure for cummulative results
		$raw_update_response_details = $this->config->get_option('update_response_details');
		if (empty($raw_update_response_details)) {
			//first update
			$update_response_details['update_type'] = $this->process_upgrade_type_prefix;
		} else {
			$update_response_details = unserialize($raw_update_response_details);
		}

		$update_response_details[$this->process_upgrade_item_type][] = array(
			'name'    => $ptc_name,
			'version' => $version,
			'status'  => empty($update_result) ? 'failed' : 'success',
			'err_msg' => $err_msg,
			);

		$this->config->set_option('update_response_details', serialize($update_response_details));
	}

	private function verify_auto_update_plugin($old_version, $updated_version){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		return ( version_compare( $old_version, $updated_version, '=' ) ) ? true : false;
	}

	public function upgrade_plugin($plugins, $plugin_details = false) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (!$plugins || empty($plugins)) {
			return array(
				'error' => 'No plugin files for upgrade.',
			);
		}

		if (!is_server_writable_wptc()) {
			return $this->get_server_not_writable_arr();
		}

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if (!function_exists('wp_update_plugins')) {
			include_once(ABSPATH . 'wp-includes/update.php');
		}

		if (!class_exists('Plugin_Upgrader')) {
			@ob_end_clean();
			return array(
				'error' => 'WordPress update required first.'
			);
		}


		@wp_update_plugins();

		$upgrader_skin = new WPTC_Updater_TraceableUpdaterSkin();
		$upgrader = new Plugin_Upgrader($upgrader_skin);
		$result = $upgrader->bulk_upgrade(array_keys($plugins));
		wptc_log($upgrader_skin->get_upgrade_messages(), '--------$upgrader_skin PLUGIN--------');
		if (empty($result)) {
			return array(
				'error' => 'Upgrade failed .'
			);
		}

		$return = array();
		foreach ($result as $plugin_slug => $plugin_info) {
			if (!$plugin_info || is_wp_error($plugin_info)) {
				$return[$plugin_slug] = array('error' => $this->parse_upgrade_response($upgrader_skin->get_upgrade_messages()));
				continue;
			}

			$return[$plugin_slug] = 1;
		}

		return array(
			'upgraded' => $return,
		);
	}

	private function get_server_not_writable_arr(){
		return array(
			'error' => 'Failed, Server is not writable, please <a target="_blank" href="http://docs.wptimecapsule.com/article/24-why-does-plugins-themes-fail-to-update">add FTP details</a>',
		);
	}


	public function upgrade_theme($themes, $theme_details = false) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (!$themes || empty($themes)) {
			return array(
				'error' => 'No theme files for upgrade.',
			);
		}

		if (!is_server_writable_wptc()) {
			return $this->get_server_not_writable_arr();
		}

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/theme.php';

		if (!function_exists('wp_update_themes')) {
			include_once ABSPATH . 'wp-includes/update.php';
		}

		@wp_update_themes();

		if (!class_exists('Theme_Upgrader')) {
			@ob_end_clean();
			return array(
				'error' => 'WordPress update required first'
			);
		}

		$upgrader_skin = new WPTC_Updater_TraceableUpdaterSkin();
		$upgrader = new Theme_Upgrader($upgrader_skin);
		$result = $upgrader->bulk_upgrade($themes);

		wptc_log($upgrader_skin, '--------$upgrader_skin--------');
		wptc_log($upgrader_skin->get_upgrade_messages(), '-------feed back of skinn--------');

		$return  = array();
		if (empty($result)) {
			return array(
				'error' => 'Upgrade failed.',
			);
		}

		foreach ($result as $theme_tmp => $theme_info) {
			if (is_wp_error($theme_info) || empty($theme_info)) {
				$return[$theme_tmp] = array('error' => $this->parse_upgrade_response($upgrader_skin->get_upgrade_messages()));
				continue;
			}

			$return[$theme_tmp] = 1;
		}

		return array(
			'upgraded' => $return,
		);
	}

	public function upgrade_core($current) {

		$current = (array) $current;
		wptc_log($current, '--------$current--------');

		if (!$current || empty($current)) {
			return array(
				'error' => 'No core data for upgrade.',
			);
		}

		//Set upgrade core version for error handling messages and Comparisions
		if (!empty($current['current'])) {
			$this->process_upgrade_version = $current['current'];
		} else if (!empty($current['version'])){
			$this->process_upgrade_version = $current['version'];
		}

		wptc_log($this->process_upgrade_version,'-----------$this->process_upgrade_version----------------');

		if (!is_server_writable_wptc()) {
			return $this->get_server_not_writable_arr();
		}

		ob_start();

		if (file_exists(ABSPATH.'/wp-admin/includes/update.php')) {
			include_once ABSPATH.'/wp-admin/includes/update.php';
		}

		$current_update = false;
		ob_end_flush();
		ob_end_clean();

		$is_multisite = is_multisite();
		$is_function_exist = function_exists('get_site_option');
		if( $is_multisite && $is_function_exist ){
			$core = get_site_option( '_site_transient_update_core' );
		} else {
			$core = wptc_mmb_get_transient('update_core');
		}

		wptc_log($core, '--------$core--------');

		if (isset($core->updates) && !empty($core->updates)) {
			$updates = $core->updates[0];
			$updated = $core->updates[0];
			if (!isset($updated->response) || $updated->response == 'latest') {
				return array(
					'upgraded' => ' updated',
				);
			}

			if ($updated->response == "development" && $current['response'] == "upgrade") {
				return array(
					'error' => '<font color="#900">Unexpected error. Please upgrade manually.</font>',
				);
			} else {
				if ($updated->response == $current['response'] || ($updated->response == "upgrade" && $current['response'] == "development")) {
					if ($updated->locale != $current['locale']) {
						foreach ($updates as $update) {
							if ($update->locale == $current['locale']) {
								$current_update = $update;
								break;
							}
						}
						if ($current_update == false) {
							return array(
								'error' => ' Localization mismatch. Try again.',
							);
						}
					} else {
						$current_update = $updated;
					}
				} else {
					return array(
						'error' => ' Transient mismatch. Try again.',
					);
				}
			}
		} else {
			return array(
				'error' => ' Refresh transient failed. Try again.',
			);
		}
		if ($current_update != false) {
			global $wp_filesystem, $wp_version;

			if (version_compare($wp_version, '3.1.9', '>')) {
				if (!class_exists('Core_Upgrader')) {
					include_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
				}

				/** @handled class */
				// $upgrader_skin = new WPTC_Updater_TraceableUpdaterSkin();

				$upgrader_skin  = new WP_Upgrader_Skin();
				$core 	        = new Core_Upgrader($upgrader_skin);
				$result         = $core->upgrade($current_update);
				wptc_log($result,'-----------$result----------------');

				$errors = $this->parse_error_from_core_upgrade($result);

				wptc_log($errors,'-----------core upgrade $errors----------------');

				wptc_mmb_maintenance_mode(false);

				if (!empty($errors)) {
					return array(
						'error' => $errors ,
					);
				} else {
					return array(
						'upgraded' => ' updated',
					);
				}
			} else {
				if (!class_exists('WP_Upgrader')) {
					include_once ABSPATH.'wp-admin/includes/update.php';
					if (function_exists('wp_update_core')) {
						$result = wp_update_core($current_update);
						if (is_wp_error($result)) {
							return array(
								'error' => wptc_mmb_get_error($result),
							);
						} else {
							return array(
								'upgraded' => ' updated',
							);
						}
					}
				}

				if (class_exists('WP_Upgrader')) {
					/** @handled class */
					$upgrader_skin              = new WP_Upgrader_Skin();
					$upgrader_skin->done_header = true;

					/** @handled class */
					$upgrader = new WP_Upgrader($upgrader_skin);

					// Is an update available?
					if (!isset($current_update->response) || $current_update->response == 'latest') {
						return array(
							'upgraded' => ' updated',
						);
					}

					$res = $upgrader->fs_connect(
						array(
							ABSPATH,
							WP_CONTENT_DIR,
						)
					);
					if (is_wp_error($res)) {
						return array(
							'error' => wptc_mmb_get_error($res),
						);
					}

					$wp_dir = trailingslashit($wp_filesystem->abspath());

					$core_package = false;
					if (isset($current_update->package) && !empty($current_update->package)) {
						$core_package = $current_update->package;
					} elseif (isset($current_update->packages->full) && !empty($current_update->packages->full)) {
						$core_package = $current_update->packages->full;
					}

					$download = $upgrader->download_package($core_package);
					if (is_wp_error($download)) {
						return array(
							'error' => wptc_mmb_get_error($download),
						);
					}

					$working_dir = $upgrader->unpack_package($download);
					if (is_wp_error($working_dir)) {
						return array(
							'error' => wptc_mmb_get_error($working_dir),
						);
					}

					if (!$wp_filesystem->copy($working_dir.'/wordpress/wp-admin/includes/update-core.php', $wp_dir.'wp-admin/includes/update-core.php', true)) {
						$wp_filesystem->delete($working_dir, true);

						return array(
							'error' => 'Unable to move update files.',
						);
					}

					$wp_filesystem->chmod($wp_dir.'wp-admin/includes/update-core.php', FS_CHMOD_FILE);

					require ABSPATH.'wp-admin/includes/update-core.php';

					$update_core = update_core($working_dir, $wp_dir);
					ob_end_clean();

					$this->wptc_mmb_maintenance_mode(false);
					if (is_wp_error($update_core)) {
						return array(
							'error' => wptc_mmb_get_error($update_core),
						);
					}
					ob_end_flush();

					return array(
						'upgraded' => 'updated',
					);
				} else {
					return array(
						'error' => 'failed',
					);
				}
			}
		} else {
			return array(
				'error' => 'failed',
			);
		}
	}

	private function parse_error_from_core_upgrade($result){
		if ( !is_wp_error($result) || !$result->get_error_code() ) {
			return false;
		}

		$errors = '';

		foreach ( $result->get_error_messages() as $message ) {
			if ( $result->get_error_data() && is_string( $result->get_error_data() ) ){
				$errors = empty($errors) ? $message . ' ' . esc_html( strip_tags( $result->get_error_data() ) ) : $error + $message . ' ' . esc_html( strip_tags( $result->get_error_data() ) ) ;
			} else {
				$errors = empty($errors) ? $message : $error + $message ;
			}
		}

		return $errors;
	}

	public function upgrade_translation($data = false) {

		$this->process_upgrade_version = 'New version';

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once ABSPATH . 'wp-admin/includes/template.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/theme.php';

		if (!function_exists('wp_version_check') || !function_exists('get_core_checksums')) {
			include_once ABSPATH . '/wp-admin/includes/update.php';
		}

		if (!class_exists('Language_Pack_Upgrader')) {
			return array(
				'error' => 'WordPress update required first',
			);
		}

		if (!is_server_writable_wptc()) {
			return $this->get_server_not_writable_arr();
		}

		/** @handled class */
		$upgrader_skin = new WPTC_Updater_TraceableUpdaterSkin();
		$upgrader = new Language_Pack_Upgrader($upgrader_skin);
		$result = $upgrader->bulk_upgrade();
		wptc_log($upgrader_skin->get_upgrade_messages(), '--------$upgrader_skin Translations--------');
		if (empty($result)) {
			return array(
				'error' => 'Upgrade failed.',
			);
		}

		$return = 1;
		foreach ($result as $translate_tmp => $translate_info) {
			if (is_wp_error($translate_info) || empty($translate_info)) {
				$return = array('error' => $this->parse_upgrade_response($upgrader_skin->get_upgrade_messages()));
				break;
			}
		}

		return array('upgraded' => $return);
	}

	public function parse_upgrade_response($response = null , $parse_error = true){
		// wptc_log($response, '--------$response--------');
		$error_message = '';
		foreach ($response as $key => $message) {

			if(in_array($message['key'], $this->upgrade_wp_flow_keys) !== false){
				// wptc_log($message['key'], '--------WP FLOW SKIPPED--------');
				continue;
			}

			if(in_array($message['key'], $this->upgrade_success_keys) !== false){
				// wptc_log($message['key'], '--------SUCCESS KEYS SKIPPED--------');
				break;
			}

			if(in_array($message['key'], $this->upgrade_error_keys) !== false){
				// wptc_log($message['key'], '--------FOUND ERROR KEY--------');
				// wptc_log($message['message'], '--------ERROR MESSAGE--------');
				$error_message = $message['message'];
				break;
			}

			// wptc_log($message['key'], '--------FOUND ERROR BECAUSE ITS NEW--------');
			// wptc_log($message['message'], '--------ERROR MESSAGE--------');
			$error_message = $message['message'];
			break;
		}

		if ($parse_error) {
			return !empty($error_message) ? $error_message : 'Could not find error from response : ' . serialize($response);
		}
	}

	public function handle_iwp_update_request($request){

		//Support for iwp single updates - "bulkActionParams"
		$upgrade_details = !empty($request['bulkActionParams']) ? $request['bulkActionParams'] : $request;

		wptc_log($upgrade_details, '--------$upgrade_details handle_iwp_update_request--------');

		if($this->config->get_option('backup_before_update_setting') !== 'always'){
			wptc_log(array(), '--------USER not enabled BBU as always--------');
			return false;
		}

		if(!WPTC_Base_Factory::get('Wptc_App_Functions')->is_user_purchased_this_class('Wptc_Backup_Before_Update')){
			wptc_log(array(), '--------User is not purchased BBU--------');
			return false;
		}

		if (is_any_other_wptc_process_going_on()) {
			return array(
				'error' => 'WP Time Capsule is running a task in your site. Please try again once its finished.',
				'error_code' => 'WPTC_BUSY_WITH_ANOTHER_TASK'
				);
		}

		if (is_any_ongoing_wptc_backup_process()){
			return array(
				'error' => 'WP Time Capsule is backing up your site. Please try again once its finished.',
				'error_code' => 'WPTC_BUSY_WITH_ANOTHER_BACKUP'
				);
		}

		if (is_any_ongoing_wptc_restore_process()){
			return array(
				'error' => 'WP Time Capsule is restoring your site. Please try again once its finished.',
				'error_code' => 'WPTC_BUSY_WITH_RESTORE'
				);
		}

		$upgrade_plugins = array();

		if (!empty($upgrade_details['upgrade_plugins'])) {
			foreach ($upgrade_details['upgrade_plugins'] as $key => $plugin) {

				wptc_log($plugin, '--------$plugin--------');
				
				if (!isset($plugin['file'])) {
					$upgrade_plugins[$plugin['slug']] = $plugin['new_version'];
				} else {
					$upgrade_plugins[$plugin['file']] = $plugin['new_version'];
				}
			}
		}

		$upgrade_themes = array();

		if (!empty($upgrade_details['upgrade_themes'])) {
			foreach ($upgrade_details['upgrade_themes'] as $key => $theme) {
				$upgrade_themes[] = $theme['theme_tmp'];
			}
		}

		$wp_upgrade = array();

		if (!empty($upgrade_details['wp_upgrade'])) {
			$wp_upgrade = $upgrade_details['wp_upgrade'];
		}

		$upgrade_translations = !empty($upgrade_details['upgrade_translations']) ? true: false;

		if (!empty($upgrade_plugins)) {
			$final_upgrade_details['upgrade_plugins']['update_items'] = $upgrade_plugins;
			$final_upgrade_details['upgrade_plugins']['updates_type'] = 'plugin';
			$final_upgrade_details['upgrade_plugins']['is_auto_update'] = '0';
		}

		if (!empty($upgrade_themes)) {
			$final_upgrade_details['upgrade_themes']['update_items'] = $upgrade_themes;
			$final_upgrade_details['upgrade_themes']['updates_type'] = 'theme';
			$final_upgrade_details['upgrade_themes']['is_auto_update'] = '0';

		}

		if (!empty($wp_upgrade)) {
			$final_upgrade_details['wp_upgrade']['update_items'] = $wp_upgrade;
			$final_upgrade_details['wp_upgrade']['updates_type'] = 'core';
			$final_upgrade_details['wp_upgrade']['is_auto_update'] = '0';
		}

		if (!empty($upgrade_translations)) {
			$final_upgrade_details['upgrade_translations']['update_items'] = $upgrade_translations;
			$final_upgrade_details['upgrade_translations']['updates_type'] = 'translation';
			$final_upgrade_details['upgrade_translations']['is_auto_update'] = '0';
		}

		$this->bulk_update_request($final_upgrade_details);
		$this->config->set_option('is_bulk_update_request', true);
		$this->config->set_option('single_upgrade_details', false);

		// wptc_login_as_admin();

		start_fresh_backup_tc_callback_wptc('manual', null, true, false, $is_iwp = true);

		/*Support for iwp single updates - "bulkActionParams", sending response so iwp client will update
			WPTC_TAKES_CARE_OF_IT_LATEST - Latest response
			WPTC_TAKES_CARE_OF_IT 		 - Old response
		*/
		return !empty($request['bulkActionParams']) ? array('success' => 'The update will now be handled by WP Time Capsule. Check the WPTC page for its status.', 'success_code' => 'WPTC_TAKES_CARE_OF_IT_LATEST') : 'WPTC_TAKES_CARE_OF_IT';
	}

	public function do_bulk_upgrade_request(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");


		//Check single updates availabe if yes then do it first
		$singel_upgrades = $this->config->get_option('single_upgrade_details');
		wptc_log($singel_upgrades, '--------$singel_upgrades--------');
		if ($singel_upgrades) {
			return $this->do_single_upgrades();
		}

		//Check bulk updates available
		$raw_bulk_update_request = $this->config->get_option('bulk_update_request');

		if (empty($raw_bulk_update_request)) {
			wptc_log(array(), '--------bulk_update_request empty--------');
			$this->config->set_option('is_bulk_update_request', false);
			$this->config->set_option('bulk_update_request', false);
			$this->is_bulk_update_request = true;
			return $this->parse_bulk_upgrade_response();
		}

		$bulk_update_request = unserialize($raw_bulk_update_request);

		wptc_log($bulk_update_request, '--------$bulk_update_request--------');

		if (!empty($bulk_update_request['upgrade_plugins'])) {
			return $this->split_bulk_into_single_upgrades($key = 'upgrade_plugins', $bulk_update_request);
		}

		if (!empty($bulk_update_request['upgrade_themes'])) {
			return $this->split_bulk_into_single_upgrades($key = 'upgrade_themes', $bulk_update_request);
		}

		if (!empty($bulk_update_request['wp_upgrade'])) {
			return $this->split_bulk_into_single_upgrades($key = 'wp_upgrade', $bulk_update_request);
		}

		if (!empty($bulk_update_request['upgrade_translations'])) {
			return $this->split_bulk_into_single_upgrades($key = 'upgrade_translations', $bulk_update_request);
		}

		wptc_log(array(),'-----------No updates found so reset----------------');
		$this->config->flush();
	}

	private function split_bulk_into_single_upgrades($key, $bulk_update_request){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		//Save single upgrades
		$this->config->set_option('single_upgrade_details', serialize($bulk_update_request[$key]));

		//Remove updates from bulk once split to single
		unset($bulk_update_request[$key]);

		//Save remaining bulk updates
		$this->bulk_update_request($bulk_update_request);

		//Run single upgrades
		return $this->do_single_upgrades();
	}

	public function bulk_update_request($bulk_update_request){
		wptc_log($bulk_update_request, '--------$bulk_update_request--------');
		if (empty($bulk_update_request)) {
			return $this->config->set_option('bulk_update_request', false);
		}

		$this->config->set_option('bulk_update_request', serialize($bulk_update_request));
	}

	private function ithemes_updater_compatiblity() {
		// Check for the iThemes updater class
		if ( empty($GLOBALS['ithemes_updater_path']) || !file_exists($GLOBALS['ithemes_updater_path'] . '/settings.php') ) {
			return;
		}

		// Include iThemes updater
		require_once $GLOBALS['ithemes_updater_path'].'/settings.php';

		// Check if the updater is instantiated
		if (empty($GLOBALS['ithemes-updater-settings'])) {
			return;
		}

		// Update the download link
		$GLOBALS['ithemes-updater-settings']->flush('forced');
	}
}
