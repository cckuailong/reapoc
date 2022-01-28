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

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ Email Alerts & Log File Settings', 'bulletproof-security'); ?></h2>
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

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Email & Log Settings', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('Email Alerts & Log File Settings ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('For Login Security, Security Log & DB Backup Log', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a><br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting): ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a><br>'.__('Protect all of your Plugins (plugin folders and files) with an IP Firewall: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Plugin Firewall">'.__('Get BPS Pro Plugin Firewall', 'bulletproof-security').'</a><br>'.__('Protect your WordPress uploads folder against remote access or execution of files: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Uploads Anti-Exploit Guard">'.__('Get BPS Pro Uploads Anti-Exploit Guard', 'bulletproof-security').'</a></div>'; echo $text; ?> 
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Email|Log Settings', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('Email|Log Settings', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		// Forum Help Links or of course both
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php $text = '<strong>'.__('Email Alerts & Log File Settings', 'bulletproof-security').'</strong><br>'.__('The email address fields To, From, Cc and Bcc can be email addresses for your hosting account, your WordPress Administrator email address or 3rd party email addresses like gmail or yahoo email. If you are sending emails to multiple email recipients then separate the email addresses with a comma. Example: someone@somewhere.com, someoneelse@somewhereelse.com. You can add a space or not add a space after the comma between email addresses.', 'bulletproof-security').'<br><br><strong>'.__('Note: ', 'bulletproof-security').'</strong>'.__('Email Alerting and Log file options are located in S-Monitor in BPS Pro.', 'bulletproof-security').'<br><br><strong>'.__('Login Security: Send Email Alert When...', 'bulletproof-security').'</strong><br>'.__('There are 5 different email options. Choose to have email alerts sent when a User Account is locked out, An Administrator Logs in, An Administrator Logs in and when a User Account is locked out, Any User logs in and when a User Account is locked out or Do Not Send Email Alerts.', 'bulletproof-security').'<br><br>'.__('The email alerts contain the action that occurred with Timestamp and these fields: Username, Status, Role, Email, Lockout Time, Lockout Time Expires, User IP Address, User Hostname, Request URI and URL link for the website where the action occurred.', 'bulletproof-security').'<br><br><strong>'.__('Security Log File Email|Delete Log File When...', 'bulletproof-security').'</strong><br>'.__('Select the maximum Log File size that you want to allow for your Security Log File and then select the option that you want when your log file reaches that maximum size. Choose to either automatically Email the Log file to you and delete it or just delete it without emailing the log file to you first.', 'bulletproof-security').'<br><br><strong>'.__('DB Backup Log File Email|Delete Log File When...', 'bulletproof-security').'</strong><br>'.__('Select the maximum Log File size that you want to allow for your DB Backup Log File and then select the option that you want when your log file reaches that maximum size. Choose to either automatically Email the Log file to you and delete it or just delete it without emailing the log file to you first.', 'bulletproof-security'); echo $text; ?></p>
</div>

<div id="EmailOptions" style="width:100%;">   

<form name="bpsEmailAlerts" action="options.php" method="post">
    <?php settings_fields('bulletproof_security_options_email'); ?>
	<?php $options = get_option('bulletproof_security_options_email'); 
	$admin_email = get_option('admin_email'); 
	$bps_send_email_to = ! isset($options['bps_send_email_to']) ? '' : $options['bps_send_email_to'];
	$bps_send_email_from = ! isset($options['bps_send_email_from']) ? '' : $options['bps_send_email_from'];
	$bps_send_email_cc = ! isset($options['bps_send_email_cc']) ? '' : $options['bps_send_email_cc'];
	$bps_send_email_bcc = ! isset($options['bps_send_email_bcc']) ? '' : $options['bps_send_email_bcc'];
	$bps_login_security_email = ! isset($options['bps_login_security_email']) ? '' : $options['bps_login_security_email'];
	$bps_security_log_size = ! isset($options['bps_security_log_size']) ? '' : $options['bps_security_log_size'];
	$bps_security_log_emailL = ! isset($options['bps_security_log_emailL']) ? '' : $options['bps_security_log_emailL'];
	$bps_dbb_log_size = ! isset($options['bps_dbb_log_size']) ? '' : $options['bps_dbb_log_size'];
	$bps_dbb_log_email = ! isset($options['bps_dbb_log_email']) ? '' : $options['bps_dbb_log_email'];
	$bps_mscan_log_size = ! isset($options['bps_mscan_log_size']) ? '' : $options['bps_mscan_log_size'];
	$bps_mscan_log_email = ! isset($options['bps_mscan_log_email']) ? '' : $options['bps_mscan_log_email'];
