<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// Setup Wizard - Enable|Disable htaccess Files: htaccess Files Disabled 
// Deletes all htaccess files: /bps-backup/.htaccess, /master-backups/.htaccess, Root, /wp-admin/ and all BPS Core folder htaccess files.
function bpsSetupWizard_delete_htaccess_files() {

	if ( is_admin() && current_user_can('manage_options') ) {

	$bps_backup = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	$bps_master_backups = WP_CONTENT_DIR . '/bps-backup/master-backups/.htaccess';
	$root_htaccess = ABSPATH . '.htaccess';	
	$wpadmin_htaccess = ABSPATH . 'wp-admin/.htaccess';
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

		$files = array( $bps_backup, $bps_master_backups, $root_htaccess, $wpadmin_htaccess, $core1, $core2, $core3, $core4, $core5, $core6, $core7, $core8, $core9, $core10, $core11 );

		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
				
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {

			foreach ( $files as $file ) {

				if ( file_exists($file) ) {
					unlink($file);
				}
			}
			echo '<strong><font color="blue">'.__('htaccess Files Disabled: Existing BPS htaccess files have been deleted and new BPS htaccess files will not be created. All BPS htaccess features are disabled.', 'bulletproof-security').'</font></strong>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';		
		
		}
	}
}

function bpsPro_network_domain_check_wizard() {
	global $wpdb;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );
	return false;
}

function bpsPro_get_clean_basedomain_wizard() {
	if ( $existing_domain = bpsPro_network_domain_check_wizard() )
		return $existing_domain;
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	if ( $slash = strpos( $domain, '/' ) )
		$domain = substr( $domain, 0, $slash );
	return $domain;
}

// Setup Wizard - Create the secure.htaccess Master file and copy it to the WordPress installation folder 
function bpsSetupWizardCreateRootHtaccess() {
global $bps_version;

$bps_get_domain_root = bpsGetDomainRoot();
$bps_get_wp_root_default = bps_wp_get_root_folder();
// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
$successTextBegin = '<font color="green"><strong>';
$successTextEnd = '</strong></font><br>';
$failTextBegin = '<font color="#fb0101"><strong>';
$failTextEnd = '</strong></font><br>';	
	
	if ( is_multisite() ) {
	
	$hostname          = bpsPro_get_clean_basedomain_wizard();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix       = str_replace( '\\', '/', ABSPATH );
	$home_path         = 0 === strpos( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$wp_siteurl_subdir = preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base      = ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';
	$subdomain_install = is_subdomain_install();
	$subdir_match          = $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?';
	$subdir_replacement_01 = $subdomain_install ? '' : '$1';
	$subdir_replacement_12 = $subdomain_install ? '$1' : '$2';
		
		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}wp-includes/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}
	}

$BPSCustomCodeOptions = get_option('bulletproof_security_options_customcode');
$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
$bps_get_wp_root_secure = bps_wp_get_root_folder();
$bps_auto_write_secure_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
$bps_auto_write_secure_file_root = ABSPATH . '.htaccess';

