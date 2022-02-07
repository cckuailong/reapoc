<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No access.');

/**
 * A class for interfacing with storage methods.
 * N.B. This class began life Sep 2018; it is not guaranteed that there are not many places that bypass it that could be ported over to use it.
 */
class UpdraftPlus_Storage_Methods_Interface {

	/**
	 * Instantiate a remote storage object. If one of the same type has previously been fetched, then it will be returned.
	 *
	 * @param String $method - the storage method (e.g. 'dropbox', 's3', etc.)
	 *
	 * @return Object|WP_Error - an instance of UpdraftPlus_BackupModule, or an error
	 */
	public static function get_storage_object($method) {
	
		if (!preg_match('/^[\-a-z0-9]+$/i', $method)) return new WP_Error('no_such_storage_class', "The specified storage method ($method) was not found");
	
		static $objects = array();
	
		if (!empty($objects[$method])) return $objects[$method];
	
		$method_class = 'UpdraftPlus_BackupModule_'.$method;
		
		if (!class_exists($method_class)) include_once UPDRAFTPLUS_DIR.'/methods/'.$method.'.php';
		
		if (!class_exists($method_class)) return new WP_Error('no_such_storage_class', "The specified storage method ($method) was not found");
		
		$objects[$method] = new $method_class;
		
		return $objects[$method];
	}
	
	/**
	 * This method will return an array of remote storage options and storage_templates.
	 *
	 * @return Array - returns an array which consists of storage options and storage_templates multidimensional array
	 */
	public static function get_remote_storage_options_and_templates() {

		global $updraftplus;
	
		$storage_objects_and_ids = self::get_storage_objects_and_ids(array_keys($updraftplus->backup_methods));
		$options = array();
		$templates = array();

		foreach ($storage_objects_and_ids as $method => $method_info) {

			$object = $method_info['object'];

			if (!$object->supports_feature('multi_options')) {
				ob_start();
				do_action('updraftplus_config_print_before_storage', $method, null);
				do_action('updraftplus_config_print_add_conditional_logic', $method, $object);
				$object->config_print();
				$templates[$method] = ob_get_clean();
			} else {
				$templates[$method] = $object->get_template();
			}

			if (isset($method_info['instance_settings'])) {
				// Add the methods default settings so that we can add new instances
				$method_info['instance_settings']['default'] = $object->get_default_options();

				foreach ($method_info['instance_settings'] as $instance_id => $instance_options) {

					$opts_without_transform = $instance_options;

					if ($object->supports_feature('multi_options')) {
						$opts_without_transform['instance_id'] = $instance_id;
					}

					$opts = $object->transform_options_for_template($opts_without_transform);

					foreach ($object->filter_frontend_settings_keys() as $filter_frontend_settings_key) {
						unset($opts[$filter_frontend_settings_key]);
					}

					$options[$method][$instance_id] = $opts;
				}
			}
		}

		return array(
			'options' => $options,
			'templates' => $templates,
		);
	}
	
