<?php

class Wptc_Staging_Hooks_Hanlder extends Wptc_Base_Hooks_Handler{
	protected $staging;
	protected $config;
	protected $update_in_staging;

	public function __construct() {
		$this->staging = WPTC_Pro_Factory::get('Wptc_Staging');
		$this->config = WPTC_Pro_Factory::get('Wptc_staging_Config');
		$this->update_in_staging = new WPTC_Update_In_Staging();
	}

	public function staging_view(){
		include_once ( WPTC_PLUGIN_DIR . 'Pro/Staging/Views/wptc-staging-options.php' );
	}

	public function init_staging_wptc_h(){
		wptc_log(array(), '-----------init_staging_wptc_h-------------');
		$this->staging->init_staging_wptc_h(true);
	}

	public function get_staging_details(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();
		$details = $this->staging->get_staging_details();
		$details['is_running'] = $this->is_any_staging_process_going_on();
		wptc_die_with_json_encode( $details, 1 );
	}

	public function delete_staging_wptc(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->staging->delete_staging_wptc();
	}

	public function stop_staging_wptc(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		wptc_log(array(), '-----------stop_staging_wptc_h-------------');
		$this->staging->stop_staging_wptc();
	}

	public function send_response_node_staging_wptc_h(){
		$progress_status = $this->config->get_option('staging_progress_status', true);
		$return_array = array('progress_status' => $progress_status);
		send_response_wptc('progress', 'STAGING', $return_array);
	}

	public function get_staging_url_wptc(){

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		wptc_log(array(), '---------get_staging_url_wptc---------------');
		$this->staging->get_staging_url_wptc();
	}

	public function page_settings_tab($tabs){
		$tabs['staging'] = __( 'Staging', 'wp-time-capsule' );
		return $tabs;
	}

	public function add_additional_sub_menus_wptc_h($name = '', $type = 'sub'){

		if ($type === 'main') {
			$text = __($name, 'wptc');
			add_menu_page($text, $text, 'activate_plugins', 'wp-time-capsule-staging-options', 'wordpress_time_capsule_staging_options', 'dashicons-cloud', '80.0564');
			return ;
		}

		$text = __('Staging', 'wptc');
		add_submenu_page('wp-time-capsule-monitor', $text, $text, 'activate_plugins', 'wp-time-capsule-staging-options', 'wordpress_time_capsule_staging_options');
	}

	public function is_any_staging_process_going_on($value=''){
		// wptc_log(array(), '---------is_any_staging_process_going_on---------------');
		return $this->staging->is_any_staging_process_going_on();
	}

	public function get_internal_staging_db_prefix($value=''){
		// wptc_log(array(), '---------get_internal_staging_db_prefix---------------');
		return $this->staging->get_staging_details('db_prefix');
	}

	public function is_staging_taken($value=''){
		// wptc_log(array(), '---------get_internal_staging_db_prefix---------------');
		if($this->config->get_option('same_server_staging_status') === 'staging_completed'){
			return true;
		}

		return false;
	}

	public function enque_js_files() {
		wp_enqueue_style('wptc-staging-style', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Pro/Staging/style.css', array(), WPTC_VERSION);
		wp_enqueue_script('wptc-staging', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Pro/Staging/init.js', array(), WPTC_VERSION);
	}

	public function save_stage_n_update() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		wptc_log($_POST, '---------$_POST------------');
		if (empty($_POST['update_items'])) {
			wptc_die_with_json_encode( array('status' => 'failed') );
		}
		return $this->update_in_staging->save_stage_n_update($_POST);
	}

	public function force_update_in_staging() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->force_update_in_staging();
	}

