<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

/**
 * Converted to job_options: yes
 * Converted to array options: yes
 * Migration code for "new"-style options removed: Feb 2017 (created: Dec 2013)
 */

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

/**
 * Old SDK
 */
class UpdraftPlus_BackupModule_cloudfiles_oldsdk extends UpdraftPlus_BackupModule {

	/**
	 * This function does not catch any exceptions - that should be done by the caller
	 *
	 * @param  String  $user
	 * @param  String  $apikey
	 * @param  String  $authurl
	 * @param  Boolean $useservercerts
	 * @return Array
	 */
	private function getCF($user, $apikey, $authurl, $useservercerts = false) {
		
		$storage = $this->get_storage();
		if (!empty($storage)) return $storage;
		
		if (!class_exists('UpdraftPlus_CF_Authentication')) include_once(UPDRAFTPLUS_DIR.'/includes/cloudfiles/cloudfiles.php');

		if (!defined('UPDRAFTPLUS_SSL_DISABLEVERIFY')) define('UPDRAFTPLUS_SSL_DISABLEVERIFY', UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'));

		$auth = new UpdraftPlus_CF_Authentication($user, trim($apikey), null, $authurl);

		$this->log("authentication URL: $authurl");

		$auth->authenticate();

		$storage = new UpdraftPlus_CF_Connection($auth);

		if (!$useservercerts) $storage->ssl_use_cabundle(UPDRAFTPLUS_DIR.'/includes/cacert.pem');

		$this->set_storage($storage);
		
		return $storage;

	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'user' => '',
			'authurl' => 'https://auth.api.rackspacecloud.com',
			'apikey' => '',
			'path' => '',
			'region' => null
		);
	}
	
	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && isset($opts['user']) && '' != $opts['user'] && !empty($opts['apikey'])) return true;
		return false;
	}

	public function backup($backup_array) {

		global $updraftplus;

		$opts = $this->get_options();

		$updraft_dir = $updraftplus->backups_dir_location().'/';

		$container = $opts['path'];

		try {
			$storage = $this->getCF($opts['user'], $opts['apikey'], $opts['authurl'], UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'));
			$container_object = $storage->create_container($container);
		} catch (AuthenticationException $e) {
			$this->log('authentication failed ('.$e->getMessage().')');
			$this->log(__('authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		} catch (NoSuchAccountException $s) {
			$this->log('authentication failed ('.$e->getMessage().')');
			$this->log(__('authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		} catch (Exception $e) {
			$this->log('error - failed to create and access the container ('.$e->getMessage().')');
			$this->log(__('error - failed to create and access the container', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		}

		$chunk_size = 5*1024*1024;

		foreach ($backup_array as $file) {

			$fullpath = $updraft_dir.$file;
			$orig_file_size = filesize($fullpath);

// $cfpath = ($path == '') ? $file : "$path/$file";
// $chunk_path = ($path == '') ? "chunk-do-not-delete-$file" : "$path/chunk-do-not-delete-$file";
			$cfpath = $file;
			$chunk_path = "chunk-do-not-delete-$file";

			try {
				$object = new UpdraftPlus_CF_Object($container_object, $cfpath);
				$object->content_type = "application/zip";

				$uploaded_size = (isset($object->content_length)) ? $object->content_length : 0;

				if ($uploaded_size <= $orig_file_size) {

					$fp = @fopen($fullpath, "rb");// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					if (!$fp) {
						$this->log("failed to open file: $fullpath");
						$this->log("$file: ".__('Error: Failed to open local file', 'updraftplus'), 'error');
						return false;
					}

					$chunks = floor($orig_file_size / $chunk_size);
					// There will be a remnant unless the file size was exactly on a 5MB boundary
					if ($orig_file_size % $chunk_size > 0) $chunks++;

					$this->log("upload: $file (chunks: $chunks) -> cloudfiles://$container/$cfpath ($uploaded_size)");

					if ($chunks < 2) {
						try {
							$object->load_from_filename($fullpath);
							$this->log("regular upload: success");
							$updraftplus->uploaded_file($file);
						} catch (Exception $e) {
							$this->log("regular upload: failed ($file) (".$e->getMessage().")");
							$this->log("$file: ".__('Error: Failed to upload', 'updraftplus'), 'error');
						}
					} else {
						$errors_so_far = 0;
						for ($i = 1; $i <= $chunks; $i++) {
							$upload_start = ($i-1)*$chunk_size;
							// The file size -1 equals the byte offset of the final byte
							$upload_end = min($i*$chunk_size-1, $orig_file_size-1);
							$upload_remotepath = $chunk_path."_$i";
							// Don't forget the +1; otherwise the last byte is omitted
							$upload_size = $upload_end - $upload_start + 1;
							$chunk_object = new UpdraftPlus_CF_Object($container_object, $upload_remotepath);
							$chunk_object->content_type = "application/zip";
							// Without this, some versions of Curl add Expect: 100-continue, which results in Curl then giving this back: curl error: 55) select/poll returned error
							// Didn't make the difference - instead we just check below for actual success even when Curl reports an error
							// $chunk_object->headers = array('Expect' => '');

							$remote_size = (isset($chunk_object->content_length)) ? $chunk_object->content_length : 0;

							if ($remote_size >= $upload_size) {
								$this->log("Chunk $i ($upload_start - $upload_end): already uploaded");
							} else {
								$this->log("Chunk $i ($upload_start - $upload_end): begin upload");
								// Upload the chunk
								fseek($fp, $upload_start);
								try {
									$chunk_object->write($fp, $upload_size, false);
									$updraftplus->record_uploaded_chunk(round(100*$i/$chunks, 1), $i, $fullpath);
								} catch (Exception $e) {
									$this->log("chunk upload: error: ($file / $i) (".$e->getMessage().")");
									// Experience shows that Curl sometimes returns a select/poll error (curl error 55) even when everything succeeded. Google seems to indicate that this is a known bug.
									
									$chunk_object = new UpdraftPlus_CF_Object($container_object, $upload_remotepath);
									$chunk_object->content_type = "application/zip";
									$remote_size = (isset($chunk_object->content_length)) ? $chunk_object->content_length : 0;
									
									if ($remote_size >= $upload_size) {

										$this->log("$file: Chunk now exists; ignoring error (presuming it was an apparently known curl bug)");

									} else {

										$this->log("$file: ".__('Error: Failed to upload', 'updraftplus'), 'error');
										$errors_so_far++;
										if ($errors_so_far >=3) return false;

									}

								}
							}
						}
						if ($errors_so_far) return false;
						// All chunks are uploaded - now upload the manifest
						
						try {
							$object->manifest = $container."/".$chunk_path."_";
							// Put a zero-length file
							$object->write("", 0, false);
							$object->sync_manifest();
							$this->log("upload: success");
							$updraftplus->uploaded_file($file);
// } catch (InvalidResponseException $e) {
						} catch (Exception $e) {
							$this->log('error - failed to re-assemble chunks ('.$e->getMessage().')');
							$this->log(__('error - failed to re-assemble chunks', 'updraftplus').' ('.$e->getMessage().')', 'error');
							return false;
						}
					}
				}

			} catch (Exception $e) {
				$this->log(__('error - failed to upload file', 'updraftplus').' ('.$e->getMessage().')');
				$this->log(__('error - failed to upload file', 'updraftplus').' ('.$e->getMessage().')', 'error');
				return false;
			}

		}

		return array('cloudfiles_object' => $container_object, 'cloudfiles_orig_path' => $opts['path'], 'cloudfiles_container' => $container);

	}

	public function listfiles($match = 'backup_') {

		$opts = $this->get_options();
		$container = $opts['path'];

		if (empty($opts['user']) || empty($opts['apikey'])) new WP_Error('no_settings', __('No settings were found', 'updraftplus'));

		try {
			$storage = $this->getCF($opts['user'], $opts['apikey'], $opts['authurl'], UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'));
			$container_object = $storage->create_container($container);
		} catch (Exception $e) {
			return new WP_Error('no_access', sprintf(__('%s authentication failed', 'updraftplus'), 'Cloud Files').' ('.$e->getMessage().')');
		}

		$results = array();

		try {
			$objects = $container_object->list_objects(0, null, $match);
			foreach ($objects as $name) {
				$result = array('name' => $name);
				try {
					$object = new UpdraftPlus_CF_Object($container_object, $name, true);
					if (0 == $object->content_length) {
						$result = false;
					} else {
						$result['size'] = $object->content_length;
					}
				} catch (Exception $e) {
					// Catch
				}
				if (is_array($result)) $results[] = $result;
			}
		} catch (Exception $e) {
			return new WP_Error('cf_error', 'Cloud Files error ('.$e->getMessage().')');
		}

		return $results;

	}
	
	/**
	 * Delete a single file from the service using the CloudFiles API
	 *
	 * @param Array $files         - array of file paths to delete
	 * @param Array $cloudfilesarr - CloudFiles container and object details
	 * @param Array $sizeinfo      - unused here
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete($files, $cloudfilesarr = false, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $sizeinfo is unused

		if (is_string($files)) $files =array($files);

		if ($cloudfilesarr) {
			$container_object = $cloudfilesarr['cloudfiles_object'];
			$container = $cloudfilesarr['cloudfiles_container'];
		} else {
			try {
				$opts = $this->get_options();
				$container = $opts['path'];
				$storage = $this->getCF($opts['user'], $opts['apikey'], $opts['authurl'], UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'));
				$container_object = $storage->create_container($container);
			} catch (Exception $e) {
				$this->log('authentication failed ('.$e->getMessage().')');
				$this->log(__('authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
				return 'authentication_fail';
			}
		}

		$ret = true;
		foreach ($files as $file) {

			$fpath = $file;

			$this->log("Delete remote: container=$container, path=$fpath");

			// We need to search for chunks
			$chunk_path = "chunk-do-not-delete-$file";

			try {
				$objects = $container_object->list_objects(0, null, $chunk_path.'_');
				foreach ($objects as $chunk) {
					$this->log('Chunk to delete: '.$chunk);
					$container_object->delete_object($chunk);
					$this->log('Chunk deleted: '.$chunk);
				}
			} catch (Exception $e) {
				$this->log('chunk delete failed: '.$e->getMessage());
			}

			try {
				$container_object->delete_object($fpath);
				$this->log('Deleted: '.$fpath);
			} catch (Exception $e) {
				$this->log('delete failed: '.$e->getMessage());
				$ret = 'file_delete_error';
			}
		}
		return $ret;
	}

	public function download($file) {

		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();

		$opts = $this->get_options();

		try {
			$storage = $this->getCF($opts['user'], $opts['apikey'], $opts['authurl'], UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'));
		} catch (AuthenticationException $e) {
			$this->log('authentication failed ('.$e->getMessage().')');
			$this->log(__('authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		} catch (NoSuchAccountException $s) {
			$this->log('authentication failed ('.$e->getMessage().')');
			$this->log(__('authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		} catch (Exception $e) {
			$this->log('error - failed to create and access the container ('.$e->getMessage().')');
			$this->log(__('error - failed to create and access the container', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		}

		$path = untrailingslashit($opts['path']);

		$container = $path;

		try {
			$container_object = $storage->create_container($container);
		} catch (Exception $e) {
			$this->log('error - failed to create and access the container ('.$e->getMessage().')');
			$this->log(__('error - failed to create and access the container', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		}

		$path = $file;

		$this->log("download: cloudfiles://$container/$path");

		try {
			// The third parameter causes an exception to be thrown if the object does not exist remotely
			$object = new UpdraftPlus_CF_Object($container_object, $path, true);
			
			$fullpath = $updraft_dir.'/'.$file;

			$start_offset = (file_exists($fullpath)) ? filesize($fullpath) : 0;

			// Get file size from remote - see if we've already finished

			$remote_size = $object->content_length;

			if ($start_offset >= $remote_size) {
				$this->log("file is already completely downloaded ($start_offset/$remote_size)");
				return true;
			}

			// Some more remains to download - so let's do it
			if (!$fh = fopen($fullpath, 'a')) {
				$this->log("Error opening local file: $fullpath");
				$this->log("$file: ".__('Error opening local file: Failed to download', 'updraftplus'), 'error');
				return false;
			}

			$headers = array();
			// If resuming, then move to the end of the file
			if ($start_offset) {
				$this->log("local file is already partially downloaded ($start_offset/$remote_size)");
				fseek($fh, $start_offset);
				$headers['Range'] = "bytes=$start_offset-";
			}

			// Now send the request itself
			try {
				$object->stream($fh, $headers);
			} catch (Exception $e) {
				$this->log("Failed to download: $file (".$e->getMessage().")");
				$this->log("$file: ".__('Error downloading remote file: Failed to download', 'updraftplus').' ('.$e->getMessage().")", 'error');
				return false;
			}
			
			// All-in-one-go method:
			// $object->save_to_filename($fullpath);

		} catch (NoSuchObjectException $e) {
			$this->log('error - no such file exists. ('.$e->getMessage().')');
			$this->log(__('Error - no such file exists.', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		} catch (Exception $e) {
			$this->log('error - failed to download the file ('.$e->getMessage().')');
			$this->log(__('Error - failed to download the file', 'updraftplus').' ('.$e->getMessage().')', 'error');
			return false;
		}

		return true;

	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		global $updraftplus_admin;

		$classes = $this->get_css_classes(false);
		
		?>
		<tr class="<?php echo $classes . ' ' . 'cloudfiles_pre_config_container';?>">
			<td colspan="2">
				<img alt="Rackspace Cloud Files" src="<?php echo UPDRAFTPLUS_URL;?>/images/rackspacecloud-logo.png"><br>
			<?php
				// Check requirements.
				global $updraftplus_admin;
				if (!function_exists('mb_substr')) {
					$updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__('Your web server\'s PHP installation does not included a required module (%s). Please contact your web hosting provider\'s support.', 'updraftplus'), 'mbstring').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.", 'updraftplus'), 'Cloud Files', 'mbstring'), 'cloudfiles', false);
				}
				$updraftplus_admin->curl_check('Rackspace Cloud Files', false, 'cloudfiles', false);
			?>

			<?php
				echo '<p>' . __('Get your API key <a href="https://mycloud.rackspace.com/" target="_blank">from your Rackspace Cloud console</a> (<a href="http://www.rackspace.com/knowledge_center/article/rackspace-cloud-essentials-1-generating-your-api-key" target="_blank">read instructions here</a>), then pick a container name to use for storage. This container will be created for you if it does not already exist.', 'updraftplus').' <a href="https://updraftplus.com/faqs/there-appear-to-be-lots-of-extra-files-in-my-rackspace-cloud-files-container/" target="_blank">'.__('Also, you should read this important FAQ.', 'updraftplus').'</a></p>';
			?>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		$classes = $this->get_css_classes();
		$template_str = '
		<tr class="'.$classes.'">
			<th>'.__('US or UK Cloud', 'updraftplus').':</th>
			<td>
				<select data-updraft_settings_test="authurl" '.$this->output_settings_field_name_and_id('authurl', true).'>
					<option {{#ifeq "https://auth.api.rackspacecloud.com" authurl}}selected="selected"{{/ifeq}} value="https://auth.api.rackspacecloud.com">'.__('US (default)', 'updraftplus').'</option>
					<option {{#ifeq "https://lon.auth.api.rackspacecloud.com" authurl}}selected="selected"{{/ifeq}} value="https://lon.auth.api.rackspacecloud.com">'.__('UK', 'updraftplus').'</option>
				</select>
			</td>
		</tr>
		<input type="hidden" data-updraft_settings_test="region" '.$this->output_settings_field_name_and_id('region', true).' value="">';
			
			/*
			// Can put a message here if someone asks why region storage is not available (only available on new SDK)
			<tr class="updraftplusmethod cloudfiles">
				<th><?php _e('Rackspace Storage Region','updraftplus');?>:</th>
				<td>
				</td>
			</tr>
			*/
		$template_str .= '
		<tr class="'.$classes.'">
			<th>'.__('Cloud Files username', 'updraftplus').':</th>
			<td><input data-updraft_settings_test="user" type="text" autocomplete="off" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('user', true).' value="{{user}}" /></td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.__('Cloud Files API Key', 'updraftplus').':</th>
			<td><input data-updraft_settings_test="apikey" type="'.apply_filters('updraftplus_admin_secret_field_type', 'password').'" autocomplete="off" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('apikey', true).' value="{{apikey}}" />
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.apply_filters('updraftplus_cloudfiles_location_description', __('Cloud Files Container', 'updraftplus')).':</th>
			<td><input data-updraft_settings_test="path" type="text" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('path', true).' value="{{path}}" /></td>
		</tr>';
		$template_str .= $this->get_test_button_html(__('Cloud Files', 'updraftplus'));
		return $template_str;
	}

/**
 * Modifies handerbar template options
 *
 * @param array $opts handerbar template options
 * @return Array - Modified handerbar template options
 */
	public function transform_options_for_template($opts) {
		$opts['apikey'] = trim($opts['apikey']);
		$opts['authurl'] = isset($opts['authurl']) ? $opts['authurl'] : '';
		return $opts;
	}
	
	/**
	 * Perform a test of user-supplied credentials, and echo the result
	 *
	 * @param Array $posted_settings - settings to test
	 */
	public function credentials_test($posted_settings) {

		if (empty($posted_settings['apikey'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('API key', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['user'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('Username', 'updraftplus'));
			return;
		}

		$key = $posted_settings['apikey'];
		$user = $posted_settings['user'];
		$path = $posted_settings['path'];
		$authurl = $posted_settings['authurl'];
		$useservercerts = $posted_settings['useservercerts'];
		$disableverify = $posted_settings['disableverify'];

		if (preg_match("#^([^/]+)/(.*)$#", $path, $bmatches)) {
			$container = $bmatches[1];
			$path = $bmatches[2];
		} else {
			$container = $path;
			$path = "";
		}

		if (empty($container)) {
			_e("Failure: No container details were given.", 'updraftplus');
			return;
		}

		define('UPDRAFTPLUS_SSL_DISABLEVERIFY', $disableverify);

		try {
			$storage = $this->getCF($user, $key, $authurl, $useservercerts);
			$container_object = $storage->create_container($container);
		} catch (AuthenticationException $e) {
			echo __('Cloud Files authentication failed', 'updraftplus').' ('.$e->getMessage().')';
			return;
		} catch (NoSuchAccountException $s) {
			echo __('Cloud Files authentication failed', 'updraftplus').' ('.$e->getMessage().')';
			return;
		} catch (Exception $e) {
			echo __('Cloud Files authentication failed', 'updraftplus').' ('.$e->getMessage().')';
			return;
		}

		$try_file = md5(rand()).'.txt';

		try {
			$object = $container_object->create_object($try_file);
			$object->content_type = "text/plain";
			$object->write('UpdraftPlus test file');
		} catch (Exception $e) {
			echo __('Cloud Files error - we accessed the container, but failed to create a file within it', 'updraftplus').' ('.$e->getMessage().')';
			return;
		}

		echo __('Success', 'updraftplus').": ".__('We accessed the container, and were able to create files within it.', 'updraftplus');

		@$container_object->delete_object($try_file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	}
}

// Moved to the bottom to fix a bug in some version or install of PHP which required UpdraftPlus_BackupModule_cloudfiles_oldsdk to be defined earlier in the file (despite the conditionality) - see HS#19911
if (version_compare(PHP_VERSION, '5.3.3', '>=') && (!defined('UPDRAFTPLUS_CLOUDFILES_USEOLDSDK') || UPDRAFTPLUS_CLOUDFILES_USEOLDSDK != true)) {
	include_once(UPDRAFTPLUS_DIR.'/methods/cloudfiles-new.php');
	class UpdraftPlus_BackupModule_cloudfiles extends UpdraftPlus_BackupModule_cloudfiles_opencloudsdk {
	}
} else {
	class UpdraftPlus_BackupModule_cloudfiles extends UpdraftPlus_BackupModule_cloudfiles_oldsdk {
	}
}
