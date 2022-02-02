<?php

class Wptc_Init{

	private $request_data;
	private $bulk_setup_needed_post_data;

	public function __construct(){
		$this->set_plugin_priority();

		//Bring wptc plugin to top to load at first
		$this->parse_request();

		add_action( 'plugins_loaded', array(&$this, 'init_admin_login') );
		add_action( 'setup_theme', array(&$this, 'process') );
	}

	public function init_admin_login($args = ''){

		if (!$this->should_enable_admin_login()) {
			return ;
		}

		wptc_login_as_admin();
	}

	private function should_enable_admin_login(){

		if (empty($this->request_data['type'])) {
			return false;
		}

		if (!$this->get_option('start_upgrade_process') && !$this->get_option('run_vulns_checker') && $this->request_data['type'] !== 'AUTO_UPDATE_CHECK') {
			return false;
		}

		return true;
	}

	public function parse_request(){

		// wptc_log($_GET, "--------_GET----parse_request----");

		$post_data = $this->decode_server_request_wptc();

		// wptc_log($post_data, "--------parse_request---decoded-----");

		if (empty($post_data)) {
			return false;
		}


		$offset = !empty($post_data['extra']['offset']) ? $post_data['extra']['offset'] : 0;
		$limit = !empty($post_data['extra']['limit']) ? $post_data['extra']['limit'] : 100;

		$this->request_data = array(
			'type'   => $post_data['type'],
			'offset' => $offset,
			'limit'  => $limit,
		);

		$this->wptc_plugin_compatibility_fix();

		if ($this->should_enable_admin_login()) {
			wptc_define_admin_constants();
		}
	}

	public function process(){

		if (empty($this->request_data)) {
			return ;
		}

		$this->force_wasabi_if_needed();
		check_wptc_update(); // check updates on every server pings

		$this->perform_request_wptc($this->request_data['type'], $this->request_data['offset'], $this->request_data['limit']);
	}

	private function force_wasabi_if_needed()
	{
		if( !empty($this->bulk_setup_needed_post_data) 
			&& !empty($this->bulk_setup_needed_post_data['type']) 
			&& $this->bulk_setup_needed_post_data['type'] == 'INITIAL_SETUP' 
			&& !empty($this->bulk_setup_needed_post_data['cloud_creds']) 
			&& !empty($this->bulk_setup_needed_post_data['cloud_creds']['default_repo']) 
			&& $this->bulk_setup_needed_post_data['cloud_creds']['default_repo'] == 'wasabi' ){

			wptc_log('', "-----defining---FORCE_WASABI--------");

			if(!defined('FORCE_WASABI')){
				define('FORCE_WASABI', true);
			}
		}
	}

	private function decode_server_request_wptc(){
		global $HTTP_RAW_POST_DATA;
		$HTTP_RAW_POST_DATA_LOCAL = NULL;
		$HTTP_RAW_POST_DATA_LOCAL = file_get_contents('php://input');

		if(empty($HTTP_RAW_POST_DATA_LOCAL)){
			if (isset($HTTP_RAW_POST_DATA)) {
				$HTTP_RAW_POST_DATA_LOCAL = $HTTP_RAW_POST_DATA;
			}
		}

		// wptc_log($HTTP_RAW_POST_DATA_LOCAL, "--------HTTP_RAW_POST_DATA_LOCAL----decode_server_request_wptc----");

		if( !empty($HTTP_RAW_POST_DATA_LOCAL) 
			&& strpos($HTTP_RAW_POST_DATA_LOCAL, 'IWP_JSON_PREFIX') !== false ){

			wptc_log('', "--------IWP_JSON_PREFIX--coming------");
			
			wp_cookie_constants();
			wptc_login_as_admin();
		}

		if( !empty($HTTP_RAW_POST_DATA_LOCAL) 
			&& strpos($HTTP_RAW_POST_DATA_LOCAL, 'mainwpsignature') !== false ){

			wptc_log('', "--------mainwpsignature---found-----");

			// wptc_login_as_admin();
		}

		// if( !empty($HTTP_RAW_POST_DATA_LOCAL) ){
		// 	$post_data = json_decode($HTTP_RAW_POST_DATA_LOCAL, true);

		// 	if( !empty($post_data) && !empty($post_data['BBU_handle_wptc']) ){
		// 		wptc_login_as_admin();
		// 	}
		// }

		ob_start();
		$data = base64_decode($HTTP_RAW_POST_DATA_LOCAL);
		if ($data && $HTTP_RAW_POST_DATA_LOCAL != 'action=progress'){
			$post_data_encoded = $data;
				$post_data = json_decode($post_data_encoded, true);

				$is_validated = false;
				$is_validated = $this->is_valid_wptc_request($post_data);
				if(empty($is_validated)){

					// wptc_log($post_data, "--------is_valid_wptc_request--failed----for--");

					return false;
				} else {
					$this->set_plugin_priority(false);
				}

				if (!isset($post_data['type'])) {
					wptc_log(array(), '-----------type not set-------------');
					return false;
				}
			return $post_data;
		} else {
			$HTTP_RAW_POST_DATA =  $HTTP_RAW_POST_DATA_LOCAL;
		}
		ob_end_clean();
	}

