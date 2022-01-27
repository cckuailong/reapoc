<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// jQuery ScrollTop Animation based on Browser User Agent
// Opera uses the Chromium Rendering engine & the UA is Chrome
function bpsPro_Browser_UA_scroll_animation() {
	
	$user_agent = esc_html($_SERVER['HTTP_USER_AGENT']);

	if ( preg_match( '/Chrome/i', $user_agent, $matches ) ) { ?>
		
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
	
			$("html, body").animate({ scrollTop: "50px" }, 400, function(){
 				$("html, body").animate({ scrollTop: "0px" });
				// essential for the jQuery UI Tabs framework hash anchors
				$( this ).css( "background", "url('') no-repeat left top" );
    		});
			return false;
		});
		/* ]]> */
		</script>

<?php } elseif ( preg_match( '/Firefox/i', $user_agent, $matches ) ) { ?>

		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
	
			$("html, body").animate({ scrollTop: "50px" }, 600, function(){
 				$("html, body").animate({ scrollTop: "0px" });
				// essential for the jQuery UI Tabs framework hash anchors
				$( this ).css( "background", "url('') no-repeat left top" );
			});
			return false;
		});
		/* ]]> */
		</script>

<?php } elseif ( preg_match( '/Safari/i', $user_agent, $matches ) ) { ?>

		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
	
			$("html, body").animate({ scrollTop: "100px" }, 600, function(){
 				$("html, body").animate({ scrollTop: "0px" });
				// essential for the jQuery UI Tabs framework hash anchors
				$( this ).css( "background", "url('') no-repeat left top" );
			});
			return false;
		});
		/* ]]> */
		</script>

<?php } elseif ( preg_match( '/MSIE/i', $user_agent, $matches ) || preg_match( '/Trident/i', $user_agent, $matches ) ) { ?>

		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
	
			$("html, body").animate({ scrollTop: "350px" }, 400, function(){
 				$("html, body").animate({ scrollTop: "0px" });
				// essential for the jQuery UI Tabs framework hash anchors
				$( this ).css( "background", "url('') no-repeat left top" );
			});
			return false;
		});
		/* ]]> */
		</script>

<?php } else { ?>

		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready(function($){
	
			$("html, body").animate({ scrollTop: "50px" }, 400, function(){
 				$("html, body").animate({ scrollTop: "0px" });
				// essential for the jQuery UI Tabs framework hash anchors
				$( this ).css( "background", "url('') no-repeat left top" );
    		});
			return false;
		});
		/* ]]> */
		</script>
<?php
	}
}

// Get the Current / Last Modifed Date of the bulletproof-security.php File - Minutes check
function getBPSInstallTime() {
$filename = WP_PLUGIN_DIR . '/bulletproof-security/bulletproof-security.php';

	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$last_modified_install = date("F d Y H:i", filemtime($filename) + $gmt_offset );
	return $last_modified_install;
	}
}

// Get the Current / Last Modifed Date of the bulletproof-security.php File + one minute buffer - Minutes check
function getBPSInstallTime_plusone() {
$filename = WP_PLUGIN_DIR . '/bulletproof-security/bulletproof-security.php';
	
	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$last_modified_install = date("F d Y H:i", filemtime($filename) + $gmt_offset + (60 * 1));
	return $last_modified_install;
	}
}

// Get the Current / Last Modifed Date of the Root .htaccess File - Minutes check
function getBPSRootHtaccessLasModTime_minutes() {
$filename = ABSPATH . '.htaccess';
	
	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$last_modified_install = date ("F d Y H:i", filemtime($filename) + $gmt_offset );
	return $last_modified_install;
	}
}

// Get the Current / Last Modifed Date of the wp-admin .htaccess File - Minutes check
function getBPSwpadminHtaccessLasModTime_minutes() {
$filename = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$last_modified_install = date ("F d Y H:i", filemtime($filename) + $gmt_offset );
	return $last_modified_install;
	}
}

// Recreate the User Agent filters in the 403.php file on BPS upgrade
function bpsPro_autoupdate_useragent_filters() {		
global $wpdb;

	$bps403File = WP_PLUGIN_DIR . '/bulletproof-security/403.php';

	if ( ! file_exists($bps403File) ) {
		return;
	}
	
	$blankFile = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/blank.txt';
	$userAgentMaster = WP_CONTENT_DIR . '/bps-backup/master-backups/UserAgentMaster.txt';

	if ( file_exists($blankFile) ) {
		copy($blankFile, $userAgentMaster);
	}

	$table_name = $wpdb->prefix . "bpspro_seclog_ignore";
	$search = '';
	
	$getSecLogTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE user_agent_bot LIKE %s", "%$search%" ) );
	$UserAgentRules = array();
	
	if ( $wpdb->num_rows != 0 ) {

		foreach ( $getSecLogTable as $row ) {
			$UserAgentRules[] = "(.*)".$row->user_agent_bot."(.*)|";
			file_put_contents($userAgentMaster, $UserAgentRules);
		}
	
	$UserAgentRulesT = file_get_contents($userAgentMaster);
	$stringReplace = file_get_contents($bps403File);

	$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/".trim($UserAgentRulesT, "|")."/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);
		
	file_put_contents($bps403File, $stringReplace);
	}
}

// Update/Add/Save any new DB options/features during the BPS upgrades
// bpsPro_new_version_db_options_files_autoupdate() is in general-functions.php
function bpsPro_new_feature_autoupdate() {
	bpsPro_new_version_db_options_files_autoupdate();
}

// BPS Status Display Admin notices
function bps_status_display_admin_notices() {
	
	if ( preg_match( '/page=stories-dashboard/', esc_html($_SERVER['QUERY_STRING']) ) || preg_match( '/page=backwpupbackups/', esc_html( $_SERVER['QUERY_STRING'] ) ) || preg_match( '/post_type=ai1ec_event/', esc_html( $_SERVER['QUERY_STRING'] ) ) ) {
		return;
	}

	if ( current_user_can('manage_options') ) { 
		bps_root_htaccess_status_dashboard();
		bps_wpadmin_htaccess_status_dashboard();
		bpsProMScanStatus();
		bpsProDBBStatus();
		bps_Login_Security_admin_notice_status_bps();
		bps_jtc_antispam_admin_notice_status_bps();
		bpsPro_isl_notice_status_bps();
		bpsPro_ace_notice_status_bps();
	}
}

add_action('admin_notices', 'bps_status_display_admin_notices');

