<?php
// Download the Plugin zip file version based on the current Plugin version installed.
// Ensure that the Plugin zip file is not downloaded repeatedly due to an error, issue or problem.
function bpsPro_plugin_zip_download($mstime) {
global $wp_version;
	
	$time_start = microtime( true );
	
	set_time_limit($mstime);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/plugin-hashes' ) ) {
		@mkdir( WP_CONTENT_DIR . '/bps-backup/plugin-hashes', 0755, true );
		@chmod( WP_CONTENT_DIR . '/bps-backup/plugin-hashes/', 0755 );
	}

	$plugin_hashes_dir = WP_CONTENT_DIR . '/bps-backup/plugin-hashes';
	
	if ( ! is_dir( $plugin_hashes_dir ) ) {
		
		fwrite( $handle, "Plugin Zip File Download Error: The $plugin_hashes_dir folder does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check that the Ownership or folder permissions for the /bps-backup/ folder. The /bps-backup/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		return false;
	}

	$plugin_hash_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
	$blank_hash_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wp-hashes.php';

	if ( ! file_exists($plugin_hash_file) ) {
		
		if ( ! copy($blank_hash_file, $plugin_hash_file) ) {
			fwrite( $handle, "Plugin Zip File Download Error: Unable to create the Plugin hash file: $plugin_hash_file\r\n" );
		}
	}

	fwrite( $handle, "Plugin Zip File Download: Start Plugin zip file downloads.\r\n" );

	// Note: $value['TextDomain'] is not reliable. Use $key instead.
	$all_plugins = get_plugins();
	
	$active_plugins_array = array();
	$inactive_plugins_array = array();
	$hello_dolly_plugin_array = array();

	foreach ( $all_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
		
			$active_plugins = in_array( $key, apply_filters('active_plugins', get_option('active_plugins')));

			if ( 1 == $active_plugins || is_plugin_active_for_network( $key ) ) {
			
				$pos = strpos($key, '/');
				$dolly_pos = strpos($value['Name'], 'Hello Dolly');				
				
				if ( $pos !== false ) {

					$plugin_name = strstr($key, '/', true);
					$active_plugins_array[] = $plugin_name.'.'.$value['Version'];
				
				} else {
					
					if ( $dolly_pos !== false ) {
					
						$hello_dolly_plugin_array[] = 'hello-dolly'.'.'.$value['Version'];
					}				
				}
			
			} else {
				
				$pos = strpos($key, '/');
				$dolly_pos = strpos($value['Name'], 'Hello Dolly');
				
				if ( $pos !== false ) {
					
					$plugin_name = strstr($key, '/', true);
					$inactive_plugins_array[] = $plugin_name.'.'.$value['Version'];	
				
				} else {
					
					if ( $dolly_pos !== false ) {
					
						$hello_dolly_plugin_array[] = 'hello-dolly'.'.'.$value['Version'];
					}
				}
			}
		}
	}

	$plugins_array_merged = array_merge($active_plugins_array, $inactive_plugins_array, $hello_dolly_plugin_array);
	
	$bps_mscan_plugin_hash_version_check_array = array();
	$bps_mscan_plugin_hash_paths_array = array();
	$bps_mscan_plugin_hash_zip_file_array = array();

	$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
	$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
	$tmp_file = '';
	$plugin_no_zip_array = array();
	
	foreach ( $plugins_array_merged as $key => $value ) {
		
		// Plugins: https://downloads.wordpress.org/plugin/plugin-name.1.0.zip
		$plugin_zip_file = $value . '.zip';
		$local_zip_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/' . $plugin_zip_file;

		$plugin_name = strstr($value, '.', true); // plugin-name
		$plugin_version = strstr($value, '.'); // .1.0
		$plugin_version_nodot = substr($plugin_version, 1);	// 1.0

 		$bps_mscan_plugin_hash_version_check_array[$plugin_name] = $plugin_version_nodot;
		$bps_mscan_plugin_hash_paths_array[$plugin_name][] = '';
		
		// Check the WP Plugin Repo for plugins that do not exist in the Repo. ie premium/paid and custom plugins. 
		// Note: some plugins do not include the version # in the zip file name. Those plugins will not be downloaded.
		$plugin_zip_file_url = 'https://downloads.wordpress.org/plugin/' . $plugin_zip_file;
		
		$response = wp_remote_get( $plugin_zip_file_url );
	
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			
			if ( $response['response']['code'] == '404' ) {
				
				$value = preg_replace( '/\.\d(.*)/', "", $value );
				$plugin_no_zip_array[] = $value;
			}
		}
		
		@$bps_mscan_plugin_hash_version_check = isset($mscan_plugin_hash['bps_mscan_plugin_hash_version_check']) ? $mscan_plugin_hash['bps_mscan_plugin_hash_version_check']["$plugin_name"] : '';

		// If the BPS DB option plugin version matches then don't download another zip file and just log a log entry.
		// Note: Any plugins not in the WP Plugin Repository will be listed in the array for later use.
		if ( $bps_mscan_plugin_hash_version_check == $plugin_version_nodot ) {
			
			//fwrite( $handle, "Plugin Zip File Download: No new Plugin Zip files were downloaded.\r\n" );
		
		} else {

			// Extra Precaution: Don't download another zip file if it already exists.
			if ( ! file_exists($local_zip_file) ) {
	
				if ( file_exists($plugin_hash_file) ) {
					
					$url = 'https://downloads.wordpress.org/plugin/' . $plugin_zip_file;
				 	$tmp_file = download_url( $url, $timeout = 300 );
			
					// Important: Do not use a return in this condition or all valid downloads will fail.
					// This condition prevents a fatal error on WP_ERROR in PHP 8, which halts processing.
					if ( is_wp_error( $tmp_file ) ) {

						fwrite( $handle, "Plugin Zip File Download: WP_Error: Unable to download Plugin zip file: $plugin_zip_file from WordPress.org.\r\n" );
				
					} else {

						if ( ! copy( $tmp_file, $local_zip_file )  ) {
							fwrite( $handle, "Plugin Zip File Download: Unable to download this Plugin zip file: $plugin_zip_file\r\n" );
						} else {
							fwrite( $handle, "Plugin Zip File Download: Zip file download successful: $plugin_zip_file\r\n" );
						}
						
						unlink( $tmp_file );
					}
				}
			}
		}
	
		if ( file_exists( $local_zip_file ) ) {
			$zip_file = 'yes';
		} else {
			$zip_file = 'no';			
		}
		
		$bps_mscan_plugin_hash_zip_file_array[$plugin_name] = $zip_file;	
	}

	// Update the non-downloadable plugin DB option on each scan run.
	// display this db option in a the new report that will be created. ie these plugins are not downloadable from WP. premium/paid and custom plugins.
	// Add this help info: use the plugin upload zip form to upload premium/paid or custom plugin zip files
	// Any plugins that are uploaded using the plugin zip upload form will be automatically extracted, added to the plugin hash array and deleted on next MScan run.
	$mscan_nodownload = get_option('bulletproof_security_options_mscan_nodownload');
	$bps_theme_nodownload = isset( $mscan_nodownload['bps_theme_nodownload']) ? $mscan_nodownload['bps_theme_nodownload'] : '';
	
	$mscan_nodownload_options = array(
	'bps_plugin_nodownload' 	=> $plugin_no_zip_array, 
	'bps_theme_nodownload' 		=> $bps_theme_nodownload 
	);

	foreach( $mscan_nodownload_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_nodownload', $mscan_nodownload_options);
	}	
	
	$mscan_plugin_hash_options_db = 'bulletproof_security_options_mscan_plugin_hash';
	
	// I'm really only interested in preserving the Plugin hash paths and updating the plugin versions, everything else is an afterthought for future scaleability.
	// On first run plugin hash paths are saved as an empty 2D array, plugin version numbers are saved and zip file yes or no value is saved.
	// Plugin hash paths and hashes are created in the Plugin hash maker function.
	if ( ! get_option( $mscan_plugin_hash_options_db ) ) {	
	
		$mscan_plugin_hash_options = array(
		'bps_mscan_plugin_hash_version_check' 	=> $bps_mscan_plugin_hash_version_check_array, 
		'bps_mscan_plugin_hash_paths' 			=> $bps_mscan_plugin_hash_paths_array, 
		'bps_mscan_plugin_hash_zip_file'		=> $bps_mscan_plugin_hash_zip_file_array 
		);

		foreach( $mscan_plugin_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_plugin_hash', $mscan_plugin_hash_options);
		}

	} else {
		
		// All future runs create new DB arrays used in the Plugin hash maker function to add or remove array values from the first run DB option.
		delete_option('bulletproof_security_options_mscan_p_hash_new');

		$mscan_plugin_hash_options_new = array(
		'bps_mscan_plugin_hash_version_check_new' 	=> $bps_mscan_plugin_hash_version_check_array, 
		'bps_mscan_plugin_hash_paths_new' 			=> $bps_mscan_plugin_hash_paths_array, 
		'bps_mscan_plugin_hash_zip_file_new'		=> $bps_mscan_plugin_hash_zip_file_array 
		);		

		foreach( $mscan_plugin_hash_options_new as $key => $value ) {
			update_option('bulletproof_security_options_mscan_p_hash_new', $mscan_plugin_hash_options_new);
		}

		// Update any new Plugin versions in the first run db option 
		$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
		$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
		
		$plugin_hash_version_check_update_array = array();
		
		foreach ( $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'] as $key => $value ) {
			
			foreach ( $mscan_plugin_hash_new['bps_mscan_plugin_hash_version_check_new'] as $key_new => $value_new ) {
				
				if ( $key == $key_new ) {
		
					$plugin_hash_version_check_update_array[$key] = $value_new;
				}
			}
		}
		
		// Update any new Plugin versions & add/merge any new Plugins by into the first run db option 
		$array_diff_key_plugin_hash_version = array_diff_key($mscan_plugin_hash_new['bps_mscan_plugin_hash_version_check_new'], $mscan_plugin_hash['bps_mscan_plugin_hash_version_check']);
		$array_merge_new_plugin_hash_version = array_merge($plugin_hash_version_check_update_array, $array_diff_key_plugin_hash_version);

		$mscan_plugin_hash_options = array(
		'bps_mscan_plugin_hash_version_check' 	=> $array_merge_new_plugin_hash_version, 
		'bps_mscan_plugin_hash_paths' 			=> $mscan_plugin_hash['bps_mscan_plugin_hash_paths'], 
		'bps_mscan_plugin_hash_zip_file'		=> $mscan_plugin_hash['bps_mscan_plugin_hash_zip_file'] 
		);

		foreach( $mscan_plugin_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_plugin_hash', $mscan_plugin_hash_options);
		}
	}

	$time_end = microtime( true );
	$download_time = $time_end - $time_start;

	$hours = (int)($download_time / 60 / 60);
	$minutes = (int)($download_time / 60) - $hours * 60;
	$seconds = (int)$download_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$download_time_log = 'Plugin Zip File Download Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	fwrite( $handle, "$download_time_log\r\n" );
	fclose($handle);
	
	return true;
}

