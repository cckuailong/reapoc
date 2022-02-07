<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * Handles UpdraftCentral Plugin Commands which basically handles
 * the installation and activation of a plugin
 */
class UpdraftCentral_Plugin_Commands extends UpdraftCentral_Commands {

	private $switched = false;

	/**
	 * Function that gets called before every action
	 *
	 * @param string $command    a string that corresponds to UDC command to call a certain method for this class.
	 * @param array  $data       an array of data post or get fields
	 * @param array  $extra_info extrainfo use in the udrpc_action, e.g. user_id
	 *
	 * link to udrpc_action main function in class UpdraftCentral_Listener
	 */
	public function _pre_action($command, $data, $extra_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- This function is called from listner.php and $extra_info is being sent.
		// Here we assign the current blog_id to a variable $blog_id
		$blog_id = get_current_blog_id();
		if (!empty($data['site_id'])) $blog_id = $data['site_id'];
	
		if (function_exists('switch_to_blog') && is_multisite() && $blog_id) {
			$this->switched = switch_to_blog($blog_id);
		}
	}
	
	/**
	 * Function that gets called after every action
	 *
	 * @param string $command    a string that corresponds to UDC command to call a certain method for this class.
	 * @param array  $data       an array of data post or get fields
	 * @param array  $extra_info extrainfo use in the udrpc_action, e.g. user_id
	 *
	 * link to udrpc_action main function in class UpdraftCentral_Listener
	 */
	public function _post_action($command, $data, $extra_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// Here, we're restoring to the current (default) blog before we switched
		if ($this->switched) restore_current_blog();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->_admin_include('plugin.php', 'file.php', 'template.php', 'class-wp-upgrader.php', 'plugin-install.php', 'update.php');
	}

	/**
	 * Installs and activates a plugin through upload
	 *
	 * @param array $params Parameter array containing information pertaining the currently uploaded plugin
	 * @return array Contains the result of the current process
	 */
	public function upload_plugin($params) {
		return $this->process_chunk_upload($params, 'plugin');
	}

	/**
	 * Checks whether the plugin is currently installed and activated.
	 *
	 * @param array $query Parameter array containing the name of the plugin to check
	 * @return array Contains the result of the current process
	 */
	public function is_plugin_installed($query) {

		if (!isset($query['plugin']))
			return $this->_generic_error_response('plugin_name_required');


		$result = $this->_get_plugin_info($query);
		return $this->_response($result);
	}