// BPS Update/Upgrade Status Alert in WP Dashboard|Status Display BPS pages only
function bps_root_htaccess_status_dashboard() {

	if ( current_user_can('manage_options') ) {

		global $bps_version, $bps_last_version, $aitpro_bullet;

	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' ) {
		
		$bps_status_display = get_option('bulletproof_security_options_status_display'); 

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' ) {

			if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI']));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) );
			}		
		
			echo '<div id="bps-status-display" style="float:left;margin:6px 0px -40px 8px;padding:3px 5px 3px 5px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'" style="text-decoration:none;font-weight:bold;">'.__('Reload BPS Status Display', 'bulletproof-security').'</a></div>';
			echo '<div style="clear:both;"></div>';
			}
		}
		
		if ( isset($_POST['Submit-DBB-Run-Job']) && @$_POST['Submit-DBB-Run-Job'] == true || isset($_POST['Submit-DB-Table-Prefix']) && @$_POST['Submit-DB-Table-Prefix'] == true || isset($_POST['Submit-DB-Prefix-Table-Refresh']) && @$_POST['Submit-DB-Prefix-Table-Refresh'] == true ) {  
		
			$bpsPro_Spinner = get_option('bulletproof_security_options_spinner');	
	
		if ( $bpsPro_Spinner['bps_spinner'] != 'Off' ) {

			echo '<div id="bps-status-display" style="padding:2px 0px 4px 8px;width:240px;">';
			echo '<div id="bps-spinner" class="bps-spinner" style="background:#fff;border:4px solid black;">';
   			echo '<img id="bps-img-spinner" src="'.plugins_url('/bulletproof-security/admin/images/bps-spinner.gif').'" style="float:left;margin:0px 20px 0px 0px;" />'; 
			echo '<div id="bps-spinner-text-btn" style="padding:20px 0px 26px 0px;font-size:14px;">Processing...<br><button style="margin:10px 0px 0px 10px;" onclick="javascript:history.go(-1)">Cancel</button></div>';
			echo '</div>';

?>
    
<style>
<!--
.bps-spinner {
    visibility:visible;
	position:fixed;
    top:7%;
    left:45%;
 	width:240px;
	padding:2px 0px 4px 8px;   
	z-index:99999;
}
-->
</style>

<?php
		echo '</div>';
		}  
		}

	} elseif ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/system-info/system-info.php' ) {
		
		$bps_status_display = get_option('bulletproof_security_options_status_display');

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' ) {
		
		echo '<div id="bps-status-display" style="float:left;padding:0px 0px 10px 0px;">'.__('The BPS Status Display is set to Off by default on the System Info page', 'bulletproof-security').'</div>';
		echo '<div style="clear:both;"></div>';
		}

	} else {

	$options = get_option('bulletproof_security_options_autolock');	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	$filename = ABSPATH . '.htaccess';
	
	if ( file_exists($filename) ) {	
	
	$permsHtaccess = @substr(sprintf('%o', fileperms($filename)), -4);
	$sapi_type = @php_sapi_name();	
	$check_string = @file_get_contents($filename);
	$section = @file_get_contents($filename, NULL, NULL, 3, 38);	
	$bps_get_domain_root = bpsGetDomainRoot();
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	$bps_root_upgrade = '';
	$hostaddress = esc_html( @gethostbyaddr( $_SERVER['SERVER_ADDR'] ) );

	$patterna = '/RedirectMatch\s403\s\/\\\.\.\*\$/';
	//$pattern0 = '/ErrorDocument\s404\s(.*)\/404\.php\s*ErrorDocument\s410\s(.*)410\.php/s';		
	$pattern0 = '/#{1,}(\s|){1,}ErrorDocument\s405(.*)\/bulletproof-security\/405\.php/';
	$pattern1 = '/#\sFORBID\sEMPTY\sREFFERER\sSPAMBOTS(.*)RewriteCond\s%{HTTP_USER_AGENT}\s\^\$\sRewriteRule\s\.\*\s\-\s\[F\]/s';	
	// Only match 2 or more identical duplicate referer lines: 1 will not match and 2, 3, 4... will match
	$pattern2 = '/AnotherWebsite\.com\)\.\*\s*(RewriteCond\s%\{HTTP_REFERER\}\s\^\.\*'.$bps_get_domain_root.'\.\*\s*){2,}\s*RewriteRule\s\.\s\-\s\[S=1\]/s';
	$pattern4 = '/\.\*\(allow_url_include\|allow_url_fopen\|safe_mode\|disable_functions\|auto_prepend_file\) \[NC,OR\]/s';
	$pattern6 = '/(\[|\]|\(|\)|<|>|%3c|%3e|%5b|%5d)/s';
	$pattern7 = '/RewriteCond %{QUERY_STRING} \^\.\*(.*)[3](.*)[5](.*)[5](.*)[7](.*)\)/';
	$pattern8 = '/\[NC\]\s*RewriteCond\s%{HTTP_REFERER}\s\^\.\*(.*)\.\*\s*(.*)\s*(.*)\s*(.*)\s*(.*)\s*(.*)\s*RewriteRule\s\.\s\-\s\[S=1\]/';
	$pattern9 = '/RewriteCond\s%{QUERY_STRING}\s\(sp_executesql\)\s\[NC\]\s*(.*)\s*(.*)END\sBPSQSE(.*)\s*RewriteCond\s%{REQUEST_FILENAME}\s!-f\s*RewriteCond\s%{REQUEST_FILENAME}\s!-d\s*RewriteRule\s\.(.*)\/index\.php\s\[L\]\s*(.*)LOOP\sEND/';
	$pattern10 = '/#\sBEGIN\sBPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s*#\sThe\slibwww-perl\sUser\sAgent\sis\sforbidden/';
	$pattern10a = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern10b = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';	
	$pattern10c = '/RewriteCond\s%\{THE_REQUEST\}\s\(\\\\?.*%2a\)\+\(%20\+\|\\\\s\+.*HTTP\(:\/.*\[NC,OR\]/';
	$pattern11 = '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]\=http:\/\/\s\[OR\]/';
	$pattern12 = '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]\=\(\\\.\\\.\/\/\?\)\+\s\[OR\]/';
	$pattern13 = '/RewriteCond\s%\{QUERY_STRING\}\s\(\\\.\\\.\/\|\\\.\\\.\)\s\[OR\]/';
	$pattern14 = '/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/';
	$pattern_amod = '/#\sDENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES(.*\s*){6,8}<FilesMatch(.*)wp-config(.*\s*){4,6}<\/FilesMatch>/';
	$pattern15 = '/BPS\sPOST\sRequest\sAttack\sProtection/';
	$pattern16 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	$pattern17 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}#{1,}(\s|){1,}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	$pattern18 = '/#\sREQUEST\sMETHODS\sFILTERED(.*)RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\|TRACE\|DELETE\|TRACK\|DEBUG\)\s\[NC\](\s*){1}RewriteRule\s\^\(\.\*\)\$\s\-\s\[F\]/s';	
	$pattern19 = '/RewriteRule\s\^\(\.\*\)\$\s\-\s\[R=405,L\]/';
	// 2.3: Reverting: Match R,L for replacement to L
	$pattern20 = '/RewriteRule\s\^\(\.\*\)\$(.*)\/bulletproof-security\/405\.php\s\[R,L\]/';
	$pattern21 = '/RewriteCond\s%\{THE_REQUEST\}\s\(\\\?.*%2a\)\+\(%20.*HTTP\(:\/.*\[NC,OR\]/';
	$pattern22 = '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=http:\/\/\s\[NC,OR\]/';
	$pattern23 = '/RewriteCond\s%\{QUERY_STRING\}\s\^\(\.\*\)cPath=http:\/\/\(\.\*\)\$\s\[NC,OR\]/';
	$pattern24 = '/RewriteCond\s%\{QUERY_STRING\}\shttp\\\:\s\[NC,OR\](.*\s*){1}.*RewriteCond\s%\{QUERY_STRING\}\shttps\\\:\s\[NC,OR\]/';
	// BPS 1.0: version numbering change. The string replace is on line 365
	$BPSVpattern = '/BULLETPROOF\s\.[\d](.*)[\>]/';
	$BPSVpattern2 = '/BULLETPROOF\s[\d]\.[\d]/';
	$BPSVpattern3 = '/BULLETPROOF\s\.[\d][\d]\.[\d]/';
	$BPSVreplace = "BULLETPROOF $bps_version";
	}

	if ( ! file_exists($filename) ) {
		
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {

			echo '<div id="bps-status-display" style="float:left;"><strong>'.__('BPS ', 'bulletproof-security').$bps_version.'</strong></div>';

		} elseif ( $HFiles_options['bps_htaccess_files'] != 'disabled' ) {
		
			if ( ! get_option('bulletproof_security_options_wizard_free') ) {
			
				$text = '<div class="update-nag" style="BPS Setup Wizard Notification><font color="blue">'.__('BPS Setup Wizard Notification', 'bulletproof-security').'</font><br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Setup Wizard page and click the Setup Wizard button to setup the BPS plugin.', 'bulletproof-security').'</div>';
				echo $text;			

			} else {
		
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:500;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! An htaccess file was NOT found in your WordPress root folder', 'bulletproof-security').'</font><br>'.__('If you have deleted the root htaccess file for troubleshooting purposes you can disregard this Alert.', 'bulletproof-security').'<br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
				echo $text;
			}
		}

	} else {
	
	if ( file_exists($filename) ) {

switch ( $bps_version ) {
    case $bps_last_version: // for testing
		if ( strpos( $check_string, "BULLETPROOF $bps_last_version" ) && strpos( $check_string, "BPSQSE" ) ) {
			print($section);
		}
		break; 
    case ! strpos( $check_string, "BULLETPROOF" ) && ! strpos( $check_string, "DEFAULT" ):
	
		// Setup Wizard Notice
		if ( ! get_option('bulletproof_security_options_wizard_free') ) {
				
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Setup Wizard Notification', 'bulletproof-security').'</font><br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Setup Wizard page and click the Setup Wizard button to setup the BPS plugin.', 'bulletproof-security').'</div>';
			echo $text;			
		
		} else {

			global $current_user;
			$user_id = $current_user->ID;		
			
			if ( ! get_user_meta($user_id, 'bps_ignore_root_version_check_notice') ) {
			
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! Your site may not be protected by BulletProof Security', 'bulletproof-security').'</font><br>'.__('The BPS version: BULLETPROOF x.x SECURE .HTACCESS line of code was not found at the top of your Root htaccess file.', 'bulletproof-security').'<br>'.__('The BPS version line of code MUST be at the very top of your Root htaccess file.', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Setup Wizard page and click the Setup Wizard button to setup the BPS plugin again.', 'bulletproof-security').'<br>'.__('Important Note: If you manually added other htaccess code above the BPS version line of code in your root htaccess file, you can copy that code to BPS Root Custom Code so that your code is saved in the correct place in the BPS root htaccess file. ', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Custom Code page, add your Root custom htaccess code in an appropriate Root Custom Code text box and click the Save Root Custom Code button before running the Setup Wizard again.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_root_version_check_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
			}
		}

		break;	
	case ! strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ):
	
			// Update/Add/Save any New DB options/features on upgrade
			bpsPro_new_feature_autoupdate();
			// mod_authz_core forward/backward compatibility: create new htaccess files if needed
			bpsPro_apache_mod_directive_check();
			$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
			$BPSCustomCodeOptions = get_option('bulletproof_security_options_customcode');
			// Recreate the User Agent filters in the 403.php file on BPS upgrade
			bpsPro_autoupdate_useragent_filters();
			
			if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
				@chmod($filename, 0644);
			}			

			$stringReplace = @file_get_contents($filename);
			
			if ( preg_match($BPSVpattern, $stringReplace) ) {
				$stringReplace = preg_replace($BPSVpattern, $BPSVreplace, $stringReplace);
			} elseif ( preg_match($BPSVpattern2, $stringReplace) ) {
				$stringReplace = preg_replace($BPSVpattern2, $BPSVreplace, $stringReplace);
			} elseif ( preg_match($BPSVpattern3, $stringReplace) ) {
				$stringReplace = preg_replace($BPSVpattern3, $BPSVreplace, $stringReplace);
			}	
			
			$stringReplace = str_replace("RewriteCond %{HTTP_USER_AGENT} (libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]", $stringReplace);
			
		if ( preg_match($patterna, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/#\sDENY\sACCESS\sTO\sPROTECTED\sSERVER\sFILES(.*)RedirectMatch\s403\s\/\\\.\.\*\$/s', "# DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS\n# Files and folders starting with a dot: .htaccess, .htpasswd, .errordocs, .logs\nRedirectMatch 403 \.(htaccess|htpasswd|errordocs|logs)$", $stringReplace);
		}

		// .53.1: Create new block of Error Logging and Tracking code & help text if Custom Code is blank & New ErrorDocument 405 code does not exist.
		if ( $BPSCustomCodeOptions['bps_customcode_error_logging'] == '' && ! preg_match( $pattern0, $stringReplace, $matches ) ) {
			$stringReplace = preg_replace('/#\sBPS\sERROR\sLOGGING\sAND\sTRACKING.*(ErrorDocument\s404(.*)\/404\.php|ErrorDocument\s410(.*)\/bulletproof-security\/410\.php)/s', "# BPS ERROR LOGGING AND TRACKING\n# Use BPS Custom Code to modify/edit/change this code and to save it permanently.\n# BPS has premade 400 Bad Request, 403 Forbidden, 404 Not Found, 405 Method Not Allowed and\n# 410 Gone template logging files that are used to track and log 400, 403, 404, 405 and 410 errors\n# that occur on your website. When a hacker attempts to hack your website the hackers IP address,\n# Host name, Request Method, Referering link, the file name or requested resource, the user agent\n# of the hacker and the query string used in the hack attempt are logged.\n# All BPS log files are htaccess protected so that only you can view them.\n# The 400.php, 403.php, 404.php, 405.php and 410.php files are located in /$bps_plugin_dir/bulletproof-security/\n# The 400, 403, 405 and 410 Error logging files are already set up and will automatically start logging errors\n# after you install BPS and have activated BulletProof Mode for your Root folder.\n# If you would like to log 404 errors you will need to copy the logging code in the BPS 404.php file\n# to your Theme's 404.php template file. Simple instructions are included in the BPS 404.php file.\n# You can open the BPS 404.php file using the WP Plugins Editor or manually editing the file.\n# NOTE: By default WordPress automatically looks in your Theme's folder for a 404.php Theme template file.\n\nErrorDocument 400 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/400.php\nErrorDocument 401 default\nErrorDocument 403 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/403.php\nErrorDocument 404 $bps_get_wp_root_secure"."404.php\nErrorDocument 405 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/405.php\nErrorDocument 410 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/410.php", $stringReplace);
		}
		
		// .53: Create new block of Request Methods Filtered code & help text.
		// .53.1: Old RMF Code exists: Conditional host check added to create either R=405 for Go Daddy or dumbed down code for all other hosts.
		// 3.9: removing this RMF cleanup code. Only dumbed down RMF code is created now.		
		/*
		if ( preg_match( $pattern18, $stringReplace, $matches ) && preg_match( '/secureserver\.net/', $hostaddress, $matches ) ) {
			$stringReplace = preg_replace( $pattern18, "# REQUEST METHODS FILTERED\n# If you want to allow HEAD Requests use BPS Custom Code and copy\n# this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code\n# text box: CUSTOM CODE REQUEST METHODS FILTERED.\n# See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.\nRewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]\nRewriteRule ^(.*)$ - [F]\nRewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]\nRewriteRule ^(.*)$ - [R=405,L]", $stringReplace);
		} elseif ( preg_match( $pattern18, $stringReplace, $matches ) && ! preg_match( '/secureserver\.net/', $hostaddress, $matches ) ) {
			$stringReplace = preg_replace( $pattern18, "# REQUEST METHODS FILTERED\n# If you want to allow HEAD Requests use BPS Custom Code and copy\n# this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code\n# text box: CUSTOM CODE REQUEST METHODS FILTERED.\n# See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.\nRewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]\nRewriteRule ^(.*)$ - [F]\nRewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]\nRewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]", $stringReplace);			
		}

		// .53.1: New RMF R=405 Code exists: Replace the R=405 code if the host is Not Go Daddy & the R=405 code does not exist in Custom Code.
		if ( preg_match( $pattern19, $stringReplace, $matches ) && ! preg_match( '/secureserver\.net/', $hostaddress ) && ! preg_match( '/R=405/', $BPSCustomCodeOptions['bps_customcode_request_methods'] ) ) {			
			$stringReplace = preg_replace( $pattern19, "RewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]", $stringReplace);
		}

		// 2.0: Add R to the dumb downed Request Methods Filtered 405 htaccess code in the Root htaccess file.
		// 2.3: Reverted: Remove R due to duplicate security log entries
		if ( preg_match( $pattern20, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern20, "RewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]", $stringReplace);
		}
		*/
		
		// 2.0: Add additional https scheme conditions to 3 htaccess security rules and combine 2 rules into 1 rule.
		if ( preg_match( $pattern21, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern21, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)(http|https)(:/|/) [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern22, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern22, "RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern23, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern23, "RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern24, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern24, "RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern1, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/#\sFORBID\sEMPTY\sREFFERER\sSPAMBOTS(.*)RewriteCond\s%{HTTP_USER_AGENT}\s\^\$\sRewriteRule\s\.\*\s\-\s\[F\]/s', '', $stringReplace);
		}			
			
		if ( preg_match($pattern2, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/AnotherWebsite\.com\)\.\*\s*(RewriteCond\s%\{HTTP_REFERER\}\s\^\.\*'.$bps_get_domain_root.'\.\*\s*){2,}\s*RewriteRule\s\.\s\-\s\[S=1\]/s', "AnotherWebsite.com).*\nRewriteCond %{HTTP_REFERER} ^.*$bps_get_domain_root.*\nRewriteRule . - [S=1]", $stringReplace);
		}
		
		if ( ! preg_match($pattern10, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/#\sBPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s*#\sThe\slibwww-perl\sUser\sAgent\sis\sforbidden/', "# BEGIN BPSQSE BPS QUERY STRING EXPLOITS\n# The libwww-perl User Agent is forbidden", $stringReplace);
		}

		if ( preg_match($pattern10a, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10a, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern10b, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10b, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern10c, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10c, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern11, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]\=http:\/\/\s\[OR\]/s', "RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern12, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]\=\(\\\.\\\.\/\/\?\)\+\s\[OR\]/s', "RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern13, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/RewriteCond\s%\{QUERY_STRING\}\s\(\\\.\\\.\/\|\\\.\\\.\)\s\[OR\]/s', "RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern6, $stringReplace, $matches)) {
			$stringReplace = str_replace("RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>|%3c|%3e|%5b|%5d).* [NC,OR]", "RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>|%3c|%3e).* [NC,OR]", $stringReplace);
			$stringReplace = str_replace("RewriteCond %{QUERY_STRING} ^.*(\x00|\x04|\x08|\x0d|\x1b|\x20|\x3c|\x3e|\x5b|\x5d|\x7f).* [NC,OR]", "RewriteCond %{QUERY_STRING} ^.*(\x00|\x04|\x08|\x0d|\x1b|\x20|\x3c|\x3e|\x7f).* [NC,OR]", $stringReplace);		
		}
		
		if ( preg_match($pattern7, $stringReplace, $matches)) {
			$stringReplace = preg_replace('/RewriteCond %{QUERY_STRING} \^\.\*(.*)[5](.*)[5](.*)\)/', 'RewriteCond %{QUERY_STRING} ^.*(\x00|\x04|\x08|\x0d|\x1b|\x20|\x3c|\x3e|\x7f)', $stringReplace);
		}

		if ( preg_match($pattern14, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/s', "RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]", $stringReplace);
		}

		if ( ! preg_match($pattern4, $stringReplace, $matches) ) {
			$stringReplace = str_replace("RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]", "RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]\nRewriteCond %{QUERY_STRING} \-[sdcr].*(allow_url_include|allow_url_fopen|safe_mode|disable_functions|auto_prepend_file) [NC,OR]", $stringReplace);
		}

		if ( ! is_multisite() && ! preg_match($pattern9, $stringReplace, $matches) ) {
			$stringReplace = preg_replace('/RewriteCond\s%{QUERY_STRING}\s\(sp_executesql\)\s\[NC\]\s*(.*)\s*RewriteCond\s%{REQUEST_FILENAME}\s!-f\s*RewriteCond\s%{REQUEST_FILENAME}\s!-d\s*RewriteRule\s\.(.*)\/index\.php\s\[L\]/', "RewriteCond %{QUERY_STRING} (sp_executesql) [NC]\nRewriteRule ^(.*)$ - [F,L]\n# END BPSQSE BPS QUERY STRING EXPLOITS\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . ".$bps_get_wp_root_secure."index.php [L]\n# WP REWRITE LOOP END", $stringReplace);
		}

		if ( preg_match( $pattern_amod, $stringReplace, $matches ) && $BPSCustomCodeOptions['bps_customcode_deny_files'] == '' && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
			
			$stringReplace = preg_replace( $pattern_amod, "# DENY BROWSER ACCESS TO THESE FILES\n# Use BPS Custom Code to modify/edit/change this code and to save it permanently.\n# wp-config.php, bb-config.php, php.ini, php5.ini, readme.html\n# To be able to view these files from a Browser, replace 127.0.0.1 with your actual\n# current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1\n# Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1\n# Note: The BPS System Info page displays which modules are loaded on your server.\n\n<FilesMatch \"^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)\">\n<IfModule mod_authz_core.c>\nRequire all denied\n#Require ip 127.0.0.1\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\nDeny from all\n#Allow from 127.0.0.1\n</IfModule>\n</IfModule>\n</FilesMatch>", $stringReplace);
		
		} elseif ( preg_match( $pattern_amod, $stringReplace, $matches ) && $BPSCustomCodeOptions['bps_customcode_deny_files'] == '' && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) {
			
			$stringReplace = preg_replace( $pattern_amod, "# DENY BROWSER ACCESS TO THESE FILES\n# Use BPS Custom Code to modify/edit/change this code and to save it permanently.\n# wp-config.php, bb-config.php, php.ini, php5.ini, readme.html\n# To be able to view these files from a Browser, replace 127.0.0.1 with your actual\n# current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1\n# Note: The BPS System Info page displays which modules are loaded on your server.\n\n<FilesMatch \"^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)\">\nOrder Allow,Deny\nDeny from all\n#Allow from 127.0.0.1\n</FilesMatch>", $stringReplace);	
		}

		// .52.9: POST Request Attack Protection code correction|addition
		// .53: Condition added to allow commenting out wp-admin URI whitelist rule
		if ( preg_match( $pattern15, $stringReplace, $matches ) && ! preg_match( $pattern16, $stringReplace, $matches ) && ! preg_match( $pattern17, $stringReplace, $matches ) ) {
			$stringReplace = preg_replace('/RewriteCond\s%\{REQUEST_METHOD\}\sPOST\s\[NC\]/s', "RewriteCond %{REQUEST_METHOD} POST [NC]\n# NEVER COMMENT OUT THIS LINE OF CODE BELOW FOR ANY REASON\nRewriteCond %{REQUEST_URI} !^.*/wp-admin/ [NC]\n# Whitelist the WordPress Theme Customizer\nRewriteCond %{HTTP_REFERER} !^.*/wp-admin/customize.php", $stringReplace);
		}

		// Clean up - replace 3 and 4 multiple newlines with 1 newline
		if ( preg_match('/(\n\n\n|\n\n\n\n)/', $stringReplace, $matches) ) {			
			$stringReplace = preg_replace("/(\n\n\n|\n\n\n\n)/", "\n", $stringReplace);
		}
		// remove duplicate referer lines
		if ( preg_match($pattern8, $stringReplace, $matches) ) {
			$stringReplace = preg_replace("/\[NC\]\s*RewriteCond\s%{HTTP_REFERER}\s\^\.\*(.*)\.\*\s*(.*)\s*(.*)\s*(.*)\s*(.*)\s*(.*)\s*RewriteRule\s\.\s\-\s\[S=1\]/", "[NC]\nRewriteCond %{HTTP_REFERER} ^.*$bps_get_domain_root.*\nRewriteRule . - [S=1]", $stringReplace);
		}

		file_put_contents($filename, $stringReplace);
		
		if ( $options['bps_root_htaccess_autolock'] == 'On') {			
			@chmod($filename, 0404);
		}

		if ( getBPSInstallTime() == getBPSRootHtaccessLasModTime_minutes() || getBPSInstallTime_plusone() == getBPSRootHtaccessLasModTime_minutes() ) {
			
			$bps_root_upgrade = 'upgrade';
			
			$pos = strpos( $check_string, 'IMPORTANT!!! DO NOT DELETE!!! - B E G I N Wordpress' );
			
			if ( $pos === false ) {			
			
				$updateText = '<div class="update-nag" style="float:left;"background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);><font color="blue">'.__("The BPS Automatic htaccess File Update Completed Successfully!", 'bulletproof-security').'</font></div>';
				print($updateText);				
			}
		}  // end up upgrade processing
		break;		
	case strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ):
		
		$bps_status_display = get_option('bulletproof_security_options_status_display');

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' ) {
					
			if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

				$RBM = $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'" title="Root Folder BulletProof Mode" style="text-decoration:none;">'.__('RBM', 'bulletproof-security').'</a>: <font color="green"><strong>'.__('On', 'bulletproof-security').'</strong></font>';
				$RBM_str = str_replace( "BULLETPROOF $bps_version SECURE .HTACCESS", "BPS $bps_version", $section );
			
				echo '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px 0px 0px 5px;"><strong>'.$RBM_str.'</strong>'.$RBM.'</div>';		
			}
		}
		break;
	default:
		
		if ( $bps_root_upgrade != 'upgrade' ) {		
		
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! Your site does not appear to be protected by BulletProof Security', 'bulletproof-security').'</font><br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
			echo $text;
		}
	}
	}
	}
	}
	}
}