// Extract the Plugin zip files.
// Note: The extracted plugin folders do not have a version number: /plugin-name/
// Both ZipArchive and PclZip have been tested.
function bpsPro_plugin_zip_extractor() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';

	$handle = fopen( $mscan_log, 'a' );
	
	$plugin_hash_folder = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
	$zip_files = preg_grep('~\.(zip)$~', scandir($plugin_hash_folder));
	
	if ( class_exists('ZipArchive') ) {	

		fwrite( $handle, "Plugin Zip File Extraction: Start ZipArchive zip file extraction.\r\n" );
		
		foreach ( $zip_files as $zip_file ) {
		
			$zip_file_path = $plugin_hash_folder . $zip_file;
			
			$PluginZip = new ZipArchive;
			$res = $PluginZip->open( $zip_file_path );
 
			if ( $res === TRUE ) {
		
				$PluginZip->extractTo( WP_CONTENT_DIR . '/bps-backup/plugin-hashes/' );
				$PluginZip->close();
				
				fwrite( $handle, "Plugin Zip File Extraction ZipArchive: Zip file extraction successful: $zip_file.\r\n" );
			} else {
				fwrite( $handle, "Plugin Zip File Extraction ZipArchive Error: Unable to extract this Plugin zip file: $zip_file.\r\n" );
			}
		}
			
		$time_end = microtime( true );
		$zip_extract_time = $time_end - $time_start;

		$hours = (int)($zip_extract_time / 60 / 60);
		$minutes = (int)($zip_extract_time / 60) - $hours * 60;
		$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

		$zip_extract_time_log = 'Plugin Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

		fwrite( $handle, "$zip_extract_time_log\r\n" );
		fclose($handle);			
		
		return true;

	} else { 
		
		fwrite( $handle, "Plugin Zip File Extraction: Start PclZip zip file extraction.\r\n" );

		define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/plugin-hashes/' );
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
		if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
			$previous_encoding = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}	

		foreach ( $zip_files as $zip_file ) {
		
			$zip_file_path = $plugin_hash_folder . $zip_file;

			$archive = new PclZip( $zip_file_path );
		
			if ( $archive->extract( PCLZIP_OPT_PATH, WP_CONTENT_DIR . '/bps-backup/plugin-hashes', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/plugin-hashes' ) ) {
				fwrite( $handle, "Plugin Zip File Extraction PclZip: Zip file extracted successfully: $zip_file.\r\n" );
			} else {
				fwrite( $handle, "Plugin Zip File Extraction PclZip Error: Unable to unzip this Plugin zip file: $zip_file.\r\n" );
			}
		}
	
		$time_end = microtime( true );
		$zip_extract_time = $time_end - $time_start;

		$hours = (int)($zip_extract_time / 60 / 60);
		$minutes = (int)($zip_extract_time / 60) - $hours * 60;
		$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

		$zip_extract_time_log = 'Plugin Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

		fwrite( $handle, "$zip_extract_time_log\r\n" );
		fclose($handle);			
		
		return true;	
	}
}

