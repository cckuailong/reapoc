<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/***********************************************/
// BPS Free - Zip, Email, Delete Log File Cron //
/***********************************************/
// 262144 bytes = 256KB = .25MB
// 524288 bytes = 512KB = .5MB
// 1048576 bytes = 1024KB = 1MB
// 2097152 bytes = 2048KB = 2MB
// FailSafe - if log file is larger than 2MB zip, email and delete or just delete

add_action('bpsPro_email_log_files', 'bps_Log_File_Processing');

function bpsPro_schedule_Email_Log_Files() {
	if ( ! wp_next_scheduled( 'bpsPro_email_log_files' ) ) {
		wp_schedule_event(time(), 'hourly', 'bpsPro_email_log_files');
	}
}
add_action('init', 'bpsPro_schedule_Email_Log_Files');

function bpsPro_add_hourly_email_log_cron( $schedules ) {
	$schedules['hourly'] = array('interval' => 3600, 'display' => __('Once Hourly'));
	return $schedules;
}
add_filter('cron_schedules', 'bpsPro_add_hourly_email_log_cron');

function bps_Log_File_Processing() {
$options = get_option('bulletproof_security_options_email');
$SecurityLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
$SecurityLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/http_error_log.txt';
$DBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
$DBBLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/db_backup_log.txt';
$MScanLog = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
$MScanLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/mscan_log.txt';

switch ( isset($options['bps_security_log_size']) && $options['bps_security_log_size'] ) {
    case "256KB":
		if ( file_exists($SecurityLog) && filesize($SecurityLog) >= 262144 && filesize($SecurityLog) < 524288 || file_exists($SecurityLog) && filesize($SecurityLog) > 2097152) {
		if ( $options['bps_security_log_emailL'] == 'email') {
			if ( bps_Zip_Security_Log_File()==TRUE ) {
				bps_Email_Security_Log_File();
			}
		} elseif ( $options['bps_security_log_emailL'] == 'delete') {
			copy($SecurityLogMaster, $SecurityLog);	
		}
		break;
		}
    case "500KB":
		if ( file_exists($SecurityLog) && filesize($SecurityLog) >= 524288 && filesize($SecurityLog) < 1048576 || file_exists($SecurityLog) && filesize($SecurityLog) > 2097152) {
		if ( $options['bps_security_log_emailL'] == 'email') {
			if ( bps_Zip_Security_Log_File()==TRUE ) {
				bps_Email_Security_Log_File();
			}
		} elseif ( $options['bps_security_log_emailL'] == 'delete') {
			copy($SecurityLogMaster, $SecurityLog);	
		}		
		break;
		}
    case "1MB":
		if ( file_exists($SecurityLog) && filesize($SecurityLog) >= 1048576 && filesize($SecurityLog) < 2097152 || file_exists($SecurityLog) && filesize($SecurityLog) > 2097152) {
		if ( $options['bps_security_log_emailL'] == 'email') {
			if ( bps_Zip_Security_Log_File()==TRUE ) {
				bps_Email_Security_Log_File();
			}
		} elseif ( $options['bps_security_log_emailL'] == 'delete') {
			copy($SecurityLogMaster, $SecurityLog);	
		}		
		break;
		}
	}

switch ( isset($options['bps_dbb_log_size']) && $options['bps_dbb_log_size'] ) {
    case "256KB":
		if ( file_exists($DBBLog) && filesize($DBBLog) >= 262144 && filesize($DBBLog) < 524288 || file_exists($DBBLog) && filesize($DBBLog) > 2097152) {
		if ( $options['bps_dbb_log_email'] == 'email') {
			if ( bps_Zip_DBB_Log_File()==TRUE ) {
				bps_Email_DBB_Log_File();
			}
		} elseif ( $options['bps_dbb_log_email'] == 'delete') {
			copy($DBBLogMaster, $DBBLog);	
		}
		break;
		}
    case "500KB":
		if ( file_exists($DBBLog) && filesize($DBBLog) >= 524288 && filesize($DBBLog) < 1048576 || file_exists($DBBLog) && filesize($DBBLog) > 2097152) {
		if ( $options['bps_dbb_log_email'] == 'email') {
			if ( bps_Zip_DBB_Log_File()==TRUE ) {
				bps_Email_DBB_Log_File();
			}
		} elseif ( $options['bps_dbb_log_email'] == 'delete') {
			copy($DBBLogMaster, $DBBLog);	
		}		
		break;
		}
    case "1MB":
		if ( file_exists($DBBLog) && filesize($DBBLog) >= 1048576 && filesize($DBBLog) < 2097152 || file_exists($DBBLog) && filesize($DBBLog) > 2097152) {
		if ( $options['bps_dbb_log_email'] == 'email') {
			if ( bps_Zip_DBB_Log_File()==TRUE ) {
				bps_Email_DBB_Log_File();
			}
		} elseif ( $options['bps_dbb_log_email'] == 'delete') {
			copy($DBBLogMaster, $DBBLog);	
		}		
		break;
		}
	}
switch ( isset($options['bps_mscan_log_size']) && $options['bps_mscan_log_size'] ) {
    case "256KB":
		if ( file_exists($MScanLog) && filesize($MScanLog) >= 262144 && filesize($MScanLog) < 524288 || file_exists($MScanLog) && filesize($MScanLog) > 2097152) {
		if ( $options['bps_mscan_log_email'] == 'email') {
			if ( bps_Zip_MScan_Log_File() == true ) {
				bps_Email_MScan_Log_File();
			}
		} elseif ( $options['bps_arq_log_email'] == 'delete') {
			copy($MScanLogMaster, $MScanLog);	
		}
		break;
		}
    case "500KB":
		if ( file_exists($MScanLog) && filesize($MScanLog) >= 524288 && filesize($MScanLog) < 1048576 || file_exists($MScanLog) && filesize($MScanLog) > 2097152) {
		if ( $options['bps_mscan_log_email'] == 'email') {
			if ( bps_Zip_MScan_Log_File() == true ) {
				bps_Email_MScan_Log_File();
			}
		} elseif ( $options['bps_arq_log_email'] == 'delete') {
			copy($MScanLogMaster, $MScanLog);	
		}		
		break;
		}
    case "1MB":
		if ( file_exists($MScanLog) && filesize($MScanLog) >= 1048576 && filesize($MScanLog) < 2097152 || file_exists($MScanLog) && filesize($MScanLog) > 2097152) {
		if ( $options['bps_mscan_log_email'] == 'email') {
			if ( bps_Zip_MScan_Log_File() == true ) {
				bps_Email_MScan_Log_File();
			}
		} elseif ( $options['bps_mscan_log_email'] == 'delete') {
			copy($MScanLogMaster, $MScanLog);	
		}		
		break;
		}
}
}

