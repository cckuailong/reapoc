<?php

class Wptc_InitialSetup extends Wptc_InitialSetup_Init {
	public $tabs;
	private $config,
			$options,
			$JQUERY_UI_JS_PATH,
			$FANCY_TREE_JS_PATH,
			$FANCY_TREE_CSS_PATH,
			$WPTC_DIALOG_CSS,
			$WPTC_PLANS_CSS,
			$WPTC_PLANS_JS;
	public function __construct(){
		$this->config = WPTC_Base_Factory::get('Wptc_InitialSetup_Config');
		$this->options = WPTC_Factory::get('config');
	}

	public function load_page(){
		$this->define_header_file_paths();
		$this->include_header_files();
	}

	private function define_header_file_paths(){
		$this->JQUERY_UI_JS_PATH = '/treeView/jquery-ui.custom.js';
		$this->FANCY_TREE_JS_PATH = '/treeView/jquery.fancytree.js';
		$this->FANCY_TREE_CSS_PATH = '/treeView/skin/ui.fancytree.css';
		$this->WPTC_DIALOG_CSS = '/wptc-dialog.css';
		$this->WPTC_PLANS_CSS = '/wptc-plans.css';
		$this->WPTC_PLANS_JS = '/Views/wptc-plans.js';
	}

	private function include_header_files(){
		wp_enqueue_style('wptc-dialog-css', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . $this->WPTC_DIALOG_CSS, array(), WPTC_VERSION);
		wp_enqueue_style('wptc-plans-css', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . $this->WPTC_PLANS_CSS, array(), WPTC_VERSION);
		wp_enqueue_script('wptc-jquery-ui-custom-js', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . $this->JQUERY_UI_JS_PATH, array(), WPTC_VERSION);
		wp_enqueue_script('wptc-fancytree-js', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . $this->FANCY_TREE_JS_PATH, array(), WPTC_VERSION);
		wp_enqueue_style('wptc-fancytree-css', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . $this->FANCY_TREE_CSS_PATH, array(), WPTC_VERSION);
	}

	public function requirement_check(){
		global $wpdb;

		$requirements = array(
			array(
				'title'			=>		'PHP Version',
				'min'			=>		'>= 5.4.0',
				'suggestion'	=>		'>= 5.6.0',
				'value'			=>		PHP_VERSION,
				'status'		=>		(version_compare(PHP_VERSION, '5.4.0', '>=')) ? true : false,
			),
			array(
				'title'			=>		'MySQL Version',
				'min'			=>		'>= 5.0.15',
				'suggestion'	=>		'>= 5.5 (WordPress recommends 5.6+)',
				'value'			=>		$wpdb->db_version(),
				'status'		=>		(version_compare($wpdb->db_version(), '5.0.15', '>=')) ? true : false,
			),
			array(
				'title'			=>		'cURL Function',
				'min'			=>		'Enabled',
				'suggestion'	=>		'',
				'value'			=>		($this->is_curl_available()) ? 'Enabled' : 'Disabled',
				'status'		=>		($this->is_curl_available()) ? true : false,
			),
			array(
				'title'			=>		'Site accessibility from WPTC Server',
				'min'			=>		$this->is_localhost() ? 'WPTC does not support localhost sites' : 'Enabled',
				'suggestion'	=>		'',
				'value'			=>		$this->is_localhost() ? '' : 'Enabled',
				'status'		=>		!$this->is_localhost() ? true : false,
			),
			array(
				'title'			=>		'Query Monitor',
				'min'			=>		'Deactivated',
				'suggestion'	=>		$this->is_query_monitor_activated() ? 'Deactivate Query monitor and refresh this page.' : '',
				'value'			=>		$this->is_query_monitor_activated() ? 'Activated' : 'Deactivated',
				'status'		=>		!$this->is_query_monitor_activated() ? true : false,
			)
		);

		$required_failed  = false;
		foreach ($requirements as $key => $requirement) {
			if ($requirement['status'] === false) {
				$required_failed = true;
			}
		}

		$requirements['overall_requirements_passed'] = ($required_failed) ? false : true;

		return $requirements;
	}

	private function is_localhost(){

		if (WPTC_ENV === 'local') {
			return false;
		}

		return !strstr( site_url(), '.' );
	}

	private function is_query_monitor_activated(){

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if (is_plugin_active('query-monitor/query-monitor.php')) {
			return true;
		}
		return false;
	}