$bpsSuccessMessageSec = '<font color="green"><strong>'.__('The secure.htaccess Root Master htaccess file was created successfully.', 'bulletproof-security').'<br>'.__('Root Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font><br>';

$bpsFailMessageSec = '<font color="#fb0101"><strong>'.__('Error: The secure.htaccess Root Master htaccess file and root .htaccess file cannot be created. Root Folder BulletProof Mode has NOT been activated.', 'bulletproof-security').'</strong></font><br><strong>'.__('If your Server configuration is DSO you must first make some one-time manual changes to your website before running the Setup Wizard. Please click this Forum Link for instructions: ', 'bulletproof-security').' <a href="https://forum.ait-pro.com/forums/topic/dso-setup-steps/" target="_blank" title="Link opens in a new Browser window">'.__('DSO Setup Steps', 'bulletproof-security').'</a></strong><br>';

if ( ! is_multisite() && $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'] != '' ) {        
$bpsBeginWP = "# CUSTOM CODE WP REWRITE LOOP START\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'], ENT_QUOTES ) . "\n\n";
} else {
$bpsBeginWP = "# WP REWRITE LOOP START
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase $bps_get_wp_root_default
RewriteRule ^index\.php$ - [L]\n";
}

// Network/Multisite all site types and versions
if ( is_multisite() ) {
if ( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'] != '' ) {    
$bpsMUSDirTop = "# CUSTOM CODE WP REWRITE LOOP START\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'], ENT_QUOTES ) . "\n\n";
} else {
$bpsMUSDirTop = "# WP REWRITE LOOP START
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase $bps_get_wp_root_default
RewriteRule ^index\.php$ - [L]\n
{$ms_files_rewriting}
# add a trailing slash to /wp-admin
RewriteRule ^{$subdir_match}wp-admin$ {$subdir_replacement_01}wp-admin/ [R=301,L]\n\n";
}

// Network/Multisite all site types and versions
if ( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_end'] != '' ) {    
$bpsMUSDirBottom = "# CUSTOM CODE WP REWRITE LOOP END\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_end'], ENT_QUOTES ) . "\n\n";
} else {
$bpsMUSDirBottom = "RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^{$subdir_match}(wp-(content|admin|includes).*) {$rewrite_base}{$subdir_replacement_12} [L]
RewriteRule ^{$subdir_match}(.*\.php)$ {$rewrite_base}$subdir_replacement_12 [L]
RewriteRule . index.php [L]
# WP REWRITE LOOP END\n";
}
}

$bps_secure_content_top = "#   BULLETPROOF $bps_version SECURE .HTACCESS     \n\n";

if ( $BPSCustomCodeOptions['bps_customcode_one'] != '' ) {
$bps_secure_phpini_cache = "# CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_one'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_phpini_cache = "# PHP/PHP.INI HANDLER/CACHE CODE
# Use BPS Custom Code to add php/php.ini Handler and Cache htaccess code and to save it permanently.
# Most Hosts do not have/use/require php/php.ini Handler htaccess code\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_server_signature'] != '' ) {
$bps_server_signature = "# CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_server_signature'], ENT_QUOTES ) . "\n\n";
} else {
$bps_server_signature = "# TURN OFF YOUR SERVER SIGNATURE
# Suppresses the footer line server version number and ServerName of the serving virtual host
ServerSignature Off\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_directory_index'] != '' ) {        
$bps_secure_directory_list_index = "# CUSTOM CODE DIRECTORY LISTING/DIRECTORY INDEX\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_directory_index'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_directory_list_index = "# DO NOT SHOW DIRECTORY LISTING
# Disallow mod_autoindex from displaying a directory listing
# If a 500 Internal Server Error occurs when activating Root BulletProof Mode 
# copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code 
# and paste it into BPS Custom Code and comment out Options -Indexes 
# by adding a # sign in front of it.
# Example: #Options -Indexes
Options -Indexes\n
# DIRECTORY INDEX FORCE INDEX.PHP
# Use index.php as default directory index file. index.html will be ignored.
# If a 500 Internal Server Error occurs when activating Root BulletProof Mode 
# copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code 
# and paste it into BPS Custom Code and comment out DirectoryIndex 
# by adding a # sign in front of it.
# Example: #DirectoryIndex index.php index.html /index.php
DirectoryIndex index.php index.html /index.php\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_server_protocol'] != '' ) {        
$bps_secure_brute_force_login = "# CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_server_protocol'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_brute_force_login = "# BRUTE FORCE LOGIN PAGE PROTECTION
# PLACEHOLDER ONLY
# Use BPS Custom Code to add Brute Force Login protection code and to save it permanently.
# See this link: https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/
# for more information.\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_error_logging'] != '' ) {        
$bps_secure_error_logging = "# CUSTOM CODE ERROR LOGGING AND TRACKING\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_error_logging'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_error_logging = "# BPS ERROR LOGGING AND TRACKING
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# BPS has premade 400 Bad Request, 403 Forbidden, 404 Not Found, 405 Method Not Allowed and 
# 410 Gone template logging files that are used to track and log 400, 403, 404, 405 and 410 errors 
# that occur on your website. When a hacker attempts to hack your website the hackers IP address, 
# Host name, Request Method, Referering link, the file name or requested resource, the user agent 
# of the hacker and the query string used in the hack attempt are logged.
# All BPS log files are htaccess protected so that only you can view them. 
# The 400.php, 403.php, 404.php, 405.php and 410.php files are located in /$bps_plugin_dir/bulletproof-security/
# The 400, 403, 405 and 410 Error logging files are already set up and will automatically start logging errors
# after you install BPS and have activated BulletProof Mode for your Root folder.
# If you would like to log 404 errors you will need to copy the logging code in the BPS 404.php file
# to your Theme's 404.php template file. Simple instructions are included in the BPS 404.php file.
# You can open the BPS 404.php file using the WP Plugins Editor or manually editing the file.
# NOTE: By default WordPress automatically looks in your Theme's folder for a 404.php Theme template file.\n
ErrorDocument 400 " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/400.php
ErrorDocument 401 default
ErrorDocument 403 " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/403.php
ErrorDocument 404 " . $bps_get_wp_root_secure . "404.php
ErrorDocument 405 " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php
ErrorDocument 410 " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/410.php\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_deny_dot_folders'] != '' ) {        
$bps_secure_dot_server_files = "# CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_deny_dot_folders'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_dot_server_files = "# DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# Files and folders starting with a dot: .htaccess, .htpasswd, .errordocs, .logs
RedirectMatch 403 \.(htaccess|htpasswd|errordocs|logs)$\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_admin_includes'] != '' ) {        
$bps_secure_content_wpadmin = "# CUSTOM CODE WP-ADMIN/INCLUDES\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_admin_includes'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_content_wpadmin = "# WP-ADMIN/INCLUDES
# Use BPS Custom Code to remove this code permanently.
RewriteEngine On
RewriteBase $bps_get_wp_root_secure
RewriteRule ^wp-admin/includes/ - [F]
RewriteRule !^wp-includes/ - [S=3]
RewriteRule ^wp-includes/[^/]+\.php$ - [F]
RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F]
RewriteRule ^wp-includes/theme-compat/ - [F]\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_request_methods'] != '' ) {        
$bps_secure_request_methods = "\n# CUSTOM CODE REQUEST METHODS FILTERED\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_request_methods'], ENT_QUOTES)."\n\n";
} else {
$bps_secure_request_methods = "\n# REQUEST METHODS FILTERED
# If you want to allow HEAD Requests use BPS Custom Code and copy 
# this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code 
# text box: CUSTOM CODE REQUEST METHODS FILTERED.
# See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.
RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]
RewriteRule ^(.*)$ - [F]
RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]
RewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]\n\n";
}

$bps_secure_begin_plugins_skip_rules_text = "# PLUGINS/THEMES AND VARIOUS EXPLOIT FILTER SKIP RULES
# To add plugin/theme skip/bypass rules use BPS Custom Code.
# The [S] flag is used to skip following rules. Skip rule [S=12] will skip 12 following RewriteRules.
# The skip rules MUST be in descending consecutive number order: 12, 11, 10, 9...
# If you delete a skip rule, change the other skip rule numbers accordingly.
# Examples: If RewriteRule [S=5] is deleted than change [S=6] to [S=5], [S=7] to [S=6], etc.
# If you add a new skip rule above skip rule 12 it will be skip rule 13: [S=13]\n\n";

// Plugin/Theme skip/bypass rules
$bps_secure_plugins_themes_skip_rules = '';
if ( $BPSCustomCodeOptions['bps_customcode_two'] != '' ) {
$bps_secure_plugins_themes_skip_rules = "# CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_two'], ENT_QUOTES ) . "\n\n";
}

$bps_secure_default_skip_rules = "# Adminer MySQL management tool data populate
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/adminer/ [NC]
RewriteRule . - [S=12]
# Comment Spam Pack MU Plugin - CAPTCHA images not displaying 
RewriteCond %{REQUEST_URI} ^". $bps_get_wp_root_secure . $bps_wpcontent_dir . "/mu-plugins/custom-anti-spam/ [NC]
RewriteRule . - [S=11]
# Peters Custom Anti-Spam display CAPTCHA Image
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/peters-custom-anti-spam-image/ [NC] 
RewriteRule . - [S=10]
# Status Updater plugin fb connect
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/fb-status-updater/ [NC] 
RewriteRule . - [S=9]
# Stream Video Player - Adding FLV Videos Blocked
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/stream-video-player/ [NC]
RewriteRule . - [S=8]
# XCloner 404 or 403 error when updating settings
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/xcloner-backup-and-restore/ [NC]
RewriteRule . - [S=7]
# BuddyPress Logout Redirect
RewriteCond %{QUERY_STRING} action=logout&redirect_to=http%3A%2F%2F(.*) [NC]
RewriteRule . - [S=6]
# redirect_to=
RewriteCond %{QUERY_STRING} redirect_to=(.*) [NC]
RewriteRule . - [S=5]
# Login Plugins Password Reset And Redirect 1
RewriteCond %{QUERY_STRING} action=resetpass&key=(.*) [NC]
RewriteRule . - [S=4]
# Login Plugins Password Reset And Redirect 2
RewriteCond %{QUERY_STRING} action=rp&key=(.*) [NC]
RewriteRule . - [S=3]\n\n";

