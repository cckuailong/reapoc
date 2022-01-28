<!-- force the vertical scrollbar -->
<style>
#wpwrap{min-height:100.1%};
div.notice{display:none}
</style>

<div id="bps-container" class="wrap" style="margin:45px 20px 5px 0px;">

<!-- MUST be in my page container div. hide notices from other plugins so they don't break the MScan iFrame-->
<style>
div.notice{display:none}
</style>

<noscript><div id="message" class="updated" style="font-weight:600;font-size:13px;padding:5px;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><span style="color:blue">BPS Warning: JavaScript is disabled in your Browser</span><br />BPS plugin pages will not display visually correct and all BPS JavaScript functionality will not work correctly.</div></noscript>

<?php
		echo '<div class="bps-star-container">';
		echo '<div class="bps-star"><img src="'.plugins_url('/bulletproof-security/admin/images/star.png').'" /></div>';
		echo '<div class="bps-downloaded">';
		echo '<div class="bps-star-link"><a href="https://wordpress.org/support/view/plugin-reviews/bulletproof-security#postform" target="_blank" title="Add a Star Rating for the BPS plugin">'.__('Rate BPS', 'bulletproof-security').'</a><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Upgrade to BulletProof Security Pro">Upgrade to Pro</a></div>';
		echo '</div>';
		echo '</div>';

## 2.9: Created new file for mscan pattern matching code. If web host deletes or nulls that file or Dir then mscan will not work, but BPS Pro will still work.
## 4.8: Major rebuild: Plugin and Theme files will be checked using MD5 file hash comparisons. Will still offer pattern matching, but am recommending 
## Premium/custom plugin and theme zip uploads so that MD5 hashes can be made from those plugin and theme files.
## MScan pattern matching code is now saved in the DB.
function bpsPro_mscan_pattern_match_file_check() {
	
	$mscan_db_pattern_match_options = get_option('bulletproof_security_options_mscan_patterns');
	
		if ( ! empty($mscan_db_pattern_match_options['mscan_pattern_match_files']) ) {
		
			foreach ( $mscan_db_pattern_match_options['mscan_pattern_match_files'] as $key => $value ) {
				
				foreach ( $value as $inner_key => $inner_value ) {
					
					if ( $inner_key == 'js_patterns' ) {
						$js_pattern = $inner_value;
					}
					if ( $inner_key == 'htaccess_patterns' ) {
						$htaccess_pattern = $inner_value;
					}
					if ( $inner_key == 'php_patterns' ) {
						$php_pattern = $inner_value;
					}
				}
			}
		}
		
		if ( empty($js_pattern) ) {
			$text = '<div id="bps-inpage-message" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin:-7px 0px 10px 0px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('The MScan pattern matching code does not exist in your database.', 'bulletproof-security').'</font><br>'.__('Most likely your web host saw the pattern matching code in the MScan /bulletproof-security/admin/htaccess/mscan-pattern-match.php file as malicious and has either deleted the file or made the file or folder unreadable.', 'bulletproof-security').'<br>'.__('Unfortunately that means you will not be able to use MScan on your website/server/web host.', 'bulletproof-security').'</div>';
			echo $text;
	}
}
bpsPro_mscan_pattern_match_file_check();
?>

<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

$bpsSpacePop = '-------------------------------------------------------------';

require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/mscan/mscan-help-text.php';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Replace ABSPATH = wp-content/uploads
$wp_upload_dir = wp_upload_dir();
$bps_uploads_dir = str_replace( ABSPATH, '', $wp_upload_dir['basedir'] );

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_mscan() {
	
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

// Create a new Deny All .htaccess file on first page load with users current IP address to allow the cc-master.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
function bpsPro_Core_mscan_deny_all() {

	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
		
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
		if ( isset( $HFiles_options['bps_htaccess_files'] ) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset( $Zip_download_Options['bps_zip_download_fix'] ) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			return;
		}

		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_mscan()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_mscan()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_mscan()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/mscan/.htaccess';
		
		if ( file_exists($create_denyall_htaccess_file) ) {
			$check_string = @file_get_contents($create_denyall_htaccess_file);
		}
		
		if ( ! file_exists($create_denyall_htaccess_file) ) { 

			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) && ! strpos( $check_string, bpsPro_get_real_ip_address_mscan() ) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}
	}
}

bpsPro_Core_mscan_deny_all();

?>
</div>

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ MScan 2.0 Malware Scanner', 'bulletproof-security'); ?></h2>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('MScan 2.0', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('MScan Log', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-3"><?php _e('MScan Report', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-4"><?php _e('MScan Saved Reports', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-5"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>