// EMAIL NOTES: You cannot fake a zip file by renaming a file extension 
// The zip file must be a real zip archive or it will not be successfully attached to an email.
// A plain txt file cannot be attached to an email.
// Email Security Log File
// .53.5: Gets the current sec-log-master.txt Log entries and adds it to the message body.
function bps_Email_Security_Log_File() {
$options = get_option('bulletproof_security_options_email');
$bps_email_to = $options['bps_send_email_to'];
$bps_email_from = $options['bps_send_email_from'];
$bps_email_cc = $options['bps_send_email_cc'];
$bps_email_bcc = $options['bps_send_email_bcc'];
$path = '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fsecurity-log%2Fsecurity-log.php';
$justUrl = get_site_url(null, $path, null);
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
$SecurityLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
$SecurityLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/http_error_log.txt';
$SecurityLogZip = WP_CONTENT_DIR . '/bps-backup/logs/security-log.zip';
$SecLogMasterTXT = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/sec-log-master.txt';
$SecLogMasterTXT_contents = @file_get_contents($SecLogMasterTXT);
$pattern = '/Total/';
$SecLogEntries = preg_replace( $pattern, "<br />Total", $SecLogMasterTXT_contents );	

	$attachments = array( $SecurityLogZip );
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
	$subject = " BPS Security Log - $timestamp ";
	$message = '<p><font color="blue"><strong>Security Log File For:</strong></font></p>';
	$message .= '<p>Site: '.$justUrl.'</p>'; 
	$message .= '<p><font color="blue"><strong>Total # of Security Log Entries by Type:</strong></font>'. $SecLogEntries .'</p>';
		
	$mailed = wp_mail($bps_email_to, $subject, $message, $headers, $attachments);

	if ( $mailed && file_exists($SecurityLogZip) ) {
		unlink($SecurityLogZip);
		copy($SecurityLogMaster, $SecurityLog);
	}
}

