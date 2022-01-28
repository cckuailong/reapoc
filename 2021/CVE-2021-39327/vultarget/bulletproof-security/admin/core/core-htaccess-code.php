<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
/*****************************/
// BEGIN HTACCESS FILE WRITING
/*****************************/
function bpsPro_network_domain_check() {
	global $wpdb;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );
	return false;
}

function bpsPro_get_clean_basedomain() {
	if ( $existing_domain = bpsPro_network_domain_check() )
		return $existing_domain;
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	if ( $slash = strpos( $domain, '/' ) )
		$domain = substr( $domain, 0, $slash );
	return $domain;
}

	if ( is_multisite() ) {
	
	$hostname				= bpsPro_get_clean_basedomain();
	$slashed_home			= trailingslashit( get_option( 'home' ) );
	$base 					= parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix		= str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix			= str_replace( '\\', '/', ABSPATH );
	$home_path				= 0 === strpos( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$wp_siteurl_subdir		= preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base			= ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';
	$subdomain_install		= is_subdomain_install();
	$subdir_match			= $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?';
	$subdir_replacement_01	= $subdomain_install ? '' : '$1';
	$subdir_replacement_12	= $subdomain_install ? '$1' : '$2';
		
		$ms_files_rewriting = '';
		
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}wp-includes/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}
	}

$BPSCustomCodeOptions = get_option('bulletproof_security_options_customcode');
$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
$bps_auto_write_default_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';

$bpsFailMessageDef = '<font color="#fb0101"><strong>'.__('The file ', 'bulletproof-security').$bps_auto_write_default_file.__(' is not writable or does not exist.', 'bulletproof-security').'</strong></font><br><strong>'.__('Check that the file is named default.htaccess and that the file exists in the /bulletproof-security/admin/htaccess master folder. If this is not the problem click ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/read-me-first/" target="_blank">'.__('HERE', 'bulletproof-security').'</a>'.__(' to go the the BulletProof Security Forum.', 'bulletproof-security').'</strong><br>';

if ( ! is_multisite() && $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'] != '' ) {        
$bpsBeginWP = "# CUSTOM CODE WP REWRITE LOOP START\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_wp_rewrite_start'], ENT_QUOTES ) . "\n\n";
} else {
$bpsBeginWP = "# WP REWRITE LOOP START
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase $bps_get_wp_root_default
RewriteRule ^index\.php$ - [L]\n";
}

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

/** 
# secure.htaccess fwrite content for all WP site types 
**/
$bps_get_wp_root_secure = bps_wp_get_root_folder();
$bps_auto_write_secure_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';

$bpsFailMessageSec = '<font color="#fb0101"><strong>'.__('The file ', 'bulletproof-security').$bps_auto_write_secure_file.__(' is not writable or does not exist.', 'bulletproof-security').'</strong></font><br><strong>'.__('Check that the file is named secure.htaccess and that the file exists in the /bulletproof-security/admin/htaccess master folder. If this is not the problem click', 'bulletproof-security').' <a href="https://forum.ait-pro.com/read-me-first/" target="_blank">'.__('HERE', 'bulletproof-security').'</a>'.__(' to go the the BulletProof Security Forum.', 'bulletproof-security').'</strong><br>';

$bps_secure_content_top = "#   BULLETPROOF $bps_version SECURE .HTACCESS     \n\n";

if ( $BPSCustomCodeOptions['bps_customcode_one'] != '' ) {
$bps_secure_phpini_cache = "# CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE\n" . htmlspecialchars_decode( $BPSCustomCodeOptions['bps_customcode_one'], ENT_QUOTES ) . "\n\n";
} else {
$bps_secure_phpini_cache = "# PHP/PHP.INI HANDLER/CACHE CODE
# Use BPS Custom Code to add php/php.ini Handler and Cache htaccess code and to save it permanently.
# Most Hosts do not have/use/require php/php.ini Handler htaccess code\n\n";
}

if ( @$BPSCustomCodeOptions['bps_customcode_server_signature'] != '' ) {
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

if ( @$BPSCustomCodeOptions['bps_customcode_deny_dot_folders'] != '' ) {        
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

// RBM Deactivation: Single|Standard WordPress site type: Create default.htaccess Master File & copy it to the root folder
// .53.9: Copy the Custom default.htaccess file from: /bps-backup/master-backups/default.htaccess instead of using a BPS default default.htacces file.
if ( isset( $_POST['Submit-RBM-Deactivate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_rbm_deactivate' );

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$options = get_option('bulletproof_security_options_autolock');
	$DefaultHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
	$RootHtaccess = ABSPATH . '.htaccess';
	$permsRootHtaccess = @substr(sprintf('%o', fileperms($RootHtaccess)), -4);
	$sapi_type = php_sapi_name();	

	$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';
	
	if ( file_exists($custom_default_htaccess) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($RootHtaccess, 0644);
		}
		
		if ( ! copy($custom_default_htaccess, $RootHtaccess) ) {
			echo $bps_topDiv;
			$text = '<strong><font color="#fb0101">'.__('Failed to copy your Custom default.htaccess file: ', 'bulletproof-security').'</font>'.$custom_default_htaccess.__(' to: ', 'bulletproof-security').$RootHtaccess.__(' Check the folder permissions or Ownership for these folders.', 'bulletproof-security').'</strong>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
				@chmod($RootHtaccess, 0404);
			}
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode deactivated successfully. Your Custom default.htaccess is currently in use.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}		
	
	} else {

		$stringReplace = file_get_contents($bps_auto_write_default_file);

		if ( file_exists($bps_auto_write_default_file) ) {
			$stringReplace = $bps_default_content_top.$bps_default_content_bottom;
		
			if ( ! file_put_contents( $bps_auto_write_default_file, $stringReplace ) ) {
				echo $bps_topDiv;
    			echo $bpsFailMessageDef;
				echo $bps_bottomDiv;
			}

			if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
				@chmod($RootHtaccess, 0644);
			}

			if ( ! copy($DefaultHtaccess, $RootHtaccess) ) {
				echo $bps_topDiv;
				$text = '<font color="#fb0101"><strong>'.__('Failed to deactivate Root Folder BulletProof Mode. Unable to Copy the default.htaccess file to your root folder.', 'bulletproof-security').'</strong></font>';
				echo $text;
   				echo $bps_bottomDiv;
		
			} else {

				if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
					@chmod($RootHtaccess, 0404);
				}
			
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode deactivated successfully.', 'bulletproof-security').'</strong></font>';
				echo $text;
				echo $bps_bottomDiv;
			}
		}
	}
}

