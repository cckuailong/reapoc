<?php
/*
Plugin Name: BPS MU Tools
Description: To turn On any of these WordPress Automatic Update options/filters click the links. When any of these WordPress Automatic Update options/filters are turned On that means that particular WP Automatic Update option/filter is enabled and the link will be displayed in green font. When any of these WordPress Automatic Update options/filters are turned Off that means that particular WP Automatic Update option/filter is not in use. It does not mean that particular WP Automatic Update filter is disabling or turning Off a particular WP Automatic Update. For additional help info about each of these WordPress Automatic Update options/filters click the "WordPress Automatic Update Help Forum Topic" link below. &bull; Disable all Updates: On = All WordPress Automatic Updates: Core, Plugins, Themes and Translations will be disabled. &bull; Disable all Core Updates: On = All WordPress Core Automatic Updates: Development, Minor and Major versions are disabled. &bull; Enable all Core Updates: On = All WordPress Core Automatic Updates: Development, Minor and Major versions are enabled. &bull; Enable Development Updates: On = WordPress Core Automatic Updates are enabled for Development WP versions. &bull; Enable Minor Updates: On = WordPress Core Automatic Updates are enabled for Minor WP versions. &bull; Enable Major Updates: On = WordPress Core Automatic Updates are enabled for Major WP versions.
Version: 6.0
Author: AITpro
Author URI: https://forum.ait-pro.com/forums/forum/bulletproof-security-free/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

## IMPORTANT!!!!: error_reporting(0); cannot be used generally in this file or all PHP Error Logging will be broken, but can be used safely within certain conditions.
## Important Note: If you would like to add additional customizations to this file it is recommended that you make a copy of this file after you make any customizations. 
## Most likely additional things will be added/created in this BPS Pro must-use file at a later time. 
## If you customize this BPS file then you will lose your customizations if/when this file is updated in the future.
## The MU Tools auto-update function: bpsPro_mu_tools_plugin_copy() is located in general-functions.php at code line: 816
## 1.0: Added Toggle Action Links and DB options so that BPS MU Tools can be enabled or disabled.
## 2.0: Added CSRF Nonce verification to Toggle GET links.
## 2.7: BugFix for SSL sites nonce verification failing.
## 3.2: Disabling all functions except for the BPS Plugin automatic update function.
## 4.2: Added WP Automatic Update options/filters. Removed the MU Tools Enable|Disable BPS Plugin AutoUpdates & Enable|Disable BPS Folder|Deactivation Checks code.

## Uncommenting these filters below and commenting out this BPS filter: add_filter( 'auto_update_plugin', 'bpsPro_autoupdate_bps_plugin', 10, 2 );
## will allow ALL plugin and theme automatic updates on your website. At a later time|version this BPS MU plugin file will include options to enable|disable these things.
/** 
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
**/ 

function bpsPro_autoupdate_bps_plugin( $update, $item ) {
    
	$MUTools_Options = get_option('bulletproof_security_options_MU_tools_free');
	
	if ( @$MUTools_Options['bps_mu_tools_enable_disable_autoupdate'] == 'disable' ) {
		return;
	}

	// Array of plugin slugs to AutoUpdate
    $plugins = array ( 
		'bulletproof-security',
    );
    
	// AutoUpdate plugins in the $plugins array
	if ( in_array( $item->slug, $plugins ) ) {
        return true;
    } else {
		// For any/all other plugins that are not in the $plugins array, return the WP $update API response
		return $update; 
    }
}

//add_filter( 'auto_update_plugin', 'bpsPro_autoupdate_bps_plugin', 10, 2 );

