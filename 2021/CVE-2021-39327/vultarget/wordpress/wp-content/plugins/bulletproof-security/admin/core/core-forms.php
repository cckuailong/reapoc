<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// WBM Activation: copy and rename wpadmin-secure.htaccess Master file to wp-admin folder
// Do String Replacements for Custom Code AFTER new .htaccess file has been copied to wp-admin
if ( isset( $_POST['Submit-WBM-Activate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_wbm_activate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wp-admin htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('wp-admin Folder BulletProof Mode was not activated. Either it is disabled on the Setup Wizard Options page or you have a Go Daddy Managed WordPress Hosting account. The wp-admin folder is restricted on GDMW hosting account types.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;		
	return;
	}
	
	$options = get_option('bulletproof_security_options_customcode_WPA');  
	$HtaccessMaster = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
	$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
	$permsHtaccess = @substr(sprintf('%o', fileperms($wpadminHtaccess)), -4);
	$sapi_type = php_sapi_name();	
	$bpsString1 = "# CCWTOP";
	$bpsString2 = "# CCWPF";
	$bpsString3 = '/#\sBEGIN\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES(.*)#\sEND\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES/s';
	$bpsString4 = '/#\sBEGIN\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS(.*)#\sEND\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS/s';
	$bpsReplace1 = htmlspecialchars_decode($options['bps_customcode_one_wpa'], ENT_QUOTES);
	$bpsReplace2 = htmlspecialchars_decode($options['bps_customcode_two_wpa'], ENT_QUOTES);
	$bpsReplace3 = htmlspecialchars_decode($options['bps_customcode_deny_files_wpa'], ENT_QUOTES);	
	$bpsReplace4 = htmlspecialchars_decode($options['bps_customcode_bpsqse_wpa'], ENT_QUOTES);	
	
	if ( @substr($sapi_type, 0, 6) != 'apache' || @$permsHtaccess != '0666' || @$permsHtaccess != '0777') { // Windows IIS, XAMPP, etc
		@chmod($wpadminHtaccess, 0644);
	}		

	if ( ! copy($HtaccessMaster, $wpadminHtaccess) ) {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Failed to activate wp-admin Folder BulletProof Mode protection. Your wp-admin folder is NOT protected.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;
			
	} else {
	
		if ( file_exists($wpadminHtaccess) ) {
				
			if ( @$permsHtaccess != '0666' || @$permsHtaccess != '0777' ) { // Windows IIS, XAMPP, etc
				@chmod($wpadminHtaccess, 0644);
			}				
				
			$bpsBaseContent = file_get_contents($wpadminHtaccess);
		
			if ( $options['bps_customcode_deny_files_wpa'] != '') {        
				$bpsBaseContent = preg_replace('/#\sBEGIN\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES(.*)#\sEND\sBPS\sWPADMIN\sDENY\sACCESS\sTO\sFILES/s', $bpsReplace3, $bpsBaseContent);
			}
			
			if ( $options['bps_customcode_bpsqse_wpa'] != '') {        
				$bpsBaseContent = preg_replace('/#\sBEGIN\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS(.*)#\sEND\sBPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS/s', $bpsReplace4, $bpsBaseContent);
			}
				
			$bpsBaseContent = str_replace($bpsString1, $bpsReplace1, $bpsBaseContent);
			$bpsBaseContent = str_replace($bpsString2, $bpsReplace2, $bpsBaseContent);
				
			if ( file_put_contents( $wpadminHtaccess, $bpsBaseContent ) ) {
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('wp-admin Folder BulletProof Mode protection activated successfully.', 'bulletproof-security').'</strong></font>';
				echo $text;
				echo $bps_bottomDiv;
			}
		}
	}
}

// WBM Deactivation: delete the wp-admin folder htaccess file
if ( isset( $_POST['Submit-WBM-Deactivate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_wbm_deactivate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wp-admin htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('wp-admin Folder BulletProof Mode was not activated. Either it is disabled on the Setup Wizard Options page or you have a Go Daddy Managed WordPress Hosting account. The wp-admin folder is restricted on GDMW hosting account types.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;		
		return;
	}
	
	$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
	
	@unlink($wpadminHtaccess);
	
	if ( file_exists($wpadminHtaccess) ) {
		
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Failed to deactivate wp-admin Folder BulletProof Mode. The wp-admin htaccess file does not exist. It may have been deleted or renamed already.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;
	
	} else {
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('wp-admin Folder BulletProof Mode deactivated successfully. The wp-admin htaccess file has been deleted.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}

// Form: HPF Save Hidden Plugin Folders & Files Cron Form Options
if ( isset( $_POST['Submit-Hidden-Plugins'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_hpf_cron');

	$HPF_Options = array( 
	'bps_hidden_plugins_cron' 				=> esc_html($_POST['hpf_on_off']), 
	'bps_hidden_plugins_cron_frequency' 	=> esc_html($_POST['hpf_cron_frequency']), 
	'bps_hidden_plugins_cron_email' 		=> esc_html(''), 
	'bps_hidden_plugins_cron_alert' 		=> esc_html('')
	);
	
	foreach( $HPF_Options as $key => $value ) {
		update_option('bulletproof_security_options_hpf_cron', $HPF_Options);
	}
	
	$hpf_options = get_option('bulletproof_security_options_hpf_cron');
	
	if ( $hpf_options['bps_hidden_plugins_cron'] == 'On' ) {
	
		echo $bps_topDiv;
		$text = '<strong><font color="green">'.__('The Hidden Plugin Folders|Files (HPF) Cron is turned On.', 'bulletproof-security').'<br>'.__('The HPF Cron Check Frequency setting is: ', 'bulletproof-security').$hpf_options['bps_hidden_plugins_cron_frequency'].__(' minutes.', 'bulletproof-security').'</font></strong>';
		echo $text;
		echo $bps_bottomDiv;
		
	} elseif ( $hpf_options['bps_hidden_plugins_cron'] == 'Off' ) {
		
		wp_clear_scheduled_hook('bpsPro_HPF_check');
			
		echo $bps_topDiv;
		$text = '<strong><font color="green">'.__('Hidden Plugin Folders|Files (HPF) Cron has been turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $text;
		echo $bps_bottomDiv;		
	}
}

// Form: HPF Save Ignore Hidden Plugin Folders & Files Rules
if ( isset( $_POST['Hidden-Plugins-Ignore-Submit'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_hpf_cron_ignore');
	
	$HPFI_Options = array( 'bps_hidden_plugins_check' => esc_html( $_POST['bps_hidden_plugins_check'] ) );
	
	foreach( $HPFI_Options as $key => $value ) {
		update_option('bulletproof_security_options_hidden_plugins', $HPFI_Options);
	}

	$hpfi_options = get_option('bulletproof_security_options_hidden_plugins');
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('Ignore Hidden Plugin Folders & Files settings saved.', 'bulletproof-security').'<br>'.__('Current HPF Ignore Rules: ', 'bulletproof-security').$hpfi_options['bps_hidden_plugins_check'].'</font></strong>';
	echo $text;
	echo $bps_bottomDiv;
}

// MBM Activation: copy Deny All htaccess file to BPS Master htaccess folder /htaccess
if ( isset( $_POST['Submit-MBM-Activate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_mbm_activate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Master htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$bps_rename_htaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all.htaccess';
	$deny_all_ifmodule = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all-ifmodule.htaccess';
	$bps_rename_htaccess_renamed = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/.htaccess';
	
	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');	

	if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
			
		if ( ! copy($deny_all_ifmodule, $bps_rename_htaccess_renamed) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate Master htaccess Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /bulletproof-security/admin/htaccess/.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Master htaccess Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}			
		
	} else {
			
		if ( ! copy($bps_rename_htaccess, $bps_rename_htaccess_renamed) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate Master htaccess Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /bulletproof-security/admin/htaccess/.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Master htaccess Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}			
	}
}

// MBM Deactivation: delete BPS Master htaccess folder /htaccess file
if ( isset( $_POST['Submit-MBM-Deactivate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_mbm_deactivate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Master htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$mbm_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/.htaccess';

	@unlink($mbm_htaccess_file);
	
	if ( file_exists($mbm_htaccess_file) ) {
		
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Failed to deactivate Master htaccess Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /bulletproof-security/admin/htaccess/.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;
	
	} else {
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('Master htaccess Folder BulletProof Mode deactivated successfully.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}

// BBM Activation: copy Deny All htaccess file to BPS Backup Folder /bps-backup
if ( isset( $_POST['Submit-BBM-Activate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_bbm_activate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BPS Backup htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$bps_rename_htaccess_backup = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all.htaccess';
	$deny_all_ifmodule = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/deny-all-ifmodule.htaccess';
	$bps_rename_htaccess_backup_online = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	
	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');	

	if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {
			
		if ( ! copy($deny_all_ifmodule, $bps_rename_htaccess_backup_online) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate BPS Backup Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('BPS Backup Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}			
		
	} else {
			
		if ( ! copy($bps_rename_htaccess_backup, $bps_rename_htaccess_backup_online) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to activate BPS Backup Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup.', 'bulletproof-security').'</strong></font>';
			echo $text;
   			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('BPS Backup Folder BulletProof Mode activated successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}			
	}
}

// BBM Deactivation: delete BPS Backup folder /bps-backup htaccess file
if ( isset( $_POST['Submit-BBM-Deactivate'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_bbm_deactivate' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: BPS Backup htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$bbm_htaccess_file = WP_CONTENT_DIR . '/bps-backup/.htaccess';

	@unlink($bbm_htaccess_file);
	
	if ( file_exists($bbm_htaccess_file) ) {
		
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Failed to deactivate BPS Backup Folder BulletProof Mode. Check the file or folder permissions or Ownership for this folder: /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;
	
	} else {
		
		echo $bps_topDiv;
		$text = '<font color="green"><strong>'.__('BPS Backup Folder BulletProof Mode deactivated successfully.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}

// Form: Backup htaccess files
if ( isset( $_POST['Submit-Backup-htaccess-Files'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_backup_active_htaccess_files' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: htaccess file Backup is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$old_backroot = ABSPATH . '.htaccess';
	$new_backroot = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess';
	$old_backwpadmin = ABSPATH . 'wp-admin/.htaccess';
	$new_backwpadmin = WP_CONTENT_DIR . '/bps-backup/master-backups/wpadmin.htaccess';
	
	if ( ! file_exists($old_backroot) ) { 
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('You do not currently have an .htaccess file in your Root folder to backup.', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo '</p></div>';
	
	} else {	
		
		if ( ! copy($old_backroot, $new_backroot) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to Backup Your Root .htaccess File. File copy function failed. Check the folder permissions for the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder. Folder permissions should be set to 755.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Your currently active Root .htaccess file has been backed up successfully.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		}
	}
		
	if ( ! file_exists($old_backwpadmin) ) { 
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('You do not currently have an htaccess file in your wp-admin folder to backup.', 'bulletproof-security').'</strong></font>';
		echo $text;
		echo $bps_bottomDiv;
		
	} else {
		
		if ( ! copy($old_backwpadmin, $new_backwpadmin) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Failed to Backup Your wp-admin htaccess File. File copy function failed. Check the folder permissions for the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder. Folder permissions should be set to 755.', 'bulletproof-security').'</strong></font>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
			
			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Your currently active wp-admin htaccess file has been backed up successfully.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}
	}
}

// Form: Restore backed up htaccess files
if ( isset( $_POST['Submit-Restore-htaccess-Files'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_restore_active_htaccess_files' );
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		echo $bps_topDiv;
		$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: htaccess file Restore is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
		echo $text;
    	echo $bps_bottomDiv;
		return;
	}

	$old_restoreroot = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess';
	$new_restoreroot = ABSPATH . '.htaccess';
	$old_restorewpadmin = WP_CONTENT_DIR . '/bps-backup/master-backups/wpadmin.htaccess';
	$new_restorewpadmin = ABSPATH . 'wp-admin/.htaccess';
	$permsRootHtaccess = @substr(sprintf('%o', fileperms($new_restoreroot)), -4);
	$sapi_type = php_sapi_name();		

	if ( file_exists($old_restoreroot) ) { 
		
		if ( @substr($sapi_type, 0, 6) != 'apache' && @$permsRootHtaccess != '0666' || @$permsRootHtaccess != '0777') { // Windows IIS, XAMPP, etc
			@chmod($new_restoreroot, 0644);
		}	
		
	if ( ! copy($old_restoreroot, $new_restoreroot) ) {
		echo $bps_topDiv;
		echo '<font color="#fb0101"><strong>'.__('Failed to Restore Your Root htaccess File. Either you DO NOT currently have a Backed up Root htaccess file or your current active Root htaccess file permissions do not allow the file to be replaced/restored.', 'bulletproof-security').'</strong></font>';
   		echo $bps_bottomDiv;
		
	} else {
			
		if ( @substr($sapi_type, 0, 6) != 'apache' && $options['bps_root_htaccess_autolock'] != 'Off' || $options['bps_root_htaccess_autolock'] != 'On' ) {			
			@chmod($new_restoreroot, 0404);
		}
			
		echo $bps_topDiv;
		$textRoot = '<font color="green"><strong>'.__('Your Root htaccess file has been Restored successfully.', 'bulletproof-security').'</strong></font>';
		echo $textRoot;
		echo $bps_bottomDiv;
	}
	}
		
	if ( file_exists($old_restorewpadmin) ) { 	
	
	if ( ! copy($old_restorewpadmin, $new_restorewpadmin) ) {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong>'.__('Failed to Restore Your wp-admin htaccess File. Either you DO NOT currently have a Backed up wp-admin htaccess file or your current active wp-admin htaccess file permissions do not allow the file to be replaced/restored.', 'bulletproof-security').'</strong></font>';
		echo $text;
   		echo $bps_bottomDiv;
		
	} else {
			
		echo $bps_topDiv;
		$textWpadmin = '<font color="green"><strong>'.__('Your wp-admin htaccess file has been Restored successfully.', 'bulletproof-security').'</strong></font>';
		echo $textWpadmin;
		echo $bps_bottomDiv;
	}
	}
}

// Custom Code page: Reset/Recheck Dismiss Notices
if ( isset( $_POST['bpsResetDismissSubmit'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_reset_dismiss_notices' );	  

	$user_id = $current_user->ID;

	echo '<div id="message" class="updated fade" style="color:#000000;font-weight:600;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';



	if ( ! delete_user_meta($user_id, 'bps_ignore_iis_notice') ) {
		$text = __('The Windows IIS Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Windows IIS check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {
		$text = __('The Bonus Custom Code: Dismiss All Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Dismiss All Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) {
		$text = __('The Bonus Custom Code: Brute Force Login Protection Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Brute Force Login Protection Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_speed_boost_cache_notice') ) {
		$text = __('The Bonus Custom Code: Speed Boost Cache Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Speed Boost Cache Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_author_enumeration_notice') ) {
		$text = __('The Bonus Custom Code: Author Enumeration BOT Probe Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Author Enumeration BOT Probe Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) {
		$text = __('The Bonus Custom Code: XML-RPC DDoS Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: XML-RPC DDoS Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	/*
	if ( ! delete_user_meta($user_id, 'bps_referer_spam_notice') ) {
		$text = __('The Bonus Custom Code: Referer Spam|Phishing Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Referer Spam|Phishing Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}
	*/
	
	if ( ! delete_user_meta($user_id, 'bps_post_request_attack_notice') ) {
		$text = __('The Bonus Custom Code: POST Request Attack Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: POST Request Attack Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_sniff_driveby_notice') ) {
		$text = __('The Bonus Custom Code: Mime Sniffing|Drive-by Download Attack Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Mime Sniffing|Drive-by Download Attack Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {
		$text = __('The Bonus Custom Code: External iFrame|Clickjacking Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: External iFrame|Clickjacking Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
		$text = __('The PHP|php.ini handler htaccess code check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The PHP|php.ini handler htaccess code check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_safemode_notice') ) {
		$text = __('The Safe Mode HUD Check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Safe Mode HUD Check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) {
		$text = __('The Custom Permalinks HUD Check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Custom Permalinks HUD Check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_sucuri_notice') ) {
		$text = __('The Sucuri Restrict wp-content access Hardening Option problem Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Sucuri Restrict wp-content access Hardening Option problem check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
		$text = __('The WordPress Firewall 2 Plugin Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;>'.__('Success! The WordPress Firewall 2 Plugin check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}	

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) {
		$text = __('The New Improved BPS Speed Boost Cache Code Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The New Improved BPS Speed Boost Cache Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	/*
	if ( ! delete_user_meta($user_id, 'bps_ignore_autoupdate_notice') ) {
		$text = __('The BPS Plugin Automatic Update Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS Plugin Automatic Update Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}
	*/

	/*
	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_EPC_plugin_notice') ) {
		$text = __('The Endurance Page Cache (EPC) plugin Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Endurance Page Cache (EPC) plugin Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}
	*/
/*
	if ( ! delete_user_meta($user_id, 'bps_ignore_mscan_notice') ) {
		$text = __('The MScan First Run Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The MScan First Run Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}
*/
	if ( ! delete_user_meta($user_id, 'bps_ignore_jtc_lite_notice') ) {
		$text = __('The JTC-Lite New Feature Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The JTC-Lite New Feature Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_rate_notice') ) {
		$text = __('The BPS Plugin Star Rating Request Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS Plugin Star Rating Request Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_mod_security_notice') ) {
		$text = __('The Mod Security Module is Loaded|Enabled Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Mod Security Module is Loaded|Enabled Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice') ) {
		$text = __('The GDPR Compliance Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The GDPR Compliance Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_root_version_check_notice') ) {
		$text = __('The Root htaccess File Version Check Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Root htaccess File Version Check Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice') ) {
		$text = __('The BPS wp-config.php file WP Automatic Update constants detected Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS wp-config.php file WP Automatic Update constants detected Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	echo '<div class="bps-message-button" style="width:90px;margin-bottom:9px;"><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ).'">'.__('Refresh Status', 'bulletproof-security').'</a></div>';
	echo '</p></div>';
	}

?>