// RBM Activation: Single|Standard WordPress site type: Create secure.htaccess Master File & copy it to the root folder
if ( isset( $_POST['Submit-RBM-Activate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_rbm_activate' );

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$stringReplace = file_get_contents($bps_auto_write_secure_file);

	if ( file_exists($bps_auto_write_secure_file) ) {
		$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bps_secure_content_wpadmin.$bpsBeginWP.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bps_secure_wp_rewrite_loop_end.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;
		
		if ( ! file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
			echo $bps_topDiv;
    		echo $bpsFailMessageSec;
			echo $bps_bottomDiv;
		}
	
		$options = get_option('bulletproof_security_options_autolock');
		$RootHtaccess = ABSPATH . '.htaccess';
		$SecureHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
		$permsRootHtaccess = @substr(sprintf('%o', fileperms($RootHtaccess)), -4);
		$sapi_type = php_sapi_name();		
	
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($RootHtaccess, 0644);
		}		
		
		if ( ! copy($SecureHtaccess, $RootHtaccess) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate Root Folder BulletProof Mode protection. Your website is NOT protected.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
				@chmod($RootHtaccess, 0404);
			}
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode protection activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;

			// Save the Setup Wizard DB option only once if it does not already exist
			// A manual new setup of BPS was chosen instead of using the Setup Wizard
			// A BPS upgrade updates the value to: upgrade once if the option does not already exist.
			$bps_setup_wizard = 'bulletproof_security_options_wizard_free';
			$BPS_Wizard = array( 'bps_wizard_free' => 'manual' );	
	
			if ( ! get_option( $bps_setup_wizard ) ) {	
		
				foreach( $BPS_Wizard as $key => $value ) {
					update_option('bulletproof_security_options_wizard_free', $BPS_Wizard);
				}
			}
		}
	}
}

