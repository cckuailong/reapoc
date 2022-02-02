<?php

class Wptc_Rollback_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {
	protected $rollback;
	protected $config;
	protected $app_functions;

	const JS_URL = '/Pro/Rollback/wptc-rollback.js';

	public function __construct() {
		$this->rollback = WPTC_Base_Factory::get('Wptc_Rollback');
		$this->config = WPTC_Pro_Factory::get('Wptc_Rollback_Config');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
	}

	public function enque_js_files() {
		if(apply_filters('is_whitelabling_active_wptc', false) && !apply_filters('is_whitelabling_override_wptc', false)){
			return ;
		}

		wp_enqueue_script('wptc-rollback-update', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . self::JS_URL, array(), WPTC_VERSION);
	}

	public function get_previous_versions() {
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (empty($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'error', 'msg' => 'post data is missing' ) );
		}

		$this->rollback->get_previous_versions($_POST['data']);
	}

	public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {

		if(apply_filters('is_whitelabling_active_wptc', false) && !apply_filters('is_whitelabling_override_wptc', false)){
			return $actions;
		}

		// Multisite check.
		if ( is_multisite() && ( ! is_network_admin() && ! is_main_site() ) ) {
			return $actions;
		}

		// Must have version.
		if ( ! isset( $plugin_data['Version'] ) ) {
			return $actions;
		}

		// Final Output
		$actions['wptc-rollback'] = '<a class="wptc-rollback" type="plugin" slug="' . $plugin_data['TextDomain']. '" current_version="' . $plugin_data['Version'] . '" name="' . $plugin_data['Name'] . '">' . __( 'WPTC Rollback', 'wp-time-capsule' ) . '</a>';

		return $actions;

	}
}