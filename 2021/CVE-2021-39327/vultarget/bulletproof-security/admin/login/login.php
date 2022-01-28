<?php
// Direct calls to this file are Forbidden when core files are not present
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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' && ! isset( $_POST['Submit-Login-Security-search'] ) || isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {

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

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_lsm() {
	
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

// Create a new Deny All .htaccess file on first page load with users current IP address to allow the lsm-master.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
function bpsPro_Core_LSM_deny_all() {

	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
		
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
		if ( isset( $HFiles_options['bps_htaccess_files'] ) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset( $Zip_download_Options['bps_zip_download_fix'] ) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			return;
		}

		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_lsm()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_lsm()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_lsm()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/login/.htaccess';
		$check_string = @file_get_contents($create_denyall_htaccess_file);
		
		if ( ! file_exists($create_denyall_htaccess_file) ) { 

			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) && ! strpos( $check_string, bpsPro_get_real_ip_address_lsm() ) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}
	}
}
bpsPro_Core_LSM_deny_all();

?>

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ Login Security & Monitoring', 'bulletproof-security'); ?></h2>
<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Top div & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

if ( ! current_user_can('manage_options') ) { 
	_e('Permission Denied', 'bulletproof-security'); 
	
	} else { 
	
	require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-export.php' );
	require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-help-text.php' );
}

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Login Security & Monitoring', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('JTC-Lite', 'bulletproof-security'); ?></a></li>
 			<?php if ( is_multisite() && $blog_id != 1 ) { ?>
            <!-- <li><a href="#bps-tabs-3"><?php //_e('Idle Session Logout', 'bulletproof-security'); ?></a></li> -->  
            <?php } else { ?>
            <li><a href="#bps-tabs-3"><?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?></a></li>
            <?php } ?>
			<li><a href="#bps-tabs-4"><?php _e('Force Strong Passwords', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-5"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<?php
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	
	if ( isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' ) {
		$text = '<h3><strong><span style="font-size:1em;"><font color="blue">'.__('Notice: ', 'bulletproof-security').'</font></span><span style="font-size:.75em;">'.__('You have disabled wp-admin BulletProof Mode on the Security Modes page.', 'bulletproof-security').'<br>'.__('If you have Go Daddy "Managed WordPress Hosting" click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/gdmw/" target="_blank" title="Link opens in a new Browser window">'.__('Go Daddy Managed WordPress Hosting', 'bulletproof-security').'</a>.</span></strong></h3>';
		echo $text;
	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('Login Security & Monitoring (LSM) ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Log All Account Logins or Log Only Account Lockouts ~ Brute Force Login Protection', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting) & User Account Lockouts: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a>'; echo $text; ?>
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Login Security & Monitoring', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('Login Security & Monitoring', 'bulletproof-security'); ?>">
	<p>
	<?php 
		$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;
		echo $bps_modal_content1; 
	?>
	</p>
</div>

<?php if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else { 

// Standard Static visible Login Security form proccessing - Lock, Unlock or Delete user login status from DB
if ( isset($_POST['Submit-Login-Security-Radio'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security');
	
	$LSradio = isset($_POST['LSradio']) ? $_POST['LSradio'] : '';
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";

	switch( $_POST['Submit-Login-Security-Radio'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$delete_users = array();
		$unlock_users = array();
		$lock_users = array();		
		
		if ( ! empty($LSradio) ) {
			
			foreach ( $LSradio as $key => $value ) {
				
				if ( $value == 'deleteuser' ) {
					$delete_users[] = $key;
				
				} elseif ( $value == 'unlockuser' ) {
					$unlock_users[] = $key;
				
				} elseif ( $value == 'lockuser' ) {
					$lock_users[] = $key;
				}
			}
		}
			
		if ( ! empty($delete_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_users as $delete_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
				
					$textDelete = '<font color="green">'.$row->username.__(' has been deleted from the Login Security Database Table.', 'bulletproof-security').'</font><br>';
					echo $textDelete;
				}
			}
			echo '</p></div>';		
		}
		
		if ( ! empty($unlock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unlock_users as $unlock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $unlock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$NLstatus = 'Not Locked';
					$lockout_time = '0';		
					$failed_logins ='0';

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $NLstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );
				
					$textUnlock = '<font color="green">'.$row->username.__(' has been Unlocked.', 'bulletproof-security').'</font><br>';
					echo $textUnlock;				
				}			
			}
			echo '</p></div>';		
		}

		if ( ! empty($lock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $lock_users as $lock_user ) {

				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $lock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$Lstatus = 'Locked';
					$manual_lockout_time = time() + (60 * $BPSoptions['bps_manual_lockout_duration']); // default is 1 hour/3600 seconds
					$BPSoptions = get_option('bulletproof_security_options_login_security');
					$failed_logins = $BPSoptions['bps_max_logins'];	

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $Lstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $manual_lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );

					$textLock = '<font color="green">'.$row->username.__(' has been Locked.', 'bulletproof-security').'</font><br>';
					echo $textLock;
				}			
			}
			echo '</p></div>';		
		}
		break;
	} // end Switch
}

// Search Form - Login Security Dynamic Search Form - Lock, Unlock or Delete user login status from DB
if ( isset($_POST['Submit-Login-Search-Radio'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security_search');
	
	$LSradio = isset($_POST['LSradio']) ? $_POST['LSradio'] : '';
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	
	switch( $_POST['Submit-Login-Search-Radio'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$delete_users = array();
		$unlock_users = array();
		$lock_users = array();		
		
		if ( ! empty($LSradio) ) {
			
			foreach ( $LSradio as $key => $value ) {
				
				if ( $value == 'deleteuser' ) {
					$delete_users[] = $key;
				
				} elseif ( $value == 'unlockuser' ) {
					$unlock_users[] = $key;
				
				} elseif ( $value == 'lockuser' ) {
					$lock_users[] = $key;
				}
			}
		}
			
		if ( ! empty($delete_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_users as $delete_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
				
					$textDelete = '<font color="green">'.$row->username.__(' has been deleted from the Login Security Database Table.', 'bulletproof-security').'</font><br>';
					echo $textDelete;
				}
			}
			echo '</p></div>';		
		}
		
		if ( ! empty($unlock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unlock_users as $unlock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $unlock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$NLstatus = 'Not Locked';
					$lockout_time = '0';		
					$failed_logins ='0';						
					
					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $NLstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );
				
					$textUnlock = '<font color="green">'.$row->username.__(' has been Unlocked.', 'bulletproof-security').'</font><br>';
					echo $textUnlock;
				}			
			}
			echo '</p></div>';
		}

		if ( ! empty($lock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $lock_users as $lock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $lock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$Lstatus = 'Locked';
					$manual_lockout_time = time() + (60 * $BPSoptions['bps_manual_lockout_duration']); // default is 1 hour/3600 seconds 	
					$BPSoptions = get_option('bulletproof_security_options_login_security');
					$failed_logins = $BPSoptions['bps_max_logins'];

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $Lstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $manual_lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );

					$textLock = '<font color="green">'.$row->username.__(' has been Locked.', 'bulletproof-security').'</font><br>';
					echo $textLock;
				}			
			}
			echo '</p></div>';
		}
		break;
	} // end Switch
}
?>

<div id="LoginSecurityOptions" style="width:100%;">

<form name="LoginSecurityOptions" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_login_security'); ?> 
	<?php $BPSoptions = get_option('bulletproof_security_options_login_security'); 
	$bps_max_logins = ! isset($BPSoptions['bps_max_logins']) ? '' : $BPSoptions['bps_max_logins'];
	$bps_lockout_duration = ! isset($BPSoptions['bps_lockout_duration']) ? '' : $BPSoptions['bps_lockout_duration'];	
	$bps_manual_lockout_duration = ! isset($BPSoptions['bps_manual_lockout_duration']) ? '' : $BPSoptions['bps_manual_lockout_duration'];
	$bps_max_db_rows_display = ! isset($BPSoptions['bps_max_db_rows_display']) ? '' : $BPSoptions['bps_max_db_rows_display'];
	//$bps_enable_lsm_woocommerce = ! isset($BPSoptions['bps_enable_lsm_woocommerce']) ? '' : checked( $BPSoptions['bps_enable_lsm_woocommerce'], 1 );
	$bps_login_security_OnOff = ! isset($BPSoptions['bps_login_security_OnOff']) ? '' : $BPSoptions['bps_login_security_OnOff'];
	$bps_login_security_logging = ! isset($BPSoptions['bps_login_security_logging']) ? '' : $BPSoptions['bps_login_security_logging'];	
	$bps_login_security_errors = ! isset($BPSoptions['bps_login_security_errors']) ? '' : $BPSoptions['bps_login_security_errors'];
	$bps_login_security_remaining = ! isset($BPSoptions['bps_login_security_remaining']) ? '' : $BPSoptions['bps_login_security_remaining'];
	$bps_login_security_pw_reset = ! isset($BPSoptions['bps_login_security_pw_reset']) ? '' : $BPSoptions['bps_login_security_pw_reset'];
	$bps_login_security_sort = ! isset($BPSoptions['bps_login_security_sort']) ? '' : $BPSoptions['bps_login_security_sort'];	
	?>
 