	private function is_curl_available(){
		if (!function_exists('curl_version') || !function_exists('curl_exec')) {
			return false;
		}

		$disabled_functions = explode(',', ini_get('disable_functions'));
		$curl_exec_enabled = !in_array('curl_exec', $disabled_functions);

		return ($curl_exec_enabled) ? true : false;
	}

	public function success_and_error_flaps(){
		$email = $this->config->get_option('main_account_email');

		if(!empty($_GET['show_plan_success_flap'])){
			return '<div class="bs-callout bs-callout-success flap_boxes_group_wptc wptc_success_box" style="display: none;"></div>';
		} elseif(!empty($_GET['show_plan_error_flap'])){
			return '<div class="bs-callout bs-callout-danger flap_boxes_group_wptc wptc_error_box "><span class="error_label">Error:</span><span class="err_msg">'.stripslashes(urldecode($_GET['err_msg'])).'</span></div>';
		}
	}

	public function get_s3_creds_box_div() {

		$div = $sub_div = '';

		$as3_access_key = $this->config->get_option('as3_access_key');
		$sub_div = $sub_div . '<div >
			You should now create an IAM user with <br>
			<label title="Yes">
				<input name="wptc_iam_user_status"  type="radio" id="full_access_creds" checked value="full_access">
					'.__( 'Full Access (recommended)', 'wp-time-capsule' ).'
				</span>
			</label>
			<label title="No" style="margin-left: 10px !important;">
				<input name="wptc_iam_user_status" type="radio" id="restricted_access_creds" value="restricted_access">
				<span class="">
					'.__( 'Restricted access', 'wp-time-capsule' ).'
				</span>
			</label>
		</div>
		<div class="l1" id ="wptc-connect-cloud-note-s3-automatic" style="padding: 20px 0px 0px 0px; display:none" >See <a href="https://docs.wptimecapsule.com/article/37-how-to-connect-your-amazon-s3-account-to-wptc-more-securely" target="_blank">How to create an IAM user with full access</a>. We will create the bucket and set it up automatically.</div>
		<div class="l1" id ="wptc-connect-cloud-note-s3-manual" style="padding: 20px 0px 0px 0px; display:none" >See <a href="https://docs.wptimecapsule.com/article/46-how-to-manually-connect-your-amazon-s3-account-to-wptc-more-securely" target="_blank">How to create an IAM user with restricted access, create a bucket and set it up for WPTC</a>. <br>
			<span>SITE_NAME : <strong>' . $this->options->get_dropbox_folder_tc() . '</strong> </span>
		</div>';


		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="as3_access_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Access Key" id="as3_access_key" required value="' . $as3_access_key . '" /> </div>';

		$as3_secure_key = $this->config->get_option('as3_secure_key');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="as3_secure_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Secure Key" id="as3_secure_key" required value="' . $as3_secure_key . '" /> </div>';

		$as3_bucket_region = $this->config->get_option('as3_bucket_region');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;">'
					.  $this->get_s3_select_box_div($as3_bucket_region)  .
					'</div>';

		$as3_bucket_name = $this->config->get_option('as3_bucket_name');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" class="wptc_general_inputs" style="width: 45%;" name="as3_bucket_name" placeholder="Bucket Name" id="as3_bucket_name" required value="' . $as3_bucket_name . '" /> </div>';

		$sub_div .= '<span id="wptc_bucket_note" style="margin-top: -20px;margin-left: 346px;font-style: italic;" > Bucket will be created automatically</span>';

		$div = $div . '<div class="l1 s3_inputs creds_box_inputs"  style="padding-bottom: 10px; display:none; margin-top: -36px; position: relative;"><div style="text-align: center; font-size: 13px; padding-bottom: 10px;">' . $sub_div . '</div></div>';

		return $div;
	}