	/**
	 * This method will return an array of remote storage objects and instance settings of the currently connected remote storage services.
	 *
	 * @param Array $services - an list of service identifiers (e.g. ['dropbox', 's3'])
	 *
	 * @uses self::get_storage_object()
	 *
	 * @return Array - returns an array, with a key equal to each member of the $services list passed in. The corresponding value is then an array with keys 'object', 'instance_settings'. The value for 'object' is an UpdraftPlus_BackupModule instance. The value for 'instance_settings' is an array keyed by associated instance IDs, with the values being the associated settings for the instance ID.
	 */
	public static function get_storage_objects_and_ids($services) {
	
		$storage_objects_and_ids = array();

		// N.B. The $services can return any type of values (null, false, etc.) as mentioned from one of the comment found
		// in the "save_backup_to_history" function above especially if upgrading from (very) old versions. Thus,
		// here we're adding some check to make sure that we're receiving a non-empty array before iterating through
		// all the backup services that the user has in store.
		if (empty($services) || !is_array($services)) return $storage_objects_and_ids;

		foreach ($services as $method) {

			if ('none' === $method || '' == $method) continue;
		
			$remote_storage = self::get_storage_object($method);
		
			if (is_a($remote_storage, 'UpdraftPlus_BackupModule')) {
			
				if (empty($storage_objects_and_ids[$method])) $storage_objects_and_ids[$method] = array();
				
				$storage_objects_and_ids[$method]['object'] = $remote_storage;
				
				if ($remote_storage->supports_feature('multi_options')) {
				
					$settings_from_db = UpdraftPlus_Options::get_updraft_option('updraft_'.$method);
					
					$settings = is_array($settings_from_db) ? $settings_from_db : array();
				
					if (!isset($settings['version'])) $settings = self::update_remote_storage_options_format($method);
					
					if (is_wp_error($settings)) {
						if (!empty($settings_from_db)) error_log("UpdraftPlus: failed to convert storage options format: $method");
						$settings = array('settings' => array());
					}

					if (empty($settings['settings'])) {
					
						// Try to recover by getting a default set of options for display
						if (is_callable(array($remote_storage, 'get_default_options'))) {
							$uuid = 's-'.md5(rand().uniqid().microtime(true));
							$settings['settings'] = array($uuid => $remote_storage->get_default_options());
						}
						
						// See: https://wordpress.org/support/topic/cannot-setup-connectionauthenticate-with-dropbox/
						if (empty($settings['settings'])) {
							// This can get sent to the browser, and break the page, if the user has configured that. However, it should now (1.13.6+) be impossible for this condition to occur, now that we only log it after getting some default options.
							error_log("UpdraftPlus: Warning: settings for $method are empty. A dummy field is usually needed so that something is saved.");
						}
						
					}

					if (!empty($settings['settings'])) {
						
						if (!isset($storage_objects_and_ids[$method]['instance_settings'])) $storage_objects_and_ids[$method]['instance_settings'] = array();
						
						foreach ($settings['settings'] as $instance_id => $storage_options) {
							$storage_objects_and_ids[$method]['instance_settings'][$instance_id] = $storage_options;
						}
					}
				} else {
					if (!isset($storage_objects_and_ids[$method]['instance_settings'])) $storage_objects_and_ids[$method]['instance_settings'] = $remote_storage->get_default_options();
				}

			} else {
				error_log("UpdraftPlus: storage method not found: $method");
			}
		}

		return $storage_objects_and_ids;
		
	}
	
	/**
	 * This converts array-style options (i.e. late 2013-onwards) to
	 * 2017-style multi-array-style options.
	 *
	 * N.B. Don't actually call this on any particular method's options
	 * until the functions which read the options can cope!
	 *
	 * Don't call for settings that aren't array-style. You may lose
	 * the settings if you do.
	 *
	 * It is safe to call this if you are not sure if the options are
	 * already updated.
	 *
	 * @param String $method - the method identifier
	 *
	 * @returns Array|WP_Error - returns the new options, or a WP_Error if it failed
	 */
	public static function update_remote_storage_options_format($method) {
	
		global $updraftplus;
	
		// Prevent recursion
		static $already_active = false;
		
		if ($already_active) return new WP_Error('recursion', 'self::update_remote_storage_options_format() was called in a loop. This is usually caused by an options filter failing to correctly process a "recursion" error code');
	
		if (!file_exists(UPDRAFTPLUS_DIR.'/methods/'.$method.'.php')) return new WP_Error('no_such_method', 'Remote storage method not found', $method);
		
		// Sanity/inconsistency check
		$settings_keys = $updraftplus->get_settings_keys();
		
		$method_key = 'updraft_'.$method;
		
		if (!in_array($method_key, $settings_keys)) return new WP_Error('no_such_setting', 'Setting not found for this method', $method);
	
		$current_setting = UpdraftPlus_Options::get_updraft_option($method_key, array());
		if ('' == $current_setting) $current_setting = array();
		
		if (!is_array($current_setting) && false !== $current_setting) return new WP_Error('format_unrecognised', 'Settings format not recognised', array('method' => $method, 'current_setting' => $current_setting));

		// Already converted?
		if (isset($current_setting['version'])) return $current_setting;
		if (empty($current_setting)) {
			$remote_storage = self::get_storage_object($method);
			$current_setting = $remote_storage->get_default_options();
		}
		$new_setting = self::wrap_remote_storage_options($current_setting);
		
		$already_active = true;
		$updated = UpdraftPlus_Options::update_updraft_option($method_key, $new_setting);
		$already_active = false;
		
		if ($updated) {
			return $new_setting;
		} else {
			return new WP_Error('save_failed', 'Saving the options in the new format failed', array('method' => $method, 'current_setting' => $new_setting));
		}
	
	}
	