if ( $BPSCustomCodeOptions['bps_customcode_timthumb_misc'] != '' ) {        
$bps_secure_timthumb_misc = "# CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_timthumb_misc'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_timthumb_misc = "# TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# Remote File Inclusion (RFI) security rules
# Note: Only whitelist your additional domains or files if needed - do not whitelist hacker domains or files
RewriteCond %{QUERY_STRING} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC,OR]
RewriteCond %{THE_REQUEST} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC]
RewriteRule .* index.php [F]
# 
# Example: Whitelist additional misc files: (example\.php|another-file\.php|phpthumb\.php|thumb\.php|thumbs\.php)
RewriteCond %{REQUEST_URI} (timthumb\.php|phpthumb\.php|thumb\.php|thumbs\.php) [NC]
# Example: Whitelist additional website domains: RewriteCond %{HTTP_REFERER} ^.*(YourWebsite.com|AnotherWebsite.com).*
RewriteCond %{HTTP_REFERER} ^.*" . $bps_get_domain_root . ".*
RewriteRule . - [S=1]\n\n";
}

if ( $BPSCustomCodeOptions['bps_customcode_bpsqse'] != '' ) {        
$bps_secure_BPSQSE = "# CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_bpsqse'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_BPSQSE = "# BEGIN BPSQSE BPS QUERY STRING EXPLOITS
# The libwww-perl User Agent is forbidden - Many bad bots use libwww-perl modules, but some good bots use it too.
# Good sites such as W3C use it for their W3C-LinkChecker. 
# Use BPS Custom Code to add or remove user agents temporarily or permanently from the 
# User Agent filters directly below or to modify/edit/change any of the other security code rules below.
RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\s+|%20+\\\\s+|\\\\s+%20+|\\\\s+%20+\\\\s+)(http|https)(:/|/) [NC,OR]
RewriteCond %{THE_REQUEST} etc/passwd [NC,OR]
RewriteCond %{THE_REQUEST} cgi-bin [NC,OR]
RewriteCond %{THE_REQUEST} (%0A|%0D|\\"."\\"."r|\\"."\\"."n) [NC,OR]
RewriteCond %{REQUEST_URI} owssvr\.dll [NC,OR]
RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_REFERER} \.opendirviewer\. [NC,OR]
RewriteCond %{HTTP_REFERER} users\.skynet\.be.* [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC,OR]
RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]
RewriteCond %{QUERY_STRING} ftp\: [NC,OR]
RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR] 
RewriteCond %{QUERY_STRING} \=\|w\| [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)/self/(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^s]*s)+cript.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*embed.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^e]*e)+mbed.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*object.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^o]*o)+bject.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*iframe.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^i]*i)+frame.*(>|%3E) [NC,OR] 
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code[^(]*\([^)]*\) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>|%3c|%3e).* [NC,OR]
RewriteCond %{QUERY_STRING} ^.*(\\x00|\\x04|\\x08|\\x0d|\\x1b|\\x20|\\x3c|\\x3e|\\x7f).* [NC,OR]
RewriteCond %{QUERY_STRING} (NULL|OUTFILE|LOAD_FILE) [OR]
RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{QUERY_STRING} concat[^\(]*\( [NC,OR]
RewriteCond %{QUERY_STRING} union([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} \-[sdcr].*(allow_url_include|allow_url_fopen|safe_mode|disable_functions|auto_prepend_file) [NC,OR]
RewriteCond %{QUERY_STRING} (;|<|>|'|".'"'."|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode) [NC,OR]
RewriteCond %{QUERY_STRING} (sp_executesql) [NC]
RewriteRule ^(.*)$ - [F]
# END BPSQSE BPS QUERY STRING EXPLOITS\n";
}

$bps_secure_wp_rewrite_loop_end = "RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . " . $bps_get_wp_root_secure . "index.php [L]
# WP REWRITE LOOP END\n";

if ( $BPSCustomCodeOptions['bps_customcode_deny_files'] != '' ) {        
$bps_secure_deny_browser_access = "\n# CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_deny_files'], ENT_QUOTES ) . "\n\n";

} else {

	if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
		$bps_secure_deny_browser_access = "\n# DENY BROWSER ACCESS TO THESE FILES 
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# wp-config.php, bb-config.php, php.ini, php5.ini, readme.html
# To be able to view these files from a Browser, replace 127.0.0.1 with your actual 
# current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1
# Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1 
# Note: The BPS System Info page displays which modules are loaded on your server. 

<FilesMatch \"^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)\">
<IfModule mod_authz_core.c>
Require all denied
#Require ip 127.0.0.1
</IfModule>

<IfModule !mod_authz_core.c>
<IfModule mod_access_compat.c>
Order Allow,Deny
Deny from all
#Allow from 127.0.0.1
</IfModule>
</IfModule>
</FilesMatch>\n\n";
	
	} else {
		
		$bps_secure_deny_browser_access = "\n# DENY BROWSER ACCESS TO THESE FILES 
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# wp-config.php, bb-config.php, php.ini, php5.ini, readme.html
# To be able to view these files from a Browser, replace 127.0.0.1 with your actual 
# current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1 
# Note: The BPS System Info page displays which modules are loaded on your server. 

<FilesMatch \"^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)\">
Order Allow,Deny
Deny from all
#Allow from 127.0.0.1
</FilesMatch>\n\n";		
	}
}

// CUSTOM CODE BOTTOM
$bps_secure_bottom_misc_code = '';
if ( $BPSCustomCodeOptions['bps_customcode_three'] != '' ) {
$bps_secure_bottom_misc_code = "# CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_three'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_bottom_misc_code = "# HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE
# PLACEHOLDER ONLY
# Use BPS Custom Code to add custom code and save it permanently here.\n";
}

	// A root htaccess file does NOT exist - create it
	// Do not lock the root htaccess file and do not display a message that the root htaccess file is not locked
	if ( ! file_exists($bps_auto_write_secure_file_root) ) {
		
		// Single/Standard WordPress site type: Create secure.htaccess Master File
		if ( ! is_multisite() ) {

			$stringReplace = file_get_contents($bps_auto_write_secure_file);

			if ( file_exists($bps_auto_write_secure_file) ) {
				$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bps_secure_content_wpadmin.$bpsBeginWP.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bps_secure_wp_rewrite_loop_end.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;		
		
				if ( file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
					@copy($bps_auto_write_secure_file, $bps_auto_write_secure_file_root);
    		
					echo $bpsSuccessMessageSec;
		
				} else {
		
    				echo $bpsFailMessageSec;
				}
			}
		}

		// Network site type: Create secure.htaccess Master File
		if ( is_multisite() && is_super_admin() ) { 

			$stringReplace = file_get_contents($bps_auto_write_secure_file);

			if ( file_exists($bps_auto_write_secure_file) ) {
				$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bpsMUSDirTop.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bpsMUSDirBottom.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;		
		
				if ( file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
					@copy($bps_auto_write_secure_file, $bps_auto_write_secure_file_root);
    		
					echo $bpsSuccessMessageSec;
		
				} else {
		
    				echo $bpsFailMessageSec;
				}
			}
		}
	} // end if ( ! file_exists($bps_auto_write_secure_file_root) ) {

	// A root htaccess file exists - backup the existing root htaccess file first.
	// Only create a new root htaccess file if the PHP/php.ini handler issue does not exist else return.
	// root htaccess file backup to /master-backups
	$bps_master_backup_root_file = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess';
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$rootHtaccessBackupTime = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess-'.date( 'Y-m-d-g-i-s-a', time() + $gmt_offset );
	
	if ( is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		@copy($bps_auto_write_secure_file_root, $bps_master_backup_root_file);
		// root htaccess file backup with timestamp: root.htaccess-2017-11-02-3-00-00
		copy($bps_auto_write_secure_file_root, $rootHtaccessBackupTime);
		echo $successTextBegin.$bps_master_backup_root_file.__(' Root .htaccess File backup Successful! ', 'bulletproof-security').$rootHtaccessBackupTime.$successTextEnd;
	}

	// PHP/php.ini handler check: continue or return and do not create a root htaccess file
	$rootHtaccessContents = @file_get_contents($bps_auto_write_secure_file_root);
	
	preg_match_all( '/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $rootHtaccessContents, $Rootmatches );
	preg_match_all( '/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $BPSCustomCodeOptions['bps_customcode_one'], $DBmatches );
		
	if ( $Rootmatches[0] && ! $DBmatches[0] ) {
		echo '<br><font color="#fb0101"><strong>'.__('Error: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</strong></font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code. A new root .htaccess file was NOT created to prevent a possible problem occurring on your website. Click this Forum Link ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/pre-installation-wizard-checks-phpphp-ini-handler-htaccess-code-check/" target="_blank" title="Link opens in a new Browser window"><strong>'.__('Add PHP/php.ini handler htaccess code to BPS Custom Code', 'bulletproof-security').'</a></strong>'.__(' for instructions on how to copy your PHP/php.ini handler htaccess code to BPS Custom Code.', 'bulletproof-security').'<br><br>';	
	
	return;
	}		
		
	$permsRootHtaccess = @substr(sprintf('%o', fileperms($bps_auto_write_secure_file_root)), -4);
	$sapi_type = php_sapi_name();
	$lock = '';
	
	if ( file_exists( $bps_auto_write_secure_file_root) && @$permsRootHtaccess == '0404' ) {
		$lock = '0404';
	} elseif ( file_exists( $bps_auto_write_secure_file_root) && @$permsRootHtaccess == '0444' ) {
		$lock = '0444';			
	} elseif ( file_exists( $bps_auto_write_secure_file_root) && @$permsRootHtaccess == '0604' ) {
		$lock = '0604';			
	} elseif ( file_exists( $bps_auto_write_secure_file_root) && @$permsRootHtaccess == '0644' ) {
		$lock = '0644';			
	}

	if ( file_exists( $bps_auto_write_secure_file_root) && @substr( $sapi_type, 0, 6) != 'apache' && @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777' ) { 
		@chmod($bps_auto_write_secure_file_root, 0644);
	}	

	// Single/Standard WordPress site type: Create secure.htaccess Master File
	if ( ! is_multisite() ) {

		$stringReplace = file_get_contents($bps_auto_write_secure_file);

		if ( file_exists($bps_auto_write_secure_file) ) {
			$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bps_secure_content_wpadmin.$bpsBeginWP.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bps_secure_wp_rewrite_loop_end.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;		
		
			if ( file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
				@copy($bps_auto_write_secure_file, $bps_auto_write_secure_file_root);
    		
				echo $bpsSuccessMessageSec;
		
			} else {
		
    			echo $bpsFailMessageSec;
			}
		}

		if ( @$lock == '0404' ) {	
			@chmod($bps_auto_write_secure_file_root, 0404);
			echo $successTextBegin.__('Root .htaccess File writing completed. File Locked with 404 file permissions.', 'bulletproof-security').$successTextEnd;
		}
		if ( @$lock == '0444' ) {	
			@chmod($bps_auto_write_secure_file_root, 0444);
			echo $successTextBegin.__('Root .htaccess File writing completed. File Locked with 444 file permissions.', 'bulletproof-security').$successTextEnd;
		}
	}

	// Network site type: Create secure.htaccess Master File
	if ( is_multisite() && is_super_admin() ) { 

		$stringReplace = file_get_contents($bps_auto_write_secure_file);

		if ( file_exists($bps_auto_write_secure_file) ) {
			$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bpsMUSDirTop.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bpsMUSDirBottom.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;		
		
			if ( file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
				@copy($bps_auto_write_secure_file, $bps_auto_write_secure_file_root);
    		
				echo $bpsSuccessMessageSec;
		
			} else {
		
    			echo $bpsFailMessageSec;
			}
		}
	
		if ( $lock == '0404' ) {	
			@chmod($bps_auto_write_secure_file_root, 0404);
			echo $successTextBegin.__('Root .htaccess File writing completed. File Locked with 404 file permissions.', 'bulletproof-security').$successTextEnd;
		}
		if ( $lock == '0444' ) {	
			@chmod($bps_auto_write_secure_file_root, 0444);
			echo $successTextBegin.__('Root .htaccess File writing completed. File Locked with 444 file permissions.', 'bulletproof-security').$successTextEnd;
		}	
	}

	// AutoLock: Off by default on new installations or echo saved DB option. 
	// A recommendation is made to lock and AutoLock the root htaccess file. each person needs to make that choice.
	// For 444 permissions do not do anything with lock or autolock settings
	if ( @$lock != '0444' ) {	
	
		$BPS_autolock_options = get_option('bulletproof_security_options_autolock');
		$bps_autolock_options = 'bulletproof_security_options_autolock';

		if ( ! get_option( $bps_autolock_options ) ) {	
		
			$bps_autolock_values = array( 'bps_root_htaccess_autolock' => 'Off' );
		
			foreach( $bps_autolock_values as $key => $value ) {
				update_option('bulletproof_security_options_autolock', $bps_autolock_values);
				echo $successTextBegin.$key.__(' DB Option created or updated Successfully!', 'bulletproof-security').$successTextEnd;	
			}
	
		} else {

			$bps_autolock_values = array( 'bps_root_htaccess_autolock' => $BPS_autolock_options['bps_root_htaccess_autolock'] );
		
			foreach( $bps_autolock_values as $key => $value ) {
				update_option('bulletproof_security_options_autolock', $bps_autolock_values);
				echo $successTextBegin.$key.__(' DB Option created or updated Successfully!', 'bulletproof-security').$successTextEnd;	
			}
		}
	}
	
	// 4.3: New check and recommendation to Lock the Root htaccess file and turn on AutoLock.
	if ( $lock == '0604' || $lock == '0644' ) {
		echo '<strong><font color="blue">'.__('Your current Root .htaccess file is not locked. It is recommended that you lock your Root .htaccess file on the htaccess Core > htaccess File Editor page. Click the Lock htaccess File and Turn On AutoLock buttons on the htaccess File Editor page.', 'bulletproof-security').'</font></strong><br>';
	}
}

// Setup Wizard - Create wpadmin-secure.htaccess htaccess file and copy it to the /wp-admin folder
function bpsSetupWizardCreateWpadminHtaccess() {
$options = get_option('bulletproof_security_options_customcode_WPA');  

$bpsSuccessMessageSec = '<font color="green"><strong>'.__('The wpadmin-secure.htaccess wp-admin Master htaccess file was created successfully.', 'bulletproof-security').'<br>'.__('wp-admin Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font><br>';

$bpsFailMessageSec = '<font color="#fb0101"><strong>'.__('Error: The wpadmin-secure.htaccess wp-admin Master htaccess file and wp-admin .htaccess file cannot be created. wp-admin Folder BulletProof Mode has NOT been activated.', 'bulletproof-security').'</strong></font><br><strong>'.__('If your Server configuration is DSO you must first make some one-time manual changes to your website before running the Setup Wizard. Please click this Forum Link for instructions: ', 'bulletproof-security').' <a href="https://forum.ait-pro.com/forums/topic/dso-setup-steps/" target="_blank" title="Link opens in a new Browser window">'.__('DSO Setup Steps', 'bulletproof-security').'</a></strong><br>';

	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		$text = '<font color="blue"><strong>'.__('Go Daddy Managed WordPress Hosting option is set to Yes or Enable|Disable wp-admin BulletProof Mode option is set to disabled. GDMW hosting does not allow wp-admin htaccess files.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	return;
	}

	$wpadminMasterHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
	$bps_master_backup_wpadmin_file = WP_CONTENT_DIR . '/bps-backup/master-backups/wpadmin.htaccess';
	$wpadminActiveHtaccess = ABSPATH . 'wp-admin/.htaccess';
	$permsHtaccess = '';
	if ( file_exists($wpadminActiveHtaccess) ) {
	$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminActiveHtaccess)), -4);
	}
	$sapi_type = php_sapi_name();
	$bpsString1 = "# CCWTOP";
	$bpsString2 = "# CCWPF";
	$bpsString3 = '/#\sBEGIN\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES(.*)#\sEND\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES/s';
	$bpsString4 = '/#\sBEGIN\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS(.*)#\sEND\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS/s';
	$bpsReplace1 = htmlspecialchars_decode($options['bps_customcode_one_wpa'], ENT_QUOTES);
	$bpsReplace2 = htmlspecialchars_decode($options['bps_customcode_two_wpa'], ENT_QUOTES);
	$bpsReplace3 = htmlspecialchars_decode($options['bps_customcode_deny_files_wpa'], ENT_QUOTES);	
	$bpsReplace4 = htmlspecialchars_decode($options['bps_customcode_bpsqse_wpa'], ENT_QUOTES);	
	
	// backup an existing wp-admin htaccess file first.
	if ( file_exists($wpadminActiveHtaccess) ) {

		if ( is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
			@copy($wpadminActiveHtaccess, $bps_master_backup_wpadmin_file);
			echo '<font color="green"><strong>'.$bps_master_backup_wpadmin_file.__(' wp-admin .htaccess File backup Successful!', 'bulletproof-security').'</strong></font><br>';
		}
	}
	
	if ( @substr($sapi_type, 0, 6) != 'apache' || file_exists($permsHtaccess) && @$permsHtaccess != '0666' || file_exists($permsHtaccess) && @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
		@chmod($wpadminActiveHtaccess, 0644);
	}

	if ( @copy($wpadminMasterHtaccess, $wpadminActiveHtaccess) ) {
		echo $bpsSuccessMessageSec;
	} else {
		echo $bpsFailMessageSec;	
	}
	
	if ( file_exists($wpadminActiveHtaccess) ) {
		$bpsBaseContent = @file_get_contents($wpadminActiveHtaccess);
		
		if ( $options['bps_customcode_deny_files_wpa'] != '') {        
			$bpsBaseContent = preg_replace('/#\sBEGIN\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES(.*)#\sEND\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES/s', $bpsReplace3, $bpsBaseContent);
		}
		
		if ( $options['bps_customcode_bpsqse_wpa'] != '') {        
			$bpsBaseContent = preg_replace('/#\sBEGIN\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS(.*)#\sEND\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS/s', $bpsReplace4, $bpsBaseContent);
		}
		$bpsBaseContent = str_replace($bpsString1, $bpsReplace1, $bpsBaseContent);
		$bpsBaseContent = str_replace($bpsString2, $bpsReplace2, $bpsBaseContent);
		@file_put_contents($wpadminActiveHtaccess, $bpsBaseContent);

	}
}

// Setup Wizard - Create the default.htaccess htaccess file
function bpsSetupWizardCreateDefaultHtaccess() {
global $bps_version;

$bps_get_wp_root_default = bps_wp_get_root_folder();
	
	if ( is_multisite() ) {
	
	$hostname          = bpsPro_get_clean_basedomain_wizard();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix       = str_replace( '\\', '/', ABSPATH );
	$home_path         = 0 === strpos( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$wp_siteurl_subdir = preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base      = ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';
	$subdomain_install = is_subdomain_install();
	$subdir_match          = $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?';
	$subdir_replacement_01 = $subdomain_install ? '' : '$1';
	$subdir_replacement_12 = $subdomain_install ? '$1' : '$2';
		
		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}wp-includes/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}
	}

$BPSCustomCodeOptions = get_option('bulletproof_security_options_customcode');

$bpsSuccessMessageSec = '<font color="green"><strong>'.__('The default.htaccess Master htaccess file was created successfully.', 'bulletproof-security').'</strong></font><br>';

$bpsFailMessageSec = '<font color="#fb0101"><strong>'.__('Error: The default.htaccess Master htaccess file cannot be created.', 'bulletproof-security').'</strong></font><br><strong>'.__('If your Server configuration is DSO you must first make some one-time manual changes to your website before running the Setup Wizard. Please click this Forum Link for instructions: ', 'bulletproof-security').' <a href="https://forum.ait-pro.com/forums/topic/dso-setup-steps/" target="_blank" title="Link opens in a new Browser window">'.__('DSO Setup Steps', 'bulletproof-security').'</a></strong><br>';

$bps_default_content_top = "#   BULLETPROOF DEFAULT .HTACCESS      \n
# WARNING!!! THE default.htaccess FILE DOES NOT PROTECT YOUR WEBSITE AGAINST HACKERS
# This is a standard generic htaccess file that does NOT provide any website security
# The DEFAULT .HTACCESS file should be used for testing and troubleshooting purposes only\n
# BEGIN BPS WordPress\n";

$bps_default_content_bottom = "<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase $bps_get_wp_root_default
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . " . $bps_get_wp_root_default . "index.php [L]
</IfModule>\n
# END BPS WordPress";

$bpsMUEndWP = "# END BPS WordPress";

// Network/Multisite all site types and versions
if ( is_multisite() ) {
if ( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'] != '' ) {    
$bpsMUSDirTop = "# CUSTOM CODE WP REWRITE LOOP START\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'], ENT_QUOTES ) . "\n\n";
} else {
$bpsMUSDirTop = "# WP REWRITE LOOP START
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase $bps_get_wp_root_default
RewriteRule ^index\.php$ - [L]\n
{$ms_files_rewriting}
# add a trailing slash to /wp-admin
RewriteRule ^{$subdir_match}wp-admin$ {$subdir_replacement_01}wp-admin/ [R=301,L]\n\n";
}

// Network/Multisite all site types and versions
if ( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_end'] != '' ) {    
$bpsMUSDirBottom = "# CUSTOM CODE WP REWRITE LOOP END\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_end'], ENT_QUOTES ) . "\n\n";
} else {
$bpsMUSDirBottom = "RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^{$subdir_match}(wp-(content|admin|includes).*) {$rewrite_base}{$subdir_replacement_12} [L]
RewriteRule ^{$subdir_match}(.*\.php)$ {$rewrite_base}$subdir_replacement_12 [L]
RewriteRule . index.php [L]
# WP REWRITE LOOP END\n";
}
}

	$bps_auto_write_default_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';

	// Single/Standard WordPress site type: Create default.htaccess Master File
	if ( ! is_multisite() ) {

		$stringReplace = file_get_contents($bps_auto_write_default_file);

	if ( file_exists($bps_auto_write_default_file) ) {
		$stringReplace = $bps_default_content_top.$bps_default_content_bottom;
		
		if ( file_put_contents( $bps_auto_write_default_file, $stringReplace ) ) {
    		
			echo $bpsSuccessMessageSec;
		
		} else {
		
    		echo $bpsFailMessageSec;
		}
	}
	}

	// Network site type: Create default.htaccess Master File
	if ( is_multisite() && is_super_admin() ) {

		$stringReplace = file_get_contents($bps_auto_write_default_file);

	if ( file_exists($bps_auto_write_default_file) ) {
		$stringReplace = $bps_default_content_top.$bpsMUSDirTop.$bpsMUSDirBottom.$bpsMUEndWP;
		
		if ( file_put_contents( $bps_auto_write_default_file, $stringReplace ) ) {
    		
			echo $bpsSuccessMessageSec;
		
		} else {
		
    		echo $bpsFailMessageSec;
		}
	}
	}
}