	private function get_s3_select_box_div($selected_bucket_region) {
		$buc_region_arr =
			array(
				''               => 'Select Bucket Region',
				'us-east-2'      => 'US East (Ohio)',
				'us-east-1'      => 'US East (N. Virginia)',
				'us-west-1'      => 'US West (N. California)',
				'us-west-2'      => 'US West (Oregon)',
				'ap-south-1'     => 'Asia Pacific (Mumbai)',
				'ap-northeast-2' => 'Asia Pacific (Seoul)',
				'ap-southeast-1' => 'Asia Pacific (Singapore)',
				'ap-southeast-2' => 'Asia Pacific (Sydney)',
				'ap-northeast-1' => 'Asia Pacific (Tokyo)',
				'ca-central-1'   => 'Canada (Central)',
				// 'cn-north-1'     => 'China (Beijing)',
				// 'cn-northwest-1' => 'China (Ningxia)',
				'eu-central-1'   => 'EU (Frankfurt)',
				'eu-west-1'      => 'EU (Ireland)',
				'eu-west-2'      => 'EU (London)',
				'eu-west-3'      => 'EU (Paris)',
				'eu-north-1'      => 'EU (Stockholm)',
				'sa-east-1'      => 'South America (São Paulo)'
			);

		$div = '<select name="as3_bucket_region" id="as3_bucket_region" class="wptc_general_inputs" style="width:45%; height: 38px;">';

		foreach ($buc_region_arr as $k => $v) {
			$selected = '';
			if ($k == $selected_bucket_region) {
				$selected = 'selected';
			}
			$div = $div . '<option value="' . $k . '" ' . $selected . ' class="dropOption" >' . $v . '</option>';
		}
		$div = $div . '</select>';
		return $div;
	}

	public function get_wasabi_creds_box_div() {

		$div = $sub_div = '';

		$wasabi_access_key = $this->config->get_option('wasabi_access_key');

		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="wasabi_access_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Access Key" id="wasabi_access_key" required value="' . $wasabi_access_key . '" /> </div>';

		$wasabi_secure_key = $this->config->get_option('wasabi_secure_key');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="wasabi_secure_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Secure Key" id="wasabi_secure_key" required value="' . $wasabi_secure_key . '" /> </div>';

		$wasabi_bucket_region = $this->config->get_option('wasabi_bucket_region');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;">'
					.  $this->get_wasabi_select_box_div($wasabi_bucket_region)  .
					'</div>';

		$wasabi_bucket_name = $this->config->get_option('wasabi_bucket_name');
		$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" class="wptc_general_inputs" style="width: 45%;" name="wasabi_bucket_name" placeholder="Bucket Name" id="wasabi_bucket_name" required value="' . $wasabi_bucket_name . '" /> </div>';

		$div = $div . '<div class="l1 wasabi_inputs creds_box_inputs"  style="padding-bottom: 10px; display:none; margin-top: -36px; position: relative;"><div style="text-align: center; font-size: 13px; padding-bottom: 10px;">' . $sub_div . '</div></div>';

		return $div;
	}

	private function get_wasabi_select_box_div($selected_bucket_region) {
		$buc_region_arr =
			array(
				''               => 'Select Bucket Region',
				'us-east-2'      => 'US East',
				'us-west-1'      => 'US West',
				'eu-central-1'      => 'EU Central',
			);

		$div = '<select name="wasabi_bucket_region" id="wasabi_bucket_region" class="wptc_general_inputs" style="width:45%; height: 38px;">';

		foreach ($buc_region_arr as $k => $v) {
			$selected = '';
			if ($k == $selected_bucket_region) {
				$selected = 'selected';
			}
			$div = $div . '<option value="' . $k . '" ' . $selected . ' class="dropOption" >' . $v . '</option>';
		}
		$div = $div . '</select>';
		return $div;
	}

	public function store_cloud_access_token_wptc(){
		if ((isset($_GET['cloud_auth_action']) && $_GET['cloud_auth_action'] == 'g_drive') && isset($_GET['code']) && !isset($_GET['error'])) {
			$this->config->set_option('oauth_state_g_drive', 'access');
			$req_token_dets['refresh_token'] = $_GET['code'];
			$this->config->set_option('gdrive_old_token', serialize($req_token_dets));
		} else if ((isset($_GET['cloud_auth_action']) && $_GET['cloud_auth_action'] == 'dropbox') && isset($_GET['code']) && !isset($_GET['error'])) {
			$access_token = base64_decode(urldecode($_GET['code']));
			$this->config->set_option('dropbox_access_token', $access_token);
			$this->config->set_option('dropbox_oauth_state', 'access');
		}
	}

	public function check_cloud_min_php_min_req() {
		$cloud_eligible = array();
		if (is_php_version_compatible_for_g_drive_wptc()) {
			$cloud_eligible[] = 'gdrive';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$cloud_eligible[] = 's3';
		}
		$cloud_eligible[] = 'dropbox'; // available all version of php
		return json_encode($cloud_eligible);
	}