// 3.7: Changed the BPS version check in the root htaccess file to a Dismiss Notice
add_action('admin_init', 'bps_root_version_check_nag_ignore');

function bps_root_version_check_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_root_version_check_nag_ignore']) && '0' == $_GET['bps_root_version_check_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_root_version_check_notice', 'true', true);
	}
}

// BPS Update/Upgrade Status Alert in WP Dashboard|Status Display in BPS pages only
function bps_wpadmin_htaccess_status_dashboard() {

	if ( current_user_can('manage_options') ) {

	global $bps_version, $bps_last_version, $aitpro_bullet;

	if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {

	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( isset( $BPS_wpadmin_Options['bps_wpadmin_restriction'] ) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || isset( $GDMW_options['bps_gdmw_hosting'] ) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		return;
	}
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
	$filename = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($filename) ) {

	$permsHtaccess = @substr(sprintf('%o', fileperms($filename)), -4);	
	$check_string = @file_get_contents($filename);
	$section = @file_get_contents($filename, NULL, NULL, 3, 46);
	$bps_wpadmin_upgrade = '';	
	
	$pattern10a = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern10b = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';	
	$pattern10c = '/RewriteCond\s%\{THE_REQUEST\}\s\(\\\\?.*%2a\)\+\(%20\+\|\\\\s\+.*HTTP\(:\/.*\[NC,OR\]/';
	$pattern1 = '/(\[|\]|\(|\)|<|>)/s';
	$pattern_amod = '/#\sWPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES(.*\s*){13,16}#\sEND\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES/';
	$pattern21 = '/RewriteCond\s%\{THE_REQUEST\}\s\(\\\?.*%2a\)\+\(%20.*HTTP\(:\/.*\[NC,OR\]/';
	$pattern22 = '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=http:\/\/\s\[NC,OR\]/';
	$pattern23 = '/RewriteCond\s%\{QUERY_STRING\}\s\^\(\.\*\)cPath=http:\/\/\(\.\*\)\$\s\[NC,OR\]/';
	$pattern24 = '/RewriteCond\s%\{QUERY_STRING\}\shttp\\\:\s\[NC,OR\](.*\s*){1}.*RewriteCond\s%\{QUERY_STRING\}\shttps\\\:\s\[NC,OR\]/';
	$pattern25 = '/#\sREQUEST\sMETHODS\sFILTERED(.*\s*){1}RewriteEngine\sOn(.*\s*){1}RewriteCond(.*\s*){1}RewriteRule\s\^\(\.\*\)\$\s\-\s\[F\]/';
	$BPSVpattern = '/BULLETPROOF\s\.[\d](.*)WP-ADMIN/';
	$BPSVpattern2 = '/BULLETPROOF\s[\d]\.[\d]\sWP-ADMIN/';
	$BPSVreplace = "BULLETPROOF $bps_version WP-ADMIN";
	}
	
	if ( ! file_exists($filename) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {
		
		if ( get_option('bulletproof_security_options_wizard_free') ) {	
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! An htaccess file was NOT found in your WordPress wp-admin folder', 'bulletproof-security').'</font><br>'.__('If you have deleted the wp-admin htaccess file for troubleshooting purposes you can disregard this Alert.', 'bulletproof-security').'<br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the wp-admin Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
			echo $text;
		}
	
	} else {
	
	if ( file_exists($filename) ) {

switch ( $bps_version ) {
    case $bps_last_version: // for Testing
		if ( strpos( $check_string, "BULLETPROOF $bps_last_version" ) && strpos( $check_string, "BPSQSE-check" ) ) {
			// echo or print for testing
		}
		break;
    case ! strpos( $check_string, "BULLETPROOF" ):

		// Setup Wizard Notice: not displayed. The Setup Wizard DB option is automatically saved in the root htaccess funcion on BPS plugin upgrades.
		if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		// display nothing. Notice is already displayed in the root htaccess function.	
		
		} else {

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! Your wp-admin folder may not be protected by BulletProof Security', 'bulletproof-security').'</font><br>'.__('The BPS version: BULLETPROOF .xx.x WP-ADMIN SECURE .HTACCESS line of code was not found at the top of your wp-admin htaccess file.', 'bulletproof-security').'<br>'.__('The BPS version line of code MUST be at the very top of your wp-admin htaccess file.', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Setup Wizard page and click the Setup Wizard button to setup the BPS plugin again.', 'bulletproof-security').'<br>'.__('Important Note: If you manually added other htaccess code above the BPS version line of code in your wp-admin htaccess file, you can copy that code to BPS wp-admin Custom Code so that your code is saved in the correct place in the BPS wp-admin htaccess file. ', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ).'">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Custom Code page, add your wp-admin custom htaccess code in an appropriate wp-admin Custom Code text box and click the Save wp-admin Custom Code button before running the Setup Wizard again.', 'bulletproof-security').'</div>';
			echo $text;
		}

		break;
	case ! strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ):
			
			// mod_authz_core forward/backward compatibility: create new htaccess files if needed
			bpsPro_apache_mod_directive_check();
			$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA');
			$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
			$sapi_type = php_sapi_name();
			
			if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
				@chmod($filename, 0644);
			}
			
			$stringReplace = @file_get_contents($filename);
			
			if ( preg_match($BPSVpattern, $stringReplace) ) {
				$stringReplace = preg_replace($BPSVpattern, $BPSVreplace, $stringReplace);
			} elseif ( preg_match($BPSVpattern2, $stringReplace) ) {
				$stringReplace = preg_replace($BPSVpattern2, $BPSVreplace, $stringReplace);
			}
			
		if ( preg_match( $pattern_amod, $stringReplace, $matches ) && $CC_Options_wpadmin['bps_customcode_deny_files_wpa'] == '' && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
			
			$stringReplace = preg_replace( $pattern_amod, "# WPADMIN DENY BROWSER ACCESS TO FILES\n# Deny Browser access to /wp-admin/install.php\n# Use BPS Custom Code to modify/edit/change this code and to save it permanently.\n# To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual\n# current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1\n# Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1\n# Note: The BPS System Info page displays which modules are loaded on your server.\n\n# BEGIN BPS WPADMIN DENY ACCESS TO FILES\n<FilesMatch \"^(install\.php)\">\n<IfModule mod_authz_core.c>\nRequire all denied\n#Require ip 127.0.0.1\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\nDeny from all\n#Allow from 127.0.0.1\n</IfModule>\n</IfModule>\n</FilesMatch>\n# END BPS WPADMIN DENY ACCESS TO FILES", $stringReplace);
		
		} elseif ( preg_match( $pattern_amod, $stringReplace, $matches ) && $CC_Options_wpadmin['bps_customcode_deny_files_wpa'] == '' && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) {
			
			$stringReplace = preg_replace( $pattern_amod, "# WPADMIN DENY BROWSER ACCESS TO FILES\n# Deny Browser access to /wp-admin/install.php\n# Use BPS Custom Code to modify/edit/change this code and to save it permanently.\n# To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual\n# current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1\n# Note: The BPS System Info page displays which modules are loaded on your server.\n\n# BEGIN BPS WPADMIN DENY ACCESS TO FILES\n<FilesMatch \"^(install\.php)\">\nOrder Allow,Deny\nDeny from all\n#Allow from 127.0.0.1\n</FilesMatch>\n# END BPS WPADMIN DENY ACCESS TO FILES", $stringReplace);	
		}

		if ( preg_match( $pattern25, $stringReplace, $matches ) ) {
			$stringReplace = preg_replace( $pattern25, "# BPS REWRITE ENGINE\nRewriteEngine On", $stringReplace);
		}

		if ( preg_match($pattern10a, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10a, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern10b, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10b, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern10c, $stringReplace, $matches) ) {
			$stringReplace = preg_replace( $pattern10c, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)HTTP(:/|/) [NC,OR]", $stringReplace);
		}

		// 2.0: Add additional https scheme conditions to 3 htaccess security rules and combine 2 rules into 1 rule.
		if ( preg_match( $pattern21, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern21, "RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)(http|https)(:/|/) [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern22, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern22, "RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern23, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern23, "RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]", $stringReplace);	
		}

		if ( preg_match( $pattern24, $stringReplace, $matches ) ) {			
			$stringReplace = preg_replace( $pattern24, "RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]", $stringReplace);
		}

		if ( preg_match($pattern1, $stringReplace, $matches) ) {
			$stringReplace = str_replace("RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>).* [NC,OR]", "RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>).* [NC,OR]", $stringReplace);		
		}

			file_put_contents($filename, $stringReplace);
		
		if ( getBPSInstallTime() == getBPSwpadminHtaccessLasModTime_minutes() || getBPSInstallTime_plusone() == getBPSwpadminHtaccessLasModTime_minutes() ) {
			//print("Testing wp-admin auto-update");	
			$bps_wpadmin_upgrade = 'upgrade';
		} // end upgrade processing		
		break;		
	case strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ):		
		
		$bps_status_display = get_option('bulletproof_security_options_status_display');

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' ) {		

			if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

				$WBM = $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#RBM-Status' ).'" title="wp-admin Folder BulletProof Mode" style="text-decoration:none;">'.__('WBM', 'bulletproof-security').'</a>: <font color="green"><strong>'.__('On', 'bulletproof-security').'</strong></font>';
				$WBM_str = str_replace( "BULLETPROOF $bps_version WP-ADMIN SECURE .HTACCESS", "$WBM", $section );			
			
				echo '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">'.$WBM_str.'</div>';
			}
		}
		break;
	default:
		
		if ( $bps_wpadmin_upgrade != 'upgrade' ) {		
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! A valid BPS htaccess file was NOT found in your wp-admin folder', 'bulletproof-security').'</font><br>'.__('BulletProof Mode for the wp-admin folder should also be activated when you have BulletProof Mode activated for the Root folder.', 'bulletproof-security').'</div>';
		echo $text;
		}
	}
	}
	}
	}
	}
}

