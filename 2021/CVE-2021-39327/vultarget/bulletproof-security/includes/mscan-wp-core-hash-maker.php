<?php
// Download the WordPress zip file version based on the current WP version installed.
// Ensure that the WP zip file is not downloaded repeatedly due to an error, issue or problem.
## 13.8: Removed cURL GET code and replaced with simple fopen code. It is unnecessary to use the WP HTTP API for something as simple as a zip file download.
## 13.9: changed fopen code to download_url() function due to problems with allow_url_fopen being turned off.
function bpsPro_wp_zip_download($mstime) {
global $wp_version;
	
	$time_start = microtime( true );
	
	set_time_limit($mstime);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	$wp_hashes_dir = WP_CONTENT_DIR . '/bps-backup/wp-hashes';
	
	if ( ! is_dir( $wp_hashes_dir ) ) {
		
		fwrite( $handle, "WP Zip File Download Error: The $wp_hashes_dir folder does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check that the Ownership or folder permissions for the /bps-backup/ folder. The /bps-backup/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		
		return false;
	}

	$wp_zip_file = 'wordpress-'. $wp_version . '.zip';
	$local_zip_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/' . $wp_zip_file;
	
	if ( file_exists($local_zip_file) ) {
		fwrite( $handle, "WP Zip File Download: The $wp_zip_file already exists and was not downloaded again.\r\n" );
		fclose($handle);
		
		return true;
	}
	
	$wp_hashes_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';
	
	if ( file_exists($wp_hashes_file) ) {
		$check_string = file_get_contents($wp_hashes_file);
		
		if ( strpos( $check_string, "WordPress $wp_version Hashes" ) ) {
			fwrite( $handle, "WP Zip File Download: The wp-hashes.php file already exists for WordPress $wp_version. The $wp_zip_file was not downloaded again.\r\n" );
			fclose($handle);			
			
			return true;			
		}
	}

	fwrite( $handle, "WP Zip File Download: Start $wp_zip_file zip file download.\r\n" );

	$url = 'https://wordpress.org/latest.zip';
	$tmp_file = download_url( $url, $timeout = 300 );

	if ( ! copy( $tmp_file, $local_zip_file )  ) {
		fwrite( $handle, "WP Zip File Download Error: Unable to download the WordPress zip file from $url\r\n" );
		fwrite( $handle, "Manual Solution: You will need to manually download the WordPress zip file to your computer, unzip it and then use FTP and upload the unzipped /wordpress/ folder to this BPS folder: $wp_hashes_dir\r\n" );
	}
	
	unlink( $tmp_file );

	$time_end = microtime( true );
	$download_time = $time_end - $time_start;

	$hours = (int)($download_time / 60 / 60);
	$minutes = (int)($download_time / 60) - $hours * 60;
	$seconds = (int)$download_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$download_time_log = 'WP Zip File Download Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	fwrite( $handle, "$download_time_log\r\n" );
	fclose($handle);
	
	return true;
}

// Extract the downloaded WordPress zip file.
// The extracted WordPress folder name is: /wordpress/
function bpsPro_wp_zip_extractor() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';

	$handle = fopen( $mscan_log, 'a' );
	
	$wp_hashes_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';
	
	if ( file_exists($wp_hashes_file) ) {
		$check_string = file_get_contents($wp_hashes_file);
		
		if ( strpos( $check_string, "WordPress $wp_version Hashes" ) ) {
			fwrite( $handle, "WP Zip File Extraction: The wp-hashes.php file already exists for WordPress $wp_version. The wordpress-$wp_version.zip file does not need to be extracted.\r\n" );
			fclose($handle);
			
			return true;			
		}
	}

	$wp_folder = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wordpress';
	$wp_hashes_dir = WP_CONTENT_DIR . '/bps-backup/wp-hashes';
	$wp_zip_file = 'wordpress-'. $wp_version . '.zip';
	$local_zip_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/' . $wp_zip_file;

	if ( class_exists('ZipArchive') ) {	

		fwrite( $handle, "WP Zip File Extraction: Start ZipArchive zip file extraction.\r\n" );
		
		$WPZip = new ZipArchive;
	
		if ( $WPZip->open( $local_zip_file ) === true ) {
 
			$WPZip->extractTo( WP_CONTENT_DIR . '/bps-backup/wp-hashes/' );
			$WPZip->close();
		
			$time_end = microtime( true );
			$zip_extract_time = $time_end - $time_start;

			$hours = (int)($zip_extract_time / 60 / 60);
			$minutes = (int)($zip_extract_time / 60) - $hours * 60;
			$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
			$hours_format = $hours == 0 ? "00" : $hours;
			$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
			$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

			$zip_extract_time_log = 'WP Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

			fwrite( $handle, "$zip_extract_time_log\r\n" );
			fclose($handle);			
			
			return true;
		
		} else {
			
			if ( ! is_dir($wp_folder) ) {
			
				fwrite( $handle, "WP Zip File Extraction ZipArchive Error: Unable to unzip the WordPress zip file: $local_zip_file.\r\n" );
				fwrite( $handle, "Manual Solution: You will need to manually download the WordPress zip file to your computer, unzip it and then use FTP and upload the unzipped /wordpress/ folder to this BPS folder: $wp_hashes_dir.\r\n" );
				fclose($handle);
				
				return false;
			}
		}
	
	} else { 
		
		fwrite( $handle, "WP Zip File Extraction: Start PclZip zip file extraction.\r\n" );

		define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/wp-hashes/' );
		
		require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
	
		if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
			$previous_encoding = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}	

		$archive = new PclZip( $local_zip_file );
		
		if ( $archive->extract( PCLZIP_OPT_PATH, WP_CONTENT_DIR . '/bps-backup/wp-hashes', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/wp-hashes' ) ) {
			
			$time_end = microtime( true );
			$zip_extract_time = $time_end - $time_start;

			$hours = (int)($zip_extract_time / 60 / 60);
			$minutes = (int)($zip_extract_time / 60) - $hours * 60;
			$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
			$hours_format = $hours == 0 ? "00" : $hours;
			$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
			$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

			$zip_extract_time_log = 'WP Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

			fwrite( $handle, "$zip_extract_time_log\r\n" );
			fclose($handle);			
			
			return true;
		
		} else {
			
			if ( ! is_dir($wp_folder) ) {
			
				fwrite( $handle, "WP Zip File Extraction PclZip Error: Unable to unzip the WordPress zip file: $local_zip_file.\r\n" );
				fwrite( $handle, "Manual Solution: You will need to manually download the WordPress zip file to your computer, unzip it and then use FTP and upload the unzipped /wordpress/ folder to this BPS folder: $wp_hashes_dir.\r\n" );
				fclose($handle);
				
				return false;
			}		
		}
	}
}

