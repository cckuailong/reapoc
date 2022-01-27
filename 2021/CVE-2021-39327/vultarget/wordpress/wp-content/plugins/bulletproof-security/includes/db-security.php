<?php
// Direct calls to this file are Forbidden when core files are not present 
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/********************************/
// DB Backup Job Processing
/********************************/

/** DB Backup Hourly Cron check for any Scheduled Backup Jobs that need to be run **/
// commented out during development so that no processing will occur
add_action('bpsPro_DBB_check', 'bpsPro_DBB_processing');

function bpsPro_DBB_cron( $schedules ) {
$schedules['hourly'] = array( 'interval' => 3600, 'display' => __('Hourly') );
	return $schedules;
}
add_filter('cron_schedules', 'bpsPro_DBB_cron');

// $clock syncs to the exact current UNIX hour - ie 5:00:00, 6:00:00, 7:00:00
function bpsPro_schedule_DBB_checks() {
$bpsDBBCronCheck = wp_get_schedule('bpsPro_DBB_check');
$DBBoptions = get_option('bulletproof_security_options_db_backup');
$clock = mktime( date( "H", time() ), 0, 0, date( "n", time() ), date( "j", time() ), date( "Y", time() ) );
	
	if ( isset($DBBoptions['bps_db_backup']) && $DBBoptions['bps_db_backup'] == 'On' ) {
	if ( ! wp_next_scheduled('bpsPro_DBB_check') ) {
		wp_schedule_event( $clock, 'hourly', 'bpsPro_DBB_check' );
	}
	}
}
add_action('init', 'bpsPro_schedule_DBB_checks');

// DB Backup Cron Job Processing & delete old Backup Files if that option has been chosen
function bpsPro_DBB_processing() {
global $wpdb;	
	
	$DBBoptions = get_option('bulletproof_security_options_db_backup');	
	
	bpsPro_DBB_delete_old_backup_files();
	
	$DBB_table_name = $wpdb->prefix . "bpspro_db_backup";
	$DBB_Rows = 'Scheduled';
	$DBB_TableRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $DBB_table_name WHERE bps_job_type = %s", $DBB_Rows ) );
	
	$db_backup = $DBBoptions['bps_db_backup_folder'] . '/' . DB_NAME . '.sql';

	foreach ( $DBB_TableRows as $row ) {
			
		if ( time() >= $row->bps_next_job_unix ) {

			$job_name = $row->bps_desc;			
			$job_type = $row->bps_job_type;
			$email_zip = $row->bps_email_zip;
					
			$build_query_1 = "SHOW TABLES FROM `".DB_NAME."` WHERE `Tables_in_".DB_NAME."` LIKE '";
			$build_query_2 = str_replace( ', ', "' OR `Tables_in_".DB_NAME."` LIKE '", $row->bps_table_name );
			$build_query_3 = "'";
			$tables = $wpdb->get_results( $build_query_1.$build_query_2.$build_query_3, ARRAY_A );

			bpsPro_db_backup( $db_backup, $tables, $job_name, $job_type, $email_zip );
			
			if ( $row->bps_frequency == 'Hourly' ) {
				$update_rows = $wpdb->update( $DBB_table_name, array( 'bps_next_job_unix' => time() + 3600 ), array( 'bps_id' => $row->bps_id ) );
			}
			if ( $row->bps_frequency == 'Daily' ) {
				$update_rows = $wpdb->update( $DBB_table_name, array( 'bps_next_job_unix' => time() + 86400 ), array( 'bps_id' => $row->bps_id ) );
			}
			if ( $row->bps_frequency == 'Weekly' ) {
				$update_rows = $wpdb->update( $DBB_table_name, array( 'bps_next_job_unix' => time() + 604800 ), array( 'bps_id' => $row->bps_id ) );
			}	
			if ( $row->bps_frequency == 'Monthly' ) {
				$update_rows = $wpdb->update( $DBB_table_name, array( 'bps_next_job_unix' => time() + 2592000 ), array( 'bps_id' => $row->bps_id ) );
			}		
		}
	}
}

