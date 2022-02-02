<?php

class Wptc_Restore_To_Staging extends WPTC_Privileges{
	protected 	$config,
				$app_functions;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Restore_To_Staging_Config');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
	}

	public function init(){
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on(){
		return true;
	}

	public function init_restore($data){
		unset($data['security']);
		$this->config->set_option('R2S_replace_links', false);
		$this->config->set_option('restore_deep_links_completed', false);
		$this->config->set_option('is_restore_to_staging', true);
		$this->config->set_option('restore_to_staging_details', serialize( $data ));

		if ($data['is_latest_restore_point']) {
			wptc_log(array(),'-----------is latest restore point----------------');
			$this->config->set_option('is_latest_restore_point', true);
		} else {
			$this->config->set_option('is_latest_restore_point', false);
		}

		wptc_die_with_json_encode( array('status' => 'success') );
	}

	public function is_restore_to_staging(){
		return $this->config->get_option('is_restore_to_staging');
	}

	public function get_request(){

		$raw_data = $this->config->get_option('restore_to_staging_details');

		wptc_log($raw_data,'-----------$raw_data----------------');

		if (empty($raw_data)) {
			return false;
		}

		return unserialize($raw_data);
	}
}