// Setup Wizard - DB Backup is setup in admin.php on BPS installation.
// if someone uninstalls BPS and runs the setup wizard again then the db options need to be updated
// with the db backup folder and db backup download URL
function bpsSetupWizard_dbbackup_folder_check() {
$successTextBegin = '<font color="green"><strong>';
$dbb_successMessage = __(' DB Option created or updated Successfully!', 'bulletproof-security');
$successMessage2 = __(' Folder created Successfully!', 'bulletproof-security');
$successTextEnd = '</strong></font><br>';
$failTextBegin = '<font color="#fb0101"><strong>';
$failTextEnd = '</strong></font><br>';

	if ( current_user_can('manage_options') ) {

		$DBBoptions = get_option('bulletproof_security_options_db_backup');
	
	if ( isset($DBBoptions['bps_db_backup_folder']) && $DBBoptions['bps_db_backup_folder'] != '' ) {	
		
		$DBB_Options = array(
		'bps_db_backup' 						=> $DBBoptions['bps_db_backup'], 
		'bps_db_backup_description' 			=> $DBBoptions['bps_db_backup_description'], 
		'bps_db_backup_folder' 					=> $DBBoptions['bps_db_backup_folder'], 
		'bps_db_backup_download_link' 			=> $DBBoptions['bps_db_backup_download_link'], 
		'bps_db_backup_job_type' 				=> $DBBoptions['bps_db_backup_job_type'], 
		'bps_db_backup_frequency' 				=> $DBBoptions['bps_db_backup_frequency'], 
		'bps_db_backup_start_time_hour' 		=> $DBBoptions['bps_db_backup_start_time_hour'], 
		'bps_db_backup_start_time_weekday' 		=> $DBBoptions['bps_db_backup_start_time_weekday'],  
		'bps_db_backup_start_time_month_date' 	=> $DBBoptions['bps_db_backup_start_time_month_date'], 
		'bps_db_backup_email_zip' 				=> $DBBoptions['bps_db_backup_email_zip'], 
		'bps_db_backup_delete' 					=> $DBBoptions['bps_db_backup_delete'], 
		'bps_db_backup_status_display' 			=> $DBBoptions['bps_db_backup_status_display'] 
		);
		
		echo $successTextBegin.$DBBoptions['bps_db_backup_folder'].$successMessage2.$successTextEnd;	
		
		foreach( $DBB_Options as $key => $value ) {
			update_option('bulletproof_security_options_db_backup', $DBB_Options);
			echo $successTextBegin.$key.$dbb_successMessage.$successTextEnd;	
		}		
	
	} else {

		$source = WP_CONTENT_DIR . '/bps-backup';

		if ( is_dir($source) ) {
		
			$iterator = new DirectoryIterator($source);
			
			foreach ( $iterator as $folder ) {
			
				if ( $folder->isDir() && ! $folder->isDot() && preg_match( '/backups_[a-zA-Z0-9]/', $folder ) ) {

					$bps_db_backup_folder = addslashes($source.DIRECTORY_SEPARATOR.$folder);
					$bps_db_backup_download_link = content_url( '/bps-backup/' ) . $folder . '/';
			
					$bps_db_backup_description = ! isset($DBBoptions['bps_db_backup_description']) ? '' : $DBBoptions['bps_db_backup_description'];
					$bps_db_backup_job_type = ! isset($DBBoptions['bps_db_backup_job_type']) ? '' : $DBBoptions['bps_db_backup_job_type'];
					$bps_db_backup_frequency = ! isset($DBBoptions['bps_db_backup_frequency']) ? '' : $DBBoptions['bps_db_backup_frequency'];
					$bps_db_backup_start_time_hour = ! isset($DBBoptions['bps_db_backup_start_time_hour']) ? '' : $DBBoptions['bps_db_backup_start_time_hour'];
					$bps_db_backup_start_time_weekday = ! isset($DBBoptions['bps_db_backup_start_time_weekday']) ? '' : $DBBoptions['bps_db_backup_start_time_weekday'];
					$bps_db_backup_start_time_month_date = ! isset($DBBoptions['bps_db_backup_start_time_month_date']) ? '' : $DBBoptions['bps_db_backup_start_time_month_date'];
					$bps_db_backup_email_zip = ! isset($DBBoptions['bps_db_backup_email_zip']) ? '' : $DBBoptions['bps_db_backup_email_zip'];
					$bps_db_backup_delete = ! isset($DBBoptions['bps_db_backup_delete']) ? '' : $DBBoptions['bps_db_backup_delete'];
					$bps_db_backup_status_display = ! isset($DBBoptions['bps_db_backup_status_display']) ? '' : $DBBoptions['bps_db_backup_status_display'];
					
					$DBB_Options = array( 
					'bps_db_backup' 						=> 'On', 
					'bps_db_backup_description' 			=> $bps_db_backup_description, 
					'bps_db_backup_folder' 					=> $bps_db_backup_folder, 
					'bps_db_backup_download_link' 			=> $bps_db_backup_download_link, 
					'bps_db_backup_job_type' 				=> $bps_db_backup_job_type, 
					'bps_db_backup_frequency' 				=> $bps_db_backup_frequency, 
					'bps_db_backup_start_time_hour' 		=> $bps_db_backup_start_time_hour, 
					'bps_db_backup_start_time_weekday' 		=> $bps_db_backup_start_time_weekday, 
					'bps_db_backup_start_time_month_date' 	=> $bps_db_backup_start_time_month_date, 
					'bps_db_backup_email_zip' 				=> $bps_db_backup_email_zip, 
					'bps_db_backup_delete' 					=> $bps_db_backup_delete, 
					'bps_db_backup_status_display' 			=> $bps_db_backup_status_display 
					);
	
					echo $successTextBegin.$bps_db_backup_folder.$successMessage2.$successTextEnd;

					foreach( $DBB_Options as $key => $value ) {
						update_option('bulletproof_security_options_db_backup', $DBB_Options);
						echo $successTextBegin.$key.$dbb_successMessage.$successTextEnd;	
					}			
				}
			}
		}
	}
	}
}

