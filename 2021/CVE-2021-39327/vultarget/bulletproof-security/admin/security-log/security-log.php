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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' && ! isset( $_POST['Submit-SecLog-Search'] ) || isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {

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

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ Security Log', 'bulletproof-security'); ?></h2>
<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// Form - Security Log page - Turn Error Logging Off
if ( isset( $_POST['Submit-Error-Log-Off'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps-error-log-off' );

	$AutoLockoptions = get_option('bulletproof_security_options_autolock');	
	$filename = ABSPATH . '.htaccess';
	$permsHtaccess = @substr(sprintf('%o', fileperms($filename)), -4);
	$sapi_type = php_sapi_name();	
	$stringReplace = file_get_contents($filename);
	$pattern1 = '/#{1,}(\s|){1,}ErrorDocument\s403(.*)\/bulletproof-security\/403\.php/';
	$pattern2 = '/(\s|){1,}ErrorDocument\s403(.*)\/bulletproof-security\/403\.php/';
	$bps_get_wp_root_secure = bps_wp_get_root_folder();		
	
	// need to get the $lock value first because permissions are cached
	if 	( file_exists($filename) && @$permsHtaccess == '0404' ) {
		$lock = '0404';			
	}

	if ( file_exists($filename) && preg_match($pattern1, $stringReplace, $matches) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777' ) { // Windows IIS, XAMPP, etc
			@chmod($filename, 0644);
		}		
		
		$stringReplace = preg_replace('/#{1,}(\s|){1,}ErrorDocument\s400(.*)ErrorDocument\s410\s(.*)\/410\.php/s', "#ErrorDocument 400 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/400.php\n#ErrorDocument 401 default\n#ErrorDocument 403 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/403.php\n#ErrorDocument 404 $bps_get_wp_root_secure"."404.php\n#ErrorDocument 405 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/405.php\n#ErrorDocument 410 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/410.php", $stringReplace);

		if ( ! file_put_contents($filename, $stringReplace) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to turn Logging Off. Either the root .htaccess file is not writable, it does not exist or the ErrorDocument .htaccess code does not exist in your Root .htaccess file. Check that the root .htaccess file exists, the code exists and that file permissions allow writing.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$lock == '0404' || $AutoLockoptions['bps_root_htaccess_autolock'] == 'On' ) {			
				@chmod($filename, 0404);
			}			
		}
	}

	if ( file_exists($filename) && preg_match($pattern2, $stringReplace, $matches) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777' ) { // Windows IIS, XAMPP, etc
			@chmod($filename, 0644);
		}		

		$stringReplace = preg_replace('/ErrorDocument\s400(.*)ErrorDocument\s410\s(.*)\/410\.php/s', "#ErrorDocument 400 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/400.php\n#ErrorDocument 401 default\n#ErrorDocument 403 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/403.php\n#ErrorDocument 404 $bps_get_wp_root_secure"."404.php\n#ErrorDocument 405 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/405.php\n#ErrorDocument 410 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/410.php", $stringReplace);
		
		if ( ! file_put_contents($filename, $stringReplace) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to turn Logging Off. Either the root .htaccess file is not writable, it does not exist or the ErrorDocument .htaccess code does not exist in your Root .htaccess file. Check that the root .htaccess file exists, the code exists and that file permissions allow writing.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$lock == '0404' || $AutoLockoptions['bps_root_htaccess_autolock'] == 'On' ) {		
				@chmod($filename, 0404);
			}	

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Logging has been turned Off', 'bulletproof-security').'</strong></font>';
			echo $text;		
			echo $bps_bottomDiv;
		}
	}
}

