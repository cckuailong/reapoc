<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

class UpdraftCentral_Updates_Commands extends UpdraftCentral_Commands {

	public function do_updates($updates) {
	
		if (!is_array($updates)) $this->_generic_error_response('invalid_data');
		
		if (!empty($updates['plugins']) && !current_user_can('update_plugins')) return $this->_generic_error_response('updates_permission_denied', 'update_plugins');

		if (!empty($updates['themes']) && !current_user_can('update_themes')) return $this->_generic_error_response('updates_permission_denied', 'update_themes');

		if (!empty($updates['core']) && !current_user_can('update_core')) return $this->_generic_error_response('updates_permission_denied', 'update_core');
		
		if (!empty($updates['translations']) && !$this->user_can_update_translations()) return $this->_generic_error_response('updates_permission_denied', 'update_translations');
		
		$this->_admin_include('plugin.php', 'update.php', 'file.php', 'template.php');
		$this->_frontend_include('update.php');

		if (!empty($updates['meta']) && isset($updates['meta']['filesystem_credentials'])) {
			parse_str($updates['meta']['filesystem_credentials'], $filesystem_credentials);
			if (is_array($filesystem_credentials)) {
				foreach ($filesystem_credentials as $key => $value) {
					// Put them into $_POST, which is where request_filesystem_credentials() checks for them.
					$_POST[$key] = $value;
				}
			}
		}
		
		$plugins = empty($updates['plugins']) ? array() : $updates['plugins'];
		$plugin_updates = array();
		foreach ($plugins as $plugin_info) {
			$plugin_updates[] = $this->_update_plugin($plugin_info['plugin'], $plugin_info['slug']);
		}

		$themes = empty($updates['themes']) ? array() : $updates['themes'];
		$theme_updates = array();
		foreach ($themes as $theme_info) {
			$theme = $theme_info['theme'];
			$theme_updates[] = $this->_update_theme($theme);
		}

		$cores = empty($updates['core']) ? array() : $updates['core'];
		$core_updates = array();
		foreach ($cores as $core) {	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- We dont use $core but we need the AS in the foreach so it needs to stay
			$core_updates[] = $this->_update_core(null);
			// Only one (and always we go to the latest version) - i.e. we ignore the passed parameters
			break;
		}
		
		$translation_updates = array();
		if (!empty($updates['translations'])) {
			$translation_updates[] = $this->_update_translation();
		}
		
		return $this->_response(array(
			'plugins' => $plugin_updates,
			'themes' => $theme_updates,
			'core' => $core_updates,
			'translations' => $translation_updates,
		));

	}

	/**
	 * Updates a plugin. A facade method that exposes a private updates
	 * feature for other modules to consume.
	 *
	 * @param string $plugin Specific plugin to be updated
	 * @param string $slug   Unique key passed for updates
	 *
	 * @return array
	 */
	public function update_plugin($plugin, $slug) {
		return $this->_update_plugin($plugin, $slug);
	}

	/**
	 * Updates a theme. A facade method that exposes a private updates
	 * feature for other modules to consume.
	 *
	 * @param string $theme Specific theme to be updated
	 *
	 * @return array
	 */
	public function update_theme($theme) {
		return $this->_update_theme($theme);
	}

	/**
	 * Gets available updates for a certain entity (e.g. plugin or theme). A facade method that
	 * exposes a private updates feature for other modules to consume.
	 *
	 * @param string $entity The name of the entity that this request is intended for (e.g. themes or plugins)
	 *
	 * @return array
	 */
	public function get_item_updates($entity) {
		$updates = array();
		switch ($entity) {
			case 'themes':
				wp_update_themes();
				$updates = $this->maybe_add_third_party_items(get_theme_updates(), 'theme');
				break;
			case 'plugins':
				wp_update_plugins();
				$updates = $this->maybe_add_third_party_items(get_plugin_updates(), 'plugin');
				break;
		}

		return $updates;
	}

