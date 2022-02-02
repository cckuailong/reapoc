<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WPTC_Update_In_Staging{

	private $config;
	private	$staging_common;
	private	$logger;
	private	$staging_id;
	private	$app_functions;

	public function __construct(){
		$this->config = WPTC_Factory::get('config');
		$this->staging_common = new WPTC_Stage_Common();
		$this->logger = WPTC_Factory::get('logger');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->init_staging_id();
	}

	private function init_staging_id(){
		$this->staging_id = $this->staging_common->init_staging_id();
	}

	private function decode_response($response){
		wptc_remove_response_junk($response);
		wptc_log($response,'--------------$response after junk-------------');
		if (empty($response)) {
			return false;
		}
		$decode_data = base64_decode($response);
		if (empty($decode_data)) {
			return false;
		}
		$unserialized_response = @unserialize($decode_data);
		if (empty($unserialized_response)) {
			return false;
		}
		return $unserialized_response;

	}

	public function save_stage_n_update($data) {
		$update_items = $data['update_items'];
		$type = $data['type'];
		if ($type == 'plugin') {
			$upgrade_details = purify_plugin_update_data_wptc($update_items);
		} else if ($type == 'theme') {
			$upgrade_details = purify_theme_update_data_wptc($update_items);
		} else if ($type == 'core') {
			if (!$is_auto_update) {
				$upgrade_details = purify_core_update_data_wptc($update_items);
			} else {
				$upgrade_details = $update_items;
			}
		} else if ($type == 'translation') {
			$upgrade_details = purify_translation_update_data_wptc($update_items);
		}

		$this->update_formated_stage_n_update_details($type, $upgrade_details);
	}

	private function update_formated_stage_n_update_details($update_ptc_type, $upgrade_details) {
		wptc_log($upgrade_details, '---------upgrade_details-------------');

		$upgrade_details_data['update_items'] = $upgrade_details;
		$upgrade_details_data['updates_type'] = $update_ptc_type;

		$this->config->set_option('stage_n_update_details', serialize($upgrade_details_data));
		$this->app_functions->die_with_json_encode(array('status' => 'success'));
	}

	public function get_update_in_staging(){
		$raw_upgrade_details = $this->config->get_option('stage_n_update_details');
		$this->config->set_option('stage_n_update_details', false);
		wptc_log($raw_upgrade_details, '---------$raw_upgrade_details------------');
		if (empty($raw_upgrade_details)) {
			wptc_log(array(), '---------No data update_in_staging------------');
			$this->logger->log("No update requests for staging", 'staging', $this->staging_id);
			return false;
		}
		$upgrade_details = unserialize($raw_upgrade_details);
		wptc_log($upgrade_details, '---------$upgrade_details------------');
		if (empty($upgrade_details) || !is_array($upgrade_details)) {
			$this->logger->log("Update request data is corrupted so skipped updates in the staging", 'staging', $this->staging_id);
			wptc_log(array(), '---------corrupted data update_in_staging-----------');
			return false;
		}

		$type_of_update = $upgrade_details['updates_type'];
		$update_items = $upgrade_details['update_items'];
		if (empty($type_of_update) || empty($update_items)) {
			if($type_of_update != 'translation'){
				$this->logger->log("Update request data is corrupted so skipped updates in the staging", 'staging', $this->staging_id);
				wptc_log(array(), '---------corrupted data update_in_staging-----------');
				return false;
			}
		}
		return array('type' => $type_of_update, 'update_items' => $update_items);
	}

	public function update_in_staging($destination_bridge_url = false){

		$update_details = $this->get_update_in_staging();

		if($update_details === false){
			$this->config->set_option('staging_progress_status', 'staging_completed');
			return ;
		}

		if(is_wptc_timeout_cut(false, 10)){
			send_response_wptc('replace_links_over', array());
		}

		$request_params = array();
		$request_params['action'] = 'update_in_staging';
		$request_params['type'] = $type_of_update =$update_details['type'];
		$request_params['update_items'] = $update_details['update_items'];
		wptc_log($request_params, '---------$request_params------------');

		if (!$destination_bridge_url) {
			$destination_bridge_url = WPTC_Pro_Factory::get('Wptc_Staging')->same_server_staging_bridge_url();
		}

		wptc_log($destination_bridge_url, '---------$destination_bridge_url------------');

		$raw_response = $this->config->doCall($destination_bridge_url, $request_params);

		wptc_log($raw_response, '---------update_in_staging response------------');
		$response = $this->decode_response($raw_response);

		wptc_log($response, '---------$response------------');
		$this->config->set_option('staging_progress_status', 'staging_completed');

		if (empty($response)) {
			return $this->logger->log("Update failed in the staging.", 'staging', $this->staging_id);

		} else if(isset($response['error'])){
			return $this->logger->log("Update failed - ". $response['error'], 'staging', $this->staging_id);
		}

		if ($type_of_update == 'plugin') {
			$this->process_plugin_update_response($response);
		} else if($type_of_update == 'theme'){
			$this->process_theme_update_response($response);
		} else if ($type_of_update == 'core') {
			$this->process_core_update_response($response);
		} else if ($type_of_update == 'translation') {
			$this->process_translation_update_response($response);
		}
	}

	private function process_plugin_update_response($response){

		if (!isset($response['upgraded'])) {
			return $this->logger->log("Updating plugin in the staging failed - response corrupted", 'staging', $this->staging_id);
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once WPTC_ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins_data = get_plugins();
		$plugins_count = $plugins_success = $plugins_failure = 0;

		foreach ($response['upgraded'] as $key => $value) {
			$plugins_count++;
			if ($value === 1) {
				$plugins_success++;
				$this->logger->log("Plugin ".$plugins_data[$key]['Name']. " updated successfully in staging site.", 'staging', $this->staging_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $plugins_data[$key]['Name'].' updated successfully in the staging site. :)')));
			} else if($value['error']){
				$plugins_failure++;
				$this->logger->log("Plugin ".$plugins_data[$key]['Name'] . ' update failed - '.$value['error'], 'staging', $this->staging_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $plugins_data[$key]['Name'].' update failed in the staging site.')));
			} else if($value['error_code']){
				$this->logger->log("Plugin ".$plugins_data[$key]['Name'] . ' update failed - '.$value['error_code'], 'staging', $backup_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $plugins_data[$key]['Name'].' update failed in the staging site.')));
				$plugins_failure++;
			}
		}

		if ($plugins_count <= 1) {
			return false;
		}

		if ($plugins_success === $plugins_count) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $plugins_count.' plugins updated successfully in the staging site :)')));
		} else if ($plugins_failure === $plugins_count) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $plugins_count.' plugin updates failed in the staging site.')));
		} else {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'warning', 'note' => $plugins_success.' plugin updated successfully and '.$plugins_failure.' plugin updates failed in the staging site.')));
		}
	}

	private function process_theme_update_response($response){

		if (!isset($response['upgraded'])) {
			return $this->logger->log("Updating theme in the staging failed - response corrupted", 'staging', $this->staging_id);
		}

		$themes_count = $themes_success = $themes_failure = 0;

		foreach ($response['upgraded'] as $key => $value) {
			$theme_info = wp_get_theme($key);
			if (!empty($theme_info)) {
				$theme_name = $theme_info->get( 'Name' );
			}
			$themes_count++;
			if ($value === 1) {
				$themes_success++;
				$this->logger->log("Theme " . $theme_name ." updated successfully in staging site.", 'staging', $this->staging_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $theme_name .' updated successfully in the staging site :)')));
			} else if($value['error']){
				$themes_failure++;
				$this->logger->log("Theme " . $theme_name . ' update failed - '.$value['error'], 'staging', $this->staging_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $theme_name .' update failed in the staging site.')));
			} else if($value['error_code']){
				$this->logger->log("Theme " . $theme_name . ' update failed - '.$value['error_code'], 'staging', $backup_id);
				$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $theme_name .' update failed in the staging site.')));
				$themes_failure++;
			}
		}

		if ($themes_count <= 1) {
			return false;
		}

		if ($themes_success === $themes_count) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => $themes_count.' themes updated successfully in the staging site :)')));
		} else if ($themes_failure === $themes_count) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => $themes_count.' theme updates failed in the staging site.')));
		} else {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'warning', 'note' => $themes_success.' theme updated successfully and '.$themes_failure.' theme updates failed in the staging site.')));
		}
	}

	private function process_core_update_response($response){

		if (!isset($response['upgraded'])) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => 'Latest version of WordPress update failed in the staging site.')));
			return $this->logger->log("Updating wordpress in the staging failed - response corrupted", 'staging', $this->staging_id);
		}

		$this->logger->log("Wordpress updated successfully", 'staging', $this->staging_id);
		$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => 'Latest version of WordPress updated successfully in the staging site :)')));
	}

	private function process_translation_update_response($response){
		if (!isset($response['upgraded'])) {
			$this->config->set_option('bbu_note_view', serialize(array('type' => 'error', 'note' => 'Translation updates failed in the staging site')));
			return $this->logger->log("Updating translation in the staging failed - response corrupted", 'staging', $this->staging_id);
		}

		$this->logger->log("Translations updated successfully", 'staging', $this->staging_id);
		$this->config->set_option('bbu_note_view', serialize(array('type' => 'success', 'note' => 'Translations updated successfully in the staging site :)')));
	}
}