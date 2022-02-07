<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * - A container for all the RPC commands implemented. Commands map exactly onto method names (and hence this class should not implement anything else, beyond the constructor, and private methods)
 * - Return format is array('response' => (string - a code), 'data' => (mixed));
 *
 * RPC commands are not allowed to begin with an underscore. So, any private methods can be prefixed with an underscore.
 */
abstract class UpdraftCentral_Commands {

	protected $rc;

	protected $ud;

	protected $installed_data;

	/**
	 * Class constructor
	 *
	 * @param string $rc
	 */
	public function __construct($rc) {
		$this->rc = $rc;
		global $updraftplus;
		$this->ud = $updraftplus;
		$this->installed_data = array();
	}

	/**
	 * Include a file or files from wp-admin/includes
	 */
	final protected function _admin_include() {
		$files = func_get_args();
		foreach ($files as $file) {
			include_once(ABSPATH.'/wp-admin/includes/'.$file);
		}
	}
	
	/**
	 * Include a file or files from wp-includes
	 */
	final protected function _frontend_include() {
		$files = func_get_args();
		foreach ($files as $file) {
			include_once(ABSPATH.WPINC.'/'.$file);
		}
	}
	
	/**
	 * Return a response in the expected format
	 *
	 * @param Mixed  $data
	 * @param String $code
	 *
	 * @return Array
	 */
	final protected function _response($data = null, $code = 'rpcok') {
		return array(
			'response' => $code,
			'data' => $data
		);
	}
	
	/**
	 * Return an error in the expected format
	 *
	 * @param String $code
	 * @param Mixed  $data
	 *
	 * @return Array
	 */
	final protected function _generic_error_response($code = 'central_unspecified', $data = null) {
		return $this->_response(
			array(
				'code' => $code,
				'data' => $data
			),
			'rpcerror'
		);
	}

