<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No access.');

/*
	- A container for all the remote commands implemented. Commands map exactly onto method names (and hence this class should not implement anything else, beyond the constructor, and private methods)
	- Return format is either to return data (boolean, string, array), or an WP_Error object
	Commands are not allowed to begin with an underscore. So, any private methods can be prefixed with an underscore.
	TODO: Many of these just verify input, and then call back into a relevant method in UpdraftPlus_Admin. Once all commands have been ported over to go via this class, those methods in UpdraftPlus_Admin can generally be folded into the relevant method in here, and removed from UpdraftPlus_Admin. (Since this class is intended to become the official way of performing actions). As a bonus, we then won't need so much _load_ud(_admin) boilerplate.
*/

if (class_exists('UpdraftPlus_Commands')) return;

class UpdraftPlus_Commands {

	private $_uc_helper;

	/**
	 * Constructor
	 *
	 * @param Class $uc_helper The 'helper' needs to provide the method _updraftplus_background_operation_started
	 */
	public function __construct($uc_helper) {
		$this->_uc_helper = $uc_helper;
	}

	/**
	 * Get the Advanced Tools HTMl and return to Central
	 *
	 * @param  string $options Options for advanced settings
	 * @return string
	 */
	public function get_advanced_settings($options) {
		// load global updraftplus and admin
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$html = $updraftplus_admin->settings_advanced_tools(true, array('options' => $options));
		
		return $html;
	}

	public function get_download_status($items) {
		// load global updraftplus and admin
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		if (!is_array($items)) $items = array();

		return $updraftplus_admin->get_download_statuses($items);
	
	}
	
	/**
	 * Begin a download process
	 *
	 * @param Array $downloader_params - download parameters (findex, type, timestamp, stage)
	 *
	 * @return Array - as from UpdraftPlus_Admin::do_updraft_download_backup() (with 'request' key added, with value $downloader_params)
	 */
	public function downloader($downloader_params) {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$findex = $downloader_params['findex'];
		$type = $downloader_params['type'];
		$timestamp = $downloader_params['timestamp'];
		// Valid stages: 2='spool the data'|'delete'='delete local copy'|anything else='make sure it is present'
		$stage = empty($downloader_params['stage']) ? false : $downloader_params['stage'];
	
		// This may, or may not, return, depending upon whether the files are already downloaded
		// The response is usually an array with key 'result', and values deleted|downloaded|needs_download|download_failed
		$response = $updraftplus_admin->do_updraft_download_backup($findex, $type, $timestamp, $stage, array($this->_uc_helper, '_updraftplus_background_operation_started'));
	
		if (is_array($response)) {
			$response['request'] = $downloader_params;
		}
	
		return $response;
	}
	
	public function delete_downloaded($set_info) {
		$set_info['stage'] = 'delete';
		return $this->downloader($set_info);
	}
	
	/**
	 * Get backup progress (as HTML) for a particular backup
	 *
	 * @param Array $params - should have a key 'job_id' with corresponding value
	 *
	 * @return String - the HTML
	 */
	public function backup_progress($params) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$request = array(
			'thisjobonly' => $params['job_id']
		);
		$activejobs_list = $updraftplus_admin->get_activejobs_list($request);
		
