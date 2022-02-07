<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Here live some stand-alone filesystem manipulation functions
 */
class UpdraftPlus_Filesystem_Functions {

	/**
	 * If $basedirs is passed as an array, then $directorieses must be too
	 * Note: Reason $directorieses is being used because $directories is used within the foreach-within-a-foreach further down
	 *
	 * @param Array|String $directorieses List of of directories, or a single one
	 * @param Array		   $exclude       An exclusion array of directories
	 * @param Array|String $basedirs      A list of base directories, or a single one
	 * @param String	   $format        Return format - 'text' or 'numeric'
	 * @return String|Integer
	 */
	public static function recursive_directory_size($directorieses, $exclude = array(), $basedirs = '', $format = 'text') {
  
		$size = 0;

		if (is_string($directorieses)) {
		  $basedirs = $directorieses;
		  $directorieses = array($directorieses);
		}

		if (is_string($basedirs)) $basedirs = array($basedirs);

		foreach ($directorieses as $ind => $directories) {
			if (!is_array($directories)) $directories = array($directories);

			$basedir = empty($basedirs[$ind]) ? $basedirs[0] : $basedirs[$ind];

			foreach ($directories as $dir) {
				if (is_file($dir)) {
					$size += @filesize($dir);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} else {
					$suffix = ('' != $basedir) ? ((0 === strpos($dir, $basedir.'/')) ? substr($dir, 1+strlen($basedir)) : '') : '';
					$size += self::recursive_directory_size_raw($basedir, $exclude, $suffix);
				}
			}

		}

		if ('numeric' == $format) return $size;

		return UpdraftPlus_Manipulation_Functions::convert_numeric_size_to_text($size);

	}
	
	/**
	 * Ensure that WP_Filesystem is instantiated and functional. Otherwise, outputs necessary HTML and dies.
	 *
	 * @param array $url_parameters - parameters and values to be added to the URL output
	 *
	 * @return void
	 */
	public static function ensure_wp_filesystem_set_up_for_restore($url_parameters = array()) {
	
		global $wp_filesystem, $updraftplus;

		$build_url = UpdraftPlus_Options::admin_page().'?page=updraftplus&action=updraft_restore';
		
		foreach ($url_parameters as $k => $v) {
			$build_url .= '&'.$k.'='.$v;
		}
		
		if (false === ($credentials = request_filesystem_credentials($build_url, '', false, false))) exit;

		if (!WP_Filesystem($credentials)) {

			$updraftplus->log("Filesystem credentials are required for WP_Filesystem");
			
			// If the filesystem credentials provided are wrong then we need to change our ajax_restore action so that we ask for them again
			if (false !== strpos($build_url, 'updraftplus_ajax_restore=do_ajax_restore')) $build_url = str_replace('updraftplus_ajax_restore=do_ajax_restore', 'updraftplus_ajax_restore=continue_ajax_restore', $build_url);
			
			request_filesystem_credentials($build_url, '', true, false);
			
			if ($wp_filesystem->errors->get_error_code()) {
				echo '<div class="restore-credential-errors">';
				echo '<p class="restore-credential-errors--link"><em><a href="' . apply_filters('updraftplus_com_link', "https://updraftplus.com/faqs/asked-ftp-details-upon-restorationmigration-updates/") . '" target="_blank">' . __('Why am I seeing this?', 'updraftplus') . '</a></em></p>';
				echo '<div class="restore-credential-errors--list">';
				foreach ($wp_filesystem->errors->get_error_messages() as $message) show_message($message);
				echo '</div>';
				echo '</div>';
				exit;
			}
		}
	}
	
	/**
	 * Get the html of "Web-server disk space" line which resides above of the existing backup table
	 *
	 * @param Boolean $will_immediately_calculate_disk_space Whether disk space should be counted now or when user click Refresh link
	 *
	 * @return String Web server disk space html to render
	 */
	public static function web_server_disk_space($will_immediately_calculate_disk_space = true) {
		if ($will_immediately_calculate_disk_space) {
			$disk_space_used = self::get_disk_space_used('updraft', 'numeric');
			if ($disk_space_used > apply_filters('updraftplus_display_usage_line_threshold_size', 104857600)) { // 104857600 = 100 MB = (100 * 1024 * 1024)
				$disk_space_text = UpdraftPlus_Manipulation_Functions::convert_numeric_size_to_text($disk_space_used);
				$refresh_link_text = __('refresh', 'updraftplus');
				return self::web_server_disk_space_html($disk_space_text, $refresh_link_text);
			} else {
				return '';
			}
		} else {
			$disk_space_text = '';
			$refresh_link_text = __('calculate', 'updraftplus');
			return self::web_server_disk_space_html($disk_space_text, $refresh_link_text);
		}
	}
	