// Check if the /bulletproof-security/ plugin folder has been renamed or deleted.
// Writes a log entry and sends an email alert once every 5 minutes.
function bpsPro_plugin_folder_check() {
	
	$MUTools_Options = get_option('bulletproof_security_options_MU_tools_free');
	
	if ( @$MUTools_Options['bps_mu_tools_enable_disable_deactivation'] == 'disable' ) {
		return;
	}

	if ( time() > $MUTools_Options['bps_mu_tools_timestamp'] ) {

		if ( ! is_dir( WP_PLUGIN_DIR . '/bulletproof-security' ) ) {
		
			global $blog_id;

			if ( is_multisite() && $blog_id != 1 ) {
				return;
			} else {			
			
				$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
				$timeNow = time();
				$gmt_offset = get_option( 'gmt_offset' ) * 3600;
				$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
		
				$log_contents = "\r\n" . '[BPS Plugin Folder Renamed or Deleted: ' . $timestamp . ']' . "\r\n" . 'This Security Log entry is created when the /bulletproof-security/ plugin folder is renamed or deleted. An email alert is also sent to you when the /bulletproof-security/ plugin folder is renamed or deleted.'."\r\n";

				if ( is_writable( $bpsProLog ) ) {

				if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
        			exit;
    			}

    			if ( fwrite( $handle, $log_contents) === FALSE ) {
       				exit;
    			}

    			fclose($handle);
				}	
	
				$EmailOptions = get_option('bulletproof_security_options_email');
				$bps_email_to = $EmailOptions['bps_send_email_to'];
				$bps_email_from = $EmailOptions['bps_send_email_from'];
				$bps_email_cc = $EmailOptions['bps_send_email_cc'];
				$bps_email_bcc = $EmailOptions['bps_send_email_bcc'];
				$justUrl = get_site_url();
				$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	
				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				$headers .= "From: " . $bps_email_from . "\r\n";
				$headers .= "Cc: " . $bps_email_cc . "\r\n";
				$headers .= "Bcc: " . $bps_email_bcc . "\r\n";		
		
				$subject = " BPS Alert: The /bulletproof-security/ plugin folder has been renamed or deleted - $timestamp ";
				$message = '<p>When the /bulletproof-security/ plugin folder is renamed or deleted this email alert will be sent to you every 5 minutes. To stop these email alerts from being sent, go to the WordPress Plugins page, click the Must-Use link, click the BPS MU Tools Disable BPS Folder|Deactivation Checks link.</p>';	
				$message .= '<p>Website: '.$justUrl.'</p>'; 
		
				mail( $bps_email_to, $subject, $message, $headers );

				$MUTools_Option_settings = array( 
				'bps_mu_tools_timestamp' 					=> time() + 300,
				'bps_mu_tools_enable_disable_autoupdate' 	=> $MUTools_Options['bps_mu_tools_enable_disable_autoupdate'], 
				'bps_mu_tools_enable_disable_deactivation' 	=> $MUTools_Options['bps_mu_tools_enable_disable_deactivation'] 
				);	

				foreach ( $MUTools_Option_settings as $key => $value ) {
					update_option('bulletproof_security_options_MU_tools_free', $MUTools_Option_settings);
				}		
			}
		}
	}
}

//bpsPro_plugin_folder_check();

// Check if the BPS plugin has been deactivated.
// Writes a log entry and sends an email alert once every 5 minutes. 
function bpsPro_plugin_deactivation_check() {
	
	$MUTools_Options = get_option('bulletproof_security_options_MU_tools_free');
	
	if ( @$MUTools_Options['bps_mu_tools_enable_disable_deactivation'] == 'disable' ) {
		return;
	}
	
	global $blog_id;

	if ( is_multisite() && $blog_id != 1 ) {
		return;
	}

	if ( time() > $MUTools_Options['bps_mu_tools_timestamp'] ) {

		// The require_once for plugin.php will cause a php warning error: headers already being sent.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		$bps_plugin = 'bulletproof-security/bulletproof-security.php';
		@$bps_plugin_active = in_array( $bps_plugin, apply_filters('active_plugins', get_option('active_plugins') ) );

		if ( $bps_plugin_active != 1 && ! is_plugin_active_for_network( $bps_plugin ) ) { 

			$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
			$timeNow = time();
			$gmt_offset = get_option( 'gmt_offset' ) * 3600;
			$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
		
			$log_contents = "\r\n" . '[BPS Plugin Deactivated: ' . $timestamp . ']' . "\r\n" . 'This Security Log entry is created when the BPS plugin is deactivated. An email alert is also sent to you when the BPS plugin is deactivated.'."\r\n";

			if ( is_writable( $bpsProLog ) ) {

			if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
        		exit;
    		}

    		if ( fwrite( $handle, $log_contents) === FALSE ) {
       			exit;
    		}

    		fclose($handle);
			}	
	
			$EmailOptions = get_option('bulletproof_security_options_email');
			$bps_email_to = $EmailOptions['bps_send_email_to'];
			$bps_email_from = $EmailOptions['bps_send_email_from'];
			$bps_email_cc = $EmailOptions['bps_send_email_cc'];
			$bps_email_bcc = $EmailOptions['bps_send_email_bcc'];
			$justUrl = get_site_url();
			$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			$headers .= "From: " . $bps_email_from . "\r\n";
			$headers .= "Cc: " . $bps_email_cc . "\r\n";
			$headers .= "Bcc: " . $bps_email_bcc . "\r\n";		
		
			$subject = " BPS Pro Alert: The BPS plugin has been deactivated - $timestamp ";
			$message = '<p>The BPS plugin has been deactivated on website: '.$justUrl.'. To stop these email alerts from being sent while BPS is deactivated, go to the WordPress Plugins page, click the Must-Use link, click the BPS MU Tools Disable BPS Folder|Deactivation Checks link. If you just upgraded BPS you can ignore this email alert.</p>';	
			$message .= '<p>Note: If you are troubleshooting the BPS plugin then click this BPS Troubleshooting link: https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting. The BPS plugin has built-in troubleshooting capability and should not be deactivated for troubleshooting. Deactivating BPS removes the built-in troubleshooting tools/capabilities. You can turn all BPS security features On or Off for troubleshooting to isolate exactly which BPS security feature is causing an issue/problem or to confirm or eliminate BPS as the cause of an issue/problem.</p>';
		
			mail( $bps_email_to, $subject, $message, $headers );

			$MUTools_Option_settings = array( 
			'bps_mu_tools_timestamp' 					=> time() + 300,
			'bps_mu_tools_enable_disable_autoupdate' 	=> $MUTools_Options['bps_mu_tools_enable_disable_autoupdate'], 
			'bps_mu_tools_enable_disable_deactivation' 	=> $MUTools_Options['bps_mu_tools_enable_disable_deactivation'] 
			);	

			foreach ( $MUTools_Option_settings as $key => $value ) {
				update_option('bulletproof_security_options_MU_tools_free', $MUTools_Option_settings);
			}
		}
	}
}

