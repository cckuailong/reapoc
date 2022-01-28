<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/*********************************************************************** */
/* Hidden Plugin Folders|Files (HPF) Cron schedules, intervals, function */
/*********************************************************************** */

add_filter('cron_schedules', 'bpsPro_add_cron_intervals'); 

// Add Cron Schedule Intervals - 1, 2, 3, 4, 5, 10, 15, 30, 60 minutes
// Intervals only need to be setup once - other cron jobs can hook into and use these intervals
// Note: future planned usage for: 2, 3, and 4 minute intervals
function bpsPro_add_cron_intervals($schedules) {
	$schedules['minutes_1'] = array(
		'interval' 	=> 60,
		'display' 	=> __('Once every minute')
	);
	$schedules['minutes_2'] = array(
		'interval' 	=> 120,
		'display' 	=> __('Once every 2 minutes')
	);
	$schedules['minutes_3'] = array(
		'interval' 	=> 180,
		'display' 	=> __('Once every 3 minutes')
	);
	$schedules['minutes_4'] = array(
		'interval' 	=> 240,
		'display' 	=> __('Once every 4 minutes')
	);
	$schedules['minutes_5'] = array(
		'interval' 	=> 300,
		'display' 	=> __('Once every 5 minutes')
	);
	$schedules['minutes_10'] = array(
		'interval' 	=> 600,
		'display' 	=> __('Once every 10 minutes')
	);
	$schedules['minutes_15'] = array(
		'interval' 	=> 900,
		'display' 	=> __('Once every 15 minutes')
	);
	$schedules['minutes_30'] = array(
		'interval' 	=> 1800,
		'display' 	=> __('Once every 30 minutes')
	);
	$schedules['minutes_60'] = array(
		'interval' 	=> 3600,
		'display' 	=> __('Once every 60 minutes')
	);
	$schedules['daily'] = array( 
		'interval' => 86400, 
		'display' => __('Once Daily') 
	);

	return $schedules;
}

add_action('bpsPro_HPF_check', 'bpsPro_hidden_plugins_check');

function bpsPro_schedule_HPF_checks() {
global $blog_id;
$options = get_option('bulletproof_security_options_hpf_cron');
$killit = '';
	
	if ( ! get_option('bulletproof_security_options_hpf_cron') || ! $options['bps_hidden_plugins_cron'] || $options['bps_hidden_plugins_cron'] == '' || is_multisite() && $blog_id != 1 ) {
		return $killit;
	}	
	
	if ( $options['bps_hidden_plugins_cron'] == 'On' ) {
	
		$bpsCronCheck = wp_get_schedule('bpsPro_HPF_check');

	if ( $options['bps_hidden_plugins_cron_frequency'] == '1' ) {
	if ( $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'minutes_60' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}

	if ( ! wp_next_scheduled( 'bpsPro_HPF_check' ) ) {
		wp_schedule_event( time(), 'minutes_1', 'bpsPro_HPF_check');
	}
	}
	
	if ( $options['bps_hidden_plugins_cron_frequency'] == '5' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'minutes_60' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'minutes_5', 'bpsPro_HPF_check' );
	}
	}
	
	if ( $options['bps_hidden_plugins_cron_frequency'] == '10' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'minutes_60' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'minutes_10', 'bpsPro_HPF_check' );
	}
	}
	
	if ( $options['bps_hidden_plugins_cron_frequency'] == '15' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'minutes_60' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'minutes_15', 'bpsPro_HPF_check' );
	}
	}
	
	if ( $options['bps_hidden_plugins_cron_frequency'] == '30' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_60' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'minutes_30', 'bpsPro_HPF_check' );
	}
	}

	if ( $options['bps_hidden_plugins_cron_frequency'] == '60' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'daily' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'minutes_60', 'bpsPro_HPF_check' );
	}
	}

	if ( $options['bps_hidden_plugins_cron_frequency'] == 'daily' ) {
	if ( $bpsCronCheck == 'minutes_1' || $bpsCronCheck == 'minutes_5' || $bpsCronCheck == 'minutes_10' || $bpsCronCheck == 'minutes_15' || $bpsCronCheck == 'minutes_30' || $bpsCronCheck == 'minutes_60' ) {
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
	
	if ( ! wp_next_scheduled('bpsPro_HPF_check') ) {
		wp_schedule_event( time(), 'daily', 'bpsPro_HPF_check' );
	}
	}

	}
	elseif ( $options['bps_hidden_plugins_cron'] == 'Off' ) { 
		wp_clear_scheduled_hook('bpsPro_HPF_check');
	}
}