	/**
	 * Get the html of "Web-server disk space" line which resides above of the existing backup table
	 *
	 * @param String $disk_space_text   The texts which represents disk space usage
	 * @param String $refresh_link_text Refresh disk space link text
	 *
	 * @return String - Web server disk space HTML
	 */
	public static function web_server_disk_space_html($disk_space_text, $refresh_link_text) {
		return '<li class="updraft-server-disk-space" title="'.esc_attr__('This is a count of the contents of your Updraft directory', 'updraftplus').'"><strong>'.__('Web-server disk space in use by UpdraftPlus', 'updraftplus').':</strong> <span class="updraft_diskspaceused"><em>'.$disk_space_text.'</em></span> <a class="updraft_diskspaceused_update" href="#">'.$refresh_link_text.'</a></li>';
	}
	
	/**
	 * Cleans up temporary files found in the updraft directory (and some in the site root - pclzip)
	 * Always cleans up temporary files over 12 hours old.
	 * With parameters, also cleans up those.
	 * Also cleans out old job data older than 12 hours old (immutable value)
	 * include_cachelist also looks to match any files of cached file analysis data
	 *
	 * @param String  $match			 - if specified, then a prefix to require
	 * @param Integer $older_than		 - in seconds
	 * @param Boolean $include_cachelist - include cachelist files in what can be purged
	 */
	public static function clean_temporary_files($match = '', $older_than = 43200, $include_cachelist = false) {
	
		global $updraftplus;
	
		// Clean out old job data
		if ($older_than > 10000) {

			global $wpdb;
			$table = is_multisite() ? $wpdb->sitemeta : $wpdb->options;
			$key_column = is_multisite() ? 'meta_key' : 'option_name';
			$value_column = is_multisite() ? 'meta_value' : 'option_value';
			
			// Limit the maximum number for performance (the rest will get done next time, if for some reason there was a back-log)
			$all_jobs = $wpdb->get_results("SELECT $key_column, $value_column FROM $table WHERE $key_column LIKE 'updraft_jobdata_%' LIMIT 100", ARRAY_A);
			
			foreach ($all_jobs as $job) {
				$nonce = str_replace('updraft_jobdata_', '', $job[$key_column]);
				$val = maybe_unserialize($job[$value_column]);
				// TODO: Can simplify this after a while (now all jobs use job_time_ms) - 1 Jan 2014
				$delete = false;
				if (!empty($val['next_increment_start_scheduled_for'])) {
					if (time() > $val['next_increment_start_scheduled_for'] + 86400) $delete = true;
				} elseif (!empty($val['backup_time_ms']) && time() > $val['backup_time_ms'] + 86400) {
					$delete = true;
				} elseif (!empty($val['job_time_ms']) && time() > $val['job_time_ms'] + 86400) {
					$delete = true;
				} elseif (!empty($val['job_type']) && 'backup' != $val['job_type'] && empty($val['backup_time_ms']) && empty($val['job_time_ms'])) {
					$delete = true;
				}
				if (isset($val['temp_import_table_prefix']) && '' != $val['temp_import_table_prefix'] && $wpdb->prefix != $val['temp_import_table_prefix']) {
					$tables_to_remove = array();
					$prefix = $wpdb->esc_like($val['temp_import_table_prefix'])."%";
					$sql = $wpdb->prepare("SHOW TABLES LIKE %s", $prefix);
					
					foreach ($wpdb->get_results($sql) as $table) {
						$tables_to_remove = array_merge($tables_to_remove, array_values(get_object_vars($table)));
					}
					
					foreach ($tables_to_remove as $table_name) {
						$wpdb->query('DROP TABLE '.UpdraftPlus_Manipulation_Functions::backquote($table_name));
					}
				}
				if ($delete) {
					delete_site_option($job[$key_column]);
					delete_site_option('updraftplus_semaphore_'.$nonce);
				}
			}
		}
		$updraft_dir = $updraftplus->backups_dir_location();
		$now_time = time();
		$files_deleted = 0;
		if ($handle = opendir($updraft_dir)) {
			while (false !== ($entry = readdir($handle))) {
				$manifest_match = preg_match("/updraftplus-manifest.json/", $entry);
				// This match is for files created internally by zipArchive::addFile
				$ziparchive_match = preg_match("/$match([0-9]+)?\.zip\.tmp\.([A-Za-z0-9]){6}?$/i", $entry);
				// zi followed by 6 characters is the pattern used by /usr/bin/zip on Linux systems. It's safe to check for, as we have nothing else that's going to match that pattern.
				$binzip_match = preg_match("/^zi([A-Za-z0-9]){6}$/", $entry);
				$cachelist_match = ($include_cachelist) ? preg_match("/$match-cachelist-.*.tmp$/i", $entry) : false;
				$browserlog_match = preg_match('/^log\.[0-9a-f]+-browser\.txt$/', $entry);
				// Temporary files from the database dump process - not needed, as is caught by the time-based catch-all
				// $table_match = preg_match("/${match}-table-(.*)\.table(\.tmp)?\.gz$/i", $entry);
				// The gz goes in with the txt, because we *don't* want to reap the raw .txt files
				if ((preg_match("/$match\.(tmp|table|txt\.gz)(\.gz)?$/i", $entry) || $cachelist_match || $ziparchive_match || $binzip_match || $manifest_match || $browserlog_match) && is_file($updraft_dir.'/'.$entry)) {
					// We delete if a parameter was specified (and either it is a ZipArchive match or an order to delete of whatever age), or if over 12 hours old
					if (($match && ($ziparchive_match || $binzip_match || $cachelist_match || $manifest_match || 0 == $older_than) && $now_time-filemtime($updraft_dir.'/'.$entry) >= $older_than) || $now_time-filemtime($updraft_dir.'/'.$entry)>43200) {
						$skip_dblog = (0 == $files_deleted % 25) ? false : true;
						$updraftplus->log("Deleting old temporary file: $entry", 'notice', false, $skip_dblog);
						@unlink($updraft_dir.'/'.$entry);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						$files_deleted++;
					}
				} elseif (preg_match('/^log\.[0-9a-f]+\.txt$/', $entry) && $now_time-filemtime($updraft_dir.'/'.$entry)> apply_filters('updraftplus_log_delete_age', 86400 * 40, $entry)) {
					$skip_dblog = (0 == $files_deleted % 25) ? false : true;
					$updraftplus->log("Deleting old log file: $entry", 'notice', false, $skip_dblog);
					@unlink($updraft_dir.'/'.$entry);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$files_deleted++;
				}
			}
			@closedir($handle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

		// Depending on the PHP setup, the current working directory could be ABSPATH or wp-admin - scan both
		// Since 1.9.32, we set them to go into $updraft_dir, so now we must check there too. Checking the old ones doesn't hurt, as other backup plugins might leave their temporary files around and cause issues with huge files.
		foreach (array(ABSPATH, ABSPATH.'wp-admin/', $updraft_dir.'/') as $path) {
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					// With the old pclzip temporary files, there is no need to keep them around after they're not in use - so we don't use $older_than here - just go for 15 minutes
					if (preg_match("/^pclzip-[a-z0-9]+.tmp$/", $entry) && $now_time-filemtime($path.$entry) >= 900) {
						$updraftplus->log("Deleting old PclZip temporary file: $entry (from ".basename($path).")");
						@unlink($path.$entry);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
				}
				@closedir($handle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		}
	}
	
	/**
	 * Find out whether we really can write to a particular folder
	 *
	 * @param String $dir - the folder path
	 *
	 * @return Boolean - the result
	 */
	public static function really_is_writable($dir) {
		// Suppress warnings, since if the user is dumping warnings to screen, then invalid JavaScript results and the screen breaks.
		if (!@is_writable($dir)) return false;// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		// Found a case - GoDaddy server, Windows, PHP 5.2.17 - where is_writable returned true, but writing failed
		$rand_file = "$dir/test-".md5(rand().time()).".txt";
		while (file_exists($rand_file)) {
			$rand_file = "$dir/test-".md5(rand().time()).".txt";
		}
		$ret = @file_put_contents($rand_file, 'testing...');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		@unlink($rand_file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		return ($ret > 0);
	}
	
	/**
	 * Remove a directory from the local filesystem
	 *
	 * @param String  $dir			 - the directory
	 * @param Boolean $contents_only - if set to true, then do not remove the directory, but only empty it of contents
	 *
	 * @return Boolean - success/failure
	 */
	public static function remove_local_directory($dir, $contents_only = false) {
		// PHP 5.3+ only
		// foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
		// $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		// }
		// return rmdir($dir);

		if ($handle = @opendir($dir)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			while (false !== ($entry = readdir($handle))) {
				if ('.' !== $entry && '..' !== $entry) {
					if (is_dir($dir.'/'.$entry)) {
						self::remove_local_directory($dir.'/'.$entry, false);
					} else {
						@unlink($dir.'/'.$entry);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
				}
			}
			@closedir($handle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

		return $contents_only ? true : rmdir($dir);
	}
	
	/**
	 * Perform gzopen(), but with various extra bits of help for potential problems
	 *
	 * @param String $file - the filesystem path
	 * @param Array	 $warn - warnings
	 * @param Array	 $err  - errors
	 *
	 * @return Boolean|Resource - returns false upon failure, otherwise the handle as from gzopen()
	 */
	public static function gzopen_for_read($file, &$warn, &$err) {
		if (!function_exists('gzopen') || !function_exists('gzread')) {
			$missing = '';
			if (!function_exists('gzopen')) $missing .= 'gzopen';
			if (!function_exists('gzread')) $missing .= ($missing) ? ', gzread' : 'gzread';
			$err[] = sprintf(__("Your web server's PHP installation has these functions disabled: %s.", 'updraftplus'), $missing).' '.sprintf(__('Your hosting company must enable these functions before %s can work.', 'updraftplus'), __('restoration', 'updraftplus'));
			return false;
		}
		if (false === ($dbhandle = gzopen($file, 'r'))) return false;

		if (!function_exists('gzseek')) return $dbhandle;

		if (false === ($bytes = gzread($dbhandle, 3))) return false;
		// Double-gzipped?
		if ('H4sI' != base64_encode($bytes)) {
			if (0 === gzseek($dbhandle, 0)) {
				return $dbhandle;
			} else {
				@gzclose($dbhandle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				return gzopen($file, 'r');
			}
		}
		// Yes, it's double-gzipped

		$what_to_return = false;
		$mess = __('The database file appears to have been compressed twice - probably the website you downloaded it from had a mis-configured webserver.', 'updraftplus');
		$messkey = 'doublecompress';
		$err_msg = '';

		if (false === ($fnew = fopen($file.".tmp", 'w')) || !is_resource($fnew)) {

			@gzclose($dbhandle);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$err_msg = __('The attempt to undo the double-compression failed.', 'updraftplus');

		} else {

			@fwrite($fnew, $bytes);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$emptimes = 0;
			while (!gzeof($dbhandle)) {
				$bytes = @gzread($dbhandle, 262144);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				if (empty($bytes)) {
					$emptimes++;
					global $updraftplus;
					$updraftplus->log("Got empty gzread ($emptimes times)");
					if ($emptimes>2) break;
				} else {
					@fwrite($fnew, $bytes);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				}
			}

			gzclose($dbhandle);
			fclose($fnew);
			// On some systems (all Windows?) you can't rename a gz file whilst it's gzopened
			if (!rename($file.".tmp", $file)) {
				$err_msg = __('The attempt to undo the double-compression failed.', 'updraftplus');
			} else {
				$mess .= ' '.__('The attempt to undo the double-compression succeeded.', 'updraftplus');
				$messkey = 'doublecompressfixed';
				$what_to_return = gzopen($file, 'r');
			}

		}

		$warn[$messkey] = $mess;
		if (!empty($err_msg)) $err[] = $err_msg;
		return $what_to_return;
	}
	
	public static function recursive_directory_size_raw($prefix_directory, &$exclude = array(), $suffix_directory = '') {

		$directory = $prefix_directory.('' == $suffix_directory ? '' : '/'.$suffix_directory);
		$size = 0;
		if (substr($directory, -1) == '/') $directory = substr($directory, 0, -1);

		if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory)) return -1;
		if (file_exists($directory.'/.donotbackup')) return 0;

		if ($handle = opendir($directory)) {
			while (($file = readdir($handle)) !== false) {
				if ('.' != $file && '..' != $file) {
					$spath = ('' == $suffix_directory) ? $file : $suffix_directory.'/'.$file;
					if (false !== ($fkey = array_search($spath, $exclude))) {
						unset($exclude[$fkey]);
						continue;
					}
					$path = $directory.'/'.$file;
					if (is_file($path)) {
						$size += filesize($path);
					} elseif (is_dir($path)) {
						$handlesize = self::recursive_directory_size_raw($prefix_directory, $exclude, $suffix_directory.('' == $suffix_directory ? '' : '/').$file);
						if ($handlesize >= 0) {
							$size += $handlesize;
						}
					}
				}
			}
			closedir($handle);
		}

		return $size;

	}

	/**
	 * Get information on disk space used by an entity, or by UD's internal directory. Returns as a human-readable string.
	 *
	 * @param String $entity - the entity (e.g. 'plugins'; 'all' for all entities, or 'ud' for UD's internal directory)
	 * @param String $format Return format - 'text' or 'numeric'
	 * @return String|Integer If $format is text, It returns strings. Otherwise integer value.
	 */
	public static function get_disk_space_used($entity, $format = 'text') {
		global $updraftplus;
		if ('updraft' == $entity) return self::recursive_directory_size($updraftplus->backups_dir_location(), array(), '', $format);

		$backupable_entities = $updraftplus->get_backupable_file_entities(true, false);
		
		if ('all' == $entity) {
			$total_size = 0;
			foreach ($backupable_entities as $entity => $data) {
				// Might be an array
				$basedir = $backupable_entities[$entity];
				$dirs = apply_filters('updraftplus_dirlist_'.$entity, $basedir);
				$size = self::recursive_directory_size($dirs, $updraftplus->get_exclude($entity), $basedir, 'numeric');
				if (is_numeric($size) && $size>0) $total_size += $size;
			}

			if ('numeric' == $format) {
				return $total_size;
			} else {
				return UpdraftPlus_Manipulation_Functions::convert_numeric_size_to_text($total_size);
			}
			
		} elseif (!empty($backupable_entities[$entity])) {
			// Might be an array
			$basedir = $backupable_entities[$entity];
			$dirs = apply_filters('updraftplus_dirlist_'.$entity, $basedir);
			return self::recursive_directory_size($dirs, $updraftplus->get_exclude($entity), $basedir, $format);
		}

		// Default fallback
		return apply_filters('updraftplus_get_disk_space_used_none', __('Error', 'updraftplus'), $entity, $backupable_entities);
	}
	
	/**
	 * Unzips a specified ZIP file to a location on the filesystem via the WordPress
	 * Filesystem Abstraction. Forked from WordPress core in version 5.1-alpha-44182.
	 * Forked to allow us to modify the behaviour (eventually, to provide feedback on progress)
	 *
	 * Assumes that WP_Filesystem() has already been called and set up. Does not extract
	 * a root-level __MACOSX directory, if present.
	 *
	 * Attempts to increase the PHP memory limit before uncompressing. However,
	 * the most memory required shouldn't be much larger than the archive itself.
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @param String  $file			  - Full path and filename of ZIP archive.
	 * @param String  $to			  - Full path on the filesystem to extract archive to.
	 * @param Integer $starting_index - index of entry to start unzipping from (allows resumption)
	 *
	 * @return Boolean|WP_Error True on success, WP_Error on failure.
	 */
	public static function unzip_file($file, $to, $starting_index = 0) {
		global $wp_filesystem;

		if (!$wp_filesystem || !is_object($wp_filesystem)) {
			return new WP_Error('fs_unavailable', __('Could not access filesystem.'));
		}

		// Unzip can use a lot of memory, but not this much hopefully.
		if (function_exists('wp_raise_memory_limit')) wp_raise_memory_limit('admin');

		$needed_dirs = array();
		$to = trailingslashit($to);

		// Determine any parent dir's needed (of the upgrade directory)
		if (!$wp_filesystem->is_dir($to)) { // Only do parents if no children exist
			$path = preg_split('![/\\\]!', untrailingslashit($to));
			for ($i = count($path); $i >= 0; $i--) {
			
				if (empty($path[$i])) continue;

				$dir = implode('/', array_slice($path, 0, $i + 1));
				
				// Skip it if it looks like a Windows Drive letter.
				if (preg_match('!^[a-z]:$!i', $dir)) continue;

				// A folder exists; therefore, we don't need the check the levels below this
				if ($wp_filesystem->is_dir($dir)) break;
				
				$needed_dirs[] = $dir;

			}
		}

		static $added_unzip_action = false;
		if (!$added_unzip_action) {
			add_action('updraftplus_unzip_file_unzipped', array('UpdraftPlus_Filesystem_Functions', 'unzip_file_unzipped'), 10, 5);
			$added_unzip_action = true;
		}
		
		if (class_exists('ZipArchive', false) && apply_filters('unzip_file_use_ziparchive', true)) {
			$result = self::unzip_file_go($file, $to, $needed_dirs, 'ziparchive', $starting_index);
			if (true === $result || (is_wp_error($result) && 'incompatible_archive' != $result->get_error_code())) return $result;
		}
		
		// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
		// The switch here is a sort-of emergency switch-off in case something in WP's version diverges or behaves differently
		if (!defined('UPDRAFTPLUS_USE_INTERNAL_PCLZIP') || UPDRAFTPLUS_USE_INTERNAL_PCLZIP) {
			return self::unzip_file_go($file, $to, $needed_dirs, 'pclzip', $starting_index);
		} else {
			return _unzip_file_pclzip($file, $to, $needed_dirs);
		}
	}
	
	/**
	 * Called upon the WP action updraftplus_unzip_file_unzipped, to indicate that a file has been unzipped.
	 *
	 * @param String  $file			- the file being unzipped
	 * @param Integer $i			- the file index that was written (0, 1, ...)
	 * @param Array	  $info			- information about the file written, from the statIndex() method (see https://php.net/manual/en/ziparchive.statindex.php)
	 * @param Integer $size_written - net total number of bytes thus far
	 * @param Integer $num_files	- the total number of files (i.e. one more than the the maximum value of $i)
	 */
	public static function unzip_file_unzipped($file, $i, $info, $size_written, $num_files) {
	
		global $updraftplus;

		static $last_file_seen = null;

		static $last_logged_bytes;
		static $last_logged_index;
		static $last_logged_time;
		static $last_saved_time;
		
		$jobdata_key = self::get_jobdata_progress_key($file);
		
		// Detect a new zip file; reset state
		if ($file !== $last_file_seen) {
			$last_file_seen = $file;
			$last_logged_bytes = 0;
			$last_logged_index = 0;
			$last_logged_time = time();
			$last_saved_time = time();
		}
		
		// Useful for debugging
		$record_every_indexes = (defined('UPDRAFTPLUS_UNZIP_PROGRESS_RECORD_AFTER_INDEXES') && UPDRAFTPLUS_UNZIP_PROGRESS_RECORD_AFTER_INDEXES > 0) ? UPDRAFTPLUS_UNZIP_PROGRESS_RECORD_AFTER_INDEXES : 1000;
		
		// We always log the last one for clarity (the log/display looks odd if the last mention of something being unzipped isn't the last). Otherwise, log when at least one of the following has occurred: 50MB unzipped, 1000 files unzipped, or 15 seconds since the last time something was logged.
		if ($i >= $num_files -1 || $size_written > $last_logged_bytes + 100 * 1048576 || $i > $last_logged_index + $record_every_indexes || time() > $last_logged_time + 15) {
		
			$updraftplus->jobdata_set($jobdata_key, array('index' => $i, 'info' => $info, 'size_written' => $size_written));
			
			$updraftplus->log(sprintf(__('Unzip progress: %d out of %d files', 'updraftplus').' (%s, %s)', $i+1, $num_files, UpdraftPlus_Manipulation_Functions::convert_numeric_size_to_text($size_written), $info['name']), 'notice-restore');
			$updraftplus->log(sprintf('Unzip progress: %d out of %d files (%s, %s)', $i+1, $num_files, UpdraftPlus_Manipulation_Functions::convert_numeric_size_to_text($size_written), $info['name']), 'notice');
			
			do_action('updraftplus_unzip_progress_restore_info', $file, $i, $size_written, $num_files);

			$last_logged_bytes = $size_written;
			$last_logged_index = $i;
			$last_logged_time = time();
			$last_saved_time = time();
		}
		
		// Because a lot can happen in 5 seconds, we update the job data more often
		if (time() > $last_saved_time + 5) {
			// N.B. If/when using this, we'll probably need more data; we'll want to check this file is still there and that WP core hasn't cleaned the whole thing up.
			$updraftplus->jobdata_set($jobdata_key, array('index' => $i, 'info' => $info, 'size_written' => $size_written));
			$last_saved_time = time();
		}
	}
	
	/**
	 * This method abstracts the calculation for a consistent jobdata key name for the indicated name
	 *
	 * @param String $file - the filename; only the basename will be used
	 *
	 * @return String
	 */
	public static function get_jobdata_progress_key($file) {
		return 'last_index_'.md5(basename($file));
	}
	
	/**
	 * Compatibility function (exists in WP 4.8+)
	 */
	public static function wp_doing_cron() {
		if (function_exists('wp_doing_cron')) return wp_doing_cron();
		return apply_filters('wp_doing_cron', defined('DOING_CRON') && DOING_CRON);
	}
	
	/**
	 * Attempts to unzip an archive; forked from _unzip_file_ziparchive() in WordPress 5.1-alpha-44182, and modified to use the UD zip classes.
	 *
	 * Assumes that WP_Filesystem() has already been called and set up.
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @param String  $file		  	  - full path and filename of ZIP archive.
	 * @param String  $to		  	  - full path on the filesystem to extract archive to.
	 * @param Array	  $needed_dirs	  - a partial list of required folders needed to be created.
	 * @param String  $method	 	  - either 'ziparchive' or 'pclzip'.
	 * @param Integer $starting_index - index of entry to start unzipping from (allows resumption)
	 *
	 * @return Boolean|WP_Error True on success, WP_Error on failure.
	 */
	private static function unzip_file_go($file, $to, $needed_dirs = array(), $method = 'ziparchive', $starting_index = 0) {
		global $wp_filesystem, $updraftplus;
		
		$class_to_use = ('ziparchive' == $method) ? 'UpdraftPlus_ZipArchive' : 'UpdraftPlus_PclZip';

		if (!class_exists($class_to_use)) require_once(UPDRAFTPLUS_DIR.'/includes/class-zip.php');
		
		$updraftplus->log('Unzipping '.basename($file).' to '.$to.' using '.$class_to_use.', starting index '.$starting_index);
		
		$z = new $class_to_use;

		$flags = (version_compare(PHP_VERSION, '5.2.12', '>') && defined('ZIPARCHIVE::CHECKCONS')) ? ZIPARCHIVE::CHECKCONS : 4;
		
		// This is just for crazy people with mbstring.func_overload enabled (deprecated from PHP 7.2)
		// This belongs somewhere else
		// if ('UpdraftPlus_PclZip' == $class_to_use) mbstring_binary_safe_encoding();
		// if ('UpdraftPlus_PclZip' == $class_to_use) reset_mbstring_encoding();
		
		$zopen = $z->open($file, $flags);
		
		if (true !== $zopen) {
			return new WP_Error('incompatible_archive', __('Incompatible Archive.'), array($method.'_error' => $z->last_error));
		}

		$uncompressed_size = 0;

		$num_files = $z->numFiles;
		
		for ($i = $starting_index; $i < $num_files; $i++) {
			if (!$info = $z->statIndex($i)) {
				return new WP_Error('stat_failed_'.$method, __('Could not retrieve file from archive.').' ('.$z->last_error.')');
			}

			// Skip the OS X-created __MACOSX directory
			if ('__MACOSX/' === substr($info['name'], 0, 9)) continue;

			// Don't extract invalid files:
			if (0 !== validate_file($info['name'])) continue;

			$uncompressed_size += $info['size'];

			if ('/' === substr($info['name'], -1)) {
				// Directory.
				$needed_dirs[] = $to . untrailingslashit($info['name']);
			} elseif ('.' !== ($dirname = dirname($info['name']))) {
				// Path to a file.
				$needed_dirs[] = $to . untrailingslashit($dirname);
			}
		}

		/*
		* disk_free_space() could return false. Assume that any falsey value is an error.
		* A disk that has zero free bytes has bigger problems.
		* Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
		*/
		if (self::wp_doing_cron()) {
			$available_space = function_exists('disk_free_space') ? @disk_free_space(WP_CONTENT_DIR) : false;// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if ($available_space && ($uncompressed_size * 2.1) > $available_space) {
				return new WP_Error('disk_full_unzip_file', __('Could not copy files. You may have run out of disk space.'), compact('uncompressed_size', 'available_space'));
			}
		}

		$needed_dirs = array_unique($needed_dirs);
		foreach ($needed_dirs as $dir) {
			// Check the parent folders of the folders all exist within the creation array.
			if (untrailingslashit($to) == $dir) { // Skip over the working directory, We know this exists (or will exist)
				continue;
			}
			
			// If the directory is not within the working directory, Skip it
			if (false === strpos($dir, $to)) continue;

			$parent_folder = dirname($dir);
			while (!empty($parent_folder) && untrailingslashit($to) != $parent_folder && !in_array($parent_folder, $needed_dirs)) {
				$needed_dirs[] = $parent_folder;
				$parent_folder = dirname($parent_folder);
			}
		}
		asort($needed_dirs);

		// Create those directories if need be:
		foreach ($needed_dirs as $_dir) {
			// Only check to see if the Dir exists upon creation failure. Less I/O this way.
			if (!$wp_filesystem->mkdir($_dir, FS_CHMOD_DIR) && !$wp_filesystem->is_dir($_dir)) {
				return new WP_Error('mkdir_failed_'.$method, __('Could not create directory.'), substr($_dir, strlen($to)));
			}
		}
		unset($needed_dirs);

		$size_written = 0;
		
		$content_cache = array();
		$content_cache_highest = -1;

		for ($i = $starting_index; $i < $num_files; $i++) {

			if (!$info = $z->statIndex($i)) {
				return new WP_Error('stat_failed_'.$method, __('Could not retrieve file from archive.'));
			}

			// directory
			if ('/' == substr($info['name'], -1)) continue;

			// Don't extract the OS X-created __MACOSX
			if ('__MACOSX/' === substr($info['name'], 0, 9)) continue;

			// Don't extract invalid files:
			if (0 !== validate_file($info['name'])) continue;

			// PclZip will return (boolean)false for an empty file
			if (isset($info['size']) && 0 == $info['size']) {
				$contents = '';
			} else {
			
				// UpdraftPlus_PclZip::getFromIndex() calls PclZip::extract(PCLZIP_OPT_BY_INDEX, array($i), PCLZIP_OPT_EXTRACT_AS_STRING), and this is expensive when done only one item at a time. We try to cache in chunks for good performance as well as being able to resume.
				if ($i > $content_cache_highest && 'UpdraftPlus_PclZip' == $class_to_use) {

					$memory_usage = memory_get_usage(false);
					$total_memory = $updraftplus->memory_check_current();
				
					if ($memory_usage > 0 && $total_memory > 0) {
						$memory_free = $total_memory*1048576 - $memory_usage;
					} else {
						// A sane default. Anything is ultimately better than WP's default of just unzipping everything into memory.
						$memory_free = 50*1048576;
					}
					
					$use_memory = max(10485760, $memory_free - 10485760);

					$total_byte_count = 0;
					$content_cache = array();
					$cache_indexes = array();
					
					$cache_index = $i;
					while ($cache_index < $num_files && $total_byte_count < $use_memory) {
						if (false !== ($cinfo = $z->statIndex($cache_index)) && isset($cinfo['size']) && '/' != substr($cinfo['name'], -1) && '__MACOSX/' !== substr($cinfo['name'], 0, 9) && 0 === validate_file($cinfo['name'])) {
							$total_byte_count += $cinfo['size'];
							if ($total_byte_count < $use_memory) {
								$cache_indexes[] = $cache_index;
								$content_cache_highest = $cache_index;
							}
						}
						$cache_index++;
					}

					if (!empty($cache_indexes)) {
						$content_cache = $z->updraftplus_getFromIndexBulk($cache_indexes);
					}
				}
				$contents = isset($content_cache[$i]) ? $content_cache[$i] : $z->getFromIndex($i);
			}
			
			if (false === $contents && ('pclzip' !== $method || 0 !== $info['size'])) {
				return new WP_Error('extract_failed_'.$method, __('Could not extract file from archive.').' '.$z->last_error, json_encode($info));
			}

			if (!$wp_filesystem->put_contents($to . $info['name'], $contents, FS_CHMOD_FILE)) {
				return new WP_Error('copy_failed_'.$method, __('Could not copy file.'), $info['name']);
			}

			if (!empty($info['size'])) $size_written += $info['size'];

			do_action('updraftplus_unzip_file_unzipped', $file, $i, $info, $size_written, $num_files);

		}

		$z->close();

		return true;
	}
}