	/**
	 * This method will return an array of enabled remote storage objects and instance settings of the currently connected remote storage services.
	 *
	 * @param Array $services                 - an list of service identifiers (e.g. ['dropbox', 's3'])
	 * @param Array $remote_storage_instances - a list of remote storage instances the user wants to backup to, if empty we use the saved options
	 *
	 * @uses self::get_storage_objects_and_ids()
	 *
	 * @return Array					- returns an array, with a key equal to only enabled service member of the $services list passed in. The corresponding value is then an array with keys 'object', 'instance_settings'. The value for 'object' is an UpdraftPlus_BackupModule instance. The value for 'instance_settings' is an array keyed by associated enabled instance IDs, with the values being the associated settings for the enabled instance ID.
	 */
	public static function get_enabled_storage_objects_and_ids($services, $remote_storage_instances = array()) {
		
		$storage_objects_and_ids = self::get_storage_objects_and_ids($services);
		
		foreach ($storage_objects_and_ids as $method => $method_information) {

			if (!$method_information['object']->supports_feature('multi_options')) continue;
			
			foreach ($method_information['instance_settings'] as $instance_id => $instance_information) {
				if (!isset($instance_information['instance_enabled'])) $instance_information['instance_enabled'] = 1;
				if (!empty($remote_storage_instances) && isset($remote_storage_instances[$method]) && !in_array($instance_id, $remote_storage_instances[$method])) {
					unset($storage_objects_and_ids[$method]['instance_settings'][$instance_id]);
				} elseif (empty($remote_storage_instances) && empty($instance_information['instance_enabled'])) {
					unset($storage_objects_and_ids[$method]['instance_settings'][$instance_id]);
				}
			}
			
			if (empty($storage_objects_and_ids[$method]['instance_settings'])) unset($storage_objects_and_ids[$method]);
		}
		
		return $storage_objects_and_ids;
	}
	