<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Max Login Attempts:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_max_logins]" class="regular-text-50-fixed" value="<?php if ( $bps_max_logins != '' ) { echo esc_html( $bps_max_logins ); } else { echo esc_html('3'); } ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Automatic Lockout Time:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_lockout_duration]" class="regular-text-50-fixed" value="<?php if ( $bps_lockout_duration != '' ) { echo esc_html( $bps_lockout_duration ); } else { echo esc_html('15'); } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Minutes', 'bulletproof-security'); ?></strong></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Manual Lockout Time:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_manual_lockout_duration]" class="regular-text-50-fixed" value="<?php if ( $bps_manual_lockout_duration != '' ) { echo esc_html( $bps_manual_lockout_duration ); } else { echo esc_html('60'); } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Minutes', 'bulletproof-security'); ?></strong></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Max DB Rows To Show:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_max_db_rows_display]" class="regular-text-50-fixed" value="<?php if ( $bps_max_db_rows_display != '' ) { echo esc_html( $bps_max_db_rows_display ); } else { echo esc_html(''); } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Blank = Show All Rows', 'bulletproof-security'); ?></strong></label></td>
  </tr>
</table>

	<div id="LSM-woocommerce" style="margin:10px 0px 10px 0px">
	<input type="checkbox" name="bulletproof_security_options_login_security[bps_enable_lsm_woocommerce]" value="" <?php if ( empty( $BPSoptions['bps_enable_lsm_woocommerce'] ) ) { echo ''; } else { checked( $BPSoptions['bps_enable_lsm_woocommerce'], 1 ); } ?> /><label><?php _e(' Enable Login Security for WooCommerce (BPS Pro Only)', 'bulletproof-security'); ?></label>
	</div>