// MScan Status display BPS pages only
// Displays the question mark hover icon if a scan has not been run or the Delete Scan Status Tool has been used.
// Displays last scan timestamp when scheduled scans are Off > MSCAN: August 3, 2017 8:45 am
// BPS Pro only (code removed): Displays next scheduled cron job when scheduled scan frequency is used > MSCAN: On : 60 Min : 9:30 am
function bpsProMScanStatus() {

	if ( current_user_can('manage_options') ) {
		global $aitpro_bullet;

		if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {
	
		$bps_status_display = get_option('bulletproof_security_options_status_display');
	
		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
			return;
		}
	
			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {	
		
				// New BPS installation - do not display status
				if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
					return;
				}

				$MScan_status = get_option('bulletproof_security_options_MScan_status');

				?>
				
				<style>
				<!--
				div.mscan-tooltip {display:inline-block;position:relative;}
				div.mscan-tooltip:hover {z-index:10;}
				div.mscan-tooltip img:hover {z-index:10;}
				div.mscan-tooltip span {display:none;position:absolute;bottom:0;left:0;right:0;}
				div.mscan-tooltip:hover span {width:500px;height:60px;display:block;position:absolute;top:30px;left:5px;right:0;color:#000;background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow:3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow:3px 3px 5px -1px rgba(153,153,153,0.7);}
				-->
				</style>
				
				<?php	
				$bps_question_mark_mscan = '<div class="mscan-tooltip"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>An MScan scan has not been run yet.</span></div>';

				if ( ! isset($MScan_status['bps_mscan_status']) || ! isset($MScan_status['bps_mscan_last_scan_timestamp']) ) {	
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:-2px 0px 0px 0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" title="MScan Malware Scanner" style="text-decoration:none;">'.esc_attr__('MSCAN', 'bulletproof-security').'</a>: '.$bps_question_mark_mscan.'</div>';					
					echo $text;
					return;
				}
			
				$MScan_options = get_option('bulletproof_security_options_MScan');
			
				if ( $MScan_options['mscan_scan_frequency'] == 'Off' ) {
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'" title="MScan Malware Scanner" style="text-decoration:none;">'.esc_attr__('MSCAN', 'bulletproof-security').'</a>: <font color="green"><strong>'.$MScan_status['bps_mscan_last_scan_timestamp'].'</strong></font></div>';
					echo $text;
			
				}
			}
		}
	}
}

