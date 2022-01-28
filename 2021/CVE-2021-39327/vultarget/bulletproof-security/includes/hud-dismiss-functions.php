<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// HUD Alerts in WP Dashboard
// Reset|Recheck Dismiss Notices is in core-forms.php
## 3.9: Commented out the Bonus Custom Code Dismiss Notice function and ModSecurity Check function.
function bps_HUD_WP_Dashboard() {
	
	if ( preg_match( '/page=stories-dashboard/', esc_html($_SERVER['QUERY_STRING']) ) || preg_match( '/page=backwpupbackups/', esc_html( $_SERVER['QUERY_STRING'] ) ) || preg_match( '/post_type=ai1ec_event/', esc_html( $_SERVER['QUERY_STRING'] ) ) ) {
		return;
	}

	if ( current_user_can('manage_options') ) { 
		bps_check_php_version_error();
		bps_check_safemode();
		bps_check_permalinks_error();
		bps_check_iis_supports_permalinks();
		bps_hud_check_bpsbackup();
		//bpsPro_bonus_custom_code_dismiss_notices();
		bps_hud_PhpiniHandlerCheck();
		bps_hud_check_sucuri();
		bps_hud_check_wordpress_firewall2();
		bps_hud_BPSQSE_old_code_check();
		bpsPro_BBM_htaccess_check();
		bpsPro_hud_speed_boost_cache_code();
		//bps_hud_check_autoupdate();
		//bpsPro_hud_mscan_notice();
		bpsPro_hud_jtc_lite_notice();
		bpsPro_hud_rate_notice();
		//bpsPro_hud_mod_security_check();
		bpsPro_hud_gdpr_compliance();
		//bps_hud_check_public_username();
		bpsPro_mu_wp_automatic_updates_notice();
		bpsPro_hud_new_feature_notice();
	}
}
add_action('admin_notices', 'bps_HUD_WP_Dashboard');

