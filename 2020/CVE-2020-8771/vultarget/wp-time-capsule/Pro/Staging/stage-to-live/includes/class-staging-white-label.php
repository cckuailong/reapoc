<?php

class Wptc_White_Label_Staging {

	protected $config;
	protected $settings;
	protected $wptc_base_file;
	protected $wptc_readme_file;
	protected $app_functions;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->wptc_base_file = 'wp-time-capsule-staging/wp-time-capsule-staging.php';
		$this->wptc_readme_file = 'wp-time-capsule-staging/readme.txt';
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
	}

	public function admin_actions(){
		$settings = $this->get_settings();

		wptc_log($settings,'-----------$admin_actions---white label-settings------------');

		if(empty($settings) || empty($settings->wl_select_action)){
			return false;
		}

		if ($settings->wl_select_action == 'normal') {
			return false;
		}

		if($this->validate_users_access_wptc() === 'authorized') {

			wptc_log(array(),'-----------dont apply whitelabel----------------');

			return false;
		}

		//Hiding the view details alone.
		add_filter('plugin_row_meta', array($this, 'replace_row_meta'), 10, 2);

		//Hiding the wptc update details.
		add_filter('site_transient_update_plugins', array($this, 'site_transient_update_plugins'), 10, 2);

		//Modifying the link available in plugin's view version details link.
		add_filter('admin_url', array($this, 'user_admin_url'), 10, 2);

		//Replacing name and other details.
		add_filter('all_plugins', array($this, 'replace_details'));
	}

	public function staging_page() {
		$stage_to_live = $this->stage_to_live;
		include_once 'views/wp-time-capsule-staging.php';
	}

	public function get_settings(){
		if ($this->settings) return $this->settings;

		$data = $this->get_live_site_option('white_lable_details');

		if (empty($data)) return false;

		$this->settings = unserialize($data);

		if (empty($this->settings)) {
			$this->settings = array();
		}

		return $this->settings;
	}

	public function get_live_site_white_label_options()	{
		
	}

	public function get_live_site_option($key){
		$live_site_prefix = $this->config->get_option('s2l_live_db_prefix');

		if(empty($live_site_prefix)){

			wptc_log('', "-----mepty---s2l_live_db_prefix---get_live_site_option-----");

			return false;
		}

		global $wpdb;
		return $wpdb->get_var("SELECT value FROM ".$live_site_prefix."wptc_options WHERE name = '$key'");
	}

	public function replace_row_meta($links, $file) {
		//Hiding the view details alone.

		if($file == $this->wptc_base_file){
			if(!empty($links[2])){
				unset($links[2]);
			}
		}

		return $links;
	}

	public 	function site_transient_update_plugins($value){
		if(empty($value->response[$this->wptc_base_file]))	return $value;

		$settings = $this->get_settings();

		if( empty($settings) ) {
			return $value;
		}

		if(empty($settings->plugin_name)){
			return $value;
		}

		$file_traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$called_by_file = array_pop($file_traces);
		$called_by_file = basename($called_by_file['file']);
		if($called_by_file == "update-core.php"){
			//Hiding the updates available in updates dashboard section
			unset($value->response[$this->wptc_base_file]);
		} else if($called_by_file == "plugins.php"){
			//Hiding the updates available in plugins section
			$value->response[$this->wptc_base_file]->slug = $settings->plugin_name;
			$value->response[$this->wptc_base_file]->Name = $settings->plugin_name;
		}

		return $value;
	}

	public function user_admin_url($value, $path){
		if(strpos($path, 'plugin-install.php?tab=plugin-information&plugin') === false) return $value;

		$settings = $this->get_settings();

		if( empty($settings) ) {
			return $value;
		}

		if(empty($settings->plugin_name)){
			return $value;
		}

		$search_str = 'plugin-install.php?tab=plugin-information&plugin=' . $settings->plugin_name . '&section=changelog';

		if(strpos($path, $search_str) === false){
			return $value;
		}

		//Modifying the link available in plugin's view version details link.
		$return_var = plugins_url( '/'.$this->wptc_readme_file ) . 'TB_iframe=true&width=600&height=550';

		return  $return_var;
	}

	public function replace_details($all_plugins){

		$settings = $this->get_settings();

		if(empty($settings)){
			return $all_plugins;
		}

		if ($settings->wl_select_action === 'change_details') {
			$all_plugins[$this->wptc_base_file]['Name']        = $settings->plugin_name . ' Staging';
			$all_plugins[$this->wptc_base_file]['Title']       = $settings->plugin_name;
			$all_plugins[$this->wptc_base_file]['Description'] = $settings->plugin_description;
			$all_plugins[$this->wptc_base_file]['AuthorURI']   = $settings->author_url;
			$all_plugins[$this->wptc_base_file]['Author']      = $settings->author_name;
			$all_plugins[$this->wptc_base_file]['AuthorName']  = $settings->author_name;
			$all_plugins[$this->wptc_base_file]['PluginURI']   = '';
		}

		if($settings->wl_select_action == 'hide_details'){

			return $all_plugins;
		}

		// no hide details in staging white label

		if($settings->wl_select_action !== 'hide_details'){

			return $all_plugins;
		}

		if (is_multisite()) {
			if (!empty($all_plugins[$this->wptc_base_file])) {
				unset($all_plugins[$this->wptc_base_file]);
			}
		} else {
			$activated_plugins = get_option('active_plugins');

			if (!$activated_plugins){
				return $all_plugins;
			}

			if(in_array($this->wptc_base_file,$activated_plugins)){
				unset($all_plugins[$this->wptc_base_file]);
			}
		}

		return $all_plugins;
	}

	public function remove_updates($value){

		if(isset($value->response)){
			unset($value->response[$this->wptc_base_file]);
		}

		if(isset($value->updates)){
			unset($value->updates[$this->wptc_base_file]);
		}

		return $value;
	}

	public function replace_action_links($links, $file){

		//Hiding edit on plugins page.
		if(!empty($links['edit'])){
			unset($links['edit']);
		}
		return $links;
	}

	public function update_settings($obj){

		if (!isset($obj->white_label_settings)) {
			return false;
		}

		$default = (object) array(
				'wl_select_action'   => 'normal',
				'plugin_name'        => '',
				'author_name'        => '',
				'author_url'         => '',
				'plugin_description' => '',
				'hide_updates'	     => '',
				'hide_edit'	         => '',
				'allowed_pages'	     => '',
				'admin_username'	 => '',
				'additional_control' => '',
		);

		if ($obj->white_label_settings == false || empty($obj->white_label_settings)) {
			return $this->config->set_option('white_lable_details', serialize($default));
		}

		$this->config->set_option('white_lable_details', serialize($obj->white_label_settings));
	}

	public function validate_users(){

		if (!isset($_GET['wptc_wl_code'])) return false;

		$wptc_wl_code = base64_decode(urldecode($_GET['wptc_wl_code']));

		wptc_log($wptc_wl_code, '--------$wptc_wl_code--------');

		if (empty($wptc_wl_code)) return false;

		if (md5($this->config->get_option('uuid')) != $wptc_wl_code) return false;

		WPTC_Base_Factory::get('Wptc_App_Functions')->set_user_to_access();
	}

	public function is_whitelabling_override(){
		$settings = $this->get_settings();

		if (empty($settings) || empty($settings->wl_select_action) || $settings->wl_select_action == 'normal') {
			return true;
		}

		return ($this->validate_users_access_wptc() === 'authorized') ? true : false;
	}

	public function validate_users_access_wptc(){
		$this->validate_users();
		$settings = $this->get_settings();

		//if settings empty then user not enabled whitelabling so authorize all users
		if (empty($settings) || empty($settings->wl_select_action) ){
			wptc_log(array(),'-----------Authorized 1----------------');
			return 'authorized';
		}

		//Whitelabling is not restricted so authorize all users
		if ($settings->wl_select_action == 'normal'){
			wptc_log(array(),'-----------Authorized 2----------------');
			return 'authorized';
		}

		$username = $this->app_functions->get_current_user_meta('user_login');

		if (empty($username)) {
			return 'not_authorized';
		}

		if ( !empty($settings->admin_username) && $settings->admin_username === $username) {
			// wptc_log(array(),'-----------Authorized 3----------------');
			return 'authorized';
		}

		$user_id = $this->app_functions->get_current_user_meta('ID');

		if (empty($user_id)) {
			return 'not_authorized';
		}

		$allowed_user_id = isset($_COOKIE['wptc_wl_allowed_user_id']) ? $_COOKIE['wptc_wl_allowed_user_id'] : false ;

		if (empty($allowed_user_id)) {
			return 'not_authorized';
		}

		return ($user_id != $allowed_user_id) ? 'not_authorized' : 'authorized';
	}

	public function is_whitelabling_active(){
		$settings = $this->get_settings();

		// wptc_log($settings,'-----------is_whitelabling_active $settings----------------');

		if (empty($settings) || empty($settings->wl_select_action) || $settings->wl_select_action == 'normal') {
			return false;
		}

		return true;
	}

	public function modify_settings($settings){
		$settings['white_label_settings'] = $this->get_settings();
		return $settings;
	}
}