	/**
	 * Mostly from wp_ajax_update_plugin() in wp-admin/includes/ajax-actions.php (WP 4.5.2)
	 * Code-formatting style has been retained from the original, for ease of comparison/updating
	 *
	 * @param  string $plugin Specific plugin to be updated
	 * @param  string $slug   Unique key passed for updates
	 * @return array
	 */
	private function _update_plugin($plugin, $slug) {

		$status = array(
			'update'     => 'plugin',
			'plugin'     => $plugin,
			'slug'       => sanitize_key($slug),
			'oldVersion' => '',
			'newVersion' => '',
		);

		if (false !== strpos($plugin, '..') || false !== strpos($plugin, ':') || !preg_match('#^[^\/]#i', $plugin)) {
			$status['error'] = 'not_found';
			return $status;
		}
		
		$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
		if (!isset($plugin_data['Name']) || !isset($plugin_data['Author']) || ('' == $plugin_data['Name'] && '' == $plugin_data['Author'])) {
			$status['error'] = 'not_found';
			return $status;
		}
		
		if ($plugin_data['Version']) {
			$status['oldVersion'] = $plugin_data['Version'];
		}

		if (!current_user_can('update_plugins')) {
			$status['error'] = 'updates_permission_denied';
			return $status;
		}

		include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

		wp_update_plugins();

		// WP < 3.7
		if (!class_exists('Automatic_Upgrader_Skin')) include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/classes/class-automatic-upgrader-skin.php');
		
		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);
		$result = $upgrader->bulk_upgrade(array($plugin));

		if (is_array($result) && empty($result[$plugin]) && is_wp_error($skin->result)) {
			$result = $skin->result;
		}

		$status['messages'] = $upgrader->skin->get_upgrade_messages();
		