add_action('init', 'bpsPro_schedule_HPF_checks');

function bpsPro_hidden_plugins_check() {

	$HPF_options = get_option('bulletproof_security_options_hpf_cron');	
	
	if ( $HPF_options['bps_hidden_plugins_cron'] == 'Off' || ! get_option('bulletproof_security_options_hpf_cron' || is_multisite() && $blog_id != 1 ) ) {
		exit();
	}
	
	bpsPro_hidden_plugins_check_alert();
}

$HPF_options = get_option('bulletproof_security_options_hpf_cron');
// Note: This simply handles displaying a Dashboard alert or not based on the alert value == display_alert.
if ( isset($HPF_options['bps_hidden_plugins_cron_alert']) && $HPF_options['bps_hidden_plugins_cron_alert'] == 'display_alert' ) {

	if ( is_multisite() && $blog_id != 1 ) {
		// do nothing
	} else {
		add_action('admin_notices', 'bpsPro_hidden_plugins_check_alert');
		add_action('network_admin_notices', 'bpsPro_hidden_plugins_check_alert');
	}
}

// Check for hidden or empty plugin folders & check root /plugins/ folder for unrecognized non-standard WP files.
// Option to Ignore Hidden Plugin Folders & Files.
// Notes: must-use plugins/files cannot be hidden so no need to check for must-use plugins/files.
// PHP Standard PHP Library (SPL) Check - if SPL is not installed exit.
function bpsPro_hidden_plugins_check_alert() {

	if ( ! is_array( spl_classes() ) ) {
		exit();
	}

	global $bps_topDiv, $bps_bottomDiv;
		
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$all_plugins = get_plugins();
	$wp_plugins_array = array();

	foreach ( $all_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {

			$wp_plugins_array[] = str_replace( array( '\\', '//' ), "/", WP_PLUGIN_DIR . '/' . dirname($key) );
		}
	}

	$source = WP_PLUGIN_DIR;
	$options = get_option('bulletproof_security_options_hidden_plugins');
	$HPF_options = get_option('bulletproof_security_options_hpf_cron');
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$alert1 = '';
	$alert2 = '';
	$alert3 = '';
	$alert4 = '';
	$alert5 = '';
		
	$hidden_plugins = array_filter( explode( ', ', trim( $options['bps_hidden_plugins_check'], ", \t\n\r" ) ) );
	$hidden_plugins_array = array();
		
	foreach ( $hidden_plugins as $key => $value ) {
		$hidden_plugins_array[] = $value;
	}

	if ( is_dir($source) ) {
		
		$depth = 1;
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);	
		$iterator->setMaxDepth($depth);
		
		$dir_plugins_array = array();
		$hello_dolly = WP_PLUGIN_DIR . '/hello.php';
		$plugins_index = WP_PLUGIN_DIR . '/index.php';
		$plugins_htaccess = WP_PLUGIN_DIR . '/.htaccess';
		// Replace ABSPATH = wp-content/plugins
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		// Replace ABSPATH = wp-content
		$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
		// Replace wp-content/ = plugins
		$plugins_dir_name = str_replace( $bps_wpcontent_dir . '/', "", $bps_plugin_dir );
		$pre_background_image_url = site_url( '/wp-content/plugins/bulletproof-security/admin/images/pre_bg.png' );

		foreach ( $iterator as $files ) {
			
			if ( $files->isFile() ) {
				
				// only search files in the root /plugins/ folder
				if ( ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/).*(\\\|\/)/', $files ) ) {
					
					if ( file_exists($hello_dolly) ) {
						$check_string_hd = @file_get_contents($hello_dolly);
						
						if ( preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)hello\.php/', $files ) && ! strpos( $check_string_hd, "Plugin Name: Hello Dolly" ) && ! in_array( $files->getFilename(), $hidden_plugins_array ) ) {
							
							if ( @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
								$alert1 = 'alert';
								echo $bps_topDiv;
								$text = '<strong><font color="#fb0101">'.__('BPS Hidden Plugin Folder|Files (HPF) Alert', 'bulletproof-security').'</font><br>'.__('A non-standard WP hello.php file (Hello Dolly Plugin) was found in your /plugins/ folder and it is hidden/not displayed on the WordPress Plugins page. Most likely the hello.php file is a hacker file or contains hacker code. If you have modified the hello.php file and/or it is safe to ignore this file you can ignore this file check by adding the HPF Ignore Rule shown below in the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#UAEG-Menu-Link' ).'">'.__('Ignore Hidden Plugin Folders & Files', 'bulletproof-security').'</a>'.__(' textarea box option to make this Alert go away.', 'bulletproof-security').'</strong><br><strong>'.__('File Path: ', 'bulletproof-security').'</strong>'.$files->getPathname().'<br><strong>'.__('HPF Ignore Rule: ', 'bulletproof-security').'</strong>'.basename($files->getPathname()).'<br><strong>'.__('Last Modified Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getMTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getMTime() + $gmt_offset).'<br><strong>'.__('Last Change Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getCTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getCTime() + $gmt_offset).'<br><strong>'.__('Last Access Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getATime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getATime() + $gmt_offset).'<br><strong>'.__('File Contents: ', 'bulletproof-security').'</strong><pre id="shown" style="overflow:auto;white-space:pre-wrap;height:100px;width:60%;margin:0px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">'.htmlspecialchars($check_string_hd).'</pre>';
								echo $text;
								echo $bps_bottomDiv;
							}
						}
					}
				
					if ( file_exists($plugins_index) ) {
						$check_string_index = @file_get_contents($plugins_index);
						
						if ( preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)index\.php/', $files ) && preg_match( '/[\=\%\{\}\(\)\,\;@\'\"\&\+\!]/', $check_string_index ) && ! in_array( $files->getFilename(), $hidden_plugins_array ) ) {
							
							if ( @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
								$alert2 = 'alert';
								echo $bps_topDiv;
								$text = '<strong><font color="#fb0101">'.__('BPS Hidden Plugin Folder|Files (HPF) Alert', 'bulletproof-security').'</font><br>'.__('A non-standard WP index.php file found in your /plugins/ folder appears to have been altered/tampered with. Most likely the index.php file is a hacker file or contains hacker code. If you have modified the index.php file and/or it is safe to ignore this file you can ignore this file check by adding the HPF Ignore Rule shown below in the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#UAEG-Menu-Link' ).'">'.__('Ignore Hidden Plugin Folders & Files', 'bulletproof-security').'</a>'.__(' textarea box option to make this Alert go away.', 'bulletproof-security').'</strong><br><strong>'.__('File Path: ', 'bulletproof-security').'</strong>'.$files->getPathname().'<br><strong>'.__('HPF Ignore Rule: ', 'bulletproof-security').'</strong>'.basename($files->getPathname()).'<br><strong>'.__('Last Modified Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getMTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getMTime() + $gmt_offset).'<br><strong>'.__('Last Change Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getCTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getCTime() + $gmt_offset).'<br><strong>'.__('Last Access Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getATime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getATime() + $gmt_offset).'<br><strong>'.__('File Contents: ', 'bulletproof-security').'</strong><pre id="shown" style="overflow:auto;white-space:pre-wrap;height:100px;width:60%;margin:0px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">'.htmlspecialchars($check_string_index).'</pre>';
								echo $text;
								echo $bps_bottomDiv;
							}
						}
					}				

					if ( file_exists($plugins_htaccess) ) {
						$check_string_ht = @file_get_contents($plugins_htaccess);
						
						if ( preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)\.htaccess/', $files ) && ! strpos( $check_string_ht, "BULLETPROOF" ) && ! in_array( $files->getFilename(), $hidden_plugins_array ) ) {
							
							if ( @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
								$alert3 = 'alert';
								echo $bps_topDiv;
								$text = '<strong><font color="#fb0101">'.__('BPS Hidden Plugin Folder|Files (HPF) Alert', 'bulletproof-security').'</font><br>'.__('An htaccess file was found in your /plugins/ folder and it does not appear to be a BPS htaccess file. Most likely the htaccess file is a hacker file or contains hacker code. If you have modified the htaccess file and/or it is safe to ignore this file you can ignore this file check by adding the HPF Ignore Rule shown below in the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#UAEG-Menu-Link' ).'">'.__('Ignore Hidden Plugin Folders & Files', 'bulletproof-security').'</a>'.__(' textarea box option to make this Alert go away.', 'bulletproof-security').'</strong><br><strong>'.__('File Path: ', 'bulletproof-security').'</strong>'.$files->getPathname().'<br><strong>'.__('HPF Ignore Rule: ', 'bulletproof-security').'</strong>'.basename($files->getPathname()).'<br><strong>'.__('Last Modified Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getMTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getMTime() + $gmt_offset).'<br><strong>'.__('Last Change Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getCTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getCTime() + $gmt_offset).'<br><strong>'.__('Last Access Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getATime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getATime() + $gmt_offset).'<br><strong>'.__('File Contents: ', 'bulletproof-security').'</strong><pre id="shown" style="overflow:auto;white-space:pre-wrap;height:100px;width:60%;margin:0px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">'.htmlspecialchars($check_string_ht).'</pre>';
								echo $text;
								echo $bps_bottomDiv;
							}
						}
					}
					
					// list any other files found in the /plugins/ folder except for ignored file names.
					if ( ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)hello\.php/', $files ) && ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)index\.php/', $files ) && ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)\.htaccess/', $files ) && ! in_array( $files->getFilename(), $hidden_plugins_array ) ) {
					
						$file_contents = @file_get_contents($files->getPathname());
						
						if ( @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
							$alert4 = 'alert';
							echo $bps_topDiv;
							$text = '<strong><font color="#fb0101">'.__('BPS Hidden Plugin Folder|Files (HPF) Alert', 'bulletproof-security').'</font><br>'.__('An unrecognized/non-standard WP file was found in your /plugins/ folder. This file may be a hacker file or contain hacker code. If you recognize this file and/or it is safe to ignore this file you can ignore this file check by adding the HPF Ignore Rule shown below in the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#UAEG-Menu-Link' ).'">'.__('Ignore Hidden Plugin Folders & Files', 'bulletproof-security').'</a>'.__(' textarea box option to make this Alert go away.', 'bulletproof-security').'</strong><br><strong>'.__('File Path: ', 'bulletproof-security').'</strong>'.$files->getPathname().'<br><strong>'.__('HPF Ignore Rule: ', 'bulletproof-security').'</strong>'.basename($files->getPathname()).'<br><strong>'.__('Last Modified Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getMTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getMTime() + $gmt_offset).'<br><strong>'.__('Last Change Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getCTime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getCTime() + $gmt_offset).'<br><strong>'.__('Last Access Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), $files->getATime() + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), $files->getATime() + $gmt_offset).'<br><strong>'.__('File Contents: ', 'bulletproof-security').'</strong><pre id="shown" style="overflow:auto;white-space:pre-wrap;height:100px;width:60%;margin:0px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">'.htmlspecialchars($file_contents).'</pre>';
							echo $text;
							echo $bps_bottomDiv;
						}
					}
				}
			} // end if ( $files->isFile() ) {
			
			if ( $files->isDir() ) {
			
				// only return root folders in the root /plugins/ folder and not child subfolders & dir dots
				if ( ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/).*(\\\|\/)/', $files ) && ! preg_match( '/\/'.$plugins_dir_name.'(\\\|\/)(\.|\.\.)/', $files ) ) {
				
					$dir_plugins_array[] = str_replace( array( '\\', '//' ), "/", $files );
				}
			}
		} // end foreach ( $iterator as $files ) {

		$result = array_diff( $dir_plugins_array, $wp_plugins_array );

		if ( ! empty($result) ) {
		
			foreach ( $result as $key => $value ) {
					
				if ( ! in_array( basename($value), $hidden_plugins_array ) ) {
					
					if ( @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
						$alert5 = 'alert';
						echo $bps_topDiv;
						$text = '<strong><font color="#fb0101">'.__('BPS Hidden Plugin Folder|Files (HPF) Alert', 'bulletproof-security').'</font><br>'.__('A plugin folder was found in your /plugins/ folder that is either a hidden plugin (plugin that is not displayed on the WordPress Plugins page) or an empty plugin folder. You can either delete this folder or if you recognize this folder and/or it is safe to ignore this folder you can ignore this folder check by adding the HPF Ignore Rule shown below in the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#UAEG-Menu-Link' ).'">'.__('Ignore Hidden Plugin Folders & Files', 'bulletproof-security').'</a>'.__(' textarea box option to make this Alert go away.', 'bulletproof-security').'</strong><br><strong>'.__('Plugin Folder Path: ', 'bulletproof-security').'</strong>'.$value.'<br><strong>'.__('HPF Ignore Rule: ', 'bulletproof-security').'</strong>'.basename($value).'<br><strong>'.__('Last Modified Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), filemtime($value) + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), filemtime($value) + $gmt_offset).'<br><strong>'.__('Last Change Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), filectime($value) + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), filectime($value) + $gmt_offset).'<br><strong>'.__('Last Access Time: ', 'bulletproof-security').'</strong>'.date_i18n( get_option('date_format'), fileatime($value) + $gmt_offset) . ' @ ' . date_i18n(get_option('time_format'), fileatime($value) + $gmt_offset).'<br>';
						echo $text;
						echo $bps_bottomDiv;
					}
				}
			}
		}

		if ( $alert1 == 'alert'	|| $alert2 == 'alert' || $alert3 == 'alert' || $alert4 == 'alert' || $alert5 == 'alert' ) {
		
			$HPF_cron_time = wp_next_scheduled( 'bpsPro_HPF_check' );
		
			// Send email on Cron fire with 0-15 second buffer & do not send an email on page load if user is logged into the site.
			if ( $HPF_cron_time - time() >= '15' && ! is_user_logged_in() ) {
				bps_smonitor_hpf_email();
			}

			$HPF_DB_Options = array( 
			'bps_hidden_plugins_cron' 				=> $HPF_options['bps_hidden_plugins_cron'], 
			'bps_hidden_plugins_cron_frequency' 	=> $HPF_options['bps_hidden_plugins_cron_frequency'], 
			'bps_hidden_plugins_cron_email' 		=> 'send_email', // decided not to use this
			'bps_hidden_plugins_cron_alert' 		=> 'display_alert' 
			);
	
			foreach( $HPF_DB_Options as $key => $value ) {
				update_option('bulletproof_security_options_hpf_cron', $HPF_DB_Options);
			}	
	
		} else {
		
			$HPF_DB_Options = array( 
			'bps_hidden_plugins_cron' 				=> $HPF_options['bps_hidden_plugins_cron'], 
			'bps_hidden_plugins_cron_frequency' 	=> $HPF_options['bps_hidden_plugins_cron_frequency'], 
			'bps_hidden_plugins_cron_email' 		=> '', 
			'bps_hidden_plugins_cron_alert' 		=> '' 
			);
	
			foreach( $HPF_DB_Options as $key => $value ) {
				update_option('bulletproof_security_options_hpf_cron', $HPF_DB_Options);
			}
		}
	}
}