// DB Backup Status display BPS pages only
// First time installations and upgrades the DB option bps_db_backup_status_display has value "No DB Backups"
// When a Backup Job is created for the first time the value is "Backup Job Created" - one time/one-shot option
// All DB Backup options are automatically created and saved for new installations and upgrades
function bpsProDBBStatus() {

	if ( current_user_can('manage_options') ) {
	
		global $aitpro_bullet;
	
		if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {

			$bps_status_display = get_option('bulletproof_security_options_status_display');

			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
				return;
			}

			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {	
	
				// New BPS installation - do not display status
				if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
					return;
				}

				$DBBoptions = get_option('bulletproof_security_options_db_backup');

			?>

			<style>
            <!--
            div.dbb-status-tooltip {display:inline-block;position:relative;}
            div.dbb-status-tooltip:hover {z-index:10;}
            div.dbb-status-tooltip img:hover {z-index:10;}
            div.dbb-status-tooltip span {display:none;position:absolute;bottom:0;left:0;right:0;}
            div.dbb-status-tooltip:hover span {width:500px;height:60px;display:block;position:absolute;top:30px;left:5px;right:0;color:#000;background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow:3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow:3px 3px 5px -1px rgba(153,153,153,0.7);}
            -->
            </style>

			<?php
			$bps_qm_dbb1 = '<div class="dbb-status-tooltip"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>A BPS DB Backup has not been performed yet. To run a DB Backup go to the BPS DB Backup page, create a Backup Job and run the Backup Job or you can just ignore this hover tooltip and not perform a DB Backup.</span></div>';
			
			$bps_qm_dbb2 = '<div class="dbb-status-tooltip"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>A BPS DB Backup Job has been created. To run a DB Backup go to the BPS DB Backup page and run the Backup Job or you can just ignore this hover tooltip and not perform a DB Backup.</span></div>';

			if ( $DBBoptions['bps_db_backup_status_display'] == 'No DB Backups' || $DBBoptions['bps_db_backup_status_display'] == '' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:-2px 0px 0px 0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" title="Database Backup" style="text-decoration:none;">'.esc_attr__('DBB', 'bulletproof-security').'</a>: '.$bps_qm_dbb1.'</div>';
				echo $text;
	
			} elseif ( $DBBoptions['bps_db_backup_status_display'] == 'Backup Job Created' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:-2px 0px 0px 0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" title="Database Backup" style="text-decoration:none;">'.esc_attr__('DBB', 'bulletproof-security').'</a>: '.$bps_qm_dbb2.'</div>';
				echo $text;		
	
			} else {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/db-backup-security/db-backup-security.php' ).'" title="Database Backup" style="text-decoration:none;">'.esc_attr__('DBB', 'bulletproof-security').'</a>: <font color="green"><strong>'.$DBBoptions['bps_db_backup_status_display'].'</strong></font></div>';
				echo $text;
			}
		}
	}
	}
}

