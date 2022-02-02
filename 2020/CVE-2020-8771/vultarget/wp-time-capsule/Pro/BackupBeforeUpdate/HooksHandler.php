<?php

class Wptc_Backup_Before_Update_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {
	const JS_URL = '/Pro/BackupBeforeUpdate/init.js';

	protected $config;
	protected $backup_before_update_obj;
	protected $backup_before_auto_update_obj;
	protected $backup_before_auto_update_settings;
	protected $upgrade_wait_time;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update_Config');
		$this->upgrade_wait_time = 2 * 60; // 2 min
		$this->backup_before_update_obj = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update');
		$this->backup_before_auto_update_obj = WPTC_Pro_Factory::get('Wptc_Backup_Before_Auto_Update');
		$this->backup_before_auto_update_settings = WPTC_Pro_Factory::get('Wptc_Backup_Before_Auto_Update_Settings');
		$this->install_actions_wptc();
	}

	//WPTC's specific hooks start

	public function just_initialized_fresh_backup_wptc_h($args) {
		wptc_log($args, '-------just_initialized_fresh_backup_wptc_h--------');

		$this->backup_before_update_obj->check_and_initiate_if_update_required_after_backup_wptc($args);
	}

	public function do_ptc_upgrades() {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->config->set_option('upgrade_process_running', time());

		do_action('take_screenshot_wptc', 'Before');

		if ( $this->config->get_option('is_bulk_update_request') ) {
			wptc_log(array(), '--------Start Bulk Updates-------------');
			$this->backup_before_update_obj->do_bulk_upgrade_request();
		} else {
			wptc_log(array(), '--------Start Single Updates-------------');
			//Change func name like do_single_upgrade
			$this->backup_before_update_obj->do_single_upgrades();
		}

		$this->config->flush();
	}

	public function upgrader_pre_download($request){
		// wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		wptc_log($request,'-----------$upgrader_pre_download----------------');

		// if (strstr($request['package'], 'updraftplus') !== false) {
		// 	$request['package'] = str_replace('updraftplus', 'dummy', $request['package']);
		// }

		return $request;
	}

	public function site_transient_update_plugins_h($value, $url){
		// wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (stripos($url, 'https://downloads.wordpress.org/plugin/') === 0) {
			wptc_log(array(), '---------PLUGIN UPDATE------------');
			// if(strstr($url, 'updraftplus') !== false) {
			// 	return false;
			// }

			// if(strstr($url, 'theme-check') !== false) {
			// 	return false;
			// }
		} else if (stripos($url, 'https://downloads.wordpress.org/theme/') === 0) {
			wptc_log(array(), '---------THEME UPDATE------------');
			// if(strstr($url, 'twentysixteen') !== false) {
			// 	return false;
			// }

			// if(strstr($url, 'twentyseventeen') !== false) {
			// 	return false;
			// }
		} else if (stripos($url, 'https://downloads.wordpress.org/release/') === 0) {
			wptc_log(array(), '---------CORE UPDATE------------');
			//enable this to stop core updates
			// return false;
		} else if (stripos($url, 'https://downloads.wordpress.org/translation/') === 0) {
			wptc_log(array(), '---------TRANSLATION UPDATE------------');
			//enable this to stop translation updates
			// return false;
		}

		return $value;
	}


	public function page_settings_content($more_tables_div, $dets1 = null, $dets2 = null, $dets3 = null) {

		$current_setting = $this->config->get_option('backup_before_update_setting');

		$more_tables_div .= '
		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-bbu"> <p></p>
			<table class="form-table">
				<tr>
					<th scope="row"> '.__( 'Backup Before Manual Updates', 'wp-time-capsule' ).'
					</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">Backup Before Manual Updates</legend>
							<label title="Always">' .
				get_checkbox_input_wptc('backup_before_update_always', 'always', $current_setting, 'backup_before_update_setting') .
				'<span class="">
									'.__( 'Always', 'wp-time-capsule' ).'
								</span>
							</label>
							<p class="description">'.__( 'The site is backed up before each manual update. <br>
								Note: Checking this option will allow WPTC plugin to listen IWP update requests and perform backup before update.', 'wp-time-capsule' ).'</p>
						</fieldset>
					</td>
				</tr>';
		$more_tables_div .= $this->get_auto_update_settings_html($current_setting);
		$more_tables_div .= '</table>
		</div>';
		return $more_tables_div;
	}


	public function page_settings_tab($tabs){
		$tabs['bbu'] = __( 'Backup/Auto Updates', 'wp-time-capsule' );
		return $tabs;
	}

	public function may_be_prevent_auto_update($is_update_required, $update_details = null, $dets2 = null, $dets3 = null) {

		//Stop WP default auto updates
		return false;
	}

	public function enque_js_files() {
		wp_enqueue_script('wptc-backup-before-update', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . self::JS_URL, array(), WPTC_VERSION);
	}

	public function get_backup_before_update_setting_wptc() {
		return $this->config->get_option('backup_before_update_setting');
	}

	public function get_bbu_note_view() {
		$data = $this->config->get_option('bbu_note_view');
		return empty($data) ? false : unserialize($data);
	}

	public function clear_bbu_notes() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->config->set_option('bbu_note_view', false);
		die(json_encode(array('status' => 'success')));
	}

	public function get_auto_update_settings(){
		return $this->backup_before_auto_update_settings->get_auto_update_settings();
	}

	public function save_bbu_settings(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$data = $_POST['data'];

		if ( !empty( $data['backup_before_update_setting'] ) && $data['backup_before_update_setting'] == 'true') {
			$this->config->set_option('backup_before_update_setting', 'always');
		} else {
			$this->config->set_option('backup_before_update_setting', 'everytime');
		}
		return $this->backup_before_auto_update_settings->update_auto_update_settings($data);
	}

	public function get_auto_update_settings_html($bbu_setting){
		// wptc_log(array(), '---------get_auto_update_settings_html-----------');
		return $this->backup_before_auto_update_settings->get_auto_update_settings_html($bbu_setting);
	}

	public function get_installed_plugins(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$plugins = $this->backup_before_auto_update_settings->get_installed_plugins();
		if ($plugins) {
			die(json_encode($plugins));
		}

	}

	public function get_installed_themes(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$themes = $this->backup_before_auto_update_settings->get_installed_themes();
		if ($themes) {
			die(json_encode($themes));
		}
	}

	public function install_actions_wptc(){
		if ($this->config->get_option('run_init_setup_bbu')) {
			$this->config->set_option('run_init_setup_bbu', false);
			return $this->backup_before_auto_update_settings->save_default_settings();
		}
	}

	public function turn_off_auto_update(){
		return $this->backup_before_auto_update_settings->turn_off_auto_update();
	}

	public function auto_update_failed_email_user(){
		return $this->backup_before_auto_update_obj->auto_update_failed_email_user();
	}

	public function process_auto_updates(){

		if ( !$this->backup_before_auto_update_settings->can_process_auto_updates($strict_check_custom_time = true) ) {
			return false;
		}

		//Trigger auto updates request from server here
		do_action('check_auto_updates_wptc', time());
	}

	public function get_auto_updates(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if ( !$this->backup_before_auto_update_settings->can_process_auto_updates($strict_check_custom_time = false) ) {
			return false;
		}


		wptc_login_as_admin();

		wptc_log(array(),'-----------Setting wp loaded hookss----------------');
		add_action('wp_loaded', 'admin_wp_loaded_wptc', 2147483649);
		add_action('wp_loaded', array(&$this, 'set_auto_updates'), 2147483650);

		return true;
	}

	public function set_auto_updates(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$response = $this->backup_before_auto_update_obj->set_updates();

		if ($response === false) {
			send_response_wptc('No Update available', 'BACKUP');
		}
	}

	public function check_auto_updates(){
		$this->backup_before_auto_update_obj->check_auto_updates();
	}

	public function is_upgrade_in_progress(){
		$progress = $this->config->get_option('upgrade_process_running');
		if (empty($progress)) {
			return false;
		}

		$progress += $this->upgrade_wait_time;
		if ($progress < time()) {
			return false;
		}

		return true;
	}

	public function backup_and_update($data){
		return $this->backup_before_update_obj->handle_iwp_update_request($data);
	}

	public function turn_off_themes_auto_updates(){
		return $this->backup_before_auto_update_settings->disable_theme_updates($data);
	}

	public function modify_settings($settings){
		return $this->backup_before_auto_update_settings->modify_settings($settings);
	}

	public function included_new_items($upgrade_object, $options){
		return $this->backup_before_auto_update_settings->included_new_items($upgrade_object, $options);
	}

	public function check_any_upgrades_available(){
		if( !$this->config->get_option('bulk_update_request') 
			&& !$this->config->get_option('single_upgrade_details') ){

			return ;
		}

		$this->config->set_option('start_upgrade_process', time());

		wptc_log(array(),'-----------Updates set----------------');
		
		send_response_wptc('Updates set, Ping me to start upgrade', 'Backup');
	}

	public function is_upgrade_request(){

		if( !$this->config->get_option('start_upgrade_process') ){
			return false;
		}

		wptc_log(array(),'-----------Updates available so setting Hooks----------------');

		/* -- Enable this to avoid throwing errors
		error_reporting(0);
		@ini_set("display_errors", 0);
		*/

		wptc_login_as_admin();

		add_action('wp_loaded', 'admin_wp_loaded_wptc', 2147483649);
		add_action('wp_loaded', array( &$this, 'do_upgrade'), 2147483650);
		return true;
	}

	public function do_upgrade(){

		if (apply_filters('is_upgrade_in_progress_wptc', '')) {
			wptc_log(array(),'-----------Previosu upgrade under progress----------------');
			send_response_wptc('PREVIOUS_UPGRADE_UNDER_PROGRESS_REQUEST_ME_LATER', 'SCHEDULE');
		}

		//Run all updates here
		do_action('do_ptc_upgrades_wptc', time());

		WPTC_Factory::get('config')->complete_backup();

		send_response_wptc('Updates completed');
	}

	public function update_bulk_settings($server_data){
		$this->backup_before_auto_update_settings->update_bulk_settings($server_data);
	}
}
