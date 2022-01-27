<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// LSM Export|Download Login Security Table to lsm-master.csv CSV file and Zip it.
function bpsPro_LSM_Table_CSV() {
global $wpdb, $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['Submit-LSM-Export'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_lsm_export' );

		$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
		$searchAll = '';
		$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $bpspro_login_table WHERE login_time != %s", "%$searchAll%" ) );	
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
		$LSM_Master_CSV = WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-master.csv';

		$handle = fopen( $LSM_Master_CSV, 'w' );
	
		foreach ( $getLoginSecurityTable as $row ) {

			if ( $row->lockout_time == 0 ) { 
				$lockout_time = 'NA';
			} else {
				$lockout_time = date_i18n(get_option('date_format').' '.get_option('time_format'), $row->lockout_time + $gmt_offset);
			}

			$list = array( array( $row->status, $row->user_id, $row->username, $row->public_name, $row->email, $row->role, $row->human_time, $lockout_time, $row->ip_address, $row->hostname, $row->request_uri ) );

			foreach ( $list as $fields ) {
		
				fputcsv($handle, $fields);	
			}
		}
		fclose($handle);

		if ( file_exists($LSM_Master_CSV) ) {
		
			if ( bps_Zip_LSM_Table_CSV() == true ) {
			
				unlink($LSM_Master_CSV);
		
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('The Login Security Table was exported successfully. Click the Download Zip Export button to download the Login Security Table lsm-master.zip file.', 'bulletproof-security').'<br>'.__('If you see a 403 error and/or are unable to download the zip file then click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'" target="_blank">'.__('Setup Wizard Options', 'bulletproof-security').'</a>'.__(' and select the Zip File Download Fix On setting for the Zile File Download Fix option. You should now be able to download the lsm-master.zip file.', 'bulletproof-security').'</strong></font><br><div style="width:140px;font-size:1em;text-align:center;margin:10px 0px 0px 0px;padding:4px 6px 4px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.plugins_url( '/bulletproof-security/admin/login/lsm-master.zip' ).'" style="font-size:1em;font-weight:bold;text-decoration:none;">'.__('Download Zip Export', 'bulletproof-security').'</a></div>';
				echo $text;
				echo $bps_bottomDiv;
			}		
		}
	}
}

// Zip LSM Table Master CSV file: lsm-master.csv - If ZipArchive Class is not available use PclZip
function bps_Zip_LSM_Table_CSV() {
	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

		$zip = new ZipArchive();
		$filename = WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-master.zip';
	
		if ( $zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE ) {
    		exit("Error: Cannot Open $filename\n");
		}

		$zip->addFile(WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-master.csv', "lsm-master.csv");
		$zip->close();

	return true;

	} else {

		// Use PclZip
		define( 'PCLZIP_TEMPORARY_DIR', WP_PLUGIN_DIR . '/bulletproof-security/admin/login/' );
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
		if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
			$previous_encoding = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}
  		
		$archive = new PclZip(WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-master.zip');
  		$v_list = $archive->create(WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-master.csv', PCLZIP_OPT_REMOVE_PATH, WP_PLUGIN_DIR . '/bulletproof-security/admin/login/');
  	
	return true;

		if ( $v_list == 0) {
			die("Error : ".$archive->errorInfo(true) );
		return false;	
		}
	}
}

?>