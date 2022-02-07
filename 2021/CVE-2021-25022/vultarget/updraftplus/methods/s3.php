<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// Converted to multi-options (Feb 2017-) and previous options conversion removed: Yes

/**
 * This class is used by both UpdraftPlus_S3 and UpdraftPlus_S3_Compat
 */
class UpdraftPlus_S3Exception extends Exception {
	public function __construct($message, $file, $line, $code = 0) {
		parent::__construct($message, $code);
		$this->file = $file;
		$this->line = $line;
	}
}

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

class UpdraftPlus_BackupModule_s3 extends UpdraftPlus_BackupModule {

	// This variable can/should be over-ridden in child classes as appropriate
	protected $use_v4 = true;
	
	// This variable can/should be over-ridden in child classes as appropriate
	protected $provider_can_use_aws_sdk = true;

	// This variable can/should be over-ridden in child classes as appropriate
	protected $provider_has_regions = true;

	protected $quota_used = null;

	protected $s3_exception;

	protected $download_chunk_size = 10485760;

	private $got_with;

	/**
	 * Retrieve specific options for this remote storage module
	 *
	 * @param Boolean $force_refresh - if set, and if relevant, don't use cached credentials, but get them afresh
	 *
	 * @return Array - an array of options
	 */
	protected function get_config($force_refresh = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $force_refresh unused
		$opts = $this->get_options();
		$opts['whoweare'] = 'S3';
		$opts['whoweare_long'] = 'Amazon S3';
		$opts['key'] = 's3';
		return $opts;
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not mentioned are asuumed to not be supported)
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
			'accesskey' => '',
			'secretkey' => '',
			'path' => '',
			'rrs' => '',
			'server_side_encryption' => '',
		);
	}

	/**
	 * Return the name of the class that should be used to interface with the storage provider, and make sure that it has been include()-ed.
	 *
	 * @return String - e.g. UpdraftPlus_S3, UpdraftPlus_S3_Compat
	 */
	protected function indicate_s3_class() {
	
		// N.B. : The classes must have different names, as if multiple remote storage options are chosen, then we could theoretically need both (if both Amazon and a compatible-S3 provider are used)

		// UpdraftPlus_S3 (the internal, non-AWS toolkit) is used when not accessing Amazon Web Services, so set that as the default
		$class_to_use = 'UpdraftPlus_S3';
		
		// If on a PHP version supported by the AWS SDK, and if the constant forcing the internal toolkit is not set, then consider using it. The 3.x branch of the AWS SDK requires PHP 5.5+
		if (version_compare(PHP_VERSION, '5.5', '>=') && $this->provider_can_use_aws_sdk && (!defined('UPDRAFTPLUS_S3_OLDLIB') || !UPDRAFTPLUS_S3_OLDLIB)) {
			$class_to_use = 'UpdraftPlus_S3_Compat';
		}

		if (!class_exists($class_to_use)) {
			if ('UpdraftPlus_S3_Compat' == $class_to_use) {
				include_once(UPDRAFTPLUS_DIR.'/includes/S3compat.php');
			} else {
				include_once(UPDRAFTPLUS_DIR.'/includes/S3.php');
			}
		}
		
		return $class_to_use;
	}

	/**
	 * Get an S3 object, after setting our options
	 *
	 * @param  String	   $key 		   S3 Key
	 * @param  String	   $secret 		   S3 secret
	 * @param  Boolean	   $useservercerts User server certificates
	 * @param  Boolean	   $disableverify  Check if disableverify is enabled
	 * @param  Boolean	   $nossl 		   Check if there is SSL or not
	 * @param  Null|String $endpoint 	   S3 endpoint to use
	 * @param  Boolean	   $sse 		   A flag to use server side encryption
	 * @param  String	   $session_token  The session token returned by AWS for temporary credentials access
	 * @return Array
	 */
	public function getS3($key, $secret, $useservercerts, $disableverify, $nossl, $endpoint = null, $sse = false, $session_token = null) {
		$storage = $this->get_storage();
		if (!empty($storage) && !is_wp_error($storage)) return $storage;

		if (is_string($key)) $key = trim($key);
		if (is_string($secret)) $secret = trim($secret);

		// Ignore the 'nossl' setting if the endpoint is DigitalOcean Spaces (https://developers.digitalocean.com/documentation/v2/)
		if (is_string($endpoint) && preg_match('^/[\.^]digitaloceanspaces\.com$/', $endpoint)) {
			$nossl = apply_filters('updraftplus_gets3_nossl', false, $endpoint, $nossl);
		}
		
		// Saved in case the object needs recreating for the corner-case where there is no permission to look up the bucket location
		$this->got_with = array(
			'key' => $key,
			'secret' => $secret,
			'useservercerts' => $useservercerts,
			'disableverify' => $disableverify,
			'nossl' => $nossl,
			'server_side_encryption' => $sse,
			'session_token' => $session_token
		);

		if (is_wp_error($key)) return $key;

		if ('' == $key || '' == $secret) {
			return new WP_Error('no_settings', get_class($this).': '.__('No settings were found - please go to the Settings tab and check your settings', 'updraftplus'));
		}

		$use_s3_class = $this->indicate_s3_class();

		if (!class_exists('WP_HTTP_Proxy')) include_once(ABSPATH.WPINC.'/class-http.php');
		$proxy = new WP_HTTP_Proxy();

		$use_ssl = true;
		$ssl_ca = false;
		$config = $this->get_config();
		if (!$nossl) {
			$curl_version = function_exists('curl_version') ? curl_version() : array('features' => null);
			$curl_ssl_supported = ($curl_version['features'] && defined('CURL_VERSION_SSL') && CURL_VERSION_SSL);
			if ($curl_ssl_supported) {
				if ($disableverify) {
					$ssl_ca = false;
					$this->log("Disabling verification of SSL certificates");
				} else {
					if ($useservercerts) {
						$this->log("Using the server's SSL certificates");
						$ssl_ca = 'system';
					} else {
						$ssl_ca = file_exists(UPDRAFTPLUS_DIR.'/includes/cacert.pem') ? UPDRAFTPLUS_DIR.'/includes/cacert.pem' : true;
					}
				}
			} else {
				$use_ssl = false;
				$this->log("Curl/SSL is not available. Communications will not be encrypted.");
			}
		} else {
			$use_ssl = false;
			$this->log("SSL was disabled via the user's preference. Communications will not be encrypted.");
		}

		try {
			$storage = new $use_s3_class($key, $secret, $use_ssl, $ssl_ca, $endpoint, $session_token);

			// The use of calling use_dns_bucket_name method here is to switch the storage from using path-style to dns style.
			// regardless of what the $storage object is instantiated from (UpdraftPlus_S3_Compat class (S3compat.php) or UpdraftPlus_S3 class (S3.php)), the method doesn't use the bucket name for any purpose so it's not needed to pass it
			$this->maybe_use_dns_bucket_name($storage, $config);

			$signature_version = empty($this->use_v4) ? 'v2' : 'v4';
			$signature_version = apply_filters('updraftplus_s3_signature_version', $signature_version, $this->use_v4, $this);
			if (!is_a($storage, 'UpdraftPlus_S3_Compat')) {
				$storage->setSignatureVersion($signature_version);
			}
		} catch (Exception $e) {
		
			// Catch a specific PHP engine bug - see HS#6364
			if ('UpdraftPlus_S3_Compat' == $use_s3_class && is_a($e, 'InvalidArgumentException') && false !== strpos('Invalid signature type: s3', $e->getMessage())) {
				include_once(UPDRAFTPLUS_DIR.'/includes/S3.php');
				$use_s3_class = 'UpdraftPlus_S3';
				$try_again = true;
			} else {
				$this->log(__('Error: Failed to initialise', 'updraftplus').": ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
				$this->log(__('Error: Failed to initialise', 'updraftplus'));
				return new WP_Error('s3_init_failed', sprintf(__('%s Error: Failed to initialise', 'updraftplus'), 'S3').": ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			}
		}
		
		if (!empty($try_again)) {
			try {
				$storage = new $use_s3_class($key, $secret, $use_ssl, $ssl_ca, $endpoint, $session_token);
				// The use of calling use_dns_bucket_name method here is to switch the storage from using path-style to dns style.
				// regardless of what the $storage object is instantiated from (UpdraftPlus_S3_Compat class (S3compat.php) or UpdraftPlus_S3 class (S3.php)), the method doesn't use the bucket name for any purpose so it's not needed to pass it
				$this->maybe_use_dns_bucket_name($storage, $config);
			} catch (Exception $e) {
				$this->log(__('Error: Failed to initialise', 'updraftplus').": ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
				$this->log(__('Error: Failed to initialise', 'updraftplus'));
				return new WP_Error('s3_init_failed', sprintf(__('%s Error: Failed to initialise', 'updraftplus'), 'S3').": ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
			}
			$this->log("Hit a PHP engine bug - had to switch to the older S3 library");
		}

		if ($proxy->is_enabled()) {
			// WP_HTTP_Proxy returns empty strings where we want nulls
			$user = $proxy->username();
			if (empty($user)) {
				$user = null;
				$pass = null;
			} else {
				$pass = $proxy->password();
				if (empty($pass)) $pass = null;
			}
			$port = (int) $proxy->port();
			if (empty($port)) $port = 8080;
			$storage->setProxy($proxy->host(), $user, $pass, CURLPROXY_HTTP, $port);
		}
		
		if (method_exists($storage, 'setServerSideEncryption') && ($this->use_sse() || $sse)) $storage->setServerSideEncryption('AES256');

		$this->set_storage($storage);

		return $storage;
	}

	/**
	 * Given an S3 object, possibly set the region on it
	 *
	 * @param Object $obj		  - like UpdraftPlus_S3
	 * @param String $region
	 * @param String $bucket_name
	 */
	protected function set_region($obj, $region, $bucket_name = '') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $bucket_name

	// AWS Regions: https://docs.aws.amazon.com/general/latest/gr/rande.html
		switch ($region) {
			case 'EU':
			case 'eu-west-1':
			$endpoint = 's3-eu-west-1.amazonaws.com';
				break;
			case 'us-east-1':
			$endpoint = 's3.amazonaws.com';
				break;
			case 'us-west-1':
			case 'us-east-2':
			case 'us-west-2':
			$endpoint = 's3-'.$region.'.amazonaws.com';
				break;
			case 'sa-east-1':
			case 'us-gov-west-1':
			case 'ca-central-1':
			case 'ap-northeast-1':
			case 'ap-northeast-2':
			case 'ap-southeast-1':
			case 'ap-southeast-2':
			case 'eu-central-1':
			case 'eu-south-1':
			case 'eu-north-1':
			case 'eu-west-2':
			case 'eu-west-3':
			case 'ap-south-1':
			case 'me-south-1':
			case 'af-south-1':
			case 'ap-east-1':
			case 'ap-northeast-3':
			$endpoint = 's3.'.$region.'.amazonaws.com';
				break;
			case 'cn-north-1':
			case 'cn-northwest-1':
			$endpoint = 's3.'.$region.'.amazonaws.com.cn';
				break;
			default:
				break;
		}

		if (isset($endpoint)) {
			$this->log("Set region: $region");
			$obj->setRegion($region);

			if (!is_a($obj, 'UpdraftPlus_S3_Compat')) {
				$this->log("Set endpoint: $endpoint");
				$obj->setEndpoint($endpoint);
			}
		}
	}

	/**
	 * Whether to always use server-side encryption.
	 *
	 * This can be over-ridden in child classes of course... and the method here is both the default and the value used for AWS
	 *
	 * @return Boolean
	 */
	protected function use_sse() {
		return false;
	}
	
	/**
	 * Perform the upload of backup archives
	 *
	 * @param Array $backup_array - a list of file names (basenames) (within UD's directory) to be uploaded
	 *
	 * @return Mixed - return (boolean)false ot indicate failure, or anything else to have it passed back at the delete stage (most useful for a storage object).
	 */
	public function backup($backup_array) {

		global $updraftplus;

		$config = $this->get_config();

		if (empty($config['accesskey']) && !empty($config['error_message'])) {
			$err = new WP_Error('no_settings', $config['error_message']);
			return $updraftplus->log_wp_error($err, false, true);
		}

		if (empty($config['sessiontoken'])) $config['sessiontoken'] = null;
		
		$storage = $this->getS3(
			$config['accesskey'],
			$config['secretkey'],
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
			null,
			!empty($config['server_side_encryption']),
			$config['sessiontoken']
		);

		if (is_wp_error($storage)) return $updraftplus->log_wp_error($storage, false, true);

		$whoweare = $config['whoweare'];
		$whoweare_key = $config['key'];
		$whoweare_keys = substr($whoweare_key, 0, 3);
		
		if (is_a($storage, 'UpdraftPlus_S3_Compat') && !class_exists('XMLWriter')) {
			$this->log('The required XMLWriter PHP module is not installed');
			$this->log(sprintf(__('The required %s PHP module is not installed - ask your web hosting company to enable it', 'updraftplus'), 'XMLWriter'), 'error');
			return false;
		}

		$bucket_name = untrailingslashit($config['path']);
		$bucket_path = "";
		$orig_bucket_name = $bucket_name;

		if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
			$bucket_name = $bmatches[1];
			$bucket_path = $bmatches[2]."/";
		}

		list($storage, $config, $bucket_exists, $region) = $this->get_bucket_access($storage, $config, $bucket_name, $bucket_path);

		// See if we can detect the region (which implies the bucket exists and is ours), or if not create it
		if ($bucket_exists) {

			$updraft_dir = trailingslashit($updraftplus->backups_dir_location());

			foreach ($backup_array as $key => $file) {

				// We upload in 5MB chunks to allow more efficient resuming and hence uploading of larger files
				// N.B.: 5MB is Amazon's minimum. So don't go lower or you'll break it.
				$fullpath = $updraft_dir.$file;
				$orig_file_size = filesize($fullpath);
				
				if (!file_exists($fullpath)) {
					$this->log("File not found: $file: $whoweare: ");
					$this->log("$file: ".sprintf(__('Error: %s', 'updraftplus'), __('File not found', 'updraftplus')), 'error');
					continue;
				}

				if (isset($config['quota']) && method_exists($this, 's3_get_quota_info')) {
					$quota_used = $this->s3_get_quota_info('numeric', $config['quota']);
					if (false === $quota_used) {
						$this->log("Quota usage: count failed");
					} else {
						$this->quota_used = $quota_used;
						if ($config['quota'] - $this->quota_used < $orig_file_size) {
							if (method_exists($this, 's3_out_of_quota')) call_user_func(array($this, 's3_out_of_quota'), $config['quota'], $this->quota_used, $orig_file_size);
							continue;
						} else {
							// We don't need to log this always - the s3_out_of_quota method will do its own logging
							$this->log("Quota is available: used=$quota_used (".round($quota_used/1048576, 1)." MB), total=".$config['quota']." (".round($config['quota']/1048576, 1)." MB), needed=$orig_file_size (".round($orig_file_size/1048576, 1)." MB)");
						}
					}
				}

				$chunks = floor($orig_file_size / 5242880);
				// There will be a remnant unless the file size was exactly on a 5MB boundary
				if ($orig_file_size % 5242880 > 0) $chunks++;
				$hash = md5($file);

				$extra = $this->provider_has_regions ? " ($region)" : '';
				
				$this->log("upload{$extra}: $file (chunks: $chunks) -> $whoweare_key://$bucket_name/$bucket_path$file");

				$filepath = $bucket_path.$file;

				// This is extra code for the 1-chunk case, but less overhead (no bothering with job data)
				if ($chunks < 2) {
					$storage->setExceptions(true);
					try {
						if (!$storage->putObjectFile($fullpath, $bucket_name, $filepath, 'private', array(), array(), apply_filters('updraft_'.$whoweare_key.'_storageclass', 'STANDARD', $storage, $config))) {
							$this->log("regular upload: failed ($fullpath)");
							$this->log("$file: ".sprintf(__('%s Error: Failed to upload', 'updraftplus'), $whoweare), 'error');
						} else {
							$this->quota_used += $orig_file_size;
							if (method_exists($this, 's3_record_quota_info')) $this->s3_record_quota_info($this->quota_used, $config['quota']);
							$extra_log = '';
							if (method_exists($this, 's3_get_quota_info')) {
								$extra_log = ', quota used now: '.round($this->quota_used / 1048576, 1).' MB';
							}
							$this->log("regular upload: success$extra_log");
							$updraftplus->uploaded_file($file);
						}
					} catch (Exception $e) {
						$this->log("$file: ".sprintf(__('%s Error: Failed to upload', 'updraftplus'), $whoweare).": ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile());
						$this->log("$file: ".sprintf(__('%s Error: Failed to upload', 'updraftplus'), $whoweare), 'error');
					}
					$storage->setExceptions(false);
				} else {

					// Retrieve the upload ID
					$upload_id = $this->jobdata_get($hash.'_uid', null, "upd_${whoweare_keys}_${hash}_uid");
					if (empty($upload_id)) {
						$storage->setExceptions(true);
						try {
							$upload_id = $storage->initiateMultipartUpload($bucket_name, $filepath, 'private', array(), array(), apply_filters('updraft_'.$whoweare_key.'_storageclass', 'STANDARD', $storage, $config));
						} catch (Exception $e) {
						
							$this->log("exception (".get_class($e).") whilst trying initiateMultipartUpload: ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
							
							$upload_id = false;
							
						}
						$storage->setExceptions(false);

						if (empty($upload_id)) {
							$this->log("upload: failed: could not get uploadId for multipart upload ($filepath)");
							$this->log(sprintf(__("%s upload: getting uploadID for multipart upload failed - see log file for more details", 'updraftplus'), $whoweare), 'error');
							continue;
						} else {
							$this->log("chunked upload: got multipart ID: $upload_id");
							$this->jobdata_set($hash.'_uid', $upload_id);
						}
					} else {
						$this->log("chunked upload: retrieved previously obtained multipart ID: $upload_id");
					}

					$successes = 0;
					$etags = array();
					for ($i = 1; $i <= $chunks; $i++) {
						$etag = $this->jobdata_get($hash.'_etag_'.$i, null, "ud_${whoweare_keys}_${hash}_e$i");
						if (strlen($etag) > 0) {
							$this->log("chunk $i: was already completed (etag: $etag)");
							$successes++;
							array_push($etags, $etag);
						} else {
							// Sanity check: we've seen a case where an overlap was truncating the file from underneath us
							if (filesize($fullpath) < $orig_file_size) {
								$this->log("error: $key: chunk $i: file was truncated underneath us (orig_size=$orig_file_size, now_size=".filesize($fullpath).")");
								$this->log(sprintf(__('error: file %s was shortened unexpectedly', 'updraftplus'), $fullpath), 'error');
							}
							$etag = $storage->uploadPart($bucket_name, $filepath, $upload_id, $fullpath, $i);
							if (false !== $etag && is_string($etag)) {
								$updraftplus->record_uploaded_chunk(round(100*$i/$chunks, 1), "$i, $etag", $fullpath);
								array_push($etags, $etag);
								$this->jobdata_set($hash.'_etag_'.$i, $etag);
								$successes++;
							} else {
								$this->log("chunk $i: upload failed");
								$this->log(sprintf(__("chunk %s: upload failed", 'updraftplus'), $i), 'error');
							}
						}
					}
					if ($successes >= $chunks) {
						$this->log("upload: all chunks uploaded; will now instruct $whoweare to re-assemble");

						$storage->setExceptions(true);
						try {
							if ($storage->completeMultipartUpload($bucket_name, $filepath, $upload_id, $etags)) {
								$this->log("upload ($key): re-assembly succeeded");
								$updraftplus->uploaded_file($file);
								$this->quota_used += $orig_file_size;
								if (method_exists($this, 's3_record_quota_info')) $this->s3_record_quota_info($this->quota_used, $config['quota']);
							} else {
								$this->log("upload ($key): re-assembly failed ($file)");
								$this->log(sprintf(__('upload (%s): re-assembly failed (see log for more details)', 'updraftplus'), $key), 'error');
							}
						} catch (Exception $e) {
							$this->log("re-assembly error ($key): ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
							$this->log($e->getMessage().": ".sprintf(__('%s re-assembly error (%s): (see log file for more)', 'updraftplus'), $whoweare, $e->getMessage()), 'error');
						}
						// Remember to unset, as the deletion code later reuses the object
						$storage->setExceptions(false);
					} else {
						$this->log("upload: upload was not completely successful on this run");
					}
				}
			}
			
			// Allows counting of the final quota accurately
			if (method_exists($this, 's3_prune_retained_backups_finished')) {
				add_action('updraftplus_prune_retained_backups_finished', array($this, 's3_prune_retained_backups_finished'));
			}
			
			return array('storage' => $storage, 's3_orig_bucket_name' => $orig_bucket_name);
		} else {
		
			$extra_text = empty($this->s3_exception) ? '' : ' '.$this->s3_exception->getMessage().' (line: '.$this->s3_exception->getLine().', file: '.$this->s3_exception->getFile().')';
			$extra_text_short = empty($this->s3_exception) ? '' : ' '.$this->s3_exception->getMessage();
		
			$this->log("Error: Failed to access bucket $bucket_name.".$extra_text);
			$this->log(sprintf(__('Error: Failed to access bucket %s. Check your permissions and credentials.', 'updraftplus'), $bucket_name).' (1)'.$extra_text_short, 'error');
		}
	}
	
	public function listfiles($match = 'backup_') {
		$config = $this->get_config();
		return $this->listfiles_with_path($config['path'], $match);
	}
	
	protected function possibly_wait_for_bucket_or_user($config, $storage) {

		$bucket_name = untrailingslashit($config['path']);

		if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
			$bucket_name = $bmatches[1];
		}

		if (!empty($config['is_new_bucket'])) {
			if (method_exists($storage, 'waitForBucket')) {
				$storage->setExceptions(true);
				try {
					$storage->waitForBucket($bucket_name);
				} catch (Exception $e) {
					// This seems to often happen - we get a 403 on a newly created user/bucket pair, even though the bucket was already waited for by the creator
					// We could just sleep() - a sleep(5) seems to do it. However, given that it's a new bucket, that's unnecessary.
					$storage->setExceptions(false);
					return array();
				}
				$storage->setExceptions(false);
			} else {
				sleep(4);
			}
		} elseif (!empty($config['is_new_user'])) {
			// A crude waiter, because the AWS toolkit does not have one for IAM propagation - basically, loop around a few times whilst the access attempt still fails
			$attempt_flag = 0;
			while ($attempt_flag < 5) {

				$attempt_flag++;
				if (@$storage->getBucketLocation($bucket_name)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$attempt_flag = 100;
				} else {
					if (empty($config['sessiontoken'])) $config['sessiontoken'] = null;
					sleep($attempt_flag*1.5 + 1);

					$sse = !empty($config['server_side_encryption']);
					
					// Get the bucket object again... because, for some reason, the AWS PHP SDK (at least on the current version we're using, March 2016) calculates an incorrect signature on subsequent attempts
					$this->set_storage(null);

					$storage = $this->getS3(
						$config['accesskey'],
						$config['secretkey'],
						UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
						UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
						UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
						null,
						$sse,
						$config['sessiontoken']
					);

					if (is_wp_error($storage)) return $storage;
					if (!is_a($storage, 'UpdraftPlus_S3') && !is_a($storage, 'UpdraftPlus_S3_Compat')) return new WP_Error('no_s3object', 'Failed to gain access to '.$config['whoweare']);
					
				}
			}
		}
		
		return $storage;
	}
	
	/**
	 * The purpose of splitting this into a separate method, is to also allow listing with a different path
	 *
	 * @param  String  $path 			   Path to check
	 * @param  String  $match 			   THe match for idetifying the bucket name
	 * @param  Boolean $include_subfolders Check if list file need to include sub folders
	 * @return Array
	 */
	public function listfiles_with_path($path, $match = 'backup_', $include_subfolders = false) {
		
		$bucket_name = untrailingslashit($path);
		$bucket_path = '';

		if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
			$bucket_name = $bmatches[1];
			$bucket_path = trailingslashit($bmatches[2]);
		}

		$config = $this->get_config();
		
		$sse = empty($config['server_side_encryption']) ? false : true;
		if (empty($config['sessiontoken'])) $config['sessiontoken'] = null;
		
		$storage = $this->getS3(
			$config['accesskey'],
			$config['secretkey'],
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
			null,
			$sse,
			$config['sessiontoken']
		);

		if (is_wp_error($storage)) return $storage;
		if (!is_a($storage, 'UpdraftPlus_S3') && !is_a($storage, 'UpdraftPlus_S3_Compat')) return new WP_Error('no_s3object', 'Failed to gain access to '.$config['whoweare']);
		
		$storage = $this->possibly_wait_for_bucket_or_user($config, $storage);
		if (!is_a($storage, 'UpdraftPlus_S3') && !is_a($storage, 'UpdraftPlus_S3_Compat')) return $storage;
		
		list($storage, $config, $bucket_exists, $region) = $this->get_bucket_access($storage, $config, $bucket_name, $bucket_path);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- The value is passed back from get_bucket_access

		$bucket = $storage->getBucket($bucket_name, $bucket_path.$match);

		if (!is_array($bucket)) return array();

		$results = array();

		foreach ($bucket as $key => $object) {
			if (!is_array($object) || empty($object['name'])) continue;
			if (isset($object['size']) && 0 == $object['size']) continue;

			if ($bucket_path) {
				if (0 !== strpos($object['name'], $bucket_path)) continue;
				$object['name'] = substr($object['name'], strlen($bucket_path));
			} else {
				if (!$include_subfolders && false !== strpos($object['name'], '/')) continue;
			}

			$result = array('name' => $object['name']);
			if (isset($object['size'])) $result['size'] = (int) $object['size'];
			unset($bucket[$key]);
			$results[] = $result;
		}

		return $results;

	}
	
	/**
	 * Delete a single file from the service using S3
	 *
	 * @param Array|String $files    - array of file names to delete
	 * @param Array        $s3arr    - s3 service object and container details
	 * @param Array        $sizeinfo - size of files to delete, used for quota calculation
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete($files, $s3arr = false, $sizeinfo = array()) {

		global $updraftplus;
		if (is_string($files)) $files = array($files);

		$config = $this->get_config();
		$sse = empty($config['server_side_encryption']) ? false : true;
		if (empty($config['sessiontoken'])) $config['sessiontoken'] = null;

		if ($s3arr) {
			$storage = $s3arr['storage'];
			$orig_bucket_name = $s3arr['s3_orig_bucket_name'];
		} else {
			$storage = $this->getS3(
				$config['accesskey'],
				$config['secretkey'],
				UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
				UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
				UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
				null,
				$sse,
				$config['sessiontoken']
			);

			if (is_wp_error($storage)) return $updraftplus->log_wp_error($storage, false, false);

			$bucket_name = untrailingslashit($config['path']);
			$orig_bucket_name = $bucket_name;

			if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
				$bucket_name = $bmatches[1];
				$bucket_path = $bmatches[2]."/";
			} else {
				$bucket_path = '';
			}
			
			list($storage, $config, $bucket_exists, $region) = $this->get_bucket_access($storage, $config, $bucket_name, $bucket_path);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- The value is passed back from get_bucket_access

			if (!$bucket_exists) {
				$this->log("Error: Failed to access bucket $bucket_name. Check your permissions and credentials.");
				$this->log(sprintf(__('Error: Failed to access bucket %s. Check your permissions and credentials.', 'updraftplus'), $bucket_name), 'error');
				return 'container_access_error';
			}
		}

		$ret = true;

		foreach ($files as $i => $file) {

			if (preg_match("#^([^/]+)/(.*)$#", $orig_bucket_name, $bmatches)) {
				$s3_bucket=$bmatches[1];
				$s3_uri = $bmatches[2]."/".$file;
			} else {
				$s3_bucket = $orig_bucket_name;
				$s3_uri = $file;
			}
			$this->log("Delete remote: bucket=$s3_bucket, URI=$s3_uri");

			$storage->setExceptions(true);
			try {
				if (!$storage->deleteObject($s3_bucket, $s3_uri)) {
					$this->log("Delete failed");
				} elseif (null !== $this->quota_used && !empty($sizeinfo[$i]) && isset($config['quota']) && method_exists($this, 's3_record_quota_info')) {
					$this->quota_used -= $sizeinfo[$i];
					$this->s3_record_quota_info($this->quota_used, $config['quota']);
				}
			} catch (Exception $e) {
				$this->log("delete failed (".get_class($e)."): ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
				$storage->setExceptions(false);
				$ret = 'file_delete_error';
			}
			$storage->setExceptions(false);

		}

		return $ret;

	}

	/**
	 * Download a file from the remote storage
	 *
	 * @param string $file The specific file to be downloaded
	 * @return void
	 */
	public function download($file) {

		global $updraftplus;

		$config = $this->get_config();
		$sse = empty($config['server_side_encryption']) ? false : true;
		if (empty($config['sessiontoken'])) $config['sessiontoken'] = null;
		
		$storage = $this->getS3(
			$config['accesskey'],
			$config['secretkey'],
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
			UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
			null,
			$sse,
			$config['sessiontoken']
		);
		if (is_wp_error($storage)) return $updraftplus->log_wp_error($storage, false, true);

		$bucket_name = untrailingslashit($config['path']);
		$bucket_path = "";

		if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
			$bucket_name = $bmatches[1];
			$bucket_path = $bmatches[2]."/";
		}

		
		list($storage, $config, $bucket_exists, $region) = $this->get_bucket_access($storage, $config, $bucket_name, $bucket_path);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- The value is passed back from get_bucket_access

		if ($bucket_exists) {

			$file_info = $this->listfiles($file);
			
			if (is_array($file_info)) {
				foreach ($file_info as $finfo) {
					if ($finfo['name'] == $file) {
						$file_size = $finfo['size'];
						break;
					}
				}
			}
			
			if (!isset($file_size)) {
				$this->log("Error: Failed to download $file. Check your permissions and credentials. Retrieved data: ".serialize($file_info));
				$this->log(sprintf(__('Error: Failed to download %s. Check your permissions and credentials.', 'updraftplus'), $file), 'error');
				return false;
			}
			
			return $updraftplus->chunked_download($file, $this, $file_size, true, $storage, $this->download_chunk_size);
			
			/*
			// The code before we switched to chunked downloads. Unfortunately the version of the AWS SDK we have to use for PHP 5.3 compatibility doesn't have callbacks, which makes it possible for multiple downloaders to start at once and over-write each-other.
			$whoweare = $config['whoweare'];
			$fullpath = $updraftplus->backups_dir_location().'/'.$file;
			if (!$storage->getObject($bucket_name, $bucket_path.$file, $fullpath, true)) {
				$updraftplus->log("$whoweare Error: Failed to download $file. Check your permissions and credentials.");
				$updraftplus->log(sprintf(__('%s Error: Failed to download %s. Check your permissions and credentials.','updraftplus'),$whoweare, $file), 'error');
				return false;
			}
			*/
			
		} else {
			$this->log("Error: Failed to access bucket $bucket_name. Check your permissions and credentials.");
			$this->log(sprintf(__('Error: Failed to access bucket %s. Check your permissions and credentials.', 'updraftplus'), $bucket_name), 'error');
			return false;
		}
		return true;

	}
	
	public function chunked_download($file, $headers, $storage, $fh) {

		$resume = false;
		$config = $this->get_config();
		$bucket_name = untrailingslashit($config['path']);
		$bucket_path = "";

		if (preg_match("#^([^/]+)/(.*)$#", $bucket_name, $bmatches)) {
			$bucket_name = $bmatches[1];
			$bucket_path = $bmatches[2]."/";
		}
	
		if (is_array($headers) && !empty($headers['Range']) && preg_match('/bytes=(\d+)-(\d+)$/', $headers['Range'], $matches)) {
			$resume = $headers['Range'];
		}
		
		if (!$storage->getObject($bucket_name, $bucket_path.$file, $fh, $resume)) {
			$this->log("Error: Failed to download $file. Check your permissions and credentials.");
			$this->log(sprintf(__('Error: Failed to download %s. Check your permissions and credentials.', 'updraftplus'), $file), 'error');
			return false;
		}

		// This instructs the caller to look at the file pointer's position (i.e. ftell($fh)) to work out how many bytes were written.
		return true;
	
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		$this->get_pre_configuration_template_engine('s3', 'S3', 'Amazon S3', 'AWS', 'https://aws.amazon.com/console/', '<img src="'. UPDRAFTPLUS_URL .'/images/aws_logo.png" alt="Amazon Web Services">');
	}

	/**
	 * Get pre configuration template engine for remote method which is S3 Compatible
	 *
	 * @param String $key             Remote storage method key which is unique
	 * @param String $whoweare_short  Remote storage method short name which is prefix of field label generally
	 * @param String $whoweare_long   Remote storage method long name which is generally used in instructions
	 * @param String $console_descrip Remote storage method console description. It is used console link text like "from your %s console"
	 * @param String $console_url     Remote storage method console url. It is used for get credential instruction
	 * @param String $opening_html    HTML to appear first inside the container
	 */
	protected function get_pre_configuration_template_engine($key, $whoweare_short, $whoweare_long, $console_descrip, $console_url, $opening_html = '') {
		$classes = $this->get_css_classes(false);
		?>
		<tr class="<?php echo $classes . ' ' . $whoweare_short . '_pre_config_container';?>">
			<td colspan="2">
				<?php echo $opening_html.'<br>'; ?>
				<?php

					global $updraftplus_admin;

					$use_s3_class = $this->indicate_s3_class();

					if ('UpdraftPlus_S3_Compat' == $use_s3_class && !class_exists('XMLWriter')) {
					$updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '. sprintf(__("Your web server's PHP installation does not included a required module (%s). Please contact your web hosting provider's support and ask for them to enable it.", 'updraftplus'), 'XMLWriter'));
					}

					if (!class_exists('SimpleXMLElement')) {
					$updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '.sprintf(__("Your web server's PHP installation does not included a required module (%s). Please contact your web hosting provider's support.", 'updraftplus'), 'SimpleXMLElement').' '.sprintf(__("UpdraftPlus's %s module <strong>requires</strong> %s. Please do not file any support requests; there is no alternative.", 'updraftplus'), $whoweare_long, 'SimpleXMLElement'), $key);
					}
					$updraftplus_admin->curl_check($whoweare_long, true, $key);
				?>
				<br>
				<p>
					<?php if ($console_url) echo sprintf(__('Get your access key and secret key from your <a href="%s">%s console</a>, then pick a (globally unique - all %s users) bucket name (letters and numbers) (and optionally a path) to use for storage. This bucket will be created for you if it does not already exist.', 'updraftplus'), $console_url, $console_descrip, $whoweare_long);?>

					<a href="<?php echo apply_filters("updraftplus_com_link", "https://updraftplus.com/faqs/i-get-ssl-certificate-errors-when-backing-up-andor-restoring/");?>" target="_blank"><?php _e('If you see errors about SSL certificates, then please go here for help.', 'updraftplus');?></a>

					<a href="<?php echo apply_filters("updraftplus_com_link", "https://updraftplus.com/faq-category/amazon-s3/");?>" target="_blank"><?php if ('s3' == $key) echo sprintf(__('Other %s FAQs.', 'updraftplus'), 'S3');?></a>
				</p>
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
		// White: https://d36cz9buwru1tt.cloudfront.net/Powered-by-Amazon-Web-Services.jpg
		// Black: https://awsmedia.s3.amazonaws.com/AWS_logo_poweredby_black_127px.png
		return $this->get_configuration_template_engine('s3', 'S3', 'Amazon S3', 'AWS', 'https://aws.amazon.com/console/', '<img src="'. UPDRAFTPLUS_URL .'/images/aws_logo.png" alt="Amazon Web Services">');
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		return apply_filters('updraftplus_options_s3_options', $opts);
	}
	
	/**
	 * Get configuration template engine for remote method which is S3 Compatible
	 *
	 * @param String $key             Remote storage method key which is unique
	 * @param String $whoweare_short  Remote storage method short name which is prefix of field label generally
	 * @param String $whoweare_long   Remote storage method long name which is generally used in instructions
	 * @param String $console_descrip Remote storage method console description. It is used console link text like "from your %s console"
	 * @param String $console_url     Remote storage method console url. It is used for get credential instruction
	 * @param String $img_html        Image html tag
	 *
	 * @return String $template_str handlebars template string
	 */
	public function get_configuration_template_engine($key, $whoweare_short, $whoweare_long, $console_descrip, $console_url, $img_html = '') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $whoweare_long, $console_descrip, $console_url, $img_html unused
		global $updraftplus;
		ob_start();
		$classes = $this->get_css_classes();
		$template_str = '';
		
		if ('s3' == $key && version_compare(PHP_VERSION, '5.3.3', '>=') && class_exists('UpdraftPlus_Addon_S3_Enhanced')) {
		?>
			<tr class="<?php echo $classes;?>">
				<td colspan="2">
				<?php
					echo apply_filters('updraft_s3_apikeysetting', '<a href="'.$updraftplus->get_url('premium').'" target="_blank"><em>'.__('To create a new IAM sub-user and access key that has access only to this bucket, upgrade to Premium.', 'updraftplus').'</em></a>');
				?>
				</td>
			</tr>
		<?php
		}
		?>

		<tr class="<?php echo $classes;?>">
			<th><?php echo sprintf(__('%s access key', 'updraftplus'), $whoweare_short);?>:</th>
			<td><input class="updraft_input--wide" data-updraft_settings_test="accesskey" type="text" autocomplete="off" <?php $this->output_settings_field_name_and_id('accesskey');?> value="{{accesskey}}" /></td>
		</tr>
		<tr class="<?php echo $classes;?>">
			<th><?php echo sprintf(__('%s secret key', 'updraftplus'), $whoweare_short);?>:</th>
			<td><input class="updraft_input--wide" data-updraft_settings_test="secretkey" type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" autocomplete="off" <?php $this->output_settings_field_name_and_id('secretkey');?> value="{{secretkey}}" /></td>
		</tr>
		<tr class="<?php echo $classes;?>">
			<th><?php echo sprintf(__('%s location', 'updraftplus'), $whoweare_short);?>:</th>
			<td><?php echo $key; ?>://<input class="updraft_input--wide" data-updraft_settings_test="path" title="<?php echo htmlspecialchars(__('Enter only a bucket name or a bucket and path. Examples: mybucket, mybucket/mypath', 'updraftplus')); ?>" type="text" <?php $this->output_settings_field_name_and_id('path');?> value="{{path}}" /></td>
		</tr>
		<?php
		$template_str .= ob_get_clean();
		$template_str .= $this->get_partial_configuration_template_for_endpoint();
		$template_str .= apply_filters('updraft_'.$key.'_extra_storage_options_configuration_template', '', $this);
		$template_str .= $this->get_test_button_html($whoweare_short);
		return $template_str;
	}

	/**
	 * Get handlebar partial template string for endpoint of s3 compatible remote storage method. Other child class can extend it.
	 *
	 * @return String - the partial template
	 */
	protected function get_partial_configuration_template_for_endpoint() {
		return '';
	}
	
	/**
	 * Look at the config, and decide whether or not to call self::use_dns_bucket_name()
	 *
	 * @param Object $storage S3 Name
	 * @param Array	 $config  an array of specific options for particular S3 remote storage module
	 *
	 * @return Boolean - looking use_dns_bucket_name(), this should apparently always be true
	 */
	protected function maybe_use_dns_bucket_name($storage, $config) {
		if ('s3' === $config['key']) return $this->use_dns_bucket_name($storage, '');
		return true;
	}
	
	/**
	 * This is not pretty, but is the simplest way to accomplish the task within the pre-existing structure (no need to re-invent the wheel of code with corner-cases debugged over years)
	 *
	 * @param  object $storage S3 Name
	 * @param  String $bucket  S3 Bucket
	 * @return Boolean
	 */
	public function use_dns_bucket_name($storage, $bucket) {
		return is_a($storage, 'UpdraftPlus_S3_Compat') ? true : $storage->useDNSBucketName(true, $bucket);
	}
	
	/**
	 * Acts as a WordPress options filter
	 *
	 * @param Array $settings - pre-filtered settings
	 *
	 * @return Array filtered settings
	 */
	public function options_filter($settings) {
		if (is_array($settings) && !empty($settings['version']) && !empty($settings['settings'])) {
			foreach ($settings['settings'] as $instance_id => $instance_settings) {
				if (!empty($instance_settings['path'])) {
					$settings['settings'][$instance_id]['path'] = trim($instance_settings['path'], "/ \t\n\r\0\x0B");
				}
			}
		}
		return $settings;
	}
	
	/**
	 * This method contains some repeated code. After getting an S3 object, it's time to see if we can access that bucket - either immediately, or via creating it, etc.
	 *
	 * @param Object $storage S3 name
	 * @param Array  $config  array of config details; if the provider does not have the concept of regions, then the key 'endpoint' is required to be set
	 * @param String $bucket  S3 Bucket
	 * @param String $path    S3 Path
	 *
	 * @return Array - N.B. May contain updated versions of $storage and $config
	 */
	private function get_bucket_access($storage, $config, $bucket, $path) {
	
		$bucket_exists = false;
		
		if ($this->provider_has_regions) {
		
			$storage->setExceptions(true);
			
			if ('dreamobjects' == $config['key']) {
				$endpoint = isset($config['endpoint']) ? $config['endpoint'] : '';
				$this->set_region($storage, $endpoint);
			}
			
			try {
				$region = @$storage->getBucketLocation($bucket);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// We want to distinguish between an empty region (null), and an exception or missing bucket (false)
				if (empty($region) && false !== $region) $region = null;
			} catch (Exception $e) {
				$region = false;
		
				// On this 'first try', we trap this particular condition. So, whatever S3 network call it happens on, we'll eventually get it here on the resumption.
				if (false !== strpos($e->getMessage(), 'The provided token has expired')) {
				
					$this->log($e->getMessage().": Requesting new credentials");
							
					$new_config = $this->get_config(true);

					if (empty($new_config['sessiontoken'])) $new_config['sessiontoken'] = null;
		
					if (!empty($new_config['accesskey'])) {
			
						$new_storage = $this->getS3(
							$new_config['accesskey'],
							$new_config['secretkey'],
							UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts'),
							UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify'),
							UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl'),
							null,
							!empty($new_config['server_side_encryption']),
							$new_config['sessiontoken']
						);
						
						if (!is_wp_error($new_storage)) {
							// Try again
							try {
								$region = @$new_storage->getBucketLocation($bucket);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
								// We want to distinguish between an empty region (null), and an exception or missing bucket (false)
								if (empty($region) && false !== $region) $region = null;
								// Worked this time; update the passed-in information
								$storage = $new_storage;
								$config = $new_config;
							} catch (Exception $e) {
								$region = false;
							}
						}
					}
				}
				
			}
			$storage->setExceptions(false);
		} else {
			$region = false;
			$this->set_region($storage, $config['endpoint'], $bucket);
		}
		
		// See if we can detect the region (which implies the bucket exists and is ours), or if not create it
		if (!$this->provider_has_regions || false === $region) {
			$storage->setExceptions(true);
			try {
				if (@$storage->putBucket($bucket, 'private')) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$bucket_exists = true;
				}
				
			} catch (Exception $e) {
				$this->s3_exception = $e;
				try {
					if ($this->maybe_use_dns_bucket_name($storage, $config) && false !== @$storage->getBucket($bucket, $path, null, 1)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						$bucket_exists = true;
					}
				} catch (Exception $e) {

					// We don't put this in a separate catch block which names the exception, since we need to remain compatible with PHP 5.2
					if (is_a($storage, 'UpdraftPlus_S3_Compat') && is_a($e, 'Aws\S3\Exception\S3Exception')) {
						$xml = $e->getResponse()->xml();

						if (!empty($xml->Code) && 'AuthorizationHeaderMalformed' == $xml->Code && !empty($xml->Region)) {

							$this->set_region($storage, $xml->Region);
							$storage->setExceptions(false);
							
							if (false !== @$storage->getBucket($bucket, $path, null, 1)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
								$bucket_exists = true;
							}
							
						} else {
							$this->s3_exception = $e;
						}
					} else {
						$this->s3_exception = $e;
					}
				}
			
			}
			$storage->setExceptions(false);
			
		} else {
			$bucket_exists = true;
		}
		
		// For a region-less S3 system, we set this to true so that we can carry on trying anyway, since the behaviour of different S3-compatible systems can vary. e.g. DigitalOcean spaces API keys allow you to create a bucket.
		if (!$this->provider_has_regions) {
			$bucket_exists = true;
		} elseif ($bucket_exists) {
			if ('s3' == $config['key'] || 'updraftvault' == $config['key']) {
				$this->set_region($storage, $region, $bucket);
			} elseif (!empty($region)) {
				// N.B. region non-empty here implies that it's dreamobjects; but in that case, we already called set_region earlier. So, this is now commented (May 2021)
				// if (!$endpoint) $this->set_region($storage, $endpoint, $bucket);
			}
		}
		
		return array($storage, $config, $bucket_exists, $region);
		
	}

	/**
	 * Perform a test of user-supplied credentials, and echo the result
	 *
	 * @param Array $posted_settings - settings to test
	 */
	public function credentials_test($posted_settings) {

		if (empty($posted_settings['accesskey'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('API key', 'updraftplus'));
			return;
		}
		if (empty($posted_settings['secretkey'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('API secret', 'updraftplus'));
			return;
		}

		$key = $posted_settings['accesskey'];
		$secret = $posted_settings['secretkey'];
		$path = $posted_settings['path'];
		$useservercerts = isset($posted_settings['useservercerts']) ? absint($posted_settings['useservercerts']) : 0;
		$disableverify = isset($posted_settings['disableverify']) ? absint($posted_settings['disableverify']) : 0;
		$nossl = isset($posted_settings['nossl']) ? absint($posted_settings['nossl']) : 0;
		$endpoint = isset($posted_settings['endpoint']) ? $posted_settings['endpoint'] : '';
		$sse = empty($posted_settings['server_side_encryption']) ? false : true;

		if (preg_match("#^/*([^/]+)/(.*)$#", $path, $bmatches)) {
			$bucket = $bmatches[1];
			$path = trailingslashit($bmatches[2]);
		} else {
			$bucket = $path;
			$path = "";
		}

		if (empty($bucket)) {
			_e("Failure: No bucket details were given.", 'updraftplus');
			return;
		}
		
		if (!$this->provider_has_regions && '' == $endpoint) {
			_e("Failure: No endpoint details were given.", 'updraftplus');
			return;
		}
		
		$config = $this->get_config();
		
		if ('' !== $endpoint) $config['endpoint'] = $endpoint;
		
		$whoweare = $config['whoweare'];
		
		$session_token = empty($config['sessiontoken']) ? null : $config['sessiontoken'];
		
		$storage = $this->getS3($key, $secret, $useservercerts, $disableverify, $nossl, null, $sse, $session_token);
		if (is_wp_error($storage)) {
			foreach ($storage->get_error_messages() as $msg) {
				echo $msg."\n";
			}
			return;
		}

		if (method_exists($storage, 'useDNSBucketName')) {
			if (!empty($posted_settings['bucket_access_style']) && 'virtual_host_style' === $posted_settings['bucket_access_style']) {
				// due to the merge of S3-generic bucket access style MR on March 2021, if virtual-host bucket access style is selected, connecting to an amazonaws bucket location where the user doesn't have an access to it will throw an S3 InvalidRequest exception. It requires the signature to be set to version 4
				if (!is_a($storage, 'UpdraftPlus_S3_Compat') && preg_match('/\.amazonaws\.com$/i', $endpoint)) {
					$this->use_v4 = true;
					$storage->setSignatureVersion('v4');
				}
				$storage->useDNSBucketName(true, $bucket);
			} else {
				$storage->useDNSBucketName(false, $bucket);
			}
		}

		list($storage, $config, $bucket_exists, $region) = $this->get_bucket_access($storage, $config, $bucket, $path);

		$bucket_verb = '';
		if ($this->provider_has_regions && $region) {
			if ('s3' == $config['key']) {
				$bucket_verb = __('Region', 'updraftplus').": $region: ";
			}
		}

		if (empty($bucket_exists)) {
		
			printf(__("Failure: We could not successfully access or create such a bucket. Please check your access credentials, and if those are correct then try another bucket name (as another %s user may already have taken your name).", 'updraftplus'), $whoweare);
			
			if (!empty($this->s3_exception)) echo "\n\n".sprintf(__('The error reported by %s was:', 'updraftplus'), $whoweare).' '.$this->s3_exception;
			
			if ('s3' == $config['key'] && 'AK' != substr($key, 0, 2)) echo "\n\n".sprintf(__('The AWS access key looks to be wrong (valid %s access keys begin with "AK")', 'updraftplus'), $whoweare);
		
		} else {
		
			$try_file = md5(rand());

			$storage->setExceptions(true);
			try {
				if (!$storage->putObjectString($try_file, $bucket, $path.$try_file)) {
					echo __('Failure', 'updraftplus').": ${bucket_verb}".__('We successfully accessed the bucket, but the attempt to create a file in it failed.', 'updraftplus');
				} else {
					echo __('Success', 'updraftplus').": ${bucket_verb}".__('We accessed the bucket, and were able to create files within it.', 'updraftplus').' ';
					$comm_with = ('' !== $endpoint) ? $endpoint : $config['whoweare_long'];
					if ($storage->getuseSSL()) {
						echo sprintf(__('The communication with %s was encrypted.', 'updraftplus'), $comm_with);
					} else {
						echo sprintf(__('The communication with %s was not encrypted.', 'updraftplus'), $comm_with);
					}
					$create_success = true;
				}
			} catch (Exception $e) {
				echo __('Failure', 'updraftplus').": ${bucket_verb}".__('We successfully accessed the bucket, but the attempt to create a file in it failed.', 'updraftplus').' '.__('Please check your access credentials.', 'updraftplus').' ('.$e->getMessage().')';
			}

			if (!empty($create_success)) {
				try {
					@$storage->deleteObject($bucket, $path.$try_file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} catch (Exception $e) {
					echo ' '.__('Delete failed:', 'updraftplus').' '.$e->getMessage();
				}
			}

		}

	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && isset($opts['accesskey']) && '' != $opts['accesskey'] && isset($opts['secretkey'])) return true;
		return false;
	}
}