// Login Security Status display - BPS pages ONLY
function bps_Login_Security_admin_notice_status_bps() {
global $aitpro_bullet;
	
	if ( current_user_can('manage_options') ) {
	
		if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {
	
			$bps_status_display = get_option('bulletproof_security_options_status_display');
		
			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
				return;
			}
	
			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {
	
				// New BPS installation - do not display status
				if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
					return;
				}
	
				$BPSoptions = get_option('bulletproof_security_options_login_security');	
	
				if ( $BPSoptions['bps_login_security_OnOff'] == 'On' ) {
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" title="Login Security & Monitoring" style="text-decoration:none;">'.esc_attr__('LSM', 'bulletproof-security').'</a>: <font color="green">'.__('On', 'bulletproof-security').'</font></div>';
					echo $text;
				}
	
				if ( ! $BPSoptions['bps_login_security_OnOff'] || $BPSoptions['bps_login_security_OnOff'] == 'Off' || $BPSoptions['bps_login_security_OnOff'] == '' || $BPSoptions['bps_login_security_OnOff'] == 'pwreset' ) {
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" title="Login Security & Monitoring" style="text-decoration:none;">'.__('LSM', 'bulletproof-security').'</a>: <font color="#fb0101">'.__('Off', 'bulletproof-security').'</font></div>';
					echo $text;
				}
			}
		}
	}
}

// JTC-Lite Status display - BPS pages ONLY
function bps_jtc_antispam_admin_notice_status_bps() {
global $aitpro_bullet;
	
	if ( current_user_can('manage_options') ) {
	
		if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {
	
			$bps_status_display = get_option('bulletproof_security_options_status_display');
		
			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
				return;
			}
	
			if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {
	
				// New BPS installation - do not display status
				if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
					return;
				}

				$BPSoptionsJTC = get_option('bulletproof_security_options_login_security_jtc');

				if ( ! get_option('bulletproof_security_options_idle_session') && ! get_option('bulletproof_security_options_auth_cookie') ) {				
					$status_DDiv = '</div><div style="clear:both;"></div>';
				} else {
					$status_DDiv = '</div>';
				}

				if ( $BPSoptionsJTC['bps_jtc_login_form'] == '1' ) {
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="JTC-Lite" style="text-decoration:none;">'.esc_attr__('JTC', 'bulletproof-security').'</a>: <font color="green">'.__('On', 'bulletproof-security').'</font>'.$status_DDiv;
					echo $text;
				} 
			
				if ( $BPSoptionsJTC['bps_jtc_login_form'] != '1' ) {
					$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">' . $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="'.esc_attr( 'JTC-Lite' ).'" style="text-decoration:none;">'.__('JTC', 'bulletproof-security').'</a>: <font color="#fb0101">'.__('Off', 'bulletproof-security').'</font>'.$status_DDiv;
					echo $text;
				}
			}
		}
	}
}

// Idle Session Logout ISL Status display - BPS pages ONLY
function bpsPro_isl_notice_status_bps() {
global $aitpro_bullet;
	
	if ( current_user_can('manage_options') ) {
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {

	$bps_status_display = get_option('bulletproof_security_options_status_display');

	if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
		return;
	}

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

			// New BPS installation - do not display status
			if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
				return;
			}

			if ( ! get_option('bulletproof_security_options_idle_session') ) {				
				return;				
			}

			$BPSoptionsISL = get_option('bulletproof_security_options_idle_session');	
	
			if ( ! get_option('bulletproof_security_options_auth_cookie') ) {				
				$status_DDiv = '</div><div style="clear:both;"></div>';
			} else {
				$status_DDiv = '</div>';	
			}

			if ( $BPSoptionsISL['bps_isl'] == 'On' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">'. $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="'.esc_attr( 'Idle Session Logout' ).'" style="text-decoration:none;">'.__('ISL', 'bulletproof-security').'</a>: <font color="green"><strong>'.__('On', 'bulletproof-security').'</strong></font>'.$status_DDiv;
				echo $text;
			} 

			if ( $BPSoptionsISL['bps_isl'] == 'Off' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">'. $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="'.esc_attr( 'Idle Session Logout' ).'" style="text-decoration:none;">'.__('ISL', 'bulletproof-security').'</a>: <font color="#fb0101"><strong>'.__('Off', 'bulletproof-security').'</strong></font>'.$status_DDiv;
				echo $text;
			}			
		}
	}
	}
}

// Auth Cookie Expiration ACE Status display - BPS pages ONLY
function bpsPro_ace_notice_status_bps() {
global $aitpro_bullet;
	
	if ( current_user_can('manage_options') ) {
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) != 'POST' && esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/system-info/system-info.php' ) {

	$bps_status_display = get_option('bulletproof_security_options_status_display');

	if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] == 'Off' ) {
		return;
	}

		if ( isset($bps_status_display['bps_status_display']) && $bps_status_display['bps_status_display'] != 'Off' && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

			// New BPS installation - do not display status
			if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
				return;
			}

			if ( ! get_option('bulletproof_security_options_auth_cookie') ) {				
				return;				
			}
			
			$BPSoptionsACE = get_option('bulletproof_security_options_auth_cookie');				
			$status_DDiv = '</div><div style="clear:both;"></div>';

			if ( $BPSoptionsACE['bps_ace'] == 'On' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">'. $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="'.esc_attr( 'Auth Cookie Expiration' ).'" style="text-decoration:none;">'.__('ACE', 'bulletproof-security').'</a>: <font color="green"><strong>'.__('On', 'bulletproof-security').'</strong></font>'.$status_DDiv;
				echo $text;
			} 

			if ( $BPSoptionsACE['bps_ace'] == 'Off' ) {
		
				$text = '<div id="bps-status-display" style="float:left;font-weight:600;margin:0px;">'. $aitpro_bullet . '<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'" title="'.esc_attr( 'Auth Cookie Expiration' ).'" style="text-decoration:none;">'.__('ACE', 'bulletproof-security').'</a>: <font color="#fb0101"><strong>'.__('Off', 'bulletproof-security').'</strong></font>'.$status_DDiv;
				echo $text;
			}			
		}
	}
	}
}