//bpsPro_plugin_deactivation_check();

// Note: you cannot use current_user_can('manage_options') in a must-use plugin.
function bpsPro_toggle_links() {
	
	if ( is_admin() && preg_match( '/\/wp-admin\/plugins\.php/', esc_html($_SERVER['REQUEST_URI']) ) || is_network_admin() && preg_match( '/\/wp-admin\/network\/plugins\.php/', esc_html($_SERVER['REQUEST_URI']) ) ) {
		
		if ( isset( $_GET['bps_toggle_automatic_updater_disabled'] ) || isset( $_GET['bps_toggle_auto_update_core_updates_disabled'] ) || isset( $_GET['bps_toggle_auto_update_core'] ) || isset( $_GET['bps_toggle_allow_dev_auto_core_updates'] ) || isset( $_GET['bps_toggle_allow_minor_auto_core_updates'] ) ||isset( $_GET['bps_toggle_allow_major_auto_core_updates'] ) ) {

			if ( ! function_exists( 'wp_verify_nonce' ) ) {
				require_once( ABSPATH . '/wp-includes/pluggable.php' );
			}

			if ( ! defined( 'COOKIEHASH' ) ) {
        		$siteurl = get_site_option( 'siteurl' );
        		
				if ( $siteurl )
            		define( 'COOKIEHASH', md5( $siteurl ) );
        		else
            		define( 'COOKIEHASH', '' );
    		}

			if ( ! defined('AUTH_COOKIE') )
        		define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);
			
			if ( ! defined('SECURE_AUTH_COOKIE') )
				define('SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH);

    		if ( ! defined('LOGGED_IN_COOKIE') )
				define('LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH);			

			if ( empty( $_REQUEST['_wpnonce'] ) ) {
				$nonce = '';
			} else {
				$nonce = $_REQUEST['_wpnonce'];
			}
		
			if ( ! wp_verify_nonce( $nonce, 'bps-anti-csrf' ) ) {
				die( 'CSRF Error: Invalid Nonce used in BPS MU Tools must-use plugin GET Request' );
			
			} else {		
		
				$wp_auto_update_options = get_option('bulletproof_security_options_mu_wp_autoupdate');

				if ( ! isset( $_GET['bps_toggle_automatic_updater_disabled'] ) ) {
					$bps_toggle_automatic_updater_disabled = $wp_auto_update_options['bps_automatic_updater_disabled'];
				} elseif ( 'enable' == $_GET['bps_toggle_automatic_updater_disabled'] ) {
					$bps_toggle_automatic_updater_disabled = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_automatic_updater_disabled'] ) {
					$bps_toggle_automatic_updater_disabled = 'disabled';
				}

				if ( ! isset( $_GET['bps_toggle_auto_update_core_updates_disabled'] ) ) {
					$bps_toggle_auto_update_core_updates_disabled = $wp_auto_update_options['bps_auto_update_core_updates_disabled'];
				} elseif ( 'enable' == $_GET['bps_toggle_auto_update_core_updates_disabled'] ) {
					$bps_toggle_auto_update_core_updates_disabled = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_auto_update_core_updates_disabled'] ) {
					$bps_toggle_auto_update_core_updates_disabled = 'disabled';
				}

				if ( ! isset( $_GET['bps_toggle_auto_update_core'] ) ) {
					$bps_toggle_auto_update_core = $wp_auto_update_options['bps_auto_update_core'];
				} elseif ( 'enable' == $_GET['bps_toggle_auto_update_core'] ) {
					$bps_toggle_auto_update_core = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_auto_update_core'] ) {
					$bps_toggle_auto_update_core = 'disabled';
				}

				if ( ! isset( $_GET['bps_toggle_allow_dev_auto_core_updates'] ) ) {
					$bps_toggle_allow_dev_auto_core_updates = $wp_auto_update_options['bps_allow_dev_auto_core_updates'];
				} elseif ( 'enable' == $_GET['bps_toggle_allow_dev_auto_core_updates'] ) {
					$bps_toggle_allow_dev_auto_core_updates = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_allow_dev_auto_core_updates'] ) {
					$bps_toggle_allow_dev_auto_core_updates = 'disabled';
				}

				if ( ! isset( $_GET['bps_toggle_allow_minor_auto_core_updates'] ) ) {
					$bps_toggle_allow_minor_auto_core_updates = $wp_auto_update_options['bps_allow_minor_auto_core_updates'];
				} elseif ( 'enable' == $_GET['bps_toggle_allow_minor_auto_core_updates'] ) {
					$bps_toggle_allow_minor_auto_core_updates = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_allow_minor_auto_core_updates'] ) {
					$bps_toggle_allow_minor_auto_core_updates = 'disabled';
				}

				if ( ! isset( $_GET['bps_toggle_allow_major_auto_core_updates'] ) ) {
					$bps_toggle_allow_major_auto_core_updates = $wp_auto_update_options['bps_allow_major_auto_core_updates'];
				} elseif ( 'enable' == $_GET['bps_toggle_allow_major_auto_core_updates'] ) {
					$bps_toggle_allow_major_auto_core_updates = 'enabled';
				} elseif ( 'disable' == $_GET['bps_toggle_allow_major_auto_core_updates'] ) {
					$bps_toggle_allow_major_auto_core_updates = 'disabled';
				}

				$BPS_WP_Autoupdate_Options = array(
				'bps_automatic_updater_disabled' 		=> $bps_toggle_automatic_updater_disabled, 
				'bps_auto_update_core_updates_disabled' => $bps_toggle_auto_update_core_updates_disabled, 
				'bps_auto_update_core' 					=> $bps_toggle_auto_update_core, 
				'bps_allow_dev_auto_core_updates' 		=> $bps_toggle_allow_dev_auto_core_updates, 
				'bps_allow_minor_auto_core_updates' 	=> $bps_toggle_allow_minor_auto_core_updates, 
				'bps_allow_major_auto_core_updates' 	=> $bps_toggle_allow_major_auto_core_updates 
				);	
						
				foreach( $BPS_WP_Autoupdate_Options as $key => $value ) {
					update_option('bulletproof_security_options_mu_wp_autoupdate', $BPS_WP_Autoupdate_Options);
				}			
			}
		}
	}
}

bpsPro_toggle_links();

function bpsPro_mu_plugin_actlinks( $links, $file ) {
	static $this_plugin;
	
	if ( ! $this_plugin ) 
		$this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {

		$wp_auto_update_options = get_option('bulletproof_security_options_mu_wp_autoupdate');
		
		if ( ! function_exists( 'wp_create_nonce' ) ) {
			require_once( ABSPATH . '/wp-includes/pluggable.php' );
		}
		
		$nonce = wp_create_nonce( 'bps-anti-csrf' );
	
		// Disable all Automatic Updates: Core, Plugins and Themes.
		if ( $wp_auto_update_options['bps_automatic_updater_disabled'] == 'enabled' ) {			

			if ( is_multisite() ) {
				$links[] = '<a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_automatic_updater_disabled=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Disable all Updates: On</a>';
			} else {
				$links[] = '<a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_automatic_updater_disabled=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Disable all Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_automatic_updater_disabled=enable&_wpnonce=$nonce" ).'">Disable all Updates: Off</a>';
			} else {
				$links[] = '<a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_automatic_updater_disabled=enable&_wpnonce=$nonce" ).'">Disable all Updates: Off</a>';
			}
		}

		// Disable all WordPress Core Automatic Updates: Development, Minor and Major
		if ( $wp_auto_update_options['bps_auto_update_core_updates_disabled'] == 'enabled' ) {			

			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core_updates_disabled=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Disable all Core Updates: On</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core_updates_disabled=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Disable all Core Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core_updates_disabled=enable&_wpnonce=$nonce" ).'">Disable all Core Updates: Off</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core_updates_disabled=enable&_wpnonce=$nonce" ).'">Disable all Core Updates: Off</a>';
			}
		}

		// Enable all WordPress Core Automatic Updates: Development, Minor and Major
		if ( $wp_auto_update_options['bps_auto_update_core'] == 'enabled' ) {			

			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable all Core Updates: On</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable all Core Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core=enable&_wpnonce=$nonce" ).'">Enable all Core Updates: Off</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_auto_update_core=enable&_wpnonce=$nonce" ).'">Enable all Core Updates: Off</a>';
			}
		}

		// Enable WordPress Core Development Automatic Updates
		if ( $wp_auto_update_options['bps_allow_dev_auto_core_updates'] == 'enabled' ) {	

			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_dev_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Development Updates: On</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_dev_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Development Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_dev_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Development Updates: Off</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_dev_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Development Updates: Off</a>';
			}
		}

		// Enable WordPress Core Minor Automatic Updates
		if ( $wp_auto_update_options['bps_allow_minor_auto_core_updates'] == 'enabled' ) {

			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_minor_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Minor Updates: On</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_minor_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Minor Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_minor_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Minor Updates: Off</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_minor_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Minor Updates: Off</a>';
			}
		}
		
		// Enable WordPress Core Major Automatic Updates
		if ( $wp_auto_update_options['bps_allow_major_auto_core_updates'] == 'enabled' ) {

			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_major_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Major Updates: On</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_major_auto_core_updates=disable&_wpnonce=$nonce" ).'" style="color:green;font-weight:600">Enable Major Updates: On</a>';
			}		

		} else {
		
			if ( is_multisite() ) {
				$links[] = '<br><a href="'.network_admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_major_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Major Updates: Off</a>';
			} else {
				$links[] = '<br><a href="'.admin_url( "plugins.php?plugin_status=mustuse&bps_toggle_allow_major_auto_core_updates=enable&_wpnonce=$nonce" ).'">Enable Major Updates: Off</a>';
			}
		}	
	}
	return $links;
}

