<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * - A container for RPC commands (core UpdraftCentral commands). Commands map exactly onto method names (and hence this class should not implement anything else, beyond the constructor, and private methods)
 * - Return format is array('response' => (string - a code), 'data' => (mixed));
 *
 * RPC commands are not allowed to begin with an underscore. So, any private methods can be prefixed with an underscore.
 */
class UpdraftCentral_Core_Commands extends UpdraftCentral_Commands {

	/**
	 * Executes a list of submitted commands (multiplexer)
	 *
	 * @param Array $query An array containing the commands to execute and a flag to indicate how to handle command execution failure.
	 * @return Array An array containing the results of the process.
	 */
	public function execute_commands($query) {

		try {

			$commands = $query['commands'];
			$command_results = array();
			$error_count = 0;

			/**
			 * Should be one of the following options:
			 * 1 = Abort on first failure
			 * 2 = Abort if any command fails
			 * 3 = Abort if all command fails (default)
			 */
			$error_flag = isset($query['error_flag']) ? (int) $query['error_flag'] : 3;

			
			foreach ($commands as $command => $params) {
				$command_info = apply_filters('updraftcentral_get_command_info', false, $command);
				if (!$command_info) {
					list($_prefix, $_command) = explode('.', $command);
					$command_results[$_prefix][$_command] = array('response' => 'rpcerror', 'data' => array('code' => 'unknown_rpc_command', 'data' => $command));

					$error_count++;
					if (1 === $error_flag) break;
				} else {

					$action = $command_info['command'];
					$command_php_class = $command_info['command_php_class'];

					// Instantiate the command class and execute the needed action
					if (class_exists($command_php_class)) {
						$instance = new $command_php_class($this->rc);

						if (method_exists($instance, $action)) {
							$params = empty($params) ? array() : $params;
							$call_result = call_user_func(array($instance, $action), $params);

							$command_results[$command] = $call_result;
							if ('rpcerror' === $call_result['response'] || (isset($call_result['data']['error']) && $call_result['data']['error'])) {
								$error_count++;
								if (1 === $error_flag) break;
							}
						}
					}
				}
			}

			if (0 !== $error_count) {
				// N.B. These error messages should be defined in UpdraftCentral's translation file (dashboard-translations.php)
				// before actually using this multiplexer function.
				$message = 'general_command_execution_error';

				switch ($error_flag) {
					case 1:
						$message = 'command_execution_aborted';
						break;
					case 2:
						$message = 'failed_to_execute_some_commands';
						break;
					case 3:
						if (count($commands) === $error_count) {
							$message = 'failed_to_execute_all_commands';
						}
						break;
					default:
						break;
				}

				$result = array('error' => true, 'message' => $message, 'values' => $command_results);
			} else {
				$result = $command_results;
			}

		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		return $this->_response($result);
	}

	/**
	 * Validates the credentials entered by the user
	 *
	 * @param  array $creds an array of filesystem credentials
	 * @return array        An array containing the result of the validation process.
	 */
	public function validate_credentials($creds) {
		
		try {
			
			$entity = $creds['entity'];
			if (isset($creds['filesystem_credentials'])) {
				parse_str($creds['filesystem_credentials'], $filesystem_credentials);
				if (is_array($filesystem_credentials)) {
					foreach ($filesystem_credentials as $key => $value) {
						// Put them into $_POST, which is where request_filesystem_credentials() checks for them.
						$_POST[$key] = $value;
					}
				}
			}
			
			// Include the needed WP Core file(s)
			// template.php needed for submit_button() which is called by request_filesystem_credentials()
			$this->_admin_include('file.php', 'template.php');
			
			// Directory entities that we currently need permissions
			// to update.
			$entity_directories = array(
				'plugins' => WP_PLUGIN_DIR,
				'themes' => WP_CONTENT_DIR.'/themes',
				'core' => untrailingslashit(ABSPATH)
			);
			
			if ('translations' === $entity) {
				// 'en_US' don't usually have the "languages" folder, thus, we
				// check if there's a need to ask for filesystem credentials for that
				// folder if it exists, most especially for locale other than 'en_US'.
				$language_dir = WP_CONTENT_DIR.'/languages';
				if ('en_US' !== get_locale() && is_dir($language_dir)) {
					$entity_directories['translations'] = $language_dir;
				}
			}
			
			$url = wp_nonce_url(site_url());

			$passed = false;
			if (isset($entity_directories[$entity])) {
				$directory = $entity_directories[$entity];
	
				// Check if credentials are valid and have sufficient
				// privileges to create and delete (e.g. write)
				ob_start();
				$credentials = request_filesystem_credentials($url, '', false, $directory);
				ob_end_clean();
	
				// The "WP_Filesystem" will suffice in validating the inputted credentials
				// from UpdraftCentral, as it is already attempting to connect to the filesystem
				// using the chosen transport (e.g. ssh, ftp, etc.)
				$passed = WP_Filesystem($credentials, $directory);
			}

			if ($passed) {
				$result = array('error' => false, 'message' => 'credentials_ok', 'values' => array());
			} else {
				// We're adding some useful error information to help troubleshooting any problems
				// that may arise in the future. If the user submitted a wrong password or username
				// it usually falls through here.
				global $wp_filesystem;

				$errors = array();
				if (isset($wp_filesystem->errors) && is_wp_error($wp_filesystem->errors)) {
					$errors = $wp_filesystem->errors->errors;
				}

				$result = array('error' => true, 'message' => 'failed_credentials', 'values' => array('errors' => $errors));
			}
			
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage(), 'values' => array());
		}
		
		return $this->_response($result);
	}