	public function process_GET_request_wptc() {

		if (isset($_GET['error'])) {
			if (isset($_GET['cloud_auth_action'])) {
				$this->config->set_option('last_cloud_error', $_GET['error']);
			}
		}

		if (!empty($_GET['logout'])) {
			process_wptc_logout('logout');
		}
	}

	public function process_wptc_login($die_on_success = true) {

		wptc_log($_POST['data'], "--------process_wptc_login-----post data---");

		if (empty( $_POST['data'] )) {
			wptc_die_with_json_encode(array('status' => 'error' , 'msg' => 'Username and password cannot be empty'));
		}

		$request = $_POST['data'];

		$this->config->set_option('wptc_main_acc_email_temp', base64_encode($request['email']));
		$this->config->set_option('wptc_main_acc_pwd_temp', base64_encode(md5(trim( wp_unslash( $request[ 'password' ] ) ))));
		$this->config->set_option('wptc_token', false);

		$auth_result = $this->options->request_service(
			array(
				'email'                 => $request['email'],
				'pwd'                   => trim( wp_unslash( $request[ 'password' ] )),
				'return_response'       => false,
				'sub_action' 	        => false,
				'login_request'         => true,
				'reset_login_if_failed' => true,
			)
		);

		wptc_log($auth_result, "--------auth_result-------");

		if (isset( $auth_result['error'] )) {
			wptc_die_with_json_encode(array('status' => 'error' , 'msg' => $auth_result['error']));
		}

		$privileges_wptc = $this->config->get_option('privileges_wptc');
		$privileges_wptc = json_decode($privileges_wptc);

		wptc_log($privileges_wptc, "--------privileges_wptc-----process_wptc_login---");

		$default_repo = $this->config->get_option('default_repo');

		if (!empty($default_repo)) {
			push_settings_wptc_server();
		}

		if($die_on_success){
			wptc_die_with_json_encode(
				array(
					'status' => 'success',
					'url' => network_admin_url("admin.php?page=wp-time-capsule")
				)
			);
		}
	}

	public function process_bulk_setup_wptc_login($die_on_success = true, $request = null) {

		wptc_log($request, "--------process_bulk_setup_wptc_login-----request---");

		if (empty( $request )) {
			wptc_die_with_json_encode(array('status' => 'error' , 'msg' => 'Username and password cannot be empty'));
		}

		if( empty($request['cloud_creds']['cloud_settings_email']) || 
			$request['email'] != $request['cloud_creds']['cloud_settings_email'] ){

			wptc_log('', "--------different email-during bulk-------");

			wptc_die_with_json_encode(array('status' => 'error' , 'msg' => 'Email account is different.'));
		}

		$this->config->set_option('wptc_main_acc_email_temp', base64_encode($request['email']));

		$this->config->set_option('wptc_main_acc_pwd_temp', base64_encode(trim( wp_unslash( $request[ 'pwd_hash' ] ))));

		$this->config->set_option('wptc_token', false);

		$request_service_params = array(
			'email'                 => $request['email'],
			'pwd'                   => trim( wp_unslash( $request[ 'pwd_hash' ] )),
			'return_response'       => false,
			'sub_action' 	        => false,
			'login_request'         => true,
			'reset_login_if_failed' => true,
			'is_bulk_setup' 		=> true
		);

		$auth_result = $this->options->request_service($request_service_params);

		wptc_log($auth_result, "--------auth_result-------");

		if (empty($auth_result) || isset( $auth_result['error'] )) {
			wptc_die_with_json_encode(array('status' => 'error' , 'msg' => $auth_result['error']));
		}

		$privileges_wptc = $this->config->get_option('privileges_wptc');
		$privileges_wptc = json_decode($privileges_wptc);

		wptc_log($privileges_wptc, "--------privileges_wptc-----process_wptc_login---");

		$default_repo = $this->config->get_option('default_repo');

		if (!empty($default_repo)) {
			push_settings_wptc_server();
		}

		if($die_on_success){
			wptc_die_with_json_encode(
				array(
					'status' => 'success',
					'url' => network_admin_url("admin.php?page=wp-time-capsule")
				)
			);
		}
	}