<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_OnOff]" class="form-220">
<option value="On" <?php selected('On', $bps_login_security_OnOff); ?>><?php _e('Login Security On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_login_security_OnOff); ?>><?php _e('Login Security Off', 'bulletproof-security'); ?></option>
<option value="pwreset" <?php selected('pwreset', $bps_login_security_OnOff); ?>><?php _e('Login Security Off|Use Password Reset Option ONLY', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Logging Options:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_logging]" class="form-220">
<option value="logLockouts" <?php selected('logLockouts', $bps_login_security_logging); ?>><?php _e('Log Only Account Lockouts', 'bulletproof-security'); ?></option>
<option value="logAll" <?php selected('logAll', $bps_login_security_logging); ?>><?php _e('Log All Account Logins', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Error Messages:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_errors]" class="form-220">
<option value="wpErrors" <?php selected('wpErrors', $bps_login_security_errors); ?>><?php _e('Standard WP Login Errors', 'bulletproof-security'); ?></option>
<option value="generic" <?php selected('generic', $bps_login_security_errors); ?>><?php _e('User|Pass Invalid Entry Error', 'bulletproof-security'); ?></option>
<option value="genericAll" <?php selected('genericAll', $bps_login_security_errors); ?>><?php _e('User|Pass|Lock Invalid Entry Error', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Attempts Remaining:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_remaining]" class="form-220">
<option value="On" <?php selected('On', $bps_login_security_remaining); ?>><?php _e('Show Login Attempts Remaining', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_login_security_remaining); ?>><?php _e('Do Not Show Login Attempts Remaining', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Password Reset:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_pw_reset]" class="form-220">
<option value="enable" <?php selected('enable', $bps_login_security_pw_reset); ?>><?php _e('Enable Password Reset', 'bulletproof-security'); ?></option>
<option value="disableFrontend" <?php selected('disableFrontend', $bps_login_security_pw_reset); ?>><?php _e('Disable Password Reset Frontend Only', 'bulletproof-security'); ?></option>
<option value="disable" <?php selected('disable', $bps_login_security_pw_reset); ?>><?php _e('Disable Password Reset Frontend & Backend', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Sort DB Rows:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_sort]" class="form-220">
<option value="ascending" <?php selected('ascending', $bps_login_security_sort); ?>><?php _e('Ascending - Show Oldest Login First', 'bulletproof-security'); ?></option>
<option value="descending" <?php selected('descending', $bps_login_security_sort); ?>><?php _e('Descending - Show Newest Login First', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
</table>

<input type="submit" name="Submit-Security-Log-Options" class="button bps-button" style="margin:10px 0px 0px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" />
</form>
</div>

<div id="LSMExportButton">
<form name="bpsLSMExport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_lsm_export'); ?>
	<input type="submit" name="Submit-LSM-Export" class="button bps-button" value="<?php esc_attr_e('Export|Download Login Security Table', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will Export (copy) the Login Security Table into the lsm-master.csv file, which you can then download to your computer by clicking the Download Zip Export button displayed in the Login Security Table Export success message.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Export the Login Security Table or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_LSM_Table_CSV(); ?>
</form>
</div>

<div id="LoginSecuritySearch">
<form name="LoginSecuritySearchForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#LSM-DB-Table' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_login_security_search'); ?>
    <input type="text" name="LSSearch" class="LSSearch-text" value="" />
    <input type="submit" name="Submit-Login-Security-search" class="button bps-button" value="<?php esc_attr_e('Search', 'bulletproof-security') ?>" />
    </form>
</div>

<?php

function bpsDBRowCount() {
global $wpdb;
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	$id = '0';
	$DB_row_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $bpspro_login_table WHERE id != %d", $id ) );
	$BPSoptions = get_option('bulletproof_security_options_login_security');
	$Max_db_rows = ! isset($BPSoptions['bps_max_db_rows_display']) ? '' : $BPSoptions['bps_max_db_rows_display'];

	echo '<div id="LoginSecurityDBRowCount">';
	
	if ( isset($BPSoptions['bps_max_db_rows_display']) && $BPSoptions['bps_max_db_rows_display'] != '') {
		$text = $Max_db_rows.__(' out of ', 'bulletproof-security')."{$DB_row_count}".__(' Database Rows are currently being displayed', 'bulletproof-security');
		echo $text;
	} else {
		$text = __('Total number of Database Rows is: ', 'bulletproof-security')."{$DB_row_count}";
		echo $text;	
	}
	echo '</div>';
}
bpsDBRowCount();

// Login Security Search Form
if ( isset( $_POST['Submit-Login-Security-search'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security_search');
	
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	$search = isset($_POST['LSSearch']) ? $_POST['LSSearch'] : '';

	$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE (status = %s) OR (user_id = %s) OR (username LIKE %s) OR (public_name LIKE %s) OR (email LIKE %s) OR (role LIKE %s) OR (ip_address LIKE %s) OR (hostname LIKE %s) OR (request_uri LIKE %s)", $search, $search, "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%" ) );

	echo '<form name="bpsLoginSecuritySearchDBRadio" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_login_security_search');

	echo '<div id="LoginSecurityCheckall">';
	echo '<table class="widefat">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:10%;font-size:16px;"><strong>'.__('Login Status', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallLock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Lock', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallUnlock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unlock', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallDelete" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Delete', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('User ID', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Username', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Display Name', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Email', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Role', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Login Time', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Lockout Expires', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('IP Address', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Hostname', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Request URI', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	foreach ( $getLoginSecurityTable as $row ) {

		if ( $wpdb->num_rows != 0 ) {
			$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		
			if ( $row->status == 'Locked' ) {
				echo '<th scope="row" style="border-bottom:none;color:red;font-weight:bold;">'.$row->status.'</th>';
			} else {
				echo '<th scope="row" style="border-bottom:none;">'.$row->status.'</th>';
			}
	
			echo "<td><input type=\"checkbox\" id=\"lockuser\" name=\"LSradio[$row->user_id]\" value=\"lockuser\" class=\"lockuserALL\" /><br><span style=\"font-size:10px;\">".__('Lock', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"unlockuser\" name=\"LSradio[$row->user_id]\" value=\"unlockuser\" class=\"unlockuserALL\" /><br><span style=\"font-size:10px;\">".__('Unlock', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"deleteuser\" name=\"LSradio[$row->user_id]\" value=\"deleteuser\" class=\"deleteuserALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
		
			echo '<td>'.$row->user_id.'</td>';
			echo '<td>'.$row->username.'</td>';
			echo '<td>'.$row->public_name.'</td>';	
			echo '<td>'.$row->email.'</td>';	
			echo '<td>'.$row->role.'</td>';	
			echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->login_time + $gmt_offset).'</td>';
			
			if ( $row->lockout_time == 0 ) { 
			echo '<td>'.__('NA', 'bulletproof-security').'</td>';
			} else {
			echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</td>';
			}
			
			echo '<td>'.$row->ip_address.'</td>';	
			echo '<td>'.$row->hostname.'</td>';
			echo '<td>'.$row->request_uri.'</td>';	
			echo '</tr>';			
		}
	} 
	
	if ( $wpdb->num_rows == 0 ) {		
		echo '<th scope="row" style="border-bottom:none;">'.__('No Logins|Locked', 'bulletproof-security').'</th>';
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '<td></td>';
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '</tr>';		
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';	

	echo "<input type=\"submit\" name=\"Submit-Login-Search-Radio\" value=\"".__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Locking and Unlocking a User is reversible, but Deleting a User is not.\n\n-------------------------------------------------------------\n\nWhen you delete a User you are deleting that User database row from the BPS Login Security Database Table and not from the WordPress User Database Table.\n\n-------------------------------------------------------------\n\nTo delete a User Account from your WordPress website use the standard/normal WordPress Users page.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />&nbsp;&nbsp;<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"javascript:history.go(0)\" /></form><br>";

	} else { // if the LSM Search form is not submitted then display the static LSM form

		echo '<form name="bpsLoginSecurityDBRadio" class="LSM-DBRadio-Form" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" method="post">';
		wp_nonce_field('bulletproof_security_login_security');

		$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
		$searchAll = ''; // return all rows
		$BPSoptions = get_option('bulletproof_security_options_login_security');
	
		if ( ! isset($BPSoptions['bps_login_security_sort']) || isset($BPSoptions['bps_login_security_sort']) && $BPSoptions['bps_login_security_sort'] == 'ascending' ) {
			$sorting = 'ASC';
		} else {
			$sorting = 'DESC';
		}
	
		if ( isset($BPSoptions['bps_max_db_rows_display']) && $BPSoptions['bps_max_db_rows_display'] != '' ) {
			$db_row_limit = 'LIMIT '. $BPSoptions['bps_max_db_rows_display'];
			$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE login_time != %s ORDER BY login_time $sorting $db_row_limit", "%$searchAll%" ) );
	
		} else {
			$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE login_time != %s ORDER BY login_time $sorting", "%$searchAll%" ) );	
		}

		echo '<div id="LoginSecurityCheckall">';
		echo '<table class="widefat">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:10%;font-size:16px;"><strong>'.__('Login Status', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallLock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Lock', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallUnlock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unlock', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallDelete" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Delete', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('User ID', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Username', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Display Name', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Email', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Role', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Login Time', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Lockout Expires', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('IP Address', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Hostname', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Request URI', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
		foreach ( $getLoginSecurityTable as $row ) {

			if ( $wpdb->num_rows != 0 ) {
				$gmt_offset = get_option( 'gmt_offset' ) * 3600;
				
				if ( $row->status == 'Locked' ) {
					echo '<th scope="row" style="border-bottom:none;color:red;font-weight:bold;">'.$row->status.'</th>';
				} else {
					echo '<th scope="row" style="border-bottom:none;">'.$row->status.'</th>';
				}
	
				echo "<td><input type=\"checkbox\" id=\"lockuser\" name=\"LSradio[$row->user_id]\" value=\"lockuser\" class=\"lockuserALL\" /><br><span style=\"font-size:10px;\">".__('Lock', 'bulletproof-security')."</span></td>";
				echo "<td><input type=\"checkbox\" id=\"unlockuser\" name=\"LSradio[$row->user_id]\" value=\"unlockuser\" class=\"unlockuserALL\" /><br><span style=\"font-size:10px;\">".__('Unlock', 'bulletproof-security')."</span></td>";
				echo "<td><input type=\"checkbox\" id=\"deleteuser\" name=\"LSradio[$row->user_id]\" value=\"deleteuser\" class=\"deleteuserALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
		
				echo '<td>'.$row->user_id.'</td>';
				echo '<td>'.$row->username.'</td>';
				echo '<td>'.$row->public_name.'</td>';	
				echo '<td>'.$row->email.'</td>';	
				echo '<td>'.$row->role.'</td>';	
				echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->login_time + $gmt_offset).'</td>';
				
				if ( $row->lockout_time == 0 ) { 
				echo '<td>'.__('NA', 'bulletproof-security').'</td>';
				} else {
				echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset).'</td>';
				}
				
				echo '<td>'.$row->ip_address.'</td>';	
				echo '<td>'.$row->hostname.'</td>';
				echo '<td>'.$row->request_uri.'</td>';	
				echo '</tr>';			
			}
		} 
		
		if ( $wpdb->num_rows == 0 ) {		
			echo '<th scope="row" style="border-bottom:none;">'.__('No Logins|Locked', 'bulletproof-security').'</th>';
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '<td></td>';
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '</tr>';		
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';	

		echo "<input type=\"submit\" name=\"Submit-Login-Security-Radio\" value=\"".__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Locking and Unlocking a User is reversible, but Deleting a User is not.\n\n-------------------------------------------------------------\n\nWhen you delete a User you are deleting that User database row from the BPS Login Security Database Table and not from the WordPress User Database Table.\n\n-------------------------------------------------------------\n\nTo delete a User Account from your WordPress website use the standard/normal WordPress Users page.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />&nbsp;&nbsp;<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"javascript:history.go(0)\" /></form><br>";
	}
?>
<br />

<?php
$UIoptions = get_option('bulletproof_security_options_theme_skin');

if ( isset($UIoptions['bps_ui_theme_skin']) && $UIoptions['bps_ui_theme_skin'] == 'blue' ) {
?>
<br />

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#LoginSecurityCheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<?php } ?>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallLock').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.lockuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallUnlock').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.unlockuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallDelete').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.deleteuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<?php 
} // end if current_user_can('manage_options') - forms are not displayed to non-administrators
?>
</td>
  </tr>
</table>
</div>

<div id="bps-tabs-2" class="bps-tab-page">
	
<?php
	// Nonce for Crypto-js
	$bps_nonceValue = 'ghbhnyxu';
	$bpsSpacePop = '-------------------------------------------------------------';

	$GDMW_options = get_option('bulletproof_security_options_GDMW');
	
	if ( isset($GDMW_options['bps_gdmw_hosting']) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		$text = '<h3><strong><span style="font-size:1em;"><font color="blue">'.__('Notice: ', 'bulletproof-security').'</font></span><span style="font-size:.75em;">'.__('The Setup Wizard Go Daddy "Managed WordPress Hosting" option is set to Yes.', 'bulletproof-security').'<br>'.__('If you have Go Daddy "Managed WordPress Hosting" click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/gdmw/" target="_blank" title="Link opens in a new Browser window">'.__('Go Daddy Managed WordPress Hosting', 'bulletproof-security').'</a>.<br>'.__('If you do not have Go Daddy "Managed WordPress Hosting" then change the Go Daddy "Managed WordPress Hosting" Setup Wizard option to No.', 'bulletproof-security').'</span></strong></h3>';
		echo $text;
	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
	<?php $text = '<h2>'.__('jQuery ToolTip CAPTCHA-Lite (JTC-Lite) ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Protects the WP Login Form against constant lockouts by Bots trying to Brute Force Login to your website', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Get the full version of JTC Anti-Spam|Anti-Hacker that protects all of your WP Forms', 'bulletproof-security').'<br>'.__('against SpamBots & HackerBots (auto-registering, auto-logins, auto-posting, auto-commenting) & User Account Lockouts: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a>'; echo $text; ?>
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 5px 0px;"><?php _e('JTC-Lite', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content2" class="bps-dialog-hide" title="<?php _e('JTC-Lite', 'bulletproof-security'); ?>">
	<p>
	<?php
		$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;
		echo $bps_modal_content2;
	?>
    </p>
</div>

<?php
// JTC Form processing
if ( isset( $_POST['Submit-Security-Log-Options-JTC'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_login_security_jtc' );
	
	$Custom_Roles = empty($_POST['bps_jtc_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_jtc_custom_roles'];		
	
	switch( $_POST['Submit-Security-Log-Options-JTC'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = '';
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
	
		$pos1 = strpos( $_POST['bps_jtc_custom_form_error'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_jtc_comment_form_error'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_jtc_comment_form_label'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_jtc_comment_form_input'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
	
		if ( $pos1 === false ) {
			$bps_jtc_custom_form_error = stripslashes($_POST['bps_jtc_custom_form_error']);
		} else {
			$bps_jtc_custom_form_error = $Encryption->decrypt($_POST['bps_jtc_custom_form_error'], $nonceValue);
		}
	
		if ( $pos2 === false ) {
			$bps_jtc_comment_form_error = stripslashes($_POST['bps_jtc_comment_form_error']);
		} else {
			$bps_jtc_comment_form_error = $Encryption->decrypt($_POST['bps_jtc_comment_form_error'], $nonceValue);
		}
	
		if ( $pos3 === false ) {
			$bps_jtc_comment_form_label = esc_html($_POST['bps_jtc_comment_form_label']);
		} else {
			$bps_jtc_comment_form_label = $Encryption->decrypt($_POST['bps_jtc_comment_form_label'], $nonceValue);
		}
	
		if ( $pos4 === false ) {
			$bps_jtc_comment_form_input = esc_html($_POST['bps_jtc_comment_form_input']);
		} else {
			$bps_jtc_comment_form_input = $Encryption->decrypt($_POST['bps_jtc_comment_form_input'], $nonceValue);
		}
	}

	$bps_jtc_login_form = ! empty($_POST['bps_jtc_login_form']) ? '1' : '';
	
	$JTC_Options = array(
	'bps_tooltip_captcha_key' 			=> esc_html($_POST['bps_tooltip_captcha_key']), 
	'bps_tooltip_captcha_hover_text'	=> esc_html($_POST['bps_tooltip_captcha_hover_text']), 
	'bps_tooltip_captcha_title' 		=> esc_html($_POST['bps_tooltip_captcha_title']), 
	'bps_tooltip_captcha_logging' 		=> 'Off', 
	'bps_jtc_login_form' 				=> $bps_jtc_login_form, 
	'bps_jtc_register_form' 			=> '', 
	'bps_jtc_lostpassword_form' 		=> '', 
	'bps_jtc_comment_form' 				=> '', 
	'bps_jtc_mu_register_form' 			=> '', 
	'bps_jtc_buddypress_register_form' 	=> '', 
	'bps_jtc_buddypress_sidebar_form' 	=> '', 
	'bps_jtc_administrator' 			=> '', 
	'bps_jtc_editor' 					=> '', 
	'bps_jtc_author' 					=> '', 
	'bps_jtc_contributor' 				=> '', 
	'bps_jtc_subscriber' 				=> '', 
	'bps_jtc_comment_form_error' 		=> $bps_jtc_comment_form_error, 
	'bps_jtc_comment_form_label' 		=> $bps_jtc_comment_form_label, 
	'bps_jtc_comment_form_input' 		=> $bps_jtc_comment_form_input, 
	'bps_jtc_custom_roles' 				=> $Custom_Roles_array, 
	'bps_enable_jtc_woocommerce' 		=> '', 
	'bps_jtc_custom_form_error' 		=> $bps_jtc_custom_form_error
	);	
	
	foreach( $JTC_Options as $key => $value ) {
		update_option('bulletproof_security_options_login_security_jtc', $JTC_Options);
	}

	echo $bps_topDiv;
	echo '<strong><font color="green">'.__('JTC-Lite Settings Saved.', 'bulletproof-security').'</font></strong><br>';
	echo $bps_bottomDiv;
}

if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else {

?>

<div id="LoginSecurityJTC" style="position:relative;top:0px;left:0px;margin:0px 0px 0px 0px;">

<form name="LoginSecurityJTC" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ); ?>" method="post">
	<?php wp_nonce_field('bps_login_security_jtc'); ?> 
	<?php $BPSoptionsJTC = get_option('bulletproof_security_options_login_security_jtc'); 
	$bps_tooltip_captcha_key = ! isset($BPSoptionsJTC['bps_tooltip_captcha_key']) ? '' : $BPSoptionsJTC['bps_tooltip_captcha_key'];	
	$bps_tooltip_captcha_hover_text = ! isset($BPSoptionsJTC['bps_tooltip_captcha_hover_text']) ? '' : $BPSoptionsJTC['bps_tooltip_captcha_hover_text'];		
	$bps_tooltip_captcha_title = ! isset($BPSoptionsJTC['bps_tooltip_captcha_title']) ? '' : $BPSoptionsJTC['bps_tooltip_captcha_title'];		
	$bps_tooltip_captcha_logging = ! isset($BPSoptionsJTC['bps_tooltip_captcha_logging']) ? '' : $BPSoptionsJTC['bps_tooltip_captcha_logging'];		
	$bps_jtc_custom_form_error = ! isset($BPSoptionsJTC['bps_jtc_custom_form_error']) ? '' : $BPSoptionsJTC['bps_jtc_custom_form_error'];
	$bps_jtc_comment_form_error = ! isset($BPSoptionsJTC['bps_jtc_comment_form_error']) ? '' : $BPSoptionsJTC['bps_jtc_comment_form_error'];
	$bps_jtc_comment_form_label = ! isset($BPSoptionsJTC['bps_jtc_comment_form_label']) ? '' : $BPSoptionsJTC['bps_jtc_comment_form_label'];
	$bps_jtc_comment_form_input = ! isset($BPSoptionsJTC['bps_jtc_comment_form_input']) ? '' : $BPSoptionsJTC['bps_jtc_comment_form_input'];	
	?>
    
 <h3><?php _e('JTC-Lite Settings', 'bulletproof-security'); ?></h3>   
    
<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('JTC CAPTCHA:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_tooltip_captcha_key" class="regular-text-250" value="<?php if ( $bps_tooltip_captcha_key != '' ) { echo $bps_tooltip_captcha_key; } else { echo ''; } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('jtc', 'bulletproof-security'); ?></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('JTC ToolTip:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_tooltip_captcha_hover_text" class="regular-text-250" value="<?php if ( $bps_tooltip_captcha_hover_text != '' ) { echo $bps_tooltip_captcha_hover_text; } else { echo 'Type/Enter:  '; } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('Type/Enter:  jtc. Enter a blank space for no text (Spacebar Key)', 'bulletproof-security'); ?></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('JTC Title|Text:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_tooltip_captcha_title" class="regular-text-250" value="<?php if ( $bps_tooltip_captcha_title != '' ) { echo $bps_tooltip_captcha_title; } else { echo 'Hover or click the text box below'; } ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('Enter a blank space for no text (Spacebar Key)', 'bulletproof-security'); ?></label></td>
  </tr>


<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 0px 0px;"></div>'; } else { ?>

  <tr>
    <td><label for="LSLog"><?php _e('JTC Logging:', 'bulletproof-security'); ?></label></td>
    <td><select name="bps_tooltip_captcha_logging" class="form-250">
	<option value="Off" <?php selected('Off', $bps_tooltip_captcha_logging); ?>><?php _e('JTC Logging Off', 'bulletproof-security'); ?></option>
	</select>
	</td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px; font-style:italic;font-weight:normal;"><?php _e('Logged in the Security Log (BPS Pro Only)', 'bulletproof-security'); ?></label></td>
  </tr>

<?php } ?>
<!-- Important: </table> needs to come after the closing php tag above for Network subsites -->
</table>
	
    <div id="JTC-woocommerce" style="margin:10px 0px 10px 0px">
	<input type="checkbox" name="bps_enable_jtc_woocommerce" value="1" <?php if ( empty( $BPSoptionsJTC['bps_enable_jtc_woocommerce'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_enable_jtc_woocommerce'], 1 ); } ?> /><label><?php _e(' Enable JTC for WooCommerce (BPS Pro Only)', 'bulletproof-security'); ?></label>
	</div>

   <label><strong><?php _e('Enable|Disable JTC For These Forms: ', 'bulletproof-security'); ?></strong></label><br />
   <label><i><?php _e('Check to Enable. Uncheck to Disable.', 'bulletproof-security'); ?></i></label><br />
    <input type="checkbox" name="bps_jtc_login_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_login_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_login_form'], 1 ); } ?> /><label><?php _e(' Login Form', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_register_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_register_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_register_form'], 1 ); } ?> /><label><?php _e(' Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_lostpassword_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_lostpassword_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_lostpassword_form'], 1 ); } ?> /><label><?php _e(' Lost Password Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_jtc_comment_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_comment_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_comment_form'], 1 ); } ?> /><label><?php _e(' Comment Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_mu_register_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_mu_register_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_mu_register_form'], 1 ); } ?> /><label><?php _e(' Multisite Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_buddypress_register_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_buddypress_register_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_buddypress_register_form'], 1 ); } ?> /><label><?php _e(' BuddyPress Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_buddypress_sidebar_form" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_buddypress_sidebar_form'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_buddypress_sidebar_form'], 1 ); } ?> /><label><?php _e(' BuddyPress Sidebar Login Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br /><br />

    <label><strong><?php _e('Comment Form: (BPS Pro Only)', 'bulletproof-security'); ?></strong></label><br />
    <label><strong><?php _e('Enable|Disable JTC For These Registered/Logged In User Roles (BPS Pro Only): ', 'bulletproof-security'); ?></strong></label><br />  
  <label><i><?php _e('Check to Enable. Uncheck to Disable.', 'bulletproof-security'); ?></i></label><br />
    <div id="Roles-scroller">
    <input type="checkbox" name="bps_jtc_administrator" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_administrator'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_administrator'], 1 ); } ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_editor" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_editor'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_editor'], 1 ); } ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_author" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_author'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_author'], 1 ); } ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_jtc_contributor" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_contributor'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_contributor'], 1 ); } ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_subscriber" value="1" <?php if ( empty( $BPSoptionsJTC['bps_jtc_subscriber'] ) ) { echo ''; } else { checked( $BPSoptionsJTC['bps_jtc_subscriber'], 1 ); } ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			echo "<input type=\"checkbox\" name=\"bps_jtc_custom_roles[$role_name]\" value=\"1\"";
			if ( ! empty($BPSoptionsJTC['bps_jtc_custom_roles'][$role_name]) ) {
			checked( $BPSoptionsJTC['bps_jtc_custom_roles'][$role_name], 1 );
			}
			echo " /><label> ". $role_info['name'] ."</label>".'<br>';
		}
	}
?> 
</div>

	<br />
    <label for="LSLog"><?php _e('Login Form: CAPTCHA Error message', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt29" name="bps_jtc_custom_form_error" class="regular-text-short-fixed" style="width:75%;" value="<?php if ($bps_jtc_custom_form_error != '') { echo $bps_jtc_custom_form_error; } else { echo '<strong>ERROR</strong>: Incorrect CAPTCHA Entered.'; } ?>" /><br /><br />

    <label for="LSLog"><?php _e('Comment Form: CAPTCHA Error message (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt30" name="bps_jtc_comment_form_error" class="regular-text-short-fixed" style="width:75%;" value="<?php if ($bps_jtc_comment_form_error != '') { echo $bps_jtc_comment_form_error; } else { echo '<strong>ERROR</strong>: Incorrect JTC CAPTCHA Entered. Click your Browser back button and re-enter the JTC CAPTCHA.'; } ?>" /><br /><br />
    
    <label><strong><?php _e('Comment Form: CSS Styling (BPS Pro Only)', 'bulletproof-security'); ?></strong></label><br />
    <label><?php _e('Comment Form Label (BPS Pro Only): <i>The JTC Title|Text above the Form Input text box</i>', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt31" name="bps_jtc_comment_form_label" class="regular-text-short-fixed" style="width:75%;" value="<?php if ($bps_jtc_comment_form_label != '') { echo $bps_jtc_comment_form_label; } else { echo 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;'; } ?>" /><br />
    <label><?php _e('Comment Form Input Text Box (BPS Pro Only): <i>The JTC CAPTCHA Form Input text box</i>', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt32" name="bps_jtc_comment_form_input" class="regular-text-short-fixed" style="width:75%;" value="<?php if ($bps_jtc_comment_form_input != '') { echo $bps_jtc_comment_form_input; } else { echo 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;'; } ?>" /><br /><br />

	<?php echo '<div id="jtc-tooltip" style="margin:0px 0px 10px 0px;max-width:640px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your JTC option settings then click the Encrypt JTC Code button first and then click the Save Options button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your JTC options settings and CSS code unless you encrypt it first by clicking the Encrypt JTC Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt JTC Code button and then want to edit your CSS code again click the Decrypt JTC Code button. After you are done editing click the Encrypt JTC Code button before clicking the Save Options button.', 'bulletproof-security').'<br><br>'.__('Click the JTC Anti-Spam|Anti-Hacker Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

<input type="submit" name="Submit-Security-Log-Options-JTC" class="button bps-button"  style="margin-top:5px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form><br />
</div>  

	<button onclick="bpsJTCEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt JTC Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsJTCDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt JTC Code', 'bulletproof-security'); ?></button>

<script type="text/javascript">
/* <![CDATA[ */
function bpsJTCEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt29").value;
  var String2 = document.getElementById("crypt30").value;  
  var String3 = document.getElementById("crypt31").value;
  var String4 = document.getElementById("crypt32").value; 

  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = String1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt2 = String2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt3 = String3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt4 = String4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (String1 != '' && NoEncrypt1 === false) {
  	var encrypted1 = encryption.encrypt(String1, nonceValue);
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	var encrypted2 = encryption.encrypt(String2, nonceValue);
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	var encrypted3 = encryption.encrypt(String3, nonceValue);
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	var encrypted4 = encryption.encrypt(String4, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (String1 != '' && NoEncrypt1 === false) {
  	document.getElementById("crypt29").value = encrypted1;
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	document.getElementById("crypt30").value = encrypted2;
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	document.getElementById("crypt31").value = encrypted3;
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	document.getElementById("crypt32").value = encrypted4;
  }
}

function bpsJTCDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt29").value;
  var String2 = document.getElementById("crypt30").value;
  var String3 = document.getElementById("crypt31").value;
  var String4 = document.getElementById("crypt32").value;

  let encryption = new bpsProJSEncryption();

  if (String1 != '') {
	var decrypted1 = encryption.decrypt(String1, nonceValue);
  }
  if (String2 != '') {
	var decrypted2 = encryption.decrypt(String2, nonceValue);
  }
  if (String3 != '') {
	var decrypted3 = encryption.decrypt(String3, nonceValue);
  }
  if (String4 != '') {
	var decrypted4 = encryption.decrypt(String4, nonceValue);
  }
  //console.log(decrypted1);
  
  if (String1 != '') {
	document.getElementById("crypt29").value = decrypted1;
  }
  if (String2 != '') {
	document.getElementById("crypt30").value = decrypted2;
  }
  if (String1 != '') {
	document.getElementById("crypt31").value = decrypted3;
  }
  if (String1 != '') {
	document.getElementById("crypt32").value = decrypted4;
  }
}
/* ]]> */
</script>

<?php } ?>

</td>
  </tr>
</table>

</div>

<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 0px 0px;"></div>'; } else { ?>

<div id="bps-tabs-3" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('Idle Session Logout (ISL) ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Automatically Logout Idle/Inactive User Accounts', 'bulletproof-security'); ?></span><br /><?php _e('Auth Cookie Expiration (ACE) ~ ', 'bulletproof-security'); ?></span><span style="font-size:.75em;"><?php _e('Change the WordPress Authentication Cookie Expiration Time', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 5px 0px;"><?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?>  <button id="bps-open-modal3" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content3" class="bps-dialog-hide" title="<?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?>">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-readme-table">
  <tr>
    <td class="bps-readme-table-td">

<?php 
	$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text; 	
	
	$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong>'; 	
	echo $text;
?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/idle-session-logout-isl-and-authentication-cookie-expiration-ace" title="ISL and ACE" target="_blank">
	<?php _e('ISL and ACE Forum Topic', 'bulletproof-security'); ?></a></strong><br /><br />

<?php
	echo $bps_modal_content3;
	$text = '<strong>'.__('The Help & FAQ tab pages contain help links.', 'bulletproof-security').'</strong>'; 
	echo $text;
?>
    </td>
  </tr> 
</table> 

</div>

<?php
if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else {
	$scrolltoISLMessage = isset($_REQUEST['scrolltoISLMessage']) ? (int) $_REQUEST['scrolltoISLMessage'] : 0;

// ISL Form processing
if ( isset( $_POST['Submit-ISL-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_isl_logout' );
	
	$Custom_Roles = empty($_POST['bps_isl_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_isl_custom_roles'];
		
	switch( $_POST['Submit-ISL-Options'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = $value;
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
	
		$pos1 = strpos( $_POST['bps_isl_custom_css_1'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_isl_custom_css_2'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_isl_custom_css_3'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_isl_custom_css_4'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
	
		if ( $pos1 === false ) {
			$bps_isl_custom_css_1 = esc_html($_POST['bps_isl_custom_css_1']);
		} else {
			$bps_isl_custom_css_1 = $Encryption->decrypt($_POST['bps_isl_custom_css_1'], $nonceValue);
		}
	
		if ( $pos2 === false ) {
			$bps_isl_custom_css_2 = esc_html($_POST['bps_isl_custom_css_2']);
		} else {
			$bps_isl_custom_css_2 = $Encryption->decrypt($_POST['bps_isl_custom_css_2'], $nonceValue);
		}
	
		if ( $pos3 === false ) {
			$bps_isl_custom_css_3 = esc_html($_POST['bps_isl_custom_css_3']);
		} else {
			$bps_isl_custom_css_3 = $Encryption->decrypt($_POST['bps_isl_custom_css_3'], $nonceValue);
		}
	
		if ( $pos4 === false ) {
			$bps_isl_custom_css_4 = esc_html($_POST['bps_isl_custom_css_4']);
		} else {
			$bps_isl_custom_css_4 = $Encryption->decrypt($_POST['bps_isl_custom_css_4'], $nonceValue);
		}
	}

	$bps_isl_administrator = ! empty($_POST['bps_isl_administrator']) ? '1' : '';
	$bps_isl_editor = ! empty($_POST['bps_isl_editor']) ? '1' : '';
	$bps_isl_author = ! empty($_POST['bps_isl_author']) ? '1' : '';
	$bps_isl_contributor = ! empty($_POST['bps_isl_contributor']) ? '1' : '';
	$bps_isl_subscriber = ! empty($_POST['bps_isl_subscriber']) ? '1' : '';
	$bps_isl_tinymce = ! empty($_POST['bps_isl_tinymce']) ? '1' : '';

	$ISL_Options = array(
	'bps_isl' 							=> $_POST['bps_isl'], 
	'bps_isl_timeout' 					=> esc_html($_POST['bps_isl_timeout']), 
	'bps_isl_logout_url' 				=> esc_html($_POST['bps_isl_logout_url']), 
	'bps_isl_login_url' 				=> esc_html($_POST['bps_isl_login_url']),
	'bps_isl_custom_message' 			=> esc_html($_POST['bps_isl_custom_message']),
	'bps_isl_custom_css_1' 				=> $bps_isl_custom_css_1,
	'bps_isl_custom_css_2' 				=> $bps_isl_custom_css_2,
	'bps_isl_custom_css_3' 				=> $bps_isl_custom_css_3,
	'bps_isl_custom_css_4' 				=> $bps_isl_custom_css_4,
	'bps_isl_user_account_exceptions' 	=> esc_html($_POST['bps_isl_user_account_exceptions']), 
	'bps_isl_administrator' 			=> $bps_isl_administrator, 
	'bps_isl_editor' 					=> $bps_isl_editor, 
	'bps_isl_author' 					=> $bps_isl_author, 
	'bps_isl_contributor' 				=> $bps_isl_contributor, 
	'bps_isl_subscriber' 				=> $bps_isl_subscriber, 
	'bps_isl_tinymce' 					=> $bps_isl_tinymce, 
	'bps_isl_uri_exclusions' 			=> esc_html($_POST['bps_isl_uri_exclusions']), 
	'bps_isl_custom_roles' 				=> $Custom_Roles_array  
	);	
	
	foreach( $ISL_Options as $key => $value ) {
		update_option('bulletproof_security_options_idle_session', $ISL_Options);
	}
	
	if ( $_POST['bps_isl'] == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ISL has been turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $_POST['bps_isl'] == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ISL has been turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}
?>

<div id="Idle-Session-Logout">

<form name="IdleSessionLogout" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-3' ); ?>" method="post">
	<?php wp_nonce_field('bps_isl_logout'); ?> 
	<?php $BPS_ISL_options = get_option('bulletproof_security_options_idle_session'); 
$ISL_on_off = ! isset($BPS_ISL_options['bps_isl']) ? '' : $BPS_ISL_options['bps_isl'];
$ISL_timeout = isset($BPS_ISL_options['bps_isl_timeout']) ? preg_replace('/\D/', "", esc_html($BPS_ISL_options['bps_isl_timeout'])) : esc_html('60');
$ISL_logout_url = isset($BPS_ISL_options['bps_isl_logout_url']) ? esc_url($BPS_ISL_options['bps_isl_logout_url']) : esc_url(plugins_url('/bulletproof-security/isl-logout.php'));
$ISL_login_url = isset($BPS_ISL_options['bps_isl_login_url']) ? esc_url($BPS_ISL_options['bps_isl_login_url']) : esc_url(site_url('/wp-login.php'));
$ISL_exclusions = isset($BPS_ISL_options['bps_isl_uri_exclusions']) ? esc_html($BPS_ISL_options['bps_isl_uri_exclusions']) : esc_html('');
$ISL_message = isset($BPS_ISL_options['bps_isl_custom_message']) ? esc_html($BPS_ISL_options['bps_isl_custom_message']) : esc_html('');
$ISL_css_1 = isset($BPS_ISL_options['bps_isl_custom_css_1']) ? esc_html($BPS_ISL_options['bps_isl_custom_css_1']) : esc_html('background-color:#fff;line-height:normal;');
$ISL_css_2 = isset($BPS_ISL_options['bps_isl_custom_css_2']) ? esc_html($BPS_ISL_options['bps_isl_custom_css_2']) : esc_html('position:fixed;top:20%;left:0%;text-align:center;height:100%;width:100%;');
$ISL_css_3 = isset($BPS_ISL_options['bps_isl_custom_css_3']) ? esc_html($BPS_ISL_options['bps_isl_custom_css_3']) : esc_html('border:5px solid gray;background-color:#BCE2F1;');
$ISL_css_4 = isset($BPS_ISL_options['bps_isl_custom_css_4']) ? esc_html($BPS_ISL_options['bps_isl_custom_css_4']) : esc_html('font-family:Verdana, Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;');
$ISL_exceptions = isset($BPS_ISL_options['bps_isl_user_account_exceptions']) ? esc_html($BPS_ISL_options['bps_isl_user_account_exceptions']) : esc_html('');	
	?>
    
 <h3><?php _e('Idle Session Logout (ISL) Settings', 'bulletproof-security'); ?></h3>   
    
<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label><br />
    <select name="bps_isl" class="form-250">
	<option value="On" <?php selected('On', $ISL_on_off); ?>><?php _e('ISL On', 'bulletproof-security'); ?></option>
	<option value="Off" <?php selected('Off', $ISL_on_off); ?>><?php _e('ISL Off', 'bulletproof-security'); ?></option>
	</select>
	</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_isl_timeout" class="regular-text-250" value="<?php echo $ISL_timeout; ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page URL:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_isl_logout_url" class="regular-text-450" value="<?php echo $ISL_logout_url; ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Login URL:', 'bulletproof-security'); ?></label><br />
    <label><strong><i><?php _e('Enter/Type: "No" (without quotes) if you do not want a Login URL displayed.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="text" name="bps_isl_login_url" class="regular-text-450" value="<?php echo $ISL_login_url; ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Exclude URLs|URIs:', 'bulletproof-security'); ?></label><br />
	<label><strong><i><?php _e('Enter URIs separated by a comma and a space: /some-post/, /some-page/', 'bulletproof-security'); ?></i></strong></label><br />
 	<textarea class="PFW-Allow-From-Text-Area" name="bps_isl_uri_exclusions" tabindex="1"><?php echo $ISL_exclusions; ?></textarea>
	<input type="hidden" name="scrolltoISLMessage" id="scrolltoISLMessage" value="<?php echo esc_html( $scrolltoISLMessage ); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Custom Message:', 'bulletproof-security'); ?></label><br />
 	<textarea class="PFW-Allow-From-Text-Area" name="bps_isl_custom_message" tabindex="1"><?php echo $ISL_message; ?></textarea>
	<input type="hidden" name="scrolltoISLMessage" id="scrolltoISLMessage" value="<?php echo esc_html( $scrolltoISLMessage ); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Custom CSS Style:', 'bulletproof-security'); ?></label><br />
	<label><strong><?php echo 'body {'; ?></strong></label>
    <input type="text" id="crypt33" name="bps_isl_custom_css_1" class="bps_isl_custom_css_1" value="<?php echo $ISL_css_1; ?>" />
    <label><strong><?php echo '}'; ?></strong></label><br />
	<label><strong><?php echo '#bpsMessage {'; ?></strong></label>
    <input type="text" id="crypt34" name="bps_isl_custom_css_2" class="bps_isl_custom_css_2" value="<?php echo $ISL_css_2; ?>" />
    <label><strong><?php echo '}'; ?></strong></label><br />
	<label><strong><?php echo '#bpsMessageTextBox {'; ?></strong></label>
    <input type="text" id="crypt35" name="bps_isl_custom_css_3" class="bps_isl_custom_css_3" value="<?php echo $ISL_css_3; ?>" />
    <label><strong><?php echo '}'; ?></strong></label><br />
	<label><strong><?php echo 'p {'; ?></strong></label>
    <input type="text" id="crypt36" name="bps_isl_custom_css_4" class="bps_isl_custom_css_4" value="<?php echo $ISL_css_4; ?>" />
    <label><strong><?php echo '}'; ?></strong></label><br />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('User Account Exceptions:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><i><?php _e('Enter User Account names separated by a comma and a space: johnDoe, janeDoe', 'bulletproof-security'); ?></i></label><br />
    <label for="LSLog"><i><?php _e('Idle Session Logout Time Will Not Be Applied For These User Accounts.', 'bulletproof-security'); ?></i></label><br />
    <input type="text" name="bps_isl_user_account_exceptions" class="regular-text-450" value="<?php echo $ISL_exceptions; ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Idle Session Logouts For These User Roles: ', 'bulletproof-security'); ?></strong></label><br />  
  	<label><strong><i><?php _e('Check to Enable. Uncheck to Disable. See the Read Me help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <div id="Roles-scroller">
   <input type="checkbox" name="bps_isl_administrator" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_administrator'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_administrator'], 1 ); } ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_isl_editor" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_editor'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_editor'], 1 ); } ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_isl_author" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_author'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_author'], 1 ); } ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_isl_contributor" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_contributor'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_contributor'], 1 ); } ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_isl_subscriber" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_subscriber'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_subscriber'], 1 ); } ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			echo "<input type=\"checkbox\" name=\"bps_isl_custom_roles[$role_name]\" value=\"1\""; 
			if ( ! empty($BPS_ISL_options['bps_isl_custom_roles'][$role_name]) ) {
				checked( $BPS_ISL_options['bps_isl_custom_roles'][$role_name], 1 );
			}
			echo " /><label> ". $role_info['name'] ."</label>".'<br>';
		}
	}
?> 
</div>

	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Idle Session Logouts For TinyMCE Editors: ', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Disable. Uncheck to Enable. See the Read Me help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="checkbox" name="bps_isl_tinymce" value="1" <?php if ( empty( $BPS_ISL_options['bps_isl_tinymce'] ) ) { echo ''; } else { checked( $BPS_ISL_options['bps_isl_tinymce'], 1 ); } ?> /><label><?php _e(' Enable|Disable ISL For TinyMCE Editor', 'bulletproof-security'); ?></label><br /><br />

	<?php echo '<div id="jtc-tooltip" style="margin:0px 0px 10px 0px;max-width:640px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your ISL option settings then click the Encrypt ISL Code button first and then click the Save Options button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your ISL option settings and CSS code unless you encrypt it first by clicking the Encrypt ISL Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt ISL Code button and then want to edit your CSS code again click the Decrypt ISL Code button. After you are done editing click the Encrypt ISL Code button before clicking the Save Options button.', 'bulletproof-security').'<br><br>'.__('Click the Idle Session Logout|Auth Cookie Expiration Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

<input type="submit" name="Submit-ISL-Options" class="button bps-button"  style="margin:5px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form>

</td>
</tr>
</table>
</form>

</div> 

	<button onclick="bpsISLEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt ISL Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsISLDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt ISL Code', 'bulletproof-security'); ?></button>
    
<script type="text/javascript">
/* <![CDATA[ */
function bpsISLEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt33").value;
  var String2 = document.getElementById("crypt34").value;  
  var String3 = document.getElementById("crypt35").value;
  var String4 = document.getElementById("crypt36").value; 

  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = String1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt2 = String2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt3 = String3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt4 = String4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (String1 != '' && NoEncrypt1 === false) {
  	var encrypted1 = encryption.encrypt(String1, nonceValue);
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	var encrypted2 = encryption.encrypt(String2, nonceValue);
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	var encrypted3 = encryption.encrypt(String3, nonceValue);
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	var encrypted4 = encryption.encrypt(String4, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (String1 != '' && NoEncrypt1 === false) {
  	document.getElementById("crypt33").value = encrypted1;
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	document.getElementById("crypt34").value = encrypted2;
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	document.getElementById("crypt35").value = encrypted3;
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	document.getElementById("crypt36").value = encrypted4;
  }
}

function bpsISLDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt33").value;
  var String2 = document.getElementById("crypt34").value;
  var String3 = document.getElementById("crypt35").value;
  var String4 = document.getElementById("crypt36").value;

  let encryption = new bpsProJSEncryption();

  if (String1 != '') {
	var decrypted1 = encryption.decrypt(String1, nonceValue);
  }
  if (String2 != '') {
	var decrypted2 = encryption.decrypt(String2, nonceValue);
  }
  if (String3 != '') {
	var decrypted3 = encryption.decrypt(String3, nonceValue);
  }
  if (String4 != '') {
	var decrypted4 = encryption.decrypt(String4, nonceValue);
  }
  //console.log(decrypted1);
  
  if (String1 != '') {
	document.getElementById("crypt33").value = decrypted1;
  }
  if (String2 != '') {
	document.getElementById("crypt34").value = decrypted2;
  }
  if (String1 != '') {
	document.getElementById("crypt35").value = decrypted3;
  }
  if (String1 != '') {
	document.getElementById("crypt36").value = decrypted4;
  }
}
/* ]]> */
</script>

<div id="ACE-Menu-Link"></div>

<h3 style="border-bottom:1px solid #999999;"><?php _e('WordPress Authentication Cookie Expiration (ACE) Settings', 'bulletproof-security'); ?></h3>

<div id="ACE-logout" style="position:relative;top:0px;left:0px;margin:0px 0px 0px 0px;">

<?php
// ACE Form processing
if ( isset( $_POST['Submit-ACE-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_auth_cookie_expiration' );
	
	$Custom_Roles = empty($_POST['bps_ace_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_ace_custom_roles'];		
	
	switch( $_POST['Submit-ACE-Options'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = $value;
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}
	}

	$bps_ace_administrator = ! empty($_POST['bps_ace_administrator']) ? '1' : '';
	$bps_ace_editor = ! empty($_POST['bps_ace_editor']) ? '1' : '';
	$bps_ace_author = ! empty($_POST['bps_ace_author']) ? '1' : '';
	$bps_ace_contributor = ! empty($_POST['bps_ace_contributor']) ? '1' : '';
	$bps_ace_subscriber = ! empty($_POST['bps_ace_subscriber']) ? '1' : '';
	$bps_ace_rememberme_disable = ! empty($_POST['bps_ace_rememberme_disable']) ? '1' : '';

	$ACE_Options = array(
	'bps_ace' 							=> $_POST['bps_ace'], 
	'bps_ace_expiration' 				=> esc_html($_POST['bps_ace_expiration']), 
	'bps_ace_rememberme_expiration' 	=> esc_html($_POST['bps_ace_rememberme_expiration']), 
	'bps_ace_user_account_exceptions' 	=> esc_html($_POST['bps_ace_user_account_exceptions']), 
	'bps_ace_administrator' 			=> $bps_ace_administrator, 
	'bps_ace_editor' 					=> $bps_ace_editor, 
	'bps_ace_author' 					=> $bps_ace_author, 
	'bps_ace_contributor' 				=> $bps_ace_contributor, 
	'bps_ace_subscriber' 				=> $bps_ace_subscriber, 
	'bps_ace_rememberme_disable' 		=> $bps_ace_rememberme_disable, 
	'bps_ace_custom_roles' 				=> $Custom_Roles_array  
	);	
	
	foreach( $ACE_Options as $key => $value ) {
		update_option('bulletproof_security_options_auth_cookie', $ACE_Options);
	}
	
	if ( $_POST['bps_ace'] == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ACE has been turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $_POST['bps_ace'] == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ACE has been turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}
?>

<form name="ACELogout" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-3' ); ?>" method="post">
	<?php wp_nonce_field('bps_auth_cookie_expiration'); ?>
	<?php $BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie'); 
$ACE_on_off = ! isset($BPS_ACE_options['bps_ace']) ? '' : $BPS_ACE_options['bps_ace'];
$ACE_Expiration = isset($BPS_ACE_options['bps_ace_expiration']) ? preg_replace('/\D/', "", esc_html($BPS_ACE_options['bps_ace_expiration'])) : esc_html('2880');
$ACE_RM_Expiration = isset($BPS_ACE_options['bps_ace_rememberme_expiration']) ? preg_replace('/\D/', "", esc_html($BPS_ACE_options['bps_ace_rememberme_expiration'])) : esc_html('20160');
$ACE_exceptions = isset($BPS_ACE_options['bps_ace_user_account_exceptions']) ? esc_html($BPS_ACE_options['bps_ace_user_account_exceptions']) : esc_html('');	
	?>
 
<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label><br />
    <select name="bps_ace" class="form-250"><br />
	<option value="On" <?php selected('On', $ACE_on_off); ?>><?php _e('ACE On', 'bulletproof-security'); ?></option>
	<option value="Off" <?php selected('Off', $ACE_on_off); ?>><?php _e('ACE Off', 'bulletproof-security'); ?></option>
	</select>
	</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Auth Cookie Expiration Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><?php _e('WP Default setting is 2880 Minutes/2 Days:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_ace_expiration" class="regular-text-250" value="<?php echo $ACE_Expiration; ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Remember Me Auth Cookie Expiration Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><?php _e('WP Default setting is 20160 Minutes/14 Days:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_ace_rememberme_expiration" class="regular-text-250" value="<?php echo $ACE_RM_Expiration; ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Remember Me Checkbox:', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Disable. Uncheck to Enable. See the Read Me help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="checkbox" name="bps_ace_rememberme_disable" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_rememberme_disable'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_rememberme_disable'], 1 ); } ?> /><label><?php _e(' Disable & do not display the Remember Me checkbox', 'bulletproof-security'); ?></label><br />
</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('User Account Exceptions:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><i><?php _e('Enter User Account names separated by a comma and a space: johnDoe, janeDoe', 'bulletproof-security'); ?></i></label><br />
    <label for="LSLog"><i><?php _e('Auth Cookie Expiration Time Will Not Be Applied To These User Accounts.', 'bulletproof-security'); ?></i></label><br />
    <input type="text" name="bps_ace_user_account_exceptions" class="regular-text-450" value="<?php echo $ACE_exceptions; ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Auth Cookie Expiration Time For These User Roles: ', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Enable. Uncheck to Disable. See the Read Me help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <div id="Roles-scroller">
    <input type="checkbox" name="bps_ace_administrator" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_administrator'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_administrator'], 1 ); } ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_ace_editor" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_editor'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_editor'], 1 ); } ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_ace_author" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_author'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_author'], 1 ); } ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_ace_contributor" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_contributor'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_contributor'], 1 ); } ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_ace_subscriber" value="1" <?php if ( empty( $BPS_ACE_options['bps_ace_subscriber'] ) ) { echo ''; } else { checked( $BPS_ACE_options['bps_ace_subscriber'], 1 ); } ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			echo "<input type=\"checkbox\" name=\"bps_ace_custom_roles[$role_name]\" value=\"1\"";
			if ( ! empty($BPS_ACE_options['bps_ace_custom_roles'][$role_name]) ) {
				checked( $BPS_ACE_options['bps_ace_custom_roles'][$role_name], 1 );
			}
			echo " /><label> ". $role_info['name'] ."</label>".'<br>';
		}
	}
?>    
	</div>    

	<input type="submit" name="Submit-ACE-Options" class="button bps-button" style="margin:15px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form><br />
</div> 

</td>
  </tr>
</table> 

<?php } ?>

</td>
  </tr>
</table>

</div>

<?php } ?>

<div id="bps-tabs-4" class="bps-tab-page" style="">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('Force Strong Passwords ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Set password requirements for strong passwords', 'bulletproof-security'); ?></span></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help" style="max-width:800px;">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Force Strong Passwords', 'bulletproof-security'); ?>  <button id="bps-open-modal4" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content4" class="bps-dialog-hide" title="<?php _e('Force Strong Passwords', 'bulletproof-security'); ?>">
	<p>
	
	<?php 
		$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		echo $bps_modal_content4; 
	?>
	
    </p>
</div>

<?php 

// FSP Form processing
if ( isset( $_POST['Submit-FSP-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_fsp_settings' );
	
	$bps_fsp_lower_case = ! empty($_POST['bps_fsp_lower_case']) ? '1' : '';
	$bps_fsp_upper_case = ! empty($_POST['bps_fsp_upper_case']) ? '1' : '';
	$bps_fsp_number = ! empty($_POST['bps_fsp_number']) ? '1' : '';
	$bps_fsp_special_char = ! empty($_POST['bps_fsp_special_char']) ? '1' : '';

	$FSP_Options = array(
	'bps_fsp_on_off' 		=> $_POST['bps_fsp_on_off'], 
	'bps_fsp_char_length' 	=> esc_html( trim($_POST['bps_fsp_char_length']) ), 
	'bps_fsp_lower_case' 	=> $bps_fsp_lower_case, 
	'bps_fsp_upper_case' 	=> $bps_fsp_upper_case,
	'bps_fsp_number' 		=> $bps_fsp_number,
	'bps_fsp_special_char'	=> $bps_fsp_special_char,
	'bps_fsp_message' 		=> esc_html($_POST['bps_fsp_message']) 
	);	
	
	foreach( $FSP_Options as $key => $value ) {
		update_option('bulletproof_security_options_fsp', $FSP_Options);
	}
	
	if ( $_POST['bps_fsp_on_off'] == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. FSP is turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $_POST['bps_fsp_on_off'] == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. FSP is turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}

$scrolltoFSPMessage = isset($_REQUEST['scrolltoFSPMessage']) ? (int) $_REQUEST['scrolltoFSPMessage'] : 0; 

?>

<form name="BPS-FSP" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-4' ); ?>" method="post">
	
<?php 
	wp_nonce_field('bps_fsp_settings'); 
	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');
	$bps_fsp_on_off = isset($BPS_FSP_options['bps_fsp_on_off']) ? esc_html($BPS_FSP_options['bps_fsp_on_off']) : esc_html('Off');
	$bps_fsp_char_length = isset($BPS_FSP_options['bps_fsp_char_length']) ? preg_replace('/\D/', "", esc_html($BPS_FSP_options['bps_fsp_char_length'])) : esc_html('12');	
	$FSP_Message = isset($BPS_FSP_options['bps_fsp_message']) ? esc_html($BPS_FSP_options['bps_fsp_message']) : esc_html('Password must contain 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be a minimum of 12 characters long.');	
?>

<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Turn FSP On|Turn FSP Off:', 'bulletproof-security'); ?></label></td>
    <td><select name="bps_fsp_on_off" class="regular-text-150" style="width:80px;">
		<option value="Off" <?php selected('Off', $bps_fsp_on_off); ?>><?php _e('FSP Off', 'bulletproof-security'); ?></option>
		<option value="On" <?php selected('On', $bps_fsp_on_off); ?>><?php _e('FSP On', 'bulletproof-security'); ?></option>
		</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Password Character Length:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_fsp_char_length" class="regular-text-150" style="width:80px;" value="<?php echo trim($bps_fsp_char_length); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('Example: 12', 'bulletproof-security'); ?></label></td>
  </tr>
</table>

<br />

   <label><strong><?php _e('Password Criteria Requirements: ', 'bulletproof-security'); ?></strong></label><br />
   <label><i><?php _e('Check to require. Uncheck to remove requirement.', 'bulletproof-security'); ?></i></label><br />
    <input type="checkbox" name="bps_fsp_lower_case" value="1" <?php if ( empty( $BPS_FSP_options['bps_fsp_lower_case'] ) ) { echo ''; } else { checked( $BPS_FSP_options['bps_fsp_lower_case'], 1 ); } ?> /><label><?php _e(' At least 1 lowercase letter', 'bulletproof-security'); ?></label><br />
     <input type="checkbox" name="bps_fsp_upper_case" value="1" <?php if ( empty( $BPS_FSP_options['bps_fsp_upper_case'] ) ) { echo ''; } else { checked( $BPS_FSP_options['bps_fsp_upper_case'], 1 ); } ?> /><label><?php _e(' At least 1 uppercase letter', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_fsp_number" value="1" <?php if ( empty( $BPS_FSP_options['bps_fsp_number'] ) ) { echo ''; } else { checked( $BPS_FSP_options['bps_fsp_number'], 1 ); } ?> /><label><?php _e(' At least 1 number', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_fsp_special_char" value="1" <?php  if ( empty( $BPS_FSP_options['bps_fsp_special_char'] ) ) { echo ''; } else { checked( $BPS_FSP_options['bps_fsp_special_char'], 1 ); } ?> /><label><?php _e(' At least 1 special character', 'bulletproof-security'); ?></label><br />

<br />

<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Displayed Message/Error Message:', 'bulletproof-security'); ?></label><br />
 	<textarea class="PFW-Allow-From-Text-Area" name="bps_fsp_message" tabindex="1"><?php echo $FSP_Message; ?></textarea>
	<input type="hidden" name="scrolltoFSPMessage" id="scrolltoFSPMessage" value="<?php echo esc_html( $scrolltoFSPMessage ); ?>" />
    </td>
  </tr>
</table>
	<input type="submit" name="Submit-FSP-Options" class="button bps-button" style="margin:15px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-5" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
   <tr>
    <td class="bps-table_title"><h2><?php _e('BulletProof Security Help &amp; FAQ', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    <a href="https://forum.ait-pro.com/forums/topic/security-log-event-codes/" target="_blank"><?php _e('Security Log Event Codes', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://www.ait-pro.com/aitpro-blog/category/bulletproof-security-contributors/" target="_blank"><?php _e('Contributors Page', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a>
    </td>
  </tr>
</table>
</div>
         
<div id="AITpro-link">BulletProof Security <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://www.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
</div>