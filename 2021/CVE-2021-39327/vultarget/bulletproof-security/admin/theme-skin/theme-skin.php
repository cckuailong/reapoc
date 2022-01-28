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

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ UI|UX Settings', 'bulletproof-security'); ?></h2>
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
// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-free-logo.gif'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('UI|UX Settings', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            

<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">
<?php $text = '<h2>'.__('UI|UX Settings ~ ', 'bulletproof-security').'<span style="font-size:.75em;">'.__('Change UI|UX visual preferences & functionality', 'bulletproof-security').'</span></h2><div class="promo-text">'.__('Want even more security protection?', 'bulletproof-security').'<br>'.__('Protect all of your website files with AutoRestore|Quarantine Intrusion Detection & Prevention System: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro ARQ IDPS">'.__('Get BPS Pro ARQ IDPS', 'bulletproof-security').'</a><br>'.__('Protect against SpamBot & HackerBot (auto-registering, auto-logins, auto-posting, auto-commenting): ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro JTC Anti-Spam|Anti-Hacker">'.__('Get BPS Pro JTC Anti-Spam|Anti-Hacker', 'bulletproof-security').'</a><br>'.__('Protect all of your Plugins (plugin folders and files) with an IP Firewall: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Plugin Firewall">'.__('Get BPS Pro Plugin Firewall', 'bulletproof-security').'</a><br>'.__('Protect your WordPress uploads folder against remote access or execution of files: ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank" title="BPS Pro Uploads Anti-Exploit Guard">'.__('Get BPS Pro Uploads Anti-Exploit Guard', 'bulletproof-security').'</a></div>'; echo $text; ?> 
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('UI|UX Settings', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('UI|UX Settings', 'bulletproof-security'); ?>">
	<p><?php $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br><strong>'.__('Select a UI Theme Skin', 'bulletproof-security').'</strong><br>'.__('Select a UI Theme Skin and click the Save Skin button.', 'bulletproof-security').'<br><br><strong>'.__('Notes:', 'bulletproof-security').'</strong><br>- '.__('All elements and CSS properties should automatically be refreshed when you select and save your Theme Skin. If some Theme Skin elements or properties are not displaying correctly, Refresh your Browser.', 'bulletproof-security').'<br><br><strong>'.__('Inpage Status Display', 'bulletproof-security').'</strong><br>'.__('The Inpage Status Display displays the status of BPS features, options and your site security in real-time. The Inpage Status Display automatically turns itself off when a Form is submitted using POST and displays a Reload BPS Status Display button. Automatically turning off the Status Display during Form processing is a performance enhancement|optimization. Clicking the Reload BPS Status Display button reloads|displays the Inpage Status Display.', 'bulletproof-security').'<br><br><strong>'.__('Turn On|Off The Processing Spinner:', 'bulletproof-security').'</strong><br>'.__('The Processing Spinner is displayed during processing of the Forms listed below. The Processing Spinner includes a Cancel button to cancel the Form processing. The Processing Spinner can be turned off if you do not want to see it. If the Processing Spinner is not displaying correctly or at all then either your theme or another plugin is interfering with it. Since the Processing Spinner is just a visual enhancement it is not critical that it is being displayed.', 'bulletproof-security').'<br><br><strong>'.__('Forms That Display The Processing Spinner:', 'bulletproof-security').'</strong><br>'.__('DB Backup Job Processing, DB Table Names & Character Length Table, DB Table Prefix Changer and Setup Wizard.', 'bulletproof-security').'<br><br><strong>'.__('Turn On|Off jQuery ScrollTop Animation:', 'bulletproof-security').'</strong><br>'.__('The jQuery ScrollTop Animation is the scrolling animation that you see after submitting BPS Forms, which automatically scrolls to the top of BPS plugin pages to display success or error messages. The jQuery ScrollTop animation code is conditional based on your Browser User Agent or Rendering Engine. The jQuery ScrollTop animation has been customized for each major Browser individually for best visual animation/appearance. jQuery ScrollTop Animation can be turned On or Off.', 'bulletproof-security').'<br><br><strong>'.__('WP Toolbar Functionality In BPS Plugin Pages:', 'bulletproof-security').'</strong><br>'.__('This option affects the WP Toolbar in BPS plugin pages ONLY and does not affect the WP Toolbar anywhere else on your site. WP Toolbar additional menu items (nodes) added by other plugins and themes can cause problems for BPS when the WP Toolbar is loaded in BPS plugin pages. This option allows you to load only the default WP Toolbar without any additional menu items (nodes) loading/displayed on BPS plugin pages or to load the WP Toolbar with any/all other menu items (nodes) that have been added by other plugins and themes. The default setting is: Load Only The Default WP Toolbar (without loading any additional menu items (nodes) from other plugins or themes). If the BPS Processing Spinner is not working/displaying correctly then set this option to the default setting: Load Only The Default WP Toolbar.', 'bulletproof-security').'<br><br><strong>'.__('Script|Style Loader Filter (SLF) In BPS Plugin Pages:', 'bulletproof-security').'</strong><br>'.__('SLF is set to On by default. This option prevents other plugin and theme scripts from loading in BPS plugin pages, which can break BPS js and CSS scripts and cause BPS plugin pages to display visually broken.', 'bulletproof-security').'<br><br><strong>'.__('BPS UI|UX|AutoFix Debug:', 'bulletproof-security').'</strong><br>'.__('BPS UI|UX|AutoFix Debug is set to Off by default. Turning On the BPS UI|UX|AutoFix Debug option will display: plugin or theme Scripts that were Dequeued (prevented) from loading in BPS plugin pages, any plugin or theme Scripts that are loading in BPS plugin pages, WP Toolbar nodes|menu items that were Removed from the WP Toolbar in BPS plugin pages, plugin or theme names and the BPS Custom Code text box where plugins or themes should be creating Custom Code whitelist rules. Usage: If the BPS Setup Wizard AutoFix (AutoWhitelist|AutoSetup|AutoCleanup) Notice is still being displayed after running the Pre-Installation Wizard and Setup Wizard then the BPS UI|UX|AutoFix Debug option should be turned On to find the exact plugin or theme and the Custom Code text box where the problem is occurring. Example Debug Displayed message: CC Root Text Box 10: WooCommerce Plugin. This option could also be used generally to see which plugins and themes BPS AutoFix is creating Custom Code whitelist rules for and which Custom Code text boxes the AutoFix whitelist rules will be created in.', 'bulletproof-security'); echo $text; ?></p>
</div>

<div id="UI-Theme-Skin" style="width:340px;">
<form name="ui-theme-skin-form" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_theme_skin'); ?> 
	<?php $UIoptions = get_option('bulletproof_security_options_theme_skin'); 
		$bps_ui_theme_skin = ! isset($UIoptions['bps_ui_theme_skin']) ? '' : $UIoptions['bps_ui_theme_skin'];
	?>

	<label for="UI-Skin"><?php _e('Select a UI Theme Skin:', 'bulletproof-security'); ?></label>
<select name="bulletproof_security_options_theme_skin[bps_ui_theme_skin]" class="form-275">
<option value="blue" <?php selected('blue', $bps_ui_theme_skin); ?>><?php _e('Blue|Light Blue|White UI Theme', 'bulletproof-security'); ?></option>
<option value="black" <?php selected('black', $bps_ui_theme_skin); ?>><?php _e('Black|Dark Grey|Silver UI Theme', 'bulletproof-security'); ?></option>
<option value="grey" <?php selected('grey', $bps_ui_theme_skin); ?>><?php _e('Grey|Light Grey|Silver|White UI Theme', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-UI-Theme-Skin-Options" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

<div id="Inpage-Status-Display" style="max-width:340px;">
<form name="Inpage-Status-Display-form" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_status_display'); ?> 
	<?php $bps_status_display_options = get_option('bulletproof_security_options_status_display'); 
	$bps_status_display = ! isset($bps_status_display_options['bps_status_display']) ? '' : $bps_status_display_options['bps_status_display'];
	?>

	<label for="UI-UX-label"><?php _e('Turn On|Off The Inpage Status Display:', 'bulletproof-security'); ?></label><br />
<select name="bulletproof_security_options_status_display[bps_status_display]" class="form-250">
<option value="On" <?php selected('On', $bps_status_display); ?>><?php _e('Inpage Status Display On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_status_display); ?>><?php _e('Inpage Status Display Off', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-Status-Display" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>


<div id="UI-Spinner" style="max-width:340px;">
<form name="ui-spinner-form" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_spinner'); ?> 
	<?php $UISpinneroptions = get_option('bulletproof_security_options_spinner'); 
		$bps_spinner = ! isset($UISpinneroptions['bps_spinner']) ? '' : $UISpinneroptions['bps_spinner'];
	?>

	<label for="UI-Spinner"><?php _e('Turn On|Off The Processing Spinner:', 'bulletproof-security'); ?></label>
<select name="bulletproof_security_options_spinner[bps_spinner]" class="form-275">
<option value="On" <?php selected('On', $bps_spinner); ?>><?php _e('Processing Spinner On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_spinner); ?>><?php _e('Processing Spinner Off', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-UI-Spinner" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

<div id="ScrollTop-Animation" style="max-width:340px;">
<form name="scrolltop-form" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_scrolltop'); ?> 
	<?php $ScrollTopoptions = get_option('bulletproof_security_options_scrolltop'); 
		$bps_scrolltop = ! isset($ScrollTopoptions['bps_scrolltop']) ? '' : $ScrollTopoptions['bps_scrolltop'];	
	?>

	<label for="scrolltop"><?php _e('Turn On|Off jQuery ScrollTop Animation:', 'bulletproof-security'); ?></label>
<select name="bulletproof_security_options_scrolltop[bps_scrolltop]" class="form-275">
<option value="On" <?php selected('On', $bps_scrolltop); ?>><?php _e('jQuery ScrollTop Animation On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_scrolltop); ?>><?php _e('jQuery ScrollTop Animation Off', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-ScrollTop-Animation" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

<div id="UI-WP-Toolbar" style="max-width:340px;">
<form name="ui-wp-toolbar-form" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_wpt_nodes'); ?> 
	<?php $UIWPToptions = get_option('bulletproof_security_options_wpt_nodes'); 
		$bps_wpt_nodes = ! isset($UIWPToptions['bps_wpt_nodes']) ? '' : $UIWPToptions['bps_wpt_nodes'];		
	?>

	<label for="UI-WP-Toolbar"><?php _e('WP Toolbar Functionality In BPS Plugin Pages:', 'bulletproof-security'); ?></label><br />
	<label for="UI-WP-Toolbar" style="color:#2ea2cc;"><?php _e('Click the Read Me help button for information', 'bulletproof-security'); ?></label><br />
<select name="bulletproof_security_options_wpt_nodes[bps_wpt_nodes]" class="form-275">
<option value="wpnodesonly" <?php selected('wpnodesonly', $bps_wpt_nodes); ?>><?php _e('Load Only The Default WP Toolbar', 'bulletproof-security'); ?></option>
<option value="allnodes" <?php selected('allnodes', $bps_wpt_nodes); ?>><?php _e('Load WP Toolbar With All Menu Items', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-UI-WP-Toolbar" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

<?php
// SLF Values Form
function bpsPro_slf_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['bpsSLFSubmit'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bpsSLFValues' );
		
		$BPS_SLF_Options = array( 
		'bps_slf_filter' 		=> $_POST['bps_slf_filter'], 
		'bps_slf_filter_new' 	=> $_POST['bps_slf_filter_new'] 
		);	

		foreach( $BPS_SLF_Options as $key => $value ) {
			update_option('bulletproof_security_options_SLF', $BPS_SLF_Options);
		}	

		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('SLF Option settings saved', 'bulletproof-security').'</strong></font>';
		echo $text;		
		echo $bps_bottomDiv;
	}
}
?>

<div id="Script-Loader-Filter" style="max-width:340px;">
<form name="script_loader_filter_form" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/theme-skin/theme-skin.php' ); ?>" method="post">
<?php 
	wp_nonce_field('bpsSLFValues'); 
	bpsPro_slf_values_form(); 
	$bpsPro_SLF_options = get_option('bulletproof_security_options_SLF');
	$bps_slf_filter = ! isset($bpsPro_SLF_options['bps_slf_filter']) ? '' : $bpsPro_SLF_options['bps_slf_filter'];	
?>

<label for="SLF"><?php _e('Script|Style Loader Filter (SLF) In BPS Plugin Pages:', 'bulletproof-security'); ?></label><br />
<label for="SLF" style="color:#2ea2cc;"><?php _e('Click the Read Me help button for information', 'bulletproof-security'); ?></label><br />
<select name="bps_slf_filter" class="form-275">
<option value="On" <?php selected('On', $bps_slf_filter); ?>><?php _e('SLF On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_slf_filter); ?>><?php _e('SLF Off', 'bulletproof-security'); ?></option>
</select>
<input type="hidden" name="bps_slf_filter_new" value="14" />
<input type="submit" name="bpsSLFSubmit" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

<div id="BPS-Debug" style="max-width:340px;">
<form name="bps-debug" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_debug'); ?> 
	<?php $Debug_options = get_option('bulletproof_security_options_debug'); 
		$bps_debug = ! isset($Debug_options['bps_debug']) ? '' : $Debug_options['bps_debug'];	
	?>

	<label for="debug"><?php _e('BPS UI|UX|AutoFix Debug:', 'bulletproof-security'); ?></label><br />
	<label for="debug" style="color:#2ea2cc;"><?php _e('Click the Read Me help button for information', 'bulletproof-security'); ?></label><br />
<select name="bulletproof_security_options_debug[bps_debug]" class="form-275">
<option value="Off" <?php selected('Off', $bps_debug); ?>><?php _e('Debug Off', 'bulletproof-security'); ?></option>
<option value="On" <?php selected('On', $bps_debug); ?>><?php _e('Debug On', 'bulletproof-security'); ?></option>
</select>
<input type="submit" name="Submit-Debug" class="button bps-button" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Save Option', 'bulletproof-security') ?>" />
</form>
</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-2" class="bps-tab-page">

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
            
<div id="AITpro-link">BulletProof Security <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://forum.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
</div>