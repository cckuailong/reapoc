<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

class UpdraftPlus_BackupModule_openstack_base extends UpdraftPlus_BackupModule {

	protected $chunk_size;

	protected $client;
	
	protected $method;

	protected $desc;

	protected $long_desc;

	protected $img_url;

	public function __construct($method, $desc, $long_desc = null, $img_url = '') {
		$this->method = $method;
		$this->desc = $desc;
		$this->long_desc = (is_string($long_desc)) ? $long_desc : $desc;
		$this->img_url = $img_url;
	}

	public function backup($backup_array) {

		global $updraftplus;

		$default_chunk_size = (defined('UPDRAFTPLUS_UPLOAD_CHUNKSIZE') && UPDRAFTPLUS_UPLOAD_CHUNKSIZE > 0) ? max(UPDRAFTPLUS_UPLOAD_CHUNKSIZE, 1048576) : 5242880;

		$this->chunk_size = $updraftplus->jobdata_get('openstack_chunk_size', $default_chunk_size);
		
		$opts = $this->get_options();

		$this->container = $opts['path'];

		try {
			$storage = $this->get_openstack_service($opts, UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'), UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'));
		} catch (AuthenticationError $e) {
			$updraftplus->log($this->desc.' authentication failed ('.$e->getMessage().')');
			$updraftplus->log(sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
			return false;
		} catch (Exception $e) {
			$updraftplus->log($this->desc.' error - failed to access the container ('.$e->getMessage().') (line: '.$e->getLine().', file: '.$e->getFile().')');
			$updraftplus->log(sprintf(__('%s error - failed to access the container', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
			return false;
		}
		// Get the container
		try {
			$this->container_object = $storage->getContainer($this->container);
		} catch (Exception $e) {
			$updraftplus->log('Could not access '.$this->desc.' container ('.get_class($e).', '.$e->getMessage().') (line: '.$e->getLine().', file: '.$e->getFile().')');
			$updraftplus->log(sprintf(__('Could not access %s container', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')', 'error');
			return false;
		}

		foreach ($backup_array as $file) {
		
			$file_key = 'status_'.md5($file);
			$file_status = $this->jobdata_get($file_key, null, 'openstack_'.$file_key);
			if (is_array($file_status) && !empty($file_status['chunks']) && !empty($file_status['chunks'][1]['size'])) $this->chunk_size = $file_status['chunks'][1]['size'];
		
			// First, see the object's existing size (if any)
			$uploaded_size = $this->get_remote_size($file);

			try {
				if (1 === $updraftplus->chunked_upload($this, $file, $this->method."://".$this->container."/$file", $this->desc, $this->chunk_size, $uploaded_size)) {
					try {
						if (false !== ($data = fopen($updraftplus->backups_dir_location().'/'.$file, 'r+'))) {
							$this->container_object->uploadObject($file, $data);
							$updraftplus->log($this->desc." regular upload: success");
							$updraftplus->uploaded_file($file);
						} else {
							throw new Exception('uploadObject failed: fopen failed');
						}
					} catch (Exception $e) {
						$this->log("regular upload: failed ($file) (".$e->getMessage().")");
						$this->log("$file: ".sprintf(__('Error: Failed to upload', 'updraftplus')), 'error');
					}
				}
			} catch (Exception $e) {
				$updraftplus->log($this->desc.' error - failed to upload file'.' ('.$e->getMessage().') (line: '.$e->getLine().', file: '.$e->getFile().')');
				$updraftplus->log(sprintf(__('%s error - failed to upload file', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
				return false;
			}
		}

		return array('object' => $this->container_object, 'orig_path' => $opts['path'], 'container' => $this->container);

	}

	private function get_remote_size($file) {
		try {
			$response = $this->container_object->getClient()->head($this->container_object->getUrl($file))->send();
			$response_object = $this->container_object->dataObject()->populateFromResponse($response)->setName($file);
			return $response_object->getContentLength();
		} catch (Exception $e) {
			// Allow caller to distinguish between zero-sized and not-found
			return false;
		}
	}

	/**
	 * This function lists the files found in the configured storage location
	 *
	 * @param  String $match a substring to require (tested via strpos() !== false)
	 *
	 * @return Array - each file is represented by an array with entries 'name' and (optional) 'size'
	 */
	public function listfiles($match = 'backup_') {
		$opts = $this->get_options();
		$container = $opts['path'];

		if (empty($opts['user']) || (empty($opts['apikey']) && empty($opts['password']))) return new WP_Error('no_settings', __('No settings were found', 'updraftplus'));

		try {
			$storage = $this->get_openstack_service($opts, UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'), UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'));
		} catch (Exception $e) {
			return new WP_Error('no_access', sprintf(__('%s error - failed to access the container', 'updraftplus'), $this->desc).' ('.$e->getMessage().')');
		}

		// Get the container
		try {
			$this->container_object = $storage->getContainer($container);
		} catch (Exception $e) {
			return new WP_Error('no_access', sprintf(__('%s error - failed to access the container', 'updraftplus'), $this->desc).' ('.$e->getMessage().')');
		}

		$results = array();
		$marker = '';
		$page_size = 1000;
		try {
			// http://php-opencloud.readthedocs.io/en/latest/services/object-store/objects.html#list-objects-in-a-container
			while (null !== $marker) {

				$params = array(
					'prefix' => $match,
					'limit' => $page_size,
					'marker' => $marker
				);
				
				$objects = $this->container_object->objectList($params);
				
				$total = $objects->count();
				
				if (0 == $total) break;
				
				$index = 0;
				
				while (false !== ($file = $objects->offsetGet($index)) && !empty($file)) {
					$index++;
					try {
						if ((is_object($file) && !empty($file->name))) {
							$result = array('name' => $file->name);
							// Rackspace returns the size of a manifested file properly; other OpenStack implementations may not
							if (!empty($file->bytes)) {
								$result['size'] = $file->bytes;
							} else {
								$size = $this->get_remote_size($file->name);
								if (false !== $size && $size > 0) $result['size'] = $size;
							}
							$results[] = $result;
						}
					} catch (Exception $e) {
						// Catch
					}
					$marker = (!empty($file->name) && $total >= $page_size) ? $file->name : null;
				}
				
			}
		} catch (Exception $e) {
			// Catch
		}

		return $results;
	}

	/**
	 * Called when all chunks have been uploaded, to allow any required finishing actions to be carried out
	 *
	 * @param String $file - the basename of the file being uploaded
	 *
	 * @return Boolean - success or failure state of any finishing actions
	 */
	public function chunked_upload_finish($file) {

		$chunk_path = 'chunk-do-not-delete-'.$file;
		try {

			$headers = array(
				'Content-Length'    => 0,
				'X-Object-Manifest' => sprintf('%s/%s', $this->container, $chunk_path.'_')
			);
			
			$url = $this->container_object->getUrl($file);
			$this->container_object->getClient()->put($url, $headers)->send();
			return true;

		} catch (Exception $e) {
			global $updraftplus;
			$updraftplus->log("Error when sending manifest (".get_class($e)."): ".$e->getMessage());
			return false;
		}
	}

	/**
	 * N.B. Since we use varying-size chunks, we must be careful as to what we do with $chunk_index
	 *
	 * @param  String	$file 			 Full path for the file being uploaded
	 * @param  Resource $fp 			 File handle to read upload data from
	 * @param  Integer	$chunk_index 	 Index of chunked upload
	 * @param  Integer	$upload_size 	 Size of the upload, in bytes
	 * @param  Integer	$upload_start    How many bytes into the file the upload process has got
	 * @param  Integer	$upload_end 	 How many bytes into the file we will be after this chunk is uploaded
	 * @param  Integer	$total_file_size Total file size
	 *
	 * @return Boolean
	 */
	public function chunked_upload($file, $fp, $chunk_index, $upload_size, $upload_start, $upload_end, $total_file_size) {

		global $updraftplus;

		$file_key = 'status_'.md5($file);
		$file_status = $this->jobdata_get($file_key, null, 'openstack_'.$file_key);
		
		$next_chunk_size = $upload_size;
		
		$bytes_already_uploaded = 0;
		
		$last_uploaded_chunk_index = 0;
		
		// Once a chunk is uploaded, its status is set, allowing the sequence to be reconstructed
		if (is_array($file_status) && isset($file_status['chunks']) && !empty($file_status['chunks'])) {
			foreach ($file_status['chunks'] as $c_id => $c_status) {
				if ($c_id > $last_uploaded_chunk_index) $last_uploaded_chunk_index = $c_id;
				if ($chunk_index + 1 == $c_id) {
					$next_chunk_size = $c_status['size'];
				}
				$bytes_already_uploaded += $c_status['size'];
			}
		} else {
			$file_status = array('chunks' => array());
		}
		
		$this->jobdata_set($file_key, $file_status);
		
		if ($upload_start < $bytes_already_uploaded) {
			if ($next_chunk_size != $upload_size) {
				$response = new stdClass;
				$response->new_chunk_size = $upload_size;
				$response->log = false;
				return $response;
			} else {
				return 1;
			}
		}
		
		// Shouldn't be able to happen
		if ($chunk_index <= $last_uploaded_chunk_index) {
			$updraftplus->log($this->desc.": Chunk sequence error; chunk_index=$chunk_index, last_uploaded_chunk_index=$last_uploaded_chunk_index, upload_start=$upload_start, upload_end=$upload_end, file_status=".json_encode($file_status));
		}
		
		// Used to use $chunk_index here, before switching to variable chunk sizes
		$upload_remotepath = 'chunk-do-not-delete-'.$file.'_'.sprintf("%016d", $chunk_index);

		$remote_size = $this->get_remote_size($upload_remotepath);

		// Without this, some versions of Curl add Expect: 100-continue, which results in Curl then giving this back: curl error: 55) select/poll returned error
		// Didn't make the difference - instead we just check below for actual success even when Curl reports an error
		// $chunk_object->headers = array('Expect' => '');

		if ($remote_size >= $upload_size) {
			$updraftplus->log($this->desc.": Chunk ($upload_start - $upload_end, $chunk_index): already uploaded");
		} else {
			$updraftplus->log($this->desc.": Chunk ($upload_start - $upload_end, $chunk_index): begin upload");
			// Upload the chunk
			try {
				$data = fread($fp, $upload_size);
				$time_start = microtime(true);
				$this->container_object->uploadObject($upload_remotepath, $data);
				$time_now = microtime(true);
				$time_taken = $time_now - $time_start;
				if ($next_chunk_size < 52428800 && $total_file_size > 0 && $upload_end + 1 < $total_file_size) {
					$job_run_time = $time_now - $updraftplus->job_time_ms;
					if ($time_taken < 10) {
						$upload_rate = $upload_size / max($time_taken, 0.0001);
						$upload_secs = min(floor($job_run_time), 10);
						if ($job_run_time < 15) $upload_secs = max(6, $job_run_time*0.6);
						
						// In megabytes
						$memory_limit_mb = $updraftplus->memory_check_current();
						$bytes_used = memory_get_usage();
						$bytes_free = $memory_limit_mb * 1048576 - $bytes_used;
						
						$new_chunk = max(min($upload_secs * $upload_rate * 0.9, 52428800, $bytes_free), 5242880);
						$new_chunk = $new_chunk - ($new_chunk % 5242880);
						$next_chunk_size = (int) $new_chunk;
						$updraftplus->jobdata_set('openstack_chunk_size', $next_chunk_size);
					}
				}
				
			} catch (Exception $e) {
				$updraftplus->log($this->desc." chunk upload: error: ($file / $chunk_index) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
				// Experience shows that Curl sometimes returns a select/poll error (curl error 55) even when everything succeeded. Google seems to indicate that this is a known bug.
				
				$remote_size = $this->get_remote_size($upload_remotepath);

				if ($remote_size >= $upload_size) {
					$updraftplus->log("$file: Chunk now exists; ignoring error (presuming it was an apparently known curl bug)");
				} else {
					$updraftplus->log("$file: ".sprintf(__('%s Error: Failed to upload', 'updraftplus'), $this->desc), 'error');
					return false;
				}
			}
		}
		
		$file_status['chunks'][$chunk_index]['size'] = $upload_size;

		$this->jobdata_set($file_key, $file_status);
		
		if ($next_chunk_size != $upload_size) {
			$response = new stdClass;
			$response->new_chunk_size = $next_chunk_size;
			$response->log = true;
			return $response;
		}
		
		return true;
	}
	
	/**
	 * Delete a single file from the service using OpenStack API
	 *
	 * @param Array|String $files    - array of file names to delete
	 * @param Array        $data     - service object and container details
	 * @param Array        $sizeinfo - unused here
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete($files, $data = false, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $sizeinfo unused

		global $updraftplus;
		if (is_string($files)) $files = array($files);

		if (is_array($data)) {
			$container_object = $data['object'];
			$container = $data['container'];
		} else {
			$opts = $this->get_options();
			$container = $opts['path'];
			try {
				$storage = $this->get_openstack_service($opts, UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'), UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'));
			} catch (AuthenticationError $e) {
				$updraftplus->log($this->desc.' authentication failed ('.$e->getMessage().')');
				$updraftplus->log(sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
				return 'authentication_fail';
			} catch (Exception $e) {
				$updraftplus->log($this->desc.' error - failed to access the container ('.$e->getMessage().')');
				$updraftplus->log(sprintf(__('%s error - failed to access the container', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
				return 'service_unavailable';
			}
			// Get the container
			try {
				$container_object = $storage->getContainer($container);
			} catch (Exception $e) {
				$updraftplus->log('Could not access '.$this->desc.' container ('.get_class($e).', '.$e->getMessage().')');
				$updraftplus->log(sprintf(__('Could not access %s container', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')', 'error');
				return 'container_access_error';
			}

		}

		$ret = true;
		foreach ($files as $file) {

			$updraftplus->log($this->desc.": Delete remote: container=$container, path=$file");

			// We need to search for chunks
			$chunk_path = "chunk-do-not-delete-".$file;

			try {
				$objects = $container_object->objectList(array('prefix' => $chunk_path));
				$index = 0;
				while (false !== ($chunk = $objects->offsetGet($index)) && !empty($chunk)) {
					try {
						$name = $chunk->name;
						$container_object->dataObject()->setName($name)->delete();
						$updraftplus->log($this->desc.': Chunk deleted: '.$name);
					} catch (Exception $e) {
						$updraftplus->log($this->desc." chunk delete failed: $name: ".$e->getMessage());
					}
					$index++;
				}
			} catch (Exception $e) {
				$updraftplus->log($this->desc.' chunk delete failed: '.$e->getMessage());
			}

			// Finally, delete the object itself
			try {
				$container_object->dataObject()->setName($file)->delete();
				$updraftplus->log($this->desc.': Deleted: '.$file);
			} catch (Exception $e) {
				$updraftplus->log($this->desc.' delete failed: '.$e->getMessage());
				$ret = 'file_delete_error';
			}
		}
		return $ret;
	}

	public function download($file) {

		global $updraftplus;

		$opts = $this->get_options();

		try {
			$storage = $this->get_openstack_service($opts, UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'), UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'));
		} catch (AuthenticationError $e) {
			$updraftplus->log($this->desc.' authentication failed ('.$e->getMessage().')');
			$updraftplus->log(sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
			return false;
		} catch (Exception $e) {
			$updraftplus->log($this->desc.' error - failed to access the container ('.$e->getMessage().')');
			$updraftplus->log(sprintf(__('%s error - failed to access the container', 'updraftplus'), $this->desc).' ('.$e->getMessage().')', 'error');
			return false;
		}

		$container = untrailingslashit($opts['path']);
		$updraftplus->log($this->desc." download: ".$this->method."://$container/$file");

		// Get the container
		try {
			$this->container_object = $storage->getContainer($container);
		} catch (Exception $e) {
			$updraftplus->log('Could not access '.$this->desc.' container ('.get_class($e).', '.$e->getMessage().')');
			$updraftplus->log(sprintf(__('Could not access %s container', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')', 'error');
			return false;
		}

		// Get information about the object within the container
		$remote_size = $this->get_remote_size($file);
		if (false === $remote_size) {
			$updraftplus->log('Could not access '.$this->desc.' object');
			$updraftplus->log(sprintf(__('The %s object was not found', 'updraftplus'), $this->desc), 'error');
			return false;
		}

		return (!is_bool($remote_size)) ? $updraftplus->chunked_download($file, $this, $remote_size, true, $this->container_object) : false;

	}

	public function chunked_download($file, $headers, $container_object) {
		try {
			$dl = $container_object->getObject($file, $headers);
		} catch (Exception $e) {
			global $updraftplus;
			$updraftplus->log("$file: Failed to download (".$e->getMessage().")");
			$updraftplus->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error downloading remote file: Failed to download', 'updraftplus').' ('.$e->getMessage().")", 'error');
			return false;
		}
		return $dl->getContent();
	}

	public function credentials_test_go($opts, $path, $useservercerts, $disableverify) {

		if (preg_match("#^([^/]+)/(.*)$#", $path, $bmatches)) {
			$container = $bmatches[1];
			$path = $bmatches[2];
		} else {
			$container = $path;
			$path = '';
		}

		if (empty($container)) {
			_e('Failure: No container details were given.', 'updraftplus');
			return;
		}

		try {
			$storage = $this->get_openstack_service($opts, $useservercerts, $disableverify);
		// @codingStandardsIgnoreLine
		} catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
			$response = $e->getResponse();
			$code = $response->getStatusCode();
			$reason = $response->getReasonPhrase();
			if (401 == $code && 'Unauthorized' == $reason) {
				echo __('Authorisation failed (check your credentials)', 'updraftplus');
			} else {
				echo __('Authorisation failed (check your credentials)', 'updraftplus')." ($code:$reason)";
			}
			return;
		} catch (AuthenticationError $e) {
			echo sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.$e->getMessage().')';
			return;
		} catch (Exception $e) {
			echo sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')';
			return;
		}

		try {
			$container_object = $storage->getContainer($container);
		// @codingStandardsIgnoreLine
		} catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
			$response = $e->getResponse();
			$code = $response->getStatusCode();
			$reason = $response->getReasonPhrase();
			if (404 == $code) {
				$container_object = $storage->createContainer($container);
			} else {
				echo __('Authorisation failed (check your credentials)', 'updraftplus')." ($code:$reason)";
				return;
			}
		} catch (Exception $e) {
			echo sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')';
			return;
		}

		if (!is_a($container_object, 'OpenCloud\ObjectStore\Resource\Container') && !is_a($container_object, 'Container')) {
			echo sprintf(__('%s authentication failed', 'updraftplus'), $this->desc).' ('.get_class($container_object).')';
			return;
		}

		$try_file = md5(rand()).'.txt';

		try {
			$object = $container_object->uploadObject($try_file, 'UpdraftPlus test file', array('content-type' => 'text/plain'));
		} catch (Exception $e) {
			echo sprintf(__('%s error - we accessed the container, but failed to create a file within it', 'updraftplus'), $this->desc).' ('.get_class($e).', '.$e->getMessage().')';
			if (!empty($this->region)) echo ' '.sprintf(__('Region: %s', 'updraftplus'), $this->region);
			return;
		}

		echo __('Success', 'updraftplus').": ".__('We accessed the container, and were able to create files within it.', 'updraftplus');
		if (!empty($this->region)) echo ' '.sprintf(__('Region: %s', 'updraftplus'), $this->region);

		try {
			if (!empty($object)) {
				// One OpenStack server we tested on did not delete unless we slept... some kind of race condition at their end
				sleep(1);
				$object->delete();
			}
		} catch (Exception $e) {
			// Catch
		}

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
		<tr class="<?php echo $classes . ' ' . $this->method . '_pre_config_container';?>">
			<td colspan="2">
				<?php
					if (!empty($this->img_url)) {
					?>
						<img alt="<?php echo $this->long_desc; ?>" src="<?php echo UPDRAFTPLUS_URL.$this->img_url; ?>">
					<?php
					}
					?>
				<br>
			<?php
			// Check requirements.
			global $updraftplus_admin;
			if (!function_exists('mb_substr')) {
				$updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__('Your web server\'s PHP installation does not included a required module (%s). Please contact your web hosting provider\'s support.', 'updraftplus'), 'mbstring').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.", 'updraftplus'), $this->desc, 'mbstring'), $this->method);
			}
			$updraftplus_admin->curl_check($this->long_desc, false, $this->method);
			echo '<br>';
			$this->get_pre_configuration_middlesection_template();
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
		ob_start();
		$template_str = ob_get_clean();
		$template_str .= $this->get_configuration_middlesection_template();
		$template_str .= $this->get_test_button_html($this->desc);
		return $template_str;
	}
}