// Delete Old Backup files and log the deleted Backup file name in the DB Backup Log
function bpsPro_DBB_delete_old_backup_files() {
$DBBoptions = get_option('bulletproof_security_options_db_backup');
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);	
$bpsDBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';

	if ( ! $DBBoptions['bps_db_backup_delete'] || $DBBoptions['bps_db_backup_delete'] == 'Never' ) {
		return;
	}

	$no_zips = '';
	$handle = fopen( $bpsDBBLog, 'a' );

	if ( $handle )

		$source = $DBBoptions['bps_db_backup_folder'];
	
	if ( is_dir($source) ) {
		
		$iterator = new DirectoryIterator($source);
		
		foreach ( $iterator as $file ) {
			
			if ( $file->isFile() && $file->getFilename() != '.htaccess' ) {

				$last_modified = filemtime( $source.DIRECTORY_SEPARATOR.$file->getFilename() ); 
				
				if ( $DBBoptions['bps_db_backup_delete'] == '1' && time() - ( $last_modified ) >= 86400 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '5' && time() - ( $last_modified ) >= 432000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '10' && time() - ( $last_modified ) >= 864000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '15' && time() - ( $last_modified ) >= 1296000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '30' && time() - ( $last_modified ) >= 2592000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '60' && time() - ( $last_modified ) >= 5184000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '90' && time() - ( $last_modified ) >= 7776000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				} elseif ( $DBBoptions['bps_db_backup_delete'] == '180' && time() - ( $last_modified ) >= 15552000 ) {
					if ( unlink( $source.DIRECTORY_SEPARATOR.$file->getFilename() ) ) {
						fwrite( $handle, "\r\n[Old Zip Backup File(s) Automatic Deletion: ". $timestamp . "]\n" );
						fwrite( $handle, "Deleted Zip Backup File Name: ". $file->getFilename() . "\n" );
					}
				}
			}
		}
	}

	fclose( $handle );

	$DBBLog_Options = array( 'bps_dbb_log_date_mod' => bpsPro_DBB_LogLastMod_wp_secs() );
	
	foreach ( $DBBLog_Options as $key => $value ) {
		update_option('bulletproof_security_options_DBB_log', $DBBLog_Options);
	}
}

// Get the Current / Last Modifed time of the DB Backup Log File - Seconds
function bpsPro_DBB_LogLastMod_wp_secs() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
$gmt_offset = get_option( 'gmt_offset' ) * 3600;

if ( file_exists($filename) ) {
	$last_modified = date( "F d Y H:i:s", filemtime($filename) + $gmt_offset );
	return $last_modified;
	}
}