// Setup Wizard - Create/Recreate the User Agent filters in the 403.php file
function bpsSetupWizard_autoupdate_useragent_filters() {		
global $wpdb;
$table_name = $wpdb->prefix . "bpspro_seclog_ignore";
$blankFile = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/blank.txt';
$userAgentMaster = WP_CONTENT_DIR . '/bps-backup/master-backups/UserAgentMaster.txt';
$bps403File = WP_PLUGIN_DIR . '/bulletproof-security/403.php';
$search = '';		

	if ( ! file_exists($bps403File) ) {
		return;
	}
	
	if ( file_exists($blankFile) ) {
		copy($blankFile, $userAgentMaster);
	}

	$getSecLogTable = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_agent_bot LIKE %s", "%$search%" ) );
	$UserAgentRules = array();
	
	if ( $wpdb->num_rows == 0 ) {
		$text = '<strong><font color="green">'.__('Security Log User Agent Filter Check Successful! 0 User Agent Filters to update.', 'bulletproof-security').'</font></strong><br>';
		echo $text;	
	}
	
	if ( $wpdb->num_rows != 0 ) {

		foreach ( $getSecLogTable as $row ) {
			$UserAgentRules[] = "(.*)".$row->user_agent_bot."(.*)|";
			file_put_contents($userAgentMaster, $UserAgentRules);
		
			$text = '<strong><font color="green">'.__('Security Log User Agent Filter ', 'bulletproof-security').$row->user_agent_bot.__(' created or updated Successfully!', 'bulletproof-security').'</font></strong><br>';
			echo $text;
		}
	
	$UserAgentRulesT = file_get_contents($userAgentMaster);
	$stringReplace = file_get_contents($bps403File);

	$stringReplace = preg_replace('/# BEGIN USERAGENT FILTER(.*)# END USERAGENT FILTER/s', "# BEGIN USERAGENT FILTER\nif ( @!preg_match('/".trim($UserAgentRulesT, "|")."/', \$_SERVER['HTTP_USER_AGENT']) ) {\n# END USERAGENT FILTER", $stringReplace);
		
	file_put_contents($bps403File, $stringReplace);
		
	}
}

