<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

abstract class UpdraftPlus_RemoteSend {

	protected $receivers = array();

	protected $php_events = array();

	private $job_id;
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action('updraft_migrate_newdestination', array($this, 'updraft_migrate_newdestination'));
		add_action('updraft_remote_ping_test', array($this, 'updraft_remote_ping_test'));
		add_action('updraft_migrate_key_create', array($this, 'updraft_migrate_key_create'));
		add_filter('updraft_migrate_key_create_return', array($this, 'updraft_migrate_key_create_return'), 10, 2);
		add_action('updraft_migrate_key_delete', array($this, 'updraft_migrate_key_delete'));
		add_action('updraft_migrate_delete_existingsites', array($this, 'updraft_migrate_delete_existingsites'));
		add_filter('updraftplus_initial_jobdata', array($this, 'updraftplus_initial_jobdata'), 10, 3);
		add_filter('updraft_printjob_beforewarnings', array($this, 'updraft_printjob_beforewarnings'), 10, 2);
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
	}

	/**
	 * Runs upon the WP action plugins_loaded; sets up UDRPC listeners for site-to-site migration
	 */
	public function plugins_loaded() {

		global $updraftplus;
		
		// Prevent fatal errors if UD was not loaded (e.g. some CLI method)
		if (!is_a($updraftplus, 'UpdraftPlus')) return;

		// Create a receiver for each key
		if (!class_exists('UpdraftPlus_Options')) {
			error_log("UpdraftPlus_Options class not found: is UpdraftPlus properly installed?");
			return;
		}
		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (is_array($our_keys) && !empty($our_keys)) {
			foreach ($our_keys as $name_hash => $key) {
				if (!is_array($key)) return;
				$ud_rpc = $updraftplus->get_udrpc($name_hash.'.migrator.updraftplus.com');
				if (!empty($key['sender_public'])) {
					$ud_rpc->set_message_format(2);
					$ud_rpc->set_key_local($key['key']);
					$ud_rpc->set_key_remote($key['sender_public']);
				} else {
					$ud_rpc->set_message_format(1);
					$ud_rpc->set_key_local($key['key']);
				}
				$this->receivers[$name_hash] = $ud_rpc;
				// Create listener (which causes WP actions to be fired when messages are received)
				$ud_rpc->activate_replay_protection();
				$ud_rpc->create_listener();
			}
			add_filter('udrpc_command_send_chunk', array($this, 'udrpc_command_send_chunk'), 10, 3);
			add_filter('udrpc_command_get_file_status', array($this, 'udrpc_command_get_file_status'), 10, 3);
			add_filter('udrpc_command_upload_complete', array($this, 'udrpc_command_upload_complete'), 10, 3);
			add_filter('udrpc_action', array($this, 'udrpc_action'), 10, 4);
		}
	}

	/**
	 * This function will return a response to the remote site on any action
	 *
	 * @param string $response		 - a string response
	 * @param string $command		 - the incoming command
	 * @param array  $data			 - an array of response data
	 * @param string $name_indicator - a string to identify the request
	 *
	 * @return array                 - the array response
	 */
	public function udrpc_action($response, $command, $data, $name_indicator) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (is_array($data) && isset($data['sender_public'])) {
			// Do we already know the sender's public key?
			$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
			if (is_array($our_keys) && preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches) && !empty($our_keys[$matches[1]]) && empty($our_keys[$matches[1]]['sender_public'])) {
				// N.B. When the sender sends a public key, that indicates that *all* future communications will use it
				$our_keys[$matches[1]]['sender_public'] = $data['sender_public'];
				UpdraftPlus_Options::update_updraft_option('updraft_migrator_localkeys', $our_keys);
				if (!is_array($response['data'])) $response['data'] = array();
				$response['data']['got_public'] = 1;
			}
		}
	
		return $response;
	}
	
	protected function initialise_listener_error_handling($hash) {
		global $updraftplus;
		$updraftplus->error_reporting_stop_when_logged = true;
		set_error_handler(array($updraftplus, 'php_error'), E_ALL & ~E_STRICT);
		$this->php_events = array();
		add_filter('updraftplus_logline', array($this, 'updraftplus_logline'), 10, 4);
		if (!UpdraftPlus_Options::get_updraft_option('updraft_debug_mode')) return;
		$updraftplus->nonce = $hash;
		$updraftplus->logfile_open($hash);
	}

	protected function return_rpc_message($msg) {
		if (is_array($msg) && isset($msg['response']) && 'error' == $msg['response']) {
			global $updraftplus;
			$updraftplus->log('Unexpected response code in remote communications: '.serialize($msg));
		}
		if (!empty($this->php_events)) {
			if (!isset($msg['data'])) $msg['data'] = null;
			$msg['data'] = array('php_events' => array(), 'previous_data' => $msg['data']);
			foreach ($this->php_events as $logline) {
				$msg['data']['php_events'][] = $logline;
			}
		}
		restore_error_handler();

		return $msg;
	}

	public function updraftplus_logline($line, $nonce, $level, $uniq_id) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ('notice' === $level && 'php_event' === $uniq_id) {
			$this->php_events[] = $line;
		}
		return $line;
	}

	public function udrpc_command_send_chunk($response, $data, $name_indicator) {

		if (!preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches)) return $response;
		$name_hash = $matches[1];

		$this->initialise_listener_error_handling($name_hash);

		global $updraftplus;

		// send_message('send_chunk', array('file' => $file, 'data' => $chunk, 'start' => $upload_start))

		if (!is_array($data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_expected_array'));

		if (!isset($data['file'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_file'));

		if (!isset($data['data'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_data'));

		if (!isset($data['start'])) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_no_start'));

		// Make sure the parameters are valid
		if (!is_numeric($data['start']) || absint($data['start']) != $data['start']) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_start'));

		// Sanity-check the file name
		$file = $data['file'];
		if (!preg_match('/(-db\.gz|-db\.gz\.crypt|-db|\.(sql|sql\.gz|sql\.bz2|zip|tar|tar\.bz2|tar\.gz|txt))/i', $file)) return array('response' => 'error', 'data' => 'illegal_file_name1');
		if (basename($file) != $file) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_illegal_character'));

		$start = $data['start'];

		$is_last_chunk = empty($data['last_chunk']) ? 0 : 1;
		if (!$is_last_chunk) {
		} else {
			$orig_file = $file;
			if (!empty($data['label'])) $label = $data['label'];
		}
		$file .= '.tmp';

		// Intentionally over-write the variable, in case memory is short and in case PHP's garbage collector is this clever
		$data = base64_decode($data['data']);

		$updraft_dir = $updraftplus->backups_dir_location();
		$fullpath = $updraft_dir.'/'.$file;

		$existing_size = file_exists($fullpath) ? filesize($fullpath) : 0;

		if ($start > $existing_size) {
			return $this->return_rpc_message(array('response' => 'error', 'data' => "invalid_start_too_big:start=${start},existing_size=${existing_size}"));
		}

		if (false == ($fhandle = fopen($fullpath, 'ab'))) {
			return $this->return_rpc_message(array('response' => 'error', 'data' => 'file_open_failure'));
		}

		// fseek() returns 0 for success, or -1 for failure
		if ($start != $existing_size && -1 == fseek($fhandle, $start))  return $this->return_rpc_message(array('response' => 'error', 'data' => 'fseek_failure'));

		$write_status = fwrite($fhandle, $data);
		
		if (false === $write_status || (false == $write_status && !empty($data))) return $this->return_rpc_message(array('response' => 'error', 'data' => 'fwrite_failure'));

		@fclose($fhandle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (is_array($our_keys) && isset($our_keys[$name_hash]) && !empty($our_keys[$name_hash]['name'])) $updraftplus->log("Received data chunk on key ".$our_keys[$name_hash]['name']. " ($file, ".$start.", is_last=$is_last_chunk)");

		if ($is_last_chunk) {
			if (!rename($fullpath, $updraft_dir.'/'.$orig_file)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'rename_failure'));
			$only_add_this_file = array('file' => $orig_file);
			if (isset($label)) $only_add_this_file['label'] = $label;
			UpdraftPlus_Backup_History::rebuild(false, $only_add_this_file);
		}

		return $this->return_rpc_message(array(
			'response' => 'file_status',
			'data' => $this->get_file_status($file)
		));
	}

	protected function get_file_status($file) {

		global $updraftplus;
		$fullpath = $updraftplus->backups_dir_location().'/'.basename($file);

		if (file_exists($fullpath)) {
			$size = filesize($fullpath);
			$status = 1;
		} elseif (file_exists($fullpath.'.tmp')) {
			$size = filesize($fullpath.'.tmp');
			$status = 0;
		} else {
			$size = 0;
			$status = 0;
		}

		return array(
			'size' => $size,
			'status' => $status,
		);
	}

	public function udrpc_command_get_file_status($response, $data, $name_indicator) {
		if (!preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches)) return $response;
		$name_hash = $matches[1];

		$this->initialise_listener_error_handling($name_hash);

		if (!is_string($data)) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_expected_string'));

		if (basename($data) != $data) return $this->return_rpc_message(array('response' => 'error', 'data' => 'invalid_input_illegal_character'));

		return $this->return_rpc_message(array(
			'response' => 'file_status',
			'data' => $this->get_file_status($data)
		));
	}

	/**
	 * This function will return a response to the remote site to acknowledge that we have recieved the upload_complete message and if this is a clone it call the ready_for_restore action
	 *
	 * @param string $response       - a string response
	 * @param array  $data           - an array of data
	 * @param string $name_indicator - a string to identify the request
	 *
	 * @return array                 - the array response
	 */
	public function udrpc_command_upload_complete($response, $data, $name_indicator) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if (!preg_match('/^([a-f0-9]+)\.migrator.updraftplus.com$/', $name_indicator, $matches)) return $response;
		
		if (defined('UPDRAFTPLUS_THIS_IS_CLONE') && UPDRAFTPLUS_THIS_IS_CLONE) {
			$job_id = (is_array($data) && !empty($data['job_id'])) ? $data['job_id'] : null;
			
			$signal_ready_for_restore_now = true;
			
			if (class_exists('UpdraftPlus_Remote_Communications')) {
				$test_udrpc = new UpdraftPlus_Remote_Communications();
				if (version_compare($test_udrpc->version, '1.4.21', '>=')) {
					$signal_ready_for_restore_now = false;
					$this->job_id = $job_id;
					add_action('udrpc_action_send_response', array($this, 'udrpc_action_send_response'));
				}
			}
			
			if ($signal_ready_for_restore_now) {
				do_action('updraftplus_temporary_clone_ready_for_restore', $job_id);
			}
		}

		return $this->return_rpc_message(array(
			'response' => 'file_status',
			'data' => ''
		));
	}

	/**
	 * UpdraftPlus_Remote_Communications is going to echo a response and then die. We pre-empt it.
	 *
	 * @param String $response
	 */
	public function udrpc_action_send_response($response) {
	
		global $updraftplus;
	
		$updraftplus->close_browser_connection($response);
		
		do_action('updraftplus_temporary_clone_ready_for_restore', $this->job_id);
		
		die;
	
	}
	
	public function updraftplus_initial_jobdata($initial_jobdata, $options, $split_every) {

		if (is_array($options) && !empty($options['extradata']) && !empty($options['extradata']['services']) && preg_match('#remotesend/(\d+)#', $options['extradata']['services'], $matches)) {

			// Load the option now - don't wait until send time
			$site_id = $matches[1];
			$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
			if (!is_array($remotesites)) $remotesites = array();

			if (empty($remotesites[$site_id]) || empty($remotesites[$site_id]['url']) || empty($remotesites[$site_id]['key']) || empty($remotesites[$site_id]['name_indicator'])) {
				throw new Exception("Remote site id ($site_id) not found - send aborted");
			}

			array_push($initial_jobdata, 'remotesend_info', $remotesites[$site_id]);

			// Reduce to 100MB if it was above. Since the user isn't expected to directly manipulate these zip files, the potentially higher number of zip files doesn't matter.
			$split_every_key = array_search('split_every', $initial_jobdata) + 1;
			if ($split_every > 100) $initial_jobdata[$split_every_key] = 100;

		}

		return $initial_jobdata;
	}

	public function updraft_printjob_beforewarnings($ret, $jobdata) {
		if (!empty($jobdata['remotesend_info']) && !empty($jobdata['remotesend_info']['url'])) {
			$ret .= '<p style="padding:0px; margin:2px 0;">'.__('Backup data will be sent to:', 'updraftplus').' '.htmlspecialchars($jobdata['remotesend_info']['url']).'</p>';
		}
		return $ret;
	}

	public function updraft_remote_ping_test($data) {

		if (!isset($data['id']) || !is_numeric($data['id']) || empty($data['url'])) die;

		$remote_indicator = $data['id'];

		$ping_result = $this->do_ping_test($remote_indicator, $data['url']);
		
		die(json_encode($ping_result));
		
	}
	
	/**
	 * Do an RPC ping test
	 *
	 * @param String $remote_indicator
	 * @param String $url
	 *
	 * @return Array - results
	 */
	public function do_ping_test($remote_indicator, $url) {
	
		global $updraftplus;
	
		$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
		if (!is_array($remotesites)) $remotesites = array();

		if (empty($remotesites[$remote_indicator]) || $url != $remotesites[$remote_indicator]['url'] || empty($remotesites[$remote_indicator]['key']) || empty($remotesites[$remote_indicator]['name_indicator'])) {
			return array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.__('site not found', 'updraftplus'));
		}

		try {
		
			$updraftplus->error_reporting_stop_when_logged = true;
			set_error_handler(array($updraftplus, 'php_error'), E_ALL & ~E_STRICT);
			$this->php_events = array();
			add_filter('updraftplus_logline', array($this, 'updraftplus_logline'), 10, 4);
		
			$opts = $remotesites[$remote_indicator];
			$ud_rpc = $updraftplus->get_udrpc($opts['name_indicator']);
			$send_data = null;
			
			if (!empty($opts['format_support']) && 2 == $opts['format_support']) {
				if (empty($opts['remote_got_public'])) {
					// Can't upgrade to format 2 until we know the other end has our public key
					$use_format = 1;
					$send_data = array('sender_public' => $opts['local_public']);
				} else {
					$use_format = 2;
				}
			} else {
				$use_format = 1;
			}
			
			$ud_rpc->set_message_format($use_format);
			
			if (2 == $use_format) {
				$ud_rpc->set_key_remote($opts['key']);
				$ud_rpc->set_key_local($opts['local_private']);
			} else {
				$ud_rpc->set_key_local($opts['key']);
			}
			
			$ud_rpc->set_destination_url($url);
			$ud_rpc->activate_replay_protection();
			
			do_action('updraftplus_remotesend_udrpc_object_obtained', $ud_rpc, $opts);

			$response = $ud_rpc->send_message('ping', $send_data);

			restore_error_handler();
			
			if (is_wp_error($response)) {

				$err_msg = __('Error:', 'updraftplus').' '.$response->get_error_message();
				$err_data = $response->get_error_data();
				$err_code = $response->get_error_code();

			} elseif (!is_array($response) || empty($response['response']) || 'pong' != $response['response']) {

				$err_msg = __('Error:', 'updraftplus').' '.sprintf(__('You should check that the remote site is online, not firewalled, does not have security modules that may be blocking access, has UpdraftPlus version %s or later active and that the keys have been entered correctly.', 'updraftplus'), '2.10.3');
				$err_data = $response;
				$err_code = 'no_pong';

			} elseif (!empty($response['data']['got_public'])) {
				$remotesites[$remote_indicator]['remote_got_public'] = 1;
				UpdraftPlus_Options::update_updraft_option('updraft_remotesites', $remotesites);
			}

			if (isset($err_msg)) {

				$res = array('e' => 1, 'r' => $err_msg);

				if ($this->url_looks_internal($url)) {
					$res['moreinfo'] = '<p>'.sprintf(__('The site URL you are sending to (%s) looks like a local development website. If you are sending from an external network, it is likely that a firewall will be blocking this.', 'updraftplus'), htmlspecialchars($url)).'</p>';
				}

				// We got several support requests from people who didn't seem to be aware of other methods
				$msg_try_other_method = '<p>'.__('If sending directly from site to site does not work for you, then there are three other methods - please try one of these instead.', 'updraftplus').' <a href="https://updraftplus.com/faqs/how-do-i-migrate-to-a-new-site-location/#importing" target="_blank">'.__('For longer help, including screenshots, follow this link.', 'updraftplus').'</a></p>';

				$res['moreinfo'] = isset($res['moreinfo']) ? $res['moreinfo'].$msg_try_other_method : $msg_try_other_method;

				if (isset($err_data)) $res['data'] = $err_data;
				if (isset($err_code)) $res['code'] = $err_code;
				
				if (!empty($this->php_events)) $res['php_events'] = $this->php_events;
				
				return $res;
			}

			$ret = '<p>'.__('Testing connection...', 'updraftplus').' '.__('OK', 'updraftplus').'</p>';

			global $updraftplus_admin;

			$ret .= '<label class="updraft_checkbox" for="remotesend_backupnow_db"><input type="checkbox" checked="checked" id="remotesend_backupnow_db">'.__("Database", 'updraftplus').'</label>';
			$ret .= $updraftplus_admin->files_selector_widgetry('remotesend_', false, false);

			$service = $updraftplus->just_one(UpdraftPlus_Options::get_updraft_option('updraft_service'));
			if (is_string($service)) $service = array($service);

			if (is_array($service) && !empty($service) && array('none') !== $service) {
				$first_one = true;
				foreach ($service as $s) {
					if (!$s) continue;
					if (isset($updraftplus->backup_methods[$s])) {
						if ($first_one) {
							$first_one = false;
							$ret .= '<p>';
							$ret .= '<input type="checkbox" id="remotesend_backupnow_cloud"> <label for="remotesend_backupnow_cloud">'.__("Also send this backup to the active remote storage locations", 'updraftplus');
							$ret .= ' (';
						} else {
							$ret .= ', ';
						}
						$ret .= $updraftplus->backup_methods[$s];
					}
				}
				if (!$first_one) $ret .= ')';
				$ret .= '</label></p>';
			}

			$ret .= apply_filters('updraft_backupnow_modal_afteroptions', '', 'remotesend_');
			$ret .= '<button class="button-primary" style="font-size:16px; margin-left: 3px; width:85px;" id="updraft_migrate_send_button" onclick="updraft_migrate_go_backup();">'.__('Send', 'updraftplus').'</button>';

			return array('success' => 1, 'r' => $ret);
		} catch (Exception $e) {
			return array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
		}
	}

	/**
	 * This is used only for an advisory warning - does not have to be able to always detect
	 *
	 * @param  string $url
	 */
	protected function url_looks_internal($url) {
		$url_host = strtolower(parse_url($url, PHP_URL_HOST));
		if ('localhost' == $url_host || strpos($url_host, '127.') === 0 || strpos($url_host, '10.') === 0 || '::1' == $url_host || strpos($url_host, 'localhost') !== false || substr($url_host, -4, 4) == '.dev') return true;
		return false;
	}

	public function updraft_migrate_key_delete($data) {
		if (empty($data['keyid'])) die;
		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (!is_array($our_keys)) $our_keys = array();
		unset($our_keys[$data['keyid']]);
		UpdraftPlus_Options::update_updraft_option('updraft_migrator_localkeys', $our_keys);
		echo json_encode(array('ourkeys' => $this->list_our_keys($our_keys)));
		die;
	}

	/**
	 * This function is a wrapper for updraft_migrate_key_create when being called from WP_CLI it allows us to return the created key rather than echo it, by passing return_instead_of_echo as part of $data.
	 *
	 * @param string $string - empty string to filter on
	 * @param array  $data   - an array of data needed to create the RSA keypair should also include return_instead_of_echo to return the result
	 *
	 * @return string        - the RSA remote key
	 */
	public function updraft_migrate_key_create_return($string, $data) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		return $this->updraft_migrate_key_create($data);
	}

	/**
	 * Called upon the WP action updraft_s3_newuser. Dies.
	 *
	 * @param array $data - the posted data
	 *
	 * @return void
	 */
	public function updraft_migrate_key_create($data) {

		if (empty($data['name'])) die;
		$name = stripslashes($data['name']);
		
		$size = (empty($data['size']) || !is_numeric($data['size']) || $data['size'] < 1024) ? 2048 : (int) $data['size'];

		$name_hash = md5($name); // 32 characters
		$indicator_name = $name_hash.'.migrator.updraftplus.com';

		$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		if (!is_array($our_keys)) $our_keys = array();
		
		if (isset($our_keys[$name_hash])) {
			echo json_encode(array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.__('A key with this name already exists; you must use a unique name.', 'updraftplus')));
			die;
		}

		global $updraftplus;
		$ud_rpc = $updraftplus->get_udrpc($indicator_name);

		if (is_object($ud_rpc) && $ud_rpc->generate_new_keypair($size)) {
			$local_bundle = $ud_rpc->get_portable_bundle('base64_with_count');

			$our_keys[$name_hash] = array('name' => $name, 'key' => $ud_rpc->get_key_local());
			UpdraftPlus_Options::update_updraft_option('updraft_migrator_localkeys', $our_keys);

			if (isset($data['return_instead_of_echo']) && $data['return_instead_of_echo']) return $local_bundle;

			echo json_encode(array(
				'bundle' => $local_bundle,
				'r' => __('Key created successfully.', 'updraftplus').' '.__('You must copy and paste this key on the sending site now - it cannot be shown again.', 'updraftplus'),
				'selector' => $this->get_remotesites_selector(),
				'ourkeys' => $this->list_our_keys($our_keys),
			));
			die;
		}

		if (extension_loaded('mbstring')) {
			// phpcs:ignore  PHPCompatibility.IniDirectives.RemovedIniDirectives.mbstring_func_overloadDeprecated -- Commented out as this flags as not compatible with PHP 5.2
			if (ini_get('mbstring.func_overload') & 2) {
				echo json_encode(array('e' => 1, 'r' => __('Error:', 'updraftplus').' '.sprintf(__('The setting %s is turned on in your PHP settings. It is deprecated, causes encryption to malfunction, and should be turned off.', 'updraftplus'), 'mbstring.func_overload')));
				die;
			}
		}

		echo json_encode(array('e' => 1));
		die;
	}

	public function updraft_migrate_newdestination($data) {

		global $updraftplus;
		$ret = array();

		if (empty($data['key'])) {
			$ret['e'] = sprintf(__("Failure: No %s was given.", 'updraftplus'), __('key', 'updraftplus'));
		} else {
		
			// The indicator isn't really needed - we won't be receiving on it
			$our_indicator = md5(network_site_url()).'.migrator.updraftplus.com';
			$ud_rpc = $updraftplus->get_udrpc($our_indicator);
			
			$ud_rpc->set_can_generate(true);

			// A bundle has these keys: key, name_indicator, url
			$decode_bundle = $ud_rpc->decode_portable_bundle($data['key'], 'base64_with_count');

			if (!is_array($decode_bundle) || !empty($decode_bundle['code'])) {
				$ret['e'] = __('Error:', 'updraftplus');
				if (!empty($decode_bundle['code']) && 'invalid_wrong_length' == $decode_bundle['code']) {
					$ret['e'] .= ' '.__('The entered key was the wrong length - please try again.', 'updraftplus');
				} elseif (!empty($decode_bundle['code']) && 'invalid_corrupt' == $decode_bundle['code']) {
					$ret['e'] .= ' '.__('The entered key was corrupt - please try again.', 'updraftplus').' ('.$decode_bundle['data'].')';
				} elseif (empty($decode_bundle['key']) || empty($decode_bundle['url'])) {
					$ret['e'] .= ' '.__('The entered key was corrupt - please try again.', 'updraftplus');
					$ret['data'] = $decode_bundle;
				}
			} elseif (empty($decode_bundle['key']) || empty($decode_bundle['url'])) {
					$ret['e'] = __('Error:', 'updraftplus').' '.__('The entered key was corrupt - please try again.', 'updraftplus');
					$ret['data'] = $decode_bundle;
			} else {
				
				if (trailingslashit(network_site_url()) == $decode_bundle['url']) {
					$ret['e'] = __('Error:', 'updraftplus').' '.__('The entered key does not belong to a remote site (it belongs to this one).', 'updraftplus');
				} else {

					// Store the information
					$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
					if (!is_array($remotesites)) $remotesites = array();
					foreach ($remotesites as $k => $rsite) {
						if (!is_array($rsite)) continue;
						if ($rsite['url'] == $decode_bundle['url']) unset($remotesites[$k]);
					}

					if (false == $ud_rpc->generate_new_keypair()) {
						$ret['e'] = __('Error:', 'updraftplus').' An error occurred when attempting to generate a new key-pair';
					} else {
					
						$decode_bundle['local_private'] = $ud_rpc->get_key_local();
						$decode_bundle['local_public'] = $ud_rpc->get_key_remote();
					
						$remotesites[] = $decode_bundle;
						UpdraftPlus_Options::update_updraft_option('updraft_remotesites', $remotesites);

						$ret['selector'] = $this->get_remotesites_selector($remotesites);

						// Return the new HTML widget to the front end
						$ret['r'] = __('The key was successfully added.', 'updraftplus').' '.__('It is for sending backups to the following site: ', 'updraftplus').htmlspecialchars($decode_bundle['url']);
					}

				}
			}

		}

		echo json_encode($ret);
		die;
	}

	protected function get_remotesites_selector($remotesites = false) {

		if (false === $remotesites) {
			$remotesites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');
			if (!is_array($remotesites)) $remotesites = array();
		}

		$ret = '';

		if (empty($remotesites)) {
			$ret .= '<p id="updraft_migrate_receivingsites_nonemsg"><em>'.__('No receiving sites have yet been added.', 'updraftplus').'</em></p>';
		} else {
			$ret .= '<p class="updraftplus-remote-sites-selector"><label>'.__('Send to site:', 'updraftplus').'</label> <select id="updraft_remotesites_selector">';
			foreach ($remotesites as $k => $rsite) {
				if (!is_array($rsite) || empty($rsite['url'])) continue;
				$ret .= '<option value="'.esc_attr($k).'">'.htmlspecialchars($rsite['url']).'</option>';
			}
			$ret .= '</select>';
			$ret .= ' <button class="button-primary" id="updraft_migrate_send_button" onclick="updraft_migrate_send_backup();">'.__('Send', 'updraftplus').'</button>';
			$ret .= '</p>';
		}

		$ret .= '<div class="text-link-menu">';
		$ret .= '<a href="#" class="updraft_migrate_add_site--trigger"><span class="dashicons dashicons-plus"></span>'.__('Add a site', 'updraftplus').'</a>';
		$ret .= sprintf(
			'<a href="javascript:void(0)" class="updraft_migrate_clear_sites" %s onclick="updraft_migrate_delete_existingsites(\'%s\');"><span class="dashicons dashicons-trash"></span>%s</a>',
			empty($remotesites) ? 'style="display: none"' : '',
			esc_js(__("You are about to permanently delete the list of existing sites. This action cannot be undone. 'Cancel' to stop, 'OK' to delete.")),
			__('Clear list of existing sites', 'updraftplus')
		);
		$ret .= '</div>';

		return $ret;
	}

	protected function list_our_keys($our_keys = false) {
		if (false === $our_keys) {
			$our_keys = UpdraftPlus_Options::get_updraft_option('updraft_migrator_localkeys');
		}

		if (empty($our_keys)) return '<em>'.__('No keys to allow remote sites to send backup data here have yet been created.', 'updraftplus').'</em>';

		$ret = '';
		$first_one = true;

		foreach ($our_keys as $k => $key) {
			if (!is_array($key)) continue;
			if ($first_one) {
				$first_one = false;
				$ret .= '<p><strong>'.__('Existing keys', 'updraftplus').'</strong><br>';
			}
			$ret .= htmlspecialchars($key['name']);
			$ret .= ' - <a href="'.UpdraftPlus::get_current_clean_url().'" onclick="updraft_migrate_local_key_delete(\''.esc_attr($k).'\'); return false;" class="updraft_migrate_local_key_delete" data-keyid="'.esc_attr($k).'">'.__('Delete', 'updraftplus').'</a>';
			$ret .= '<br>';
		}

		if ($ret) $ret .= '</p>';

		return $ret;

	}

	/**
	 * Delete the list of existing remote sites from the database
	 *
	 * @return String The JSON format of the response of the deletion process
	 */
	public function updraft_migrate_delete_existingsites() {

		global $wpdb;

		$ret = array();

		$old_val = $wpdb->suppress_errors();

		UpdraftPlus_Options::delete_updraft_option('updraft_remotesites');

		$remote_sites = UpdraftPlus_Options::get_updraft_option('updraft_remotesites');

		if (is_array($remote_sites) && !empty($remote_sites)) {
			$err_msg = __('There was an error while trying to remove the list of existing sites.', 'updraftplus');
			$err_db = !empty($wpdb->last_error) ? ' ('.$wpdb->last_error.' - '.$wpdb->last_query.')' : '';
			$ret['error'] = $err_msg.$err_db;
		} else {
			$ret['success'] = __('The list of existing sites has been removed', 'updraftplus');
			$ret['html'] = $this->get_remotesites_selector();
		}

		$wpdb->suppress_errors($old_val);

		echo json_encode($ret);
	}
}
