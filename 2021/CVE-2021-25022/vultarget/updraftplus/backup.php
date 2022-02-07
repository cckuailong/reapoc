<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_PclZip')) require_once(UPDRAFTPLUS_DIR.'/includes/class-zip.php');

/**
 * This file contains code that is only needed/loaded when a backup is running
 */
class UpdraftPlus_Backup {

	private $index = 0;

	private $manifest_path;

	private $zipfiles_added;

	private $zipfiles_added_thisrun = 0;

	public $zipfiles_dirbatched;

	public $zipfiles_batched;

	public $zipfiles_skipped_notaltered;

	private $zip_split_every = 419430400; // 400MB

	private $zip_last_ratio = 1;

	private $whichone;

	private $zip_basename = '';

	private $backup_basename = '';

	private $zipfiles_lastwritetime;

	// 0 = unknown; false = failed
	public $binzip = 0;

	private $dbhandle;

	private $dbhandle_isgz;

	// Array of entities => times
	private $altered_since = -1;

	// Time for the current entity
	private $makezip_if_altered_since = -1;

	private $excluded_extensions = false;

	private $use_zip_object = 'UpdraftPlus_ZipArchive';

	public $debug = false;

	public $updraft_dir;

	private $site_name;

	private $wpdb_obj;

	private $job_file_entities = array();

	private $first_run = 0;

	// Record of zip files created
	private $backup_files_array = array();

	// Used when deciding to use the 'store' or 'deflate' zip storage method
	private $extensions_to_not_compress = array();

	// Append to this any skipped tables
	private $skipped_tables;
	
	// When initialised, a boolean
	public $last_storage_instance;
	
	// The absolute upper limit that will be considered for a zip batch (in bytes)
	private $zip_batch_ceiling;

	private $backup_excluded_patterns = array();
	
	// Bytes of uncompressed data written since last open
	private $db_current_raw_bytes = 0;
	
	private $table_prefix;

	private $table_prefix_raw;
	
	private $many_rows_warning = false;
	
	private $expected_rows = false;
	
	// @var Boolean
	private $try_split = false;
	
	/**
	 * Class constructor
	 *
	 * @param Array|String $backup_files  - files to backup, or (string)'no'
	 * @param Integer	   $altered_since - only backup files altered since this time (UNIX epoch time)
	 */
	public function __construct($backup_files, $altered_since = -1) {

		global $updraftplus;

		$this->site_name = $this->get_site_name();

		// Decide which zip engine to begin with
		$this->debug = UpdraftPlus_Options::get_updraft_option('updraft_debug_mode');
		$this->updraft_dir = $updraftplus->backups_dir_location();
		$this->updraft_dir_realpath = realpath($this->updraft_dir);

		require_once(UPDRAFTPLUS_DIR.'/includes/class-database-utility.php');

		if ('no' === $backup_files) {
			$this->use_zip_object = 'UpdraftPlus_PclZip';
			return;
		}

		$this->extensions_to_not_compress = array_unique(array_map('strtolower', array_map('trim', explode(',', UPDRAFTPLUS_ZIP_NOCOMPRESS))));

		$this->backup_excluded_patterns = array(
			array(
				// all in one wp migration pattern: WP_PLUGIN_DIR/all-in-one-wp-migration/storage/*/*.wpress, `ai1wm-backups` folder in wp-content is already implicitly handled on the UDP settings with a `*backups` predefined exclusion rule for `others` directory
				'directory' => realpath(WP_PLUGIN_DIR).DIRECTORY_SEPARATOR.'all-in-one-wp-migration'.DIRECTORY_SEPARATOR.'storage',
				'regex' => '/.+\.wpress$/is',
			),
		);

		$this->altered_since = $altered_since;

		$resumptions_since_last_successful = $updraftplus->current_resumption - $updraftplus->last_successful_resumption;
		
		// false means 'tried + failed'; whereas 0 means 'not yet tried'
		// Disallow binzip on OpenVZ when we're not sure there's plenty of memory
		if (0 === $this->binzip && (!defined('UPDRAFTPLUS_PREFERPCLZIP') || !UPDRAFTPLUS_PREFERPCLZIP) && (!defined('UPDRAFTPLUS_NO_BINZIP') || !UPDRAFTPLUS_NO_BINZIP) && ($updraftplus->current_resumption < 9 || $resumptions_since_last_successful < 2)) {

			if (@file_exists('/proc/user_beancounters') && @file_exists('/proc/meminfo') && @is_readable('/proc/meminfo')) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$meminfo = @file_get_contents('/proc/meminfo', false, null, 0, 200);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				if (is_string($meminfo) && preg_match('/MemTotal:\s+(\d+) kB/', $meminfo, $matches)) {
					$memory_mb = $matches[1]/1024;
					// If the report is of a large amount, then we're probably getting the total memory on the hypervisor (this has been observed), and don't really know the VPS's memory
					$vz_log = "OpenVZ; reported memory: ".round($memory_mb, 1)." MB";
					if ($memory_mb < 1024 || $memory_mb > 8192) {
						$openvz_lowmem = true;
						$vz_log .= " (will not use BinZip)";
					}
					$updraftplus->log($vz_log);
				}
			}
			if (empty($openvz_lowmem)) {
				$updraftplus->log('Checking if we have a zip executable available');
				$binzip = $updraftplus->find_working_bin_zip();
				if (is_string($binzip)) {
					$updraftplus->log("Zip engine: found/will use a binary zip: $binzip");
					$this->binzip = $binzip;
					$this->use_zip_object = 'UpdraftPlus_BinZip';
				}
			}
		}

		// In tests, PclZip was found to be 25% slower than ZipArchive
		if ('UpdraftPlus_PclZip' != $this->use_zip_object && empty($this->binzip) && ((defined('UPDRAFTPLUS_PREFERPCLZIP') && UPDRAFTPLUS_PREFERPCLZIP == true) || !class_exists('ZipArchive') || !class_exists('UpdraftPlus_ZipArchive') || (!extension_loaded('zip') && !method_exists('ZipArchive', 'AddFile')))) {
			global $updraftplus;
			$updraftplus->log("Zip engine: ZipArchive (a.k.a. php-zip) is not available or is disabled (will use PclZip (much slower) if needed)");
			$this->use_zip_object = 'UpdraftPlus_PclZip';
		}
		
		$this->zip_batch_ceiling = (defined('UPDRAFTPLUS_ZIP_BATCH_CEILING') && UPDRAFTPLUS_ZIP_BATCH_CEILING > 104857600) ? UPDRAFTPLUS_ZIP_BATCH_CEILING : 200 * 1048576;