// Setup Wizard: pre-save Custom Code DB options for Custom Code Export|Import features if they do not exist
function bpsSetupWizardCustomCodePresave() {
				
	$bps_Root_CC_Options = 'bulletproof_security_options_customcode';

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> '', 
		'bps_customcode_server_signature' 	=> '', 
		'bps_customcode_directory_index' 	=> '', 
		'bps_customcode_server_protocol' 	=> '', 
		'bps_customcode_error_logging' 		=> '', 
		'bps_customcode_deny_dot_folders' 	=> '', 
		'bps_customcode_admin_includes' 	=> '', 
		'bps_customcode_wp_rewrite_start' 	=> '', 
		'bps_customcode_request_methods' 	=> '', 
		'bps_customcode_two' 				=> '', 
		'bps_customcode_timthumb_misc' 		=> '', 
		'bps_customcode_bpsqse' 			=> '', 
		'bps_customcode_deny_files' 		=> '', 
		'bps_customcode_three' 				=> ''
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> '', 
		'bps_customcode_server_signature' 	=> '', 
		'bps_customcode_directory_index' 	=> '', 
		'bps_customcode_server_protocol' 	=> '', 
		'bps_customcode_error_logging' 		=> '', 
		'bps_customcode_deny_dot_folders' 	=> '', 
		'bps_customcode_admin_includes' 	=> '', 
		'bps_customcode_wp_rewrite_start' 	=> '', 
		'bps_customcode_request_methods' 	=> '', 
		'bps_customcode_two' 				=> '', 
		'bps_customcode_timthumb_misc' 		=> '', 
		'bps_customcode_bpsqse' 			=> '', 
		'bps_customcode_wp_rewrite_end' 	=> '', 
		'bps_customcode_deny_files' 		=> '', 
		'bps_customcode_three' 				=> ''
		);					
	}

	if ( ! get_option( $bps_Root_CC_Options ) ) {			

		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}
	}

	$bps_wpadmin_CC_Options = 'bulletproof_security_options_customcode_WPA';			

	$wpadmin_CC_Options = array(
	'bps_customcode_deny_files_wpa' => '', 
	'bps_customcode_one_wpa' 		=> '', 
	'bps_customcode_two_wpa' 		=> '', 
	'bps_customcode_bpsqse_wpa' 	=> ''
	);
			
	if ( ! get_option( $bps_wpadmin_CC_Options ) ) {			
		
		foreach( $wpadmin_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
		}
	}
}

