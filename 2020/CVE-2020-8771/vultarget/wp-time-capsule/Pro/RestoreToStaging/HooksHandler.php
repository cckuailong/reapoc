<?php

class Wptc_Restore_To_Staging_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {
	protected $vulns;
	protected $config;
	protected $app_functions;

	const JS_DIR = '/Pro/RestoreToStaging/';

	public function __construct() {
		$this->restore_to_staging = WPTC_Base_Factory::get('Wptc_Restore_To_Staging');
		$this->config = WPTC_Pro_Factory::get('Wptc_Restore_To_Staging_Config');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
	}

	public function enque_js_files() {
		wp_enqueue_script('wptc-restore-to-staging', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . self::JS_DIR . 'script.js', array(), WPTC_VERSION);
		if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'page=wp-time-capsule-staging-options') !== false ) {
			wp_enqueue_script('wptc-monitor-js', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Views/wptc-monitor.js', array(), WPTC_VERSION);
			wptc_init_monitor_js_keys();
		}
	}

	public function get_restore_to_staging_button() {
		if (!is_main_site()) {
			return ;
		}

		if( is_wptc_filter_registered('hide_this_option_wl_wptc') 
			&& apply_filters('hide_this_option_wl_wptc', 'restore_to_staging')){
			return '';
		}

		$is_staging_taken = apply_filters('is_staging_taken_wptc', '');

		if ($is_staging_taken) {
			return '<a class="btn_wptc restore_to_staging_wptc " >RESTORE TO STAGING</a>';
		}

		return '<a class="btn_wptc restore_to_staging_wptc disabled" title="Set up a staging in WP Time Capsule -> Staging"> RESTORE TO STAGING</a>';
	}

	public function init_restore() {

		$this->app_functions->verify_ajax_requests();

		if (empty($_POST)) {
			wptc_die_with_json_encode( array('status' => 'error' , 'msg' => 'Backup id is empty !') );
		}

		return $this->restore_to_staging->init_restore($_POST);
	}

	public function is_restore_to_staging(){
		return $this->restore_to_staging->is_restore_to_staging();
	}

	public function get_request(){
		return $this->restore_to_staging->get_request();
	}

}