		add_filter('updraftplus_exclude_file', array($this, 'backup_exclude_file'), 10, 2);

	}

	/**
	 * Get a site name suitable for use in the backup filename
	 *
	 * @return String
	 */
	private function get_site_name() {
		// Get the blog name and rip out known-problematic characters. Remember that we may need to be able to upload this to any FTP server or cloud storage, where filename support may be unknown
		$site_name = str_replace('__', '_', preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', substr(get_bloginfo(), 0, 32))));
		if (!$site_name || preg_match('#^_+$#', $site_name)) {
			// Try again...
			$parsed_url = parse_url(home_url(), PHP_URL_HOST);
			$parsed_subdir = untrailingslashit(parse_url(home_url(), PHP_URL_PATH));
			if ($parsed_subdir && '/' != $parsed_subdir) $parsed_url .= str_replace(array('/', '\\'), '_', $parsed_subdir);
			$site_name = str_replace('__', '_', preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', substr($parsed_url, 0, 32))));
			if (!$site_name || preg_match('#^_+$#', $site_name)) $site_name = 'WordPress_Backup';
		}

		// Allow an over-ride. Careful about introducing characters not supported by your filesystem or cloud storage.
		return apply_filters('updraftplus_blog_name', $site_name);
	}

	/**
	 * Public, because called from the 'More Files' add-on
	 *
	 * @param String|Array	  $create_from_dir      Directory/ies to create the zip
	 * @param String		  $whichone             Entity being backed up (e.g. 'plugins', 'uploads')
	 * @param String		  $backup_file_basename Name of backup file
	 * @param Integer		  $index                Index of zip in the sequence
	 * @param Integer|Boolean $first_linked_index   First linked index in the sequence, or false
	 *
	 * @return Boolean
	 */
	public function create_zip($create_from_dir, $whichone, $backup_file_basename, $index, $first_linked_index = false) {
		// Note: $create_from_dir can be an array or a string
		
		if (function_exists('set_time_limit')) set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);
		
		$original_index = $index;

		$this->index = $index;
		$this->first_linked_index = (false === $first_linked_index) ? 0 : $first_linked_index;
		$this->whichone = $whichone;

		global $updraftplus;

		$this->zip_split_every = max((int) $updraftplus->jobdata_get('split_every'), UPDRAFTPLUS_SPLIT_MIN)*1048576;

		if ('others' != $whichone) $updraftplus->log("Beginning creation of dump of $whichone (split every: ".round($this->zip_split_every/1048576, 1)." MB)");

		if (is_string($create_from_dir) && !file_exists($create_from_dir)) {
			$flag_error = true;
			$updraftplus->log("Does not exist: $create_from_dir");
			if ('mu-plugins' == $whichone) {
				if (!function_exists('get_mu_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
				$mu_plugins = get_mu_plugins();
				if (count($mu_plugins) == 0) {
					$updraftplus->log("There appear to be no mu-plugins to backup. Will not raise an error.");
					$flag_error = false;
				}
			}
			if ($flag_error) $updraftplus->log(sprintf(__("%s - could not back this entity up; the corresponding directory does not exist (%s)", 'updraftplus'), $whichone, $create_from_dir), 'error');
			return false;
		}

		$itext = empty($index) ? '' : $index+1;
		$base_path = $backup_file_basename.'-'.$whichone.$itext.'.zip';
		$full_path = $this->updraft_dir.'/'.$base_path;
		$time_now = time();

		// This is compatible with filenames which indicate increments, as it is looking only for the current increment
		if (file_exists($full_path)) {
			// Gather any further files that may also exist
			$files_existing = array();
			while (file_exists($full_path)) {
				$files_existing[] = $base_path;
				$time_mod = (int) @filemtime($full_path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$updraftplus->log($base_path.": this file has already been created (age: ".round($time_now-$time_mod, 1)." s)");
				if ($time_mod > 100 && ($time_now - $time_mod) < 30) {
					UpdraftPlus_Job_Scheduler::terminate_due_to_activity($base_path, $time_now, $time_mod);
				}
				$index++;
				// This is compatible with filenames which indicate increments, as it is looking only for the current increment
				$base_path = $backup_file_basename.'-'.$whichone.($index+1).'.zip';
				$full_path = $this->updraft_dir.'/'.$base_path;
			}
		}

		// Temporary file, to be able to detect actual completion (upon which, it is renamed)

		// Jun-13 - be more aggressive in removing temporary files from earlier attempts - anything >=600 seconds old of this kind
		UpdraftPlus_Filesystem_Functions::clean_temporary_files('_'.$updraftplus->file_nonce."-$whichone", 600);

		// Firstly, make sure that the temporary file is not already being written to - which can happen if a resumption takes place whilst an old run is still active
		$zip_name = $full_path.'.tmp';
		$time_mod = file_exists($zip_name) ? filemtime($zip_name) : 0;
		if (file_exists($zip_name) && $time_mod>100 && ($time_now-$time_mod)<30) {
			UpdraftPlus_Job_Scheduler::terminate_due_to_activity($zip_name, $time_now, $time_mod);
		}

		if (file_exists($zip_name)) {
			$updraftplus->log("File exists ($zip_name), but was apparently not modified within the last 30 seconds, so we assume that any previous run has now terminated (time_mod=$time_mod, time_now=$time_now, diff=".($time_now-$time_mod).")");
		}

		// Now, check for other forms of temporary file, which would indicate that some activity is going on (even if it hasn't made it into the main zip file yet)
		// Note: this doesn't catch PclZip temporary files
		$d = dir($this->updraft_dir);
		$match = '_'.$updraftplus->file_nonce."-".$whichone;
		while (false !== ($e = $d->read())) {
			if ('.' == $e || '..' == $e || !is_file($this->updraft_dir.'/'.$e)) continue;
			$ziparchive_match = preg_match("/$match(?:[0-9]*)\.zip\.tmp\.[A-Za-z0-9]+$/i", $e);
			$binzip_match = preg_match("/^zi([A-Za-z0-9]){6}$/", $e);
			$pclzip_match = preg_match("/^pclzip-[a-z0-9]+.(?:gz|tmp)$/", $e);
			if ($time_now-filemtime($this->updraft_dir.'/'.$e) < 30 && ($ziparchive_match || (0 != $updraftplus->current_resumption && ($binzip_match || $pclzip_match)))) {
				UpdraftPlus_Job_Scheduler::terminate_due_to_activity($this->updraft_dir.'/'.$e, $time_now, filemtime($this->updraft_dir.'/'.$e));
			}
		}
		@$d->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		clearstatcache();

		if (isset($files_existing)) {
			// Because of zip-splitting, the mere fact that files exist is not enough to indicate that the entity is finished. For that, we need to also see that no subsequent file has been started.
			// Q. What if the previous runner died in between zips, and it is our job to start the next one? A. The next temporary file is created before finishing the former zip, so we are safe (and we are also safe-guarded by the updated value of the index being stored in the database).
			return $files_existing;
		}

		$this->log_account_space();

		$this->zip_microtime_start = microtime(true);

		// The paths in the zip should then begin with '$whichone', having removed WP_CONTENT_DIR from the front
		$zipcode = $this->make_zipfile($create_from_dir, $backup_file_basename, $whichone);
		if (true !== $zipcode) {
			$updraftplus->log("ERROR: Zip failure: Could not create $whichone zip (".$this->index." / $index)");
			$updraftplus->log(sprintf(__("Could not create %s zip. Consult the log file for more information.", 'updraftplus'), $whichone), 'error');
			// The caller is required to update $index from $this->index
			return false;
		} else {
			$itext = empty($this->index) ? '' : $this->index+1;
			$full_path = $this->updraft_dir.'/'.$backup_file_basename.'-'.$whichone.$itext.'.zip';
			if (file_exists($full_path.'.tmp')) {
				if (@filesize($full_path.'.tmp') === 0) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$updraftplus->log("Did not create $whichone zip (".$this->index.") - not needed");
					@unlink($full_path.'.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} else {
				
					$checksum_description = '';
		
					$checksums = $updraftplus->which_checksums();
		
					foreach ($checksums as $checksum) {
					
						$cksum = hash_file($checksum, $full_path.'.tmp');
						$updraftplus->jobdata_set($checksum.'-'.$whichone.$this->index, $cksum);
						if ($checksum_description) $checksum_description .= ', ';
						$checksum_description .= "$checksum: $cksum";
					
					}
				
					@rename($full_path.'.tmp', $full_path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$timetaken = max(microtime(true)-$this->zip_microtime_start, 0.000001);
					$kbsize = filesize($full_path)/1024;
					$rate = round($kbsize/$timetaken, 1);
					$updraftplus->log("Created $whichone zip (".$this->index.") - ".round($kbsize, 1)." KB in ".round($timetaken, 1)." s ($rate KB/s) ($checksum_description)");
					// We can now remove any left-over temporary files from this job
				}
			} elseif ($this->index > $original_index) {
				$updraftplus->log("Did not create $whichone zip (".$this->index.") - not needed (2)");
				// Added 12-Feb-2014 (to help multiple morefiles)
				$this->index--;
			} else {
				$updraftplus->log("Looked-for $whichone zip (".$this->index.") was not found (".basename($full_path).".tmp)", 'warning');
			}
			UpdraftPlus_Filesystem_Functions::clean_temporary_files('_'.$updraftplus->file_nonce."-$whichone", 0);
		}

		// Remove cache list files as well, if there are any
		UpdraftPlus_Filesystem_Functions::clean_temporary_files('_'.$updraftplus->file_nonce."-$whichone", 0, true);

		// Create the results array to send back (just the new ones, not any prior ones)
		$files_existing = array();
		$res_index = $original_index;
		for ($i = $original_index; $i<= $this->index; $i++) {
			$itext = empty($i) ? '' : ($i+1);
			$full_path = $this->updraft_dir.'/'.$backup_file_basename.'-'.$whichone.$itext.'.zip';
			if (file_exists($full_path)) {
				$files_existing[$res_index] = $backup_file_basename.'-'.$whichone.$itext.'.zip';
			}
			$res_index++;
		}
		return $files_existing;
	}

	/**
	 * This method is for calling outside of a cloud_backup() context. It constructs a list of services for which prune operations should be attempted, and then calls prune_retained_backups() if necessary upon them.
	 */
	public function do_prune_standalone() {
		global $updraftplus;

		$services = (array) $updraftplus->just_one($updraftplus->jobdata_get('service'));

		$prune_services = array();

		foreach ($services as $service) {
			if ('none' === $service || '' == $service) continue;

			$objname = "UpdraftPlus_BackupModule_${service}";
			if (!class_exists($objname) && file_exists(UPDRAFTPLUS_DIR.'/methods/'.$service.'.php')) {
				include_once(UPDRAFTPLUS_DIR.'/methods/'.$service.'.php');
			}
			if (class_exists($objname)) {
				$remote_obj = new $objname;
				$prune_services[$service]['all'] = array($remote_obj, null);
			} else {
				$updraftplus->log("Could not prune from service $service: remote method not found");
			}

		}

		if (!empty($prune_services)) $this->prune_retained_backups($prune_services);
	}

	/**
	 * Dispatch to the relevant function
	 *
	 * @param Array $backup_array List of archives for the backup
	 */
	public function cloud_backup($backup_array) {

		global $updraftplus;

		$services = (array) $updraftplus->just_one($updraftplus->jobdata_get('service'));
		$remote_storage_instances = $updraftplus->jobdata_get('remote_storage_instances', array());

		// We need to make sure that the loop below actually runs
		if (empty($services)) $services = array('none');
		
		$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_enabled_storage_objects_and_ids($services, $remote_storage_instances);

		$total_instances_count = 0;

		foreach ($storage_objects_and_ids as $service) {
			if ($service['object']->supports_feature('multi_options')) $total_instances_count += count($service['instance_settings']);
		}

		$updraftplus->jobdata_set('jobstatus', 'clouduploading');

		$updraftplus->register_wp_http_option_hooks();

		$upload_status = $updraftplus->jobdata_get('uploading_substatus');
		if (!is_array($upload_status) || !isset($upload_status['t'])) {
			$upload_status = array('i' => 0, 'p' => 0, 't' => max(1, $total_instances_count)*count($backup_array));
			$updraftplus->jobdata_set('uploading_substatus', $upload_status);
		}

		$do_prune = array();

		// If there was no check-in last time, then attempt a different service first - in case a time-out on the attempted service leads to no activity and everything stopping
		if (count($services) >1 && $updraftplus->no_checkin_last_time) {
			$updraftplus->log('No check-in last time: will try a different remote service first');
			array_push($services, array_shift($services));
			// Make sure that the 'no worthwhile activity' detector isn't flumoxed by the starting of a new upload at 0%
			if ($updraftplus->current_resumption > 9) $updraftplus->jobdata_set('uploaded_lastreset', $updraftplus->current_resumption);
			if (1 == ($updraftplus->current_resumption % 2) && count($services)>2) array_push($services, array_shift($services));
		}

		$errors_before_uploads = $updraftplus->error_count();

		foreach ($services as $ind => $service) {
			try {
				$instance_id_count = 0;
				$total_instance_ids = ('none' !== $service && '' !== $service && $storage_objects_and_ids[$service]['object']->supports_feature('multi_options')) ? count($storage_objects_and_ids[$service]['instance_settings']) : 1;
	
				// Used for logging by record_upload_chunk()
				$this->current_service = $service;
	
				// Used when deciding whether to delete the local file
				$this->last_storage_instance = ($ind+1 >= count($services) && $instance_id_count+1 >= $total_instance_ids && $errors_before_uploads == $updraftplus->error_count()) ? true : false;
				$log_extra = $this->last_storage_instance ? ' (last)' : '';
				$updraftplus->log("Cloud backup selection (".($ind+1)."/".count($services)."): ".$service." with instance (".($instance_id_count+1)."/".$total_instance_ids.")".$log_extra);
				if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	
				if ('none' == $service || '' == $service) {
					$updraftplus->log('No remote despatch: user chose no remote backup service');
					// Still want to mark as "uploaded", to signal that nothing more needs doing. (Important on incremental runs with no cloud storage).
					foreach ($backup_array as $file) {
						if ($updraftplus->is_uploaded($file)) {
							$updraftplus->log("Already uploaded: $file");
						} else {
							$updraftplus->uploaded_file($file, true);
						}
						$fullpath = $this->updraft_dir.'/'.$file;
						if (file_exists($fullpath.'.list.tmp')) {
							$updraftplus->log("Deleting zip manifest ({$file}.list.tmp)");
							unlink($fullpath.'.list.tmp');
						}
					}
					$this->prune_retained_backups(array('none' => array('all' => array(null, null))));
				} elseif (!empty($storage_objects_and_ids[$service]['object']) && !$storage_objects_and_ids[$service]['object']->supports_feature('multi_options')) {
					$remote_obj = $storage_objects_and_ids[$service]['object'];
					
					$do_prune = array_merge_recursive($do_prune, $this->upload_cloud($remote_obj, $service, $backup_array, ''));
				} elseif (!empty($storage_objects_and_ids[$service]['instance_settings'])) {
					foreach ($storage_objects_and_ids[$service]['instance_settings'] as $instance_id => $options) {
					
						if ($instance_id_count > 0) {
							$this->last_storage_instance = ($ind+1 >= count($services) && $instance_id_count+1 >= $total_instance_ids && $errors_before_uploads == $updraftplus->error_count()) ? true : false;
							$log_extra = $this->last_storage_instance ? ' (last)' : '';
							$updraftplus->log("Cloud backup selection (".($ind+1)."/".count($services)."): ".$service." with instance (".($instance_id_count+1)."/".$total_instance_ids.")".$log_extra);
						}
					
						// Used for logging by record_upload_chunk()
						$this->current_instance = $instance_id;
	
						if (!isset($options['instance_enabled'])) $options['instance_enabled'] = 1;
	
						// if $remote_storage_instances is not empty then we are looping over a list of instances the user wants to backup to so we want to ignore if the instance is enabled or not
						if (1 == $options['instance_enabled'] || !empty($remote_storage_instances)) {
							$remote_obj = $storage_objects_and_ids[$service]['object'];
							$remote_obj->set_options($options, true, $instance_id);
							$do_prune = array_merge_recursive($do_prune, $this->upload_cloud($remote_obj, $service, $backup_array, $instance_id));
						} else {
							$updraftplus->log("This instance id ($instance_id) is set as inactive.");
						}
	
						$instance_id_count++;
					}
				}
			} catch (Exception $e) {
				$log_message = 'Exception ('.get_class($e).') occurred during backup uploads to the '.$service.'. Exception Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$updraftplus->log($log_message);
				error_log($log_message);
				$updraftplus->log(sprintf(__('A PHP exception (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during backup uploads to the '.$service.'. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$updraftplus->log($log_message);
				error_log($log_message);
				$updraftplus->log(sprintf(__('A PHP fatal error (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
			}
		}

		if (!empty($do_prune)) $this->prune_retained_backups($do_prune);

		$updraftplus->register_wp_http_option_hooks(false);

	}

	/**
	 * This method will start the upload of the backups to the chosen remote storage method and return an array of files to be pruned and their location.
	 *
	 * @param  Object $remote_obj   - the remote storage object
	 * @param  String $service      - the name of the service we are uploading to
	 * @param  Array  $backup_array - an array that contains the backup files we want to upload
	 * @param  String $instance_id  - the instance id we are using
	 * @return Array                - an array with information about what files to prune and where they are located
	 */
	private function upload_cloud($remote_obj, $service, $backup_array, $instance_id) {

		global $updraftplus;

		$do_prune = array();

		if ('' == $instance_id) {
			$updraftplus->log("Beginning dispatch of backup to remote ($service)");
		} else {
			$updraftplus->log("Beginning dispatch of backup to remote ($service) (instance identifier $instance_id)");
		}

		$errors_before_uploads = $updraftplus->error_count();

		$sarray = array();
		foreach ($backup_array as $bind => $file) {
			if ($updraftplus->is_uploaded($file, $service, $instance_id)) {
				if ('' == $instance_id) {
					$updraftplus->log("Already uploaded to $service: $file", 'notice', false, true);
				} else {
					$updraftplus->log("Already uploaded to $service / $instance_id: $file", 'notice', false, true);
				}
			} else {
				$sarray[$bind] = $file;
			}
		}
		
		if (count($sarray) > 0) {
			$pass_to_prune = $remote_obj->backup($sarray);
			if ('remotesend' != $service) {
				$do_prune[$service][$instance_id] = array($remote_obj, $pass_to_prune);
			} else {
				$do_prune[$service]['default'] = array($remote_obj, $pass_to_prune);
			}

			// Check there are no errors in the uploads, if none then call upload_completed() if it exists otherwise mark as complete
			if ($errors_before_uploads == $updraftplus->error_count()) {
				if (is_callable(array($remote_obj, 'upload_completed'))) {
					$result = $remote_obj->upload_completed();
					if ($result) $updraftplus->mark_upload_complete($service);
				} else {
					$updraftplus->mark_upload_complete($service);
				}
			}
		} else {
			// We still need to make sure that prune is run on this remote storage method, even if all entities were previously uploaded
			$do_prune[$service]['all'] = array($remote_obj, null);
		}

		return $do_prune;
	}

	/**
	 * Group the backup history into sets for retention processing and indicate the retention rule to apply to each group. This is a 'default' function which just puts them all in together.
	 *
	 * @param Array $backup_history
	 *
	 * @return Array
	 */
	private function group_backups($backup_history) {
		return array(array('sets' => $backup_history, 'process_order' => 'keep_newest'));
	}
	
	/**
	 * Logs a message; with the message being logged to the database also only if that has not been done in the last 3 seconds. Useful for better overall performance on slow database servers with rapid logging.
	 *
	 * @uses UpdraftPlus::log()
	 *
	 * @param String $message - the message to log
	 * @param String $level	  - the log level
	 */
	private function log_with_db_occasionally($message, $level = 'notice') {
		global $updraftplus;
		static $last_db = false;
		
		if (time() > $last_db + 3) {
			$last_db = time();
			$skip_dblog = false;
		} else {
			$skip_dblog = true;
		}
		
		return $updraftplus->log($message, $level, false, $skip_dblog);
	}
	
	/**
	 * Prunes historical backups, according to the user's settings
	 *
	 * @param Array $services - An associative array with list of services as key and remote object and boolean flag as values to prune on. This must be an array (i.e. it is not flexible like some other places)
	 *
	 * @return void
	 */
	public function prune_retained_backups($services) {

		global $updraftplus, $wpdb;

		if ('' != $updraftplus->jobdata_get('remotesend_info')) {
			$updraftplus->log("Prune old backups from local store: skipping, as this was a remote send operation");
			return;
		}

		if (method_exists($wpdb, 'check_connection') && (!defined('UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS') || !UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS)) {
			if (!$wpdb->check_connection(false)) {
				UpdraftPlus_Job_Scheduler::reschedule(60);
				$updraftplus->log('It seems the database went away; scheduling a resumption and terminating for now');
				UpdraftPlus_Job_Scheduler::record_still_alive();
				die;
			}
		}

		// If they turned off deletion on local backups, then there is nothing to do
		if (!UpdraftPlus_Options::get_updraft_option('updraft_delete_local', 1) && 1 == count($services) && array_key_exists('none', $services)) {
			$updraftplus->log("Prune old backups from local store: nothing to do, since the user disabled local deletion and we are using local backups");
			return;
		}

		$updraftplus->jobdata_set_multi(array('jobstatus' => 'pruning', 'prune' => 'begun'));

		// Number of backups to retain - files
		$updraft_retain = UpdraftPlus_Options::get_updraft_option('updraft_retain', 2);
		$updraft_retain = is_numeric($updraft_retain) ? $updraft_retain : 1;

		// Number of backups to retain - db
		$updraft_retain_db = UpdraftPlus_Options::get_updraft_option('updraft_retain_db', $updraft_retain);
		$updraft_retain_db = is_numeric($updraft_retain_db) ? $updraft_retain_db : 1;

		$updraftplus->log("Retain: beginning examination of existing backup sets; user setting: retain_files=$updraft_retain, retain_db=$updraft_retain_db");

		// Returns an array, most recent first, of backup sets
		$backup_history = UpdraftPlus_Backup_History::get_history();
		
		$ignored_because_imported = array();
		
		// Remove non-native (imported) backups, which are neither counted nor pruned. It's neater to do these in advance, and log only one line.
		$functional_backup_history = $backup_history;
		foreach ($functional_backup_history as $backup_time => $backup_to_examine) {
			if (isset($backup_to_examine['native']) && false == $backup_to_examine['native']) {
				$ignored_because_imported[] = $backup_time;
				unset($functional_backup_history[$backup_time]);
			}
		}
		if (!empty($ignored_because_imported)) {
			$updraftplus->log("These backup set(s) were imported from a remote location, so will not be counted or pruned. Skipping: ".implode(', ', $ignored_because_imported));
		}
		
		$backupable_entities = $updraftplus->get_backupable_file_entities(true);

		$database_backups_found = array();

		$file_entities_backups_found = array();
		foreach ($backupable_entities as $entity => $info) {
			$file_entities_backups_found[$entity] = 0;
		}

		if (false === ($backup_db_groups = apply_filters('updraftplus_group_backups_for_pruning', false, $functional_backup_history, 'db'))) {
			$backup_db_groups = $this->group_backups($functional_backup_history);
		}
		$updraftplus->log("Number of backup sets in history: ".count($backup_history)."; groups (db): ".count($backup_db_groups));

		foreach ($backup_db_groups as $group_id => $group) {
			
			// N.B. The array returned by UpdraftPlus_Backup_History::get_history() is already sorted, with most-recent first

			if (empty($group['sets']) || !is_array($group['sets'])) continue;
			$sets = $group['sets'];

			// Sort the groups into the desired "keep this first" order
			$process_order = (!empty($group['process_order']) && 'keep_oldest' == $group['process_order']) ? 'keep_oldest' : 'keep_newest';
			if ('keep_oldest' == $process_order) ksort($sets);
			
			$rule = !empty($group['rule']) ? $group['rule'] : array('after-howmany' => 0, 'after-period' => 0, 'every-period' => 1, 'every-howmany' => 1);
			
			foreach ($sets as $backup_datestamp => $backup_to_examine) {

				$files_to_prune = array();
				$nonce = empty($backup_to_examine['nonce']) ? '???' : $backup_to_examine['nonce'];

				// $backup_to_examine is an array of file names, keyed on db/plugins/themes/uploads
				// The new backup_history array is saved afterwards, so remember to unset the ones that are to be deleted
				$this->log_with_db_occasionally(sprintf("Examining (for databases) backup set with group_id=$group_id, nonce=%s, datestamp=%s (%s)", $nonce, $backup_datestamp, gmdate('M d Y H:i:s', $backup_datestamp)));
				
				// "Always Keep" Backups should be counted in the count of how many have been retained for purposes of the "how many to retain" count... but if that count is already matched, it's not a problem
				$is_always_keep = !empty($backup_to_examine['always_keep']);
				
				// Auto-backups are only counted or deleted once we have reached the retain limit - before that, they are skipped
				$is_autobackup = !empty($backup_to_examine['autobackup']);
				
				$remote_sent = (!empty($backup_to_examine['service']) && ((is_array($backup_to_examine['service']) && in_array('remotesend', $backup_to_examine['service'])) || 'remotesend' === $backup_to_examine['service'])) ? true : false;

				$any_deleted_via_filter_yet = false;

				// Databases
				foreach ($backup_to_examine as $key => $data) {
					if ('db' != strtolower(substr($key, 0, 2)) || '-size' == substr($key, -5, 5)) continue;

					if (empty($database_backups_found[$key])) $database_backups_found[$key] = 0;
					
					if ($nonce == $updraftplus->nonce || $nonce == $updraftplus->file_nonce) {
						$this->log_with_db_occasionally("This backup set is the backup set just made, so will not be deleted.");
						$database_backups_found[$key]++;
						continue;
					}
					
					if ($is_always_keep) {
						if ($database_backups_found[$key] < $updraft_retain) {
							$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an 'Always Keep' backup, and we have not yet reached any retain limits, so it should be counted in the count of how many have been retained for purposes of the 'how many to retain' count. It will not be pruned. Skipping.");
							$database_backups_found[$key]++;
						} else {
							$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an 'Always Keep' backup, so it will not be pruned. Skipping.");
						}
						continue;
					}
					
					if ($is_autobackup) {
						if ($any_deleted_via_filter_yet) {
							$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an automatic backup, but we have previously deleted a backup due to a limit, so it will be pruned (but not counted towards numerical limits).");
							$prune_it = true;
						} elseif ($database_backups_found[$key] < $updraft_retain_db) {
							$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an automatic backup, and we have not yet reached any retain limits, so it will not be counted or pruned. Skipping.");
							continue;
						} else {
							$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an automatic backup, and we have already reached retain limits, so it will be pruned.");
							$prune_it = true;
						}
					} else {
						$prune_it = false;
					}

					if ($remote_sent) {
						$prune_it = true;
						$this->log_with_db_occasionally("$backup_datestamp: $key: was sent to remote site; will remove from local record (only)");
					}
					
					// All non-auto backups must be run through this filter (in date order) regardless of the current state of $prune_it - so that filters are able to track state.
					$prune_it_before_filter = $prune_it;

					if (!$is_autobackup) $prune_it = apply_filters('updraftplus_prune_or_not', $prune_it, 'db', $backup_datestamp, $key, $database_backups_found[$key], $rule, $group_id);

					// Apply the final retention limit list (do not increase the 'retained' counter before seeing if the backup is being pruned for some other reason)
					if (!$prune_it && !$is_autobackup) {

						if ($database_backups_found[$key] + 1 > $updraft_retain_db) {
							$prune_it = true;

							$fname = is_string($data) ? $data : $data[0];
							$this->log_with_db_occasionally("$backup_datestamp: $key: this set includes a database (".$fname."); db count is now ".$database_backups_found[$key]);

							$this->log_with_db_occasionally("$backup_datestamp: $key: over retain limit ($updraft_retain_db); will delete this database");
						}
					
					}
					
					if ($prune_it) {
						if (!$prune_it_before_filter) $any_deleted_via_filter_yet = true;

						if (!empty($data)) {
							$size_key = $key.'-size';
							$size = isset($backup_to_examine[$size_key]) ? $backup_to_examine[$size_key] : null;
							foreach ($services as $service => $instance_ids_to_prune) {
								foreach ($instance_ids_to_prune as $instance_id_to_prune => $sd) {
									if ('none' != $service && '' != $service && $sd[0]->supports_feature('multi_options')) {
										$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array($service));
										if ('all' == $instance_id_to_prune) {
											foreach ($storage_objects_and_ids[$service]['instance_settings'] as $saved_instance_id => $options) {
												$sd[0]->set_options($options, false, $saved_instance_id);
												$this->prune_file($service, $data, $sd[0], $sd[1], array($size));
											}
										} else {
											$opts = $storage_objects_and_ids[$service]['instance_settings'][$instance_id_to_prune];
											$sd[0]->set_options($opts, false, $instance_id_to_prune);
											$this->prune_file($service, $data, $sd[0], $sd[1], array($size));
										}
									} else {
										$this->prune_file($service, $data, $sd[0], $sd[1], array($size));
									}
								}
							}
						}
						unset($backup_to_examine[$key]);
						UpdraftPlus_Job_Scheduler::record_still_alive();
					} elseif (!$is_autobackup) {
						$database_backups_found[$key]++;
					}

					$backup_to_examine = $this->remove_backup_set_if_empty($backup_to_examine, $backupable_entities);
					if (empty($backup_to_examine)) {
						unset($functional_backup_history[$backup_datestamp]);
						unset($backup_history[$backup_datestamp]);
						$this->maybe_save_backup_history_and_reschedule($backup_history);
					} else {
						$functional_backup_history[$backup_datestamp] = $backup_to_examine;
						$backup_history[$backup_datestamp] = $backup_to_examine;
					}
				}
			}
		}

		if (false === ($backup_files_groups = apply_filters('updraftplus_group_backups_for_pruning', false, $functional_backup_history, 'files'))) {
			$backup_files_groups = $this->group_backups($functional_backup_history);
		}

		$updraftplus->log("Number of backup sets in history: ".count($backup_history)."; groups (files): ".count($backup_files_groups));
		
		// Now again - this time for the files
		foreach ($backup_files_groups as $group_id => $group) {
			
			// N.B. The array returned by UpdraftPlus_Backup_History::get_history() is already sorted, with most-recent first

			if (empty($group['sets']) || !is_array($group['sets'])) continue;
			$sets = $group['sets'];
			
			// Sort the groups into the desired "keep this first" order
			$process_order = (!empty($group['process_order']) && 'keep_oldest' == $group['process_order']) ? 'keep_oldest' : 'keep_newest';
			// Youngest - i.e. smallest epoch - first
			if ('keep_oldest' == $process_order) ksort($sets);

			$rule = !empty($group['rule']) ? $group['rule'] : array('after-howmany' => 0, 'after-period' => 0, 'every-period' => 1, 'every-howmany' => 1);
			
			foreach ($sets as $backup_datestamp => $backup_to_examine) {

				$files_to_prune = array();
				$nonce = empty($backup_to_examine['nonce']) ? '???' : $backup_to_examine['nonce'];

				// $backup_to_examine is an array of file names, keyed on db/plugins/themes/uploads
				// The new backup_history array is saved afterwards, so remember to unset the ones that are to be deleted
				$this->log_with_db_occasionally(sprintf("Examining (for files) backup set with nonce=%s, datestamp=%s (%s)", $nonce, $backup_datestamp, gmdate('M d Y H:i:s', $backup_datestamp)));

				// "Always Keep" Backups should be counted in the count of how many have been retained for purposes of the "how many to retain" count... but if that count is already matched, it's not a problem
				$is_always_keep = !empty($backup_to_examine['always_keep']);
				
				// Auto-backups are only counted or deleted once we have reached the retain limit - before that, they are skipped
				$is_autobackup = !empty($backup_to_examine['autobackup']);

				$remote_sent = (!empty($backup_to_examine['service']) && ((is_array($backup_to_examine['service']) && in_array('remotesend', $backup_to_examine['service'])) || 'remotesend' === $backup_to_examine['service'])) ? true : false;

				$any_deleted_via_filter_yet = false;
		
				$file_sizes = array();

				// Files
				foreach ($backupable_entities as $entity => $info) {
					if (!empty($backup_to_examine[$entity])) {
					
						// This should only be able to happen if you import backups with a future timestamp
						if ($nonce == $updraftplus->nonce || $nonce == $updraftplus->file_nonce) {
							$updraftplus->log("This backup set is the backup set just made, so will not be deleted.");
							$file_entities_backups_found[$entity]++;
							continue;
						}

						if ($is_always_keep) {
							if ($file_entities_backups_found[$entity] < $updraft_retain) {
								$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an 'Always Keep' backup, and we have not yet reached any retain limits, so it should be counted in the count of how many have been retained for purposes of the 'how many to retain' count. It will not be pruned. Skipping.");
								$file_entities_backups_found[$entity]++;
							} else {
								$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an 'Always Keep' backup, so it will not be pruned. Skipping.");
							}
							continue;
						}
						
						if ($is_autobackup) {
							if ($any_deleted_via_filter_yet) {
								$this->log_with_db_occasionally("This backup set was an automatic backup, but we have previously deleted a backup due to a limit, so it will be pruned (but not counted towards numerical limits).");
								$prune_it = true;
							} elseif ($file_entities_backups_found[$entity] < $updraft_retain) {
								$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an automatic backup, and we have not yet reached any retain limits, so it will not be counted or pruned. Skipping.");
								continue;
							} else {
								$this->log_with_db_occasionally("This backup set ($backup_datestamp) was an automatic backup, and we have already reached retain limits, so it will be pruned.");
								$prune_it = true;
							}
						} else {
							$prune_it = false;
						}

						if ($remote_sent) {
							$prune_it = true;
						}

						// All non-auto backups must be run through this filter (in date order) regardless of the current state of $prune_it - so that filters are able to track state.
						$prune_it_before_filter = $prune_it;
						if (!$is_autobackup) $prune_it = apply_filters('updraftplus_prune_or_not', $prune_it, 'files', $backup_datestamp, $entity, $file_entities_backups_found[$entity], $rule, $group_id);

						// The "more than maximum to keep?" counter should not be increased until we actually know that the set is being kept. Before verison 1.11.22, we checked this before running the filter, which resulted in the counter being increased for sets that got pruned via the filter (i.e. not kept) - and too many backups were thus deleted
						if (!$prune_it && !$is_autobackup) {
							if ($file_entities_backups_found[$entity] >= $updraft_retain) {
								$this->log_with_db_occasionally("$entity: over retain limit ($updraft_retain); will delete this file entity");
								$prune_it = true;
							}
						}
						
						if ($prune_it) {
							if (!$prune_it_before_filter) $any_deleted_via_filter_yet = true;
							$prune_this = $backup_to_examine[$entity];
							if (is_string($prune_this)) $prune_this = array($prune_this);

							foreach ($prune_this as $k => $prune_file) {
								if ($remote_sent) {
									$updraftplus->log("$entity: $backup_datestamp: was sent to remote site; will remove from local record (only)");
								}
								$size_key = (0 == $k) ? $entity.'-size' : $entity.$k.'-size';
								$size = (isset($backup_to_examine[$size_key])) ? $backup_to_examine[$size_key] : null;
								$files_to_prune[] = $prune_file;
								$file_sizes[] = $size;
							}
							unset($backup_to_examine[$entity]);
							
						} elseif (!$is_autobackup) {
							$file_entities_backups_found[$entity]++;
						}
					}
				}

				// Sending an empty array is not itself a problem - except that the remote storage method may not check that before setting up a connection, which can waste time: especially if this is done every time around the loop.
				if (!empty($files_to_prune)) {
					// Actually delete the files
					foreach ($services as $service => $instance_ids_to_prune) {
						foreach ($instance_ids_to_prune as $instance_id_to_prune => $sd) {
							if ("none" != $service && '' != $service && $sd[0]->supports_feature('multi_options')) {
								$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array($service));
								if ('all' == $instance_id_to_prune) {
									foreach ($storage_objects_and_ids[$service]['instance_settings'] as $saved_instance_id => $options) {
										$sd[0]->set_options($options, false, $saved_instance_id);
										$this->prune_file($service, $files_to_prune, $sd[0], $sd[1], array($size));
									}
								} else {
									$opts = $storage_objects_and_ids[$service]['instance_settings'][$instance_id_to_prune];
									$sd[0]->set_options($opts, false, $instance_id_to_prune);
									$this->prune_file($service, $files_to_prune, $sd[0], $sd[1], array($size));
								}
							} else {
								$this->prune_file($service, $files_to_prune, $sd[0], $sd[1], array($size));
							}
							UpdraftPlus_Job_Scheduler::record_still_alive();
						}
					}
				}

				$backup_to_examine = $this->remove_backup_set_if_empty($backup_to_examine, $backupable_entities);
				if (empty($backup_to_examine)) {
					unset($backup_history[$backup_datestamp]);
					$this->maybe_save_backup_history_and_reschedule($backup_history);
				} else {
					$backup_history[$backup_datestamp] = $backup_to_examine;
				}

			// Loop over backup sets
			}
			
		// Look over backup groups
		}

		$updraftplus->log("Retain: saving new backup history (sets now: ".count($backup_history).") and finishing retain operation");
		UpdraftPlus_Backup_History::save_history($backup_history, false);

		do_action('updraftplus_prune_retained_backups_finished');
		
		$updraftplus->jobdata_set('prune', 'finished');

	}

	/**
	 * The purpose of this is to save the backup history periodically - for the benefit of setups where the pruning takes longer than the total allow run time (e.g. if the network communications to the remote storage have delays in, and there are a lot of sets to scan)
	 *
	 * @param Array $backup_history - the backup history to possible save
	 */
	private function maybe_save_backup_history_and_reschedule($backup_history) {
		static $last_saved_at = 0;
		if (!$last_saved_at) $last_saved_at = time();
		if (time() - $last_saved_at >= 10) {
			global $updraftplus;
			$updraftplus->log("Retain: saving new backup history, because at least 10 seconds have passed since the last save (sets now: ".count($backup_history).")");
			UpdraftPlus_Backup_History::save_history($backup_history, false);
			UpdraftPlus_Job_Scheduler::something_useful_happened();
			$last_saved_at = time();
		}
	}
	
	/**
	 * Examine a backup set; if it is empty (no files or DB), then remove the associated log file
	 *
	 * @param Array $backup_to_examine	 - backup set
	 * @param Array $backupable_entities - compare with this list of backup entities
	 *
	 * @return Array|Boolean - if it was empty, false is returned
	 */
	private function remove_backup_set_if_empty($backup_to_examine, $backupable_entities) {
	
		global $updraftplus;

		// Get new result, post-deletion; anything left in this set?
		$contains_files = 0;
		foreach ($backupable_entities as $entity => $info) {
			if (isset($backup_to_examine[$entity])) {
				$contains_files = 1;
				break;
			}
		}

		$contains_db = 0;
		foreach ($backup_to_examine as $key => $data) {
			if ('db' == strtolower(substr($key, 0, 2)) && '-size' != substr($key, -5, 5)) {
				$contains_db = 1;
				break;
			}
		}

		// Delete backup set completely if empty, o/w just remove DB
		// We search on the four keys which represent data, allowing other keys to be used to track other things
		if (!$contains_files && !$contains_db) {
			$updraftplus->log("This backup set is now empty; will remove from history");
			if (isset($backup_to_examine['nonce'])) {
				$fullpath = $this->updraft_dir."/log.".$backup_to_examine['nonce'].".txt";
				if (is_file($fullpath)) {
					$updraftplus->log("Deleting log file (log.".$backup_to_examine['nonce'].".txt)");
					@unlink($fullpath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} else {
					$updraftplus->log("Corresponding log file (log.".$backup_to_examine['nonce'].".txt) not found - must have already been deleted");
				}
			} else {
				$updraftplus->log("No nonce record found in the backup set, so cannot delete any remaining log file");
			}
			return false;
		} else {
			$updraftplus->log("This backup set remains non-empty (f=$contains_files/d=$contains_db); will retain in history");
			return $backup_to_examine;
		}
		
	}
	
	/**
	 * Prune files from remote and local storage
	 *
	 * @param String	   $service         Service to prune (one only)
	 * @param Array|String $dofiles         An array of files (or a single string for one file)
	 * @param Array		   $method_object   specific method object
	 * @param Array		   $object_passback specific passback object
	 * @param Array		   $file_sizes      size of files
	 */
	private function prune_file($service, $dofiles, $method_object = null, $object_passback = null, $file_sizes = array()) {
		global $updraftplus;
		if (!is_array($dofiles)) $dofiles = array($dofiles);
		
		if (!apply_filters('updraftplus_prune_file', true, $dofiles, $service, $method_object, $object_passback, $file_sizes)) {
			$updraftplus->log("Prune: service=$service: skipped via filter");
			return;
		}
		
		foreach ($dofiles as $dofile) {
			if (empty($dofile)) continue;
			$updraftplus->log("Delete file: $dofile, service=$service");
			$fullpath = $this->updraft_dir.'/'.$dofile;
			// delete it if it's locally available
			if (file_exists($fullpath)) {
				$updraftplus->log("Deleting local copy ($dofile)");
				unlink($fullpath);
				if (file_exists($fullpath.'.list.tmp')) {
					$updraftplus->log("Deleting zip manifest ({$dofile}.list.tmp)");
					unlink($fullpath.'.list.tmp');
				}
			}
		}
		// Despatch to the particular method's deletion routine
		if (!is_null($method_object)) $method_object->delete($dofiles, $object_passback, $file_sizes);
	}

	/**
	 * The purpose of this function is to make sure that the options table is put in the database first, then the users table, then the site + blogs tables (if present - multisite), then the usermeta table; and after that the core WP tables - so that when restoring we restore the core tables first
	 *
	 * @param Array $a_arr First array to be compared
	 * @param Array $b_arr Second array to be compared
	 * @return Integer - according to the rules of usort()
	 */
	private function backup_db_sorttables($a_arr, $b_arr) {

		$a = $a_arr['name'];
		$a_table_type = $a_arr['type'];
		$b = $b_arr['name'];
		$b_table_type = $b_arr['type'];
	
		// Views must always go after tables (since they can depend upon them)
		if ('VIEW' == $a_table_type && 'VIEW' != $b_table_type) return 1;
		if ('VIEW' == $b_table_type && 'VIEW' != $a_table_type) return -1;
	
		if ('wp' != $this->whichdb) return strcmp($a, $b);

		global $updraftplus;
		if ($a == $b) return 0;
		$our_table_prefix = $this->table_prefix_raw;
		if ($a == $our_table_prefix.'options') return -1;
		if ($b == $our_table_prefix.'options') return 1;
		if ($a == $our_table_prefix.'site') return -1;
		if ($b == $our_table_prefix.'site') return 1;
		if ($a == $our_table_prefix.'blogs') return -1;
		if ($b == $our_table_prefix.'blogs') return 1;
		if ($a == $our_table_prefix.'users') return -1;
		if ($b == $our_table_prefix.'users') return 1;
		if ($a == $our_table_prefix.'usermeta') return -1;
		if ($b == $our_table_prefix.'usermeta') return 1;

		if (empty($our_table_prefix)) return strcmp($a, $b);

		try {
			$core_tables = array_merge($this->wpdb_obj->tables, $this->wpdb_obj->global_tables, $this->wpdb_obj->ms_global_tables);
		} catch (Exception $e) {
			$updraftplus->log($e->getMessage());
		}
		
		if (empty($core_tables)) $core_tables = array('terms', 'term_taxonomy', 'termmeta', 'term_relationships', 'commentmeta', 'comments', 'links', 'postmeta', 'posts', 'site', 'sitemeta', 'blogs', 'blogversions', 'blogmeta');

		global $updraftplus;
		$na = UpdraftPlus_Manipulation_Functions::str_replace_once($our_table_prefix, '', $a);
		$nb = UpdraftPlus_Manipulation_Functions::str_replace_once($our_table_prefix, '', $b);
		if (in_array($na, $core_tables) && !in_array($nb, $core_tables)) return -1;
		if (!in_array($na, $core_tables) && in_array($nb, $core_tables)) return 1;
		return strcmp($a, $b);
	}

	/**
	 * Log the amount account space free/used, if possible
	 */
	private function log_account_space() {
		// Don't waste time if space is huge
		if (!empty($this->account_space_oodles)) return;
		global $updraftplus;
		$hosting_bytes_free = $updraftplus->get_hosting_disk_quota_free();
		if (is_array($hosting_bytes_free)) {
			$perc = round(100*$hosting_bytes_free[1]/(max($hosting_bytes_free[2], 1)), 1);
			$updraftplus->log(sprintf('Free disk space in account: %s (%s used)', round($hosting_bytes_free[3]/1048576, 1)." MB", "$perc %"));
		}
	}

	/**
	 * Returns the basename up to and including the nonce (but not the entity)
	 *
	 * @param Integer $use_time epoch time to use
	 * @return String
	 */
	private function get_backup_file_basename_from_time($use_time) {
		global $updraftplus;
		return apply_filters('updraftplus_get_backup_file_basename_from_time', 'backup_'.get_date_from_gmt(gmdate('Y-m-d H:i:s', $use_time), 'Y-m-d-Hi').'_'.$this->site_name.'_'.$updraftplus->file_nonce, $use_time, $this->site_name);
	}

	/**
	 * Find the zip files in a given directory for a given nonce
	 *
	 * @param String $dir		  - directory to look in
	 * @param Strign $match_nonce - backup ID to match
	 *
	 * @return Array
	 */
	private function find_existing_zips($dir, $match_nonce) {
		$zips = array();
		if (!$handle = opendir($dir)) return $zips;
		while (false !== ($entry = readdir($handle))) {
			if ('.' == $entry || '..' == $entry) continue;
			if (preg_match('/^backup_(\d{4})-(\d{2})-(\d{2})-(\d{2})(\d{2})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?\.zip$/i', $entry, $matches)) {
				if ($matches[6] !== $match_nonce) continue;
				$timestamp = mktime($matches[4], $matches[5], 0, $matches[2], $matches[3], $matches[1]);
				$entity = $matches[7];
				$index = empty($matches[8]) ? '0' : $matches[8];
				$zips[$entity][$index] = array($timestamp, $entry);
			}
		}
		return $zips;
	}

	/**
	 * Get information on whether a particular file exists in a set
	 *
	 * @param  Array   $files  should be an array as returned by find_existing_zips()]
	 * @param  String  $entity entty of the file (e.g. 'plugins')
	 * @param  Integer $index  Index within the files array
	 * @return String|Boolean - false if the file does not exist; otherwise, the basename
	 */
	private function file_exists($files, $entity, $index = 0) {
		if (isset($files[$entity]) && isset($files[$entity][$index])) {
			$file = $files[$entity][$index];
			// Return the filename
			return $file[1];
		} else {
			return false;
		}
	}

	/**
	 * This function is resumable
	 *
	 * @param String $job_status Current status
	 *
	 * @return Array - array of backed-up files
	 */
	private function backup_dirs($job_status) {

		global $updraftplus;

		if (!$updraftplus->backup_time) $updraftplus->backup_time_nonce();

		$use_time = $updraftplus->backup_time;
		$backup_file_basename = $this->get_backup_file_basename_from_time($use_time);

		$backup_array = array();

		// Was there a check-in last time? If not, then reduce the amount of data attempted
		if ('finished' != $job_status && $updraftplus->current_resumption >= 2) {

			// NOTYET: Possible amendment to original algorithm; not just no check-in, but if the check in was very early (can happen if we get a very early checkin for some trivial operation, then attempt something too big)

			// 03-Sep-2015 - came across a case (HS#2052) where there apparently was a check-in 'last time', but no resumption was scheduled because the 'useful_checkin' jobdata was *not* last time - which must indicate dying at a very unfortunate/unlikely point in the code. As a result, the split was not auto-reduced. Consequently, we've added !$updraftplus->newresumption_scheduled as a condition on the first check here (it was already on the second), as if no resumption is scheduled then whatever checkin there was last time was only partial. This was on GoDaddy, for which a number of curious I/O event combinations have been seen in recent months - their platform appears to have some odd behaviour when PHP is killed off.
			// 04-Sep-2015 - move the '$updraftplus->current_resumption<=10' check to the inner loop (instead of applying to this whole section), as I see no reason for that restriction (case seen in HS#2064 where it was required on resumption 15)
			if ($updraftplus->no_checkin_last_time || !$updraftplus->newresumption_scheduled) {
				// Apr 2015: !$updraftplus->newresumption_scheduled added after seeing a log where there was no activity on resumption 9, and extra resumption 10 then tried the same operation.
				if ($updraftplus->current_resumption - $updraftplus->last_successful_resumption > 2 || !$updraftplus->newresumption_scheduled) {
					$this->try_split = true;
				} elseif ($updraftplus->current_resumption <= 10) {
					$maxzipbatch = $updraftplus->jobdata_get('maxzipbatch', 26214400);
					if ((int) $maxzipbatch < 1) $maxzipbatch = 26214400;
					$new_maxzipbatch = max(floor($maxzipbatch * 0.75), 20971520);
					if ($new_maxzipbatch < $maxzipbatch) {
						$updraftplus->log("No check-in was detected on the previous run - as a result, we are reducing the batch amount (old=$maxzipbatch, new=$new_maxzipbatch)");
						$updraftplus->jobdata_set('maxzipbatch', $new_maxzipbatch);
						$updraftplus->jobdata_set('maxzipbatch_ceiling', $new_maxzipbatch);
					}
				}
			}
		}

		if ('finished' != $job_status && !UpdraftPlus_Filesystem_Functions::really_is_writable($this->updraft_dir)) {
			$updraftplus->log("Backup directory (".$this->updraft_dir.") is not writable, or does not exist");
			$updraftplus->log(sprintf(__("Backup directory (%s) is not writable, or does not exist.", 'updraftplus'), $this->updraft_dir), 'error');
			return array();
		}

		$this->job_file_entities = $updraftplus->jobdata_get('job_file_entities');
		
		// This is just used for the visual feedback (via the 'substatus' key)
		$which_entity = 0;
		// e.g. plugins, themes, uploads, others
		// $whichdir might be an array (if $youwhat is 'more')

		// Returns an array (keyed off the entity) of ($timestamp, $filename) arrays
		$existing_zips = $this->find_existing_zips($this->updraft_dir, $updraftplus->file_nonce);

		$possible_backups = $updraftplus->get_backupable_file_entities(true);

		foreach ($possible_backups as $youwhat => $whichdir) {

			if (!isset($this->job_file_entities[$youwhat])) {
				$updraftplus->log("No backup of $youwhat: excluded by user's options");
				continue;
			}

			$index = (int) $this->job_file_entities[$youwhat]['index'];
			if (empty($index)) $index=0;
			$indextext = (0 == $index) ? '' : (1+$index);

			$zip_file = $this->updraft_dir.'/'.$backup_file_basename.'-'.$youwhat.$indextext.'.zip';

			// Split needed?
			$split_every = max((int) $updraftplus->jobdata_get('split_every'), 250);

			if (false != ($existing_file = $this->file_exists($existing_zips, $youwhat, $index)) && filesize($this->updraft_dir.'/'.$existing_file) > $split_every*1048576) {
				$index++;
				$this->job_file_entities[$youwhat]['index'] = $index;
				$updraftplus->jobdata_set('job_file_entities', $this->job_file_entities);
			}

			// Populate prior parts of $backup_array, if we're on a subsequent zip file
			if ($index > 0) {
				for ($i=0; $i<$index; $i++) {
					$itext = (0 == $i) ? '' : ($i+1);
					// Get the previously-stored filename if possible (which should be always); failing that, base it on the current run

					$zip_file = (isset($this->backup_files_array[$youwhat]) && isset($this->backup_files_array[$youwhat][$i])) ? $this->backup_files_array[$youwhat][$i] : $backup_file_basename.'-'.$youwhat.$itext.'.zip';

					$backup_array[$youwhat][$i] = $zip_file;
					$z = $this->updraft_dir.'/'.$zip_file;
					$itext = (0 == $i) ? '' : $i;

					$fs_key = $youwhat.$itext.'-size';
					if (file_exists($z)) {
						$backup_array[$fs_key] = filesize($z);
					} elseif (isset($this->backup_files_array[$fs_key])) {
						$backup_array[$fs_key] = $this->backup_files_array[$fs_key];
					}
				}
			}

			// I am not certain that all the conditions in here are possible. But there's no harm.
			if ('finished' == $job_status) {
				// Add the final part of the array
				if ($index > 0) {
					$zip_file = (isset($this->backup_files_array[$youwhat]) && isset($this->backup_files_array[$youwhat][$index])) ? $this->backup_files_array[$youwhat][$index] : $backup_file_basename.'-'.$youwhat.($index+1).'.zip';
					$z = $this->updraft_dir.'/'.$zip_file;
					$fs_key = $youwhat.$index.'-size';
					$backup_array[$youwhat][$index] = $zip_file;
					if (file_exists($z)) {
						$backup_array[$fs_key] = filesize($z);
					} elseif (isset($this->backup_files_array[$fs_key])) {
						$backup_array[$fs_key] = $this->backup_files_array[$fs_key];
					}
				} else {
					$zip_file = (isset($this->backup_files_array[$youwhat]) && isset($this->backup_files_array[$youwhat][0])) ? $this->backup_files_array[$youwhat][0] : $backup_file_basename.'-'.$youwhat.'.zip';

					$backup_array[$youwhat] = $zip_file;
					$fs_key=$youwhat.'-size';

					if (file_exists($zip_file)) {
						$backup_array[$fs_key] = filesize($zip_file);
					} elseif (isset($this->backup_files_array[$fs_key])) {
						$backup_array[$fs_key] = $this->backup_files_array[$fs_key];
					}
				}
			} else {

				$which_entity++;
				$updraftplus->jobdata_set('filecreating_substatus', array('e' => $youwhat, 'i' => $which_entity, 't' => count($this->job_file_entities)));

				if ('others' == $youwhat) $updraftplus->log("Beginning backup of other directories found in the content directory (index: $index)");

				// Apply a filter to allow add-ons to provide their own method for creating a zip of the entity
				$created = apply_filters('updraftplus_backup_makezip_'.$youwhat, $whichdir, $backup_file_basename, $index);

				// If the filter did not lead to something being created, then use the default method
				if ($created === $whichdir) {

					// http://www.phpconcept.net/pclzip/user-guide/53
					/* First parameter to create is:
						An array of filenames or dirnames,
						or
						A string containing the filename or a dirname,
						or
						A string containing a list of filename or dirname separated by a comma.
					*/

					if ('others' == $youwhat) {
						$dirlist = $updraftplus->backup_others_dirlist(true);
					} elseif ('uploads' == $youwhat) {
						$dirlist = $updraftplus->backup_uploads_dirlist(true);
					} else {
						$dirlist = $whichdir;
						if (is_array($dirlist)) $dirlist = array_shift($dirlist);
					}

					if (!empty($dirlist)) {
						$created = $this->create_zip($dirlist, $youwhat, $backup_file_basename, $index);
						// Now, store the results
						if (!is_string($created) && !is_array($created)) $updraftplus->log("$youwhat: create_zip returned an error");
					} else {
						$updraftplus->log("No backup of $youwhat: there was nothing found to backup");
					}
				}

				if ($created != $whichdir && (is_string($created) || is_array($created))) {
					if (is_string($created)) $created =array($created);
					foreach ($created as $fname) {
						$backup_array[$youwhat][$index] = $fname;
						$itext = (0 == $index) ? '' : $index;
						$index++;
						$backup_array[$youwhat.$itext.'-size'] = filesize($this->updraft_dir.'/'.$fname);
					}
				}

				$this->job_file_entities[$youwhat]['index'] = $this->index;
				$updraftplus->jobdata_set('job_file_entities', $this->job_file_entities);

			}
		}

		return $backup_array;
	}

	/**
	 * This uses a saved status indicator; its only purpose is to indicate *total* completion; there is no actual danger, just wasted time, in resuming when it was not needed. So the saved status indicator just helps save resources.
	 *
	 * @param Integer $resumption_no Check for first run
	 *
	 * @return Array
	 */
	public function resumable_backup_of_files($resumption_no) {
		global $updraftplus;
		// Backup directories and return a numerically indexed array of file paths to the backup files
		$bfiles_status = $updraftplus->jobdata_get('backup_files');
		$this->backup_files_array = $updraftplus->jobdata_get('backup_files_array');

		if (!is_array($this->backup_files_array)) $this->backup_files_array = array();
		if ('finished' == $bfiles_status) {
			$updraftplus->log("Creation of backups of directories: already finished");
			// Check for recent activity
			foreach ($this->backup_files_array as $files) {
				if (!is_array($files)) $files =array($files);
				foreach ($files as $file) $updraftplus->check_recent_modification($this->updraft_dir.'/'.$file);
			}
		} elseif ('begun' == $bfiles_status) {
			$this->first_run = apply_filters('updraftplus_filerun_firstrun', 0);
			if ($resumption_no > $this->first_run) {
				$updraftplus->log("Creation of backups of directories: had begun; will resume");
			} else {
				$updraftplus->log("Creation of backups of directories: beginning");
			}
			$updraftplus->jobdata_set('jobstatus', 'filescreating');
			$this->backup_files_array = $this->backup_dirs($bfiles_status);
			$updraftplus->jobdata_set('backup_files_array', $this->backup_files_array);
			$updraftplus->jobdata_set('backup_files', 'finished');
			$updraftplus->jobdata_set('jobstatus', 'filescreated');
		} else {
			// This is not necessarily a backup run which is meant to contain files at all
			$updraftplus->log('This backup run is not intended for files - skipping');
			return array();
		}

		/*
		// DOES NOT WORK: there is no crash-safe way to do this here - have to be renamed at cloud-upload time instead
		$new_backup_array = array();
		foreach ($backup_array as $entity => $files) {
			if (!is_array($files)) $files=array($files);
			$outof = count($files);
			foreach ($files as $ind => $file) {
				$nval = $file;
				if (preg_match('/^(backup_[\-0-9]{15}_.*_[0-9a-f]{12}-[\-a-z]+)([0-9]+)?\.zip$/i', $file, $matches)) {
					$num = max((int)$matches[2],1);
					$new = $matches[1].$num.'of'.$outof.'.zip';
					if (file_exists($this->updraft_dir.'/'.$file)) {
						if (@rename($this->updraft_dir.'/'.$file, $this->updraft_dir.'/'.$new)) {
							$updraftplus->log(sprintf("Renaming: %s to %s", $file, $new));
							$nval = $new;
						}
					} elseif (file_exists($this->updraft_dir.'/'.$new)) {
						$nval = $new;
					}
				}
				$new_backup_array[$entity][$ind] = $nval;
			}
		}
		*/
		return $this->backup_files_array;
	}

	/**
	 * This function is resumable, using the following method:
	 * Each table is written out to ($final_filename).table.tmp
	 * When the writing finishes, it is renamed to ($final_filename).table
	 * When all tables are finished, they are concatenated into the final file
	 *
	 * @param String $already_done Status of backup
	 * @param String $whichdb      Indicated which database is being backed up
	 * @param Array  $dbinfo       is only used when whichdb != 'wp'; and the keys should be: user, pass, name, host, prefix
	 *
	 * @return Boolean|String - the basename of the database backup, or false for failure
	 */
	public function backup_db($already_done = 'begun', $whichdb = 'wp', $dbinfo = array()) {

		global $updraftplus, $wpdb;

		$this->whichdb = $whichdb;
		$this->whichdb_suffix = ('wp' == $whichdb) ? '' : $whichdb;

		if (!$updraftplus->backup_time) $updraftplus->backup_time_nonce();
		if (!$updraftplus->opened_log_time) $updraftplus->logfile_open($updraftplus->nonce);

		if ('wp' == $this->whichdb) {
			$this->wpdb_obj = $wpdb;
			// The table prefix after being filtered - i.e. what filters what we'll actually backup
			$this->table_prefix = $updraftplus->get_table_prefix(true);
			// The unfiltered table prefix - i.e. the real prefix that things are relative to
			$this->table_prefix_raw = $updraftplus->get_table_prefix(false);
			$dbinfo['host'] = DB_HOST;
			$dbinfo['name'] = DB_NAME;
			$dbinfo['user'] = DB_USER;
			$dbinfo['pass'] = DB_PASSWORD;
		} else {
			if (!is_array($dbinfo) || empty($dbinfo['host'])) return false;
			// The methods that we may use: check_connection (WP>=3.9), get_results, get_row, query
			$this->wpdb_obj = new UpdraftPlus_WPDB_OtherDB($dbinfo['user'], $dbinfo['pass'], $dbinfo['name'], $dbinfo['host']);
			if (!empty($this->wpdb_obj->error)) {
				$updraftplus->log($dbinfo['user'].'@'.$dbinfo['host'].'/'.$dbinfo['name'].' : database connection attempt failed');
				$updraftplus->log($dbinfo['user'].'@'.$dbinfo['host'].'/'.$dbinfo['name'].' : '.__('database connection attempt failed.', 'updraftplus').' '.__('Connection failed: check your access details, that the database server is up, and that the network connection is not firewalled.', 'updraftplus'), 'error');
				return $updraftplus->log_wp_error($this->wpdb_obj->error);
			}
			$this->table_prefix = $dbinfo['prefix'];
			$this->table_prefix_raw = $dbinfo['prefix'];
		}
		
		$this->dbinfo = $dbinfo;

		do_action('updraftplus_backup_db_begin', $whichdb, $dbinfo, $already_done, $this);

		UpdraftPlus_Database_Utility::set_sql_mode(array(), array('ANSI_QUOTES'), $this->wpdb_obj);

		$errors = 0;

		$use_time = apply_filters('updraftplus_base_backup_timestamp', $updraftplus->backup_time);
		$file_base = $this->get_backup_file_basename_from_time($use_time);
		$backup_file_base = $this->updraft_dir.'/'.$file_base;

		if ('finished' == $already_done) return basename($backup_file_base).'-db'.(('wp' == $whichdb) ? '' : $whichdb).'.gz';
		if ('encrypted' == $already_done) return basename($backup_file_base).'-db'.(('wp' == $whichdb) ? '' : $whichdb).'.gz.crypt';

		$updraftplus->jobdata_set('jobstatus', 'dbcreating'.$this->whichdb_suffix);

		$binsqldump = $updraftplus->find_working_sqldump();

		$total_tables = 0;

		// WP 3.9 onwards - https://core.trac.wordpress.org/browser/trunk/src/wp-includes/wp-db.php?rev=27925 - check_connection() allows us to get the database connection back if it had dropped
		if ('wp' == $whichdb && method_exists($this->wpdb_obj, 'check_connection') && (!defined('UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS') || !UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS)) {
			if (!$this->wpdb_obj->check_connection(false)) {
				UpdraftPlus_Job_Scheduler::reschedule(60);
				$updraftplus->log("It seems the database went away; scheduling a resumption and terminating for now");
				UpdraftPlus_Job_Scheduler::record_still_alive();
				die;
			}
		}

		// SHOW FULL - so that we get to know whether it's a BASE TABLE or a VIEW
		$all_tables = $this->wpdb_obj->get_results("SHOW FULL TABLES", ARRAY_N);
		
		if (empty($all_tables) && !empty($this->wpdb_obj->last_error)) {
			$all_tables = $this->wpdb_obj->get_results("SHOW TABLES", ARRAY_N);
			$all_tables = array_map(array($this, 'cb_get_name_base_type'), $all_tables);
		} else {
			$all_tables = array_map(array($this, 'cb_get_name_type'), $all_tables);
		}

		// If this is not the WP database, then we do not consider it a fatal error if there are no tables
		if ('wp' == $whichdb && 0 == count($all_tables)) {
			$extra = ($updraftplus->newresumption_scheduled) ? ' - '.__('please wait for the rescheduled attempt', 'updraftplus') : '';
			$updraftplus->log("Error: No WordPress database tables found (SHOW TABLES returned nothing)".$extra);
			$updraftplus->log(__("No database tables found", 'updraftplus').$extra, 'error');
			die;
		}

		// Put the options table first
		usort($all_tables, array($this, 'backup_db_sorttables'));
		
		$all_table_names = array_map(array($this, 'cb_get_name'), $all_tables);

		if (!UpdraftPlus_Filesystem_Functions::really_is_writable($this->updraft_dir)) {
			$updraftplus->log("The backup directory (".$this->updraft_dir.") could not be written to (could be account/disk space full, or wrong permissions).");
			$updraftplus->log($this->updraft_dir.": ".__('The backup directory is not writable (or disk space is full) - the database backup is expected to shortly fail.', 'updraftplus'), 'warning');
			// Why not just fail now? We saw a bizarre case when the results of really_is_writable() changed during the run.
		}

		// This check doesn't strictly get all possible duplicates; it's only designed for the case that can happen when moving between deprecated Windows setups and Linux
		$this->duplicate_tables_exist = false;
		foreach ($all_table_names as $table) {
			if (strtolower($table) != $table && in_array(strtolower($table), $all_table_names)) {
				$this->duplicate_tables_exist = true;
				$updraftplus->log("Tables with names differing only based on case-sensitivity exist in the MySQL database: $table / ".strtolower($table));
			}
		}
		$how_many_tables = count($all_tables);

		$stitch_files = array();
		$found_options_table = false;
		$is_multisite = is_multisite();

		$anonymisation_options = $updraftplus->jobdata_get('anonymisation_options', array());

		if (!empty($anonymisation_options)) {
			$updraftplus->log("Anonymisation options have been set, so mysqldump (which does not support them) will be disabled.");
		}

		// Gather the list of files that look like partial table files once only
		$potential_stitch_files = array();
		$table_file_prefix_base= $file_base.'-db'.$this->whichdb_suffix.'-table-';
		if (false !== ($dir_handle = opendir($this->updraft_dir))) {
			while (false !== ($e = readdir($dir_handle))) {
				// The 'r' in 'tmpr' indicates that the new scheme is being used. N.B. That does *not* imply that the table has a usable primary key.
				if (!is_file($this->updraft_dir.'/'.$e)) continue;
				if (preg_match('#'.$table_file_prefix_base.'.*\.table\.tmpr?(\d+)\.gz$#', $e, $matches)) {
					// We need to stich them in order
					$potential_stitch_files[] = $e;
				}
			}
		} else {
			$updraftplus->log("Error: Failed to open directory for reading");
			$updraftplus->log(__("Failed to open directory for reading:", 'updraftplus').' '.$this->updraft_dir, 'error');
		}
		
		$errors_at_all_tables_start = $updraftplus->error_count();
		
		foreach ($all_tables as $ti) {

			$table = $ti['name'];
			$stitch_files[$table] = array();
			$table_type = $ti['type'];
			$errors_at_table_start = $updraftplus->error_count();
			
			$this->many_rows_warning = false;
			$total_tables++;

			// Increase script execution time-limit to 15 min for every table.
			if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			// The table file may already exist if we have produced it on a previous run
			$table_file_prefix = $file_base.'-db'.$this->whichdb_suffix.'-table-'.$table.'.table';

			if ('wp' == $whichdb && (strtolower($this->table_prefix_raw.'options') == strtolower($table) || ($is_multisite && (strtolower($this->table_prefix_raw.'sitemeta') == strtolower($table) || strtolower($this->table_prefix_raw.'1_options') == strtolower($table))))) $found_options_table = true;

			// Already finished?
			if (file_exists($this->updraft_dir.'/'.$table_file_prefix.'.gz')) {
				$stitched = count($stitch_files, COUNT_RECURSIVE);
				$skip_dblog = (($stitched > 10 && 0 != $stitched % 20) || ($stitched > 100 && 0 != $stitched % 100));
				$updraftplus->log("Table $table: corresponding file already exists; moving on", 'notice', false, $skip_dblog);
				
				$max_record = false;
				foreach ($potential_stitch_files as $e) {
					// The 'r' in 'tmpr' indicates that the new scheme is being used. N.B. That does *not* imply that the table has a usable primary key.
					if (preg_match('#'.$table_file_prefix.'\.tmpr?(\d+)\.gz$#', $e, $matches)) {
						// We need to stich them in order
						$stitch_files[$table][$matches[1]] = $e;
						if (false === $max_record || $matches[1] > $max_record) $max_record = $matches[1];
					}
				}
				$stitch_files[$table][$max_record+1] = $table_file_prefix.'.gz';
				
				// Move on to the next table
				continue;
			}

			// === is needed with strpos/stripos, otherwise 'false' matches (i.e. prefix does not match)
			if (empty($this->table_prefix) || (!$this->duplicate_tables_exist && 0 === stripos($table, $this->table_prefix)) || ($this->duplicate_tables_exist && 0 === strpos($table, $this->table_prefix))) {

				// Skip table due to filter?
				if (!apply_filters('updraftplus_backup_table', true, $table, $this->table_prefix, $whichdb, $dbinfo)) {
					$updraftplus->log("Skipping table (filtered): $table");
					if (empty($this->skipped_tables)) $this->skipped_tables = array();

					// whichdb could be an int in which case to get the name of the database and the array key use the name from dbinfo
					$key = ('wp' === $whichdb) ? 'wp' : $dbinfo['name'];

					if (empty($this->skipped_tables[$key])) $this->skipped_tables[$key] = array();
					$this->skipped_tables[$key][] = $table;

					$total_tables--;
					continue;
				}

				add_filter('updraftplus_backup_table_sql_where', array($this, 'backup_exclude_jobdata'), 3, 10);

				$updraftplus->jobdata_set('dbcreating_substatus', array('t' => $table, 'i' => $total_tables, 'a' => $how_many_tables));
				
				// .tmp.gz is the current temporary file. When the row limit has been reached, it is moved to .tmp1.gz, .tmp2.gz, etc. (depending on which already exist). When we're all done, then they all get stitched in.
				
				$db_temp_file = $this->updraft_dir.'/'.$table_file_prefix.'.tmp.gz';
				$updraftplus->check_recent_modification($db_temp_file);
			
				// Open file, store the handle
				if (false === $this->backup_db_open($db_temp_file, true)) return false;

				$table_status = $this->wpdb_obj->get_row("SHOW TABLE STATUS WHERE Name='$table'");
				
				// Create the preceding SQL statements for the table
				$this->stow("# " . sprintf('Table: %s', UpdraftPlus_Manipulation_Functions::backquote($table)) . "\n");
				
				// Meaning: false = don't yet know; true = know and have logged it; integer = the expected number
				$this->expected_rows = false;
				
				if (isset($table_status->Rows)) {
					$this->expected_rows = $table_status->Rows;
				}

				// If no check-in last time, then we could in future try the other method (but - any point in retrying slow method on large tables??)

				// New Jul 2014: This attempt to use bindump instead at a lower threshold is quite conservative - only if the last successful run was exactly two resumptions ago - may be useful to expand
				$bindump_threshold = (!$updraftplus->something_useful_happened && !empty($updraftplus->current_resumption) && (2 == $updraftplus->current_resumption - $updraftplus->last_successful_resumption)) ? 1000 : 8000;

				if (isset($table_status->Rows) && ($table_status->Rows > $bindump_threshold || (defined('UPDRAFTPLUS_ALWAYS_TRY_MYSQLDUMP') && UPDRAFTPLUS_ALWAYS_TRY_MYSQLDUMP)) && is_string($binsqldump) && empty($anonymisation_options)) {
					if (!is_bool($this->expected_rows)) {
						$this->log_expected_rows($table, $this->expected_rows);
						$this->expected_rows = true;
					}
					$bindump = $this->backup_table_bindump($binsqldump, $table);
				} else {
					$bindump = false;
				}
				
				// Means "start of table". N.B. The meaning of an integer depends upon whether the table has a usable primary key or not.
				$start_record = true;
				$can_use_primary_key = apply_filters('updraftplus_can_use_primary_key_default', true, $table);
				foreach ($potential_stitch_files as $e) {
					// The 'r' in 'tmpr' indicates that the new scheme is being used. N.B. That does *not* imply that the table has a usable primary key.
					if (preg_match('#'.$table_file_prefix.'\.tmp(r)?(\d+)\.gz$#', $e, $matches)) {
						$stitch_files[$table][$matches[2]] = $e;
						if (true === $start_record || $matches[2] > $start_record) $start_record = $matches[2];
						// Legacy scheme. The purpose of this is to prevent backups failing if one is in progress during an upgrade to a new version that implements the new scheme
						if ('r' !== $matches[1]) $can_use_primary_key = false;
					}
				}
				
				// Legacy file-naming scheme in use
				if (false === $can_use_primary_key && true !== $start_record) {
					$start_record = ($start_record + 100) * 1000;
				}
				
				if (true !== $bindump) {
				
					while (!is_array($start_record) && !is_wp_error($start_record)) {
						$start_record = $this->backup_table($table, $table_type, $start_record, $can_use_primary_key);
						if (is_integer($start_record) || is_array($start_record)) {
						
							$this->backup_db_close();
							
							// Add one here in case no records were returned - don't want to over-write the previous file
							$use_record = is_array($start_record) ? (isset($start_record['next_record']) ? $start_record['next_record']+1 : false) : $start_record;
							if (!$can_use_primary_key) $use_record = (ceil($use_record/100000)-1) * 100;
							
							if (false !== $use_record) {
								// N.B. Renaming using the *next* record is intentional - it allows UD to know where to resume from.
								$rename_base = $table_file_prefix.'.tmp'.($can_use_primary_key ? 'r' : '').$use_record.'.gz';
								
								rename($db_temp_file, $this->updraft_dir.'/'.$rename_base);
								$stitch_files[$table][$use_record] = $rename_base;
							}
							
							UpdraftPlus_Job_Scheduler::something_useful_happened();
							
							if (false === $this->backup_db_open($db_temp_file, true)) return false;
							
						} elseif (is_wp_error($start_record)) {
							$message = "Error (table=$table, type=$table_type) (".$start_record->get_error_code()."): ".$start_record->get_error_message();
							$updraftplus->log($message);
							// If it's a view, then the problem isn't recoverable; but views don't contain actual data except in the definition, which is likely in code, so we should not consider this a fatal error
							$level = 'error';
							if ('view' == strtolower($table_type) && 'table_details_error' == $start_record->get_error_code()) {
								$level = 'warning';
								$this->stow("# $message\n");
							}
							$updraftplus->log(__("Failed to backup database table:", 'updraftplus').' '.$start_record->get_error_message().' ('.$start_record->get_error_code().')', $level);
						}
							
					}
				}

				// If we got this far, then there were enough resources; the warning can be removed
				if (!empty($this->many_rows_warning)) $updraftplus->log_remove_warning('manyrows_'.$this->whichdb_suffix.$table);

				$this->backup_db_close();

				if ($updraftplus->error_count() > $errors_at_table_start) {
					
					$updraftplus->log('Errors occurred during backing up the table; therefore the open file will be removed');
					@unlink($db_temp_file); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					
				} else {
				
					// Renaming the file indicates that writing to it finished
					rename($db_temp_file, $this->updraft_dir.'/'.$table_file_prefix.'.gz');
					UpdraftPlus_Job_Scheduler::something_useful_happened();
					
					$final_stitch_value = empty($stitch_files[$table]) ? 1 : max(array_keys($stitch_files[$table])) + 1;
					
					$stitch_files[$table][$final_stitch_value] = $table_file_prefix.'.gz';
					
					$total_db_size = 0;
					// This is more verbose than it would be if we weren't supporting PHP 5.2
					foreach ($stitch_files[$table] as $basename) {
						$total_db_size += filesize($this->updraft_dir.'/'.$basename);
					}
					
					$updraftplus->log("Table $table: finishing file(s) (".count($stitch_files[$table]).', '.round($total_db_size/1024, 1).' KB)', 'notice', false, false);
				}
				
			} else {
				$total_tables--;
				$updraftplus->log("Skipping table (lacks our prefix (".$this->table_prefix.")): $table");
				if (empty($this->skipped_tables)) $this->skipped_tables = array();
				// whichdb could be an int in which case to get the name of the database and the array key use the name from dbinfo
				$key = ('wp' === $whichdb) ? 'wp' : $dbinfo['name'];
				if (empty($this->skipped_tables[$key])) $this->skipped_tables[$key] = array();
				$this->skipped_tables[$key][] = $table;
			}
		}

		if ('wp' == $whichdb) {
			if (!$found_options_table) {
				if ($is_multisite) {
					$updraftplus->log(__('The database backup appears to have failed', 'updraftplus').' - '.__('no options or sitemeta table was found', 'updraftplus'), 'warning', 'optstablenotfound');
				} else {
					$updraftplus->log(__('The database backup appears to have failed', 'updraftplus').' - '.__('the options table was not found', 'updraftplus'), 'warning', 'optstablenotfound');
				}
				$time_this_run = time()-$updraftplus->opened_log_time;
				if ($time_this_run > 2000) {
					// Have seen this happen; not sure how, but it was apparently deterministic; if the current process had been running for a long time, then apparently all database commands silently failed.
					// If we have been running that long, then the resumption may be far off; bring it closer
					UpdraftPlus_Job_Scheduler::reschedule(60);
					$updraftplus->log("Have been running very long, and it seems the database went away; scheduling a resumption and terminating for now");
					UpdraftPlus_Job_Scheduler::record_still_alive();
					die;
				}
			} else {
				$updraftplus->log_remove_warning('optstablenotfound');
			}
		}

		if ($updraftplus->error_count() > $errors_at_all_tables_start) {
			$updraftplus->log('Errors occurred whilst backing up the tables; will cease and wait for resumption');
			die;
		}
		
		// Race detection - with zip files now being resumable, these can more easily occur, with two running side-by-side
		$backup_final_file_name = $backup_file_base.'-db'.$this->whichdb_suffix.'.gz';
		$time_now = time();
		$time_mod = (int) @filemtime($backup_final_file_name);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if (file_exists($backup_final_file_name) && $time_mod>100 && ($time_now-$time_mod)<30) {
			UpdraftPlus_Job_Scheduler::terminate_due_to_activity($backup_final_file_name, $time_now, $time_mod);
		}
		
		if (file_exists($backup_final_file_name)) {
			$updraftplus->log("The final database file ($backup_final_file_name) exists, but was apparently not modified within the last 30 seconds (time_mod=$time_mod, time_now=$time_now, diff=".($time_now-$time_mod)."). Thus we assume that another UpdraftPlus terminated; thus we will continue.");
		}

		// Finally, stitch the files together
		if (!function_exists('gzopen')) {
			$updraftplus->log("PHP function is disabled; abort expected: gzopen()");
		}

		$decompress_mode = (defined('UPDRAFTPLUS_DB_STICH_DECOMPRESS') && UPDRAFTPLUS_DB_STICH_DECOMPRESS);
		
		if (false === $this->backup_db_open($backup_final_file_name, true)) return false;

		$this->backup_db_header();
		
		// Re-open in plain binary append mode
		if (!$decompress_mode) {
			$this->backup_db_close();
			if (false === $this->backup_db_open($backup_final_file_name, false, true)) return false;
		}
		
		// We delay the unlinking because if two runs go concurrently and fail to detect each other (should not happen, but there's no harm in assuming the detection failed) then that would lead to files missing from the db dump
		$unlink_files = array();

		$sind = 1;
		
		// Happily they have the same syntax (as far as we need it)
		// Concatenating gz files produces a valid gz file. So, decompressing is not necessary. We retain the possibility for debugging and the possibility of broken implementations.
		$open_function = $decompress_mode ? 'gzopen' : 'fopen';
		$fgets_function = $decompress_mode ? 'gzgets' : 'fgets';
		$close_function = $decompress_mode ? 'gzclose' : 'fclose';
		
		foreach ($stitch_files as $table => $table_stitch_files) {
			ksort($table_stitch_files);
			foreach ($table_stitch_files as $table_file) {
				$updraftplus->log("{$table_file} ($sind/$how_many_tables/$open_function): adding to final database dump");

				if (filesize($this->updraft_dir.'/'.$table_file) < 27 && '.gz' == substr($table_file, -3, 3)) {
					// It's a null gzip file. Don't waste time on gzopen/gzgets/gzclose. This micro-optimisation was added after seeing a site with >3000 files that was running out of time (it could apparently process 30 files/second)
					$unlink_files[] = $this->updraft_dir.'/'.$table_file;
				} elseif (!$handle = call_user_func($open_function, $this->updraft_dir.'/'.$table_file, 'r')) {
					$updraftplus->log("Error: Failed to open database file for reading: ${table_file}");
					$updraftplus->log(__("Failed to open database file for reading:", 'updraftplus').' '.$table_file, 'error');
					$errors++;
				} else {
					while ($line = call_user_func($fgets_function, $handle, 65536)) {
						$this->stow($line);
					}
					call_user_func($close_function, $handle);
					$unlink_files[] = $this->updraft_dir.'/'.$table_file;
				}
				$sind++;
				// Came across a database with 7600 tables... adding them all took over 500 seconds; and so when the resumption started up, no activity was detected
				if (0 == $sind % 100) UpdraftPlus_Job_Scheduler::something_useful_happened();
			}
		}

		// Re-open in gz append mode
		if (!$decompress_mode) {
			$this->backup_db_close();
			if (false === $this->backup_db_open($backup_final_file_name, true, true)) return false;
		}
		
		// DB triggers
		if ($this->wpdb_obj->get_results("SHOW TRIGGERS")) {
			// N.B. DELIMITER is not a valid SQL command; you cannot pass it to the server. It has to be interpreted by the interpreter - e.g. /usr/bin/mysql, or UpdraftPlus, and used to interpret what follows. The effect of this is that using it means that some SQL clients will stumble; but, on the other hand, failure to use it means that others that don't have special support for CREATE TRIGGER may stumble, because they may feed incomplete statements to the SQL server. Since /usr/bin/mysql uses it, we choose to support it too (both reading and writing).
			// Whatever the delimiter is set to needs to be used in the DROP TRIGGER and CREATE TRIGGER commands in this section further down.
			$this->stow("DELIMITER ;;\n\n");
			foreach ($all_tables as $ti) {
				$table = $ti['name'];
				if (!empty($this->skipped_tables)) {
					if ('wp' == $this->whichdb) {
						if (in_array($table, $this->skipped_tables['wp'])) continue;
					} elseif (isset($this->skipped_tables[$this->dbinfo['name']])) {
						if (in_array($table, $this->skipped_tables[$this->dbinfo['name']])) continue;
					}
				}
				$table_triggers = $this->wpdb_obj->get_results($wpdb->prepare("SHOW TRIGGERS LIKE %s", $table), ARRAY_A);
				if ($table_triggers) {
					$this->stow("\n\n# Triggers of  ".UpdraftPlus_Manipulation_Functions::backquote($table)."\n\n");
					foreach ($table_triggers as $trigger) {
						$trigger_name = $trigger['Trigger'];
						$trigger_time = $trigger['Timing'];
						$trigger_event = $trigger['Event'];
						$trigger_statement = $trigger['Statement'];
						// Since trigger name can include backquotes and trigger name is typically enclosed with backquotes as well, the backquote escaping for the trigger name can be done by adding a leading backquote
						$quoted_escaped_trigger_name = UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $trigger_name));
						$this->stow("DROP TRIGGER IF EXISTS $quoted_escaped_trigger_name;;\n");
						$trigger_query = "CREATE TRIGGER $quoted_escaped_trigger_name $trigger_time $trigger_event ON ".UpdraftPlus_Manipulation_Functions::backquote($table)." FOR EACH ROW $trigger_statement;;";
						$this->stow("$trigger_query\n\n");
					}
				}
			}
			$this->stow("DELIMITER ;\n\n");
		}

		// DB Stored Routines
		$stored_routines = UpdraftPlus_Database_Utility::get_stored_routines();
		if (is_array($stored_routines) && !empty($stored_routines)) {
			$updraftplus->log("Dumping routines for database {$this->dbinfo['name']}");
			$this->stow("\n\n# Dumping routines for database ".UpdraftPlus_Manipulation_Functions::backquote($this->dbinfo['name'])."\n\n");
			$this->stow("DELIMITER ;;\n\n");
			foreach ($stored_routines as $routine) {
				$routine_name = $routine['Name'];
				// Since routine name can include backquotes and routine name is typically enclosed with backquotes as well, the backquote escaping for the routine name can be done by adding a leading backquote
				$quoted_escaped_routine_name = UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $routine_name));
				$this->stow("DROP {$routine['Type']} IF EXISTS $quoted_escaped_routine_name;;\n\n");
				$this->stow($routine['Create '.ucfirst(strtolower($routine['Type']))]."\n\n;;\n\n");
				$updraftplus->log("Dumping routine: {$routine['Name']}");
			}
			$this->stow("DELIMITER ;\n\n");
		} elseif (is_wp_error($stored_routines)) {
			$updraftplus->log($stored_routines->get_error_message());
		}

		$this->stow("/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n");

		$updraftplus->log($file_base.'-db'.$this->whichdb_suffix.'.gz: finished writing out complete database file ('.round(filesize($backup_final_file_name)/1024, 1).' KB)');
		if (!$this->backup_db_close()) {
			$updraftplus->log('An error occurred whilst closing the final database file');
			$updraftplus->log(__('An error occurred whilst closing the final database file', 'updraftplus'), 'error');
			$errors++;
		}

		foreach ($unlink_files as $unlink_file) {
			@unlink($unlink_file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

		if ($errors > 0) return false;
		
		// We no longer encrypt here - because the operation can take long, we made it resumable and moved it to the upload loop
		$updraftplus->jobdata_set('jobstatus', 'dbcreated'.$this->whichdb_suffix);
		
		$checksums = $updraftplus->which_checksums();
		
		$checksum_description = '';
		
		foreach ($checksums as $checksum) {
		
			$cksum = hash_file($checksum, $backup_final_file_name);
			$updraftplus->jobdata_set($checksum.'-db'.(('wp' == $whichdb) ? '0' : $whichdb.'0'), $cksum);
			if ($checksum_description) $checksum_description .= ', ';
			$checksum_description .= "$checksum: $cksum";
		
		}
		
		$updraftplus->log("Total database tables backed up: $total_tables (".basename($backup_final_file_name).", size: ".filesize($backup_final_file_name).", $checksum_description)");
		
		return basename($backup_final_file_name);

	}

	/**
	 * Log the number of expected rows (both to the backup log, and database backup file)
	 *
	 * @param String  $table		 - table name
	 * @param Integer $expected_rows - number of rows
	 * @param Boolean $via_count	 - if the expected number comes via a SELECT COUNT() call
	 */
	private function log_expected_rows($table, $expected_rows, $via_count = false) {
		global $updraftplus;
		$description = $via_count ? 'via COUNT' : 'approximate';
		$updraftplus->log("Table $table: Total expected rows ($description): ".$expected_rows);
		$this->stow("# Approximate rows expected in table: $expected_rows\n");
		if ($expected_rows > UPDRAFTPLUS_WARN_DB_ROWS) {
			$this->many_rows_warning = true;
			$updraftplus->log(sprintf(__("Table %s has very many rows (%s) - we hope your web hosting company gives you enough resources to dump out that table in the backup.", 'updraftplus'), $table, $expected_rows).' '.__('If not, you will need to either remove data from this table, or contact your hosting company to request more resources.', 'updraftplus'), 'warning', 'manyrows_'.$this->whichdb_suffix.$table);
		}
	}
	
	/**
	 * This function will return a SQL WHERE clause to exclude updraft jobdata
	 *
	 * @param array  $where - an array of where clauses to add to
	 * @param string $table - the table we want to add a where clause for
	 *
	 * @return array - returns an array of where clauses for the table
	 */
	public function backup_exclude_jobdata($where, $table) {
		// Don't include the job data for any backups - so that when the database is restored, it doesn't continue an apparently incomplete backup
		if ('wp' == $this->whichdb && (!empty($this->table_prefix) && strtolower($this->table_prefix.'sitemeta') == strtolower($table))) {
			$where[] = 'meta_key NOT LIKE "updraft_jobdata_%"';
		} elseif ('wp' == $this->whichdb && (!empty($this->table_prefix) && strtolower($this->table_prefix.'options') == strtolower($table))) {
			// These might look similar, but the quotes are different
			if ('win' == strtolower(substr(PHP_OS, 0, 3))) {
				$updraft_jobdata = "'updraft_jobdata_%'";
				$site_transient_update = "'_site_transient_update_%'";
			} else {
				$updraft_jobdata = '"updraft_jobdata_%"';
				$site_transient_update = '"_site_transient_update_%"';
			}
			
			$where[] = 'option_name NOT LIKE '.$updraft_jobdata.' AND option_name NOT LIKE '.$site_transient_update.'';
		}

		return $where;
	}

	/**
	 * Produce a dump of the table using a mysqldump binary
	 *
	 * @param String $potsql	 - the path to the mysqldump binary
	 * @param String $table_name - the name of the table being dumped
	 *
	 * @return Boolean - success status
	 */
	private function backup_table_bindump($potsql, $table_name) {

		$microtime = microtime(true);

		global $updraftplus, $wpdb;

		// Deal with Windows/old MySQL setups with erroneous table prefixes differing in case
		// Can't get binary mysqldump to make this transformation
		// $dump_as_table = ($this->duplicate_tables_exist == false && stripos($table, $this->table_prefix) === 0 && strpos($table, $this->table_prefix) !== 0) ? $this->table_prefix.substr($table, strlen($this->table_prefix)) : $table;

		$pfile = md5(time().rand()).'.tmp';
		file_put_contents($this->updraft_dir.'/'.$pfile, "[mysqldump]\npassword=\"".addslashes($this->dbinfo['pass'])."\"\n");

		$where_array = apply_filters('updraftplus_backup_table_sql_where', array(), $table_name, $this);
		$where = '';
		
		if (!empty($where_array) && is_array($where_array)) {
			// N.B. Don't add a WHERE prefix here; most versions of mysqldump silently strip it out, but one was encountered that didn't.
			$first_loop = true;
			foreach ($where_array as $condition) {
				if (!$first_loop) $where .= " AND ";
				$where .= $condition;
				$first_loop = false;
			}
		}

		// Note: escapeshellarg() adds quotes around the string
		if ($where) $where = "--where=".escapeshellarg($where);

		if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
			$exec = "cd ".escapeshellarg(str_replace('/', '\\', $this->updraft_dir))." & ";
		} else {
			$exec = "cd ".escapeshellarg($this->updraft_dir)."; ";
		}

		// Allow --max_allowed_packet to be configured via constant. Experience has shown some customers with complex CMS or pagebuilder setups can have very large postmeta entries.
		$msqld_max_allowed_packet = (defined('UPDRAFTPLUS_MYSQLDUMP_MAX_ALLOWED_PACKET') && (is_int(UPDRAFTPLUS_MYSQLDUMP_MAX_ALLOWED_PACKET) || is_string(UPDRAFTPLUS_MYSQLDUMP_MAX_ALLOWED_PACKET))) ? UPDRAFTPLUS_MYSQLDUMP_MAX_ALLOWED_PACKET : '12M';

		$exec .= "$potsql --defaults-file=$pfile $where --max-allowed-packet=$msqld_max_allowed_packet --quote-names --add-drop-table";
		
		static $mysql_version = null;
		if (null === $mysql_version) {
			$mysql_version = $wpdb->get_var('SELECT VERSION()');
			if ('' == $mysql_version) $mysql_version = $wpdb->db_version();
		}
		if ($mysql_version && version_compare($mysql_version, '5.1', '>=')) {
			$exec .= " --no-tablespaces";
		}
		
		$exec .= " --skip-comments --skip-set-charset --allow-keywords --dump-date --extended-insert --user=".escapeshellarg($this->dbinfo['user'])." ";
		
		$host = $this->dbinfo['host'];
		
		if (preg_match('#^(.*):(\d+)$#', $host, $matches)) {
			// The escapeshellarg() on $matches[2] is only to avoid tripping static analysis tools
			$exec .= "--host=".escapeshellarg($matches[1])." --port=".escapeshellarg($matches[2])." ";
		} elseif (preg_match('#^(.*):(.*)$#', $host, $matches) && file_exists($matches[2])) {
			$exec .= "--host=".escapeshellarg($matches[1])." --socket=".escapeshellarg($matches[2])." ";
		} else {
			$exec .= "--host=".escapeshellarg($host)." ";
		}
		
		$exec .= $this->dbinfo['name']." ".escapeshellarg($table_name);
		
		$ret = false;
		$any_output = false;
		$writes = 0;
		$write_bytes = 0;
		$handle = function_exists('popen') ? popen($exec, 'r') : false;
		if ($handle) {
			while (!feof($handle)) {
				$w = fgets($handle, 1048576);
				if (is_string($w) && $w) {
					$this->stow($w);
					$writes++;
					$write_bytes += strlen($w);
					$any_output = true;
				}
			}
			$ret = pclose($handle);
			// The manual page for pclose() claims that only -1 indicates an error, but this is untrue
			if (0 != $ret) {
				$updraftplus->log("Binary mysqldump: error (code: $ret)");
				// Keep counter of failures? Change value of binsqldump?
				$ret = false;
			} else {
				if ($any_output) {
					$updraftplus->log("Table $table_name: binary mysqldump finished (writes: $writes, bytes $write_bytes, return code $ret) in ".sprintf("%.02f", max(microtime(true)-$microtime, 0.00001))." seconds");
					$ret = true;
				}
			}
		} else {
			$updraftplus->log("Binary mysqldump error: bindump popen failed");
		}

		// Clean temporary files
		@unlink($this->updraft_dir.'/'.$pfile);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		return $ret;

	}

	/**
	 * Write out the initial backup information for a table to the currently open file
	 *
	 * @param String $table			  - Full name of database table to backup
	 * @param String $dump_as_table	  - Table name to use when writing out
	 * @param String $table_type	  - Table type - 'VIEW' is supported; otherwise it is treated as an ordinary table
	 * @param Array  $table_structure - Table structure as returned by a DESCRIBE command
	 */
	private function write_table_backup_beginning($table, $dump_as_table, $table_type, $table_structure) {
	
		$this->stow("\n# Delete any existing table ".UpdraftPlus_Manipulation_Functions::backquote($table)."\n\nDROP TABLE IF EXISTS " . UpdraftPlus_Manipulation_Functions::backquote($dump_as_table).";\n");
		
		if ('VIEW' == $table_type) {
			$this->stow("DROP VIEW IF EXISTS " . UpdraftPlus_Manipulation_Functions::backquote($dump_as_table) . ";\n");
		}
		
		$description = ('VIEW' == $table_type) ? 'view' : 'table';
		
		$this->stow("\n# Table structure of $description ".UpdraftPlus_Manipulation_Functions::backquote($table)."\n\n");
		
		$create_table = $this->wpdb_obj->get_results("SHOW CREATE TABLE ".UpdraftPlus_Manipulation_Functions::backquote($table), ARRAY_N);
		if (false === $create_table) {
			$this->stow("#\n# Error with SHOW CREATE TABLE for $table\n#\n");
		}
		$create_line = UpdraftPlus_Manipulation_Functions::str_lreplace('TYPE=', 'ENGINE=', $create_table[0][1]);

		// Remove PAGE_CHECKSUM parameter from MyISAM - was internal, undocumented, later removed (so causes errors on import)
		if (preg_match('/ENGINE=([^\s;]+)/', $create_line, $eng_match)) {
			$engine = $eng_match[1];
			if ('myisam' == strtolower($engine)) {
				$create_line = preg_replace('/PAGE_CHECKSUM=\d\s?/', '', $create_line, 1);
			}
		}

		if ($dump_as_table !== $table) $create_line = UpdraftPlus_Manipulation_Functions::str_replace_once($table, $dump_as_table, $create_line);

		$this->stow($create_line.' ;');
		
		if (false === $table_structure) {
			$this->stow("#\n# Error getting $description structure of $table\n#\n");
		}

		// Add a comment preceding the beginning of the data
		$this->stow("\n\n# ".sprintf("Data contents of $description %s", UpdraftPlus_Manipulation_Functions::backquote($table))."\n\n");

	}
	
	/**
	 * Suggest a beginning value for how many rows to fetch in each SELECT statement (before taking into account resumptions)
	 *
	 * @param String $table - the full table name
	 *
	 * @return Integer
	 */
	private function get_rows_on_first_fetch($table) {

		// In future, we could run over the table definition; if it is all non-massive defined lengths, we could base a calculation on that.
	
		if ($this->table_prefix_raw.'term_relationships' == $table) {
			// This table is known to have very small data lengths
			$rows = 100000;
		} elseif (preg_match('/meta$/i', $table)) {
			// Meta-data rows tend to be short *on average*. 10MB / 4000 rows = 2.6KB/row, so this is still quite conservative.
			$rows = 4000;
		} else {
			// The very conservative default
			$rows = 1000;
		}
	
		return $rows;
	
	}
	
	/**
	 * Suggest how many rows to fetch in each SELECT statement
	 *
	 * @param String		  $table					- the table being fetched
	 * @param Boolean		  $allow_further_reductions - whether to enable a second level of reductions (i.e. even less rows)
	 * @param Boolean		  $is_first_fetch_for_table - whether this is the first fetch on this table
	 * @param Integer|Boolean $expected_rows			- if an integer, an estimate of the number of rows
	 * @param Boolean		  $expected_via_count		- if $expected_rows is an integer, then this indicates whether the estimate was made via a SELECT COUNT() statement
	 *
	 * @return Integer
	 */
	private function number_of_rows_to_fetch($table, $allow_further_reductions, $is_first_fetch_for_table, $expected_rows = false, $expected_via_count = false) {

		global $updraftplus;
		
		// This used to be fixed at 500; but we (after a long time) saw a case that looked like an out-of-memory even at this level. Now that we have implemented resumptions, the risk of timeouts is much lower (we just need to process enough rows).
		// October 2020: added further reductions
		// Listed in increasing order due to the handling below. At the end it gets quite drastic. Note, though, that currently we don't store this in the job-data.
		// A future improvement could, when things get drastic, grab and log data on the size of what is required, so that we can respond more dynamically. The strategy currently here will run out of road if memory falls short multiple times. See: https://stackoverflow.com/questions/4524019/how-to-get-the-byte-size-of-resultset-in-an-sql-query
		$fetch_rows_reductions = array(500, 250, 200, 100);
		
		$default_on_first_fetch = $this->get_rows_on_first_fetch($table);
		
		$known_bigger_than_table = (!is_bool($expected_rows) && $expected_rows && $expected_via_count && $default_on_first_fetch > 2 * $expected_rows);
		
		if ($known_bigger_than_table) $allow_further_reductions = true;
		
		if ($allow_further_reductions) {
			// If we're relying on LIMIT with offsets, then we have to be mindful of how that performs
			$fetch_rows_reductions = array_merge($fetch_rows_reductions, array(50, 20, 5));
		}
		
		// Remove any that are far out of range
		if ($known_bigger_than_table) {
			foreach ($fetch_rows_reductions as $k => $reduce_to) {
				if ($reduce_to > $expected_rows * 2 && count($fetch_rows_reductions) > 2) {
					unset($fetch_rows_reductions[$k]);
				}
			}
		}

		// If this is not the first fetch on a table, then get what was stored last time we set it (if we ever did). On the first fetch, reset back to the starting value (we presume problems are table-specific).
		// This means that the same value will persist whilst the table is being backed up, both during the current resumption, and subsequent ones
		$fetch_rows = $is_first_fetch_for_table ? $default_on_first_fetch : $updraftplus->jobdata_get('fetch_rows', $default_on_first_fetch);
		
		$fetch_rows_at_start = $fetch_rows;
		
		$resumptions_since_last_successful = $updraftplus->current_resumption - $updraftplus->last_successful_resumption;
		
		// Do we need to reduce the number of rows we attempt to fetch?
		// If something useful has happened on this run, then we don't try any reductions (we save them for a resumption after one on which nothing useful happened)
		if ($known_bigger_than_table || (!$updraftplus->something_useful_happened && !empty($updraftplus->current_resumption) && $resumptions_since_last_successful > 1)) {
		
			$break_after = $is_first_fetch_for_table ? max($resumptions_since_last_successful - 1, 1) : 1;

			foreach ($fetch_rows_reductions as $reduce_to) {
				if ($fetch_rows > $reduce_to) {
					// Go down one level
					$fetch_rows = $reduce_to;
					$break_after--;
					if ($break_after < 1) break;
				}
			}
			
			$log_start = $updraftplus->current_resumption ? "Last successful resumption was $resumptions_since_last_successful runs ago" : "Table is relatively small";
			$updraftplus->log("$log_start; fetch_rows will thus be: $fetch_rows (allow_further_reductions=$allow_further_reductions, is_first_fetch=$is_first_fetch_for_table, known_bigger_than_table=$known_bigger_than_table)");
		}
		
		// If it has changed, then preserve it in the job for the next resumption (of this table)
		if ($fetch_rows_at_start !== $fetch_rows || $is_first_fetch_for_table) $updraftplus->jobdata_set('fetch_rows', $fetch_rows);
		
		return $fetch_rows;
	
	}
	
	/**
	 * Return a list of primary keys (N.B. the method should not be called unless the caller already knows that the table has a single/simple primary key) for rows that have "over-sized" data.
	 * Currently this only examines the "posts" table and any other table with a longtext type, which are the primary causes of problems. If others are revealed in future, it can be generalised (e.g. examine the whole definition/all cells).
	 *
	 * @param String $table		  - the full table name
	 * @param Array	 $structure	  - the table structure, as from WPDB::get_results("DESCRIBE ...");
	 * @param String $primary_key - the primary key to use; required if $structure is set (slightly redundant, since it can be derived from structure)
	 *
	 * @return Array - list of IDs
	 */
	private function get_oversized_rows($table, $structure = array(), $primary_key = '') {
	
		if ($this->table_prefix_raw.'posts' != $table) {
			if (empty($structure) || '' === $primary_key) return array();
			foreach ($structure as $item) {
				if ('' !== $item->Field && 'longtext' === $item->Type) {
					$use_field = $item->Field;
				}
			}
		} else {
			$primary_key = 'id';
			$use_field = 'post_content';
		}
	
		if (!isset($use_field)) return array();
	
		global $updraftplus;
	
		// Look for the jobdata_delete() call elsewhere in this class - the key name needs to match
		$jobdata_key = 'oversized_rows_'.$table;
		
		$oversized_list = $updraftplus->jobdata_get($jobdata_key);
		
		if (is_array($oversized_list)) return $oversized_list;
	
		$oversized_list = array();
		
		// Allow over-ride via a constant
		$oversized_row_size = defined('UPDRAFTPLUS_OVERSIZED_ROW_SIZE') ? UPDRAFTPLUS_OVERSIZED_ROW_SIZE : 2048576;
		
		$sql = $this->wpdb_obj->prepare("SELECT ".UpdraftPlus_Manipulation_Functions::backquote($primary_key)." FROM ".UpdraftPlus_Manipulation_Functions::backquote($table)." WHERE LENGTH(".UpdraftPlus_Manipulation_Functions::backquote($use_field).") > %d ORDER BY ".UpdraftPlus_Manipulation_Functions::backquote($primary_key)." ASC", $oversized_row_size);
		
		$oversized_rows = $this->wpdb_obj->get_col($sql);
		
		// Upon an error, just return an empty list
		if (!is_array($oversized_rows)) return array();
		
		$updraftplus->jobdata_set($jobdata_key, $oversized_rows);
		
		return $oversized_rows;
	
	}
	
	/**
	 * Original version taken partially from phpMyAdmin and partially from Alain Wolf, Zurich - Switzerland to use the WordPress $wpdb object
	 * Website: http://restkultur.ch/personal/wolf/scripts/db_backup/
	 * Modified by Scott Merrill (http://www.skippy.net/)
	 * Subsequently heavily improved and modified
	 *
	 * This method should be called in a loop for a complete table backup (see the information for the returned parameter). The method may implement whatever strategy it likes for deciding when to return (the assumption is that when it does return with some results, the caller should register that something useful happened).
	 *
	 * @param String		  $table			   - Full name of database table to backup
	 * @param String		  $table_type		   - Table type - 'VIEW' is supported; otherwise it is treated as an ordinary table
	 * @param Integer|Boolean $start_record		   - Specify the starting record, or true to start at the beginning. Our internal page size is fixed at 1000 (though within that we might actually query in smaller batches).
	 * @param Boolean		  $can_use_primary_key - Whether it is allowed to perform quicker SELECTS based on the primary key. The intended use case for false is to support backups running during a version upgrade. N.B. This "can" is not absolute; there may be other constraints dealt with within this method.
	 *
	 * @return Integer|Array|WP_Error - a WP_Error to indicate an error; an array indicates that it finished (if it includes 'next_record' that means it finished via producing something); an integer to indicate the next page the case that there are more to do.
	 */
	private function backup_table($table, $table_type = 'BASE TABLE', $start_record = true, $can_use_primary_key = true) {
		$process_pages = 100;
		
		// Preserve the passed-in value
		$original_start_record = $start_record;
	
		global $updraftplus;

		$microtime = microtime(true);
		$total_rows = 0;

		// Deal with Windows/old MySQL setups with erroneous table prefixes differing in case
		$dump_as_table = (false == $this->duplicate_tables_exist && 0 === stripos($table, $this->table_prefix) && 0 !== strpos($table, $this->table_prefix)) ? $this->table_prefix.substr($table, strlen($this->table_prefix)) : $table;

		$table_structure = $this->wpdb_obj->get_results("DESCRIBE ".UpdraftPlus_Manipulation_Functions::backquote($table));
		if (!$table_structure) {
			// $updraftplus->log(__('Error getting table details', 'updraftplus') . ": $table", 'error');
			$error_message = '';
			if ($this->wpdb_obj->last_error) $error_message .= ' ('.$this->wpdb_obj->last_error.')';
			return new WP_Error('table_details_error', $error_message);
		}
	
		// If at the beginning of the dump for a table, then add the DROP and CREATE statements
		if (true === $start_record) {
			$this->write_table_backup_beginning($table, $dump_as_table, $table_type, $table_structure);
		}

		// Some tables have optional data, and should be skipped if they do not work
		$table_sans_prefix = substr($table, strlen($this->table_prefix_raw));
		$data_optional_tables = ('wp' == $this->whichdb) ? apply_filters('updraftplus_data_optional_tables', explode(',', UPDRAFTPLUS_DATA_OPTIONAL_TABLES)) : array();
		if (in_array($table_sans_prefix, $data_optional_tables)) {
			if (!$updraftplus->something_useful_happened && !empty($updraftplus->current_resumption) && ($updraftplus->current_resumption - $updraftplus->last_successful_resumption > 2)) {
				$updraftplus->log("Table $table: Data skipped (previous attempts failed, and table is marked as non-essential)");
				return array();
			}
		}

		$table_data = array();
		if ('VIEW' != $table_type) {
			$fields = array();
			$defs = array();
			$integer_fields = array();
			$binary_fields = array();
			$bit_fields = array();
			$bit_field_exists = false;

			// false means "not yet set"; a string means what it was set to; null means that there are multiple (and so not useful to us). If it is not a string, then $primary_key_type is invalid and should not be used.
			$primary_key = false;
			$primary_key_type = false;
			
			// $table_structure was from "DESCRIBE $table"
			foreach ($table_structure as $struct) {
			
				if (isset($struct->Key) && 'PRI' == $struct->Key && '' != $struct->Field) {
					$primary_key = (false === $primary_key) ? $struct->Field : null;
					$primary_key_type = $struct->Type;
				}
			
				if ((0 === strpos($struct->Type, 'tinyint')) || (0 === strpos(strtolower($struct->Type), 'smallint'))
					|| (0 === strpos(strtolower($struct->Type), 'mediumint')) || (0 === strpos(strtolower($struct->Type), 'int')) || (0 === strpos(strtolower($struct->Type), 'bigint'))
				) {
						$defs[strtolower($struct->Field)] = (null === $struct->Default) ? 'NULL' : $struct->Default;
						$integer_fields[strtolower($struct->Field)] = true;
				}
				
				if ((0 === strpos(strtolower($struct->Type), 'binary')) || (0 === strpos(strtolower($struct->Type), 'varbinary')) || (0 === strpos(strtolower($struct->Type), 'tinyblob')) || (0 === strpos(strtolower($struct->Type), 'mediumblob')) || (0 === strpos(strtolower($struct->Type), 'blob')) || (0 === strpos(strtolower($struct->Type), 'longblob'))) {
					$binary_fields[strtolower($struct->Field)] = true;
				}
				
				if (preg_match('/^bit(?:\(([0-9]+)\))?$/i', trim($struct->Type), $matches)) {
					if (!$bit_field_exists) $bit_field_exists = true;
					$bit_fields[strtolower($struct->Field)] = !empty($matches[1]) ? max(1, (int) $matches[1]) : 1;
					// the reason why if bit fields are found then the fields need to be cast into binary type is that if mysqli_query function is being used, mysql will convert the bit field value to a decimal number and represent it in a string format whereas, if mysql_query function is being used, mysql will not convert it to a decimal number but instead will keep it retained as it is
					$struct->Field = "CAST(".UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $struct->Field))." AS BINARY) AS ".UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $struct->Field));
					$fields[] = $struct->Field;
				} else {
					$fields[] = UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $struct->Field));
				}
			}
			
			$expected_via_count = false;
			
			// N.B. At this stage this is for optimisation, mainly targets what is used on the core WP tables (bigint(20)); a value can be relied upon, but false is not definitive. N.B. https://docs.oracle.com/cd/E17952_01/mysql-8.0-en/numeric-type-syntax.html (retrieved Aug 2021): "As of MySQL 8.0.17, the display width attribute is deprecated for integer data types; you should expect support for it to be removed in a future version of MySQL." MySQL 8.0.20 is not returning it.
			$use_primary_key = false;
			if ($can_use_primary_key && is_string($primary_key) && preg_match('#^(small|medium|big)?int(\(| |$)#i', $primary_key_type)) {
				$use_primary_key = true;
				
				// We don't bother re-counting if it's likely to be so large that we're not going to do anything with the result
				if (is_bool($this->expected_rows) || $this->expected_rows < 1000) {
					$expected_rows = $this->wpdb_obj->get_var('SELECT COUNT('.UpdraftPlus_Manipulation_Functions::backquote($primary_key).') FROM '.UpdraftPlus_Manipulation_Functions::backquote($table));
					if (!is_bool($expected_rows)) {
						$this->expected_rows = $expected_rows;
						$expected_via_count = true;
					}
				}
				
				$oversized_rows = $this->get_oversized_rows($table, $table_structure, $primary_key);

				if (preg_match('# unsigned$#i', $primary_key_type)) {
					if (true === $start_record) $start_record = -1;
				} else {
					if (true === $start_record) {
						$min_value = $this->wpdb_obj->get_var('SELECT MIN('.UpdraftPlus_Manipulation_Functions::backquote($primary_key).') FROM '.UpdraftPlus_Manipulation_Functions::backquote($table));
						$start_record = (is_numeric($min_value) && $min_value) ? (int) $min_value - 1 : -1;
					}
				}
			}
			
			if (!is_bool($this->expected_rows)) {
				$this->log_expected_rows($table, $this->expected_rows, $expected_via_count);
			}
			
			$search = array("\x00", "\x0a", "\x0d", "\x1a");
			$replace = array('\0', '\n', '\r', '\Z');

			$where_array = apply_filters('updraftplus_backup_table_sql_where', array(), $table, $this);
			$where = '';
			if (!empty($where_array) && is_array($where_array)) {
				$where = 'WHERE '.implode(' AND ', $where_array);
			}
			
			// Experimentation here shows that on large tables (we tested with 180,000 rows) on MyISAM, 1000 makes the table dump out 3x faster than the previous value of 100. After that, the benefit diminishes (increasing to 4000 only saved another 12%)

			$fetch_rows = $this->number_of_rows_to_fetch($table, $use_primary_key || $start_record < 500000, true === $original_start_record, $this->expected_rows, $expected_via_count);
			
			if (!is_bool($this->expected_rows)) $this->expected_rows = true;
			
			$original_fetch_rows = $fetch_rows;
		
			$select = $bit_field_exists ? implode(', ', $fields) : '*';
			
			$enough_for_now = false;
			
			$began_writing_at = time();
			
			$enough_data_after = 104857600;
			$enough_time_after = ($fetch_rows > 250) ? 15 : 9;
			
			// Loop which retrieves data
			do {

				if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

				// Reset back to that which has constructed before the loop began
				$final_where = $where;
				
				if ($use_primary_key) {
				
					// The point of this is to leverage the indexing on the private key to make the SELECT much faster than index-less paging
					$final_where = $where . ($where ? ' AND ' : 'WHERE ');
				
					// If it's -1, then we avoid mentioning a negative value, as the value may be unsigned
					$final_where .= UpdraftPlus_Manipulation_Functions::backquote($primary_key).((-1 === $start_record) ? ' >= 0' : " > $start_record");
				
					$oversized_last_row_id = false;
					// Remove ones we've gone past
					foreach ($oversized_rows as $k => $row_id) {
						if ($start_record >= $row_id) {
							unset($oversized_rows[$k]);
						} else {
							$oversized_last_row_id = $row_id;
							// At this point we are only willing to fetch a single over-sized row. If this ever changes, we'll need to also keep track of their length.
							break;
						}
					}
					// Number the keys again from zero
					$oversized_rows = array_values($oversized_rows);
				
					if ($oversized_last_row_id) {
						$final_where .= " AND ". UpdraftPlus_Manipulation_Functions::backquote($primary_key)." <= $oversized_last_row_id";
					}
				
					$limit_statement = sprintf('LIMIT %d', $fetch_rows);
					
					$order_by = 'ORDER BY '.UpdraftPlus_Manipulation_Functions::backquote($primary_key).' ASC';
				
				} else {
					$order_by = '';
					if (true === $start_record) $start_record = 0;
					$limit_statement = sprintf('LIMIT %d, %d', $start_record, $fetch_rows);
				}
				
				// $this->wpdb_obj->prepare() not needed (will throw a notice) as there are no parameters (all parts are already sanitised or cast to known-safe types if not sanitised here)
				$select_sql = "SELECT $select FROM ".UpdraftPlus_Manipulation_Functions::backquote($table)." $final_where $order_by $limit_statement";
				
				if (defined('UPDRAFTPLUS_LOG_BACKUP_SELECTS') && UPDRAFTPLUS_LOG_BACKUP_SELECTS) $updraftplus->log($select_sql);
				
				// Allow the data to be filtered (e.g. anonymisation)
				$table_data = apply_filters('updraftplus_backup_table_results', $this->wpdb_obj->get_results($select_sql, ARRAY_A), $table, $this->table_prefix, $this->whichdb);
				
				if (null === $table_data) {
					$updraftplus->log("Database fetch error (null returned) when running: $select_sql");
				}
				
				$oversized_changes = false;
				
				if (!$table_data) {
					// Nothing was found - not even the expected over-sized row; this means it was deleted - so don't try to use a limitation based on it again, or we may get an infinite loop.
					if (isset($oversized_last_row_id) && false !== $oversized_last_row_id) {
						if (false !== ($key = array_search($oversized_last_row_id, $oversized_rows))) {
							unset($oversized_rows[$key]);
							$oversized_changes = true;
						}
					}
					if ($oversized_changes) $updraftplus->jobdata_set('oversized_rows_'.$table, $oversized_rows);
					continue;
				}
				$entries = 'INSERT INTO '.UpdraftPlus_Manipulation_Functions::backquote($dump_as_table).' VALUES ';

				// \x08\\x09, not required
				
				$this_entry = '';
				foreach ($table_data as $row) {
					$total_rows++;
					if ($this_entry) $this_entry .= ",\n ";
					$this_entry .= '(';
					$key_count = 0;
					foreach ($row as $key => $value) {
					
						if ($key_count) $this_entry .= ', ';
						$key_count++;
					
						if ($use_primary_key && strtolower($primary_key) == strtolower($key) && $value > $start_record) {
							$start_record = $value;
							foreach ($oversized_rows as $k => $row_id) {
								if ($start_record >= $row_id) {
									unset($oversized_rows[$k]);
								} else {
									break;
								}
							}
						}
					
						if (isset($integer_fields[strtolower($key)])) {
							// make sure there are no blank spots in the insert syntax,
							// yet try to avoid quotation marks around integers
							$value = (null === $value || '' === $value) ? $defs[strtolower($key)] : $value;
							$value = ('' === $value) ? "''" : $value;
							$this_entry .= $value;
						} elseif (isset($binary_fields[strtolower($key)])) {
							if (null === $value) {
								$this_entry .= 'NULL';
							} elseif ('' === $value) {
								$this_entry .= "''";
							} else {
								$this_entry .= "0x" . bin2hex(str_repeat("0", floor(strspn($value, "0") / 4)).$value);
							}
						} elseif (isset($bit_fields[$key])) {
							mbstring_binary_safe_encoding();
							$val_len = strlen($value);
							reset_mbstring_encoding();
							$hex = '';
							for ($i=0; $i<$val_len; $i++) {
								$hex .= sprintf('%02X', ord($value[$i]));
							}
							$this_entry .= "b'".str_pad($this->hex2bin($hex), $bit_fields[$key], '0', STR_PAD_LEFT)."'";
						} else {
							$this_entry .= (null === $value) ? 'NULL' : "'" . str_replace($search, $replace, str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value))) . "'";
						}
					}
					$this_entry .= ')';
					
					// Flush every 512KB
					if (strlen($this_entry) > 524288) {
						$this_entry .= ';';
						if (strlen($this_entry) > 10485760) {
							// This is an attempt to prevent avoidable duplication of long strings in-memory, at the cost of one extra write
							$this->stow(" \n".$entries);
							$this->stow($this_entry);
						} else {
							$this->stow(" \n".$entries.$this_entry);
						}
						$this_entry = '';
						// Potentially indicate that enough has been done to loop
						if ($this->db_current_raw_bytes > $enough_data_after || time() - $began_writing_at > $enough_time_after) {
							$enough_for_now = true;
						}
					}
					
				}
				
				if ($this_entry) {
					$this_entry .= ';';
					if (strlen($this_entry) > 10485760) {
						// This is an attempt to prevent avoidable duplication of long strings in-memory, at the cost of one extra write
						$this->stow(" \n".$entries);
						$this->stow($this_entry);
					} else {
						$this->stow(" \n".$entries.$this_entry);
					}
				}
				
				// Increment this before any potential changes to $fetch_rows
				if (!$use_primary_key) {
					$start_record += $fetch_rows;
				}
				
				// Potentially fetch more rows at once, if performance has been good on a sufficient number of rows
				// However - testing indicates that this makes very little difference to overall performance; MySQL's performance scales linearly with the number of rows requested. So optimisations here are unlikely to be worthwhile. (Probably better to remove LIMIT and ORDER BY entirely on tables that look small enough to fit into memory in one go)
				if (!$enough_for_now && $total_rows > 0 && $fetch_rows >= $original_fetch_rows && $fetch_rows < $original_fetch_rows * 8 && $this->db_current_raw_bytes > 10000 && $total_rows > 5000) {
					$bytes_per_row = $this->db_current_raw_bytes / $total_rows;
					// Increase the numbers of rows fetched if we still expect it to be less than 5MB, and the rate is acceptable
					if (2 * $fetch_rows * $bytes_per_row < 5242880) {
						// N.B. This does not persist across resumptions
						$fetch_rows = $fetch_rows * 2;
						$process_pages = $process_pages / 2;
					}
				}
				
				if ($process_pages > 0) $process_pages--;
				
			// The condition involving count($oversized_rows) is for when rows that were in oversized_rows got deleted before being fetched; the "ID < (row)" condition could result in no data being returned, even though the table isn't finished
			} while (!$enough_for_now && (count($table_data) > 0 || (isset($oversized_rows) && count($oversized_rows) > 0)) && (-1 == $process_pages || $process_pages > 0));
		}
		
		$fetch_time = max(microtime(true)-$microtime, 0.00001);
		
		$updraftplus->log("Table $table: Rows added in this batch (next record: $start_record): $total_rows (uncompressed bytes in this segment=".$this->db_current_raw_bytes.") in ".sprintf('%.02f', $fetch_time).' seconds');

		// If all data has been fetched, then write out the closing comment
		if (-1 == $process_pages || 0 == count($table_data)) {
			$this->stow("\n# End of data contents of table ".UpdraftPlus_Manipulation_Functions::backquote($table)."\n\n");
			// Keep the keyname here in sync with what is in self::get_oversized_rows()
			$updraftplus->jobdata_delete('oversized_rows_'.$table);
			return is_numeric($start_record) ? array('next_record' => (int) $start_record) : array();
		}

		return is_numeric($start_record) ? (int) $start_record : $start_record;
		
	}

	/**
	 * Convert hexadecimal (base16) number into binary (base2) and no need to worry about the platform-dependent of 32bit/64bit size limitation
	 *
	 * @param String $hex Hexadecimal number
	 * @return String a base2 format of the given hexadecimal number
	 */
	public function hex2bin($hex) {
		$table = array(
			'0' => '0000',
			'1' => '0001',
			'2' => '0010',
			'3' => '0011',
			'4' => '0100',
			'5' => '0101',
			'6' => '0110',
			'7' => '0111',
			'8' => '1000',
			'9' => '1001',
			'a' => '1010',
			'b' => '1011',
			'c' => '1100',
			'd' => '1101',
			'e' => '1110',
			'f' => '1111'
		);
		$bin = '';

		if (!preg_match('/^[0-9a-f]+$/i', $hex)) return '';

		for ($i = 0; $i < strlen($hex); $i++) {
			$bin .= $table[strtolower(substr($hex, $i, 1))];
		}

		return $bin;
	}

	/**
	 * Encrypts the file if the option is set; returns the basename of the file (according to whether it was encrypted or nto)
	 *
	 * @param String $file - file to encrypt
	 *
	 * @return array
	 */
	public function encrypt_file($file) {
		global $updraftplus;
		$encryption = $updraftplus->get_job_option('updraft_encryptionphrase');
		if (strlen($encryption) > 0) {
			$updraftplus->log("Attempting to encrypt backup file");
			try {
				$result = apply_filters('updraft_encrypt_file', null, $file, $encryption, $this->whichdb, $this->whichdb_suffix);
			} catch (Exception $e) {
				$log_message = 'Exception ('.get_class($e).') occurred during encryption: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				// @codingStandardsIgnoreLine
				$log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP exception (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
				die();
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during encryption. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				// @codingStandardsIgnoreLine
				$log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP fatal error (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'error');
				die();
			}
			if (null === $result) return basename($file);
			return $result;
		} else {
			return basename($file);
		}
	}

	/**
	 * Close the database file currently being written
	 *
	 * @return Boolean
	 */
	private function backup_db_close() {
		return $this->dbhandle_isgz ? gzclose($this->dbhandle) : fclose($this->dbhandle);
	}

	/**
	 * Open a file, store its file handle, and reset the class variable db_current_raw_bytes back to zero.
	 *
	 * @param String  $file     Full path to the file to open
	 * @param Boolean $allow_gz	Use gzopen() if available, instead of fopen()
	 * @param Boolean $append	Use append mode for writing
	 *
	 * @return Resource|Boolean - the opened file handle, or false for an error
	 */
	public function backup_db_open($file, $allow_gz = true, $append = false) {
		$mode = $append ? 'ab' : 'w';
		if ($allow_gz && function_exists('gzopen')) {
			$this->dbhandle = gzopen($file, $mode);
			$this->dbhandle_isgz = true;
		} else {
			$this->dbhandle = fopen($file, $mode);
			$this->dbhandle_isgz = false;
		}
		if (false === $this->dbhandle) {
			global $updraftplus;
			$updraftplus->log("ERROR: $file: Could not open the backup file for writing (mode: $mode)");
			$updraftplus->log($file.": ".__("Could not open the backup file for writing", 'updraftplus'), 'error');
		}
		$this->db_current_raw_bytes = 0;
		return $this->dbhandle;
	}

	/**
	 * Adds a line to the database backup
	 *
	 * @param String $write_line - the line to write
	 *
	 * @return Integer|Boolean - the number of octets written, or false for a failure (as returned by gzwrite() / fwrite)
	 */
	private function stow($write_line) {
	
		if ('' === $write_line) return 0;
	
		$write_function = $this->dbhandle_isgz ? 'gzwrite' : 'fwrite';
	
		if (false == ($ret = call_user_func($write_function, $this->dbhandle, $write_line))) {
			$this->log_with_db_occasionally("There was an error writing a line to the backup file: $write_line");
		}
		
		$this->db_current_raw_bytes += strlen($write_line);
		
		return $ret;
	}

	/**
	 * Stow the database backup header
	 */
	private function backup_db_header() {

		global $updraftplus;
		$wp_version = $updraftplus->get_wordpress_version();
		$mysql_version = $this->wpdb_obj->get_var('SELECT VERSION()');
		if ('' == $mysql_version) $mysql_version = $this->wpdb_obj->db_version();

		if ('wp' == $this->whichdb) {
			$wp_upload_dir = wp_upload_dir();
			$this->stow("# WordPress MySQL database backup\n");
			$this->stow("# Created by UpdraftPlus version ".$updraftplus->version." (https://updraftplus.com)\n");
			$this->stow("# WordPress Version: $wp_version, running on PHP ".phpversion()." (".$_SERVER["SERVER_SOFTWARE"]."), MySQL $mysql_version\n");
			$this->stow("# Backup of: ".untrailingslashit(site_url())."\n");
			$this->stow("# Home URL: ".untrailingslashit(home_url())."\n");
			$this->stow("# Content URL: ".untrailingslashit(content_url())."\n");
			$this->stow("# Uploads URL: ".untrailingslashit($wp_upload_dir['baseurl'])."\n");
			$this->stow("# Table prefix: ".$this->table_prefix_raw."\n");
			$this->stow("# Filtered table prefix: ".$this->table_prefix."\n");
			$this->stow("# ABSPATH: ".trailingslashit(ABSPATH)."\n");
			$this->stow("# Site info: multisite=".(is_multisite() ? '1' : '0')."\n");
			$this->stow("# Site info: sql_mode=".$this->wpdb_obj->get_var('SELECT @@SESSION.sql_mode')."\n");
			$this->stow("# Site info: end\n");
		} else {
			$this->stow("# MySQL database backup (supplementary database ".$this->whichdb.")\n");
			$this->stow("# Created by UpdraftPlus version ".$updraftplus->version." (https://updraftplus.com)\n");
			$this->stow("# WordPress Version: $wp_version, running on PHP ".phpversion()." (".$_SERVER["SERVER_SOFTWARE"]."), MySQL $mysql_version\n");
			$this->stow("# ".sprintf('External database: (%s)', $this->dbinfo['user'].'@'.$this->dbinfo['host'].'/'.$this->dbinfo['name'])."\n");
			$this->stow("# Backup created by: ".untrailingslashit(site_url())."\n");
			$this->stow("# Table prefix: ".$this->table_prefix_raw."\n");
			$this->stow("# Filtered table prefix: ".$this->table_prefix."\n");
		}

		$label = $updraftplus->jobdata_get('label');
		if (!empty($label)) $this->stow("# Label: $label\n");

		$this->stow("\n# Generated: ".date("l j. F Y H:i T")."\n");
		$this->stow("# Hostname: ".$this->dbinfo['host']."\n");
		$this->stow("# Database: ".UpdraftPlus_Manipulation_Functions::backquote($this->dbinfo['name'])."\n");

		if (!empty($this->skipped_tables[$this->whichdb])) {
			if ('wp' == $this->whichdb) {
				$this->stow("# Skipped tables: " . implode(', ', $this->skipped_tables['wp'])."\n");
			} elseif (isset($this->skipped_tables[$this->dbinfo['name']])) {
				$this->stow("# Skipped tables: " . implode(', ', $this->skipped_tables[$this->dbinfo['name']])."\n");
			}
		}
		
		$this->stow("# --------------------------------------------------------\n");

		$this->stow("/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
		$this->stow("/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
		$this->stow("/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
		$this->stow("/*!40101 SET NAMES ".$updraftplus->get_connection_charset($this->wpdb_obj)." */;\n");
		$this->stow("/*!40101 SET foreign_key_checks = 0 */;\n\n");
		
	}

	/**
	 * This function recursively packs the zip, dereferencing symlinks but packing into a single-parent tree for universal unpacking
	 *
	 * @param String  $fullpath              Full path
	 * @param String  $use_path_when_storing Controls the path to use when storing in the zip file
	 * @param String  $original_fullpath     Original path
	 * @param Integer $startlevels           How deep within the directory structure the recursive operation has gone
	 * @param Array   $exclude               passed by reference so that we can remove elements as they are matched - saves time checking against already-dealt-with objects]
	 * @return Boolean
	 */
	private function makezip_recursive_add($fullpath, $use_path_when_storing, $original_fullpath, $startlevels, &$exclude) {

// $zipfile = $this->zip_basename.(($this->index == 0) ? '' : ($this->index+1)).'.zip.tmp';

		global $updraftplus;

		// Only BinZip supports symlinks. This means that as a consistent outcome, the only think that can be done with directory symlinks is either a) potentially duplicate the data or b) skip it. Whilst with internal WP entities (e.g. plugins) we definitely want the data, in the case of user-selected directories, we assume the user knew what they were doing when they chose the directory - i.e. we can skip symlink-accessed data that's outside.
		if (is_link($fullpath) && is_dir($fullpath) && 'more' == $this->whichone) {
			$updraftplus->log("Directory symlink encounted in more files backup: $use_path_when_storing -> ".readlink($fullpath).": skipping");
			return true;
		}
		
		// De-reference. Important to do to both, because on Windows only doing it to one can make them non-equal, where they were previously equal - something which we later rely upon
		$fullpath = realpath($fullpath);
		$original_fullpath = realpath($original_fullpath);

		// Is the place we've ended up above the original base? That leads to infinite recursion
		if (($fullpath !== $original_fullpath && strpos($original_fullpath, $fullpath) === 0) || ($original_fullpath == $fullpath && ((1== $startlevels && strpos($use_path_when_storing, '/') !== false) || (2 == $startlevels && substr_count($use_path_when_storing, '/') >1)))) {
			$updraftplus->log("Infinite recursion: symlink led us to $fullpath, which is within $original_fullpath");
			$updraftplus->log(__("Infinite recursion: consult your log for more information", 'updraftplus'), 'error');
			return false;
		}

		// This is sufficient for the ones we have exclude options for - uploads, others, wpcore
		$stripped_storage_path = (1 == $startlevels) ? $use_path_when_storing : substr($use_path_when_storing, strpos($use_path_when_storing, '/') + 1);
		if (false !== ($fkey = array_search($stripped_storage_path, $exclude))) {
			$updraftplus->log("Entity excluded by configuration option: $stripped_storage_path");
			unset($exclude[$fkey]);
			return true;
		}

		$if_altered_since = $this->makezip_if_altered_since;

		if (is_file($fullpath)) {
			if (!empty($this->excluded_extensions) && $this->is_entity_excluded_by_extension($fullpath)) {
				$updraftplus->log("Entity excluded by configuration option (extension): ".basename($fullpath));
			} elseif (!empty($this->excluded_prefixes) && $this->is_entity_excluded_by_prefix($fullpath)) {
				$updraftplus->log("Entity excluded by configuration option (prefix): ".basename($fullpath));
			} elseif (!empty($this->excluded_wildcards) && $this->is_entity_excluded_by_wildcards(basename($fullpath))) {
				$updraftplus->log("Entity excluded by configuration option (wildcards): ".basename($fullpath));
			} elseif (apply_filters('updraftplus_exclude_file', false, $fullpath)) {
				$updraftplus->log("Entity excluded by filter: ".basename($fullpath));
			} elseif (is_readable($fullpath)) {
				$mtime = filemtime($fullpath);
				$key = ($fullpath == $original_fullpath) ? ((2 == $startlevels) ? $use_path_when_storing : $this->basename($fullpath)) : $use_path_when_storing.'/'.$this->basename($fullpath);
				if ($mtime > 0 && $mtime > $if_altered_since) {
					$this->zipfiles_batched[$fullpath] = $key;
					$this->makezip_recursive_batchedbytes += @filesize($fullpath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					// @touch($zipfile);
				} else {
					$this->zipfiles_skipped_notaltered[$fullpath] = $key;
				}
			} else {
				$updraftplus->log("$fullpath: unreadable file");
				$updraftplus->log(sprintf(__("%s: unreadable file - could not be backed up (check the file permissions and ownership)", 'updraftplus'), $fullpath), 'warning');
			}
		} elseif (is_dir($fullpath)) {
			if ($fullpath == $this->updraft_dir_realpath) {
				$updraftplus->log("Skip directory (UpdraftPlus backup directory): $use_path_when_storing");
				return true;
			}
			
			if (apply_filters('updraftplus_exclude_directory', false, $fullpath, $use_path_when_storing)) {
				$updraftplus->log("Skip filtered directory: $use_path_when_storing");
				return true;
			}
			
			if (file_exists($fullpath.'/.donotbackup')) {
				$updraftplus->log("Skip directory (.donotbackup file found): $use_path_when_storing");
				return true;
			}

			if (!isset($this->existing_files[$use_path_when_storing])) $this->zipfiles_dirbatched[] = $use_path_when_storing;
			
			if (!$dir_handle = @opendir($fullpath)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$updraftplus->log("Failed to open directory: $fullpath");
				$updraftplus->log(sprintf(__("Failed to open directory (check the file permissions and ownership): %s", 'updraftplus'), $fullpath), 'error');
				return false;
			}

			while (false !== ($e = readdir($dir_handle))) {
				if ('.' == $e || '..' == $e) continue;

				if (is_link($fullpath.'/'.$e)) {
					$deref = realpath($fullpath.'/'.$e);
					if (is_file($deref)) {
						$use_stripped = $stripped_storage_path.'/'.$e;
						if (false !== ($fkey = array_search($use_stripped, $exclude))) {
							$updraftplus->log("Entity excluded by configuration option: $use_stripped");
							unset($exclude[$fkey]);
						} elseif (!empty($this->excluded_extensions) && $this->is_entity_excluded_by_extension($e)) {
							$updraftplus->log("Entity excluded by configuration option (extension): $use_stripped");
						} elseif (!empty($this->excluded_prefixes) && $this->is_entity_excluded_by_prefix($e)) {
							$updraftplus->log("Entity excluded by configuration option (prefix): $use_stripped");
						} elseif (!empty($this->excluded_wildcards) && $this->is_entity_excluded_by_wildcards($use_stripped)) {
							$updraftplus->log("Entity excluded by configuration option (wildcards): $use_stripped");
						} elseif (apply_filters('updraftplus_exclude_file', false, $deref, $use_stripped)) {
							$updraftplus->log("Entity excluded by filter: $use_stripped");
						} elseif (is_readable($deref)) {
							$mtime = filemtime($deref);
							if ($mtime > 0 && $mtime > $if_altered_since) {
								$this->zipfiles_batched[$deref] = $use_path_when_storing.'/'.$e;
								$this->makezip_recursive_batchedbytes += @filesize($deref);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
								// @touch($zipfile);
							} else {
								$this->zipfiles_skipped_notaltered[$deref] = $use_path_when_storing.'/'.$e;
							}
						} else {
							$updraftplus->log("$deref: unreadable file (de-referenced from the link $e in $fullpath)");
							$updraftplus->log(sprintf(__("%s: unreadable file - could not be backed up"), $deref), 'warning');
						}
					} elseif (is_dir($deref)) {
						$this->makezip_recursive_add($deref, $use_path_when_storing.'/'.$e, $original_fullpath, $startlevels, $exclude);
					}
				} elseif (is_file($fullpath.'/'.$e)) {
					$use_stripped = $stripped_storage_path.'/'.$e;
					if (false !== ($fkey = array_search($use_stripped, $exclude))) {
						$updraftplus->log("Entity excluded by configuration option: $use_stripped");
						unset($exclude[$fkey]);
					} elseif (!empty($this->excluded_extensions) && $this->is_entity_excluded_by_extension($e)) {
						$updraftplus->log("Entity excluded by configuration option (extension): $use_stripped");
					} elseif (!empty($this->excluded_prefixes) && $this->is_entity_excluded_by_prefix($e)) {
						$updraftplus->log("Entity excluded by configuration option (prefix): $use_stripped");
					} elseif (!empty($this->excluded_wildcards) && $this->is_entity_excluded_by_wildcards($use_stripped)) {
						$updraftplus->log("Entity excluded by configuration option (wildcards): $use_stripped");
					} elseif (apply_filters('updraftplus_exclude_file', false, $fullpath.'/'.$e)) {
						$updraftplus->log("Entity excluded by filter: $use_stripped");
					} elseif (is_readable($fullpath.'/'.$e)) {
						$mtime = filemtime($fullpath.'/'.$e);
						if ($mtime > 0 && $mtime > $if_altered_since) {
							$this->zipfiles_batched[$fullpath.'/'.$e] = $use_path_when_storing.'/'.$e;
							$this->makezip_recursive_batchedbytes += @filesize($fullpath.'/'.$e);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						} else {
							$this->zipfiles_skipped_notaltered[$fullpath.'/'.$e] = $use_path_when_storing.'/'.$e;
						}
					} else {
						$updraftplus->log("$fullpath/$e: unreadable file");
						$updraftplus->log(sprintf(__("%s: unreadable file - could not be backed up", 'updraftplus'), $use_path_when_storing.'/'.$e), 'warning', "unrfile-$e");
					}
				} elseif (is_dir($fullpath.'/'.$e)) {
					$use_stripped = $stripped_storage_path.'/'.$e;
					if ('wpcore' == $this->whichone && 'updraft' == $e && basename($use_path_when_storing) == 'wp-content' && (!defined('UPDRAFTPLUS_WPCORE_INCLUDE_UPDRAFT_DIRS') || !UPDRAFTPLUS_WPCORE_INCLUDE_UPDRAFT_DIRS)) {
						// This test, of course, won't catch everything - it just aims to make things better by default
						$updraftplus->log("Directory excluded for looking like a sub-site's internal UpdraftPlus directory (enable by defining UPDRAFTPLUS_WPCORE_INCLUDE_UPDRAFT_DIRS): ".$use_path_when_storing.'/'.$e);
					} elseif (!empty($this->excluded_wildcards) && $this->is_entity_excluded_by_wildcards($use_stripped)) {
						$updraftplus->log("Entity excluded by configuration option (wildcards): $use_stripped");
					} else {
						// no need to add_empty_dir here, as it gets done when we recurse
						$this->makezip_recursive_add($fullpath.'/'.$e, $use_path_when_storing.'/'.$e, $original_fullpath, $startlevels, $exclude);
					}
				}
			}
			closedir($dir_handle);
		} else {
			$updraftplus->log("Unexpected: path ($use_path_when_storing) fails both is_file() and is_dir()");
		}

		return true;

	}

	/**
	 * Get a list of excluded extensions
	 *
	 * @param Array $exclude - settings passed in
	 *
	 * @return Array
	 */
	private function get_excluded_extensions($exclude) {
		if (!is_array($exclude)) $exclude = array();
		$exclude_extensions = array();
		foreach ($exclude as $ex) {
			if (preg_match('/^ext:(.+)$/i', $ex, $matches)) {
				$exclude_extensions[] = strtolower($matches[1]);
			}
		}

		if (defined('UPDRAFTPLUS_EXCLUDE_EXTENSIONS')) {
			$exclude_from_define = explode(',', UPDRAFTPLUS_EXCLUDE_EXTENSIONS);
			foreach ($exclude_from_define as $ex) {
				$exclude_extensions[] = strtolower(trim($ex));
			}
		}

		return $exclude_extensions;
	}

	/**
	 * Get a list of excluded prefixes
	 *
	 * @param Array $exclude - settings passed in
	 *
	 * @return Array - each is listed in lower case
	 */
	private function get_excluded_prefixes($exclude) {
		if (!is_array($exclude)) $exclude = array();
		$exclude_prefixes = array();
		foreach ($exclude as $pref) {
			if (preg_match('/^prefix:(.+)$/i', $pref, $matches)) {
				$exclude_prefixes[] = strtolower($matches[1]);
			}
		}
		return $exclude_prefixes;
	}

	/**
	 * List all the wildcard patterns from the given excluded items
	 *
	 * @param Array $exclude the list of excluded items which may contain not just wildcard patterns but also specific file/directory names as well
	 *
	 * $exclude argument may contains data in an array format like below:
	 *     [
	 *         "snapshots" // definitely not a wildcard parttern, this could be directories/files named `snapshots` which are located in the root/parent directory
	 *         "2021/03/image.jpg", // not a wildcard parttern, this could be files/directories named `image.jpg` which are located in the 2021/03/ directory
	 *         "ext:zip", // not a wildcard pattern, this is to exclude all files that end with `zip` extension
	 *         "prefix:file-", // not a wildcard pattern, this is to exclude all files that begin with `file-` prefix
	 *         "2021/04", // not a wildcard pattern, this is to exclude all files/directories which are located in the 2021/04 directory
	 *         "backup*", // wildcard pattern that excludes all files/directories beginning with `backup` in the root/parent directory
	 *         "2021/*optimise*", // wildcard pattern that excludes all files/directories that have `optimise` anywhere in their names in the `2021` directory
	 *         "2021/04/*.tmp" // wildcard pattern that excludes all files/directories ending with `optimise` anywhere in their names in the `2021/04` directory
	 *     ]
	 *
	 * @return Array an array of wilcard patterns
	 *
	 * After the $exclude has gone through the regex parsing step, only excluded items containing valid wildcard patterns got captured and will return them in an array in a format like below:
	 *
	 *     [
	 *         [
	 *             "directory_path" => "",
	 *             "pattern" => "backup*"
	 *         ],
	 *         [
	 *             "directory_path" => "2021\",
	 *             "pattern" => "*optimise*"
	 *         ],
	 *         [
	 *             "directory_path" => "2021\04\",
	 *             "pattern" => "*.tmp"
	 *         ]
	 *     ]
	 */
	private function get_excluded_wildcards($exclude) {
		if (!is_array($exclude)) $exclude = array();
		$excluded_wildcards = array();
		foreach ($exclude as $wch) {
			// https://regex101.com/r/dMFI0P/1/
			if (preg_match('#(.*(?<!\\\)/)?(.*?(?<!\\\)\*.*)#i', $wch, $matches)) {
				// the regex will make sure only excluded items containing valid wildcard patterns get captured, it will lookup for asterisk char(s) at the very end of the string right after the last path separator (if any). e.g. foo/bar/b*a*z
				$excluded_wildcards[] = array(
					// in case the excluded item has doubled separators (e.g. dir1//dir2//file) or if the user added a directory separator at the beginning then trim and/or replace them
					'directory_path' => preg_replace(array('/^[\/\s]*/', '/\/\/*/', '/[\/\s]*$/'), array('', '/', ''), $matches[1]),
					'pattern' => $matches[2]
				);
			}
		}
		return $excluded_wildcards;
	}

	/**
	 * Check whether or not the given entity(file/directory) is excluded from the backup by matching it against a set of wildcard patterns
	 *
	 * @param String $entity the file/directory's stripped path
	 * @return Boolean true if the entity is excluded, false otherwise
	 */
	private function is_entity_excluded_by_wildcards($entity) {
		$entity_basename = untrailingslashit($entity);
		$entity_basename = substr_replace($entity_basename, '', 0, (false === strrpos($entity_basename, '/') ? 0 : strrpos($entity_basename, '/') + 1));
		foreach ($this->excluded_wildcards as $wch) {
			if (!is_array($wch) || empty($wch)) continue;
			if (substr_replace($entity, '', (int) strrpos($entity, '/'), strlen($entity) - (int) strrpos($entity, '/')) !== $wch['directory_path']) continue;
			if ('*' == substr($wch['pattern'], -1, 1) && '*' == substr($wch['pattern'], 0, 1) && strlen($wch['pattern']) > 2) {
				$wch['pattern'] = substr($wch['pattern'], 1, strlen($wch['pattern'])-2);
				$wch['pattern'] = str_replace('\*', '*', $wch['pattern']);
				if (strpos($entity_basename, $wch['pattern']) !== false) return true;
			} elseif ('*' == substr($wch['pattern'], -1, 1) && strlen($wch['pattern']) > 1) {
				$wch['pattern'] = substr($wch['pattern'], 0, strlen($wch['pattern'])-1);
				$wch['pattern'] = str_replace('\*', '*', $wch['pattern']);
				if (substr($entity_basename, 0, strlen($wch['pattern'])) == $wch['pattern']) return true;
			} elseif ('*' == substr($wch['pattern'], 0, 1) && strlen($wch['pattern']) > 1) {
				$wch['pattern'] = substr($wch['pattern'], 1);
				$wch['pattern'] = str_replace('\*', '*', $wch['pattern']);
				if (strlen($entity_basename) >= strlen($wch['pattern']) && substr($entity_basename, strlen($wch['pattern'])*-1) == $wch['pattern']) return true;
			}
		}
		return false;
	}

	private function is_entity_excluded_by_extension($entity) {
		foreach ($this->excluded_extensions as $ext) {
			if (!$ext) continue;
			$eln = strlen($ext);
			if (strtolower(substr($entity, -$eln, $eln)) == $ext) return true;
		}
		return false;
	}

	private function is_entity_excluded_by_prefix($entity) {
		$entity = basename($entity);
		foreach ($this->excluded_prefixes as $pref) {
			if (!$pref) continue;
			$eln = strlen($pref);
			if (strtolower(substr($entity, 0, $eln)) == $pref) return true;
		}
		return false;
	}

	private function unserialize_gz_cache_file($file) {
		if (!$whandle = gzopen($file, 'r')) return false;
		global $updraftplus;
		$emptimes = 0;
		$var = '';
		while (!gzeof($whandle)) {
			$bytes = @gzread($whandle, 1048576);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if (empty($bytes)) {
				$emptimes++;
				$updraftplus->log("Got empty gzread ($emptimes times)");
				if ($emptimes>2) return false;
			} else {
				$var .= $bytes;
			}
		}
		gzclose($whandle);
		return unserialize($var);
	}

	/**
	 * Make Zip File.
	 *
	 * @param Array|String $source               Caution: $source is allowed to be an array, not just a filename
	 * @param String	   $backup_file_basename Name of backup file
	 * @param String	   $whichone             Backup entity type (e.g. 'plugins')
	 * @param Boolean	   $retry_on_error       Set to retry upon error
	 * @return Boolean
	 */
	private function make_zipfile($source, $backup_file_basename, $whichone, $retry_on_error = true) {

		global $updraftplus;

		$original_index = $this->index;

		$itext = (empty($this->index)) ? '' : ($this->index+1);
		$destination_base = $backup_file_basename.'-'.$whichone.$itext.'.zip.tmp';
		// $destination is the temporary file (ending in .tmp)
		$destination = $this->updraft_dir.'/'.$destination_base;

		// When to prefer PCL:
		// - We were asked to
		// - No zip extension present and no relevant method present
		// The zip extension check is not redundant, because method_exists segfaults some PHP installs, leading to support requests

		// We need meta-info about $whichone
		$backupable_entities = $updraftplus->get_backupable_file_entities(true, false);
		// This is only used by one corner-case in BinZip
		// $this->make_zipfile_source = (isset($backupable_entities[$whichone])) ? $backupable_entities[$whichone] : $source;
		$this->make_zipfile_source = (is_array($source) && isset($backupable_entities[$whichone])) ? (('uploads' == $whichone) ? dirname($backupable_entities[$whichone]) : $backupable_entities[$whichone]) : dirname($source);

		$this->existing_files = array();
		// Used for tracking compression ratios
		$this->existing_files_rawsize = 0;
		$this->existing_zipfiles_size = 0;

		// Enumerate existing files
		// Usually first_linked_index is zero; the exception being with more files, where previous zips' contents are irrelevant
		for ($j = $this->first_linked_index; $j <= $this->index; $j++) {
			$jtext = (0 == $j) ? '' : $j+1;
			// This is, in a non-obvious way, compatible with filenames which indicate increments
			// $j does not need to start at zero; it should start at the index which the current entity split at. However, this is not directly known, and can only be deduced from examining the filenames. And, for other indexes from before the current increment, the searched-for filename won't exist (even if there is no cloud storage). So, this indirectly results in the desired outcome when we start from $j=0.
			$examine_zip = $this->updraft_dir.'/'.$backup_file_basename.'-'.$whichone.$jtext.'.zip'.(($j == $this->index) ? '.tmp' : '');

			// This comes from https://wordpress.org/support/topic/updraftplus-not-moving-all-files-to-remote-server - where it appears that the jobdata's record of the split was done (i.e. database write), but the *earlier* rename of the .tmp file was not done (i.e. I/O lost). i.e. In theory, this should be impossible; but, the sychnronicity apparently cannot be fully relied upon in some setups. The check for the index being one behind is being conservative - there's no inherent reason why it couldn't be done for other indexes.
			// Note that in this 'impossible' case, no backup data was being lost - the design still ensures that the on-disk backup is fine. The problem was a gap in the sequence numbering of the zip files, leading to user confusion.
			// Other examples of this appear to be in HS#1001 and #1047
			if ($j != $this->index && !file_exists($examine_zip)) {
				$alt_examine_zip = $this->updraft_dir.'/'.$backup_file_basename.'-'.$whichone.$jtext.'.zip'.(($j == $this->index - 1) ? '.tmp' : '');
				if ($alt_examine_zip != $examine_zip && file_exists($alt_examine_zip) && is_readable($alt_examine_zip) && filesize($alt_examine_zip)>0) {
					$updraftplus->log("Looked-for zip file not found; but non-zero .tmp zip was, despite not being current index ($j != ".$this->index." - renaming zip (assume previous resumption's IO was lost before kill)");
					if (rename($alt_examine_zip, $examine_zip)) {
						clearstatcache();
					} else {
						$updraftplus->log("Rename failed - backup zips likely to not have sequential numbers (does not affect backup integrity, but can cause user confusion)");
					}
				}
			}

			// If the file exists, then we should grab its index of files inside, and sizes
			// Then, when we come to write a file, we should check if it's already there, and only add if it is not
			if (file_exists($examine_zip) && is_readable($examine_zip) && filesize($examine_zip) > 0) {

				// Do not use (which also means do not create) a manifest if the file is still a .tmp file, since this may not be complete. If we are in this place in the code from a resumption, creating a manifest here will mean the manifest becomes out-of-date if further files are added.
				$this->populate_existing_files_list($examine_zip, substr($examine_zip, -4, 4) === '.zip');
				
				// try_split is true if there have been no check-ins recently - or if it needs to be split anyway
				if ($j == $this->index) {
					if ($this->try_split) {
						if (filesize($examine_zip) > 50*1048576) {
							// We could, as a future enhancement, save this back to the job data, if we see a case that needs it
							$this->zip_split_every = max(
								(int) $this->zip_split_every/2,
								UPDRAFTPLUS_SPLIT_MIN*1048576,
								min(filesize($examine_zip)-1048576, $this->zip_split_every)
							);
							$updraftplus->jobdata_set('split_every', (int) ($this->zip_split_every/1048576));
							$updraftplus->log("No check-in on last two runs; bumping index and reducing zip split to: ".round($this->zip_split_every/1048576, 1)." MB");
							$do_bump_index = true;
						}
						unset($this->try_split);
					} elseif (filesize($examine_zip) > $this->zip_split_every) {
						$updraftplus->log(sprintf("Zip size is at/near split limit (%s MB / %s MB) - bumping index (from: %d)", filesize($examine_zip), round($this->zip_split_every/1048576, 1), $this->index));
						$do_bump_index = true;
					}
				}

			} elseif (file_exists($examine_zip)) {
				$updraftplus->log("Zip file already exists, but is not readable or was zero-sized; will remove: ".basename($examine_zip));
				@unlink($examine_zip);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		}

		$this->zip_last_ratio = ($this->existing_files_rawsize > 0) ? ($this->existing_zipfiles_size/$this->existing_files_rawsize) : 1;

		$this->zipfiles_added = 0;
		$this->zipfiles_added_thisrun = 0;
		$this->zipfiles_dirbatched = array();
		$this->zipfiles_batched = array();
		$this->zipfiles_skipped_notaltered = array();
		$this->zipfiles_lastwritetime = time();
		$this->zip_basename = $this->updraft_dir.'/'.$backup_file_basename.'-'.$whichone;

		if (!empty($do_bump_index)) $this->bump_index();

		$error_occurred = false;

		// Store this in its original form
		$this->source = $source;

		// Reset. This counter is used only with PcLZip, to decide if it's better to do it all-in-one
		$this->makezip_recursive_batchedbytes = 0;
		if (!is_array($source)) $source = array($source);

		$exclude = $updraftplus->get_exclude($whichone);

		$files_enumerated_at = $updraftplus->jobdata_get('files_enumerated_at');
		if (!is_array($files_enumerated_at)) $files_enumerated_at = array();
		$files_enumerated_at[$whichone] = time();
		$updraftplus->jobdata_set('files_enumerated_at', $files_enumerated_at);

		$this->makezip_if_altered_since = is_array($this->altered_since) ? (isset($this->altered_since[$whichone]) ? $this->altered_since[$whichone] : -1) : -1;

		// Reset
		$got_uploads_from_cache = false;
		
		// Uploads: can/should we get it back from the cache?
		// || 'others' == $whichone
		if (('uploads' == $whichone || 'others' == $whichone) && function_exists('gzopen') && function_exists('gzread')) {
			$use_cache_files = false;
			$cache_file_base = $this->zip_basename.'-cachelist-'.$this->makezip_if_altered_since;
			// Cache file suffixes: -zfd.gz.tmp, -zfb.gz.tmp, -info.tmp, (possible)-zfs.gz.tmp
			if (file_exists($cache_file_base.'-zfd.gz.tmp') && file_exists($cache_file_base.'-zfb.gz.tmp') && file_exists($cache_file_base.'-info.tmp')) {
				// Cache files exist; shall we use them?
				$mtime = filemtime($cache_file_base.'-zfd.gz.tmp');
				// Require < 30 minutes old
				if (time() - $mtime < 1800) {
					$use_cache_files = true;
				}
				$any_failures = false;
				if ($use_cache_files) {
					$var = $this->unserialize_gz_cache_file($cache_file_base.'-zfd.gz.tmp');
					if (is_array($var)) {
						$this->zipfiles_dirbatched = $var;
						$var = $this->unserialize_gz_cache_file($cache_file_base.'-zfb.gz.tmp');
						if (is_array($var)) {
							$this->zipfiles_batched = $var;
							if (file_exists($cache_file_base.'-info.tmp')) {
								$var = maybe_unserialize(file_get_contents($cache_file_base.'-info.tmp'));
								if (is_array($var) && isset($var['makezip_recursive_batchedbytes'])) {
									$this->makezip_recursive_batchedbytes = $var['makezip_recursive_batchedbytes'];
									if (file_exists($cache_file_base.'-zfs.gz.tmp')) {
										$var = $this->unserialize_gz_cache_file($cache_file_base.'-zfs.gz.tmp');
										if (is_array($var)) {
											$this->zipfiles_skipped_notaltered = $var;
										} else {
											$any_failures = true;
										}
									} else {
										$this->zipfiles_skipped_notaltered = array();
									}
								} else {
									$any_failures = true;
								}
							}
						} else {
							$any_failures = true;
						}
					} else {
						$any_failures = true;
					}
					if ($any_failures) {
						$updraftplus->log("Failed to recover file lists from existing cache files");
						// Reset it all
						$this->zipfiles_skipped_notaltered = array();
						$this->makezip_recursive_batchedbytes = 0;
						$this->zipfiles_batched = array();
						$this->zipfiles_dirbatched = array();
					} else {
						$updraftplus->log("File lists recovered from cache files; sizes: ".count($this->zipfiles_batched).", ".count($this->zipfiles_batched).", ".count($this->zipfiles_skipped_notaltered).")");
						$got_uploads_from_cache = true;
					}
				}
			}
		}

		$time_counting_began = time();

		$this->excluded_extensions = $this->get_excluded_extensions($exclude);
		$this->excluded_prefixes = $this->get_excluded_prefixes($exclude);
		$this->excluded_wildcards = $this->get_excluded_wildcards($exclude);

		foreach ($source as $element) {
			// makezip_recursive_add($fullpath, $use_path_when_storing, $original_fullpath, $startlevels = 1, $exclude_array)
			if ('uploads' == $whichone) {
				if (empty($got_uploads_from_cache)) {
					$dirname = dirname($element);
					$basename = $this->basename($element);
					$add_them = $this->makezip_recursive_add($element, basename($dirname).'/'.$basename, $element, 2, $exclude);
				} else {
					$add_them = true;
				}
			} else {
				if (empty($got_uploads_from_cache)) {
					$add_them = $this->makezip_recursive_add($element, $this->basename($element), $element, 1, $exclude);
				} else {
					$add_them = true;
				}
			}
			if (is_wp_error($add_them) || false === $add_them) $error_occurred = true;
		}

		$time_counting_ended = time();

		// Cache the file scan, if it looks like it'll be useful
		// We use gzip to reduce the size as on hosts which limit disk I/O, the cacheing may make things worse
		// || 'others' == $whichone
		if (('uploads' == $whichone || 'others' == $whichone) && !$error_occurred && function_exists('gzopen') && function_exists('gzwrite')) {
			$cache_file_base = $this->zip_basename.'-cachelist-'.$this->makezip_if_altered_since;

			// Just approximate - we're trying to avoid an otherwise-unpredictable PHP fatal error. Cacheing only happens if file enumeration took a long time - so presumably there are very many.
			$memory_needed_estimate = 0;
			foreach ($this->zipfiles_batched as $k => $v) {
				$memory_needed_estimate += strlen($k)+strlen($v)+12;
			}

			// We haven't bothered to check if we just fetched the files from cache, as that shouldn't take a long time and so shouldn't trigger this
			// Let us suppose we need 15% overhead for gzipping
			
			$memory_limit = ini_get('memory_limit');
			$memory_usage = round(@memory_get_usage(false)/1048576, 1);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$memory_usage2 = round(@memory_get_usage(true)/1048576, 1);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			
			if ($time_counting_ended-$time_counting_began > 20 && $updraftplus->verify_free_memory($memory_needed_estimate*0.15) && $whandle = gzopen($cache_file_base.'-zfb.gz.tmp', 'w')) {
				$updraftplus->log("File counting took a long time (".($time_counting_ended - $time_counting_began)."s); will attempt to cache results (memory_limit: $memory_limit (used: ${memory_usage}M | ${memory_usage2}M), estimated uncompressed bytes: ".round($memory_needed_estimate/1024, 1)." Kb)");
				
				$buf = 'a:'.count($this->zipfiles_batched).':{';
				foreach ($this->zipfiles_batched as $file => $add_as) {
					$k = addslashes($file);
					$v = addslashes($add_as);
					$buf .= 's:'.strlen($k).':"'.$k.'";s:'.strlen($v).':"'.$v.'";';
					if (strlen($buf) > 1048576) {
						gzwrite($whandle, $buf, strlen($buf));
						$buf = '';
					}
				}
				$buf .= '}';
				$final = gzwrite($whandle, $buf);
				unset($buf);
				
				if (!$final) {
					@unlink($cache_file_base.'-zfb.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					@gzclose($whandle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} else {
					gzclose($whandle);
					if (!empty($this->zipfiles_skipped_notaltered)) {
						if ($shandle = gzopen($cache_file_base.'-zfs.gz.tmp', 'w')) {
							if (!gzwrite($shandle, serialize($this->zipfiles_skipped_notaltered))) {
								$aborted_on_skipped = true;
							}
							gzclose($shandle);
						} else {
							$aborted_on_skipped = true;
						}
					}
					if (!empty($aborted_on_skipped)) {
						@unlink($cache_file_base.'-zfs.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						@unlink($cache_file_base.'-zfb.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					} else {
						$info_array = array('makezip_recursive_batchedbytes' => $this->makezip_recursive_batchedbytes);
						if (!file_put_contents($cache_file_base.'-info.tmp', serialize($info_array))) {
							@unlink($cache_file_base.'-zfs.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
							@unlink($cache_file_base.'-zfb.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						}
						if ($dhandle = gzopen($cache_file_base.'-zfd.gz.tmp', 'w')) {
							if (!gzwrite($dhandle, serialize($this->zipfiles_dirbatched))) {
								$aborted_on_dirbatched = true;
							}
							gzclose($dhandle);
						} else {
							$aborted_on_dirbatched = true;
						}
						if (!empty($aborted_on_dirbatched)) {
							@unlink($cache_file_base.'-zfs.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
							@unlink($cache_file_base.'-zfd.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
							@unlink($cache_file_base.'-zfb.gz.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
							@unlink($cache_file_base.'-info.tmp');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						// @codingStandardsIgnoreLine
						} else {
							// Success.
						}
					}
				}
			}

/*
		Class variables that get altered:
		zipfiles_batched
		makezip_recursive_batchedbytes
		zipfiles_skipped_notaltered
		zipfiles_dirbatched
		Class variables that the result depends upon (other than the state of the filesystem):
		makezip_if_altered_since
		existing_files
		*/

		}

		// Any not yet dispatched? Under our present scheme, at this point nothing has yet been despatched. And since the enumerating of all files can take a while, we can at this point do a further modification check to reduce the chance of overlaps.
		// This relies on us *not* touch()ing the zip file to indicate to any resumption 'behind us' that we're already here. Rather, we're relying on the combined facts that a) if it takes us a while to search the directory tree, then it should do for the one behind us too (though they'll have the benefit of cache, so could catch very fast) and b) we touch *immediately* after finishing the enumeration of the files to add.
		// $retry_on_error is here being used as a proxy for 'not the second time around, when there might be the remains of the file on the first time around'
		if ($retry_on_error) $updraftplus->check_recent_modification($destination);
		// Here we're relying on the fact that both PclZip and ZipArchive will happily operate on an empty file. Note that BinZip *won't* (for that, may need a new strategy - e.g. add the very first file on its own, in order to 'lay down a marker')
		if (empty($do_bump_index)) touch($destination);

		if (count($this->zipfiles_dirbatched) > 0 || count($this->zipfiles_batched) > 0) {

			$updraftplus->log(sprintf("Total entities for the zip file: %d directories, %d files (%d skipped as non-modified), %s MB", count($this->zipfiles_dirbatched), count($this->zipfiles_batched), count($this->zipfiles_skipped_notaltered), round($this->makezip_recursive_batchedbytes/1048576, 1)));

			// No need to warn if we're going to retry anyway. (And if we get killed, the zip will be rescanned for its contents upon resumption).
			$warn_on_failures = ($retry_on_error) ? false : true;
			$add_them = $this->makezip_addfiles($warn_on_failures);

			if (is_wp_error($add_them)) {
				foreach ($add_them->get_error_messages() as $msg) {
					$updraftplus->log("Error returned from makezip_addfiles: ".$msg);
				}
				$error_occurred = true;
			} elseif (false === $add_them) {
				$updraftplus->log("Error: makezip_addfiles returned false");
				$error_occurred = true;
			}

		}

		// Reset these variables because the index may have changed since we began

		$itext = empty($this->index) ? '' : $this->index+1;
		$destination_base = $backup_file_basename.'-'.$whichone.$itext.'.zip.tmp';
		$destination = $this->updraft_dir.'/'.$destination_base;

		// ZipArchive::addFile sometimes fails - there's nothing when we expected something.
		// Did not used to have || $error_occured here. But it is better to retry, than to simply warn the user to check his logs.
		if (((file_exists($destination) || $this->index == $original_index) && @filesize($destination) < 90 && 'UpdraftPlus_ZipArchive' == $this->use_zip_object) || ($error_occurred && $retry_on_error)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			// This can be made more sophisticated if feedback justifies it. Currently we just switch to PclZip. But, it may have been a BinZip failure, so we could then try ZipArchive if that is available. If doing that, make sure that an infinite recursion isn't made possible.
			$updraftplus->log("makezip_addfiles(".$this->use_zip_object.") apparently failed (file=".basename($destination).", type=$whichone, size=".filesize($destination).") - retrying with PclZip");
			$saved_zip_object = $this->use_zip_object;
			$this->use_zip_object = 'UpdraftPlus_PclZip';
			$ret = $this->make_zipfile($source, $backup_file_basename, $whichone, false);
			$this->use_zip_object = $saved_zip_object;
			return $ret;
		}

		// zipfiles_added > 0 means that $zip->close() has been called. i.e. An attempt was made to add something: something _should_ be there.
		// Why return true even if $error_occurred may be set? 1) Because in that case, a warning has already been logged. 2) Because returning false causes an error to be logged, which means it'll all be retried again. Also 3) this has been the pattern of the code for a long time, and the algorithm has been proven in the real-world: don't change what's not broken.
		// (file_exists($destination) || $this->index == $original_index) might be an alternative to $this->zipfiles_added > 0 - ? But, don't change what's not broken.
		if (false == $error_occurred || $this->zipfiles_added > 0) {
			return true;
		} else {
			$updraftplus->log("makezip failure: zipfiles_added=".$this->zipfiles_added.", error_occurred=".$error_occurred." (method=".$this->use_zip_object.")");
			return false;
		}

	}

	/**
	 * This function is an ugly, conservative workaround for https://bugs.php.net/bug.php?id=62119. It does not aim to always work-around, but to ensure that nothing is made worse.
	 *
	 * @param String $element
	 *
	 * @return String
	 */
	private function basename($element) {
		$dirname = dirname($element);
		$basename_manual = preg_replace('#^[\\/]+#', '', substr($element, strlen($dirname)));
		$basename = basename($element);
		if ($basename_manual != $basename) {
			$locale = setlocale(LC_CTYPE, "0");
			if ('C' == $locale) {
				setlocale(LC_CTYPE, 'en_US.UTF8');
				$basename_new = basename($element);
				if ($basename_new == $basename_manual) $basename = $basename_new;
				setlocale(LC_CTYPE, $locale);
			}
		}
		return $basename;
	}

	/**
	 * Determine if a file should be stored without compression
	 *
	 * @param String $file - the filename
	 *
	 * @return Boolean
	 */
	private function file_should_be_stored_without_compression($file) {
		if (!is_array($this->extensions_to_not_compress)) return false;
		foreach ($this->extensions_to_not_compress as $ext) {
			$ext_len = strlen($ext);
			if (strtolower(substr($file, -$ext_len, $ext_len)) == $ext) return true;
		}
		return false;
	}

	/**
	 * This method will add a manifest file to the backup zip
	 *
	 * @param  String $whichone - the type of backup (e.g. 'plugins', 'themes')
	 *
	 * @return Boolean - success/failure status
	 */
	private function updraftplus_include_manifest($whichone) {
		global $updraftplus;

		$manifest_name = "updraftplus-manifest.json";
		$manifest = trailingslashit($this->updraft_dir).$manifest_name;

		$updraftplus->log(sprintf("Creating file manifest ($manifest_name) for incremental backup (included: %d, skipped: %d)", count($this->zipfiles_batched), count($this->zipfiles_skipped_notaltered)));
		
		if (false === ($handle = fopen($manifest, 'w+'))) return $updraftplus->log("Failed to open manifest file ($manifest_name)");

		$this->manifest_path = $manifest;

		$version = 1;
		
		$go_to_levels = array(
			'plugins' => 2,
			'themes' => 2,
			'uploads' => 3,
			'others' => 3
		);

		$go_to_levels = apply_filters('updraftplus_manifest_go_to_level', $go_to_levels, $whichone);

		$go_to_level = isset($go_to_levels[$whichone]) ? $go_to_levels[$whichone] : 'all';

		$directory = '';

		if ('more' == $whichone) {
			foreach ($this->zipfiles_batched as $index => $dir) {
				$directory = '"directory":"' . dirname($index) . '",';
			}
		}

		if (false === fwrite($handle, '{"version":'.$version.',"type":"'.$whichone.'",'.$directory.'"listed_levels":"'.$go_to_level.'","contents":{"directories":[')) $updraftplus->log("First write to manifest file failed ($manifest_name)");

		// First loop: find out which is the last entry, so that we don't write the comma after it
		$last_dir_index = false;
		foreach ($this->zipfiles_dirbatched as $index => $dir) {
			if ('all' !== $go_to_level && substr_count($dir, '/') > $go_to_level - 1) continue;
			$last_dir_index = $index;
		}
		
		// Second loop: write out the entry
		foreach ($this->zipfiles_dirbatched as $index => $dir) {
			if ('all' !== $go_to_level && substr_count($dir, '/') > $go_to_level - 1) continue;
			fwrite($handle, json_encode($dir).(($index != $last_dir_index) ? ',' : ''));
		}
		
		// Now do the same for files
		fwrite($handle, '],"files":[');
		
		$last_file_index = false;
		foreach ($this->zipfiles_batched as $store_as) {
			if ('all' !== $go_to_level && substr_count($store_as, '/') > $go_to_level - 1) continue;
			$last_file_index = $store_as;
		}
		foreach ($this->zipfiles_skipped_notaltered as $store_as) {
			if ('all' !== $go_to_level && substr_count($store_as, '/') > $go_to_level - 1) continue;
			$last_file_index = $store_as;
		}

		foreach ($this->zipfiles_batched as $store_as) {
			if ('all' !== $go_to_level && substr_count($store_as, '/') > $go_to_level - 1) continue;
			fwrite($handle, json_encode($store_as).(($store_as != $last_file_index) ? ',' : ''));
		}

		foreach ($this->zipfiles_skipped_notaltered as $store_as) {
			if ('all' !== $go_to_level && substr_count($store_as, '/') > $go_to_level - 1) continue;
			fwrite($handle, json_encode($store_as).(($store_as != $last_file_index) ? ',' : ''));
		}

		fwrite($handle, ']}}');
		fclose($handle);

		$this->zipfiles_batched[$manifest] = $manifest_name;
		
		$updraftplus->log("Successfully created file manifest (size: ".filesize($manifest).")");
		
		return true;
	}

	// Q. Why don't we only open and close the zip file just once?
	// A. Because apparently PHP doesn't write out until the final close, and it will return an error if anything file has vanished in the meantime. So going directory-by-directory reduces our chances of hitting an error if the filesystem is changing underneath us (which is very possible if dealing with e.g. 1GB of files)

	/**
	 * We batch up the files, rather than do them one at a time. So we are more efficient than open,one-write,close.
	 * To call into here, the array $this->zipfiles_batched must be populated (keys=paths, values=add-to-zip-as values). It gets reset upon exit from here.
	 *
	 * @param Boolean $warn_on_failures See if it warns on faliures or not
	 *
	 * @return Boolean|WP_Error
	 */
	private function makezip_addfiles($warn_on_failures) {

		global $updraftplus;

		// Used to detect requests to bump the size
		$bump_index = false;
		$ret = true;

		$zipfile = $this->zip_basename.((0 == $this->index) ? '' : ($this->index+1)).'.zip.tmp';

		$maxzipbatch = $updraftplus->jobdata_get('maxzipbatch', 26214400);
		if ((int) $maxzipbatch < 1024) $maxzipbatch = 26214400;

		// Short-circuit the null case, because we want to detect later if something useful happenned
		if (count($this->zipfiles_dirbatched) == 0 && count($this->zipfiles_batched) == 0) return true;

		// If on PclZip, then if possible short-circuit to a quicker method (makes a huge time difference - on a folder of 1500 small files, 2.6s instead of 76.6)
		// This assumes that makezip_addfiles() is only called once so that we know about all needed files (the new style)
		// This is rather conservative - because it assumes zero compression. But we can't know that in advance.
		$force_allinone = false;
		if (0 == $this->index && $this->makezip_recursive_batchedbytes < $this->zip_split_every) {
			// So far, we only have a processor for this for PclZip; but that check can be removed - need to address the below items
			// TODO: Is this really what we want? Always go all-in-one for < 500MB???? Should be more conservative? Or, is it always faster to go all-in-one? What about situations where we might want to auto-split because of slowness - check that that is still working.
			// TODO: Test this new method for PclZip - are we still getting the performance gains? Test for ZipArchive too.
			if ('UpdraftPlus_PclZip' == $this->use_zip_object && ($this->makezip_recursive_batchedbytes < 512*1048576 || (defined('UPDRAFTPLUS_PCLZIP_FORCEALLINONE') && UPDRAFTPLUS_PCLZIP_FORCEALLINONE == true && 'UpdraftPlus_PclZip' == $this->use_zip_object))) {
				$updraftplus->log("Only one archive required (".$this->use_zip_object.") - will attempt to do in single operation (data: ".round($this->makezip_recursive_batchedbytes/1024, 1)." KB, split: ".round($this->zip_split_every/1024, 1)." KB)");
				// $updraftplus->log("PclZip, and only one archive required - will attempt to do in single operation (data: ".round($this->makezip_recursive_batchedbytes/1024, 1)." KB, split: ".round($this->zip_split_every/1024, 1)." KB)");
								$force_allinone = true;
				// if(!class_exists('PclZip')) require_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
				// $zip = new PclZip($zipfile);
				// $remove_path = ($this->whichone == 'wpcore') ? untrailingslashit(ABSPATH) : WP_CONTENT_DIR;
				// $add_path = false;
				// Remove prefixes
				// $backupable_entities = $updraftplus->get_backupable_file_entities(true);
				// if (isset($backupable_entities[$this->whichone])) {
				// if ('plugins' == $this->whichone || 'themes' == $this->whichone || 'uploads' == $this->whichone) {
				// $remove_path = dirname($backupable_entities[$this->whichone]);
				// To normalise instead of removing (which binzip doesn't support, so we don't do it), you'd remove the dirname() in the above line, and uncomment the below one.
				// #$add_path = $this->whichone;
				// } else {
				// $remove_path = $backupable_entities[$this->whichone];
				// }
				// }
				// if ($add_path) {
				// $zipcode = $zip->create($this->source, PCLZIP_OPT_REMOVE_PATH, $remove_path, PCLZIP_OPT_ADD_PATH, $add_path);
				// } else {
				// $zipcode = $zip->create($this->source, PCLZIP_OPT_REMOVE_PATH, $remove_path);
				// }
				// if ($zipcode == 0) {
				// $updraftplus->log("PclZip Error: ".$zip->errorInfo(true), 'warning');
				// return $zip->errorCode();
				// } else {
				// UpdraftPlus_Job_Scheduler::something_useful_happened();
				// return true;
				// }
			}
		}

		// 05-Mar-2013 - added a new check on the total data added; it appears that things fall over if too much data is contained in the cumulative total of files that were addFile'd without a close-open cycle; presumably data is being stored in memory. In the case in question, it was a batch of MP3 files of around 100MB each - 25 of those equals 2.5GB!

		$data_added_since_reopen = 0;
		// static $data_added_this_resumption = 0;
		// $max_data_added_any_resumption = $updraftplus->jobdata_get('max_data_added_any_resumption', 0);
		
		// The following array is used only for error reporting if ZipArchive::close fails (since that method itself reports no error messages - we have to track manually what we were attempting to add)
		$files_zipadded_since_open = array();

		$zip = new $this->use_zip_object;
		if (file_exists($zipfile)) {
			$opencode = $zip->open($zipfile);
			$original_size = filesize($zipfile);
			clearstatcache();
		} else {
			$create_code = (version_compare(PHP_VERSION, '5.2.12', '>') && defined('ZIPARCHIVE::CREATE')) ? ZIPARCHIVE::CREATE : 1;
			$opencode = $zip->open($zipfile, $create_code);
			$original_size = 0;
		}

		if (true !== $opencode) return new WP_Error('no_open', sprintf(__('Failed to open the zip file (%s) - %s', 'updraftplus'), $zipfile, $zip->last_error));

		if (apply_filters('updraftplus_include_manifest', false, $this->whichone, $this)) {
			$this->updraftplus_include_manifest($this->whichone);
		}

		// Make sure all directories are created before we start creating files
		while ($dir = array_pop($this->zipfiles_dirbatched)) {
			$zip->addEmptyDir($dir);
		}
		$zipfiles_added_thisbatch = 0;

		// Go through all those batched files
		foreach ($this->zipfiles_batched as $file => $add_as) {

			if (!file_exists($file)) {
				$updraftplus->log("File has vanished from underneath us; dropping: $add_as");
				continue;
			}

			$fsize = filesize($file);

			$large_file_warning_key = 'vlargefile_'.md5($this->whichone.'#'.$add_as);
			
			if (defined('UPDRAFTPLUS_SKIP_FILE_OVER_SIZE') && UPDRAFTPLUS_SKIP_FILE_OVER_SIZE && $fsize > UPDRAFTPLUS_SKIP_FILE_OVER_SIZE) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$updraftplus->log("File is larger than the user-configured (UPDRAFTPLUS_SKIP_FILE_OVER_SIZE) maximum (is: ".round($fsize/1024, 1)." KB); will skip: ".$add_as);
				continue;
			} elseif ($fsize > UPDRAFTPLUS_WARN_FILE_SIZE) {
			
				$log_msg = __('A very large file was encountered: %s (size: %s Mb)', 'updraftplus');
			
				// Was this warned about on a previous run?
				if ($updraftplus->warning_exists($large_file_warning_key)) {
					$updraftplus->log_remove_warning($large_file_warning_key);
					$large_file_warning_key .= '-2';
					$log_msg .= ' - '.__('a second attempt is being made (upon further failure it will be skipped)', 'updraftplus');
				} elseif ($updraftplus->warning_exists($large_file_warning_key.'-2') || $updraftplus->warning_exists($large_file_warning_key.'-final')) {
					$updraftplus->log_remove_warning($large_file_warning_key.'-2');
					$large_file_warning_key .= '-final';
					$log_msg .= ' - '.__('two unsuccessful attempts were made to include it, and it will now be omitted from the backup', 'updraftplus');
				}
			
				$updraftplus->log(sprintf($log_msg, $add_as, round($fsize/1048576, 1)), 'warning', $large_file_warning_key);
				
				if ('-final' == substr($large_file_warning_key, -6, 6)) {
					continue;
				}
			}

			// Skips files that are already added
			if (!isset($this->existing_files[$add_as]) || $this->existing_files[$add_as] != $fsize) {

				@touch($zipfile);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$zip->addFile($file, $add_as);
				$zipfiles_added_thisbatch++;

				if (method_exists($zip, 'setCompressionName') && $this->file_should_be_stored_without_compression($add_as) && false == $zip->setCompressionName($add_as, ZipArchive::CM_STORE)) {
					$updraftplus->log("Zip: setCompressionName failed on: $add_as");
				}

				// N.B., Since makezip_addfiles() can get called more than once if there were errors detected, potentially $zipfiles_added_thisrun can exceed the total number of batched files (if they get processed twice).
				$this->zipfiles_added_thisrun++;
				$files_zipadded_since_open[] = array('file' => $file, 'addas' => $add_as);

				$data_added_since_reopen += $fsize;
				// $data_added_this_resumption += $fsize;
				/* Conditions for forcing a write-out and re-open:
				- more than $maxzipbatch bytes have been batched
				- more than 2.0 seconds have passed since the last time we wrote
				- that adding this batch of data is likely already enough to take us over the split limit (and if that happens, then do actually split - to prevent a scenario of progressively tinier writes as we approach but don't actually reach the limit)
				- more than 500 files batched (should perhaps intelligently lower this as the zip file gets bigger - not yet needed)
				*/

				// Add 10% margin. It only really matters when the OS has a file size limit, exceeding which causes failure (e.g. 2GB on 32-bit)
				// Since we don't test before the file has been created (so that zip_last_ratio has meaningful data), we rely on max_zip_batch being less than zip_split_every - which should always be the case
				$reaching_split_limit = ($this->zip_last_ratio > 0 && $original_size>0 && ($original_size + 1.1*$data_added_since_reopen*$this->zip_last_ratio) > $this->zip_split_every) ? true : false;

				if (!$force_allinone && ($zipfiles_added_thisbatch > UPDRAFTPLUS_MAXBATCHFILES || $reaching_split_limit || $data_added_since_reopen > $maxzipbatch || (time() - $this->zipfiles_lastwritetime) > 2)) {

					// We are coming towards a limit and about to close the zip, check if this is a more file backup and the manifest file has made it into this zip if not add it
					if (apply_filters('updraftplus_include_manifest', false, $this->whichone, $this)) {
						
						$manifest = false;

						foreach ($files_zipadded_since_open as $info) {
							if ('updraftplus-manifest.json' == $info['file']) $manifest = true;
						}

						if (!$manifest) {
							@touch($zipfile);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
							$path = array_search('updraftplus-manifest.json', $this->zipfiles_batched);
							$zip->addFile($path, 'updraftplus-manifest.json');
							$zipfiles_added_thisbatch++;

							if (method_exists($zip, 'setCompressionName') && $this->file_should_be_stored_without_compression($this->zipfiles_batched[$path])) {
								if (false == $zip->setCompressionName($this->zipfiles_batched[$path], ZipArchive::CM_STORE)) {
									$updraftplus->log("Zip: setCompressionName failed on: $this->zipfiles_batched[$path]");
								}
							}

							// N.B., Since makezip_addfiles() can get called more than once if there were errors detected, potentially $zipfiles_added_thisrun can exceed the total number of batched files (if they get processed twice).
							$this->zipfiles_added_thisrun++;
							$files_zipadded_since_open[] = array('file' => $path, 'addas' => 'updraftplus-manifest.json');
							$data_added_since_reopen += filesize($path);
							// $data_added_this_resumption += filesize($path);
						}
					}

					if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$something_useful_sizetest = false;

					if ($data_added_since_reopen > $maxzipbatch) {
						$something_useful_sizetest = true;
						$updraftplus->log("Adding batch to zip file (".$this->use_zip_object."): over ".round($maxzipbatch/1048576, 1)." MB added on this batch (".round($data_added_since_reopen/1048576, 1)." MB, ".count($this->zipfiles_batched)." files batched, $zipfiles_added_thisbatch (".$this->zipfiles_added_thisrun.") added so far); re-opening (prior size: ".round($original_size/1024, 1).' KB)');
					} elseif ($zipfiles_added_thisbatch > UPDRAFTPLUS_MAXBATCHFILES) {
						$updraftplus->log("Adding batch to zip file (".$this->use_zip_object."): over ".UPDRAFTPLUS_MAXBATCHFILES." files added on this batch (".round($data_added_since_reopen/1048576, 1)." MB, ".count($this->zipfiles_batched)." files batched, $zipfiles_added_thisbatch (".$this->zipfiles_added_thisrun.") added so far); re-opening (prior size: ".round($original_size/1024, 1).' KB)');
					} elseif (!$reaching_split_limit) {
						$updraftplus->log("Adding batch to zip file (".$this->use_zip_object."): over 2.0 seconds have passed since the last write (".round($data_added_since_reopen/1048576, 1)." MB, $zipfiles_added_thisbatch (".$this->zipfiles_added_thisrun.") files added so far); re-opening (prior size: ".round($original_size/1024, 1).' KB)');
					} else {
						$updraftplus->log("Adding batch to zip file (".$this->use_zip_object."): possibly approaching split limit (".round($data_added_since_reopen/1048576, 1)." MB, $zipfiles_added_thisbatch (".$this->zipfiles_added_thisrun.") files added so far); last ratio: ".round($this->zip_last_ratio, 4)."; re-opening (prior size: ".round($original_size/1024, 1).' KB)');
					}

					if (!$zip->close()) {
						// Though we will continue processing the files we've got, the final error code will be false, to allow a second attempt on the failed ones. This also keeps us consistent with a negative result for $zip->close() further down. We don't just retry here, because we have seen cases (with BinZip) where upon failure, the existing zip had actually been deleted. So, to be safe we need to re-scan the existing zips.
						$ret = false;
						$this->record_zip_error($files_zipadded_since_open, $zip->last_error, $warn_on_failures);
					}

					// if ($data_added_this_resumption > $max_data_added_any_resumption) {
					// $max_data_added_any_resumption = $data_added_this_resumption;
					// $updraftplus->jobdata_set('max_data_added_any_resumption', $max_data_added_any_resumption);
					// }
					
					$zipfiles_added_thisbatch = 0;

					// This triggers a re-open, later
					unset($zip);
					$files_zipadded_since_open = array();
					// Call here, in case we've got so many big files that we don't complete the whole routine
					if (filesize($zipfile) > $original_size) {

						// It is essential that this does not go above 1, even though in reality (and this can happen at the start, if just 1 file is added (e.g. due to >2.0s detection) the 'compressed' zip file may be *bigger* than the files stored in it. When that happens, if the ratio is big enough, it can then fire the "approaching split limit" detection (very) prematurely
						$this->zip_last_ratio = ($data_added_since_reopen > 0) ? min((filesize($zipfile) - $original_size)/$data_added_since_reopen, 1) : 1;

						// We need a rolling update of this
						$original_size = filesize($zipfile);

						// Move on to next zip?
						if ($reaching_split_limit || filesize($zipfile) > $this->zip_split_every) {
							$bump_index = true;
							// Take the filesize now because later we wanted to know we did clearstatcache()
							$bumped_at = round(filesize($zipfile)/1048576, 1);
						}

						// Need to make sure that something_useful_happened() is always called

						// How long since the current run began? If it's taken long (and we're in danger of not making it at all), or if that is forseeable in future because of general slowness, then we should reduce the parameters.
						if (!$something_useful_sizetest) {
							UpdraftPlus_Job_Scheduler::something_useful_happened();
						} else {

							// Do this as early as possible
							UpdraftPlus_Job_Scheduler::something_useful_happened();

							$time_since_began = max(microtime(true)- $this->zipfiles_lastwritetime, 0.000001);
							$normalised_time_since_began = $time_since_began*($maxzipbatch/$data_added_since_reopen);

							// Don't measure speed until after ZipArchive::close()
							$rate = round($data_added_since_reopen/$time_since_began, 1);

							$updraftplus->log(sprintf("A useful amount of data was added after this amount of zip processing: %s s (normalised: %s s, rate: %s KB/s)", round($time_since_began, 1), round($normalised_time_since_began, 1), round($rate/1024, 1)));

							// We want to detect not only that we need to reduce the size of batches, but also the capability to increase them. This is particularly important because of ZipArchive()'s (understandable, given the tendency of PHP processes being terminated without notice) practice of first creating a temporary zip file via copying before acting on that zip file (so the information is atomic). Unfortunately, once the size of the zip file gets over 100MB, the copy operation beguns to be significant. By the time you've hit 500MB on many web hosts the copy is the majority of the time taken. So we want to do more in between these copies if possible.

							/* "Could have done more" - detect as:
							- A batch operation would still leave a "good chunk" of time in a run
							- "Good chunk" means that the time we took to add the batch is less than 50% of a run time
							- We can do that on any run after the first (when at least one ceiling on the maximum time is known)
							- But in the case where a max_execution_time is long (so that resumptions are never needed), and we're always on run 0, we will automatically increase chunk size if the batch took less than 6 seconds.
							*/

							// At one stage we had a strategy of not allowing check-ins to have more than 20s between them. However, once the zip file got to a certain size, PHP's habit of copying the entire zip file first meant that it *always* went over 18s, and thence a drop in the max size was inevitable - which was bad, because with the copy time being something that only grew, the outcome was less data being copied every time

							// Gather the data. We try not to do this unless necessary (may be time-sensitive)
							if ($updraftplus->current_resumption >= 1) {
								$time_passed = $updraftplus->jobdata_get('run_times');
								if (!is_array($time_passed)) $time_passed = array();
								list($max_time, $timings_string, $run_times_known) = UpdraftPlus_Manipulation_Functions::max_time_passed($time_passed, $updraftplus->current_resumption-1, $this->first_run);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
							} else {
								// $run_times_known = 0;
								// $max_time = -1;
								$run_times_known = 1;
								$max_time = microtime(true)-$updraftplus->opened_log_time;
							}

							if ($normalised_time_since_began < 6 || ($updraftplus->current_resumption >= 1 && $run_times_known >= 1 && $time_since_began < 0.6*$max_time) || (0 == $updraftplus->current_resumption && $max_time > 240)) {

								// How much can we increase it by?
								if ($normalised_time_since_began < 6 || 0 == $updraftplus->current_resumption) {
									if ($run_times_known > 0 && $max_time > 0) {
										$new_maxzipbatch = min(floor(max($maxzipbatch*6/$normalised_time_since_began, $maxzipbatch*((0.6*$max_time)/$normalised_time_since_began))), $this->zip_batch_ceiling);
									} else {
										// Maximum of 200MB in a batch
										$new_maxzipbatch = min(floor($maxzipbatch*6/$normalised_time_since_began), $this->zip_batch_ceiling);
									}
								} else {
									// Use up to 60% of available time
									$new_maxzipbatch = min(floor($maxzipbatch*((0.6*$max_time)/$normalised_time_since_began)), $this->zip_batch_ceiling);
								}

								// Throttle increases - don't increase by more than 2x in one go - ???
								// $new_maxzipbatch = floor(min(2*$maxzipbatch, $new_maxzipbatch));
								// Also don't allow anything that is going to be more than 18 seconds - actually, that's harmful because of the basically fixed time taken to copy the file
								// $new_maxzipbatch = floor(min(18*$rate ,$new_maxzipbatch));

								// Don't go above the split amount (though we expect that to be higher anyway, unless sending via email)
								$new_maxzipbatch = min($new_maxzipbatch, $this->zip_split_every);

								// Don't raise it above a level that failed on a previous run
								$maxzipbatch_ceiling = $updraftplus->jobdata_get('maxzipbatch_ceiling');
								if (is_numeric($maxzipbatch_ceiling) && $maxzipbatch_ceiling > 20*1048576 && $new_maxzipbatch > $maxzipbatch_ceiling) {
									$updraftplus->log("Was going to raise maxzipbytes to $new_maxzipbatch, but this is too high: a previous failure led to the ceiling being set at $maxzipbatch_ceiling, which we will use instead");
									$new_maxzipbatch = $maxzipbatch_ceiling;
								}

								// Final sanity check
								if ($new_maxzipbatch > 1048576) $updraftplus->jobdata_set('maxzipbatch', $new_maxzipbatch);
								
								if ($new_maxzipbatch <= 1048576) {
									$updraftplus->log("Unexpected new_maxzipbatch value obtained (time=$time_since_began, normalised_time=$normalised_time_since_began, max_time=$max_time, data points known=$run_times_known, old_max_bytes=$maxzipbatch, new_max_bytes=$new_maxzipbatch)");
								} elseif ($new_maxzipbatch > $maxzipbatch) {
									$updraftplus->log("Performance is good - will increase the amount of data we attempt to batch (time=$time_since_began, normalised_time=$normalised_time_since_began, max_time=$max_time, data points known=$run_times_known, old_max_bytes=$maxzipbatch, new_max_bytes=$new_maxzipbatch)");
								} elseif ($new_maxzipbatch < $maxzipbatch) {
									// Ironically, we thought we were speedy...
									$updraftplus->log("Adjust: Reducing maximum amount of batched data (time=$time_since_began, normalised_time=$normalised_time_since_began, max_time=$max_time, data points known=$run_times_known, new_max_bytes=$new_maxzipbatch, old_max_bytes=$maxzipbatch)");
								} else {
									$updraftplus->log("Performance is good - but we will not increase the amount of data we batch, as we are already at the present limit (time=$time_since_began, normalised_time=$normalised_time_since_began, max_time=$max_time, data points known=$run_times_known, max_bytes=$maxzipbatch)");
								}

								if ($new_maxzipbatch > 1048576) $maxzipbatch = $new_maxzipbatch;
							}

							// Detect excessive slowness
							// Don't do this until we're on at least resumption 7, as we want to allow some time for things to settle down and the maxiumum time to be accurately known (since reducing the batch size unnecessarily can itself cause extra slowness, due to PHP's usage of temporary zip files)
								
							// We use a percentage-based system as much as possible, to avoid the various criteria being in conflict with each other (i.e. a run being both 'slow' and 'fast' at the same time, which is increasingly likely as max_time gets smaller).

							if (!$updraftplus->something_useful_happened && $updraftplus->current_resumption >= 7) {

								UpdraftPlus_Job_Scheduler::something_useful_happened();

								if ($run_times_known >= 5 && ($time_since_began > 0.8 * $max_time || $time_since_began + 7 > $max_time)) {

									$new_maxzipbatch = max(floor($maxzipbatch*0.8), 20971520);
									if ($new_maxzipbatch < $maxzipbatch) {
										$maxzipbatch = $new_maxzipbatch;
										$updraftplus->jobdata_set("maxzipbatch", $new_maxzipbatch);
										$updraftplus->log("We are within a small amount of the expected maximum amount of time available; the zip-writing thresholds will be reduced (time_passed=$time_since_began, normalised_time_passed=$normalised_time_since_began, max_time=$max_time, data points known=$run_times_known, old_max_bytes=$maxzipbatch, new_max_bytes=$new_maxzipbatch)");
									} else {
										$updraftplus->log("We are within a small amount of the expected maximum amount of time available, but the zip-writing threshold is already at its lower limit (20MB), so will not be further reduced (max_time=$max_time, data points known=$run_times_known, max_bytes=$maxzipbatch)");
									}
								}

							} else {
								UpdraftPlus_Job_Scheduler::something_useful_happened();
							}
						}
						$data_added_since_reopen = 0;
					} else {
						// ZipArchive::close() can take a very long time, which we want to know about
						UpdraftPlus_Job_Scheduler::record_still_alive();
					}

					clearstatcache();
					$this->zipfiles_lastwritetime = time();
				}
			} elseif (0 == $this->zipfiles_added_thisrun) {
				// Update lastwritetime, because otherwise the 2.0-second-activity detection can fire prematurely (e.g. if it takes >2.0 seconds to process the previously-written files, then the detector fires after 1 file. This then can have the knock-on effect of having something_useful_happened() called, but then a subsequent attempt to write out a lot of meaningful data fails, and the maximum batch is not then reduced.
				// Testing shows that calling time() 1000 times takes negligible time
				$this->zipfiles_lastwritetime = time();
			}

			$this->zipfiles_added++;

			// Don't call something_useful_happened() here - nothing necessarily happens until close() is called
			if (0 == $this->zipfiles_added % 100) {
				$skip_dblog = ($this->zipfiles_added_thisrun > 0 || 0 == $this->zipfiles_added % 1000) ? false : true;
				$updraftplus->log("Zip: ".basename($zipfile).": ".$this->zipfiles_added." files added (on-disk size: ".round(@filesize($zipfile)/1024, 1)." KB)", 'notice', false, $skip_dblog);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}

			if ($bump_index) {
				$updraftplus->log(sprintf("Zip size is at/near split limit (%s MB / %s MB) - bumping index (from: %d)", $bumped_at, round($this->zip_split_every/1048576, 1), $this->index));
				$bump_index = false;
				$this->bump_index();
				$zipfile = $this->zip_basename.($this->index+1).'.zip.tmp';
			}

			if (empty($zip)) {
				$zip = new $this->use_zip_object;

				if (file_exists($zipfile)) {
					$opencode = $zip->open($zipfile);
					$original_size = filesize($zipfile);
					clearstatcache();
				} else {
					$create_code = defined('ZIPARCHIVE::CREATE') ? ZIPARCHIVE::CREATE : 1;
					$opencode = $zip->open($zipfile, $create_code);
					$original_size = 0;
				}

				if (true !== $opencode) return new WP_Error('no_open', sprintf(__('Failed to open the zip file (%s) - %s', 'updraftplus'), $zipfile, $zip->last_error));
			}

		}

		// Reset array
		$this->zipfiles_batched = array();
		$this->zipfiles_skipped_notaltered = array();

		if (false == ($nret = $zip->close())) $this->record_zip_error($files_zipadded_since_open, $zip->last_error, $warn_on_failures);

		if (apply_filters('updraftplus_include_manifest', false, $this->whichone, $this)) {
			if (!empty($this->manifest_path) && file_exists($this->manifest_path)) {
				$updraftplus->log('Removing manifest file: '.basename($this->manifest_path).': '.(@unlink($this->manifest_path) ? 'OK' : 'failed'));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		}

		$this->zipfiles_lastwritetime = time();
		// May not exist if the last thing we did was bump
		if (file_exists($zipfile) && filesize($zipfile) > $original_size) UpdraftPlus_Job_Scheduler::something_useful_happened();

		// Move on to next archive?
		if (file_exists($zipfile) && filesize($zipfile) > $this->zip_split_every) {
			$updraftplus->log(sprintf("Zip size has gone over split limit (%s, %s) - bumping index (%d)", round(filesize($zipfile)/1048576, 1), round($this->zip_split_every/1048576, 1), $this->index));
			$this->bump_index();
		}

		$manifest = preg_replace('/\.tmp$/', '.list.tmp', $zipfile);
		if (!file_exists($manifest)) $this->write_zip_manifest_from_zip($zipfile);

		clearstatcache();

		return (false == $ret) ? false : $nret;
	}

	private function record_zip_error($files_zipadded_since_open, $msg, $warn = true) {
		global $updraftplus;

		if (!empty($updraftplus->cpanel_quota_readable)) {
			$hosting_bytes_free = $updraftplus->get_hosting_disk_quota_free();
			if (is_array($hosting_bytes_free)) {
				$perc = round(100*$hosting_bytes_free[1]/(max($hosting_bytes_free[2], 1)), 1);
				$quota_free_msg = sprintf('Free disk space in account: %s (%s used)', round($hosting_bytes_free[3]/1048576, 1)." MB", "$perc %");
				$updraftplus->log($quota_free_msg);
				if ($hosting_bytes_free[3] < 1048576*50) {
					$quota_low = true;
					$quota_free_mb = round($hosting_bytes_free[3]/1048576, 1);
					$updraftplus->log(sprintf(__('Your free space in your hosting account is very low - only %s Mb remain', 'updraftplus'), $quota_free_mb), 'warning', 'lowaccountspace'.$quota_free_mb);
				}
			}
		}

		// Always warn of this
		if (strpos($msg, 'File Size Limit Exceeded') !== false && 'UpdraftPlus_BinZip' == $this->use_zip_object) {
			$updraftplus->log(sprintf(__('The zip engine returned the message: %s.', 'updraftplus'), 'File Size Limit Exceeded'). __('Go here for more information.', 'updraftplus').' https://updraftplus.com/what-should-i-do-if-i-see-the-message-file-size-limit-exceeded/', 'warning', 'zipcloseerror-filesizelimit');
		} elseif ($warn) {
			$warn_msg = __('A zip error occurred', 'updraftplus').' - ';
			if (!empty($quota_low)) {
				$warn_msg = sprintf(__('your web hosting account appears to be full; please see: %s', 'updraftplus'), 'https://updraftplus.com/faqs/how-much-free-disk-space-do-i-need-to-create-a-backup/');
			} else {
				$warn_msg .= __('check your log for more details.', 'updraftplus');
			}
			$updraftplus->log($warn_msg, 'warning', 'zipcloseerror-'.$this->whichone);
		}

		$updraftplus->log("The attempt to close the zip file returned an error ($msg). List of files we were trying to add follows (check their permissions).");

		foreach ($files_zipadded_since_open as $ffile) {
			$updraftplus->log("File: ".$ffile['addas']." (exists: ".(int) @file_exists($ffile['file']).", is_readable: ".(int) @is_readable($ffile['file'])." size: ".@filesize($ffile['file']).')', 'notice', false, true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}
	}

	/**
	 * Bump the zip index number. No parameters or returned value, since it is dealing with class variables.
	 */
	private function bump_index() {
		global $updraftplus;
		$youwhat = $this->whichone;

		$timetaken = max(microtime(true)-$this->zip_microtime_start, 0.000001);

		$itext = (0 == $this->index) ? '' : ($this->index+1);
		$full_path = $this->zip_basename.$itext.'.zip';
		
		$checksums = $updraftplus->which_checksums();
		
		$checksum_description = '';
		
		foreach ($checksums as $checksum) {
		
			$cksum = hash_file($checksum, $full_path.'.tmp');
			$updraftplus->jobdata_set($checksum.'-'.$youwhat.$this->index, $cksum);
			if ($checksum_description) $checksum_description .= ', ';
			$checksum_description .= "$checksum: $cksum";
		
		}

		$next_full_path = $this->zip_basename.($this->index+2).'.zip';
		// We touch the next zip before renaming the temporary file; this indicates that the backup for the entity is not *necessarily* finished
		touch($next_full_path.'.tmp');

		if (file_exists($full_path.'.tmp') && filesize($full_path.'.tmp') > 0) {
			if (!rename($full_path.'.tmp', $full_path)) {
				$updraftplus->log("Rename failed for $full_path.tmp");
			} else {
				$manifest = $full_path.'.list.tmp';
				if (!file_exists($manifest)) $this->write_zip_manifest_from_zip($full_path);
				UpdraftPlus_Job_Scheduler::something_useful_happened();
			}
		}
		
		$kbsize = filesize($full_path)/1024;
		$rate = round($kbsize/$timetaken, 1);
		$updraftplus->log("Created ".$this->whichone." zip (".$this->index.") - ".round($kbsize, 1)." KB in ".round($timetaken, 1)." s ($rate KB/s) (checksums: $checksum_description)");
		$this->zip_microtime_start = microtime(true);

		// No need to add $itext here - we can just delete any temporary files for this zip
		UpdraftPlus_Filesystem_Functions::clean_temporary_files('_'.$updraftplus->file_nonce."-".$youwhat, 600);

		$this->index++;
		$this->job_file_entities[$youwhat]['index'] = $this->index;
		$updraftplus->jobdata_set('job_file_entities', $this->job_file_entities);
	}

	/**
	 * This function will populate $this->existing_files with a list of files found inside the passed in zip
	 *
	 * @param string  $zip_path           - the zip file name we want to list files for; must end in .tmp
	 * @param boolean $read_from_manifest - a boolean to indicate if we should try to read from the manifest or not
	 *
	 * @return void
	 */
	private function populate_existing_files_list($zip_path, $read_from_manifest) {
		global $updraftplus;

		// Get the name of the final manifest file
		if (preg_match('/\.tmp$/', $zip_path)) {
			$manifest = preg_replace('/\.tmp$/', '.list.tmp', $zip_path);
		} else {
			$manifest = $zip_path.'.list.tmp';
		}

		if ($read_from_manifest && file_exists($manifest)) {
			$manifest_contents = json_decode(file_get_contents($manifest), true);

			if (empty($manifest_contents)) {
				$updraftplus->log("Zip manifest file found, but reading failed: ".basename($manifest));
			} elseif (!empty($manifest_contents['files'])) {
				$this->existing_files = array_merge($this->existing_files, $manifest_contents['files'][0]);
				$updraftplus->log("Successfully read zip manifest file contents");
				return;
			} else {
				$updraftplus->log("Zip manifest file found, but no files contents were found: ".basename($manifest));
			}
		} elseif ($read_from_manifest) {
			$updraftplus->log("No zip manifest file found; will create one");
		}

		$zip = new $this->use_zip_object;
		if (true !== $zip->open($zip_path)) {
			$updraftplus->log("Could not open zip file to examine (".$zip->last_error."); will remove: ".basename($zip_path));
			@unlink($zip_path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {

			$this->existing_zipfiles_size += filesize($zip_path);
			
			// Don't put this in the for loop, or the magic __get() method which accessing the property invokes gets repeatedly called every time the loop goes round
			$numfiles = $zip->numFiles;

			for ($i=0; $i < $numfiles; $i++) {
				$si = $zip->statIndex($i);
				$name = $si['name'];
				// Exclude folders
				if ('/' == substr($name, -1)) continue;
				if (!isset($this->existing_files[$name])) {
					$this->existing_files[$name] = $si['size'];
					$this->existing_files_rawsize += $si['size'];
				}
			}

			@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

			if (preg_match('/\.tmp$/', $zip_path)) {
				$manifest = preg_replace('/\.tmp$/', '.list-temp.tmp', $zip_path);
			} else {
				$manifest = $zip_path.'.list-temp.tmp';
			}

			$this->write_zip_manifest_from_list($manifest, $this->existing_files);

			$updraftplus->log(basename($zip_path).": Zip file already exists, with ".count($this->existing_files)." files");
		}
	}

	/**
	 * This function will get a list of files found inside the passed in zip and call the function to create the zip manifest, returns true on success and false on failure
	 *
	 * @uses self::write_zip_manifest_from_list()
	 * @param string $zip_path - the zip file path; must end in .tmp
	 *
	 * @return boolean - returns true on success and false on failure
	 */
	private function write_zip_manifest_from_zip($zip_path) {
		global $updraftplus;
		
		$zip_files = array();

		$zip = new $this->use_zip_object;
		if (true !== $zip->open($zip_path)) {
			$updraftplus->log("Could not open zip file to examine (".$zip->last_error."); file: ".basename($zip_path));
			return false;
		} else {
			// Don't put this in the for loop, or the magic __get() method gets repeatedly called every time the loop goes round
			$numfiles = $zip->numFiles;

			for ($i=0; $i < $numfiles; $i++) {
				$si = $zip->statIndex($i);
				$name = $si['name'];
				// Exclude folders
				if ('/' == substr($name, -1)) continue;
				$zip_files[$name] = $si['size'];
			}

			@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}
		
		if (empty($zip_files)) {
			$updraftplus->log("Did not find any files in the zip: ".basename($zip_path));
			return false;
		}

		if (preg_match('/\.tmp$/', $zip_path)) {
			$manifest = preg_replace('/\.tmp$/', '.list-temp.tmp', $zip_path);
		} else {
			$manifest = $zip_path.'.list-temp.tmp';
		}

		$this->write_zip_manifest_from_list($manifest, $zip_files);
		
		return true;
	}

	/**
	 * This function will create and write the contents of the zip manifest
	 *
	 * @param string $manifest  - path of the manifest file
	 * @param array  $zip_files - an array of files and their sizes
	 *
	 * @return boolean - returns false on failure to write
	 */
	private function write_zip_manifest_from_list($manifest, $zip_files) {
		global $updraftplus;

		$updraftplus->log("Creating zip file manifest ({$manifest})");
		
		if (false === ($handle = fopen($manifest, 'w+'))) {
			$updraftplus->log("Failed to open zip manifest file ({$manifest})");
			return false;
		}

		$version = 1;
		
		if (false === fwrite($handle, '{"version":'.$version.', "files":[{')) {
			$updraftplus->log("First write to manifest file failed ({$manifest})");
			return false;
		}

		$last_dir_index = key(array_slice($zip_files, -1, 1, true));

		foreach ($zip_files as $name => $size) {
			fwrite($handle, json_encode($name).' : '.$size.(($name != $last_dir_index) ? ',' : ''));
		}

		fwrite($handle, '}]}');
		fclose($handle);
		
		$updraftplus->log("Successfully created zip file manifest (size: ".filesize($manifest).")");

		$final_manifest = preg_replace('/\.list-temp.tmp$/', '.list.tmp', $manifest);
		rename($manifest, $final_manifest);
	}

	/**
	 * Returns the member of the array with key (int)0, as a new array. This function is used as a callback for array_map().
	 *
	 * @param Array $a - the array
	 *
	 * @return Array - with keys 'name' and 'type'
	 */
	private function cb_get_name_base_type($a) {
		return array('name' => $a[0], 'type' => 'BASE TABLE');
	}

	/**
	 * Returns the members of the array with keys (int)0 and (int)1, as part of a new array.
	 *
	 * @param Array $a - the array
	 *
	 * @return Array - keys are 'name' and 'type'
	 */
	private function cb_get_name_type($a) {
		return array('name' => $a[0], 'type' => $a[1]);
	}

	/**
	 * Returns the member of the array with key (string)'name'. This function is used as a callback for array_map().
	 *
	 * @param Array $a - the array
	 *
	 * @return Mixed - the value with key (string)'name'
	 */
	private function cb_get_name($a) {
		return $a['name'];
	}

	/**
	 * Exclude files from backup
	 *
	 * @param Boolean $filter initial boolean value of whether the given file is excluded or not
	 * @param String  $file   the full path of the filename to be checked
	 * @return Boolean true if the specified file will be excluded, false otherwise
	 */
	public function backup_exclude_file($filter, $file) {
		foreach ($this->backup_excluded_patterns as $pattern) {
			if (0 === stripos($file, $pattern['directory']) && preg_match($pattern['regex'], $file)) return true;
		}
		return $filter;
	}
}

class UpdraftPlus_WPDB_OtherDB extends wpdb {
	/**
	 * This adjusted bail() does two things: 1) Never dies and 2) logs in the UD log
	 *
	 * @param  String $message    Error text
	 * @param  String $error_code Error code
	 *
	 * @return Boolean
	 */
	public function bail($message, $error_code = '500') {
		global $updraftplus;
		if ('db_connect_fail' == $error_code) $message = 'Connection failed: check your access details, that the database server is up, and that the network connection is not firewalled.';
		$updraftplus->log("WPDB_OtherDB error: $message ($error_code)");
		// Now do the things that would have been done anyway
		if (class_exists('WP_Error')) {
			$this->error = new WP_Error($error_code, $message);
		} else {
			$this->error = $message;
		}
		return false;
	}
}
