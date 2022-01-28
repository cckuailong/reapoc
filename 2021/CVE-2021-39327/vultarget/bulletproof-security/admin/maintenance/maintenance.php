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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' || isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {

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

<h2 class="bps-tab-title">

<?php 
if ( is_multisite() && $blog_id != 1 ) {
_e('BulletProof Security ~ Maintenance Mode', 'bulletproof-security');
} else {
_e('Maintenance Mode ~ FrontEnd ~ BackEnd', 'bulletproof-security');
}
?>
</h2>

<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_mmode() {
	
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

// Preview - write a new denyall htaccess file with the user's current IP address in the /admin/htaccess/ folder
// on Network sites if 2 users with 2 different ips are using mmode this will be a problem 
// see what happens and then beef this function up if needed
function bpsPro_maintenance_mode_preview_ip() {

	if ( current_user_can('manage_options') ) {
	
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	

		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			return;
		}

		$denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/.htaccess';
	
		if ( file_exists($denyall_htaccess_file) ) {	
		
			$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');		
		
			if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
				$bps_denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_mmode()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_mmode()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
			} else {
		
				$bps_denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_mmode()."\n</FilesMatch>";		
			}		
		
			file_put_contents( $denyall_htaccess_file, $bps_denyall_content );
		}
	}
}
bpsPro_maintenance_mode_preview_ip();

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';
?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Maintenance Mode', 'bulletproof-security'); ?></a></li>
            <li><a href="#bps-tabs-2"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<?php
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' ) {
		$text = '<h3><strong><span style="font-size:1em;"><font color="blue">'.__('Notice: ', 'bulletproof-security').'</font></span><span style="font-size:.75em;">'.__('You have disabled wp-admin BulletProof Mode on the Security Modes page.', 'bulletproof-security').'<br>'.__('If you have Go Daddy "Managed WordPress Hosting" click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/gdmw/" target="_blank" title="Link opens in a new Browser window">'.__('Go Daddy Managed WordPress Hosting', 'bulletproof-security').'</a>.</span></strong></h3>';
		echo $text;
	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">

<?php 
	if ( is_multisite() && $blog_id != 1 ) {
		$text = '<h2>'.__('FrontEnd Maintenance Mode Page ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Display a website under maintenance page to website visitors', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a><br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting): ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a><br>'.__('Protect all of your Plugins (plugin folders and files) with an IP Firewall: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Plugin Firewall">'.__('Get BPS Pro Plugin Firewall', 'bulletproof-security').'</a><br>'.__('Protect your WordPress uploads folder against remote access or execution of files: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Uploads Anti-Exploit Guard">'.__('Get BPS Pro Uploads Anti-Exploit Guard', 'bulletproof-security').'</a></div>'; echo $text; 
	} else {
		$text = '<h2>'.__('FrontEnd & BackEnd Maintenance Mode ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Display a website under maintenance page to website visitors ~ Lock wp-admin BackEnd by IP Address', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a><br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting): ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a><br>'.__('Protect all of your Plugins (plugin folders and files) with an IP Firewall: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Plugin Firewall">'.__('Get BPS Pro Plugin Firewall', 'bulletproof-security').'</a><br>'.__('Protect your WordPress uploads folder against remote access or execution of files: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Uploads Anti-Exploit Guard">'.__('Get BPS Pro Uploads Anti-Exploit Guard', 'bulletproof-security').'</a></div>'; echo $text; 
	}
	?>

    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Maintenance Mode', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" style="background-color:#fff; padding:0px 10px 10px 10px;" title="<?php _e('Maintenance Mode', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		// Forum Help Links or of course both
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/maintenance-mode-guide-read-me-first/" title="Maintenance Mode Guide" target="_blank"><?php _e('Maintenance Mode Guide', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php $text = '<strong>'.__('Create/add whatever messages, images, videos, etc. you want to display to website visitors with the MMode Editor, select your MMode options/settings, click the Save Options button, Preview your Maintenance Mode page and click the Turn On button. Rinse and repeat if you make any new changes to your options/settings.', 'bulletproof-security').'</strong><br><br><strong>'.__('For more extensive help info, CSS, HTML code examples, Image & Video embedding code examples to add in the MMode Editor see Forum Help Links at the top of this Read Me help window.', 'bulletproof-security').'</strong><br><br><strong>'.__('Maintenance Mode Text, CSS Style Code, Images, Videos Displayed To Website Visitors:', 'bulletproof-security').'</strong><br>'.__('This is a standard WordPress TinyMCE WYSIWYG editor that has a Visual Editor and a Text Editor for adding CSS or HTML code. Enter plain text, CSS, HTML code, insert images, videos, etc. For examples/example code of embedding images or YouTube videos using CSS and HTML code, which you can copy and paste into the Text editor, go to the Maintenance Mode Guide Forum Help Link above. After you copy and paste the example code into the Text Editor you can edit it, add/change links/code or whatever you want change and click the Save Options button to save your edits.', 'bulletproof-security').'<br><br><strong>'.__('Enable Countdown Timer:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to enable a javascript Countdown Timer that will be displayed to visitors. When the Countdown Timer reaches 0/has completed your website will still be in Maintenance Mode until you turn Off Maintenance Mode. An additional option will be added in the future to automatically turn off Maintenance Mode when the Countdown Timer reaches 0/has completed.', 'bulletproof-security').'<br><br><strong>'.__('Countdown Timer Text Color:', 'bulletproof-security').'</strong><br>'.__('Select the text color for the Countdown Timer.', 'bulletproof-security').'<br><br><strong>'.__('Maintenance Mode Time (in Minutes):', 'bulletproof-security').'</strong><br>'.__('Enter the amount of time that you want to put your site into Maintenance Mode in minutes. Example: 10 = 10 minutes, 180 = 3 hours, 1440 = 24 hours, 4320 = 3 days.', 'bulletproof-security').'<br><br><strong>'.__('Header Retry-After (enter the same time as Maintenance Mode Time above):', 'bulletproof-security').'</strong><br>'.__('This is the amount of time that you are telling Search Engines to wait before visiting your website again. Enter the same time in minutes that you entered for Maintenance Mode Time.', 'bulletproof-security').'<br><br><strong>'.__('Enable FrontEnd Maintenance Mode:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to enable FrontEnd Maintenance Mode. When you Turn On FrontEnd Maintenance Mode your website Maintenance Mode page will be displayed to website visitors instead of your website. Hint: besides using Preview to see what your site will look like to visitors you can also not enter your IP address in the Maintenance Mode IP Address Whitelist Text Box - CAUTION: do not enable BackEnd Maintenance Mode if you do that or you will be locked out of your WordPress Dashboard.', 'bulletproof-security').'<br><br><strong>'.__('Enable BackEnd Maintenance Mode:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to enable BackEnd Maintenance Mode. Be sure to enter the Your IP address/the Recommended IP address in the Maintenance Mode IP Address Whitelist Text Box before you click the Save Options button and click the Turn On button. If you Turn On BackEnd Maintenance Mode and your IP address is not entered and saved then you will be locked out of your WordPress Dashboard. To get back into your WordPress Dashboard, FTP to your website and delete the /wp-admin/.htaccess file to be able to log back into your WordPress Dashboard.', 'bulletproof-security').'<br><br><strong>'.__('Maintenance Mode IP Address Whitelist Text Box:', 'bulletproof-security').'</strong><br>'.__('Enter The IP Address That Can View The Website Normally (not in Maintenance Mode):', 'bulletproof-security').'<br>'.__('Enter Multiple IP addresses separated by a comma and a single space. Example: 100.99.88.77, 200.66.55.44, 44.33.22.1 It is recommended that you use the Recommended IP address that is displayed to you. IP addresses are dynamic and will be changed frequently by your ISP. The Recommended IP address is 3 octets (xxx.xxx.xxx.) of your IP address instead of 4 octets (xxx.xxx.xxx.xxx). ISP\'s typically only change the 4th octet of IP addresses that are assigned to you. You can use/enter either 1 octet, 2 octets, 3 octets or your current IP address to whitelist your IP address.', 'bulletproof-security').'<br><br><strong>'.__('Background Images:', 'bulletproof-security').'</strong><br>'.__('Select a background image that you want to use. BPS includes 20 background images and 15 center images (text box images) that you can mix and match to your design/color scheme preference.', 'bulletproof-security').'<br><br><strong>'.__('Center Images:', 'bulletproof-security').'</strong><br>'.__('Select a center image that you want to use. BPS includes 20 background images and 15 center images (text box images) that you can mix and match to your design/color scheme preference.', 'bulletproof-security').'<br><br><strong>'.__('Background Colors (If not using a Background Image):', 'bulletproof-security').'</strong><br>'.__('Select a background color that you want to use. If you do not want to use a background image then you can instead choose a background color.', 'bulletproof-security').'<br><br><strong>'.__('Display Visitor IP Address:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to display the website visitor\'s IP addresses.', 'bulletproof-security').'<br><br><strong>'.__('Display Admin|Login Link', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to display a Login link that points to your wp-admin folder/Login page.', 'bulletproof-security').'<br><br><strong>'.__('Display Dashboard Reminder Message when site is in Maintenance Mode:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to display a WordPress Dashboard Reminder Notice that your website is in Maintenance Mode.', 'bulletproof-security').'<br><br><strong>'.__('Enable Visitor Logging:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to enable visitor logging. Logs all visitors to your site while your site is in Maintenance Mode. Log entries are created in the BPS Security Log file. ', 'bulletproof-security').'Example Log Entry:<br>[Maintenance Mode - Visitor Logged: March 31, 2016 - 11:45 am]<br>REMOTE_ADDR: 127.0.0.1<br>Host Name: xxxxx<br>SERVER_PROTOCOL: HTTP/1.1<br>HTTP_CLIENT_IP:<br>HTTP_FORWARDED:<br>HTTP_X_FORWARDED_FOR:<br>HTTP_X_CLUSTER_CLIENT_IP:<br>REQUEST_METHOD: GET<br>HTTP_REFERER: http://www.example.com/<br>REQUEST_URI: /<br>QUERY_STRING:<br>HTTP_USER_AGENT: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36
'.'<br><br><strong>'.__('Send Email Reminder when Maintenance Mode Countdown Timer has completed:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to enable the javascript Countdown Timer to send you an email reminder when the Countdown Timer reaches 0/is completed. More importantly when this option is selected you will receive another email reminder each time a visitor visits your website in Maintenance Mode. When the Countdown Timer reaches 0/has completed your website will still be in Maintenance Mode until you turn Off Maintenance Mode. An additional option will be added in the future to automatically turn off Maintenance Mode when the Countdown Timer reaches 0/has completed.', 'bulletproof-security').'<br><br><strong>'.__('Testing the Countdown Timer Send Email Option:', 'bulletproof-security').'</strong><br>'.__('There is a 1 minute buffer so that when the Maintenance Mode page is created an email will not be sent immediately. To test the Send Email option use 2 minutes for the Maintenance Mode Time, click the Save Options button and click the Preview button. Leave the Preview Browser Window/Tab open. When the Countdown Timer has completed (reached 0) an email will be sent. You may receive the email immediately or it may take several minutes depending on how fast your Mail Server sends the email to you.', 'bulletproof-security').'<br><br><strong>'.__('Send Countdown Timer Email:', 'bulletproof-security').'</strong><br>'.__('Enter the email addresses that you would like the Countdown Timer reminder email sent to, from, cc or bcc.', 'bulletproof-security').'<br><br><strong>'.__('Network/Multisite Primary Site Options ONLY:', 'bulletproof-security').'</strong><br>'.__('These options/settings are for Network/Multisite ONLY and are ONLY displayed on the Primary Network/Multisite site. Checking these options on a Single/Standard WordPress installation have no effect since these options are ONLY for Network/Multisite WordPress installations.', 'bulletproof-security').'<br><br><strong>'.__('Steps to use these special Network/Multisite options:', 'bulletproof-security').'</strong><br>'.__('To turn On either of these special options, check the checkbox, click the Save Options button and click the Turn On button. To turn Off either of these options, uncheck the checkbox, click the Save Options button and click the Turn On button - you would not click the Turn Off button. You are resaving your options and then writing those saved option settings to the Maintenance template files. Or in other words, you have removed those options settings and are creating another new template file without these special option settings in that template file.', 'bulletproof-security').'<br><br><strong>'.__('Put The Primary Site And All Subsites In Maintenance Mode:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to put all of the sites into Maintenance Mode.', 'bulletproof-security').'<br><br><strong>'.__('Put All Subsites In Maintenance Mode, But Not The Primary Site:', 'bulletproof-security').'</strong><br>'.__('Check this checkbox to put all of the subsites into Maintenance Mode except for the Primary site.', 'bulletproof-security').'<br><br><strong>'.__('Save Options Button', 'bulletproof-security').'</strong><br>'.__('Clicking the Save Options button does 2 things: Saves all your options/settings to your Database and creates all necessary Maintenance Mode files/Forms. Clicking the Save Options button does NOT Turn On Maintenance Mode. Click the Turn On button after clicking the Save Options button.', 'bulletproof-security').'<br><br><strong>'.__('Preview Button', 'bulletproof-security').'</strong><br>'.__('Clicking the Preview button allows you to preview the Maintenance Mode files/Forms that were created when you clicked the Save Options button. Preview allows you to view what will be displayed to visitors to your website when you turn On Maintenance Mode. Maintenance Mode is not turned On when you click the Preview button. Maintenance Mode is turned On by clicking the Turn On button.', 'bulletproof-security').'<br><br><strong>'.__('Turn On Button', 'bulletproof-security').'</strong><br>'.__('Clicking the Turn On button turns On Maintenance Mode. Turn On is conditional and allows you to make changes to your Maintenance Mode page that is displayed to your website visitors. You can make any new changes to your options/settings, click the Save Options button again, click the Turn On button again and your new changes/settings will be immediately displayed on your Maintenance Mode page.', 'bulletproof-security').'<br><br><strong>'.__('Turn Off Button', 'bulletproof-security').'</strong><br>'.__('Clicking the Turn Off button turns Off Maintenance Mode. Turn Off is non-conditional and works like a Form Reset, but does not remove any of your Saved Options/settings. All active/enabled maintenance mode files/Forms are removed from your site and of course maintenance mode is turned Off. If you have a Network/Multisite site then some Maintenance Mode files need to remain in your website root folder, but Maintenance Mode will be turned Off.', 'bulletproof-security').'<br><br><strong>'.__('BPS help links can be found in the Help & FAQ pages.', 'bulletproof-security').'</strong>'; echo $text; ?></p>
</div>

<h3><?php $text = '<strong><a href="https://forum.ait-pro.com/forums/topic/maintenance-mode-guide-read-me-first/" target="_blank" title="Link opens in a new Browser window">'.__('Maintenance Mode Guide', 'bulletproof-security').'</a></strong>'; echo $text; ?></h3>

<?php
// Maintenance Mode Values Form Single/GWIOD/Network - Saves DB Options & creates bps-maintenance-values.php
// Uses $current_blog->path for Network file naming bps-maintenance-values-{subsite-uri}.php & bps-maintenance-{subsite-uri}.php
function bpsPro_maintenance_mode_values_form() {
global $current_blog, $blog_id, $bps_topDiv, $bps_bottomDiv;

if ( isset( $_POST['Submit-Maintenance-Mode-Form'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bpsMaintenanceMode' );

	$MMoptions = get_option('bulletproof_security_options_maint_mode');

	if ( isset($MMoptions['bps_maint_on_off']) && $MMoptions['bps_maint_on_off'] == 'On' ) {
		$bps_maint_on_off = 'On';
	} else {
		$bps_maint_on_off = 'Off';
	}
	
	if ( is_multisite() && $blog_id != 1 ) {	
		$bps_maint_backend = '';
		$bps_maint_mu_entire_site = '';
		$bps_maint_mu_subsites_only = '';
	
	} else {
		
		$bps_maint_backend = ! empty($_POST['mmode_backend']) ? '1' : '';
		$bps_maint_mu_entire_site = ! empty($_POST['mmode_mu_entire_site']) ? '1' : '';
		$bps_maint_mu_subsites_only = ! empty($_POST['mmode_mu_subsites_only']) ? '1' : '';
	}
	
	$mmode_countdown_timer = ! empty($_POST['mmode_countdown_timer']) ? '1' : '';
	
	if ( empty($_POST['mmode_time']) && $mmode_countdown_timer == '1' ) {
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You did not enter anything in the Maintenance Mode Time Text Box.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	}

	if ( empty($_POST['mmode_time']) ) {	
		$bps_maint_time = '0';
	} else {
		$bps_maint_time = $_POST['mmode_time'];		
	}

	if ( empty($_POST['mmode_retry_after']) ) {	
		$bps_maint_retry_after = '0';
	} else {
		$bps_maint_retry_after = $_POST['mmode_retry_after'];		
	}

	if ( empty($_POST['mmode_ip_allowed']) ) {	
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You did not enter an IP Address in the Maintenance Mode IP Address Whitelist Text Box.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	
	} else {
		
		$bps_maint_ip_allowed = trim( $_POST['mmode_ip_allowed'], ", \t\n\r");		
	}

	$bps_maint_frontend = ! empty($_POST['mmode_frontend']) ? '1' : '';
	$bps_maint_show_visitor_ip = ! empty($_POST['mmode_visitor_ip']) ? '1' : '';	
	$bps_maint_show_login_link = ! empty($_POST['mmode_login_link']) ? '1' : '';
	$bps_maint_dashboard_reminder = ! empty($_POST['mmode_dashboard_reminder']) ? '1' : '';
	$bps_maint_log_visitors = ! empty($_POST['mmode_log_visitors']) ? '1' : '';
	$bps_maint_countdown_email = ! empty($_POST['mmode_countdown_email']) ? '1' : '';

	$BPS_Options = array(
	'bps_maint_on_off' 					=> $bps_maint_on_off, 
	'bps_maint_countdown_timer' 		=> $mmode_countdown_timer, 
	'bps_maint_countdown_timer_color' 	=> $_POST['mmode_countdown_timer_color'], 
	'bps_maint_time' 					=> $bps_maint_time, 
	'bps_maint_retry_after' 			=> $bps_maint_retry_after, 
	'bps_maint_frontend' 				=> $bps_maint_frontend, 
	'bps_maint_backend' 				=> $bps_maint_backend, 
	'bps_maint_ip_allowed' 				=> $bps_maint_ip_allowed, 
	'bps_maint_text' 					=> $_POST['bpscustomeditor'],
	'bps_maint_background_images' 		=> $_POST['mmode_background_images'], 
	'bps_maint_center_images' 			=> $_POST['mmode_center_images'], 
	'bps_maint_background_color' 		=> $_POST['mmode_background_color'], 
	'bps_maint_show_visitor_ip' 		=> $bps_maint_show_visitor_ip, 
	'bps_maint_show_login_link' 		=> $bps_maint_show_login_link, 
	'bps_maint_dashboard_reminder' 		=> $bps_maint_dashboard_reminder, 
	'bps_maint_log_visitors' 			=> $bps_maint_log_visitors, 
	'bps_maint_countdown_email' 		=> $bps_maint_countdown_email, 
	'bps_maint_email_to' 				=> $_POST['mmode_email_to'], 
	'bps_maint_email_from' 				=> $_POST['mmode_email_from'], 
	'bps_maint_email_cc' 				=> $_POST['mmode_email_cc'], 
	'bps_maint_email_bcc' 				=> $_POST['mmode_email_bcc'], 
	'bps_maint_mu_entire_site' 			=> $bps_maint_mu_entire_site, 
	'bps_maint_mu_subsites_only' 		=> $bps_maint_mu_subsites_only
	);
	
		foreach( $BPS_Options as $key => $value ) {
			update_option('bulletproof_security_options_maint_mode', $BPS_Options);
		}	

	// Get the new saved/updated DB option values for Form processing with current values
	$MMoptions = get_option('bulletproof_security_options_maint_mode');
	$bps_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance.php';
	$bps_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values.php';
	$bps_maint_time = time() + ( $MMoptions['bps_maint_time'] * 60 );
	
	if ( is_multisite() ) {
		
		if ( is_subdomain_install() ) {
		
			$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );	
	
		} else {
	
			$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
		}
	
		$bps_maintenance_values_network = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values-'.$subsite_remove_slashes.'.php';
		$bps_maintenance_values_network_ARQ = WP_CONTENT_DIR . '/bps-backup/autorestore/wp-content/plugins/bulletproof-security/admin/htaccess/bps-maintenance-values-'.$subsite_remove_slashes.'.php';
		$subsite_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php';
		$subsite_maintenance_file_ARQ = WP_CONTENT_DIR . '/bps-backup/autorestore/wp-content/plugins/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php';	
	
	}	

	if ( is_multisite() && $blog_id == '1' ) {

		if ( ! is_subdomain_install() ) {
			
			$primary_site_uri_path = $current_blog->path;
		
		} else {
			
			$primary_site_uri_path = array_shift( explode( "." , str_replace( 'www.', "", $current_blog->domain ) ) );		
		}
		
	} else {
		
		$primary_site_uri_path = '';
	}

$bps_maint_content = '<?php'."\n".'# BEGIN BPS MAINTENANCE MODE'."\n"
.'$bps_maint_countdown_timer = \''.$MMoptions['bps_maint_countdown_timer'].'\';'."\n"
.'$bps_maint_countdown_timer_color = \''.$MMoptions['bps_maint_countdown_timer_color'].'\';'."\n"
.'$bps_maint_time = \''.$bps_maint_time.'\';'."\n"
.'$bps_maint_retry_after = \''.$MMoptions['bps_maint_retry_after'].'\';'."\n"
.'$bps_maint_text = "'.str_replace( array("\&#039;", "\'") , "'", htmlspecialchars_decode( $MMoptions['bps_maint_text'], ENT_QUOTES) ).'";'."\n"
.'$bps_maint_background_images = \''.$MMoptions['bps_maint_background_images'].'\';'."\n"
.'$bps_maint_center_images = \''.$MMoptions['bps_maint_center_images'].'\';'."\n"
.'$bps_maint_background_color = \''.$MMoptions['bps_maint_background_color'].'\';'."\n"
.'$bps_maint_show_visitor_ip = \''.$MMoptions['bps_maint_show_visitor_ip'].'\';'."\n"
.'$bps_maint_show_login_link = \''.$MMoptions['bps_maint_show_login_link'].'\';'."\n"
.'$bps_maint_login_link = \''.get_site_url().'/wp-admin/' .'\';'."\n"
.'$bps_maint_log_visitors = \''.$MMoptions['bps_maint_log_visitors'].'\';'."\n"
.'$bps_maint_countdown_email = \''.$MMoptions['bps_maint_countdown_email'].'\';'."\n"
.'$bps_maint_email_to = \''.$MMoptions['bps_maint_email_to'].'\';'."\n"
.'$bps_maint_email_from = \''.$MMoptions['bps_maint_email_from'].'\';'."\n"
.'$bps_maint_email_cc = \''.$MMoptions['bps_maint_email_cc'].'\';'."\n"
.'$bps_maint_email_bcc = \''.$MMoptions['bps_maint_email_bcc'].'\';'."\n"
.'# BEGIN BPS MAINTENANCE MODE PRIMARY SITE'."\n"
.'$all_sites = \''.$MMoptions['bps_maint_mu_entire_site'].'\';'."\n"
.'$all_subsites = \''.$MMoptions['bps_maint_mu_subsites_only'].'\';'."\n"
.'$primary_site_uri = \''.$primary_site_uri_path.'\';'."\n"
.'# END BPS MAINTENANCE MODE PRIMARY SITE'."\n"
.'# END BPS MAINTENANCE MODE'."\n".'?>';

	if ( is_multisite() && $blog_id != 1 ) {
		
		$bps_maintenance_file_include = '/#\sBEGIN\sBPS\sINCLUDE(\s*(.*)){3}\s*#\sEND\sBPS\sINCLUDE/';
		
		if ( @copy($bps_maintenance_file, $subsite_maintenance_file) ) {
			$stringReplaceMaint = file_get_contents($subsite_maintenance_file);
		}
		
		if ( preg_match($bps_maintenance_file_include, $stringReplaceMaint, $matches ) ) {
			
			$stringReplaceMaint = preg_replace('/#\sBEGIN\sBPS\sINCLUDE(\s*(.*)){3}\s*#\sEND\sBPS\sINCLUDE/', "# BEGIN BPS INCLUDE\nif ( file_exists( dirname( __FILE__ ) . '/bps-maintenance-values-$subsite_remove_slashes.php' ) ) {\ninclude( dirname( __FILE__ ) . '/bps-maintenance-values-$subsite_remove_slashes.php' );\n}\n# END BPS INCLUDE", $stringReplaceMaint);
		}		

		if ( file_put_contents( $subsite_maintenance_file, $stringReplaceMaint ) ) {
			// ARQ condition not used in BPS free
		}

		@copy($bps_maintenance_values, $bps_maintenance_values_network);
		
		$stringReplace = file_get_contents($bps_maintenance_values_network);
		$stringReplace = $bps_maint_content;

		if ( file_put_contents( $bps_maintenance_values_network, $stringReplace ) ) {
    		
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Success! Your Options have been saved and your Maintenance Mode Form has been created successfully! Click the Preview button to preview your Website Under Maintenance page. To Enable/Turn On Maintenance Mode click the Turn On button.', 'bulletproof-security').'</strong></font>';
			echo $text;		
			echo $bps_bottomDiv;
		
		} else {
		
			echo $bps_topDiv;
    		$text = '<font color="#fb0101"><strong>'.__('The file ', 'bulletproof-security').$bps_maintenance_values_network.__(' is not writable or does not exist.', 'bulletproof-security').'</strong></font><br><strong>'.__('Check that the file exists in the /bulletproof-security/admin/htaccess/ master folder. If this is not the problem ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/" target="_blank">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for assistance.', 'bulletproof-security').'</strong>';
			echo $text;		
			echo $bps_bottomDiv;
		}	
	
	} else {
	
		$stringReplace = file_get_contents($bps_maintenance_values);
		$stringReplace = $bps_maint_content;
		
		if ( file_put_contents( $bps_maintenance_values, $stringReplace ) ) {
    		
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Success! Your Options have been saved and your Maintenance Mode Form has been created successfully! Click the Preview button to preview your Website Under Maintenance page. To Enable/Turn On Maintenance Mode click the Turn On button.', 'bulletproof-security').'</strong></font>';
			echo $text;		
			echo $bps_bottomDiv;
		
		} else {
		
			echo $bps_topDiv;
    $text = '<font color="#fb0101"><strong>'.__('The file ', 'bulletproof-security').$bps_maintenance_values.__(' is not writable or does not exist.', 'bulletproof-security').'</strong></font><br><strong>'.__('Check that the bps-maintenance-values.php file exists in the /bulletproof-security/admin/htaccess/ master folder. If this is not the problem ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/" target="_blank">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for assistance.', 'bulletproof-security').'</strong>';
			echo $text;		
			echo $bps_bottomDiv;
		}
	}
}
}

$scrolltommode1 = isset($_REQUEST['scrolltommode1']) ? (int) $_REQUEST['scrolltommode1'] : 0;
//$scrolltommode2 = isset($_REQUEST['scrolltommode2']) ? (int) $_REQUEST['scrolltommode2'] : 0;

// MMODE Background Image Paths
$background_image_url = plugins_url('/bulletproof-security/images/');
$blackHL = $background_image_url . 'black-honeycomb-large.png';
$blackHLG = $background_image_url . 'black-honeycomb-large-grey-line.png';
$blackMS = $background_image_url . 'black-mesh-small.png';
$blackMSG = $background_image_url . 'black-mesh-small-grey-line.png';
$blueHL = $background_image_url . 'blue-honeycomb-large.png';
$blueMS = $background_image_url . 'blue-mesh-small.png';
$brownHL = $background_image_url . 'brown-honeycomb-large.png';
$brownMS = $background_image_url . 'brown-mesh-small.png';
$greenHL = $background_image_url . 'green-honeycomb-large.png';
$greenMS = $background_image_url . 'green-mesh-small.png';
$grayHL = $background_image_url . 'grey-honeycomb-large.png';
$grayMS = $background_image_url . 'grey-mesh-small.png';
$orangeHL = $background_image_url . 'orange-honeycomb-large.png';
$orangeMS = $background_image_url . 'orange-mesh-small.png';
$purpleHL = $background_image_url . 'purple-honeycomb-large.png';
$purpleMS = $background_image_url . 'purple-mesh-small.png';
$redHL = $background_image_url . 'red-burgundy-honeycomb-large.png';
$redMS = $background_image_url . 'red-burgundy-mesh-small.png';
$yellowHL = $background_image_url . 'yellow-honeycomb-large.png';
$yellowMS = $background_image_url . 'yellow-mesh-small.png';

// MMODE Center Image Paths
$basicBlack = $background_image_url . 'basic-black-center.png';
$blackVeins = $background_image_url . 'black-veins-center.png';
$blueGlass = $background_image_url . 'blue-glass-center.png';
$brushedMetal = $background_image_url . 'brush-metal-stamped-center.png';
$chrome = $background_image_url . 'chrome-center.png';
$chromeSlick = $background_image_url . 'slick-chrome-center.png';
$fire = $background_image_url . 'fire-center.png';
$gunMetal = $background_image_url . 'gun-metal-center.png';
$mercury = $background_image_url . 'mercury-center.png';
$smoke = $background_image_url . 'smoke-center.png';
$stripedCone = $background_image_url . 'striped-cone-center.png';
$swampBevel = $background_image_url . 'swamp-bevel-center.png';
$toy = $background_image_url . 'toy-center.png';
$waterReflection = $background_image_url . 'water-reflection-center.png';
$woodGrain = $background_image_url . 'wood-grain-center.png';

// Get Real IP address & 3 Octets - USE EXTREME CAUTION!!!
// Will display an IPv6 IP address as Current IP Address so not going to do anything additional with that for now
function bps_get_proxy_real_ip_address_maint() {
if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
	
	$pattern = "/\d{1,3}\.\d{1,3}\.\d{1,3}\./";
	
	if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = esc_html( $_SERVER['HTTP_CLIENT_IP'] );
		$octets_ip = preg_match( $pattern, $_SERVER['HTTP_CLIENT_IP'], $matches );
		echo '<font color="#2ea2cc" style="font-size:14px;"><strong>'.__('Your Current IP Address: ', 'bulletproof-security').$ip.'<br>'.__('Recommended IP Address: ', 'bulletproof-security');
		print_r($matches[0]);
		echo '</strong></font><br>';
	
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = esc_html( $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$octets_ip = preg_match( $pattern, $_SERVER['HTTP_X_FORWARDED_FOR'], $matches );
		echo '<font color="#2ea2cc" style="font-size:14px;"><strong>'.__('Your Current IP Address: ', 'bulletproof-security').$ip.'<br>'.__('Recommended IP Address: ', 'bulletproof-security');
		print_r($matches[0]);
		echo '</strong></font><br>';
	
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = esc_html( $_SERVER['REMOTE_ADDR'] );
		$octets_ip = preg_match( $pattern, $_SERVER['REMOTE_ADDR'], $matches );
		echo '<font color="#2ea2cc" style="font-size:14px;"><strong>'.__('Your Current IP Address: ', 'bulletproof-security').$ip.'<br>'.__('Recommended IP Address: ', 'bulletproof-security');
		print_r($matches[0]);
		echo '</strong></font><br>';	
	}
}
}	
?>

<div id="Maintenance-Mode" style="position:relative;top:0px;left:0px;margin:0px 0px 0px 0px;">

<form name="bpsMaintenanceMode" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/maintenance/maintenance.php' ); ?>" method="post">
<?php 
	wp_nonce_field('bpsMaintenanceMode'); 
	bpsPro_maintenance_mode_values_form();
	$MMoptions = get_option('bulletproof_security_options_maint_mode');
	
	$bps_maint_text = ! isset($MMoptions['bps_maint_text']) ? '' : $MMoptions['bps_maint_text'];
	$bps_maint_countdown_timer_color = ! isset($MMoptions['bps_maint_countdown_timer_color']) ? '' : $MMoptions['bps_maint_countdown_timer_color'];
	$bps_maint_time = isset($MMoptions['bps_maint_time']) ? esc_html($MMoptions['bps_maint_time']) : esc_html('');
	$bps_maint_retry_after = isset($MMoptions['bps_maint_retry_after']) ? esc_html($MMoptions['bps_maint_retry_after']) : esc_html('');
	$bps_maint_ip_allowed = isset($MMoptions['bps_maint_ip_allowed']) ? esc_html($MMoptions['bps_maint_ip_allowed']) : esc_html('');
	$bps_maint_background_images = ! isset($MMoptions['bps_maint_background_images']) ? '' : $MMoptions['bps_maint_background_images'];
	$bps_maint_center_images = ! isset($MMoptions['bps_maint_center_images']) ? '' : $MMoptions['bps_maint_center_images'];
	$bps_maint_background_color = ! isset($MMoptions['bps_maint_background_color']) ? '' : $MMoptions['bps_maint_background_color'];
	$bps_maint_email_to = isset($MMoptions['bps_maint_email_to']) ? esc_html($MMoptions['bps_maint_email_to']) : esc_html('');
	$bps_maint_email_from = isset($MMoptions['bps_maint_email_to']) ? esc_html($MMoptions['bps_maint_email_to']) : esc_html('');
	$bps_maint_email_cc = isset($MMoptions['bps_maint_email_to']) ? esc_html($MMoptions['bps_maint_email_to']) : esc_html('');
	$bps_maint_email_bcc = isset($MMoptions['bps_maint_email_to']) ? esc_html($MMoptions['bps_maint_email_to']) : esc_html('');
?>

<div id="bps-accordion-3" class="bps-accordion-main-2" style="">
<h3><?php _e('MMode Editor', 'bulletproof-security'); ?></h3>
<div id="mmode-accordion-inner">

  	<label for="mmode"><?php _e('Maintenance Mode Text, CSS Style Code, Images, Videos Displayed To Website Visitors:', 'bulletproof-security'); ?></label><br />
    <label for="mmode"><?php _e('Click the Maintenance Mode Guide link above for CSS Code, Image & Video Embed examples.', 'bulletproof-security'); ?></label><br /><br />
    
    <!-- Note: wp_editor/TinyMCE causes XAMPP Apache server crash: XAMPP: 1.8.1, pcre.dll, PHP 5.4.7 (VC9 X86 32bit thread safe) + PEAR -->
    <div class="mmode-tinymce">
	<?php wp_editor( stripslashes( htmlspecialchars_decode( $bps_maint_text, ENT_QUOTES ) ), 'bpscustomeditor' ); ?><br />
    </div> 

</div>
  
<h3><?php _e('MMode Option Settings', 'bulletproof-security'); ?></h3>
<div id="mmode-accordion-inner">
    
    <input type="checkbox" name="mmode_countdown_timer" style="margin-top:5px;" value="1" <?php if ( empty( $MMoptions['bps_maint_countdown_timer'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_countdown_timer'], 1 ); } ?> /><label for="mmode"><?php _e('Enable Countdown Timer', 'bulletproof-security'); ?></label><br /><br />
    
    <label for="mmode"><?php _e('Countdown Timer Text Color:', 'bulletproof-security'); ?></label><br />
<select name="mmode_countdown_timer_color" class="form-300">
<option value="lime" <?php selected('lime', $bps_maint_countdown_timer_color); ?>><?php _e('LCD|Lime Green', 'bulletproof-security'); ?></option>
<option value="white" <?php selected('white', $bps_maint_countdown_timer_color); ?>><?php _e('White', 'bulletproof-security'); ?></option>
<option value="silver" <?php selected('silver', $bps_maint_countdown_timer_color); ?>><?php _e('Silver', 'bulletproof-security'); ?></option>
<option value="gray" <?php selected('gray', $bps_maint_countdown_timer_color); ?>><?php _e('Gray', 'bulletproof-security'); ?></option>
</select><br /><br />

    <label for="mmode"><?php _e('Maintenance Mode Time (in Minutes):', 'bulletproof-security'); ?></label><br />
    <label for="mmode"><?php _e('Example: 10 = 10 minutes, 180 = 3 hours, 1440 = 24 hours.', 'bulletproof-security'); ?></label><br />
    <input type="text" name="mmode_time" class="regular-text-250" value="<?php echo $bps_maint_time; ?>" /><br /><br />
    
    <label for="mmode"><?php _e('Header Retry-After (enter the same time as Maintenance Mode Time above):', 'bulletproof-security'); ?></label><br />
    <label for="mmode"><?php _e('Example: 10 = 10 minutes, 180 = 3 hours, 1440 = 24 hours.', 'bulletproof-security'); ?></label><br />
    <input type="text" name="mmode_retry_after" class="regular-text-250" value="<?php echo $bps_maint_retry_after; ?>" /><br /><br />   
     
	<input type="checkbox" name="mmode_frontend" value="1" <?php if ( empty( $MMoptions['bps_maint_frontend'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_frontend'], 1 ); } ?> /><label for="mmode"><?php _e('Enable FrontEnd Maintenance Mode', 'bulletproof-security'); ?></label><br /><br />    
    
<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 0px 0px;"></div>'; } else { ?>
	
    <div id="mmode-caution">
    <?php $text = '<font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font><font color="blue">'.__('You MUST enter Your Current IP Address or the', 'bulletproof-security').'<br>'.__('Recommended IP Address if you Enable BackEnd Maintenance Mode', 'bulletproof-security').'<br>'.__('or you will be locked out of your WordPress Dashboard.', 'bulletproof-security').'</font>'; echo $text; ?></div>
    <input type="checkbox" name="mmode_backend" value="1" <?php if ( empty( $MMoptions['bps_maint_backend'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_backend'], 1 ); } ?> /><label for="mmode"><?php _e('Enable BackEnd Maintenance Mode ', 'bulletproof-security'); ?></label><br /><br />        

<?php } ?>    

    <!-- important note: in a text area you cannot leave whitespace within the form code or that whitespace will be echoed -->
	<label for="mmode"><?php _e('Maintenance Mode IP Address Whitelist Text Box:', 'bulletproof-security'); ?></label><br />
	<div id="mmode-small-text">
	<span class="mmode-small-text">
	<?php _e('Enter The IP Address That Can View The Website Normally (not in Maintenance Mode).', 'bulletproof-security'); ?><br />
	<?php _e('Enter Multiple IP addresses separated by a comma and a single space.', 'bulletproof-security'); ?><br />
	<?php _e('Example IPv4 IP Addresses: 100.99.88.77, 200.66.55.44, 44.33.22.1', 'bulletproof-security'); ?><br />
	<?php _e('Example IPv6 IP Addresses: 0:0:0:0:0:ffff:6463:584d, 0:0:0:0:0:ffff:c842:372c', 'bulletproof-security'); ?><br />    
	</span>
	</div>    
	
	<?php bps_get_proxy_real_ip_address_maint(); ?>
	
    <input type="hidden" name="scrolltommode1" id="scrolltommode1" value="<?php echo esc_html( $scrolltommode1 ); ?>" />
    <textarea class="PFW-Allow-From-Text-Area" name="mmode_ip_allowed" id="mmode_ip_allowed" tabindex="1"><?php echo trim( $bps_maint_ip_allowed, ", \t\n\r"); ?></textarea><br /><br />

    <label for="mmode"><?php _e('Background Images:', 'bulletproof-security'); ?></label><br />
<select name="mmode_background_images" class="form-300">
<option value="0" <?php selected('0', $bps_maint_background_images); ?>><?php _e('No Background Image', 'bulletproof-security'); ?></option>
<option value="<?php echo $blackHL; ?>" <?php selected($blackHL, $bps_maint_background_images); ?>><?php _e('Black Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $blackHLG; ?>" <?php selected($blackHLG, $bps_maint_background_images); ?>><?php _e('Black Honeycomb Large Grey Line', 'bulletproof-security'); ?></option>
<option value="<?php echo $blackMS; ?>" <?php selected($blackMS, $bps_maint_background_images); ?>><?php _e('Black Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $blackMSG; ?>" <?php selected($blackMSG, $bps_maint_background_images); ?>><?php _e('Black Mesh Small Grey Line', 'bulletproof-security'); ?></option>
<option value="<?php echo $blueHL; ?>" <?php selected($blueHL, $bps_maint_background_images); ?>><?php _e('Blue Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $blueMS; ?>" <?php selected($blueMS, $bps_maint_background_images); ?>><?php _e('Blue Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $brownHL; ?>" <?php selected($brownHL, $bps_maint_background_images); ?>><?php _e('Brown Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $brownMS; ?>" <?php selected($brownMS, $bps_maint_background_images); ?>><?php _e('Brown Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $greenHL; ?>" <?php selected($greenHL, $bps_maint_background_images); ?>><?php _e('Green Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $greenMS; ?>" <?php selected($greenMS, $bps_maint_background_images); ?>><?php _e('Green Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $grayHL; ?>" <?php selected($grayHL, $bps_maint_background_images); ?>><?php _e('Gray Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $grayMS; ?>" <?php selected($grayMS, $bps_maint_background_images); ?>><?php _e('Gray Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $orangeHL; ?>" <?php selected($orangeHL, $bps_maint_background_images); ?>><?php _e('Orange Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $orangeMS; ?>" <?php selected($orangeMS, $bps_maint_background_images); ?>><?php _e('Orange Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $purpleHL; ?>" <?php selected($purpleHL, $bps_maint_background_images); ?>><?php _e('Purple Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $purpleMS; ?>" <?php selected($purpleMS, $bps_maint_background_images); ?>><?php _e('Purple Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $redHL; ?>" <?php selected($redHL, $bps_maint_background_images); ?>><?php _e('Red|Burgundy Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $redMS; ?>" <?php selected($redMS, $bps_maint_background_images); ?>><?php _e('Red|Burgundy Mesh Small', 'bulletproof-security'); ?></option>
<option value="<?php echo $yellowHL; ?>" <?php selected($yellowHL, $bps_maint_background_images); ?>><?php _e('Yellow Honeycomb Large', 'bulletproof-security'); ?></option>
<option value="<?php echo $yellowMS; ?>" <?php selected($yellowMS, $bps_maint_background_images); ?>><?php _e('Yellow Mesh Small', 'bulletproof-security'); ?></option>
</select><br /><br />    

    <label for="mmode"><?php _e('Center Images:', 'bulletproof-security'); ?></label><br />
<select name="mmode_center_images" class="form-300">
<option value="0" <?php selected('0', $bps_maint_center_images); ?>><?php _e('No Center Image', 'bulletproof-security'); ?></option>
<option value="<?php echo $basicBlack; ?>" <?php selected($basicBlack, $bps_maint_center_images); ?>><?php _e('Basic Black', 'bulletproof-security'); ?></option>
<option value="<?php echo $blackVeins; ?>" <?php selected($blackVeins, $bps_maint_center_images); ?>><?php _e('Black Veins', 'bulletproof-security'); ?></option>
<option value="<?php echo $blueGlass; ?>" <?php selected($blueGlass, $bps_maint_center_images); ?>><?php _e('Blue Glass', 'bulletproof-security'); ?></option>
<option value="<?php echo $brushedMetal; ?>" <?php selected($brushedMetal, $bps_maint_center_images); ?>><?php _e('Brushed Metal Stamped', 'bulletproof-security'); ?></option>
<option value="<?php echo $chrome; ?>" <?php selected($chrome, $bps_maint_center_images); ?>><?php _e('Chrome', 'bulletproof-security'); ?></option>
<option value="<?php echo $chromeSlick; ?>" <?php selected($chromeSlick, $bps_maint_center_images); ?>><?php _e('Chrome Slick', 'bulletproof-security'); ?></option>
<option value="<?php echo $fire; ?>" <?php selected($fire, $bps_maint_center_images); ?>><?php _e('Fire', 'bulletproof-security'); ?></option>
<option value="<?php echo $gunMetal; ?>" <?php selected($gunMetal, $bps_maint_center_images); ?>><?php _e('Gun Metal', 'bulletproof-security'); ?></option>
<option value="<?php echo $mercury; ?>" <?php selected($mercury, $bps_maint_center_images); ?>><?php _e('Mercury', 'bulletproof-security'); ?></option>
<option value="<?php echo $smoke; ?>" <?php selected($smoke, $bps_maint_center_images); ?>><?php _e('Smoke', 'bulletproof-security'); ?></option>
<option value="<?php echo $stripedCone; ?>" <?php selected($stripedCone, $bps_maint_center_images); ?>><?php _e('Striped Cone', 'bulletproof-security'); ?></option>
<option value="<?php echo $swampBevel; ?>" <?php selected($swampBevel, $bps_maint_center_images); ?>><?php _e('Swamp Bevel', 'bulletproof-security'); ?></option>
<option value="<?php echo $toy; ?>" <?php selected($toy, $bps_maint_center_images); ?>><?php _e('Toy', 'bulletproof-security'); ?></option>
<option value="<?php echo $waterReflection; ?>" <?php selected($waterReflection, $bps_maint_center_images); ?>><?php _e('Water Reflection', 'bulletproof-security'); ?></option>
<option value="<?php echo $woodGrain; ?>" <?php selected($woodGrain, $bps_maint_center_images); ?>><?php _e('Wood Grain', 'bulletproof-security'); ?></option>
</select><br /><br />    

    <label for="mmode"><?php _e('Background Colors (If not using a Background Image):', 'bulletproof-security'); ?></label><br />
<select name="mmode_background_color" class="form-300">
<option value="white" <?php selected('white', $bps_maint_background_color); ?>><?php _e('No Background Color', 'bulletproof-security'); ?></option>
<option value="white" <?php selected('white', $bps_maint_background_color); ?>><?php _e('White', 'bulletproof-security'); ?></option>
<option value="black" <?php selected('black', $bps_maint_background_color); ?>><?php _e('Black', 'bulletproof-security'); ?></option>
<option value="gray" <?php selected('gray', $bps_maint_background_color); ?>><?php _e('Gray', 'bulletproof-security'); ?></option>
</select><br /><br />

    <input type="checkbox" name="mmode_visitor_ip" value="1" <?php if ( empty( $MMoptions['bps_maint_show_visitor_ip'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_show_visitor_ip'], 1 ); } ?> /><label for="mmode"><?php _e('Display Visitor IP Address', 'bulletproof-security'); ?></label><br /><br />
	
    <input type="checkbox" name="mmode_login_link" value="1" <?php if ( empty( $MMoptions['bps_maint_show_login_link'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_show_login_link'], 1 ); } ?> /><label for="mmode"><?php _e('Display Admin|Login Link', 'bulletproof-security'); ?></label><br /><br />

    <input type="checkbox" name="mmode_dashboard_reminder" value="1" <?php if ( empty( $MMoptions['bps_maint_dashboard_reminder'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_dashboard_reminder'], 1 ); } ?> /><label for="mmode"><?php _e('Display Dashboard Reminder Message when site is in Maintenance Mode', 'bulletproof-security'); ?></label><br /><br />

    <input type="checkbox" name="mmode_log_visitors" value="1" <?php if ( empty( $MMoptions['bps_maint_log_visitors'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_log_visitors'], 1 ); } ?> /><label for="mmode"><?php _e('Enable Visitor Logging', 'bulletproof-security'); ?></label><br /><br />

	<input type="checkbox" name="mmode_countdown_email" value="1" <?php if ( empty( $MMoptions['bps_maint_countdown_email'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_countdown_email'], 1 ); } ?> /><label for="mmode"><?php _e('Send Email Reminder when Maintenance Mode Countdown Timer has completed', 'bulletproof-security'); ?></label><br /><br />
    
    <strong><label for="mmode-email"><?php _e('Send Countdown Timer Email To:', 'bulletproof-security'); ?> </label></strong><br />
    <input type="text" name="mmode_email_to" class="regular-text-250" value="<?php echo $bps_maint_email_to; ?>" /><br />
    <strong><label for="mmode-email"><?php _e('Send Countdown Timer Email From:', 'bulletproof-security'); ?> </label></strong><br />
    <input type="text" name="mmode_email_from" class="regular-text-250" value="<?php echo $bps_maint_email_from; ?>" /><br />
    <strong><label for="mmode-email"><?php _e('Send Countdown Timer Email Cc:', 'bulletproof-security'); ?> </label></strong><br />
    <input type="text" name="mmode_email_cc" class="regular-text-250" value="<?php echo $bps_maint_email_cc; ?>" /><br />
    <strong><label for="mmode-email"><?php _e('Send Countdown Timer Email Bcc:', 'bulletproof-security'); ?> </label></strong><br />
    <input type="text" name="mmode_email_bcc" class="regular-text-250" value="<?php echo $bps_maint_email_bcc; ?>" /><br />

</div>

<h3><?php _e('MMode Network|Multisite Options', 'bulletproof-security'); ?></h3>
<div id="mmode-accordion-inner">

	<div id="mmode-network-text" style="font-size:16px;font-weight:bold;"><?php _e('Network|Multisite Primary Site Options ONLY', 'bulletproof-security'); ?></div> 

<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 10px 0px;"></div>'; } else { ?>

 	<strong><label for="mmode" style="color:#2ea2cc;"><?php _e('Click the Maintenance Mode Read Me help button for the steps to use these special options:', 'bulletproof-security'); ?></label></strong><br /><br />
    <input type="checkbox" name="mmode_mu_entire_site" value="1" <?php if ( empty( $MMoptions['bps_maint_mu_entire_site'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_mu_entire_site'], 1 ); } ?> /><label for="mmode"><?php _e('Put The Primary Site And All Subsites In Maintenance Mode', 'bulletproof-security'); ?></label><br /><br />

    <input type="checkbox" name="mmode_mu_subsites_only" value="1" <?php if ( empty( $MMoptions['bps_maint_mu_subsites_only'] ) ) { echo ''; } else { checked( $MMoptions['bps_maint_mu_subsites_only'], 1 ); } ?> /><label for="mmode"><?php _e('Put All Subsites In Maintenance Mode, But Not The Primary Site', 'bulletproof-security'); ?></label><br /><br />   
    
<?php } ?> 

</div>
</div>

<div id="MMode-button-position" style="position:relative;bottom:0px;left:0px;">
    <input type="submit" name="Submit-Maintenance-Mode-Form" class="button bps-button" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK Saves your Options/Settings to your Database and also creates your Maintenance Mode page. Click the Preview button to preview your Maintenance Mode page. After previewing your Maintenance Mode page click the Turn On button to enable Maintenance Mode on your website.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</div>

</form>
</div> 

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsMaintenanceMode').submit(function(){ $('#scrolltommode1').val( $('#mmode_ip_allowed').scrollTop() ); });
	$('#mmode_ip_allowed').scrollTop( $('#scrolltommode1').val() );
});
/* ]]> */
</script>

<?php
// Maintenance Mode Preview - check Referer
if ( isset( $_POST['maintenance-mode-preview-submit'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_maintenance_preview' );
}
?>

<div id="MMode-button-position">

<?php
	
	if ( is_multisite() && $blog_id != 1 ) { 

		if ( is_subdomain_install() ) {
		
			$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );	
	
		} else {
	
			$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
		}
?>

<form name="MaintenanceModePreview" method="post" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/maintenance/maintenance.php' ); ?>" target="" onSubmit="window.open('<?php echo plugins_url('/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php'); ?>','','scrollbars=yes,menubar=yes,width=800,height=600,resizable=yes,status=yes,toolbar=yes')">
<?php wp_nonce_field('bulletproof_security_maintenance_preview'); ?>
<p class="submit" style="float:left;margin:15px 10px 0px 0px;">
<input type="submit" name="maintenance-mode-preview-submit" class="button bps-button" style="width:72px;height:auto;white-space:normal" value="<?php esc_attr_e('Preview', 'bulletproof-security') ?>" />
</p>
</form>

<?php } else { ?>
		
<form name="MaintenanceModePreview" method="post" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/maintenance/maintenance.php' ); ?>" target="" onSubmit="window.open('<?php echo plugins_url('/bulletproof-security/admin/htaccess/bps-maintenance.php'); ?>','','scrollbars=yes,menubar=yes,width=800,height=600,resizable=yes,status=yes,toolbar=yes')">
<?php wp_nonce_field('bulletproof_security_maintenance_preview'); ?>
<p class="submit" style="float:left;margin:15px 10px 0px 0px;">
<input type="submit" name="maintenance-mode-preview-submit" class="button bps-button" style="width:72px;height:auto;white-space:normal" value="<?php esc_attr_e('Preview', 'bulletproof-security') ?>" />
</p>
</form>

<?php } ?>

<form name="bpsMaintenanceModeOn" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/maintenance/maintenance.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mmode_on'); ?>
<p class="submit" style="float:left;margin:15px 10px 0px 0px;">
<input type="submit" name="Submit-maintenance-mode-on" class="button bps-button" style="width:72px;height:auto;white-space:normal" value="<?php esc_attr_e('Turn On', 'bulletproof-security') ?>" />
</p>
</form>

<form name="bpsMaintenanceModeOff" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/maintenance/maintenance.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mmode_off'); ?>
<p class="submit" style="float:left;margin:15px 10px 0px 0px;">
<input type="submit" name="Submit-maintenance-mode-off" class="button bps-button" style="width:72px;height:auto;white-space:normal" value="<?php esc_attr_e('Turn Off', 'bulletproof-security') ?>" />
</p>
</form>

</div>

<?php
// Maintenance Mode Single/GWIOD: Turn On - Frontend & Backend Maintenance Modes are independent of each other
function bpsPro_mmode_single_gwiod_turn_on() {
global $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index.php';
$bps_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance.php';
$bps_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
$pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$format_error_1 = '/,(\s){2,20}/'; // 2 to 20 extra whitespaces
$format_error_2 = '/,[^\s]/'; // no whitespaces between commas
		
	if ( $MMoptions['bps_maint_ip_allowed'] == '' ) {	
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You did not enter an IP Address in the Maintenance Mode IP Address Whitelist Text Box.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	}	
	
	// IP Address Text Box Error Checking: 2 to 20 extra whitespaces, no whitespace between commas, no commas
	// The 3 dot error check is only valid for IPv4 IP addresses and will not match (do nothing) if the IP address is an IPv6 IP address
	if ( substr_count( $MMoptions['bps_maint_ip_allowed'], '.' ) > 3 && substr_count( $MMoptions['bps_maint_ip_allowed'], ',' ) <= 0 || preg_match( $format_error_1, $MMoptions['bps_maint_ip_allowed'] ) || preg_match( $format_error_2, $MMoptions['bps_maint_ip_allowed'] ) ) {
		
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('IP Address Format Error: You have entered multiple IP Addresses using an incorrect Format.', 'bulletproof-security').'</font><br>'.__('The correct IP Address Format is: IP Address comma single space. Example: 100.99.88.77, 200.66.55.44, 44.33.22.1 or 100.99.88., 200.66.55., 44.33.22. if you are using the recommended 3 octet IP addresses.', 'bulletproof-security').'<br>'.__('Correct the IP Address Format and click the Save Options button again. If you have an IPv6 IP address use the same general format as an IPv4 IP address - comma single space.', 'bulletproof-security').'</strong>';
		echo $text;		
		echo $bps_bottomDiv;
	return;		
	}	
	
	// Frontend Maintenance Mode
	// Single/GWIOD: if a user unchecks frontend mmode, saves options again and then clicks turn on then frontend mmode needs to be turned off
	if ( $MMoptions['bps_maint_frontend'] != '1' ) {
		bpsPro_mmode_single_gwiod_turn_off_frontend();
	}
	
	if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_frontend'] == '1' ) {
		
		if ( get_option('home') != get_option('siteurl') ) {
			bpsPro_mmode_gwiod_site_root_index_file_on();
		}
		
		$stringReplace = file_get_contents($MMindexMaster);
			
	if ( preg_match($pattern, $stringReplace, $matches ) ) {
			
		$stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/', "# BEGIN BPS MAINTENANCE MODE IP\n".'$bps_maintenance_ip'." = array('".str_replace(', ', "', '", $MMoptions['bps_maint_ip_allowed'])."');\n# END BPS MAINTENANCE MODE IP", $stringReplace);			
			
		if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					
			if ( @$permsIndex == '0400') {
				$lock = '0400';			
			}
			
			if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
				@chmod($root_index_file, 0644);
			}	

			$index_contents = file_get_contents($root_index_file);

		// First click Turn On: backup the WP root index.php file. Second... click Turn On: do not backup the index.php file to master-backups again
		if ( !strpos($index_contents, "BPS MAINTENANCE MODE IP") ) {
			copy( $root_index_file, $root_index_file_backup );			
		} 
			
			// first, second, third clicks...
			@copy($bps_maintenance_values, $root_folder_maintenance_values);
				
			// first click only, but someone may want to modify the Master mmode template file so copy it again
			@copy($bps_maintenance_file, $root_folder_maintenance);

			// first, second, third clicks...
			@copy($MMindexMaster, $root_index_file);
		
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned On.', 'bulletproof-security').'</strong></font>';
				echo $text;
    			echo $bps_bottomDiv;

			if ( $lock == '0400') {	
				@chmod($root_index_file, 0400);
			}
		}
	}
	} // end if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_frontend'] == '1' ) {

	// Backend Maintenance Mode
	// if a user unchecks backend mmode, saves options again and then clicks turn on then backend mmode needs to be turned off
	// .53.6: htaccess Files Disabled
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BackEnd Maintenance Mode is disabled.', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';	
		echo $text;
    	echo $bps_bottomDiv;
	
	} else {
	
	if ( $MMoptions['bps_maint_backend'] != '1' ) {
		bpsPro_mmode_single_gwiod_turn_off_backend();
	}
	
	$MMAllowFromTXT = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode.txt';
	$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
	$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);
	$sapi_type = php_sapi_name();
	# BPS .52.5: new pattern|new IfModule conditions
	$pattern2 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
	$pattern3 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP(.*\s*){8}(Allow(.*)\s*){1,}<\/IfModule>\s*<\/IfModule>\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';	
	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');		

	if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_backend'] == '1' ) {

		if ( @$permsHtaccess == '0404') {
			$lock = '0404';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($wpadminHtaccess, 0644);
		}	
		
		$wpadmin_allow_from = array_filter( explode(', ', trim( $MMoptions['bps_maint_ip_allowed'], ", \t\n\r") ) );
		$allow_whiteList = array();
		
		foreach ( $wpadmin_allow_from as $allow_Key => $allow_Value ) {
			$allow_whiteList[] = 'Allow from '.$allow_Value."\n";
			file_put_contents($MMAllowFromTXT, $allow_whiteList);
		}

		$AllowFromRules = file_get_contents($MMAllowFromTXT);
		$stringReplace = file_get_contents($wpadminHtaccess);
				
		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {

			if ( ! preg_match( $pattern3, $stringReplace, $matches ) ) {
				
				$stringReplace = "\n# BEGIN BPS MAINTENANCE MODE IP\n<IfModule mod_authz_core.c>\nRequire ip ".str_replace( array( ',', ", ", ",  "), "", $MMoptions['bps_maint_ip_allowed'])."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\n" . $AllowFromRules . "</IfModule>\n</IfModule>\n# END BPS MAINTENANCE MODE IP";
				
				file_put_contents($wpadminHtaccess, $stringReplace, FILE_APPEND | LOCK_EX);				
				
			} else {
				
				$stringReplace = preg_replace( $pattern3, "# BEGIN BPS MAINTENANCE MODE IP\n<IfModule mod_authz_core.c>\nRequire ip ".str_replace( array( ',', ", ", ",  "), "", $MMoptions['bps_maint_ip_allowed'])."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\n" . $AllowFromRules . "</IfModule>\n</IfModule>\n# END BPS MAINTENANCE MODE IP", $stringReplace);
				
				file_put_contents($wpadminHtaccess, $stringReplace);		
			}				

		} else { // IfModule No and any other coditions

			if ( ! preg_match( $pattern2, $stringReplace, $matches ) ) {
				
				$stringReplace = "\n# BEGIN BPS MAINTENANCE MODE IP\nOrder Allow,Deny\n" . $AllowFromRules . "# END BPS MAINTENANCE MODE IP";	
				file_put_contents($wpadminHtaccess, $stringReplace, FILE_APPEND | LOCK_EX);				
				
			} else {
				
				$stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/', "# BEGIN BPS MAINTENANCE MODE IP\nOrder Allow,Deny\n" . $AllowFromRules . "# END BPS MAINTENANCE MODE IP", $stringReplace);	

				file_put_contents($wpadminHtaccess, $stringReplace);		
			}			
		}		
	
		if ( $lock == '0404') {	
			@chmod($wpadminHtaccess, 0404);
		}
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('BackEnd Maintenance Mode has been Turned On.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;
	}
	}
}

// Maintenance Mode Network/GWIOD: Turn On - Frontend & Backend Maintenance Modes are independent of each other
// .53.9: BuFix replace subsite site name variable name with dash/hyphen to underscore.
function bpsPro_mmode_network_turn_on() {
global $current_blog, $blog_id, $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index-MU.php';

// Primary Site
$bps_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance.php';
$bps_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';

// Subsites
	if ( is_multisite() && is_subdomain_install() ) {
		
		$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );
		$subsite_replace_chars = str_replace( array( '.', '-' ), "_", $current_blog->domain );	
	
	} else {
	
		$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
		$subsite_replace_chars = str_replace( array( '/', '-' ), "_", $current_blog->path );
	}

$subsite_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php';
$subsite_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values-'.$subsite_remove_slashes.'.php';
$subsite_root_folder_maintenance = ABSPATH . 'bps-maintenance-'.$subsite_remove_slashes.'.php';
$subsite_root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values-'.$subsite_remove_slashes.'.php';

// Regex
$subsite_case_pattern = '/#\sBEGIN\s'.$subsite_replace_chars.'\sCASE\s*((.*)\s*){13}break;\s*#\sEND\s'.$subsite_replace_chars.'\sCASE/';
$subsite_case_ip_pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\s'.$subsite_replace_chars.'\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\s'.$subsite_replace_chars.'\sIP/';
$primary_site_ip_pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sPRIMARY\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sPRIMARY\sIP/';

// Error Checks
$format_error_1 = '/,(\s){2,20}/'; // 2 to 20 extra whitespaces
$format_error_2 = '/,[^\s]/'; // no whitespaces between commas

	if ( $MMoptions['bps_maint_ip_allowed'] == '' ) {	
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You did not enter an IP Address in the Maintenance Mode IP Address Whitelist Text Box.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	}	
	
	// IP Address Text Box Error Checking: 2 to 20 extra whitespaces, no whitespace between commas, no commas
	// The 3 dot error check is only valid for IPv4 IP addresses and will not match (do nothing) if the IP address is an IPv6 IP address
	if ( substr_count( $MMoptions['bps_maint_ip_allowed'], '.' ) > 3 && substr_count( $MMoptions['bps_maint_ip_allowed'], ',' ) <= 0 || preg_match( $format_error_1, $MMoptions['bps_maint_ip_allowed'] ) || preg_match( $format_error_2, $MMoptions['bps_maint_ip_allowed'] ) ) {
		
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('IP Address Format Error: You have entered multiple IP Addresses using an incorrect Format.', 'bulletproof-security').'</font><br>'.__('The correct IP Address Format is: IP Address comma single space. Example: 100.99.88.77, 200.66.55.44, 44.33.22.1 or 100.99.88., 200.66.55., 44.33.22. if you are using the recommended 3 octet IP addresses.', 'bulletproof-security').'<br>'.__('Correct the IP Address Format and click the Save Options button again. If you have an IPv6 IP address use the same general format as an IPv4 IP address - comma single space.', 'bulletproof-security').'</strong>';
		echo $text;		
		echo $bps_bottomDiv;
	return;		
	}	
	
	// Frontend Maintenance Mode
	// Network/Multisite: if a user unchecks frontend mmode, saves options again and then clicks turn on then frontend mmode needs to be turned off
	if ( $MMoptions['bps_maint_frontend'] != '1' ) {
		bpsPro_mmode_network_turn_off_frontend();
	}
	
	if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_frontend'] == '1' ) {
		
		// backup the original WP root index.php file ONLY once the first time mmode is turned On and never again.
		if ( !file_exists($root_index_file_backup) ) {
			@copy( $root_index_file, $root_index_file_backup );			
		} 

	// Primary Network Site
	if ( is_multisite() && $blog_id == 1 ) {

		$stringReplace = file_get_contents($MMindexMaster);

	if ( preg_match( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', $stringReplace, $matches ) ) {
		$stringReplace = preg_replace( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', "# BEGIN PRIMARY SITE STATUS\n\$primary_site_status = 'On';\n# END PRIMARY SITE STATUS", $stringReplace);	
	}
	
		if ( is_subdomain_install() && preg_match( '/switch\s\(\s\$_SERVER\[\'REQUEST_URI\'\]\s\)\s\{/', $stringReplace, $matches ) ) {
			$stringReplace = preg_replace( '/switch\s\(\s\$_SERVER\[\'REQUEST_URI\'\]\s\)\s\{/', 'switch ( $subdomain ) {', $stringReplace);
		}

	if ( preg_match( $primary_site_ip_pattern, $stringReplace, $matches ) ) {
			
		$stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sPRIMARY\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sPRIMARY\sIP/', "# BEGIN BPS MAINTENANCE MODE PRIMARY IP\n		".'$bps_maintenance_ip'." = array('".str_replace(', ', "', '", $MMoptions['bps_maint_ip_allowed'])."');\n		# END BPS MAINTENANCE MODE PRIMARY IP", $stringReplace);			
			
		if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					
			if ( @$permsIndex == '0400') {
				$lock = '0400';			
			}			
			
			if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
				@chmod($root_index_file, 0644);
			}	

			@copy($bps_maintenance_values, $root_folder_maintenance_values);
			@copy($bps_maintenance_file, $root_folder_maintenance);
			@copy($MMindexMaster, $root_index_file);
		
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned On.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;

			// Network GWIOD Site type - process this function after the new index file has been created with file_put_contents
			if ( network_site_url() != get_site_option('siteurl') ) {
				bpsPro_mmode_network_gwiod_site_root_index_file_on();
			}			
			
			if ( $lock == '0400') {	
				@chmod($root_index_file, 0400);
			}
		}
	}
	
	/** Network/Multisite Subsites **/
	// Up to this point / after Save Options for subsites:
	// subsite values and maintenance files have been created & the subsite include: bps-maintenance-values-{subsite-uri}.php 
	// has been created in the subsite maintenance file: bps-maintenance-{subsite-uri}.php
	// the same index master file is used for all sites, each subsite will string replace its ip address array and copy the index file to the root folder again
	
	} else {
		
		$stringReplace = file_get_contents($MMindexMaster);
		$subsite_subdomain_var_value = array_shift( explode( "." , str_replace( 'www.', "", $current_blog->domain ) ) );

		if ( is_multisite() && ! is_subdomain_install() ) {
		
			// Subdirectory site type: Create or update the subsite Status variable with value On in maintenance-mode-index-MU.php
			if ( ! preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {	
			
				$stringReplace = preg_replace('/#\sEND\sSUBSITE\sSTATUS/', "\$$subsite_replace_chars".'_status'." = 'On';\n# END SUBSITE STATUS", $stringReplace);
		
			} else {
		
				$stringReplace = preg_replace( '/\$'.$subsite_remove_slashes.'_status = \'(.*)\';/', "\$$subsite_remove_slashes".'_status'." = 'On';", $stringReplace);		
			}		

			// Create the subsite URI in maintenance-mode-index-MU.php if it does not already exist
			if ( ! preg_match( '/\$'.$subsite_remove_slashes.' = \'\/'.$subsite_remove_slashes.'\/\';/', $stringReplace, $matches ) ) {	
	
				$stringReplace = preg_replace('/#\sEND\sSUBSITE\sURI/', "\$$subsite_replace_chars = '$current_blog->path';\n# END SUBSITE URI", $stringReplace);
			}	
	
			// Create a new subsite Switch case in maintenance-mode-index-MU.php if it does not already exist
			if ( ! preg_match($subsite_case_pattern, $stringReplace, $matches ) ) {
			
			$stringReplace = preg_replace('/default:(\s*(.*)){5}\s*#\sEND\sBPS\sSWITCH\s*\}/', "# BEGIN $subsite_replace_chars CASE\n	case \$$subsite_replace_chars:\n		# BEGIN BPS MAINTENANCE MODE $subsite_replace_chars IP\n		\$bps_maintenance_ip = array('127.0.0.1');\n		# END BPS MAINTENANCE MODE $subsite_replace_chars IP\n		if ( \$all_sites == '1' || \$all_subsites == '1' ) {\n		require( dirname( __FILE__ ) . '/bps-maintenance.php' );\n		} else {\n		if ( in_array( \$_SERVER['REMOTE_ADDR'], \$bps_maintenance_ip ) || in_array( \$matches_three[0], \$bps_maintenance_ip ) || in_array( \$matches_two[0], \$bps_maintenance_ip ) || in_array( \$matches_one[0], \$bps_maintenance_ip ) || \$$subsite_replace_chars".'_status'." == 'Off' ) {\n		require( dirname( __FILE__ ) . '/wp-blog-header.php' );\n		} else {\n		require( dirname( __FILE__ ) . '/bps-maintenance-$subsite_remove_slashes.php' );\n		}\n		}\n		break;\n 	# END $subsite_replace_chars CASE\n	default:\n		if ( \$all_sites == '1' || \$all_subsites == '1' ) {\n		require( dirname( __FILE__ ) . '/bps-maintenance.php' );\n		} else {\n		require( dirname( __FILE__ ) . '/wp-blog-header.php' );\n		}\n	# END BPS SWITCH\n	}", $stringReplace );
			}
		
		} else {
			
			// Subdomain site type: Create or update the subsite Status variable with value On in maintenance-mode-index-MU.php
			if ( ! preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {	
			
				$stringReplace = preg_replace('/#\sEND\sSUBSITE\sSTATUS/', "\$$subsite_replace_chars".'_status'." = 'On';\n# END SUBSITE STATUS", $stringReplace);
		
			} else {
		
				$stringReplace = preg_replace( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', "\$$subsite_replace_chars".'_status'." = 'On';", $stringReplace);		
			}		

			// Create the subsite root domain in maintenance-mode-index-MU.php if it does not already exist
			if ( ! preg_match( '/\$'.$subsite_replace_chars.' = \''.$subsite_subdomain_var_value.'\';/', $stringReplace, $matches ) ) {	
	
				$stringReplace = preg_replace('/#\sEND\sSUBSITE\sURI/', "\$$subsite_replace_chars = '$subsite_subdomain_var_value';\n# END SUBSITE URI", $stringReplace);
			}	
	
			// Create the HTTP_HOST based switch condition instead of REQUEST_URI 
			if ( preg_match( '/switch\s\(\s\$_SERVER\[\'REQUEST_URI\'\]\s\)\s\{/', $stringReplace, $matches ) ) {
				$stringReplace = preg_replace( '/switch\s\(\s\$_SERVER\[\'REQUEST_URI\'\]\s\)\s\{/', 'switch ( $subdomain ) {', $stringReplace);
			}

			// Create a new subsite Switch case in maintenance-mode-index-MU.php if it does not already exist
			if ( ! preg_match( $subsite_case_pattern, $stringReplace, $matches ) ) {
			
			$stringReplace = preg_replace('/default:(\s*(.*)){5}\s*#\sEND\sBPS\sSWITCH\s*\}/', "# BEGIN $subsite_replace_chars CASE\n	case \$$subsite_replace_chars:\n		# BEGIN BPS MAINTENANCE MODE $subsite_replace_chars IP\n		\$bps_maintenance_ip = array('127.0.0.1');\n		# END BPS MAINTENANCE MODE $subsite_replace_chars IP\n		if ( \$all_sites == '1' || \$all_subsites == '1' ) {\n		require( dirname( __FILE__ ) . '/bps-maintenance.php' );\n		} else {\n		if ( in_array( \$_SERVER['REMOTE_ADDR'], \$bps_maintenance_ip ) || in_array( \$matches_three[0], \$bps_maintenance_ip ) || in_array( \$matches_two[0], \$bps_maintenance_ip ) || in_array( \$matches_one[0], \$bps_maintenance_ip ) || \$$subsite_replace_chars".'_status'." == 'Off' ) {\n		require( dirname( __FILE__ ) . '/wp-blog-header.php' );\n		} else {\n		require( dirname( __FILE__ ) . '/bps-maintenance-$subsite_remove_slashes.php' );\n		}\n		}\n		break;\n 	# END $subsite_replace_chars CASE\n	default:\n		if ( \$all_sites == '1' || \$all_subsites == '1' ) {\n		require( dirname( __FILE__ ) . '/bps-maintenance.php' );\n		} else {\n		require( dirname( __FILE__ ) . '/wp-blog-header.php' );\n		}\n	# END BPS SWITCH\n	}", $stringReplace );
			}
		}
	
		// Create the subsite IP addresses array in maintenance-mode-index-MU.php
		if ( preg_match( $subsite_case_ip_pattern, $stringReplace, $matches ) ) {
			
		$stringReplace = preg_replace( $subsite_case_ip_pattern, "# BEGIN BPS MAINTENANCE MODE $subsite_replace_chars IP\n		".'$bps_maintenance_ip'." = array('".str_replace(', ', "', '", $MMoptions['bps_maint_ip_allowed'])."');\n		# END BPS MAINTENANCE MODE $subsite_replace_chars IP", $stringReplace);			
			
			if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					
				if ( @$permsIndex == '0400') {
					$lock = '0400';			
				}				
				
				if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
					@chmod($root_index_file, 0644);
				}	

				@copy($subsite_maintenance_values, $subsite_root_folder_maintenance_values);
				@copy($bps_maintenance_values, $root_folder_maintenance_values);
				@copy($subsite_maintenance_file, $subsite_root_folder_maintenance);
				@copy($bps_maintenance_file, $root_folder_maintenance);
				@copy($MMindexMaster, $root_index_file);
		
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned On.', 'bulletproof-security').'</strong></font>';
				echo $text;
    			echo $bps_bottomDiv;
			
			// Network GWIOD Site type - process this function after the new index file has been created with file_put_contents
			if ( network_site_url() != get_site_option('siteurl') ) {
				bpsPro_mmode_network_gwiod_site_root_index_file_on();
			}
			
			if ( $lock == '0400') {	
				@chmod($root_index_file, 0400);
			}
		}	
	}
	}
	} // end if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_frontend'] == '1' ) {

	// Backend Maintenance Mode - Primary Site ONLY - subsites do not have this option available
	// if a user unchecks backend mmode, saves options again and then clicks turn on then backend mmode needs to be turned off
	// .53.6: htaccess Files Disabled
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BackEnd Maintenance Mode is disabled.', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';	
		echo $text;
    	echo $bps_bottomDiv;
	
	} else {

	if ( is_multisite() && $blog_id == 1 ) {	

	if ( $MMoptions['bps_maint_backend'] != '1' ) {
		bpsPro_mmode_single_gwiod_turn_off_backend();
	}
	
	$MMAllowFromTXT = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode.txt';
	$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
	$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);
	$sapi_type = php_sapi_name();
	# BPS .52.5: new pattern|new IfModule conditions
	$pattern2 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
	$pattern3 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP(.*\s*){8}(Allow(.*)\s*){1,}<\/IfModule>\s*<\/IfModule>\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';	
	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');	
	
	if ( $MMoptions['bps_maint_ip_allowed'] != '' && $MMoptions['bps_maint_backend'] == '1' ) {

		if ( @$permsHtaccess == '0404') {
			$lock = '0404';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($wpadminHtaccess, 0644);
		}	
		
		$wpadmin_allow_from = array_filter( explode(', ', trim( $MMoptions['bps_maint_ip_allowed'], ", \t\n\r") ) );
		$allow_whiteList = array();
		
		foreach ( $wpadmin_allow_from as $allow_Key => $allow_Value ) {
			$allow_whiteList[] = 'Allow from '.$allow_Value."\n";
			file_put_contents($MMAllowFromTXT, $allow_whiteList);
		}

		$AllowFromRules = file_get_contents($MMAllowFromTXT);
		$stringReplace = file_get_contents($wpadminHtaccess);
				
		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {

			if ( ! preg_match( $pattern3, $stringReplace, $matches ) ) {
				
				$stringReplace = "\n# BEGIN BPS MAINTENANCE MODE IP\n<IfModule mod_authz_core.c>\nRequire ip ".str_replace( array( ',', ", ", ",  "), "", $MMoptions['bps_maint_ip_allowed'])."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\n" . $AllowFromRules . "</IfModule>\n</IfModule>\n# END BPS MAINTENANCE MODE IP";
				
				file_put_contents($wpadminHtaccess, $stringReplace, FILE_APPEND | LOCK_EX);				
				
			} else {
				
				$stringReplace = preg_replace( $pattern3, "# BEGIN BPS MAINTENANCE MODE IP\n<IfModule mod_authz_core.c>\nRequire ip ".str_replace( array( ',', ", ", ",  "), "", $MMoptions['bps_maint_ip_allowed'])."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\nOrder Allow,Deny\n" . $AllowFromRules . "</IfModule>\n</IfModule>\n# END BPS MAINTENANCE MODE IP", $stringReplace);
				
				file_put_contents($wpadminHtaccess, $stringReplace);		
			}				

		} else { // IfModule No and any other coditions

			if ( ! preg_match( $pattern2, $stringReplace, $matches ) ) {
				
				$stringReplace = "\n# BEGIN BPS MAINTENANCE MODE IP\nOrder Allow,Deny\n".$AllowFromRules."# END BPS MAINTENANCE MODE IP";	
				file_put_contents($wpadminHtaccess, $stringReplace, FILE_APPEND | LOCK_EX);				
				
			} else {
				
				$stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/', "# BEGIN BPS MAINTENANCE MODE IP\nOrder Allow,Deny\n".$AllowFromRules."# END BPS MAINTENANCE MODE IP", $stringReplace);	

				file_put_contents($wpadminHtaccess, $stringReplace);		
			}			
		}

		if ( $lock == '0404') {	
			@chmod($wpadminHtaccess, 0404);
		}		
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('BackEnd Maintenance Mode has been Turned On.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;
	}	
	}
	}
}

// Form - Turn On Maintenance Mode
if ( isset( $_POST['Submit-maintenance-mode-on'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_mmode_on' );

	$MMoptions = get_option('bulletproof_security_options_maint_mode');

	if ( ! get_option('bulletproof_security_options_maint_mode') ) {
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You have not saved your option settings yet. Click the Save Options button.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	}

	if ( is_multisite() && $blog_id != 1 ) {	
		$bps_maint_backend = '';
		$bps_maint_mu_entire_site = '';
		$bps_maint_mu_subsites_only = '';
	
	} else {
		
		$bps_maint_backend = $MMoptions['bps_maint_backend'];
		$bps_maint_mu_entire_site = $MMoptions['bps_maint_mu_entire_site'];
		$bps_maint_mu_subsites_only = $MMoptions['bps_maint_mu_subsites_only'];	
	}
	
	$BPS_Options = array(
	'bps_maint_on_off' 					=> 'On', 
	'bps_maint_countdown_timer' 		=> $MMoptions['bps_maint_countdown_timer'], 
	'bps_maint_countdown_timer_color' 	=> $MMoptions['bps_maint_countdown_timer_color'], 
	'bps_maint_time' 					=> $MMoptions['bps_maint_time'], 
	'bps_maint_retry_after' 			=> $MMoptions['bps_maint_retry_after'], 
	'bps_maint_frontend' 				=> $MMoptions['bps_maint_frontend'], 
	'bps_maint_backend' 				=> $bps_maint_backend, 
	'bps_maint_ip_allowed' 				=> $MMoptions['bps_maint_ip_allowed'], 
	'bps_maint_text' 					=> $MMoptions['bps_maint_text'], 
	'bps_maint_background_images' 		=> $MMoptions['bps_maint_background_images'], 
	'bps_maint_center_images' 			=> $MMoptions['bps_maint_center_images'], 
	'bps_maint_background_color' 		=> $MMoptions['bps_maint_background_color'], 
	'bps_maint_show_visitor_ip' 		=> $MMoptions['bps_maint_show_visitor_ip'], 
	'bps_maint_show_login_link' 		=> $MMoptions['bps_maint_show_login_link'], 
	'bps_maint_dashboard_reminder' 		=> $MMoptions['bps_maint_dashboard_reminder'], 
	'bps_maint_log_visitors' 			=> $MMoptions['bps_maint_log_visitors'], 
	'bps_maint_countdown_email' 		=> $MMoptions['bps_maint_countdown_email'], 
	'bps_maint_email_to' 				=> $MMoptions['bps_maint_email_to'], 
	'bps_maint_email_from' 				=> $MMoptions['bps_maint_email_from'], 
	'bps_maint_email_cc' 				=> $MMoptions['bps_maint_email_cc'], 
	'bps_maint_email_bcc' 				=> $MMoptions['bps_maint_email_bcc'], 
	'bps_maint_mu_entire_site' 			=> $bps_maint_mu_entire_site, 
	'bps_maint_mu_subsites_only' 		=> $bps_maint_mu_subsites_only
	);	
	
		foreach( $BPS_Options as $key => $value ) {
			update_option('bulletproof_security_options_maint_mode', $BPS_Options);
		}	
	
	if ( ! is_multisite() ) {
		bpsPro_mmode_single_gwiod_turn_on();
	} else {
		bpsPro_mmode_network_turn_on();
	}
}

// Maintenance Mode - Turn On for Single GWIOD site root index.php file
function bpsPro_mmode_gwiod_site_root_index_file_on() {
global $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$publicly_displayed_url = get_option('home');
$actual_wp_install_url = get_option('siteurl');
$gwiod_MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index-GWIOD.php';
$gwiod_pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sGWIOD\s*require(.*)\s*\}(.*)\s*require(.*)\s*\}\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sGWIOD/';
$gwiod_pattern_ip = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';

	if ( $publicly_displayed_url != $actual_wp_install_url ) {

		$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
		$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
		$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
		$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
		$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
		$sapi_type = php_sapi_name();

		if ( ! file_exists( $gwiod_root_index_file ) ) {
			echo $bps_topDiv;
    		$text = '<font color="#fb0101"><strong>'.__('Error: Unable to get/find the site root index.php file for this GWIOD - Giving WordPress Its Own Directory - website.', 'bulletproof-security').'</font><br>'.__('GWIOD Site Root index.php File Path Checked: ', 'bulletproof-security').$gwiod_root_index_file.'<br>'.__('BPS Maintenance Mode will not work correctly with your WordPress GWIOD setup. Try another WordPress Maintenance Mode plugin.', 'bulletproof-security').'</strong>';
			echo $text;		
			echo $bps_bottomDiv;
		return;		
	
	} else {

		$gwiod_stringReplace = file_get_contents($gwiod_MMindexMaster);
	
		if ( preg_match($gwiod_pattern_ip, $gwiod_stringReplace, $matches ) ) {
			
			$gwiod_stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/', "# BEGIN BPS MAINTENANCE MODE IP\n".'$bps_maintenance_ip'." = array('".str_replace(', ', "', '", $MMoptions['bps_maint_ip_allowed'])."');\n# END BPS MAINTENANCE MODE IP", $gwiod_stringReplace);			
		}		
		
		if ( preg_match($gwiod_pattern, $gwiod_stringReplace, $matches ) ) {
			
			$gwiod_stringReplace = preg_replace('/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sGWIOD\s*require(.*)\s*\}(.*)\s*require(.*)\s*\}\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sGWIOD/', "# BEGIN BPS MAINTENANCE MODE GWIOD\nrequire( dirname( __FILE__ ) . '".$gwiod_url."/wp-blog-header.php' );\n} else {\nrequire( dirname( __FILE__ ) . '".$gwiod_url."/bps-maintenance.php' );\n}\n# END BPS MAINTENANCE MODE GWIOD", $gwiod_stringReplace);		
		}

			if ( file_put_contents($gwiod_MMindexMaster, $gwiod_stringReplace) ) {
		
				if ( @$gwiod_permsIndex == '0400') {
					$lock = '0400';			
				}
				
				if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
					@chmod($gwiod_root_index_file, 0644);
				}	
			
				$gwiod_index_contents = file_get_contents($gwiod_root_index_file);

				// First click Turn On: backup the WP root index.php file. Second... click Turn On: do not backup the index.php file to master-backups again
				if ( ! strpos($gwiod_index_contents, "BPS MAINTENANCE MODE IP") ) {
					copy( $gwiod_root_index_file, $gwiod_root_index_file_backup );	
				} 
			
				@copy($gwiod_MMindexMaster, $gwiod_root_index_file);
				
				if ( $lock == '0400') {	
					@chmod($gwiod_root_index_file, 0400);
				}	
			}
		}
	}
}

// Maintenance Mode - Turn On for Network GWIOD site root index.php file
function bpsPro_mmode_network_gwiod_site_root_index_file_on() {
global $current_blog, $blog_id, $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index-MU.php';
$publicly_displayed_url = network_site_url();
$actual_wp_install_url = get_site_option('siteurl');

	if ( $publicly_displayed_url != $actual_wp_install_url ) {

		$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
		$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
		$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
		$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
		$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
		$sapi_type = php_sapi_name();
		
	if ( ! file_exists( $gwiod_root_index_file ) ) {
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: Unable to get/find the site root index.php file for this Network GWIOD - Giving WordPress Its Own Directory - website.', 'bulletproof-security').'</font><br>'.__('Network GWIOD Site Root index.php File Path Checked: ', 'bulletproof-security').$gwiod_root_index_file.'<br>'.__('Please copy this error message and send it in an email to info@ait-pro.com for assistance.', 'bulletproof-security').'</strong>';
		echo $text;		
		echo $bps_bottomDiv;
	return;		
	
	} else {

		if ( @$gwiod_permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($gwiod_root_index_file, 0644);
		}	
	
	if ( !file_exists($gwiod_root_index_file_backup) ) {
			
		copy( $gwiod_root_index_file, $gwiod_root_index_file_backup );
	}
		
	if ( copy( $MMindexMaster, $gwiod_root_index_file ) ) {
		
		$gwiod_stringReplace = file_get_contents($gwiod_root_index_file);
	}
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlbps-maintenance" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/bps-maintenance/', "/$gwiod_url".'bps-maintenance', $gwiod_stringReplace);
	}			
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlwp-blog-header" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/wp-blog-header/', "/$gwiod_url".'wp-blog-header', $gwiod_stringReplace);
	}	
		
	if ( file_put_contents($gwiod_root_index_file, $gwiod_stringReplace) ) {
		
				if ( $lock == '0400') {	
					@chmod($gwiod_root_index_file, 0400);
				}
			}
		}
	}	
}
?>

<?php
// Maintenance Mode - Frontend MMODE Turn Off used in Turn On function - Single & GWIOD
// conditional / based on $MMoptions['bps_maint_frontend'] != '1' in bpsPro_mmode_single_gwiod_turn_on()
function bpsPro_mmode_single_gwiod_turn_off_frontend() {
global $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';

	if ( file_exists($root_index_file_backup) ) {
		
		if ( @$permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($root_index_file, 0644);
		}	
		
		if ( @copy($root_index_file_backup, $root_index_file) ) {
	
			$delete_files = array($root_folder_maintenance, $root_folder_maintenance_values);

			foreach ( $delete_files as $file ) {
				if ( file_exists($file) ) {
					@unlink($file);	
				}
			}
		
		if ( $lock == '0400') {	
			@chmod($root_index_file, 0400);
		}			
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;		
		
		}
	}

	// Single GWIOD
	$publicly_displayed_url = get_option('home');
	$actual_wp_install_url = get_option('siteurl');
	$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
	$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
	$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
	$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
	$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
	
	if ( file_exists($gwiod_root_index_file_backup) ) {
	
		if ( @$gwiod_permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($gwiod_root_index_file, 0644);
		}	
	
		@copy($gwiod_root_index_file_backup, $gwiod_root_index_file);
		
		if ( $lock == '0400') {	
			@chmod($gwiod_root_index_file, 0400);
		}
	}
}

// Maintenance Mode - Frontend MMODE Turn Off used in Turn On function - Network/GWIOD
// conditional / based on $MMoptions['bps_maint_frontend'] != '1' in bpsPro_mmode_network_turn_on()
// .53.9: BuFix replace subsite site name variable name with dash/hyphen to underscore.
function bpsPro_mmode_network_turn_off_frontend() {
global $current_blog, $blog_id, $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$bps_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index-MU.php';

		if ( @$permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($root_index_file, 0644);
		}	

			// Primary Network Site
			if ( is_multisite() && $blog_id == 1 ) {
			
				$stringReplace = @file_get_contents($MMindexMaster);

				if ( preg_match( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', $stringReplace, $matches ) ) {
			
					$stringReplace = preg_replace( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', "# BEGIN PRIMARY SITE STATUS\n\$primary_site_status = 'Off';\n# END PRIMARY SITE STATUS", $stringReplace);		
				}
				
				if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					@copy( $MMindexMaster, $root_index_file );
					@copy( $bps_maintenance_values, $root_folder_maintenance_values );
				}
			
			// Network Subsites
			} else {
				
				if ( is_multisite() && is_subdomain_install() ) {
		
					$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );					
					$subsite_replace_chars = str_replace( array( '.', '-' ), "_", $current_blog->domain );
				
				} else {
	
					$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
					$subsite_replace_chars = str_replace( array( '/', '-' ), "_", $current_blog->path );
				}
				
				$subsite_root_folder_maintenance = ABSPATH . 'bps-maintenance-'.$subsite_remove_slashes.'.php';
				$subsite_root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values-'.$subsite_remove_slashes.'.php';
								
				$stringReplace = @file_get_contents($MMindexMaster);

				if ( is_multisite() && ! is_subdomain_install() ) {

					if ( preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {
			
						$stringReplace = preg_replace( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', "\$$subsite_replace_chars".'_status'." = 'Off';", $stringReplace);		
					}
				
				} else {
					
					if ( preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {	
	
						$stringReplace = preg_replace( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', "\$$subsite_replace_chars".'_status'." = 'Off';", $stringReplace);		
					}			
				}
				
				if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					@copy( $MMindexMaster, $root_index_file );
				}			
				
				$delete_files = array($subsite_root_folder_maintenance, $subsite_root_folder_maintenance_values);

				foreach ( $delete_files as $file ) {
					if ( file_exists($file) ) {
						@unlink($file);	
					}
				}
			}
		
		if ( $lock == '0400') {	
			@chmod($root_index_file, 0400);
		}			
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;		

	// Network GWIOD: network_site_url: http://example.local/ vs get_site_option siteurl: http://example.local/gwiod/
	$publicly_displayed_url = network_site_url();
	$actual_wp_install_url = get_site_option('siteurl');
	$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
	$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
	$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
	$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
	$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
	
		if ( @$gwiod_permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($gwiod_root_index_file, 0644);
		}	
	
	if ( @copy( $MMindexMaster, $gwiod_root_index_file ) ) {
		
		$gwiod_stringReplace = file_get_contents($gwiod_root_index_file);
	}
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlbps-maintenance" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/bps-maintenance/', "/$gwiod_url".'bps-maintenance', $gwiod_stringReplace);
	}			
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlwp-blog-header" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/wp-blog-header/', "/$gwiod_url".'wp-blog-header', $gwiod_stringReplace);
	}		

	if ( file_put_contents($gwiod_root_index_file, $gwiod_stringReplace) ) {		
		
		if ( $lock == '0400') {	
			@chmod($gwiod_root_index_file, 0400);
		}	
	}	
}

// Maintenance Mode - Backend MMODE Turn Off used in Turn On function - Single & GWIOD & Network
// conditional / based on $MMoptions['bps_maint_backend'] != '1' in bpsPro_mmode_single_gwiod_turn_on()
function bpsPro_mmode_single_gwiod_turn_off_backend() {
global $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
$sapi_type = php_sapi_name();
# BPS .52.5: new pattern|new IfModule conditions
$pattern2 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$pattern3 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP(.*\s*){8}(Allow(.*)\s*){1,}<\/IfModule>\s*<\/IfModule>\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);

	// .53.6: htaccess Files Disabled
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BackEnd Maintenance Mode is disabled.', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';	
		echo $text;
    	echo $bps_bottomDiv;
	
	} else {

	if ( file_exists($wpadminHtaccess) ) {
		
		if ( @$permsHtaccess == '0404') {
			$lock = '0404';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($wpadminHtaccess, 0644);
		}
	
		$stringReplace = file_get_contents($wpadminHtaccess);
		
		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
			
			if ( preg_match( $pattern3, $stringReplace, $matches ) ) {
				
				$stringReplace = preg_replace( $pattern3, "", $stringReplace);				
			}

		} else {

			if ( preg_match( $pattern2, $stringReplace, $matches ) ) {
				
				$stringReplace = preg_replace( $pattern2, "", $stringReplace);
			}
		}			
		
		if ( file_put_contents($wpadminHtaccess, $stringReplace) ) {

			if ( $lock == '0404') {	
				@chmod($wpadminHtaccess, 0404);
			}			
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('BackEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;
		}		
	}
	}
}

// Maintenance Mode - Turn Off - Single & GWIOD
// non-conditional / not based on option settings so that clicking turn off again will not cause problems
# BPS .52.5: An Apache Mod conditional check is not done here to ensure that any old previous htaccess code is removed on Turn Off.
function bpsPro_mmode_single_gwiod_turn_off() {
global $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
$pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
# BPS .52.5: new pattern|new IfModule conditions
$pattern2 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$pattern3 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP(.*\s*){8}(Allow(.*)\s*){1,}<\/IfModule>\s*<\/IfModule>\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);

	if ( file_exists($root_index_file_backup) ) {
		
		if ( @$permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($root_index_file, 0644);
		}	
		
		if ( @copy($root_index_file_backup, $root_index_file) ) {
	
			$delete_files = array($root_folder_maintenance, $root_folder_maintenance_values);

			foreach ( $delete_files as $file ) {
				if ( file_exists($file) ) {
					@unlink($file);	
				}
			}
		
		if ( $lock == '0400') {	
			@chmod($root_index_file, 0400);
		}	
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;			
		}
	}
		
	// GWIOD
	$publicly_displayed_url = get_option('home');
	$actual_wp_install_url = get_option('siteurl');
	$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
	$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
	$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
	$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
	$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
	
	if ( file_exists($gwiod_root_index_file_backup) ) {
	
		if ( @$gwiod_permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($gwiod_root_index_file, 0644);
		}	
	
		@copy($gwiod_root_index_file_backup, $gwiod_root_index_file);
	
		if ( $lock == '0400') {	
			@chmod($gwiod_root_index_file, 0400);
		}	
	}
	
	// .53.6: htaccess Files Disabled
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BackEnd Maintenance Mode is disabled.', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';	
		echo $text;
    	echo $bps_bottomDiv;
	
	} else {

	// wp-admin .htaccess
	if ( file_exists($wpadminHtaccess) ) {
		
		if ( @$permsHtaccess == '0404') {
			$lock = '0404';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($wpadminHtaccess, 0644);
		}
	
		$stringReplace = file_get_contents($wpadminHtaccess);
		
		if ( preg_match( $pattern2, $stringReplace, $matches ) ) {
				
			$stringReplace = preg_replace( $pattern2, "", $stringReplace );
		}

		if ( preg_match( $pattern3, $stringReplace, $matches ) ) {
				
			$stringReplace = preg_replace( $pattern3, "", $stringReplace );
		}

		if ( file_put_contents($wpadminHtaccess, $stringReplace) ) {

			if ( $lock == '0404') {	
				@chmod($wpadminHtaccess, 0404);
			}		
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('BackEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;
		}	
	}
	}
}

// Maintenance Mode - Turn Off - Network/GWIOD
// non-conditional / not based on option settings so that clicking turn off again will not cause problems
# BPS .52.5: An Apache Mod conditional check is not done here to ensure that any old previous htaccess code is removed on Turn Off.
// .53.9: BuFix replace subsite site name variable name with dash/hyphen to underscore.
function bpsPro_mmode_network_turn_off() {
global $current_blog, $blog_id, $bps_topDiv, $bps_bottomDiv;

$MMoptions = get_option('bulletproof_security_options_maint_mode');
$permsIndex = @substr(sprintf('%o', fileperms($root_index_file)), -4);
$sapi_type = php_sapi_name();
$root_index_file = ABSPATH . 'index.php';
$root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_index.php';
$root_folder_maintenance = ABSPATH . 'bps-maintenance.php';
$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
$bps_maintenance_values = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-values.php';
$pattern = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*(.*)\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
# BPS .52.5: new pattern|new IfModule conditions
$pattern2 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP\s*Order(.*)\s*(Allow(.*)\s*){1,}#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$pattern3 = '/#\sBEGIN\sBPS\sMAINTENANCE\sMODE\sIP(.*\s*){8}(Allow(.*)\s*){1,}<\/IfModule>\s*<\/IfModule>\s*#\sEND\sBPS\sMAINTENANCE\sMODE\sIP/';
$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);
$MMindexMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/maintenance-mode-index-MU.php';

		if ( @$permsIndex == '0400') {
			$lock = '0400';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsIndex != '0666' || @$permsIndex != '0777') { // Windows IIS, XAMPP, etc
			@chmod($root_index_file, 0644);
		}	

			// Primary Network Site
			if ( is_multisite() && $blog_id == 1 ) {
			
				$stringReplace = @file_get_contents($MMindexMaster);

				if ( preg_match( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', $stringReplace, $matches ) ) {
			
					$stringReplace = preg_replace( '/#\sBEGIN\sPRIMARY\sSITE\sSTATUS\s*(.*)\s*#\sEND\sPRIMARY\sSITE\sSTATUS/', "# BEGIN PRIMARY SITE STATUS\n\$primary_site_status = 'Off';\n# END PRIMARY SITE STATUS", $stringReplace);		
				}
				
				if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					@copy( $MMindexMaster, $root_index_file );
					@copy( $bps_maintenance_values, $root_folder_maintenance_values );
				}
			
			// Network Subsites
			} else {
				
				if ( is_multisite() && is_subdomain_install() ) {
		
					$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );
					$subsite_replace_chars = str_replace( array( '.', '-' ), "_", $current_blog->domain );	
	
				} else {
	
					$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
					$subsite_replace_chars = str_replace( array( '/', '-' ), "_", $current_blog->path );
				}
				
				$subsite_root_folder_maintenance = ABSPATH . 'bps-maintenance-'.$subsite_remove_slashes.'.php';
				$subsite_root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values-'.$subsite_remove_slashes.'.php';
				
				$stringReplace = @file_get_contents($MMindexMaster);
				//$subsite_subdomain_variable = str_replace( '.', "_", $current_blog->domain );

				if ( is_multisite() && ! is_subdomain_install() ) {

					if ( preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {
			
						$stringReplace = preg_replace( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', "\$$subsite_replace_chars".'_status'." = 'Off';", $stringReplace);		
					}
				
				} else {
					
					if ( preg_match( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', $stringReplace, $matches ) ) {	
	
						$stringReplace = preg_replace( '/\$'.$subsite_replace_chars.'_status = \'(.*)\';/', "\$$subsite_replace_chars".'_status'." = 'Off';", $stringReplace);		
					}			
				}
				
				if ( file_put_contents($MMindexMaster, $stringReplace) ) {
					@copy( $MMindexMaster, $root_index_file );
				}			
				
				$delete_files = array( $subsite_root_folder_maintenance, $subsite_root_folder_maintenance_values );

				foreach ( $delete_files as $file ) {
					if ( file_exists($file) ) {
						@unlink($file);	
					}
				}
			}
		
		if ( $lock == '0400') {	
			@chmod($root_index_file, 0400);
		}			
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('FrontEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
		echo $text;
    	echo $bps_bottomDiv;
		
		// Network/GWIOD
		$publicly_displayed_url = network_site_url();
		$actual_wp_install_url = get_site_option('siteurl');
		$gwiod_root_index_file_backup = WP_CONTENT_DIR . '/bps-backup/master-backups/backup_gwiod_index.php';
		$gwiod_url = str_replace( $publicly_displayed_url, "", $actual_wp_install_url );
		$gwiod_url_path = str_replace( '\\', '/', ABSPATH );
		$gwiod_root_index_file = dirname( $gwiod_url_path ) . '/index.php';
		$gwiod_permsIndex = @substr(sprintf('%o', fileperms($gwiod_root_index_file)), -4);
	
	if ( @$gwiod_permsIndex == '0400') {
		$lock = '0400';			
	}	
	
	if ( @substr($sapi_type, 0, 6) != 'apache' && @$gwiod_permsIndex != '0666' || @$gwiod_permsIndex != '0777') { // Windows IIS, XAMPP, etc
		@chmod($gwiod_root_index_file, 0644);
	}	
	
	if ( @copy( $MMindexMaster, $gwiod_root_index_file ) ) {
		
		$gwiod_stringReplace = file_get_contents($gwiod_root_index_file);
	}
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlbps-maintenance" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/bps-maintenance/', "/$gwiod_url".'bps-maintenance', $gwiod_stringReplace);
	}			
		
	if ( !strpos($gwiod_stringReplace, "/$gwiod_urlwp-blog-header" ) ) {
			
		$gwiod_stringReplace = preg_replace('/\/wp-blog-header/', "/$gwiod_url".'wp-blog-header', $gwiod_stringReplace);
	}	
		
	if ( file_put_contents($gwiod_root_index_file, $gwiod_stringReplace) ) {
		
		if ( $lock == '0400') {	
			@chmod($gwiod_root_index_file, 0400);
		}
	}
	
	// .53.6: htaccess Files Disabled
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BackEnd Maintenance Mode is disabled.', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';	
		echo $text;
    	echo $bps_bottomDiv;
	
	} else {

	// wp-admin .htaccess
	if ( is_multisite() && $blog_id == 1 && file_exists($wpadminHtaccess) ) {
		
		if ( @$permsHtaccess == '0404') {
			$lock = '0404';			
		}		
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($wpadminHtaccess, 0644);
		}
	
		$stringReplace = file_get_contents($wpadminHtaccess);
		
		if ( preg_match( $pattern2, $stringReplace, $matches ) ) {
				
			$stringReplace = preg_replace( $pattern2, "", $stringReplace);
		}

		if ( preg_match( $pattern3, $stringReplace, $matches ) ) {
				
			$stringReplace = preg_replace( $pattern3, "", $stringReplace );
		}

		if ( file_put_contents($wpadminHtaccess, $stringReplace) ) {

			if ( $lock == '0404') {	
				@chmod($wpadminHtaccess, 0404);
			}	
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('BackEnd Maintenance Mode has been Turned Off.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;
		}	
	}
	}
}

// Form - Turn Off Maintenance Mode
if ( isset( $_POST['Submit-maintenance-mode-off'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_mmode_off' );

	$MMoptions = get_option('bulletproof_security_options_maint_mode');

	if ( !get_option('bulletproof_security_options_maint_mode') ) {
		echo $bps_topDiv;
    	$text = '<font color="#fb0101"><strong>'.__('Error: You have not saved your option settings yet. Click the Save Options button.', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	return;
	}
	
	if ( is_multisite() && $blog_id != 1 ) {	
		$bps_maint_backend = '';
		$bps_maint_mu_entire_site = '';
		$bps_maint_mu_subsites_only = '';
	
	} else {
		
		$bps_maint_backend = $MMoptions['bps_maint_backend'];
		$bps_maint_mu_entire_site = $MMoptions['bps_maint_mu_entire_site'];
		$bps_maint_mu_subsites_only = $MMoptions['bps_maint_mu_subsites_only'];	
	}
	
	$BPS_Options = array(
	'bps_maint_on_off' 					=> 'Off', 
	'bps_maint_countdown_timer' 		=> $MMoptions['bps_maint_countdown_timer'], 
	'bps_maint_countdown_timer_color' 	=> $MMoptions['bps_maint_countdown_timer_color'], 
	'bps_maint_time' 					=> $MMoptions['bps_maint_time'], 
	'bps_maint_retry_after' 			=> $MMoptions['bps_maint_retry_after'], 
	'bps_maint_frontend' 				=> $MMoptions['bps_maint_frontend'], 
	'bps_maint_backend' 				=> $bps_maint_backend, 
	'bps_maint_ip_allowed' 				=> $MMoptions['bps_maint_ip_allowed'], 
	'bps_maint_text' 					=> $MMoptions['bps_maint_text'], 
	'bps_maint_background_images' 		=> $MMoptions['bps_maint_background_images'], 
	'bps_maint_center_images' 			=> $MMoptions['bps_maint_center_images'], 
	'bps_maint_background_color' 		=> $MMoptions['bps_maint_background_color'], 
	'bps_maint_show_visitor_ip' 		=> $MMoptions['bps_maint_show_visitor_ip'], 
	'bps_maint_show_login_link' 		=> $MMoptions['bps_maint_show_login_link'], 
	'bps_maint_dashboard_reminder' 		=> $MMoptions['bps_maint_dashboard_reminder'], 
	'bps_maint_log_visitors' 			=> $MMoptions['bps_maint_log_visitors'], 
	'bps_maint_countdown_email' 		=> $MMoptions['bps_maint_countdown_email'], 
	'bps_maint_email_to' 				=> $MMoptions['bps_maint_email_to'], 
	'bps_maint_email_from' 				=> $MMoptions['bps_maint_email_from'], 
	'bps_maint_email_cc' 				=> $MMoptions['bps_maint_email_cc'], 
	'bps_maint_email_bcc' 				=> $MMoptions['bps_maint_email_bcc'], 
	'bps_maint_mu_entire_site' 			=> $bps_maint_mu_entire_site, 
	'bps_maint_mu_subsites_only' 		=> $bps_maint_mu_subsites_only
	);	
	
		foreach( $BPS_Options as $key => $value ) {
			update_option('bulletproof_security_options_maint_mode', $BPS_Options);
		}	
		
	if ( ! is_multisite() ) {
		bpsPro_mmode_single_gwiod_turn_off();
	} else {
		bpsPro_mmode_network_turn_off();
	}
}
?>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-2" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"><h2><?php _e('Help & FAQ', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    <a href="https://forum.ait-pro.com/forums/topic/maintenance-mode-guide-read-me-first/" target="_blank"><?php _e('Maintenance Mode Guide', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://www.ait-pro.com/aitpro-blog/category/bulletproof-security-contributors/" target="_blank"><?php _e('Contributors Page', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/security-log-event-codes/" target="_blank"><?php _e('Security Log Event Codes', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a>
    </td>
  </tr>
</table>
</div>
         
<div id="AITpro-link">BulletProof Security <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://www.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
</div>