// Heads Up Display - Check PHP version - top error message new activations/installations
function bps_check_php_version_error() {
	
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) ) {
		return;
	}
	
	if ( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS requires at least PHP5 to function correctly. Your PHP version is: ', 'bulletproof-security').PHP_VERSION.'</font><br><a href="https://www.ait-pro.com/aitpro-blog/1166/bulletproof-security-plugin-support/bulletproof-security-plugin-guide-bps-version-45#bulletproof-security-issues-problems" target="_blank">'.__('BPS Guide - PHP5 Solution', 'bulletproof-security').'</a><br>'.__('The BPS Guide will open in a new browser window. You will not be directed away from your WordPress Dashboard.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display w/ Dismiss - Check if PHP Safe Mode is On - 1 is On - 0 is Off
function bps_check_safemode() {
	
	if ( ini_get('safe_mode') == 1 ) {
		
		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		
		
		if ( ! get_user_meta($user_id, 'bps_ignore_safemode_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that Safe Mode is set to On in your php.ini file.', 'bulletproof-security').'</font><br>'.__('If you see errors that BPS was unable to automatically create the backup folders this is probably the reason why.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_safemode_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_safemode_nag_ignore');

function bps_safemode_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_safemode_nag_ignore']) && '0' == $_GET['bps_safemode_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_safemode_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if Permalinks are enabled - top error message new activations/installations
function bps_check_permalinks_error() {

	if ( current_user_can('manage_options') && get_option('permalink_structure') == '' ) {

		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}	
	
		if ( ! get_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Custom Permalinks are NOT being used.', 'bulletproof-security').'</font><br>'.__('It is recommended that you use Custom Permalinks: ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2304/wordpress-tips-tricks-fixes/permalinks-wordpress-custom-permalinks-wordpress-best-wordpress-permalinks-structure/" target="_blank" title="Link opens in a new Browser window">'.__('How to setup Custom Permalinks', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_Permalinks_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bps_Permalinks_nag_ignore');

function bps_Permalinks_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_Permalinks_nag_ignore']) && '0' == $_GET['bps_Permalinks_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_Permalinks_notice', 'true', true);
	}
}

// Heads Up Display w/Dismiss - Check if Windows IIS server and if IIS7 supports permalink rewriting
function bps_check_iis_supports_permalinks() {
global $wp_rewrite, $is_IIS, $is_iis7, $current_user;
$user_id = $current_user->ID;	

	if ( current_user_can('manage_options') && $is_IIS && ! iis7_supports_permalinks() ) {
	if ( ! get_user_meta($user_id, 'bps_ignore_iis_notice')) {

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that your Server is a Windows IIS Server that does not support htaccess rewriting.', 'bulletproof-security').'</font><br>'.__('Do NOT activate BulletProof Modes unless you know what you are doing.', 'bulletproof-security').'<br>'.__('Your Server Type is: ', 'bulletproof-security').esc_html( $_SERVER['SERVER_SOFTWARE'] ).'<br><a href="http://codex.wordpress.org/Using_Permalinks" target="_blank" title="This link will open in a new browser window.">'.__('WordPress Codex - Using Permalinks - see IIS section', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_iis_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';		
		echo $text;
	}
	}
}

add_action('admin_init', 'bps_iis_nag_ignore');

function bps_iis_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_iis_nag_ignore'] ) && '0' == $_GET['bps_iis_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_iis_notice', 'true', true);
	}
}

// Heads Up Display - check if /bps-backup and /bps-backup/master-backups folders exist
function bps_hud_check_bpsbackup() {

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );	

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder manually via FTP. The folder permissions for the bps-backup folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
	
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder manually via FTP. The folder permissions for the master-backups folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
}

// Heads Up Display - Bonus Custom Code with Dismiss Notices
function bpsPro_bonus_custom_code_dismiss_notices() {
global $current_user;
$user_id = $current_user->ID;	
	
	if ( current_user_can('manage_options') ) { 
		$text = '';
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		return;
	}

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}
		
	if ( get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') && ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';


		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		echo $text;
		echo '</div>';
	}		
	
	if ( ! get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 		
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) { 	
		
		$text .= '<div id="BC1" style="">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Additional Protection for the Login Page from Brute Force Login Attacks" target="_blank">'.__('Brute Force Login Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_brute_force_login_protection_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') ) { 	

		$text .= '<div id="BC2" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Speed up your website performance with Browser Cache code" target="_blank">'.__('Speed Boost Cache Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_speed_boost_cache_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_author_enumeration_notice') ) { 

		$text .= '<div id="BC3" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-author-enumeration-bot-probe-protection-author-id-user-id/" title="Protects against hacker and spammer bots finding Author names & User names on your website" target="_blank">'.__('Author Enumeration BOT Probe Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_author_enumeration_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) { 		

		$text .= '<div id="BC4" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-xml-rpc-ddos-protection-protect-xmlrpc-php-block-xmlrpc-php-forbid-xmlrpc-php/" title="Protects against the XML Quadratic Blowup Attack, DDoS Attacks as well as other various XML-RPC exploits" target="_blank">'.__('XML-RPC DDoS Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_xmlrpc_ddos_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	
	/*
	if ( ! get_user_meta($user_id, 'bps_referer_spam_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/block-referer-spammers-semalt-kambasoft-ranksonic-buttons-for-website/" title="Protects against Referer Spamming and Phishing" target="_blank">'.__('Referer Spam|Phishing Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_referer_spam_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	*/
	
	if ( ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_sniff_driveby_notice') ) {		
		
		$text .= '<div id="BC6" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mime-sniffing-data-sniffing-content-sniffing-drive-by-download-attack-protection/" title="Protects against Mime Sniffing, Data Sniffing, Content Sniffing and Drive-by Download Attacks" target="_blank">'.__('Mime Sniffing|Drive-by Download Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_sniff_driveby_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

	if ( ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {		
		
		$text .= '<div id="BC7" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/rssing-com-good-or-bad/" title="Protects against external websites displaying your website pages or Feeds in iFrames and Clickjacking Protection" target="_blank">'.__('External iFrame|Clickjacking Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_iframe_clickjack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

		echo $text;
		
		if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 	
		echo '</div>';
		}
		}
	}
}

add_action('admin_init', 'bpsPro_bonus_custom_code_nag_ignores');

function bpsPro_bonus_custom_code_nag_ignores() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_bonus_code_dismiss_all_nag_ignore']) && '0' == $_GET['bps_bonus_code_dismiss_all_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice', 'true', true);
	}

	if ( isset($_GET['bps_brute_force_login_protection_nag_ignore']) && '0' == $_GET['bps_brute_force_login_protection_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_brute_force_login_protection_notice', 'true', true);
	}

	if ( isset($_GET['bps_speed_boost_cache_nag_ignore']) && '0' == $_GET['bps_speed_boost_cache_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_speed_boost_cache_notice', 'true', true);
	}

	if ( isset($_GET['bps_author_enumeration_nag_ignore']) && '0' == $_GET['bps_author_enumeration_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_author_enumeration_notice', 'true', true);
	}

	if ( isset($_GET['bps_xmlrpc_ddos_nag_ignore']) && '0' == $_GET['bps_xmlrpc_ddos_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_xmlrpc_ddos_notice', 'true', true);
	}

	/*
	if ( isset($_GET['bps_referer_spam_nag_ignore']) && '0' == $_GET['bps_referer_spam_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_referer_spam_notice', 'true', true);
	}
	*/
	
	if ( isset($_GET['bps_post_request_attack_nag_ignore']) && '0' == $_GET['bps_post_request_attack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_post_request_attack_notice', 'true', true);
	}

	if ( isset($_GET['bps_sniff_driveby_nag_ignore']) && '0' == $_GET['bps_sniff_driveby_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_sniff_driveby_notice', 'true', true);
	}

	if ( isset($_GET['bps_iframe_clickjack_nag_ignore']) && '0' == $_GET['bps_iframe_clickjack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_iframe_clickjack_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if php.ini handler code exists in root .htaccess file, but not in Custom Code
// .53.6: Additional conditional check added for Wordfence WAF Firewall mess.
function bps_hud_PhpiniHandlerCheck() {
global $current_user;
$user_id = $current_user->ID;
$file = ABSPATH . '.htaccess';
$pre_background_image_url = site_url( '/wp-content/plugins/bulletproof-security/admin/images/pre_bg.png' );

	if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
	
		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			$bps_customcode_one = ! isset($CustomCodeoptions['bps_customcode_one']) ? '' : $CustomCodeoptions['bps_customcode_one'];
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $bps_customcode_one, $DBmatches);

			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}			
			
			if ( stripos( $file_contents, "Wordfence WAF" ) ) {

				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem before running the Setup Wizard.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;

			} else {
				
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;			
				echo '<pre id="shown" style="overflow:auto;white-space:pre-wrap;height:65px;width:66%;margin:5px 0px 0px 2px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">';
				echo '# PHP/php.ini handler htaccess code<br>';				
				
				foreach ( $h_matches[0] as $Key => $Value ) {
					echo $Value;
				}
				echo '</pre>';
			}
			}
		}
	}

	if ( esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {

		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			$bps_customcode_one = ! isset($CustomCodeoptions['bps_customcode_one']) ? '' : $CustomCodeoptions['bps_customcode_one'];
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $bps_customcode_one, $DBmatches);
		
			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
			
				if ( stripos( $file_contents, "Wordfence WAF" ) ) {
					
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;				
				
				} else {

					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;			
					echo '<pre id="shown" style="overflow:auto;white-space:pre-wrap;height:65px;width:66%;margin:5px 0px 0px 2px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">';
					echo '# PHP/php.ini handler htaccess code<br>';				
				
					foreach ( $h_matches[0] as $Key => $Value ) {
						echo $Value;
					}
					echo '</pre>';
				}
			}
		}
	}
}

