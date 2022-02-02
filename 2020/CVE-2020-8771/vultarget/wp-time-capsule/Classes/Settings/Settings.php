<?php

class Wptc_Settings extends Wptc_Settings_Init {
	private $plugin_base_file_path;
	private $config;
	public $plugin_data;
	public $tabs;
	private $server_ips;

	public function __construct(){
		if(WPTC_ENV === 'production'){
			$this->server_ips =  array( '52.33.122.174', '52.27.206.180');
		} else {
			$this->server_ips =  array( '52.32.120.186');
		}
		$this->config = WPTC_Base_Factory::get('Wptc_Settings_Config');
		$this->plugin_base_file_path = WPTC_PLUGIN_DIR.'wp-time-capsule.php';
	}

	public function load_page(){
		$this->init_settings_tabs();
		$this->include_header_files();
	}

	private function include_header_files(){
		wp_enqueue_script('wptc-settings-js',         plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Classes/Settings/scripts.js',    array(), WPTC_VERSION);
		wp_enqueue_script('wptc-jquery-ui-custom-js', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/treeView/jquery-ui.custom.js',   array(), WPTC_VERSION);
		wp_enqueue_script('wptc-fancytree-js',        plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/treeView/jquery.fancytree.js',   array(), WPTC_VERSION);
		wp_enqueue_style('wptc-fancytree-css',        plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/treeView/skin/ui.fancytree.css', array(), WPTC_VERSION);
		wp_enqueue_script('wptc-fileTree-common-js',  plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/treeView/common.js',             array(), WPTC_VERSION);
		wp_enqueue_script('wptc-settings-common-js',  plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/js/settings-common.js',          array(), WPTC_VERSION);
		wp_enqueue_script('wptc-file-upload-js', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/wp-tcapsule-bridge/upload/js/jquery.fileupload.js', array(), WPTC_VERSION);
	}

	private function init_settings_tabs(){
		$is_override_by_white_label_admin = apply_filters('is_whitelabling_override_wptc', true);
		$is_allowed_by_white_label_settings = apply_filters('is_general_tab_allowed_wptc', false);

		if ( $is_override_by_white_label_admin || $is_allowed_by_white_label_settings ) {
			$this->tabs['general'] = __( 'General', 'wp-time-capsule' );
		}

		$this->tabs['backup']      = __( 'Backup', 'wp-time-capsule' );
		$this->tabs                = apply_filters( 'page_settings_tab_wptc', $this->tabs );
		$this->tabs['advanced']    = __( 'Advanced', 'wp-time-capsule' );
		$this->tabs['information'] = __( 'Information', 'wp-time-capsule' );
	}

	public function get_plugin_data( $name = NULL ) {

		if ( $name )
			$name = strtolower( trim( $name ) );

		if ( empty( $this->plugin_data ) ) {
			$this->plugin_data = get_file_data(
									$this->plugin_base_file_path,
									array(
										'name'		=> 'Plugin Name',
										'version'	=> 'Version'
									), 'plugin'
								);

			$this->plugin_data[ 'name' ]        = trim( $this->plugin_data[ 'name' ] );
			$this->plugin_data[ 'url' ] = plugins_url( '', $this->plugin_base_file_path );

			global $wp_version;
			$this->plugin_data[ 'wp_version' ] = $wp_version;
		}

		if ( ! empty( $name ) ){
			return $this->plugin_data[ $name ];
		} else {
			return $this->plugin_data;
		}
	}

	public function auto_whitelist_ips(){
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		global $wpdb;
		// wptc_log($plugins, '--------$plugins--------');
		$whitelisted = '';

		//wp-spamshield
		if (!empty($plugins['wp-spamshield/wp-spamshield.php'])) {
			//Updating their global value to avoid overwritting prev settings by them.
			global $spamshield_options;
			//enable whitelisting option
			$spamshield_settings = get_option('spamshield_options');

			if (empty($spamshield_settings)) {
				return false; //spamshield is not activated atleast once
			}
			$spamshield_settings['enable_whitelist'] = 1;

			//Updating their global value to avoid overwritting prev settings by them.
			$spamshield_options = $spamshield_settings;

			$result = update_option('spamshield_options', $spamshield_settings);

			//Updating their global value to avoid overwritting prev settings by them.
			$spamshield_options = $spamshield_settings;

			//update our ip
			$spamshield_whitelist_keys = get_option('spamshield_whitelist_keys');

			$added = false;
			foreach ($this->server_ips as $ip) {
				if (strpos($spamshield_whitelist_keys, $ip) === false) {
					$spamshield_whitelist_keys .= "\n$ip";
					$added =true;
				}
			}

			if($added){
				update_option('spamshield_whitelist_keys', $spamshield_whitelist_keys);
				$whitelisted .= $plugins['wp-spamshield/wp-spamshield.php']['Name'];
			}
		}

		//wordfence
		if (!empty($plugins['wordfence/wordfence.php'])) {
			$table = $wpdb->base_prefix.'wfConfig';
			$wordfence_whitelist_keys = $wpdb->get_var($wpdb->prepare("SELECT val FROM {$table} WHERE name = %s", 'whitelisted'));
			wptc_log($wordfence_whitelist_keys, '--------$wordfence_whitelist_keys--------');

			$added = false;

			foreach ($this->server_ips as $ip) {
				if (strpos($wordfence_whitelist_keys, $ip) === false) {
					if (empty($wordfence_whitelist_keys)) {
						$wordfence_whitelist_keys .= "$ip";
					} else {
						$wordfence_whitelist_keys .= ",$ip";
					}
					$added = true;
				}
			}

			if($added){
				$data = $wpdb->query("UPDATE $table SET val = '".$wordfence_whitelist_keys."' WHERE name = 'whitelisted'");
				if (empty($whitelisted)) {
					$whitelisted .=  $plugins['wordfence/wordfence.php']['Name'];
				} else {
					$whitelisted .= ', '. $plugins['wordfence/wordfence.php']['Name'];
				}
			}
		}

		//better-wp-security
		if (!empty($plugins['better-wp-security/better-wp-security.php'])) {

			//update our ip
			$itsec_whitelist_keys = get_option('itsec-storage');

			$added = false;
			if (empty($itsec_whitelist_keys['global']['lockout_white_list'])) {
				$itsec_whitelist_keys['global']['lockout_white_list'] = array();
			}
			foreach ($this->server_ips as $ip) {
				if (in_array($ip, $itsec_whitelist_keys['global']['lockout_white_list']) === false) {
					$itsec_whitelist_keys['global']['lockout_white_list'][] = $ip;
					$added = true;
				}
			}

			if($added){
				update_option('itsec-storage', $itsec_whitelist_keys);
				if (empty($whitelisted)) {
					$whitelisted .= $plugins['better-wp-security/better-wp-security.php']['Name'];
				} else {
					$whitelisted .= ', '.$plugins['better-wp-security/better-wp-security.php']['Name'];
				}
			}
		}

		//all-in-one-wp-security-and-firewall
		if (!empty($plugins['all-in-one-wp-security-and-firewall/wp-security.php'])) {

			//enable whitelisting option
			$aio_wp_security_configs = get_option('aio_wp_security_configs');
			if (empty($aio_wp_security_configs)) {
				return false; //spamshield is not activated atleast once
			}

			$aio_wp_security_configs['aiowps_lockdown_enable_whitelisting'] = 1;

			$added = false;

			//update our ip
			foreach ($this->server_ips as $ip) {
				if (strpos($aio_wp_security_configs['aiowps_lockdown_allowed_ip_addresses'], $ip) === false) {
					$aio_wp_security_configs['aiowps_lockdown_allowed_ip_addresses'] .= "\n$ip";
					$added = true;
				}
			}

			if($added){
				update_option('aio_wp_security_configs', $aio_wp_security_configs);
				if (empty($whitelisted)) {
					$whitelisted .= $plugins['all-in-one-wp-security-and-firewall/wp-security.php']['Name'];
				} else {
					$whitelisted .= ', '.$plugins['all-in-one-wp-security-and-firewall/wp-security.php']['Name'];
				}
			}
		}
		if (empty($whitelisted)) {
			return false;
		}
		$whitelisted_msg = 'WP Time Capsule has whitelisted its server IP\'s automatically in the following plugins <strong>'.$whitelisted.'</strong> to prevent us from getting blocked from your security plugins';
		set_admin_notices_wptc($whitelisted_msg, 'success', false);
	}

	public function get_account_email(){
		return $this->config->get_option('main_account_email');
	}

	public function get_anonymouse_report_settings(){
		return $this->config->get_option('anonymous_datasent');
	}

	public function get_user_excluded_extenstions(){
		return strtolower($this->config->get_option('user_excluded_extenstions'));
	}

	public function get_connected_cloud_info() {

		$connected_repos_arr = $this->config->get_option('signed_in_repos');

		$dropbox = WPTC_Factory::get(DEFAULT_REPO);

		if (!$dropbox || empty($connected_repos_arr) || !$dropbox->is_authorized()) {
			return 'Not connected';
		}

		$connected_repos = unserialize($connected_repos_arr);

		foreach ($connected_repos as $key => $details) {
			if ($key == DEFAULT_REPO) {
				return $details;
			}
		}
	}

	public function get_current_timezone(){
		$current_timezone = $this->config->get_option('wptc_timezone');
		return empty($current_timezone) ? 'UTC' : $current_timezone ;
	}

	public function get_backup_slots_html(){
		$backup_timing = $this->get_backup_slots();
		$current_slot = $this->config->get_option('backup_slot');

		$html = '';

		foreach ($backup_timing as $value => $name) {
			$html .= $this->get_dropdown_option_html($value, $name, $current_slot);
		}

		return $html;
	}

	public function get_dropdown_option_html($value, $name, $selected_value, $extra_text = ''){

		if ($selected_value == $value) {
			return "<option value='" . $value . "' selected >" . $name . " " . $extra_text . "</option>";
		}

		return "<option value='" . $value . "' >" . $name . " " . $extra_text . "</option>";
	}

	public function get_backup_slots(){
		$default_timing = array(
			WPTC_DEFAULT_BACKUP_SLOT => 'Daily'
			);

		$backup_timing = apply_filters('get_backup_slots_wptc', $default_timing);
		return $backup_timing;
	}

	public function get_gdrive_old_token(){
		return htmlspecialchars($this->config->get_option('gdrive_old_token'));
	}

	public function get_bulk_settings_str(){
		
		$bulk_settings = array();

		switch (DEFAULT_REPO) {
			case 'dropbox':
				$bulk_settings['default_repo'] = $this->config->get_option('default_repo');
				$bulk_settings['default_repo_history'] = $this->config->get_option('default_repo_history');
				$bulk_settings['signed_in_repos'] = $this->config->get_option('signed_in_repos');

				$bulk_settings['dropbox_access_token'] = $this->config->get_option('dropbox_access_token');
				$bulk_settings['dropbox_oauth_state'] = $this->config->get_option('dropbox_oauth_state');

				break;

			case 'g_drive':
				$bulk_settings['default_repo'] = $this->config->get_option('default_repo');
				$bulk_settings['default_repo_history'] = $this->config->get_option('default_repo_history');
				$bulk_settings['signed_in_repos'] = $this->config->get_option('signed_in_repos');

				$bulk_settings['gdrive_old_token'] = $this->config->get_option('gdrive_old_token');
				$bulk_settings['oauth_state_g_drive'] = $this->config->get_option('oauth_state_g_drive');
				$bulk_settings['current_g_drive_email'] = $this->config->get_option('current_g_drive_email');

				break;

			case 's3':
				$bulk_settings['default_repo'] = $this->config->get_option('default_repo');
				$bulk_settings['default_repo_history'] = $this->config->get_option('default_repo_history');
				$bulk_settings['signed_in_repos'] = $this->config->get_option('signed_in_repos');

				$bulk_settings['as3_access_key'] = $this->config->get_option('as3_access_key');
				$bulk_settings['as3_secure_key'] = $this->config->get_option('as3_secure_key');
				$bulk_settings['as3_bucket_name'] = $this->config->get_option('as3_bucket_name');
				$bulk_settings['as3_bucket_region'] = $this->config->get_option('as3_bucket_region');

				break;

			case 'wasabi':
				$bulk_settings['default_repo'] = $this->config->get_option('default_repo');
				$bulk_settings['default_repo_history'] = $this->config->get_option('default_repo_history');
				$bulk_settings['signed_in_repos'] = $this->config->get_option('signed_in_repos');

				$bulk_settings['wasabi_access_key'] = $this->config->get_option('wasabi_access_key');
				$bulk_settings['wasabi_secure_key'] = $this->config->get_option('wasabi_secure_key');
				$bulk_settings['wasabi_bucket_name'] = $this->config->get_option('wasabi_bucket_name');
				$bulk_settings['wasabi_bucket_region'] = $this->config->get_option('wasabi_bucket_region');

				break;
			
			default:
				break;
		}

		$bulk_settings['cloud_settings_email'] = $this->config->get_option('main_account_email');
		$bulk_settings['schedule_time_str'] = $this->config->get_option('schedule_time_str');
		$bulk_settings['revision_limit'] = $this->config->get_option('revision_limit');
		$bulk_settings['wptc_timezone'] = $this->config->get_option('wptc_timezone');

		$bulk_settings = json_encode($bulk_settings, true);
		$bulk_settings = base64_encode($bulk_settings);

		return $bulk_settings;
	}

	public function is_setting_blocking_process_going_on(){

		if (is_any_ongoing_wptc_backup_process()){
			return 'Backup Process';
		}

		if(is_any_other_wptc_process_going_on()) {
			return 'Staging Process';
		}

		return false;
	}

	public function get_schedule_times_div_wptc($type = 'backup', $current_time = false) {

		$div = '';

		if ($type === 'backup') {
			$current_time = $this->config->get_option('schedule_time_str');
		}

		for ($i = 1; $i <= 24; $i++) {
			$time = date("g:i a", strtotime("$i:00"));
			$div .= $this->get_dropdown_option_html($time, $time, $current_time);
		}

		return $div;
	}

	public function admin_footer_text( $admin_footer_text ) {

		if ( !apply_filters('is_whitelabling_override_wptc', true) ) {
			return $admin_footer_text;
		}

		$default_text = $admin_footer_text;

		if ( isset( $_REQUEST[ 'page' ] ) && strpos($_REQUEST[ 'page' ], 'wp-time-capsule' ) !== false ) {
			$admin_footer_text =  __( '<span>If you have any questions, see the <a href="http://wptc.helpscoutdocs.com/article/7-commonly-asked-questions" target="_blank">FAQs</a> or email us at <a href="mailto:help@wptimecapsule.com?Subject=Contact" target="_top">help@wptimecapsule.com</a> <br>  <a href="' . network_admin_url() . 'admin.php?page=wp-time-capsule&logout=true"> Logout </a> </span><br>', 'wp-time-capsule' );
			return $admin_footer_text . $default_text;
		}


		return $admin_footer_text;
	}

	public function update_footer( $update_footer_text ) {

		if ( !apply_filters('is_whitelabling_override_wptc', true) ) {
			return $update_footer_text;
		}

		$default_text = $update_footer_text;

		if ( isset( $_REQUEST[ 'page' ] ) && strpos($_REQUEST[ 'page' ], 'wp-time-capsule' ) !== false ) {
			$update_footer_text  = '<span class="wp-time-capsule-update-footer"><a href="' . __( 'https://wptimecapsule.com', 'wp-time-capsule' ) . '">' . $this->get_plugin_data( 'Name' ) . '</a> '. sprintf( __( 'version %s' ,'wp-time-capsule'), $this->get_plugin_data( 'Version' ) ) . '</span>';

			return $update_footer_text . $default_text;
		}

		return $update_footer_text;
	}

	public function get_all_timezone_html() {
		$html = '<optgroup label="Africa">
				<option value="Africa/Abidjan">Abidjan</option><option value="Africa/Accra">Accra</option><option value="Africa/Addis_Ababa">Addis Ababa</option><option value="Africa/Algiers">Algiers</option><option value="Africa/Asmara">Asmara</option><option value="Africa/Bamako">Bamako</option><option value="Africa/Bangui">Bangui</option><option value="Africa/Banjul">Banjul</option><option value="Africa/Bissau">Bissau</option><option value="Africa/Blantyre">Blantyre</option><option value="Africa/Brazzaville">Brazzaville</option><option value="Africa/Bujumbura">Bujumbura</option><option value="Africa/Cairo">Cairo</option><option value="Africa/Casablanca">Casablanca</option><option value="Africa/Ceuta">Ceuta</option><option value="Africa/Conakry">Conakry</option><option value="Africa/Dakar">Dakar</option><option value="Africa/Dar_es_Salaam">Dar es Salaam</option><option value="Africa/Djibouti">Djibouti</option><option value="Africa/Douala">Douala</option><option value="Africa/El_Aaiun">El Aaiun</option><option value="Africa/Freetown">Freetown</option><option value="Africa/Gaborone">Gaborone</option><option value="Africa/Harare">Harare</option><option value="Africa/Johannesburg">Johannesburg</option><option value="Africa/Juba">Juba</option><option value="Africa/Kampala">Kampala</option><option value="Africa/Khartoum">Khartoum</option><option value="Africa/Kigali">Kigali</option><option value="Africa/Kinshasa">Kinshasa</option><option value="Africa/Lagos">Lagos</option><option value="Africa/Libreville">Libreville</option><option value="Africa/Lome">Lome</option><option value="Africa/Luanda">Luanda</option><option value="Africa/Lubumbashi">Lubumbashi</option><option value="Africa/Lusaka">Lusaka</option><option value="Africa/Malabo">Malabo</option><option value="Africa/Maputo">Maputo</option><option value="Africa/Maseru">Maseru</option><option value="Africa/Mbabane">Mbabane</option><option value="Africa/Mogadishu">Mogadishu</option><option value="Africa/Monrovia">Monrovia</option><option value="Africa/Nairobi">Nairobi</option><option value="Africa/Ndjamena">Ndjamena</option><option value="Africa/Niamey">Niamey</option><option value="Africa/Nouakchott">Nouakchott</option><option value="Africa/Ouagadougou">Ouagadougou</option><option value="Africa/Porto-Novo">Porto-Novo</option><option value="Africa/Sao_Tome">Sao Tome</option><option value="Africa/Tripoli">Tripoli</option><option value="Africa/Tunis">Tunis</option><option value="Africa/Windhoek">Windhoek</option>
			</optgroup>
			<optgroup label="America">
				<option value="America/Adak">Adak</option><option value="America/Anchorage">Anchorage</option><option value="America/Anguilla">Anguilla</option><option value="America/Antigua">Antigua</option><option value="America/Araguaina">Araguaina</option><option value="America/Argentina/Buenos_Aires">Argentina - Buenos Aires</option><option value="America/Argentina/Catamarca">Argentina - Catamarca</option><option value="America/Argentina/Cordoba">Argentina - Cordoba</option><option value="America/Argentina/Jujuy">Argentina - Jujuy</option><option value="America/Argentina/La_Rioja">Argentina - La Rioja</option><option value="America/Argentina/Mendoza">Argentina - Mendoza</option><option value="America/Argentina/Rio_Gallegos">Argentina - Rio Gallegos</option><option value="America/Argentina/Salta">Argentina - Salta</option><option value="America/Argentina/San_Juan">Argentina - San Juan</option><option value="America/Argentina/San_Luis">Argentina - San Luis</option><option value="America/Argentina/Tucuman">Argentina - Tucuman</option><option value="America/Argentina/Ushuaia">Argentina - Ushuaia</option><option value="America/Aruba">Aruba</option><option value="America/Asuncion">Asuncion</option><option value="America/Atikokan">Atikokan</option><option value="America/Bahia">Bahia</option><option value="America/Bahia_Banderas">Bahia Banderas</option><option value="America/Barbados">Barbados</option><option value="America/Belem">Belem</option><option value="America/Belize">Belize</option><option value="America/Blanc-Sablon">Blanc-Sablon</option><option value="America/Boa_Vista">Boa Vista</option><option value="America/Bogota">Bogota</option><option value="America/Boise">Boise</option><option value="America/Cambridge_Bay">Cambridge Bay</option><option value="America/Campo_Grande">Campo Grande</option><option value="America/Cancun">Cancun</option><option value="America/Caracas">Caracas</option><option value="America/Cayenne">Cayenne</option><option value="America/Cayman">Cayman</option><option value="America/Chicago">Chicago</option><option value="America/Chihuahua">Chihuahua</option><option value="America/Costa_Rica">Costa Rica</option><option value="America/Creston">Creston</option><option value="America/Cuiaba">Cuiaba</option><option value="America/Curacao">Curacao</option><option value="America/Danmarkshavn">Danmarkshavn</option><option value="America/Dawson">Dawson</option><option value="America/Dawson_Creek">Dawson Creek</option><option value="America/Denver">Denver</option><option value="America/Detroit">Detroit</option><option value="America/Dominica">Dominica</option><option value="America/Edmonton">Edmonton</option><option value="America/Eirunepe">Eirunepe</option><option value="America/El_Salvador">El Salvador</option><option value="America/Fortaleza">Fortaleza</option><option value="America/Glace_Bay">Glace Bay</option><option value="America/Godthab">Godthab</option><option value="America/Goose_Bay">Goose Bay</option><option value="America/Grand_Turk">Grand Turk</option><option value="America/Grenada">Grenada</option><option value="America/Guadeloupe">Guadeloupe</option><option value="America/Guatemala">Guatemala</option><option value="America/Guayaquil">Guayaquil</option><option value="America/Guyana">Guyana</option><option value="America/Halifax">Halifax</option><option value="America/Havana">Havana</option><option value="America/Hermosillo">Hermosillo</option><option value="America/Indiana/Indianapolis">Indiana - Indianapolis</option><option value="America/Indiana/Knox">Indiana - Knox</option><option value="America/Indiana/Marengo">Indiana - Marengo</option><option value="America/Indiana/Petersburg">Indiana - Petersburg</option><option value="America/Indiana/Tell_City">Indiana - Tell City</option><option value="America/Indiana/Vevay">Indiana - Vevay</option><option value="America/Indiana/Vincennes">Indiana - Vincennes</option><option value="America/Indiana/Winamac">Indiana - Winamac</option><option value="America/Inuvik">Inuvik</option><option value="America/Iqaluit">Iqaluit</option><option value="America/Jamaica">Jamaica</option><option value="America/Juneau">Juneau</option><option value="America/Kentucky/Louisville">Kentucky - Louisville</option><option value="America/Kentucky/Monticello">Kentucky - Monticello</option><option value="America/Kralendijk">Kralendijk</option><option value="America/La_Paz">La Paz</option><option value="America/Lima">Lima</option><option value="America/Los_Angeles">Los Angeles</option><option value="America/Lower_Princes">Lower Princes</option><option value="America/Maceio">Maceio</option><option value="America/Managua">Managua</option><option value="America/Manaus">Manaus</option><option value="America/Marigot">Marigot</option><option value="America/Martinique">Martinique</option><option value="America/Matamoros">Matamoros</option><option value="America/Mazatlan">Mazatlan</option><option value="America/Menominee">Menominee</option><option value="America/Merida">Merida</option><option value="America/Metlakatla">Metlakatla</option><option value="America/Mexico_City">Mexico City</option><option value="America/Miquelon">Miquelon</option><option value="America/Moncton">Moncton</option><option value="America/Monterrey">Monterrey</option><option value="America/Montevideo">Montevideo</option><option value="America/Montserrat">Montserrat</option><option value="America/Nassau">Nassau</option><option value="America/New_York">New York</option><option value="America/Nipigon">Nipigon</option><option value="America/Nome">Nome</option><option value="America/Noronha">Noronha</option><option value="America/North_Dakota/Beulah">North Dakota - Beulah</option><option value="America/North_Dakota/Center">North Dakota - Center</option><option value="America/North_Dakota/New_Salem">North Dakota - New Salem</option><option value="America/Ojinaga">Ojinaga</option><option value="America/Panama">Panama</option><option value="America/Pangnirtung">Pangnirtung</option><option value="America/Paramaribo">Paramaribo</option><option value="America/Phoenix">Phoenix</option><option value="America/Port-au-Prince">Port-au-Prince</option><option value="America/Port_of_Spain">Port of Spain</option><option value="America/Porto_Velho">Porto Velho</option><option value="America/Puerto_Rico">Puerto Rico</option><option value="America/Rainy_River">Rainy River</option><option value="America/Rankin_Inlet">Rankin Inlet</option><option value="America/Recife">Recife</option><option value="America/Regina">Regina</option><option value="America/Resolute">Resolute</option><option value="America/Rio_Branco">Rio Branco</option><option value="America/Santa_Isabel">Santa Isabel</option><option value="America/Santarem">Santarem</option><option value="America/Santiago">Santiago</option><option value="America/Santo_Domingo">Santo Domingo</option><option value="America/Sao_Paulo">Sao Paulo</option><option value="America/Scoresbysund">Scoresbysund</option><option value="America/Sitka">Sitka</option><option value="America/St_Barthelemy">St Barthelemy</option><option value="America/St_Johns">St Johns</option><option value="America/St_Kitts">St Kitts</option><option value="America/St_Lucia">St Lucia</option><option value="America/St_Thomas">St Thomas</option><option value="America/St_Vincent">St Vincent</option><option value="America/Swift_Current">Swift Current</option><option value="America/Tegucigalpa">Tegucigalpa</option><option value="America/Thule">Thule</option><option value="America/Thunder_Bay">Thunder Bay</option><option value="America/Tijuana">Tijuana</option><option value="America/Toronto">Toronto</option><option value="America/Tortola">Tortola</option><option value="America/Vancouver">Vancouver</option><option value="America/Whitehorse">Whitehorse</option><option value="America/Winnipeg">Winnipeg</option><option value="America/Yakutat">Yakutat</option><option value="America/Yellowknife">Yellowknife</option>
			</optgroup>
			<optgroup label="Antarctica">
				<option value="Antarctica/Casey">Casey</option><option value="Antarctica/Davis">Davis</option><option value="Antarctica/DumontDUrville">DumontDUrville</option><option value="Antarctica/Macquarie">Macquarie</option><option value="Antarctica/Mawson">Mawson</option><option value="Antarctica/McMurdo">McMurdo</option><option value="Antarctica/Palmer">Palmer</option><option value="Antarctica/Rothera">Rothera</option><option value="Antarctica/Syowa">Syowa</option><option value="Antarctica/Troll">Troll</option><option value="Antarctica/Vostok">Vostok</option>
			</optgroup>
			<optgroup label="Arctic">
				<option value="Arctic/Longyearbyen">Longyearbyen</option>
			</optgroup>
			<optgroup label="Asia">
				<option value="Asia/Aden">Aden</option><option value="Asia/Almaty">Almaty</option><option value="Asia/Amman">Amman</option><option value="Asia/Anadyr">Anadyr</option><option value="Asia/Aqtau">Aqtau</option><option value="Asia/Aqtobe">Aqtobe</option><option value="Asia/Ashgabat">Ashgabat</option><option value="Asia/Baghdad">Baghdad</option><option value="Asia/Bahrain">Bahrain</option><option value="Asia/Baku">Baku</option><option value="Asia/Bangkok">Bangkok</option><option value="Asia/Beirut">Beirut</option><option value="Asia/Bishkek">Bishkek</option><option value="Asia/Brunei">Brunei</option><option value="Asia/Chita">Chita</option><option value="Asia/Choibalsan">Choibalsan</option><option value="Asia/Colombo">Colombo</option><option value="Asia/Damascus">Damascus</option><option value="Asia/Dhaka">Dhaka</option><option value="Asia/Dili">Dili</option><option value="Asia/Dubai">Dubai</option><option value="Asia/Dushanbe">Dushanbe</option><option value="Asia/Gaza">Gaza</option><option value="Asia/Hebron">Hebron</option><option value="Asia/Ho_Chi_Minh">Ho Chi Minh</option><option value="Asia/Hong_Kong">Hong Kong</option><option value="Asia/Hovd">Hovd</option><option value="Asia/Irkutsk">Irkutsk</option><option value="Asia/Jakarta">Jakarta</option><option value="Asia/Jayapura">Jayapura</option><option value="Asia/Jerusalem">Jerusalem</option><option value="Asia/Kabul">Kabul</option><option value="Asia/Kamchatka">Kamchatka</option><option value="Asia/Karachi">Karachi</option><option value="Asia/Kathmandu">Kathmandu</option><option value="Asia/Khandyga">Khandyga</option><option value="Asia/Kolkata">Kolkata</option><option value="Asia/Krasnoyarsk">Krasnoyarsk</option><option value="Asia/Kuala_Lumpur">Kuala Lumpur</option><option value="Asia/Kuching">Kuching</option><option value="Asia/Kuwait">Kuwait</option><option value="Asia/Macau">Macau</option><option value="Asia/Magadan">Magadan</option><option value="Asia/Makassar">Makassar</option><option value="Asia/Manila">Manila</option><option value="Asia/Muscat">Muscat</option><option value="Asia/Nicosia">Nicosia</option><option value="Asia/Novokuznetsk">Novokuznetsk</option><option value="Asia/Novosibirsk">Novosibirsk</option><option value="Asia/Omsk">Omsk</option><option value="Asia/Oral">Oral</option><option value="Asia/Phnom_Penh">Phnom Penh</option><option value="Asia/Pontianak">Pontianak</option><option value="Asia/Pyongyang">Pyongyang</option><option value="Asia/Qatar">Qatar</option><option value="Asia/Qyzylorda">Qyzylorda</option><option value="Asia/Rangoon">Rangoon</option><option value="Asia/Riyadh">Riyadh</option><option value="Asia/Sakhalin">Sakhalin</option><option value="Asia/Samarkand">Samarkand</option><option value="Asia/Seoul">Seoul</option><option value="Asia/Shanghai">Shanghai</option><option value="Asia/Singapore">Singapore</option><option value="Asia/Srednekolymsk">Srednekolymsk</option><option value="Asia/Taipei">Taipei</option><option value="Asia/Tashkent">Tashkent</option><option value="Asia/Tbilisi">Tbilisi</option><option value="Asia/Tehran">Tehran</option><option value="Asia/Thimphu">Thimphu</option><option value="Asia/Tokyo">Tokyo</option><option value="Asia/Ulaanbaatar">Ulaanbaatar</option><option value="Asia/Urumqi">Urumqi</option><option value="Asia/Ust-Nera">Ust-Nera</option><option value="Asia/Vientiane">Vientiane</option><option value="Asia/Vladivostok">Vladivostok</option><option value="Asia/Yakutsk">Yakutsk</option><option value="Asia/Yekaterinburg">Yekaterinburg</option><option value="Asia/Yerevan">Yerevan</option>
			</optgroup>
			<optgroup label="Atlantic">
				<option value="Atlantic/Azores">Azores</option><option value="Atlantic/Bermuda">Bermuda</option><option value="Atlantic/Canary">Canary</option><option value="Atlantic/Cape_Verde">Cape Verde</option><option value="Atlantic/Faroe">Faroe</option><option value="Atlantic/Madeira">Madeira</option><option value="Atlantic/Reykjavik">Reykjavik</option><option value="Atlantic/South_Georgia">South Georgia</option><option value="Atlantic/Stanley">Stanley</option><option value="Atlantic/St_Helena">St Helena</option>
			</optgroup>
			<optgroup label="Australia">
				<option value="Australia/Adelaide">Adelaide</option><option value="Australia/Brisbane">Brisbane</option><option value="Australia/Broken_Hill">Broken Hill</option><option value="Australia/Currie">Currie</option><option value="Australia/Darwin">Darwin</option><option value="Australia/Eucla">Eucla</option><option value="Australia/Hobart">Hobart</option><option value="Australia/Lindeman">Lindeman</option><option value="Australia/Lord_Howe">Lord Howe</option><option value="Australia/Melbourne">Melbourne</option><option value="Australia/Perth">Perth</option><option value="Australia/Sydney">Sydney</option>
			</optgroup>
			<optgroup label="Europe">
				<option value="Europe/Amsterdam">Amsterdam</option><option value="Europe/Andorra">Andorra</option><option value="Europe/Athens">Athens</option><option value="Europe/Belgrade">Belgrade</option><option value="Europe/Berlin">Berlin</option><option value="Europe/Bratislava">Bratislava</option><option value="Europe/Brussels">Brussels</option><option value="Europe/Bucharest">Bucharest</option><option value="Europe/Budapest">Budapest</option><option value="Europe/Busingen">Busingen</option><option value="Europe/Chisinau">Chisinau</option><option value="Europe/Copenhagen">Copenhagen</option><option value="Europe/Dublin">Dublin</option><option value="Europe/Gibraltar">Gibraltar</option><option value="Europe/Guernsey">Guernsey</option><option value="Europe/Helsinki">Helsinki</option><option value="Europe/Isle_of_Man">Isle of Man</option><option value="Europe/Istanbul">Istanbul</option><option value="Europe/Jersey">Jersey</option><option value="Europe/Kaliningrad">Kaliningrad</option><option value="Europe/Kiev">Kiev</option><option value="Europe/Lisbon">Lisbon</option><option value="Europe/Ljubljana">Ljubljana</option><option value="Europe/London">London</option><option value="Europe/Luxembourg">Luxembourg</option><option value="Europe/Madrid">Madrid</option><option value="Europe/Malta">Malta</option><option value="Europe/Mariehamn">Mariehamn</option><option value="Europe/Minsk">Minsk</option><option value="Europe/Monaco">Monaco</option><option value="Europe/Moscow">Moscow</option><option value="Europe/Oslo">Oslo</option><option value="Europe/Paris">Paris</option><option value="Europe/Podgorica">Podgorica</option><option value="Europe/Prague">Prague</option><option value="Europe/Riga">Riga</option><option value="Europe/Rome">Rome</option><option value="Europe/Samara">Samara</option><option value="Europe/San_Marino">San Marino</option><option value="Europe/Sarajevo">Sarajevo</option><option value="Europe/Simferopol">Simferopol</option><option value="Europe/Skopje">Skopje</option><option value="Europe/Sofia">Sofia</option><option value="Europe/Stockholm">Stockholm</option><option value="Europe/Tallinn">Tallinn</option><option value="Europe/Tirane">Tirane</option><option value="Europe/Uzhgorod">Uzhgorod</option><option value="Europe/Vaduz">Vaduz</option><option value="Europe/Vatican">Vatican</option><option value="Europe/Vienna">Vienna</option><option value="Europe/Vilnius">Vilnius</option><option value="Europe/Volgograd">Volgograd</option><option value="Europe/Warsaw">Warsaw</option><option value="Europe/Zagreb">Zagreb</option><option value="Europe/Zaporozhye">Zaporozhye</option><option value="Europe/Zurich">Zurich</option>
			</optgroup>
			<optgroup label="Indian">
				<option value="Indian/Antananarivo">Antananarivo</option><option value="Indian/Chagos">Chagos</option><option value="Indian/Christmas">Christmas</option><option value="Indian/Cocos">Cocos</option><option value="Indian/Comoro">Comoro</option><option value="Indian/Kerguelen">Kerguelen</option><option value="Indian/Mahe">Mahe</option><option value="Indian/Maldives">Maldives</option><option value="Indian/Mauritius">Mauritius</option><option value="Indian/Mayotte">Mayotte</option><option value="Indian/Reunion">Reunion</option>
			</optgroup>
			<optgroup label="Pacific">
				<option value="Pacific/Apia">Apia</option><option value="Pacific/Auckland">Auckland</option><option value="Pacific/Chatham">Chatham</option><option value="Pacific/Chuuk">Chuuk</option><option value="Pacific/Easter">Easter</option><option value="Pacific/Efate">Efate</option><option value="Pacific/Enderbury">Enderbury</option><option value="Pacific/Fakaofo">Fakaofo</option><option value="Pacific/Fiji">Fiji</option><option value="Pacific/Funafuti">Funafuti</option><option value="Pacific/Galapagos">Galapagos</option><option value="Pacific/Gambier">Gambier</option><option value="Pacific/Guadalcanal">Guadalcanal</option><option value="Pacific/Guam">Guam</option><option value="Pacific/Honolulu">Honolulu</option><option value="Pacific/Johnston">Johnston</option><option value="Pacific/Kiritimati">Kiritimati</option><option value="Pacific/Kosrae">Kosrae</option><option value="Pacific/Kwajalein">Kwajalein</option><option value="Pacific/Majuro">Majuro</option><option value="Pacific/Marquesas">Marquesas</option><option value="Pacific/Midway">Midway</option><option value="Pacific/Nauru">Nauru</option><option value="Pacific/Niue">Niue</option><option value="Pacific/Norfolk">Norfolk</option><option value="Pacific/Noumea">Noumea</option><option value="Pacific/Pago_Pago">Pago Pago</option><option value="Pacific/Palau">Palau</option><option value="Pacific/Pitcairn">Pitcairn</option><option value="Pacific/Pohnpei">Pohnpei</option><option value="Pacific/Port_Moresby">Port Moresby</option><option value="Pacific/Rarotonga">Rarotonga</option><option value="Pacific/Saipan">Saipan</option><option value="Pacific/Tahiti">Tahiti</option><option value="Pacific/Tarawa">Tarawa</option><option value="Pacific/Tongatapu">Tongatapu</option><option value="Pacific/Wake">Wake</option><option value="Pacific/Wallis">Wallis</option>
			</optgroup>
			<optgroup label="UTC">
				<option value="UTC">UTC</option>
			</optgroup>';
			$current_timezone = $this->get_current_timezone();
			if($this->get_current_timezone()){
				$html =str_replace($current_timezone.'"', $current_timezone.'" selected ', $html);
			}

			return $html;
	}

	public function save_settings_revision_limit($requested_revision_limit){
		$default_repo = $this->config->get_option('default_repo');
		$eligible_revision_limit = $this->config->get_option('eligible_revision_limit');

		wptc_log($requested_revision_limit, "--------save_settings_revision_limit--------");

		if ($requested_revision_limit > $eligible_revision_limit) {

			wptc_log('', "--------revision limit exceed error--save_settings_revision_limit------");

			return array('title' => 'Oops...',  'message' => 'You are not eligible for this revisions limit', 'type' => 'error');
		}

		if($requested_revision_limit <= WPTC_DEFAULT_MAX_REVISION_LIMIT && $default_repo !== 's3'){

			wptc_log('', "--------setting this revision limit--save_settings_revision_limit------");

			do_action('set_revision_limit_wptc', $requested_revision_limit);
			return ;
		}

		$cloud_repo = WPTC_Factory::get($default_repo);


		if ($default_repo === 'g_drive') {
			return array('title' => 'Oops...',  'message' => 'Google drive does not supports the revisions more than ' . WPTC_DEFAULT_MAX_REVISION_LIMIT . ' days', 'type' => 'error');
		}

		if ($default_repo === 'dropbox') {
			$modified_revision_limits = $cloud_repo->validate_max_revision_limit($requested_revision_limit);
			if ($modified_revision_limits < $requested_revision_limit) {
				$help_doc = '<a href="http://docs.wptimecapsule.com/article/30-how-to-enable-120-days-restore-points" target="_blank">Know more ?</a>';
				return array('title' => 'Oops...', 'message' => 'Upgrade your dropbox account to use revisions more than ' . WPTC_DEFAULT_MAX_REVISION_LIMIT . ' days ' . $help_doc, 'type' => 'error');
			}
		}

		if ($default_repo === 's3') {
			$response = $cloud_repo->validate_max_revision_limit($requested_revision_limit);
			if (!empty($response['error'])) {

				wptc_log($response, "--------s3 revision limit error--------");

				return array('title' => 'Error!', 'message' => 'WPTC Cannot update bucket lifecycle,' . $response['error'], 'type' => 'error');
			}
		}

		do_action('set_revision_limit_wptc', $requested_revision_limit);
	}

	public function get_total_tables_count_wptc($exclude_staging = false){
		global $wpdb;

		$db_name = DB_NAME;
		$data = $wpdb->get_var("SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name';");

		if(empty($data)){

			return 0;
		}

		if(!empty($exclude_staging)){
			$base_prefix = $wpdb->base_prefix;
			$data = $wpdb->get_var("SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME LIKE '$base_prefix%';");

			if(empty($data)){

				return 0;
			}

			return $data;
		}

		return $data;
	}

	public function get_total_excluded_tables_count_wptc(){
		global $wpdb;

		$data = $wpdb->get_var("SELECT COUNT('key') FROM {$wpdb->base_prefix}wptc_inc_exc_contents WHERE `type` = 'table' AND `action` = 'exclude' AND `category` = 'backup'");

		if(empty($data)){

			return 0;
		}

		return $data;
	}

	public function get_user_excluded_files_more_than_size(){

		$settings = WPTC_Base_Factory::get('Wptc_ExcludeOption')->get_user_excluded_files_more_than_size();

		$yes = $settings['status'] === 'yes' ? 'checked' : '';
		$no  = $settings['status'] === 'no' ? 'checked' : '';

		$style = '';
		if ($no === 'checked') {
			$style = 'style="display: none"';
		}

		return  '<fieldset >
			<label title="Yes">
				<input name="user_excluded_files_more_than_size_status"  type="radio" class="user_excluded_files_more_than_size_status" ' . $yes . ' value="yes">
				<span class="">
					'.__( 'Yes', 'wp-time-capsule' ).'
				</span>
			</label>
			<label title="No" style="margin-left: 10px !important;">
				<input name="user_excluded_files_more_than_size_status" type="radio" class="user_excluded_files_more_than_size_status" '. $no . ' value="no">
				<span class="">
					'.__( 'No', 'wp-time-capsule' ).'
				</span>
			</label>
		</fieldset>

		<fieldset id="user_excluded_files_more_than_size_div" ' . $style . '>
		<br>
			<input class="wptc-split-column" type="text" style="width: 70px;" name="user_excluded_files_more_than_size" id="user_excluded_files_more_than_size" placeholder="50" value='.  $settings['hr']  . '>MB
		</fieldset>';
	}

	public function database_encryption_html(){
		$status = WPTC_Factory::get('config')->get_database_encryption_settings('status');

		$yes = $status === true ? 'checked' : '';
		$no  = $status === false ? 'checked' : '';

		$style = '';
		if ($no === 'checked') {
			$style = 'style="display: none"';
		}

		return  '<fieldset >
			<label title="Yes">
				<input name="database_encryption_status"  type="radio" id="enable_database_encryption" ' . $yes . ' value="yes">
				<span class="">
					'.__( 'Yes', 'wp-time-capsule' ).'
				</span>
			</label>
			<label title="No" style="margin-left: 10px !important;">
				<input name="database_encryption_status" type="radio" id="disable_database_encryption" '. $no . ' value="no">
				<span class="">
					'.__( 'No', 'wp-time-capsule' ).'
				</span>
			</label>
		</fieldset>

		<fieldset id="database_encryption_key_div" ' . $style . '>
		<br>
			<lable>Enter Encryption Phrase : </lable>
			<input class="wptc-split-column" type="text" name="database_encryption_key" id="database_encryption_key" placeholder="Enter encryption phrase" value='.  WPTC_Factory::get('config')->get_database_encryption_settings('key')  . '>
			<p class="description" >' . sprintf( 'If you enter text here, it is used to encrypt database backups (Rijndael). <br>Do make a separate record of it and do not lose it, or all your backups will be useless.', 'wp-time-capsule' ) . ' <a href="https://docs.wptimecapsule.com/article/42-how-to-encrypt-your-wptc-datbase-backup" target="_blank">Learn more</a></p>
		</fieldset>';
	}
}
