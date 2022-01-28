<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
$scrolltoCCode = isset( $_REQUEST['scrolltoCCode'] ) ? (int) $_REQUEST['scrolltoCCode'] : 0; 
$scrolltoCCodeWPA = isset( $_REQUEST['scrolltoCCodeWPA'] ) ? (int) $_REQUEST['scrolltoCCodeWPA'] : 0; 

// Custom Code Check BPS Query String DB option for invalid code
// .51.8: added check for Default WP Rewrite htaccess code
function bps_CustomCode_BPSQSE_check() {
global $bps_topDiv, $bps_bottomDiv;

$options = get_option('bulletproof_security_options_customcode');
$bps_customcode_bpsqse = ! isset($options['bps_customcode_bpsqse']) ? '' : $options['bps_customcode_bpsqse'];
$pattern = '/RewriteCond\s%{REQUEST_FILENAME}\s!-f\s*RewriteCond\s%{REQUEST_FILENAME}\s!-d\s*RewriteRule\s\.(.*)\/index\.php\s\[L\]/';

	if ( preg_match( $pattern, htmlspecialchars_decode( $bps_customcode_bpsqse, ENT_QUOTES ), $matches ) ) {
 		
		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('The BPS Query String Exploits Custom Code below is NOT valid.', 'bulletproof-security').'</font><br>'.__('Delete the code shown below from the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS: text box and click the Save Root Custom Code button.', 'bulletproof-security').'</strong><br>';
 		echo $text;
		echo '<pre>';
 		print_r(htmlspecialchars($matches[0]));
 		echo '</pre>';
		echo $bps_bottomDiv;
	}

$pattern2 = '/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/';

/*
Check these Custom Code DB option values:
CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE: bps_customcode_one
CUSTOM CODE WP REWRITE LOOP START: bps_customcode_wp_rewrite_start
CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS: bps_customcode_bpsqse
CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE: bps_customcode_three
*/

$bps_customcode_one = ! isset($options['bps_customcode_one']) ? '' : $options['bps_customcode_one'];
$bps_customcode_wp_rewrite_start = ! isset($options['bps_customcode_wp_rewrite_start']) ? '' : $options['bps_customcode_wp_rewrite_start'];
$bps_customcode_bpsqse = ! isset($options['bps_customcode_bpsqse']) ? '' : $options['bps_customcode_bpsqse'];
$bps_customcode_three = ! isset($options['bps_customcode_three']) ? '' : $options['bps_customcode_three'];
	
	if ( preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_wp_rewrite_start, ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_bpsqse, ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_three, ENT_QUOTES ), $matches ) ) {

		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('Default WordPress Rewrite htaccess code has been added to BPS Custom Code.', 'bulletproof-security').'</font><br>'.__('The BPS plugin already uses/has Default WordPress Rewrite code. Delete the Default WordPress Rewrite htaccess code shown below from the CUSTOM CODE text box were it was added and click the Save Root Custom Code button.', 'bulletproof-security').'</strong><br>';
 		echo $text;
		echo '<pre>';
 		print_r(htmlspecialchars($matches[0]));
 		echo '</pre>';
		echo $bps_bottomDiv;
	}
}

bps_CustomCode_BPSQSE_check();