// Creates an MD5 plugin file hash array. 
// Cleanup: Deletes the plugin zip files and the extracted plugin folders.
function bpsPro_plugin_hash_maker() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	if ( ! is_array( spl_classes() ) ) {
		fwrite( $handle, "Plugin MD5 File Hash Maker Error: The Standard PHP Library (SPL) is Not available/installed. Unable to create Plugin MD5 hash files.\r\n" );
		fwrite( $handle, "Solution: Contact your web host and ask them to install the Standard PHP Library (SPL) on your server.\r\n" );
		fclose($handle);		
		
		return false;
	}

	$plugin_hash_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
	
	if ( ! file_exists( $plugin_hash_file ) ) {
		fwrite( $handle, "Plugin MD5 File Hash Maker Error: The $plugin_hash_file file does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check the Ownership or folder permissions for the /bps-backup/plugin-hashes/ folder. The /bps-backup/plugin-hashes/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/plugin-hashes/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		
		return false;
	}

	fwrite( $handle, "Plugin MD5 File Hash Maker: Start creating the plugin-hashes.php file.\r\n" );

	$source = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
	$plugin_dir_path_array = array();
	$plugin_folder_name_array = array();

	if ( is_dir($source) ) {
		$iterator = new DirectoryIterator($source);
			
		foreach ( $iterator as $files ) {
			if ( $files->isDir() && ! $files->isDot() ) {

				$plugin_dir_path_array[] = $files->getPathname();
			}
		}
	}

	// If the $plugin_dir_path_array array is empty there are no new plugin folders to process
	if ( ! empty($plugin_dir_path_array) ) {	

		$str1 = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
		$str2 = WP_CONTENT_DIR . '/bps-backup/plugin-hashes\\';
		$str3 = WP_CONTENT_DIR . '\bps-backup\plugin-hashes\\';
			
		$filePath = array();
	
		foreach ( $plugin_dir_path_array as $plugin_dir_path ) {
			
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugin_dir_path), RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ( $objects as $files ) {
				
				if ( $files->isFile() ) {
					
					$filePath[] = str_replace( array( $str1, $str2, $str3 ), "", $files->getPathname() ). '\' => \'' . md5_file($files->getPathname());
				}
			}
		}
		
		$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
		$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
		
		$plugin_name_key_array = array();
	
		foreach ( $filePath as $key => $value ) {
			
			$key_value = preg_replace( '/(\\\\.*|\/.*)/', "", $value);
	
			// These must be below the $key_value variable above.
			if ( preg_match( '/hello-dolly(.*)readme\.txt/', $value ) ) {	
				unset($value);
				$value = ! isset($value) ? '' : $value; // PHP8 weirdness
			}
	
			if ( preg_match( '/hello-dolly(.*)hello\.php/', $value ) ) {	
				$value = preg_replace( '/.*hello\.php/', "hello.php", $value);
			}

			if ( ! empty($value) ) {
				$plugin_name_key_array[$key_value][] = $value;
			}
		}
	}

	// Error Check: If any .php files exist in the plugin-hashes folder that are not the plugin-hashes.php file then the plugin zip file
	// did not extract the plugin files into a plugin folder and instead extracted the files into the root plugin-hashes folder.
	// Could be caused by an incorrect archive or if someone manually copies a plugin zip that does not extract into a plugin folder in the plugin-hashes folder.
	
	// Important Note: The plugin zip file must extract the plugin zip file into a plugin folder: /plugin-name/
	// in order for someone to manually copy premium and custom plugins into the /plugin-hashes/ folder to be iterated and added to the hash array.
	// HALT script execution if any files exist in the /plugin-hashes/ folder that are not the plugin-hashes.php file.
	// Update the MScan option setting to: 3 to get out of a looping situation with status 2.
	// Use MScan Status: 3 and let the iframe js script update the status to 4 in case the estimated scan time is excessive.
	
	$plugin_hash_folder = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
	$plugin_files = preg_grep('~\.(php)$~', scandir($plugin_hash_folder));
	
	foreach ( $plugin_files as $plugin_file ) {
		
		if ( $plugin_file != 'plugin-hashes.php' ) {
			
			fwrite( $handle, "Plugin MD5 File Hash Maker Error: Files exist in the $plugin_hash_folder that should not be there. If you copied plugin files into this folder then delete them. Only Plugin Zip files or extracted Plugin folders should be in this folder.\r\n" );
			
			$MScan_status = get_option('bulletproof_security_options_MScan_status');

			$MScan_status_db = array( 
			'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
			'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
			'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'], 
			'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
			'bps_mscan_status' 						=> '3', 
			'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
			'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
			'bps_mscan_total_website_files' 		=> '', 
			'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
			'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
			'bps_mscan_total_image_files' 			=> '', 
			'bps_mscan_total_all_scannable_files' 	=> 'Error: Files found in the plugin-hashes folder', 
			'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
			'bps_mscan_total_suspect_files' 		=> $MScan_status['bps_mscan_total_suspect_files'], 
			'bps_mscan_suspect_skipped_files' 		=> $MScan_status['bps_mscan_suspect_skipped_files'], 
			'bps_mscan_total_suspect_db' 			=> $MScan_status['bps_mscan_total_suspect_db'], 
			'bps_mscan_total_ignored_files' 		=> $MScan_status['bps_mscan_total_ignored_files'],
			'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
			'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
			);		
				
			foreach( $MScan_status_db as $key => $value ) {
				update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
			}
			
			return false;
		}
	}

	$mscan_plugin_hash_options_db_new = 'bulletproof_security_options_mscan_p_hash_new';

	// First Run
	if ( ! get_option( $mscan_plugin_hash_options_db_new ) ) {

		$mscan_plugin_hash_options = array(
		'bps_mscan_plugin_hash_version_check' 	=> $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'], 
		'bps_mscan_plugin_hash_paths' 			=> $plugin_name_key_array, 
		'bps_mscan_plugin_hash_zip_file'		=> $mscan_plugin_hash['bps_mscan_plugin_hash_zip_file'] 
		);

		foreach( $mscan_plugin_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_plugin_hash', $mscan_plugin_hash_options);
		}

	} else { // All future Runs.
	
		if ( ! empty($plugin_dir_path_array) ) {

			// Don't manually delete this option here for testing - will cause problems.		
			$mscan_plugin_hash_options = array(
			'bps_mscan_plugin_hash_version_check_new' 	=> $mscan_plugin_hash_new['bps_mscan_plugin_hash_version_check_new'], 
			'bps_mscan_plugin_hash_paths_new' 			=> $plugin_name_key_array, 
			'bps_mscan_plugin_hash_zip_file_new'		=> $mscan_plugin_hash_new['bps_mscan_plugin_hash_zip_file_new'] 
			);		
	
			foreach( $mscan_plugin_hash_options as $key => $value ) {
				update_option('bulletproof_security_options_mscan_p_hash_new', $mscan_plugin_hash_options);
			}
		}
	}
	
	$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
	$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
	
	// Note: $value['TextDomain'] is not reliable. Use $key instead.
	$all_plugins = get_plugins();
	
	$active_plugins_array = array();
	$inactive_plugins_array = array();
	$hello_dolly_plugin_array = array();

	foreach ( $all_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
		
			$active_plugins = in_array( $key, apply_filters('active_plugins', get_option('active_plugins')));

			if ( 1 == $active_plugins || is_plugin_active_for_network( $key ) ) {
			
				$pos = strpos($key, '/');
				$dolly_pos = strpos($value['Name'], 'Hello Dolly');				
				
				if ( $pos !== false ) {

					$plugin_name = strstr($key, '/', true);
					$active_plugins_array[] = $plugin_name;
				
				} else {
					
					if ( $dolly_pos !== false ) {
					
						$hello_dolly_plugin_array[] = 'hello-dolly';
					}				
				}
			
			} else {
				
				$pos = strpos($key, '/');
				$dolly_pos = strpos($value['Name'], 'Hello Dolly');
				
				if ( $pos !== false ) {
					
					$plugin_name = strstr($key, '/', true);
					$inactive_plugins_array[] = $plugin_name;	
				
				} else {
					
					if ( $dolly_pos !== false ) {
					
						$hello_dolly_plugin_array[] = 'hello-dolly';
					}
				}
			}
		}
	}

	$installed_plugins_array_merged = array_merge($active_plugins_array, $inactive_plugins_array, $hello_dolly_plugin_array);
	
	$mscan_plugin_hash_new_array_keys = array();
	
	if ( get_option( 'bulletproof_security_options_mscan_p_hash_new' ) ) {

		// Get the new hash array keys that have a value otherwise return an empty array of array keys.
		foreach ( $mscan_plugin_hash_new['bps_mscan_plugin_hash_paths_new'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
			
				if ( ! empty($inner_value) ) {
					$mscan_plugin_hash_new_array_keys[] = $key;
				}
			}
		}
	}

	$plugin_hash_removal_array = array();
	
	foreach ( $mscan_plugin_hash['bps_mscan_plugin_hash_paths'] as $key => $value ) {
		
		// Plugin Deleted/Removed:
		// Remove deleted plugins from the First Run DB option array
		if ( ! in_array( $key, $installed_plugins_array_merged ) ) {
			unset($value);
		}
		
		// Plugin Updated:
		// Remove plugin hashes (values) from the First Run DB option array ONLY if the key exists and the value is not empty in the new plugin hash array.
		// The DB arrays will be merged at a later point. So this removes/unsets any matching keys in the first run array if the new hash array key matches
		// AND the value is not empty.
		// Important Note: The new hash array will ONLY contain the key and hashes for any new plugins and nothing else - no other default keys.
		if ( in_array( $key, $mscan_plugin_hash_new_array_keys ) ) {
			unset($value);
		}
		
		if ( ! empty($value) ) {
			$plugin_hash_removal_array[$key] = $value;
		}
	}

	$mscan_plugin_hash_options = array(
	'bps_mscan_plugin_hash_version_check' 	=> $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'], 
	'bps_mscan_plugin_hash_paths' 			=> $plugin_hash_removal_array, 
	'bps_mscan_plugin_hash_zip_file'		=> $mscan_plugin_hash['bps_mscan_plugin_hash_zip_file'] 
	);

	foreach( $mscan_plugin_hash_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_plugin_hash', $mscan_plugin_hash_options);
	}

	$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
	$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');

	if ( empty($mscan_plugin_hash_new_array_keys) ) {
		
		$result = $mscan_plugin_hash['bps_mscan_plugin_hash_paths'];
		
	} else {
		
		$result = array_merge($mscan_plugin_hash_new['bps_mscan_plugin_hash_paths_new'], $mscan_plugin_hash['bps_mscan_plugin_hash_paths']);
	}

	$mscan_plugin_hash_options = array(
	'bps_mscan_plugin_hash_version_check' 	=> $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'], 
	'bps_mscan_plugin_hash_paths' 			=> $result, 
	'bps_mscan_plugin_hash_zip_file'		=> $mscan_plugin_hash['bps_mscan_plugin_hash_zip_file'] 
	);

	foreach( $mscan_plugin_hash_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_plugin_hash', $mscan_plugin_hash_options);
	}

	$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
	$final_result = $mscan_plugin_hash['bps_mscan_plugin_hash_paths'];

	## IMPORTANT!!! Do NOT sort the 2D array - very buggy.
	/*
	ksort($final_result);
	
	foreach( $final_result as &$value ) {
		ksort($value);
	}
	*/

	$mscan_plugin_hash_options_db = 'bulletproof_security_options_mscan_plugin_hash';

	// Create the plugin hash file on first time scan & on future scans if new plugin hashes exist in the p_hash_new db array
	// This condition is important to limit potentional parse and fatal php errors when parsing the plugin-hashes.php file in the MScan AJAX function.
	if ( ! empty($mscan_plugin_hash_new_array_keys ) || ! get_option( 'bulletproof_security_options_mscan_p_hash_new' ) ) {
	
		$plugin_hashes_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
		
		$handleH = fopen( $plugin_hashes_file, 'wb' );
		fwrite( $handleH, "<?php\n" );
		fwrite( $handleH, "\$plugin_hashes = array(\n" );
		
		foreach ( $final_result as $key => $value ) {
			
			fwrite( $handleH, "## BEGIN " . $key . " ##" . "\n" );
			
			foreach ( $value as $key2 => $value2 ) {
			
				fwrite( $handleH, "'" . $value2 . "', " . "\n" );
			}
		
			fwrite( $handleH, "## END " . $key . " ##" . "\n" );	
		}
	
		fwrite( $handleH, ");\n" );
		fwrite( $handleH, "?>" );	
		fclose( $handleH );
	
		fwrite( $handle, "Plugin MD5 File Hash Maker & Cleanup: plugin-hashes.php file created.\r\n" );
		fwrite( $handle, "Plugin MD5 File Hash Maker & Cleanup: Start /bps-backup/plugin-hashes/ folder cleanup.\r\n" );
		
		// Cleanup
		$plugin_hash_folder = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
		$plugin_hash_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
		
		if ( is_dir($plugin_hash_folder) ) {
			
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugin_hash_folder), RecursiveIteratorIterator::CHILD_FIRST);		
			
			foreach ( $iterator as $file ) {
				
				if ( $file->isFile() ) {
					
					if ( $file->getFilename() != 'plugin-hashes.php' ) {
						@unlink( $file->getRealPath() );
					}
	
				} else {
				
					if ( $file->isDir() )	{
						@rmdir( $file->getRealPath() );
					}
				}
			}
		}
	}
	
	$time_end = microtime( true );
	$hash_maker_time = $time_end - $time_start;

	$hours = (int)($hash_maker_time / 60 / 60);
	$minutes = (int)($hash_maker_time / 60) - $hours * 60;
	$seconds = (int)$hash_maker_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$hash_maker_time_log = 'Plugin MD5 File Hash Maker & Cleanup Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	if ( ! empty($mscan_plugin_hash_new_array_keys ) || ! get_option( 'bulletproof_security_options_mscan_p_hash_new' ) ) {
		fwrite( $handle, "Plugin MD5 File Hash Maker & Cleanup: Plugin Zip files deleted.\r\n" );
		fwrite( $handle, "Plugin MD5 File Hash Maker & Cleanup: Extracted Plugin folders deleted.\r\n" );
	}

	fwrite( $handle, "$hash_maker_time_log\r\n" );
	fclose($handle);

	return true;
}

?>