add_action('admin_init', 'bps_PhpiniHandler_nag_ignore');

function bps_PhpiniHandler_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_PhpiniHandler_nag_ignore'] ) && '0' == $_GET['bps_PhpiniHandler_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Sucuri Restrict wp-content access Hardening Option wp-content .htaccess file problem - breaks BPS and lots of other stuff
// Unfortunately the limited whitelisting options provided by Sucuri in their settings don't provide any workable solutions for BPS.
// Defender Security also does this retarded thing.
## 3.5: updated this check due to changes in the Sucuri wp-content htaccess file.
## 3.7: updated the error message to include Defender Security.
## 3.8: updated the error message to include older versions of iThemes Security. Newer versions of iThemes Security now create root htaccess code that does not break things.
function bps_hud_check_sucuri() {
$filename = WP_CONTENT_DIR . '/.htaccess';

	if ( ! file_exists($filename) ) {
		return;	
	}
	
	$file_contents = @file_get_contents($filename);
	
	if ( file_exists($filename) ) {
		
		if ( preg_match( '/(Require\sall\sdenied|Deny\sfrom\sall)/', $file_contents ) ) { 

			global $current_user;
			$user_id = $current_user->ID;
	
			if ( ! get_user_meta($user_id, 'bps_ignore_sucuri_notice') ) {
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
		
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('An htaccess file has been detected in the wp-content folder that breaks BPS features and functionality', 'bulletproof-security').'</font><br>'.__('If you have or had the Sucuri, Defender or iThemes Security plugins installed, they create a wp-content htaccess file that breaks several things in BPS Pro and other plugins as well.', 'bulletproof-security').'<br>'.__('To fix the Sucuri problem go to the Sucuri Settings page, click the Hardening tab and click the Revert Hardening button for the Block PHP Files in WP-CONTENT Directory option setting.', 'bulletproof-security').'<br>'.__('To fix the Defender Security problem go to the Security Tweaks page, click the PHP Execution option setting and click the Revert button.', 'bulletproof-security').'<br>'.__('o fix the iThemes problem go to the System Tweaks page, uncheck the Disable PHP in Plugins option setting.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_sucuri_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bps_sucuri_nag_ignore');

function bps_sucuri_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_sucuri_nag_ignore'] ) && '0' == $_GET['bps_sucuri_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_sucuri_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WordPress Firewall 2 plugin - breaks BPS and lots of other stuff
function bps_hud_check_wordpress_firewall2() {
$firewall2 = 'wordpress-firewall-2/wordpress-firewall-2.php';
$firewall2_active = in_array( $firewall2, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $firewall2_active != 1 && ! is_plugin_active_for_network( $firewall2 ) ) {
		return;	
	}
	
	if ( $firewall2_active == 1 || is_plugin_active_for_network( $firewall2 ) ) {
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('The WordPress Firewall 2 plugin is installed and activated', 'bulletproof-security').'</font><br>'.__('It is recommended that you delete the WordPress Firewall 2 plugin.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordpress-firewall-2-plugin-unable-to-save-custom-code/" target="_blank" title="Link opens in a new Browser window">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for more information.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_wpfirewall2_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_wpfirewall2_nag_ignore');

function bps_wpfirewall2_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_wpfirewall2_nag_ignore'] ) && '0' == $_GET['bps_wpfirewall2_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_wpfirewall2_notice', 'true', true);
	}
}

// Check for older BPS Query String Exploits code saved to BPS Custom Code
function bps_hud_BPSQSE_old_code_check() {
$CustomCodeoptions = get_option('bulletproof_security_options_customcode');	

	if ( isset($CustomCodeoptions['bps_customcode_bpsqse']) && $CustomCodeoptions['bps_customcode_bpsqse'] == '' ) {
		return;
	}
	
	$subject = ! isset($CustomCodeoptions['bps_customcode_bpsqse']) ? '' : $CustomCodeoptions['bps_customcode_bpsqse'];	
	$pattern1 = '/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/';
	$pattern2 = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern3 = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';

	if ( isset($CustomCodeoptions['bps_customcode_bpsqse']) && $CustomCodeoptions['bps_customcode_bpsqse'] != '' && preg_match($pattern1, $subject, $matches) || preg_match($pattern2, $subject, $matches) || preg_match($pattern3, $subject, $matches) ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Notice: BPS Query String Exploits Code Changes', 'bulletproof-security').'</font><br>'.__('Older BPS Query String Exploits code was found in BPS Custom Code. Several Query String Exploits rules were changed/added/modified in the root .htaccess file in BPS .49.6, .50.2 & .50.3.', 'bulletproof-security').'<br>'.__('Copy the new Query String Exploits section of code from your root .htaccess file and paste it into this BPS Custom Code text box: CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS and click the Save Root Custom Code button.', 'bulletproof-security').'<br>'.__('This Notice will go away once you have copied the new Query String Exploits code to BPS Custom Code and clicked the Save Root Custom Code button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display - Check if the /bps-backup/.htaccess file exists
function bpsPro_BBM_htaccess_check() {

	// New BPS installation - do not check or display error
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$options = get_option('bulletproof_security_options_monitor');
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

	if ( ! file_exists($filename) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' && ! isset($_POST['Submit-BBM-Activate']) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! A BPS htaccess file was NOT found in the BPS Backup folder: ', 'bulletproof-security').'/'.$bps_wpcontent_dir.'/bps-backup/</font><br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the BPS Backup Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

## Checks for older BPS Speed Boost Cache code saved in BPS Custom Code
## 2.0: Checks for redundant Browser caching code & the BPS NOCHECK Marker in BPS Custom Code
function bpsPro_hud_speed_boost_cache_code() {
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	$bps_customcode_one = ! isset($CC_options['bps_customcode_one']) ? '' : $CC_options['bps_customcode_one'];
	$bps_customcode_one_decode = htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES );	
	
	if ( isset($CC_options['bps_customcode_one']) && $CC_options['bps_customcode_one'] == '' || strpos( $bps_customcode_one_decode, "BPS NOCHECK" ) ) {
		return;
	}	
	
	if ( isset ( $_POST['bps_customcode_submit'] ) && $_POST['bps_customcode_submit'] == true ) {
		return;
	}

	global $current_user;
	$user_id = $current_user->ID;	
	
	$pattern1 = '/BEGIN\sWEBSITE\sSPEED\sBOOST/';
	$pattern2 = '/AddOutputFilterByType\sDEFLATE\stext\/plain\s*AddOutputFilterByType\sDEFLATE\stext\/html\s*AddOutputFilterByType\sDEFLATE\stext\/xml\s*AddOutputFilterByType\sDEFLATE\stext\/css\s*AddOutputFilterByType\sDEFLATE\sapplication\/xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/xhtml\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/rss\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-php\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-fastphp\s*AddOutputFilterByType\sDEFLATE\simage\/svg\+xml/';

	if ( ! get_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) { 
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		

		if ( preg_match( $pattern1, htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES ), $matches1 ) && preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES ), $matches2 ) ) {

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</font><br>'.__('Older BPS Speed Boost Cache Code was found saved in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'.<br>'.__('Newer improved BPS Speed Boost Cache Code has been created, which should improve website load speed performance even more.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" target="_blank" title="BPS Speed Boost Cache Code">'.__('Get The New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</a>'.__('. To dismiss this Notice click the Dismiss Notice button below.', 'bulletproof-security').'<br>'.__('To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_hud_speed_boost_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}

		if ( strpos( $bps_customcode_one, "WEBSITE SPEED BOOST" ) ) {
			if ( strpos( $bps_customcode_one, "WPSuperCache" ) || strpos( $bps_customcode_one, "W3TC Browser Cache" ) || strpos( $bps_customcode_one, "Comet Cache" ) || strpos( $bps_customcode_one, "GzipWpFastestCache" ) || strpos( $bps_customcode_one, "LBCWpFastestCache" ) || strpos( $bps_customcode_one, "WP Rocket" ) ) {
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Speed Boost Cache Custom Code Notice', 'bulletproof-security').'</font><br>'.__('BPS Speed Boost Cache Code was found in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'<br>'.__('and another caching plugin\'s Marker text was also found in this BPS Custom Code text box.', 'bulletproof-security').'<br>'.__('Click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-speed-boost-cache-custom-code-notice/" target="_blank" title="BPS SBC Custom Code Forum Topic">'.__('BPS Speed Boost Cache Custom Code Notice Forum Topic', 'bulletproof-security').'</a>'.__(' for help information on what this Notice means and what to do next.', 'bulletproof-security').'</div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bpsPro_hud_speed_boost_nag_ignore');

function bpsPro_hud_speed_boost_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_hud_speed_boost_nag_ignore']) && '0' == $_GET['bpsPro_hud_speed_boost_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - JTC-Lite New Feature Dismiss Notice
function bpsPro_hud_jtc_lite_notice() {

	$jtc_options = get_option('bulletproof_security_options_login_security_jtc');

	if ( isset($jtc_options['bps_jtc_login_form']) && $jtc_options['bps_jtc_login_form'] == '0' ) {
		
		global $current_user;
		$user_id = $current_user->ID;
	
		if ( ! get_user_meta($user_id, 'bps_ignore_jtc_lite_notice') ) {
				
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}

			$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin:0px 0px 35px 0px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS New Feature Notice: JTC-Lite', 'bulletproof-security').'</font><br>'.__('JTC-Lite protects the WordPress Login page Form against automated SpamBot and HackerBot Brute Force Login attacks', 'bulletproof-security').'<br>'.__('and also prevents User Accounts from being locked repeatedly by Brute Force Login Bot attacks on your Login page Form.', 'bulletproof-security').'<br>'.__('To enable/turn On JTC-Lite, click this ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'">'.esc_attr__('JTC-Lite link', 'bulletproof-security').'</a>'.__('. Click/check the Login Form Checkbox and click the Save Options button.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_jtc_lite_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_jtc_lite_nag_ignore');

function bpsPro_jtc_lite_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_jtc_lite_nag_ignore']) && '0' == $_GET['bpsPro_jtc_lite_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_jtc_lite_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - BPS plugin 30 day review/rating request Dismiss Notice
function bpsPro_hud_rate_notice() {

	global $current_user, $pagenow;
	$user_id = $current_user->ID;

	if ( ! get_option('bulletproof_security_options_rate_free') ) {
		return;
	}

	$options = get_option('bulletproof_security_options_rate_free');

	if ( time() >= $options['bps_free_rate_review'] ) {

		if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches) || 'update-core.php' == $pagenow || 'plugins.php' == $pagenow ) {
	
			if ( ! get_user_meta($user_id, 'bps_ignore_rate_notice') ) {
					
				if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
					$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
				} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
					$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
				} else {
					$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
				}

				$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin:0px 0px 35px 0px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Plugin Star Rating Request', 'bulletproof-security').'</font><br>'.__('A BPS star rating only takes a couple of minutes and would be very much appreciated. We are looking for 5 star ratings and not "fancy" reviews.', 'bulletproof-security').'<br>'.__('A simple review like "works great" or "has been protecting my website for X months or X years" is perfect.', 'bulletproof-security').'<br>'.__('Click this link to submit a BPS Plugin Star Rating: ', 'bulletproof-security').'<a href="https://wordpress.org/support/plugin/bulletproof-security/reviews/#postform" target="_blank" title="BPS Plugin Star Rating">'.__('BPS Plugin Star Rating', 'bulletproof-security').'</a>, '.__('login to the WordPress.org site and scroll to the bottom of the Reviews page.', 'bulletproof-security').'<br>'.__('To Dismiss this one-time Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_rate_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;
			}
		}
	}
}

add_action('admin_init', 'bpsPro_rate_nag_ignore');

function bpsPro_rate_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_rate_nag_ignore']) && '0' == $_GET['bpsPro_rate_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_rate_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice - Check if Mod Security is Loaded|Enabled. Displays a link to a help forum topic.
function bpsPro_hud_mod_security_check() {
	
	$bps_mod_security_options = get_option('bulletproof_security_options_mod_security');

	if ( $bps_mod_security_options['bps_mod_security_check'] == '1' ) {

		global $current_user;
		$user_id = $current_user->ID;		

		if ( ! get_user_meta($user_id, 'bpsPro_ignore_mod_security_notice')) { 
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Notice: Mod Security Module is Loaded|Enabled', 'bulletproof-security').'</font><br>'.__('Please take a minute to view this Mod Security help forum topic: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mod-security-common-known-problems/" target="_blank" title="Mod Security Common Known Problems">'.__('Mod Security Common Known Problems', 'bulletproof-security').'</a>.<br>'.__('If you are not experiencing any of the problems listed in the Mod Security help forum topic then you can dismiss this Dismiss Notice.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_mod_security_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_mod_security_nag_ignore');

function bpsPro_mod_security_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_mod_security_nag_ignore']) && '0' == $_GET['bpsPro_mod_security_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_mod_security_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice - GDPR Compliance Dismiss Notice. Displays a link to a help forum topic.
function bpsPro_hud_gdpr_compliance() {
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	global $current_user;
	$user_id = $current_user->ID;		
	
	if ( ! get_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice')) { 
	
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS GDPR Compliance Notice', 'bulletproof-security').'</font><br>'.__('A new Setup Wizard Option has been created which allows you to turn off all IP address logging in BPS to make your website GDPR Compliant.', 'bulletproof-security').'<br>'.__('Click this ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'">'.__('GDPR Compliance Setup Wizard Option link', 'bulletproof-security').'</a>. '.__('Choose the GDPR Compliance On setting.', 'bulletproof-security').'<br>'.__('For more information about GDPR Compliance click this ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-gdpr-compliance/" target="_blank" title="GDPR Compliance">'.__('GDPR Compliance Forum Topic link', 'bulletproof-security').'</a>.<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_gdpr_compliance_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
		echo $text;
	}
}

add_action('admin_init', 'bpsPro_gdpr_compliance_nag_ignore');

function bpsPro_gdpr_compliance_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_gdpr_compliance_nag_ignore']) && '0' == $_GET['bpsPro_gdpr_compliance_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice: If someone has enabled any of the BPS Pro MU Tools WP Automatic Update options check the wp-config.php file for redundant constants.
function bpsPro_mu_wp_automatic_updates_notice() {
	
	if ( ! get_option('bulletproof_security_options_mu_wp_autoupdate') ) {
		return;
	}
	
	$wpconfig_file = ABSPATH . 'wp-config.php';
	
	// If someone has moved their wp-config.php file exit.
	if ( ! file_exists( $wpconfig_file ) ) {
		return;
	}

	if ( file_exists($wpconfig_file) ) {
		
		$file_contents = @file_get_contents($wpconfig_file);
		$wp_auto_update_options = get_option('bulletproof_security_options_mu_wp_autoupdate');
		
		if ( $wp_auto_update_options['bps_automatic_updater_disabled'] == 'enabled' || $wp_auto_update_options['bps_auto_update_core_updates_disabled'] == 'enabled' || $wp_auto_update_options['bps_auto_update_core'] == 'enabled' || $wp_auto_update_options['bps_allow_dev_auto_core_updates'] == 'enabled' || $wp_auto_update_options['bps_allow_minor_auto_core_updates'] == 'enabled' || $wp_auto_update_options['bps_allow_major_auto_core_updates'] == 'enabled' ) {
		
			if ( preg_match( '/(WP_AUTO_UPDATE_CORE|AUTOMATIC_UPDATER_DISABLED)/', $file_contents ) ) {
	
				global $current_user;
				$user_id = $current_user->ID;		
			
				if ( ! get_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice') ) {
			
					if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
						$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
					} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
						$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
					} else {
						$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
					}
			
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS wp-config.php file WP Automatic Update constants detected', 'bulletproof-security').'</font><br>'.__('You are using the BPS MU Tools plugin option settings to handle WP Automatic Updates. BPS detected that you are also using one or both of these WP Automatic Update constants in your wp-config.php file: WP_AUTO_UPDATE_CORE and/or AUTOMATIC_UPDATER_DISABLED. Either comment out these constants in your wp-config.php file or delete these constants. If you choose to comment out these constants instead of deleting them then dismiss this Dismiss Notice after you have commented them out.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_mu_wp_automatic_updates_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;
				}
			}
		}
	}
}

add_action('admin_init', 'bpsPro_mu_wp_automatic_updates_nag_ignore');

function bpsPro_mu_wp_automatic_updates_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_mu_wp_automatic_updates_nag_ignore']) && '0' == $_GET['bpsPro_mu_wp_automatic_updates_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - New feature or option Dismiss Notice - Used for very important new features and very rarely.
function bpsPro_hud_new_feature_notice() {

	if ( ! get_option('bulletproof_security_options_new_feature') ) {
		return;	
	}
	
	$new_feature_options = get_option('bulletproof_security_options_new_feature');

	if ( $new_feature_options['bps_mscan_rebuild'] == 'new' ) {
		return;		
	}

	if ( $new_feature_options['bps_mscan_rebuild'] == 'upgrade' ) {
	
		global $current_user;
		$options = get_option('bulletproof_security_options_monitor');
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bpsPro_hud_new_feature_notice') ) { 
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('MScan 2.0 Rebuild Notice', 'bulletproof-security').'</font><br>'.__('MScan has been completely rebuilt. MScan 2.0 is faster, very accurate and user friendly. ', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'">'.__('Check out MScan 2.0', 'bulletproof-security').'</a>.'.__(' Recommendation: Click the Reset MScan button before running a new scan.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the BPS Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_new_feature_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bpsPro_new_feature_nag_ignore');

function bpsPro_new_feature_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_new_feature_nag_ignore']) && '0' == $_GET['bpsPro_new_feature_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_hud_new_feature_notice', 'true', true);
	}
}
?>