// Create the wp-hashes.php file array, which contains all MD5 file hashes for all current WP Core files.
// Cleanup: Deletes the wp zip file and the extracted /wordpress/ folder.
function bpsPro_wp_hash_maker() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	if ( ! is_array( spl_classes() ) ) {
		fwrite( $handle, "WP MD5 File Hash Maker Error: The Standard PHP Library (SPL) is Not available/installed. Unable to create WP MD5 file hashes.\r\n" );
		fwrite( $handle, "Solution: Contact your web host and ask them to install the Standard PHP Library (SPL) on your server.\r\n" );
		fclose($handle);		
		
		return false;
	}

	$wp_hashes_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';
	
	if ( ! file_exists( $wp_hashes_file ) ) {
		fwrite( $handle, "WP MD5 File Hash Maker Error: The $wp_hashes_file file does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check the Ownership or folder permissions for the /bps-backup/wp-hashes/ folder. The /bps-backup/wp-hashes/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/wp-hashes/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		
		return false;
	}

	if ( file_exists($wp_hashes_file) ) {
		$check_string = file_get_contents($wp_hashes_file);
		
		if ( strpos( $check_string, "WordPress $wp_version Hashes" ) ) {
			fwrite( $handle, "WP MD5 File Hash Maker: The wp-hashes.php file already exists for WordPress $wp_version. The wp-hashes.php file was not created again.\r\n" );
			fclose($handle);
			
			return true;			
		}
	}

	$str1 = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wordpress/';
	$str2 = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wordpress\\';
	$str3 = WP_CONTENT_DIR . '\bps-backup\wp-hashes\wordpress\\';
	
	$path = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wordpress';

	if ( ! is_dir($path) ) {
		
		fwrite( $handle, "WP MD5 File Hash Maker Error: The $path folder does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check the Ownership or folder permissions for the /bps-backup/wp-hashes/ folder. The /bps-backup/wp-hashes/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/wp-hashes/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		
		return false;
	}

	fwrite( $handle, "WP MD5 File Hash Maker & Cleanup: Start creating the wp-hashes.php file.\r\n" );

	$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
	$filePath = array();
	
	foreach ( $objects as $files ) {
		if ( $files->isFile() ) {
			$filePath[] = str_replace( array( $str1, $str2, $str3 ), "", $files->getPathname() ). '\' => \'' . md5_file($files->getPathname());
		}
	}
	
	$handleH = fopen( $wp_hashes_file, 'wb' );
	fwrite( $handleH, "<?php\n" );
	fwrite( $handleH, "// WordPress $wp_version Hashes\n" );
	fwrite( $handleH, "\$wp_hashes = array(\n" );
	
	foreach ( $filePath as $key => $value ) {
		fwrite( $handleH, "'" . $value . "', " . "\n" );
	}

	fwrite( $handleH, ");\n" );
	fwrite( $handleH, "?>" );	
	fclose( $handleH );

	fwrite( $handle, "WP MD5 File Hash Maker & Cleanup: wp-hashes.php file created.\r\n" );
	fwrite( $handle, "WP MD5 File Hash Maker & Cleanup: Start /bps-backup/wp-hashes/ folder cleanup.\r\n" );
	
	// Cleanup
	$wp_zip_file = 'wordpress-'. $wp_version . '.zip';
	$local_zip_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/' . $wp_zip_file;
	
	if ( is_dir($path) ) {
		
		if ( file_exists($local_zip_file) ) {
			unlink($local_zip_file);
		}
	
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach ( $iterator as $file ) {
			
			if ( $file->isDir() ) {
				@rmdir( $file->getRealPath() );

			} else {			
		
				if ( $file->isFile() ) {
					unlink( $file->getRealPath() );
				}
			}
		}
		rmdir($path);	
	}	

	$time_end = microtime( true );
	$hash_maker_time = $time_end - $time_start;

	$hours = (int)($hash_maker_time / 60 / 60);
	$minutes = (int)($hash_maker_time / 60) - $hours * 60;
	$seconds = (int)$hash_maker_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$hash_maker_time_log = 'WP MD5 File Hash Maker & Cleanup Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	fwrite( $handle, "WP MD5 File Hash Maker & Cleanup: WP $wp_zip_file file deleted.\r\n" );
	fwrite( $handle, "WP MD5 File Hash Maker & Cleanup: Extracted /bps-backup/wp-hashes/wordpress/ folder deleted.\r\n" );
	fwrite( $handle, "$hash_maker_time_log\r\n" );
	fclose($handle);

	return true;
}

?>