add_filter( 'plugin_action_links', 'bpsPro_mu_plugin_actlinks', 10, 2 );
add_filter( 'network_admin_plugin_action_links', 'bpsPro_mu_plugin_actlinks', 10, 2 );

function bpsPro_wp_automatic_updates_free() {
	
	$wp_auto_update_options = get_option('bulletproof_security_options_mu_wp_autoupdate');

	if ( isset($wp_auto_update_options['bps_automatic_updater_disabled']) && $wp_auto_update_options['bps_automatic_updater_disabled'] == 'enabled' ) {
		add_filter( 'automatic_updater_disabled', '__return_true' );
	}

	if ( isset($wp_auto_update_options['bps_auto_update_core_updates_disabled']) && $wp_auto_update_options['bps_auto_update_core_updates_disabled'] == 'enabled' ) {
		add_filter( 'auto_update_core', '__return_false' );
	}

	if ( isset($wp_auto_update_options['bps_auto_update_core']) && $wp_auto_update_options['bps_auto_update_core'] == 'enabled' ) {
		add_filter( 'auto_update_core', '__return_true' );
	}

	if ( isset($wp_auto_update_options['bps_allow_dev_auto_core_updates']) && $wp_auto_update_options['bps_allow_dev_auto_core_updates'] == 'enabled' ) {
		add_filter( 'allow_dev_auto_core_updates', '__return_true' );
	}

	if ( isset($wp_auto_update_options['bps_allow_minor_auto_core_updates']) && $wp_auto_update_options['bps_allow_minor_auto_core_updates'] == 'enabled' ) {
		add_filter( 'allow_minor_auto_core_updates', '__return_true' );
	}

	if ( isset($wp_auto_update_options['bps_allow_major_auto_core_updates']) && $wp_auto_update_options['bps_allow_major_auto_core_updates'] == 'enabled' ) {
		add_filter( 'allow_major_auto_core_updates', '__return_true' );
	}
}

bpsPro_wp_automatic_updates_free();

// Add additional links on the BPS Must-Use plugins page
function bpsPro_mu_plugin_extra_links_free($links, $file) {
	static $this_plugin;
	//if ( ! current_user_can('install_plugins') )
		//return $links;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		$links[] = '<a href="https://forum.ait-pro.com/forums/topic/wordpress-automatic-update-help-forum-topic-bps-must-use-plugin/" target="_blank" title="WordPress Automatic Update Help Forum Topic">' . __('WordPress Automatic Update Help Forum Topic', 'bulleproof-security').'</a>';
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'bpsPro_mu_plugin_extra_links_free', 10, 2 )

?>