// Root Custom Code Form
// Important Note: stripslashes is used to strip any slashes that are added to a $_POST value and not slashes in the code itself.
// Note: Form value bps_customcode_wp_rewrite_end is conditional to Network|Multisite and is hidden for single WP site types, which means the value is not saved in the DB.
## 3.6: Encryption|Decryption added to Forms to bypass/evade OWASP ModSecurity CRS Ruleset on web hosts.
function bpsPro_CC_Root_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['bps_customcode_submit'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_CC_Root' );
		
		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		
		$pos1 = strpos( $_POST['bps_customcode_one'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_customcode_server_signature'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_customcode_directory_index'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_customcode_server_protocol'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos5 = strpos( $_POST['bps_customcode_error_logging'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos6 = strpos( $_POST['bps_customcode_deny_dot_folders'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos7 = strpos( $_POST['bps_customcode_admin_includes'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos8 = strpos( $_POST['bps_customcode_wp_rewrite_start'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos9 = strpos( $_POST['bps_customcode_request_methods'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos10 = strpos( $_POST['bps_customcode_two'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos11 = strpos( $_POST['bps_customcode_timthumb_misc'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos12 = strpos( $_POST['bps_customcode_bpsqse'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos13 = strpos( $_POST['bps_customcode_wp_rewrite_end'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos14 = strpos( $_POST['bps_customcode_deny_files'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos15 = strpos( $_POST['bps_customcode_three'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos1 === false ) {
			$bps_customcode_one = stripslashes($_POST['bps_customcode_one']);
		} else {
			$bps_customcode_one = $Encryption->decrypt($_POST['bps_customcode_one'], $nonceValue);
		}

		if ( $pos2 === false ) {
			$bps_customcode_server_signature = stripslashes($_POST['bps_customcode_server_signature']);
		} else {
			$bps_customcode_server_signature = $Encryption->decrypt($_POST['bps_customcode_server_signature'], $nonceValue);
		}

		if ( $pos3 === false ) {
			$bps_customcode_directory_index = stripslashes($_POST['bps_customcode_directory_index']);
		} else {
			$bps_customcode_directory_index = $Encryption->decrypt($_POST['bps_customcode_directory_index'], $nonceValue);
		}

		if ( $pos4 === false ) {
			$bps_customcode_server_protocol = stripslashes($_POST['bps_customcode_server_protocol']);
		} else {
			$bps_customcode_server_protocol = $Encryption->decrypt($_POST['bps_customcode_server_protocol'], $nonceValue);
		}

		if ( $pos5 === false ) {
			$bps_customcode_error_logging = stripslashes($_POST['bps_customcode_error_logging']);
		} else {
			$bps_customcode_error_logging = $Encryption->decrypt($_POST['bps_customcode_error_logging'], $nonceValue);
		}

		if ( $pos6 === false ) {
			$bps_customcode_deny_dot_folders = stripslashes($_POST['bps_customcode_deny_dot_folders']);
		} else {
			$bps_customcode_deny_dot_folders = $Encryption->decrypt($_POST['bps_customcode_deny_dot_folders'], $nonceValue);
		}

		if ( $pos7 === false ) {
			$bps_customcode_admin_includes = stripslashes($_POST['bps_customcode_admin_includes']);
		} else {
			$bps_customcode_admin_includes = $Encryption->decrypt($_POST['bps_customcode_admin_includes'], $nonceValue);
		}

		if ( $pos8 === false ) {
			$bps_customcode_wp_rewrite_start = stripslashes($_POST['bps_customcode_wp_rewrite_start']);
		} else {
			$bps_customcode_wp_rewrite_start = $Encryption->decrypt($_POST['bps_customcode_wp_rewrite_start'], $nonceValue);
		}

		if ( $pos9 === false ) {
			$bps_customcode_request_methods = stripslashes($_POST['bps_customcode_request_methods']);
		} else {
			$bps_customcode_request_methods = $Encryption->decrypt($_POST['bps_customcode_request_methods'], $nonceValue);
		}

		if ( $pos10 === false ) {
			$bps_customcode_two = stripslashes($_POST['bps_customcode_two']);
		} else {
			$bps_customcode_two = $Encryption->decrypt($_POST['bps_customcode_two'], $nonceValue);
		}

		if ( $pos11 === false ) {
			$bps_customcode_timthumb_misc = stripslashes($_POST['bps_customcode_timthumb_misc']);
		} else {
			$bps_customcode_timthumb_misc = $Encryption->decrypt($_POST['bps_customcode_timthumb_misc'], $nonceValue);
		}

		if ( $pos12 === false ) {
			$bps_customcode_bpsqse = stripslashes($_POST['bps_customcode_bpsqse']);
		} else {
			$bps_customcode_bpsqse = $Encryption->decrypt($_POST['bps_customcode_bpsqse'], $nonceValue);
		}

		if ( $pos13 === false ) {
			$bps_customcode_wp_rewrite_end = stripslashes($_POST['bps_customcode_wp_rewrite_end']);
		} else {
			$bps_customcode_wp_rewrite_end = $Encryption->decrypt($_POST['bps_customcode_wp_rewrite_end'], $nonceValue);
		}

		if ( $pos14 === false ) {
			$bps_customcode_deny_files = stripslashes($_POST['bps_customcode_deny_files']);
		} else {
			$bps_customcode_deny_files = $Encryption->decrypt($_POST['bps_customcode_deny_files'], $nonceValue);
		}

		if ( $pos15 === false ) {
			$bps_customcode_three = stripslashes($_POST['bps_customcode_three']);
		} else {
			$bps_customcode_three = $Encryption->decrypt($_POST['bps_customcode_three'], $nonceValue);
		}

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $bps_customcode_one, 
		'bps_customcode_server_signature' 	=> $bps_customcode_server_signature, 
		'bps_customcode_directory_index' 	=> $bps_customcode_directory_index, 
		'bps_customcode_server_protocol' 	=> $bps_customcode_server_protocol, 
		'bps_customcode_error_logging' 		=> $bps_customcode_error_logging, 
		'bps_customcode_deny_dot_folders' 	=> $bps_customcode_deny_dot_folders, 
		'bps_customcode_admin_includes' 	=> $bps_customcode_admin_includes, 
		'bps_customcode_wp_rewrite_start' 	=> $bps_customcode_wp_rewrite_start, 
		'bps_customcode_request_methods' 	=> $bps_customcode_request_methods, 
		'bps_customcode_two' 				=> $bps_customcode_two, 
		'bps_customcode_timthumb_misc' 		=> $bps_customcode_timthumb_misc, 
		'bps_customcode_bpsqse' 			=> $bps_customcode_bpsqse, 
		'bps_customcode_wp_rewrite_end' 	=> $bps_customcode_wp_rewrite_end, 
		'bps_customcode_deny_files' 		=> $bps_customcode_deny_files, 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);

		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('Root Custom Code saved successfully! Go to the Security Modes tab page and click the Root Folder BulletProof Mode Activate button to add/create your new Custom Code in your Root htaccess file.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}

// wp-admin Custom Code Form
// Important Note: stripslashes is used to strip any slashes that are added to a $_POST value and not slashes in the code itself.
## 3.6: Encryption|Decryption added to Forms to bypass/evade OWASP ModSecurity CRS Ruleset on web hosts.
function bpsPro_CC_WPA_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['bps_customcode_submit_wpa'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_CC_WPA' );
		
		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		
		$pos1 = strpos( $_POST['bps_customcode_deny_files_wpa'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_customcode_one_wpa'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_customcode_two_wpa'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_customcode_bpsqse_wpa'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos1 === false ) {
			$bps_customcode_deny_files_wpa = stripslashes($_POST['bps_customcode_deny_files_wpa']);
		} else {
			$bps_customcode_deny_files_wpa = $Encryption->decrypt($_POST['bps_customcode_deny_files_wpa'], $nonceValue);
		}

		if ( $pos2 === false ) {
			$bps_customcode_one_wpa = stripslashes($_POST['bps_customcode_one_wpa']);
		} else {
			$bps_customcode_one_wpa = $Encryption->decrypt($_POST['bps_customcode_one_wpa'], $nonceValue);
		}

		if ( $pos3 === false ) {
			$bps_customcode_two_wpa = stripslashes($_POST['bps_customcode_two_wpa']);
		} else {
			$bps_customcode_two_wpa = $Encryption->decrypt($_POST['bps_customcode_two_wpa'], $nonceValue);
		}

		if ( $pos4 === false ) {
			$bps_customcode_bpsqse_wpa = stripslashes($_POST['bps_customcode_bpsqse_wpa']);
		} else {
			$bps_customcode_bpsqse_wpa = $Encryption->decrypt($_POST['bps_customcode_bpsqse_wpa'], $nonceValue);
		}

		$wpadmin_CC_Options = array(
		'bps_customcode_deny_files_wpa' => $bps_customcode_deny_files_wpa, 
		'bps_customcode_one_wpa' 		=> $bps_customcode_one_wpa, 
		'bps_customcode_two_wpa' 		=> $bps_customcode_two_wpa, 
		'bps_customcode_bpsqse_wpa' 	=> $bps_customcode_bpsqse_wpa 
		);

		foreach( $wpadmin_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('wp-admin Custom Code saved successfully! Go to the Security Modes tab page and click wp-admin Folder BulletProof Mode Activate button to add/create your new Custom Code in your wp-admin htaccess file.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}

	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
	// Nonce for Crypto-js
	$bps_nonceValue = 'ghbhnyxu';
	$bpsSpacePop = '-------------------------------------------------------------';
?>
        
<div id="bps-accordion-2" class="bps-accordion-main-2" style="">
    <h3><?php _e('Root htaccess File Custom Code', 'bulletproof-security'); ?></h3>
<div id="cc-accordion-inner">

    <button onclick="bpsRootCCEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt Custom Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsRootCCDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt Custom Code', 'bulletproof-security'); ?></button>

<table width="100%" border="0" cellspacing="0" cellpadding="10" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title"></td>
  </tr>
  <tr>
    
    <td class="bps-table_cell_help_custom_code">
    
<form name="bpsCustomCodeForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
<?php  
	wp_nonce_field('bulletproof_security_CC_Root'); 
	bpsPro_CC_Root_values_form();
	$CC_Options_root = get_option('bulletproof_security_options_customcode'); 
	$bps_customcode_one = ! isset($CC_Options_root['bps_customcode_one']) ? '' : $CC_Options_root['bps_customcode_one'];
	$bps_customcode_server_signature = ! isset($CC_Options_root['bps_customcode_server_signature']) ? '' : $CC_Options_root['bps_customcode_server_signature'];
	$bps_customcode_directory_index = ! isset($CC_Options_root['bps_customcode_directory_index']) ? '' : $CC_Options_root['bps_customcode_directory_index'];
	$bps_customcode_server_protocol = ! isset($CC_Options_root['bps_customcode_server_protocol']) ? '' : $CC_Options_root['bps_customcode_server_protocol'];
	$bps_customcode_error_logging = ! isset($CC_Options_root['bps_customcode_error_logging']) ? '' : $CC_Options_root['bps_customcode_error_logging'];
	$bps_customcode_deny_dot_folders = ! isset($CC_Options_root['bps_customcode_deny_dot_folders']) ? '' : $CC_Options_root['bps_customcode_deny_dot_folders'];
	$bps_customcode_admin_includes = ! isset($CC_Options_root['bps_customcode_admin_includes']) ? '' : $CC_Options_root['bps_customcode_admin_includes'];
	$bps_customcode_wp_rewrite_start = ! isset($CC_Options_root['bps_customcode_wp_rewrite_start']) ? '' : $CC_Options_root['bps_customcode_wp_rewrite_start'];
	$bps_customcode_request_methods = ! isset($CC_Options_root['bps_customcode_request_methods']) ? '' : $CC_Options_root['bps_customcode_request_methods'];
	$bps_customcode_two = ! isset($CC_Options_root['bps_customcode_two']) ? '' : $CC_Options_root['bps_customcode_two'];
	$bps_customcode_timthumb_misc = ! isset($CC_Options_root['bps_customcode_timthumb_misc']) ? '' : $CC_Options_root['bps_customcode_timthumb_misc'];
	$bps_customcode_bpsqse = ! isset($CC_Options_root['bps_customcode_bpsqse']) ? '' : $CC_Options_root['bps_customcode_bpsqse'];
	$bps_customcode_wp_rewrite_end = ! isset($CC_Options_root['bps_customcode_wp_rewrite_end']) ? '' : $CC_Options_root['bps_customcode_wp_rewrite_end'];
	$bps_customcode_deny_files = ! isset($CC_Options_root['bps_customcode_deny_files']) ? '' : $CC_Options_root['bps_customcode_deny_files'];
	$bps_customcode_three = ! isset($CC_Options_root['bps_customcode_three']) ? '' : $CC_Options_root['bps_customcode_three'];
?>    

    <strong><label for="bps-CCode"><?php echo number_format_i18n( 1 ).'. '; _e('CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE:<br>Add php/php.ini handler code, cache code and/or <a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Link opens in a new Browser window" target="_blank">Speed Boost Cache Code</a>', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid php/php.ini handler htaccess code and/or cache htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt1" class="bps-text-area-custom-code" name="bps_customcode_one" tabindex="1"><?php echo $bps_customcode_one; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your php/php.ini handler and/or cache htaccess code will be created in your root htaccess file. If you have php/php.ini handler and/or cache htaccess code, copy and paste it into the CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE text box to the left.</span><pre># PHP/PHP.INI HANDLER/CACHE CODE<br /># Use BPS Custom Code to add php/php.ini Handler and Cache htaccess code and to save it permanently.<br /># Most Hosts do not have/use/require php/php.ini Handler htaccess code</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 2 ).'. '; _e('CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire TURN OFF YOUR SERVER SIGNATURE section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt2" class="bps-text-area-custom-code" name="bps_customcode_server_signature" tabindex="2"><?php echo $bps_customcode_server_signature; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE text box. Go to the htaccess File Editor tab page and copy your actual TURN OFF YOUR SERVER SIGNATURE root htaccess file code and paste it into the CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE text box to the left.</span><pre># TURN OFF YOUR SERVER SIGNATURE<br /># Suppresses the footer line server version number and ServerName of the serving virtual host<br />ServerSignature Off</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 3 ).'. '; _e('CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt3" class="bps-text-area-custom-code" name="bps_customcode_directory_index" tabindex="3"><?php echo $bps_customcode_directory_index; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX text box. Go to the htaccess File Editor tab page and copy your actual DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX root htaccess file code and paste it into the CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX text box to the left.</span><pre style="max-height:130px;"># DO NOT SHOW DIRECTORY LISTING<br /># Disallow mod_autoindex from displaying a directory listing<br /># If a 500 Internal Server Error occurs when activating Root BulletProof Mode<br /># copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code<br /># and paste it into BPS Custom Code and comment out Options -Indexes<br /># by adding a # sign in front of it.<br /># Example: #Options -Indexes<br />Options -Indexes<br /><br /># DIRECTORY INDEX FORCE INDEX.PHP<br /># Use index.php as default directory index file. index.html will be ignored.<br /># If a 500 Internal Server Error occurs when activating Root BulletProof Mode<br /># copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code<br /># and paste it into BPS Custom Code and comment out DirectoryIndex<br /># by adding a # sign in front of it.<br /># Example: #DirectoryIndex index.php index.html /index.php<br />DirectoryIndex index.php index.html /index.php</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 4 ).'. '; _e('CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('This Custom Code text box is for optional/Bonus code. To get this code click the link below:', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Link opens in a new Browser window" target="_blank">Brute Force Login Page Protection Code</a></font>'; echo $text ; ?></strong><br />
    <textarea id="crypt4" class="bps-text-area-custom-code" name="bps_customcode_server_protocol" tabindex="4"><?php echo $bps_customcode_server_protocol; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your Brute Force Login Page Protection code will be created in your root htaccess file if you decide to add the option/Bonus code. You can get the code by clicking the Brute Force Login Page Protection Code link. Copy and paste it into the CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION text box to the left.</span><pre># BRUTE FORCE LOGIN PAGE PROTECTION<br /># PLACEHOLDER ONLY<br /># Use BPS Custom Code to add Brute Force Login protection code and to save it permanently.<br /># See this link: https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/<br /># for more information.</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 5 ).'. '; _e('CUSTOM CODE ERROR LOGGING AND TRACKING:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire ERROR LOGGING AND TRACKING section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt5" class="bps-text-area-custom-code" name="bps_customcode_error_logging" tabindex="5"><?php echo $bps_customcode_error_logging; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE ERROR LOGGING AND TRACKING text box. Go to the htaccess File Editor tab page and copy your actual ERROR LOGGING AND TRACKING root htaccess file code and paste it into the CUSTOM CODE ERROR LOGGING AND TRACKING text box to the left.</span><pre style="max-height:145px;"># BPS PRO ERROR LOGGING AND TRACKING<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># BPS Pro has premade 400 Bad Request, 403 Forbidden, 404 Not Found, 405 Method Not Allowed and<br /># 410 Gone template logging files that are used to track and log 400, 403, 404, 405 and 410 errors<br />.....<br />.....<br />ErrorDocument 400 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/400.php<br />ErrorDocument 401 default<br />ErrorDocument 403 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/403.php<br />ErrorDocument 404 /404.php<br />ErrorDocument 405 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/405.php<br />ErrorDocument 410 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/410.php</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 6 ).'. '; _e('CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt6" class="bps-text-area-custom-code" name="bps_customcode_deny_dot_folders" tabindex="6"><?php echo $bps_customcode_deny_dot_folders; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS text box. Go to the htaccess File Editor tab page and copy your actual DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS root htaccess file code and paste it into the CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS text box to the left.</span><pre># DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># Files and folders starting with a dot: .htaccess, .htpasswd, .errordocs, .logs<br />RedirectMatch 403 \.(htaccess|htpasswd|errordocs|logs)$</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 7 ).'. '; _e('CUSTOM CODE WP-ADMIN/INCLUDES: DO NOT add wp-admin .htaccess code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('Add one pound sign # below to prevent the WP-ADMIN/INCLUDES section of code from being created in your root .htaccess file', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt7" class="bps-text-area-custom-code" name="bps_customcode_admin_includes" tabindex="7"><?php echo $bps_customcode_admin_includes; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP-ADMIN/INCLUDES text box. Go to the htaccess File Editor tab page and copy your actual WP-ADMIN/INCLUDES root htaccess file code and paste it into the CUSTOM CODE WP-ADMIN/INCLUDES text box to the left.</span><pre># WP-ADMIN/INCLUDES<br /># Use BPS Custom Code to remove this code permanently.<br />RewriteEngine On<br />RewriteBase /<br />RewriteRule ^wp-admin/includes/ - [F]<br />RewriteRule !^wp-includes/ - [S=3]<br />RewriteRule ^wp-includes/[^/]+\.php$ - [F]<br />RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F]<br />RewriteRule ^wp-includes/theme-compat/ - [F]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 8 ).'. '; _e('CUSTOM CODE WP REWRITE LOOP START: www/non-www http/https Rewrite code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire WP REWRITE LOOP START section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').' <a href="https://forum.ait-pro.com/forums/topic/wordpress-ssl-htaccess-code-rewrite-ssl-rewritecond-server_port/#post-7233" title="Link opens in a new Browser window" target="_blank">Get HTTPS/SSL Rewrite htaccess Code</a>.</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt8" class="bps-text-area-custom-code" name="bps_customcode_wp_rewrite_start" tabindex="8"><?php echo $bps_customcode_wp_rewrite_start; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP REWRITE LOOP START text box. Go to the htaccess File Editor tab page and copy your actual WP REWRITE LOOP START root htaccess file code and paste it into the CUSTOM CODE WP REWRITE LOOP START text box to the left.</span><br /><pre># CUSTOM CODE WP REWRITE LOOP START<br /># WP REWRITE LOOP START<br />RewriteEngine On<br />RewriteBase /<br />RewriteRule ^index\.php$ - [L]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
     <strong><label for="bps-CCode">
	<?php echo number_format_i18n( 9 ).'. '; _e('CUSTOM CODE REQUEST METHODS FILTERED:', 'bulletproof-security'); ?><br />
	<?php _e('Whitelist User Agents and allow HEAD Requests', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire REQUEST METHODS FILTERED section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes. To Allow HEAD Requests click the Read Me help button at the top of the Custom Code page for instructions and examples.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea id="crypt9" class="bps-text-area-custom-code" name="bps_customcode_request_methods" tabindex="9"><?php echo $bps_customcode_request_methods; ?></textarea>   
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;">
    
<?php if ( preg_match( '/R=405/', $bps_customcode_request_methods ) ) { ?>

<span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE REQUEST METHODS FILTERED text box. Go to the htaccess File Editor tab page and copy your actual REQUEST METHODS FILTERED root htaccess file code and paste it into the CUSTOM CODE REQUEST METHODS FILTERED text box to the left.</span><pre># REQUEST METHODS FILTERED<br /># If you want to allow HEAD Requests use BPS Custom Code and copy<br /># this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code<br /># text box: CUSTOM CODE REQUEST METHODS FILTERED.<br /># See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.<br />RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]<br />RewriteRule ^(.*)$ - [F]<br />RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]<br />RewriteRule ^(.*)$ - [R=405,L]</pre>

<?php } else { ?>   

    <span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE REQUEST METHODS FILTERED text box. Go to the htaccess File Editor tab page and copy your actual REQUEST METHODS FILTERED root htaccess file code and paste it into the CUSTOM CODE REQUEST METHODS FILTERED text box to the left.</span><pre># REQUEST METHODS FILTERED<br /># If you want to allow HEAD Requests use BPS Custom Code and copy<br /># this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code<br /># text box: CUSTOM CODE REQUEST METHODS FILTERED.<br /># See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.<br />RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]<br />RewriteRule ^(.*)$ - [F]<br />RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]<br />RewriteRule ^(.*)$ <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/405.php [L]</pre>

<?php } ?>
   
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 10 ).'. '; _e('CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES:<br>Add personal plugin/theme skip/bypass rules here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt10" class="bps-text-area-custom-code" name="bps_customcode_two" tabindex="10"><?php echo $bps_customcode_two; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your plugin/theme skip/bypass rules code will be created in your root htaccess file. If you have plugin/theme skip/bypass rules, copy and paste it into the CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES text box to the left. Click the Read Me help button for more information about plugin/theme skip/bypass rules code.</span><pre style="max-height:145px;"># PLUGINS/THEMES AND VARIOUS EXPLOIT FILTER SKIP RULES<br /># To add plugin/theme skip/bypass rules use BPS Custom Code.<br /># The [S] flag is used to skip following rules. Skip rule [S=12] will skip 12 following RewriteRules.<br /># The skip rules MUST be in descending consecutive number order: 12, 11, 10, 9...<br /># If you delete a skip rule, change the other skip rule numbers accordingly.<br /># Examples: If RewriteRule [S=5] is deleted than change [S=6] to [S=5], [S=7] to [S=6], etc.<br /># If you add a new skip rule above skip rule 12 it will be skip rule 13: [S=13]<br /><br /><div style="background-color:#FFFF00;padding:3px;">Your plugin/theme skip/bypass rules will be created here in your root htaccess file</div><br /># Adminer MySQL management tool data populate<br />RewriteCond %{REQUEST_URI} ^/<?php echo $bps_plugin_dir; ?>/adminer/ [NC]<br />RewriteRule . - [S=12]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 11 ).'. '; _e('CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire TIMTHUMB FORBID RFI section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt11" class="bps-text-area-custom-code" name="bps_customcode_timthumb_misc" tabindex="11"><?php echo $bps_customcode_timthumb_misc; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE text box. Go to the htaccess File Editor tab page and copy your actual TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE root htaccess file code and paste it into the CUSTOM CODE text box to the left.</span><pre style="max-height:145px;"># TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># Remote File Inclusion (RFI) security rules<br />.....<br />.....<br /># Example: Whitelist additional misc files: (example\.php|another-file\.php|phpthumb\.php|thumb\.php|thumbs\.php)<br />RewriteCond %{REQUEST_URI} (timthumb\.php|phpthumb\.php|thumb\.php|thumbs\.php) [NC]<br /># Example: Whitelist additional website domains: RewriteCond %{HTTP_REFERER} ^.*(YourWebsite.com|AnotherWebsite.com).*<br />RewriteCond %{HTTP_REFERER} ^.*<?php echo $bps_get_domain_root; ?>.*<br />RewriteRule . - [S=1]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 12 ).'. '; _e('CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire BPSQSE QUERY STRING EXPLOITS section of code from your root .htaccess file from # BEGIN BPSQSE BPS QUERY STRING EXPLOITS to # END BPSQSE BPS QUERY STRING EXPLOITS into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt12" class="bps-text-area-custom-code" name="bps_customcode_bpsqse" tabindex="12"><?php echo $bps_customcode_bpsqse; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:90px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS text box. Go to the htaccess File Editor tab page and copy your actual BPSQSE BPS QUERY STRING EXPLOITS root htaccess file code and paste it into the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS text box to the left.</span><pre># BEGIN BPSQSE BPS QUERY STRING EXPLOITS<br /># The libwww-perl User Agent is forbidden - Many bad bots use libwww-perl modules, but some good bots use it too.<br /># Good sites such as W3C use it for their W3C-LinkChecker.<br /># Use BPS Custom Code to add or remove user agents temporarily or permanently from the<br />.....<br />.....<br />RewriteCond %{QUERY_STRING} (sp_executesql) [NC]<br />RewriteRule ^(.*)$ - [F]<br /># END BPSQSE BPS QUERY STRING EXPLOITS</pre></td>
  </tr>

<?php if ( is_multisite() ) { ?>

  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 12 ).'b. '; _e('CUSTOM CODE WP REWRITE LOOP END: Add WP Rewrite Loop End code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('This is a Special Custom Code text box that should only be used if the correct WP REWRITE LOOP END code is not being created in your root .htaccess file. See the Read Me help button for more information.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt12b" class="bps-text-area-custom-code" name="bps_customcode_wp_rewrite_end" tabindex="13"><?php echo $bps_customcode_wp_rewrite_end; ?></textarea>

</td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: The actual WP REWRITE LOOP END code for your website may be different. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP REWRITE LOOP END text box. Go to the htaccess File Editor tab page and copy your actual WP REWRITE LOOP END root htaccess file code and paste it into the CUSTOM CODE WP REWRITE LOOP END text box to the left.</span><br /><pre># END BPSQSE BPS QUERY STRING EXPLOITS<br /><div style="background-color:#FFFF00;padding:3px;">RewriteCond %{REQUEST_FILENAME} -f [OR]<br />RewriteCond %{REQUEST_FILENAME} -d<br />RewriteRule ^ - [L]<br />RewriteRule ^[_0-9a-zA-Z-]+/(wp-(content|admin|includes).*) $1 [L]<br />RewriteRule ^[_0-9a-zA-Z-]+/(.*\.php)$ $1 [L]<br />RewriteRule . index.php [L]<br /># WP REWRITE LOOP END</div></pre>
	</td>
  </tr>

<?php } else { ?>

<textarea id="crypt12b" class="bps-text-area-custom-code" name="bps_customcode_wp_rewrite_end" tabindex="13" style="display:none;"></textarea>

<?php } ?>

  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 13 ).'. '; _e('CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DENY BROWSER ACCESS section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt13" class="bps-text-area-custom-code" name="bps_customcode_deny_files" tabindex="14"><?php echo $bps_customcode_deny_files; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES text box. Go to the htaccess File Editor tab page and copy your actual DENY BROWSER ACCESS TO THESE FILES root htaccess file code and paste it into the CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES text box to the left.</span>
    
<?php if ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) { ?>   
    
<pre style="max-height:145px;"># DENY BROWSER ACCESS TO THESE FILES<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># wp-config.php, bb-config.php, php.ini, php5.ini, readme.html<br /># To be able to view these files from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1<br /># Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br />&lt;FilesMatch &quot;^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)&quot;&gt;<br />&lt;IfModule mod_authz_core.c&gt;<br />Require all denied<br />#Require ip 127.0.0.1<br />&lt;/IfModule&gt;<br /><br />&lt;IfModule !mod_authz_core.c&gt;<br />&lt;IfModule mod_access_compat.c&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;/IfModule&gt;<br />&lt;/FilesMatch&gt;</pre>
    
<?php } elseif ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) { ?>

<pre style="max-height:145px;"># DENY BROWSER ACCESS TO THESE FILES<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># wp-config.php, bb-config.php, php.ini, php5.ini, readme.html<br /># To be able to view these files from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br />&lt;FilesMatch &quot;^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)&quot;&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/FilesMatch&gt;</pre>

<?php } ?>  
    
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 14 ).'. '; _e('CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE: Add miscellaneous code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt14" class="bps-text-area-custom-code" name="bps_customcode_three" tabindex="15"><?php echo $bps_customcode_three; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your custom htaccess code will be created in your root htaccess file. If you have Hotlinking, Redirect, IP Blocking htaccess code then copy and paste it into the CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE text box to the left.</span><pre># CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE<br /># PLACEHOLDER ONLY<br /># Use BPS Custom Code to add custom code and save it permanently here.</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">

	<?php echo '<label for="bps-mscan-label" style="">'.__('If you are unable to save Custom Code and/or see an error message when trying to save Custom Code, click the Encrypt Custom Code button first and then click the Save Root Custom Code button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your custom htaccess code unless you encrypt it first by clicking the Encrypt Custom Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt Custom Code button, but then want to add or edit additional custom code click the Decrypt Custom Code button. After you are done adding or editing custom code click the Encrypt Custom Code button before clicking the Save Root Custom Code button.', 'bulletproof-security').'<br><br>'.__('Additional Encrypt and Decrypt buttons have been added at the top of the Root Custom Code Form.', 'bulletproof-security').'<br><br>'.__('Click the Custom Code Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong><br><br>'; ?>

    <input type="hidden" name="scrolltoCCode" value="<?php echo esc_html( $scrolltoCCode ); ?>" />
	<input type="submit" name="bps_customcode_submit" value="<?php esc_attr_e('Save Root Custom Code', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('IMPORTANT!!! Did you remember to click the Encrypt Custom Code button first before saving your Root Custom Code?', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to save your Root Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
    </form>
    
    <button onclick="bpsRootCCEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt Custom Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsRootCCDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt Custom Code', 'bulletproof-security'); ?></button>

    </td>
    <td class="bps-table_cell_help_custom_code">&nbsp;</td>
    </tr>
  <tr>
    <td class="bps-table_cell_help">&nbsp;</td>
    <td class="bps-table_cell_help">&nbsp;</td>
  </tr>
</table>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsCustomCodeForm').submit(function(){ $('#scrolltoCCode').val( $('#bps_customcode_one').scrollTop() ); });
	$('#bps_customcode_one').scrollTop( $('#scrolltoCCode').val() ); 
});

function bpsRootCCEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt1").value;
  var CCString2 = document.getElementById("crypt2").value;
  var CCString3 = document.getElementById("crypt3").value;  
  var CCString4 = document.getElementById("crypt4").value;
  var CCString5 = document.getElementById("crypt5").value;
  var CCString6 = document.getElementById("crypt6").value;
  var CCString7 = document.getElementById("crypt7").value;  
  var CCString8 = document.getElementById("crypt8").value;
  var CCString9 = document.getElementById("crypt9").value;  
  var CCString10 = document.getElementById("crypt10").value;
  var CCString11 = document.getElementById("crypt11").value;  
  var CCString12 = document.getElementById("crypt12").value;
  var CCString12b = document.getElementById("crypt12b").value;
  var CCString13 = document.getElementById("crypt13").value;
  var CCString14 = document.getElementById("crypt14").value; 
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);
  var NoEncrypt2 = CCString2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt2);
  var NoEncrypt3 = CCString3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt3);
  var NoEncrypt4 = CCString4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt4);
  var NoEncrypt5 = CCString5.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt5);
  var NoEncrypt6 = CCString6.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt6);
  var NoEncrypt7 = CCString7.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt7);
  var NoEncrypt8 = CCString8.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt8);
  var NoEncrypt9 = CCString9.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt9);
  var NoEncrypt10 = CCString10.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt10);
  var NoEncrypt11 = CCString11.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt11);
  var NoEncrypt12 = CCString12.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt12);
  var NoEncrypt12b = CCString12b.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt12b);
  var NoEncrypt13 = CCString13.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt13);
  var NoEncrypt14 = CCString14.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt14);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  if (CCString2 != '' && NoEncrypt2 === false) {
  var encrypted2 = encryption.encrypt(CCString2, nonceValue);
  }
  if (CCString3 != '' && NoEncrypt3 === false) {
  var encrypted3 = encryption.encrypt(CCString3, nonceValue);
  }
  if (CCString4 != '' && NoEncrypt4 === false) {
  var encrypted4 = encryption.encrypt(CCString4, nonceValue);
  }
  if (CCString5 != '' && NoEncrypt5 === false) {
  var encrypted5 = encryption.encrypt(CCString5, nonceValue);
  }
  if (CCString6 != '' && NoEncrypt6 === false) {
  var encrypted6 = encryption.encrypt(CCString6, nonceValue);
  }
  if (CCString7 != '' && NoEncrypt7 === false) {
  var encrypted7 = encryption.encrypt(CCString7, nonceValue);
  }
  if (CCString8 != '' && NoEncrypt8 === false) {
  var encrypted8 = encryption.encrypt(CCString8, nonceValue);
  }
  if (CCString9 != '' && NoEncrypt9 === false) {
  var encrypted9 = encryption.encrypt(CCString9, nonceValue);
  }
  if (CCString10 != '' && NoEncrypt10 === false) {
  var encrypted10 = encryption.encrypt(CCString10, nonceValue);
  }
  if (CCString11 != '' && NoEncrypt11 === false) {
  var encrypted11 = encryption.encrypt(CCString11, nonceValue);
  }
  if (CCString12 != '' && NoEncrypt12 === false) {
  var encrypted12 = encryption.encrypt(CCString12, nonceValue);
  }
  if (CCString12b != '' && NoEncrypt12b === false) {
  var encrypted12b = encryption.encrypt(CCString12b, nonceValue);
  }
  if (CCString13 != '' && NoEncrypt13 === false) {
  var encrypted13 = encryption.encrypt(CCString13, nonceValue);
  }
  if (CCString14 != '' && NoEncrypt14 === false) {
  var encrypted14 = encryption.encrypt(CCString14, nonceValue);
  }
  //console.log(encrypted); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt1").value = encrypted1;
  }
  if (CCString2 != '' && NoEncrypt2 === false) {
  document.getElementById("crypt2").value = encrypted2;
  }
  if (CCString3 != '' && NoEncrypt3 === false) {
  document.getElementById("crypt3").value = encrypted3;
  }
  if (CCString4 != '' && NoEncrypt4 === false) {
  document.getElementById("crypt4").value = encrypted4;
  }
  if (CCString5 != '' && NoEncrypt5 === false) {
  document.getElementById("crypt5").value = encrypted5;
  }
  if (CCString6 != '' && NoEncrypt6 === false) {
  document.getElementById("crypt6").value = encrypted6;
  }
  if (CCString7 != '' && NoEncrypt7 === false) {
  document.getElementById("crypt7").value = encrypted7;
  }
  if (CCString8 != '' && NoEncrypt8 === false) {
  document.getElementById("crypt8").value = encrypted8;
  }
  if (CCString9 != '' && NoEncrypt9 === false) {
  document.getElementById("crypt9").value = encrypted9;
  }
  if (CCString10 != '' && NoEncrypt10 === false) {
  document.getElementById("crypt10").value = encrypted10;
  }
  if (CCString11 != '' && NoEncrypt11 === false) {
  document.getElementById("crypt11").value = encrypted11;
  }
  if (CCString12 != '' && NoEncrypt12 === false) {
  document.getElementById("crypt12").value = encrypted12;
  }
  if (CCString12b != '' && NoEncrypt12b === false) {
  document.getElementById("crypt12b").value = encrypted12b;
  }
  if (CCString13 != '' && NoEncrypt13 === false) {
  document.getElementById("crypt13").value = encrypted13;
  }
  if (CCString14 != '' && NoEncrypt14 === false) {
  document.getElementById("crypt14").value = encrypted14;
  }
}

function bpsRootCCDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt1").value;
  var CCString2 = document.getElementById("crypt2").value;
  var CCString3 = document.getElementById("crypt3").value;  
  var CCString4 = document.getElementById("crypt4").value;
  var CCString5 = document.getElementById("crypt5").value;
  var CCString6 = document.getElementById("crypt6").value;
  var CCString7 = document.getElementById("crypt7").value;  
  var CCString8 = document.getElementById("crypt8").value;
  var CCString9 = document.getElementById("crypt9").value;  
  var CCString10 = document.getElementById("crypt10").value;
  var CCString11 = document.getElementById("crypt11").value;  
  var CCString12 = document.getElementById("crypt12").value;
  var CCString12b = document.getElementById("crypt12b").value;
  var CCString13 = document.getElementById("crypt13").value;
  var CCString14 = document.getElementById("crypt14").value;
  
  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  if (CCString2 != '') {
  var decrypted2 = encryption.decrypt(CCString2, nonceValue);
  }
  if (CCString3 != '') {
  var decrypted3 = encryption.decrypt(CCString3, nonceValue);
  }
  if (CCString4 != '') {
  var decrypted4 = encryption.decrypt(CCString4, nonceValue);
  }
  if (CCString5 != '') {
  var decrypted5 = encryption.decrypt(CCString5, nonceValue);
  }
  if (CCString6 != '') {
  var decrypted6 = encryption.decrypt(CCString6, nonceValue);
  }
  if (CCString7 != '') {
  var decrypted7 = encryption.decrypt(CCString7, nonceValue);
  }
  if (CCString8 != '') {
  var decrypted8 = encryption.decrypt(CCString8, nonceValue);
  }
  if (CCString9 != '') {
  var decrypted9 = encryption.decrypt(CCString9, nonceValue);
  }
  if (CCString10 != '') {
  var decrypted10 = encryption.decrypt(CCString10, nonceValue);
  }
  if (CCString11 != '') {
  var decrypted11 = encryption.decrypt(CCString11, nonceValue);
  }
  if (CCString12 != '') {
  var decrypted12 = encryption.decrypt(CCString12, nonceValue);
  }
  if (CCString12b != '') {
  var decrypted12b = encryption.decrypt(CCString12b, nonceValue);
  }
  if (CCString13 != '') {
  var decrypted13 = encryption.decrypt(CCString13, nonceValue);
  }
  if (CCString14 != '') {
  var decrypted14 = encryption.decrypt(CCString14, nonceValue);
  }
  //console.log(decrypted);
  
  if (CCString1 != '') {
  document.getElementById("crypt1").value = decrypted1;
  }
  if (CCString2 != '') {
  document.getElementById("crypt2").value = decrypted2;  
  }
  if (CCString3 != '') {
  document.getElementById("crypt3").value = decrypted3;  
  }
  if (CCString4 != '') {
  document.getElementById("crypt4").value = decrypted4;  
  }
  if (CCString5 != '') {
  document.getElementById("crypt5").value = decrypted5;  
  }
  if (CCString6 != '') {
  document.getElementById("crypt6").value = decrypted6;  
  }
  if (CCString7 != '') {
  document.getElementById("crypt7").value = decrypted7;  
  }
  if (CCString8 != '') {
  document.getElementById("crypt8").value = decrypted8;  
  }
  if (CCString9 != '') {
  document.getElementById("crypt9").value = decrypted9;  
  }
  if (CCString10 != '') {
  document.getElementById("crypt10").value = decrypted10;  
  }
  if (CCString11 != '') {
  document.getElementById("crypt11").value = decrypted11;
  }
  if (CCString12 != '') {
  document.getElementById("crypt12").value = decrypted12;  
  }
  if (CCString12b != '') {
  document.getElementById("crypt12b").value = decrypted12b;  
  }
  if (CCString13 != '') {
  document.getElementById("crypt13").value = decrypted13;  
  }
  if (CCString14 != '') {
  document.getElementById("crypt14").value = decrypted14;
  }
}
/* ]]> */
</script>

