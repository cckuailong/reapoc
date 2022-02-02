<?php

class WPTC_Prepare_Restore_Bridge{

	private $config;
	private $app_functions;
	private $logger;
	private $restore_request = array();
	private $restore_id;
	private $fs;
	private $is_restore_to_staging;

	public  function __construct($request){
		$this->config = WPTC_Factory::get('config');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->logger = WPTC_Factory::get('logger');
		$this->pre_check();
		$this->init_bridge_time();
		$this->set_restore_request($request);
		$this->prepare();
	}

	private function pre_check(){
		return true;
	}

	private function init_bridge_time(){
		wptc_manual_debug('', 'start_restore');
		global $start_time_tc_bridge;
		$start_time_tc_bridge = time();
	}

	private function set_restore_request($request){
		if ( empty($request) ) {
			wptc_die_with_json_encode( array('error' => 'Request is empty.') );
		}

		$this->restore_request = $request;
	}

	public function prepare(){
		try {

			$this->set_flags();

			$this->get_restore_id();

			$this->is_restore_to_staging = apply_filters('is_restore_to_staging_wptc', '');

			$this->send_restore_report('STARTED');

			$this->create_dump_dir();

			wptc_manual_debug('', 'start_copy_bridge');
			$copy_result = $this->copy_bridge_files();
			wptc_manual_debug('', 'end_copy_bridge');

			if (!empty($copy_result) && !empty($copy_result['error'])) {
				$this->send_restore_report('FAILED');
				wptc_die_with_json_encode( $copy_result );
			}

			if (!apply_filters('is_restore_to_staging_wptc', '')) {
				send_restore_initiated_email_wptc();
			}

			do_action('turn_off_auto_update_wptc', time());

			$staging_url = apply_filters('process_staging_details_hook_wptc', array( 'type' => 'get', 'key' => 'destination_url') );
			if (is_string($staging_url)) {
				$staging_url = wptc_remove_trailing_slash($staging_url);
			}

			$this->config->set_option('is_restore_to_staging', false);
			$this->config->set_option('restore_to_staging_details', false);

			wptc_die_with_json_encode(
				array(
					'restoreInitiatedResult' =>
						array(
							'bridgeFileName'        => $this->config->get_option('current_bridge_file_name'),
							'safeToCallPluginAjax'  => true,
							'is_restore_to_staging' => $this->is_restore_to_staging,
							'staging_url'           => $staging_url,
						)
					)
			);

		} catch (Exception $e) {
			$this->send_restore_report('FAILED');
			wptc_die_with_json_encode( array('error' => $e->getMessage()) );
		}
	}

	private function create_dump_dir(){
		$this->config->create_dump_dir(); //This will initialize wp_filesystem
	}

	private function get_restore_id(){
		$this->restore_id = $this->config->get_option('restore_action_id');
		$this->restore_id = empty($this->restore_id) ? time() : $this->restore_id;
	}

	private function send_restore_report($status = 'FAILED'){
		$this->get_restore_id();

		if ($this->is_restore_to_staging) {
			return do_action('send_report_data_wptc', $this->restore_id, 'RESTORE_TO_STAGING', $status);
		}

		do_action('send_report_data_wptc', $this->restore_id, 'RESTORE', $status);
	}

	private function set_flags(){

		if (empty($this->restore_request) || empty($this->restore_request['is_first_call'])) {
			return ;
		}

		//initializing restore options
		reset_restore_related_settings_wptc();
		$this->set_option('restore_post_data', serialize($this->restore_request));
		$this->set_option('restore_action_id', time(), true); //main ID used througout the restore process
		$this->set_option('in_progress_restore', true);
		$this->set_option('current_bridge_file_name', "wp-tcapsule-bridge-" . hash("crc32", time()) , true);
		$this->set_option('check_is_safe_for_write_restore', 1);

		wptc_log(is_multisite(), "--------is_multisite_first_call--------");

		if (is_multisite()) {
			global $wpdb;
			//Confirming its not network restore
			if ($wpdb->base_prefix !== $wpdb->prefix) {

				wptc_log($wpdb->base_prefix, "--------restore_multisite_base_prefix--------");
				wptc_log($wpdb->prefix, "--------restore_multisite_current_prefix--------");
				wptc_log(wptc_get_upload_dir(), "--------restore_multisite_upload_dir--------");

				$this->set_option('restore_is_multisite', true);
				$this->set_option('restore_multisite_upload_dir', wptc_get_upload_dir());
				$this->set_option('restore_multisite_base_prefix', $wpdb->base_prefix);
				$this->set_option('restore_multisite_current_prefix', $wpdb->prefix);
			}
		}

		if ( !empty($this->restore_request['is_latest_restore_point']) 
			&& $this->restore_request['is_latest_restore_point'] != 'already_set' ) {
			if ($this->restore_request['is_latest_restore_point']) {
				wptc_log(array(),'-----------is latest restore point----------------');
				$this->set_option('is_latest_restore_point', true);
			} else {
				$this->set_option('is_latest_restore_point', false);
			}
		}


		if (isset($this->restore_request['ignore_file_write_check']) && !empty($this->restore_request['ignore_file_write_check'])) {
			$this->set_option('check_is_safe_for_write_restore', $this->restore_request['ignore_file_write_check']);
		}
	}