	public function add_cloud_details_for_bulk_support($data = null)
	{
		switch ($data['default_repo']) {
			case 'dropbox':
				
				$this->config->set_option('default_repo', $data['default_repo']);
				$this->config->set_option('default_repo_history', $data['default_repo']);
				$this->config->set_option('dropbox_access_token', $data['dropbox_access_token']);
				$this->config->set_option('dropbox_oauth_state', $data['dropbox_oauth_state']);
				$this->config->set_option('signed_in_repos', $data['signed_in_repos']);

				break;

			case 'g_drive':
				
				$this->config->set_option('default_repo', $data['default_repo']);
				$this->config->set_option('default_repo_history', $data['default_repo']);
				$this->config->set_option('signed_in_repos', $data['signed_in_repos']);
				
				$this->config->set_option('gdrive_old_token', $data['gdrive_old_token']);
				$this->config->set_option('oauth_state_g_drive', $data['oauth_state_g_drive']);
				$this->config->set_option('current_g_drive_email', $data['current_g_drive_email']);

				break;

			case 's3':
				
				$this->config->set_option('default_repo', $data['default_repo']);
				$this->config->set_option('default_repo_history', $data['default_repo']);
				$this->config->set_option('signed_in_repos', $data['signed_in_repos']);

				$this->config->set_option('as3_access_key', $data['as3_access_key']);
				$this->config->set_option('as3_secure_key', $data['as3_secure_key']);
				$this->config->set_option('as3_bucket_region', $data['as3_bucket_region']);
				$this->config->set_option('as3_bucket_name', $data['as3_bucket_name']);

				include_once WPTC_PLUGIN_DIR . 'S3/class.iam.php';
				$iam = new WPTC_IAM_S3();
				$iam->authorize_full_access();
				$response = $iam->process_full_access();

				if (!empty($response['error'])) {
					$this->config->set_option('as3_access_key', false);
					$this->config->set_option('as3_secure_key', false);
					$this->config->set_option('as3_bucket_region', false);
					$this->config->set_option('as3_bucket_name', false);
					$this->config->set_option('default_repo', false);
					$this->config->set_option('s3_NoncurrentVersionExpiration_days', false);

					wptc_log($response, "--------s3 iam add_cloud_details_for_bulk_support--error------");

					send_bulk_setup_status_to_server('Error setting up cloud.');

					wptc_die_with_json_encode( $response );
				}

				break;

			case 'wasabi':

				if( !defined('DEFAULT_REPO') && !empty($data['default_repo']) ){
					define('DEFAULT_REPO', 'wasabi');
				}
				
				$this->config->set_option('default_repo', $data['default_repo']);
				$this->config->set_option('default_repo_history', $data['default_repo']);
				$this->config->set_option('signed_in_repos', $data['signed_in_repos']);

				$this->config->set_option('wasabi_access_key', $data['wasabi_access_key']);
				$this->config->set_option('wasabi_secure_key', $data['wasabi_secure_key']);
				$this->config->set_option('wasabi_bucket_region', $data['wasabi_bucket_region']);
				$this->config->set_option('wasabi_bucket_name', $data['wasabi_bucket_name']);

				$obj = WPTC_Factory::get('WasabiFacade');
				$obj->init();
				$response = $obj->is_authorized_during_initial_setup(true);

				break;
			
			default:
				# code...
				break;
		}
	}

	public function record_signed_in_repos(&$dropbox) {
		$signed_in_arr = $this->config->get_option('signed_in_repos');
		if (empty($signed_in_arr)) {
			$signed_in_arr = array();
		} else {
			$signed_in_arr = unserialize($signed_in_arr);
		}
		if (empty($dropbox)) {
			return false;
		}
		$this->wipe_out_prev_acc_backups_wptc($signed_in_arr, $this->config->get_option('default_repo'), $dropbox->get_quota_div());
		$signed_in_arr[$this->config->get_option('default_repo')] = $dropbox->get_quota_div();
		$this->config->set_option('signed_in_repos', serialize($signed_in_arr));
	}