// Form - Security Log page - Turn Error Logging On
if ( isset( $_POST['Submit-Error-Log-On'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps-error-log-on' );

	$AutoLockoptions = get_option('bulletproof_security_options_autolock');	
	$filename = ABSPATH . '.htaccess';
	$permsHtaccess = @substr(sprintf('%o', fileperms($filename)), -4);
	$sapi_type = php_sapi_name();
	$stringReplace = file_get_contents($filename);
	$pattern1 = '/#{1,}(\s|){1,}ErrorDocument\s403(.*)\/bulletproof-security\/403\.php/';
	$pattern2 = '/(\s|){1,}ErrorDocument\s403(.*)\/bulletproof-security\/403\.php/';
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$htaccessARQ = WP_CONTENT_DIR . '/bps-backup/autorestore/root-files/auto_.htaccess';
	
	// need to get the $lock value first because permissions are cached
	if 	( file_exists($filename) && @$permsHtaccess == '0404' ) {
		$lock = '0404';			
	}	
	
	// This factors in the scenario of #ErrorDocument 403 being commented out if other ErrorDocument directives are NOT commented out
	// Create a new ErrorDocument .htaccess block of code with all ErrorDocument directives uncommented
	if ( file_exists($filename) && preg_match($pattern1, $stringReplace, $matches) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777' ) { // Windows IIS, XAMPP, etc
			@chmod($filename, 0644);
		}	

		$stringReplace = preg_replace('/ErrorDocument\s400(.*)ErrorDocument\s410\s(.*)\/410\.php/s', "ErrorDocument 400 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/400.php\nErrorDocument 401 default\nErrorDocument 403 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/403.php\nErrorDocument 404 $bps_get_wp_root_secure"."404.php\nErrorDocument 405 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/405.php\nErrorDocument 410 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/410.php", $stringReplace);
		
		if ( ! file_put_contents($filename, $stringReplace) ) {		
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to turn Logging On. Either the root .htaccess file is not writable, it does not exist or the ErrorDocument .htaccess code does not exist in your Root .htaccess file. Check that the root .htaccess file exists, the code exists and that file permissions allow writing.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$lock == '0404' || $AutoLockoptions['bps_root_htaccess_autolock'] == 'On' ) {		
				@chmod($filename, 0404);
			}			

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Logging has been turned On', 'bulletproof-security').'</strong></font>';
			echo $text;	
			echo $bps_bottomDiv;
		}
	}
	
	if ( file_exists($filename) && preg_match($pattern2, $stringReplace, $matches) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777' ) { // Windows IIS, XAMPP, etc
			@chmod($filename, 0644);
		}
		
		$stringReplace = preg_replace('/#{1,}(\s|){1,}ErrorDocument\s400(.*)ErrorDocument\s410\s(.*)\/410\.php/s', "ErrorDocument 400 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/400.php\nErrorDocument 401 default\nErrorDocument 403 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/403.php\nErrorDocument 404 $bps_get_wp_root_secure"."404.php\nErrorDocument 405 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/405.php\nErrorDocument 410 $bps_get_wp_root_secure"."$bps_plugin_dir/bulletproof-security/410.php", $stringReplace);
		
		if ( ! file_put_contents($filename, $stringReplace) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to turn Logging On. Either the root .htaccess file is not writable, it does not exist or the ErrorDocument .htaccess code does not exist in your Root .htaccess file. Check that the root .htaccess file exists, the code exists and that file permissions allow writing.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$lock == '0404' || $AutoLockoptions['bps_root_htaccess_autolock'] == 'On' ) {	
				@chmod($filename, 0404);
			}				
		}
	}
}

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Security Log', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('Security Log ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Logs Blocked Hackers & Spammers ~ HTTP 400, 403, 404, 405 & 410 Logging ~ Troubleshooting Tool', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a><br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting): ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a><br>'.__('Protect all of your Plugins (plugin folders and files) with an IP Firewall: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Plugin Firewall">'.__('Get BPS Pro Plugin Firewall', 'bulletproof-security').'</a><br>'.__('Protect your WordPress uploads folder against remote access or execution of files: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Uploads Anti-Exploit Guard">'.__('Get BPS Pro Uploads Anti-Exploit Guard', 'bulletproof-security').'</a></div>'; echo $text; ?> 
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Security Log', 'bulletproof-security'); ?>  <button id="bps-open-modal9" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content9" class="bps-dialog-hide" title="<?php _e('Security Log', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		// Forum Help Links or of course both
		$text = '<strong><font color="blue">'.__('Forum Help Links & Bonus Custom Code: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br />
    <strong><a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="POST Request Attack Protection Bonus Custom Code" target="_blank"><?php _e('POST Request Attack Protection', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php $text = '<strong>'.__('Security Log General Information', 'bulletproof-security').'</strong><br>'.__('To view your Security Log click the View Log button. Your Security Log file is a plain text static file and not a dynamic file or dynamic display to keep your website resource usage at a bare minimum and keep your website performance at a maximum. Log entries are logged in descending order by Date and Time. You can copy, edit and delete this plain text file.', 'bulletproof-security').'<br><br><strong>'.__('Note: ', 'bulletproof-security').'</strong>'.__('Security Log Email Alert and Log file option settings are on the Email|Log Settings page.', 'bulletproof-security').'<strong><br><br>'.__('NOTE: ', 'bulletproof-security').'</strong>'.__('If a particular User Agent|Bot is generating excessive log entries you can add it to Add User Agents|Bots to Ignore|Not Log tool and that User Agent|Bot will no longer be logged. See the Ignoring|Not Logging User Agents|Bots help section.', 'bulletproof-security').'<strong><br><br>'.__('NOTE: ', 'bulletproof-security').'</strong>'.__('BPS logs all 403 errors, but a 403 error may not necessarily be caused by BPS. Use the troubleshooting steps in the BPS Troubleshooting Steps link at the top of this Read Me help window to confirm or eliminate that the 403 error is being caused by BPS.', 'bulletproof-security').'<br><br>'.__('The Security Log logs 400, 403, 405 and 410 HTTP Response Status Codes by default. You can also log 404 HTTP Response Status Codes by opening this BPS 404 Template file - /bulletproof-security/404.php and copying the logging code into your Theme\'s 404 Template file. When you open the BPS Pro 404.php file you will see simple instructions on how to add the 404 logging code to your Theme\'s 404 Template file. The Security Log also logs other events. See the ', 'bulletproof-security').'<strong>'.__('Total # of Security Log Entries by Type', 'bulletproof-security').'</strong>'.__(' help section below for a complete list of BPS Security Log Entry Types.', 'bulletproof-security').'<br><br><strong>'.__('Total # of Security Log Entries by Type', 'bulletproof-security').'</strong><br>'.__('Displays the total number of each type of Security Log Entry in your Security Log file. The Total # of Security Log Entries by Type is also added to each Security Log file when it is zipped and emailed to you and also added directly in the automated Security Log email. Complete list of BPS Security Log Entry Types: 400 POST Bad Request, 400 GET Bad Request, 403 GET Request, 403 POST Request, 404 GET Not Found Request, 404 POST Not Found Request, 405 HEAD Request, 410 Gone POST Request, 410 Gone GET Request, Idle Session Logout, Maintenance Mode - Visitor Logged. BPS has a total of 11 Security Log Entry Types. BPS Pro has a total of 27 Security Log Entry Types.', 'bulletproof-security').'<br><br><strong>'.__('HTTP Response Status Codes', 'bulletproof-security').'</strong><br>'.__('400 Bad Request - The request could not be understood by the server due to malformed syntax.', 'bulletproof-security').'<br><br>'.__('403 Forbidden - The Server understood the request, but is refusing to fulfill it.', 'bulletproof-security').'<br><br>'.__('404 Not Found - The Server has not found anything matching the Request-URI|URL. No indication is given of whether the condition is temporary or permanent.', 'bulletproof-security').'<br><br>'.__('405 Method Not Allowed - The method specified in the Request-Line is not allowed for the resource identified by the Request-URI. The response MUST include an Allow header containing a list of valid methods for the requested resource. BPS blocks HEAD Requests using a 405 ErrorDocument Redirect. The BPS 405 Template has an Allow header field for the GET, POST and PUT HTTP Methods.', 'bulletproof-security').'<br><br>'.__('410 Gone - The requested resource is no longer available at the Server/site and no forwarding address is known. This condition is expected to be considered permanent.', 'bulletproof-security').'<br><br><strong>'.__('Security Log File Size', 'bulletproof-security').'</strong><br>'.__('Displays the size of your Security Log file. 500KB is the optimum recommended log file size setting that you should choose for your log file to be automatically zipped, emailed and replaced with a new blank Security Log file.', 'bulletproof-security').'<br><br><strong>'.__('Security Log Status:', 'bulletproof-security').'</strong><br>'.__('Displays either Logging is Turned On or Logging is Turned Off.', 'bulletproof-security').'<br><br><strong>'.__('Security Log Last Modified Time:', 'bulletproof-security').'</strong><br>'.__('Displays the last time a Security Log entry was logged.', 'bulletproof-security').'<br><br><strong>'.__('Turn Off Logging', 'bulletproof-security').'</strong><br>'.__('Turns Off HTTP 400, 403, 404, 405 & 410 Security Logging.', 'bulletproof-security').'<br><br><strong>'.__('Turn On Logging', 'bulletproof-security').'</strong><br>'.__('Turns On HTTP 400, 403, 404, 405 & 410 Security Logging.', 'bulletproof-security').'<br><br><strong>'.__('Delete Log Button', 'bulletproof-security').'</strong><br>'.__('Clicking the Delete Log button will delete the entire contents of your Security Log File.', 'bulletproof-security').'<br><br><strong>'.__('POST Request Body Data', 'bulletproof-security').'</strong><br>'.__('The POST Request Body Data option settings only affect the REQUEST BODY Security Log field in your Security Log entries when a POST Request is blocked and logged by BPS. To capture/log all POST Request Attacks against your website you will need to add the POST Request Attack Protection Bonus Custom Code. A link to that Bonus Custom Code is at the top of this Read Me help window. If you do not want to add the Bonus Custom Code then some, but not all POST Request Attacks will be captured/logged in the Security Log.', 'bulletproof-security').'<br><br>'.__('The default POST Request Body Data option setting is "Do Not Log POST Request Body Data (0KB)", which means do not capture/log the POST Request data that was sent in the attack. You will see this text in the REQUEST BODY Security Log entry field: "REQUEST BODY: BPS Security Log option set to: Do Not Log POST Request Body Data" instead of the actual POST Request Body data used in the attack on your website. The reason the default setting is set to: "Do Not Log POST Request Body Data (0KB)" is because some web hosts falsely interpret the BPS Security Log text file as malicious since hacker code used to attack your website can be captured/logged in the Security Log text file if you are using the "Log Minimum..." or "Log Maximum..." POST Request Body Data option settings.', 'bulletproof-security').'<br><br>'.__('The "Log Minimum POST Request Body Data (5KB)" option setting will capture/log the first 500 characters or 5KB of hacker code used to attack your website in a POST Request attack and log that hacker code in the REQUEST BODY Security Log entry field. The "Log Maximum POST Request Body Data (250KB)" option setting will capture/log the first 250000 characters or roughly 250KB of hacker code used to attack your website in a POST Request attack and log that hacker code in the REQUEST BODY Security Log entry field. Hacker scripts typically range in size from 20KB to 100KB on average.', 'bulletproof-security').'<br><br><strong>'.__('Important Notes: ', 'bulletproof-security').'</strong>'.__('If you are using email security protection on your computer then your automatically zipped and emailed BPS Security Log files may be seen as containing a virus (hacker script/code) and they could be automatically deleted by your email protection application on your computer. Your computer security protection software may also see the Security Log file as malicious and block it. If your web host falsely sees the BPS Security Log file as a malicious hacker file then you will need to change your POST Request Body Data option setting and use the "Do Not Log POST Request Body Data (0KB)" option setting instead.', 'bulletproof-security').'<br><br><strong>'.__('Ignoring|Not Logging User Agents|Bots - Allowing|Logging User Agents|Bots', 'bulletproof-security').'</strong><br>'.__('Adding or Removing User Agents|Bots adds or removes User Agents|Bots to your Database and also writes new code to the 403.php Security Logging template. The 403.php Security Logging file is where the check occurs whether or not to log or not log a User Agent|Bot. It would be foolish and costly to website performance to have your WordPress database handle the task/function/burden of checking which User Agents|Bots to log or not log. WordPress database queries are the most resource draining function of a WordPress website. The more database queries that are happening at the same time on your website the slower your website will perform and load. For this reason the Security Logging check is done from code in the 403.php Security Logging file.', 'bulletproof-security').'<br><br>'.__('If a particular User Agent|Bot is being logged excessively in your Security Log file you can Ignore|Not Log that particular User Agent|Bot based on the HTTP_USER_AGENT string in your Security Log. Example User Agent strings: Mozilla/5.0 (compatible; 008/0.85; http://www.80legs.com/webcrawler.html) Gecko/2008032620 and facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php). You could enter 008 or 80legs or webcrawler to Ignore|Not Log the 80legs User Agent|Bot. You could enter facebookexternalhit or facebook or externalhit_uatext to Ignore|Not Log the facebook User Agent|Bot.', 'bulletproof-security').'<br><br><strong>'.__('Add User Agents|Bots to Ignore|Not Log', 'bulletproof-security').'</strong><br>'.__('Add the User Agent|Bot names you would like to Ignore|Not Log in your Security Log. These code characters are not allowed to be used: ', 'bulletproof-security').'/ | < > \' "<br><br><strong>'.__('Removing User Agents|Bots to Allow|Log', 'bulletproof-security').'</strong><br>'.__('To search for ALL User Agents|Bots to remove/delete from your database leave the text box blank and click the Remove|Allow button. You will see a Dynamically generated Radio Button Form that will display the User Agents|Bots in the BPS User Agent|Bot database Table, Remove or Do Not Remove Radio buttons and the Timestamp when the User Agent|Bot was added to your DB. Select the Remove Radio buttons for the User Agents|Bots you want to remove/delete from your database and click the Remove button. Removing/deleting User Agents|Bots from your database means that you want to have these User Agents|Bots logged again in your Security Log.', 'bulletproof-security').'<br><br><strong>'.__('View Log', 'bulletproof-security').'</strong><br>'.__('In previous versions of BPS the Security Log was displayed open by default. The Security Log is now closed by default due to problems with ModSecurity CRS seeing the Security Log entries as malicious and blocking access to the Security Log page. If you are unable to open/view your Security Log file you can view your Security Log file by using FTP or your web host control panel file manager and opening the Security Log file located here ', 'bulletproof-security').'/'.$bps_wpcontent_dir.'/bps-backup/logs/http_error_log.txt. '.__('The new View Log feature also resolves another problem, which is if the Security Log file automation is not working due to WP Cron jobs being disabled on a website then the Security Log file will not be automatically zipped, emailed to you and replaced with a new blank log file at regular cron intervals by the Security Log file Cron job automation. If your Security Log file is extremely large and you are unable to open/view it then you can manually download a copy of the Security Log file using FTP or your web host control panel file manager and then delete it using the Delete Log button.', 'bulletproof-security'); echo $text; ?></p>
</div>

<?php
// Counts the Total # of Security Log Entries by Type
// Note: The Total # of Security Log Entries by Type is also added to the Security Log text file before it is zipped and emailed.
function bpsPro_SecLog_Entry_Counter() {
	
	$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';

	if ( file_exists($bpsProLog) ) {		

		$check_string = @file_get_contents($bpsProLog);

		// Only creates Total Log entry listings for Log Entry types that match.
		// Leave all 27 BPS Pro Log Entry Types in case I add some more Log Entry Types in BPS free in the future. BPS free only has 11 total Log Entry Types.
		preg_match_all( '/400 POST Bad Request|400 GET Bad Request|403 GET Request|403 POST Request|404 GET Not Found Request|404 POST Not Found Request|405 HEAD Request|410 Gone POST Request|410 Gone GET Request|Idle Session Logout|Maintenance Mode - Visitor Logged|Login Form - POST Request Logged|Login Form - GET, HEAD, OTHER Request Logged|WP Register Form - POST Request Logged|WP Register Form - GET, HEAD, OTHER Request Logged|Lost Password Form - POST Request Logged|Lost Password Form - GET, HEAD, OTHER Request Logged|Comment Form User Is Logged In - POST Request Logged|Comment Form User Is Logged In - GET, HEAD, OTHER Request Logged|Comment Form User NOT Logged In - POST Request Logged|Comment Form User NOT Logged In - GET, HEAD, OTHER Request Logged|BuddyPress Register Form - POST Request Logged|BuddyPress Register Form - GET, HEAD, OTHER Request Logged|AutoRestore Turned Off Cron Check|WP Automatic Update - ARQ was turned Off|WP Automatic Update - ARQ was turned back On|Plugin Firewall AutoPilot Mode New Whitelist Rule\(s\) Created/', $check_string, $matches );
		
		foreach ( $matches[0] as $key => $value ) {
				
		}
			
		$array_count_values = array_count_values($matches[0]);
		
		echo '<div class="sec-log-counter-text-title">'.__('Total # of Security Log Entries by Type', 'bulletproof-security') . '</div>';
		
		if ( empty( $array_count_values ) ) {
			echo '<span class="sec-log-counter-text">'.__('There are no Security Log Entries yet.', 'bulletproof-security').'</span>';
		
		} else {

			ksort($array_count_values);

			foreach ( $array_count_values as $key => $value ) {
				
				echo '<span class="sec-log-counter-text">'.__('Total ', 'bulletproof-security') . $key . __(' Log Entries: ', 'bulletproof-security') . '</span><span class="sec-log-counter-text-blue">' . $value . '</span><br>';
			}
		}	
	}
}

echo '<div id=SecLogCounter>';
bpsPro_SecLog_Entry_Counter();
echo '</div>';

// Get File Size of the Security Log File
function bps_getSecurityLogSize() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';

if ( @file_exists($filename) ) {
	$logSize = filesize($filename);
	
	if ( $logSize < 2097152 ) {
 		$text = '<span style="font-size:13px;"><strong>'. __('Security Log File Size: ', 'bulletproof-security').'<font color="#2ea2cc">'. round($logSize / 1024, 2) .' KB</font></strong></span><br>';
		echo $text;
	} else {
		 $text = '<span style="font-size:13px;"><strong>'. __('Security Log File Size: ', 'bulletproof-security').'<font color="#fb0101">'. round($logSize / 1024, 2) .' KB<br>'.__('Your Security Log file is larger than 2MB. It appears that BPS is unable to automatically zip, email and delete your Security Log file.', 'bulletproof-security').'</font></strong><br>'.__('Check your Email Alerts & Log File Options.', 'bulletproof-security').'<br>'.__('You can manually delete the contents of this log file by clicking the Delete Log button.', 'bulletproof-security').'</span><br>';		
		echo $text;
	}
	}
}
bps_getSecurityLogSize();

// Echo Error Logging On or Off
function bpsErrorLoggingOnOff() {
$filename = ABSPATH . '.htaccess';
$check_string = file_get_contents($filename);
$pattern = '/#{1,}(\s|){1,}ErrorDocument\s403(.*)\/bulletproof-security\/403\.php/';	

	if ( file_exists($filename) && preg_match($pattern, $check_string, $matches) ) {
		$text = '<span style="font-size:13px;"><strong>'.__('Security Log Status: ', 'bulletproof-security').'<font color="#2ea2cc">'.__('Logging is Turned Off', 'bulletproof-security').'</font></strong></span><br>';
		echo $text;
	} else {
		$text = '<span style="font-size:13px;"><strong>'.__('Security Log Status: ', 'bulletproof-security').'<font color="#2ea2cc">'.__('Logging is Turned On', 'bulletproof-security').'</font></strong></span><br>';
		echo $text;		
	}
}
echo bpsErrorLoggingOnOff();

// Get the Current/Last Modifed Date of the Security Log File
function bps_getSecurityLogLastMod() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';

	if ( file_exists($filename) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$timestamp = date_i18n(get_option('date_format').' - '.get_option('time_format'), @filemtime($filename) + $gmt_offset);

	$text = '<span style="font-size:13px;"><strong>'. __('Security Log Last Modified Time: ', 'bulletproof-security').'<font color="#2ea2cc">'.$timestamp.'</font></strong></span><br><br>';
	echo $text;
	}
}
echo bps_getSecurityLogLastMod();

// Delete Security Log
if ( isset( $_POST['Submit-Delete-Log'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps-delete-security-log' );

	$SecurityLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	$SecurityLogMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/http_error_log.txt'; 
	copy($SecurityLogMaster, $SecurityLog);
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Success! Your Security Log file has been deleted and replaced with a new blank Security Log file.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;
}

// Security Log Form - Add User Agents to DB and write them to the 403.php template
if ( isset( $_POST['Submit-UserAgent-Ignore'] ) && current_user_can('manage_options') ) {
check_admin_referer( 'bulletproof_security_useragent_ignore' );   
		
$userAgent = trim(stripslashes($_POST['user-agent-ignore']));
$table_name = $wpdb->prefix . "bpspro_seclog_ignore";
$blankFile = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/blank.txt';
$userAgentMaster = WP_CONTENT_DIR . '/bps-backup/master-backups/UserAgentMaster.txt';
$bps403File = WP_PLUGIN_DIR . '/bulletproof-security/403.php';
$search = '';		

	// Halt Form Processing for characters that are not allowed: /, |, <, >, ' and "
	if ( preg_match( '|[\/\|\<\>\'\"]|', $userAgent ) ) {	
		
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Error: ', 'bulletproof-security').$userAgent.__(' User Agent|Bot was not successfully added. These code characters are not allowed to be used: ', 'bulletproof-security').'/ | < > \' "'.__(' Click the Read Help button for examples of valid User Agent|Bot names.', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo $bps_bottomDiv;
		return;
	}
	
	if ( $userAgent != '' ) {	

		echo $bps_topDiv;
		$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'user_agent_bot' => $userAgent ) );
		$text = '<font color="green"><strong>'.__('Success! ', 'bulletproof-security').$userAgent.__(' User Agent|Bot has been added to your DB. ', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo $bps_bottomDiv;
		
	} else {
		
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Error: You did not enter a User Agent|Bot name. User Agent|Bot was not successfully added.', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo $bps_bottomDiv;		
	}

	if ( ! file_exists($bps403File) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: The ', 'bulletproof-security').$bps403File.__(' does not exist.', 'bulletproof-security').'</strong></font>';
			echo $text;		
			echo $bps_bottomDiv;
	}
	
	if ( file_exists($blankFile) ) {
		copy($blankFile, $userAgentMaster);
	}

	$getSecLogTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE user_agent_bot LIKE %s", "%$search%") );
	$UserAgentRules = array();
		
	if ( $wpdb->num_rows != 0 ) {

		foreach ( $getSecLogTable as $row ) {
			$UserAgentRules[] = "(.*)".$row->user_agent_bot."(.*)|";
			file_put_contents($userAgentMaster, $UserAgentRules);
		}
	
	$UserAgentRulesT = @file_get_contents($userAgentMaster);
	$stringReplace = @file_get_contents($bps403File);

			$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/".trim($UserAgentRulesT, "|")."/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);
		
		if ( $userAgent != '' ) { 

			if ( ! file_put_contents($bps403File, $stringReplace) ) {
				echo $bps_topDiv;
				$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to file ', 'bulletproof-security').$bps403File.__('. Check that file permissions allow writing to this file. If you have a DSO Server check file and folder Ownership.', 'bulletproof-security').'</strong></font>';
				echo $text;	
				echo $bps_bottomDiv;
			
			} else {
			
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Success! The BPS 403.php Security Logging template file has been updated. This User Agent|Bot will be no longer be logged in your Security Log.', 'bulletproof-security').'</strong></font>';
				echo $text;	
				echo $bps_bottomDiv;
			}
		}
	}
}
?>

<div id="SecLogPostLimit">
<form name="SecLogPostLimit" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_sec_log_post_limit'); ?> 
	<?php $SecLogPostLimit = get_option('bulletproof_security_options_sec_log_post_limit'); ?>

    <strong><label for="SecLogPostLimit"><?php _e('POST Request Body Data', 'bulletproof-security'); ?></label></strong><br />
    <input type="checkbox" name="bulletproof_security_options_sec_log_post_limit[bps_security_log_post_none]" value="1" <?php if ( empty( $SecLogPostLimit['bps_security_log_post_none'] ) ) { echo ''; } else { checked( $SecLogPostLimit['bps_security_log_post_none'], 1 ); } ?> /><label><?php _e(' Do Not Log POST Request Body Data (0KB)', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bulletproof_security_options_sec_log_post_limit[bps_security_log_post_limit]" value="1" <?php if ( empty( $SecLogPostLimit['bps_security_log_post_limit'] ) ) { echo ''; } else { checked( $SecLogPostLimit['bps_security_log_post_limit'], 1 ); } ?> /><label><?php _e(' Log Minimum POST Request Body Data (5KB)', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bulletproof_security_options_sec_log_post_limit[bps_security_log_post_max]" value="1" <?php if ( empty( $SecLogPostLimit['bps_security_log_post_max'] ) ) { echo ''; } else { checked( $SecLogPostLimit['bps_security_log_post_max'], 1 ); } ?> /><label><?php _e(' Log Maximum POST Request Body Data (250KB)', 'bulletproof-security'); ?></label><br />
	<input type="submit" name="Submit-Sec-Log-Post-Limit" class="button bps-button"  style="margin-top:5px;" value="<?php esc_attr_e('Save POST Request Body Data Option', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>    
</form>
</div>

<div id="SecLogRemove-Allow"></div>

<div id="bpsUserAgent1" style="margin:0px 0px 0px 0px;">
<form action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_useragent_ignore'); ?>
    <strong><label for="UA-ignore"><?php _e('Add User Agents|Bots to Ignore|Not Log', 'bulletproof-security'); ?></label></strong><br />
    <strong><label for="UA-ignore"><?php _e('Click the Read Me Help button for examples', 'bulletproof-security'); ?></label></strong><br />    
    <input type="text" name="user-agent-ignore" class="regular-text-320" value="" />
    <input type="submit" name="Submit-UserAgent-Ignore" value="<?php esc_attr_e('Add|Ignore', 'bulletproof-security') ?>" class="button bps-button" style="width:104px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Clicking OK will Add the User Agent|Bot name you have entered to your DB and the 403.php Security Logging template.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Security logging checks are done by the 403.php Security Logging file and not by DB Queries.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('To remove User Agents|Bots from being ignored/not logged use the Remove|Allow tool.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>


<?php
/**************************************/
//  BEGIN Dynamic Security Log Form   //
/**************************************/

	// Initial User Agent|Bot Search Form - hands off to Dynamic Radio Button Form
	echo '<form name="bpsDB-UA-Search" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php#SecLogRemove-Allow' ).'" method="post">';
	wp_nonce_field('bulletproof_security_seclog_db_search');
	echo '<span style="font-size:13px;"><strong>'.__('Remove User Agents|Bots to Allow|Log', 'bulletproof-security').'</strong></span><br>';
	echo '<input type="text" name="userAgentSearchRemove" class="regular-text-320" value="" />';
	echo '<input type="submit" name="Submit-SecLog-Search" value="'.esc_attr__('Remove|Allow', 'bulletproof-security').'" class="button bps-button" style="margin-left:3px;width:104px;height:auto;white-space:normal" onclick="return confirm('."'".__('Clicking OK will search your database and display User Agent|Bot DB search results in a Dynamic Radio button Form.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('To search for ALL User Agents|Bots to remove/delete from your database leave the text box blank and click the Remove|Allow button.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security')."'".')" />';
	echo '</form><br>';

?>

<div id="SecurityLogTable">

<table width="450" border="0">
  <tr>
    <td>
<div id="SecurityLogTurnOffButton">
<form name="BPSErrorLogOff" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bps-error-log-off'); ?>
<input type="submit" name="Submit-Error-Log-Off" value="<?php esc_attr_e('Turn Off Logging', 'bulletproof-security') ?>" class="button bps-button" style="width:125px;margin-right:15px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Click OK to Turn Off Error Logging or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>
</td>
    <td>
<div id="SecurityLogTurnOnButton">
<form name="BPSErrorLogOn" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bps-error-log-on'); ?>
<input type="submit" name="Submit-Error-Log-On" value="<?php esc_attr_e('Turn On Logging', 'bulletproof-security') ?>" class="button bps-button" style="width:125px;margin-right:15px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Click OK to Turn On Logging or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>
</td>
    <td>
<div id="SecurityLogDeleteLogButton">
<form name="DeleteLogForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bps-delete-security-log'); ?>
<input type="submit" name="Submit-Delete-Log" value="<?php esc_attr_e('Delete Log', 'bulletproof-security') ?>" class="button bps-button" style="width:125px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Clicking OK will delete the contents of your Security Log file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Delete the Log file contents or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>
</td>
  </tr>
</table>
</div>

<?php
/**************************************/
//  Cont. Dynamic Security Log Form   //
/**************************************/
// Get the Search Post variable for processing other search/remove Forms 
if ( isset( $_POST['Submit-SecLog-Search'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_seclog_db_search' );
	
$search = $_POST['userAgentSearchRemove'];
$bpspro_seclog_table = $wpdb->prefix . "bpspro_seclog_ignore";
$bps403File = WP_PLUGIN_DIR . '/bulletproof-security/403.php';
$stringReplace = @file_get_contents($bps403File);
$searchAll = '';

		if ( ! file_exists($bps403File) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: The ', 'bulletproof-security').$bps403File.__(' does not exist.', 'bulletproof-security').'</strong></font>';
			echo $text;		
			echo $bps_bottomDiv;
		}

			$getSecLogTableSearch = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_seclog_table WHERE user_agent_bot LIKE %s", "%$searchAll%") );
			
		if ( $wpdb->num_rows == 0 ) { // if no rows exist in DB add the BPSUserAgentPlaceHolder back into the 403.php security logging template
			
			$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/BPSUserAgentPlaceHolder/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);		
		
		if ( ! file_put_contents($bps403File, $stringReplace) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to file ', 'bulletproof-security').$bps403File.__('. Check that file permissions allow writing to this file. If you have a DSO Server check file and folder Ownership.', 'bulletproof-security').'</strong></font>';
			echo $text;	
			echo $bps_bottomDiv;
		
		} else {
			// blah
		}		
		} // end if ($wpdb->num_rows == 0) { // No database rows
}

// Remove User Agents|Bots Dynamic Radio button Form proccessing code
if ( isset( $_POST['Submit-SecLog-Remove'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_seclog_db_remove');
	
$removeornot = $_POST['removeornot'];
$bpspro_seclog_table = $wpdb->prefix . "bpspro_seclog_ignore";
$userAgentMaster = WP_CONTENT_DIR . '/bps-backup/master-backups/UserAgentMaster.txt';
$bps403File = WP_PLUGIN_DIR . '/bulletproof-security/403.php';
$searchALLD = '';

	switch( $_POST['Submit-SecLog-Remove'] ) {
		case __('Remove', 'bulletproof-security'):
	
		$remove_rows = array();

		if ( ! empty($removeornot) ) {
			
			foreach ( $removeornot as $key => $value ) {
				if ( $value == 'remove' ) {
					$remove_rows[] = $key;
				} elseif ( $value == 'donotremove' ) {
					$donotremove .=  ', '.$key;
				}
				}
			}
			
			@$donotremove = substr($donotremove, 2);
		
		if ( ! empty($remove_rows) ) {
			
			foreach ( $remove_rows as $remove_row ) {
				if ( ! $delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $bpspro_seclog_table WHERE user_agent_bot = %s", $remove_row) )) {
					$textSecLogRemove = '<font color="#fb0101"><strong>'.sprintf(__('%s unable to delete row from your DB.', 'bulletproof-security'), $remove_row).'</strong></font><br>';			
				} else {
					$textSecLogRemove = '<font color="green"><strong>'.sprintf(__('%s has been deleted from your DB.', 'bulletproof-security'), $remove_row).'</strong></font><br>';
	
					$getSecLogTableRemove = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_seclog_table WHERE user_agent_bot LIKE %s", "%$searchALLD%") );
					$UserAgentRules = array();		

					foreach ( $getSecLogTableRemove as $row ) {
						$UserAgentRules[] = "(.*)".$row->user_agent_bot."(.*)|";
							file_put_contents($userAgentMaster, $UserAgentRules);
					}
				} // end if ( !$delete_row
			} // foreach ($remove_rows as $remove_row) {

			// Important these variables MUST BE HERE inside the switch
			$UserAgentRulesT = @file_get_contents($userAgentMaster);
			$stringReplace = @file_get_contents($bps403File);
					
			$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/".trim($UserAgentRulesT, "|")."/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);

		if ( ! file_put_contents($bps403File, $stringReplace) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to file ', 'bulletproof-security').$bps403File.__('. Check that file permissions allow writing to this file. If you have a DSO Server check file and folder Ownership.', 'bulletproof-security').'</strong></font>';
			echo $text;	
			echo $bps_bottomDiv;
		
		} else {
			// need to run the Query again just in case there are 0 DB rows
			@$getSecLogTableRemove = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_seclog_table WHERE user_agent_bot LIKE %s", "%$searchAll%" ) );
			
		if ( $wpdb->num_rows == 0 ) { // if no rows exist in DB add the BPSUserAgentPlaceHolder back into the 403.php security logging template
			
			$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/BPSUserAgentPlaceHolder/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);
			file_put_contents($bps403File, $stringReplace);		
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Success! The BPS 403.php Security Logging template file has been updated. This User Agent|Bot will be logged again in your Security Log.', 'bulletproof-security').'</strong></font>';
			echo $text;	
			echo $bps_bottomDiv;
		}
		} // end if (!empty($remove_rows)) { // no rows selected to delete
		
		if ( ! empty($donotremove) ) {
		// do nothing here - do not echo a message because it would be repeated X times
		//$textDB = '<font color="green">'.sprintf(__('DB Rows %s Not Removed', 'bulletproof-security'), $donotremove).'</font>';
		}
		break;
	} // end switch
}

if ( ! empty($textSecLogRemove) ) { 
echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>'.$textSecLogRemove.'</p></div>'; 
}
?>

<!-- Dynamic User Agent|Bot Radio Button Remove Form -->
<form name="bpsSecLogRadio" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php 
	wp_nonce_field('bulletproof_security_seclog_db_remove');

	if ( isset( $_POST['Submit-SecLog-Search'] ) ) {

		if ( preg_match ( '|[\/\|\<\>\'\"]|', $_POST['userAgentSearchRemove'] ) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: These code characters are not allowed to be used: ', 'bulletproof-security').'/ | < > \' "'.'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		return;	
		}
	
		$bpspro_seclog_table = $wpdb->prefix . "bpspro_seclog_ignore";
		$search = esc_html( @$_POST['userAgentSearchRemove'] );
		$getSecLogTableSearchForm = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_seclog_table WHERE user_agent_bot LIKE %s", "%$search%") );
		
		echo '<h3>'.__('Search Results For User Agents|Bots To Remove', 'bulletproof-security').'</h3>';	
		echo '<table class="widefat" style="margin-bottom:20px;width:675px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:20%;"><strong>'.__('User Agents|Bots in DB', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:7%;"><strong>'.__('Remove', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:7%;"><strong>'.__('Do Not<br>Remove', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:10%;"><strong>'.__('Time Added<br>To DB', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
		foreach ( $getSecLogTableSearchForm as $row ) {
		
		echo '<th scope="row" style="border-bottom:none;">'.$row->user_agent_bot.'</th>';
		echo "<td><input type=\"radio\" id=\"remove\" name=\"removeornot[$row->user_agent_bot]\" value=\"remove\" /></td>";
		echo "<td><input type=\"radio\" id=\"donotremove\" name=\"removeornot[$row->user_agent_bot]\" value=\"donotremove\" checked /></td>";
		echo '<td>'.$row->time.'</td>'; 
		echo '</tr>';			
		}
		echo '</tbody>';
		echo '</table>';	
		if ( $wpdb->num_rows != 0 ) {		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Your DB Search Results For User Agents|Bots To Remove are displayed below the Remove|Allow Search tool.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		} else {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('You do not have any User Agents|Bots in your DB To Remove. An empty/blank dynamic radio button form is displayed below the Remove|Allow Search tool since you do not have any User Agents|Bot to remove.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		}
	echo $bps_bottomDiv;

?>
<input type="submit" name="Submit-SecLog-Remove" value="<?php esc_attr_e('Remove', 'bulletproof-security'); ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Clicking OK will Remove the User Agent|Bot DB entries for any Remove Radio button selections you have made. User Agents|Bots will also be removed from the 403.php Security Logging template.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('To add a User Agent|Bot, use the Add|Ignore tool.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form><br />
<?php } 
/*************************************/
//   END Dynamic Security Log Form   //
/*************************************/
?>

<div id="ViewSecurityLog" style="margin:10px 0px 10px 0px">
<form name="ViewSecurityLogForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bps_view_security_log'); ?>
<input type="submit" name="Submit-View-Security-Log" value="<?php esc_attr_e('View Log', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Clicking OK will display the contents of your Security Log file.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to view the Log file or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="messageinner" class="updatedinner">

<?php
// Get BPS Security log file contents
// Filter out Varicode, ASCII Control characters and everything else that is not ASCII printable characters
// Convert all accent characters to ASCII characters.
// Note: If this is a problem for other non-English languages then switch to UTF-8
function bps_get_security_log() {

	if ( isset( $_POST['Submit-View-Security-Log'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bps_view_security_log' ); 
		
		$bps_sec_log = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
		$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

		if ( file_exists($bps_sec_log) ) {
			$get_sec_log_contents = file_get_contents($bps_sec_log);
			$sec_log_clean = preg_replace( '/[^\x01-\x7F]/', "", remove_accents( $get_sec_log_contents ) );

			return esc_html($sec_log_clean);
	
		} else {
		
			echo __('The Security Log File Was Not Found! Check that the file really exists here - /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/logs/http_error_log.txt and is named correctly.', 'bulletproof-security');

		}
	}
}

// Form - Security Log - Perform File Open and Write test - If append write test is successful write to file
function bpsPro_security_log_write_check() {

	if ( isset ( $_POST['submit-security-log'] ) ) {
		$_POST['submit-security-log'] = true;
	} else {
		$_POST['submit-security-log'] = null;
	}

	if ( $_POST['submit-security-log'] != true ) {

		$bps_sec_log = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	
		if ( is_writable($bps_sec_log) ) {
			$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your Security Log file is writable. Click the View Log button.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
		
		} else {
				
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$bps_sec_log . '</strong></font><br>';
			echo $text;
		}
	}
}

bpsPro_security_log_write_check();

	if ( isset( $_POST['submit-security-log'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_security_log' );
		$newcontentSecLog = stripslashes($_POST['newcontentSecLog']);
	
	if ( is_writable($bps_sec_log) ) {
		
		$handle = fopen($bps_sec_log, 'w+b');
		fwrite($handle, $newcontentSecLog);
		
		$text = '<font color="green" style="font-size:12px;"><strong>'.__('Success! Your Security Log file has been updated.', 'bulletproof-security').'</strong></font><br>';
		echo $text;	
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Success! Your Security Log file has been updated.', 'bulletproof-security').'</strong></font>';
		echo $text;	
		echo $bps_bottomDiv;	
		
		fclose($handle);
	}
}
$scrolltoSecLog = isset($_REQUEST['scrolltoSecLog']) ? (int) $_REQUEST['scrolltoSecLog'] : 0;
?>
</div>

<div id="SecLogEditor">
<form name="bpsSecLog" id="bpsSecLog" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/security-log/security-log.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_security_log'); ?>
<div id="bpsSecLog">
    <textarea class="bps-text-area-600x700" name="newcontentSecLog" id="newcontentSecLog" tabindex="1"><?php echo bps_get_security_log(); ?></textarea>
	<input type="hidden" name="scrolltoSecLog" id="scrolltoSecLog" value="<?php echo esc_html( $scrolltoSecLog ); ?>" />
    <p class="submit">
	<input type="submit" name="submit-security-log" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsSecLog').submit(function(){ $('#scrolltoSecLog').val( $('#newcontentSecLog').scrollTop() ); });
	$('#newcontentSecLog').scrollTop( $('#scrolltoSecLog').val() ); 
});
/* ]]> */
</script>
</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-2" class="bps-tab-page">

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