// DB Backup: Processes Manual and Scheduled Jobs
// Notes: fwrite is faster in benchmark tests than file_put_contents for successive writes
function bpsPro_db_backup( $db_backup, $tables, $job_name, $job_type, $email_zip ) {
global $wpdb;

$time_start = microtime( true );

	if ( $email_zip == 'Delete' ) {
		$email_zip_log = 'Yes & Delete';
	} else {
		$email_zip_log = $email_zip;
	}
	if ( $email_zip == 'EmailOnly' ) {
		$email_zip_log = 'Send Email Only';
	} else {
		$email_zip_log = $email_zip;
	}

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);

	$handle = fopen( $db_backup, 'wb' );

	if ( $handle )

	fwrite( $handle, "-- -------------------------------------------\n" );
	fwrite( $handle, "-- BulletProof Security DB Backup\n" );
	fwrite( $handle, "-- Support: https://forum.ait-pro.com/\n" );
	fwrite( $handle, "-- Backup Job Name: ". $job_name . "\n" );
	fwrite( $handle, "-- DB Backup Job Type: ". $job_type . "\n" );
	fwrite( $handle, "-- Email DB Backup: ". $email_zip_log . "\n" );
	fwrite( $handle, "-- DB Backup Time: ". $timestamp . "\n" );
	fwrite( $handle, "-- DB Name: ". DB_NAME . "\n" );		
	fwrite( $handle, "-- DB Table Prefix: ". $wpdb->base_prefix . "\n" );
	fwrite( $handle, "-- Website URL: " . get_bloginfo( 'url' ) . "\n" );
	fwrite( $handle, "-- WP ABSPATH: ". ABSPATH . "\n" );
	fwrite( $handle, "-- -------------------------------------------\n\n" );

	fwrite( $handle, "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n" );
	fwrite( $handle, "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n" );
	fwrite( $handle, "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n" );
	fwrite( $handle, "/*!40101 SET NAMES " . DB_CHARSET . " */;\n" );
	fwrite( $handle, "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n" );
	fwrite( $handle, "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n" );
	fwrite( $handle, "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n" );
	fwrite( $handle, "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n" );

	if ( !empty( $tables ) )

		foreach ( $tables as $table_array ) {
		
			$table = current( $table_array );
			$create = $wpdb->get_var( "SHOW CREATE TABLE " . $table, 1 );
			$myisam = strpos( $create, 'MyISAM' );

			fwrite( $handle, "--\n-- BEGIN Table " . $table . "\n--\n\nDROP TABLE IF EXISTS `" . $table . "`;\n/*!40101 SET @saved_cs_client     = @@character_set_client */;\n/*!40101 SET character_set_client = '" . DB_CHARSET . "' */;\n" . $create . ";\n/*!40101 SET character_set_client = @saved_cs_client */;\n\n" );

			$data = $wpdb->get_results("SELECT * FROM `" . $table . "` LIMIT 1000", ARRAY_A );
		
		if ( !empty( $data ) ) {
			
			fwrite( $handle, "LOCK TABLES `" . $table . "` WRITE;\n" );
			
			if ( false !== $myisam )
				
				fwrite( $handle, "/*!40000 ALTER TABLE `".$table."` DISABLE KEYS */;\n\n" );

			$offset = 0;
			
			do {
				foreach ( $data as $entry ) {
					foreach ( $entry as $key => $value ) {
						if ( NULL === $value )
							$entry[$key] = "NULL";
						elseif ( "" === $value || false === $value )
							$entry[$key] = "''";
						elseif ( is_numeric( $value ) && preg_match( '/[0-9A-Za-z]{17}/', $value ) ) // special condition for PayPal numeric Transaction Codes
							$entry[$key] = "'" . esc_sql($value) . "'";
						elseif ( ! is_numeric( $value ) )
							if ( method_exists( $wpdb, 'remove_placeholder_escape' ) ) { // since WP 4.8.3
								$entry[$key] = "'" . $wpdb->remove_placeholder_escape( esc_sql($value) ) . "'";
							} else {
								$entry[$key] = "'" . esc_sql($value) . "'";
							}
					}
					fwrite( $handle, "INSERT INTO `" . $table . "` ( " . implode( ", ", array_keys( $entry ) ) . " )\n VALUES ( " . implode( ", ", $entry ) . " );\n" );
				}

				$offset += 1000;
				$data = $wpdb->get_results("SELECT * FROM `" . $table . "` LIMIT " . $offset . ",1000", ARRAY_A );
			
			} while ( !empty( $data ) );

			fwrite( $handle, "\n--\n-- END Table " . $table . "\n--\n" );
			
		if ( false !== $myisam )
			fwrite( $handle, "\n/*!40000 ALTER TABLE `" . $table . "` ENABLE KEYS */;" );
			fwrite( $handle, "\nUNLOCK TABLES;\n\n" );
		}
	}

	fwrite( $handle, "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n" );
	fwrite( $handle, "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n" );
	fwrite( $handle, "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n" );
	fwrite( $handle, "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n" );
	fwrite( $handle, "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n" );
	fwrite( $handle, "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n" );
	fwrite( $handle, "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n" );

	fclose( $handle );
	
	if ( file_exists($db_backup) ) {
	
	$DBBoptions = get_option('bulletproof_security_options_db_backup'); 

	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

	$zip = new ZipArchive();
	$filename = $DBBoptions['bps_db_backup_folder'] . '/' . date( 'Y-m-d-\t\i\m\e-g-i-s-a', $timeNow + $gmt_offset ) . '.zip';
	
	if ( $zip->open( $filename, ZIPARCHIVE::CREATE )!==TRUE ) {
    	exit("Error: Cannot Open $filename\n");
	}

	$zip->addFile( $db_backup, DB_NAME . ".sql" );
	$zip->close();
	
	@unlink($db_backup);
	
	} else {

	// Use PCLZip
	define( 'PCLZIP_TEMPORARY_DIR', $DBBoptions['bps_db_backup_folder'] . '/' );
	require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
	if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding( 'ISO-8859-1' );
	}
 		$filename = $DBBoptions['bps_db_backup_folder'] . '/' . date( 'Y-m-d-\t\i\m\e-g-i-s-a', $timeNow + $gmt_offset ) . '.zip';
  		$archive = new PclZip( $filename );
		$sql_filename = str_replace( $DBBoptions['bps_db_backup_folder'] . '/', "", $db_backup );
		$db_backup = str_replace( array( '\\', '//'), "/", $db_backup );
		$db_backup_folder = str_replace( DB_NAME . '.sql', "", $db_backup );
		$v_list = $archive->create( $db_backup_folder . $sql_filename, PCLZIP_OPT_REMOVE_PATH, $db_backup_folder );
		
		@unlink($db_backup);
	}
	}
	
	$time_end = microtime( true );
	
	$backup_time = $time_end - $time_start;
	$backup_time_log = 'Backup Job Completion Time: '. round( $backup_time, 2 ) . ' Seconds';
	$backup_time_display = '<strong>Backup Job Completion Time: </strong>'. round( $backup_time, 2 ) . ' Seconds';
	$bpsDBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
	
	echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
	echo bpsPro_memory_resource_usage();
	echo $backup_time_display;
	echo '</p></div>';

	$log_contents = "\r\n" . '[Backup Job Logged: ' . $timestamp . ']' . "\r\n" . 'Backup Job Name: ' . $job_name .  "\r\n" . 'Backup Job Type: ' . $job_type .  "\r\n" . 'Email DB Backup: ' . $email_zip_log . "\r\n" . $backup_time_log . "\r\n" . bpsPro_memory_resource_usage_logging() . "\r\n" . 'Zip Backup File Name: ' . $filename . "\r\n";
					
	if ( is_writable( $bpsDBBLog ) ) {
	if ( !$handle = fopen( $bpsDBBLog, 'a' ) ) {
       	exit;
    }
    if ( fwrite( $handle, $log_contents ) === FALSE ) {
       	exit;
    }
    fclose($handle);
	}	
		
	$DBBLog_Options = array( 'bps_dbb_log_date_mod' => bpsPro_DBB_LogLastMod_wp_secs() );
	
	foreach( $DBBLog_Options as $key => $value ) {
		update_option('bulletproof_security_options_DBB_log', $DBBLog_Options);
	}

	$DBB_Backup_Options = array( 
	'bps_db_backup' => $DBBoptions['bps_db_backup'], 
	'bps_db_backup_description' => $DBBoptions['bps_db_backup_description'], 
	'bps_db_backup_folder' => $DBBoptions['bps_db_backup_folder'], 
	'bps_db_backup_download_link' => $DBBoptions['bps_db_backup_download_link'], 
	'bps_db_backup_job_type' => $DBBoptions['bps_db_backup_job_type'], 
	'bps_db_backup_frequency' => $DBBoptions['bps_db_backup_frequency'], 		 
	'bps_db_backup_start_time_hour' => $DBBoptions['bps_db_backup_start_time_hour'], 
	'bps_db_backup_start_time_weekday' => $DBBoptions['bps_db_backup_start_time_weekday'], 
	'bps_db_backup_start_time_month_date' => $DBBoptions['bps_db_backup_start_time_month_date'], 
	'bps_db_backup_email_zip' => $DBBoptions['bps_db_backup_email_zip'], 
	'bps_db_backup_delete' => $DBBoptions['bps_db_backup_delete'], 
	'bps_db_backup_status_display' => $timestamp
	);
	
		foreach( $DBB_Backup_Options as $key => $value ) {
			update_option('bulletproof_security_options_db_backup', $DBB_Backup_Options);
		}
	
	// Send Email last: attaching a large zip file may fail
	if ( $job_type != 'Manual' || $email_zip != 'No' ) {

		$Email_options = get_option('bulletproof_security_options_email');
		$bps_email_to = $Email_options['bps_send_email_to'];
		$bps_email_from = $Email_options['bps_send_email_from'];
		$bps_email_cc = $Email_options['bps_send_email_cc'];
		$bps_email_bcc = $Email_options['bps_send_email_bcc'];
		$path = '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fdb-backup-security%2Fdb-backup-security.php';
		$justUrl = get_site_url(null, $path, null);
	
	if ( $email_zip == 'EmailOnly' ) {

		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
		$subject = " BPS DB Backup Completed - $timestamp ";
		$message = '<p><font color="blue"><strong>DB Backup Has Completed For:</strong></font></p>';
		$message .= '<p>Website: '.$justUrl.'</p>';
	
	$mailed = wp_mail( $bps_email_to, $subject, $message, $headers );	
	}

	if ( $email_zip == 'Delete' || $email_zip == 'Yes' ) {
		
		$attachments = array( $filename );
		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
		$subject = " BPS DB Backup Completed - $timestamp ";
		$message = '<p><font color="blue"><strong>DB Backup File is Attached For:</strong></font></p>';
		$message .= '<p>Website: '.$justUrl.'</p>';
	
	$mailed = wp_mail( $bps_email_to, $subject, $message, $headers, $attachments );	
	}

		if ( @$mailed && $email_zip == 'Delete' ) {
			unlink($filename);
		}
	}
} 

?>