// Pre-save UI|UX DB option settings to avoid doing additional Form coding work for PHP 7.4.9 Notice errors
function bpsPro_presave_uiux_settings() {
	
	// Theme Skin
	$UITSoptions = get_option('bulletproof_security_options_theme_skin');
	$uits = ! isset($UITSoptions['bps_ui_theme_skin']) ? 'blue' : $UITSoptions['bps_ui_theme_skin'];
	$uits_options = array('bps_ui_theme_skin' => $uits);

	foreach( $uits_options as $key => $value ) {
		update_option('bulletproof_security_options_theme_skin', $uits_options);
	}

	// Turn On|Off The Inpage Status Display
	$UIStatus_display = get_option('bulletproof_security_options_status_display');
	$ui_status = ! isset($UIStatus_display['bps_status_display']) ? 'On' : $UIStatus_display['bps_status_display'];
	$ui_status_display = array('bps_status_display' => $ui_status);

	foreach( $ui_status_display as $key => $value ) {
		update_option('bulletproof_security_options_status_display', $ui_status_display);
	}

	// Processing Spinner
	$UISpinneroptions = get_option('bulletproof_security_options_spinner');
	$uips = ! isset($UISpinneroptions['bps_spinner']) ? 'On' : $UISpinneroptions['bps_spinner'];
	$uips_options = array('bps_spinner' => $uips);

	foreach( $uips_options as $key => $value ) {
		update_option('bulletproof_security_options_spinner', $uips_options);
	}

	// ScrollTop Animation
	$ScrollTopoptions = get_option('bulletproof_security_options_scrolltop');
	$uist = ! isset($ScrollTopoptions['bps_scrolltop']) ? 'On' : $ScrollTopoptions['bps_scrolltop'];
	$uist_options = array('bps_scrolltop' => $uist);

	foreach( $uist_options as $key => $value ) {
		update_option('bulletproof_security_options_scrolltop', $uist_options);
	}
	
	// WP Toolbar Functionality in BPS plugin pages		
	$UIWPToptions = get_option('bulletproof_security_options_wpt_nodes');
	$uiwpt = ! isset($UIWPToptions['bps_wpt_nodes']) ? 'allnodes' : $UIWPToptions['bps_wpt_nodes'];
	$uiwpt_options = array('bps_wpt_nodes' => $uiwpt);

	foreach( $uiwpt_options as $key => $value ) {
		update_option('bulletproof_security_options_wpt_nodes', $uiwpt_options);
	}		

	// Script|Style Loader Filter (SLF) In BPS Plugin Pages	
	$UISLFoptions = get_option('bulletproof_security_options_SLF');
	$uislf1 = ! isset($UISLFoptions['bps_slf_filter']) ? 'On' : $UISLFoptions['bps_slf_filter'];
	$uislf2 = ! isset($UISLFoptions['bps_slf_filter_new']) ? '14' : $UISLFoptions['bps_slf_filter_new'];	
	$uislf_options = array(
	'bps_slf_filter' 		=> $uislf1, 
	'bps_slf_filter_new' 	=> $uislf2
	);

	foreach( $uislf_options as $key => $value ) {
		update_option('bulletproof_security_options_SLF', $uislf_options);
	}

	// BPS UI|UX|AutoFix Debug
	$UIDebug_options = get_option('bulletproof_security_options_debug');
	$uidb = ! isset($UIDebug_options['bps_debug']) ? 'Off' : $UIDebug_options['bps_debug'];
	$uidb_options = array('bps_debug' => $uidb);

	foreach( $uidb_options as $key => $value ) {
		update_option('bulletproof_security_options_debug', $uidb_options);
	}
}