?>

<table border="0">
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files To:', 'bulletproof-security'); ?> </label></td>
    <td><input type="text" name="bulletproof_security_options_email[bps_send_email_to]" class="regular-text-200" value="<?php if ( $bps_send_email_to != '' ) { echo esc_html( $bps_send_email_to ); } else { echo esc_html( $admin_email ); } ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files From:', 'bulletproof-security'); ?> </label></td>
    <td><input type="text" name="bulletproof_security_options_email[bps_send_email_from]" class="regular-text-200" value="<?php if ( $bps_send_email_from != '' ) { echo esc_html( $bps_send_email_from ); } else { echo esc_html( $admin_email ); } ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files Cc:', 'bulletproof-security'); ?> </label></td>
    <td><input type="text" name="bulletproof_security_options_email[bps_send_email_cc]" class="regular-text-200" value="<?php echo esc_html( $bps_send_email_cc ); ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files Bcc:', 'bulletproof-security'); ?> </label></td>
    <td><input type="text" name="bulletproof_security_options_email[bps_send_email_bcc]" class="regular-text-200" value="<?php echo esc_html( $bps_send_email_bcc ); ?>" /></td>
  </tr>
</table>
<br />

<table border="0">
  <tr>
    <td><strong><label for="bps-monitor-email"><?php _e('Login Security: Send Login Security Email Alert When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_login_security_email]" class="form-340">
<option value="lockoutOnly" <?php selected( $bps_login_security_email, 'lockoutOnly'); ?>><?php _e('A User Account Is Locked Out', 'bulletproof-security'); ?></option>
<option value="adminLoginOnly" <?php selected( $bps_login_security_email, 'adminLoginOnly'); ?>><?php _e('An Administrator Logs In', 'bulletproof-security'); ?></option>
<option value="adminLoginLock" <?php selected( $bps_login_security_email, 'adminLoginLock'); ?>><?php _e('An Administrator Logs In & A User Account is Locked Out', 'bulletproof-security'); ?></option>
<option value="anyUserLoginLock" <?php selected( $bps_login_security_email, 'anyUserLoginLock'); ?>><?php _e('Any User Logs In & A User Account is Locked Out', 'bulletproof-security'); ?></option>
<option value="no" <?php selected( $bps_login_security_email, 'no'); ?>><?php _e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
</select></td>
  </tr>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('Security Log: Email|Delete Security Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_security_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_security_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_security_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_security_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_security_log_emailL]" class="form-255">
<option value="email" <?php selected( $bps_security_log_emailL, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_security_log_emailL, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select></td>
  </tr>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('DB Backup Log: Email|Delete DB Backup Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_dbb_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_dbb_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_dbb_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_dbb_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_dbb_log_email]" class="form-255">
<option value="email" <?php selected( $bps_dbb_log_email, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_dbb_log_email, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select>
	</td>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('MScan Malware Scanner Email|Delete Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_mscan_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_mscan_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_mscan_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_mscan_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_mscan_log_email]" class="form-255">
<option value="email" <?php selected( $bps_mscan_log_email, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_mscan_log_email, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select>
	</td> 
  </tr>
</table>

<!-- <strong><label for="bps-monitor-email" style="margin:0px 0px 0px 0px;"><?php //_e('BPS Plugin Upgrade Email Notification', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_upgrade_email]" class="form-340">
<option value="yes" <?php //selected( $bps_upgrade_email, 'yes'); ?>><?php //_e('Send Email Alerts', 'bulletproof-security'); ?></option>
<option value="no" <?php //selected( $bps_upgrade_email, 'no'); ?>><?php //_e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
</select><br /><br /> -->

<input type="hidden" name="bpsEMA" value="bps-EMA" />
<input type="submit" name="bpsEmailAlertSubmit" class="button bps-button" style="margin:15px 0px 20px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" />
</form>
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