	private function set_option($key, $value, $apply_all = false){

		if (apply_filters('is_restore_to_staging_wptc', '')) {
			$result = apply_filters('set_options_to_staging_site_wptc', $key, $value);
			wptc_log($result,'-----------$result----------------');
			if (!$apply_all) {
				return ;
			}
		}

		$this->config->set_option($key, $value);
	}

	public function set_fs(){
		global $wp_filesystem;

		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (!$wp_filesystem) {
				wptc_die_with_json_encode( array('error' => 'Cannot initiate WordPress file system.') );
			}
		}

		$this->fs = $wp_filesystem;
	}

	public function copy_bridge_files() {

		$this->set_fs();

		wptc_set_time_limit(0);

		$this->config->remove_tmp_dir();

		$config_like_file = $this->create_config_file();

		$bridge_dir = $this->get_bridge_dir();

		if (!empty($bridge_dir['error'])) {
			$this->logger->log('Failed to Copy Bridge files', 'restores', $this->restore_id);
			return $bridge_dir;
		}

		//copy bridge folder
		$plugin_path_tc = $this->get_wptc_plugin_dir();
		$plugin_bridge_file_path = trailingslashit($plugin_path_tc . 'wp-tcapsule-bridge');
		$copy_res = $this->config->tc_file_system_copy_dir($plugin_bridge_file_path, $bridge_dir, array('multicall_exit' => true));

		if (!$copy_res) {
			$this->logger->log('Failed to Copy Bridge files', 'restores', $this->restore_id);
			return array('error' => 'Cannot copy Bridge Directory.');
		}

		$plugin_folders_to_copy = array('Classes', 'Base', 'Dropbox', 'S3', 'Google', 'utils', 'lib');
		foreach ($plugin_folders_to_copy as $v) {
			$plugin_folder = trailingslashit($plugin_path_tc . $v);
			$bridge_dir_sub = trailingslashit($bridge_dir . $v);

			if (!$this->fs->is_dir($bridge_dir_sub)) {
				if (!$this->fs->mkdir($bridge_dir_sub, FS_CHMOD_DIR)) {
					$this->logger->log('Failed to create bridge directory while restoring . Check your folder permissions', 'restores', $this->restore_id);
					return array('error' => 'Cannot create Plugin Directory in bridge.');
				}
			}

			$copy_res = $this->config->tc_file_system_copy_dir($plugin_folder, $bridge_dir_sub, array('multicall_exit' => true));
			if (!$copy_res) {
				$this->logger->log('Failed to Copy Bridge files', 'restores', $this->restore_id);
				return array('error' => 'Cannot copy Plugin Directory(' . $plugin_folder . ').');
			}
		}

		$files_other_than_bridge                              = array();
		$files_other_than_bridge['wp-tc-config.php']          = $config_like_file; //config-like-file which was prepared already
		$files_other_than_bridge['common-functions.php']      = $plugin_path_tc . '/common-functions.php';
		$files_other_than_bridge['wptc-constants.php']        = $plugin_path_tc . '/wptc-constants.php';
		$files_other_than_bridge['restore-progress-ajax.php'] = $plugin_path_tc . '/restore-progress-ajax.php';
		$files_other_than_bridge['wptc-monitor.js']           = $plugin_path_tc . '/Views/wptc-monitor.js';

		if(WPTC_ENV != 'production'){
			$files_other_than_bridge['wptc-env-parameters.php'] = $plugin_path_tc . '/wptc-env-parameters.php';
		}

		foreach ($files_other_than_bridge as $key => $value) {
			$copy_result = $this->config->tc_file_system_copy($value, $bridge_dir . $key, true);
			if (!$copy_result) {
				return array('error' => 'Cannot copy Bridge files(' . $value . ').');
			}
		}

		if (apply_filters('is_restore_to_staging_wptc', '')) {
			if ($this->fs->exists($config_like_file)) {
				$this->fs->delete($config_like_file);
			}
		}

		$this->logger->log('Bridge Files are prepared successfully', 'restores', $this->restore_id);
		return true;
	}

	private function get_wptc_plugin_dir(){
		$plugin_path_tc = $this->fs->wp_plugins_dir() . WPTC_TC_PLUGIN_NAME;
		return trailingslashit($plugin_path_tc);
	}

	private function create_config_file(){
		$config_like_file = $this->create_config_like_file();
		if ($config_like_file) {
			return $config_like_file;
		}

		$this->logger->log('Error Creating config like file.', 'restores', $this->restore_id);
		return array('error' => 'Error Creating config like file.');
	}

	private function get_bridge_dir(){

		if (!apply_filters('is_restore_to_staging_wptc', '')) {
			$bridge_dir = $this->fs->abspath() . $this->config->get_option('current_bridge_file_name');
		} else {
			$bridge_dir = apply_filters('process_staging_details_hook_wptc', array( 'type' => 'get_dir' ) ) .  $this->config->get_option('current_bridge_file_name');
		}

		wptc_log($bridge_dir,'-----------$bridge_dir----------------');

		$bridge_dir = trailingslashit($bridge_dir);

		if ($this->fs->is_dir($bridge_dir)) {
			return $bridge_dir;
		}

		if ($this->fs->mkdir($bridge_dir, FS_CHMOD_DIR)) {
			return $bridge_dir;
		}

		$this->logger->log('Failed to create bridge directory while restoring . Check your folder permissions', 'restores', $this->restore_id);
		return array('error' => 'Cannot create Bridge Directory in root.');
	}

	public function create_config_like_file() {

		global $wpdb;

		if (!apply_filters('is_restore_to_staging_wptc', '')) {
			$base_prefix          = $wpdb->base_prefix;
			$uploads_dir          = WPTC_UPLOADS_DIR;
			$content_dir          = WPTC_WP_CONTENT_DIR;
			$plugin_dir           = WPTC_PLUGIN_DIR;
			$lang_dir             = WP_LANG_DIR;

			if(defined('FTP_CONTENT_DIR'))
				$ftp_content_dir      = FTP_CONTENT_DIR;

			if(defined('FTP_PLUGIN_DIR'))
				$ftp_plugin_dir       = FTP_PLUGIN_DIR;

		} else {
			$base_prefix          = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'get' , 'key' => 'db_prefix' ) );
			$uploads_dir          = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => WPTC_UPLOADS_DIR ) );
			$content_dir          = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => WPTC_WP_CONTENT_DIR ) );
			$lang_dir             = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => WP_LANG_DIR ) );
			$plugin_dir           = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => WPTC_PLUGIN_DIR ) );

			if(defined('FTP_CONTENT_DIR'))
				$ftp_content_dir      = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => FTP_CONTENT_DIR ) );

			if(defined('FTP_PLUGIN_DIR'))
				$ftp_plugin_dir       = apply_filters( 'process_staging_details_hook_wptc', array( 'type' => 'replace' , 'key' => FTP_PLUGIN_DIR ) );
		}

		$contents_to_be_written = "
		<?php
		/** The name of the database for WordPress */
		if(!defined('DB_NAME'))
		define('DB_NAME', '" . DB_NAME . "');

		/** MySQL database username */
		if(!defined('DB_USER'))
		define('DB_USER', '" . DB_USER . "');

		/** MySQL database password */
		if(!defined('DB_PASSWORD'))
		define('DB_PASSWORD', '" . DB_PASSWORD . "');

		/** MySQL hostname */
		if(!defined('DB_HOST'))
		define('DB_HOST', '" . DB_HOST . "');

		/** Database Charset to use in creating database tables. */
		if(!defined('DB_CHARSET'))
		define('DB_CHARSET', '" . DB_CHARSET . "');

		/** The Database Collate type. Don't change this if in doubt. */
		if(!defined('DB_COLLATE'))
		define('DB_COLLATE', '" . DB_COLLATE . "');

		if(!defined('DB_PREFIX_WPTC'))
		define('DB_PREFIX_WPTC', '" . $base_prefix . "');

		if(!defined('DEFAULT_REPO'))
		define('DEFAULT_REPO', '" . DEFAULT_REPO . "');

		if(!defined('WPTC_UPLOADS_DIR'))
		define('WPTC_UPLOADS_DIR', '" .  wp_normalize_path($uploads_dir) . "');

		if(!defined('WPTC_RELATIVE_UPLOADS_DIR'))
		define('WPTC_RELATIVE_UPLOADS_DIR', '" .  wp_normalize_path(WPTC_RELATIVE_UPLOADS_DIR) . "');

		if(!defined('BRIDGE_NAME_WPTC'))
		define('BRIDGE_NAME_WPTC', '" . $this->config->get_option('current_bridge_file_name') . "');

		if (!defined('WP_MAX_MEMORY_LIMIT')) {
			define('WP_MAX_MEMORY_LIMIT', '256M');
		}

		if(!defined('WP_DEBUG'))
		define('WP_DEBUG', false);

		if(!defined('WP_DEBUG_DISPLAY'))
		define('WP_DEBUG_DISPLAY', false);

		if ( !defined('MINUTE_IN_SECONDS') )
		define('MINUTE_IN_SECONDS', 60);
		if ( !defined('HOUR_IN_SECONDS') )
		define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
		if ( !defined('DAY_IN_SECONDS') )
		define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
		if ( !defined('WEEK_IN_SECONDS') )
		define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
		if ( !defined('YEAR_IN_SECONDS') )
		define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);



		/** Absolute path to the WordPress directory. */
		if ( !defined('ABSPATH') )
		define('ABSPATH',  wp_normalize_path(dirname(dirname(__FILE__)) . '/'));

		if ( !defined('WP_CONTENT_DIR') )
		define('WP_CONTENT_DIR',  wp_normalize_path('" . $content_dir . "'));

		if ( !defined('WP_LANG_DIR') )
		define('WP_LANG_DIR',  wp_normalize_path('" . $lang_dir . "'));

		if(!defined('WP_PLUGIN_DIR'))
		define('WP_PLUGIN_DIR', '" .  $plugin_dir . "');

			  ";

		if (defined('MULTISITE')) {
			$contents_to_be_written .= "
		define('MULTISITE', '" . MULTISITE . "');
			";
		}

		if (defined('FS_METHOD')) {
			$contents_to_be_written .= "
		define('FS_METHOD', '" . FS_METHOD . "');
			";
		}
		if (defined('FTP_BASE')) {
			$contents_to_be_written .= "
		define('FTP_BASE', '" . FTP_BASE . "');
			";
		}
		if (defined('FTP_USER')) {
			$contents_to_be_written .= "
		define('FTP_USER', '" . FTP_USER . "');
			";
		}
		if (defined('FTP_PASS')) {
			$contents_to_be_written .= "
		define('FTP_PASS', '" . FTP_PASS . "');
			";
		}
		if (defined('FTP_HOST')) {
			$contents_to_be_written .= "
		define('FTP_HOST', '" . FTP_HOST . "');
			";
		}
		if (defined('FTP_SSL')) {
			$contents_to_be_written .= "
		define('FTP_SSL', '" . FTP_SSL . "');
			";
		}
		if (defined('FTP_CONTENT_DIR')) {
			$contents_to_be_written .= "
		define('FTP_CONTENT_DIR', '" . $ftp_content_dir . "');
			";
		}
		if (defined('FTP_PLUGIN_DIR')) {
			$contents_to_be_written .= "
		define('FTP_PLUGIN_DIR', '" . FTP_PLUGIN_DIR . "');
			";
		}
		if (defined('FTP_PUBKEY')) {
			$contents_to_be_written .= "
		define('FTP_PUBKEY', '" . FTP_PUBKEY . "');
			";
		}
		if (defined('FTP_PRIKEY')) {
			$contents_to_be_written .= "
		define('FTP_PRIKEY', '" . FTP_PRIKEY . "');
			";
		}

		$dump_dir = $this->config->get_backup_dir();

		$dump_dir = $this->config->wp_filesystem_safe_abspath_replace($dump_dir);

		$dump_dir_parent = trailingslashit(dirname($dump_dir));

		$config_like_file = $dump_dir_parent . 'config-like-file.php';

		$result = $this->fs->put_contents($config_like_file, $contents_to_be_written, 0644);

		if (!$result) {
			wptc_log($config_like_file, "--------create_config_like_file--------");
			return false;
		}

		return $config_like_file;
	}


}
