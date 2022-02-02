<?php

class Wptc_Vulns extends WPTC_Privileges{
	protected	$config,
				$cron_server_curl,
				$update_common,
				$app_functions,
				$update_stats;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Vulns_Config');
		$this->cron_server_curl = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->update_common = WPTC_Base_Factory::get('Wptc_Update_Common');
	}

	public function init(){
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function init_get_updates_stats(){
		include_once ( WPTC_CLASSES_DIR . '/getUpdateStats.php' );
		$this->update_stats = new Wptc_Update_Stats();
	}

	private function is_switch_on(){
		return true;
	}

	public function check_vulns_updates(){
		$this->config->set_option('run_vulns_checker', true);

		if( !$this->is_vulns_enabled() ){
			wptc_log(array(),'-----------Vulsn disabled----------------');
			return false;
		}


		wptc_log(array(),'----------Set vulns checker-----------------');

		send_response_wptc('vulnerable checker set, Ping me to start vulnerable updates', 'Backup');
	}

	public function run_vulns_check(){
		$this->config->set_option('run_vulns_checker', false);

		//if vulns not enabled in the settings then do not run vulns updates
		if(!$this->is_vulns_enabled()){
			return false;
		}


		$this->init_get_updates_stats();

		$upgradable_plugins = $this->get_upgradable_plugins();
		$upgradable_themes = $this->get_upgradable_themes();
		$upgradable_core_arr = $this->get_upgradable_core();

		$upgradable_core = $upgradable_core_arr['update_data'];
		$upgradable_core_deep_data = $upgradable_core_arr['deep_data'];

		$post_arr = array(
			'plugins_data' => $upgradable_plugins,
			'themes_data' => $upgradable_themes,
			'core_data' => $upgradable_core,
			);

		$raw_response = $this->cron_server_curl->do_call('run-vulns-check', $post_arr);

		if (empty($raw_response)) {
			return false;
		}

		$response = json_decode($raw_response);
		$response = $response->vulns_result;

		$plugins = $themes = $core = array();

		if (!empty($response->affectedPlugins)) {
			$plugins = (array) $response->affectedPlugins;
		}

		if(!empty($response->affectedThemes)){
			$themes = (array) $response->affectedThemes;
		}

		if(!empty($response->affectedCores)){
			$core = (array) $response->affectedCores;
		}

		$update_plugins = $this->purify_plugins_for_update($plugins, $upgradable_plugins);
		$update_themes = $this->purify_themes_for_update($themes);
		$update_core = $this->purify_core_for_update($core, $upgradable_core_deep_data);

		$this->prepare_bulk_upgrade_structure($update_plugins, $update_themes, $update_core);
	}

	private function prepare_bulk_upgrade_structure($upgrade_plugins, $upgrade_themes, $wp_upgrade){
		$final_upgrade_details = array();

		if (!empty($upgrade_plugins)) {
			$final_upgrade_details['upgrade_plugins']['update_items'] = $upgrade_plugins;
			$final_upgrade_details['upgrade_plugins']['updates_type'] = 'plugin';
			$final_upgrade_details['upgrade_plugins']['is_auto_update'] = '0';
		}

		if (!empty($upgrade_themes)) {
			$final_upgrade_details['upgrade_themes']['update_items'] = $upgrade_themes;
			$final_upgrade_details['upgrade_themes']['updates_type'] = 'theme';
			$final_upgrade_details['upgrade_themes']['is_auto_update'] = '0';

		}

		if (!empty($wp_upgrade)) {
			$final_upgrade_details['wp_upgrade']['update_items'] = $wp_upgrade;
			$final_upgrade_details['wp_upgrade']['updates_type'] = 'core';
			$final_upgrade_details['wp_upgrade']['is_auto_update'] = '0';
		}

		//Translations does not have vulns updates
		/*if (!empty($upgrade_translations)) {
			$final_upgrade_details['upgrade_translations']['update_items'] = $upgrade_translations;
			$final_upgrade_details['upgrade_translations']['updates_type'] = 'translation';
			$final_upgrade_details['upgrade_translations']['is_auto_update'] = '0';
		}*/

		if (empty($final_upgrade_details)) {
			return false;
		}

		wptc_log($final_upgrade_details, '--------$final_upgrade_details--------');

		$this->bulk_update_request($final_upgrade_details);
		$this->config->set_option('is_bulk_update_request', true);
		$this->config->set_option('single_upgrade_details', false);
		$this->config->set_option('is_vulns_updates', true);
		start_fresh_backup_tc_callback_wptc('manual');
	}

	private function bulk_update_request($bulk_update_request){
		wptc_log($bulk_update_request, '--------$bulk_update_request--------');
		if (empty($bulk_update_request)) {
			return $this->config->set_option('bulk_update_request', false);
		}

		$this->config->set_option('bulk_update_request', serialize($bulk_update_request));
	}

	private function purify_plugins_for_update($plugins_data, $upgradable_plugins){

		$plugins = array();

		if (empty($plugins_data)) {
			return $plugins;
		}

		foreach ($plugins_data as $key => $plugin_data) {
			$plugins[$upgradable_plugins[$key]['path']] = $upgradable_plugins[$key]['version'];
		}

		return $plugins;

	}

	private function purify_themes_for_update($themes_data){

		$themes = array();

		if (empty($themes_data)) {
			return $themes;
		}

		foreach ($themes_data as $key => $theme_data) {
			$themes[] = $key;
		}

		return $themes;

	}

	private function purify_core_for_update($core_data, $upgradable_core_deep_data){
		if (empty($core_data)) {
			return array();
		}

		return $upgradable_core_deep_data;
	}

	public function get_upgradable_plugins() {

		$plugins_meta = $this->update_stats->get_upgradable_plugins();

		$upgradable_plugins = array();

		if (empty($plugins_meta)) {
			return $upgradable_plugins;
		}

		foreach ($plugins_meta as $plugin_meta) {
			$path = $plugin_meta->file;
			$slug = $this->app_functions->shortern_plugin_slug($path);
			$upgradable_plugins[$slug] = array(
				'path' => $path,
				'version' => $plugin_meta->new_version,
				'slug' => $slug,
			);
		}

		return $upgradable_plugins;
	}

	public function get_upgradable_themes() {

		$themes_meta = $this->update_stats->get_upgradable_themes();


		$upgradable_themes = array();

		if (empty($themes_meta)) {
			return $upgradable_themes;
		}

		foreach ($themes_meta as $theme_meta) {
			$upgradable_themes[$theme_meta['name']] = array(
				'slug' => $theme_meta['theme_tmp'],
				'version' => $theme_meta['new_version'],
			);
		}

		return $upgradable_themes;
	}

	public function get_upgradable_core() {

		$upgrade_core = array(
				'update_data' => array(),
				'deep_data' => '',
			);

		$core = $this->update_stats->get_core_update();

		if ( empty($core->current) ) {
			return false;
		}

		$wp_version = WPTC_Base_Factory::get('Wptc_App_Functions')->get_wp_core_version($hard_refresh = true );

		if ($core->response == "development" || version_compare($wp_version, $core->current, '<')) {
			$core->current_version                    = $wp_version;
			$upgrade_core['update_data'][$wp_version] = array('Version' => $wp_version);
			$upgrade_core['deep_data']                = $core;
		}

		return $upgrade_core;
	}

	public function is_vulns_enabled(){
		$settings = $this->get_vulns_settings();
		return ( !empty($settings['status']) && $settings['status'] === 'yes') ? true : false;

	}

	public function get_vulns_settings(){
		$settings_serialized = $this->config->get_option('vulns_settings');
		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);

		return empty($settings) ? array() : $settings;
	}

	public function get_enabled_themes(){

		$all_themes = $this->app_functions->get_all_themes_data();

		$themes = array();

		$vulns_settings = $this->get_vulns_settings();

		$excluded_themes = empty($vulns_settings['themes']['excluded']) ? array() : unserialize($vulns_settings['themes']['excluded']);

		if (empty($excluded_themes)) {
			$excluded_themes = array();
		}

		$i=0;
		foreach ($all_themes as $slug => $theme) {
			$themes[$i]['slug'] = $slug;
			$themes[$i]['name'] = $theme->get('Name');
			$themes[$i]['selected'] = (!in_array($slug, $excluded_themes)) ?  true : false;
			$i++;
		}

		return $themes;
	}

	public function get_enabled_plugins(){

		$all_plugins = $this->app_functions->get_all_plugins_data();

		$plugins = array();
		$vulns_settings = $this->get_vulns_settings();
		$excluded_plugins = empty($vulns_settings['plugins']['excluded']) ? array() : unserialize($vulns_settings['plugins']['excluded']);

		$i=0;
		foreach ($all_plugins as $slug => $plugin) {
			$plugins[$i]['slug'] = $slug;
			$plugins[$i]['name'] = $plugin['Name'];
			$plugins[$i]['selected'] = (!in_array($slug, $excluded_plugins)) ?  true : false;
			$i++;
		}

		return $plugins;
	}

	public function update_vulns_settings($options){
		wptc_log($options, '---------$options------------');
		$settings['status'] = empty($options['vulns_wptc_setting']) ? "no" : $options['vulns_wptc_setting'];
		$settings['core']['status'] = empty($options['wptc_vulns_core']) ? 0 : 1;
		$settings['themes']['status'] = empty($options['wptc_vulns_themes']) ? 0 : 1;
		$settings['plugins']['status'] = empty($options['wptc_vulns_plugins']) ? 0 : 1;

		$vulns_plugins_included =  !empty($options['vulns_plugins_included']) ? $options['vulns_plugins_included'] : array();

		$plugin_include_array = array();

		if (!empty($vulns_plugins_included)) {
			$plugin_include_array = explode(',', $vulns_plugins_included);
			$plugin_include_array = !empty($plugin_include_array) ? $plugin_include_array : array() ;
		}

		wptc_log($plugin_include_array, '--------$plugin_include_array--------');

		$included_plugins = $this->filter_plugins($plugin_include_array);

		wptc_log($included_plugins, '--------$included_plugins--------');

		$settings['plugins']['excluded'] = serialize($included_plugins);


		$vulns_themes_included =  !empty($options['vulns_themes_included']) ? $options['vulns_themes_included'] : array();
		$themes_include_array  = array();

		if (!empty($vulns_themes_included)) {
			$themes_include_array = explode(',', $vulns_themes_included);
		}

		$included_themes = $this->filter_themes($themes_include_array);
		$settings['themes']['excluded'] = serialize($included_themes);

		$result = $this->config->set_option('vulns_settings', serialize($settings));

		do_action('send_ptc_list_to_server_wptc', time());
	}

	private function filter_plugins($included_plugins){
		$plugins_data = $this->app_functions->get_all_plugins_data($specific = true, $attr = 'slug');
		$not_included_plugin = array_diff($plugins_data, $included_plugins);
		wptc_log($plugins_data, '--------$plugins_data--------');
		wptc_log($not_included_plugin, '--------$not_included_plugin--------');
		return $not_included_plugin;
	}

	private function filter_themes($included_themes){
		$themes_data = $this->app_functions->get_all_themes_data($specific = true, $attr = 'slug');
		$not_included_theme = array_diff($themes_data, $included_themes);
		wptc_log($themes_data, '--------$themes_data--------');
		wptc_log($not_included_theme, '--------$not_included_theme--------');
		return $not_included_theme;
	}

	public function get_format_vulns_settings_to_send_server(){
		$vulns_settings = $this->get_vulns_settings();

		$excluded_themes = empty($vulns_settings['themes']['excluded']) ? array() : unserialize($vulns_settings['themes']['excluded']);
		$excluded_plugins = empty($vulns_settings['plugins']['excluded']) ? array() : unserialize($vulns_settings['plugins']['excluded']);

		return array(
			'status' => ($vulns_settings['status'] === 'yes' ) ? true : false,
			'is_core_exclude' => empty($vulns_settings['core']['status'] ) ? false : true,
			'themes_to_exclude' => $excluded_themes,
			'plugins_to_exclude' => $excluded_plugins,
			);
	}

	public function update_bulk_settings($server_data){
		wptc_log(func_get_args(),'-----vulns------update_bulk_settings---------------');
		if (!isset($server_data->plugins_to_exclude)) {
			return ;
		}

		$current_settings = $this->get_vulns_settings();

		wptc_log($current_settings,'-----------$current_settings----------------');

		$arr_plugins_to_exclude = array();
		if( empty($server_data->plugins_to_exclude) 
			|| is_array($server_data->plugins_to_exclude) ){
			$arr_plugins_to_exclude = WPTC_Base_Factory::get('Wptc_App_Functions')->append_slugs_plugins($server_data->plugins_to_exclude);
		}
		$current_settings['plugins']['excluded'] = serialize($arr_plugins_to_exclude);

		$arr_themes_to_exclude = array();
		if( empty($server_data->themes_to_exclude) 
			|| is_array($server_data->themes_to_exclude) ){
			$arr_themes_to_exclude = $server_data->themes_to_exclude;
		}
		$current_settings['themes']['excluded']  = serialize($arr_themes_to_exclude);
		$current_settings['core']['status']      = empty($server_data->is_core_exclude) ? 0 : 1;

		wptc_log($current_settings,'-----------$current_settings----------------');
		$this->config->set_option('vulns_settings', serialize($current_settings));
	}
}
