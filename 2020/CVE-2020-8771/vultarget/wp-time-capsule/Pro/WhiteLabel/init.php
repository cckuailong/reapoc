<?php

class Wptc_White_Label extends WPTC_Privileges {

	protected $config;
	protected $settings;
	protected $wptc_base_file;
	protected $wptc_readme_file;
	protected $app_functions;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_White_Label_Config');
		$this->wptc_base_file = 'wp-time-capsule/wp-time-capsule.php';
		$this->wptc_readme_file = 'wp-time-capsule/readme.txt';
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
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

	public function get_settings(){
		if ($this->settings) return $this->settings;

		$data = $this->config->get_option('white_lable_details');

		if (empty($data)) return false;

		$this->settings = unserialize($data);

		if (empty($this->settings)) {
			$this->settings = array();
		}

		return $this->settings;
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

		wptc_log($settings,'-----------$replace_details----------------');

		if(empty($settings)){
			return $all_plugins;
		}

		if ($settings->wl_select_action === 'change_details') {
			$all_plugins[$this->wptc_base_file]['Name']        = $settings->plugin_name;
			$all_plugins[$this->wptc_base_file]['Title']       = $settings->plugin_name;
			$all_plugins[$this->wptc_base_file]['Description'] = $settings->plugin_description;
			$all_plugins[$this->wptc_base_file]['AuthorURI']   = $settings->author_url;
			$all_plugins[$this->wptc_base_file]['Author']      = $settings->author_name;
			$all_plugins[$this->wptc_base_file]['AuthorName']  = $settings->author_name;
			$all_plugins[$this->wptc_base_file]['PluginURI']   = '';
		}

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

	public function mu_plugin_list_filter($previousValue, $type) {

		if($this->is_whitelabling_override() ||$this->validate_users_access_wptc() === 'authorized') {
			return $previousValue;
		}

		$settings = $this->get_settings();

		if(empty($settings) || $settings->wl_select_action == 'normal'){
			return $previousValue;
		}

		// Drop-in's are filtered after MU plugins.
		if ($type !== 'dropins') {
			return $previousValue;
		}

		if (!empty($previousValue['wp-time-capsule/wp-time-capsule.php'])) {
			return $previousValue;
		}

		$settings = $this->get_settings();

		if( empty($settings) ){
			return $previousValue;
		}

		if ( $settings->wl_select_action === 'hide_details' ) {
			unset($GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']);
			return ;
		}

		if ( $settings->wl_select_action === 'change_details' ) {
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['Name']        = $settings->plugin_name;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['Title']       = $settings->plugin_name;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['Description'] = $settings->plugin_description;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['AuthorURI']   = $settings->author_url;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['Author']      = $settings->author_name;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['AuthorName']  = $settings->author_name;
			$GLOBALS['plugins']['mustuse']['0-mu-wp-time-capsule.php']['PluginURI']   = '';
		}

		return $previousValue;
	}

	public function is_whitelabling_active(){
		$settings = $this->get_settings();

		// wptc_log($settings,'-----------is_whitelabling_active $settings----------------');

		if (empty($settings) || empty($settings->wl_select_action) || $settings->wl_select_action == 'normal') {
			return false;
		}

		return true;
	}

	public function is_general_tab_allowed()
	{
		$settings = $this->get_settings();

		if (empty($settings->allowed_pages)) {
			return false;
		}

		if(in_array('general', $settings->allowed_pages)){
			return true;
		}

		return false;
	}

	public function is_staging_tab_allowed()
	{
		$settings = $this->get_settings();

		if(!empty($settings->wl_select_action) && $settings->wl_select_action == 'normal'){

			return true;
		}

		$is_validated_user = $this->validate_users_access_wptc();
		if($is_validated_user == 'authorized'){

			wptc_log($is_validated_user, "--------is_validated_user------sdasdasd--");

			return true;
		}

		if(!empty($settings->wl_select_action) && $settings->wl_select_action == 'hide_details'){

			return false;
		}

		if (empty($settings->allowed_pages)) {
			return false;
		}

		if(in_array('staging', $settings->allowed_pages)){
			return true;
		}

		return false;
	}

	public function is_backup_tab_allowed_with_admin_user_check()
	{
		$settings = $this->get_settings();

		if(!empty($settings->wl_select_action) && $settings->wl_select_action == 'normal'){

			return true;
		}

		$is_validated_user = $this->validate_users_access_wptc();
		if($is_validated_user == 'authorized'){

			wptc_log($is_validated_user, "--------is_validated_user------sdasdasd--");

			return true;
		}

		if(!empty($settings->wl_select_action) && $settings->wl_select_action == 'hide_details'){

			return false;
		}

		if (empty($settings->allowed_pages)) {
			return false;
		}

		if(in_array('trigger_backup', $settings->additional_control)){
			return true;
		}

		return false;
	}

	public function add_pages(){

		if ($this->is_whitelabling_override()) {
			return ;
		}

		$settings = $this->get_settings();

		if (empty($settings) || $settings->wl_select_action === 'hide_details') {
			return ;
		}

		if (empty($settings->allowed_pages)) {
			return ;
		}

		$page_details = array(
				'backup' => array(
					'title'    => 'Backups',
					'slug'     => 'wp-time-capsule-monitor',
					'callback' => 'wordpress_time_capsule_monitor',
					'level'    => 'activate_plugins',
				),
				'settings' => array(
					'title'    => 'Settings',
					'slug'     => 'wp-time-capsule-settings',
					'callback' => 'wptimecapsule_settings_hook',
					'level'    => 'activate_plugins',
				),
				'activitylog' => array(
					'title'    => 'Activity Log',
					'slug'     => 'wp-time-capsule-activity',
					'callback' => 'wordpress_time_capsule_activity',
					'level'    => 'activate_plugins',
				)
		);

		if (count($settings->allowed_pages) === 1) {
			if ($settings->allowed_pages[0] === 'staging') {
				do_action('add_additional_sub_menus_wptc_h', $settings->plugin_name, 'main');
				return ;
			}

			$menu = $page_details[$settings->allowed_pages[0]];
			$text = __($settings->plugin_name, 'wptc');
			add_menu_page($text, $text, $menu['level'], $menu['slug'], $menu['callback'], 'dashicons-cloud', '80.0564');
			return ;
		}

		$counter = 0;
		$main_menu = '';

		$allowed_pages = $this->order_pages($settings->allowed_pages);

		foreach ($allowed_pages as $key => $page) {

			if($page == 'general'){
				$text = __('Initial Setup', 'wptc');
				add_submenu_page(null, $text, $text, 'activate_plugins', 'wp-time-capsule', 'wordpress_time_capsule_admin_menu_contents');
				continue;
			}

			if ($counter++ < 1) {

				if (!isset($page_details[$page])) {
					do_action('add_additional_sub_menus_wptc_h', $settings->plugin_name, 'main');
					$main_menu = 'wp-time-capsule-staging-options';
					continue;
				}

				$menu = $page_details[$page];
				$text = __($settings->plugin_name, 'wptc');
				add_menu_page($text, $text, $menu['level'], $menu['slug'], $menu['callback'], 'dashicons-cloud', '80.0564');
				$main_menu = $menu['slug'];



				continue;
			}

			if (!isset($page_details[$page])) {
				do_action('add_additional_sub_menus_wptc_h', '');
				continue;
			}

			$menu = $page_details[$page];
			$text = __($menu['title'], 'wptc');
			add_submenu_page($main_menu, $text, $text, $menu['level'], $menu['slug'], $menu['callback']);
		}
	}

	private function order_pages($allowed_pages){

		if(empty($allowed_pages)){
			return array();
		}

		$sorted = array();

		if(in_array('general', $allowed_pages)){
			array_push($sorted, 'general');
		}

		if(in_array('backup', $allowed_pages)){
			array_push($sorted, 'backup');
		}

		if(in_array('staging', $allowed_pages)){
			array_push($sorted, 'staging');
		}

		if(in_array('settings', $allowed_pages)){
			array_push($sorted, 'settings');
		}

		if(in_array('activitylog', $allowed_pages)){
			array_push($sorted, 'activitylog');
		}

		return $sorted;
	}

	public function hide_this_option($type){

		if ($this->is_whitelabling_override()) {
			return false;
		}

		$settings = $this->get_settings();

		if (empty($settings->additional_control)) {
			return true;
		}

		if (in_array($type, $settings->additional_control)) {
			return false;
		}

		return true;
	}

	public function modify_settings($settings){
		$settings['white_label_settings'] = $this->get_settings();
		return $settings;
	}
}
