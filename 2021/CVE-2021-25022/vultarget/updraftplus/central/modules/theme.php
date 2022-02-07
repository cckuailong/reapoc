<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * Handles UpdraftCentral Theme Commands which basically handles
 * the installation and activation of a theme
 */
class UpdraftCentral_Theme_Commands extends UpdraftCentral_Commands {

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
	public function _pre_action($command, $data, $extra_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- This function is called from listener.php and $extra_info is being sent.
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
		$this->_admin_include('theme.php', 'file.php', 'template.php', 'class-wp-upgrader.php', 'theme-install.php', 'update.php');
	}

	/**
	 * Installs and activates a theme through upload
	 *
	 * @param array $params Parameter array containing information pertaining the currently uploaded theme
	 * @return array Contains the result of the current process
	 */
	public function upload_theme($params) {
		return $this->process_chunk_upload($params, 'theme');
	}

	/**
	 * Checks whether the theme is currently installed and activated.
	 *
	 * @param array $query Parameter array containing the name of the theme to check
	 * @return array Contains the result of the current process
	 */
	public function is_theme_installed($query) {

		if (!isset($query['theme']))
			return $this->_generic_error_response('theme_name_required');


		$result = $this->_get_theme_info($query['theme']);
		return $this->_response($result);
	}

	/**
	 * Applies currently requested action for theme processing
	 *
	 * @param string $action The action to apply (e.g. activate or install)
	 * @param array  $query  Parameter array containing information for the currently requested action
	 *
	 * @return array
	 */
	private function _apply_theme_action($action, $query) {

		$result = array();
		switch ($action) {
			case 'activate':
				$info = $this->_get_theme_info($query['theme']);
				if ($info['installed']) {
					switch_theme($info['slug']);
					if (wp_get_theme()->get_stylesheet() === $info['slug']) {
						$result = array('activated' => true, 'info' => $this->_get_theme_info($query['theme']));
					} else {
						$result = $this->_generic_error_response('theme_not_activated', array(
							'theme' => $query['theme'],
							'error_code' => 'theme_not_activated',
							'error_message' => __('There appears to be a problem activating or switching to the intended theme. Please kindly check your permission and try again.', 'updraftplus'),
							'info' => $this->_get_theme_info($query['theme'])
						));
					}
				} else {
					$result = $this->_generic_error_response('theme_not_installed', array(
						'theme' => $query['theme'],
						'error_code' => 'theme_not_installed',
						'error_message' => __('The theme you wish to activate is either not installed or has been removed recently.', 'updraftplus'),
						'info' => $info
					));
				}
				break;
			case 'network_enable':
				$info = $this->_get_theme_info($query['theme']);
				if ($info['installed']) {
					// Make sure that network_enable_theme is present and callable since
					// it is only available at 4.6. If not, we'll do things the old fashion way
					if (is_callable(array('WP_Theme', 'network_enable_theme'))) {
						WP_Theme::network_enable_theme($info['slug']);
					} else {
						$allowed_themes = get_site_option('allowedthemes');
						$allowed_themes[$info['slug']] = true;

						update_site_option('allowedthemes', $allowed_themes);
					}

					$allowed = WP_Theme::get_allowed_on_network();
					if (is_array($allowed) && !empty($allowed[$info['slug']])) {
						$result = array('enabled' => true, 'info' => $this->_get_theme_info($query['theme']));
					} else {
						$result = $this->_generic_error_response('theme_not_enabled', array(
							'theme' => $query['theme'],
							'error_code' => 'theme_not_enabled',
							'error_message' => __('There appears to be a problem enabling the intended theme on your network. Please kindly check your permission and try again.', 'updraftplus'),
							'info' => $this->_get_theme_info($query['theme'])
						));
					}
				} else {
					$result = $this->_generic_error_response('theme_not_installed', array(
						'theme' => $query['theme'],
						'error_code' => 'theme_not_installed',
						'error_message' => __('The theme you wish to enable on your network is either not installed or has been removed recently.', 'updraftplus'),
						'info' => $info
					));
				}
				break;
			case 'network_disable':
				$info = $this->_get_theme_info($query['theme']);
				if ($info['installed']) {
					// Make sure that network_disable_theme is present and callable since
					// it is only available at 4.6. If not, we'll do things the old fashion way
					if (is_callable(array('WP_Theme', 'network_disable_theme'))) {
						WP_Theme::network_disable_theme($info['slug']);
					} else {
						$allowed_themes = get_site_option('allowedthemes');
						if (isset($allowed_themes[$info['slug']])) {
							unset($allowed_themes[$info['slug']]);
						}

						update_site_option('allowedthemes', $allowed_themes);
					}

					$allowed = WP_Theme::get_allowed_on_network();
					if (is_array($allowed) && empty($allowed[$info['slug']])) {
						$result = array('disabled' => true, 'info' => $this->_get_theme_info($query['theme']));
					} else {
						$result = $this->_generic_error_response('theme_not_disabled', array(
							'theme' => $query['theme'],
							'error_code' => 'theme_not_disabled',
							'error_message' => __('There appears to be a problem disabling the intended theme from your network. Please kindly check your permission and try again.', 'updraftplus'),
							'info' => $this->_get_theme_info($query['theme'])
						));
					}
				} else {
					$result = $this->_generic_error_response('theme_not_installed', array(
						'theme' => $query['theme'],
						'error_code' => 'theme_not_installed',
						'error_message' => __('The theme you wish to disable from your network is either not installed or has been removed recently.', 'updraftplus'),
						'info' => $info
					));
				}
				break;
			case 'install':
				$api = themes_api('theme_information', array(
					'slug' => $query['slug'],
					'fields' => array(
						'description' => true,
						'sections' => false,
						'rating' => true,
						'ratings' => true,
						'downloaded' => true,
						'downloadlink' => true,
						'last_updated' => true,
						'screenshot_url' => true,
						'parent' => true,
					)
				));

				$info = $this->_get_theme_info($query['theme']);
				if (is_wp_error($api)) {
					$result = $this->_generic_error_response('generic_response_error', array(
						'theme' => $query['theme'],
						'error_code' => 'theme_not_installed',
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
						$upgrader = new Theme_Upgrader($skin);

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
						$result = $this->_generic_error_response('theme_install_failed', array(
							'theme' => $query['theme'],
							'error_code' => $error_code,
							'error_message' => $error_message,
							'info' => $this->_get_theme_info($query['theme'])
						));
					} else {
						$result = array('installed' => true, 'info' => $this->_get_theme_info($query['theme']));
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
						$error = $this->_generic_error_response('theme_'.$query[$field].'_required');
					}
					break;
				}
			}
		}

		if (empty($error) && !empty($capabilities)) {
			for ($i=0; $i<count($capabilities); $i++) {
				if (!current_user_can($capabilities[$i])) {
					$error = $this->_generic_error_response('theme_insufficient_permission');
					break;
				}
			}
		}

		return $error;
	}