		return $activejobs_list;
	
	}
	
	public function backupnow($params) {
		
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!empty($params['updraftplus_clone_backup'])) {
			add_filter('updraft_backupnow_options', array($updraftplus, 'updraftplus_clone_backup_options'), 10, 2);
			add_filter('updraftplus_initial_jobdata', array($updraftplus, 'updraftplus_clone_backup_jobdata'), 10, 3);
		}

		if (!empty($params['db_anon_all']) || !empty($params['db_anon_non_staff'])) {
			if (!class_exists('UpdraftPlus_Anonymisation_Functions')) include_once(UPDRAFTPLUS_DIR.'/addons/anonymisation.php');

			add_filter('updraft_backupnow_options', 'UpdraftPlus_Anonymisation_Functions::updraftplus_backup_anonymisation_options', 10, 2);
			add_filter('updraftplus_initial_jobdata', 'UpdraftPlus_Anonymisation_Functions::updraftplus_backup_anonymisation_jobdata', 10, 2);
		}

		$background_operation_started_method_name = empty($params['background_operation_started_method_name']) ? '_updraftplus_background_operation_started' : $params['background_operation_started_method_name'];
		$updraftplus_admin->request_backupnow($params, array($this->_uc_helper, $background_operation_started_method_name));
		
		// Control returns when the backup finished; but, the browser connection should have been closed before
		die;
	}
	
	/**
	 * Mark a backup as "do not delete"
	 *
	 * @param array $params this is an array of parameters sent via ajax it can include the following:
	 * backup_key - Integer - backup timestamp
	 * always_keep - Boolean - "Always keep" value
	 * @return array which contains rawbackup html
	 */
	public function always_keep_this_backup($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		$backup_key = $params['backup_key'];
		$backup_history = UpdraftPlus_Backup_History::get_history();
		if (empty($params['always_keep'])) {
			unset($backup_history[$backup_key]['always_keep']);
		} else {
			$backup_history[$backup_key]['always_keep'] = true;
		}
		UpdraftPlus_Backup_History::save_history($backup_history);
		$nonce = $backup_history[$backup_key]['nonce'];
		$rawbackup = $updraftplus_admin->raw_backup_info($backup_history, $backup_key, $nonce);
		return array(
			'rawbackup' => html_entity_decode($rawbackup),
		);
	}

	/**
	 * Function to retrieve raw backup history given a timestamp and nonce
	 *
	 * @param Array $data - Data parameter; keys: timestamp, nonce
	 *
	 * @return Array - An array that contains the raw backup history
	 */
	public function rawbackup_history($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		$history = UpdraftPlus_Backup_History::get_history();

		$rawbackup = $updraftplus_admin->raw_backup_info($history, $data['timestamp'], $data['nonce'], null);

		return array(
			'rawbackup' => html_entity_decode($rawbackup),
		);
	}
	
	private function _load_ud() {
		global $updraftplus;
		return is_a($updraftplus, 'UpdraftPlus') ? $updraftplus : false;
	}
	
	private function _load_ud_admin() {
		if (!defined('UPDRAFTPLUS_DIR') || !is_file(UPDRAFTPLUS_DIR.'/admin.php')) return false;
		include_once(UPDRAFTPLUS_DIR.'/admin.php');
		global $updraftplus_admin;
		return $updraftplus_admin;
	}
	
	public function get_log($job_id = '') {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
	
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if ('' != $job_id && !preg_match("/^[0-9a-f]{12}$/", $job_id)) return new WP_Error('updraftplus_permission_invalid_jobid');
		
		return $updraftplus_admin->fetch_log($job_id);
	
	}
	
	public function activejobs_delete($job_id) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->activejobs_delete((string) $job_id);

	}
	
	public function deleteset($what) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = $updraftplus_admin->delete_set($what);
	
		$get_history_opts = isset($what['get_history_opts']) ? $what['get_history_opts'] : array();
	
		$backup_history = UpdraftPlus_Backup_History::get_history();
	
		$results['history'] = $updraftplus_admin->settings_downloading_and_restoring($backup_history, true, $get_history_opts);

		$results['backupnow_file_entities'] = apply_filters('updraftplus_backupnow_file_entities', array());
		$results['modal_afterfileoptions'] = apply_filters('updraft_backupnow_modal_afterfileoptions', '', '');
		
		$results['count_backups'] = count($backup_history);

		return $results;
	
	}
	
	/**
	 * Slightly misnamed - this doesn't always rescan, but it does always return the history status (possibly after a rescan)
	 *
	 * @param  Array|String $data - with keys 'operation' and 'debug'; or, if a string (backwards compatibility), just the value of the 'operation' key (with debug assumed as 0)
	 *
	 * @return Array - returns an array of history statuses
	 */
	public function rescan($data) {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$backup_count = 0;

		if (is_array($data)) {
			$operation = empty($data['operation']) ? '' : $data['operation'];
			$debug = !empty($data['debug']);
			$backup_count = empty($data['backup_count']) ? 0 : $data['backup_count'];
		} else {
			$operation = $data;
			$debug = false;
		}
	
		$remotescan = ('remotescan' == $operation);
		$rescan = ($remotescan || 'rescan' == $operation);
		
		$history_status = $updraftplus_admin->get_history_status($rescan, $remotescan, $debug, $backup_count);
		$history_status['backupnow_file_entities'] = apply_filters('updraftplus_backupnow_file_entities', array());
		$history_status['modal_afterfileoptions'] = apply_filters('updraft_backupnow_modal_afterfileoptions', '', '');

		return $history_status;
		
	}
	
	public function get_settings($options) {
		global $updraftplus;
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		ob_start();
		$updraftplus_admin->settings_formcontents($options);
		$output = ob_get_contents();
		ob_end_clean();
		
		$remote_storage_options_and_templates = UpdraftPlus_Storage_Methods_Interface::get_remote_storage_options_and_templates();
		
		return array(
			'settings' => $output,
			'remote_storage_options' => $remote_storage_options_and_templates['options'],
			'remote_storage_templates' => $remote_storage_options_and_templates['templates'],
			'meta' => apply_filters('updraftplus_get_settings_meta', array()),
			'updraftplus_version' => $updraftplus->version,
		);
		
	}
	
	/**
	 * Run a credentials test
	 *
	 * @param Array $test_data - test configuration
	 *
	 * @return WP_Error|Array - test results (keys: results, (optional)data), or an error
	 */
	public function test_storage_settings($test_data) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = $updraftplus_admin->do_credentials_test($test_data, true);
	
		return $results;
	
	}
	
	/**
	 * Perform a connection test on a database
	 *
	 * @param Array $info - test parameters
	 *
	 * @return Array - test results
	 */
	public function extradb_testconnection($info) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = apply_filters('updraft_extradb_testconnection_go', array(), $info);
	
		return $results;
	
	}
	
	/**
	 * This method will make a call to the methods responsible for recounting the quota in the UpdraftVault account
	 *
	 * @param  array $params - an array of parameters such as a instance_id
	 * @return string - the result of the call
	 */
	public function vault_recountquota($params = array()) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$instance_id = empty($params['instance_id']) ? '' : $params['instance_id'];

		$vault = $updraftplus_admin->get_updraftvault($instance_id);

		return $vault->ajax_vault_recountquota(false);
	}
	
	/**
	 * This method will make a call to the methods responsible for creating a connection to UpdraftVault
	 *
	 * @param  array $credentials - an array of parameters such as the user credentials and instance_id
	 * @return string - the result of the call
	 */
	public function vault_connect($credentials) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$instance_id = empty($credentials['instance_id']) ? '' : $credentials['instance_id'];

		return $updraftplus_admin->get_updraftvault($instance_id)->ajax_vault_connect(false, $credentials);
	
	}
	
	/**
	 * This method will make a call to the methods responsible for removing a connection to UpdraftVault
	 *
	 * @param array $params - an array of parameters such as a instance_id
	 * @return string - the result of the call
	 */
	public function vault_disconnect($params = array()) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$echo_results = empty($params['immediate_echo']) ? false : true;

		$instance_id = empty($params['instance_id']) ? '' : $params['instance_id'];
		
		$results = (array) $updraftplus_admin->get_updraftvault($instance_id)->ajax_vault_disconnect($echo_results);

		return $results;
	
	}
	
	/**
	 * A handler method to call the UpdraftPlus admin save settings method. It will check if the settings passed to it are in the format of a string if so it converts it to an array otherwise just pass the array
	 *
	 * @param  String/Array $settings Settings to be saved to UpdraftPlus either in the form of a string ready to be converted to an array or already an array ready to be passed to the save settings function in UpdraftPlus.
	 * @return Array An Array response to be sent back
	 */
	public function save_settings($settings) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!empty($settings)) {

			if (is_string($settings)) {
				parse_str($settings, $settings_as_array);
			} elseif (is_array($settings)) {
				$settings_as_array = $settings;
			} else {
				return new WP_Error('invalid_settings');
			}
		}
		
		$results = $updraftplus_admin->save_settings($settings_as_array);

		return $results;
	
	}
	
	public function s3_newuser($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$results = apply_filters('updraft_s3_newuser_go', array(), $data);
		
		return $results;
	}
	
	public function cloudfiles_newuser($data) {
	
		global $updraftplus_addon_cloudfilesenhanced;
		if (!is_a($updraftplus_addon_cloudfilesenhanced, 'UpdraftPlus_Addon_CloudFilesEnhanced')) {
			$data = array('e' => 1, 'm' => sprintf(__('%s add-on not found', 'updraftplus'), 'Rackspace Cloud Files'));
		} else {
			$data = $updraftplus_addon_cloudfilesenhanced->create_api_user($data);
		}
		
		if (0 === $data['e']) {
			return $data;
		} else {
			return new WP_Error('error', '', $data);
		}
	}
	
	/**
	 * Get an HTML fragment
	 *
	 * @param String|Array $fragment - what fragment to fetch. If an array, the fragment identifier is in 'fragment' (and 'data' is associated data)
	 *
	 * @return Array|WP_Error
	 */
	public function get_fragment($fragment) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (is_array($fragment)) {
			$data = $fragment['data'];
			$fragment = $fragment['fragment'];
		}
		
		$error = false;
		
		switch ($fragment) {
		
			case 'last_backup_html':
					$output = $updraftplus_admin->last_backup_html();
				break;
			
			case 's3_new_api_user_form':
				ob_start();
				do_action('updraft_s3_print_new_api_user_form', false);
				$output = ob_get_contents();
				ob_end_clean();
				break;
				
			case 'cloudfiles_new_api_user_form':
				global $updraftplus_addon_cloudfilesenhanced;
				if (!is_a($updraftplus_addon_cloudfilesenhanced, 'UpdraftPlus_Addon_CloudFilesEnhanced')) {
						$error = true;
						$output = 'cloudfiles_addon_not_found';
				} else {
					$output = array(
						'accounts' => $updraftplus_addon_cloudfilesenhanced->account_options(),
						'regions' => $updraftplus_addon_cloudfilesenhanced->region_options(),
					);
				}
				break;
			
			case 'backupnow_modal_contents':
				$updraft_dir = $updraftplus->backups_dir_location();
				if (!UpdraftPlus_Filesystem_Functions::really_is_writable($updraft_dir)) {
					$output = array('error' => true, 'html' => __("The 'Backup Now' button is disabled as your backup directory is not writable (go to the 'Settings' tab and find the relevant option).", 'updraftplus'));
				} else {
					$output = array('html' => $updraftplus_admin->backupnow_modal_contents(), 'backupnow_file_entities' => apply_filters('updraftplus_backupnow_file_entities', array()), 'incremental_installed' => apply_filters('updraftplus_incremental_addon_installed', false));
				}
				break;
			
			case 'panel_download_and_restore':
				$backup_history = UpdraftPlus_Backup_History::get_history();
				$output = $updraftplus_admin->settings_downloading_and_restoring($backup_history, true, $data);
				break;
			
			case 'disk_usage':
				$output = UpdraftPlus_Filesystem_Functions::get_disk_space_used($data);
				break;
			default:
				// We just return a code - translation is done on the other side
				$output = 'ud_get_fragment_could_not_return';
				$error = true;
				break;
		}
		
		if (!$error) {
			return array(
				'output' => $output,
			);
		} else {
			return new WP_Error('get_fragment_error', '', $output);
		}
		
	}
	
	/**
	 * This gets the http_get function from admin to grab information on a url
	 *
	 * @param  string $uri URL to be used
	 * @return array returns response from specific URL
	 */
	public function http_get($uri) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (empty($uri)) {
			return new WP_Error('error', '', 'no_uri');
		}
		
		$response = $updraftplus_admin->http_get($uri, false);
		$response_decode = json_decode($response);

		if (isset($response_decode->e)) {
		  return new WP_Error('error', '', htmlspecialchars($response_decode->e));
		}

		return array('status' => $response_decode->code, 'response' => $response_decode->html_response);
	}

	/**
	 * This gets the http_get function from admin to grab cURL information on a url
	 *
	 * @param  string $uri URL to be used
	 * @return array
	 */
	public function http_get_curl($uri) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (empty($uri)) {
			return new WP_Error('error', '', 'no_uri');
		}
		
		if (!function_exists('curl_exec')) {
			return new WP_Error('error', '', 'no_curl');
		}
		
		$response_encode = $updraftplus_admin->http_get($uri, true);
		$response_decode = json_decode($response_encode);

		$response = 'Curl Info: ' . $response_decode->verb
					.'Response: ' . $response_decode->response;

		if (false === $response_decode->response) {
			return new WP_Error('error', '', array(
				'error' => htmlspecialchars($response_decode->e),
				"status" => $response_decode->status,
				"log" => htmlspecialchars($response_decode->verb)
			));
		}
		
		return array(
			'response'=> htmlspecialchars(substr($response, 0, 2048)),
			'status'=> $response_decode->status,
			'log'=> htmlspecialchars($response_decode->verb)
		);
	}

	/**
	 * Display raw backup and file list
	 *
	 * @return string
	 */
	public function show_raw_backup_and_file_list() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		/*
			Need to remove the pre tags as the modal assumes a <pre> is for a new box.
			This cause issues specifically with fetch log events. Do this by passing true
			to the method show_raw_backups
		 */
		
		$response = $updraftplus_admin->show_raw_backups(true);

		return $response['html'];
	}

	public function reset_site_id() {
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		delete_site_option('updraftplus-addons_siteid');
		return $updraftplus->siteid();
	}

	public function search_replace($query) {

		if (!class_exists('UpdraftPlus_Addons_Migrator')) {
			return new WP_Error('error', '', 'no_class_found');
		}
		
		global $updraftplus_addons_migrator;
		
		if (!is_a($updraftplus_addons_migrator, 'UpdraftPlus_Addons_Migrator')) {
			return new WP_Error('error', 'no_object_found');
		}

		$_POST = $query;
		
		ob_start();

		do_action('updraftplus_adminaction_searchreplace', $query);
		
		$response = array('log' => ob_get_clean());
		
		return $response;
	}

	public function change_lock_settings($data) {
		global $updraftplus_addon_lockadmin;
		
		if (!class_exists('UpdraftPlus_Addon_LockAdmin')) {
			return new WP_Error('error', '', 'no_class_found');
		}
		
		if (!is_a($updraftplus_addon_lockadmin, "UpdraftPlus_Addon_LockAdmin")) {
			return new WP_Error('error', '', 'no_object_found');
		}

		$session_length = empty($data["session_length"]) ? '' : $data["session_length"];
		$password 		= empty($data["password"]) ? '' : $data["password"];
		$old_password 	= empty($data["old_password"]) ? '' : $data["old_password"];
		$support_url 	= $data["support_url"];
		
		$user = wp_get_current_user();
		if (0 == $user->ID) {
			return new WP_Error('no_user_found');
		}
		
		$options = $updraftplus_addon_lockadmin->return_opts();

		if ($old_password == $options['password']) {
			
			$options['password'] = (string) $password;
			$options['support_url'] = (string) $support_url;
			$options['session_length'] = (int) $session_length;
			UpdraftPlus_Options::update_updraft_option('updraft_adminlocking', $options);
						
			return "lock_changed";
		} else {
			return new WP_Error('error', '', 'wrong_old_password');
		}
	}

	public function delete_key($key_id) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = $updraftcentral_main->delete_key($key_id);
		return $response;
		
	}
	
	public function create_key($data) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = call_user_func(array($updraftcentral_main, 'create_key'), $data);
		
		return $response;
	}
	
	public function fetch_log($data) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = call_user_func(array($updraftcentral_main, 'get_log'), $data);
		return $response;
	}

	/**
	 * A handler method to call the UpdraftPlus admin auth_remote_method
	 *
	 * @param Array - $data It consists of below key elements:
	 *                $remote_method - Remote storage service
	 *                $instance_id - Remote storage instance id
	 * @return Array An Array response to be sent back
	 */
	public function auth_remote_method($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$response = $updraftplus_admin->auth_remote_method($data);
		return $response;
	}

	/**
	 * A handler method to call the UpdraftPlus admin deauth_remote_method
	 *
	 * @param Array - $data It consists of below key elements:
	 *                $remote_method - Remote storage service
	 *                $instance_id - Remote storage instance id
	 * @return Array An Array response to be sent back
	 */
	public function deauth_remote_method($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$response = $updraftplus_admin->deauth_remote_method($data);
		return $response;
	}

	/**
	 * A handler method to call the relevant remote storage manual authentication methods and return the authentication result
	 *
	 * @param array $data - an array of authentication data, normally includes the state and auth code
	 *
	 * @return array - an array response to be sent back to the frontend
	 */
	public function manual_remote_storage_authentication($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$response = array(
			'result' => 'success'
		);

		$method = $data['method'];

		$enabled_services = UpdraftPlus_Storage_Methods_Interface::get_enabled_storage_objects_and_ids(array($method));
		
		if (empty($enabled_services[$method]['object']) || empty($enabled_services[$method]['instance_settings']) || !$enabled_services[$method]['object']->supports_feature('manual_authentication')) {
			$response['result'] = 'error';
			$response['data'] = __('Manual authentication is not available for this remote storage method', 'updraftplus') . '(' . $method . ')';
			return $response;
		}

		$backup_obj = $enabled_services[$method]['object'];

		$auth_data = json_decode(base64_decode($data['auth_data']), true);
		$instance_id = '';

		$state = isset($auth_data['state']) ? urldecode($auth_data['state']) : '';
		$code = isset($auth_data['code']) ? urldecode($auth_data['code']) : '';

		if (empty($code) && isset($auth_data['access_token']) && isset($auth_data['user_id'])) {
			// If there is no code, but the access_token and user_id is set then this is for Google Drive so create a code array using these values
			$access_token = urldecode($auth_data['access_token']);
			$user_id = urldecode($auth_data['user_id']);
			$code = array(
				'access_token' => $access_token,
				'user_id' => $user_id
			);
		} elseif (empty($code) && isset($auth_data['token'])) {
			// If there is no code, but a token is set then this is for OneDrive so assign token to code
			$encoded_token = stripslashes($auth_data['token']);
			$token = json_decode($encoded_token);
			$code = $token;
		}

		if (empty($state) || empty($code)) {
			$response['result'] = 'error';
			$response['data'] = __('Missing authentication data:', 'updraftplus') . " ({$state}) ({$code})";
			return $response;
		}

		if (false !== strpos($state, ':')) {
			$parts = explode(':', $state);
			$instance_id = $parts[1];
		}

		if (empty($instance_id)) {
			$response['result'] = 'error';
			$response['data'] = __('Missing instance id:', 'updraftplus') . " ($state)";
			return $response;
		}

		if (isset($enabled_services[$method]['instance_settings'][$instance_id])) {
			$opts = $enabled_services[$method]['instance_settings'][$instance_id];
			$backup_obj->set_options($opts, false, $instance_id);
		}

		$result = $backup_obj->complete_authentication($state, $code, true);
		
		$response['data'] = $result;

		return $response;
	}
	
	/**
	 * A handler method to call the UpdraftPlus admin wipe settings method
	 *
	 * @return Array An Array response to be sent back
	 */
	public function wipe_settings() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		// pass false to this method so that it does not remove the UpdraftCentral key
		$response = $updraftplus_admin->wipe_settings(false);

		return $response;
	}

	/**
	 * Retrieves backup information (next scheduled backups, last backup jobs and last log message)
	 * for UpdraftCentral consumption
	 *
	 * @return Array An array containing the results of the backup information retrieval
	 */
	public function get_backup_info() {
		try {
			
			// load global updraftplus admin
			if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

			ob_start();
			$updraftplus_admin->next_scheduled_backups_output();
			$next_scheduled_backups = ob_get_clean();

			$response = array(
				'next_scheduled_backups' => $next_scheduled_backups,
				'last_backup_job' => $updraftplus_admin->last_backup_html(),
				'last_log_message' => UpdraftPlus_Options::get_updraft_lastmessage()
			);

			$updraft_last_backup = UpdraftPlus_Options::get_updraft_option('updraft_last_backup', false);
			$backup_history = UpdraftPlus_Backup_History::get_history();
			
			if (false !== $updraft_last_backup && !empty($backup_history)) {
				$backup_nonce = $updraft_last_backup['backup_nonce'];

				$response['backup_nonce'] = $backup_nonce;
				$response['log'] = $this->get_log($backup_nonce);
			}

		} catch (Exception $e) {
			$response = array('error' => true, 'message' => $e->getMessage());
		}

		return $response;
	}

	/**
	 * This method will check the connection status to UpdraftPlus.com using the submitted credentials and return the result of that check.
	 *
	 * @param  array $data - an array that contains the users UpdraftPlus.com credentials
	 *
	 * @return array       - an array with the result of the connection status
	 */
	public function updraftplus_com_login_submit($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		global $updraftplus_addons2, $updraftplus;

		$options = $updraftplus_addons2->get_option(UDADDONS2_SLUG.'_options');
		$new_options = $data['data'];
		
		// Check if we can make a connection if we can then we don't want to reset the options in the case where the user has removed their password from the form
		$result = !empty($options['email']) ? $updraftplus_addons2->connection_status() : false;
		
		if (true !== $result) {
			// We failed to make a connection so try the new options
			$updraftplus_addons2->update_option(UDADDONS2_SLUG.'_options', $new_options);
			$result = $updraftplus_addons2->connection_status();
		}

		if (true !== $result) {
			if (is_wp_error($result)) {
				$connection_errors = array();
				foreach ($result->get_error_messages() as $msg) {
					$connection_errors[] = $msg;
				}
			} else {
				if (!empty($options['email']) && !empty($options['password'])) $connection_errors = array(__('An unknown error occurred when trying to connect to UpdraftPlus.Com', 'updraftplus'));
			}
			$result = false;
		}
		if ($result && isset($new_options['auto_update'])) {
			$updraftplus->set_automatic_updates($new_options['auto_update']);
		}
		if ($result) {
			return array(
				'success' => true
			);
		} else {
			// There was an error reset the options so that we don't get unwanted notices on the dashboard.
			$updraftplus_addons2->update_option(UDADDONS2_SLUG.'_options', array('email' => '', 'password' => ''));

			return array(
				'error' => true,
				'message' => $connection_errors
			);
		}
	}

	/**
	 * This function will add some needed filters in order to be able to send a local backup to remote storage it will then boot the backup process.
	 *
	 * @param array $data - data sent from the front end, it includes the backup timestamp and nonce
	 *
	 * @return array      - the response to be sent back to the front end
	 */
	public function upload_local_backup($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		add_filter('updraftplus_initial_jobdata', array($updraftplus_admin, 'upload_local_backup_jobdata'), 10, 3);
		add_filter('updraftplus_get_backup_file_basename_from_time', array($updraftplus_admin, 'upload_local_backup_name'), 10, 3);
		
		$background_operation_started_method_name = empty($data['background_operation_started_method_name']) ? '_updraftplus_background_operation_started' : $data['background_operation_started_method_name'];

		$msg = array(
			'nonce' => $data['use_nonce'],
			'm' => apply_filters('updraftplus_backupnow_start_message', '<strong>'.__('Start backup', 'updraftplus').':</strong> '.htmlspecialchars(__('OK. You should soon see activity in the "Last log message" field below.', 'updraftplus')), $data['use_nonce'])
		);

		$close_connection_callable = array($this->_uc_helper, $background_operation_started_method_name);

		if (is_callable($close_connection_callable)) {
			call_user_func($close_connection_callable, $msg);
		} else {
			$updraftplus->close_browser_connection(json_encode($msg));
		}

		do_action('updraft_backupnow_backup_all', apply_filters('updraft_backupnow_options', $data, array()));

		// Control returns when the backup finished; but, the browser connection should have been closed before
		die;
	}

	/**
	 * Pre-check before sending request and delegates login request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftcentral_login($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->get_updraftcentral_cloud()->ajax_process_login($params);
	}

	/**
	 * Pre-check before sending request and delegates registration request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftcentral_registration($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->get_updraftcentral_cloud()->ajax_process_registration($params);
	}

	/**
	 * Pre-check before sending request and delegates login request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftplus_clone_login($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!defined('UPDRAFTPLUS_DO_NOT_USE_IPINFO') || !UPDRAFTPLUS_DO_NOT_USE_IPINFO) {
			// Try to get the users region code we can then use this to find their closest clone region
			$response = wp_remote_get('https://ipinfo.io/json', array(
				'timeout' => 3,
				// The API always returns 429 rate limit unless this header is passed
				'headers' => array(
					'Referer' => network_site_url()
				)
			));
			
			if (200 === wp_remote_retrieve_response_code($response)) {
				$body = wp_remote_retrieve_body($response);
				$response = json_decode($body, true);
				if (isset($response['country'])) $params['form_data']['country_code'] = $response['country'];
			}
		}
		
		$response = $updraftplus->get_updraftplus_clone()->ajax_process_login($params, false);
		
		if (isset($response['status']) && 'authenticated' == $response['status']) {
			$tokens = isset($response['tokens']) ? $response['tokens'] : 0;
			$content = '<div class="updraftclone-main-row">';
			$content .= '<div class="updraftclone-tokens">';
			$content .= '<p>' . __("Available temporary clone tokens:", "updraftplus") . ' <span class="tokens-number">' . esc_html($tokens) . '</span></p>';
			$content .= '<p><a href="'.$updraftplus->get_url('buy-tokens').'">'.__('You can buy more temporary clone tokens here.', 'updraftplus').'</a></p>';
			$content .= '</div>';
			
			if (0 != $response['tokens']) {
				$is_vps_tester = !empty($response['is_vps_tester']);
				$supported_wp_versions = isset($response['supported_wp_versions']) ? $response['supported_wp_versions'] : array();
				$supported_packages = isset($response['supported_packages']) ? $response['supported_packages'] : array();
				$supported_regions = isset($response['supported_regions']) ? $response['supported_regions'] : array();
				$nearest_region = isset($response['nearest_region']) ? $response['nearest_region'] : '';
				$content .= '<div class="updraftclone_action_box">';
				$content .= $updraftplus_admin->updraftplus_clone_ui_widget($is_vps_tester, $supported_wp_versions, $supported_packages, $supported_regions, $nearest_region);
				$content .= '<p class="updraftplus_clone_status"></p>';
				$content .= '<button id="updraft_migrate_createclone" class="button button-primary button-hero" data-clone_id="'.$response['clone_info']['id'].'" data-secret_token="'.$response['clone_info']['secret_token'].'">'. __('Create clone', 'updraftplus') . '</button>';
				$content .= '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span><br>';
				$content .= '<div id="ud_downloadstatus3"></div>';
				$content .= '</div>';
			}
			$content .= '</div>'; // end .updraftclone-main-row

			$content .= isset($response['clone_list']) ? '<div class="clone-list"><h3>'.__('Current clones', 'updraftplus').' - <a target="_blank" href="https://updraftplus.com/my-account/clones/">'.__('manage', 'updraftplus').'</a></h3>'.$response['clone_list'].'</div>' : '';

			$response['html'] = $content;
		}

		return $response;
	}

	/**
	 * This function sends the request to create the clone
	 *
	 * @param array $params - The submitted data
	 * @return string - the result of the call
	 */
	public function process_updraftplus_clone_create($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$response = $updraftplus->get_updraftplus_clone()->ajax_process_clone($params);
		
		if (!isset($response['status']) && 'success' != $response['status']) return $response;

		$content = '';
		
		if (isset($response['data'])) {
			$tokens = isset($response['data']['tokens']) ? $response['data']['tokens'] : 0;
			$url = isset($response['data']['url']) ? $response['data']['url'] : '';
			
			if (isset($response['data']['secret_token'])) {
				$response['secret_token'] = $response['data']['secret_token'];
				unset($response['data']['secret_token']);
			}

			$content .= '<div class="updraftclone-main-row">';

			$content .= '<div class="updraftclone-tokens">';
			$content .= '<p>' . __("Available temporary clone tokens:", "updraftplus") . ' <span class="tokens-number">' . esc_html($tokens) . '</span></p>';
			$content .= '</div>';

			$content .= '<div class="updraftclone_action_box">';
			
			$content .= $updraftplus_admin->updraftplus_clone_info($url);

			$content .= '</div>';

			$content .= '</div>'; // end .updraftclone-main-row
		}
		if (isset($params['form_data']['install_info']['wp_only'])) {
			$content .= '<p id="updraft_clone_progress">' . __('No backup will be started. The creation of your clone should now begin, and your WordPress username and password will be displayed below when ready.', 'updraftplus') . ' ' . __('N.B. You will be charged one token once the clone is ready. If the clone fails to boot, then the token will be released within an hour.', 'updraftplus') . '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span></p>';
		} else {
			$content .= '<p id="updraft_clone_progress">' . __('The creation of your data for creating the clone should now begin.', 'updraftplus') . ' ' . __('N.B. You will be charged one token once the clone is ready. If the clone fails to boot, then the token will be released within an hour.', 'updraftplus') . '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span></p>';
			$content .= '<div id="updraft_clone_activejobsrow" style="display:none;"></div>';
		}

		$response['html'] = $content;
		$response['url'] = $url;
		$response['key'] = '';

		return $response;
	}

	/**
	 * This function will get the clone network and credential info
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array|WP_Error - the response array that includes the credential info or a WP_Error
	 */
	public function process_updraftplus_clone_poll($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$response = $updraftplus->get_updraftplus_clone()->clone_info_poll($params);

		return $response;
	}

	/**
	 * This function will get the clone netowrk info HTML for the passed in clone URL
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array        - the response array that includes the network HTML
	 */
	public function get_clone_network_info($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$url = empty($params['clone_url']) ? '' : $params['clone_url'];

		$response = array();

		$response['html'] = $updraftplus_admin->updraftplus_clone_info($url);

		return $response;
	}

	/**
	 * This function will get the restore resume notice
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array|WP_Error - the response array that includes the restore resume notice
	 */
	public function get_restore_resume_notice($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$job_id = empty($params['job_id']) ? '' : $params['job_id'];

		$response = array(
			'status' => 'success',
		);

		if (empty($job_id)) return new WP_Error('missing_parameter', 'Missing parameters.');

		$html = $updraftplus_admin->get_restore_resume_notice($job_id);

		if (is_wp_error($html)) return $html;

		$response['html'] = $html;

		return $response;
	}
}