// Pre-save the Setup Wizard Options DB option settings to avoid doing additional Form coding work for PHP 7.4.9 Notice errors
function bpsPro_presave_setupwizard_option_settings() {
	
	// AutoFix
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	$swoaf = ! isset($AutoFix_Options['bps_wizard_autofix']) ? 'On' : $AutoFix_Options['bps_wizard_autofix'];
	$SWOAF_options = array('bps_wizard_autofix' => $swoaf);

	foreach( $SWOAF_options as $key => $value ) {
		update_option('bulletproof_security_options_wizard_autofix', $SWOAF_options);
	}		
	
	// GDPR Compliance
	$GDPR_Options = get_option('bulletproof_security_options_gdpr');	
	$swgdpr = ! isset($GDPR_Options['bps_gdpr_on_off']) ? 'Off' : $GDPR_Options['bps_gdpr_on_off'];
	$SWGDPR_options = array('bps_gdpr_on_off' => $swgdpr);

	foreach( $SWGDPR_options as $key => $value ) {
		update_option('bulletproof_security_options_gdpr', $SWGDPR_options);
	}		
	
	// GDMW 
	$GDMW_options = get_option('bulletproof_security_options_GDMW');
	$swgdmw = ! isset($GDMW_options['bps_gdmw_hosting']) ? 'no' : $GDMW_options['bps_gdmw_hosting'];
	$SWGDMW_options = array('bps_gdmw_hosting' => $swgdmw);

	foreach( $SWGDMW_options as $key => $value ) {
		update_option('bulletproof_security_options_GDMW', $SWGDMW_options);
	}		
	
	// Enable|Disable htaccess files
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');		
	$swhf = ! isset($HFiles_options['bps_htaccess_files']) ? 'enabled' : $HFiles_options['bps_htaccess_files'];
	$SWHF_options = array('bps_htaccess_files' => $swhf);

	foreach( $SWHF_options as $key => $value ) {
		update_option('bulletproof_security_options_htaccess_files', $SWHF_options);
	}		
				
	// Enable|Disable wp-admin BulletProof Mode
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$swwhf = ! isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) ? 'enabled' : $BPS_wpadmin_Options['bps_wpadmin_restriction'];
	$SWWHF_options = array('bps_wpadmin_restriction' => $swwhf);

	foreach( $SWWHF_options as $key => $value ) {
		update_option('bulletproof_security_options_htaccess_res', $SWWHF_options);
	}

	// Zip File Download Fix
	$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
	$swzd = ! isset($Zip_download_Options['bps_zip_download_fix']) ? 'Off' : $Zip_download_Options['bps_zip_download_fix'];
	$SWZD_options = array('bps_zip_download_fix' => $swzd);

	foreach( $SWZD_options as $key => $value ) {
		update_option('bulletproof_security_options_zip_fix', $SWZD_options);
	}		

	// Multisite Hide|Display System Info Page for Subsites
	$Mu_Sysinfo_page_options = get_option('bulletproof_security_options_mu_sysinfo');
	$swmus = ! isset($Mu_Sysinfo_page_options['bps_sysinfo_hide_display']) ? 'display' : $Mu_Sysinfo_page_options['bps_sysinfo_hide_display'];
	$SWMUS_options = array('bps_sysinfo_hide_display' => $swmus);

	foreach( $SWMUS_options as $key => $value ) {
		update_option('bulletproof_security_options_mu_sysinfo', $SWMUS_options);
	}	
}
?>