	private function process_bulk_initial_setup() {
		wptc_log($this->bulk_setup_needed_post_data, "-----bulk---INITIAL_SETUP---call from node-----");

		update_bulk_settings_default_flags_wptc($this->bulk_setup_needed_post_data);
		login_request_for_bulk_support_wptc($this->bulk_setup_needed_post_data);

		send_bulk_setup_status_to_server('Setting up cloud');

		WPTC_Base_Factory::get('Wptc_InitialSetup')->add_cloud_details_for_bulk_support( $this->bulk_setup_needed_post_data['cloud_creds'] );
		WPTC_Factory::get('config')->set_option('starting_first_backup', true);
		WPTC_Factory::get('config')->set_option('first_backup_started_atleast_once', true);

		first_backup_basics_wptc();

		sleep(3);

		start_fresh_backup_tc_callback_wptc($type = '', $args = null, $test_connection = false, $ajax_check = false, $is_iwp = false);

		send_bulk_setup_status_to_server('Initial Setup Finished.');
	}

	private function perform_request_wptc($request_type, $offset = 0, $limit = 100){

		set_server_req_wptc();

		if ($request_type === 'SCHEDULE') {
			wptc_check_cloud_in_auth_state();
		}

		set_server_req_wptc(true);

		wptc_load_files();

		global $wptc_ajax_start_time, $wptc_profiling_start;
		$wptc_ajax_start_time = $wptc_profiling_start = time();

		wptc_manual_debug('', 'start_cron_request');

		wptc_log($request_type, '---------$request_type from node------------');

		switch ($request_type) {
			case 'BACKUP':
			case 'B':
			case 'RETRY':
			case 'R':
				$this->fixIWPClientBranding();
				if (apply_filters( 'is_upgrade_request_wptc', false )) {

					wptc_log('', "--------is_upgrade_request_wptc---return-----");

					return ;
				}

				if ( apply_filters( 'is_vulns_checker_request_wptc', false ) ) {

					wptc_log('', "--------is_vulns_checker_request_wptc---return-----");

					return ;
				}

				$this->is_previous_request_completed();

				if (monitor_tcdropbox_backup_wptc() == 'declined') {
					send_response_wptc('backup_not_initialized', $request_type);
				}
				break;
			case 'S':
			case 'SCHEDULE':
			case 'A':
			case 'AUTOBACKUP':
			// case 'WEEKLYBACKUP':
				sub_cycle_event_func_wptc($request_type);
				break;
			break;
			case 'STAGING':
				break;
			case 'SYNC_SERVER':
				wptc_own_cron_status();
				send_response_wptc('connected', $request_type);
				break;
			case 'SYNC_SERVICE':
				WPTC_Factory::get('config')->request_service(
					array(
						'email'           => false,
						'pwd'             => false,
						'return_response' => false,
						'sub_action' 	  => 'sync_all_settings_to_node',
						'login_request'   => true,
					)
				);
				break;
			case 'TEST':
				send_response_wptc('connected', $request_type);
				break;
			case 'BACKUP_RESET':
				stop_fresh_backup_tc_callback_wptc(null, false);
				reset_backup_related_settings_wptc();
				send_response_wptc('success', $request_type);
				break;
			case 'STAGING_RESET':
				do_action('wp_ajax_delete_staging_wptc', time());
				send_response_wptc('success', $request_type);
				break;
			case 'BACKUP_STATUS':
				do_action('send_backups_data_to_server_wptc', time());
				wptc_send_current_backup_response_to_server();
				break;
			case 'BACKUP_START':
				reset_backup_related_settings_wptc();
				start_fresh_backup_tc_callback_wptc('manual', null, true, false);
				send_response_wptc('backup_started', $request_type);
				break;
			case 'LOGOUT':
				process_wptc_logout();
				break;
			case 'INITIAL_SETUP':

				$this->process_bulk_initial_setup();

				break;
			case 'AUTO_UPDATE_CHECK':
				$this->fixIWPClientBranding();
				$response = apply_filters('get_auto_updates_wptc', time());
				if ($response) {

					wptc_log('', "--------get_auto_updates_wptc---return-----");

					return ;
				} else {
					send_response_wptc('AUTO_UPDATE_CHECK_NOT_ENABLED', $request_type);
				}
				break;
			default:
				send_response_wptc('request_not_in_list', $request_type);
				wptc_log(array(), '-----------Request is not in list-------------');
				break;
			}
			wptc_log(array(), '-----------Task end-------------');
			send_response_wptc('notified', $request_type);
	}