	/**
	 * Checks whether a backup and a security credentials is required for the given request
	 *
	 * @param array $dir The directory location to check
	 * @return array
	 */
	final protected function _get_backup_credentials_settings($dir) {

		// Do we need to ask the user for filesystem credentials? when installing and/or deleting items in the given directory
		$filesystem_method = get_filesystem_method(array(), $dir);

		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials(site_url(), $filesystem_method);
		ob_end_clean();
		$request_filesystem_credentials = ('direct' != $filesystem_method && !$filesystem_credentials_are_stored);

		// Do we need to execute a backup process before installing/managing items
		$automatic_backups = (class_exists('UpdraftPlus_Options') && class_exists('UpdraftPlus_Addon_Autobackup') && UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default', true)) ? true : false;
		
		return array(
			'request_filesystem_credentials' => $request_filesystem_credentials,
			'automatic_backups' => $automatic_backups
		);
	}

	/**
	 * Retrieves the information of the currently installed item (e.g. plugin or theme) through filter
	 *
	 * @param bool  $response Indicates whether the installation was a success or failure
	 * @param array $args     Extra argument for the hook
	 * @param array $data     Contains paths used and other relevant information regarding the file
	 * @return array
	 */
	final public function get_install_data($response, $args, $data) {

		if ($response) {
			switch ($args['type']) {
				case 'plugin':
					$plugin_data = get_plugins('/'.$data['destination_name']);
					if (!empty($plugin_data)) {
						$info = reset($plugin_data);
						$key = key($plugin_data);
	
						$info['slug'] = $data['destination_name'].'/'.$key;
						$this->installed_data = $info;
					}
					break;
				case 'theme':
					$theme = wp_get_theme($data['destination_name']);
					if ($theme->exists()) {
						// Minimalistic info here, if you need to add additional information
						// you can add them here. For now, the "Name" and "slug" fields will suffice
						// in the succeeding process.
						$this->installed_data = array(
							'Name' => $theme->get('Name'),
							'slug' => $data['destination_name'],
							'template' => $theme->get_template()
						);
					}
					break;
				default:
					break;
			}
		}

		return $response;
	}

	/**
	 * Installs and activates either a plugin or theme through zip file upload
	 *
	 * @param array  $params Parameter array containing information pertaining the currently uploaded item
	 * @param string $type   Indicates whether this current process is intended for a 'plugin' or a 'theme' item
	 * @return array
	 */
	final protected function process_chunk_upload($params, $type) {
		global $updraftcentral_host_plugin, $updraftcentral_main;

		if (!in_array($type, array('plugin', 'theme'))) {
			return $this->_generic_error_response('upload_type_not_supported');
		}

		$permission_error = false;
		if ('plugin' === $type) {
			if (!current_user_can('install_plugins') || !current_user_can('activate_plugins')) $permission_error = true;
		} else {
			if (!current_user_can('install_themes') || !current_user_can('switch_themes')) $permission_error = true;
		}

		if ($permission_error) {
			return $this->_generic_error_response($type.'_insufficient_permission');
		}

		// Pull any available and writable directory where we can store
		// our data/file temporarily before running the installation process.
		$upload_dir = untrailingslashit(get_temp_dir());
		if (!is_writable($upload_dir)) {
			$upload_dir = WP_CONTENT_DIR.'/upgrade';

			if (!is_dir($upload_dir)) {
				$wp_dir = wp_upload_dir();
				if (!empty($wp_dir['basedir'])) $upload_dir = $wp_dir['basedir'];
			}
		}

		// If we haven't found any writable directory to temporarily store our file then
		// we bail and send an error back to the caller.
		if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
			return $this->_generic_error_response('upload_dir_not_available');
		}

		// Preloads the submitted credentials to the global $_POST variable
		if (!empty($params) && isset($params['filesystem_credentials'])) {
			parse_str($params['filesystem_credentials'], $filesystem_credentials);
			if (is_array($filesystem_credentials)) {
				foreach ($filesystem_credentials as $key => $value) {
					// Put them into $_POST, which is where request_filesystem_credentials() checks for them.
					$_POST[$key] = $value;
				}
			}
		}

		// Save uploaded file
		$filename = basename($params['filename']);
		$is_chunked = false;

		if (isset($params['chunks']) && 1 < (int) $params['chunks']) {
			$filename = basename($params['filename']).'.part';
			$is_chunked = true;
		}

		if (empty($params['data'])) {
			return $this->_generic_error_response('data_empty_or_invalid');
		}
		
		$result = file_put_contents($upload_dir.'/'.$filename, base64_decode($params['data']), FILE_APPEND | LOCK_EX);
		
		if (false === $result) {
			return $this->_generic_error_response('unable_to_write_content');
		}
		
		// Set $install_now to true for single upload and for the last chunk of a multi-chunks upload process
		$install_now = true;

		if ($is_chunked) {
			if ($params['chunk'] == (int) $params['chunks'] - 1) {
				// If this is the last chunk of the request, then we're going to restore the
				// original filename of the file (without the '.part') since our upload is now complete.
				$orig_filename = basename($filename, '.part');
				$success = rename($upload_dir.'/'.$filename, $upload_dir.'/'.$orig_filename);

				// If renaming the file was successful then restore the original name and override the $filename variable.
				// Overriding the $filename variable makes it easy for us to use the same variable for both
				// non-chunked and chunked zip file for the installation process.
				if ($success) {
					$filename = $orig_filename;
				} else {
					return $this->_generic_error_response('unable_to_rename_file');
				}
			} else {
				// Bypass installation for now since we're waiting for the last chunk to arrive
				// to complete the uploading of the zip file.
				$install_now = false;
			}
		}

		// Everything is already good (upload completed), thus, we proceed with the installation
		if ($install_now) {

			// We have successfully uploaded the zip file in this location with its original filename intact.
			$zip_filepath = $upload_dir.'/'.$filename;

			// Making sure that the file does actually exists, since we've just run through
			// a renaming process above.
			if (file_exists($zip_filepath)) {
				add_filter('upgrader_post_install', array($this, 'get_install_data'), 10, 3);

				// WP < 3.7
				if (!class_exists('Automatic_Upgrader_Skin')) include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/classes/class-automatic-upgrader-skin.php');

				$skin = new Automatic_Upgrader_Skin();
				$upgrader = ('plugin' === $type) ? new Plugin_Upgrader($skin) : new Theme_Upgrader($skin);

				$install_result = $upgrader->install($zip_filepath);
				remove_filter('upgrader_post_install', array($this, 'get_install_data'), 10, 3);

				// Remove zip file on success and on error (cleanup)
				if ($install_result || is_null($install_result) || is_wp_error($install_result)) {
					@unlink($zip_filepath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				}

				if (false === $install_result || is_wp_error($install_result)) {
					$message = $updraftcentral_host_plugin->retrieve_show_message('unable_to_connect');
					if (is_wp_error($install_result)) $message = $install_result->get_error_message();

					return $this->_generic_error_response($type.'_install_failed', array('message' => $message));
				} else {
					// Pull installed data
					$data = $this->installed_data;

					// For WP 3.4 the intended filter hook isn't working or not available
					// so we're going to pull the data manually.
					if ($install_result && empty($data)) {
						$result = $this->get_install_data($install_result, array('type' => $type), $skin->result);
						if ($result) {
							// Getting the installed data one more time after manually calling
							// the "get_install_data" function.
							$data = $this->installed_data;
						}
					}

					if (!empty($data)) {
						// Activate item if set
						$is_active = ('plugin' === $type) ? is_plugin_active($data['slug']) : ((wp_get_theme()->get('Name') === $data['Name']) ? true : false);

						if ((bool) $params['activate'] && !$is_active) {
							if ('plugin' === $type) {
								if (is_multisite()) {
									$activate = activate_plugin($data['slug'], '', true);
								} else {
									$activate = activate_plugin($data['slug']);
								}
							} else {
								// In order to make it compatible with older versions of switch_theme which takes two
								// arguments we're going to pass two arguments instead of one. Latest versions have backward
								// compatibility so it's safe to do it this way.
								switch_theme($data['template'], $data['slug']);
								$activate = (wp_get_theme()->get_stylesheet() === $data['slug']) ? true : false;
							}

							if (false === $activate || is_wp_error($activate)) {
								$wp_version = $updraftcentral_main->get_wordpress_version();

								$message = is_wp_error($activate) ? array('message' => $activate->get_error_message()) : array('message' => sprintf($updraftcentral_host_plugin->retrieve_show_message('unable_to_activate'), $type, $type, $wp_version));
								return $this->_generic_error_response('unable_to_activate_'.$type, $message);
							}
						}

						return $this->_response(
							array(
								'installed' => true,
								'installed_data' => $data,
							)
						);
					}

					if (is_wp_error($skin->result)) {
						$code = $skin->result->get_error_code();
						$message = $skin->result->get_error_message();

						$error_data = $skin->result->get_error_data($code);
						if (!empty($error_data)) {
							if (is_array($error_data)) $error_data = json_encode($error_data);

							$message .= ' '.$error_data;
						}

						return $this->_generic_error_response($code, $message);
					} else {
						return $this->_response(
							array(
								'installed' => false,
								'message' => sprintf($updraftcentral_host_plugin->retrieve_show_message('unable_to_install'), $type, $type, $type, $type, 'wp-content/'.$type.'s'),
							)
						);
					}
				}
			}
		} else {
			// Returning response to a chunk requests while still processing and
			// completing the file upload process. If we don't return a positive response
			// for every chunk requests then the caller will assumed an error has occurred
			// and will eventually stop the upload process.
			return $this->_response(array('in_progress' => true));
		}
	}
}