	/**
	 * Applies currently requested action for plugin processing
	 *
	 * @param string $action The action to apply (e.g. activate or install)
	 * @param array  $query  Parameter array containing information for the currently requested action
	 *
	 * @return array
	 */
	private function _apply_plugin_action($action, $query) {

		$result = array();
		switch ($action) {
			case 'activate':
			case 'network_activate':
				$info = $this->_get_plugin_info($query);
				if ($info['installed']) {
					if (is_multisite() && 'network_activate' === $action) {
						$activate = activate_plugin($info['plugin_path'], '', true);
					} else {
						$activate = activate_plugin($info['plugin_path']);
					}

					if (is_wp_error($activate)) {
						$result = $this->_generic_error_response('generic_response_error', array(
							'plugin' => $query['plugin'],
							'error_code' => 'generic_response_error',
							'error_message' => $activate->get_error_message(),
							'info' => $this->_get_plugin_info($query)
						));
					} else {
						$result = array('activated' => true, 'info' => $this->_get_plugin_info($query));
					}
				} else {
					$result = $this->_generic_error_response('plugin_not_installed', array(
						'plugin' => $query['plugin'],
						'error_code' => 'plugin_not_installed',
						'error_message' => __('The plugin you wish to activate is either not installed or has been removed recently.', 'updraftplus'),
						'info' => $info
					));
				}
				break;
			case 'deactivate':
			case 'network_deactivate':
				$info = $this->_get_plugin_info($query);
				if ($info['active']) {
					if (is_multisite() && 'network_deactivate' === $action) {
						deactivate_plugins($info['plugin_path'], false, true);
					} else {
						deactivate_plugins($info['plugin_path']);
					}

					if (!is_plugin_active($info['plugin_path'])) {
						$result = array('deactivated' => true, 'info' => $this->_get_plugin_info($query));
					} else {
						$result = $this->_generic_error_response('deactivate_plugin_failed', array(
							'plugin' => $query['plugin'],
							'error_code' => 'deactivate_plugin_failed',
							'error_message' => __('There appears to be a problem deactivating the intended plugin. Please kindly check your permission and try again.', 'updraftplus'),
							'info' => $this->_get_plugin_info($query)
						));
					}
				} else {
					$result = $this->_generic_error_response('not_active', array(
						'plugin' => $query['plugin'],
						'error_code' => 'not_active',
						'error_message' => __('The plugin you wish to deactivate is currently not active or is already deactivated.', 'updraftplus'),
						'info' => $info
					));
				}
				break;
			case 'install':
				$api = plugins_api('plugin_information', array(
					'slug' => $query['slug'],
					'fields' => array(
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					)
				));

				$info = $this->_get_plugin_info($query);
				if (is_wp_error($api)) {
					$result = $this->_generic_error_response('generic_response_error', array(
						'plugin' => $query['plugin'],
						'error_code' => 'generic_response_error',
						'error_message' => $api->get_error_message(),
						'info' => $info
					));
				} else {
					$installed = $info['installed'];

					$error_code = $error_message = '';
					if (!$installed) {
						// WP < 3.7
						if (!class_exists('Automatic_Upgrader_Skin')) include_once(dirname(dirname(__FILE__)).'/classes/class-automatic-upgrader-skin.php');

						$skin = new Automatic_Upgrader_Skin();
						$upgrader = new Plugin_Upgrader($skin);

						$download_link = $api->download_link;
						$installed = $upgrader->install($download_link);

						if (is_wp_error($installed)) {
							$error_code = $installed->get_error_code();
							$error_message = $installed->get_error_message();
						} elseif (is_wp_error($skin->result)) {
							$error_code = $skin->result->get_error_code();
							$error_message = $skin->result->get_error_message();

							$error_data = $skin->result->get_error_data($error_code);
							if (!empty($error_data)) {
								if (is_array($error_data)) $error_data = json_encode($error_data);
								$error_message .= ' '.$error_data;
							}
						} elseif (is_null($installed) || !$installed) {
							global $wp_filesystem;
							$upgrade_messages = $skin->get_upgrade_messages();

							if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH.'/wp-admin/includes/class-wp-filesystem-base.php');

							// Pass through the error from WP_Filesystem if one was raised.
							if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
								$error_code = $wp_filesystem->errors->get_error_code();
								$error_message = $wp_filesystem->errors->get_error_message();
							} elseif (!empty($upgrade_messages)) {
								// We're only after for the last feedback that we received from the install process. Mostly,
								// that is where the last error has been inserted.
								$messages = $skin->get_upgrade_messages();
								$error_code = 'install_failed';
								$error_message = end($messages);
							} else {
								$error_code = 'unable_to_connect_to_filesystem';
								$error_message = __('Unable to connect to the filesystem. Please confirm your credentials.');
							}
						}
					}

					if (!$installed || is_wp_error($installed)) {
						$result = $this->_generic_error_response('plugin_install_failed', array(
							'plugin' => $query['plugin'],
							'error_code' => $error_code,
							'error_message' => $error_message,
							'info' => $this->_get_plugin_info($query)
						));
					} else {
						$result = array('installed' => true, 'info' => $this->_get_plugin_info($query));
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * Preloads the submitted credentials to the global $_POST variable
	 *
	 * @param array $query Parameter array containing information for the currently requested action
	 */
	private function _preload_credentials($query) {
		if (!empty($query) && isset($query['filesystem_credentials'])) {
			parse_str($query['filesystem_credentials'], $filesystem_credentials);
			if (is_array($filesystem_credentials)) {
				foreach ($filesystem_credentials as $key => $value) {
					// Put them into $_POST, which is where request_filesystem_credentials() checks for them.
					$_POST[$key] = $value;
				}
			}
		}
	}

	/**
	 * Checks whether we have the required fields submitted and the user has
	 * the capabilities to execute the requested action
	 *
	 * @param array $query        The submitted information
	 * @param array $fields       The required fields to check
	 * @param array $capabilities The capabilities to check and validate
	 *
	 * @return array|string
	 */
	private function _validate_fields_and_capabilities($query, $fields, $capabilities) {

		$error = '';
		if (!empty($fields)) {
			for ($i=0; $i<count($fields); $i++) {
				$field = $fields[$i];

				if (!isset($query[$field])) {
					if ('keyword' === $field) {
						$error = $this->_generic_error_response('keyword_required');
					} else {
						$error = $this->_generic_error_response('plugin_'.$query[$field].'_required');
					}
					break;
				}
			}
		}

		if (empty($error) && !empty($capabilities)) {
			for ($i=0; $i<count($capabilities); $i++) {
				if (!current_user_can($capabilities[$i])) {
					$error = $this->_generic_error_response('plugin_insufficient_permission');
					break;
				}
			}
		}

		return $error;
	}

	/**
	 * Activates the plugin
	 *
	 * @param array $query Parameter array containing the name of the plugin to activate
	 * @return array Contains the result of the current process
	 */
	public function activate_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin'), array('activate_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$action = 'activate';
		if (!empty($query['multisite']) && (bool) $query['multisite']) $action = 'network_'.$action;

		$result = $this->_apply_plugin_action($action, $query);
		if (empty($result['activated'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Deactivates the plugin
	 *
	 * @param array $query Parameter array containing the name of the plugin to deactivate
	 * @return array Contains the result of the current process
	 */
	public function deactivate_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin'), array('activate_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$action = 'deactivate';
		if (!empty($query['multisite']) && (bool) $query['multisite']) $action = 'network_'.$action;

		$result = $this->_apply_plugin_action($action, $query);
		if (empty($result['deactivated'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Download, install and activates the plugin
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the plugin name and slug
	 * @return array Contains the result of the current process
	 */
	public function install_activate_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin', 'slug'), array('install_plugins', 'activate_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);

		$result = $this->_apply_plugin_action('install', $query);
		if (!empty($result['installed']) && $result['installed']) {
			$action = 'activate';
			if (!empty($query['multisite']) && (bool) $query['multisite']) $action = 'network_'.$action;

			$result = $this->_apply_plugin_action($action, $query);
			if (empty($result['activated'])) {
				return $result;
			}
		} else {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Download, install the plugin
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the plugin name and slug
	 * @return array Contains the result of the current process
	 */
	public function install_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin', 'slug'), array('install_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);

		$result = $this->_apply_plugin_action('install', $query);
		if (empty($result['installed'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Uninstall/delete the plugin
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the plugin name and slug
	 * @return array Contains the result of the current process
	 */
	public function delete_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin'), array('delete_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);
		$info = $this->_get_plugin_info($query);

		if ($info['installed']) {
			$deleted = delete_plugins(array($info['plugin_path']));

			if ($deleted) {
				$result = array('deleted' => true, 'info' => $this->_get_plugin_info($query));
			} else {
				$result = $this->_generic_error_response('delete_plugin_failed', array(
					'plugin' => $query['plugin'],
					'error_code' => 'delete_plugin_failed',
					'info' => $info
				));
			}
		} else {
			$result = $this->_generic_error_response('plugin_not_installed', array(
				'plugin' => $query['plugin'],
				'error_code' => 'plugin_not_installed',
				'info' => $info
			));
		}

		return $this->_response($result);
	}

	/**
	 * Updates/upgrade the plugin
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the plugin name and slug
	 * @return array Contains the result of the current process
	 */
	public function update_plugin($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('plugin', 'slug'), array('update_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);
		$info = $this->_get_plugin_info($query);

		// Make sure that we still have the plugin installed before running
		// the update process
		if ($info['installed']) {
			// Load the updates command class if not existed
			if (!class_exists('UpdraftCentral_Updates_Commands')) include_once('updates.php');
			$update_command = new UpdraftCentral_Updates_Commands($this->rc);

			$result = $update_command->update_plugin($info['plugin_path'], $query['slug']);
			if (!empty($result['error'])) {
				$result['values'] = array('plugin' => $query['plugin'], 'info' => $info);
			}
		} else {
			$result = $this->_generic_error_response('plugin_not_installed', array(
				'plugin' => $query['plugin'],
				'error_code' => 'plugin_not_installed',
				'info' => $info
			));
		}

		return $this->_response($result);
	}

	/**
	 * Gets the plugin information along with its active and install status
	 *
	 * @internal
	 * @param array $query Contains either the plugin name or slug or both to be used when retrieving information
	 * @return array
	 */
	private function _get_plugin_info($query) {

		$info = array(
			'active' => false,
			'installed' => false
		);
		
		// Clear plugin cache so that newly installed/downloaded plugins
		// gets reflected when calling "get_plugins"
		if (function_exists('wp_clean_plugins_cache')) {
			wp_clean_plugins_cache();
		}
		
		// Gets all plugins available.
		$get_plugins = get_plugins();

		// Loops around each plugin available.
		foreach ($get_plugins as $key => $value) {
			$slug = basename($key, '.php');

			// If the plugin name matches that of the specified name, it will gather details.
			// In case name check isn't enough, we'll use slug to verify if the plugin being queried is actually installed.
			//
			// Reason for name check failure:
			// Due to plugin name inconsistencies - where wordpress.org registered plugin name is different
			// from the actual plugin files's metadata (found inside the plugin's PHP file itself).
			if ((!empty($query['plugin']) && $value['Name'] === $query['plugin']) || (!empty($query['slug']) && $slug === $query['slug'])) {
				$info['installed'] = true;
				$info['active'] = is_plugin_active($key);
				$info['plugin_path'] = $key;
				$info['data'] = $value;
				break;
			}
		}

		return $info;
	}

	/**
	 * Loads all available plugins with additional attributes and settings needed by UpdraftCentral
	 *
	 * @param array $query Parameter array Any available parameters needed for this action
	 * @return array Contains the result of the current process
	 */
	public function load_plugins($query) {

		$error = $this->_validate_fields_and_capabilities($query, array(), array('install_plugins', 'activate_plugins'));
		if (!empty($error)) {
			return $error;
		}

		$website = get_bloginfo('name');
		$results = array();

		// Load the updates command class if not existed
		if (!class_exists('UpdraftCentral_Updates_Commands')) include_once('updates.php');
		$updates = new UpdraftCentral_Updates_Commands($this->rc);

		// Get plugins for update
		$plugin_updates = $updates->get_item_updates('plugins');

		// Get all plugins
		$plugins = get_plugins();

		foreach ($plugins as $key => $value) {
			$slug = basename($key, '.php');

			$plugin = new stdClass();
			$plugin->name = $value['Name'];
			$plugin->description = $value['Description'];
			$plugin->slug = $slug;
			$plugin->version = $value['Version'];
			$plugin->author = $value['Author'];
			$plugin->status = is_plugin_active($key) ? 'active' : 'inactive';
			$plugin->website = $website;
			$plugin->multisite = is_multisite();
			$plugin->site_url = trailingslashit(get_bloginfo('url'));

			if (!empty($plugin_updates[$key])) {
				$update_info = $plugin_updates[$key];

				if (version_compare($update_info->Version, $update_info->update->new_version, '<')) {
					if (!empty($update_info->update->new_version)) $plugin->latest_version = $update_info->update->new_version;
					if (!empty($update_info->update->package)) $plugin->download_link = $update_info->update->package;
					if (!empty($update_info->update->sections)) $plugin->sections = $update_info->update->sections;
				}
			}

			if (empty($plugin->short_description) && !empty($plugin->description)) {
				// Only pull the first sentence as short description, it should be enough rather than displaying
				// an empty description or a full blown one which the user can access anytime if they press on
				// the view details link in UpdraftCentral.
				$temp = explode('.', $plugin->description);
				$short_description = $temp[0];

				// Adding the second sentence wouldn't hurt, in case the first sentence is too short.
				if (isset($temp[1])) $short_description .= '.'.$temp[1];

				$plugin->short_description = $short_description.'.';
			}

			$results[] = $plugin;
		}

		$result = array(
			'plugins' => $results
		);

		$result = array_merge($result, $this->_get_backup_credentials_settings(WP_PLUGIN_DIR));
		return $this->_response($result);
	}

	/**
	 * Gets the backup and security credentials settings for this website
	 *
	 * @param array $query Parameter array Any available parameters needed for this action
	 * @return array Contains the result of the current process
	 */
	public function get_plugin_requirements() {
		return $this->_response($this->_get_backup_credentials_settings(WP_PLUGIN_DIR));
	}
}
