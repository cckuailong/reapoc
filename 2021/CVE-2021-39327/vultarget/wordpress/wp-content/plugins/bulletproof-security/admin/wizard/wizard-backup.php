<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_wizard() {
	
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

// Create a new Deny All .htaccess file ONLY on page load with users current IP address to allow the root-htaccess-file.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
// .53.6: This function is now executed after the bpsPro_pre_installation_prep() function in the wizard.php file.
function bpsPro_Wizard_deny_all() {

	if ( isset( $_POST['Submit-Setup-Wizard'] ) || isset( $_POST['Submit-Net-LSM'] ) || isset( $_POST['Submit-Wizard-GDMW'] ) || isset( $_POST['Submit-Wizard-HFiles'] ) ) {
		return;
	}

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
	$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
	if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset($Zip_download_Options['bps_zip_download_fix']) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
		return;
	}

	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
		
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		
		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_wizard()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_wizard()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_wizard()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/.htaccess';
		
		$check_string = '';
		
		if ( file_exists($create_denyall_htaccess_file) ) {
			$check_string = @file_get_contents($create_denyall_htaccess_file);
		}
		
		if ( ! file_exists($create_denyall_htaccess_file) ) { 

			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) && ! strpos( $check_string, bpsPro_get_real_ip_address_wizard() ) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}
	}
}

// Zip Root htaccess file: If ZipArchive Class is not available use PclZip
function bps_zip_root_htaccess_file() {
	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

		$zip = new ZipArchive();
		$filename = WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/htaccess-files.zip';
	
		if ( $zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE ) {
    		exit("Error: Cannot Open $filename\n");
		}

		$zip->addFile( ABSPATH . '.htaccess', "root.htaccess" );
		
		if ( file_exists( ABSPATH . '/wp-admin/.htaccess' ) ) {
			$zip->addFile( ABSPATH . '/wp-admin/.htaccess', "wp-admin.htaccess" );
		}
		
		$zip->close();

	return true;

	} else {

		// Use PclZip
		define( 'PCLZIP_TEMPORARY_DIR', WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/' );
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
		if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
			$previous_encoding = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}
  		
		$root_htaccess = ABSPATH . '.htaccess';
		$wp_admin_htaccess = ABSPATH . '/wp-admin/.htaccess';
		$root_htaccess_dest = WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/root.htaccess';
		$wp_admin_htaccess_dest = WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/wp-admin.htaccess';
		$blank_dummy_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/blank.txt';;		
		
		if ( file_exists($root_htaccess) ) {
			copy($root_htaccess, $root_htaccess_dest);
		}
		if ( file_exists($wp_admin_htaccess) ) {
			copy($wp_admin_htaccess, $wp_admin_htaccess_dest);
		}
		// PclZip will lose its mind if the wp-admin htaccess file does not exist. So create a dummy file.
		if ( ! file_exists($wp_admin_htaccess) ) {
			copy($blank_dummy_file, $wp_admin_htaccess_dest);
		}				

		$archive = new PclZip(WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/htaccess-files.zip');
  	
		$v_list = $archive->create(array(
			array( PCLZIP_ATT_FILE_NAME => WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/root.htaccess', 
				PCLZIP_ATT_FILE_NEW_SHORT_NAME => 'root.htaccess'
				),
			array( PCLZIP_ATT_FILE_NAME => WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/wp-admin.htaccess', 
				PCLZIP_ATT_FILE_NEW_SHORT_NAME => 'wp-admin.htaccess' 
				),
			),
			PCLZIP_OPT_REMOVE_PATH, WP_PLUGIN_DIR . '/bulletproof-security/admin/wizard/');

		if ( $v_list >= 1 ) {
			unlink($root_htaccess_dest);
			unlink($wp_admin_htaccess_dest);
	
			return true;
		}

		if ( $v_list == 0 ) {
			die("Error : ".$archive->errorInfo(true) );
			return false;	
		}
	}
}