	/**
	 * Gets the FileSystem Credentials
	 *
	 * Extract the needed filesystem credentials (permissions) to be used
	 * to update/upgrade the plugins, themes and the WP core.
	 *
	 * @return array $result - An array containing the creds form and some flags
	 *                         to determine whether we need to extract the creds
	 *						  manually from the user.
	 */
	public function get_credentials() {
		
		try {
			
			// Check whether user has enough permission to update entities
			if (!current_user_can('update_plugins') && !current_user_can('update_themes') && !current_user_can('update_core')) return $this->_generic_error_response('updates_permission_denied');
			
			// Include the needed WP Core file(s)
			$this->_admin_include('file.php', 'template.php');
			
			// A container that will hold the state (in this case, either true or false) of
			// each directory entities (plugins, themes, core) that will be used to determine
			// whether or not there's a need to show a form that will ask the user for their credentials
			// manually.
			$request_filesystem_credentials = array();
			
			// A container for the filesystem credentials form if applicable.
			$filesystem_form = '';
			
			// Directory entities that we currently need permissions
			// to update.
			$check_fs = array(
				'plugins' => WP_PLUGIN_DIR,
				'themes' => WP_CONTENT_DIR.'/themes',
				'core' => untrailingslashit(ABSPATH)
			);
			
			// Here, we're looping through each entities and find output whether
			// we have sufficient permissions to update objects belonging to them.
			foreach ($check_fs as $entity => $dir) {
				
				// We're determining which method to use when updating
				// the files in the filesystem.
				$filesystem_method = get_filesystem_method(array(), $dir);
				
				// Buffering the output to pull the actual credentials form
				// currently being used by this WP instance if no sufficient permissions
				// is found.
				$url = wp_nonce_url(site_url());
				
				ob_start();
				$filesystem_credentials_are_stored = request_filesystem_credentials($url, $filesystem_method);
				$form = strip_tags(ob_get_contents(), '<div><h2><p><input><label><fieldset><legend><span><em>');
				
				if (!empty($form)) {
					$filesystem_form = $form;
				}
				ob_end_clean();

				// Save the state whether or not there's a need to show the
				// credentials form to the user.
				$request_filesystem_credentials[$entity] = ('direct' !== $filesystem_method && !$filesystem_credentials_are_stored);
			}
			
			// Wrapping the credentials info before passing it back
			// to the client issuing the request.
			$result = array(
				'request_filesystem_credentials' => $request_filesystem_credentials,
				'filesystem_form' => $filesystem_form
			);
			
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage(), 'values' => array());
		}
		
