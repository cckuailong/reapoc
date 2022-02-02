<?php

class Wptc_Screenshot_Loader {

	private $config;

	public function __construct(){
		$this->init();
	}

	public function init(){
		$this->init_objects();

		if(!$this->is_required() || !$this->is_user_eligible() ){
			return ;
		}

		$this->init_hooks();
	}

	private function is_required(){
		if ( empty($_GET['vs_edit_wptc']) && ( empty($_POST['action'] ) || $_POST['action'] !== 'screenshot_save_wptc') ) {
			return false;
		}

		$vs_edit_wptc = base64_decode( urldecode( $_GET['vs_edit_wptc'] ) );

		wptc_log($vs_edit_wptc, '--------$vs_edit_wptc--------');

		if (empty($vs_edit_wptc)){
			return false;
		}

		if ( md5( $this->config->get_option( 'uuid' ) ) != $vs_edit_wptc ) {
			return false;
		}

		return true;
	}

	private function is_user_eligible(){
		$privileges_arr = WPTC_Factory::get('config')->get_option('privileges_wptc');
		$privileges_arr = json_decode($privileges_arr);

		wptc_log($privileges_arr, '--------$privileges_arr--------');

		if(empty($privileges_arr)){
			return false;
		}

		$features_arr = array();
		foreach ($privileges_arr as $key => $value) {
			$features_arr = $value;
		}

		return in_array('Wptc_Screenshot', $features_arr);
	}

	private function init_hooks(){
		add_action( 'wp_enqueue_scripts', array( $this, 'screenshots_scripts') );
		add_action( 'wp_ajax_screenshot_save_wptc', array( $this, 'send_screenshot_request') );
		add_action( 'wp_ajax_nopriv_screenshot_save_wptc', array( $this, 'send_screenshot_request') );
		add_action( 'wp_ajax_att_settings_save_wptc', array( $this, 'att_settings_save_wptc') );
		add_action( 'wp_ajax_nopriv_att_settings_save_wptc', array( $this, 'att_settings_save_wptc') );
		add_action( 'wp_ajax_prefill_scrn_shot_settings_wptc', array( $this, 'prefill_scrn_shot_settings_wptc') );
		add_action( 'wp_ajax_nopriv_prefill_scrn_shot_settings_wptc', array( $this, 'prefill_scrn_shot_settings_wptc') );
	}

	private function init_objects(){
		$this->config = WPTC_Factory::get('config');
	}

	public function screenshots_scripts() {
		wp_enqueue_style('wptc-screenshot-inject', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Pro/Screenshot/screenshot-inject.css', array(), WPTC_VERSION);
		wp_enqueue_script('wptc-screenshot-inject', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Pro/Screenshot/screenshot-inject.js', array('jquery'), WPTC_VERSION);

		wp_localize_script(
			'wptc-screenshot-inject',
			'wptc_screenshots_actions',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce( 'wptc_nonce' ),
			)
		);
	}

	public function send_screenshot_request(){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests($admin_check = false);

		$main_account_email = $this->config->get_option('main_account_email');
		$main_account_pwd   = $this->config->get_option('main_account_pwd');
		$wptc_token         = $this->config->get_option('wptc_token');
		$site_url           = $this->config->get_option('site_url_wptc');
		$params 			= $_POST['data'];

		$params['email']      = $main_account_email;
		$params['pwd']        = $main_account_pwd;
		$params['site_url']   = $site_url;
		$params['version']    = WPTC_VERSION;
		$params['sub_action'] = 'screenshot_save_wptc';

		wptc_log($params, "--------params going--screenshot_save_wptc------");

		$rawResponseData = $this->config->doCall( WPTC_USER_SERVICE_URL, $params, 20, array('normalPost' => 1), $wptc_token );

		wptc_log($rawResponseData, "--------rawResponseData--------");

		if (empty($rawResponseData) || !is_string($rawResponseData)) {
			return false;
		}

		$result_decode = json_decode(base64_decode($rawResponseData));

		wptc_log($result_decode, "-----screenshot_save_wptc---result_decode--------");

		return true;
	}

	public function att_settings_save_wptc(){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests($admin_check = false);

		$main_account_email = $this->config->get_option('main_account_email');
		$main_account_pwd   = $this->config->get_option('main_account_pwd');
		$wptc_token         = $this->config->get_option('wptc_token');
		$site_url           = $this->config->get_option('site_url_wptc');
		$params 			= $_POST['data'];


		$params['email']      = $main_account_email;
		$params['pwd']        = $main_account_pwd;
		$params['site_url']   = $site_url;
		$params['version']    = WPTC_VERSION;
		$params['sub_action'] = 'att_settings_save_wptc';

		wptc_log($params, "--------params going--att_settings_save_wptc------");

		$rawResponseData = $this->config->doCall( WPTC_USER_SERVICE_URL, $params, 20, array('normalPost' => 1), $wptc_token );

		wptc_log($rawResponseData, "--------rawResponseData--------");

		if (empty($rawResponseData) || !is_string($rawResponseData)) {
			return false;
		}

		$result_decode = json_decode(base64_decode($rawResponseData));

		wptc_log($result_decode, "-----att_settings_save_wptc---result_decode--------");

		return true;
	}

	public function prefill_scrn_shot_settings_wptc(){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests($admin_check = false);

		$main_account_email = $this->config->get_option('main_account_email');
		$main_account_pwd   = $this->config->get_option('main_account_pwd');
		$wptc_token         = $this->config->get_option('wptc_token');
		$site_url           = $this->config->get_option('site_url_wptc');
		$params 			= $_POST['data'];

		$params['email']      = $main_account_email;
		$params['pwd']        = $main_account_pwd;
		$params['site_url']   = $site_url;
		$params['version']    = WPTC_VERSION;
		$params['sub_action'] = 'prefill_scrn_shot_settings_wptc';

		wptc_log($params, "--------params going--prefill_scrn_shot_settings_wptc------");

		$rawResponseData = $this->config->doCall( WPTC_USER_SERVICE_URL, $params, 20, array('normalPost' => 1), $wptc_token );

		wptc_log($rawResponseData, "--------rawResponseData--------");

		if (empty($rawResponseData) || !is_string($rawResponseData)) {
			return false;
		}

		echo '<WPTC_START>'.$rawResponseData.'<WPTC_END>';
		// echo $rawResponseData;

		return true;
	}
}