// If there is additional code in the root htaccess file besides just the standard WP Rewrite code then display message, forum link and download button.
// return if the BULLETPROOF string is found in the root htaccess file.
// Note: Using (\w|\d|\W|\D){1,} causes XAMPP to crash
function bpsPro_root_precheck_download() {

	if ( isset( $_POST['Submit-Setup-Wizard'] ) ) {
		return;
	}

	$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
	$bps_bottomDiv = '</p></div>';

	$root_htaccess_file = ABSPATH . '.htaccess';

	if ( file_exists($root_htaccess_file) ) {

		$get_root_contents = @file_get_contents($root_htaccess_file);

		if ( strpos( $get_root_contents, "BULLETPROOF" ) ) {
			return;
		}

		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$rootHtaccess = ABSPATH . '.htaccess';
		$rootHtaccessBackupTime = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess-'.date( 'Y-m-d-g-i-s-a', time() + $gmt_offset );	
		$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
		
		if ( file_exists($wpadminHtaccess) ) {
			$wpadminHtaccessBackupTime = WP_CONTENT_DIR . '/bps-backup/master-backups/wpadmin.htaccess-'.date( 'Y-m-d-g-i-s-a', time() + $gmt_offset );
		} else {
			$wpadminHtaccessBackupTime = 'NA';		
		}

		if ( ! is_multisite() ) {
	
			$wp_single_default = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';
		
			$wp_single_default_no_code_top = '/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';
		
			$wp_single_default_no_code_bottom = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress(\s*|){1,}/';
	
			if ( preg_match( $wp_single_default, $get_root_contents, $matches ) || preg_match( $wp_single_default_no_code_top, $get_root_contents, $matches ) || preg_match( $wp_single_default_no_code_bottom, $get_root_contents, $matches ) ) {
	
				// zip root htaccess file, display message with forum link and download button.
				bps_zip_root_htaccess_file();
				// root + wp-admin htaccess file backups with timestamp: root.htaccess-2017-11-02-3-00-00
				if ( file_exists($rootHtaccess) ) {
					copy($rootHtaccess, $rootHtaccessBackupTime);
				}
				if ( file_exists($wpadminHtaccess) ) {
					copy($wpadminHtaccess, $wpadminHtaccessBackupTime);
				}			
				
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Custom additional htaccess code was found in your current root htaccess file. Your root and wp-admin htaccess files have been backed up and zipped in this zip file: /bulletproof-security/admin/wizard/htaccess-files.zip. Click the Download Root htaccess File button below to download your htaccess-files.zip file to your computer.', 'bulletproof-security').'<br>'.__('Click this forum link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/setup-wizard-root-htaccess-file-backup/" target="_blank" style="text-decoration:underline;">'.__('Setup Wizard Root and wp-admin htaccess File Backup', 'bulletproof-security').'</a>'.__(' for help information about what this means and what to do.', 'bulletproof-security').'<br>'.__('If you see a 403 error and/or are unable to download the zip file then click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'" target="_blank">'.__('Setup Wizard Options', 'bulletproof-security').'</a>'.__(' and select the Zip File Download Fix On setting for the Zile File Download Fix option. You should now be able to download the htaccess-files.zip file. If you are still unable to download the zip file then click the forum link above for what to do next.', 'bulletproof-security').'</strong></font><br><div style="width:200px;font-size:1em;text-align:center;margin:10px 0px 5px 0px;padding:4px 6px 4px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.plugins_url( '/bulletproof-security/admin/wizard/htaccess-files.zip' ).'" style="font-size:1em;font-weight:bold;text-decoration:none;">'.__('Download htaccess-files.zip File', 'bulletproof-security').'</a></div><font color="blue"><strong>'.__('Additional Plain Text htaccess file backups: ', 'bulletproof-security').'</strong></font><br><strong>'.__('Root htaccess File: ', 'bulletproof-security').'</strong>'.$rootHtaccessBackupTime.'<br><strong>'.__('wp-admin htaccess File: ', 'bulletproof-security').'</strong>'.$wpadminHtaccessBackupTime;
				echo $text;
				echo $bps_bottomDiv;			
			}
	
		} else {
			
			// WP 3.5+ Subfolder & Subdomain Sites
			$subfolder_subdomain35 = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\sadd(.*)wp-admin\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){3}RewriteRule\s\.\s(.*)index\.php\s\[L\](\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';		
	
			$subfolder_subdomain35_no_code_top = '/RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\sadd(.*)wp-admin\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){3}RewriteRule\s\.\s(.*)index\.php\s\[L\](\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';				
	
			$subfolder_subdomain35_no_code_bottom = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\sadd(.*)wp-admin\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){3}RewriteRule\s\.\s(.*)index\.php\s\[L\](\s*|){1,}/';
	
			// WP 3.4 or older Subfolder
			$subfolder34 = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*#\sadd(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';
		
			$subfolder34_no_code_top = '/#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*#\sadd(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';
	
			$subfolder34_no_code_bottom = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*#\sadd(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}/';
	
			// WP 3.4 or older Subdomain
			$subdomain34 = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';
	
			$subdomain34_no_code_top = '/#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}/';	
		
			$subdomain34_no_code_bottom = '/[a-zA-Z0-9\#\^\/\$\:\.\[\]\<\>\*\=\%\{\}_\-\(\)\,\;@\\\\|\?\'\"\&\+\!]{1,}(\s*|){1,}#\sBEGIN\sWordPress\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*#\suploaded(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule((.*)\s*){4}#\sEND\sWordPress(\s*|){1,}/';	
		
			if ( preg_match( $subfolder_subdomain35, $get_root_contents, $matches ) || preg_match( $subfolder_subdomain35_no_code_top, $get_root_contents, $matches ) || preg_match( $subfolder_subdomain35_no_code_bottom, $get_root_contents, $matches ) || preg_match( $subfolder34, $get_root_contents, $matches ) || preg_match( $subfolder34_no_code_top, $get_root_contents, $matches ) || preg_match( $subfolder34_no_code_bottom, $get_root_contents, $matches ) || preg_match( $subdomain34, $get_root_contents, $matches ) || preg_match( $subdomain34_no_code_top, $get_root_contents, $matches ) || preg_match( $subdomain34_no_code_bottom, $get_root_contents, $matches ) ) {
	
				// zip root htaccess file, display message with forum link and download button.
				bps_zip_root_htaccess_file();
				// root + wp-admin htaccess file backups with timestamp: root.htaccess-2017-11-02-3-00-00
				if ( file_exists($rootHtaccess) ) {
					copy($rootHtaccess, $rootHtaccessBackupTime);
				}
				if ( file_exists($wpadminHtaccess) ) {
					copy($wpadminHtaccess, $wpadminHtaccessBackupTime);
				}				

				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Custom additional htaccess code was found in your current root htaccess file. Your root and wp-admin htaccess files have been backed up and zipped in this zip file: /bulletproof-security/admin/wizard/htaccess-files.zip. Click the Download Root htaccess File button below to download your htaccess-files.zip file to your computer.', 'bulletproof-security').'<br>'.__('Click this forum link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/setup-wizard-root-htaccess-file-backup/" target="_blank" style="text-decoration:underline;">'.__('Setup Wizard Root and wp-admin htaccess File Backup', 'bulletproof-security').'</a>'.__(' for help information about what this means and what to do.', 'bulletproof-security').'<br>'.__('If you see a 403 error and/or are unable to download the zip file then click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'" target="_blank">'.__('Setup Wizard Options', 'bulletproof-security').'</a>'.__(' and select the Zip File Download Fix On setting for the Zile File Download Fix option. You should now be able to download the htaccess-files.zip file. If you are still unable to download the zip file then click the forum link above for what to do next.', 'bulletproof-security').'</strong></font><br><div style="width:200px;font-size:1em;text-align:center;margin:10px 0px 5px 0px;padding:4px 6px 4px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.plugins_url( '/bulletproof-security/admin/wizard/htaccess-files.zip' ).'" style="font-size:1em;font-weight:bold;text-decoration:none;">'.__('Download htaccess-files.zip File', 'bulletproof-security').'</a></div><font color="blue"><strong>'.__('Additional Plain Text htaccess file backups: ', 'bulletproof-security').'</strong></font><br><strong>'.__('Root htaccess File: ', 'bulletproof-security').'</strong>'.$rootHtaccessBackupTime.'<br><strong>'.__('wp-admin htaccess File: ', 'bulletproof-security').'</strong>'.$wpadminHtaccessBackupTime;
				echo $text;
				echo $bps_bottomDiv;			
			}
		}
	}
}

?>