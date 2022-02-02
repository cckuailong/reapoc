<?php

class Wptc_Backup_Before_Auto_Update_Settings {
	protected $config,
			  $logger,
			  $update_common;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Backup_Before_Update_Config');
		$this->logger = WPTC_Factory::get('logger');
		$this->update_common = WPTC_Base_Factory::get('Wptc_Update_Common');
	}

	public function get_auto_update_settings(){
		$settings_serialized = $this->config->get_option('wptc_auto_update_settings');
		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);
		return $settings['update_settings'];
	}

	public function get_auto_update_settings_html($bbu_setting){
		$auto_updater_settings = $this->get_auto_update_settings();
		// wptc_log($auto_updater_settings, '---------$auto_updater_settings------------');

		$enable_auto_update_wptc = $disable_auto_update_wptc = $show_options = $schedule_time_show_status = '';

		if ($auto_updater_settings['status'] == 'yes') {
			$enable_auto_update_wptc  = 'checked';
			$show_options             = 'display:block';
		} else {
			$disable_auto_update_wptc = 'checked';
			$show_options             = 'display:none';
		}

		$core_major               = $auto_updater_settings['core']['major']['status'];
		$core_major_checked       = ($core_major) ? 'checked="checked"' : '';

		$core_minor               = $auto_updater_settings['core']['minor']['status'];
		$core_minor_checked       = ($core_minor) ? 'checked="checked"' : '';

		$plugins                  = $auto_updater_settings['plugins']['status'];
		$plugins_checked          = ($plugins) ? 'checked="checked"'    : '';

		$themes                   = $auto_updater_settings['themes']['status'];
		$themes_checked           = ($themes) ? 'checked="checked"'     : '';

		$schedule_enabled   	  = !empty($auto_updater_settings['schedule']['enabled']) ? $auto_updater_settings['schedule']['enabled'] : '' ;
		$set_schedule_enabled     = ($schedule_enabled) ? 'checked="checked"' : '';

		$schedule_time 		  = !empty($auto_updater_settings['schedule']['time']) ? $auto_updater_settings['schedule']['time'] : '' ;

		$inc_plugins_automatically 		  = !empty($auto_updater_settings['include_automatically']['plugins']) ? 'fancytree-selected' : '' ;
		$inc_themes_automatically 		  = !empty($auto_updater_settings['include_automatically']['themes']) ? 'fancytree-selected' : '' ;

		if (!$schedule_enabled) {
			$schedule_time_show_status = 'display:none';
		}


		$style = '';
		if ($bbu_setting !== 'always') {
			$style ="";
		}

		$header = '
			<tr '.$style.' id="auto_update_settings_wptc" valign="top">
				<th scope="row"> '.__( 'Enable Auto-Updates', 'wp-time-capsule' ).'<br> (For WP, Themes, Plugins)</th>
				<td>
					<fieldset>
						<label title="Yes">
							<input name="auto_update_wptc_setting"  type="radio" id="enable_auto_update_wptc" '.$enable_auto_update_wptc.' value="yes">
							<span class="">
								'.__( 'Yes', 'wp-time-capsule' ).'
							</span>
						</label>
						<label title="No" style="margin-left: 10px !important;">
							<input name="auto_update_wptc_setting" type="radio" id="disable_auto_update_wptc" '.$disable_auto_update_wptc.' value="no">
							<span class="">
								'.__( 'No', 'wp-time-capsule' ).'
							</span>
						</label>
						<p class="description">'.__( 'The site is automatically backed up before each auto-update', 'wp-time-capsule' ).'</p>
					</fieldset>
					<br>
					<fieldset style="padding-top:15px; '.$show_options.'" class="enable_auto_update_options_wptc" >
					<fieldset style="'.$show_options.'" class="enable_auto_update_options_wptc">
						<input type="checkbox" id="wptc_auto_update_schedule_enabled" name="wptc_auto_update_schedule_enabled" value="1" ' . $set_schedule_enabled . '>
						<label for="wptc_auto_update_schedule_enabled">'.__( 'Set update time', 'wp-time-capsule' ).'</label>
						<select   style="margin-left: 5px; ' . $schedule_time_show_status . '" name="wptc_auto_update_schedule_time" id="wptc_auto_update_schedule_time">
							' . WPTC_Base_Factory::get('Wptc_Settings')->get_schedule_times_div_wptc($type = 'auto_update', $schedule_time) . '
						</select>
					</fieldset>
					<br>
					<fieldset style="'.$show_options.'" class="enable_auto_update_options_wptc">
						<p><div class="automatic-updater-core-options">'.__( 'Update WordPress Core automatically?', 'wp-time-capsule' ).'</p>
					<fieldset style="margin-left: 30px;">';

		$core_major = '<input type="checkbox" id="wptc_auto_core_major" name="wptc_auto_core_major" value="1" '.$core_major_checked.'>
							<label for="wptc_auto_core_major">'.__( 'Major versions', 'wp-time-capsule' ).'</label><br>';

		$core_minor = '<input type="checkbox" id="wptc_auto_core_minor" name="wptc_auto_core_minor" value="1" '.$core_minor_checked.'>
							<label for="wptc_auto_core_minor">'.__( 'Minor and security versions <strong>(Strongly Recommended)', 'wp-time-capsule' ).'</strong></label>
						</fieldset>	</div>';

		$plugins = '<br><p>
						<input type="checkbox" id="wptc_auto_plugins" name="wptc_auto_plugins" value="1" '.$plugins_checked.'>
						<label for="wptc_auto_plugins">'.__( 'Update your plugins automatically?', 'wp-time-capsule' ).'
						<div id="wptc-select-all-plugins-au" style="display:none; cursor: pointer; width: 100px; margin: 7px 14px 10px 19px; float: left">
							<span class="fancytree-checkbox"></span>
							<a>Select All</a>
						</div>
						<div id="wptc-include-new-plugins-au" class=" ' . $inc_plugins_automatically . ' " style="cursor: pointer;width: 270px;margin: 7px 14px 10px -16px;float: left; display: none">
							<span class="fancytree-checkbox"></span>
							<a>Include new Plugins automatically</a>
						</div>
							<div style="display: none; width:400px" id="wptc_auto_update_plugins_dw"></div>
							<input style="display: none;" type="hidden" id="auto_include_plugins_wptc" name="auto_include_plugins_wptc"/>
						</label>
					</p>';

		$themes = '<p>
						<input type="checkbox" id="wptc_auto_themes" name="wptc_auto_themes" value="1" '.$themes_checked.'>
							<label for="wptc_auto_themes">	'.__( 'Update your themes automatically?', 'wp-time-capsule' ).'
							<div id="wptc-select-all-themes-au" style="display:none; cursor: pointer; width: 100px; margin: 7px 14px 10px 19px; float: left">
								<span class="fancytree-checkbox"></span>
								<a>Select All</a>
							</div>
							<div id="wptc-include-new-themes-au" class=" ' . $inc_themes_automatically . ' " style="cursor: pointer;width: 270px;margin: 7px 14px 10px -16px;float: left; display: none">
								<span class="fancytree-checkbox"></span>
								<a>Include new Themes automatically</a>
							</div>
								<div style="display: none; width:400px" id="wptc_auto_update_themes_dw"></div>
								<input style="display: none;" type="hidden" id="auto_include_themes_wptc" name="auto_include_themes_wptc"/>
							</label>
					</p><fieldset></td></tr>';

		return $header . $core_major . $core_minor . $plugins . $themes;
	}

	public function update_auto_update_settings($options){
		$settings['update_settings']['status']                    = empty($options['auto_update_wptc_setting']) ? "no" : $options['auto_update_wptc_setting'];
		$settings['update_settings']['schedule']['enabled'] 	  = empty($options['schedule_enabled']) ? 0 : 1;
		$settings['update_settings']['schedule']['time']   		  = empty($options['schedule_time']) ? '' : $options['schedule_time'];
		$settings['update_settings']['core']['major']['status']   = empty($options['auto_updater_core_major']) ? 0 : 1;
		$settings['update_settings']['core']['minor']['status']   = empty($options['auto_updater_core_minor']) ? 0 : 1;
		$settings['update_settings']['themes']['status']          = empty($options['auto_updater_themes']) ? 0 : 1;
		$settings['update_settings']['plugins']['status']         = empty($options['auto_updater_plugins']) ? 0 : 1;
		$settings['update_settings']['include_automatically']     = empty($options['include_automatically']) ? array('plugins' => false, 'themes' => false) : $options['include_automatically'];

		if (!empty($options['auto_updater_plugins_included'])) {
			$plugin_include_array = explode(',', $options['auto_updater_plugins_included']);
			$settings['update_settings']['plugins']['included'] = serialize($plugin_include_array);
		}

		if (!empty($options['auto_updater_themes_included'])) {
			$themes_include_array = explode(',', $options['auto_updater_themes_included']);
			$settings['update_settings']['themes']['included'] = serialize($themes_include_array);
		}

		$result = $this->config->set_option('wptc_auto_update_settings', serialize($settings));

		// if ($options['schedule_time']) {
		push_settings_wptc_server("", "", $dont_reactivate = true);
		// }
	}

	private function get_slug_from_array($items){
		$temp = array();

		$app_function = WPTC_Base_Factory::get('Wptc_App_Functions');

		if (empty($items)) {
			return $temp;
		}

		foreach ($items as $item) {
			$result = $app_function->shortern_plugin_slug($item);

			if (!$result) {
				continue;
			}

			array_push($temp, $result);
		}

		return $temp;
	}

	public function save_default_settings(){
		$default_settings = array(
			'update_settings' => array(
				'status'   => 'no',
				'schedule' => array(
					'enabled'  => false,
					'time'     => '',
				),
				'core' => array (
					'major' => array('status' => 0 ),
					'minor' => array('status' => 1 ),
				),
				'themes'                => array('status'  => 0, 'included' => array()),
				'plugins'               => array('status'  => 0, 'included' => array()),
				'include_automatically' => array(
					'plugins' => 0,
					'themes' => 0
				)
			),

		);
		// wptc_log($default_settings, '---------$default_settings------------');
		$result = $this->config->set_option('wptc_auto_update_settings', serialize($default_settings));
	}

	public function get_installed_themes(){
		if (!function_exists('wp_get_themes')) {
			include_once ABSPATH . 'wp-includes/theme.php';
		}
		$all_themes = wp_get_themes();
		$themes = array();
		$auto_updater_settings = $this->get_auto_update_settings();
		$included_themes = empty($auto_updater_settings['themes']['included']) ? array() : unserialize($auto_updater_settings['themes']['included']);

		$i=0;
		foreach ($all_themes as $slug => $theme) {
			$themes[$i]['slug'] = $slug;
			$themes[$i]['name'] = $theme->get('Name');
			$themes[$i]['selected'] = (in_array($slug, $included_themes)) ?  true : false;
			$i++;
		}
		return WPTC_Base_Factory::get('Wptc_App_Functions')->fancytree_format($themes, 'themes');
	}

	public function get_installed_plugins(){
		if (!function_exists('get_plugins')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();
		$plugins = array();
		$auto_updater_settings = $this->get_auto_update_settings();
		$included_plugins = empty($auto_updater_settings['plugins']['included']) ? array() : unserialize($auto_updater_settings['plugins']['included']);

		$i=0;
		foreach ($all_plugins as $slug => $plugin) {
			$plugins[$i]['slug'] = $slug;
			$plugins[$i]['name'] = $plugin['Name'];
			$plugins[$i]['selected'] = (in_array($slug, $included_plugins)) ?  true : false;
			$i++;
		}
		return WPTC_Base_Factory::get('Wptc_App_Functions')->fancytree_format($plugins, 'plugins');
	}

	public function is_allowed_to_auto_update($update_details){
		$type = $this->parse_update_type_details($update_details);
		wptc_log($type, '---------$type------------');
		switch ($type) {
			case 'plugin':
				return $this->check_if_included_plugin($update_details);
			case 'theme':
				return $this->check_if_included_theme($update_details);
			case 'translation':
				return $this->check_if_included_translation($update_details);
			case 'core':
				return $this->check_if_included_core($update_details);
			default:
				return false;
		}
	}

	public function parse_update_type_details($update_details){
		wptc_log($update_details, '---------$update_details------------');
		$array = get_object_vars($update_details);
		$object_properties = array_keys($array);
		wptc_log($object_properties, '---------$object_properties------------');
		if (in_array('plugin', $object_properties)) {
			return 'plugin';
		}

		if (in_array('theme', $object_properties)) {
			return 'theme';
		}

		if (in_array('language', $object_properties)) {
			return 'translation';
		}

		return 'core';
	}

	public function check_if_included_plugin($update_details, $save = false){

		$auto_updater_settings = $this->get_auto_update_settings();

		if (!$auto_updater_settings['plugins']['status']) {
			wptc_log(array(), '---------Plugin update is off------------');
			return false;
		}

		$included_plugins = empty($auto_updater_settings['plugins']['included']) ? array() : unserialize($auto_updater_settings['plugins']['included']);

		if (empty($included_plugins)) {
			return false;
		}

		if (!in_array($update_details->file, $included_plugins)) {
			wptc_log(array(), '---------Plugin is not included------------');
			return false;
		}

		wptc_log(array(), '---------Plugin is included------------');

		if (!$save) {
			return true;
		}

		$this->config->set_option(
			'auto_update_queue',
			serialize(
				array(
					'item_type'   => 'plugin',
					'update_type' => 'autoupdate',
					'item'        => purify_plugin_update_data_wptc( array( $update_details->plugin ) )
				)
			)
		);

		return true;
	}

	public function check_if_included_theme($theme_slug, $save = false){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$auto_updater_settings = $this->get_auto_update_settings();

		if (!$auto_updater_settings['themes']['status']) {
			wptc_log(array(), '---------themes update is off------------');
			return false;
		}

		$included_themes = empty($auto_updater_settings['themes']['included']) ? array() : unserialize($auto_updater_settings['themes']['included']);

		wptc_log($included_themes, '---------$included_Theme------------');

		if ( empty($included_themes) ) {
			wptc_log(array(), '---------themes selected or name is empty------------');
			return false;
		}

		// $theme_info = wp_get_theme($update_details->theme);

		// if(version_compare($update_details->new_version, $theme_info->get( 'Version' ))  === 0){
		// 	wptc_log(array(), '--------Current and new version are same so rejecting backup------------');
		// 	return false;
		// }

		if (!in_array($theme_slug, $included_themes)) {
			wptc_log(array(), '---------Theme is not included------------');
			return false;
		}

		wptc_log(array(), '---------Theme is included------------');
		if (!$save) {
			return true;
		}

		$this->config->set_option(
			'auto_update_queue',
			serialize(
				array(
					'item_type'   => 'theme',
					'update_type' => 'autoupdate',
					'item'        => purify_theme_update_data_wptc( array($theme_slug) )
				)
			)
		);

		return true;
	}

	public function check_if_included_translation($update_details = false, $save = false){

		$auto_updater_settings = $this->get_auto_update_settings();

		if (!empty($auto_updater_settings['translation']) && !$auto_updater_settings['translation']['status']) {

			wptc_log(array(), '---------translation update is off------------');

			return false;
		}

		if (!$save) {
			return true;
		}

		$this->config->set_option(
			'auto_update_queue',
			serialize(
				array(
					'item_type'   => 'translation',
					'update_type' => 'autoupdate',
					'item'        => purify_translation_update_data_wptc( true )
				)
			)
		);

		return true;
	}

	public function check_if_included_core($update_details, $save = false){
		global $wp_version;

		if (!empty($update_details->response) && $update_details->response === 'development' ) {
			wptc_log(array(), '---------development version so return false-----------');
			return false;
		}

		if(empty($update_details->download)) {
			wptc_log(array(), "------download link not available so return false--------");
			return false;
		}

		$offered_ver           = $update_details->current;
		$current_version       = implode( '.', array_slice( preg_split( '/[.-]/', $wp_version  ), 0, 2 ) ); // x.y
		$new_version           = implode( '.', array_slice( preg_split( '/[.-]/', $offered_ver ), 0, 2 ) ); // x.y
		$auto_updater_settings = $this->get_auto_update_settings();

		//Minor version updates
		if ( version_compare( $new_version, $current_version, '=' ) ) {
			wptc_log(array(), '---------Minor version------------');
			if (!$auto_updater_settings['core']['minor']['status']) {
				wptc_log(array(),'-----------Minor updates disabled----------------');
				return false;
			}

			if (!$save) {
				return true;
			}

			$this->config->set_option(
				'auto_update_queue',
				serialize(
					array(
						'item_type'   => 'core',
						'update_type' => 'autoupdate',
						'item'        => purify_core_update_data_wptc(array($offered_ver))
					)
				)
			);

			return true;
		} else if ( version_compare( $new_version, $current_version, '>' ) ) { 	// Major version updates (3.7.0 -> 3.8.0 -> 3.9.1)
			wptc_log(array(), '---------Major version------------');
			if (!$auto_updater_settings['core']['major']['status']) {
				wptc_log(array(),'-----------Major updates disabled----------------');
				return false;
			}

			if (!$save) {
				return true;
			}

			$this->config->set_option(
				'auto_update_queue',
				serialize(
					array(
						'item_type'   => 'core',
						'update_type' => 'autoupdate',
						'item'        => purify_core_update_data_wptc(array($offered_ver))
					)
				)
			);

			return true;
		}
	}


	public function add_auto_update_queue($update_details) {
		wptc_log('Function :','---------'.__FUNCTION__.'-----------------');

		$type = $this->parse_update_type_details($update_details);
		wptc_log($type, '---------$type------------');
		switch ($type) {
			case 'plugin':
				if($this->check_if_included_plugin($update_details, $save = true));
			case 'theme':
				return $this->check_if_included_theme($update_details, $save = true);
			case 'translation':
				return $this->check_if_included_translation($update_details, $save = true);
			case 'core':
				return $this->check_if_included_core($update_details, $save = true);
			default:
				return false;
		}
	}

	public function is_backup_required_before_auto_update(){
		$settings = $this->get_auto_update_settings();
		if ($settings['status'] === 'yes') {
			return true;
		}
		return false;
	}

	private function is_scheduled_update_enabled(){
		$settings = $this->get_auto_update_settings();

		if ( empty($settings['schedule']) ) {
			wptc_log(array(),'-----------schedule tie nto set----------------');
			return false;
		}

		if ( empty($settings['schedule']['enabled']) ) {
			wptc_log(array(),'-----------schedule not enabled----------------');
			return false;
		}

		wptc_log(array(),'-----------time set----------------');

		return true;
	}

	public function turn_off_auto_update(){
		$settings_serialized = $this->config->get_option('wptc_auto_update_settings');
		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);
		$settings['update_settings']['status'] = 'no';
		$this->config->set_option('wptc_auto_update_settings', serialize($settings));
		wptc_log(array(), '---------Turned off auto update------------');

		push_settings_wptc_server("", "", $dont_reactivate = true);
	}

	public function disable_theme_updates(){
		$settings_serialized = $this->config->get_option('wptc_auto_update_settings');

		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);
		$settings['update_settings']['themes']['status'] = false;
		$this->config->set_option('wptc_auto_update_settings', serialize($settings));
		wptc_log(array(), '---------Turned off auto update for themes------------');
	}

	public function can_process_auto_updates($strict_check_custom_time = false){

		if ( !$this->is_backup_required_before_auto_update() ) {
			wptc_log(array(),'-----------AUTO update disabled----------------');
			return false;
		}

		if ( $strict_check_custom_time && $this->is_scheduled_update_enabled() ) {
			wptc_log(array(),'-----------custom time set so dont auto update now----------------');
			return false;
		}

		if( $this->config->get_option('bulk_update_request') ){
			wptc_log(array(),'-----------Updates Alreadys set----------------');
			return false;
		}

		return true;
	}

	public function modify_settings($settings){
		$settings['auto_update'] = $this->get_auto_update_settings();

		if(!empty($settings['auto_update']['plugins']['included'])){
			$plugins_included = $this->get_slug_from_array(unserialize($settings['auto_update']['plugins']['included']));
		} else {
			$plugins_included = array();
		}

		$settings['auto_update']['plugins']['included'] = $plugins_included;

		if(!empty($settings['auto_update']['themes']['included'])){
			$themes_included = unserialize($settings['auto_update']['themes']['included']);
		} else {
			$themes_included = array();
		}

		$settings['auto_update']['themes']['included'] = $themes_included;

		return $settings;
	}

	public function exclude_item_from_auto_update($remove_key, $name, $version, $type, $backup_id){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->logger->log( 'Maximum retries failed so ' . ucfirst($name) . " is excluded from Auto updates", 'auto_update', $backup_id);

		switch ($type) {
			case 'plugin':
			case 'theme':
				$this->exclude_plugins_n_themes_from_auto_update($remove_key, $type);
				break;
			case 'core':
			case 'translation':
				$this->exclude_core_n_translation_from_auto_update($remove_key, $type);
				break;
		}

		return $this->add_into_temp_excluded_list_for_email_content($name, $version, $type);
	}

	private function exclude_plugins_n_themes_from_auto_update($remove_key, $type){
		$type .= 's';

		wptc_log($type,'-----------$type----------------');

		$settings_serialized = $this->config->get_option('wptc_auto_update_settings');
		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);

		// wptc_log($settings,'-----------$settings----------------');

		if (empty($settings['update_settings'][$type]) || empty($settings['update_settings'][$type]['included'])) {
			wptc_log(array(),'-----------list empty ----------------');
			return false;
		}

		$items = $settings['update_settings'][$type]['included'];
		$items = unserialize($items);

		if (empty($items)) {
			wptc_log(array(),'-----------list empty ----------------');
			return false;
		}

		if (!in_array($remove_key, $items)) {
			wptc_log(array(),'-----------Already excluded----------------');
			return false;
		}

		if (($key = array_search($remove_key, $items)) !== false) {
			unset($items[$key]);
			wptc_log($key,'-----------removed----------------');
		}

		$settings['update_settings'][$type]['included'] = serialize($items);
		// wptc_log($settings,'-----------$settings----------------');

		$this->config->set_option('wptc_auto_update_settings', serialize($settings));

		wptc_log(array(),'-----------key removed----------------');

		push_settings_wptc_server("", "", $dont_reactivate = true);
	}

	private function exclude_core_n_translation_from_auto_update($remove_key, $type){

		$settings_serialized = $this->config->get_option('wptc_auto_update_settings');

		if (empty($settings_serialized)) {
			return false;
		}

		$settings = unserialize($settings_serialized);

		// wptc_log($settings,'-----------$settings----------------');

		if ($type === 'core') {
			$disable_core =  array ( 'major' => array ( 'status' => 0 ), 'minor' => array ( 'status' => 0 ) );
			$settings['update_settings']['core'] = $disable_core;
		} else {
			$disable_translation =  array ( 'status' => 0 );
			$settings['update_settings']['translation'] = $disable_translation;
		}

		// wptc_log($settings,'-----------$settings----------------');

		$this->config->set_option('wptc_auto_update_settings', serialize($settings));

		wptc_log(array(),'-----------key removed----------------');

		push_settings_wptc_server("", "", $dont_reactivate = true);
	}

	public function add_into_temp_excluded_list_for_email_content($name, $version, $type){
		$current_list = $this->config->get_option('temp_auto_excluded_auto_updates_lists');

		$list = array();

		if (!empty($current_list)) {
			$list = unserialize($current_list);
		}

		$list[$type][$name] = $version;

		wptc_log($list ,'-----------$add_into_temp_excluded_list_for_email_content list ----------------');

		$this->config->set_option('temp_auto_excluded_auto_updates_lists', serialize($list));
	}

	public function included_new_items($upgrade_object, $options){
		$auto_updater_settings = $this->get_auto_update_settings();
		wptc_log($auto_updater_settings,'-----------$auto_updater_settings----------------');

		if (empty($auto_updater_settings['include_automatically'])) {
			return false;
		}

		if ( empty($options['action']) 
			 || $options['action'] != 'install' 
			 || ($options['type'] != 'plugin' && $options['type'] != 'theme') ){
			
			return ;
		}

		if ($options['type'] === 'plugin' && !empty($auto_updater_settings['include_automatically']['plugins'])) {
			return $this->include_new_plugin($upgrade_object->result['destination_name']);
		}

		if ($options['type'] === 'theme' && !empty($auto_updater_settings['include_automatically']['themes'])) {
			return $this->include_new_theme($upgrade_object->result['destination_name']);
		}

	}

	private function include_new_plugin($destination){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (empty($destination)) {
			return ;
		}

		$settings['update_settings'] = $this->get_auto_update_settings();

		if (empty($settings)) {
			return ;
		}

		$plugin = $this->get_plugin_slug_by_dir($destination);

		wptc_log($plugin,'-----------$plugin----------------');

		if (!$plugin) {
			return ;
		}

		$existing_plugins_list = !empty($settings['update_settings']['plugins']['included']) ? unserialize($settings['update_settings']['plugins']['included']) : array();

		wptc_log($existing_plugins_list,'-----------$existing_plugins_list----------------');

		if (!in_array($plugin, $existing_plugins_list)) {
			array_push($existing_plugins_list, $plugin);
		}

		wptc_log($existing_plugins_list,'-----------$existing_plugins_list----------------');

		WPTC_Factory::get('logger')->log('New Plugin ' . ucfirst($destination) . ' added into Auto-update list by WPTC.' , 'others');


		$settings['update_settings']['plugins']['included'] = serialize($existing_plugins_list);

		$this->config->set_option('wptc_auto_update_settings', serialize($settings));

		push_settings_wptc_server("", "", $dont_reactivate = true);
	}

	private function get_plugin_slug_by_dir($destination){
		$plugins = get_plugins();

		foreach ($plugins as $slug => $plugin) {
			if (stripos($slug, $destination . '/') === 0) {
				return $slug;
			}
		}

		return false;
	}

	private function include_new_theme($theme){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (empty($theme)) {
			return ;
		}

		$settings['update_settings'] = $this->get_auto_update_settings();

		if (empty($settings)) {
			return ;
		}

		$existing_themes_list = !empty($settings['update_settings']['themes']['included']) ? unserialize($settings['update_settings']['themes']['included']) : array();

		if (!in_array($theme, $existing_themes_list)) {
			array_push($existing_themes_list, $theme);
		}

		WPTC_Factory::get('logger')->log('New theme ' . ucfirst($theme) . ' added into Auto-update list by WPTC.' , 'others');

		$settings['update_settings']['themes']['included'] = serialize($existing_themes_list);

		$this->config->set_option('wptc_auto_update_settings', serialize($settings));

		push_settings_wptc_server("", "", $dont_reactivate = true);

	}

	public function update_bulk_settings($server_data){
		$settings['update_settings'] = $this->get_auto_update_settings();

		if (empty($server_data->auto_update_settings)) {
			return ;
		}

		if (isset($server_data->auto_update_settings->status)) {
			$settings['update_settings']['status'] = $server_data->auto_update_settings->status;
		}

		if (isset($server_data->auto_update_settings->schedule)) {
			$settings['update_settings']['schedule'] = (array) $server_data->auto_update_settings->schedule;
		}

		if (isset($server_data->auto_update_settings->include_automatically)) {
			$settings['update_settings']['include_automatically'] = (array) $server_data->auto_update_settings->include_automatically;
		}


		if (!empty($server_data->auto_update_settings->plugins)) {
			$settings['update_settings']['plugins']['included']     = !empty($server_data->auto_update_settings->plugins->included) && is_array($server_data->auto_update_settings->plugins->included) ? serialize(WPTC_Base_Factory::get('Wptc_App_Functions')->append_slugs_plugins($server_data->auto_update_settings->plugins->included)) : serialize(array());
		}

		if (!empty($server_data->auto_update_settings->themes)) {
			$settings['update_settings']['themes']['included']      = !empty($server_data->auto_update_settings->themes->included) && is_array($server_data->auto_update_settings->themes->included) ? serialize($server_data->auto_update_settings->themes->included) : serialize(array());
		}

		if (!empty($server_data->auto_update_settings->core)) {
			if (!empty($server_data->auto_update_settings->core->minor->status)) {
				$settings['update_settings']['core']['minor']['status'] = $server_data->auto_update_settings->core->minor->status;
			}

			if (!empty($server_data->auto_update_settings->core->major->status)) {
				$settings['update_settings']['core']['major']['status'] = $server_data->auto_update_settings->core->major->status;
			}
		}

		$this->config->set_option('wptc_auto_update_settings', serialize($settings));
	}
}
