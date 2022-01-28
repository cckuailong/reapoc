<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// Displays Initial, Peak and Total Memory usage
function bpsPro_memory_resource_usage() {
	
	if ( is_admin() && current_user_can('manage_options') ) {

	$memory_usage_peak = memory_get_peak_usage();
	$mbytes_peak = number_format( $memory_usage_peak / ( 1024 * 1024 ), 2 );
	$kbytes_peak = number_format( $memory_usage_peak / ( 1024 ) );	
	
	$memory_usage = memory_get_usage();
	$mbytes = number_format( $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes = number_format( $memory_usage / ( 1024 ) );
	
	$mbytes_total = number_format( $memory_usage_peak / ( 1024 * 1024 ) - $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes_total = number_format( $memory_usage_peak / ( 1024 ) - $memory_usage / ( 1024 ) );	
	
	$usage = '<strong>'.__('Peak Memory Usage: ', 'bulletproof-security').'</strong>'. $mbytes_peak . __('MB|', 'bulletproof-security').$kbytes_peak.__('KB', 'bulletproof-security').'<br><strong>'.__('Initial Memory in Use: ', 'bulletproof-security').'</strong>'. $mbytes . __('MB|', 'bulletproof-security').$kbytes.__('KB', 'bulletproof-security').'<br><strong>'.__('Total Memory Used: ', 'bulletproof-security').'</strong>'. $mbytes_total . __('MB|', 'bulletproof-security').$kbytes_total.__('KB', 'bulletproof-security').'<br>';

	return $usage;
	}
}

// Logs Initial, Peak and Total Memory usage
function bpsPro_memory_resource_usage_logging() {
	
	$memory_usage_peak = memory_get_peak_usage();
	$mbytes_peak = number_format( $memory_usage_peak / ( 1024 * 1024 ), 2 );
	$kbytes_peak = number_format( $memory_usage_peak / ( 1024 ) );	
	
	$memory_usage = memory_get_usage();
	$mbytes = number_format( $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes = number_format( $memory_usage / ( 1024 ) );
	
	$mbytes_total = number_format( $memory_usage_peak / ( 1024 * 1024 ) - $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes_total = number_format( $memory_usage_peak / ( 1024 ) - $memory_usage / ( 1024 ) );	
	
	$usage = __('Peak Memory Usage: ', 'bulletproof-security'). $mbytes_peak . __('MB|', 'bulletproof-security').$kbytes_peak.__('KB', 'bulletproof-security')."\r\n".__('Initial Memory in Use: ', 'bulletproof-security'). $mbytes . __('MB|', 'bulletproof-security').$kbytes.__('KB', 'bulletproof-security')."\r\n".__('Total Memory Used: ', 'bulletproof-security'). $mbytes_total . __('MB|', 'bulletproof-security').$kbytes_total.__('KB', 'bulletproof-security');

	return $usage;
}

// BPS Master htaccess File Editing - file checks and get contents for editor
function bps_get_secure_htaccess() {
$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';

	if ( file_exists($secure_htaccess_file) ) {
		$bpsString = file_get_contents($secure_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The secure.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the secure.htaccess file exists and is named secure.htaccess.', 'bulletproof-security');
	}
}

function bps_get_default_htaccess() {
$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';

	if ( file_exists($default_htaccess_file) ) {
		$bpsString = file_get_contents($default_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The default.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the default.htaccess file exists and is named default.htaccess.', 'bulletproof-security');
	}
}

function bps_get_wpadmin_htaccess() {
$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';

	if ( file_exists($wpadmin_htaccess_file) ) {
		$bpsString = file_get_contents($wpadmin_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The wpadmin-secure.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the wpadmin-secure.htaccess file exists and is named wpadmin-secure.htaccess.', 'bulletproof-security');
	}
}

// The current active root htaccess file - file check
function bps_get_root_htaccess() {
$root_htaccess_file = ABSPATH . '.htaccess';
	
	if ( file_exists($root_htaccess_file) ) {
		$bpsString = file_get_contents($root_htaccess_file);
		echo $bpsString;
	} else {
		_e('An htaccess file was not found in your website root folder.', 'bulletproof-security');
	}
}

// The current active wp-admin htaccess file - file check
function bps_get_current_wpadmin_htaccess_file() {
$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($current_wpadmin_htaccess_file) ) {
		$bpsString = file_get_contents($current_wpadmin_htaccess_file);
		echo $bpsString;
	} else {
		_e('An htaccess file was not found in your wp-admin folder.', 'bulletproof-security');
	}
}

// File write checks for editor
function bps_secure_htaccess_file_check() {
$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
	
	if ( ! is_writable($secure_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the secure.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_default_htaccess_file_check() {
$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
	
	if ( ! is_writable($default_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the default.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_wpadmin_htaccess_file_check() {
$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
	
	if ( ! is_writable($wpadmin_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the wpadmin-secure.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_root_htaccess_file_check() {
$root_htaccess_file = ABSPATH . '.htaccess';
	
	if ( ! is_writable($root_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the Root htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_current_wpadmin_htaccess_file_check() {
$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
	
	if ( ! is_writable($current_wpadmin_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the wp-admin htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// Get Domain Root without prefix
function bpsGetDomainRoot() {

	if ( is_admin() && current_user_can('manage_options') ) {
	if ( isset( $_SERVER['SERVER_NAME'] ) ) {

		$ServerName = str_replace( 'www.', "", esc_html( $_SERVER['SERVER_NAME'] ) );
		return $ServerName;		
	
	} else {
		$ServerName = str_replace( 'www.', "", esc_html( $_SERVER['HTTP_HOST'] ) );
		return $ServerName;	
	}
	}
}

// File and Folder Permission Checking
function bps_check_perms($path, $perm) {
clearstatcache();
$current_perms = @substr(sprintf('%o', fileperms($path)), -4);
$stat = @stat($path);

	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . $path . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $perm . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $current_perms . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $stat['uid'] . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . @fileowner( $path ) . '</td>';
    echo '</tr>';
	echo '</table>';
}
	
// Get WordPress Root Installation Folder 
function bps_wp_get_root_folder() {

	if ( is_admin() && current_user_can('manage_options') ) {
		$site_root = parse_url(get_option('siteurl'));
	if ( isset( $site_root['path'] ) )
		$site_root = trailingslashit($site_root['path']);
	else
		$site_root = '/';
	return $site_root;
	}
}

// Display Root or Subfolder Installation Type
function bps_wp_get_root_folder_display_type() {
$site_root = parse_url(get_option('siteurl'));
	if ( isset( $site_root['path'] ) )
		$site_root = trailingslashit($site_root['path']);
	else
		$site_root = '/';
	if ( preg_match('/[a-zA-Z0-9]/', $site_root) ) {
		echo __('Subfolder Installation', 'bulletproof-security');
	} else {
		echo __('Root Folder Installation', 'bulletproof-security');
	}
}

// System Info page - Check for GWIOD
function bps_gwiod_site_type_check() {
$WordPress_Address_url = get_option('home');
$Site_Address_url = get_option('siteurl');
	
	if ( $WordPress_Address_url == $Site_Address_url ) {
		echo __('Standard WP Site Type', 'bulletproof-security');
	} else {
		echo __('GWIOD WP Site Type', 'bulletproof-security').'<br>';
		echo __('WordPress Address (URL): ', 'bulletproof-security').$WordPress_Address_url.'<br>';
		echo __('Site Address (URL): ', 'bulletproof-security').$Site_Address_url;
	}	
}

// System Info page - Check for BuddyPress
function bps_buddypress_site_type_check() {

	if ( function_exists('bp_is_active') ) {
		echo __('BuddyPress is installed|enabled', 'bulletproof-security');
	} else {
		echo __('BuddyPress is not installed|enabled', 'bulletproof-security');
	}
}

// System Info page - Check for bbPress
function bps_bbpress_site_type_check() {

	if ( function_exists('is_bbpress') ) {
		echo __('bbPress is installed|enabled', 'bulletproof-security');
	} else {
		echo __('bbPress is not installed|enabled', 'bulletproof-security');
	}
}

// System Info page - Check for Multisite/Subdirectory/Subdomain
function bps_multisite_check() {  
	
	if ( ! is_multisite() ) { 
		$text = __('Network|Multisite is not installed|enabled', 'bulletproof-security');
		echo $text;	
	
	} else {
		
		if ( ! is_subdomain_install() ) {
			$text = __('Subdirectory Site Type', 'bulletproof-security');
			echo $text;
		} else {
			$text = __('Subdomain Site Type', 'bulletproof-security');
			echo $text;			
		}
	}
}

// Get SQL Mode from WPDB
function bps_get_sql_mode() {
global $wpdb;
$sql_mode_var = 'sql_mode';
$mysqlinfo = $wpdb->get_results( $wpdb->prepare( "SHOW VARIABLES LIKE %s", $sql_mode_var ) );	
	
	if ( is_array( $mysqlinfo ) ) { 
		$sql_mode = $mysqlinfo[0]->Value;
		if ( empty( $sql_mode ) ) { 
			$sql_mode = __('Not Set', 'bulletproof-security');
		} else {
			$sql_mode = __('Off', 'bulletproof-security');
		}
	}
}

// Show DB errors should already be set to false in /includes/wp-db.php
// Extra function insurance show_errors = false
function bps_wpdb_errors_off() {
global $wpdb;
$wpdb->show_errors = false;
	
	if ( $wpdb->show_errors != false ) {
		$text = '<font color="#fb0101"><strong>'.__('DB Show Errors: On. DB errors will be displayed', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	} else {
		$text = '<strong>'.__('DB Show Errors: ', 'bulletproof-security').'</strong>'.__('Off', 'bulletproof-security').'<br>';
		echo $text;
	}	
}

// Maintenance Mode On Dashboard Alert
function bpsPro_mmode_dashboard_alert() {

if ( current_user_can('manage_options') ) {

	$MMoptions = get_option('bulletproof_security_options_maint_mode');

	if ( ! is_multisite() ) {
		
	if ( ! get_option('bulletproof_security_options_maint_mode') || $MMoptions['bps_maint_on_off'] == 'Off' ) {
	return;
	}	
	
		$indexPHP = ABSPATH . 'index.php';
		
		if ( file_exists($indexPHP) ) {
			$check_string_index = @file_get_contents($indexPHP);			
		}

		$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';		
		
		if ( file_exists($wpadminHtaccess) ) {
			$check_string_wpadmin = @file_get_contents($wpadminHtaccess);			
		}

		if ( $MMoptions['bps_maint_on_off'] == 'On' && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {	
	
			if ( strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( ! strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style=""background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}
		}
	}
	
	if ( is_multisite() ) {
		global $current_blog, $blog_id;

		$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
		if ( file_exists($root_folder_maintenance_values) ) {		
			$check_string_values = @file_get_contents($root_folder_maintenance_values);			
		}
		
		$indexPHP = ABSPATH . 'index.php';
		if ( file_exists($indexPHP) ) {
			$check_string_index = @file_get_contents($indexPHP);
		}
		
		$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
		if ( file_exists($wpadminHtaccess) ) {		
			$check_string_wpadmin = @file_get_contents($wpadminHtaccess);
		}

		if ( $blog_id == 1 && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {

			if ( strpos( $check_string_values, '$all_sites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for The Primary Site and All Subsites.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}
		
			if ( strpos( $check_string_values, '$all_subsites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for All Subsites, but Not The Primary Site.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}	
	
		if ( $MMoptions['bps_maint_on_off'] == 'On' ) {

			if ( strpos( $check_string_index, '$primary_site_status = \'On\';' ) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( !strpos($check_string_index, '$primary_site_status = \'On\';') && strpos($check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP") ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( strpos($check_string_index, '$primary_site_status = \'On\';') && strpos($check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP") ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}
		}
		}
	
		if ( $blog_id != 1 ) {
		
			if ( is_subdomain_install() ) {
		
				$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );	
	
			} else {
	
				$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
			}			
			
			$subsite_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php';		

			if ( strpos( $check_string_values, '$all_sites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for The Primary Site and All Subsites.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}
		
			if ( strpos( $check_string_values, '$all_subsites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for All Subsites, but Not The Primary Site.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}		
		
		if ( $MMoptions['bps_maint_on_off'] == 'On' && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {

			if ( file_exists($subsite_maintenance_file) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( ! file_exists($subsite_maintenance_file) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( file_exists($subsite_maintenance_file) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}		
		}
		}
	} // end is multisite
}
}

add_action('admin_notices', 'bpsPro_mmode_dashboard_alert');

// Login Security Disable Password Reset notice: Displays a message that backend password reset has been disabled
function bpsPro_login_security_password_reset_disabled_notice() {

	if ( current_user_can( 'update_core' ) ) {
	
		global $pagenow;

		if ( 'profile.php' == $pagenow || 'user-edit.php' == $pagenow || 'user-new.php' == $pagenow ) {
			$BPSoptions = get_option('bulletproof_security_options_login_security');		
		
			if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && $BPSoptions['bps_login_security_pw_reset'] == 'disable' ) {
		
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Login Security Disable Password Reset Frontend & Backend is turned On.', 'bulletproof-security').'</font><br>'.__('Backend Password Reset has been disabled. To enable Backend Password Reset click ', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'">'.esc_attr__('here', 'bulletproof-security').'</a></div>';
				echo $text;
			}
		}
	}
}

add_action('admin_notices', 'bpsPro_login_security_password_reset_disabled_notice');
add_action('network_admin_notices', 'bpsPro_login_security_password_reset_disabled_notice');

// One time manual htaccess code update added in BPS Pro .51.2
// NOTE: Instead of automating this, this needs to be done manually by users
// "Always On" flush_rewrite_rules code correction: Unfortunately this needs to be an "Always On" check in order for it to be 100% effective
// .52.9: added additional check for the BULLETPROOF string in the root htaccess file, otherwise on a new install the WP standard rewrite code will be deleted.
// .53: unlock the root htaccess file, autofix and lock the root htaccess file again.
function bpsPro_htaccess_manual_update_notice() {
	
	if ( current_user_can('manage_options') ) {
		
		$filename = ABSPATH . '.htaccess';
		
		if ( file_exists($filename) ) {
		
			$check_string = @file_get_contents($filename);
			$pattern = '/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/';

			if ( strpos( $check_string, "BULLETPROOF" ) && preg_match( $pattern, $check_string, $flush_matches ) ) {
				
				$root_perms = @substr(sprintf('%o', fileperms($filename)), -4);
				$sapi_type = php_sapi_name();
				$autolock = get_option('bulletproof_security_options_autolock');
	
				if 	( @$root_perms == '0404') {
					$lock = '0404';			
				}

				if ( @substr( $sapi_type, 0, 6 ) != 'apache' || @$root_perms != '0666' || @$root_perms != '0777' ) { // Windows IIS, XAMPP, etc
					@chmod($filename, 0644);
				}				
				
				$stringReplace = preg_replace('/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/', "", $check_string);			
			
				if ( file_put_contents($filename, $stringReplace) ) {
					
					if ( $autolock['bps_root_htaccess_autolock'] == 'On' || @$lock == '0404' ) {	
						@chmod($filename, 0404);
					}					
				}		
			}				
		
			global $pagenow;

			// manual steps if version of BPS root htaccess file is very old
			if ( 'plugins.php' == $pagenow || preg_match( '/page=bulletproof-security\/admin\/core\/core\.php/', esc_html( $_SERVER['REQUEST_URI'] ) ) ) {

				$pos = strpos( $check_string, 'IMPORTANT!!! DO NOT DELETE!!! - B E G I N Wordpress' );
			
				if ( $pos === false ) {
    		
					return;
			
				} else {
    		
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<strong><font color="blue">'.__('BPS Notice: One-time Update Steps Required', 'bulletproof-security').'</font><br>'.__('Significant changes were made to the root and wp-admin htaccess files that require doing the one-time Update Steps below.', 'bulletproof-security').'<br>'.__('All future BPS upgrades will not require these one-time Update Steps to be performed.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/root-and-wp-admin-htaccess-file-significant-changes/" target="_blank" title="Link opens in a new Browser window" style="text-decoration:underline;">'.__('Click Here', 'bulletproof-security').'</a>'.__(' If you would like to know what changes were made to the root and wp-admin htaccess files.', 'bulletproof-security').'<br>'.__('This Notice will go away automatically after doing all of the steps below.', 'bulletproof-security').'<br><br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'" style="text-decoration:underline;">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Security Modes page.', 'bulletproof-security').'<br>'.__('1. Click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'<br>'.__('2. Click the wp-admin Folder BulletProof Mode Activate button.', 'bulletproof-security').'</strong>';
					echo $text;
					echo '</p></div>';	
				}
			}
		}
	}
}

add_action('admin_notices', 'bpsPro_htaccess_manual_update_notice');

function bpsPro_presave_ui_theme_skin_options() {
	
	$ui_theme_skin_options = 'bulletproof_security_options_theme_skin';	
	$bps_ui_theme_skin = array( 'bps_ui_theme_skin' => 'blue' );
			
	if ( ! get_option( $ui_theme_skin_options ) ) {			
		
		foreach( $bps_ui_theme_skin as $key => $value ) {
			update_option('bulletproof_security_options_theme_skin', $bps_ui_theme_skin);
		}
	}	
}

## 3.5: Pre-Save the SLF filter options. Used in the Pre-Installation Wizard. 
## The default is now set to On. New option added to use to check against for BPS upgrades: bps_slf_filter_new
function bpsPro_presave_ui_theme_skin_SLF_options() {
	
	$bpsPro_SLF_options = get_option('bulletproof_security_options_SLF');
	
	if ( $bpsPro_SLF_options['bps_slf_filter_new'] != '14' ) {
	
		$BPS_SLF_Options = array( 
		'bps_slf_filter' 		=> 'On', 
		'bps_slf_filter_new' 	=> '14'
		);	

		foreach( $BPS_SLF_Options as $key => $value ) {
			update_option('bulletproof_security_options_SLF', $BPS_SLF_Options);
		}
	}	
}

// .52.9: POST Request Attack Protection code correction|addition
// .53: Condition added to allow commenting out wp-admin URI whitelist rule
function bpsPro_post_request_protection_check() {

	$pattern1 = '/BPS\sPOST\sRequest\sAttack\sProtection/';
	$pattern2 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	$pattern3 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}#{1,}(\s|){1,}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	
	if ( preg_match( $pattern3, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) ) {
		return;
	}

	if ( preg_match( $pattern1, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) && ! preg_match( $pattern2, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) ) {
		
		$bps_customcode_three = preg_replace('/RewriteCond\s%\{REQUEST_METHOD\}\sPOST\s\[NC\]/s', "RewriteCond %{REQUEST_METHOD} POST [NC]\n# NEVER COMMENT OUT THIS LINE OF CODE BELOW FOR ANY REASON\nRewriteCond %{REQUEST_URI} !^.*/wp-admin/ [NC]\n# Whitelist the WordPress Theme Customizer\nRewriteCond %{HTTP_REFERER} !^.*/wp-admin/customize.php", htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ) );

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_options['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_options['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_options['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_options['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_options['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_options['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_options['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_options['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_options['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_options['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_options['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $CC_options['bps_customcode_bpsqse'], 
		'bps_customcode_deny_files' 		=> $CC_options['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_options['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_options['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_options['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_options['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_options['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_options['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_options['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_options['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_options['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_options['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_options['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $CC_options['bps_customcode_bpsqse'], 
		'bps_customcode_wp_rewrite_end' 	=> $CC_options['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_options['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);					
	}

		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}
	}
}

// Check if WordPress Debugging & Debug Logging is turned on and display a message
// Note: cannot check defined('WP_DEBUG_DISPLAY') && true == WP_DEBUG_DISPLAY because it is turned On and is true by default.
function bpsPro_wp_debug_check() {

	if ( preg_match( '/page=bulletproof-security\/admin\/wizard\/wizard.php/', esc_html($_SERVER['QUERY_STRING']) ) ) {
		
		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
		}

		if ( defined('WP_DEBUG') && true == WP_DEBUG ) {
	
			$text = '<strong><font color="blue">'.__('WordPress Debugging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('Turn Off WordPress Debugging before running the Setup Wizard.', 'bulletproof-security').'<br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG\', true)'.__(' in your wp-config.php file. To turn WP Debugging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
			echo $text;
		}

		if ( defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
	
			$bpsPro_error_log_string = ini_get('error_log');
			$debug_log_wp = 'debug.log';
			$debug_log_pos = strpos( $bpsPro_error_log_string, $debug_log_wp );

			if ( $debug_log_pos !== false ) {
   			
				$text = '<strong><font color="blue">'.__('WordPress Debug Logging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('Turn Off WordPress Debug Logging before running the Setup Wizard.', 'bulletproof-security').'<br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG_LOG\', true)'.__(' in your wp-config.php file. To turn WP Debug Logging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
				echo $text;
			 
			}		
		}

		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '</p></div>';
		}

	} elseif ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['QUERY_STRING']), $matches) ) {
		
		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
		}	

		if ( defined('WP_DEBUG') && true == WP_DEBUG ) {
	
			$text = '<strong><font color="blue">'.__('WordPress Debugging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG\', true)'.__(' in your wp-config.php file. To turn WP Debugging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
			echo $text;
		}

		if ( defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
	
			$text = '<strong><font color="blue">'.__('WordPress Debug Logging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG_LOG\', true)'.__(' in your wp-config.php file to log errors to the WordPress debug.log file. To turn WP Debug Logging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
			echo $text;
			 
			}

		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '</p></div>';
		}
	}
}

add_action('admin_notices', 'bpsPro_wp_debug_check');

// .53.6: Reset php handler dismiss notice if Wordfence WAF disaster code is detected.
// Note: Has the extra added benefit of checking if the code was put in the correct CC text box.
function bpsPro_php_handler_dismiss_notice_reset() {
global $current_user;
$user_id = $current_user->ID;
$file = ABSPATH . '.htaccess';		
	
	if ( file_exists($file) ) {		

		$file_contents = @file_get_contents($file);
		$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
		preg_match( '/Wordfence WAF/', $CustomCodeoptions['bps_customcode_one'], $DBmatches );

		if ( stripos( $file_contents, "Wordfence WAF" ) && ! $DBmatches[0] ) {
			
			delete_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice');
		}
	}
}

// 2.0: Add R to the dumb downed Request Methods Filtered 405 htaccess code in Root Custom Code.
// 2.3: Revert: Remove R from the dumbed downed Request Methods Filtered 405 htaccess code in Root Custom Code.
// Add additional https scheme conditions to 3 htaccess security rules and combine 2 rules into 1 rule in Root and wp-admin Custom Code.
// Note: htmlspecialchars_decode() is not necessary.
// 3.9: removed the obsolete RMF code
function bpsPro_upgrade_CC_automatic_fix() {
	
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	//$pattern1 = '/RewriteRule\s\^\(\.\*\)\$(.*)\/bulletproof-security\/405\.php\s\[R,L\]/';
	$pattern2 = '/RewriteCond\s%\{THE_REQUEST\}\s\(\\\?.*%2a\)\+\(%20.*HTTP\(:\/.*\[NC,OR\]/';
	$pattern3 = '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=http:\/\/\s\[NC,OR\]/';
	$pattern4 = '/RewriteCond\s%\{QUERY_STRING\}\s\^\(\.\*\)cPath=http:\/\/\(\.\*\)\$\s\[NC,OR\]/';
	$pattern5 = '/RewriteCond\s%\{QUERY_STRING\}\shttp\\\:\s\[NC,OR\](.*\s*){1}.*RewriteCond\s%\{QUERY_STRING\}\shttps\\\:\s\[NC,OR\]/';

	/*
	if ( $CC_Options_root['bps_customcode_bpsqse'] != '' ) {

		if ( preg_match( $pattern1, $CC_Options_root['bps_customcode_request_methods'] ) ) {
			$bps_customcode_request_methods = preg_replace( $pattern1, "RewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]", $CC_Options_root['bps_customcode_request_methods']);
		}
	
	} else {
		$bps_customcode_request_methods = $CC_Options_root['bps_customcode_request_methods'];
	}
	*/

	if ( $CC_Options_root['bps_customcode_bpsqse'] != '' ) {

		if ( preg_match( $pattern2, $CC_Options_root['bps_customcode_bpsqse'], $matches ) ) {			
			$p2 = array($pattern2);
			$r2 = array("RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)(http|https)(:/|/) [NC,OR]");
		} else {
			$p2 = array();
			$r2 = array();
		}

		if ( preg_match( $pattern3, $CC_Options_root['bps_customcode_bpsqse'], $matches ) ) {			
			$p3 = array($pattern3);
			$r3 = array("RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]");
		} else {
			$p3 = array();
			$r3 = array();
		}

		if ( preg_match( $pattern4, $CC_Options_root['bps_customcode_bpsqse'], $matches ) ) {			
			$p4 = array($pattern4);
			$r4 = array("RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]");
		} else {
			$p4 = array();
			$r4 = array();
		}

		if ( preg_match( $pattern5, $CC_Options_root['bps_customcode_bpsqse'], $matches ) ) {			
			$p5 = array($pattern5);
			$r5 = array("RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]");
		} else {
			$p5 = array();
			$r5 = array();
		}

		$pattern_array = array_merge($p2, $p3, $p4, $p5);
		$replace_array = array_merge($r2, $r3, $r4, $r5);
		$bps_customcode_bpsqse_replace = preg_replace($pattern_array, $replace_array, $CC_Options_root['bps_customcode_bpsqse']);
	
	} else {
		$bps_customcode_bpsqse_replace = $CC_Options_root['bps_customcode_bpsqse'];
	}

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $bps_customcode_bpsqse_replace, 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $bps_customcode_bpsqse_replace, 
		'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);					
	}

	foreach( $Root_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode', $Root_CC_Options);
	}

	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA');
	
	if ( $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'] != '' ) {

		if ( preg_match( $pattern2, $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], $matches ) ) {			
			$p2 = array($pattern2);
			$r2 = array("RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\\s+|%20+\\\\\s+|\\\\\s+%20+|\\\\\s+%20+\\\\\s+)(http|https)(:/|/) [NC,OR]");
		} else {
			$p2 = array();
			$r2 = array();
		}

		if ( preg_match( $pattern3, $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], $matches ) ) {			
			$p3 = array($pattern3);
			$r3 = array("RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]");
		} else {
			$p3 = array();
			$r3 = array();
		}

		if ( preg_match( $pattern4, $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], $matches ) ) {			
			$p4 = array($pattern4);
			$r4 = array("RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]");
		} else {
			$p4 = array();
			$r4 = array();
		}

		if ( preg_match( $pattern5, $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], $matches ) ) {			
			$p5 = array($pattern5);
			$r5 = array("RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]");
		} else {
			$p5 = array();
			$r5 = array();
		}

		$pattern_array = array_merge($p2, $p3, $p4, $p5);
		$replace_array = array_merge($r2, $r3, $r4, $r5);
		$bps_customcode_bpsqse_wpa_replace = preg_replace($pattern_array, $replace_array, $CC_Options_wpadmin['bps_customcode_bpsqse_wpa']);
	
	} else {
		$bps_customcode_bpsqse_wpa_replace = $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'];
	}

	$wpadmin_CC_Options = array(
	'bps_customcode_deny_files_wpa' => $CC_Options_wpadmin['bps_customcode_deny_files_wpa'], 
	'bps_customcode_one_wpa' 		=> $CC_Options_wpadmin['bps_customcode_one_wpa'], 
	'bps_customcode_two_wpa' 		=> $CC_Options_wpadmin['bps_customcode_two_wpa'], 
	'bps_customcode_bpsqse_wpa' 	=> $bps_customcode_bpsqse_wpa_replace 
	);
			
	foreach( $wpadmin_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
	}
}

// Copies the new version of BPS MU Tools must-use plugin to the /mu-plugins/ folder on BPS upgrade
// 4.3: Delete the old bulletproof_security_options_autoupdate option & delete the bulletproof_security_options_MU_tools_free option
function bpsPro_mu_tools_plugin_copy() {
	
	delete_option('bulletproof_security_options_autoupdate');
	delete_option('bulletproof_security_options_MU_tools_free');
	
	$autoupdate_master_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-plugin-autoupdate.php';
	$autoupdate_muplugins_file = WP_CONTENT_DIR . '/mu-plugins/bps-plugin-autoupdate.php';
		
	if ( file_exists($autoupdate_master_file) ) {
		unlink($autoupdate_master_file);
	}
		
	if ( file_exists($autoupdate_muplugins_file) ) {
		unlink($autoupdate_muplugins_file);
	}
		
	if ( ! is_dir( WP_CONTENT_DIR . '/mu-plugins' ) ) {
		mkdir( WP_CONTENT_DIR . '/mu-plugins', 0755, true );
		chmod( WP_CONTENT_DIR . '/mu-plugins/', 0755 );
	}
		
	$BPS_MU_tools = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-mu-tools.php';
	$BPS_MU_tools_copy = WP_CONTENT_DIR . '/mu-plugins/bps-mu-tools.php';

	if ( file_exists($BPS_MU_tools_copy) ) {
		
		$check_string = @file_get_contents($BPS_MU_tools_copy);
		$pos1 = strpos( $check_string, 'Version: 1.0' );
		$pos2 = strpos( $check_string, 'Version: 2.0' );
		$pos3 = strpos( $check_string, 'Version: 3.0' );
		$pos4 = strpos( $check_string, 'Version: 4.0' );
		$pos5 = strpos( $check_string, 'Version: 5.0' );
				
		if ( $pos1 !== false || $pos2 !== false || $pos3 !== false || $pos4 !== false || $pos5 !== false ) {
			@copy($BPS_MU_tools, $BPS_MU_tools_copy);			
		}
	}
}

// Get any new dirs that have been created and remove any old dirs from the bps_mscan_dirs db option.
// Also used in Setup Wizard: need to add setup wizard condition to display saved or updated db options etc.
// Note: MScan Status db options do not need to be pre-saved. Will use bps_mscan_status == '' for display.
// 2.6: open_basedir "fix" added
// This function is run in the BPS upgrade function and in the Setup Wizard ONLY.
function bpsPro_presave_mscan_options() {
	
	$MScan_options = get_option('bulletproof_security_options_MScan'); 
	$wp_abspath_forward_slashes = str_replace( '\\', '/', ABSPATH );
	$wp_install_folder = str_replace( array( get_home_path(), '/', ), "", $wp_abspath_forward_slashes );
	$wp_includes_forward_slashes = $wp_abspath_forward_slashes . WPINC;
	$wp_includes_folder = str_replace( $wp_abspath_forward_slashes, "", $wp_includes_forward_slashes );
	
	$source = $wp_abspath_forward_slashes;
	$dir_array = array();
	
	if ( is_dir($source) ) {
		
		$iterator = new DirectoryIterator($source);	
		
		foreach ( $iterator as $files ) {
			try {			
				if ( $files->isDir() && ! $files->isDot() ) {
	
					if ( ! empty( $files ) ) {
						$dir_array[] = $files->getFilename();
					}
				}
			} catch (RuntimeException $e) {   
				
			}
		}
	}

	$get_home_path = get_home_path();
	$home_dir_array = array();
	
	if ( $wp_abspath_forward_slashes != $get_home_path ) {

		if ( is_dir($get_home_path) ) {
			
			$iterator = new DirectoryIterator($get_home_path);	
			
			foreach ( $iterator as $files ) {
				try {			
					if ( $files->isDir() && ! $files->isDot() ) {
						
						if ( $wp_install_folder != $files->getFilename() && $bps_wpcontent_dir != $files->getFilename() && $wp_includes_folder != $files->getFilename() && 'wp-admin' != $files->getFilename()) {
		
							if ( ! empty( $files ) ) {
								$home_dir_array[] = $files->getFilename();
							}
						}
					}
				} catch (RuntimeException $e) {   
					
				}
			}
		}
	}
	
	$dir_array_merge = array_merge($dir_array, $home_dir_array);
	$dir_flip = array_flip($dir_array_merge);
		
	// replace values in all dirs found except for other WP site folders.
	$mscan_actual_dirs = array();
	$pattern = '/define\((\s|)\'WP_USE_THEMES/';
	
	foreach ( $dir_flip as $key => $value ) {
			
		$wp_index_file = $source . $key . '/index.php';
		$wp_blog_header_file = $source . $key . '/wp-blog-header.php';
		$wp_cron_file = $source . $key . '/wp-cron.php';
		$wp_load_file = $source . $key . '/wp-load.php';
		$wp_login_file = $source . $key . '/wp-login.php';
		$wp_settings_file = $source . $key . '/wp-settings.php';
		
		$home_wp_index_file = $get_home_path . $key . '/index.php';
		$home_wp_blog_header_file = $get_home_path . $key . '/wp-blog-header.php';
		$home_wp_cron_file = $get_home_path . $key . '/wp-cron.php';
		$home_wp_load_file = $get_home_path . $key . '/wp-load.php';
		$home_wp_login_file = $get_home_path . $key . '/wp-login.php';
		$home_wp_settings_file = $get_home_path . $key . '/wp-settings.php';

		if ( file_exists($wp_index_file) ) {
			$check_string = file_get_contents($wp_index_file);
		}
				
		if ( file_exists($home_wp_index_file) ) {
			$home_check_string = file_get_contents($home_wp_index_file);
		}

		if ( file_exists($wp_index_file) && preg_match( $pattern, $check_string ) && file_exists($wp_blog_header_file) && file_exists($wp_cron_file) && file_exists($wp_load_file) && file_exists($wp_login_file) && file_exists($wp_settings_file) || file_exists($home_wp_index_file) && preg_match( $pattern, $home_check_string ) && file_exists($home_wp_blog_header_file) && file_exists($home_wp_cron_file) && file_exists($home_wp_load_file) && file_exists($home_wp_login_file) && file_exists($home_wp_settings_file) ) {
			$mscan_actual_dirs[$key] = preg_replace( '/\d+/', "", $value );
		} else {
			$mscan_actual_dirs[$key] = preg_replace( '/\d+/', "1", $value );
		}
	}
				
	// This is not a mistake/redundant. See the else condition.
	if ( ! empty($MScan_options['bps_mscan_dirs']) ) {
	
		$mscan_dirs_options_inner_array = array();
			
		foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {			
			
			$wp_index_file = $source . $key . '/index.php';
			$home_wp_index_file = $get_home_path . $key . '/index.php';
			
			if ( file_exists($wp_index_file) ) {
				$check_string = file_get_contents($wp_index_file);
			}
					
			if ( file_exists($home_wp_index_file) ) {
				$home_check_string = file_get_contents($home_wp_index_file);
			}

			if ( file_exists($wp_index_file) && preg_match( $pattern, $check_string ) || file_exists($home_wp_index_file) && preg_match( $pattern, $home_check_string ) ) {
				$mscan_dirs_options_inner_array[$key] = '';
			} else {
				$mscan_dirs_options_inner_array[$key] = $value;
			}
		}

		// get new dirs found that do not exist in the bps_mscan_dirs db option. ie a new dir has been created.
		$mscan_diff_key_dir = array_diff_key($mscan_actual_dirs, $mscan_dirs_options_inner_array);

		// get old dirs that still exist in the bps_mscan_dirs db option. ie a dir has been deleted.
		$mscan_diff_key_options = array_diff_key($mscan_dirs_options_inner_array, $dir_flip);

		if ( ! empty($mscan_diff_key_options) ) {
	
			foreach ( $mscan_diff_key_options as $key => $value ) {
				unset($mscan_dirs_options_inner_array[$key]);
			}

			// merge any new dirs found
			$mscan_array_merge = array_merge( $mscan_diff_key_dir, $mscan_dirs_options_inner_array );
			ksort($mscan_array_merge);

		} else {
	
			// merge any new dirs found
			$mscan_array_merge = array_merge( $mscan_diff_key_dir, $mscan_dirs_options_inner_array );
			ksort($mscan_array_merge);		
		}

	} else {
		$mscan_array_merge = $mscan_actual_dirs;
		ksort($mscan_array_merge);
	}

	if ( ! isset($MScan_options['mscan_file_size_limit_hidden']) ) {
		$mscan_max_file_size = '1000';
		$mscan_file_size_limit_hidden = '14';
	} else {
		$mscan_max_file_size = $MScan_options['mscan_max_file_size'];
		$mscan_file_size_limit_hidden = '14';
	}
	
	$mscan_max_time_limit = ! isset($MScan_options['mscan_max_time_limit']) ? '300' : $MScan_options['mscan_max_time_limit'];
	$mscan_scan_database = ! isset($MScan_options['mscan_scan_database']) ? 'On' : $MScan_options['mscan_scan_database'];
	$mscan_scan_skipped_files = ! isset($MScan_options['mscan_scan_skipped_files']) ? 'Off' : $MScan_options['mscan_scan_skipped_files'];
	$mscan_scan_delete_tmp_files = ! isset($MScan_options['mscan_scan_delete_tmp_files']) ? 'Off' : $MScan_options['mscan_scan_delete_tmp_files'];
	$mscan_scan_frequency = ! isset($MScan_options['mscan_scan_frequency']) ? 'Off' : $MScan_options['mscan_scan_frequency'];
	$mscan_exclude_dirs = ! isset($MScan_options['mscan_exclude_dirs']) ? '' : $MScan_options['mscan_exclude_dirs'];
	$mscan_exclude_tmp_files = ! isset($MScan_options['mscan_exclude_tmp_files']) ? '' : $MScan_options['mscan_exclude_tmp_files'];
	
	$MS_Options = array(
	'bps_mscan_dirs' 				=> $mscan_array_merge, 
	'mscan_max_file_size' 			=> $mscan_max_file_size, 
	'mscan_max_time_limit' 			=> $mscan_max_time_limit, 
	'mscan_scan_database' 			=> $mscan_scan_database, 
	'mscan_scan_images' 			=> 'Off', 
	'mscan_scan_skipped_files' 		=> $mscan_scan_skipped_files, 
	'mscan_scan_delete_tmp_files' 	=> $mscan_scan_delete_tmp_files, 
	'mscan_scan_frequency' 			=> $mscan_scan_frequency, 
	'mscan_exclude_dirs' 			=> $mscan_exclude_dirs, 
	'mscan_exclude_tmp_files' 		=> $mscan_exclude_tmp_files, 
	'mscan_file_size_limit_hidden' 	=> $mscan_file_size_limit_hidden 
	);		
	
	$mscan_successMessage = __(' DB Option created or updated Successfully!', 'bulletproof-security');
	$mscan_dir_successMessage = __(' Hosting Account Root Folder Option setup or updated Successfully!', 'bulletproof-security');
	$successTextBegin = '<font color="green"><strong>';
	$successTextEnd = '</strong></font><br>';

	foreach( $MS_Options as $key => $value ) {
		update_option('bulletproof_security_options_MScan', $MS_Options);
		
		if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' ) {
			//echo $successTextBegin.$key.$mscan_successMessage.$successTextEnd;
		}
	}

	if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' ) {
		$MScan_options = get_option('bulletproof_security_options_MScan');
		
		if ( $MScan_options['bps_mscan_dirs'] != '' ) {
			
			foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {			
				//echo $successTextBegin.$key.$mscan_dir_successMessage.$successTextEnd;
			}
		}
	}
}

// 4.4: BugFix: Remove the whitespace between WP and Rocket skip/bypass rule if it exists so that the HUD AutoFix function does not detect it.
// Root htaccess file and Custom Code.
// Note: This BPS upgrade function fires right before the root htaccess file is unlocked in the root htaccess file upgrade function.
function bpsPro_wp_rocket_marker_fix() {
	
	$rootHtaccess = ABSPATH . '.htaccess';			
		
	if ( file_exists($rootHtaccess) ) {
		
		$sapi_type = php_sapi_name();
		$lock = '';
		$permsRootHtaccess = @substr(sprintf('%o', fileperms($rootHtaccess)), -4);
		
		if 	( @$permsRootHtaccess == '0404') {
			$lock = '0404';			
		}
		
		if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777' ) {
			chmod( $rootHtaccess, 0644 );
		}
		
		$root_htaccess_file_contents = @file_get_contents($rootHtaccess);	
	
		if ( preg_match( '/#\sWP\sRocket\splugin\sskip\/bypass\srule(\s*){1}RewriteCond(.*)wp-rocket\/\s\[NC\](\s*){1}RewriteRule\s\.\s\-\s\[S=\d{1,2}\]/s', $root_htaccess_file_contents, $matches ) ) {
			
			$root_htaccess_file_contents = preg_replace( '/#\sWP\sRocket\splugin\sskip\/bypass\srule/s', "# WPRocket plugin skip/bypass rule", $root_htaccess_file_contents);
		}				
		
		file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
	
		$Root_Autolock = get_option('bulletproof_security_options_autolock');
		
		if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' || @$lock == '0404' ) {
			@chmod($rootHtaccess, 0404);
		}		
	}

	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_two = htmlspecialchars_decode( $CC_Options_root['bps_customcode_two'], ENT_QUOTES );
	$pattern = '/WP\sRocket/';
	
	if ( preg_match( $pattern, $bps_customcode_two ) ) {
		
		$bps_customcode_two_preg_replace = preg_replace('/#\sWP\sRocket\splugin\sskip\/bypass\srule/', "# WPRocket plugin skip/bypass rule", $bps_customcode_two);
	
		if ( ! is_multisite() ) {

			$Root_CC_Options = array(
			'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
			'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
			'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
			'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
			'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
			'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
			'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
			'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
			'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
			'bps_customcode_two' 				=> $bps_customcode_two_preg_replace, 
			'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], // not sure if i should attempt this one or not
			'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
			'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
			'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
			);
					
		} else {
						
			$Root_CC_Options = array(
			'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
			'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
			'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
			'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
			'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
			'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
			'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
			'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
			'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
			'bps_customcode_two' 				=> $bps_customcode_two_preg_replace, 
			'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
			'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
			'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
			'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
			'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
			);
		}
	
		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}
	}
}

// Parse phpinfo() and return the PHP memory_limit Local Value
function bpsPro_phpinfo_memory_limit_array() {
    
 	$disable_functions = explode( ',', ini_get('disable_functions') );	
   	
	if ( function_exists('phpinfo') && ! in_array( 'phpinfo', $disable_functions ) ) {
		
		ob_start();
		phpinfo();
		$info_arr = array();
		$info_lines = explode( "\n", strip_tags( ob_get_clean(), "<tr><td><h2>" ) );
		$cat = "General";
		
		foreach( $info_lines as $line ) {
	
			preg_match( "~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
			
			if ( preg_match( "~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val ) ) {
				$info_arr[$cat][$val[1]] = $val[2];
			
			} elseif ( preg_match( "~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val ) ) {
				$info_arr[$cat][$val[1]] = array( "local" => $val[2], "master" => $val[3] );
			}
		}
		
		$bps_php_memory_limit = $info_arr['Core']['memory_limit']['local'];
		
		$bps_php_memory_limit_options = array( 'bps_php_memory_limit' => $bps_php_memory_limit );
	
		foreach( $bps_php_memory_limit_options as $key => $value ) {
			update_option('bulletproof_security_options_php_memory_limit', $bps_php_memory_limit_options);
		}

		return $info_arr['Core']['memory_limit']['local'];
	
	} else {
		
		$bps_php_memory_limit = 'Unable to get the PHP memory_limit value from the server. Either the phpinfo() function does not exist or it has been disabled in your server php.ini or .user.ini file. The ini_set Options memory limit setting will be set to 256M automatically by the Setup Wizard to prevent problems or errors with the BPS Pro Setup.';
		
		$bps_php_memory_limit_options = array( 'bps_php_memory_limit' => $bps_php_memory_limit );
	
		foreach( $bps_php_memory_limit_options as $key => $value ) {
			update_option('bulletproof_security_options_php_memory_limit', $bps_php_memory_limit_options);
		}
	}
}

// BPS upgrade: adds/updates/saves any new DB options, does cleanup & everything else.
// This function is executed in this function: bpsPro_new_feature_autoupdate() which is executed ONLY during BPS upgrades.
// .53.1: This function has been completely changed: literally checks if a DB option exists and has a value using ternary operations.
// If a DB option does not exist then update it with a default value else resave existing DB option values.
// NOTE: All DB options will not be added here. Only critical new DB options. For every other situation an uninstall/reinstall & Setup Wizard rerun is required.
// Note: BPS free does not use this function: bpsPro_sync_time_gmt() to sync timestamps - no dashboard alerts for logs at this point...
function bpsPro_new_version_db_options_files_autoupdate() {
	
	if ( current_user_can('manage_options') ) {
		global $bps_version, $bps_last_version, $wp_version, $wpdb, $aitpro_bullet, $pagenow, $current_user;
	
		$user_id = $current_user->ID;
		
		// 4.8: MScan new feature Dismiss Notice - only displayed to people who have upgraded BPS. Not new installations of BPS.
		if ( ! get_option('bulletproof_security_options_new_feature') ) {
			
			$bps_mscan_rebuild = array( 'bps_mscan_rebuild' => 'upgrade' );
			
			foreach( $bps_mscan_rebuild as $key => $value ) {
				update_option('bulletproof_security_options_new_feature', $bps_mscan_rebuild);
			}
		}		

		// 4.6: New Feature: Force Strong Passwords: Pre-save option settings
		if ( ! get_option('bulletproof_security_options_fsp') ) {

			$FSP_Message = 'Password must contain 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be a minimum of 12 characters long.';
		
			$BPS_FSP_Options = array(
			'bps_fsp_on_off' 		=> 'Off', 
			'bps_fsp_char_length' 	=> '12', 
			'bps_fsp_lower_case' 	=> '1', 
			'bps_fsp_upper_case' 	=> '1', 
			'bps_fsp_number' 		=> '1', 
			'bps_fsp_special_char' 	=> '1', 
			'bps_fsp_message' 		=> $FSP_Message  
			);
			
			foreach( $BPS_FSP_Options as $key => $value ) {
				update_option('bulletproof_security_options_fsp', $BPS_FSP_Options);
			}
		}		

		if ( is_multisite() ) {
			require_once ( WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/wizard.php' );
			echo '<div style="display:none;">';
			bpsPro_network_subsites_fsp_settings();
			echo '</div>';
		}		

		// 4.4: Parse phpinfo() and save the PHP memory_limit Local Value to new DB option: bps_php_memory_limit
		bpsPro_phpinfo_memory_limit_array();
		
		// 4.4: BugFix: Remove the whitespace between WP and Rocket skip/bypass rule if it exists so that the HUD AutoFix function does not detect it.
		// Note: This BPS upgrade function fires right before the root htaccess file is unlocked in the root htaccess file upgrade function.
		bpsPro_wp_rocket_marker_fix();
		
		// 4.3: Pre-save all UI|UX option settings except for SLF - already done further below. 
		// Note: Avoids having to do additional Form coding work for PHP 7.4.9 Notice errors.
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

		// BPS UI|UX|AutoFix Debug
		$UIDebug_options = get_option('bulletproof_security_options_debug');
		$uidb = ! isset($UIDebug_options['bps_debug']) ? 'Off' : $UIDebug_options['bps_debug'];
		$uidb_options = array('bps_debug' => $uidb);

		foreach( $uidb_options as $key => $value ) {
			update_option('bulletproof_security_options_debug', $uidb_options);
		}

		// 3.5: Pre-save the SLF option. The default is now set to On. New option added to use to check against for BPS upgrades: bps_slf_filter_new
		$bpsPro_SLF_options = get_option('bulletproof_security_options_SLF');
		
		if ( $bpsPro_SLF_options['bps_slf_filter_new'] != '14' ) {
		
			$BPS_SLF_Options = array( 
			'bps_slf_filter' 		=> 'On', 
			'bps_slf_filter_new' 	=> '14'
			);	
	
			foreach( $BPS_SLF_Options as $key => $value ) {
				update_option('bulletproof_security_options_SLF', $BPS_SLF_Options);
			}
		}		
		
		// 4.3: Pre-save all Setup Wizard Options DB option settings. 
		// Note: Avoids having to do additional Form coding work for PHP 7.4.9 Notice errors.
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

		// 2.9: BPS plugin 30 day review/rating request Dismiss Notice
		$bps_rate_options = 'bulletproof_security_options_rate_free';
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$bps_free_rate_review = mktime(0, 0, 0, date("m")+1, date("d")+1, date("Y"));
	
		$BPS_Rate_Option = array( 'bps_free_rate_review' => $bps_free_rate_review + $gmt_offset );
	
		if ( ! get_option( $bps_rate_options ) ) {	
		
			foreach( $BPS_Rate_Option as $key => $value ) {
				update_option('bulletproof_security_options_rate_free', $BPS_Rate_Option);
			}
		}

		// 2.4: new function created to handle all BPS MU Tools must-use plugin processing
		// 4.3: Delete the old bulletproof_security_options_autoupdate option & delete the bulletproof_security_options_MU_tools_free option
		// 4.3: delete the Dismiss Notice DB option: bps_ignore_autoupdate_notice
		bpsPro_mu_tools_plugin_copy();
		delete_user_meta($user_id, 'bps_ignore_autoupdate_notice');
		// 2.4|4.8: Pre-save MScan Options
		bpsPro_presave_mscan_options();		

		// 2.4: Pre-save MScan Log option
		$bps_option_name_mscan = 'bulletproof_security_options_MScan_log';
		$bps_new_value_mscan = bpsPro_MScan_LogLastMod_wp_secs();
		$BPS_Options_mscan = array( 'bps_mscan_log_date_mod' => $bps_new_value_mscan );
	
		if ( ! get_option( $bps_option_name_mscan ) ) {	
			foreach( $BPS_Options_mscan as $key => $value ) {
				update_option('bulletproof_security_options_MScan_log', $BPS_Options_mscan);
			}
		}

		// 3.0: Added new JTC option: bps_jtc_mu_register_form. BPS free does not use this option. Saved with a blank value.
		// 2.9: Added new JTC option: bps_jtc_custom_form_error. Defaults to standard JTC CAPTCHA error message.
		// 2.5: Change default setting to Login Form CAPTCHA Off. Has New Feature Dismiss Notice.
		// 2.4: pre-save JTC-Lite db options
		$jtc_options = get_option('bulletproof_security_options_login_security_jtc');

		if ( ! isset($jtc_options['bps_jtc_custom_roles']) ) {
			$jtc_options19 = array( 'bps', '' );
		
		} else {

			foreach ( $jtc_options as $key => $value ) {
		
				if ( $key == 'bps_jtc_custom_roles' ) {
					
					if ( ! is_array($value) ) {
						$jtc_options19 = array( 'bps', '' );
					} else { 
						$jtc_options19 = $jtc_options['bps_jtc_custom_roles'];
					}
				}
			}	
		}

		$jtc1 = ! isset($jtc_options['bps_tooltip_captcha_key']) ? 'jtc' : $jtc_options['bps_tooltip_captcha_key'];
		$jtc2 = ! isset($jtc_options['bps_tooltip_captcha_hover_text']) ? 'Type/Enter:  jtc' : $jtc_options['bps_tooltip_captcha_hover_text'];
		$jtc3 = ! isset($jtc_options['bps_tooltip_captcha_title']) ? 'Hover or click the text box below' : $jtc_options['bps_tooltip_captcha_title'];
		$jtc4 = ! isset($jtc_options['bps_jtc_login_form']) ? '0' : $jtc_options['bps_jtc_login_form'];
		$jtc5 = ! isset($jtc_options['bps_jtc_custom_form_error']) ? '' : $jtc_options['bps_jtc_custom_form_error'];

		$jtc_db_options = array(
		'bps_tooltip_captcha_key' 			=> $jtc1, 
		'bps_tooltip_captcha_hover_text' 	=> $jtc2, 
		'bps_tooltip_captcha_title' 		=> $jtc3, 
		'bps_tooltip_captcha_logging' 		=> 'Off', 
		'bps_jtc_login_form' 				=> $jtc4, 
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
		'bps_jtc_comment_form_error' 		=> $jtc_options['bps_jtc_comment_form_error'], 
		'bps_jtc_comment_form_label' 		=> $jtc_options['bps_jtc_comment_form_label'], 
		'bps_jtc_comment_form_input' 		=> $jtc_options['bps_jtc_comment_form_input'], 
		'bps_jtc_custom_roles' 				=> $jtc_options19, 
		'bps_enable_jtc_woocommerce' 		=> '', 
		'bps_jtc_custom_form_error' 		=> $jtc5 
		);

		if ( ! get_option('bulletproof_security_options_login_security_jtc') ) {	
		
			foreach( $jtc_db_options as $key => $value ) {
				update_option('bulletproof_security_options_login_security_jtc', $jtc_db_options);
			}
		}

		// 2.4: Delete the Woo Dimiss Notice and Woo check option. No longer used in BPS free.
		delete_user_meta($user_id, 'bps_ignore_woocommerce_lsm_jtc_notice');
		delete_option('bulletproof_security_options_setup_wizard_woo');
		// 2.0: New SBC Dismiss Notice check created: checks for redundant Browser caching code & the BPS NOCHECK Marker in BPS Custom Code
		delete_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice');
		// 2.0: New Endurance Page Cache Dismiss Notice check created: A dismiss notice will only be displayed if: EPC is enabled and Cache level is 1,2,3,4
		// 3.2: No longer offering Setup Wizard AutoFix for the EPC plugin.
		delete_user_meta($user_id, 'bpsPro_ignore_EPC_plugin_notice');
		// 2.0: Delete these old Dismiss Notices permanently that are no longer being used
		delete_user_meta($user_id, 'bps_ignore_BLC_notice');
		delete_user_meta($user_id, 'bps_ignore_jetpack_notice');
		delete_user_meta($user_id, 'bps_ignore_woocommerce_notice');

		// 2.0|2.3 BugFix: Changed the default option setting to: Do Not Log POST Request Body Data on new BPS installations & upgrades.
		// This will only update the db options 1 time if the bps_security_log_post_none DB option does not exist.
		// .52.7: Set Security Log Limit POST Request Body Data option to checked/limited by default		
		if ( ! get_option('bulletproof_security_options_sec_log_post_limit') ) {

			$SecLog_post_limit_settings = array( 
			'bps_security_log_post_limit' 	=> '', 
			'bps_security_log_post_none' 	=> '1', 
			'bps_security_log_post_max' 	=> '' 
			);
			
			foreach( $SecLog_post_limit_settings as $key => $value ) {
				update_option('bulletproof_security_options_sec_log_post_limit', $SecLog_post_limit_settings);
			}
		}

		// 2.0|2.3: Revert: Remove R from the dumbed downed Request Methods Filtered 405 htaccess code in Root Custom Code.
		// Add additional https scheme conditions to 3 htaccess security rules and combine 2 rules into 1 rule in Root and wp-admin Custom Code.
		bpsPro_upgrade_CC_automatic_fix();	
		
		// .54.6: New Sucuri Dismiss Notice check created. Delete the user_meta for this Dismiss Notice.
		delete_user_meta($user_id, 'bps_ignore_sucuri_notice');		
		
		// .54.3: BugFix: pre-save bps_isl_custom_roles as an array
		// .54.2: Added Custom User Roles. bps_isl_uri_exclusions was not added/updated in previous BPS version.
		// .53.8: Add/Update new ISL DB options. conditional on whether someone is using ISL or not.
		$isl_options = get_option('bulletproof_security_options_idle_session');
		
		if ( $isl_options['bps_isl'] == 'On' || $isl_options['bps_isl'] == 'Off' ) {

			if ( ! isset($isl_options['bps_isl_custom_roles']) ) {
				$isl_options18 = array( 'bps', '' );
			
			} else {
	
				foreach ( $isl_options as $key => $value ) {
			
					if ( $key == 'bps_isl_custom_roles' ) {
						
						if ( ! is_array($value) ) {
							$isl_options18 = array( 'bps', '' );
						} else { 
							$isl_options18 = $isl_options['bps_isl_custom_roles'];
						}
					}
				}	
			}
	
			$isl_db_options = array(
			'bps_isl' 							=> $isl_options['bps_isl'], 
			'bps_isl_timeout' 					=> $isl_options['bps_isl_timeout'], 
			'bps_isl_logout_url' 				=> $isl_options['bps_isl_logout_url'], 
			'bps_isl_login_url' 				=> $isl_options['bps_isl_login_url'], 
			'bps_isl_custom_message' 			=> $isl_options['bps_isl_custom_message'], 
			'bps_isl_custom_css_1' 				=> $isl_options['bps_isl_custom_css_1'], 
			'bps_isl_custom_css_2' 				=> $isl_options['bps_isl_custom_css_2'], 
			'bps_isl_custom_css_3' 				=> $isl_options['bps_isl_custom_css_3'], 
			'bps_isl_custom_css_4' 				=> $isl_options['bps_isl_custom_css_4'], 
			'bps_isl_user_account_exceptions'	=> $isl_options['bps_isl_user_account_exceptions'],  
			'bps_isl_administrator' 			=> $isl_options['bps_isl_administrator'], 
			'bps_isl_editor' 					=> $isl_options['bps_isl_editor'], 
			'bps_isl_author' 					=> $isl_options['bps_isl_author'], 
			'bps_isl_contributor' 				=> $isl_options['bps_isl_contributor'], 
			'bps_isl_subscriber' 				=> $isl_options['bps_isl_subscriber'], 
			'bps_isl_tinymce' 					=> $isl_options['bps_isl_tinymce'], 
			'bps_isl_uri_exclusions' 			=> $isl_options['bps_isl_uri_exclusions'], 
			'bps_isl_custom_roles' 				=> $isl_options18 
			);
	
			foreach( $isl_db_options as $key => $value ) {
				update_option('bulletproof_security_options_idle_session', $isl_db_options);
			}
		}

		// .54.3: BugFix: pre-save ACE db options.
		$ace_options = get_option('bulletproof_security_options_auth_cookie');  
	
		if ( $ace_options['bps_ace'] == 'On' || $ace_options['bps_ace'] == 'Off' ) {
			
			if ( ! isset($ace_options['bps_ace_custom_roles']) ) {
				$ace_options11 = array( 'bps', '' );
			
			} else {
				
				foreach ( $ace_options as $key => $value ) {
			
					if ( $key == 'bps_ace_custom_roles' ) {
						
						if ( ! is_array($value) ) {
							$ace_options11 = array( 'bps', '' );
						} else { 
							$ace_options11 = $ace_options['bps_ace_custom_roles'];
						}
					}
				}	
			}
			
			$ace_db_options = array(
			'bps_ace' 							=> $ace_options['bps_ace'], 
			'bps_ace_expiration' 				=> $ace_options['bps_ace_expiration'], 
			'bps_ace_rememberme_expiration' 	=> $ace_options['bps_ace_rememberme_expiration'], 
			'bps_ace_user_account_exceptions' 	=> $ace_options['bps_ace_user_account_exceptions'], 
			'bps_ace_administrator' 			=> $ace_options['bps_ace_administrator'], 
			'bps_ace_editor' 					=> $ace_options['bps_ace_editor'], 
			'bps_ace_author' 					=> $ace_options['bps_ace_author'], 
			'bps_ace_contributor' 				=> $ace_options['bps_ace_contributor'], 
			'bps_ace_subscriber' 				=> $ace_options['bps_ace_subscriber'], 
			'bps_ace_rememberme_disable'		=> $ace_options['bps_ace_rememberme_disable'],  
			'bps_ace_custom_roles' 				=> $ace_options11 
			);
	
			foreach( $ace_db_options as $key => $value ) {
				update_option('bulletproof_security_options_auth_cookie', $ace_db_options);
			}
		}

		// 2.4: Enable Login Security for WooCommerce option is disabled by default in BPS free and cannot be enabled.
		// 2.3: BugFix: Enable Login Security for WooCommerce option being reset on upgrade. Only enable once if the option does not exist.
		// .54.3: New Enable LSM for WooCommerce option added
		// .51.8: New Login Security Attempts Remaining option added
		$lsm = get_option('bulletproof_security_options_login_security');	
	
		$lsm1 = ! isset($lsm['bps_max_logins']) ? '3' : $lsm['bps_max_logins'];
		$lsm2 = ! isset($lsm['bps_lockout_duration']) ? '15' : $lsm['bps_lockout_duration'];
		$lsm3 = ! isset($lsm['bps_manual_lockout_duration']) ? '60' : $lsm['bps_manual_lockout_duration'];
		$lsm4 = ! isset($lsm['bps_max_db_rows_display']) ? '' : $lsm['bps_max_db_rows_display'];
		$lsm5 = ! isset($lsm['bps_login_security_OnOff']) ? 'On' : $lsm['bps_login_security_OnOff'];
		$lsm6 = ! isset($lsm['bps_login_security_logging']) ? 'logLockouts' : $lsm['bps_login_security_logging'];
		$lsm7 = ! isset($lsm['bps_login_security_errors']) ? 'wpErrors' : $lsm['bps_login_security_errors'];
		$lsm8 = ! isset($lsm['bps_login_security_remaining']) ? 'On' : $lsm['bps_login_security_remaining'];
		$lsm9 = ! isset($lsm['bps_login_security_pw_reset']) ? 'enable' : $lsm['bps_login_security_pw_reset'];
		$lsm10 = ! isset($lsm['bps_login_security_sort']) ? 'ascending' : $lsm['bps_login_security_sort'];

		$lsm_options = array(
		'bps_max_logins' 				=> $lsm1, 
		'bps_lockout_duration' 			=> $lsm2, 
		'bps_manual_lockout_duration' 	=> $lsm3, 
		'bps_max_db_rows_display' 		=> $lsm4, 
		'bps_login_security_OnOff' 		=> $lsm5, 
		'bps_login_security_logging' 	=> $lsm6, 
		'bps_login_security_errors' 	=> $lsm7, 
		'bps_login_security_remaining' 	=> $lsm8, 
		'bps_login_security_pw_reset' 	=> $lsm9, 
		'bps_login_security_sort' 		=> $lsm10, 
		'bps_enable_lsm_woocommerce' 	=> '' 
		);

		foreach( $lsm_options as $key => $value ) {
			update_option('bulletproof_security_options_login_security', $lsm_options);
		}

		// .53.9: Custom default.htacces File
		// If a /bps-backup/master-backups/default.htaccess exists then copy it to /htaccess/default.htaccess
		$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';
		$DefaultHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
		if ( file_exists($custom_default_htaccess) ) {
			copy($custom_default_htaccess, $DefaultHtaccess);
		}

		// .53.8: Add/Update new Hidden|Empty Plugin Folders|Files Cron DB options.
		$hpf_cron = get_option('bulletproof_security_options_hpf_cron');

		$hpf_cron1 = ! isset($hpf_cron['bps_hidden_plugins_cron']) ? 'On' : $hpf_cron['bps_hidden_plugins_cron'];
		$hpf_cron2 = ! isset($hpf_cron['bps_hidden_plugins_cron_frequency']) ? '15' : $hpf_cron['bps_hidden_plugins_cron_frequency'];
		$hpf_cron3 = ! isset($hpf_cron['bps_hidden_plugins_cron_email']) ? '' : $hpf_cron['bps_hidden_plugins_cron_email'];
		$hpf_cron4 = ! isset($hpf_cron['bps_hidden_plugins_cron_alert']) ? '' : $hpf_cron['bps_hidden_plugins_cron_alert'];	
		
		$hpf_cron_options = array(
		'bps_hidden_plugins_cron' 			=> $hpf_cron1, 
		'bps_hidden_plugins_cron_frequency' => $hpf_cron2, 
		'bps_hidden_plugins_cron_email' 	=> $hpf_cron3, 
		'bps_hidden_plugins_cron_alert' 	=> $hpf_cron4 
		);

		foreach( $hpf_cron_options as $key => $value ) {
			update_option('bulletproof_security_options_hpf_cron', $hpf_cron_options);
		}			
		
		// .53.8: Add/Update new Hidden|Empty Plugin Folders|Files Ignore Hidden Plugin Folders & Files DB options.
		$hpf_check = get_option('bulletproof_security_options_hidden_plugins');
		$hpf_check1 = ! isset($hpf_check['bps_hidden_plugins_check']) ? '' : $hpf_check['bps_hidden_plugins_check'];
		
		$hpf_check_options = array( 'bps_hidden_plugins_check' => $hpf_check1 );

		foreach( $hpf_check_options as $key => $value ) {
			update_option('bulletproof_security_options_hidden_plugins', $hpf_check_options);
		}

		// .53.6: Wordfence WAF mess - Reset php handler dismiss notice.
		bpsPro_php_handler_dismiss_notice_reset();

		// 2.4: new email alerting options added
		// .53.5: Old obsolete function deleted and code moved here.
		// Email Alerting & Log file zip, email and deleting DB options.
		$email_log = get_option('bulletproof_security_options_email');
		$admin_email = get_option('admin_email');

		$email_log1 = ! isset($email_log['bps_send_email_to']) ? $admin_email : $email_log['bps_send_email_to'];
		$email_log2 = ! isset($email_log['bps_send_email_from']) ? $admin_email : $email_log['bps_send_email_from'];
		$email_log3 = ! isset($email_log['bps_send_email_cc']) ? '' : $email_log['bps_send_email_cc'];
		$email_log4 = ! isset($email_log['bps_send_email_bcc']) ? '' : $email_log['bps_send_email_bcc'];
		$email_log5 = ! isset($email_log['bps_login_security_email']) ? 'lockoutOnly' : $email_log['bps_login_security_email'];
		$email_log6 = ! isset($email_log['bps_security_log_size']) ? '500KB' : $email_log['bps_security_log_size'];
		$email_log7 = ! isset($email_log['bps_security_log_emailL']) ? 'email' : $email_log['bps_security_log_emailL'];
		$email_log8 = ! isset($email_log['bps_dbb_log_email']) ? 'email' : $email_log['bps_dbb_log_email'];
		$email_log9 = ! isset($email_log['bps_dbb_log_size']) ? '500KB' : $email_log['bps_dbb_log_size'];
		$email_log10 = ! isset($email_log['bps_mscan_log_size']) ? '500KB' : $email_log['bps_mscan_log_size'];	
		$email_log11 = ! isset($email_log['bps_mscan_log_email']) ? 'email' : $email_log['bps_mscan_log_email'];	

		$email_log_options = array(
		'bps_send_email_to' 			=> $email_log1, 
		'bps_send_email_from' 			=> $email_log2, 
		'bps_send_email_cc' 			=> $email_log3, 
		'bps_send_email_bcc' 			=> $email_log4, 
		'bps_login_security_email' 		=> $email_log5, 
		'bps_security_log_size' 		=> $email_log6, 
		'bps_security_log_emailL' 		=> $email_log7, 
		'bps_dbb_log_email' 			=> $email_log8, 
		'bps_dbb_log_size' 				=> $email_log9, 
		'bps_mscan_log_size' 			=> $email_log10, 
		'bps_mscan_log_email' 			=> $email_log11 		
		);

		foreach( $email_log_options as $key => $value ) {
			update_option('bulletproof_security_options_email', $email_log_options);
		}

		// .53: Condition added to allow commenting out wp-admin URI whitelist rule
		// .52.9: POST Request Attack Protection code correction|addition
		bpsPro_post_request_protection_check();

		// BPS .52.6: Pre-save UI Theme Skin with Blue Theme if DB option does not exist
		bpsPro_presave_ui_theme_skin_options();

		// .52.3: If Custom Code db options do not exist yet, create blank values
		$ccr = get_option('bulletproof_security_options_customcode');
	
		$ccr1 = ! isset($ccr['bps_customcode_one']) ? '' : $ccr['bps_customcode_one'];
		$ccr2 = ! isset($ccr['bps_customcode_server_signature']) ? '' : $ccr['bps_customcode_server_signature'];
		$ccr3 = ! isset($ccr['bps_customcode_directory_index']) ? '' : $ccr['bps_customcode_directory_index'];
		$ccr4 = ! isset($ccr['bps_customcode_server_protocol']) ? '' : $ccr['bps_customcode_server_protocol'];
		$ccr5 = ! isset($ccr['bps_customcode_error_logging']) ? '' : $ccr['bps_customcode_error_logging'];
		$ccr6 = ! isset($ccr['bps_customcode_deny_dot_folders']) ? '' : $ccr['bps_customcode_deny_dot_folders'];
		$ccr7 = ! isset($ccr['bps_customcode_admin_includes']) ? '' : $ccr['bps_customcode_admin_includes'];
		$ccr8 = ! isset($ccr['bps_customcode_wp_rewrite_start']) ? '' : $ccr['bps_customcode_wp_rewrite_start'];
		$ccr9 = ! isset($ccr['bps_customcode_request_methods']) ? '' : $ccr['bps_customcode_request_methods'];
		$ccr10 = ! isset($ccr['bps_customcode_two']) ? '' : $ccr['bps_customcode_two'];
		$ccr11 = ! isset($ccr['bps_customcode_timthumb_misc']) ? '' : $ccr['bps_customcode_timthumb_misc'];
		$ccr12 = ! isset($ccr['bps_customcode_bpsqse']) ? '' : $ccr['bps_customcode_bpsqse'];
		$ccr12m = @! isset($ccr['bps_customcode_wp_rewrite_end']) ? '' : $ccr['bps_customcode_wp_rewrite_end'];
		$ccr13 = ! isset($ccr['bps_customcode_deny_files']) ? '' : $ccr['bps_customcode_deny_files'];
		$ccr14 = ! isset($ccr['bps_customcode_three']) ? '' : $ccr['bps_customcode_three'];

		if ( ! is_multisite() ) {

			$ccr_options = array(
			'bps_customcode_one' 				=> $ccr1, 
			'bps_customcode_server_signature' 	=> $ccr2, 
			'bps_customcode_directory_index' 	=> $ccr3, 
			'bps_customcode_server_protocol' 	=> $ccr4, 
			'bps_customcode_error_logging' 		=> $ccr5, 
			'bps_customcode_deny_dot_folders' 	=> $ccr6, 
			'bps_customcode_admin_includes' 	=> $ccr7, 
			'bps_customcode_wp_rewrite_start' 	=> $ccr8, 
			'bps_customcode_request_methods' 	=> $ccr9, 
			'bps_customcode_two' 				=> $ccr10, 
			'bps_customcode_timthumb_misc' 		=> $ccr11, 
			'bps_customcode_bpsqse' 			=> $ccr12, 
			'bps_customcode_deny_files' 		=> $ccr13, 
			'bps_customcode_three' 				=> $ccr14
			);
				
		} else {
					
			$ccr_options = array(
			'bps_customcode_one' 				=> $ccr1, 
			'bps_customcode_server_signature' 	=> $ccr2, 
			'bps_customcode_directory_index' 	=> $ccr3, 
			'bps_customcode_server_protocol' 	=> $ccr4, 
			'bps_customcode_error_logging' 		=> $ccr5, 
			'bps_customcode_deny_dot_folders' 	=> $ccr6, 
			'bps_customcode_admin_includes' 	=> $ccr7, 
			'bps_customcode_wp_rewrite_start' 	=> $ccr8, 
			'bps_customcode_request_methods' 	=> $ccr9, 
			'bps_customcode_two' 				=> $ccr10, 
			'bps_customcode_timthumb_misc' 		=> $ccr11, 
			'bps_customcode_bpsqse' 			=> $ccr12, 
			'bps_customcode_wp_rewrite_end' 	=> $ccr12m, 
			'bps_customcode_deny_files' 		=> $ccr13, 
			'bps_customcode_three' 				=> $ccr14
			);					
		}

		foreach( $ccr_options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $ccr_options);
		}

		$ccw = get_option('bulletproof_security_options_customcode_WPA');
	
		$ccw1 = ! isset($ccw['bps_customcode_deny_files_wpa']) ? '' : $ccw['bps_customcode_deny_files_wpa'];
		$ccw2 = ! isset($ccw['bps_customcode_one_wpa']) ? '' : $ccw['bps_customcode_one_wpa'];
		$ccw3 = ! isset($ccw['bps_customcode_two_wpa']) ? '' : $ccw['bps_customcode_two_wpa'];
		$ccw4 = ! isset($ccw['bps_customcode_bpsqse_wpa']) ? '' : $ccw['bps_customcode_bpsqse_wpa'];
	
		$ccw_options = array(
		'bps_customcode_deny_files_wpa' => $ccw1, 
		'bps_customcode_one_wpa' 		=> $ccw2, 
		'bps_customcode_two_wpa' 		=> $ccw3, 
		'bps_customcode_bpsqse_wpa' 	=> $ccw4
		);
			
		foreach( $ccw_options as $key => $value ) {
			update_option('bulletproof_security_options_customcode_WPA', $ccw_options);
		}

		$bps_option_name_dbb = 'bulletproof_security_options_DBB_log';
		$bps_new_value_dbb = bpsPro_DBB_LogLastMod_wp_secs();
		$BPS_Options_dbb = array( 'bps_dbb_log_date_mod' => $bps_new_value_dbb );

		if ( ! get_option( $bps_option_name_dbb ) ) {	
		
			foreach( $BPS_Options_dbb as $key => $value ) {
				update_option('bulletproof_security_options_DBB_log', $BPS_Options_dbb);
			}
		}

		// Save the Setup Wizard DB option only if it does not already exist
		$bps_setup_wizard = 'bulletproof_security_options_wizard_free';
		$BPS_Wizard = array( 'bps_wizard_free' => 'upgrade' );	
	
		if ( ! get_option( $bps_setup_wizard ) ) {	
		
			foreach( $BPS_Wizard as $key => $value ) {
				update_option('bulletproof_security_options_wizard_free', $BPS_Wizard);
			}
		}

		// Misc cleanup, etc.
		// delete the old Maintenance Mode DB option - added in BPS .49.9
		if ( get_option('bulletproof_security_options_maint') ) {	
			delete_option('bulletproof_security_options_maint');
		}
		// Delete all the old plugin api junk content in this transient
		delete_transient( 'bulletproof-security_info' );
	}
}

?>