</div>
	
<?php 
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	
	if ( isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' ) {

	} else {
?>
    <h3><?php _e('wp-admin htaccess File Custom Code', 'bulletproof-security'); ?></h3>
<div id="cc-accordion-inner">

	<button onclick="bpsWpadminCCEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt Custom Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsWpadminCCDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt Custom Code', 'bulletproof-security'); ?></button>

<table width="100%" border="0" cellspacing="0" cellpadding="10" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    
<form name="bpsCustomCodeFormWPA" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
<?php 
	wp_nonce_field('bulletproof_security_CC_WPA'); 
	bpsPro_CC_WPA_values_form();
	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA'); 
	$bps_customcode_deny_files_wpa = ! isset($CC_Options_wpadmin['bps_customcode_deny_files_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_deny_files_wpa'];
	$bps_customcode_one_wpa = ! isset($CC_Options_wpadmin['bps_customcode_one_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_one_wpa'];
	$bps_customcode_two_wpa = ! isset($CC_Options_wpadmin['bps_customcode_two_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_two_wpa'];
	$bps_customcode_bpsqse_wpa = ! isset($CC_Options_wpadmin['bps_customcode_bpsqse_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'];

?>

<strong><label for="bps-CCode"><?php echo number_format_i18n( 1 ).'. '; _e('CUSTOM CODE WPADMIN DENY BROWSER ACCESS TO FILES:<br>Add additional wp-admin files that you would like to block here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire WPADMIN DENY BROWSER ACCESS TO FILES section of code from your wp-admin .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes. Add one pound sign # below to prevent the WPADMIN DENY BROWSER ACCESS TO FILES section of code from being created in your wp-admin .htaccess file', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt15" class="bps-text-area-custom-code" name="bps_customcode_deny_files_wpa" tabindex="1"><?php echo $bps_customcode_deny_files_wpa; ?></textarea>    
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:105px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you which wp-admin htaccess file code goes in the CUSTOM CODE WPADMIN DENY BROWSER ACCESS TO FILES text box. Go to the htaccess File Editor tab page and copy your actual WPADMIN DENY BROWSER ACCESS TO FILES wp-admin htaccess file code and paste it into the CUSTOM CODE text box to the left.</span>
    
<?php if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) { ?>   
    
<pre style="max-height:145px;"># WPADMIN DENY BROWSER ACCESS TO FILES<br /># Deny Browser access to /wp-admin/install.php<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1<br /># Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br /># BEGIN BPS WPADMIN DENY ACCESS TO FILES<br />&lt;FilesMatch &quot;^(install\.php)&quot;&gt;<br />&lt;IfModule mod_authz_core.c&gt;<br />Require all denied<br />#Require ip 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;IfModule !mod_authz_core.c&gt;<br />&lt;IfModule mod_access_compat.c&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;/IfModule&gt;<br />&lt;/FilesMatch&gt;<br /># END BPS WPADMIN DENY ACCESS TO FILES</pre>
    
<?php } elseif ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) { ?>

<pre style="max-height:145px;"># WPADMIN DENY BROWSER ACCESS TO FILES<br /># Deny Browser access to /wp-admin/install.php<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br /># BEGIN BPS WPADMIN DENY ACCESS TO FILES
&lt;FilesMatch &quot;^(install\.php)&quot;&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/FilesMatch&gt;<br /># END BPS WPADMIN DENY ACCESS TO FILES</pre>

<?php } ?> 
    
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 2 ).'. '; _e('CUSTOM CODE WPADMIN TOP:<br>wp-admin password protection & miscellaneous custom code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt16" class="bps-text-area-custom-code" name="bps_customcode_one_wpa" tabindex="2"><?php echo $bps_customcode_one_wpa; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you where your wp-admin custom htaccess code will be created in your wp-admin htaccess file. If you have custom wp-admin htaccess code, copy and paste it into the CUSTOM CODE WPADMIN TOP text box to the left.</span><pre># BEGIN OPTIONAL WP-ADMIN ADDITIONAL SECURITY MEASURES:<br /><br /># BEGIN CUSTOM CODE WPADMIN TOP<br /># Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.<br /><div style="background-color:#FFFF00;padding:3px;"># CCWTOP - Your custom code will be created here when you activate wp-admin BulletProof Mode</div># END CUSTOM CODE WPADMIN TOP</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 3 ).'. '; _e('CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES:<br>Add wp-admin plugin/file skip rules code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt17" class="bps-text-area-custom-code" name="bps_customcode_two_wpa" tabindex="3"><?php echo $bps_customcode_two_wpa; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you where your wp-admin plugin/file skip rules code will be created in your wp-admin htaccess file. If you have wp-admin plugin/file skip rules code, copy and paste it into the CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES text box to the left.</span><pre># BEGIN CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES<br /># To add wp-admin plugin skip/bypass rules use BPS wp-admin Custom Code.<br /># If a plugin is calling a wp-admin file in a way that it is being blocked/forbidden<br />...<br />...<br /><div style="background-color:#FFFF00;padding:3px;"># CCWPF - Your custom code will be created here when you activate wp-admin BulletProof Mode</div># END CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 4 ).'. '; _e('CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS:<br>Modify Query String Exploit code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire BPS QUERY STRING EXPLOITS section of code from your wp-admin .htaccess file from # BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS to # END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea id="crypt18" class="bps-text-area-custom-code" name="bps_customcode_bpsqse_wpa" tabindex="4"><?php echo $bps_customcode_bpsqse_wpa; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:105px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you which wp-admin htaccess file code goes in the CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS text box. Go to the htaccess File Editor tab page and copy your actual BPS QUERY STRING EXPLOITS AND FILTERS wp-admin htaccess file code and paste it into the CUSTOM CODE text box to the left.</span><pre># BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS<br /># WORDPRESS WILL BREAK IF ALL THE BPSQSE FILTERS ARE DELETED<br /># Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.<br />RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]<br />.....<br />.....<br />RewriteCond %{QUERY_STRING} (sp_executesql) [NC]<br />RewriteRule ^(.*)$ - [F]<br /># END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    
	<?php echo '<label for="bps-mscan-label" style="">'.__('If you are unable to save Custom Code and/or see an error message when trying to save Custom Code, click the Encrypt Custom Code button first and then click the Save wp-admin Custom Code button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your custom htaccess code unless you encrypt it first by clicking the Encrypt Custom Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt Custom Code button, but then want to add or edit additional custom code click the Decrypt Custom Code button. After you are done adding or editing custom code click the Encrypt Custom Code button before clicking the Save wp-admin Custom Code button.', 'bulletproof-security').'<br><br>'.__('Additional Encrypt and Decrypt buttons have been added at the top of the wp-admin Custom Code Form.', 'bulletproof-security').'<br><br>'.__('Click the Custom Code Read Me help button for more help info.', 'bulletproof-security').'</span></span></font></strong><br><br>'; ?>

    <input type="hidden" name="scrolltoCCodeWPA" value="<?php echo esc_html( $scrolltoCCodeWPA ); ?>" />
	<input type="submit" name="bps_customcode_submit_wpa" value="<?php esc_attr_e('Save wp-admin Custom Code', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('IMPORTANT!!! Did you remember to click the Encrypt Custom Code button first before saving your wp-admin Custom Code?', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to save your wp-admin Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>

	<button onclick="bpsWpadminCCEncrypt()" class="button bps-button"><?php esc_attr_e('Encrypt Custom Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsWpadminCCDecrypt()" class="button bps-button"><?php esc_attr_e('Decrypt Custom Code', 'bulletproof-security'); ?></button>

	</td>
    <td class="bps-table_cell_help_custom_code">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">&nbsp;</td>
    <td class="bps-table_cell_help">&nbsp;</td>
  </tr>
</table>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsCustomCodeFormWPA').submit(function(){ $('#scrolltoCCodeWPA').val( $('#bps_customcode_deny_files_wpa').scrollTop() ); });
	$('#bps_customcode_deny_files_wpa').scrollTop( $('#scrolltoCCodeWPA').val() ); 
});

function bpsWpadminCCEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt15").value;
  var CCString2 = document.getElementById("crypt16").value;
  var CCString3 = document.getElementById("crypt17").value;  
  var CCString4 = document.getElementById("crypt18").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);
  var NoEncrypt2 = CCString2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt2);
  var NoEncrypt3 = CCString3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt3);
  var NoEncrypt4 = CCString4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt4);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  if (CCString2 != '' && NoEncrypt2 === false) {
  var encrypted2 = encryption.encrypt(CCString2, nonceValue);
  }
  if (CCString3 != '' && NoEncrypt3 === false) {
  var encrypted3 = encryption.encrypt(CCString3, nonceValue);
  }
  if (CCString4 != '' && NoEncrypt4 === false) {
  var encrypted4 = encryption.encrypt(CCString4, nonceValue);
  }
  //console.log(encrypted); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt15").value = encrypted1;
  }
  if (CCString2 != '' && NoEncrypt2 === false) {
  document.getElementById("crypt16").value = encrypted2;
  }
  if (CCString3 != '' && NoEncrypt3 === false) {
  document.getElementById("crypt17").value = encrypted3;
  }
  if (CCString4 != '' && NoEncrypt4 === false) {
  document.getElementById("crypt18").value = encrypted4;
  }
}

function bpsWpadminCCDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt15").value;
  var CCString2 = document.getElementById("crypt16").value;
  var CCString3 = document.getElementById("crypt17").value;  
  var CCString4 = document.getElementById("crypt18").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  if (CCString2 != '') {
  var decrypted2 = encryption.decrypt(CCString2, nonceValue);
  }
  if (CCString3 != '') {
  var decrypted3 = encryption.decrypt(CCString3, nonceValue);
  }
  if (CCString4 != '') {
  var decrypted4 = encryption.decrypt(CCString4, nonceValue);
  }
  //console.log(decrypted);
  
  if (CCString1 != '') {
  document.getElementById("crypt15").value = decrypted1;
  }
  if (CCString2 != '') {
  document.getElementById("crypt16").value = decrypted2;  
  }
  if (CCString3 != '') {
  document.getElementById("crypt17").value = decrypted3;  
  }
  if (CCString4 != '') {
  document.getElementById("crypt18").value = decrypted4;  
  }
}
/* ]]> */
</script>

</div>

<?php } ?>
</div>