	/**
	 * This method gets the remote storage information and objects and loops over each of them until we get a successful download of the passed in file.
	 *
	 * @param  Array   $services  - a list of connected service identifiers (e.g. 'dropbox', 's3', etc.)
	 * @param  String  $file      - the name of the file
	 * @param  Integer $timestamp - the backup timestamp
	 * @param  Boolean $restore   - a boolean to indicate if the caller of this method is a restore or not; if so, different messages are logged
	 */
	public static function get_remote_file($services, $file, $timestamp, $restore = false) {
		
		global $updraftplus;

		$backup_history = UpdraftPlus_Backup_History::get_history();
		
		$fullpath = $updraftplus->backups_dir_location().'/'.$file;

		$storage_objects_and_ids = self::get_storage_objects_and_ids($services);

		$is_downloaded = false;

		$updraftplus->register_wp_http_option_hooks();

		foreach ($services as $service) {

			if (empty($service) || 'none' == $service || $is_downloaded) continue;

			if ($restore) {
				$service_description = empty($updraftplus->backup_methods[$service]) ? $service : $updraftplus->backup_methods[$service];
				$updraftplus->log(__("File is not locally present - needs retrieving from remote storage", 'updraftplus')." ($service_description)", 'notice-restore');
			}

			$object = $storage_objects_and_ids[$service]['object'];

			if (!$object->supports_feature('multi_options')) {
				error_log("UpdraftPlus_Storage_Methods_Interface::get_remote_file(): Multi-options not supported by: ".$service);
				continue;
			}
			
			$instance_ids = $storage_objects_and_ids[$service]['instance_settings'];
			$backups_instance_ids = isset($backup_history[$timestamp]['service_instance_ids'][$service]) ? $backup_history[$timestamp]['service_instance_ids'][$service] : array(false);

			foreach ($backups_instance_ids as $instance_id) {

				if (isset($instance_ids[$instance_id])) {
					$options = $instance_ids[$instance_id];
				} else {
					// If we didn't find a instance id match, it could be a new UpdraftPlus upgrade or a wipe settings with the same details entered so try the default options saved.
					$options = $object->get_options();
				}

				$object->set_options($options, false, $instance_id);

				$download = self::download_file($file, $object);

				if (is_readable($fullpath) && false !== $download) {
					if ($restore) {
						$updraftplus->log(__('OK', 'updraftplus'), 'notice-restore');
					} else {
						clearstatcache();
						$updraftplus->log('Remote fetch was successful (file size: '.round(filesize($fullpath)/1024, 1).' KB)');
						$is_downloaded = true;
					}
					break 2;
				} else {
					if ($restore) {
						$updraftplus->log(__('Error', 'updraftplus'), 'notice-restore');
					} else {
						clearstatcache();
						if (0 === @filesize($fullpath)) @unlink($fullpath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						$updraftplus->log('Remote fetch failed');
					}
				}
			}
		}
		$updraftplus->register_wp_http_option_hooks(false);
	}
	
	/**
	 * Downloads a specified file into UD's directory
	 *
	 * @param String				   $file		   The basename of the file
	 * @param UpdraftPlus_BackupModule $service_object The object of the service to use to download with.
	 *
	 * @return Boolean - Whether the operation succeeded. Inherited from the storage module's download() method. N.B. At the time of writing it looks like not all modules necessarily return true upon success; but false can be relied upon for detecting failure.
	 */
	private static function download_file($file, $service_object) {

		global $updraftplus;
	
		if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$service = $service_object->get_id();
		
		$updraftplus->log("Requested file from remote service: $service: $file");

		if (method_exists($service_object, 'download')) {
		
			try {
				return $service_object->download($file);
			} catch (Exception $e) {
				$log_message = 'Exception ('.get_class($e).') occurred during download: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				// @codingStandardsIgnoreLine
				$log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP exception (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
				return false;
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during download. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				// @codingStandardsIgnoreLine
				$log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP fatal error (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
				return false;
			}
		} else {
			$updraftplus->log("Automatic backup restoration is not available with the method: $service.");
			$updraftplus->log("$file: ".sprintf(__("The backup archive for this file could not be found. The remote storage method in use (%s) does not allow us to retrieve files. To perform any restoration using UpdraftPlus, you will need to obtain a copy of this file and place it inside UpdraftPlus's working folder", 'updraftplus'), $service)." (".UpdraftPlus_Manipulation_Functions::prune_updraft_dir_prefix($updraftplus->backups_dir_location()).")", 'error');
			return false;
		}

	}
	
	/**
	 * This method will update the old style remote storage options to the new style (Apr 2017) if the user has imported a old style version of settings
	 *
	 * @param  Array $options - The remote storage options settings array
	 * @return Array          - The updated remote storage options settings array
	 */
	public static function wrap_remote_storage_options($options) {
		// Already converted?
		if (isset($options['version'])) return $options;
		
		// Generate an instance id
		$uuid = self::generate_instance_id();
		
		$new_setting = array(
			'version' => 1,
		);
		
		if (!is_array($options)) $options = array();

		$new_setting['settings'] = array($uuid => $options);

		return $new_setting;
	}

	/**
	 * This method will return a random instance id string
	 *
	 * @return String - a random instance id
	 */
	private static function generate_instance_id() {
		// Cryptographic randomness not required. The prefix helps avoid potential for type-juggling issues.
		return 's-'.md5(rand().uniqid().microtime(true));
	}
}
