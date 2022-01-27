<?php
if ( ! function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
?>

<!-- force the vertical scrollbar -->
<style>
#wpwrap{min-height:100.1%};
</style>

<div id="bps-container" class="wrap" style="margin:45px 20px 5px 0px;">

<noscript><div id="message" class="updated" style="font-weight:600;font-size:13px;padding:5px;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><span style="color:blue">BPS Warning: JavaScript is disabled in your Browser</span><br />BPS plugin pages will not display visually correct and all BPS JavaScript functionality will not work correctly.</div></noscript>

<?php 
$ScrollTop_options = get_option('bulletproof_security_options_scrolltop');

if ( isset( $ScrollTop_options['bps_scrolltop'] ) && $ScrollTop_options['bps_scrolltop'] != 'Off' ) {
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' && ! isset( $_POST['Submit-DB-Prefix-Table-Refresh'] ) || isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}
?>

<?php
		echo '<div class="bps-star-container">';
		echo '<div class="bps-star"><img src="'.plugins_url('/bulletproof-security/admin/images/star.png').'" /></div>';
		echo '<div class="bps-downloaded">';
		echo '<div class="bps-star-link"><a href="https://wordpress.org/support/view/plugin-reviews/bulletproof-security#postform" target="_blank" title="Add a Star Rating for the BPS plugin">'.__('Rate BPS', 'bulletproof-security').'</a><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Upgrade to BulletProof Security Pro">Upgrade to Pro</a></div>';
		echo '</div>';
		echo '</div>';
?>

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ DB Backup & Security', 'bulletproof-security'); ?></h2>
<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/db-backup-security/db-backup-help-text.php' );

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';
$UIoptions = get_option('bulletproof_security_options_theme_skin');

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address() {
	
	if ( is_admin() && current_user_can('manage_options') ) {
	
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = esc_html( $_SERVER['HTTP_CLIENT_IP'] );
			
			if ( ! is_array($ip) ) {
				
				if ( preg_match( '/(\d+\.){3}\d+/', $ip, $matches ) ) {

					return $matches[0];	
				
				} elseif ( preg_match( '/([:\d\w]+\.(\d+\.){2}\d+|[:\d\w]+)/', $ip, $matches ) ) {
				
					return $matches[0];	
		
				} else {
					
					return $ip;
				}
			
			} else {
				
				return current($ip);				
			}
		
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = esc_html( $_SERVER['HTTP_X_FORWARDED_FOR'] );
			
			if ( ! is_array($ip) ) {
				
				if ( preg_match( '/(\d+\.){3}\d+/', $ip, $matches ) ) {

					return $matches[0];	
				
				} elseif ( preg_match( '/([:\d\w]+\.(\d+\.){2}\d+|[:\d\w]+)/', $ip, $matches ) ) {
				
					return $matches[0];	
		
				} else {
					
					return $ip;
				}
			
			} else {
				
				return current($ip);				
			}
		
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = esc_html( $_SERVER['REMOTE_ADDR'] );
			return $ip;
		}
	}
}	

// Create Deny All .htaccess file with users current IP address on every page load.
// .53.6: BugFix changed to create htaccess file/code on every page load to ensure old htaccess file/code does not exist.
function bpsPro_DBBackup_deny_all() {

	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
		
		$DBBoptions = get_option('bulletproof_security_options_db_backup');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');		

		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			return;
		}

		if ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$bps_denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$bps_denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address()."\n</FilesMatch>";		
		}		
		
		$denyall_htaccess_file = ! isset($DBBoptions['bps_db_backup_folder']) ? '' : $DBBoptions['bps_db_backup_folder'] .'/.htaccess';
		$blank_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/blank.txt';

		if ( isset($DBBoptions['bps_db_backup_folder']) && ! file_exists($denyall_htaccess_file) ) {
			copy($blank_file, $denyall_htaccess_file);
		}

		if ( is_writable($denyall_htaccess_file) ) {
		if ( ! $handle = fopen($denyall_htaccess_file, 'w+b') ) {
         	exit;
    	}
    	if ( fwrite($handle, $bps_denyall_content) === FALSE ) {
		 	exit;
    	}
    	fclose($handle);
		}
	}
}
bpsPro_DBBackup_deny_all();

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" />
    
<style>
<!--
.bps-spinner {
    visibility:visible;
	position:fixed;
    top:7%;
    left:45%;
 	width:240px;
	background:#fff;
	border:4px solid black;
	padding:2px 0px 4px 8px;   
	z-index:99999;
}
-->
</style> 

    <div id="bps-spinner" class="bps-spinner" style="visibility:hidden;">
    	<img id="bps-img-spinner" src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-spinner.gif'); ?>" style="float:left;margin:0px 20px 0px 0px;" />
        <div id="bps-spinner-text-btn" style="padding:20px 0px 26px 0px;font-size:14px;">Processing...<br><button style="margin:10px 0px 0px 10px;" onclick="javascript:history.go(-1)">Cancel</button>
		</div>
    </div> 

<script type="text/javascript">
/* <![CDATA[ */
function bpsSpinnerDBBackup() {
	
    var r = confirm("CAUTION:\n\n-------------------------------------------------------------\n\nThis Form is used to Run Backup Jobs or Delete Backup Jobs depending on which checkbox you selected.\n\n-------------------------------------------------------------\n\nClick OK to either Run a Backup Job or Delete Backup Job(s) or click Cancel");
	
	var img = document.getElementById("bps-spinner"); 	
	
	if (r == true) {

		img.style.visibility = "visible";
	
	} else {
	
		history.go(-1);
	}
}
/* ]]> */
</script> 

<script type="text/javascript">
/* <![CDATA[ */
function bpsSpinnerTablePrefix() {
	
    var r = confirm("Clicking OK will change your DB Table Prefix name.\n\n-------------------------------------------------------------\n\nClick OK to Change your DB Table Prefix name or click Cancel.");
	
	var img = document.getElementById("bps-spinner"); 	
	
	if (r == true) {

		img.style.visibility = "visible";
	
	} else {
	
		history.go(-1);
	}
}
/* ]]> */
</script> 

<script type="text/javascript">
/* <![CDATA[ */
function bpsSpinnerTablePrefix2() {
	
    var r = confirm("Reminder: Did you click the Change DB Table Prefix button first before clicking this button?\n\n-------------------------------------------------------------\n\nClick OK to complete your DB Table Prefix Name change or click Cancel.");
	
	var img = document.getElementById("bps-spinner"); 	
	
	if (r == true) {

		img.style.visibility = "visible";
	
	} else {
	
		history.go(-1);
	}
}
/* ]]> */
</script> 

