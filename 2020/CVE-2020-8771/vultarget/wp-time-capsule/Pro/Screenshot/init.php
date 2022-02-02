<?php

class Wptc_Screenshot extends WPTC_Privileges {
	protected $config;
	protected $cron_server_curl;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Screenshot_Config');
		$this->cron_server_curl = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on(){
		return true;
	}

	public function take_screenshot($type){
		if (!$this->is_allowed_to_screenshot($type)) {
			wptc_log($type,'-----------Returned--------take_screenshot--------');
			
			return ;
		}

		$post_arr = array(
				'app_id' => $this->config->get_option('appID'),
				'stage' => $type,
		);

		wptc_log($type,'-----------SENT---------take_screenshot-------');

		$this->cron_server_curl->do_call('take-screenshot', $post_arr);
	}

	private function is_allowed_to_screenshot($type){

		if (!$this->config->get_option('do_screenshot_compare')) {
			wptc_log(array(),'-----------Screenshot disabled----------------');
			
			return false;
		}

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ( $type === 'Before' ) {

			$allow = false;


			if( $this->config->get_option('single_upgrade_details') ){
				wptc_log(array(),'-----------Manual update is_allowed_to_screenshot----------------');
				$allow = true;
			}

			if( $this->config->get_option('bulk_update_request') ){
				wptc_log(array(),'-----------Bulk update is_allowed_to_screenshot----------------');
				$allow = true;
			}

			if ($this->config->get_option('is_before_update_screenshot_taken')) {
				wptc_log(array(),'-----------is_before_update_screenshot_taken already set----------------');
				$allow = false;
			}

			if ($this->config->get_option('screenshot_group_updates_start')) {
				wptc_log(array(),'-----------screenshot_group_updates_start already running----------------');
				$allow = false;
			}

			if ($allow) {
				$this->config->set_option('is_before_update_screenshot_taken', true);
				$this->config->set_option('screenshot_group_updates_start', true);
			}

			return $allow;
		} else if ( $type === 'After' ) {

			$allow = false;

			if( !$this->config->get_option('single_upgrade_details') 
				&& !$this->config->get_option('bulk_update_request') ){
				wptc_log(array(),'-----------All updates are Done is_allowed_to_screenshot----------------');
				$allow = true;
			}

			if ($this->config->get_option('is_after_update_screenshot_taken')) {
				wptc_log(array(),'-----------is_after_update_screenshot_taken already set----------------');
				$allow = false;
			}

			if (!$this->config->get_option('is_before_update_screenshot_taken')) {
				wptc_log(array(),'-----------is_before_update_screenshot_taken not set so do not send after request----------------');
				$allow = false;
			}

			if($allow){
				$this->config->set_option('is_after_update_screenshot_taken', true);
			}

			return $allow;
		}

		return false;
	}

	public function flush(){
		$this->config->flush();
	}
}