	private function wipe_out_prev_acc_backups_wptc($signed_in_arr, $default_repo, $email) {
		if (empty($signed_in_arr) || !is_array($signed_in_arr)) {
			return false;
		}

		if ($default_repo == 'g_drive' && array_key_exists('g_drive', $signed_in_arr) && $signed_in_arr['g_drive'] != $email) {
			wptc_log('google gdrive exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
			$this->clear_prev_acc_backup_data_wptc();
		} else if ($default_repo == 'dropbox' && array_key_exists('dropbox', $signed_in_arr) && $signed_in_arr['dropbox'] != $email) {
			wptc_log('dropbox exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
			$this->clear_prev_acc_backup_data_wptc();
		} else if ($default_repo == 's3' && array_key_exists('s3', $signed_in_arr) && $signed_in_arr['s3'] != $email) {
			wptc_log('s3 exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
			$this->clear_prev_acc_backup_data_wptc();
		}
	}

	private function clear_prev_acc_backup_data_wptc() {
		$backup = new WPTC_BackupController();
		$backup->clear_prev_repo_backup_files_record();
	}

	public function get_users_own_repo_html(){

		$last_err   = $this->config->get_option('last_cloud_error');
		$show_error =  empty($last_err) ? 'display:none ' : 'display:block';
		$this->config->set_option('last_cloud_error', false);

		return '<div id="wptc_users_own_repo_html_block"  style="display:block;">
					<div class="l1" id="wptc-connect-cloud-note" style="padding: 20px 0px 0px 0px; display: block" >The backup of this website will be stored in a folder in your cloud</div>
					<form id="backup_to_dropbox_continue" name="backup_to_dropbox_continue" method="post">
						' . $this->get_select_cloud_dialog_div() . '
					</form>
					<div class="l1 wptc_error_div " style=' . $show_error . '>
						' . $last_err . '
					</div>
				</div>';
	}

	public function get_select_cloud_dialog_div() {

		$div = $sub_div = '';
		$display_status = $gdrive_not_eligible = $dropbox_not_eligible = $s3_not_eligible = 'display:none';

		$sites_count = $this->config->get_option('connected_sites_count');
		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div .= '<div style="text-align: center; padding: 10px 5px; line-height: 22px; display:none" id="google_limit_reached_text_wptc">Google has a limit on the number of sites you can authenticate per app. If you are backing up all sites to the same Google Account, use a previously generated token. <a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" style="text-decoration:none" target="_blank">Show me how.</a></div>';
		}
		$div .= '<div class="l1"  style="padding-bottom: 10px; padding-top: 0px">
					<select name="select_wptc_cloud_storage" id="select_wptc_cloud_storage" class="wptc_general_inputs" style="width:45%;height: 38px;">
						<option value="" class="dummy_select">Select your cloud storage app</option>';

		if (is_php_version_compatible_for_s3_wptc()) {
			$div .= '<option value="wasabi" label="Wasabi (recommended)" >Wasabi (recommended)</option>';
		} else {
			$div .= '<option disabled="disabled" value="wasabi" label="Wasabi (recommended)" >Wasabi</option>';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$div .= '<option value="s3" label="Amazon S3 (recommended)" >Amazon S3 (recommended)</option>';
		} else {
			$div .= '<option disabled="disabled" value="s3" label="Amazon S3 (recommended)" >Amazon S3</option>';
		}
		if (is_php_version_compatible_for_dropbox_wptc()) {
			$dropbox_not_eligible = 'display:none';
			$div .= '<option value="dropbox" label="Dropbox">Dropbox</option>;';
		} else {
			$dropbox_not_eligible = 'display:block';
			$div .= '<option disabled="disabled" value="dropbox" label="Dropbox">Dropbox</option>;';
		}
		if (is_php_version_compatible_for_g_drive_wptc()) {
			$gdrive_not_eligible = 'display:none';
			$div .= '<option value="g_drive" label="Google Drive">Google Drive</option>';
		} else {
			$div .= '<option disabled="disabled" value="g_drive" label="Google Drive">Google Drive</option>';
			$gdrive_not_eligible = 'display:block';
		}

		$div .= '</select>
				</div>';

		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div .= '<div id="gdrive_refresh_token_wptc" style="position:relative; display:none"><input type="text" id="gdrive_refresh_token_input_wptc" placeholder="Paste token here" style="width: 45%;position: relative;left: 349px;top: -10px;" class="wptc_general_inputs">';
			$div .= '<a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" id="see_how_to_add_refresh_token_wptc" target="_blank" style="text-decoration: none;position: absolute;cursor: pointer;top: 54px;right: 349px;font-size: 12px;">Need help ?</a></div>';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$s3_not_eligible = 'display:none';
			$div .=  $this->get_s3_creds_box_div();
		} else {
			$s3_not_eligible = 'display:block';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$wasabi_not_eligible = 'display:none';
			$div .=  $this->get_wasabi_creds_box_div();
		} else {
			$wasabi_not_eligible = 'display:block';
		}

		$display_status = ( $wasabi_not_eligible == 'display:block'
			|| $s3_not_eligible == 'display:block' 
			|| $gdrive_not_eligible == 'display:block' 
			|| $dropbox_not_eligible == 'display:block' ) ? 'display:block' : 'display:none';

		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div = $div . '<div id="google_token_add_btn" style="display:none"><div class="cloud_error_mesg_g_drive_token"></div><input type="button" id="save_g_drive_refresh_token" class="btn_pri cloud_go_btn" style="margin: 10px 37.9% 20px; width: 330px; text-align: center;" value="Authenticate Token" ><div style="text-align: center; margin-bottom: 20px;">(OR)</div></div>';
		}
		$div = $div . '<div class="wptc_cloud_not_recommended" style="color: orange; margin-bottom: 14px; text-align: center; display: none; ">Note: You might experience slow backups with the cloud storage you selected. The recommended storages are S3 and Wasabi.</div>';

		$div = $div . '<div class="cloud_error_mesg"></div><input type="button" id="connect_to_cloud" class="btn_pri cloud_go_btn" style="margin: 7px 37.9% 15px; width: 330px; text-align: center; display: none;" value="Connect my cloud account" >';

		$div .= '<div style="clear:both"></div>';
		$div .= '<div id="mess" style="text-align: center; font-size: 13px; padding-top: 10px; padding-bottom: 10px; display: none;">You will be redirected to the specific Cloud Site for allowing access to the plugin.<br> Click on <strong>Allow</strong> when prompted.</div>';
		$div .= "<div class='dashicons-before dashicons-warning' id='wasabi_seperate_bucket_note' style='display:none; font-style: italic; left: 10px; font-size: 13px;'><span style='line-height: 22px'>Please create a separate bucket on Wasabi since we will be enabling versioning on that bucket. We create subfolders for each site, so you don't have to create a new bucket everytime.</span></div>";
		$div .= "<div class='dashicons-before dashicons-warning' id='s3_seperate_bucket_note' style='display:none; font-style: italic; left: 10px; font-size: 13px;'><span style='line-height: 22px'>Please create a separate bucket on Amazon S3 since we will be enabling versioning on that bucket. We create subfolders for each site, so you don't have to create a new bucket everytime.</span></div>";
		$div .= "<div style='height: 60px; position: relative;".$display_status." ' id='php_req_note_wptc'><div class='dashicons-before dashicons-warning' id='dropbox_php_req_note' style='position: absolute;font-size: 12px;top: -27px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$dropbox_not_eligible."'><span style='position: absolute;top: 11px;left: 24px; '>Dropbox requires PHP v5.3.1+. Please upgrade your PHP to use Dropbox.</span></div><div class='dashicons-before dashicons-warning' id='g_drive_php_req_note' style='position: absolute;font-size: 12px;top: 0px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$gdrive_not_eligible."'><span style='position: absolute;top: 11px;left: 24px; '>Google Drive requires PHP v5.4.0+. Please upgrade your PHP to use Google Drive.</span></div><div class='dashicons-before dashicons-warning' id='s3_php_req_note' style='position: absolute;font-size: 12px;top: 26px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$s3_not_eligible."'><span style='position: absolute;top: 11px;left: 24px;''>Amazon S3 requires PHP v5.3.3+. Please upgrade your PHP to use Amazon S3.</span></div></div>";

		if ( (isset($_GET['cloud_auth_action']) 
			&& $_GET['cloud_auth_action'] == 's3') 
			&& !empty($_GET['as3_access_key']) 
			&& !empty($_GET['as3_secure_key']) 
			&& !empty($_GET['as3_bucket_name']) && DEFAULT_REPO_LABEL != 'Cloud' ) {
		}
	
		return $div;
	}

	public function is_fresh_backup(){
		global $wpdb;
		$fcount = $wpdb->get_results('SELECT COUNT(*) as files FROM ' . $wpdb->base_prefix . 'wptc_processed_files');
		return (!empty($fcount) && !empty($fcount[0]->files) && $fcount[0]->files > 0) ? 'yes' : 'no';
	}

}
