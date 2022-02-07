<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

class UpdraftPlus_BackupModule_remotesend extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	private $default_chunk_size;

	private $remotesend_use_chunk_size;
	
	private $remotesend_chunked_wp_error;
	
	private $try_format_upgrade = false;
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		// 3rd parameter: chunking? 4th: Test button?
		parent::__construct('remotesend', 'Remote send', false, false);
	}
	
	/**
	 * Supplies the list of keys for options to be saved in the backup job.
	 *
	 * @return Array
	 */
	public function get_credentials() {
		return array('updraft_ssl_disableverify', 'updraft_ssl_nossl', 'updraft_ssl_useservercerts');
	}

	/**
	 * Upload a single file
	 *
	 * @param String $file - the basename of the file to upload
	 * @param String $from - the full path of the file
	 *
	 * @return Boolean - success status. Failures can also be thrown as exceptions.
	 */
	public function do_upload($file, $from) {

		global $updraftplus;
		$opts = $this->options;
		
		try {
			$storage = $this->bootstrap();
			if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
			if (!is_object($storage)) throw new Exception("RPC service error");
		} catch (Exception $e) {
			$message = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			$this->log("RPC service error: ".$message);
			$this->log($message, 'error');
			return false;
		}
		
		$filesize = filesize($from);
		$this->remotesend_file_size = $filesize;

		// See what the sending side currently has. This also serves as a ping. For that reason, we don't try/catch - we let them be caught at the next level up.

		$get_remote_size = $this->send_message('get_file_status', $file, 30);
		
		if (is_wp_error($get_remote_size)) {
			throw new Exception($get_remote_size->get_error_message().' (get_file_status: '.$get_remote_size->get_error_code().')');
		}

		if (!is_array($get_remote_size) || empty($get_remote_size['response'])) throw new Exception(__('Unexpected response:', 'updraftplus').' '.serialize($get_remote_size));

		if ('error' == $get_remote_size['response']) {
			$msg = $get_remote_size['data'];
			// Could interpret the codes to get more interesting messages directly to the user
			throw new Exception(__('Error:', 'updraftplus').' '.$msg);
		}

		if (empty($get_remote_size['data']) || !isset($get_remote_size['data']['size']) || 'file_status' != $get_remote_size['response']) throw new Exception(__('Unexpected response:', 'updraftplus').' '.serialize($get_remote_size));

		// Possible statuses: 0=temporary file (or not present), 1=file
		if (empty($get_remote_size['data'])) {
			$remote_size = 0;
			$remote_status = 0;
		} else {
			$remote_size = (int) $get_remote_size['data']['size'];
			$remote_status = $get_remote_size['data']['status'];
		}

		$this->log("$file: existing size: ".$remote_size);
		
		// Perhaps it already exists? (if we didn't get the final confirmation)
		if ($remote_size >= $filesize && $remote_status) {
			$this->log("$file: already uploaded");
			return true;
		}

		// Length = 44 (max = 45)
		$this->remote_sent_defchunk_transient = 'ud_rsenddck_'.md5($opts['name_indicator']);

		if (empty($this->default_chunk_size)) {
		
			// Default is 2MB. After being b64-encoded twice, this is ~ 3.7MB = 113 seconds on 32KB/s uplink
			$default_chunk_size = $updraftplus->jobdata_get('clone_job') ? 4194304 : 2097152;
			
			if (defined('UPDRAFTPLUS_REMOTESEND_DEFAULT_CHUNK_BYTES') && UPDRAFTPLUS_REMOTESEND_DEFAULT_CHUNK_BYTES >= 16384) $default_chunk_size = UPDRAFTPLUS_REMOTESEND_DEFAULT_CHUNK_BYTES;
			
			$this->default_chunk_size = $default_chunk_size;
		
		}
		
		$default_chunk_size = $this->default_chunk_size;

		if (false !== ($saved_default_chunk_size = get_transient($this->remote_sent_defchunk_transient)) && $saved_default_chunk_size > 16384) {
			// Don't go lower than 256KB for the *default*. (The job size can go lower).
			$default_chunk_size = max($saved_default_chunk_size, 262144);
		}

		$this->remotesend_use_chunk_size = $updraftplus->jobdata_get('remotesend_chunksize', $default_chunk_size);

		if (0 == $remote_size && $this->remotesend_use_chunk_size == $this->default_chunk_size && $updraftplus->current_resumption - max($updraftplus->jobdata_get('uploaded_lastreset'), 1) > 1) {
			$new_chunk_size = floor($this->remotesend_use_chunk_size / 2);
			$this->log("No uploading activity has been detected for a while; reducing chunk size in case a timeout was occurring. New chunk size: ".$new_chunk_size);
			$this->remotesend_set_new_chunk_size($new_chunk_size);
		}

		try {
			if (false != ($handle = fopen($from, 'rb'))) {

				$this->remotesend_uploaded_size = $remote_size;
				$ret = $updraftplus->chunked_upload($this, $file, $this->method."://".trailingslashit($opts['url']).$file, $this->description, $this->remotesend_use_chunk_size, $remote_size, true);

				fclose($handle);

				return $ret;
			} else {
				throw new Exception("Failed to open file for reading: $from");
			}
		} catch (Exception $e) {
			$this->log("upload: error (".get_class($e)."): ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
			if (!empty($this->remotesend_chunked_wp_error) && is_wp_error($this->remotesend_chunked_wp_error)) {
				$this->log("Exception data: ".base64_encode(serialize($this->remotesend_chunked_wp_error->get_error_data())));
			}
			return false;
		}
		
		return true;
	}

	/**
	 * Chunked upload
	 *
	 * @param String   $file 		 Specific file to be used in chunked upload
	 * @param Resource $fp 		     File handle
	 * @param Integer  $chunk_index  The index of the chunked data
	 * @param Integer  $upload_size  Size of the upload
	 * @param Integer  $upload_start String the upload starts on
	 * @param Integer  $upload_end   String the upload ends on
	 *
	 * @return Boolean|Integer Result (N.B> (int)1 means the same as true, but additionally indicates "don't log it")
	 */
	public function chunked_upload($file, $fp, $chunk_index, $upload_size, $upload_start, $upload_end) {

		// Condition used to be "$upload_start < $this->remotesend_uploaded_size" - but this assumed that the other side never failed after writing only some bytes to disk
		// $upload_end is the byte offset of the final byte. Therefore, add 1 onto it when comparing with a size.
		if ($upload_end + 1 <= $this->remotesend_uploaded_size) return 1;

		global $updraftplus;

		$chunk = fread($fp, $upload_size);

		if (false === $chunk) {
			$this->log("upload: $file: fread failure ($upload_start)");
			return false;
		}

		$try_again = false;

		$data = array('file' => $file, 'data' => base64_encode($chunk), 'start' => $upload_start);

		if ($upload_end+1 >= $this->remotesend_file_size) {
			$data['last_chunk'] = true;
			if ('' != ($label = $updraftplus->jobdata_get('label'))) $data['label'] = $label;
		}

		// ~ 3.7MB of data typically - timeout allows for 15.9KB/s
		try {
			$put_chunk = $this->send_message('send_chunk', $data, 240);
		} catch (Exception $e) {
			$try_again = true;
		}

		if ($try_again || is_wp_error($put_chunk)) {
			// 413 = Request entity too large
			// Don't go lower than 64KB chunks (i.e. 128KB/2)
			// Note that mod_security can be configured to 'helpfully' decides to replace HTTP error codes + messages with a simple serving up of the site home page, which means that we need to also guess about other reasons this condition may have occurred other than detecting via the direct 413 code. Of course, our search for wp-includes|wp-content|WordPress|/themes/ would be thwarted by someone who tries to hide their WP. The /themes/ is pretty hard to hide, as the theme directory is always <wp-content-dir>/themes - even if you moved your wp-content. The point though is just a 'best effort' - this doesn't have to be infallible.
			if (is_wp_error($put_chunk)) {
			
				$error_data = $put_chunk->get_error_data();
			
				$is_413 = ('unexpected_http_code' == $put_chunk->get_error_code() && (
						413 == $error_data
						|| (is_array($error_data) && !empty($error_data['response']['code']) && 413 == $error_data['response']['code'])
					)
				);
				
				$is_timeout = ('http_request_failed' == $put_chunk->get_error_code() && false !== strpos($put_chunk->get_error_message(), 'timed out'));
			
				if ($this->remotesend_use_chunk_size >= 131072 && ($is_413 || $is_timeout || ('response_not_understood' == $put_chunk->get_error_code() && (false !== strpos($error_data, 'wp-includes') || false !== strpos($error_data, 'wp-content') || false !== strpos($error_data, 'WordPress') || false !== strpos($put_chunk->get_error_data(), '/themes/'))))) {
					if (1 == $chunk_index) {
						$new_chunk_size = floor($this->remotesend_use_chunk_size / 2);
						$this->remotesend_set_new_chunk_size($new_chunk_size);
						$log_msg = "Returned WP_Error: code=".$put_chunk->get_error_code();
						if ('unexpected_http_code' == $put_chunk->get_error_code()) $log_msg .= ' ('.$error_data.')';
						$log_msg .= " - reducing chunk size to: ".$new_chunk_size;
						$this->log($log_msg);
						return new WP_Error('reduce_chunk_size', 'HTTP 413 or possibly equivalent condition on first chunk - should reduce chunk size', $new_chunk_size);
					} elseif ($this->remotesend_use_chunk_size >= 131072 && $is_413) {
						// In this limited case, where we got a 413 but the chunk is not number 1, our algorithm/architecture doesn't allow us to just resume immediately with a new chunk size. However, we can just have UD reduce the chunk size on its next resumption.
						$new_chunk_size = floor($this->remotesend_use_chunk_size / 2);
						$this->remotesend_set_new_chunk_size($new_chunk_size);
						$log_msg = "Returned WP_Error: code=".$put_chunk->get_error_code().", message=".$put_chunk->get_error_message();
						$log_msg .= " - reducing chunk size to: ".$new_chunk_size." and then scheduling resumption/aborting";
						$this->log($log_msg);
						UpdraftPlus_Job_Scheduler::reschedule(50);
						UpdraftPlus_Job_Scheduler::record_still_alive();
						die;
						
					}
				}
			}
			$put_chunk = $this->send_message('send_chunk', $data, 240);
		}

		if (is_wp_error($put_chunk)) {
			// The exception handler is within this class. So we can store the data.
			$this->remotesend_chunked_wp_error = $put_chunk;
			throw new Exception($put_chunk->get_error_message().' ('.$put_chunk->get_error_code().')');
		}

		if (!is_array($put_chunk) || empty($put_chunk['response'])) throw new Exception(__('Unexpected response:', 'updraftplus').' '.serialize($put_chunk));

		if ('error' == $put_chunk['response']) {
			$msg = $put_chunk['data'];
			// Could interpret the codes to get more interesting messages directly to the user
			// The textual prefixes here were added after 1.12.5 - hence optional when parsing
			if (preg_match('/^invalid_start_too_big:(start=)?(\d+),(existing_size=)?(\d+)/', $msg, $matches)) {
				$existing_size = $matches[2];
				if ($existing_size < $this->remotesend_uploaded_size) {
					// The file on the remote system seems to have shrunk. Could be some load-balancing system with a distributed filesystem that is only eventually consistent.
					return new WP_Error('try_again', 'File on remote system is smaller than expected - perhaps an eventually-consistent filesystem (wait and retry)');
				}
			}
			throw new Exception(__('Error:', 'updraftplus').' '.$msg);
		}

		if ('file_status' != $put_chunk['response']) throw new Exception(__('Unexpected response:', 'updraftplus').' '.serialize($put_chunk));

		// Possible statuses: 0=temporary file (or not present), 1=file
		if (empty($put_chunk['data']) || !is_array($put_chunk['data'])) {
			$this->log("Unexpected response when putting chunk $chunk_index: ".serialize($put_chunk));
			return false;
		} else {
			$remote_size = (int) $put_chunk['data']['size'];
			$this->remotesend_uploaded_size = $remote_size;
		}

		return true;

	}

	/**
	 * This function will send a message to the remote site to inform it that the backup has finished sending, on success will update the jobdata key upload_completed and return true else false
	 *
	 * @return Boolean - returns true on success or false on error, all errors are logged to the backup log
	 */
	public function upload_completed() {
		global $updraftplus;

		$service = $updraftplus->jobdata_get('service');
		$remote_sent = (!empty($service) && ((is_array($service) && in_array('remotesend', $service)) || 'remotesend' === $service));

		if (!$remote_sent) return;

		// ensure options have been loaded
		$this->options = $this->get_options();

		try {
			$storage = $this->bootstrap();
			if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
			if (!is_object($storage)) throw new Exception("RPC service error");
		} catch (Exception $e) {
			$message = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			$this->log("RPC service error: ".$message);
			$this->log($message, 'error');
			return false;
		}
		
		if (is_wp_error($storage)) return $updraftplus->log_wp_error($storage, false, true);

		for ($i = 0; $i < 3; $i++) {

			$success = false;

			$response = $this->send_message('upload_complete', array('job_id' => $updraftplus->nonce), 30);

			if (is_wp_error($response)) {
				$message = $response->get_error_message().' (upload_complete: '.$response->get_error_code().')';
				$this->log("RPC service error: ".$message);
				$this->log($message, 'error');
			} elseif (!is_array($response) || empty($response['response'])) {
				$this->log("RPC service error: ".serialize($response));
				$this->log(serialize($response), 'error');
			} elseif ('error' == $response['response']) {
				// Could interpret the codes to get more interesting messages directly to the user
				$msg = $response['data'];
				$this->log("RPC service error: ".$msg);
				$this->log($msg, 'error');
			} elseif ('file_status' == $response['response']) {
				$success = true;
				break;
			}

			sleep(5);
		}

		return $success;
	}

	/**
	 * Change the chunk size
	 *
	 * @param Integer $new_chunk_size - in bytes
	 */
	private function remotesend_set_new_chunk_size($new_chunk_size) {
		global $updraftplus;
		$this->remotesend_use_chunk_size = $new_chunk_size;
		$updraftplus->jobdata_set('remotesend_chunksize', $new_chunk_size);
		// Save, so that we don't have to cycle through the illegitimate/larger chunk sizes each time. Set the transient expiry to 120 days, in case they change hosting/configuration - so that we're not stuck on the lower size forever.
		set_transient($this->remote_sent_defchunk_transient, $new_chunk_size, 86400*120);
	}

	/**
	 * Send a message to the remote site
	 *
	 * @param String	 $message - the message identifier
	 * @param Array|Null $data	  - the data to send with the message
	 * @param Integer	 $timeout - timeout in waiting for a response
	 *
	 * @return Array|WP_Error - results, or an error
	 */
	private function send_message($message, $data = null, $timeout = 30) {
		$storage = $this->get_storage();
		
		if (is_array($this->try_format_upgrade) && is_array($data)) {
			$data['sender_public'] = $this->try_format_upgrade['local_public'];
		}
		
		$response = $storage->send_message($message, $data, $timeout);
		
		if (is_array($response) && !empty($response['data']) && is_array($response['data'])) {
		
			if (!empty($response['data']['php_events']) && !empty($response['data']['previous_data'])) {
				foreach ($response['data']['php_events'] as $logline) {
					$this->log("From remote side: ".$logline);
				}
				$response['data'] = $response['data']['previous_data'];
			}
			
			if (is_array($response) && is_array($response['data']) && !empty($response['data']['got_public'])) {
				$name_indicator = $this->try_format_upgrade['name_indicator'];

				$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
				
				foreach ($remotesites as $key => $site) {
					if (!is_array($site) || empty($site['name_indicator']) || $site['name_indicator'] != $name_indicator) continue;
					// This means 'format 2'
					$this->try_format_upgrade = true;
					$remotesites[$key]['remote_got_public'] = 1;
					// If this DB save fails, they'll have to recreate the key
					UpdraftPlus_Options::update_updraft_option('updraft_remotesites', $remotesites);
					// Now we need to get a fresh storage object, because the remote end will no longer accept messages with format=1
					$this->set_storage(null);
					$this->do_bootstrap(null);
					break;
				}
			}
		}
		
		return $response;
	}

	public function do_bootstrap($opts, $connect = true) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $connect unused
	
		global $updraftplus;
	
		if (!class_exists('UpdraftPlus_Remote_Communications')) include_once(apply_filters('updraftplus_class_udrpc_path', UPDRAFTPLUS_DIR.'/includes/class-udrpc.php', $updraftplus->version));

		$opts = $this->get_opts();

		try {
			$ud_rpc = new UpdraftPlus_Remote_Communications($opts['name_indicator']);
			if (!empty($opts['format_support']) && 2 == $opts['format_support'] && !empty($opts['local_private']) && !empty($opts['local_public']) && !empty($opts['remote_got_public'])) {
				$ud_rpc->set_message_format(2);
				$ud_rpc->set_key_remote($opts['key']);
				$ud_rpc->set_key_local($opts['local_private']);
			} else {
				// Enforce the legacy communications protocol (which is only suitable for when only one side only sends, and the other only receives - which is what we happen to do)
				$ud_rpc->set_message_format(1);
				$ud_rpc->set_key_local($opts['key']);
				if (!empty($opts['format_support']) && 2 == $opts['format_support'] && !empty($opts['local_public']) && !empty($opts['local_private'])) {
					$this->try_format_upgrade = array('name_indicator' => $opts['name_indicator'], 'local_public' => $opts['local_public']);
				}
			}
			$ud_rpc->set_destination_url($opts['url']);
			$ud_rpc->activate_replay_protection();
		} catch (Exception $e) {
			return new WP_Error('rpc_failure', "Commmunications failure: ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
		}

		do_action('updraftplus_remotesend_udrpc_object_obtained', $ud_rpc, $opts);
		
		$this->set_storage($ud_rpc);
		
		return $ud_rpc;
	}

	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['url']) && !empty($opts['name_indicator']) && !empty($opts['key'])) return true;
		return false;
	}

	public function get_opts() {
		global $updraftplus;
		$opts = $updraftplus->jobdata_get('remotesend_info');
		$opts = $this->clone_remotesend_options($opts);
		if (true === $this->try_format_upgrade && is_array($opts)) $opts['remote_got_public'] = 1;
		return is_array($opts) ? $opts : array();
	}

	/**
	 * This function will check the options we have for the remote send and if it's a clone job and there are missing settings it will call the mothership to get this information.
	 *
	 * @param Array $opts - an array of remote send options
	 *
	 * @return Array - an array of options
	 */
	public function clone_remotesend_options($opts) {
	
		// Don't call self::log() - this then requests options (to get the label), causing an infinite loop.
	
		global $updraftplus, $updraftplus_admin;
		if (empty($updraftplus_admin)) include_once(UPDRAFTPLUS_DIR.'/admin.php');
		
		$clone_job = $updraftplus->jobdata_get('clone_job');
		
		// check this is a clone job before we proceed
		if (empty($clone_job)) return $opts;

		// check that we don't already have the needed information
		if (is_array($opts) && !empty($opts['url']) && !empty($opts['name_indicator']) && !empty($opts['key'])) return $opts;

		$updraftplus->jobdata_set('jobstatus', 'clonepolling');
		$clone_id = $updraftplus->jobdata_get('clone_id');
		$clone_url = $updraftplus->jobdata_get('clone_url');
		$clone_key = $updraftplus->jobdata_get('clone_key');
		$secret_token = $updraftplus->jobdata_get('secret_token');
			
		if (empty($clone_id) && empty($secret_token)) return $opts;
		
		$params = array('clone_id' => $clone_id, 'secret_token' => $secret_token);
		$response = $updraftplus->get_updraftplus_clone()->clone_info_poll($params);

		if (!isset($response['status']) || 'success' != $response['status']) {
			$updraftplus->log("UpdraftClone migration information poll failed with code: " . $response['code']);
			return $opts;
		}

		if (!isset($response['data']) || !isset($response['data']['url']) || !isset($response['data']['key'])) {
			$updraftplus->log("UpdraftClone migration information poll unexpected return information with code:" . $response['code']);
			return $opts;
		}

		$clone_url = $response['data']['url'];
		$clone_key = json_decode($response['data']['key'], true);

		if (empty($clone_url) || empty($clone_key)) {
			$updraftplus->log("UpdraftClone migration information not found (probably still provisioning): will poll again in 60");
			UpdraftPlus_Job_Scheduler::reschedule(60);
			UpdraftPlus_Job_Scheduler::record_still_alive();
			die;
		}

		// Store the information
		$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
		if (!is_array($remotesites)) $remotesites = array();

		foreach ($remotesites as $k => $rsite) {
			if (!is_array($rsite)) continue;
			if ($rsite['url'] == $clone_key['url']) unset($remotesites[$k]);
		}

		$remotesites[] = $clone_key;
		UpdraftPlus_Options::update_updraft_option('updraft_remotesites', $remotesites);

		$updraftplus->jobdata_set_multi('clone_url', $clone_url, 'clone_key', $clone_key, 'remotesend_info', $clone_key, 'jobstatus', 'clouduploading');

		return $clone_key;
	}

	// do_listfiles(), do_download(), do_delete() : the absence of any method here means that the parent will correctly throw an error
}