// Email DB Backup log file
function bps_Email_DBB_Log_File() {
$options = get_option('bulletproof_security_options_email');
$bps_email_to = $options['bps_send_email_to'];
$bps_email_from = $options['bps_send_email_from'];
$bps_email_cc = $options['bps_send_email_cc'];
$bps_email_bcc = $options['bps_send_email_bcc'];
$path = '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fdb-backup-security%2Fdb-backup-security.php';
$justUrl = get_site_url(null, $path, null);
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
$DBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
$DBBLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/db_backup_log.txt';
$DBBLogZip = WP_CONTENT_DIR . '/bps-backup/logs/db-backup-log.zip';
	
	$attachments = array( $DBBLogZip );
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
	$subject = " BPS DB Backup Log - $timestamp ";
	$message = '<p>This is a regular scheduled automatic log file zip email and is NOT an alert.</p>';	
	$message .= '<p><font color="blue"><strong>DB Backup Log File is Attached For:</strong></font></p>';
	$message .= '<p>Site: '.$justUrl.'</p>'; 
		
	$mailed = wp_mail($bps_email_to, $subject, $message, $headers, $attachments);

	if ( $mailed && file_exists($DBBLogZip) ) {
		unlink($DBBLogZip);
	
	if ( copy( $DBBLogMaster, $DBBLog ) ) {
		$DBBLogLastModifiedTime = get_option('bulletproof_security_options_DBB_log');
		$time = strtotime( $DBBLogLastModifiedTime['bps_dbb_log_date_mod'] );
		touch( $DBBLog, $time );	
	}
	}
}

// Email MScan Log File
function bps_Email_MScan_Log_File() {
$options = get_option('bulletproof_security_options_email');
$bps_email_to = $options['bps_send_email_to'];
$bps_email_from = $options['bps_send_email_from'];
$bps_email_cc = $options['bps_send_email_cc'];
$bps_email_bcc = $options['bps_send_email_bcc'];
$path = '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fmscan%2Fmscan.php';
$justUrl = get_site_url(null, $path, null);
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
$MScanLog = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
$MScanLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/mscan_log.txt';
$MScanLogZip = WP_CONTENT_DIR . '/bps-backup/logs/mscan-log.zip';

	$attachments = array( $MScanLogZip );
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
	$subject = " BPS Pro MScan Log - $timestamp ";
	$message = '<p><font color="blue"><strong>MScan Log File For:</strong></font></p>';
	$message .= '<p>Site: '.$justUrl.'</p>'; 
		
	$mailed = wp_mail($bps_email_to, $subject, $message, $headers, $attachments);

	if ( $mailed && file_exists($MScanLogZip) ) {
		unlink($MScanLogZip); 

	if ( copy($MScanLogMaster, $MScanLog) ) {
		$MScanLogoptions = get_option('bulletproof_security_options_MScan_log');
		$last_modified_time_db = $MScanLogoptions['bps_mscan_log_date_mod'];
		$time = strtotime($last_modified_time_db);
		touch($MScanLog, $time);	
	}	
	}
}

// Counts the Total # of Security Log Entries by Type & adds the info to the top of the Security Log text file before it is zipped and emailed.
function bpsPro_SecLog_Entry_Counter_add_to_zip() {
	
	$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	$SecLogMasterTXT = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/sec-log-master.txt';

	if ( file_exists($bpsProLog) ) {		

		$check_string = @file_get_contents($bpsProLog);

		// Only creates Total Log entry listings for Log Entry types that match.
		// Leave all 27 BPS Pro Log Entry Types in case I add some more Log Entry Types in BPS free in the future. BPS free only has 11 total Log Entry Types.
		preg_match_all( '/400 POST Bad Request|400 GET Bad Request|403 GET Request|403 POST Request|404 GET Not Found Request|404 POST Not Found Request|405 HEAD Request|410 Gone POST Request|410 Gone GET Request|Idle Session Logout|Maintenance Mode - Visitor Logged|Login Form - POST Request Logged|Login Form - GET, HEAD, OTHER Request Logged|WP Register Form - POST Request Logged|WP Register Form - GET, HEAD, OTHER Request Logged|Lost Password Form - POST Request Logged|Lost Password Form - GET, HEAD, OTHER Request Logged|Comment Form User Is Logged In - POST Request Logged|Comment Form User Is Logged In - GET, HEAD, OTHER Request Logged|Comment Form User NOT Logged In - POST Request Logged|Comment Form User NOT Logged In - GET, HEAD, OTHER Request Logged|BuddyPress Register Form - POST Request Logged|BuddyPress Register Form - GET, HEAD, OTHER Request Logged|AutoRestore Turned Off Cron Check|WP Automatic Update - ARQ was turned Off|WP Automatic Update - ARQ was turned back On|Plugin Firewall AutoPilot Mode New Whitelist Rule\(s\) Created/', $check_string, $matches );
		
		foreach ( $matches[0] as $key => $value ) {
				
		}
			
		$array_count_values = array_count_values($matches[0]);
		$log_contents = array();
		
		if ( ! empty( $array_count_values ) ) {

			ksort($array_count_values);
			
			foreach ( $array_count_values as $key => $value ) {
				
				$log_contents[] = 'Total ' . $key . ' Log Entries: ' . $value . "\n";
				file_put_contents($SecLogMasterTXT, $log_contents);
			}

			$SecLogMasterTXT_contents = @file_get_contents($SecLogMasterTXT);
			$stringReplace = @file_get_contents($bpsProLog);	
			$pattern = '/BPS\sSECURITY\sLOG(.*\s*){1}=================(.*\s*){1}=================/';			

			if ( preg_match( $pattern, $stringReplace, $matches ) ) {
				$stringReplace = preg_replace( $pattern, "BPS SECURITY LOG\r\n=================\r\n=================\r\n\n[BEGIN Total # of Security Log Entries by Type:]\r\n" . $SecLogMasterTXT_contents . "[END Total # of Security Log Entries by Type:]", $stringReplace );
		
				file_put_contents($bpsProLog, $stringReplace);
			}
		}
	}
}