<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('MScan 2.0 ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Scans website files & your database for hacker files or code', 'bulletproof-security'); ?></span></h2>
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('MScan 2.0', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('MScan 2.0', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/mscan-malware-scanner-guide/" title="MScan Malware Scanner Guide" target="_blank"><?php _e('MScan Malware Scanner Guide', 'bulletproof-security'); ?></a></strong><br />
	<strong><a href="https://forum.ait-pro.com/forums/topic/mscan-troubleshooting-questions-problems-and-code-posting/" title="MScan Troubleshooting & Code Posting" target="_blank"><?php _e('MScan Troubleshooting & Code Posting', 'bulletproof-security'); ?></a></strong><br />
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-pro/#bps-pro-general-troubleshooting" title="BPS Pro Troubleshooting Steps" target="_blank"><?php _e('BPS Pro Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />
	
	<?php echo $bps_modal_content1; ?></p>
</div>

<style>
#bps-container div.mscan-report-row-small{font-size:1em;margin:0px 0px 10px 0px}
</style>

<script> 
var MscanStatusWindow;

function openWin() {
  MscanStatusWindow = window.open("<?php echo get_site_url(null, '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fmscan%2Fmscan-scan-status.php', null); ?>", "_blank", "toolbar=no,status=yes,titlebar=yes,scrollbars=no,resizable=yes,top=200,left=200,width=1000,height=100");
} 

function closeWin() {
  MscanStatusWindow.close();
}
</script> 

<?php
	// Form Processing: Reset MScan: Deletes the bpspro_mscan DB table, saves blank values for the MScan Status DB options. 
	if ( isset( $_POST['Submit-MScan-Reset'] ) && current_user_can('manage_options') ) {
		check_admin_referer('bulletproof_security_mscan_reset');
		
		$MStable_name = $wpdb->prefix . "bpspro_mscan";
		
		$wpdb->query("DROP TABLE IF EXISTS $MStable_name");
	
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $MStable_name ) ) != $MStable_name ) {	
	
			$sql = "CREATE TABLE $MStable_name (
			mscan_id bigint(20) NOT NULL auto_increment,
			mscan_status varchar(8) NOT NULL default '',
			mscan_type varchar(16) NOT NULL default '',
			mscan_path text NOT NULL,
			mscan_pattern text NOT NULL,
			mscan_skipped varchar(7) NOT NULL default '',
			mscan_ignored varchar(6) NOT NULL default '',
			mscan_db_table varchar(64) NOT NULL default '',
			mscan_db_column varchar(64) NOT NULL default '',
			mscan_db_pkid text NOT NULL,
			mscan_time datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY (mscan_id),
			UNIQUE KEY id (mscan_id)
			);";
	
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		}
	
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> '', 
		'bps_mscan_time_stop' 					=> '', 
		'bps_mscan_time_end' 					=> '', 
		'bps_mscan_time_remaining' 				=> '', 
		'bps_mscan_status' 						=> '4', 
		'bps_mscan_last_scan_timestamp' 		=> '', 
		'bps_mscan_total_time' 					=> '', 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> '', 
		'bps_mscan_total_non_image_files' 		=> '', 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> '', 
		'bps_mscan_total_skipped_files' 		=> '', 
		'bps_mscan_total_suspect_files' 		=> '', 
		'bps_mscan_suspect_skipped_files' 		=> '', 
		'bps_mscan_total_suspect_db' 			=> '', 
		'bps_mscan_total_ignored_files' 		=> '',
		'bps_mscan_total_plugin_files' 			=> '', 			 
		'bps_mscan_total_theme_files' 			=> '' 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}

		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('MScan scan results and data has been deleted. Your MScan option settings have not been deleted.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;
	}

	// Form Processing: Delete File Hashes Tool: Deletes the plugin and theme hash DB options 
	// and the no zip download DB option (premium/paid and custom plugins and themes or plugins and themes without a zip version #).
	if ( isset( $_POST['Submit-MScan-Delete-Hashes'] ) && current_user_can('manage_options') ) {
		check_admin_referer('bulletproof_security_mscan_delete_hashes');
		
		delete_option('bulletproof_security_options_mscan_plugin_hash');
		delete_option('bulletproof_security_options_mscan_p_hash_new');
		delete_option('bulletproof_security_options_mscan_theme_hash');
		delete_option('bulletproof_security_options_mscan_t_hash_new');
		delete_option('bulletproof_security_options_mscan_nodownload');

		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('MScan Plugin and Theme file hashes have been deleted. New Plugin and Theme file hashes will be created the next time you run a  scan. You should also click the MScan Reset button after using this tool. Your MScan option settings have not been deleted.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;
	}

	// Form Processing: MScan Stop
	if ( isset( $_POST['Submit-MScan-Stop'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_mscan_stop' );
		
		$MScanStop = WP_CONTENT_DIR . '/bps-backup/master-backups/mscan-stop.txt';
		file_put_contents($MScanStop, "");
		
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		$MScan_options = get_option('bulletproof_security_options_MScan');
		
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
		'bps_mscan_time_stop' 					=> 'stop', 
		'bps_mscan_time_end' 					=> time(), 
		'bps_mscan_time_remaining' 				=> time(), 
		'bps_mscan_status' 						=> '4', 
		'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
		'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $MScan_status['bps_mscan_total_all_scannable_files'], 
		'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
		'bps_mscan_total_suspect_files' 		=> $MScan_status['bps_mscan_total_suspect_files'], 
		'bps_mscan_total_ignored_files' 		=> $MScan_status['bps_mscan_total_ignored_files'],
		'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
		'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}

		$mscan_scan_skipped_files_message = '';
		
		if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
			$mscan_scan_skipped_files_message = '<br><font color="blue"><strong>'.__('Skipped file scanning is turned On. Only skipped files will be scanned.', 'bulletproof-security').'</strong></font>';
		}

		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('MScan scanning has been stopped. Note: The Stop Scan button also stops the Scan Time Estimate Tool from calculating estimated scan time.', 'bulletproof-security').'</strong></font>'.$mscan_scan_skipped_files_message;
		echo $text;	
		echo $bps_bottomDiv;
	}

function bpsPro_mscan_displayed_messages() {
global $bps_topDiv, $bps_bottomDiv;

	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$MScan_options = get_option('bulletproof_security_options_MScan');
	
	$mscan_scan_skipped_files_message = '';

	if ( isset($MScan_options['mscan_scan_skipped_files']) && $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
		
		echo $bps_topDiv;
		$mscan_scan_skipped_files_message = '<br><font color="blue"><strong>'.__('Skipped file scanning is turned On. Only skipped files will be scanned.', 'bulletproof-security').'</strong></font>';
		echo $mscan_scan_skipped_files_message;	
		echo $bps_bottomDiv;
	}
}

bpsPro_mscan_displayed_messages();

	// Form Processing: Scan Time Estimate Tool Form > Start
	if ( isset( $_POST['Submit-MScan-Time-Estimate'] ) && current_user_can('manage_options') ) {
		check_admin_referer('bulletproof_security_mscan_time_estimate');
		
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		$MScan_options = get_option('bulletproof_security_options_MScan');
		$mstime = $MScan_options['mscan_max_time_limit'];
		ini_set('max_execution_time', $mstime);

		$bps_mscan_last_scan_timestamp = ! isset($MScan_status['bps_mscan_last_scan_timestamp']) ? '' : $MScan_status['bps_mscan_last_scan_timestamp'];
		$bps_mscan_total_time = ! isset($MScan_status['bps_mscan_total_time']) ? '' : $MScan_status['bps_mscan_total_time'];
		$bps_mscan_total_wp_core_files = ! isset($MScan_status['bps_mscan_total_wp_core_files']) ? '' : $MScan_status['bps_mscan_total_wp_core_files'];
		$bps_mscan_total_non_image_files = ! isset($MScan_status['bps_mscan_total_non_image_files']) ? '' : $MScan_status['bps_mscan_total_non_image_files'];
		$bps_mscan_total_skipped_files = ! isset($MScan_status['bps_mscan_total_skipped_files']) ? '' : $MScan_status['bps_mscan_total_skipped_files'];
		$bps_mscan_total_suspect_files = ! isset($MScan_status['bps_mscan_total_suspect_files']) ? '' : $MScan_status['bps_mscan_total_suspect_files'];
		$bps_mscan_suspect_skipped_files = ! isset($MScan_status['bps_mscan_suspect_skipped_files']) ? '' : $MScan_status['bps_mscan_suspect_skipped_files'];
		$bps_mscan_total_suspect_db = ! isset($MScan_status['bps_mscan_total_suspect_db']) ? '' : $MScan_status['bps_mscan_total_suspect_db'];
		$bps_mscan_total_ignored_files = ! isset($MScan_status['bps_mscan_total_ignored_files']) ? '' : $MScan_status['bps_mscan_total_ignored_files'];
		$bps_mscan_total_plugin_files = ! isset($MScan_status['bps_mscan_total_plugin_files']) ? '' : $MScan_status['bps_mscan_total_plugin_files'];		
		$bps_mscan_total_theme_files = ! isset($MScan_status['bps_mscan_total_theme_files']) ? '' : $MScan_status['bps_mscan_total_theme_files'];

		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> time(), 
		'bps_mscan_time_stop' 					=> '', 
		'bps_mscan_time_end' 					=> time() + 15, 
		'bps_mscan_time_remaining' 				=> time() + 15,
		'bps_mscan_status' 						=> '1', 
		'bps_mscan_last_scan_timestamp' 		=> $bps_mscan_last_scan_timestamp, 
		'bps_mscan_total_time' 					=> $bps_mscan_total_time, 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $bps_mscan_total_wp_core_files, 
		'bps_mscan_total_non_image_files' 		=> $bps_mscan_total_non_image_files, 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> '', // this needs to be reset/blank on each new scan. extremely large or small file scans have whacky results.
		'bps_mscan_total_skipped_files' 		=> $bps_mscan_total_skipped_files, 
		'bps_mscan_total_suspect_files' 		=> $bps_mscan_total_suspect_files, 
		'bps_mscan_suspect_skipped_files' 		=> $bps_mscan_suspect_skipped_files, 
		'bps_mscan_total_suspect_db' 			=> $bps_mscan_total_suspect_db, 
		'bps_mscan_total_ignored_files' 		=> $bps_mscan_total_ignored_files,
		'bps_mscan_total_plugin_files' 			=> $bps_mscan_total_plugin_files, 			 
		'bps_mscan_total_theme_files' 			=> $bps_mscan_total_theme_files 
		);		
			
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}
}

	// Form Processing: MScan Start
	if ( isset( $_POST['Submit-MScan-Start'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_mscan_start' );
		
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		$MScan_options = get_option('bulletproof_security_options_MScan');
		$mstime = $MScan_options['mscan_max_time_limit'];
		ini_set('max_execution_time', $mstime);

		$bps_mscan_time_start = ! isset($MScan_status['bps_mscan_time_start']) ? '' : $MScan_status['bps_mscan_time_start'];
		$bps_mscan_last_scan_timestamp = ! isset($MScan_status['bps_mscan_last_scan_timestamp']) ? '' : $MScan_status['bps_mscan_last_scan_timestamp'];
		$bps_mscan_total_time = ! isset($MScan_status['bps_mscan_total_time']) ? '' : $MScan_status['bps_mscan_total_time'];
		$bps_mscan_total_wp_core_files = ! isset($MScan_status['bps_mscan_total_wp_core_files']) ? '' : $MScan_status['bps_mscan_total_wp_core_files'];
		$bps_mscan_total_non_image_files = ! isset($MScan_status['bps_mscan_total_non_image_files']) ? '' : $MScan_status['bps_mscan_total_non_image_files'];
		$bps_mscan_total_skipped_files = ! isset($MScan_status['bps_mscan_total_skipped_files']) ? '' : $MScan_status['bps_mscan_total_skipped_files'];
		$bps_mscan_total_suspect_files = ! isset($MScan_status['bps_mscan_total_suspect_files']) ? '' : $MScan_status['bps_mscan_total_suspect_files'];
		$bps_mscan_suspect_skipped_files = ! isset($MScan_status['bps_mscan_suspect_skipped_files']) ? '' : $MScan_status['bps_mscan_suspect_skipped_files'];
		$bps_mscan_total_suspect_db = ! isset($MScan_status['bps_mscan_total_suspect_db']) ? '' : $MScan_status['bps_mscan_total_suspect_db'];
		$bps_mscan_total_ignored_files = ! isset($MScan_status['bps_mscan_total_ignored_files']) ? '' : $MScan_status['bps_mscan_total_ignored_files'];
		$bps_mscan_total_plugin_files = ! isset($MScan_status['bps_mscan_total_plugin_files']) ? '' : $MScan_status['bps_mscan_total_plugin_files'];		
		$bps_mscan_total_theme_files = ! isset($MScan_status['bps_mscan_total_theme_files']) ? '' : $MScan_status['bps_mscan_total_theme_files'];

		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $bps_mscan_time_start, // note: time start does not occur here. It occurs in the scan time estimate function.
		'bps_mscan_time_stop' 					=> '', 
		'bps_mscan_time_end' 					=> time() + 10, 
		'bps_mscan_time_remaining' 				=> time() + 10, // +10 is the calculating scan time countdown. Don't go any lower than +10. 
		'bps_mscan_status' 						=> '1', 		// Time Remaining is updated in the scan time estimate function with the estimated scan time.
		'bps_mscan_last_scan_timestamp' 		=> $bps_mscan_last_scan_timestamp, 
		'bps_mscan_total_time' 					=> $bps_mscan_total_time, 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $bps_mscan_total_wp_core_files, 
		'bps_mscan_total_non_image_files' 		=> $bps_mscan_total_non_image_files, 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> '', // this needs to be reset/blank on each new scan. extremely large or small file scans have whacky results.
		'bps_mscan_total_skipped_files' 		=> $bps_mscan_total_skipped_files, 
		'bps_mscan_total_suspect_files' 		=> $bps_mscan_total_suspect_files, 
		'bps_mscan_suspect_skipped_files' 		=> $bps_mscan_suspect_skipped_files, 
		'bps_mscan_total_suspect_db' 			=> $bps_mscan_total_suspect_db, 
		'bps_mscan_total_ignored_files' 		=> $bps_mscan_total_ignored_files,
		'bps_mscan_total_plugin_files' 			=> $bps_mscan_total_plugin_files, 			 
		'bps_mscan_total_theme_files' 			=> $bps_mscan_total_theme_files 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}

		if ( ! get_option('bulletproof_security_options_mscan_theme_hash') || ! get_option('bulletproof_security_options_mscan_plugin_hash') ) {	
			echo $bps_topDiv;
			$text = '<strong><font color="blue">'.__('First Time Scan or the Delete File Hashes Tool was used', 'bulletproof-security').'</font><br><font color="green">'.__('You will only see this message the first time you do a scan or if you use the Delete Files Hashes Tool. In order to make sure all Plugin and Theme hash files are created successfully no file scanning will occur during this scan. You can run a new scan after this scan has completed.', 'bulletproof-security').'</font></strong>';
			echo $text;	
			echo $bps_bottomDiv;
		}
	}

	// Form Processing: MScan Report. I want the page to refresh/reload with a POST instead of GET so that 
	// Scan Report scan data is current and the Suspicious Files/DB accordion tab forms scan data is current.
	if ( isset( $_POST['Submit-MScan-Report'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_mscan_report' );
		// don't need to echo a message
	}
?>

<div id="MscanStartStopResetTable" style="position:relative;top:0px;left:-2px;margin:0px;">

<table width="400" border="0">
  <tr>
    <td>
<form name="MScanStart" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mscan_start'); ?>
    <input type="submit" id="bps-mscan-start-button" name="Submit-MScan-Start" style="margin:0px 5px 15px 0px;" value="<?php esc_attr_e('Start Scan', 'bulletproof-security') ?>" class="button bps-button" onclick="openWin()" />
</form>
</td>
    <td>
<form name="MScanStop" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mscan_stop'); ?>
    <input type="submit" id="bps-mscan-stop-button" name="Submit-MScan-Stop" style="margin:0px 5px 15px 0px;" value="<?php esc_attr_e('Stop Scan', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to stop scanning or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</td>
    <td>
<form name="MScanReport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php#bps-tabs-3' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mscan_report'); ?>
    <input type="submit" id="bps-mscan-report-button" name="Submit-MScan-Report" style="margin:0px 5px 15px 0px;" value="<?php esc_attr_e('View Report', 'bulletproof-security') ?>" class="button bps-button" />
</form>
</td>
    <td>
<form name="MScanReset" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mscan_reset'); ?>
    <input type="submit" id="bps-mscan-reset-button" name="Submit-MScan-Reset" style="margin:0px 0px 15px 0px;" value="<?php esc_attr_e('Reset MScan', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to reset/delete all MScan scan results and data or click Cancel. Note: MScan option settings will not be reset/deleted.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</td>
  </tr>
</table>
</div>

<div id="bps-accordion-1" class="bps-accordion-main-2" style="margin:0px 0px 20px 0px;">
<h3 id="mscan-accordion-1"><?php _e('MScan Options & Tools', 'bulletproof-security'); ?></h3>
<div id="mscan-accordion-inner">

<?php
// Form Processing: MScan Options Form
// Important: This Form processing code MUST be above the Form & bpsPro_save_mscan_options() function so that new DB option values are current.
if ( isset( $_POST['Submit-MScan-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_mscan_options');
	
	$mscan_dirs = $_POST['mscan'];

	switch( $_POST['Submit-MScan-Options'] ) {
		case __('Save MScan Options', 'bulletproof-security'):
		
		$mscan_dirs_checked = array();
		
		if ( ! empty( $mscan_dirs ) ) {
			
			foreach ( $mscan_dirs as $key => $value ) {
				
				if ( $value == '1' ) {
					$mscan_dirs_checked[$key] = $value;
				}
			}
		}	

		$wp_abspath_forward_slashes = str_replace( '\\', '/', ABSPATH );
		$wp_install_folder = str_replace( array( get_home_path(), '/', ), "", $wp_abspath_forward_slashes );
		$wp_includes_forward_slashes = $wp_abspath_forward_slashes . WPINC;
		$wp_includes_folder = str_replace( $wp_abspath_forward_slashes, "", $wp_includes_forward_slashes );
		$wp_content_folder = str_replace( ABSPATH, '', WP_CONTENT_DIR );
		
		$source = $wp_abspath_forward_slashes;
		$dir_array = array();
		
		if ( is_dir($source) ) {
			
			$iterator = new DirectoryIterator($source);	
			
			foreach ( $iterator as $files ) {
				try {			
					if ( $files->isDir() && ! $files->isDot() ) {
		
						if ( ! empty( $files ) ) {
							$dir_array[] = $files->getFilename();
						}
					}
				} catch (RuntimeException $e) {   
					
				}
			}
		}
	
		$get_home_path = get_home_path();
		$home_dir_array = array();
		
		if ( $wp_abspath_forward_slashes != $get_home_path ) {
	
			if ( is_dir($get_home_path) ) {
				
				$iterator = new DirectoryIterator($get_home_path);	
				
				foreach ( $iterator as $files ) {
					try {			
						if ( $files->isDir() && ! $files->isDot() ) {
							
							if ( $wp_install_folder != $files->getFilename() && $wp_content_folder != $files->getFilename() && $wp_includes_folder != $files->getFilename() && 'wp-admin' != $files->getFilename()) {
			
								if ( ! empty( $files ) ) {
									$home_dir_array[] = $files->getFilename();
								}
							}
						}
					} catch (RuntimeException $e) {   
						
					}
				}
			}
		}
		
		$dir_array_merge = array_merge($dir_array, $home_dir_array);
		$dir_flip = array_flip($dir_array_merge);
	
		// replace values in the flipped array with blank values.
		// This seems wrong, but it is not > The $mscan_dirs_checked array (actual checked form checkboxes) is merged below.
		// I don't need to strip out any other WP sites since the form will not allow checking checkboxes for other WP sites.
		$mscan_actual_dirs = array();
	
		foreach ( $dir_flip as $key => $value ) {
			$mscan_actual_dirs[$key] = preg_replace( '/\d/', "", $value );
		}
				
		// get dirs that do not exist in the bps_mscan_dirs db option. ie an unchecked form checkbox.
		$mscan_diff_key_dir = array_diff_key( $mscan_actual_dirs, $mscan_dirs_checked );
	
		// merge checked form checkboxes and dir array with blank values
		$mscan_array_merge = array_merge( $mscan_diff_key_dir, $mscan_dirs_checked );
		ksort($mscan_array_merge);		

		break;
	}

	// Add an additional newline for: mscan_exclude_tmp_files so the last file is included in the array
	// when using explode()
	$mscan_exclude_tmp_files = $_POST['mscan_exclude_tmp_files'] . "\n";
	$mscan_exclude_tmp_files = preg_replace("/(\n\n|\n\n\n|\n\n\n\n)/", "\n", $mscan_exclude_tmp_files);
	
	$MS_Options = array(
	'bps_mscan_dirs' 				=> $mscan_array_merge, 
	'mscan_max_file_size' 			=> esc_html($_POST['mscan_max_file_size']), 
	'mscan_max_time_limit' 			=> esc_html($_POST['mscan_max_time_limit']), 
	'mscan_scan_database' 			=> $_POST['mscan_scan_database_select'], 
	'mscan_scan_images' 			=> 'Off', 
	'mscan_scan_skipped_files' 		=> $_POST['mscan_scan_skipped_files_select'], 
	'mscan_scan_delete_tmp_files' 	=> $_POST['mscan_scan_delete_tmp_files_select'], 
	'mscan_scan_frequency' 			=> 'Off', 
	'mscan_exclude_dirs' 			=> $_POST['mscan_exclude_dirs'], 
	'mscan_exclude_tmp_files' 		=> $mscan_exclude_tmp_files, 
	'mscan_file_size_limit_hidden' 	=> '14' 
	);	
	
	foreach( $MS_Options as $key => $value ) {
		update_option('bulletproof_security_options_MScan', $MS_Options);
	}	

	$MScan_options = get_option('bulletproof_security_options_MScan');
	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$mscan_scan_skipped_files_message = '';
	$mscan_scan_delete_tmp_files_message = '';

	if ( $MScan_options['mscan_scan_skipped_files'] == 'On' && $MScan_status['bps_mscan_total_skipped_files'] > 0 ) {
		$mscan_scan_skipped_files_message = '<br><font color="blue"><strong>'.__('Skipped file scanning is turned On. Only skipped files will be scanned.', 'bulletproof-security').'</strong></font>';
	}
	
	if ( $MScan_options['mscan_scan_skipped_files'] == 'On' && $MScan_status['bps_mscan_total_skipped_files'] <= 0 ) {
		$mscan_scan_skipped_files_message = '<br><font color="blue"><strong>'.__('Skipped file scanning is turned On. There are no skipped files to be scanned. Either there really are not any skipped files to scan or you have not run a regular scan yet with the Skipped File Scan option turned Off.', 'bulletproof-security').'</strong></font>';
	}

	if ( $MScan_options['mscan_scan_delete_tmp_files'] == 'On' ) {
		$mscan_scan_delete_tmp_files_message = '<br><strong><font color="#fb0101">'.__('Warning: ', 'bulletproof-security').'</font>'.__('On some web hosts (Known host issues: SiteGround, Cyon) turning On the "Automatically Delete /tmp Files" option setting will cause your website/server to crash. If your website/server does crash contact your web host support folks, tell them that you deleted /tmp files and your website/server has crashed. You can use the MScan Exclude /tmp Files option to exclude certain tmp files from being deleted. You will need to ask your web host for the names of those tmp files to exclude.', 'bulletproof-security').'</strong>';
	}

	echo $bps_topDiv;
	$text = '<font color="green"><strong>'.__('MScan Options saved.', 'bulletproof-security').'</strong></font>'.$mscan_scan_skipped_files_message.$mscan_scan_delete_tmp_files_message;
	echo $text;
	echo $bps_bottomDiv;
}

// Get any new dirs that have been created and remove any old dirs from the bps_mscan_dirs db option.
// Update the bps_mscan_dirs db option for use in the MscanOptions Form.
// 15.4: MScan now does 2 dir iterations: ABSPATH and Home directory and merges the results.
function bpsPro_save_mscan_options() {
	
	$MScan_options = get_option('bulletproof_security_options_MScan'); 
	$wp_abspath_forward_slashes = str_replace( '\\', '/', ABSPATH );
	$wp_install_folder = str_replace( array( get_home_path(), '/', ), "", $wp_abspath_forward_slashes );
	$wp_includes_forward_slashes = $wp_abspath_forward_slashes . WPINC;
	$wp_includes_folder = str_replace( $wp_abspath_forward_slashes, "", $wp_includes_forward_slashes );
	$wp_content_folder = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	
	$source = $wp_abspath_forward_slashes;
	$dir_array = array();
	
	if ( is_dir($source) ) {
		
		$iterator = new DirectoryIterator($source);	
		
		foreach ( $iterator as $files ) {
			try {			
				if ( $files->isDir() && ! $files->isDot() ) {
	
					if ( ! empty( $files ) ) {
						$dir_array[] = $files->getFilename();
					}
				}
			} catch (RuntimeException $e) {   
				
			}
		}
	}

	$get_home_path = get_home_path();
	$home_dir_array = array();
	
	if ( $wp_abspath_forward_slashes != $get_home_path ) {

		if ( is_dir($get_home_path) ) {
			
			$iterator = new DirectoryIterator($get_home_path);	
			
			foreach ( $iterator as $files ) {
				try {			
					if ( $files->isDir() && ! $files->isDot() ) {
						
						if ( $wp_install_folder != $files->getFilename() && $wp_content_folder != $files->getFilename() && $wp_includes_folder != $files->getFilename() && 'wp-admin' != $files->getFilename()) {
		
							if ( ! empty( $files ) ) {
								$home_dir_array[] = $files->getFilename();
							}
						}
					}
				} catch (RuntimeException $e) {   
					
				}
			}
		}
	}
	
	$dir_array_merge = array_merge($dir_array, $home_dir_array);
	$dir_flip = array_flip($dir_array_merge);
	
	// replace values in the flipped array, good for bulk replacing all values. ie all dirs found.
	$mscan_actual_dirs = array();
	$pattern = '/define\((\s|)\'WP_USE_THEMES/';	
	
	foreach ( $dir_flip as $key => $value ) {
		
		$wp_index_file = $source . $key . '/index.php';
		$wp_blog_header_file = $source . $key . '/wp-blog-header.php';
		$wp_cron_file = $source . $key . '/wp-cron.php';
		$wp_load_file = $source . $key . '/wp-load.php';
		$wp_login_file = $source . $key . '/wp-login.php';
		$wp_settings_file = $source . $key . '/wp-settings.php';
		
		$home_wp_index_file = $get_home_path . $key . '/index.php';
		$home_wp_blog_header_file = $get_home_path . $key . '/wp-blog-header.php';
		$home_wp_cron_file = $get_home_path . $key . '/wp-cron.php';
		$home_wp_load_file = $get_home_path . $key . '/wp-load.php';
		$home_wp_login_file = $get_home_path . $key . '/wp-login.php';
		$home_wp_settings_file = $get_home_path . $key . '/wp-settings.php';
			
		if ( file_exists($wp_index_file) ) {
			$check_string = file_get_contents($wp_index_file);
		}
				
		if ( file_exists($home_wp_index_file) ) {
			$home_check_string = file_get_contents($home_wp_index_file);
		}

		if ( file_exists($wp_index_file) && preg_match( $pattern, $check_string ) && file_exists($wp_blog_header_file) && file_exists($wp_cron_file) && file_exists($wp_load_file) && file_exists($wp_login_file) && file_exists($wp_settings_file) || file_exists($home_wp_index_file) && preg_match( $pattern, $home_check_string ) && file_exists($home_wp_blog_header_file) && file_exists($home_wp_cron_file) && file_exists($home_wp_load_file) && file_exists($home_wp_login_file) && file_exists($home_wp_settings_file) ) {
			$mscan_actual_dirs[$key] = preg_replace( '/\d+/', "", $value );
		} else {
			$mscan_actual_dirs[$key] = preg_replace( '/\d+/', "1", $value );
		}
	}
				
	// Only processed once on first MScan page load
	if ( empty($MScan_options['bps_mscan_dirs']) ) {
		
		$mscan_max_file_size = isset($MScan_options['mscan_max_file_size']) ? $MScan_options['mscan_max_file_size'] : '400';
		$mscan_max_time_limit = isset($MScan_options['mscan_max_time_limit']) ? $MScan_options['mscan_max_time_limit'] : '300';			
		$mscan_scan_database = isset($MScan_options['mscan_scan_database']) ? $MScan_options['mscan_scan_database'] : 'On';
		$mscan_scan_skipped_files = isset($MScan_options['mscan_scan_skipped_files']) ? $MScan_options['mscan_scan_skipped_files'] : 'Off';
		$mscan_scan_delete_tmp_files = isset($MScan_options['mscan_scan_delete_tmp_files']) ? $MScan_options['mscan_scan_delete_tmp_files'] : 'Off';
		$mscan_scan_frequency = isset($MScan_options['mscan_scan_frequency']) ? $MScan_options['mscan_scan_frequency'] : 'Off';			
		$mscan_exclude_dirs = isset($MScan_options['mscan_exclude_dirs']) ? $MScan_options['mscan_exclude_dirs'] : '';
		$mscan_exclude_tmp_files = isset($MScan_options['mscan_exclude_tmp_files']) ? $MScan_options['mscan_exclude_tmp_files'] : '';
		$mscan_file_size_limit_hidden = ! isset($MScan_options['mscan_file_size_limit_hidden']) ? '14' : $MScan_options['mscan_file_size_limit_hidden'];			
		
		$MS_Options = array(
		'bps_mscan_dirs' 				=> $mscan_actual_dirs, 
		'mscan_max_file_size' 			=> $mscan_max_file_size, 
		'mscan_max_time_limit' 			=> $mscan_max_time_limit, 
		'mscan_scan_database' 			=> $mscan_scan_database, 
		'mscan_scan_images' 			=> 'Off', 
		'mscan_scan_skipped_files' 		=> $mscan_scan_skipped_files, 
		'mscan_scan_delete_tmp_files' 	=> $mscan_scan_delete_tmp_files, 
		'mscan_scan_frequency' 			=> $mscan_scan_frequency, 
		'mscan_exclude_dirs' 			=> $mscan_exclude_dirs, 
		'mscan_exclude_tmp_files' 		=> $mscan_exclude_tmp_files, 
		'mscan_file_size_limit_hidden' 	=> $mscan_file_size_limit_hidden 
		);	
	
		foreach( $MS_Options as $key => $value ) {
			update_option('bulletproof_security_options_MScan', $MS_Options);
		}			
	
	} else {

		$mscan_dirs_options_inner_array = array();
			
		foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {			
			$mscan_dirs_options_inner_array[$key] = $value;
		}

		// get new dirs found that do not exist in the bps_mscan_dirs db option. ie a new dir has been created.
		$mscan_diff_key_dir = array_diff_key($mscan_actual_dirs, $mscan_dirs_options_inner_array);
	
		// get old dirs that still exist in the bps_mscan_dirs db option. ie a dir has been deleted.
		$mscan_diff_key_options = array_diff_key($mscan_dirs_options_inner_array, $dir_flip);
	
		if ( ! empty($mscan_diff_key_options) ) {
		
			foreach ( $mscan_diff_key_options as $key => $value ) {
				unset($mscan_dirs_options_inner_array[$key]);
			}
	
			// merge any new dirs found
			$mscan_array_merge = array_merge( $mscan_diff_key_dir, $mscan_dirs_options_inner_array );
			ksort($mscan_array_merge);
	
		} else {
		
			// merge any new dirs found
			$mscan_array_merge = array_merge( $mscan_diff_key_dir, $mscan_dirs_options_inner_array );
			ksort($mscan_array_merge);		
		}
	
		$MS_Options = array(
		'bps_mscan_dirs' 				=> $mscan_array_merge, 
		'mscan_max_file_size' 			=> $MScan_options['mscan_max_file_size'], 
		'mscan_max_time_limit' 			=> $MScan_options['mscan_max_time_limit'], 
		'mscan_scan_database' 			=> $MScan_options['mscan_scan_database'], 
		'mscan_scan_images' 			=> 'Off', 
		'mscan_scan_skipped_files' 		=> $MScan_options['mscan_scan_skipped_files'], 
		'mscan_scan_delete_tmp_files' 	=> $MScan_options['mscan_scan_delete_tmp_files'], 
		'mscan_scan_frequency' 			=> 'Off', 
		'mscan_exclude_dirs' 			=> $MScan_options['mscan_exclude_dirs'], 
		'mscan_exclude_tmp_files' 		=> $MScan_options['mscan_exclude_tmp_files'], 
		'mscan_file_size_limit_hidden' 	=> '14' 
		);	
	
		foreach( $MS_Options as $key => $value ) {
			update_option('bulletproof_security_options_MScan', $MS_Options);
		}
	}
}

bpsPro_save_mscan_options();

	$scrolltoExcludeDirs = isset($_REQUEST['scrolltoExcludeDirs']) ? (int) $_REQUEST['scrolltoExcludeDirs'] : 0;
	$scrolltoExcludeTmpFiles = isset($_REQUEST['scrolltoExcludeTmpFiles']) ? (int) $_REQUEST['scrolltoExcludeTmpFiles'] : 0;
	
	// Form: MScan Options Form
	echo '<form name="MscanOptions" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_mscan_options');
	$MScan_options = get_option('bulletproof_security_options_MScan');
	
	echo '<table class="widefat" style="text-align:left;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:40%;font-size:1.13em;background-color:transparent;"><strong>'.__('Website Folders & Files To Scan', 'bulletproof-security').'<br>'.__('Files are not displayed, but will be scanned', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:30%;font-size:1.13em;background-color:transparent;"><strong>'.__('MScan Options', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:30%;font-size:1.13em;background-color:transparent;"><strong>'.__('MScan Tools', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';	

	echo '<th scope="row" style="border-bottom:none;font-size:1.13em;vertical-align:top;">';

	echo '<div id="MScancheckall" style="max-height:490px;overflow:auto;">';
	echo '<table style="text-align:left;border-right:1px solid #e5e5e5;padding:5px;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:20px;border-bottom:1px solid #e5e5e5;background-color:transparent;"><strong><span style="margin-left:9px;font-size:.88em;">'.__('All', 'bulletproof-security').'</span></strong><br><input type="checkbox" class="checkallMScan" /></th>';
	echo '<th scope="col" style="width:400px;font-size:1em;padding-top:20px;margin-right:20px;border-bottom:1px solid #e5e5e5;background-color:transparent;"><strong>'.__('Folder Name', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	$source = str_replace( '\\', '/', ABSPATH );
	$get_home_path = get_home_path();
	
	foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
		
		if ( $value == '1' ) {
			$checked = ( isset( $_POST['mscan[$key]'] ) ) ? $_POST['mscan[$key]'] : 'checked';
		} else {
			$checked = ( isset( $_POST['mscan[$key]'] ) ) ? $_POST['mscan[$key]'] : '';
		}
		
		if ( ! is_readable( $source . $key ) && ! is_readable( $get_home_path . $key ) ) {
			echo "<td></td>";
			echo '<td>'.$key.' <strong><font color="blue">'.__('Folder is not readable', 'bulletproof-security').'</font></strong></td>';			
			echo '</tr>';
		
		} else {
		
			$wp_index_file = $source . $key . '/index.php';
			$wp_blog_header_file = $source . $key . '/wp-blog-header.php';
			$wp_cron_file = $source . $key . '/wp-cron.php';
			$wp_load_file = $source . $key . '/wp-load.php';
			$wp_login_file = $source . $key . '/wp-login.php';
			$wp_settings_file = $source . $key . '/wp-settings.php';
			
			$home_wp_index_file = $get_home_path . $key . '/index.php';
			$home_wp_blog_header_file = $get_home_path . $key . '/wp-blog-header.php';
			$home_wp_cron_file = $get_home_path . $key . '/wp-cron.php';
			$home_wp_load_file = $get_home_path . $key . '/wp-load.php';
			$home_wp_login_file = $get_home_path . $key . '/wp-login.php';
			$home_wp_settings_file = $get_home_path . $key . '/wp-settings.php';
			
			$pattern = '/define\((\s|)\'WP_USE_THEMES/';

			if ( file_exists($wp_index_file) ) {
				$check_string = file_get_contents($wp_index_file);
			}
					
			if ( file_exists($home_wp_index_file) ) {
				$home_check_string = file_get_contents($home_wp_index_file);
			}

			if ( file_exists($wp_index_file) && preg_match( $pattern, $check_string ) && file_exists($wp_blog_header_file) && file_exists($wp_cron_file) && file_exists($wp_load_file) && file_exists($wp_login_file) && file_exists($wp_settings_file) || file_exists($home_wp_index_file) && preg_match( $pattern, $home_check_string ) && file_exists($home_wp_blog_header_file) && file_exists($home_wp_cron_file) && file_exists($home_wp_load_file) && file_exists($home_wp_login_file) && file_exists($home_wp_settings_file) ) {

				$hover_icon = '<strong><font color="black"><span class="tooltip-250-120"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('This folder contains another WordPress website. This checkbox cannot be checked. To scan that site run MScan from that site. Click the MScan Read Me help button above and read the "Scanning Other WordPress Sites" help section.', 'bulletproof-security').'</span></span></font></strong><br>';
			
				echo "<td><input type=\"checkbox\" id=\"mscandirs\" name=\"mscan[$key]\" value=\"\" class=\"MScanALL\" $checked /></td>";
				echo '<td>'.$key.$hover_icon.'</td>';			
				echo '</tr>';

			} else {
					
				echo "<td><input type=\"checkbox\" id=\"mscandirs\" name=\"mscan[$key]\" value=\"1\" class=\"MScanALL\" $checked /></td>";
				echo '<td>'.$key.'</td>';					
				echo '</tr>';					
			}
		}
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>'; // jQuery div parent
	echo '</th>';
	
	echo '<td style="border:none">';		
	echo '<div id="MScanOptions" style="margin:0px 0px 0px 0px;float:left;">';

	$max_file_size = ( isset( $_POST['mscan_max_file_size'] ) ) ? $_POST['mscan_max_file_size'] : '1000';
	$max_time_limit = ( isset( $_POST['mscan_max_time_limit'] ) ) ? $_POST['mscan_max_time_limit'] : '300';
	
	$mscan_exclude_dirs = ! isset($MScan_options['mscan_exclude_dirs']) ? '' : $MScan_options['mscan_exclude_dirs'];
	$mscan_scan_database = ! isset($MScan_options['mscan_scan_database']) ? '' : $MScan_options['mscan_scan_database'];	
	$mscan_scan_skipped_files = ! isset($MScan_options['mscan_scan_skipped_files']) ? '' : $MScan_options['mscan_scan_skipped_files'];
	$mscan_scan_delete_tmp_files = ! isset($MScan_options['mscan_scan_delete_tmp_files']) ? '' : $MScan_options['mscan_scan_delete_tmp_files'];
	$mscan_exclude_tmp_files = ! isset($MScan_options['mscan_exclude_tmp_files']) ? '' : $MScan_options['mscan_exclude_tmp_files'];
	$mscan_scan_frequency = ! isset($MScan_options['mscan_scan_frequency']) ? 'Off' : $MScan_options['mscan_scan_frequency'];
	$mscan_file_size_limit_hidden = ! isset($MScan_options['mscan_file_size_limit_hidden']) ? '14' : $MScan_options['mscan_file_size_limit_hidden'];	

	echo '<label for="bps-mscan-label" style="padding-right:5px">'.__('Max File Size Limit to Scan:', 'bulletproof-security').'</label>';
	echo '<input type="text" name="mscan_max_file_size" class="regular-text-50-fixed" style="margin-bottom:5px" value="'; if ( isset( $_POST['mscan_max_file_size'] ) && preg_match( '/\d/', $_POST['mscan_max_file_size'] ) ) { echo esc_html($max_file_size); } else { echo esc_html(trim(stripslashes($max_file_size))); } echo '" /> KB';
	echo '<input type="hidden" name="mscan_file_size_limit_hidden" value="14" />';
	echo '<br>';

	echo '<label for="bps-mscan-label" style="padding-right:23px">'.__('Max Time Limit to Scan:', 'bulletproof-security').'</label>';
	echo '<input type="text" name="mscan_max_time_limit" class="regular-text-50-fixed" style="margin-bottom:5px" value="'; if ( isset( $_POST['mscan_max_time_limit'] ) && preg_match( '/\d/', $_POST['mscan_max_time_limit'] ) ) { echo esc_html($max_time_limit); } else { echo esc_html(trim(stripslashes($max_time_limit))); } echo '" /> Seconds';
	echo '<br>';

	echo '<label for="bps-mscan-label" style="">'.__('Exclude Individual Folders', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-120"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('Enter one folder path per line. Include folder slashes.', 'bulletproof-security').'<br>'.__('Example:', 'bulletproof-security').'<br>/parent-folder-1/child-folder-1/<br>/parent-folder-2/child-folder-2/<br><br>'.__('Click the MScan Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong><br>';
	// trimming whitespace does not work because I am not trimming newlines or returns
    echo '<textarea class="text-area-340x60" name="mscan_exclude_dirs" style="width:340px;height:60px;margin-bottom:5px" tabindex="1">'.esc_html( trim(stripslashes($mscan_exclude_dirs), " \t\0\x0B") ).'</textarea>';
	echo '<input type="hidden" name="scrolltoExcludeDirs" id="scrolltoExcludeDirs" value="'.esc_html( $scrolltoExcludeDirs ).'" />';
	echo '<br>';

	echo '<label for="bps-mscan-label">'.__('Scan Database', 'bulletproof-security').'</label><br>';
	echo '<select name="mscan_scan_database_select" class="form-340" style="margin-bottom:10px">';
	echo '<option value="On"'. selected('On', $mscan_scan_database).'>'.__('Database Scan On', 'bulletproof-security').'</option>';
	echo '<option value="Off"'. selected('Off', $mscan_scan_database).'>'.__('Database Scan Off', 'bulletproof-security').'</option>';
	echo '</select><br>';

	echo '<label for="bps-mscan-label">'.__('Scan Skipped Files Only', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-120"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('When Skipped File Scan is On only skipped files will be scanned. Note: No other MScan option settings have any effect while Skipped File Scan is set to On.', 'bulletproof-security').'<br><br>'.__('Click the MScan Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong><br>';
	echo '<select name="mscan_scan_skipped_files_select" class="form-340" style="margin-bottom:10px">';
	echo '<option value="Off"'. selected('Off', $mscan_scan_skipped_files).'>'.__('Skipped File Scan Off', 'bulletproof-security').'</option>';
	echo '<option value="On"'. selected('On', $mscan_scan_skipped_files).'>'.__('Skipped File Scan On', 'bulletproof-security').'</option>';
	echo '</select><br>';

	echo '<label for="bps-mscan-label">'.__('Automatically Delete /tmp Files', 'bulletproof-security').'</label><br>';
	echo '<select name="mscan_scan_delete_tmp_files_select" class="form-340" style="margin-bottom:10px">';
	echo '<option value="Off"'. selected('Off', $mscan_scan_delete_tmp_files).'>'.__('Delete Tmp Files Off', 'bulletproof-security').'</option>';
	echo '<option value="On"'. selected('On', $mscan_scan_delete_tmp_files).'>'.__('Delete Tmp Files On', 'bulletproof-security').'</option>';
	echo '</select><br>';

	echo '<label for="bps-mscan-label" style="">'.__('Exclude /tmp Files', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-120"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('Enter one file name per line.', 'bulletproof-security').'<br>'.__('Example:', 'bulletproof-security').'<br>mysql.sock<br>.s.PGSQL.5432<br>.per-user<br>'.__('Click the MScan Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong><br>';
	// trimming whitespace does not work because I am not trimming newlines or returns
    echo '<textarea class="text-area-340x60" name="mscan_exclude_tmp_files" style="width:340px;height:60px;margin-bottom:5px" tabindex="1">'.esc_html( trim(stripslashes($mscan_exclude_tmp_files), " \t\0\x0B") ).'</textarea>';
	echo '<input type="hidden" name="scrolltoExcludeTmpFiles" id="scrolltoExcludeTmpFiles" value="'.esc_html( $scrolltoExcludeTmpFiles ).'" />';
	echo '<br>';

	echo '<label for="bps-mscan-label">'.__('Scheduled Scan Frequency (BPS Pro only)', 'bulletproof-security').'</label><br>';
	echo '<select name="mscan_scan_frequency_select" class="form-340" style="margin-bottom:15px">';
	echo '<option value="Off"'. selected('Off', $mscan_scan_frequency).'>'.__('Scheduled Scan Off', 'bulletproof-security').'</option>';
	echo '<option value="60"'. selected('60', $mscan_scan_frequency).'>'.__('Run Scan Every 60 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="180"'. selected('180', $mscan_scan_frequency).'>'.__('Run Scan Every 3 Hours', 'bulletproof-security').'</option>';
	echo '<option value="360"'. selected('360', $mscan_scan_frequency).'>'.__('Run Scan Every 6 Hours', 'bulletproof-security').'</option>';
	echo '<option value="720"'. selected('720', $mscan_scan_frequency).'>'.__('Run Scan Every 12 Hours', 'bulletproof-security').'</option>';
	echo '<option value="1440"'. selected('1440', $mscan_scan_frequency).'>'.__('Run Scan Every 24 Hours', 'bulletproof-security').'</option>';
	echo '</select><br>';

	echo "<p><input type=\"submit\" name=\"Submit-MScan-Options\" value=\"".esc_attr__('Save MScan Options', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Click OK to save MScan Options or click Cancel', 'bulletproof-security')."')\" /></p></form>";

	echo '</div>';
	echo '</td>';
	echo '<td style="border:none">';		
	echo '<div id="MScanOptions" style="margin:19px 0px 0px 0px;float:left;">';

	/*
	echo '<form name="MScanTimeEstimate" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_mscan_time_estimate');
	echo "<input type=\"submit\" id=\"bps-mscan-time-estimate-button\" name=\"Submit-MScan-Time-Estimate\" value=\"".esc_attr__('Scan Time Estimate Tool', 'bulletproof-security')."\" class=\"button bps-button\" style=\"width:175px;height:auto;white-space:normal\" onclick=\"return confirm('".__('IMPORTANT: You can stop the scan time estimate if it hangs or is taking too long by clicking the Stop Scan button.\n\n-------------------------------------------------------------\n\nThis tool allows you to check the estimated total scan time of a scan based on your MScan option settings without actually performing/running a scan. Note: This tool does not affect or change any previous scan results except for the Total Scan Time, which will be changed to the estimated scan time.\n\n-------------------------------------------------------------\n\nExample Usage: You can check or uncheck Hosting Account Root Folders checkboxes and change any other MScan option settings, save your MScan option settings and then run the Scan Time Estimate Tool to get the total estimated time that the actual scan will take. For additional help information click the MScan Read Me help button.\n\n-------------------------------------------------------------\n\nClick OK to get a scan time estimate or click Cancel', 'bulletproof-security')."')\" />";	
	echo '</form><br>';
	*/

	echo '<form name="MScanDeleteHashes" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_mscan_delete_hashes');
	echo "<input type=\"submit\" name=\"Submit-MScan-Delete-Hashes\" value=\"".esc_attr__('Delete File Hashes Tool', 'bulletproof-security')."\" class=\"button bps-button\" style=\"width:175px;height:auto;white-space:normal\" onclick=\"return confirm('".__('CAUTION: Please click the MScan Read Me help button before using this tool. This tool allows you to delete the Plugin and Theme file hashes.\n\n-------------------------------------------------------------\n\nThis tool should ONLY be used if there is a problem when scanning Plugin and Theme files.\n\n-------------------------------------------------------------\n\nClick OK to delete Plugin and Theme file hashes or click Cancel', 'bulletproof-security')."')\" />";	
	echo '</form>';
?>

<div id="CC-Import" style="margin:24px 0px 20px 0px">
<form name="MScanPluginZipUpload" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ); ?>" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field('bulletproof_security_plugin_zip_upload'); 
		bpsPro_mscan_plugin_zip_upload();
	?>
	<input type="file" name="bps_plugin_zip_upload[]" id="bps_plugin_zip_upload" multiple="multiple" />
	<input type="submit" name="Submit-Plugin-Zip-Upload" class="button bps-button" style="margin-top:1px;" value="<?php esc_attr_e('Upload Plugin Zip Files', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will upload Plugin Zip files to the /wp-content/bps-backup/plugin-hashes/ folder. The zip files will be extracted, MD5 file hashes will be created and the zip files will be deleted.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Zip files MUST be named using this exact format: plugin-name.x.x.zip where x is the actual current version number of the plugin in the zip file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to upload Plugin Zip files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="CC-Import" style="margin:20px 0px 20px 0px">
<form name="MScanThemeZipUpload" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ); ?>" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field('bulletproof_security_theme_zip_upload'); 
		bpsPro_mscan_theme_zip_upload();
	?>
	<input type="file" name="bps_theme_zip_upload[]" id="bps_theme_zip_upload" multiple="multiple" />
	<input type="submit" name="Submit-Theme-Zip-Upload" class="button bps-button" style="margin-top:1px;" value="<?php esc_attr_e('Upload Theme Zip Files', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will upload Theme Zip files to the /wp-content/bps-backup/theme-hashes/ folder. The zip files will be extracted, MD5 file hashes will be created and the zip files will be deleted.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Zip files MUST be named using this exact format: theme-name.x.x.zip where x is the actual current version number of the theme in the zip file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to upload Theme Zip files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<?php

	echo '</div>';
	echo '</td>';
	echo '</tr>';	
	echo '</tbody>';
	echo '</table>';	

// Plugin Zip file upload Form
// Note: ModSecurity randomly breaks file uploads: https://forum.ait-pro.com/forums/topic/file-upload-does-not-work-no-errors-modsecurity/
function bpsPro_mscan_plugin_zip_upload() {
global $bps_topDiv, $bps_bottomDiv;	
	
	if ( isset( $_POST['Submit-Plugin-Zip-Upload'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_plugin_zip_upload' );
	
		if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/plugin-hashes' ) ) {
			@mkdir( WP_CONTENT_DIR . '/bps-backup/plugin-hashes', 0755, true );
			@chmod( WP_CONTENT_DIR . '/bps-backup/plugin-hashes/', 0755 );
		}

		echo $bps_topDiv;
		
		$plugin_hashes_dir = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/';
		$allowed_file_types = array( 'zip' );
		
		foreach ( $_FILES['bps_plugin_zip_upload']['error'] as $key => $error ) {                   

			if ( $error == UPLOAD_ERR_OK ) {
				
				$file_name 		= $_FILES['bps_plugin_zip_upload']['name'][$key];
				$file_tmp_name 	= $_FILES['bps_plugin_zip_upload']['tmp_name'][$key];
				$file_dest 		= $plugin_hashes_dir . $file_name;
				$file_ext 		= pathinfo( $file_name, PATHINFO_EXTENSION );

				if ( in_array( strtolower($file_ext), $allowed_file_types ) && preg_match( '/(.*)\.[\d\.]{1,}\.zip/', $file_name ) ) {
				
					if ( move_uploaded_file($file_tmp_name, $file_dest) ) {
		
						$text = '<strong><font color="green">'.__('Plugin Zip File Upload Successful: ', 'bulletproof-security').$file_dest.'</font></strong><br>';
						echo $text;

						$plugin_name_version_array = array();
						
						foreach ( $_FILES['bps_plugin_zip_upload']['name'] as $key => $value ) {
							// Plugin File Name: plugin-name.1.0.zip
							$plugin_name = strstr($value, '.', true); // plugin-name
							$plugin_version = strstr($value, '.'); // .1.0.zip
							$plugin_version_nodot = substr($plugin_version, 1);	// 1.0.zip
							$plugin_version_replace = str_replace( ".zip", "", $plugin_version_nodot );	// 1.0						
							$plugin_name_version_array[$plugin_name] = $plugin_version_replace;
						}						
						
						$zip_upload_options = 'bulletproof_security_options_mscan_zip_upload';
						$mscan_zip_db_options = get_option('bulletproof_security_options_mscan_zip_upload');
						$bps_mscan_theme_zip_upload = isset($mscan_zip_db_options['bps_mscan_theme_zip_upload']) ? $mscan_zip_db_options['bps_mscan_theme_zip_upload'] : array('' => '');
				
						$Mscan_Zip_Options = array( 
						'bps_mscan_plugin_zip_upload' 	=> $plugin_name_version_array, 
						'bps_mscan_theme_zip_upload' 	=> $bps_mscan_theme_zip_upload 
						);
						
						if ( ! get_option( $zip_upload_options ) ) {	
						
							foreach( $Mscan_Zip_Options as $key => $value ) {
								update_option('bulletproof_security_options_mscan_zip_upload', $Mscan_Zip_Options);
							}
					
						} else {
							
							$plugin_name_version_update_array = array();
							
							foreach ( $mscan_zip_db_options['bps_mscan_plugin_zip_upload'] as $key3 => $value3 ) {
								
								foreach ( $plugin_name_version_array as $key4 => $value4 ) {
									
									if ( ! empty($key3) ) {
										$plugin_name_version_update_array[$key3] = $value3;
									}
									
									if ( $key3 == $key4 ) {
										$plugin_name_version_update_array[$key3] = $value4;
									}
									
									if ( ! in_array( $key4, $mscan_zip_db_options['bps_mscan_plugin_zip_upload'] ) ) {
										$plugin_name_version_update_array[$key4] = $value4;
									}
								}
							}
							
							$Mscan_Zip_Options = array( 
							'bps_mscan_plugin_zip_upload' 	=> $plugin_name_version_update_array, 
							'bps_mscan_theme_zip_upload' 	=> $bps_mscan_theme_zip_upload 
							);
							
							foreach( $Mscan_Zip_Options as $key => $value ) {
								update_option('bulletproof_security_options_mscan_zip_upload', $Mscan_Zip_Options);
							}
						}				
					
					} else { 
				
						$text = '<strong><font color="#fb0101">'.__('Error: Zip File Upload Failed: ', 'bulletproof-security').'</font><font color="black">'.__('Unable to move this uploaded zip file: ', 'bulletproof-security').$file_name.__(' to this folder: ', 'bulletproof-security').$file_dest.'.</font></strong><br>';
						echo $text;
					}
				
				} else {
					
					$text = '<strong><font color="#fb0101">'.__('File Extension/Type or Filename Error: ', 'bulletproof-security').'</font><font color="black"> '.$file_name.__(' is either not a .zip file or the .zip file is not named correctly. Only .zip files are allowed to be uploaded. Zip files MUST be named using this exact filename format: plugin-name.x.x.zip where x is the actual current version of the plugin in the zip file.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}
			
			} else {
				
				$text = '<strong><font color="#fb0101">'.__('Error: No zip file chosen: ', 'bulletproof-security').'</font><font color="black">'.__('You need to choose zip files before clicking the Upload Plugin Zip Files button.', 'bulletproof-security').'</font></strong><br>';
				echo $text;
			}
		}
		echo $bps_bottomDiv;
	}	
}

// Theme Zip file upload Form
// Note: ModSecurity randomly breaks file uploads: https://forum.ait-pro.com/forums/topic/file-upload-does-not-work-no-errors-modsecurity/
function bpsPro_mscan_theme_zip_upload() {
global $bps_topDiv, $bps_bottomDiv;	
	
	if ( isset( $_POST['Submit-Theme-Zip-Upload'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_theme_zip_upload' );
	
		if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/theme-hashes' ) ) {
			@mkdir( WP_CONTENT_DIR . '/bps-backup/theme-hashes', 0755, true );
			@chmod( WP_CONTENT_DIR . '/bps-backup/theme-hashes/', 0755 );
		}

		echo $bps_topDiv;
		
		$theme_hashes_dir = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
		$allowed_file_types = array( 'zip' );

		foreach ( $_FILES['bps_theme_zip_upload']['error'] as $key => $error ) {                   

			if ( $error == UPLOAD_ERR_OK ) {                  

				$file_name 		= $_FILES['bps_theme_zip_upload']['name'][$key];
				$file_tmp_name 	= $_FILES['bps_theme_zip_upload']['tmp_name'][$key];
				$file_dest 		= $theme_hashes_dir . $file_name;
				$file_ext 		= pathinfo( $file_name, PATHINFO_EXTENSION );
				
				if ( in_array( strtolower($file_ext), $allowed_file_types ) && preg_match( '/(.*)\.[\d\.]{1,}\.zip/', $file_name ) ) {
				
					if ( move_uploaded_file($file_tmp_name, $file_dest) ) {
		
						$text = '<strong><font color="green">'.__('Theme Zip File Upload Successful: ', 'bulletproof-security').$file_dest.'</font></strong><br>';
						echo $text;
						
						$theme_name_version_array = array();
						
						foreach ( $_FILES['bps_theme_zip_upload']['name'] as $key => $value ) {
							// Theme File Name: theme-name.1.0.zip
							$theme_name = strstr($value, '.', true); // theme-name
							$theme_version = strstr($value, '.'); // .1.0.zip
							$theme_version_nodot = substr($theme_version, 1);	// 1.0.zip
							$theme_version_replace = str_replace( ".zip", "", $theme_version_nodot );	// 1.0						
							$theme_name_version_array[$theme_name] = $theme_version_replace;
						}						
						
						$zip_upload_options = 'bulletproof_security_options_mscan_zip_upload';
						$mscan_zip_db_options = get_option('bulletproof_security_options_mscan_zip_upload');
						$bps_mscan_plugin_zip_upload = isset($mscan_zip_db_options['bps_mscan_plugin_zip_upload']) ? $mscan_zip_db_options['bps_mscan_plugin_zip_upload'] : array('' => '');
				
						$Mscan_Zip_Options = array( 
						'bps_mscan_plugin_zip_upload' 	=> $bps_mscan_plugin_zip_upload, 
						'bps_mscan_theme_zip_upload' 	=> $theme_name_version_array 
						);
						
						if ( ! get_option( $zip_upload_options ) ) {	
						
							foreach( $Mscan_Zip_Options as $key => $value ) {
								update_option('bulletproof_security_options_mscan_zip_upload', $Mscan_Zip_Options);
							}
					
						} else {
							
							$theme_name_version_update_array = array();
							
							foreach ( $mscan_zip_db_options['bps_mscan_theme_zip_upload'] as $key3 => $value3 ) {
								
								foreach ( $theme_name_version_array as $key4 => $value4 ) {
									
									if ( ! empty($key3) ) {
										$theme_name_version_update_array[$key3] = $value3;
									}
									
									if ( $key3 == $key4 ) {
										$theme_name_version_update_array[$key3] = $value4;
									}
									
									if ( ! in_array( $key4, $mscan_zip_db_options['bps_mscan_theme_zip_upload'] ) ) {
										$theme_name_version_update_array[$key4] = $value4;
									}
								}
							}
							
							$Mscan_Zip_Options = array( 
							'bps_mscan_plugin_zip_upload' 	=> $bps_mscan_plugin_zip_upload, 
							'bps_mscan_theme_zip_upload' 	=> $theme_name_version_update_array 
							);
							
							foreach( $Mscan_Zip_Options as $key => $value ) {
								update_option('bulletproof_security_options_mscan_zip_upload', $Mscan_Zip_Options);
							}
						}	

					} else { 
				
						$text = '<strong><font color="#fb0101">'.__('Error: Zip File Upload Failed: ', 'bulletproof-security').'</font><font color="black">'.__('Unable to move this uploaded zip file: ', 'bulletproof-security').$file_name.__(' to this folder: ', 'bulletproof-security').$file_dest.'.</font></strong><br>';
						echo $text;
					}
				
				} else {
					
					$text = '<strong><font color="#fb0101">'.__('File Extension/Type or Filename Error: ', 'bulletproof-security').'</font><font color="black"> '.$file_name.__(' is either not a .zip file or the .zip file is not named correctly. Only .zip files are allowed to be uploaded. Zip files MUST be named using this exact filename format: theme-name.x.x.zip where x is the actual current version of the theme in the zip file.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}
			
			} else {
				
				$text = '<strong><font color="#fb0101">'.__('Error: No zip file chosen: ', 'bulletproof-security').'</font><font color="black">'.__('You need to choose zip files before clicking the Upload Theme Zip Files button.', 'bulletproof-security').'</font></strong><br>';
				echo $text;
			}
		}
		echo $bps_bottomDiv;
	}	
}

$UIoptions = get_option('bulletproof_security_options_theme_skin');	

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#MScancheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallMScan').click(function() {
		$(this).parents('#MScancheckall:eq(0)').find('.MScanALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</div>
<h3 id="mscan-accordion-2"><?php _e('View|Ignore|Delete Suspicious Files', 'bulletproof-security'); ?></h3>
<div id="mscan-accordion-inner">

<?php

$nonce = wp_create_nonce( 'bps-anti-csrf' );

if ( isset( $_GET['mscan_view_file'] ) && 'view_file' == $_GET['mscan_view_file'] ) {
	
	if ( ! wp_verify_nonce( $nonce, 'bps-anti-csrf' ) ) {
		die( 'CSRF Error: Invalid Nonce used in the MScan View File GET Request' );
			
	} else {

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
	}
}

// MScan Suspicious Files Form Proccessing - View, Ignore, Unignore or Delete Files
// Note: This form processing code must be above the form so that the View File output is displayed above the Suspicious Files form.
if ( isset( $_POST['Submit-MScan-Suspect-Form'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_mscan_suspicious_files');
	
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

	$mscan_files = $_POST['mscan'];
	$MStable = $wpdb->prefix . "bpspro_mscan";
	
	switch( $_POST['Submit-MScan-Suspect-Form'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$delete_files = array();
		$ignore_files = array();
		$unignore_files = array();
		$view_files = array();		
		
		if ( ! empty($mscan_files) ) {
			
			foreach ( $mscan_files as $key => $value ) {
				
				if ( $value == 'deletefile' ) {
					$delete_files[] = $key;
				
				} elseif ( $value == 'ignorefile' ) {
					$ignore_files[] = $key;
				
				} elseif ( $value == 'unignorefile' ) {
					$unignore_files[] = $key;				

				} elseif ( $value == 'viewfile' ) {
					$view_files[] = $key;
				}
			}
		}
			
		if ( ! empty($delete_files) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_files as $delete_file ) {
				
				$MScanRowsDelete = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_path = %s", $delete_file) );
			
				foreach ( $MScanRowsDelete as $row ) {
					$path_parts = pathinfo($row->mscan_path);
					$filename = $path_parts['basename'];
					
					@unlink($row->mscan_path);
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $MStable WHERE mscan_path = %s", $delete_file));
				
					$text = '<strong><font color="green">'.$filename.__(' has been deleted.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}
			}
			echo '</p></div>';	
		}
		
		if ( ! empty($ignore_files) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $ignore_files as $ignore_file ) {
				
				$MScanRowsIgnore = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_path = %s", $ignore_file) );
			
				foreach ( $MScanRowsIgnore as $row ) {
					$path_parts = pathinfo($row->mscan_path);
					$filename = $path_parts['basename'];
					
					$update_rows = $wpdb->update( $MStable, array( 'mscan_ignored' => 'ignore' ), array( 'mscan_path' => $row->mscan_path ) );	
				
					$text = '<strong><font color="green">'.$filename.__(' Current Status has been changed to Ignored File and this file will not be scanned in any future MScan Scans.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}			
			}
			echo '</p></div>';	
		}

		if ( ! empty($unignore_files) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unignore_files as $unignore_file ) {
				
				$MScanRowsUnignore = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_path = %s", $unignore_file) );
			
				foreach ( $MScanRowsUnignore as $row ) {
					$path_parts = pathinfo($row->mscan_path);
					$filename = $path_parts['basename'];
					
					$update_rows = $wpdb->update( $MStable, array( 'mscan_ignored' => '' ), array( 'mscan_path' => $row->mscan_path ) );	
				
					$text = '<strong><font color="green">'.$filename.__(' Ignored File Status has been removed. The previous Status of the file will be displayed again and this file will be scanned in future MScan scans.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}			
			}
			echo '</p></div>';	
		}

		if ( ! empty($view_files) ) {
			
			echo '<div id="message" style="width:97%;margin:-10px 0px 15px 0px;padding:1px 10px 5px 10px;background-color:#dfecf2;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $view_files as $view_file ) {
				
				$MScanRowsView = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_path = %s", $view_file) );
			
				foreach ( $MScanRowsView as $row ) {
					$filename = pathinfo( $row->mscan_path, PATHINFO_BASENAME );
					$ext = pathinfo( strtolower($row->mscan_path), PATHINFO_EXTENSION );
					$file_contents = file_get_contents($row->mscan_path);
					
					if ( $ext == 'png' || $ext == 'gif' || $ext == 'bmp' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'tif' || $ext == 'tiff' ) {
						
						$text = '<div style="margin:0px 0px 5px 0px;font-size:1.13em;font-weight:600"><span style="width:100px;margin:0px;padding:0px 6px 0px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.admin_url( "admin.php?page=bulletproof-security/admin/mscan/mscan.php&mscan_view_file=view_file&_wpnonce=$nonce" ).'" style="text-decoration:none;">'.__('Close File', 'bulletproof-security').'</a></span> '.$filename.' : '.__('MScan Pattern Match', 'bulletproof-security').': <span style="background-color:yellow;">'.esc_html($row->mscan_pattern).'</span><br>'.__('Only the MScan Pattern Match is displayed for images instead of the image file code.', 'bulletproof-security').'<br>'.__('Opening image files to view image file code does not work well in a Browser.', 'bulletproof-security').'<br>'.__('You can download suspicious image files and use a code editor like Notepad++ to check image file code for any malicious code.', 'bulletproof-security').'<br>'.__('If you are not sure what to check for or what is and is not malicious code then click the MScan Read Me help button.', 'bulletproof-security').'</div>';

						echo $text;
						echo '<pre style="max-width:100%;">';
						echo esc_html($row->mscan_pattern);
						echo '</pre>';						
						
					} else {
						
						$text = '<div style="margin:0px 0px 5px 0px;font-size:1.13em;font-weight:600"><span style="width:100px;margin:0px;padding:0px 6px 0px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.admin_url( "admin.php?page=bulletproof-security/admin/mscan/mscan.php&mscan_view_file=view_file&_wpnonce=$nonce" ).'" style="text-decoration:none;">'.__('Close File', 'bulletproof-security').'</a></span> '.$filename.' : '.__('MScan Pattern Match', 'bulletproof-security').': <span style="background-color:yellow;">'.esc_html($row->mscan_pattern).'</span><br>'.__('You can use your Browser\'s Search or Find feature to search the file contents/code displayed below using the MScan Pattern Match above for the suspicious code that was detected by MScan.', 'bulletproof-security').'<br>'.__('You can download suspicious files if you would like to check the file contents/code more extensively with a code editor like Notepad++.', 'bulletproof-security').'<br>'.__('If you are not sure what to check for or what is and is not malicious code then click the MScan Read Me help button.', 'bulletproof-security').'</div>';
						
						echo $text;
						echo '<pre style="max-width:70%;height:200px;white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;">';
						echo esc_html($file_contents);
						echo '</pre>';
					}
				}			
			}
			echo '</p></div>';			
		}
		break;
	}
}

	$mscan_file_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('File hash comparison scan results are 100% accurate. WP Core, Plugin and Theme files are scanned using file hash comparison scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results are less accurate and will usually detect some false positive matches. All other files that are not WP Core, Plugin and Theme files are scanned using pattern matching scanning.', 'bulletproof-security').'<br>'.__('You can View, Ignore and Delete files detected as suspicious using the Form below. Before deleting any files make a backup of those files on your computer not on your hosting account.', 'bulletproof-security').'<br>'.__('And of course check the file contents of suspicious files to see if they contain hacker code or are false positive matches. Use the Ignore File checkbox option to ignore false postive matches.', 'bulletproof-security').'<br>'.__('When you ignore a file it will no longer be scanned in any future scans. When you unignore an ignored file it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
	echo $mscan_file_scan_help_text;	
	
	echo '<form name="MScanSuspiciousFiles" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_mscan_suspicious_files');
	
	$MStable = $wpdb->prefix . "bpspro_mscan";
	$db_rows = 'db';
	$clean_rows = 'clean';
	$safe_rows = 'safe';
	$MScanFilesRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_type != %s AND mscan_status != %s AND mscan_status != %s", $db_rows, $clean_rows, $safe_rows ) );
	
	echo '<div id="MScanSuspectcheckall" style="">';
	echo '<table class="widefat" style="margin-bottom:20px;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:10%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><br><strong>'.__('View<br>File', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><input type="checkbox" class="checkallIgnore" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Ignore<br>File', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><input type="checkbox" class="checkallUnignore" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unignore<br>File', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><input type="checkbox" class="checkallDelete" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Delete<br>File', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:42%;"><strong>'.__('File Path', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;"><strong>'.__('File Hash or<br>Pattern Match', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;"><strong>'.__('Scan<br>Time', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	if ( $wpdb->num_rows != 0 ) {
	
		foreach ( $MScanFilesRows as $row ) {
		
			if ( $row->mscan_status == '' ) {
			
				if ( $row->mscan_ignored != 'ignore' ) {
				$status = '<strong><font color="blue">'.__('Skipped File', 'bulletproof-security').'<br>'.__('Not Scanned', 'bulletproof-security').'</font></strong>';
				}
			
				if ( $row->mscan_ignored == 'ignore' ) {
					$status = '<strong><font color="green">'.__('Ignored File', 'bulletproof-security').'</font></strong>';
				}		
			}

			if ( $row->mscan_status != '' ) {
			
				if ( $row->mscan_ignored == 'ignore' ) {
					$status = '<strong><font color="green">'.__('Ignored File', 'bulletproof-security').'</font></strong>';				
			
				} else {
			
					if ( $row->mscan_status == 'suspect' ) {
						$status = '<strong><font color="#fb0101">'.__('Suspicious File', 'bulletproof-security').'</font></strong>';
					}
				}
			}
		
			echo '<th scope="row" style="border-bottom:none;">'.$status.'</th>';
			echo "<td><input type=\"checkbox\" id=\"viewfile\" name=\"mscan[$row->mscan_path]\" value=\"viewfile\" /><br><span style=\"font-size:10px;\">".__('View', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"ignorefile\" name=\"mscan[$row->mscan_path]\" value=\"ignorefile\" class=\"ignorefileALL\" /><br><span style=\"font-size:10px;\">".__('Ignore', 'bulletproof-security')."</span></td>";
			
			echo "<td><input type=\"checkbox\" id=\"unignorefile\" name=\"mscan[$row->mscan_path]\" value=\"unignorefile\" class=\"unignorefileALL\" /><br><span style=\"font-size:10px;\">".__('Unignore', 'bulletproof-security')."</span></td>";			
			
			echo "<td><input type=\"checkbox\" id=\"deletefile\" name=\"mscan[$row->mscan_path]\" value=\"deletefile\" class=\"deletefileALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
			echo '<td>'.$row->mscan_path.'</td>';		
			
			if ( preg_match( '/Altered\sor\sunknown(.*)/', $row->mscan_pattern ) ) {
				$hash_pattern = 'File Hash:<br>';
			} else {
				$hash_pattern = 'Pattern Match:<br>';
			}
			
			echo '<td style="max-width:200px">'.$hash_pattern.esc_html($row->mscan_pattern).'</td>';
			echo '<td>'.$row->mscan_time.'</td>'; 
			echo '</tr>';			
		} 

	} else {

		echo '<th scope="row" style="border-bottom:none;font-weight:600;color:green">'.__('No Suspicious Files were detected', 'bulletproof-security').'</th>';
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '<td></td>';
		echo '</tr>';		
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';	

	echo "<input type=\"submit\" name=\"Submit-MScan-Suspect-Form\" value=\"".__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('View File Option: Selecting the View File Checkbox Form option will display the contents of the file that you have selected to view.\n\n-------------------------------------------------------------\n\nIgnore File Option: Selecting the Ignore File Checkbox Form option will change the Current Status of a file to Ignored File and MScan will ignore that file in any future scans.\n\n-------------------------------------------------------------\n\nUnignore File Option: Selecting the Unignore File Checkbox Form option will remove the Ignored File Current Status of a file and MScan will scan that file in any future scans. Note: The previous Status of the file will be displayed again.\n\n-------------------------------------------------------------\n\nDelete File Option: Selecting the Delete File Checkbox Form option will delete the file and delete the database entry for that file.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />";
	echo "<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" style=\"margin-left:20px\" onclick=\"javascript:history.go(0)\" />";
	echo '</form>';

?>

<?php
$UIoptions = get_option('bulletproof_security_options_theme_skin');

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#MScanSuspectcheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallIgnore').click(function() {
	$(this).parents('#MScanSuspectcheckall:eq(0)').find('.ignorefileALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallUnignore').click(function() {
	$(this).parents('#MScanSuspectcheckall:eq(0)').find('.unignorefileALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallDelete').click(function() {
    $(this).parents('#MScanSuspectcheckall:eq(0)').find('.deletefileALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</div>
<h3 id="mscan-accordion-3"><?php _e('View|Ignore Suspicious DB Entries', 'bulletproof-security'); ?></h3>
<div id="mscan-accordion-inner">

<?php
if ( isset( $_GET['mscan_view_db'] ) && 'view_db_entry' == $_GET['mscan_view_db'] ) {
	
	if ( ! wp_verify_nonce( $nonce, 'bps-anti-csrf' ) ) {
		die( 'CSRF Error: Invalid Nonce used in the MScan View DB Entry GET Request' );
			
	} else {

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
		active: 2,
		autoHeight: true,
		clearStyle: true,
		heightStyle: "content"
		});
	});
	/* ]]> */
	</script>

<?php
	}
}

// MScan Suspicious DB Entries Form Proccessing - View, Ignore or Unignore DB Entries
// Note: This form processing code must be above the form so that the View DB Entry output is displayed above the Suspicious DB Entries form.
if ( isset( $_POST['Submit-MScan-Suspect-DB-Form'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_mscan_suspicious_db_entries');
	
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
		active: 2,
		autoHeight: true,
		clearStyle: true,
		heightStyle: "content"
		});
	});
	/* ]]> */
	</script>

<?php

	$mscan_db_entries = $_POST['mscandb'];
	$MStable = $wpdb->prefix . "bpspro_mscan";
	
	switch( $_POST['Submit-MScan-Suspect-DB-Form'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$ignore_db_entries = array();
		$unignore_db_entries = array();
		$view_db_entries = array();		
		
		if ( ! empty($mscan_db_entries) ) {
			
			foreach ( $mscan_db_entries as $key => $value ) {
				
				if ( $value == 'ignoredb' ) {
					$ignore_db_entries[] = $key;
				
				} elseif ( $value == 'unignoredb' ) {
					$unignore_db_entries[] = $key;				

				} elseif ( $value == 'viewdb' ) {
					$view_db_entries[] = $key;
				}
			}
		}
			
		if ( ! empty($ignore_db_entries) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $ignore_db_entries as $ignore_db_entry ) {
				
				$MScanRowsIgnore = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_db_pkid = %s", $ignore_db_entry) );
			
				foreach ( $MScanRowsIgnore as $row ) {
					
					$update_rows = $wpdb->update( $MStable, array( 'mscan_ignored' => 'ignore' ), array( 'mscan_db_pkid' => $row->mscan_db_pkid, 'mscan_db_column' => $row->mscan_db_column ) );	
				
					$text = '<strong><font color="green">'.__('Current Status has been changed to Ignored for DB Row ID', 'bulletproof-security').': '.$row->mscan_db_pkid.' '.__('in DB Column', 'bulletproof-security').': '.$row->mscan_db_column.'.'.__('This DB Entry will not be scanned in any future MScan Scans.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}			
			}
			echo '</p></div>';	
		}

		if ( ! empty($unignore_db_entries) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unignore_db_entries as $unignore_db_entry ) {
				
				$MScanRowsUnignore = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_db_pkid = %s", $unignore_db_entry) );
			
				foreach ( $MScanRowsUnignore as $row ) {
					
					$update_rows = $wpdb->update( $MStable, array( 'mscan_ignored' => '' ), array( 'mscan_db_pkid' => $row->mscan_db_pkid, 'mscan_db_column' => $row->mscan_db_column ) );	
				
					$text = '<strong><font color="green">'.__('The Ignored DB Entry Status has been removed for DB Row ID', 'bulletproof-security').': '.$row->mscan_db_pkid.' '.__('in DB Column', 'bulletproof-security').': '.$row->mscan_db_column.'. '.__('The previous Status of the DB Entry will be displayed again and this DB Entry will be scanned in future MScan scans.', 'bulletproof-security').'</font></strong><br>';
					echo $text;
				}			
			}
			echo '</p></div>';	
		}

		if ( ! empty($view_db_entries) ) {
			
			echo '<div id="message" style="width:97%;margin:-10px 0px 15px 0px;padding:1px 10px 5px 10px;background-color:#dfecf2;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $view_db_entries as $view_db_entry ) {
				
				$MScanRowsView = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_db_pkid = %s", $view_db_entry) );
			
				foreach ( $MScanRowsView as $row ) {
					
					if ( $row->mscan_pattern == 'PharmaHack' ) {
						
						$text = '<div style="margin:0px 0px 5px 0px;font-size:1.13em;font-weight:600"><span style="width:100px;margin:0px;padding:0px 6px 0px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.admin_url( "admin.php?page=bulletproof-security/admin/mscan/mscan.php&mscan_view_db=view_db_entry&_wpnonce=$nonce" ).'" style="text-decoration:none;">'.__('Close', 'bulletproof-security').'</a></span> '.__('Pharma Hack DB Table and Column', 'bulletproof-security').': <span style="background-color:yellow;">'.esc_html($row->mscan_db_table).' : '.esc_html($row->mscan_db_column).'</span><br>'.__('Pharma Hack cleanup/removal steps', 'bulletproof-security').':<br>'.__('Edit your theme\'s header.php file and delete this code: ', 'bulletproof-security').'<\?php include \'nav.php\'; \?>. '.__('Delete this file in your theme\'s root folder: nav.php. Login to your web host control panel, login to your WP Database using phpMyAdmin and delete these DB option name Rows below from the DB Table and Column shown above. Note: You may or may not see all of these DB option name Rows so just delete any that you do see.', 'bulletproof-security').'<br><br>wp_check_hash<br>class_generic_support<br>widget_generic_support<br>ftp_credentials<br>fwp<br>rss_7988287cd8f4f531c6b94fbdbc4e1caf<br>rss_d77ee8bfba87fa91cd91469a5ba5abea<br>rss_552afe0001e673901a9f2caebdd3141d</div>';
						echo $text;

					} else {
						
						$text = '<div style="margin:0px 0px 5px 0px;font-size:1.13em;font-weight:600"><span style="width:100px;margin:0px;padding:0px 6px 0px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.admin_url( "admin.php?page=bulletproof-security/admin/mscan/mscan.php&mscan_view_db=view_db_entry&_wpnonce=$nonce" ).'" style="text-decoration:none;">'.__('Close', 'bulletproof-security').'</a></span> '.__('DB Table, Column and Row ID', 'bulletproof-security').': <span style="background-color:yellow;">'.esc_html($row->mscan_db_table).' : '.esc_html($row->mscan_db_column).' : '.esc_html($row->mscan_db_pkid).'</span> : '.__('MScan Pattern Match', 'bulletproof-security').': <span style="background-color:yellow;">'.esc_html($row->mscan_pattern).'</span><br>'.__('Steps to view the database data that MScan detected as suspicious', 'bulletproof-security').': '.__('Login to your web host control panel, login to your WP Database using phpMyAdmin and check the data in the DB Table, Column and Row ID shown above. Note: Look for code that matches the MScan Pattern Match.', 'bulletproof-security').'<br>'.__('If you are not sure what to check for or what is and is not malicious code then click the MScan Read Me help button.', 'bulletproof-security').'</div>';
						echo $text;
					}
				}			
			}
			echo '</p></div>';			
		}
		break;
	}
}

	$mscan_db_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('Database scanning uses pattern matching scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results will usually detect some false positive matches.', 'bulletproof-security').'<br>'.__('This form allows you to view, ignore or unignore suspicious DB Entries. Note: The view option displays the DB Table, Column, Row ID and the MScan Pattern Match that was detected by the MScan scan.', 'bulletproof-security').'<br>'.__('Before deleting any database data make a backup of your database.', 'bulletproof-security').'<br>'.__('Use phpMyAdmin or a similar tool to check your database Row where the suspicious code was found.', 'bulletproof-security').'<br>'.__('When you ignore a DB Entry it will no longer be scanned in any future scans. When you unignore an ignored DB Entry it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
	echo $mscan_db_scan_help_text;	

	echo '<form name="MScanSuspiciousDBEntries" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_mscan_suspicious_db_entries');
	
	$MStable = $wpdb->prefix . "bpspro_mscan";
	$db_rows = 'db';
	$MScanDBRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_type = %s", $db_rows ) );
	
	echo '<div id="MScanSuspectDBcheckall" style="">';
	echo '<table class="widefat" style="margin-bottom:20px;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:16%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><br><strong>'.__('View<br>DB Entry', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><input type="checkbox" class="checkallIgnoreDB" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Ignore<br>DB Entry', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:7%;"><input type="checkbox" class="checkallUnignoreDB" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unignore<br>DB Entry', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:18%;"><strong>'.__('DB Table', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:18%;"><strong>'.__('DB Column', 'bulletproof-security').'</strong>'.'</th>';
	echo '<th scope="col" style="width:7%;"><strong>'.__('DB Row ID', 'bulletproof-security').'</strong>'.'</th>';
	echo '<th scope="col" style="width:10%;"><strong>'.__('Pattern<br>Match', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:10%;"><strong>'.__('Scan<br>Time', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	if ( $wpdb->num_rows != 0 ) {
	
		foreach ( $MScanDBRows as $row ) {
		
			if ( $row->mscan_ignored == 'ignore' ) {
				$status = '<strong><font color="green">'.__('Ignored DB Entry', 'bulletproof-security').'</font></strong>';				
			
			} else {
			
				if ( $row->mscan_status == 'suspect' ) {
					$status = '<strong><font color="#fb0101">'.__('Suspicious DB Entry', 'bulletproof-security').'</font></strong>';
				}
			}
		
			echo '<th scope="row" style="border-bottom:none;">'.$status.'</th>';
			echo "<td><input type=\"checkbox\" id=\"viewdb\" name=\"mscandb[$row->mscan_db_pkid]\" value=\"viewdb\" /><br><span style=\"font-size:10px;\">".__('View', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"ignoredb\" name=\"mscandb[$row->mscan_db_pkid]\" value=\"ignoredb\" class=\"ignoreDBALL\" /><br><span style=\"font-size:10px;\">".__('Ignore', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"unignoredb\" name=\"mscandb[$row->mscan_db_pkid]\" value=\"unignoredb\" class=\"unignoreDBALL\" /><br><span style=\"font-size:10px;\">".__('Unignore', 'bulletproof-security')."</span></td>";			
			echo '<td>'.$row->mscan_db_table.'</td>';		
			echo '<td>'.$row->mscan_db_column.'</td>';
			echo '<td>'.$row->mscan_db_pkid.'</td>';
			echo '<td style="max-width:200px">'.esc_html($row->mscan_pattern).'</td>';
			echo '<td>'.$row->mscan_time.'</td>'; 
			echo '</tr>';			
		} 

	} else {

		echo '<th scope="row" style="border-bottom:none;font-weight:600;color:green">'.__('No Suspicious DB Entries were detected', 'bulletproof-security').'</th>';
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '</tr>';		
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';	

	echo "<input type=\"submit\" name=\"Submit-MScan-Suspect-DB-Form\" value=\"".__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('View DB Entry Option: Selecting the View DB Entry Checkbox Form option will display the contents of the DB Table, Column and Row ID that you have selected to view.\n\n-------------------------------------------------------------\n\nIgnore DB Entry Option: Selecting the Ignore DB Entry Checkbox Form option will change the Current Status of a DB Entry to Ignored DB Entry and MScan will ignore that DB Entry in any future scans.\n\n-------------------------------------------------------------\n\nUnignore DB Entry Option: Selecting the Unignore DB Entry Checkbox Form option will remove the Ignored DB Entry Current Status of a DB Entry and MScan will scan that DB Entry in any future scans. Note: The previous Status of the DB Entry will be displayed again.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />";
	echo "<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" style=\"margin-left:20px\" onclick=\"javascript:history.go(0)\" />";
	echo '</form>';

$UIoptions = get_option('bulletproof_security_options_theme_skin');

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) { ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#MScanSuspectDBcheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallIgnoreDB').click(function() {
	$(this).parents('#MScanSuspectDBcheckall:eq(0)').find('.ignoreDBALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallUnignoreDB').click(function() {
	$(this).parents('#MScanSuspectDBcheckall:eq(0)').find('.unignoreDBALL:checkbox').attr('checked', this.checked);
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
    <td class="bps-table_title"><h2><?php _e('MScan Log ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Logs extensive details about each scan that you run.', 'bulletproof-security'); ?></span></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('MScan Log', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content2" class="bps-dialog-hide" title="<?php _e('MScan Log', 'bulletproof-security'); ?>">
	<p><?php echo $bps_modal_content2; ?></p>
</div>

<?php

// Get the Current / Last Modifed Date of the MScan Log File
function bpsPro_MScan_Log_LastMod() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
$gmt_offset = get_option( 'gmt_offset' ) * 3600;

if ( file_exists($filename) ) {
	$last_modified = date("F d Y H:i:s", filemtime($filename) + $gmt_offset);
	return $last_modified;
	}
}

// String comparison of MScan Last Modified Time and Actual File Last Modified Time
function bpsPro_MScan_ModTimeDiff() {
$options = get_option('bulletproof_security_options_MScan_log');
$last_modified_time = bpsPro_MScan_Log_LastMod();
$last_modified_time_db = ! isset($options['bps_mscan_log_date_mod']) ? '' : $options['bps_mscan_log_date_mod'];
	
	if ( isset($options['bps_mscan_log_date_mod']) && $options['bps_mscan_log_date_mod'] == '' ) {
		$text = '<font color="#fb0101" style="padding-right:5px;"><strong>'.__('Click the Reset Last Modified Time in DB button', 'bulletproof-security').'<br>'.__('to set the', 'bulletproof-security').'</strong></font>';
		echo $text;
	}
	
	if ( strcmp( $last_modified_time, $last_modified_time_db ) == 0 ) { // 0 is equal
		$text = '<font color="green" style="padding-right:8px;"><strong>'.__('Last Modified Time in DB:', 'bulletproof-security').' </strong></font>';
		echo $text;
	
	} else {
	
		$text = '<font color="#fb0101" style="padding-right:8px;"><strong>'.__('Last Modified Time in DB:', 'bulletproof-security').' </strong></font>';
		echo $text;
	}
}

// Get File Size of the MScan Log File
function bpsPro_MScan_LogSize() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';

if ( file_exists($filename) ) {
	$logSize = filesize($filename);
	
	if ( $logSize < 2097152 ) {
 		$text = '<span style="font-size:13px;"><strong>'. __('MScan Log File Size: ', 'bulletproof-security').'<font color="#2ea2cc">'. round($logSize / 1024, 2) .' KB</font></strong></span><br><br>';
		echo $text;
	} else {
 		$text = '<span style="font-size:13px;"><strong>'. __('MScan Log File Size: ', 'bulletproof-security').'<font color="#fb0101">'. round($logSize / 1024, 2) .' KB<br>'.__('The S-Monitor Email Logging options will only send log files up to 2MB in size.', 'bulletproof-security').'</font></strong><br>'.__('Copy and paste the MScan Log file contents into a Notepad text file on your computer and save it.', 'bulletproof-security').'<br>'.__('Then click the Delete Log button to delete the contents of this Log file.', 'bulletproof-security').'</span><br><br>';		
		echo $text;
	}
	}
}
bpsPro_MScan_LogSize();
?>

<form name="MScanLogModDate" action="options.php#bps-tabs-2" method="post">
	<?php settings_fields('bulletproof_security_options_MScan_log'); ?> 
	<?php $MScanLogoptions = get_option('bulletproof_security_options_MScan_log'); 
		$bps_mscan_log_date_mod = ! isset($MScanLogoptions['bps_mscan_log_date_mod']) ? '' : $MScanLogoptions['bps_mscan_log_date_mod'];
	?>
    <label for="QLog"><strong><?php _e('MScan Log Last Modified Time:', 'bulletproof-security'); ?></strong></label><br />
	<label for="QLog"><strong><?php echo bpsPro_MScan_ModTimeDiff(); ?></strong><?php echo $bps_mscan_log_date_mod; ?></label><br />
	<label for="QLog" style="vertical-align:top;"><strong><?php _e('Last Modified Time in File:', 'bulletproof-security'); ?></strong></label>
    <input type="text" name="bulletproof_security_options_MScan_log[bps_mscan_log_date_mod]" style="color:#2ea2cc;font-size:13px;width:200px;margin-top:-6px;padding-left:4px;font-weight:600;border:none;background:none;outline:none;-webkit-box-shadow:none;box-shadow:none;-webkit-transition:none;transition:none;" value="<?php echo bpsPro_MScan_Log_LastMod(); ?>" /><br />
	<input type="submit" name="Submit-MScan-Mod" class="button bps-button" style="margin:10px 0px 0px 0px;" value="<?php esc_attr_e('Reset Last Modified Time in DB', 'bulletproof-security') ?>" />
</form>

<?php
if ( isset( $_POST['Submit-Delete-MScan-Log'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_delete_mscan_log' );

?>	
	<script type="text/javascript">
	/* <![CDATA[ */
	// Note: Active Tab numbering is literal from left to right.
	jQuery(document).ready(function($){
		$( "#bps-tabs" ).tabs({
		active: 1
		});
	});	
	/* ]]> */
	</script>

<?php

	$options = get_option('bulletproof_security_options_MScan_log');
	$last_modified_time_db = $options['bps_mscan_log_date_mod'];
	$time = strtotime($last_modified_time_db); 
	$MscanLog = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	$MscanLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/mscan_log.txt';
	
	if ( copy($MscanLogMaster, $MscanLog) ) {
		touch($MscanLog, $time);	
	
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Success! Your MScan Log has been deleted and replaced with a new blank MScan Log file.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;	
	}
}
?>

<form name="DeleteMScanLogForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php#bps-tabs-2' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_delete_mscan_log'); ?>

<input type="submit" name="Submit-Delete-MScan-Log" value="<?php esc_attr_e('Delete Log', 'bulletproof-security') ?>" class="button bps-button" style="margin:15px 0px 15px 0px" onclick="return confirm('<?php $text = __('Clicking OK will delete the contents of your MScan Log file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Delete the Log file contents or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>

<div id="messageinner" class="updatedinner">
<?php

// Get MScan log file contents
function bpsPro_MScan_get_contents() {
	
	if ( current_user_can('manage_options') ) {
		$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
		$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

	if ( file_exists($mscan_log) ) {
		$mscan_log = file_get_contents($mscan_log);
		return htmlspecialchars($mscan_log);
	
	} else {
	
	_e('The MScan Log File Was Not Found! Check that the file really exists here - /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/logs/mscan_log.txt and is named correctly.', 'bulletproof-security');
	}
	}
}

// Form: MScan Log editor
if ( current_user_can('manage_options') ) {
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	$write_test = "";
	
	if ( is_writable($mscan_log) ) {
    if ( ! $handle = fopen($mscan_log, 'a+b' ) ) {
    exit;
    }
    
	if ( fwrite($handle, $write_test) === FALSE ) {
	exit;
    }
	
	$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your MScan Log file is writable.', 'bulletproof-security').'</strong></font><br>';
	echo $text;
	}
	}
	
	if ( isset( $_POST['Submit-MScan-Log'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_mscan_log' );
		$newcontent_mscan = stripslashes( $_POST['newcontent_mscan'] );
	
	if ( is_writable($mscan_log) ) {
		$handle = fopen($mscan_log, 'w+b');
		fwrite($handle, $newcontent_mscan);
		$text = '<font color="green" style="font-size:12px;"><strong>'.__('Success! Your MScan Log file has been updated.', 'bulletproof-security').'</strong></font><br>';
		echo $text;	

		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Success! Your MScan Log file has been updated.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;

    	fclose($handle);

		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$time_now = date("F d Y H:i:s", time() + $gmt_offset );
		$MScanLog_Options = array( 'bps_mscan_log_date_mod' => $time_now );
	
		foreach( $MScanLog_Options as $key => $value ) {
			update_option('bulletproof_security_options_MScan_log', $MScanLog_Options);
		}
	}
}

$scrolltomsblog = isset($_REQUEST['scrolltomsblog']) ? (int) $_REQUEST['scrolltomsblog'] : 0;
?>
</div>

<div id="QLogEditor">
<form name="MScanLog" id="MScanLog" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php#bps-tabs-2' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_mscan_log'); ?>
<div id="MScanLog">
    <textarea class="bps-text-area-600x700" name="newcontent_mscan" id="newcontent_mscan" tabindex="1"><?php echo bpsPro_MScan_get_contents(); ?></textarea>
	<input type="hidden" name="scrolltomsblog" id="scrolltomsblog" value="<?php echo esc_html( $scrolltomsblog ); ?>" />
    <p class="submit">
	<input type="submit" name="Submit-MScan-Log" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#MScanLog').submit(function(){ $('#scrolltomsblog').val( $('#newcontent_mscan').scrollTop() ); });
	$('#newcontent_mscan').scrollTop( $('#scrolltomsblog').val() ); 
});
/* ]]> */
</script>

</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-3" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('MScan Report ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Displays the current scan results data. Click the Save MScan Report button to save MScan Reports.', 'bulletproof-security'); ?></span></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('MScan Report', 'bulletproof-security'); ?>  <button id="bps-open-modal3" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content3" class="bps-dialog-hide" title="<?php _e('MScan Report', 'bulletproof-security'); ?>">
	<p><?php echo $bps_modal_content3; ?></p>
</div>

<form name="MScanSaveReport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php#bps-tabs-3' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mscan_save_report'); ?>
    <input type="submit" id="bps-mscan-save-report-button" name="Submit-MScan-Save-Report" style="margin:5px 0px 15px 0px;" value="<?php esc_attr_e('Save MScan Report', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to save the MScan Report or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>

<style>
#bps-container div.mscan-report-row-title-large{font-size:1.5em;font-weight:600;margin:0px 0px 10px 0px}
#bps-container div.mscan-report-row-title{font-size:1.25em;font-weight:600;margin:0px 0px 5px 0px}
#bps-container div.mscan-report-row{font-size:1.13em;margin:0px 0px 0px 0px}
#bps-container div.mscan-report-row-small{font-size:1em;margin:0px 0px 10px 0px}
</style>

<?php 

// MScan Report: Displays the current Report & contains the MScan Save Report Form processing code.
function bpsPro_mscan_report() {
global $wpdb, $wp_version, $bps_topDiv, $bps_bottomDiv;

	$MScan_options = get_option('bulletproof_security_options_MScan');
	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	
	if ( ! isset($MScan_status['bps_mscan_last_scan_timestamp']) || isset($MScan_status['bps_mscan_last_scan_timestamp']) && $MScan_status['bps_mscan_last_scan_timestamp'] == '' ) {
		
		echo '<h2>'.__('No Scan Results To Display: No scans have been run yet or you clicked the MScan Reset button.', 'bulletproof-security').'</h2>';
		echo '<div id="bps-page-height" style="height:400px"></div>';
	
	} else {

		$bps_mscan_total_time = $MScan_status['bps_mscan_total_time'];
		
		$hours = (int)($bps_mscan_total_time / 60 / 60);
		$minutes = (int)($bps_mscan_total_time / 60) - $hours * 60;
		$seconds = (int)$bps_mscan_total_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

		$mscan_report_timestamp = '<div class="mscan-report-row-title-large">'.__('Scan Date|Time: ', 'bulletproof-security') . $MScan_status['bps_mscan_last_scan_timestamp'].'</div>';
		echo $mscan_report_timestamp;

		$mscan_report_website_domain = '<div class="mscan-report-row-title">'.__('Website: ', 'bulletproof-security') . get_bloginfo( 'url' ).'</div>';
		echo $mscan_report_website_domain;

		$mscan_report_total_scan_time = '<div class="mscan-report-row-title">'.__('Scan Completion Time: ', 'bulletproof-security') . $hours_format . ':'. $minutes_format . ':' . $seconds_format.'</div>';
		echo $mscan_report_total_scan_time;

		$mscan_report_total_files_scanned = '<div class="mscan-report-row-title">'.__('Total Files Scanned: ', 'bulletproof-security') . $MScan_status['bps_mscan_total_all_scannable_files'].'</div>';
		echo $mscan_report_total_files_scanned;

		$mscan_report_total_skipped_files = '<div class="mscan-report-row-title">'.__('Total Skipped Files: ', 'bulletproof-security') . $MScan_status['bps_mscan_total_skipped_files'].'</div>';
		echo $mscan_report_total_skipped_files;

		if ( $MScan_status['bps_mscan_total_suspect_files'] == '' ) {
			$bps_mscan_total_suspect_files = 0;
		} else {
			$bps_mscan_total_suspect_files = $MScan_status['bps_mscan_total_suspect_files'];		
		}

		$mscan_report_total_suspicious_files = '<div class="mscan-report-row-title">'.__('Total Suspicious Files: ', 'bulletproof-security') .$bps_mscan_total_suspect_files.'</div>';
		echo $mscan_report_total_suspicious_files;	

		if ( $MScan_status['bps_mscan_total_suspect_db'] == '' ) {
			$bps_mscan_total_suspect_db = 0;
		} else {
			$bps_mscan_total_suspect_db = $MScan_status['bps_mscan_total_suspect_db'];
		}
		
		$mscan_report_total_suspicious_db_entries = '<div class="mscan-report-row-title" style="border-bottom:2px solid #999999;padding-bottom:10px">'.__('Total Suspicious DB Entries: ', 'bulletproof-security') . $bps_mscan_total_suspect_db.'</div>';
		echo $mscan_report_total_suspicious_db_entries;	
		
		$mscan_report_option_settings = '<div class="mscan-report-row-title-large" style="padding-top:5px">'.__('MScan Option Settings: ', 'bulletproof-security') . '</div>';
		echo $mscan_report_option_settings;
		
		$mscan_dirs_array = array();
		
		foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
			
			if ( $value == '1' ) {
				$mscan_dirs_array[] = $key;
			}
		}		
		
		if ( $MScan_options['bps_mscan_dirs'] != '' ) {
			$mscan_dirs = implode( ', ', $mscan_dirs_array );
			$mscan_report_folders_to_scan = '<div class="mscan-report-row"><strong>'.__('Website Folders & Files To Scan: ', 'bulletproof-security'). '</strong>' .$mscan_dirs.'</div>';
			echo $mscan_report_folders_to_scan;
		} else {
			$mscan_report_folders_to_scan = '<div class="mscan-report-row"><strong>'.__('Website Folders & Files To Scan: ', 'bulletproof-security'). '</strong>' .__('None', 'bulletproof-security').'</div>';
			echo $mscan_report_folders_to_scan;		
		}

		if ( $MScan_options['mscan_exclude_dirs'] != '' ) {	
		
			$mscan_exclude_dirs = implode( '', explode( "\n", $MScan_options['mscan_exclude_dirs'] ) );
			$mscan_exclude_dirs_replace = str_replace( array( "", "\n", "\r\n", "\r" ), ", ", $mscan_exclude_dirs );
			$mscan_report_excluded_dirs = '<div class="mscan-report-row"><strong>'.__('Excluded Folders: ', 'bulletproof-security'). '</strong>' .$mscan_exclude_dirs_replace.'</div>';
			echo $mscan_report_excluded_dirs;			
		} else {
			$mscan_report_excluded_dirs = '<div class="mscan-report-row"><strong>'.__('Excluded Folders: ', 'bulletproof-security'). '</strong>' .__('None', 'bulletproof-security').'</div>';			
			echo $mscan_report_excluded_dirs;
		}

		$mscan_report_max_file_size = '<div class="mscan-report-row"><strong>'.__('Max File Size Limit to Scan: ', 'bulletproof-security'). '</strong>' .$MScan_options['mscan_max_file_size'].' KB</div>';
		echo $mscan_report_max_file_size;
		
		$mscan_report_max_time_limit = '<div class="mscan-report-row"><strong>'.__('Max Time Limit to Scan: ', 'bulletproof-security'). '</strong>' .$MScan_options['mscan_max_time_limit'].' Seconds</div>';
		echo $mscan_report_max_time_limit;		

		$mscan_report_db_scan = '<div class="mscan-report-row"><strong>'.__('Scan Database: ', 'bulletproof-security'). '</strong>' .$MScan_options['mscan_scan_database'].'</div>';
		echo $mscan_report_db_scan;	

		$mscan_report_skipped_file_scan = '<div class="mscan-report-row"><strong>'.__('Scan Skipped Files Only: ', 'bulletproof-security'). '</strong>' .$MScan_options['mscan_scan_skipped_files'].'</div>';
		echo $mscan_report_skipped_file_scan;	

		$mscan_report_delete_tmp_files = '<div class="mscan-report-row"><strong>'.__('Automatically Delete /tmp Files: ', 'bulletproof-security'). '</strong>' .$MScan_options['mscan_scan_delete_tmp_files'].'</div>';
		echo $mscan_report_delete_tmp_files;

		if ( $MScan_options['mscan_exclude_tmp_files'] != '' ) {	
		
			$mscan_exclude_tmp_files = implode( '', explode( "\n", $MScan_options['mscan_exclude_tmp_files'] ) );
			$mscan_exclude_tmp_files_replace = str_replace( array( "", "\n", "\r\n", "\r" ), ", ", $mscan_exclude_tmp_files );
			$mscan_exclude_tmp_files_trim = trim( $mscan_exclude_tmp_files_replace, ", " );
			$mscan_report_exclude_tmp_files = '<div class="mscan-report-row"><strong>'.__('Exclude /tmp Files: ', 'bulletproof-security'). '</strong>' .$mscan_exclude_tmp_files_trim.'</div>';
			echo $mscan_report_exclude_tmp_files;			
		} else {
			$mscan_report_exclude_tmp_files = '<div class="mscan-report-row"><strong>'.__('Exclude /tmp Files: ', 'bulletproof-security'). '</strong>' .__('None', 'bulletproof-security').'</div>';			
			echo $mscan_report_exclude_tmp_files;
		}

		if ( $MScan_options['mscan_scan_frequency'] == '60' ) {
			$mscan_scan_frequency = 'Run Scan Every 60 Minutes';
		} elseif ( $MScan_options['mscan_scan_frequency'] == '180' ) {
			$mscan_scan_frequency = 'Run Scan Every 3 Hours';		
		} elseif ( $MScan_options['mscan_scan_frequency'] == '360' ) {
			$mscan_scan_frequency = 'Run Scan Every 6 Hours';			
		} elseif ( $MScan_options['mscan_scan_frequency'] == '720' ) {
			$mscan_scan_frequency = 'Run Scan Every 12 Hours';
		} elseif ( $MScan_options['mscan_scan_frequency'] == '1440' ) {
			$mscan_scan_frequency = 'Run Scan Every 24 Hours';
		} else {
			$mscan_scan_frequency = 'Off';
		}

		$mscan_report_scheduled_scan = '<div class="mscan-report-row" style="padding-bottom:10px"><strong>'.__('Scheduled Scan Frequency: ', 'bulletproof-security'). '</strong>' .$mscan_scan_frequency.'</div>';
		echo $mscan_report_scheduled_scan;

		$mscan_report_file_hashes = '<div class="mscan-report-row-title-large" style="border-top:2px solid #999999;padding-top:10px">'.__('WP Core|Plugin|Theme File Hashes: ', 'bulletproof-security') . '</div>';
		echo $mscan_report_file_hashes;

		$wp_hashes_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';

		if ( file_exists($wp_hashes_file) ) {
			$check_string = file_get_contents($wp_hashes_file);
			$wp_core_hash_file_version = preg_match( '/WordPress\s(\d\.){1,}\d\sHashes/', $check_string, $matches );
			$wp_core_hash_file_version_replace = preg_replace( array( '/WordPress\s/', '/\sHashes/' ), "", $matches[0] );
			
			$mscan_report_core_hash_version_comparison = '<div class="mscan-report-row"><strong>'.__('WP Core Hash File Version: ', 'bulletproof-security'). '</strong>' .$wp_core_hash_file_version_replace.' | <strong>'.__('WP Installed Version: ', 'bulletproof-security').'</strong>' .$wp_version.'</div>';
			echo $mscan_report_core_hash_version_comparison;			

		} else {
			
			$mscan_report_core_hash_version_comparison = '<div class="mscan-report-row"><strong><font color="#fb0101">'.__('Error|Problem: ', 'bulletproof-security'). '</font></strong>' .__('The WP Core Hash File Does Not Exist', 'bulletproof-security').'</div>';
			echo $mscan_report_core_hash_version_comparison;
		}

		$plugin_hash_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
		$theme_hash_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';

		$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
		$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
		$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
		$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
		$mscan_nodownload = get_option('bulletproof_security_options_mscan_nodownload');
		$mscan_zip_upload_options = get_option('bulletproof_security_options_mscan_zip_upload');
		
		$mscan_report_plugin_hash_title = '<div class="mscan-report-row-title" style="padding-top:8px">'.__('Plugin File Hashes: ', 'bulletproof-security').'</div>';
		echo $mscan_report_plugin_hash_title;

		$mscan_report_plugin_hash_version_comparison_array = array();

		if ( ! file_exists($plugin_hash_file) ) {
			$mscan_report_plugin_hash_version_comparison = '<div class="mscan-report-row"><strong><font color="#fb0101">'.__('Error|Problem: ', 'bulletproof-security'). '</font></strong>' .__('The Plugin Hash File Does Not Exist', 'bulletproof-security').'</div>';
			echo $mscan_report_plugin_hash_version_comparison;
			$mscan_report_plugin_hash_version_comparison_array[] = $mscan_report_plugin_hash_version_comparison;
		
		} else {
		
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
							$active_plugins_array[$plugin_name] = $value['Version'];
						
						} else {
							
							if ( $dolly_pos !== false ) {
							
								$hello_dolly_plugin_array['hello-dolly'] = $value['Version'];
							}				
						}
					
					} else {
						
						$pos = strpos($key, '/');
						$dolly_pos = strpos($value['Name'], 'Hello Dolly');
						
						if ( $pos !== false ) {
							
							$plugin_name = strstr($key, '/', true);
							$inactive_plugins_array[$plugin_name] = $value['Version'];	
						
						} else {
							
							if ( $dolly_pos !== false ) {
							
								$hello_dolly_plugin_array['hello-dolly'] = $value['Version'];
							}
						}
					}
				}
			}
		
			$plugins_array_merged = array_merge($active_plugins_array, $inactive_plugins_array, $hello_dolly_plugin_array);
	
			$hover_icon_plugin_hash = '<strong><font color="black"><span class="tooltip-350-150"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('File hashes do not exist for this plugin. This plugin\'s files were not scanned. If you would like to scan this plugin\'s files then use the "Upload Plugin Zip Files" Form to upload a zip file for this plugin. Click the MScan 2.0 Read Me help button on the MScan 2.0 tab page and read the "Upload Plugin Zip Files" help section for more help info.', 'bulletproof-security').'</span></span></font></strong>';
	
			$hover_icon_theme_hash = '<strong><font color="black"><span class="tooltip-350-150"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('File hashes do not exist for this theme. This theme\'s files were not scanned. If you would like to scan this themes\'s files then use the "Upload Theme Zip Files" Form to upload a zip file for this theme. Click the MScan 2.0 Read Me help button on the MScan 2.0 tab page and read the "Upload Theme Zip Files" help section for more help info.', 'bulletproof-security').'</span></span></font></strong>';
	
			foreach ( $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'] as $key => $value ) {
				
				foreach ( $plugins_array_merged as $key2 => $value2 ) {
					
					if ( $key == $key2 && ! in_array( $key2, $mscan_nodownload['bps_plugin_nodownload'] ) ) {
	
						$mscan_report_plugin_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Plugin Hash File Version: ', 'bulletproof-security'). '</strong>' .$value.' | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.'</div>';
						echo $mscan_report_plugin_hash_version_comparison;
						$mscan_report_plugin_hash_version_comparison_array[] = $mscan_report_plugin_hash_version_comparison;
				
					}
				
					if ( $key == $key2 && in_array( $key2, $mscan_nodownload['bps_plugin_nodownload'] ) ) {
	
						if ( isset(	$mscan_zip_upload_options['bps_mscan_plugin_zip_upload'] ) && array_key_exists( $key2, $mscan_zip_upload_options['bps_mscan_plugin_zip_upload'] ) ) {
						
							$mscan_report_plugin_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Plugin Hash File Version: ', 'bulletproof-security'). '</strong>' .$value.' | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.'</div>';
							echo $mscan_report_plugin_hash_version_comparison;
							$mscan_report_plugin_hash_version_comparison_array[] = $mscan_report_plugin_hash_version_comparison;
						
						} else {
							
							$mscan_report_plugin_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Plugin Hash File Version: ', 'bulletproof-security'). '</strong><strong><font color="blue">'.__('No File Hashes for This Plugin', 'bulletproof-security').'</font></strong> | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.$hover_icon_plugin_hash.'</div>';
							echo $mscan_report_plugin_hash_version_comparison;
							$mscan_report_plugin_hash_version_comparison_array[] = $mscan_report_plugin_hash_version_comparison;				
						}
					}
				}
			}
		}

		$mscan_report_theme_hash_title = '<div class="mscan-report-row-title" style="padding-top:8px">'.__('Theme File Hashes: ', 'bulletproof-security').'</div>';
		echo $mscan_report_theme_hash_title;

		$mscan_report_theme_hash_version_comparison_array = array();

		if ( ! file_exists($theme_hash_file) ) {
			$mscan_report_theme_hash_version_comparison = '<div class="mscan-report-row"><strong><font color="#fb0101">'.__('Error|Problem: ', 'bulletproof-security'). '</font></strong>' .__('The Theme Hash File Does Not Exist', 'bulletproof-security').'</div>';
			echo $mscan_report_theme_hash_version_comparison;
			$mscan_report_theme_hash_version_comparison_array[] = $mscan_report_theme_hash_version_comparison;
		
		} else {

			$all_themes = wp_get_themes();
			$all_themes_array = array();
		
			foreach ( $all_themes as $key => $value ) {
					
				if ( ! empty($key) ) {
					$all_themes_array[$key] = $value['Version'];
				}
			}
	
			foreach ( $mscan_theme_hash['bps_mscan_theme_hash_version_check'] as $key => $value ) {
				
				foreach ( $all_themes_array as $key2 => $value2 ) {
					
					if ( $key == $key2 && ! in_array( $key2, $mscan_nodownload['bps_theme_nodownload'] ) ) {
	
						$mscan_report_theme_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Theme Hash File Version: ', 'bulletproof-security'). '</strong>' .$value.' | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.'</div>';
						echo $mscan_report_theme_hash_version_comparison;
						$mscan_report_theme_hash_version_comparison_array[] = $mscan_report_theme_hash_version_comparison;				
					}
				
					if ( $key == $key2 && in_array( $key2, $mscan_nodownload['bps_theme_nodownload'] ) ) {
	
						if ( isset(	$mscan_zip_upload_options['bps_mscan_theme_zip_upload'] ) && array_key_exists( $key2, $mscan_zip_upload_options['bps_mscan_theme_zip_upload'] ) ) {
						
							$mscan_report_theme_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Theme Hash File Version: ', 'bulletproof-security'). '</strong>' .$value.' | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.'</div>';
							echo $mscan_report_theme_hash_version_comparison;
							$mscan_report_theme_hash_version_comparison_array[] = $mscan_report_theme_hash_version_comparison;	
						
						} else {
							
							$mscan_report_theme_hash_version_comparison = '<div class="mscan-report-row"><strong>'.$key.__(' Theme Hash File Version: ', 'bulletproof-security'). '</strong><strong><font color="blue">'.__('No File Hashes for This Theme', 'bulletproof-security').'</font></strong> | <strong>'.$key2.__(' Installed Version: ', 'bulletproof-security').'</strong>' .$value2.$hover_icon_theme_hash.'</div>';
							echo $mscan_report_theme_hash_version_comparison;
							$mscan_report_theme_hash_version_comparison_array[] = $mscan_report_theme_hash_version_comparison;							
						}
					}
				}
			}
		}
		
		$mscan_report_scan_results_title_spacer = '<div class="spacer" style="padding-top:10px"></div>';
		echo $mscan_report_scan_results_title_spacer;		

		$mscan_report_scan_results_title = '<div class="mscan-report-row-title-large" style="border-top:2px solid #999999;padding-top:10px">'.__('Scan Results ', 'bulletproof-security').'</div>';
		echo $mscan_report_scan_results_title;

		$mscan_report_file_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('File hash comparison scan results are 100% accurate. WP Core, Plugin and Theme files are scanned using file hash comparison scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results are less accurate and will usually detect some false positive matches. All other files that are not WP Core, Plugin and Theme files are scanned using pattern matching scanning.', 'bulletproof-security').'<br>'.__('You can View, Ignore and Delete files detected as suspicious using the View|Ignore|Delete Suspicious Files Form on the MScan 2.0 tab page. Before deleting any files make a backup of those files on your computer not on your hosting account.', 'bulletproof-security').'<br>'.__('And of course check the file contents of suspicious files to see if they contain hacker code or are false positive matches. Use the Ignore File checkbox option to ignore false postive matches.', 'bulletproof-security').'<br>'.__('When you ignore a file it will no longer be scanned in any future scans. When you unignore an ignored file it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
		echo $mscan_report_file_scan_help_text;	

		$mscan_report_scan_results_file_scan_array = array();	

		$MStable = $wpdb->prefix . "bpspro_mscan";
		$db_rows = 'db';
		$clean_rows = 'clean';
		$safe_rows = 'safe';
		$MScanFilesRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_type != %s AND mscan_status != %s AND mscan_status != %s", $db_rows, $clean_rows, $safe_rows ) );
		
		echo '<div id="MScanSuspectcheckall" style="">';
		echo '<table class="widefat" style="margin-bottom:20px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:10%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:50%;"><strong>'.__('File Path', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:25%;"><strong>'.__('File Hash or Pattern Match', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:15%;"><strong>'.__('Scan Time', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanFilesRows as $row ) {
			
				if ( $row->mscan_status == '' ) {
				
					if ( $row->mscan_ignored != 'ignore' ) {
					$status = '<strong><font color="blue">'.__('Skipped File', 'bulletproof-security').'<br>'.__('Not Scanned', 'bulletproof-security').'</font></strong>';
					}
				
					if ( $row->mscan_ignored == 'ignore' ) {
						$status = '<strong><font color="green">'.__('Ignored File', 'bulletproof-security').'</font></strong>';
					}		
				}
	
				if ( $row->mscan_status != '' ) {
				
					if ( $row->mscan_ignored == 'ignore' ) {
						$status = '<strong><font color="green">'.__('Ignored File', 'bulletproof-security').'</font></strong>';				
				
					} else {
				
						if ( $row->mscan_status == 'suspect' ) {
							$status = '<strong><font color="#fb0101">'.__('Suspicious File', 'bulletproof-security').'</font></strong>';
						}
					}
				}
			
				echo '<th scope="row" style="border-bottom:none;">'.$status.'</th>';
				
				if ( preg_match( '/Altered\sor\sunknown(.*)/', $row->mscan_pattern ) ) {
					$hash_pattern = 'File Hash: ';
				} else {
					$hash_pattern = 'Pattern Match: ';
				}
				
				echo '<td>'.$row->mscan_path.'</td>';		
				echo '<td style="max-width:200px">'.$hash_pattern.esc_html($row->mscan_pattern).'</td>';
				echo '<td>'.$row->mscan_time.'</td>'; 
				echo '</tr>';	
				
					$mscan_report_scan_results_file_scan_array[] = array( $status, $row->mscan_path, $hash_pattern.esc_html($row->mscan_pattern), $row->mscan_time );
			} 
	
		} else {
	
			echo '<th scope="row" style="border-bottom:none;font-weight:600;color:green">'.__('No Suspicious Files were detected', 'bulletproof-security').'</th>';
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '<td></td>';
			echo '</tr>';		
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';	

		$mscan_report_db_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('Database scanning uses pattern matching scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results will usually detect some false positive matches.', 'bulletproof-security').'<br>'.__('You can View, Ignore and Unignore suspicious DB Entries using the View|Ignore Suspicious DB Entries Form on the MScan 2.0 tab page. Before deleting any database data make a backup of your database.', 'bulletproof-security').'<br>'.__('Use phpMyAdmin or a similar tool to check your database Row where the suspicious code was found.', 'bulletproof-security').'<br>'.__('When you ignore a DB Entry it will no longer be scanned in any future scans. When you unignore an ignored DB Entry it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
		echo $mscan_report_db_scan_help_text;	

		$mscan_report_scan_results_db_scan_array = array();	

		$MStable = $wpdb->prefix . "bpspro_mscan";
		$db_rows = 'db';
		$MScanDBRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_type = %s", $db_rows ) );
		
		echo '<div id="MScanSuspectDBcheckall" style="">';
		echo '<table class="widefat" style="margin-bottom:10px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('DB Table', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('DB Column', 'bulletproof-security').'</strong>'.'</th>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('DB Row ID', 'bulletproof-security').'</strong>'.'</th>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('Pattern Match', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:16%;"><strong>'.__('Scan Time', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanDBRows as $row ) {
			
				if ( $row->mscan_ignored == 'ignore' ) {
					$status = '<strong><font color="green">'.__('Ignored DB Entry', 'bulletproof-security').'</font></strong>';				
				
				} else {
				
					if ( $row->mscan_status == 'suspect' ) {
						$status = '<strong><font color="#fb0101">'.__('Suspicious DB Entry', 'bulletproof-security').'</font></strong>';
					}
				}
			
				echo '<th scope="row" style="border-bottom:none;">'.$status.'</th>';
				echo '<td>'.$row->mscan_db_table.'</td>';		
				echo '<td>'.$row->mscan_db_column.'</td>';
				echo '<td>'.$row->mscan_db_pkid.'</td>';
				echo '<td style="max-width:200px">'.esc_html($row->mscan_pattern).'</td>';
				echo '<td>'.$row->mscan_time.'</td>'; 
				echo '</tr>';
				
				$mscan_report_scan_results_db_scan_array[] = array( $status, $row->mscan_db_table, $row->mscan_db_column, $row->mscan_db_pkid, esc_html($row->mscan_pattern), $row->mscan_time );			
			} 
	
		} else {
	
			echo '<th scope="row" style="border-bottom:none;font-weight:600;color:green">'.__('No Suspicious DB Entries were detected', 'bulletproof-security').'</th>';
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '</tr>';		
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';	
	}

	// MScan Save Report Form processing
	if ( isset( $_POST['Submit-MScan-Save-Report'] ) && current_user_can('manage_options') ) {
		check_admin_referer('bulletproof_security_mscan_save_report');

		$bps_mscan_report_timestamp = array( 
		'Scan Report' => $mscan_report_timestamp, $mscan_report_website_domain, $mscan_report_total_scan_time, $mscan_report_total_files_scanned, $mscan_report_total_skipped_files, $mscan_report_total_suspicious_files, $mscan_report_total_suspicious_db_entries, 
		array( 'MScan Option Settings:' => $mscan_report_folders_to_scan, $mscan_report_excluded_dirs, $mscan_report_max_file_size, $mscan_report_max_time_limit, $mscan_report_db_scan, $mscan_report_skipped_file_scan, $mscan_report_delete_tmp_files, $mscan_report_exclude_tmp_files, $mscan_report_scheduled_scan, 
		array( 'WP Core|Plugin|Theme File Hashes:' => $mscan_report_core_hash_version_comparison, 
		array( 'Plugin File Hashes:' => $mscan_report_plugin_hash_version_comparison_array, 
		array( 'Theme File Hashes:' => $mscan_report_theme_hash_version_comparison_array, 
		array( 'Scan Results:' => 
		array( 'File Scan' => $mscan_report_scan_results_file_scan_array,
		array( 'DB Scan' => $mscan_report_scan_results_db_scan_array 
		) ) ) ) ) ) ) );
	
		$bps_mscan_report_data_2 = array( '' => '' );
		$bps_mscan_report_data_3 = array( '' => '' );		
		$bps_mscan_report_data_4 = array( '' => '' );
		$bps_mscan_report_data_5 = array( '' => '' );
		$bps_mscan_report_data_6 = array( '' => '' );
		$bps_mscan_report_data_7 = array( '' => '' );
		$bps_mscan_report_data_8 = array( '' => '' );		
		$bps_mscan_report_data_9 = array( '' => '' );
		$bps_mscan_report_data_10 = array( '' => '' );
		$bps_mscan_report_data_11 = array( '' => '' );
		$bps_mscan_report_data_12 = array( '' => '' );
		$bps_mscan_report_data_13 = array( '' => '' );		
		$bps_mscan_report_data_14 = array( '' => '' );
		$bps_mscan_report_data_15 = array( '' => '' );
		$bps_mscan_report_data_16 = array( '' => '' );
		$bps_mscan_report_data_17 = array( '' => '' );
		$bps_mscan_report_data_18 = array( '' => '' );		
		$bps_mscan_report_data_19 = array( '' => '' );
		$bps_mscan_report_data_20 = array( '' => '' );
		
		$Mscan_Report_Options = array( 
		'bps_mscan_report_data_1' 	=> $bps_mscan_report_timestamp, 
		'bps_mscan_report_data_2' 	=> $bps_mscan_report_data_2, 
		'bps_mscan_report_data_3' 	=> $bps_mscan_report_data_3, 
		'bps_mscan_report_data_4' 	=> $bps_mscan_report_data_4, 
		'bps_mscan_report_data_5' 	=> $bps_mscan_report_data_5, 
		'bps_mscan_report_data_6' 	=> $bps_mscan_report_data_6, 
		'bps_mscan_report_data_7' 	=> $bps_mscan_report_data_7, 
		'bps_mscan_report_data_8' 	=> $bps_mscan_report_data_8, 
		'bps_mscan_report_data_9' 	=> $bps_mscan_report_data_9, 
		'bps_mscan_report_data_10' 	=> $bps_mscan_report_data_10, 
		'bps_mscan_report_data_11' 	=> $bps_mscan_report_data_11, 
		'bps_mscan_report_data_12' 	=> $bps_mscan_report_data_12, 
		'bps_mscan_report_data_13' 	=> $bps_mscan_report_data_13, 
		'bps_mscan_report_data_14' 	=> $bps_mscan_report_data_14, 
		'bps_mscan_report_data_15' 	=> $bps_mscan_report_data_15, 
		'bps_mscan_report_data_16' 	=> $bps_mscan_report_data_16, 
		'bps_mscan_report_data_17' 	=> $bps_mscan_report_data_17, 
		'bps_mscan_report_data_18' 	=> $bps_mscan_report_data_18, 
		'bps_mscan_report_data_19' 	=> $bps_mscan_report_data_19, 
		'bps_mscan_report_data_20' 	=> $bps_mscan_report_data_20 
		);
			
		if ( ! get_option( 'bulletproof_security_options_mscan_report' ) ) {

			foreach( $Mscan_Report_Options as $key => $value ) {
				update_option('bulletproof_security_options_mscan_report', $Mscan_Report_Options);
			}
		
			$text = '<strong><font color="green">'.__('The MScan Report was saved successfully. Saved MScan Reports can be viewed on the MScan Saved Reports tab page.', 'bulletproof-security').'</font></strong>';
		
			echo $bps_topDiv;
			echo $text;		
			echo $bps_bottomDiv;
		
		} else {
			
			$report_options = get_option('bulletproof_security_options_mscan_report'); 
			
			$bps_mscan_report_data_1 = isset($report_options['bps_mscan_report_data_1']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) ? $report_options['bps_mscan_report_data_1'] : array( '' => '' );			
			$bps_mscan_report_data_2 = isset($report_options['bps_mscan_report_data_2']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) ? $report_options['bps_mscan_report_data_2'] : array( '' => '' );
			$bps_mscan_report_data_3 = isset($report_options['bps_mscan_report_data_3']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) ? $report_options['bps_mscan_report_data_3'] : array( '' => '' );			
			$bps_mscan_report_data_4 = isset($report_options['bps_mscan_report_data_4']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) ? $report_options['bps_mscan_report_data_4'] : array( '' => '' );		
			$bps_mscan_report_data_5 = isset($report_options['bps_mscan_report_data_5']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) ? $report_options['bps_mscan_report_data_5'] : array( '' => '' );		
			$bps_mscan_report_data_6 = isset($report_options['bps_mscan_report_data_6']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) ? $report_options['bps_mscan_report_data_6'] : array( '' => '' );		
			$bps_mscan_report_data_7 = isset($report_options['bps_mscan_report_data_7']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) ? $report_options['bps_mscan_report_data_7'] : array( '' => '' );
			$bps_mscan_report_data_8 = isset($report_options['bps_mscan_report_data_8']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) ? $report_options['bps_mscan_report_data_8'] : array( '' => '' );
			$bps_mscan_report_data_9 = isset($report_options['bps_mscan_report_data_9']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) ? $report_options['bps_mscan_report_data_9'] : array( '' => '' );
			$bps_mscan_report_data_10 = isset($report_options['bps_mscan_report_data_10']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) ? $report_options['bps_mscan_report_data_10'] : array( '' => '' );
			$bps_mscan_report_data_11 = isset($report_options['bps_mscan_report_data_11']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) ? $report_options['bps_mscan_report_data_11'] : array( '' => '' );
			$bps_mscan_report_data_12 = isset($report_options['bps_mscan_report_data_12']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) ? $report_options['bps_mscan_report_data_12'] : array( '' => '' );
			$bps_mscan_report_data_13 = isset($report_options['bps_mscan_report_data_13']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) ? $report_options['bps_mscan_report_data_13'] : array( '' => '' );
			$bps_mscan_report_data_14 = isset($report_options['bps_mscan_report_data_14']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) ? $report_options['bps_mscan_report_data_14'] : array( '' => '' );
			$bps_mscan_report_data_15 = isset($report_options['bps_mscan_report_data_15']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) ? $report_options['bps_mscan_report_data_15'] : array( '' => '' );
			$bps_mscan_report_data_16 = isset($report_options['bps_mscan_report_data_16']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) ? $report_options['bps_mscan_report_data_16'] : array( '' => '' );
			$bps_mscan_report_data_17 = isset($report_options['bps_mscan_report_data_17']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) ? $report_options['bps_mscan_report_data_17'] : array( '' => '' );
			$bps_mscan_report_data_18 = isset($report_options['bps_mscan_report_data_18']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) ? $report_options['bps_mscan_report_data_18'] : array( '' => '' );
			$bps_mscan_report_data_19 = isset($report_options['bps_mscan_report_data_19']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) ? $report_options['bps_mscan_report_data_19'] : array( '' => '' );
			$bps_mscan_report_data_20 = isset($report_options['bps_mscan_report_data_20']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) ? $report_options['bps_mscan_report_data_20'] : array( '' => '' );

			if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) ) {
	
				$text = '<strong><font color="#fb0101">'.__('The MScan Report was not saved', 'bulletproof-security').'</font><br>'.__('The maximum number of Reports that can be saved is 20 Reports. In order to save the current Report you will need to delete an older saved Report.', 'bulletproof-security').'</strong>';
				
				echo $bps_topDiv;
				echo $text;		
				echo $bps_bottomDiv;			
				
			} else {

				if ( isset($report_options['bps_mscan_report_data_1']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) ) {
					$bps_mscan_report_data_1 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_2']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) ) {
					$bps_mscan_report_data_2 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_3']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) ) {
					$bps_mscan_report_data_3 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_4']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) ) {
					$bps_mscan_report_data_4 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_5']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) ) {
					$bps_mscan_report_data_5 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_6']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) ) {
					$bps_mscan_report_data_6 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_7']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) ) {
					$bps_mscan_report_data_7 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_8']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) ) {
					$bps_mscan_report_data_8 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_9']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) ) {
					$bps_mscan_report_data_9 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_10']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) ) {
					$bps_mscan_report_data_10 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_11']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) ) {
					$bps_mscan_report_data_11 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_12']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) ) {
					$bps_mscan_report_data_12 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_13']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) ) {
					$bps_mscan_report_data_13 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_14']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) ) {
					$bps_mscan_report_data_14 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_15']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) ) {
					$bps_mscan_report_data_15 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_16']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) ) {
					$bps_mscan_report_data_16 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_17']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) ) {
					$bps_mscan_report_data_17 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_18']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) ) {
					$bps_mscan_report_data_18 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_19']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) ) {
					$bps_mscan_report_data_19 = $bps_mscan_report_timestamp;
				} elseif ( isset($report_options['bps_mscan_report_data_20']) && ! array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) ) {
					$bps_mscan_report_data_20 = $bps_mscan_report_timestamp;
				}
	
				$Mscan_Report_Options = array( 
				'bps_mscan_report_data_1' 	=> $bps_mscan_report_data_1, 
				'bps_mscan_report_data_2' 	=> $bps_mscan_report_data_2, 
				'bps_mscan_report_data_3' 	=> $bps_mscan_report_data_3, 
				'bps_mscan_report_data_4' 	=> $bps_mscan_report_data_4, 
				'bps_mscan_report_data_5' 	=> $bps_mscan_report_data_5, 
				'bps_mscan_report_data_6' 	=> $bps_mscan_report_data_6, 
				'bps_mscan_report_data_7' 	=> $bps_mscan_report_data_7, 
				'bps_mscan_report_data_8' 	=> $bps_mscan_report_data_8, 
				'bps_mscan_report_data_9' 	=> $bps_mscan_report_data_9, 
				'bps_mscan_report_data_10' 	=> $bps_mscan_report_data_10, 
				'bps_mscan_report_data_11' 	=> $bps_mscan_report_data_11, 
				'bps_mscan_report_data_12' 	=> $bps_mscan_report_data_12, 
				'bps_mscan_report_data_13' 	=> $bps_mscan_report_data_13, 
				'bps_mscan_report_data_14' 	=> $bps_mscan_report_data_14, 
				'bps_mscan_report_data_15' 	=> $bps_mscan_report_data_15, 
				'bps_mscan_report_data_16' 	=> $bps_mscan_report_data_16, 
				'bps_mscan_report_data_17' 	=> $bps_mscan_report_data_17, 
				'bps_mscan_report_data_18' 	=> $bps_mscan_report_data_18, 
				'bps_mscan_report_data_19' 	=> $bps_mscan_report_data_19, 
				'bps_mscan_report_data_20' 	=> $bps_mscan_report_data_20 
				);
	
				foreach( $Mscan_Report_Options as $key => $value ) {
					update_option('bulletproof_security_options_mscan_report', $Mscan_Report_Options);
				}
			
				$text = '<strong><font color="green">'.__('The MScan Report was saved successfully. Saved MScan Reports can be viewed on the MScan Saved Reports tab page.', 'bulletproof-security').'</font></strong>';
		
				echo $bps_topDiv;
				echo $text;		
				echo $bps_bottomDiv;			
			}
		}
	}
}

bpsPro_mscan_report();
?>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-4" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('MScan Saved Reports ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Saved reports can be viewed or deleted.', 'bulletproof-security'); ?></span></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('MScan Saved Reports', 'bulletproof-security'); ?>  <button id="bps-open-modal4" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content4" class="bps-dialog-hide" title="<?php _e('MScan Saved Reports', 'bulletproof-security'); ?>">
	<p><?php echo $bps_modal_content4; ?></p>
</div>

<?php

	// MScan Saved Reports Form: View or Delete Report
	if ( ! get_option('bulletproof_security_options_mscan_report') ) {
		
		echo '<h2>'.__('No Saved MScan Reports To Display: No MScan Reports have been saved yet.', 'bulletproof-security').'</h2>';
		echo '<div id="bps-page-height" style="height:400px"></div>';	
		
	} else {
		
		$report_options = get_option('bulletproof_security_options_mscan_report');		
		
		$mscan_report_date_array = array();
		
		if ( isset($report_options['bps_mscan_report_data_1']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_1']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_2']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_2']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_3']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_3']['Scan Report'];
		}		

		if ( isset($report_options['bps_mscan_report_data_4']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_4']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_5']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_5']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_6']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_6']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_7']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_7']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_8']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_8']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_9']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_9']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_10']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_10']['Scan Report'];
		}
		
		if ( isset($report_options['bps_mscan_report_data_11']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_11']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_12']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_12']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_13']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_13']['Scan Report'];
		}		

		if ( isset($report_options['bps_mscan_report_data_14']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_14']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_15']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_15']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_16']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_16']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_17']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_17']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_18']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_18']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_19']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_19']['Scan Report'];
		}

		if ( isset($report_options['bps_mscan_report_data_20']) && array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) ) {
			$mscan_report_date_array[] = $report_options['bps_mscan_report_data_20']['Scan Report'];
		}		
		
		echo '<h3>'.__('MScan Saved Reports Form', 'bulletproof-security').'</h3>';
		
		echo '<form name="MScanSavedReports" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php#bps-tabs4' ).'" method="post">';
		wp_nonce_field('bulletproof_security_mscan_saved_reports');
		
		echo '<div id="ARQcheckall" style="">';
		echo '<table class="widefat" style="margin-bottom:20px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:30%;"><strong>'.__('Report Date', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:30%;"><br><strong>'.__('View Report', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:30%;"><input type="checkbox" class="checkallDelete" style="text-align:left; margin-left:2px;" /><br><strong>'.__('Delete Report', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
			foreach ( $mscan_report_date_array as $key => $value ) {
				
				$value = preg_replace( array( '/<div class="mscan-report-row-title-large">/', '/<\/div>/'), "", $value );
				
				echo '<th scope="row" style="border-bottom:none;font-weight:600;">'.$value.'</th>';
				echo "<td><input type=\"checkbox\" id=\"view_report\" name=\"qradio[$value]\" value=\"view_report\" /><br><span style=\"font-size:10px;\">".__('View', 'bulletproof-security')."</span></td>";
				echo "<td><input type=\"checkbox\" id=\"delete_report\" name=\"qradio[$value]\" value=\"delete_report\" class=\"deletereportALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";

				echo '</tr>';			
			}

		echo '</tbody>';
		echo '</table>';
		echo '</div>';	

		echo "<input type=\"submit\" name=\"Submit-MScan-View-Delete\" value=\"".__('View|Delete Reports', 'bulletproof-security')."\" class=\"button bps-button\" style=\"margin:0px 0px 0px 0px\" onclick=\"return confirm('".__('Click OK to proceed or click Cancel', 'bulletproof-security')."')\" /></p></form>";

	}

// MScan Saved Reports Form Proccessing - View or Delete Scan Reports
if ( isset( $_POST['Submit-MScan-View-Delete'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_mscan_saved_reports');

?>	
	<script type="text/javascript">
	/* <![CDATA[ */
	// Note: Active Tab numbering is literal from left to right.
	jQuery(document).ready(function($){
		$( "#bps-tabs" ).tabs({
		active: 3
		});
	});	
	/* ]]> */
	</script>

<?php

	$qradio = isset($_POST['qradio']) ? $_POST['qradio'] : '';
	$report_options = get_option('bulletproof_security_options_mscan_report');
	
	switch( $_POST['Submit-MScan-View-Delete'] ) {
		case __('View|Delete Reports', 'bulletproof-security'):
		
		$delete_reports = array();
		$view_reports = array();		
		
		if ( ! empty($qradio) ) {
			
			foreach ( $qradio as $key => $value ) {
				
				if ( $value == 'delete_report' ) {
					$delete_reports[] = $key;
				} elseif ( $value == 'view_report' ) {
					$view_reports[] = $key;
				}
			}
		}
			
		if ( empty($delete_reports) && empty($view_reports) ) {
			
 			$text_delete = '<strong><font color="#fb0101">'.__('You did not select an MScan Report to view or delete', 'bulletproof-security').'</font><br>'.__('Click the checkbox for the MScan Report that you would like to view or delete and then click the View|Delete Reports button.', 'bulletproof-security').'</strong>';
			echo $bps_topDiv;
			echo $text_delete;		
			echo $bps_bottomDiv;			
		}

		if ( ! empty($delete_reports) ) {
			
			echo $bps_topDiv;

			$deleted_report_text = '';

			foreach ( $delete_reports as $delete_report ) {
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) && $report_options['bps_mscan_report_data_1']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_1 = array( '' => '' );
				} else {
					$bps_mscan_report_data_1 = $report_options['bps_mscan_report_data_1'];
				}

				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) && $report_options['bps_mscan_report_data_2']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_2 = array( '' => '' );
				} else {
					$bps_mscan_report_data_2 = $report_options['bps_mscan_report_data_2'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) && $report_options['bps_mscan_report_data_3']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_3 = array( '' => '' );
				} else {
					$bps_mscan_report_data_3 = $report_options['bps_mscan_report_data_3'];
				}					
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) && $report_options['bps_mscan_report_data_4']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_4 = array( '' => '' );
				} else {
					$bps_mscan_report_data_4 = $report_options['bps_mscan_report_data_4'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) && $report_options['bps_mscan_report_data_5']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_5 = array( '' => '' );
				} else {
					$bps_mscan_report_data_5 = $report_options['bps_mscan_report_data_5'];
				}	
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) && $report_options['bps_mscan_report_data_6']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_6 = array( '' => '' );
				} else {
					$bps_mscan_report_data_6 = $report_options['bps_mscan_report_data_6'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) && $report_options['bps_mscan_report_data_7']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_7 = array( '' => '' );
				} else {
					$bps_mscan_report_data_7 = $report_options['bps_mscan_report_data_7'];
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) && $report_options['bps_mscan_report_data_8']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_8 = array( '' => '' );
				} else {
					$bps_mscan_report_data_8 = $report_options['bps_mscan_report_data_8'];
				}	
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) && $report_options['bps_mscan_report_data_9']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_9 = array( '' => '' );
				} else {
					$bps_mscan_report_data_9 = $report_options['bps_mscan_report_data_9'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) && $report_options['bps_mscan_report_data_10']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_10 = array( '' => '' );
				} else {
					$bps_mscan_report_data_10 = $report_options['bps_mscan_report_data_10'];
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) && $report_options['bps_mscan_report_data_11']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_11 = array( '' => '' );
				} else {
					$bps_mscan_report_data_11 = $report_options['bps_mscan_report_data_11'];
				}

				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) && $report_options['bps_mscan_report_data_12']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_12 = array( '' => '' );
				} else {
					$bps_mscan_report_data_12 = $report_options['bps_mscan_report_data_12'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) && $report_options['bps_mscan_report_data_13']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_13 = array( '' => '' );
				} else {
					$bps_mscan_report_data_13 = $report_options['bps_mscan_report_data_13'];
				}					
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) && $report_options['bps_mscan_report_data_14']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_14 = array( '' => '' );
				} else {
					$bps_mscan_report_data_14 = $report_options['bps_mscan_report_data_14'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) && $report_options['bps_mscan_report_data_15']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_15 = array( '' => '' );
				} else {
					$bps_mscan_report_data_15 = $report_options['bps_mscan_report_data_15'];
				}	
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) && $report_options['bps_mscan_report_data_16']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_16 = array( '' => '' );
				} else {
					$bps_mscan_report_data_16 = $report_options['bps_mscan_report_data_16'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) && $report_options['bps_mscan_report_data_17']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_17 = array( '' => '' );
				} else {
					$bps_mscan_report_data_17 = $report_options['bps_mscan_report_data_17'];
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) && $report_options['bps_mscan_report_data_18']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_18 = array( '' => '' );
				} else {
					$bps_mscan_report_data_18 = $report_options['bps_mscan_report_data_18'];
				}	
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) && $report_options['bps_mscan_report_data_19']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_19 = array( '' => '' );
				} else {
					$bps_mscan_report_data_19 = $report_options['bps_mscan_report_data_19'];
				}
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) && $report_options['bps_mscan_report_data_20']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$delete_report.'</div>' ) {
					$bps_mscan_report_data_20 = array( '' => '' );
				} else {
					$bps_mscan_report_data_20 = $report_options['bps_mscan_report_data_20'];
				}			
			
				$Mscan_Report_Options = array( 
				'bps_mscan_report_data_1' 	=> $bps_mscan_report_data_1, 
				'bps_mscan_report_data_2' 	=> $bps_mscan_report_data_2, 
				'bps_mscan_report_data_3' 	=> $bps_mscan_report_data_3, 
				'bps_mscan_report_data_4' 	=> $bps_mscan_report_data_4, 
				'bps_mscan_report_data_5' 	=> $bps_mscan_report_data_5, 
				'bps_mscan_report_data_6' 	=> $bps_mscan_report_data_6, 
				'bps_mscan_report_data_7' 	=> $bps_mscan_report_data_7, 
				'bps_mscan_report_data_8' 	=> $bps_mscan_report_data_8, 
				'bps_mscan_report_data_9' 	=> $bps_mscan_report_data_9, 
				'bps_mscan_report_data_10' 	=> $bps_mscan_report_data_10, 
				'bps_mscan_report_data_11' 	=> $bps_mscan_report_data_11, 
				'bps_mscan_report_data_12' 	=> $bps_mscan_report_data_12, 
				'bps_mscan_report_data_13' 	=> $bps_mscan_report_data_13, 
				'bps_mscan_report_data_14' 	=> $bps_mscan_report_data_14, 
				'bps_mscan_report_data_15' 	=> $bps_mscan_report_data_15, 
				'bps_mscan_report_data_16' 	=> $bps_mscan_report_data_16, 
				'bps_mscan_report_data_17' 	=> $bps_mscan_report_data_17, 
				'bps_mscan_report_data_18' 	=> $bps_mscan_report_data_18, 
				'bps_mscan_report_data_19' 	=> $bps_mscan_report_data_19, 
				'bps_mscan_report_data_20' 	=> $bps_mscan_report_data_20 
				);
					
				foreach( $Mscan_Report_Options as $key => $value ) {
					update_option('bulletproof_security_options_mscan_report', $Mscan_Report_Options);
				}
				
				$text_delete = '<strong><font color="green">'.__('Report: ', 'bulletproof-security').$delete_report.__(' has been deleted. Refresh/reload the page to see current MScan Saved Reports Form data.', 'bulletproof-security').'</font></strong><br>';
				echo $text_delete;			
			}			
			echo '</p></div>';	
		}
		
		if ( ! empty($view_reports) ) {
			
			$text_view = '<strong><font color="green">'.__('The MScan Saved Report scan data is displayed below the MScan Saved Reports Form.', 'bulletproof-security').'</font></strong><br>';
			echo $bps_topDiv;
			echo $text_view;
			echo '</p></div>';

			foreach ( $view_reports as $view_report ) {

				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_1'] ) && $report_options['bps_mscan_report_data_1']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_1';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_2'] ) && $report_options['bps_mscan_report_data_2']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_2';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_3'] ) && $report_options['bps_mscan_report_data_3']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_3';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_4'] ) && $report_options['bps_mscan_report_data_4']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_4';
				}			
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_5'] ) && $report_options['bps_mscan_report_data_5']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_5';
				}		
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_6'] ) && $report_options['bps_mscan_report_data_6']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_6';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_7'] ) && $report_options['bps_mscan_report_data_7']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_7';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_8'] ) && $report_options['bps_mscan_report_data_8']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_8';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_9'] ) && $report_options['bps_mscan_report_data_9']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_9';
				}			
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_10'] ) && $report_options['bps_mscan_report_data_10']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_10';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_11'] ) && $report_options['bps_mscan_report_data_11']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_11';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_12'] ) && $report_options['bps_mscan_report_data_12']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_12';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_13'] ) && $report_options['bps_mscan_report_data_13']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_13';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_14'] ) && $report_options['bps_mscan_report_data_14']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_14';
				}			
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_15'] ) && $report_options['bps_mscan_report_data_15']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_15';
				}		
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_16'] ) && $report_options['bps_mscan_report_data_16']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_16';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_17'] ) && $report_options['bps_mscan_report_data_17']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_17';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_18'] ) && $report_options['bps_mscan_report_data_18']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_18';
				}			
			
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_19'] ) && $report_options['bps_mscan_report_data_19']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_19';
				}			
				
				if ( array_key_exists( 'Scan Report', $report_options['bps_mscan_report_data_20'] ) && $report_options['bps_mscan_report_data_20']['Scan Report'] == '<div class="mscan-report-row-title-large">'.$view_report.'</div>' ) {
					$bps_mscan_report_data_view = 'bps_mscan_report_data_20';
				}			
			
				echo $report_options[$bps_mscan_report_data_view]['Scan Report'];
				echo $report_options[$bps_mscan_report_data_view][0];
				echo $report_options[$bps_mscan_report_data_view][1];	
				echo $report_options[$bps_mscan_report_data_view][2];			
				echo $report_options[$bps_mscan_report_data_view][3];	
				echo $report_options[$bps_mscan_report_data_view][4];
				echo $report_options[$bps_mscan_report_data_view][5];
				$mscan_report_option_settings = '<div class="mscan-report-row-title-large" style="padding-top:5px">'.__('MScan Option Settings: ', 'bulletproof-security') . '</div>';
				echo $mscan_report_option_settings;			
				echo $report_options[$bps_mscan_report_data_view][6]['MScan Option Settings:'];		
				echo $report_options[$bps_mscan_report_data_view][6][0];		
				echo $report_options[$bps_mscan_report_data_view][6][1];
				echo $report_options[$bps_mscan_report_data_view][6][2];		
				echo $report_options[$bps_mscan_report_data_view][6][3];		
				echo $report_options[$bps_mscan_report_data_view][6][4];
				echo $report_options[$bps_mscan_report_data_view][6][5];
				echo $report_options[$bps_mscan_report_data_view][6][6];		
				echo $report_options[$bps_mscan_report_data_view][6][7];		
				$mscan_report_file_hashes = '<div class="mscan-report-row-title-large" style="border-top:2px solid #999999;padding-top:10px">'.__('WP Core|Plugin|Theme File Hashes: ', 'bulletproof-security') . '</div>';
				echo $mscan_report_file_hashes;		
				echo $report_options[$bps_mscan_report_data_view][6][8]['WP Core|Plugin|Theme File Hashes:'];	
				$mscan_report_plugin_hash_title = '<div class="mscan-report-row-title" style="padding-top:8px">'.__('Plugin File Hashes: ', 'bulletproof-security').'</div>';
				echo $mscan_report_plugin_hash_title;		
				
				// Plugins Hashes:
				foreach ( $report_options[$bps_mscan_report_data_view][6][8][0]['Plugin File Hashes:'] as $key => $value ) {
					echo $value;
				}			
				
				$mscan_report_theme_hash_title = '<div class="mscan-report-row-title" style="padding-top:8px">'.__('Theme File Hashes: ', 'bulletproof-security').'</div>';
				echo $mscan_report_theme_hash_title;
		
				// Theme Hashes:
				foreach ( $report_options[$bps_mscan_report_data_view][6][8][0][0]['Theme File Hashes:'] as $key => $value ) {
					echo $value;
				}	
		
				$mscan_report_scan_results_title_spacer = '<div class="spacer" style="padding-top:10px"></div>';
				echo $mscan_report_scan_results_title_spacer;		
		
				$mscan_report_scan_results_title = '<div class="mscan-report-row-title-large" style="border-top:2px solid #999999;padding-top:10px">'.__('Scan Results ', 'bulletproof-security').'</div>';
				echo $mscan_report_scan_results_title;
		
				$mscan_report_file_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('File hash comparison scan results are 100% accurate. WP Core, Plugin and Theme files are scanned using file hash comparison scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results are less accurate and will usually detect some false positive matches. All other files that are not WP Core, Plugin and Theme files are scanned using pattern matching scanning.', 'bulletproof-security').'<br>'.__('You can View, Ignore and Delete files detected as suspicious using the View|Ignore|Delete Suspicious Files Form on the MScan 2.0 tab page. Before deleting any files make a backup of those files on your computer not on your hosting account.', 'bulletproof-security').'<br>'.__('And of course check the file contents of suspicious files to see if they contain hacker code or are false positive matches. Use the Ignore File checkbox option to ignore false postive matches.', 'bulletproof-security').'<br>'.__('When you ignore a file it will no longer be scanned in any future scans. When you unignore an ignored file it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
				echo $mscan_report_file_scan_help_text;	
		
				echo '<div id="MScanSuspectcheckall" style="">';
				echo '<table class="widefat" style="margin-bottom:20px;">';
				echo '<thead>';
				echo '<tr>';
				echo '<th scope="col" style="width:10%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:50%;"><strong>'.__('File Path', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:25%;"><strong>'.__('File Hash or Pattern Match', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:15%;"><strong>'.__('Scan Time', 'bulletproof-security').'</strong></th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				echo '<tr>';
				
				foreach ( $report_options[$bps_mscan_report_data_view][6][8][0][0][0]['Scan Results:']['File Scan'] as $key => $value ) {
					
					foreach ( $value as $inner_key => $inner_value ) {
					
						if ( $inner_key == 0 ) {
							echo '<th scope="row" style="border-bottom:none;">'.$inner_value.'</th>';
						}
						
						if ( $inner_key == 1 ) {
							echo '<td>'.$inner_value.'</td>';
						}				
		
						if ( $inner_key == 2 ) {
							echo '<td style="max-width:200px">'.$inner_value.'</td>';
						}
										
						if ( $inner_key == 3 ) {
							echo '<td>'.$inner_value.'</td>'; 
						}
					}
				
					echo '</tr>';
				} 
		
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
		
				$mscan_report_db_scan_help_text = '<div class="mscan-report-row-small"><strong>'.__('Database scanning uses pattern matching scanning.', 'bulletproof-security').'<br>'.__('Pattern matching scan results will usually detect some false positive matches.', 'bulletproof-security').'<br>'.__('You can View, Ignore and Unignore suspicious DB Entries using the View|Ignore Suspicious DB Entries Form on the MScan 2.0 tab page. Before deleting any database data make a backup of your database.', 'bulletproof-security').'<br>'.__('Use phpMyAdmin or a similar tool to check your database Row where the suspicious code was found.', 'bulletproof-security').'<br>'.__('When you ignore a DB Entry it will no longer be scanned in any future scans. When you unignore an ignored DB Entry it will be scanned in future scans.', 'bulletproof-security').'</strong></div>';
				echo $mscan_report_db_scan_help_text;	
		
				$mscan_report_scan_results_db_scan_array = array();	
		
				echo '<div id="MScanSuspectDBcheckall" style="">';
				echo '<table class="widefat" style="margin-bottom:10px;">';
				echo '<thead>';
				echo '<tr>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('Current Status', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('DB Table', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('DB Column', 'bulletproof-security').'</strong>'.'</th>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('DB Row ID', 'bulletproof-security').'</strong>'.'</th>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('Pattern Match', 'bulletproof-security').'</strong></th>';
				echo '<th scope="col" style="width:16%;"><strong>'.__('Scan Time', 'bulletproof-security').'</strong></th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				echo '<tr>';
				
		
				foreach ( $report_options[$bps_mscan_report_data_view][6][8][0][0][0]['Scan Results:'][0]['DB Scan'] as $key => $value ) {
					
					foreach ( $value as $inner_key => $inner_value ) {
					
						if ( $inner_key == 0 ) {
							echo '<th scope="row" style="border-bottom:none;">'.$inner_value.'</th>';
						}
						
						if ( $inner_key == 1 ) {
							echo '<td>'.$inner_value.'</td>';
						}				
		
						if ( $inner_key == 2 ) {
							echo '<td>'.$inner_value.'</td>';
						}
										
						if ( $inner_key == 3 ) {
							echo '<td>'.$inner_value.'</td>'; 
						}
		
						if ( $inner_key == 4 ) {
							echo '<td style="max-width:200px">'.$inner_value.'</td>';
						}
		
						if ( $inner_key == 5 ) {
							echo '<td>'.$inner_value.'</td>'; 
						}
					}
					 echo '</tr>';
				}
			
				echo '</tbody>';
				echo '</table>';
				echo '</div>';			
			}
		}
		break;
	} 
}

?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
    $('.checkallDelete').click(function() {
    $(this).parents('#ARQcheckall:eq(0)').find('.deletereportALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-5" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    <a href="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/whatsnew/whatsnew.php' ); ?>" target="_blank"><?php _e('Whats New in ', 'bulletproof-security'); echo BULLETPROOF_VERSION; ?></a><br /><br />
	<a href="https://forum.ait-pro.com/forums/topic/bulletproof-security-pro-version-release-dates/" target="_blank"><?php _e('BPS Pro Features & Version Release Dates', 'bulletproof-security'); ?></a><br /><br />
	<a href="https://forum.ait-pro.com/video-tutorials/" target="_blank"><?php _e('Video Tutorials', 'bulletproof-security'); ?></a><br /><br />
	<a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a>
    </td>
  </tr>
</table>
</div>
            
<div id="AITpro-link">BulletProof Security Pro <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://forum.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
</div>