		return $this->_response($result);
	}

	/**
	 * Fetches a browser-usable URL which will automatically log the user in to the site
	 *
	 * @param String $redirect_to - the URL to got to after logging in
	 * @param Array  $extra_info  - valid keys are user_id, which should be a numeric user ID to log in as.
	 */
	public function get_login_url($redirect_to, $extra_info) {

		if (is_array($extra_info) && !empty($extra_info['user_id']) && is_numeric($extra_info['user_id'])) {
		
			$user_id = $extra_info['user_id'];
		
			if (false == ($login_key = $this->_get_autologin_key($user_id))) return $this->_generic_error_response('user_key_failure');
		
			// Default value
			$redirect_url = network_admin_url();
			if (is_array($redirect_to) && !empty($redirect_to['module'])) {
				switch ($redirect_to['module']) {
					case 'updraftplus':
						if ('initiate_restore' == $redirect_to['action'] && class_exists('UpdraftPlus_Options')) {
							$redirect_url = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&udaction=initiate_restore&entities='.urlencode($redirect_to['data']['entities']).'&showdata='.urlencode($redirect_to['data']['showdata']).'&backup_timestamp='.(int) $redirect_to['data']['backup_timestamp'];

						} elseif ('download_file' == $redirect_to['action']) {
							$findex = empty($redirect_to['data']['findex']) ? 0 : (int) $redirect_to['data']['findex'];
							// e.g. ?udcentral_action=dl&action=updraftplus_spool_file&backup_timestamp=1455101696&findex=0&what=plugins
							$redirect_url = site_url().'?udcentral_action=spool_file&action=updraftplus_spool_file&findex='.$findex.'&what='.urlencode($redirect_to['data']['what']).'&backup_timestamp='.(int) $redirect_to['data']['backup_timestamp'];
						}
						break;
					case 'direct_url':
						$redirect_url = $redirect_to['url'];
						break;
				}
			}
			
			$login_key = apply_filters('updraftplus_remotecontrol_login_key', array(
				'key' => $login_key,
				'created' => time(),
				'redirect_url' => $redirect_url
			), $redirect_to, $extra_info);
			
			// Over-write any previous value - only one can be valid at a time)
			update_user_meta($user_id, 'updraftcentral_login_key', $login_key);
		
			return $this->_response(array(
				'login_url' => network_site_url('?udcentral_action=login&login_id='.$user_id.'&login_key='.$login_key['key'])
			));

		} else {
			return $this->_generic_error_response('user_unknown');
		}
	}

	/**
	 * Get information derived from phpinfo()
	 *
	 * @return Array
	 */
	public function phpinfo() {
		$phpinfo = $this->_get_phpinfo_array();
		
		if (!empty($phpinfo)) {
			return $this->_response($phpinfo);
		}
		
		return $this->_generic_error_response('phpinfo_fail');
	}
		
	/**
	 * The key obtained is only intended to be short-lived. Hence, there's no intention other than that it is random and only used once - only the most recent one is valid.
	 *
	 * @param  Integer $user_id Specific user ID to get the autologin key
	 * @return Array
	 */
	public function _get_autologin_key($user_id) {
		$secure_auth_key = defined('SECURE_AUTH_KEY') ? SECURE_AUTH_KEY : hash('sha256', DB_PASSWORD).'_'.rand(0, 999999999);
		if (!defined('SECURE_AUTH_KEY')) return false;
		$hash_it = $user_id.'_'.microtime(true).'_'.rand(0, 999999999).'_'.$secure_auth_key;
		$hash = hash('sha256', $hash_it);
		return $hash;
	}
	
	public function site_info() {
		global $wpdb;

		// THis is included so we can get $wp_version
		@include(ABSPATH.WPINC.'/version.php');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$ud_version = is_a($this->ud, 'UpdraftPlus') ? $this->ud->version : 'none';

		return $this->_response(array(
			'versions' => array(
				'ud' => $ud_version,
				'php' => PHP_VERSION,
				'wp' => $wp_version,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
				'mysql' => $wpdb->db_version(),
				'udrpc_php' => $this->rc->udrpc_version,
			),
			'bloginfo' => array(
				'url' => network_site_url(),
				'name' => get_bloginfo('name'),
			)
		));
	}

	/**
	 * This calls the WP_Action within WP
	 *
	 * @param  array $data Array of Data to be used within call_wp_action
	 * @return array
	 */
	public function call_wordpress_action($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return $this->_generic_error_response('no_updraftplus');
		$response = $updraftplus_admin->call_wp_action($data);

		if (empty($data["wpaction"])) {
			return $this->_generic_error_response("error", "no command sent");
		}
		
		return $this->_response(array(
			"response" => $response['response'],
			"status" => $response['status'],
			"log" => $response['log']
		));
	}

	/**
	 * Get disk space used
	 *
	 * @uses UpdraftPlus_Filesystem_Functions::get_disk_space_used()
	 *
	 * @param String $entity - the entity to count (e.g. 'plugins', 'themes')
	 *
	 * @return Array - response
	 */
	public function count($entity) {
		if (!class_exists('UpdraftPlus_Filesystem_Functions')) return $this->_generic_error_response('no_updraftplus');
		$response = UpdraftPlus_Filesystem_Functions::get_disk_space_used($entity);

		return $this->_response($response);
	}
	
	/**
	 * https://secure.php.net/phpinfo
	 *
	 * @return null|array
	 */
	private function _get_phpinfo_array() {
		if (!function_exists('phpinfo')) return null;
		ob_start();
		phpinfo(INFO_GENERAL|INFO_CREDITS|INFO_MODULES);
		$phpinfo = array('phpinfo' => array());

		if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
			if (strlen($match[1])) {
				$phpinfo[$match[1]] = array();
			} elseif (isset($match[3])) {
			$keys1 = array_keys($phpinfo);
			$phpinfo[end($keys1)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			} else {
				$keys1 = array_keys($phpinfo);
				$phpinfo[end($keys1)][] = $match[2];
			
			}
		
			}
			return $phpinfo;
		}
		return false;
	}

	/**
	 * Return an UpdraftPlus_Admin object
	 *
	 * @return UpdraftPlus_Admin|Boolean - false in case of failure
	 */
	private function _load_ud_admin() {
		if (!defined('UPDRAFTPLUS_DIR') || !is_file(UPDRAFTPLUS_DIR.'/admin.php')) return false;
		include_once(UPDRAFTPLUS_DIR.'/admin.php');
		global $updraftplus_admin;
		return $updraftplus_admin;
	}
}