// Zip Security Log File - If ZipArchive Class is not available use PCLZip
function bps_Zip_Security_Log_File() {

	bpsPro_SecLog_Entry_Counter_add_to_zip();

	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

	$zip = new ZipArchive();
	$filename = WP_CONTENT_DIR . '/bps-backup/logs/security-log.zip';
	
	if ( $zip->open( $filename, ZIPARCHIVE::CREATE )!==TRUE ) {
    	exit("Error: Cannot Open $filename\n");
	}

	$zip->addFile( WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt', "http_error_log.txt" );
	$zip->close();

	return true;

	} else {

// Use PCLZip
define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/logs/' );
require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
	if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding( 'ISO-8859-1' );
	}
  		$archive = new PclZip( WP_CONTENT_DIR . '/bps-backup/logs/security-log.zip' );
  		$v_list = $archive->create( WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/logs/' );
  	
	return true;

	if ( $v_list == 0 ) {
		die("Error : ".$archive->errorInfo(true) );
		return false;	
	}
	}
}

// Zip the DB Backup Log File - If ZipArchive Class is not available use PCLZip
function bps_Zip_DBB_Log_File() {
	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

	$zip = new ZipArchive();
	$filename = WP_CONTENT_DIR . '/bps-backup/logs/db-backup-log.zip';
	
	if ( $zip->open( $filename, ZIPARCHIVE::CREATE )!==TRUE ) {
    	exit("Error: Cannot Open $filename\n");
	}

	$zip->addFile( WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt', "db_backup_log.txt" );
	$zip->close();

	return true;

	} else {

// Use PCLZip
define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/logs/' );
require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
	
	if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding( 'ISO-8859-1' );
	}
  		$archive = new PclZip( WP_CONTENT_DIR . '/bps-backup/logs/db-backup-log.zip' );
  		$v_list = $archive->create( WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/logs/' );
  	
	return true;

	if ( $v_list == 0) {
		die( "Error : ".$archive->errorInfo(true) );
		return false;	
	}
	}
}

// Zip MScan Log File - If ZipArchive Class is not available use PclZip
function bps_Zip_MScan_Log_File() {
	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

	$zip = new ZipArchive();
	$filename = WP_CONTENT_DIR . '/bps-backup/logs/mscan-log.zip';
	
	if ( $zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE ) {
    	exit("Error: Cannot Open $filename\n");
	}

	$zip->addFile(WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt', "mscan_log.txt");
	$zip->close();

	return true;

	} else {

// Use PCLZip
define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/logs/' );
require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
	if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding( 'ISO-8859-1' );
	}
  		$archive = new PclZip(WP_CONTENT_DIR . '/bps-backup/logs/mscan-log.zip');
  		$v_list = $archive->create(WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/logs/');
	
	return true;  	

	if ( $v_list == 0) {
		die("Error : ".$archive->errorInfo(true) );
		return false;
	}
	}
}

?>