// Hidden Plugin Folders|Files (HPF) Email Alert
function bps_smonitor_hpf_email() {
global $wpdb, $blog_id;
	
	if ( is_multisite() && $blog_id != 1 ) {
		// do nothing
	} else {

		$options = get_option('bulletproof_security_options_email');
		$timeNow = time();
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);

		$bps_email_to = $options['bps_send_email_to'];
		$bps_email_from = $options['bps_send_email_from'];
		$bps_email_cc = $options['bps_send_email_cc'];
		$bps_email_bcc = $options['bps_send_email_bcc'];
		$path = '/wp-admin/admin.php?page=bulletproof-security%2Fadmin%2Fcore%2Fcore.php';
		$justUrl = get_site_url(null, $path, null);

		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $bps_email_from, 'Cc: ' . $bps_email_cc, 'Bcc: ' . $bps_email_bcc );
		$subject = " BPS Alert: Hidden Plugin Folders|Files (HPF) Alert - $timestamp ";
	
		$message =  '<p>The BPS Hidden Plugin Folders|Files (HPF) Cron has detected a hidden or empty plugin folder or a non-standard WP file or altered file in the /plugins/ folder. To view exact details of what was detected, log into your website and check the Hidden Plugin Folders|Files (HPF) Dashboard Alert.</p>';
		$message .= '<p>Site: '.$justUrl.'</p>'; 

		wp_mail( $bps_email_to, $subject, $message, $headers );
	}
}

?>