	private function is_previous_request_completed(){
		$last_backup_request = WPTC_Factory::get('config')->get_option('last_backup_request');
		if (empty($last_backup_request)) {
			return $this->set_last_request_wptc();
		}

		if ( ( $last_backup_request + WPTC_MAX_REQUEST_PROGRESS_WAIT_TIME ) < time() ) {
			return $this->set_last_request_wptc();
		}

		send_response_wptc('Last request still on progress. Lets wait for ' .  ( ($last_backup_request + WPTC_MAX_REQUEST_PROGRESS_WAIT_TIME) - time() ) . ' seconds', 'BACKUP', false, false, false);
	}

	private function set_last_request_wptc(){
		WPTC_Factory::get('config')->set_option('last_backup_request', time());
	}

	private function get_option($key){
		global $wpdb;
		return $wpdb->get_var("SELECT value FROM {$wpdb->base_prefix}wptc_options WHERE name = '$key'");
	}

	private function is_bypass_bulk_initial_setup_needed($post_data){
		if( $post_data['type'] == 'INITIAL_SETUP' ){

			$app_id = $this->get_option('appID');
			if(empty($app_id)){

				return true;
			} else {
				send_bulk_setup_status_to_server('Site is already added, cannot do initial setup.');
				wptc_json_format_bulk_exit_response('Already site added');
			}
		}

		return false;
	}

	private function is_valid_wptc_request($post_data){
		if (empty($post_data['authorization'])) {
			return false;
		}

		if (empty($post_data['authorization'])) {
			return false;
		}

		if (!isset($post_data['source']) && $post_data['source'] != 'WPTC') {
			return false;
		}

		if($this->is_bypass_bulk_initial_setup_needed($post_data)){
			$pwd_hash = wptc_decode_auth_token($post_data['authorization'], 'pwd_hash');
			if(empty($pwd_hash)){
				
				return false;	
			}

			$this->bulk_setup_needed_post_data = $post_data;
			$this->bulk_setup_needed_post_data['pwd_hash'] = $pwd_hash;

			return true;
		}		

		$app_id = wptc_decode_auth_token($post_data['authorization'], 'appId');

		if (empty($app_id)) {
			return false;
		}

		global $wpdb;

		if($this->get_option('appID') != $app_id ){
			return false;
		}

		return true;
	}

	public function set_plugin_priority() {
		$wptc_plugin = WPTC_TC_PLUGIN_NAME . '/' . WPTC_TC_PLUGIN_NAME .'.php';
		$active_plugins  = get_option('active_plugins');

		if (reset($active_plugins) === $wptc_plugin) {
			return;
		}

		$wptc_position = array_search($wptc_plugin, $active_plugins);

		if ($wptc_position === false || $wptc_position === 0) {
			return;
		}

		unset($active_plugins[$wptc_position]);
		array_unshift($active_plugins, $wptc_plugin);
		update_option( 'active_plugins', array_values($active_plugins) );
	}

	public function fixIWPClientBranding() {
        if(array_key_exists('iwp_mmb_core', $GLOBALS)){
            global $iwp_mmb_core;
            if(!empty($iwp_mmb_core)){
	            // $iwp_mmb_core->request_params = true;
	            // remove_action( 'wp_footer', array( $my_class, 'class_function_being_removed' ) );

	            remove_action('init', array($iwp_mmb_core,'iwp_cpb_hide_updates'), 10, 1);
		        remove_action('admin_init', array($iwp_mmb_core,'admin_actions'));
            }
        }
    }

	private function wptc_plugin_compatibility_fix(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$wptc_plugin_fix = new WPTC_FixCompatibility();
		$wptc_plugin_fix->fixAllInOneSecurity();
		$wptc_plugin_fix->fixWpSimpleFirewall();
		$wptc_plugin_fix->fixDuoFactor();
		$wptc_plugin_fix->fixShieldUserManagementICWP();
		$wptc_plugin_fix->fixSidekickPlugin();
		$wptc_plugin_fix->fixSpamShield();
		$wptc_plugin_fix->fixWpSpamShieldBan();
	}
}