	public function continue_staging() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->choose_action();
	}

	public function start_fresh_staging() {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (empty($_POST['path'])) {
			wptc_die_with_json_encode( array('status' => 'error', 'msg' => 'path is missing') );
		}

		return $this->staging->choose_action($_POST['path'], $reqeust_type = 'fresh');
	}

	public function copy_staging() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->choose_action(false, $reqeust_type = 'copy');
	}

	public function save_staging_settings() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->save_staging_settings($_POST['data']);
	}

	public function get_staging_current_status_key() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->get_staging_current_status_key();
	}

	public function is_staging_need_request() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->is_staging_need_request();
	}

	public function process_staging_details_hook($request) {
		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		return $this->staging->process_staging_details_hook($request);
	}

	public function set_options_to_staging_site($name, $value) {
		return $this->staging->set_options_to_staging_site($name, $value);
	}

	public function page_settings_content($more_tables_div, $dets1 = null, $dets2 = null, $dets3 = null) {

		$internal_staging_db_rows_copy_limit = $this->config->get_option('internal_staging_db_rows_copy_limit');
		$internal_staging_db_rows_copy_limit = ($internal_staging_db_rows_copy_limit) ? $internal_staging_db_rows_copy_limit : WPTC_STAGING_DEFAULT_COPY_DB_ROWS_LIMIT ;

		$internal_staging_file_copy_limit = $this->config->get_option('internal_staging_file_copy_limit');
		$internal_staging_file_copy_limit = ($internal_staging_file_copy_limit) ? $internal_staging_file_copy_limit : WPTC_STAGING_DEFAULT_FILE_COPY_LIMIT ;

		$internal_staging_deep_link_limit = $this->config->get_option('internal_staging_deep_link_limit');
		$internal_staging_deep_link_limit = ($internal_staging_deep_link_limit) ? $internal_staging_deep_link_limit : WPTC_STAGING_DEFAULT_DEEP_LINK_REPLACE_LIMIT ;

		$enable_admin_login = $this->config->get_option('internal_staging_enable_admin_login');
		if ($enable_admin_login === 'yes') {
			$enable_admin_login = 'checked="checked"';
			$disable_admin_login = '';
			$login_custom_link =  '';
		} else {
			$enable_admin_login = '';
			$disable_admin_login = 'checked="checked"';
			$login_custom_link =  "style='display:none'";
		}

		$reset_permalink_wptc = $this->config->get_option('staging_is_reset_permalink');
		$reset_permalink_wptc = ($reset_permalink_wptc) ? 'checked="checked"' : '';

		$staging_login_custom_link = $this->config->get_option('staging_login_custom_link');

		$user_excluded_extenstions_staging = $this->config->get_option('user_excluded_extenstions_staging');

		$more_tables_div .= '
		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-staging" style="padding-top: 20px;">

			<table class="form-table">
				<tr>
					<th scope="row">Include/Exclude Content (<a href="https://docs.wptimecapsule.com/article/43-how-to-include-exclude-files-in-creating-or-copying-staging-site" target="_blank" style="text-decoration: underline;">Need Help?</a>)</th>
					<td >
					<fieldset style="float: left; margin-right: 2%">
						<button class="button button-secondary wptc_dropdown" id="toggle_exlclude_files_n_folders_staging" style="width: 408px; outline:none; text-align: left;">
							<span class="dashicons dashicons-portfolio" style="position: relative; top: 3px; font-size: 20px"></span>
							<span style="left: 10px; position: relative;">Folders &amp; Files </span>
							<span class="dashicons dashicons-arrow-down" style="position: relative; top: 3px; left: 255px;"></span>
						</button>
						<div style="display:none" id="wptc_exc_files_staging"></div>
					<p class="description" style="font-size: 14px;width: 408px;">Non WordPress files from the root directory are excluded by default. Include them if you want.</p>
					</fieldset>
					<fieldset style="float: left; margin-right: 2%">
							<button class="button button-secondary wptc_dropdown" id="toggle_wptc_db_tables_staging" style="width: 408px; outline:none; text-align: left;">
								<span class="dashicons dashicons-menu" style="position: relative;top: 3px; font-size: 20px"></span>
								<span style="left: 10px; position: relative;">Database</span>
								<span class="dashicons dashicons-arrow-down" style="position: relative;top: 3px;left: 288px;"></span>
							</button>
							<div style="display:none" id="wptc_exc_db_files_staging"></div>
					</fieldset>
					<br><br>
				</tr>
				<tr>
					<td></td>
					<td><br><strong style="font-size: 14px;"> The following settings are common for Live-to-Staging & Staging-to-Live processes.</strong></td>
				</tr>
				<tr>
					<th scope="row">Exclude Files of These Extensions</th>
					<td>
						<fieldset>
							<input class="wptc-split-column" type="text" name="user_excluded_extenstions_staging" id="user_excluded_extenstions_staging"  placeholder="Eg. .mp4, .mov" value="'. $user_excluded_extenstions_staging . '" />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="db_rows_clone_limit_wptc">DB Rows Cloning Limit</label>
					</th>
					<td>
						<input name="db_rows_clone_limit_wptc" type="number" min="0" step="1" id="db_rows_clone_limit_wptc" value="'.$internal_staging_db_rows_copy_limit.'" class="medium-text">
					<p class="description">'. __( 'Reduce this number by a few hundred if staging process hangs at <strong>Failed to clone database</strong>', 'wp-time-capsule' ).' </p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="files_clone_limit_wptc">Files Cloning Limit</label>
					</th>
					<td>
						<input name="files_clone_limit_wptc" type="number" min="0" step="1" id="files_clone_limit_wptc" value="'.$internal_staging_file_copy_limit.'" class="medium-text">
					<p class="description">'. __( 'Reduce this number by a few hundred if staging process hangs at <strong>Failed to copy files.</strong>', 'wp-time-capsule' ).' </p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="deep_link_replace_limit_wptc">Deep Link Replacing Limit</label>
					</th>
					<td>
						<input name="deep_link_replace_limit_wptc" type="number" min="0" step="1" id="deep_link_replace_limit_wptc" value="'.$internal_staging_deep_link_limit.'" class="medium-text">
					<p class="description">'. __( 'Reduce this number by a few hundred if staging process hangs at <strong>Failed to replace links.</strong>', 'wp-time-capsule' ).' </p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="reset_permalink_wptc">Reset Permalink</label>
					</th>
					<td>
					<input type="checkbox" id="reset_permalink_wptc" name="reset_permalink_wptc" value="1" '.$reset_permalink_wptc.'>
					<p class="description">'. __( 'Enabling this will reset the permalink to default one in staging site.', 'wp-time-capsule' ).' </p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Enable Admin Login</label>
					</th>
					<td>
					<fieldset >
						<label title="Yes">
							<input name="enable_admin_login_wptc"  type="radio" '.$enable_admin_login.' value="yes">
							<span class="">
								'.__( 'Yes', 'wp-time-capsule' ).'
							</span>
						</label>
						<label title="No" style="margin-left: 10px !important;">
							<input name="enable_admin_login_wptc" type="radio" '.$disable_admin_login.' value="no">
							<span class="">
								'.__( 'No', 'wp-time-capsule' ).'
							</span>
						</label>
						<p class="description">'. __( 'If you want to remove the requirement to login to the staging site you can deactivate it here. <br>If you disable authentication everyone can see your staging site.', 'wp-time-capsule' ).' </p>
						<br>
						<div id="login_custom_link" '. $login_custom_link.'">
							<label>Login Custom Link:</label>
							<br>
							<label>' . get_home_url() .'/ </label>
							<input  name="custom_admin_url" type="text" id="login_custom_link_wptc" value="'.$staging_login_custom_link.'" class="medium-text">
						<p class="description">'. __( 'Enter the string which links to your login page if you are using a custom login page instead the default WordPress login. ', 'wp-time-capsule' ).' </p>
						</div>
					</fieldset>
					</td>
				</tr>

				';

		return $more_tables_div. '</table> </div>';
	}

	public function upgrade_our_staging_plugin_wptc()
	{
		wptc_log('', "--------trying to upgrade_our_staging_plugin_wptc--------");

		$staging_details = $this->staging->get_staging_details();
		if(!empty($staging_details) && $staging_details['staging_folder']){
			$this->staging->staging_to_live_copy_files(true);
		}
	}

}