<script type="text/javascript">
/* <![CDATA[ */
function bpsSpinnerTableRefresh() {
	
    var r = confirm("Click OK to Load|Refresh the DB Table Names and Character Length Table or click Cancel.");
	var img = document.getElementById("bps-spinner"); 	
	
	if (r == true) {

		img.style.visibility = "visible";
	
	} else {
	
		history.go(-1);
	}
}
/* ]]> */
</script> 
    
    </div>
		<ul>
            <li><a href="#bps-tabs-1"><?php _e('DB Backup', 'bulletproof-security'); ?></a></li>
            <li><a href="#bps-tabs-2"><?php _e('DB Backup Log', 'bulletproof-security'); ?></a></li>
            <li><a href="#bps-tabs-3"><?php _e('DB Table Prefix Changer', 'bulletproof-security'); ?></a></li>
 			<li><a href="#bps-tabs-4"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('DB Backup ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Full & Partial DB Backups, Manual & Scheduled DB Backups, Email Zip Backups, Automatically Delete Old Backups', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a></div>'; echo $text; ?>    
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 15px 0px;"><?php _e('DB Backup', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('DB Backup', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		// Forum Help Links or of course both
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/database-backup-security-guide/" title="DB Backup & Security Guide" target="_blank"><?php _e('DB Backup & Security Guide', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php echo $bps_modal_content1; ?>
    </p>
</div>

<div id="bps-accordion-1" class="bps-accordion-main-2" style="margin:0px 0px 20px 0px;">
<h3><?php _e('Backup Jobs ~ Manual|Scheduled', 'bulletproof-security'); ?></h3>
<div id="dbb-accordion-inner">

<?php


	// Reusable variables
	$DBBoptions = get_option('bulletproof_security_options_db_backup');	

// Form Processing: DB Backup Create Job Form
// Note: Needs to above all Forms to display current data.
if ( isset( $_POST['Submit-DBB-Create-Job'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_db_backup_create_job');
	
	?>
	
	<style>
	<!--
	.ui-accordion.bps-accordion .ui-accordion-content {overflow:hidden;}
	-->
	</style>
	
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
			$( "#bps-accordion-1" ).accordion({
			collapsible: true,
			active: 0,
			autoHeight: true,
			clearStyle: true,
			heightStyle: "content"
			});
		});
		/* ]]> */
		</script>
	
	<?php

	if ( $DBBoptions['bps_db_backup_status_display'] == 'No DB Backups' || $DBBoptions['bps_db_backup_status_display'] == '' ) {
		$bps_db_backup_status_display = 'Backup Job Created';
	} else {
		$bps_db_backup_status_display = $DBBoptions['bps_db_backup_status_display'];
	}

	if ( $_POST['dbb_backup_on_off'] == 'Off' ) {
		wp_clear_scheduled_hook('bpsPro_DBB_check');
	}
	
	// some of these options are "one-shot" options
	$DBB_Create_Job_Options = array( 
	'bps_db_backup' 						=> $_POST['dbb_backup_on_off'], 
	'bps_db_backup_description' 			=> esc_html($_POST['DBBDescription']), 
	'bps_db_backup_folder' 					=> $_POST['DBBFolder'], 
	'bps_db_backup_download_link' 			=> $_POST['DBBDownloadLink'], 
	'bps_db_backup_job_type' 				=> $_POST['dbb_backup_job_type'], 
	'bps_db_backup_frequency' 				=> $_POST['dbb_backup_job_frequency'], 		 
	'bps_db_backup_start_time_hour' 		=> $_POST['dbb_backup_job_start_time_hour'], 
	'bps_db_backup_start_time_weekday' 		=> $_POST['dbb_backup_job_start_time_weekday'], 
	'bps_db_backup_start_time_month_date' 	=> $_POST['dbb_backup_job_start_time_month_date'], 
	'bps_db_backup_email_zip' 				=> $_POST['dbb_backup_email_zip'], 
	'bps_db_backup_delete' 					=> $_POST['dbb_backup_delete'], 
	'bps_db_backup_status_display' 			=> $bps_db_backup_status_display // one-shot/one-time option - used for one-time Dashboard status display
	);
	
	foreach( $DBB_Create_Job_Options as $key => $value ) {
		update_option('bulletproof_security_options_db_backup', $DBB_Create_Job_Options);
	}
	
	$DBB_Create_Job = $_POST['dbb'];
	$DBBtable_name = $wpdb->prefix . "bpspro_db_backup";
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$bpsDBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
	
	if ( $_POST['dbb_backup_job_type'] == 'Manual' ) {
		$bps_frequency = 'Manual';
		$bps_last_job = 'Backup Job Created';
		$bps_next_job = 'Manual';
		$bps_email_zip = 'Manual';
		$bps_email_zip_log = 'Manual';
	}

	if ( $_POST['dbb_backup_job_type'] == 'Scheduled' ) {
		$bps_frequency = $_POST['dbb_backup_job_frequency'];
		$bps_last_job = 'Backup Job Created';
		$bps_next_job = $_POST['dbb_backup_job_start_time_weekday'] . ' ' .  $_POST['dbb_backup_job_start_time_month_date'] . ' ' .  $_POST['dbb_backup_job_start_time_hour'];
		$bps_next_job = trim( str_replace( 'NA', "", $bps_next_job ) );	
		
		if ( $_POST['dbb_backup_email_zip'] == 'Delete' ) {
			$bps_email_zip_log = 'Yes & Delete';
			$bps_email_zip = 'Delete';	
		} else {
			$bps_email_zip_log = $_POST['dbb_backup_email_zip'];
			$bps_email_zip = $_POST['dbb_backup_email_zip'];
		}

		if ( $_POST['dbb_backup_email_zip'] == 'EmailOnly' ) {
			$bps_email_zip_log = 'Send Email Only';
			$bps_email_zip = 'EmailOnly';	
		} else {
			$bps_email_zip_log = $_POST['dbb_backup_email_zip'];
			$bps_email_zip = $_POST['dbb_backup_email_zip'];
		}
	}

	$log_title = "\r\n" . '[Create Backup Job Settings Logged: ' . $timestamp . ']' . "\r\n" . 'Description|Backup Job Name: ' . $_POST['DBBDescription'] . "\r\n" . 'DB Backup Folder Location: ' . $_POST['DBBFolder'] . "\r\n" . 'DB Backup File Download Link|URL: ' . $_POST['DBBDownloadLink'] . "\r\n" . 'Backup Job Type: ' . $_POST['dbb_backup_job_type'] . "\r\n" . 'Frequency: ' . $_POST['dbb_backup_job_frequency'] . "\r\n" . 'Time When Scheduled Backup is Run: ' . $bps_next_job . "\r\n" . 'Send Scheduled Backup Zip Files Via Email: ' . $bps_email_zip_log . "\r\n" . 'Automatically Delete Old Backup Files Older Than: ' . $_POST['dbb_backup_delete'] .' day(s) old'. "\r\n" . 'Scheduled Backups (override): ' . $_POST['dbb_backup_on_off'] . "\r\n";
	
	if ( empty( $DBB_Create_Job ) ) {
		echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';	
		echo '<strong><font color="#fb0101">'.__('Error: You did not select any DB Tables to backup. Backup Job was not created.', 'bulletproof-security').'</font></strong><br>';
		echo '</p></div>';
	}
	
	if ( ! empty( $DBB_Create_Job ) ) {
		
		if ( is_writable( $bpsDBBLog ) ) {
		if ( ! $handle = fopen( $bpsDBBLog, 'a' ) ) {
        	exit;
    	}
    	if ( fwrite( $handle, $log_title ) === FALSE ) {
        	exit;
    	}
    	fclose($handle);
		}

		echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
		
		$Table_array = array();
		
		foreach ( $DBB_Create_Job as $key => $value ) {

			$Table_array[] = $key;
			$comma_separated = implode(', ', $Table_array);	
			$NoDupes = implode(', ', array_unique(explode(', ', $comma_separated)));
			
			$log_contents = 'Table Name: ' . $key . "\r\n";
					
			if ( is_writable( $bpsDBBLog ) ) {
			if ( ! $handle = fopen( $bpsDBBLog, 'a' ) ) {
         		exit;
    		}
    		if ( fwrite( $handle, $log_contents ) === FALSE ) {
        		exit;
    		}
    		fclose($handle);
			}
		}

		/** Date & Time Calculations **/
		if ( $_POST['dbb_backup_job_start_time_hour'] == 'NA' ) {
			
			$hour = date( "H", time() );
		
		} else {
			
			$form_hours = array( '12AM', '1AM', '2AM', '3AM', '4AM', '5AM', '6AM', '7AM', '8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM' );
			$military_hours = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23' );		
			$hour = str_replace( $form_hours, $military_hours, $_POST['dbb_backup_job_start_time_hour'] );			
		}

		if ( $_POST['dbb_backup_job_start_time_month_date'] == 'NA' ) {
			$day = date( "j", time() );	
		
		} else {
			
			$day = $_POST['dbb_backup_job_start_time_month_date'];		
		}
		
		$clock = mktime( $hour, 0, 0, date( "n", time() ), $day, date( "Y", time() ) );

		$form_weekday = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
		$form_numeric = array( '0', '1', '2', '3', '4', '5', '6' );		
		$weekday_numeric = str_replace( $form_weekday, $form_numeric, $_POST['dbb_backup_job_start_time_weekday'] );	
		
		$day_of_week_now_numeric = date( "w", time() );
		
		if ( $day_of_week_now_numeric == $weekday_numeric || $_POST['dbb_backup_job_start_time_weekday'] == 'NA' ) {
   			$weekday_days = 0;
		
		} else {

			$dwn = $day_of_week_now_numeric;
			$dwf = $weekday_numeric;
	
			// Bulky stuff, but the "for" loops code was overly complex, problematic and limiting for some scenarios
			// sometimes simpler is better - minimal finite data so no big deal
			if ( $dwn == '0' && $dwf == '1' || $dwn == '1' && $dwf == '2' || $dwn == '2' && $dwf == '3' || $dwn == '3' && $dwf == '4' || $dwn == '4' && $dwf == '5' || $dwn == '5' && $dwf == '6' || $dwn == '6' && $dwf == '0' ) {
				$weekday_days = 1;
			}

			if ( $dwn == '0' && $dwf == '2' || $dwn == '1' && $dwf == '3' || $dwn == '2' && $dwf == '4' || $dwn == '3' && $dwf == '5' || $dwn == '4' && $dwf == '6' || $dwn == '5' && $dwf == '0' || $dwn == '6' && $dwf == '1' ) {
				$weekday_days = 2;
			}

			if ( $dwn == '0' && $dwf == '3' || $dwn == '1' && $dwf == '4' || $dwn == '2' && $dwf == '5' || $dwn == '3' && $dwf == '6' || $dwn == '4' && $dwf == '0' || $dwn == '5' && $dwf == '1' || $dwn == '6' && $dwf == '2' ) {
				$weekday_days = 3;
			}

			if ( $dwn == '0' && $dwf == '4' || $dwn == '1' && $dwf == '5' || $dwn == '2' && $dwf == '6' || $dwn == '3' && $dwf == '0' || $dwn == '4' && $dwf == '1' || $dwn == '5' && $dwf == '2' || $dwn == '6' && $dwf == '3' ) {
				$weekday_days = 4;
			}

			if ( $dwn == '0' && $dwf == '5' || $dwn == '1' && $dwf == '6' || $dwn == '2' && $dwf == '0' || $dwn == '3' && $dwf == '1' || $dwn == '4' && $dwf == '2' || $dwn == '5' && $dwf == '3' || $dwn == '6' && $dwf == '4' ) {
				$weekday_days = 5;
			}
	
			if ( $dwn == '0' && $dwf == '6' || $dwn == '1' && $dwf == '0' || $dwn == '2' && $dwf == '1' || $dwn == '3' && $dwf == '2' || $dwn == '4' && $dwf == '3' || $dwn == '5' && $dwf == '4' || $dwn == '6' && $dwf == '5' ) {
				$weekday_days = 6;
			}
		}
		
		$bps_next_job_unix = $clock + ($weekday_days * 24 * 60 * 60); 

		$DBBInsertRows = $wpdb->insert( $DBBtable_name, array( 'bps_table_name' => $NoDupes, 'bps_desc' => esc_html($_POST['DBBDescription']), 'bps_job_type' => $_POST['dbb_backup_job_type'], 'bps_frequency' => $bps_frequency, 'bps_last_job' => $bps_last_job, 'bps_next_job' => $bps_next_job, 'bps_next_job_unix' => $bps_next_job_unix, 'bps_email_zip' => $bps_email_zip, 'bps_job_created' => current_time('mysql') ) );
		
		$text = '<strong><font color="green">'.__('Backup Job ', 'bulletproof-security').$_POST['DBBDescription'].__(' Created Successfully.', 'bulletproof-security').'</font></strong><br>';
		echo $text;
		echo '<strong>'.__('Backup Job Settings Logged successfully in the DB Backup Log', 'bulletproof-security').'</strong><br>';
		echo '</p></div>';
			
		$DBBLog_Options = array( 'bps_dbb_log_date_mod' => bpsPro_DBB_Log_LastMod() );
	
		foreach( $DBBLog_Options as $key => $value ) {
			update_option('bulletproof_security_options_DBB_log', $DBBLog_Options);
		}
	}
}

// Form Processing: Backup Jobs ~ Manual|Scheduled - DB Backup Run|Delete Jobs Form
function bpsPro_dbbackup_form_processing() {

if ( isset( $_POST['Submit-DBB-Run-Job'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_db_backup_run_job');
	
	global $wpdb;	
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);	
	$DBBoptions = get_option('bulletproof_security_options_db_backup');

	$DBBjobs = $_POST['DBBjobs'];
	$DBBtable_name = $wpdb->prefix . "bpspro_db_backup";

	switch( $_POST['Submit-DBB-Run-Job'] ) {
		case __('Run Job|Delete Job', 'bulletproof-security'):
		
		$delete_jobs = array();
		$run_jobs = array();
		
		if ( ! empty( $DBBjobs ) ) {
			
			foreach ( $DBBjobs as $key => $value ) {
				
				if ( $value == 'deletejob' ) {
					$delete_jobs[] = $key;
				
				} elseif ( $value == 'runjob' ) {
					$run_jobs[] = $key;
				}
			}
		}
			
		if ( ! empty( $delete_jobs ) ) {
			
		?>
		
		<style>
		<!--
		.ui-accordion.bps-accordion .ui-accordion-content {overflow:hidden;}
		-->
		</style>
		
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($){
				$( "#bps-accordion-1" ).accordion({
				collapsible: true,
				active: 0,
				autoHeight: true,
				clearStyle: true,
				heightStyle: "content"
				});
			});
			/* ]]> */
			</script>
		
		<?php

			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_jobs as $delete_job ) {
				
				$DBBackupRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $DBBtable_name WHERE bps_id = %d", $delete_job ) );
			
				foreach ( $DBBackupRows as $row ) {
					
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $DBBtable_name WHERE bps_id = %d", $delete_job ) );
					
					wp_clear_scheduled_hook('bpsPro_DBB_check');
					
					$textDelete = '<strong><font color="green">'.__('Backup Job: ', 'bulletproof-security').$row->bps_desc.__(' has been deleted successfully.', 'bulletproof-security').'</font></strong><br>';
					echo $textDelete;
	
				}
			}
			echo '</p></div>';
		}
		
		if ( ! empty( $run_jobs ) ) {
			
		?>
		
		<style>
		<!--
		.ui-accordion.bps-accordion .ui-accordion-content {overflow:hidden;}
		-->
		</style>
		
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($){
				$( "#bps-accordion-1" ).accordion({
				collapsible: true,
				active: 1,
				autoHeight: true,
				clearStyle: true,
				heightStyle: "content"
				});
			});
			/* ]]> */
			</script>
		
		<?php

			$db_backup = $DBBoptions['bps_db_backup_folder'] . '/' . DB_NAME . '.sql';
				
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $run_jobs as $run_job ) {
				
				$DBBackupRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $DBBtable_name WHERE bps_id = %d", $run_job ) );
			
				foreach ( $DBBackupRows as $row ) {

					$job_name = $row->bps_desc;
					$job_type = $row->bps_job_type;
					$email_zip = $row->bps_email_zip;
					
					$build_query_1 = "SHOW TABLES FROM `".DB_NAME."` WHERE `Tables_in_".DB_NAME."` LIKE '";
					$build_query_2 = str_replace( ', ', "' OR `Tables_in_".DB_NAME."` LIKE '", $row->bps_table_name );
					$build_query_3 = "'";
					$tables = $wpdb->get_results( $build_query_1.$build_query_2.$build_query_3, ARRAY_A );
					
					bpsPro_db_backup( $db_backup, $tables, $job_name, $job_type, $email_zip );
					
					$update_rows = $wpdb->update( $DBBtable_name, array( 'bps_last_job' => $timestamp ), array( 'bps_id' => $row->bps_id ) );

					$textRunJob = '<strong><font color="green">'.__('Backup Job: ', 'bulletproof-security').$row->bps_desc.__(' has completed.', 'bulletproof-security').'<br>'.__('Your DB Backup Log contains the Backup Job Completion Time, Total Memory Used and other information about this Backup.', 'bulletproof-security').'</font></strong><br>';
					echo $textRunJob;

				}			
			}
			echo '</p></div>';			
		}
		break;
	} // end Switch
}
}
bpsPro_dbbackup_form_processing();

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);

	// Form: Backup Jobs ~ Manual|Scheduled - DB Backup Run|Delete Jobs Form
	echo '<form name="bpsDBBackupRunJob" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_db_backup_run_job');

	$DBBtable_name = $wpdb->prefix . "bpspro_db_backup";
	$DBBRows = '';
	$DBBTableRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $DBBtable_name WHERE bps_table_name != %s", $DBBRows ) );	
	
	echo '<div id="DBBJobscheckall">';
	echo '<table class="widefat" style="text-align:left;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:20%;font-size:1.13em;background-color:transparent;"><strong>'.__('Description|Job Name', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:1.13em;"><strong><div style="position:relative; bottom:-9px; left:0px;">'.__('Delete', 'bulletproof-security').'</span></strong><br><input type="checkbox" class="checkallDeleteJobs" style="text-align:left;margin-left:0px;" /></th>';	
	echo '<th scope="col" style="width:5%;font-size:1.13em;background-color:transparent;"><strong>'.__('Run', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;font-size:1.13em;background-color:transparent;"><strong>'.__('Job Type', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;font-size:1.13em;background-color:transparent;"><strong>'.__('Frequency', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:15%;font-size:1.13em;background-color:transparent;"><strong>'.__('Last Backup', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:15%;font-size:1.13em;background-color:transparent;"><strong>'.__('Next Backup', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;font-size:1.13em;background-color:transparent;"><strong>'.__('Email Backup', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;font-size:1.13em;background-color:transparent;"><strong>'.__('Job Created', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';

	if ( $wpdb->num_rows == 0 ) {		
		echo '<th scope="row" style="border-bottom:none;">'.__('No Backup Jobs have been created yet.', 'bulletproof-security').'</th>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '<td></td>';
		echo '</tr>';
	
	} else {

		foreach ( $DBBTableRows as $row ) {
			
			echo '<th scope="row" style="border-bottom:none;">'.$row->bps_desc.'</th>';
			echo "<td><input type=\"checkbox\" id=\"deletejob\" name=\"DBBjobs[$row->bps_id]\" value=\"deletejob\" class=\"deletejobALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"runjob\" name=\"DBBjobs[$row->bps_id]\" value=\"runjob\" /><br><span style=\"font-size:10px;\">".__('Run', 'bulletproof-security')."</span></td>";			
			echo '<td>'.$row->bps_job_type.'</td>';
			echo '<td>'.$row->bps_frequency.'</td>';
			echo '<td>'.$row->bps_last_job.'</td>';

			if ( $row->bps_frequency == 'Hourly' && $row->bps_next_job == '' ) {
				$bps_next_job_visual = 'Hourly';
			
			} else {
			
			$day_numeric = array( '1 ', '2 ', '3 ', '4 ', '5 ', '6 ', '7 ', '8 ', '9 ', '10 ', '11 ', '12 ', '13 ', '14 ', '15 ', '16 ', '17 ', '18 ', '19 ', '20 ', '21 ', '22 ', '23 ', '24 ', '25 ', '26 ', '27 ', '28 ', '29 ', '30 ' );
			$day_ordinal = array( '1st ', '2nd ', '3rd ', '4th ', '5th ', '6th ', '7th ', '8th ', '9th ', '10th ', '11th ', '12th ', '13th ', '14th ', '15th ', '16th ', '17th ', '18th ', '19th ', '20th ', '21st ', '22nd ', '23rd ', '24th ', '25th ', '26th ', '27th ', '28th ', '29th ', '30th ' );		
			$bps_next_job_visual = str_replace( $day_numeric, $day_ordinal, $row->bps_next_job );			
			}
			
			echo '<td>'.$bps_next_job_visual.'</td>';
			
			if ( $row->bps_email_zip == 'Delete' ) {
				echo '<td>'.__('Yes & Delete', 'bulletproof-security').'</td>';
			} elseif ( $row->bps_email_zip == 'EmailOnly' ) {
				echo '<td>'.__('Send Email Only', 'bulletproof-security').'</td>';			
			} else {
				echo '<td>'.$row->bps_email_zip.'</td>';
			}
			
			echo '<td>'.$row->bps_job_created.'</td>';
			echo '</tr>';
		}
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';

	echo "<p><input type=\"submit\" name=\"Submit-DBB-Run-Job\" value=\"".esc_attr__('Run Job|Delete Job', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"bpsSpinnerDBBackup()\" /></p></form>";

?>

<?php
if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#DBBJobscheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallDeleteJobs').click(function() {
        $(this).parents('#DBBJobscheckall:eq(0)').find('.deletejobALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</div>
<h3><?php _e('Backup Files ~ Download|Delete', 'bulletproof-security'); ?></h3>
<div id="dbb-accordion-inner">

<?php
// Form Processing: DB Backup File Delete Files Form (downloads are links and not processed)
if ( isset( $_POST['Submit-DBB-Files'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_db_backup_delete_files');
	
	?>
	
	<style>
	<!--
	.ui-accordion.bps-accordion .ui-accordion-content {overflow:hidden;}
	-->
	</style>
	
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
			$( "#bps-accordion-1" ).accordion({
			collapsible: true,
			active: 1,
			autoHeight: true,
			clearStyle: true,
			heightStyle: "content"
			});
		});
		/* ]]> */
		</script>
	
	<?php

	$DBBFiles = $_POST['DBBfiles'];

	switch( $_POST['Submit-DBB-Files'] ) {
		case __('Delete Files', 'bulletproof-security'):
		
		$delete_files = array();
		
		if ( ! empty( $DBBFiles ) ) {
			
			foreach ( $DBBFiles as $key => $value ) {
				
				if ( $value == 'deletefile' ) {
					$delete_files[] = $key;
					
				}
			}
		}
			
		if ( ! empty( $delete_files ) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_files as $delete_file ) {
				
				unlink( $DBBoptions['bps_db_backup_folder'] . '/' . $delete_file );
				$textDelete = '<strong><font color="green">'.__('Backup File: ', 'bulletproof-security').$delete_file.__(' has been deleted successfully.', 'bulletproof-security').'</font></strong><br>';
				echo $textDelete;
			}
			echo '</p></div>';	
		}
		break;
	}
}

	// Form: DB Backup File Delete & Download Files Form
	echo '<form name="bpsDBBackupFiles" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_db_backup_delete_files');

	$source = ! isset($DBBoptions['bps_db_backup_folder']) ? '' : $DBBoptions['bps_db_backup_folder'];
	$count = 0;	
	
	if ( is_dir($source) ) {
		
		$iterator = new DirectoryIterator($source);

		echo '<div id="DBBFilescheckall" style="max-height:270px;overflow:auto;">';
		echo '<table class="widefat" style="text-align:left;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:20%;font-size:1.13em;background-color:transparent;"><strong>'.__('Backup Filename', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:1.13em;"><strong><div style="position:relative; bottom:-9px; left:0px;">'.__('Delete', 'bulletproof-security').'</span></strong><br><input type="checkbox" class="checkallDeleteFiles" style="text-align:left;margin-left:0px;" /></th>';	
		echo '<th scope="col" style="width:5%;font-size:1.13em;background-color:transparent;"><strong>'.__('Download', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:45%;font-size:1.13em;background-color:transparent;"><strong>'.__('Backup Folder', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:10%;font-size:1.13em;background-color:transparent;"><strong>'.__('Size', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:15%;font-size:1.13em;background-color:transparent;"><strong>'.__('Date|Time', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';		

		foreach ( $iterator as $file ) {
			
			if ( $file->isFile() && $file->getFilename() != '.htaccess' ) {
				$count++;	
				$fileSize = filesize( $source.DIRECTORY_SEPARATOR.$file->getFilename() );
				$last_modified = filemtime( $source.DIRECTORY_SEPARATOR.$file->getFilename() );  

				echo '<th scope="row" style="border-bottom:none;font-size:1.13em;">'.$file->getFilename().'</th>';
				echo "<td><input type=\"checkbox\" id=\"deletefile\" name=\"DBBfiles[".$file->getFilename()."]\" value=\"deletefile\" class=\"deletefileALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
				echo '<td><div style="margin:0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$DBBoptions['bps_db_backup_download_link'] . $file->getFilename().'" style="font-size:1em;text-decoration:none;">'.__('Download', 'bulletproof-security').'</a></div></td>';			
				echo '<td>'.$DBBoptions['bps_db_backup_folder'].'</td>';
				
				if ( number_format( $fileSize, 2, '.', '' ) >= 1048576 ) {
					echo '<td>'.number_format( $fileSize / ( 1024 * 1024 ), 2 ).' MB</td>';				
				} else {
					echo '<td>'.number_format( $fileSize / 1024, 2 ).' KB</td>';
				}
				echo '<td>'.date( 'Y-m-d g:i a', $last_modified + $gmt_offset ).'</td>';
				echo '</tr>';
				
				} else {	
	
				if ( !$file->isDot() && $count <= 0 && $file->getFilename() != '.htaccess' ) {
				
				echo '<th scope="row" style="border-bottom:none;">'.__('No Backup Jobs have been Run yet. No Files in Backup.', 'bulletproof-security').'</th>';
				echo '<td></td>';		
				echo '<td></td>'; 
				echo '<td></td>';		
				echo '<td></td>'; 
				echo '<td></td>';
				echo '</tr>';						
				}
			}
		}
	
		echo '</tbody>';
		echo '</table>';
		echo '</div>';		
	}

	echo "<p><input type=\"submit\" name=\"Submit-DBB-Files\" value=\"".esc_attr__('Delete Files', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Click OK to Delete Backup File(s) or click Cancel', 'bulletproof-security')."')\" /></p></form>";

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#DBBFilescheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallDeleteFiles').click(function() {
        $(this).parents('#DBBFilescheckall:eq(0)').find('.deletefileALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</div>
<h3><?php _e('Create Backup Jobs', 'bulletproof-security'); ?></h3>
<div id="dbb-accordion-inner">

<?php

// Form Processing: Rename|Create|Reset DB Backup Folder Location and DB Backup File Download Link|URL
if ( isset( $_POST['Submit-DBB-Reset'] ) && current_user_can('manage_options') ) {
	require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/db-backup-security/db-backup-functions.php' );		
	bpsPro_reset_db_backup_folder();
}

	echo '<div id="dbb-special">';
	// Form: DB Backup Create Job Form
	echo '<form name="bpsDBBackupCreateJob" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_db_backup_create_job');

	$DBTables = '';
	$size = 0;
	$getDBTables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLE STATUS WHERE Name != %s", $DBTables ) );
	// Get new current DB option values.
	$DBBoptions = get_option('bulletproof_security_options_db_backup');

	echo '<table class="widefat" style="text-align:left;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:30%;font-size:1.13em;background-color:transparent;"><strong>'.__('Database Tables ', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:50%;font-size:1.13em;background-color:transparent;"><strong>'.__('Backup Job Settings|Independent Options', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:20%;font-size:1.13em;background-color:transparent;"><strong>'.__('Rename|Create|Reset Tool', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';	

	echo '<th scope="row" style="border-bottom:none;font-size:1.13em;vertical-align:top;">';

	echo '<div id="DBBcheckall">';
	echo '<table style="text-align:left;border-right:1px solid #e5e5e5;padding:5px;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:20px;border-bottom:1px solid #e5e5e5;background-color:transparent;"><strong><span style="margin-left:9px;font-size:.88em;">'.__('All', 'bulletproof-security').'</span></strong><br><input type="checkbox" class="checkallDBB" /></th>';
	echo '<th scope="col" style="width:400px;font-size:1em;padding-top:20px;margin-right:20px;border-bottom:1px solid #e5e5e5;background-color:transparent;"><strong>'.__('DB Table Name', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	$checked = ( isset( $_POST['dbb[$Tabledata->Name]'] ) ) ? $_POST['dbb[$Tabledata->Name]'] : 'checked';
	
	foreach ( $getDBTables as $Tabledata ) {

		echo "<td><input type=\"checkbox\" id=\"dbbtables\" name=\"dbb[$Tabledata->Name]\" value=\"dbbtables\" class=\"dbbtablesALL\" $checked /></td>";
		echo '<td>'.$Tabledata->Name.'</td>';
		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>'; // jQuery div parent
	echo '</th>';
	
	echo '<td style="border:none">';		
	echo '<div id="DBBOptions" style="margin:0px 0px 0px 0px;float:left;">';

	$DBBDescription = ( isset( $_POST['DBBDescription'] ) ) ? $_POST['DBBDescription'] : '';
	
	if ( ! isset($DBBoptions['bps_db_backup_folder']) ) {
		$DBBFolder = '';
	} else {
		$DBBFolder = ( isset( $_POST['DBBFolder'] ) ) ? $_POST['DBBFolder'] : $DBBoptions['bps_db_backup_folder'];
	}
	
	if ( ! isset($DBBoptions['bps_db_backup_download_link']) ) {
		$DBBDownloadLink = '';
	} else {
		$DBBDownloadLink = ( isset( $_POST['DBBDownloadLink'] ) ) ? $_POST['DBBDownloadLink'] : $DBBoptions['bps_db_backup_download_link'];
	}
	
	$bps_db_backup_job_type = ! isset($DBBoptions['bps_db_backup_job_type']) ? '' : $DBBoptions['bps_db_backup_job_type'];
	$bps_db_backup_frequency = ! isset($DBBoptions['bps_db_backup_frequency']) ? '' : $DBBoptions['bps_db_backup_frequency'];	
	$bps_db_backup_start_time_hour = ! isset($DBBoptions['bps_db_backup_start_time_hour']) ? '' : $DBBoptions['bps_db_backup_start_time_hour'];
	$bps_db_backup_start_time_weekday = ! isset($DBBoptions['bps_db_backup_start_time_weekday']) ? '' : $DBBoptions['bps_db_backup_start_time_weekday'];		
	$bps_db_backup_start_time_month_date = ! isset($DBBoptions['bps_db_backup_start_time_month_date']) ? '' : $DBBoptions['bps_db_backup_start_time_month_date'];
	$bps_db_backup_email_zip = ! isset($DBBoptions['bps_db_backup_email_zip']) ? '' : $DBBoptions['bps_db_backup_email_zip'];	
	$bps_db_backup_delete = ! isset($DBBoptions['bps_db_backup_delete']) ? '' : $DBBoptions['bps_db_backup_delete'];
	$bps_db_backup = ! isset($DBBoptions['bps_db_backup']) ? '' : $DBBoptions['bps_db_backup'];	

	echo '<label for="bps-dbb">'.__('Description|Backup Job Name:', 'bulletproof-security').'</label><br>';
	// allow html. is sanitized later in form processing when inserted into the DB and output is encoded.
	echo '<input type="text" name="DBBDescription" class="dbb-text-500" value="'.esc_html($DBBDescription).'" /><br>';

	echo '<label for="bps-dbb">'.__('DB Backup Folder Location:', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Recommended: Use The Default Obfuscated & Secure BPS Backup Folder.', 'bulletproof-security').'</strong></font></label><br>';
	echo '<input type="text" name="DBBFolder" class="dbb-text-500" value="'; echo esc_html(trim(stripslashes($DBBFolder))); echo '" /><br>';	

	echo '<label for="bps-dbb">'.__('DB Backup File Download Link|URL:', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Note: If you see 404 errors when trying to download zip files or if you have', 'bulletproof-security').'</strong></font></label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('changed the DB Backup Folder Location above, click the Read Me help button.', 'bulletproof-security').'</strong></font></label><br>';
	echo '<input type="text" name="DBBDownloadLink" class="dbb-text-500" value="'; echo esc_url(trim($DBBDownloadLink)); echo '" /><br>';

	echo '<label for="bps-dbb">'.__('Backup Job Type: Manual or Scheduled', 'bulletproof-security').'</label><br>';
	echo '<select name="dbb_backup_job_type" class="form-340">';
	echo '<option value="Manual"'. selected('Manual', $bps_db_backup_job_type).'>'.__('Manual DB Backup Job', 'bulletproof-security').'</option>';
	echo '<option value="Scheduled"'. selected('Scheduled', $bps_db_backup_job_type).'>'.__('Scheduled DB Backup Job', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-dbb">'.__('Frequency of Scheduled Backup Job (recurring)', 'bulletproof-security').'</label><br>';
	echo '<select name="dbb_backup_job_frequency" class="form-340">';
	echo '<option value="NA"'. selected('NA', $bps_db_backup_frequency).'>'.__('N/A', 'bulletproof-security').'</option>';
	echo '<option value="Hourly"'. selected('Hourly', $bps_db_backup_frequency).'>'.__('Hourly Scheduled DB Backup Job', 'bulletproof-security').'</option>';
	echo '<option value="Daily"'. selected('Daily', $bps_db_backup_frequency).'>'.__('Daily Scheduled DB Backup Job', 'bulletproof-security').'</option>';
	echo '<option value="Weekly"'. selected('Weekly', $bps_db_backup_frequency).'>'.__('Weekly Scheduled DB Backup Job', 'bulletproof-security').'</option>';
	echo '<option value="Monthly"'. selected('Monthly', $bps_db_backup_frequency).'>'.__('Monthly Scheduled DB Backup Job', 'bulletproof-security').'</option>';
	echo '</select><br><br>';
	
	echo '<label for="bps-dbb">'.__('Hour When Scheduled Backup is Run (recurring)', 'bulletproof-security').'</label><br>';
	echo '<select name="dbb_backup_job_start_time_hour" class="form-340">';
	echo '<option value="NA"'. selected('NA', $bps_db_backup_start_time_hour).'>'.__('N/A', 'bulletproof-security').'</option>';
	echo '<option value="12AM"'. selected('12AM', $bps_db_backup_start_time_hour).'>'.__('12AM', 'bulletproof-security').'</option>';
	echo '<option value="1AM"'. selected('1AM', $bps_db_backup_start_time_hour).'>'.__('1AM', 'bulletproof-security').'</option>';
	echo '<option value="2AM"'. selected('2AM', $bps_db_backup_start_time_hour).'>'.__('2AM', 'bulletproof-security').'</option>';
	echo '<option value="3AM"'. selected('3AM', $bps_db_backup_start_time_hour).'>'.__('3AM', 'bulletproof-security').'</option>';
	echo '<option value="4AM"'. selected('4AM', $bps_db_backup_start_time_hour).'>'.__('4AM', 'bulletproof-security').'</option>';
	echo '<option value="5AM"'. selected('5AM', $bps_db_backup_start_time_hour).'>'.__('5AM', 'bulletproof-security').'</option>';
	echo '<option value="6AM"'. selected('6AM', $bps_db_backup_start_time_hour).'>'.__('6AM', 'bulletproof-security').'</option>';
	echo '<option value="7AM"'. selected('7AM', $bps_db_backup_start_time_hour).'>'.__('7AM', 'bulletproof-security').'</option>';
	echo '<option value="8AM"'. selected('8AM', $bps_db_backup_start_time_hour).'>'.__('8AM', 'bulletproof-security').'</option>';
	echo '<option value="9AM"'. selected('9AM', $bps_db_backup_start_time_hour).'>'.__('9AM', 'bulletproof-security').'</option>';
	echo '<option value="10AM"'. selected('10AM', $bps_db_backup_start_time_hour).'>'.__('10AM', 'bulletproof-security').'</option>';
	echo '<option value="11AM"'. selected('11AM', $bps_db_backup_start_time_hour).'>'.__('11AM', 'bulletproof-security').'</option>';
	echo '<option value="12PM"'. selected('12PM', $bps_db_backup_start_time_hour).'>'.__('12PM', 'bulletproof-security').'</option>';
	echo '<option value="1PM"'. selected('1PM', $bps_db_backup_start_time_hour).'>'.__('1PM', 'bulletproof-security').'</option>';
	echo '<option value="2PM"'. selected('2PM', $bps_db_backup_start_time_hour).'>'.__('2PM', 'bulletproof-security').'</option>';
	echo '<option value="3PM"'. selected('3PM', $bps_db_backup_start_time_hour).'>'.__('3PM', 'bulletproof-security').'</option>';
	echo '<option value="4PM"'. selected('4PM', $bps_db_backup_start_time_hour).'>'.__('4PM', 'bulletproof-security').'</option>';
	echo '<option value="5PM"'. selected('5PM', $bps_db_backup_start_time_hour).'>'.__('5PM', 'bulletproof-security').'</option>';
	echo '<option value="6PM"'. selected('6PM', $bps_db_backup_start_time_hour).'>'.__('6PM', 'bulletproof-security').'</option>';
	echo '<option value="7PM"'. selected('7PM', $bps_db_backup_start_time_hour).'>'.__('7PM', 'bulletproof-security').'</option>';
	echo '<option value="8PM"'. selected('8PM', $bps_db_backup_start_time_hour).'>'.__('8PM', 'bulletproof-security').'</option>';
	echo '<option value="9PM"'. selected('9PM', $bps_db_backup_start_time_hour).'>'.__('9PM', 'bulletproof-security').'</option>';
	echo '<option value="10PM"'. selected('10PM', $bps_db_backup_start_time_hour).'>'.__('10PM', 'bulletproof-security').'</option>';
	echo '<option value="11PM"'. selected('11PM', $bps_db_backup_start_time_hour).'>'.__('11PM', 'bulletproof-security').'</option>';
	echo '</select><br><br>';	

	echo '<label for="bps-dbb">'.__('Day of Week When Scheduled Backup is Run (recurring)', 'bulletproof-security').'</label><br>';
	echo '<select name="dbb_backup_job_start_time_weekday" class="form-340">';
	echo '<option value="NA"'. selected('NA', $bps_db_backup_start_time_weekday).'>'.__('N/A', 'bulletproof-security').'</option>';
	echo '<option value="Sunday"'. selected('Sunday', $bps_db_backup_start_time_weekday).'>'.__('Sunday', 'bulletproof-security').'</option>';
	echo '<option value="Monday"'. selected('Monday', $bps_db_backup_start_time_weekday).'>'.__('Monday', 'bulletproof-security').'</option>';
	echo '<option value="Tuesday"'. selected('Tuesday', $bps_db_backup_start_time_weekday).'>'.__('Tuesday', 'bulletproof-security').'</option>';
	echo '<option value="Wednesday"'. selected('Wednesday', $bps_db_backup_start_time_weekday).'>'.__('Wednesday', 'bulletproof-security').'</option>';
	echo '<option value="Thursday"'. selected('Thursday', $bps_db_backup_start_time_weekday).'>'.__('Thursday', 'bulletproof-security').'</option>';
	echo '<option value="Friday"'. selected('Friday', $bps_db_backup_start_time_weekday).'>'.__('Friday', 'bulletproof-security').'</option>';
	echo '<option value="Saturday"'. selected('Saturday', $bps_db_backup_start_time_weekday).'>'.__('Saturday', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-dbb">'.__('Day of Month When Scheduled Backup is Run (recurring)', 'bulletproof-security').'</label><br>';
	echo '<select name="dbb_backup_job_start_time_month_date" class="form-340">';
	echo '<option value="NA"'. selected('NA', $bps_db_backup_start_time_month_date).'>'.__('N/A', 'bulletproof-security').'</option>';
	echo '<option value="1"'. selected('1', $bps_db_backup_start_time_month_date).'>'.__('1st', 'bulletproof-security').'</option>';
	echo '<option value="2"'. selected('2', $bps_db_backup_start_time_month_date).'>'.__('2nd', 'bulletproof-security').'</option>';
	echo '<option value="3"'. selected('3', $bps_db_backup_start_time_month_date).'>'.__('3rd', 'bulletproof-security').'</option>';
	echo '<option value="4"'. selected('4', $bps_db_backup_start_time_month_date).'>'.__('4th', 'bulletproof-security').'</option>';
	echo '<option value="5"'. selected('5', $bps_db_backup_start_time_month_date).'>'.__('5th', 'bulletproof-security').'</option>';
	echo '<option value="6"'. selected('6', $bps_db_backup_start_time_month_date).'>'.__('6th', 'bulletproof-security').'</option>';
	echo '<option value="7"'. selected('7', $bps_db_backup_start_time_month_date).'>'.__('7th', 'bulletproof-security').'</option>';
	echo '<option value="8"'. selected('8', $bps_db_backup_start_time_month_date).'>'.__('8th', 'bulletproof-security').'</option>';
	echo '<option value="9"'. selected('9', $bps_db_backup_start_time_month_date).'>'.__('9th', 'bulletproof-security').'</option>';
	echo '<option value="10"'. selected('10', $bps_db_backup_start_time_month_date).'>'.__('10th', 'bulletproof-security').'</option>';
	echo '<option value="11"'. selected('11', $bps_db_backup_start_time_month_date).'>'.__('11th', 'bulletproof-security').'</option>';
	echo '<option value="12"'. selected('12', $bps_db_backup_start_time_month_date).'>'.__('12th', 'bulletproof-security').'</option>';
	echo '<option value="13"'. selected('13', $bps_db_backup_start_time_month_date).'>'.__('13th', 'bulletproof-security').'</option>';
	echo '<option value="14"'. selected('14', $bps_db_backup_start_time_month_date).'>'.__('14th', 'bulletproof-security').'</option>';
	echo '<option value="15"'. selected('15', $bps_db_backup_start_time_month_date).'>'.__('15th', 'bulletproof-security').'</option>';
	echo '<option value="16"'. selected('16', $bps_db_backup_start_time_month_date).'>'.__('16th', 'bulletproof-security').'</option>';
	echo '<option value="17"'. selected('17', $bps_db_backup_start_time_month_date).'>'.__('17th', 'bulletproof-security').'</option>';
	echo '<option value="18"'. selected('18', $bps_db_backup_start_time_month_date).'>'.__('18th', 'bulletproof-security').'</option>';
	echo '<option value="19"'. selected('19', $bps_db_backup_start_time_month_date).'>'.__('19th', 'bulletproof-security').'</option>';
	echo '<option value="20"'. selected('20', $bps_db_backup_start_time_month_date).'>'.__('20th', 'bulletproof-security').'</option>';
	echo '<option value="21"'. selected('21', $bps_db_backup_start_time_month_date).'>'.__('21st', 'bulletproof-security').'</option>';
	echo '<option value="22"'. selected('22', $bps_db_backup_start_time_month_date).'>'.__('22nd', 'bulletproof-security').'</option>';
	echo '<option value="23"'. selected('23', $bps_db_backup_start_time_month_date).'>'.__('23rd', 'bulletproof-security').'</option>';
	echo '<option value="24"'. selected('24', $bps_db_backup_start_time_month_date).'>'.__('24th', 'bulletproof-security').'</option>';
	echo '<option value="25"'. selected('25', $bps_db_backup_start_time_month_date).'>'.__('25th', 'bulletproof-security').'</option>';
	echo '<option value="26"'. selected('26', $bps_db_backup_start_time_month_date).'>'.__('26th', 'bulletproof-security').'</option>';
	echo '<option value="27"'. selected('27', $bps_db_backup_start_time_month_date).'>'.__('27th', 'bulletproof-security').'</option>';
	echo '<option value="28"'. selected('28', $bps_db_backup_start_time_month_date).'>'.__('28th', 'bulletproof-security').'</option>';
	echo '<option value="29"'. selected('29', $bps_db_backup_start_time_month_date).'>'.__('29th', 'bulletproof-security').'</option>';
	echo '<option value="30"'. selected('30', $bps_db_backup_start_time_month_date).'>'.__('30th', 'bulletproof-security').'</option>';
	echo '</select><br><br>';	

	echo '<label for="bps-dbb">'.__('Send Scheduled Backup Zip File Via Email or Just Email Only:', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Note: Check with your email provider for the maximum<br>file attachment size limit that is allowed by your Mail Server', 'bulletproof-security').'</strong></font></label><br>';
	echo '<select name="dbb_backup_email_zip" class="form-340">';
	echo '<option value="No"'. selected('No', $bps_db_backup_email_zip).'>'.__('Do Not Email Zip Backup File', 'bulletproof-security').'</option>';
	echo '<option value="Delete"'. selected('Delete', $bps_db_backup_email_zip).'>'.__('Email & Delete Zip Backup File', 'bulletproof-security').'</option>';
	echo '<option value="Yes"'. selected('Yes', $bps_db_backup_email_zip).'>'.__('Email Zip Backup File', 'bulletproof-security').'</option>';
	echo '<option value="EmailOnly"'. selected('EmailOnly', $bps_db_backup_email_zip).'>'.__('Send Email Only & Not Zip Backup File', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-dbb">'.__('Automatically Delete Old Backup Files:', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Independent Option:', 'bulletproof-security').'</strong></font></label><br>';
	echo '<select name="dbb_backup_delete" class="form-340">';
	echo '<option value="Never"'. selected('Never', $bps_db_backup_delete).'>'.__('Never Delete Old Backup Files', 'bulletproof-security').'</option>';
	echo '<option value="1"'. selected('1', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 1 Day', 'bulletproof-security').'</option>';
	echo '<option value="5"'. selected('5', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 5 Days', 'bulletproof-security').'</option>';
	echo '<option value="10"'. selected('10', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 10 Days', 'bulletproof-security').'</option>';
	echo '<option value="15"'. selected('15', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 15 Days', 'bulletproof-security').'</option>';
	echo '<option value="30"'. selected('30', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 30 Days', 'bulletproof-security').'</option>';
	echo '<option value="60"'. selected('60', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 60 Days', 'bulletproof-security').'</option>';
	echo '<option value="90"'. selected('90', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 90 Days', 'bulletproof-security').'</option>';
	echo '<option value="180"'. selected('180', $bps_db_backup_delete).'>'.__('Delete Backup Files Older Than 180 Days', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-dbb">'.__('Turn On|Off All Scheduled Backups (Override):', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Independent Option:', 'bulletproof-security').'</strong></font></label><br>';
	echo '<select name="dbb_backup_on_off" class="form-340">';
	echo '<option value="On"'. selected('On', $bps_db_backup).'>'.__('All Scheduled Backups On', 'bulletproof-security').'</option>';
	echo '<option value="Off"'. selected('Off', $bps_db_backup).'>'.__('All Scheduled Backups Off', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo "<p><input type=\"submit\" name=\"Submit-DBB-Create-Job\" value=\"".esc_attr__('Create Backup Job|Save Settings', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Click OK to Create this Backup Job or click Cancel', 'bulletproof-security')."')\" /></p></form>";

	echo '</div>';
	echo '</td>';
	echo '<td style="border:none">';
	echo '<div id="DBBOptions" style="margin:0px 0px 0px 0px;float:left;">';
	
	// Form: Rename|Create|Reset DB Backup Folder Location and DB Backup File Download Link|URL
	// Notes: If an external/remote form is submitted the WP nonce is not checked.
	// Adding validation and sanitization directly in the form input field protects against external/remote form exploits.
	// $_POST['Submit-DBB-Reset'] == true condition added in admin.php return if == true
	$str = '1234567890abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$db_backup_folder_obs = 'backups_' . substr( str_shuffle($str), 0, 15 );
	$DBBFolderReset = ( isset( $_POST['DBBFolderReset'] ) ) ? $_POST['DBBFolderReset'] : $db_backup_folder_obs;
	
	echo '<form name="bpsDBBackupReset" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_db_backup_reset');
	
	echo '<label for="bps-dbb">'.__('Rename|Create|Reset DB Backup Folder Name:', 'bulletproof-security').'</label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Randomly Generated New DB Backup Folder Name.', 'bulletproof-security').'</strong></font></label><br>';
	echo '<label for="bps-dbb"><font color="#2ea2cc"><strong>'.__('Valid Folder Naming Characters: a-z A-Z 0-9 - _', 'bulletproof-security').'</strong></font></label><br>';
	echo '<input type="text" name="DBBFolderReset" class="regular-text-short-fixed" style="width:325px;margin:0px 0px 10px 0px;" value="'; echo esc_html(trim(stripslashes($DBBFolderReset))); echo '" /><br>';
	
	echo "<p><input type=\"submit\" name=\"Submit-DBB-Reset\" value=\"".esc_attr__('Rename|Create|Reset', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('The Rename|Create|Reset Tool renames the DB Backup folder if it already exists or creates a new DB Backup folder if it does not already exist.\n\n-------------------------------------------------------------\n\nIf you have DB Backup files they will not be affected/changed. The DB Backup File Download Link|URL path will also be changed and have the new DB Backup folder name in the URL path.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" /></p></form>";

	echo '</div>';
	echo '</td>';
	echo '</tr>';	
	echo '</tbody>';
	echo '</table>';	
	
	echo '</div>'; // #dbb-special

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#DBBcheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallDBB').click(function() {
		$(this).parents('#DBBcheckall:eq(0)').find('.dbbtablesALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</div>
</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-2" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('DB Backup Log ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Logs Backup Job Settings, Completion Time, Memory Usage, Zip Backup File Name, Timestamp...', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a></div>'; echo $text; ?> 
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('DB Backup Log', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content2" class="bps-dialog-hide" title="<?php _e('DB Backup Log', 'bulletproof-security'); ?>">
	<p><?php echo $bps_modal_content2; ?></p>
</div>

<?php

// Get File Size of the DB Backup Log File
function bpsPro_DBB_LogSize() {
	$filename = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';

	if ( file_exists($filename) ) {
		$logSize = filesize($filename);
		
		if ( $logSize < 2097152 ) {
			$text = '<span style="font-size:13px;"><strong>'. __('DB Backup Log File Size: ', 'bulletproof-security').'<font color="#2ea2cc">'. round($logSize / 1024, 2) .' KB</font></strong></span><br>';
			echo $text;
		} else {
			$text = '<span style="font-size:13px;"><strong>'. __('DB Backup Log File Size: ', 'bulletproof-security').'<font color="#fb0101">'. round($logSize / 1024, 2) .' KB<br>'.__('The Email Logging options will only send log files up to 2MB in size.', 'bulletproof-security').'</font></strong><br>'.__('Copy and paste the DB Backup Log file contents into a Notepad text file on your computer and save it.', 'bulletproof-security').'<br>'.__('Then click the Delete Log button to delete the contents of this Log file.', 'bulletproof-security').'</span><br>';		
			echo $text;
		}
	}
}
bpsPro_DBB_LogSize();

// Get the Current/Last Modifed Date of the DB Backup Log File
function bpsPro_DBB_Log_LastMod() {
	$filename = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';

	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$timestamp = date_i18n(get_option('date_format').' - '.get_option('time_format'), @filemtime($filename) + $gmt_offset);

	$text = '<strong>'. __('DB Backup Log Last Modified Time: ', 'bulletproof-security').'<font color="#2ea2cc">'.$timestamp.'</font></strong><br><br>';
	echo $text;
	}
}
bpsPro_DBB_Log_LastMod();

if ( isset( $_POST['Submit-Delete-DBB-Log'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_delete_dbb_log' );

	$DBBLog = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
	$DBBLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/db_backup_log.txt';
	
	copy($DBBLogMaster, $DBBLog);
	echo $bps_topDiv;
	$text = '<font color="green"><strong>'.__('Success! Your DB Backup Log file has been deleted and replaced with a new blank DB Backup Log file.', 'bulletproof-security').'</strong></font>';
	echo $text;	
	echo $bps_bottomDiv;
}
?>

<form name="DeleteDBBLogForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php#bps-tabs-2' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_delete_dbb_log'); ?>
<input type="submit" name="Submit-Delete-DBB-Log" value="<?php esc_attr_e('Delete Log', 'bulletproof-security') ?>" class="button bps-button" style="margin:15px 0px 15px 0px" onclick="return confirm('<?php $text = __('Clicking OK will delete the contents of your DB Backup Log file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Delete the Log file contents or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>

<div id="messageinner" class="updatedinner">
<?php

// Get DB Backup log file contents
function bpsPro_DBB_get_contents() {
	
	if ( current_user_can('manage_options') ) {
		$dbb_log = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
		$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

		if ( file_exists($dbb_log) ) {
			$dbb_log = file_get_contents($dbb_log);
			return htmlspecialchars($dbb_log);
		
		} else {
		
		_e('The DB Backup Log File Was Not Found! Check that the file really exists here - /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/logs/db_backup_log.txt and is named correctly.', 'bulletproof-security');
		}
	}
}

// Form: DB Backup Log editor
if ( current_user_can('manage_options') ) {
	$dbb_log = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
	$write_test = "";
	
	if ( is_writable($dbb_log) ) {
		if ( !$handle = fopen($dbb_log, 'a+b' ) ) {
		exit;
		}
		
		if ( fwrite($handle, $write_test) === FALSE ) {
		exit;
		}
		
		$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your DB Backup Log file is writable.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		}
	}
	
	if ( isset( $_POST['Submit-DBB-Log'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_dbb_log' );
		$newcontentdbb = stripslashes( $_POST['newcontentdbb'] );
	
		if ( is_writable($dbb_log) ) {
			$handle = fopen($dbb_log, 'w+b');
			fwrite($handle, $newcontentdbb);
		$text = '<font color="green style="font-size:12px;""><strong>'.__('Success! Your DB Backup Log file has been updated.', 'bulletproof-security').'</strong></font><br>';
		echo $text;	
		fclose($handle);
		}
	}

	$scrolltodbblog = isset($_REQUEST['scrolltodbblog']) ? (int) $_REQUEST['scrolltodbblog'] : 0;
?>
</div>

<div id="QLogEditor">
<form name="DBBLog" id="DBBLog" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php#bps-tabs-2' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_dbb_log'); ?>
<div id="DBBLog">
    <textarea class="bps-text-area-600x700" name="newcontentdbb" id="newcontentdbb" tabindex="1"><?php echo bpsPro_DBB_get_contents(); ?></textarea>
	<input type="hidden" name="scrolltodbblog" id="scrolltodbblog" value="<?php echo esc_html( $scrolltodbblog ); ?>" />
    <p class="submit">
	<input type="submit" name="Submit-DBB-Log" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#DBBLog').submit(function(){ $('#scrolltodbblog').val( $('#newcontentdbb').scrollTop() ); });
	$('#newcontentdbb').scrollTop( $('#scrolltodbblog').val() ); 
});
/* ]]> */
</script>
</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-3" class="bps-tab-page">

<div id="DB-Table-Prefix-Changer-Table">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title">
<?php $text = '<h2>'.__('DB Table Prefix Changer ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Tools to change & check your WordPress Database Table Prefix', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a></div>'; echo $text; ?>
    </td>
  </tr>
  <tr>
    <td width="50%" valign="top" class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('DB Table Prefix Changer', 'bulletproof-security'); ?>  <button id="bps-open-modal3" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content3" class="bps-dialog-hide" title="<?php _e('DB Table Prefix Changer', 'bulletproof-security'); ?>">
	<p><?php echo $bps_modal_content3; ?>
</div>

<?php
	if ( is_admin() && current_user_can('manage_options') && preg_match( '/page=bulletproof-security/', esc_html( $_SERVER['REQUEST_URI'] ) ) ) {	

echo '<div id="DBPrefixText" style="width:90%;padding-bottom:20px;">';
$text = '<span style="font-size:1.13em;">'.__('Your current WordPress Database Table Prefix is: ', 'bulletproof-security').'<strong><font color="#2ea2cc">'.$wpdb->base_prefix .'</span><br><br><span class="bps-dbb-small-text">'.__('NOTES: ', 'bulletproof-security').'<br>'.__('1. It is recommended that you backup your database before using this tool.', 'bulletproof-security').'<br>'.__('2. If you want to create your own DB Table Prefix name or add additional characters to the randomly generated DB Table Prefix name below then ONLY use lowercase letters, numbers and underscores in your DB Table Prefix name.', 'bulletproof-security').'<br>'.__('3. The maximum length limitation of a DB Table name, including the table prefix is 64 characters. See the DB Table Names & Character Length Table to the right.', 'bulletproof-security').'<br>'.__('4. To change your DB Table Prefix name back to the WordPress default DB Table Prefix name, enter wp_ for the DB Table Prefix name.', 'bulletproof-security').'</span></font></strong>';
echo $text;
echo '</div>';

// Form: DB Table Prefix Changer
if ( isset( $_POST['Submit-DB-Table-Prefix'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_table_prefix_changer' );
	set_time_limit(300);

	if ( preg_match( '|[^a-z0-9_]|', $_POST['DBTablePrefix'] ) ) {
		
		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('ERROR: The DB Table Prefix name can only contain numbers, lowercase letters, and underscores.', 'bulletproof-security').'</font></strong>';
		echo $text;
		echo $bps_bottomDiv;	
	return;
	
	} else {
	
		$DBTablePrefix = $_POST['DBTablePrefix'];
	}	
	
	$wpconfig_file = ABSPATH . 'wp-config.php';
	
	if ( ! file_exists($wpconfig_file) ) {
		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('A wp-config.php file was NOT found in your website root folder.', 'bulletproof-security').'</font><br>'.__('Your DB Table Prefix was not changed. If you have moved your wp-config.php file to a another Server folder then you can use this tool to change your DB Table Prefix, but first you will need to temporarily move your wp-config.php file back to the default location: your WordPress website root folder.', 'bulletproof-security').'</strong>';
		echo $text;
		echo $bps_bottomDiv;
	}

	if ( file_exists($wpconfig_file) ) {

		$permswpconfig = @substr(sprintf('%o', fileperms($wpconfig_file)), -4);
		$sapi_type = php_sapi_name();
		$lock = '';
	
		if 	( @$permswpconfig == '0400') {
			$lock = '0400';			
		}

		if ( @substr( $sapi_type, 0, 6 ) != 'apache' || @$permswpconfig != '0666' || @$permswpconfig != '0777' ) { // Windows IIS, XAMPP, etc
			@chmod($wpconfig_file, 0644);
		}

		if ( ! is_writable($wpconfig_file) ) {
			echo $bps_topDiv;
			$text = '<strong><font color="#fb0101">'.__('Error: The wp-config.php file is not writable. Unable to write to the wp-config.php file.', 'bulletproof-security').'</font><br>'.__('Your DB Table Prefix was not changed. You will need to make the wp-config.php file writable first by changing either the file permissions or Ownership of the wp-config.php file (if you have a DSO Server) before you can use the DB Table Prefix Changer tool to change your DB Table Prefix.', 'bulletproof-security').'</strong>';
			echo $text;
			echo $bps_bottomDiv;
		return;
		}

		$base_prefix = $wpdb->base_prefix;
		$MetaKeys = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->usermeta WHERE meta_key LIKE %s", "$base_prefix%" ) );
		$userRoles = '_user_roles';
		$UserRolesRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s", "%$userRoles" ) );
		$DBTables = '';
		$getDBTables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLE STATUS WHERE Name != %s", $DBTables ) );
		
		foreach ( $getDBTables as $Table ) {
			$new_table_name = preg_replace( "/^$wpdb->base_prefix/", $DBTablePrefix, $Table->Name );
			$rename_table = $wpdb->query( "RENAME TABLE $Table->Name TO $new_table_name" );
		}
				
		foreach ( $UserRolesRows as $data ) {
			$update_user_roles = $wpdb->update( $DBTablePrefix . 'options', array( 'option_name' => $DBTablePrefix . 'user_roles' ), array( 'option_name' => $data->option_name ) );
		}
	
		foreach ( $MetaKeys as $mdata ) {
			$new_meta_key = preg_replace( "/^$wpdb->base_prefix/", $DBTablePrefix, $mdata->meta_key );
			$update_meta_keys = $wpdb->update( $DBTablePrefix . 'usermeta', array( 'meta_key' => $new_meta_key ), array( 'meta_key' => $mdata->meta_key ) );
		}
	
		$contents = file_get_contents($wpconfig_file);
		$pattern = '/\$table_prefix(.*)=(.*);/';
	
			$stringReplace = @file_get_contents($wpconfig_file);
		
		if ( preg_match( $pattern, $contents, $matches ) ) {
			$stringReplace = preg_replace('/\$table_prefix(.*)=(.*);/', "\$table_prefix = '$DBTablePrefix';", $stringReplace);
		}	
	
		if ( file_put_contents( $wpconfig_file, $stringReplace ) ) {
		
			if ( $lock == '0400' ) {	
				@chmod($wpconfig_file, 0400);
			}
		}
			
		if ( ! is_multisite() ) {
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('DB Table Prefix Name change completed. Click the Load|Refresh Table button to load/refresh the DB Table Names & Character Length Table if you would like to check the new DB Table Prefix Name Changes.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;

		} else {
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Click the Update Site User Roles button to complete the DB Table Prefix Name change.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;			
		}
	} // end if ( file_exists($filename) ) {
}

	// Random DB Table Prefix Name generator
	// Notes: If an external/remote form is submitted the WP nonce is not checked.
	// Adding validation and sanitization directly in the form input field protects against external/remote form exploits.
	$str = '1234567890abcdefghijklmnopqrstuvxyz';
	$prefix_obs = substr( str_shuffle($str), 0, 6 ).'_';
	$DBTablePrefix = ( isset( $_POST['DBTablePrefix'] ) ) ? $_POST['DBTablePrefix'] : $prefix_obs;
?>

<form name="bpsTablePrefixChanger" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php#bps-tabs-3' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_table_prefix_changer'); ?>
    <div>
    <strong><label for="bpsTablePrefix"><?php _e('Randomly Generated DB Table Prefix', 'bulletproof-security'); ?></label></strong><br />  
    <input type="text" name="DBTablePrefix" value="<?php if ( isset( $_POST['DBTablePrefix'] ) && preg_match( '|[^a-z0-9_]|', $_POST['DBTablePrefix'] ) ) { echo esc_html($prefix_obs); } else { echo esc_html($DBTablePrefix); } ?>" class="table-prefix-changer" /> <br />
    <p class="submit">
    <input type="submit" name="Submit-DB-Table-Prefix" value="<?php esc_attr_e('Change DB Table Prefix', 'bulletproof-security') ?>" class="button bps-button" onclick="bpsSpinnerTablePrefix()" />
    </p>
	</div>
</form>

<?php
// Form: Network|Mulsite Update Site User Roles
if ( isset( $_POST['Submit-DB-Table-Prefix-Network'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_table_prefix_changer_net' );
	set_time_limit(300);
	
	if ( is_multisite() ) {
		
		$userRoles = '_user_roles';
		$network_ids = wp_get_sites();

		foreach ( $network_ids as $key => $value ) {
			
			$net_id = $value['blog_id'];
			
			if ( $net_id != '1' ) {
			
				$network_options_tables = $wpdb->base_prefix . $net_id . '_options'; 
				$NetUserRolesRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $network_options_tables WHERE option_name LIKE %s", "%$userRoles" ) );

				foreach ( $NetUserRolesRows as $data ) {

					$new_user_roles = preg_replace( "/$data->option_name/", $wpdb->base_prefix . $net_id . '_user_roles', $data->option_name );
					$network_update_user_roles = $wpdb->update( $network_options_tables, array( 'option_name' => $new_user_roles ), array( 'option_name' => $data->option_name ) );						
				}
			}
		}
	
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('DB Table Prefix Name change completed. Click the Load|Refresh Table button to load/refresh the DB Table Names & Character Length Table if you would like to check the new DB Table Prefix Name Changes. If you have setup the DB Monitor to monitor database tables then setup the DB Monitor again with your new DB Table names.', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo $bps_bottomDiv;			
	}
}
?>

<?php if ( is_multisite() ) { ?>

<form name="bpsTablePrefixChangerNetwork" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php#bps-tabs-3' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_table_prefix_changer_net'); ?>
<div>
<strong><label for="bpsTablePrefix" style="margin:0px 0px 10px 15px;"><?php _e('This button must be clicked AFTER clicking the Change DB Table Prefix button above.', 'bulletproof-security'); ?></label></strong><br />
<input type="submit" name="Submit-DB-Table-Prefix-Network" value="<?php esc_attr_e('Update Site User Roles', 'bulletproof-security') ?>" class="button bps-button" style="margin:5px 0px 20px 15px;" onclick="bpsSpinnerTablePrefix2()" />
</div>
</form>

<?php } ?>

</td>
    <td width="50%" valign="top" class="bps-table_cell_help">

<?php
// Form: DB Table Names & Character Length Table
function bpsPro_table_status_length() {
global $wpdb, $bps_topDiv, $bps_bottomDiv;
	
	if ( isset( $_POST['Submit-DB-Prefix-Table-Refresh'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_db_prefix_refresh' );
	
		$base_prefix = $wpdb->base_prefix;
		$DBTables = '';
		$getDBTables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLE STATUS WHERE Name != %s", $DBTables ) );

		echo '<div id="DBPrefixStatus1" style="margin:0px 0px 20px 0px;overflow:auto;width:100%;height:200px;border:1px solid black;">';
		echo '<table style="text-align:left;border-right:1px solid black;padding:5px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:250px;font-size:1.13em;border-bottom:1px solid black;background-color:transparent;"><strong>'.__('DB Table Name', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:400px;font-size:1.13em;border-bottom:1px solid black;background-color:transparent;"><strong>'.__('Length', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
	
		foreach ( $getDBTables as $Tabledata ) {
	
			echo '<td>'.$Tabledata->Name.'</td>';
			echo '<td>'.strlen($Tabledata->Name).'</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

		$userRoles = '_user_roles';
		$UserRolesRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s", "%$userRoles" ) );
		$MetaKeys = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->usermeta WHERE meta_key LIKE %s", "$base_prefix%" ) );	
	
		echo '<div id="DBPrefixStatus2" style="margin:0px 0px 20px 0px;overflow:auto;width:100%;height:200px;border:1px solid black;">';
		echo '<table style="text-align:left;border-right:1px solid black;padding:5px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:250px;font-size:1.13em;border-bottom:1px solid black;background-color:transparent;"><strong>'.__('DB Table Name|Column', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:400px;font-size:1.13em;border-bottom:1px solid black;background-color:transparent;"><strong>'.__('Other Prefix Changes', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
	
		foreach ( $UserRolesRows as $data ) {
	
		echo '<td>'.$wpdb->options.' | option_name</td>';
		echo '<td>'.$data->option_name.'</td>';
		echo '</tr>';
		}
	
		if ( is_multisite() ) {
			
		echo '<tr>';
		
			$network_ids = wp_get_sites();
	
			foreach ( $network_ids as $key => $value ) {
				
				$net_id = $value['blog_id'];
				
				if ( $net_id != '1' ) {
				
					$network_options_tables = $base_prefix . $net_id . '_options';
					$NetUserRolesRows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $network_options_tables WHERE option_name LIKE %s", "%$userRoles" ) );
	
					foreach ( $NetUserRolesRows as $data ) {
		
						echo '<td>'.$network_options_tables.' | option_name</td>';
						echo '<td>'.$data->option_name.'</td>';
						echo '</tr>';
					}
				}
			}
		}

		echo '<tr>';
		
		foreach ( $MetaKeys as $mdata ) {
		
			if ( preg_match( "/^$wpdb->base_prefix/", $mdata->meta_key, $matches ) ) {
			
			echo '<td>'.$wpdb->usermeta.' | meta_key</td>';
			echo '<td>'.'User ID: '.$mdata->user_id.' '.$mdata->meta_key.'</td>';
			echo '</tr>';
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
	}
}

	// Form: DB Table Names & Character Length Table - needs to be a clickable form otherwise causes slowness on large websites if query is running
	echo '<div id="DB-Prefix-Table-Refresh-Button" style="margin:0px 0px 20px 0px;">';
	echo '<h3 style="margin:0px 0px -5px 0px;">'.__('DB Table Names & Character Length Table', 'bulletproof-security').'</h3>';
	echo '<h4><font color="#2ea2cc">'.__('Displays your Current DB Table Names & Length Including The DB Table Prefix', 'bulletproof-security').'</font></h4>';
	echo '<form name="DB-Prefix-Table-Refresh" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php#bps-tabs-3' ).'" method="post">';
	wp_nonce_field('bulletproof_security_db_prefix_refresh');
	echo "<p><input type=\"submit\" name=\"Submit-DB-Prefix-Table-Refresh\" value=\"".esc_attr__('Load|Refresh Table', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"bpsSpinnerTableRefresh()\" /></p>";
	bpsPro_table_status_length(); 
	echo "</form>";
	echo '</div>';
	
}// end if ( is_admin() && current_user_can('manage_options')...	

?>

<?php
if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#DBPrefixStatus1 tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<?php
if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#DBPrefixStatus2 tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

    </td>
  </tr>
</table>

</div>
</div>

<div id="bps-tabs-4" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    <a href="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-10' ); ?>" target="_blank"><?php _e('Whats New in ', 'bulletproof-security'); echo BULLETPROOF_VERSION; ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/bulletproof-security-pro-version-release-dates/" target="_blank"><?php _e('BPS Pro Features & Version Release Dates', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/video-tutorials/" target="_blank"><?php _e('Video Tutorials', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/database-backup-security-guide/" target="_blank"><?php _e('DB Backup & Security Guide & Troubleshooting', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a>
    </td>
  </tr>
</table>
</div>
            
<div id="AITpro-link">BulletProof Security Pro <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://forum.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
<style>
<!--
.bps-spinner {visibility:hidden;}
-->
</style>
</div>