		if (is_array($result) && !empty($result[$plugin])) {
			$plugin_update_data = current($result);

			/*
			* If the `update_plugins` site transient is empty (e.g. when you update
			* two plugins in quick succession before the transient repopulates),
			* this may be the return.
			*
			* Preferably something can be done to ensure `update_plugins` isn't empty.
			* For now, surface some sort of error here.
			*/
			if (true === $plugin_update_data) {
				$status['error'] = 'update_failed';
				return $status;
			}

			$plugin_data = get_plugins('/' . $result[$plugin]['destination_name']);
			$plugin_data = reset($plugin_data);

			if ($plugin_data['Version']) {
				$status['newVersion'] = $plugin_data['Version'];
			}
			return $status;
			
		} elseif (is_wp_error($result)) {
			$status['error'] = $result->get_error_code();
			$status['error_message'] = $result->get_error_message();
			return $status;

		} elseif (is_bool($result) && !$result) {
			$status['error'] = 'unable_to_connect_to_filesystem';

			global $wp_filesystem;
			
			// Pass through the error from WP_Filesystem if one was raised
			if (isset($wp_filesystem->errors) && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['error'] = $wp_filesystem->errors->get_error_code();
				$status['error_message'] = $wp_filesystem->errors->get_error_message();
			}

			return $status;

		} else {
			// An unhandled error occured
			$status['error'] = 'update_failed';
			return $status;
		}
	}
	
	/**
	 * Adapted from _update_theme (above)
	 *
	 * @param  string $core
	 * @return array
	 */
	private function _update_core($core) {

		global $wp_filesystem;

		$status = array(
			'update'     => 'core',
			'core'     => $core,
			'oldVersion' => '',
			'newVersion' => '',
		);

		// THis is included so we can get $wp_version
		include(ABSPATH.WPINC.'/version.php');
		
		$status['oldVersion'] = $wp_version;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		
		if (!current_user_can('update_core')) {
			$status['error'] = 'updates_permission_denied';
			return $status;
		}

		include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

		wp_version_check();
		
		$locale = get_locale();// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		$core_update_key = false;
		$core_update_latest_version = false;
		
		$get_core_updates = get_core_updates();
		
		// THis is included so we can get $wp_version
		@include(ABSPATH.WPINC.'/version.php');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
		foreach ($get_core_updates as $k => $core_update) {
			if (isset($core_update->version) && version_compare($core_update->version, $wp_version, '>') && version_compare($core_update->version, $core_update_latest_version, '>')) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
				$core_update_latest_version = $core_update->version;
				$core_update_key = $k;
			}
		}
		
		if (false === $core_update_key) {
			$status['error'] = 'no_update_found';
			return $status;
		}

		$update = $get_core_updates[$core_update_key];

		// WP < 3.7
		if (!class_exists('Automatic_Upgrader_Skin')) include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/classes/class-automatic-upgrader-skin.php');
		
		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Core_Upgrader($skin);

		$result = $upgrader->upgrade($update);

		$status['messages'] = $upgrader->skin->get_upgrade_messages();

		if (is_wp_error($result)) {
			$status['error'] = $result->get_error_code();
			$status['error_message'] = $result->get_error_message();
			return $status;

		} elseif (is_bool($result) && !$result) {
			$status['error'] = 'unable_to_connect_to_filesystem';

			// Pass through the error from WP_Filesystem if one was raised
			if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['error'] = $wp_filesystem->errors->get_error_code();
				$status['error_message'] = $wp_filesystem->errors->get_error_message();
			}

			return $status;

		
		} elseif (preg_match('/^[0-9]/', $result)) {
			
			$status['newVersion'] = $result;

			return $status;
			
		} else {
			// An unhandled error occured
			$status['error'] = 'update_failed';
			return $status;
		}

	}

	private function _update_theme($theme) {

		global $wp_filesystem;

		$status = array(
			'update'     => 'theme',
			'theme'     => $theme,
			'oldVersion' => '',
			'newVersion' => '',
		);

		if (false !== strpos($theme, '/') || false !== strpos($theme, '\\')) {
			$status['error'] = 'not_found';
			return $status;
		}
	
		$theme_version = $this->get_theme_version($theme);
		if (false === $theme_version) {
			$status['error'] = 'not_found';
			return $status;
		}
		$status['oldVersion'] = $theme_version;
		
		if (!current_user_can('update_themes')) {
			$status['error'] = 'updates_permission_denied';
			return $status;
		}

		include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

		wp_update_themes();

		// WP < 3.7
		if (!class_exists('Automatic_Upgrader_Skin')) include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/classes/class-automatic-upgrader-skin.php');
		
		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Theme_Upgrader($skin);
		$upgrader->init();
		$result = $upgrader->bulk_upgrade(array($theme));
		
		if (is_array($result) && empty($result[$theme]) && is_wp_error($skin->result)) {
			$result = $skin->result;
		}

		$status['messages'] = $upgrader->skin->get_upgrade_messages();

		if (is_array($result) && !empty($result[$theme])) {
			$theme_update_data = current($result);

			/*
			* If the `update_themes` site transient is empty (e.g. when you update
			* two plugins in quick succession before the transient repopulates),
			* this may be the return.
			*
			* Preferably something can be done to ensure `update_themes` isn't empty.
			* For now, surface some sort of error here.
			*/
			if (true === $theme_update_data) {
				$status['error'] = 'update_failed';
				return $status;
			}
			
			$new_theme_version = $this->get_theme_version($theme);
			if (false === $new_theme_version) {
				$status['error'] = 'update_failed';
				return $status;
			}

			$status['newVersion'] = $new_theme_version;

			return $status;
			
		} elseif (is_wp_error($result)) {
			$status['error'] = $result->get_error_code();
			$status['error_message'] = $result->get_error_message();
			return $status;

		} elseif (is_bool($result) && !$result) {
			$status['error'] = 'unable_to_connect_to_filesystem';

			// Pass through the error from WP_Filesystem if one was raised
			if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['error'] = $wp_filesystem->errors->get_error_code();
				$status['error_message'] = $wp_filesystem->errors->get_error_message();
			}

			return $status;

		} else {
			// An unhandled error occured
			$status['error'] = 'update_failed';
			return $status;
		}

	}
	
	/**
	 * Updates available translations for this website
	 *
	 * @return Array
	 */
	private function _update_translation() {
		global $wp_filesystem;

		$status = array();

		include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
		if (!class_exists('Automatic_Upgrader_Skin')) include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/classes/class-automatic-upgrader-skin.php');
		
		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Language_Pack_Upgrader($skin);
		$result = $upgrader->bulk_upgrade();
		
		if (is_array($result) && !empty($result)) {
			$status['success'] = true;
		} elseif (is_wp_error($result)) {
			$status['error'] = $result->get_error_code();
			$status['error_message'] = $result->get_error_message();
		} elseif (is_bool($result) && !$result) {
			$status['error'] = 'unable_to_connect_to_filesystem';

			// Pass through the error from WP_Filesystem if one was raised
			if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['error'] = $wp_filesystem->errors->get_error_code();
				$status['error_message'] = $wp_filesystem->errors->get_error_message();
			}
		} elseif (is_bool($result) && $result) {
			$status['error'] = 'up_to_date';
		} else {
			// An unhandled error occured
			$status['error'] = 'update_failed';
		}

		return $status;
	}
	
	private function get_theme_version($theme) {
	
		if (function_exists('wp_get_theme')) {
			// Since WP 3.4.0
			$theme = wp_get_theme($theme);
			
			if (is_a($theme, 'WP_Theme')) {
				return $theme->Version;
			} else {
				return false;
			}
			
		} else {
			$theme_data = get_theme_data(WP_CONTENT_DIR . '/themes/'.$theme.'/style.css');
			
			if (isset($theme_data['Version'])) {
				return $theme_data['Version'];
			} else {
				return false;
			}
		}
	}

	/**
	 * Adding third-party plugins/theme for UDC automatic updates, for some updaters which store their information when the transient is set, instead of (like most) when it is fetched
	 *
	 * @param Array	 $items	A collection of plugins or themes for updates
	 * @param String $type	A string indicating which type of collection to process (e.g. 'plugin' or 'theme')
	 * @return Array An updated collection of plugins or themes for updates
	 */
	private function maybe_add_third_party_items($items, $type) {

		// Here we're preparing a dummy transient object that will be pass to the filter
		// and gets populated by those plugins or themes that hooked into the "pre_set_site_transient_*" filter.
		//
		// We're setting some default properties so that plugins and themes won't be able to bypass populating them,
		// because most of the plugins and themes updater scripts checks whether or not these properties are set and
		// non-empty or passed the 12 hour period (where WordPress re-starts the process of checking updates for
		// these plugins and themes), otherwise, they bypass populating the update/upgrade info for these items.
		$transient = (object) array(
			'last_checked' => time() - (13 * 3600), /* Making sure that we passed the 12 hour period check */
			'checked' => array('default' => 'none'),
			'response' => array('default' => 'none')
		);

		// Most of the premium plugin developers are hooking into the "pre_set_site_transient_update_plugins" and
		// "pre_set_site_transient_update_themes" filters if they want their plugins or themes to support automatic
		// updates. Thus, we're making sure here that if for some reason, those plugins or themes didn't get through
		// and added to the "update_plugins" or "update_themes" transients when calling the get_site_transient('update_plugins')
		// or get_site_transient('update_themes') we add them here manually.
		$filters = apply_filters("pre_set_site_transient_update_{$type}s", $transient, "update_{$type}s");


		$all_items = array();
		switch ($type) {
			case 'plugin':
				$all_items = get_plugins();
				break;
			case 'theme':
				$this->_frontend_include('theme.php');
				if (function_exists('wp_get_themes')) {
					$themes = wp_get_themes();
					if (!empty($themes)) {
						// We make sure that the return key matched the previous
						// key from "get_themes", otherwise, no updates will be found
						// even if it does have one. "get_themes" returns the name of the
						// theme as the key while "wp_get_themes" returns the slug.
						foreach ($themes as $theme) {
							$all_items[$theme->Name] = $theme;
						}
					}
				} else {
					$all_items = get_themes();
				}
				break;
			default:
				break;
		}


		if (!empty($all_items)) {
			$all_items = (array) $all_items;
			foreach ($all_items as $key => $data) {
				if (!isset($items[$key]) && isset($filters->response[$key])) {

					$update_info = ('plugin' === $type) ? $filters->response[$key] : $data;

					// If "package" is empty, it means that this plugin or theme does not support automatic updates
					// currently, since the "package" field is the one holding the download link of these plugins/themes
					// and WordPress is using this field to download the latest version of these items.
					//
					// Most of the time, this "package" field is not empty, but for premium plugins/themes this can be
					// conditional, only then if the user provides a legit access or api key can this field be populated or available.
					//
					// We set this variable to "false" by default, as plugins/themes hosted in wordpress.org always sets this
					// to the downloadable zip file of the plugin/theme.
					//
					// N.B. We only add premium plugins/themes that has this "package" field set and non-empty, otherwise, it
					// does not support automatic updates as explained above.
					$is_package_empty = false;

					if (is_object($update_info)) {
						if (!isset($update_info->package) || empty($update_info->package)) {
							$is_package_empty = true;
						}

					} elseif (is_array($update_info)) {
						if (!isset($update_info['package']) || empty($update_info['package'])) {
							$is_package_empty = true;
						}
					}

					// Add this plugin/theme to the current updates collection
					if (!$is_package_empty) {
						$items[$key] = ('plugin' === $type) ? (object) $data : $this->get_theme_info($key);
						$items[$key]->update = $update_info;
					}

				}
			}
		}

		return $this->prep_items_for_updates($items, $type);
	}

	/**
	 * Extracts theme's data or information
	 *
	 * @param  string $theme A string representing a theme's name or slug.
	 * @return object|boolean If successful, an object containing the theme data or information, "false" otherwise.
	 */
	private function get_theme_info($theme) {

		if (function_exists('wp_get_theme')) {
			$theme = wp_get_theme($theme);
			if (is_a($theme, 'WP_Theme')) {
				return $theme;
			}
		} else {
			$theme_data = get_theme_data(WP_CONTENT_DIR.'/themes/'.$theme.'/style.css');
			if (isset($theme_data['Version'])) {
				if (!isset($theme_data['ThemeURI'])) $theme_data['ThemeURI'] = $theme_data['URI'];
				return (object) $theme_data;
			}
		}

		return false;
	}

	/**
	 * Fix items for update with missing "plugin" or "theme" field if applicable
	 *
	 * @param Array	 $items	A collection of plugins or themes for updates
	 * @param String $type	A string indicating which type of collection to process (e.g. 'plugin' or 'theme')
	 * @return Array An updated collection of plugins or themes for updates
	 */
	private function prep_items_for_updates($items, $type) {

		foreach ($items as $key => $data) {
			$update_info = $data->update;

			// Some plugins and/or themes does not adhere to the standard WordPress updates meta
			// properties/fields. Thus, missing some fields such as "plugin" or "theme"
			// in their update information results in "Automatic updates is unavailable for this item"
			// in UDC since we're using these fields to process the updates.
			//
			// As a workaround, we're filling these missing fields in order to solve the above issue
			// in case the developer of these plugins/themes forgot to include them.
			if (is_object($update_info)) {
				$update_info = (array) $update_info;
				if (!isset($update_info[$type])) {
					$update_info[$type] = $key;
				}

				$update_info = (object) $update_info;

			} elseif (is_array($update_info)) {
				if (!isset($update_info[$type])) {
					$update_info[$type] = $key;
				}
			}

			// Re-assign the updated info to the original "update" property
			$items[$key]->update = $update_info;
		}

		return $items;
	}

	/**
	 * Custom validation for translation permission. Since the 'install_languages' capability insn't available until 4.9
	 * therefore, we wrapped the validation check in this block to support older version of WP.
	 *
	 * @return Boolean
	 */
	private function user_can_update_translations() {
		global $updraftcentral_main;
		$wp_version = $updraftcentral_main->get_wordpress_version();
		
		if (version_compare($wp_version, '4.9', '<')) {
			if (current_user_can('update_core') || current_user_can('update_plugins') || current_user_can('update_themes')) return true;
		} else {
			if (current_user_can('install_languages')) return true;
		}

		return false;
	}

	public function get_updates($options) {

		// Forcing Elegant Themes (Divi) updates component to load if it exist.
		if (function_exists('et_register_updates_component')) et_register_updates_component();

		if (!current_user_can('update_plugins') && !current_user_can('update_themes') && !current_user_can('update_core')) return $this->_generic_error_response('updates_permission_denied');

		$this->_admin_include('plugin.php', 'update.php', 'file.php', 'template.php');
		$this->_frontend_include('update.php');

		if (!is_array($options)) $options = array();
		
		// Normalise it
		$plugin_updates = array();
		if (current_user_can('update_plugins')) {
		
			// Detect if refresh needed
			$transient = get_site_transient('update_plugins');
			if (!empty($options['force_refresh']) || false === $transient) {
				delete_site_transient('update_plugins');
				wp_update_plugins();
			}
		
			$get_plugin_updates = $this->maybe_add_third_party_items(get_plugin_updates(), 'plugin');
			if (is_array($get_plugin_updates)) {
				foreach ($get_plugin_updates as $update) {

					// For some reason, some 3rd-party (premium) plugins are returning the same version
					// with that of the currently installed version in WordPress. Thus, we're making sure here to
					// only return those items for update that has new versions greater than the currently installed version.
					if (version_compare($update->Version, $update->update->new_version, '>=')) continue;
					
					$plugin_updates[] = array(
						'name' => $update->Name,
						'plugin_uri' => $update->PluginURI,
						'version' => $update->Version,
						'description' => $update->Description,
						'author' => $update->Author,
						'author_uri' => $update->AuthorURI,
						'title' => $update->Title,
						'author_name' => $update->AuthorName,
						'update' => array(
							// With Affiliates-WP, if you have not connected, this is null.
							'plugin' => isset($update->update->plugin) ? $update->update->plugin : null,
							'slug' => $update->update->slug,
							'new_version' => $update->update->new_version,
							'package' => $update->update->package,
							'tested' => isset($update->update->tested) ? $update->update->tested : null,
							'compatibility' => isset($update->update->compatibility) ? (array) $update->update->compatibility : null,
							'sections' => isset($update->update->sections) ? (array) $update->update->sections : null,
						),
					);
				}
			}
		}
		
		$theme_updates = array();
		if (current_user_can('update_themes')) {
		
			// Detect if refresh needed
			$transient = get_site_transient('update_themes');
			if (!empty($options['force_refresh']) || false === $transient) {
				delete_site_transient('update_themes');
				wp_update_themes();
			}
			$get_theme_updates = $this->maybe_add_third_party_items(get_theme_updates(), 'theme');
			if (is_array($get_theme_updates)) {
				foreach ($get_theme_updates as $update) {

					// We're making sure here to only return those items for update that has new
					// versions greater than the currently installed version.
					if (version_compare($update->Version, $update->update['new_version'], '>=')) continue;
					
					$name = $update->Name;
					$theme_name = !empty($name) ? $name : $update->update['theme'];

					$theme_updates[] = array(
						'name' => $theme_name,
						'theme_uri' => $update->ThemeURI,
						'version' => $update->Version,
						'description' => $update->Description,
						'author' => $update->Author,
						'author_uri' => $update->AuthorURI,
						'update' => array(
							'theme' => $update->update['theme'],
							'new_version' => $update->update['new_version'],
							'package' => $update->update['package'],
							'url' => $update->update['url'],
						),
					);

				}
			}
		}
		
		$core_updates = array();
		if (current_user_can('update_core')) {
		
			// Detect if refresh needed
			$transient = get_site_transient('update_core');
			if (!empty($options['force_refresh']) || false === $transient) {
				// The next line is only needed for older WP versions - otherwise, the parameter to wp_version_check forces a check.
				delete_site_transient('update_core');
				wp_version_check(array(), true);
			}
		
			$get_core_updates = get_core_updates();

			if (is_array($get_core_updates)) {
			
				$core_update_key = false;
				$core_update_latest_version = false;
				
				// THis is included so we can get $wp_version
				@include(ABSPATH.WPINC.'/version.php');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				
				foreach ($get_core_updates as $k => $core_update) {
					if (isset($core_update->version) && version_compare($core_update->version, $wp_version, '>') && version_compare($core_update->version, $core_update_latest_version, '>')) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
						$core_update_latest_version = $core_update->version;
						$core_update_key = $k;
					}
				}

				if (false !== $core_update_key) {
				
					$update = $get_core_updates[$core_update_key];
					
					global $wpdb;
					
					$mysql_version = $wpdb->db_version();
					
					$is_mysql = (file_exists(WP_CONTENT_DIR . '/db.php') && empty($wpdb->is_mysql)) ? false : true;
					
					// We're making sure here to only return those items for update that has new
					// versions greater than the currently installed version.
					if (version_compare($wp_version, $update->version, '<')) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
						$core_updates[] = array(
							'download' => $update->download,
							'version' => $update->version,
							'php_version' => $update->php_version,
							'mysql_version' => $update->mysql_version,
							'installed' => array(
								'version' => $wp_version,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
								'mysql' => $mysql_version,
								'php' => PHP_VERSION,
								'is_mysql' => $is_mysql,
							),
							'sufficient' => array(
								'mysql' => version_compare($mysql_version, $update->mysql_version, '>='),
								'php' => version_compare(PHP_VERSION, $update->php_version, '>='),
							),
						);
					}
					
				}
			}
			
		}
		
		$translation_updates = array();
		if (function_exists('wp_get_translation_updates') && $this->user_can_update_translations()) {
			$translations = wp_get_translation_updates();

			$translation_updates = array(
				'items' => $translations
			);
		}
		
		// Do we need to ask the user for filesystem credentials?
		$request_filesystem_credentials = array();
		$check_fs = array(
			'plugins' => WP_PLUGIN_DIR,
			'themes' => WP_CONTENT_DIR.'/themes',
			'core' => untrailingslashit(ABSPATH)
		);
		
		if (!empty($translation_updates)) {
			// 'en_US' don't usually have the "languages" folder, thus, we
			// check if there's a need to ask for filesystem credentials for that
			// folder if it exists, most especially for locale other than 'en_US'.
			$language_dir = WP_CONTENT_DIR.'/languages';
			if ('en_US' !== get_locale() && is_dir($language_dir)) {
				$check_fs['translations'] = $language_dir;
			}
		}
		
		foreach ($check_fs as $entity => $dir) {
			$filesystem_method = get_filesystem_method(array(), $dir);
			ob_start();
			$filesystem_credentials_are_stored = request_filesystem_credentials(site_url());
			$filesystem_form = strip_tags(ob_get_contents(), '<div><h2><p><input><label><fieldset><legend><span><em>');
			ob_end_clean();
			$request_filesystem_credentials[$entity] = ('direct' != $filesystem_method && !$filesystem_credentials_are_stored);
		}
		
		$automatic_backups = (class_exists('UpdraftPlus_Options') && class_exists('UpdraftPlus_Addon_Autobackup') && UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default', true)) ? true : false;
		
		return $this->_response(array(
			'plugins' => $plugin_updates,
			'themes' => $theme_updates,
			'core' => $core_updates,
			'translations' => $translation_updates,
			'meta' => array(
				'request_filesystem_credentials' => $request_filesystem_credentials,
				'filesystem_form' => $filesystem_form,
				'automatic_backups' => $automatic_backups
			),
		));
	}
}