// GET HTTP Status Response from /mod-test/ images to determine which Apache Modules are Loaded, 
// Directive Backward Compatibility & if Host is allowing/processing IfModule conditions (Known Hosts: HostGator).
// System Info page updates the DB option on page load in real-time, but does not create htaccess files. 
// htaccess Core updates/creates the DB option and creates htaccess files if needed inpage on page load based on timestamp: once per 15 minute time restriction.
// BPS plugin upgrades & Pre-Installation Wizard checks: new htaccess files created if needed.
// bpsPro_apache_mod_create_htaccess_files() executed in this function which creates new htaccess files if needed.
// .52.6: fallback to mod_access_compat. see .53.6
// .53: The Setup Wizard no longer has a time restriction so that it can create new htaccess files on each page load if
// htaccess files do not already exist or new htaccess files need to be created.
// .53.6: Fubar condition added for servers that do not have either mod_access_compat and mod_authz_core or mod_rewrite Loaded.
// A user can override this check by enabling Enable|Disable htaccess Files: htaccess Files Enabled in the Setup Wizard.
// If an override is chosen then the fallback used is mod_access_compat.
// 4.5: Using IfModule conditions for all tests. No longer checking for No IfModule conditions. All htaccess code now contains IfModule condtions.
function bpsPro_apache_mod_directive_check() {
	
	if ( current_user_can('manage_options') ) {

		if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/system-info/system-info.php' ) {

			// 2: 403: mod_access_compat Module IS loaded. "Deny from all". Allows "Order, Deny, Allow" directives
			$url2 = plugins_url( '/bulletproof-security/admin/mod-test/mod_access_compat-od-denied.png' );
			// 3: 403: mod_authz_core Module IS loaded. "Require all denied" Conditional
			$url3 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_core-denied.png' );
			// 4: 403: mod_authz_core|mod_access_compat Order Directive Denied Conditional
			$url4 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_core-od-cond-denied.png' );
			// 5: 403: mod_authz_host Module IS loaded. "Require ip 127.9.9.1" Conditional
			$url5 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_host-require-ip.png' );	
			// 6: 403: mod_authz_host|mod_access_compat Order Directive Denied Conditional
			$url6 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_host-od-cond-denied.png' );
			// 9: 302 or 200: mod_rewrite Module IS loaded.
			// 9: 500 error if mod_rewrite Module is not loaded.
			$url9 = plugins_url( '/bulletproof-security/admin/mod-test/mod_rewrite-nc.png' );
			// 12: mod_security: 403 if mod_security IS loaded.
			$url12 = plugins_url( '/bulletproof-security/admin/mod-test/mod_security.png' );
			// 13: mod_security2: 403 if mod_security2 IS loaded.
			$url13 = plugins_url( '/bulletproof-security/admin/mod-test/mod_security-2.png' );			

			$url_array = array( $url2, $url3, $url4, $url5, $url6, $url9, $url12, $url13 );
	
			echo '<strong><span class="sysinfo-label-text">'.__('Apache Modules|Directives|Backward Compatibility(Yes|No)|IfModule(Yes|No): ', 'bulletproof-security').'</span></strong><br>';

			foreach ( $url_array as $key => $value ) {
		
				$response = wp_remote_get( $value );
	
				if ( ! is_wp_error( $response ) ) {	

					if ( $key == 0 ) { // 2
						$status_code2 = $response['response']['code'];
					}
		
					if ( $key == 1 ) { // 3
						$status_code3 = $response['response']['code'];
					}

					if ( $key == 2 ) { // 4
						$status_code4 = $response['response']['code'];
					}

					if ( $key == 3 ) { // 5
						$status_code5 = $response['response']['code'];			
					}		
		
					if ( $key == 4 ) { // 6
						$status_code6 = $response['response']['code'];
					}
		
					if ( $key == 5 ) { // 9
						$status_code9 = $response['response']['code'];
					}

					if ( $key == 6 ) { // 12
						$status_code12 = $response['response']['code'];
					}

					if ( $key == 7 ) { // 13
						$status_code13 = $response['response']['code'];
					}

				} else {
		
					$text = '<font color="#fb0101"><strong>'.__('ERROR: wp_remote_get() function is blocked or unable to get the URL path', 'bulletproof-security').'</strong></font><br>';
					echo $text;;
				}
			}
			
			// Fubar: Server does not have necessary Modules loaded to use htaccess files
			// if $status_code2, 3 and 4 are not 403 errors then neither mod_access_compat or mod_authz_core are loaded.
			// if $status_code9 is a 500 error then mod_rewrite is not loaded.
			/*
			if ( 403 != $status_code2 && 403 != $status_code3 && 403 != $status_code4 || 500 == $status_code9 ) {
				
				$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
				
				if ( $HFiles_options['bps_htaccess_files'] == 'enabled' ) {
					$apache_ifmodule = 'Yes';
				} else {
					$apache_ifmodule = 'fubar';
				}
				
				$text = '<font color="#fb0101"><strong>'.$status_code2.':'.$status_code3.':'.$status_code4.':'.$status_code9.':</strong></font> '.__('mod_access_compat and mod_authz_core or mod_rewrite are not Loaded', 'bulletproof-security').'<br>';
				echo $text;
			
			} else {
				*/
				
				// mod_access_compat loaded, Order, Allow, Deny directives are supported
				if ( 403 == $status_code2 ) {

					$apache_ifmodule = 'Yes';
		
					$text = '<font color="green"><strong>'.$status_code2.':</strong></font> '.__('mod_access_compat is Loaded|Order, Allow, Deny directives are supported|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;				
			
				// mod_access_compat is not loaded|available.
				} elseif ( 403 != $status_code2 ) {
				
					$apache_ifmodule = 'Yes';

					$text = '<font color="#2ea2cc"><strong>'.$status_code2.':</strong></font> '.__('mod_access_compat is not Loaded|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;
				}

				// mod_authz_core loaded, IfModule condition working, Order, Allow, Deny directives are supported
				// 3 normal mod_authz_core test| 4 is mod_access_compat	BC
				if ( 403 == $status_code3 && 403 == $status_code4 ) {
				
					$text = '<font color="green"><strong>'.$status_code3.':</strong></font> '.__('mod_authz_core is Loaded|Order, Allow, Deny directives are supported|BC: Yes|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;
				
				} elseif ( 403 == $status_code3 && 403 != $status_code4 ) {
			
					$text = '<font color="green"><strong>'.$status_code3.':</strong></font> '.__('mod_authz_core is Loaded|Order, Allow, Deny directives are not supported|BC: No|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;		
			
				} elseif ( 403 != $status_code3 && 403 != $status_code4 ) {
				
					$text = '<font color="#2ea2cc"><strong>'.$status_code3.':</strong></font> '.__('mod_authz_core is not Loaded|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;	
				}


				// mod_authz_host loaded, IfModule condition working, Order, Allow, Deny directives are supported
				// 5 normal mod_authz_core test| 6 is mod_access_compat	BC			
				if ( 403 == $status_code5 && 403 == $status_code6 ) {
				
					$text = '<font color="green"><strong>'.$status_code5.':</strong></font> '.__('mod_authz_host is Loaded|Order, Allow, Deny directives are supported|BC: Yes|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;
				
				} elseif ( 403 == $status_code5 && 403 != $status_code6 ) {
			
					$text = '<font color="green"><strong>'.$status_code5.':</strong></font> '.__('mod_authz_host is Loaded|Order, Allow, Deny directives are not supported|BC: No|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;		
			
				} elseif ( 403 != $status_code5 && 403 != $status_code6 ) {
				
					$text = '<font color="#2ea2cc"><strong>'.$status_code6.':</strong></font> '.__('mod_authz_host is not Loaded|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;	
				}

				// mod_rewrite Module loaded.
				if ( 301 == $status_code9 || 302 == $status_code9 || 200 == $status_code9 || 404 == $status_code9 || 403 == $status_code9 ) {
				
					$text = '<font color="green"><strong>'.$status_code9.':</strong></font> '.__('mod_rewrite Module is Loaded|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;
			
				} else {
				
					$text = '<font color="#2ea2cc"><strong>'.$status_code9.':</strong></font> '.__('mod_rewrite Inconclusive: Status is not 200, 301, 302, 403 or 404', 'bulletproof-security').'<br>';
					echo $text;				
				}
			//} // End: Fubar condition
			
			$apache_modules_Options = array(
			'bps_apache_mod_ifmodule' 	=> $apache_ifmodule, 
			'bps_apache_mod_time' 		=> time() + 900 
			);

			foreach( $apache_modules_Options as $key => $value ) {
				update_option('bulletproof_security_options_apache_modules', $apache_modules_Options);
			}	
		
			if ( $apache_ifmodule == 'fubar' ) {
				
				$htaccess_files_Options = array(
				'bps_htaccess_files' 	=> 'disabled'
				);

				foreach( $htaccess_files_Options as $key => $value ) {
					update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
				}			
			}

			## 15.3: BugFix
			if ( $apache_ifmodule == 'Yes' ) {
			
				$htaccess_files_Options = array(
				'bps_htaccess_files' 	=> 'enabled'
				);

				foreach( $htaccess_files_Options as $key => $value ) {
					update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
				}			
			}

			// mod_security or mod_security2 Module loaded.
			if ( 403 == $status_code12 || 403 == $status_code13 ) {
				
				if ( 403 == $status_code12 ) {
					$text = '<font color="#2ea2cc"><strong>'.$status_code12.':</strong></font> '.__('mod_security Module is Loaded|Enabled|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;
				} elseif ( 403 == $status_code13 ) {
					$text = '<font color="#2ea2cc"><strong>'.$status_code13.':</strong></font> '.__('mod_security2 Module is Loaded|Enabled|IfModule: Yes', 'bulletproof-security').'<br>';
					echo $text;						
				}
				
				$bps_mod_security_options = array( 'bps_mod_security_check' => '1' );
				
				foreach( $bps_mod_security_options as $key => $value ) {
					update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
				}					

			} else {
				
				$text = '<font color="green"><strong>'.$status_code12.':</strong></font> '.__('mod_security Module is not Loaded|Enabled|IfModule: Yes', 'bulletproof-security').'<br>';
				echo $text;
				
				$bps_mod_security_options = array( 'bps_mod_security_check' => '0' );
				
				foreach( $bps_mod_security_options as $key => $value ) {
					update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
				}
			}

		// End: System Info page check
		// BEGIN: Setup Wizard, BPS Upgrade & Core Inpage check. Create/update db options and new htaccess files
		} else {

			// 2: 403: mod_access_compat Module IS loaded. "Deny from all". Allows "Order, Deny, Allow" directives
			$url2 = plugins_url( '/bulletproof-security/admin/mod-test/mod_access_compat-od-denied.png' );
			// 3: 403: mod_authz_core Module IS loaded. "Require all denied" Conditional
			$url3 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_core-denied.png' );
			// 4: 403: mod_authz_core|mod_access_compat Order Directive Denied Conditional
			$url4 = plugins_url( '/bulletproof-security/admin/mod-test/mod_authz_core-od-cond-denied.png' );
			// 9: 302 or 200: mod_rewrite Module IS loaded.
			// 9: 500 error if mod_rewrite Module is not loaded.
			$url9 = plugins_url( '/bulletproof-security/admin/mod-test/mod_rewrite-nc.png' );
			// 12: mod_security: 403 if mod_security IS loaded.
			$url12 = plugins_url( '/bulletproof-security/admin/mod-test/mod_security.png' );
			// 13: mod_security2: 403 if mod_security2 IS loaded.
			$url13 = plugins_url( '/bulletproof-security/admin/mod-test/mod_security-2.png' );	

			$url_array = array( $url2, $url3, $url4, $url9, $url12, $url13 );

			// Setup Wizard: No time restriction
			if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' ) {

				foreach ( $url_array as $key => $value ) {
		
					$response = wp_remote_get( $value );
	
					if ( ! is_wp_error( $response ) ) {	

						if ( $key == 0 ) { // 2
							$status_code2 = $response['response']['code'];
						}

						if ( $key == 1 ) { // 3
							$status_code3 = $response['response']['code'];
						}

						if ( $key == 2 ) { // 4
							$status_code4 = $response['response']['code'];
						}

						if ( $key == 3 ) { // 9
							$status_code9 = $response['response']['code'];
						}
						
						if ( $key == 4 ) { // 12
							$status_code12 = $response['response']['code'];
						}
	
						if ( $key == 5 ) { // 13
							$status_code13 = $response['response']['code'];
						}					
					}
				}
			
				// Fubar: Server does not have necessary Modules loaded to use htaccess files
				// if $status_code2, 3 and 4 are not 403 errors then neither mod_access_compat or mod_authz_core are loaded.
				// if $status_code9 is a 500 error then mod_rewrite is not loaded.
				/*
				if ( 403 != $status_code2 && 403 != $status_code3 && 403 != $status_code4 || 500 == $status_code9 ) {

					$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
				
					if ( $HFiles_options['bps_htaccess_files'] == 'enabled' ) {
						$apache_ifmodule = 'Yes';
					} else {
						$apache_ifmodule = 'fubar';
					}
				
				} else {
					*/

					// mod_access_compat loaded, IfModule condition working, Order, Allow, Deny directives are supported
					if ( 403 == $status_code2 ) {

						$apache_ifmodule = 'Yes';
			
					} else { 
		
						$apache_ifmodule = 'Yes';
					}
				//} // END: Fubar condition

				$apache_modules_Options = array(
				'bps_apache_mod_ifmodule' 	=> $apache_ifmodule, 
				'bps_apache_mod_time' 		=> time() + 900 
				);

				foreach( $apache_modules_Options as $key => $value ) {
					update_option('bulletproof_security_options_apache_modules', $apache_modules_Options);
				}		
		
				if ( $apache_ifmodule == 'fubar' ) {
				
					$htaccess_files_Options = array(
					'bps_htaccess_files' 	=> 'disabled'
					);

					foreach( $htaccess_files_Options as $key => $value ) {
						update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
					}			
				}

				## 15.3: BugFix
				if ( $apache_ifmodule == 'Yes' ) {
				
					$htaccess_files_Options = array(
					'bps_htaccess_files' 	=> 'enabled'
					);

					foreach( $htaccess_files_Options as $key => $value ) {
						update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
					}			
				}

				// mod_security or mod_security2 Module loaded.
				if ( 403 == $status_code12 || 403 == $status_code13 ) {
					
					$bps_mod_security_options = array( 'bps_mod_security_check' => '1' );
					
					foreach( $bps_mod_security_options as $key => $value ) {
						update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
					}					
	
				} else {
					
					$bps_mod_security_options = array( 'bps_mod_security_check' => '0' );
					
					foreach( $bps_mod_security_options as $key => $value ) {
						update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
					}
				}

				bpsPro_apache_mod_create_htaccess_files();				
			
			} else { // END: Setup Wizard no time restriction.
					// BEGIN: BPS upgrade & Core with Time restriction

				$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		
				// Note: if the db option does not exist yet it is created: time now is greater than nothing
				if ( time() < $Apache_Mod_options['bps_apache_mod_time'] ) {
					// do nothing
	
				} else {		 
		
					foreach ( $url_array as $key => $value ) {
		
						$response = wp_remote_get( $value );
	
						if ( ! is_wp_error( $response ) ) {	

							if ( $key == 0 ) { // 2
								$status_code2 = $response['response']['code'];
							}
	
							if ( $key == 1 ) { // 3
								$status_code3 = $response['response']['code'];
							}
	
							if ( $key == 2 ) { // 4
								$status_code4 = $response['response']['code'];
							}
	
							if ( $key == 3 ) { // 9
								$status_code9 = $response['response']['code'];
							}
							
							if ( $key == 4 ) { // 12
								$status_code12 = $response['response']['code'];
							}
		
							if ( $key == 5 ) { // 13
								$status_code13 = $response['response']['code'];
							}
						}
					}
			
					// Fubar: Server does not have necessary Modules loaded to use htaccess files
					// if $status_code2, 3 and 4 are not 403 errors then neither mod_access_compat or mod_authz_core are loaded.
					// if $status_code9 is a 500 error then mod_rewrite is not loaded.
					/*
					if ( 403 != $status_code2 && 403 != $status_code3 && 403 != $status_code4 || 500 == $status_code9 ) {
				
						$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
				
						if ( $HFiles_options['bps_htaccess_files'] == 'enabled' ) {
							$apache_ifmodule = 'Yes';
						} else {
							$apache_ifmodule = 'fubar';
						}
				
					} else {
						*/

						// mod_access_compat loaded, IfModule condition working, Order, Allow, Deny directives are supported
						if ( 403 == $status_code2 ) {

							$apache_ifmodule = 'Yes';
			
						} else { 
		
							$apache_ifmodule = 'Yes';
						}
					//} // END: Fubar condition

					$apache_modules_Options = array(
					'bps_apache_mod_ifmodule' 	=> $apache_ifmodule, 
					'bps_apache_mod_time' 		=> time() + 900 
					);

					foreach( $apache_modules_Options as $key => $value ) {
						update_option('bulletproof_security_options_apache_modules', $apache_modules_Options);
					}		
		
					if ( $apache_ifmodule == 'fubar' ) {
				
						$htaccess_files_Options = array(
						'bps_htaccess_files' 	=> 'disabled'
						);

						foreach( $htaccess_files_Options as $key => $value ) {
							update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
						}			
					}

					## 15.3: BugFix
					if ( $apache_ifmodule == 'Yes' ) {
					
						$htaccess_files_Options = array(
						'bps_htaccess_files' 	=> 'enabled'
						);
	
						foreach( $htaccess_files_Options as $key => $value ) {
							update_option('bulletproof_security_options_htaccess_files', $htaccess_files_Options);
						}			
					}
					
					// mod_security or mod_security2 Module loaded.
					if ( 403 == $status_code12 || 403 == $status_code13 ) {
						
						$bps_mod_security_options = array( 'bps_mod_security_check' => '1' );
						
						foreach( $bps_mod_security_options as $key => $value ) {
							update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
						}					
		
					} else {
						
						$bps_mod_security_options = array( 'bps_mod_security_check' => '0' );
						
						foreach( $bps_mod_security_options as $key => $value ) {
							update_option('bulletproof_security_options_mod_security', $bps_mod_security_options);
						}
					}				
					bpsPro_apache_mod_create_htaccess_files();
				} // end if ( time() < $Apache_Mod_options['bps_apache_mod_time'] ) {
			}
		}
	}
}


// Creates htaccess files based on bps_apache_mod_ifmodule DB value
// 11 htaccess files total
// .53.6: Fubar condition added for servers that do not have either mod_access_compat and mod_authz_core or mod_rewrite Loaded.
function bpsPro_apache_mod_create_htaccess_files() {

	if ( is_admin() && current_user_can('manage_options') ) {

		$denyall_htaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all.htaccess';
		$denyall_ifmodule_htaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all-ifmodule.htaccess';	

		$bps_backup = WP_CONTENT_DIR . '/bps-backup/.htaccess';
		$bps_master_backups = WP_CONTENT_DIR . '/bps-backup/master-backups/.htaccess';
		$bpsPro_pf = WP_PLUGIN_DIR . '/bulletproof-security/';
		$core1 = $bpsPro_pf  . 'admin/core/.htaccess';
		$core2 = $bpsPro_pf  . 'admin/db-backup-security/.htaccess';
		$core3 = $bpsPro_pf  . 'admin/htaccess/.htaccess';
		$core4 = $bpsPro_pf  . 'admin/login/.htaccess';
		$core5 = $bpsPro_pf . 'admin/maintenance/.htaccess';
		$core6 = $bpsPro_pf . 'admin/security-log/.htaccess';
		$core7 = $bpsPro_pf . 'admin/system-info/.htaccess';
		$core8 = $bpsPro_pf . 'admin/theme-skin/.htaccess';	
		$core9 = $bpsPro_pf . 'admin/wizard/.htaccess';	
		$core10 = $bpsPro_pf . 'admin/email-log-settings/.htaccess';	
		$core11 = $bpsPro_pf . 'admin/mscan/.htaccess';

		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');

		if ( isset($Zip_download_Options['bps_zip_download_fix']) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			$files = array( $bps_backup, $bps_master_backups, $core2, $core3, $core5, $core6, $core7, $core8, $core10, $core11 );		
		} else {
			$files = array( $bps_backup, $bps_master_backups, $core1, $core2, $core3, $core4, $core5, $core6, $core7, $core8, $core9, $core10, $core11 );
		}
	
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		
		// .53.6: htaccess Files Enabled|Disabled Override
		// If someone manually chooses Disable htaccess files then htaccess files will not be created.
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'enabled' ) {

			foreach ( $files as $file ) {

				if ( ! file_exists($file) ) {
					
					if ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
						@copy($denyall_ifmodule_htaccess, $file);
					} elseif ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) {
						@copy($denyall_htaccess, $file);
					}
				}
				
				if ( file_exists($file) ) {
					$check_string = @file_get_contents($file);
			
					if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' && ! strpos( $check_string, "BPS mod_authz_core IfModule BC" ) ) {
						@copy($denyall_ifmodule_htaccess, $file);
					} elseif ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' && ! strpos( $check_string, "BPS mod_access_compat" ) ) {
						@copy($denyall_htaccess, $file);
					}
				}
			}
		}
	}
}

// 4.5: Remove (unset) the WP Site Health "A scheduled event is late" error message for any BPS Cron jobs.
function bpsPro_filter_scheduled_events( $tests ) {

	if ( ! class_exists( 'WP_Site_Health' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
	}

	$get_test_scheduled_events = new WP_Site_Health();
	$test_description = $get_test_scheduled_events->get_test_scheduled_events();
	$pattern = '/(bpsPro_DBB_check|bpsPro_email_log_files|bpsPro_HPF_check|bpsPro_MScan_check)/';
	
	if ( preg_match( $pattern, $test_description['description'] ) ) {
		
		unset( $tests['direct']['scheduled_events'] );
	}
	
	return $tests;
}

add_filter( 'site_status_tests', 'bpsPro_filter_scheduled_events' );

?>