	/**
	 * Activates the theme
	 *
	 * @param array $query Parameter array containing the name of the theme to activate
	 * @return array Contains the result of the current process
	 */
	public function activate_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme'), array('switch_themes'));
		if (!empty($error)) {
			return $error;
		}

		$result = $this->_apply_theme_action('activate', $query);
		if (empty($result['activated'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Enables theme for network
	 *
	 * @param array $query Parameter array containing the name of the theme to activate
	 * @return array Contains the result of the current process
	 */
	public function network_enable_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme'), array('switch_themes'));
		if (!empty($error)) {
			return $error;
		}

		$result = $this->_apply_theme_action('network_enable', $query);
		if (empty($result['enabled'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Disables theme from network
	 *
	 * @param array $query Parameter array containing the name of the theme to activate
	 * @return array Contains the result of the current process
	 */
	public function network_disable_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme'), array('switch_themes'));
		if (!empty($error)) {
			return $error;
		}

		$result = $this->_apply_theme_action('network_disable', $query);
		if (empty($result['disabled'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Download, install and activates the theme
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the theme name and slug
	 * @return array Contains the result of the current process
	 */
	public function install_activate_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme', 'slug'), array('install_themes', 'switch_themes'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);

		$result = $this->_apply_theme_action('install', $query);
		if (!empty($result['installed']) && $result['installed']) {
			$result = $this->_apply_theme_action('activate', $query);
			if (empty($result['activated'])) {
				return $result;
			}
		} else {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Download, install the theme
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the theme name and slug
	 * @return array Contains the result of the current process
	 */
	public function install_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme', 'slug'), array('install_themes'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);

		$result = $this->_apply_theme_action('install', $query);
		if (empty($result['installed'])) {
			return $result;
		}

		return $this->_response($result);
	}

	/**
	 * Uninstall/delete the theme
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the theme name and slug
	 * @return array Contains the result of the current process
	 */
	public function delete_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme'), array('delete_themes'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);
		$info = $this->_get_theme_info($query['theme']);

		if ($info['installed']) {
			$deleted = delete_theme($info['slug']);

			if ($deleted) {
				$result = array('deleted' => true, 'info' => $this->_get_theme_info($query['theme']));
			} else {
				$result = $this->_generic_error_response('delete_theme_failed', array(
					'theme' => $query['theme'],
					'error_code' => 'delete_theme_failed',
					'info' => $info
				));
			}
		} else {
			$result = $this->_generic_error_response('theme_not_installed', array(
				'theme' => $query['theme'],
				'error_code' => 'theme_not_installed',
				'info' => $info
			));
		}

		return $this->_response($result);
	}

	/**
	 * Updates/upgrade the theme
	 *
	 * @param array $query Parameter array containing the filesystem credentials entered by the user along with the theme name and slug
	 * @return array Contains the result of the current process
	 */
	public function update_theme($query) {

		$error = $this->_validate_fields_and_capabilities($query, array('theme'), array('update_themes'));
		if (!empty($error)) {
			return $error;
		}

		$this->_preload_credentials($query);
		$info = $this->_get_theme_info($query['theme']);

		// Make sure that we still have the theme installed before running
		// the update process
		if ($info['installed']) {
			// Load the updates command class if not existed
			if (!class_exists('UpdraftCentral_Updates_Commands')) include_once('updates.php');
			$update_command = new UpdraftCentral_Updates_Commands($this->rc);

			$result = $update_command->update_theme($info['slug']);
			if (!empty($result['error'])) {
				$result['values'] = array('theme' => $query['theme'], 'info' => $info);
			}
		} else {
			return $this->_generic_error_response('theme_not_installed', array(
				'theme' => $query['theme'],
				'error_code' => 'theme_not_installed',
				'info' => $info
			));
		}

		return $this->_response($result);
	}

	/**
	 * Gets the theme information along with its active and install status
	 *
	 * @internal
	 * @param array $theme The name of the theme to pull the information from
	 * @return array Contains the theme information
	 */
	private function _get_theme_info($theme) {

		$info = array(
			'active' => false,
			'installed' => false
		);

		// Clear theme cache so that newly installed/downloaded themes
		// gets reflected when calling "get_themes"
		if (function_exists('wp_clean_themes_cache')) {
			wp_clean_themes_cache();
		}
		
		// Gets all themes available.
		$themes = wp_get_themes();
		$current_theme_slug = basename(get_stylesheet_directory());

		// Loops around each theme available.
		foreach ($themes as $slug => $value) {
			$name = $value->get('Name');
			$theme_name = !empty($name) ? $name : $slug;

			// If the theme name matches that of the specified name, it will gather details.
			if ($theme_name === $theme) {
				$info['installed'] = true;
				$info['active'] = ($slug === $current_theme_slug) ? true : false;
				$info['slug'] = $slug;
				$info['data'] = $value;
				break;
			}
		}

		return $info;
	}

	/**
	 * Loads all available themes with additional attributes and settings needed by UpdraftCentral
	 *
	 * @param array $query Parameter array Any available parameters needed for this action
	 * @return array Contains the result of the current process
	 */
	public function load_themes($query) {

		$error = $this->_validate_fields_and_capabilities($query, array(), array('install_themes', 'switch_themes'));
		if (!empty($error)) {
			return $error;
		}

		$website = get_bloginfo('name');
		$results = array();

		// Load the updates command class if not existed
		if (!class_exists('UpdraftCentral_Updates_Commands')) include_once('updates.php');
		$updates = new UpdraftCentral_Updates_Commands($this->rc);

		// Get themes for update
		$theme_updates = (array) $updates->get_item_updates('themes');

		// Get all themes
		$themes = wp_get_themes();
		$current_theme_slug = basename(get_stylesheet_directory());

		foreach ($themes as $slug => $value) {
			$name = $value->get('Name');
			$theme_name = !empty($name) ? $name : $slug;

			$theme = new stdClass();
			$theme->name = $theme_name;
			$theme->description = $value->get('Description');
			$theme->slug = $slug;
			$theme->version = $value->get('Version');
			$theme->author = $value->get('Author');
			$theme->status = ($slug === $current_theme_slug) ? 'active' : 'inactive';

			$template = $value->get('Template');
			$theme->child_theme = !empty($template) ? true : false;
			$theme->website = $website;
			$theme->multisite = is_multisite();
			$theme->site_url = trailingslashit(get_bloginfo('url'));

			if ($theme->child_theme) {
				$parent_theme = wp_get_theme($template);
				$parent_name = $parent_theme->get('Name');

				$theme->parent = !empty($parent_name) ? $parent_name : $parent_theme->get_stylesheet();
			}

			if (!empty($theme_updates[$slug])) {
				$update_info = $theme_updates[$slug];

				if (version_compare($theme->version, $update_info->update['new_version'], '<')) {
					if (!empty($update_info->update['new_version'])) $theme->latest_version = $update_info->update['new_version'];
					if (!empty($update_info->update['package'])) $theme->download_link = $update_info->update['package'];
				}
			}

			if (empty($theme->short_description) && !empty($theme->description)) {
				// Only pull the first sentence as short description, it should be enough rather than displaying
				// an empty description or a full blown one which the user can access anytime if they press on
				// the view details link in UpdraftCentral.
				$temp = explode('.', $theme->description);
				$short_description = $temp[0];

				// Adding the second sentence wouldn't hurt, in case the first sentence is too short.
				if (isset($temp[1])) $short_description .= '.'.$temp[1];

				$theme->short_description = $short_description.'.';
			}

			$results[] = $theme;
		}

		$result = array(
			'themes' => $results,
			'theme_updates' => $theme_updates,
		);

		$result = array_merge($result, $this->_get_backup_credentials_settings(get_theme_root()));
		return $this->_response($result);
	}

	/**
	 * Gets the backup and security credentials settings for this website
	 *
	 * @param array $query Parameter array Any available parameters needed for this action
	 * @return array Contains the result of the current process
	 */
	public function get_theme_requirements() {
		return $this->_response($this->_get_backup_credentials_settings(get_theme_root()));
	}
}