// RBM Deactivation: Network site type: Create default.htaccess Master File & copy it to the root folder
// .53.9: Copy the Custom default.htaccess file from: /bps-backup/master-backups/default.htaccess instead of using a BPS default default.htacces file.
if ( isset( $_POST['Submit-RBM-Deactivate-Network'] ) && current_user_can('manage_options')) {
	check_admin_referer( 'bulletproof_security_rbm_deactivate_network' );

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$options = get_option('bulletproof_security_options_autolock');
	$DefaultHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
	$RootHtaccess = ABSPATH . '.htaccess';
	$permsRootHtaccess = @substr(sprintf('%o', fileperms($RootHtaccess)), -4);
	$sapi_type = php_sapi_name();	

	$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';
	
	if ( file_exists($custom_default_htaccess) ) {
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($RootHtaccess, 0644);
		}
		
		if ( ! copy($custom_default_htaccess, $RootHtaccess) ) {
			echo $bps_topDiv;
			$text = '<strong><font color="#fb0101">'.__('Failed to copy your Custom default.htaccess file: ', 'bulletproof-security').'</font>'.$custom_default_htaccess.__(' to: ', 'bulletproof-security').$RootHtaccess.__(' Check the folder permissions or Ownership for these folders.', 'bulletproof-security').'</strong>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
				@chmod($RootHtaccess, 0404);
			}
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode deactivated successfully. Your Custom default.htaccess is currently in use.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}		
	
	} else {

		$stringReplace = file_get_contents($bps_auto_write_default_file);

		if ( file_exists($bps_auto_write_default_file) ) {
			$stringReplace = $bps_default_content_top.$bpsMUSDirTop.$bpsMUSDirBottom.$bpsMUEndWP;
		
			if ( ! file_put_contents( $bps_auto_write_default_file, $stringReplace ) ) {
    			echo $bps_topDiv;
    			echo $bpsFailMessageDef;
				echo $bps_bottomDiv;
			}

			if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
				@chmod($RootHtaccess, 0644);
			}

			if ( ! copy($DefaultHtaccess, $RootHtaccess) ) {
				echo $bps_topDiv;
				$text = '<font color="#fb0101"><strong>'.__('Failed to deactivate Root Folder BulletProof Mode. Unable to Copy the default.htaccess file to your root folder.', 'bulletproof-security').'</strong></font>';
				echo $text;
   				echo $bps_bottomDiv;
		
			} else {

				if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
					@chmod($RootHtaccess, 0404);
				}
			
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode deactivated successfully.', 'bulletproof-security').'</strong></font>';
				echo $text;
				echo $bps_bottomDiv;
			}
		}
	}
}

// RBM Activation: Network site type: Create secure.htaccess Master File & copy it to the root folder
if ( isset( $_POST['Submit-RBM-Activate-Network'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_rbm_activate_network' );

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$stringReplace = file_get_contents($bps_auto_write_secure_file);

	if ( file_exists($bps_auto_write_secure_file) ) {
		$stringReplace = $bps_secure_content_top.$bps_secure_phpini_cache.$bps_server_signature.$bps_secure_directory_list_index.$bps_secure_brute_force_login.$bps_secure_error_logging.$bps_secure_dot_server_files.$bpsMUSDirTop.$bps_secure_request_methods.$bps_secure_begin_plugins_skip_rules_text.$bps_secure_plugins_themes_skip_rules.$bps_secure_default_skip_rules.$bps_secure_timthumb_misc.$bps_secure_BPSQSE.$bpsMUSDirBottom.$bps_secure_deny_browser_access.$bps_secure_bottom_misc_code;

		if ( ! file_put_contents( $bps_auto_write_secure_file, $stringReplace ) ) {
			echo $bps_topDiv;
    		echo $bpsFailMessageSec;
			echo $bps_bottomDiv;
		}

		$options = get_option('bulletproof_security_options_autolock');
		$RootHtaccess = ABSPATH . '.htaccess';
		$SecureHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
		$permsRootHtaccess = @substr(sprintf('%o', fileperms($RootHtaccess)), -4);
		$sapi_type = php_sapi_name();		
	
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($RootHtaccess, 0644);
		}		
		
		if ( ! copy($SecureHtaccess, $RootHtaccess) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate Root Folder BulletProof Mode protection. Your website is NOT protected.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		
		} else {
			
			if ( @$permsRootHtaccess == '0644' && @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] == 'On' ) {
				@chmod($RootHtaccess, 0404);
			}
		
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Root Folder BulletProof Mode protection activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
    		echo $bps_bottomDiv;

			// Save the Setup Wizard DB option only once if it does not already exist
			// A manual new setup of BPS was chosen instead of using the Setup Wizard
			// A BPS upgrade updates the value to: upgrade once if the option does not already exist.
			$bps_setup_wizard = 'bulletproof_security_options_wizard_free';
			$BPS_Wizard = array( 'bps_wizard_free' => 'manual' );	
	
			if ( ! get_option( $bps_setup_wizard ) ) {	
		
				foreach( $BPS_Wizard as $key => $value ) {
					update_option('bulletproof_security_options_wizard_free', $BPS_Wizard);
				}
			}
		}
	}
}


/*****************************/
// END HTACCESS FILE WRITING
/*****************************/

?>