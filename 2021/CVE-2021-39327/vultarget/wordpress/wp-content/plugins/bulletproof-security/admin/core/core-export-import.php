<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// Zip Custom Code Master Text file: cc-master.txt - If ZipArchive Class is not available use PclZip
function bps_Zip_CC_Master_File() {
	// Use ZipArchive
	if ( class_exists('ZipArchive') ) {

		$zip = new ZipArchive();
		$filename = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.zip';
	
		if ( $zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE ) {
    		exit("Error: Cannot Open $filename\n");
		}

		$zip->addFile(WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.txt', "cc-master.txt");
		$zip->close();

	return true;

	} else {

		// Use PclZip
		define( 'PCLZIP_TEMPORARY_DIR', WP_PLUGIN_DIR . '/bulletproof-security/admin/core/' );
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
		if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
			$previous_encoding = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}
  		
		$archive = new PclZip(WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.zip');
  		$v_list = $archive->create(WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.txt', PCLZIP_OPT_REMOVE_PATH, WP_PLUGIN_DIR . '/bulletproof-security/admin/core/');
  	
	return true;

		if ( $v_list == 0) {
			die("Error : ".$archive->errorInfo(true) );
		return false;	
		}
	}
}

// Custom Code Export Form Processing: create the cc-master.txt and zip it for download
// NOTE: The Network|Multisite option: bps_customcode_wp_rewrite_end is intentionally not included.
function bpsPro_CC_Export() {
global $bps_topDiv, $bps_bottomDiv;
	
	if ( isset( $_POST['Submit-CC-Export'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_cc_export' );
	  
	$CC_options = get_option('bulletproof_security_options_customcode');
	$CC_wpadmin_options = get_option('bulletproof_security_options_customcode_WPA');
	$CC_Master = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.txt';

	ksort($CC_options);
	ksort($CC_wpadmin_options);

	$cc_db_option = array( "bps_customcode_admin_includes", "bps_customcode_bpsqse", "bps_customcode_deny_dot_folders", "bps_customcode_deny_files", "bps_customcode_directory_index", "bps_customcode_error_logging", "bps_customcode_one", "bps_customcode_request_methods", "bps_customcode_server_protocol", "bps_customcode_server_signature", "bps_customcode_three", "bps_customcode_timthumb_misc", "bps_customcode_two", "bps_customcode_wp_rewrite_start" ); // search

	$cc_text_title = array( "CUSTOM CODE WP-ADMIN/INCLUDES", "CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS", "CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS", "CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES", "CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX", "CUSTOM CODE ERROR LOGGING AND TRACKING", "CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE", "CUSTOM CODE REQUEST METHODS FILTERED", "CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION", "CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE", "CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE", "CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE", "CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES", "CUSTOM CODE WP REWRITE LOOP START" ); // replace	

	$cc_wpadmin_db_option = array( "bps_customcode_bpsqse_wpa", "bps_customcode_deny_files_wpa", "bps_customcode_one_wpa", "bps_customcode_two_wpa" ); // search

	$cc_wpadmin_text_title = array( "CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS", "CUSTOM CODE WPADMIN DENY BROWSER ACCESS TO FILES", "CUSTOM CODE WPADMIN TOP", "CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES" ); // replace	

		$handle = fopen( $CC_Master, 'a' );
	
		if ( $handle )

		fwrite( $handle, "################### cc-master.txt file manual editing help info ###################\r\n" );
		fwrite( $handle, "# Do NOT edit/change or delete any of the BEGIN and END placeholder lines of code.\r\n" );
		fwrite( $handle, "# Do NOT change the order of the BEGIN and END lines of code.                  	\r\n" );
		fwrite( $handle, "# You can add/edit/change any code in-between the BEGIN and END lines of code. 	\r\n" );		
		fwrite( $handle, "# After editing this file you will need to create a new cc-master.zip file. 		\r\n" );
		fwrite( $handle, "# The zip file MUST be named cc-master.zip in order to Import it to Custom Code.  \r\n" );
		fwrite( $handle, "###################################################################################\r\n\r\n" );
		
		fwrite( $handle, "##################### Root htaccess File Custom Code #####################\r\n" );

	foreach ( $CC_options as $key => $value ) {
		
		$cc_master_text_title = str_replace( $cc_db_option, $cc_text_title, $key );

		fwrite( $handle, "\r\n" . "##### BEGIN " . $cc_master_text_title . " #####\r\n" );
		fwrite( $handle, htmlspecialchars_decode( $value, ENT_QUOTES ) . "\r\n" );
		fwrite( $handle, "##### END " . $cc_master_text_title . " #####\r\n" );	
	}

		fwrite( $handle, "\r\n\r\n" . "##################### wp-admin htaccess File Custom Code #####################\r\n" );
	
	foreach ( $CC_wpadmin_options as $key => $value ) {
		
		$cc_master_wpadmin_text_title = str_replace( $cc_wpadmin_db_option, $cc_wpadmin_text_title, $key );

		fwrite( $handle, "\r\n" . "##### BEGIN " . $cc_master_wpadmin_text_title . " #####\r\n" );
		fwrite( $handle, htmlspecialchars_decode( $value, ENT_QUOTES ) . "\r\n" );
		fwrite( $handle, "##### END " . $cc_master_wpadmin_text_title . " #####\r\n" );
	}

    	fclose($handle);

		if ( file_exists($CC_Master) ) {
		
			if ( bps_Zip_CC_Master_File() == true ) {
			
				unlink($CC_Master);
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Custom Code was exported successfully. Click the Download Zip Export button to download the Custom Code cc-master.zip file.', 'bulletproof-security').'<br>'.__('If you see a 403 error and/or are unable to download the zip file then click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'" target="_blank">'.__('Setup Wizard Options', 'bulletproof-security').'</a>'.__(' and select the Zip File Download Fix On setting for the Zile File Download Fix option. You should now be able to download the cc-master.zip file.', 'bulletproof-security').'</strong></font><br><div style="width:140px;font-size:1em;text-align:center;margin:10px 0px 0px 0px;padding:4px 6px 4px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.plugins_url( '/bulletproof-security/admin/core/cc-master.zip' ).'" style="font-size:1em;font-weight:bold;text-decoration:none;">'.__('Download Zip Export', 'bulletproof-security').'</a></div>';
				echo $text;
				echo $bps_bottomDiv;
			}		
		}
	}
}

// Custom Code Import|Upload Zip Install|Extract Form: will only accept filename = cc-master.zip
function bpsPro_CC_Import() {
global $bps_topDiv, $bps_bottomDiv;	
	
	if ( isset( $_POST['Submit-CC-Import'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_cc_import' );
	
		$bpsZipFilename = 'cc-master.zip';
		$bps_tmp_file = $_FILES['bps_cc_import']['tmp_name'];
		$zip_folder_path = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/';
		$bps_uploaded_zip = str_replace( '//', '/', $zip_folder_path) . $_FILES['bps_cc_import']['name'];
		$bpsZipzUploadFail = $_FILES['bps_cc_import']['name'];
		$CC_Master = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.txt';
		
		echo $bps_topDiv;

		if ( ! empty($_FILES) ) {
		if ( $_FILES['bps_cc_import']['name'] == $bpsZipFilename ) {
		
			if ( move_uploaded_file($bps_tmp_file, $bps_uploaded_zip) ) {

				$text = '<strong><font color="green">'.__('Zip File Upload Successful.', 'bulletproof-security').'</font></strong><br>';
				echo $text;
				
				if ( class_exists('ZipArchive') ) {	

					$bpsZip = new ZipArchive;
	
					if ( $bpsZip->open( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.zip' ) === TRUE ) {
						$bpsZip->extractTo( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/' );
    					$bpsZip->close();
    	
						$text = '<strong><font color="green">'.__('Zip File Exraction Successful. Method: ZipArchive class.', 'bulletproof-security').'</font></strong><br>';
						echo $text;

						// Update the BPS CC DB options from the extracted cc-master.txt file and delete it.
						if ( bpsPro_CC_DB_Update() == true ) {

							$text = '<strong><font color="green">'.__('Root Custom Code Import Successful.', 'bulletproof-security').'<br>'.__('wp-admin Custom Code Import Successful.', 'bulletproof-security').'</font><br><br>'.__('Either run the Wizards again or do these steps below to add/create your Imported Custom Code in your htaccess files.', 'bulletproof-security').'<br>'.__('1. Go to the Security Modes page.', 'bulletproof-security').'<br>'.__('2. Click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'<br>'.__('3. Click the wp-admin Folder BulletProof Mode Activate button.', 'bulletproof-security').'</strong>';
							echo $text;
						
							unlink($CC_Master);
						}

					} else {
	
						$text = '<strong><font color="#fb0101">'.__('ERROR: Zip File Extraction Failed. Method: ZipArchive class.', 'bulletproof-security').'</font></strong>';
						echo $text;
								
					}	
		
				} else { // Use PclZip if ZipArchive class is not installed
		
					// NOTE: last modified date of files is not changed with PclZip
					define( 'PCLZIP_TEMPORARY_DIR', WP_PLUGIN_DIR . '/bulletproof-security/admin/core/' );
					require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
					if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
						$previous_encoding = mb_internal_encoding();
						mb_internal_encoding( 'ISO-8859-1' );
					}	

					$archive = new PclZip( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.zip' );
  		
					if ( $archive->extract( PCLZIP_OPT_PATH, WP_PLUGIN_DIR . '/bulletproof-security/admin/core', PCLZIP_OPT_REMOVE_PATH, WP_PLUGIN_DIR . '/bulletproof-security/admin/core' ) ) {
					
						$text = '<strong><font color="green">'.__('Zip File Extraction Successful. Method: PclZip.', 'bulletproof-security').'</font></strong><br>';
						echo $text;
				
						// Update the BPS CC DB options from the extracted cc-master.txt file and delete it.
						if ( bpsPro_CC_DB_Update() == true ) {
					
							$text = '<strong><font color="green">'.__('Root Custom Code Import Successful.', 'bulletproof-security').'<br>'.__('wp-admin Custom Code Import Successful.', 'bulletproof-security').'</font><br><br>'.__('Either run the Wizards again or do these steps below to add/create your Imported Custom Code in your htaccess files.', 'bulletproof-security').'<br>'.__('1. Go to the Security Modes page.', 'bulletproof-security').'<br>'.__('2. Click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'<br>'.__('3. Click the wp-admin Folder BulletProof Mode Activate button.', 'bulletproof-security').'</strong>';
							echo $text;
							
							unlink($CC_Master);
						}

					} else {
					
						$text = '<strong><font color="#fb0101">'.__('ERROR: Zip File Extraction Failed. Method: PclZip.', 'bulletproof-security').'</font></strong>';
						echo $text;

					}
				} // end if ( class_exists('ZipArchive') ) {		
		
			} else { // end if ( move_uploaded_file($bps_tmp_file, $bps_uploaded_zip) ) {
		
				$text = '<strong><font color="#fb0101">'.__('ERROR: Zip File Upload Failed.', 'bulletproof-security').'</font><br><font color="black">'.__('Either the cc-master.zip file has not been selected yet for Import or the file ', 'bulletproof-security').$bpsZipzUploadFail.__(' is not a valid Custom Code cc-master.zip file or file name. The BPS Custom Code Import feature only allows the cc-master.zip file to be Uploaded/Imported.', 'bulletproof-security').'</font></strong>';
				echo $text;
			}
		}
		}
		echo $bps_bottomDiv;
	}
}

// VERY IMPORTANT:  the cc-master.txt file MUST be deleted after updating the DB options or the next Export will be fubar
// NOTE: The Network|Multisite option: bps_customcode_wp_rewrite_end is intentionally not included, but is updated with the resaved DB value.
function bpsPro_CC_DB_Update() {
global $bps_topDiv, $bps_bottomDiv;		
	
	if ( current_user_can('manage_options') ) {
		
		$CC_Master = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/cc-master.txt';
		$CC_options_Root = get_option('bulletproof_security_options_customcode');
		$CC_options_wpadmin = get_option('bulletproof_security_options_customcode_WPA');

		$pattern1 = '#####\sBEGIN(.*)WP-ADMIN\/INCLUDES\s#####(.*)#####\sEND(.*)WP-ADMIN\/INCLUDES\s#####';		
		$pattern2 = '#####\sBEGIN(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####(.*)#####\sEND(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####';
		$pattern3 = '#####\sBEGIN(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####(.*)#####\sEND(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####';	
		$pattern4 = '#####\sBEGIN(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####(.*)#####\sEND(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####';	
		$pattern5 = '#####\sBEGIN(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####(.*)#####\sEND(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####';
		$pattern6 = '#####\sBEGIN(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####(.*)#####\sEND(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####';
		$pattern7 = '#####\sBEGIN(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####(.*)#####\sEND(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####';
		$pattern8 = '#####\sBEGIN(.*)REQUEST\sMETHODS\sFILTERED\s#####(.*)#####\sEND(.*)REQUEST\sMETHODS\sFILTERED\s#####';
		$pattern9 = '#####\sBEGIN(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####(.*)#####\sEND(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####';
		$pattern10 = '#####\sBEGIN(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####(.*)#####\sEND(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####';
		$pattern11 = '#####\sBEGIN(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####(.*)#####\sEND(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####';
		$pattern12 = '#####\sBEGIN(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####(.*)#####\sEND(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####';
		$pattern13 = '#####\sBEGIN(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####(.*)#####\sEND(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####';
		$pattern14 = '#####\sBEGIN(.*)WP\sREWRITE\sLOOP\sSTART\s#####(.*)#####\sEND(.*)WP\sREWRITE\sLOOP\sSTART\s#####';
		$pattern15 = '#####\sBEGIN(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####(.*)#####\sEND(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####';
		$pattern16 = '#####\sBEGIN(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####(.*)#####\sEND(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####';
		$pattern17 = '#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####(.*)#####\sEND\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####';
		$pattern18 = '#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####(.*)#####\sEND\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####';

		if ( file_exists($CC_Master) ) {
			
			$stringReplace = file_get_contents($CC_Master);

			$cc_array = preg_match_all( '/'.$pattern1.'|'.$pattern2.'|'.$pattern3.'|'.$pattern4.'|'.$pattern5.'|'.$pattern6.'|'.$pattern7.'|'.$pattern8.'|'.$pattern9.'|'.$pattern10.'|'.$pattern11.'|'.$pattern12.'|'.$pattern13.'|'.$pattern14.'|'.$pattern15.'|'.$pattern16.'|'.$pattern17.'|'.$pattern18.'/s', $stringReplace, $matches );
			
			if ( count($matches[0]) != '18' ) {
				
				echo $bps_topDiv;			
				$text = '<font color="#fb0101"><strong>'.__('Error: The cc-master.txt file MUST contain all of the BEGIN and END placeholder lines of code even if they are blank/do not have any custom code. Create a new Custom Code Export file and manually add/edit your additional custom code, BUT leave all of the BEGIN and END comment placeholder lines of code in the new cc-master.txt file that you Export.', 'bulletproof-security').'</strong></font>';
				echo $text;
				echo $bps_bottomDiv;			
				
				return;
			}
			
			foreach ( $matches[0] as $Key => $Value ) {
				
				if ( $Key == 0 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)WP-ADMIN\/INCLUDES\s#####\s*#####\sEND(.*)WP-ADMIN\/INCLUDES\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_admin_includes = $CC_options_Root['bps_customcode_admin_includes'];
					
					} else {

						$bps_customcode_admin_includes = preg_replace( '/#####\sBEGIN(.*)WP-ADMIN\/INCLUDES\s#####|#####\sEND(.*)WP-ADMIN\/INCLUDES\s#####/', "", $Value );
					}
				}

				if ( $Key == 1 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####\s*#####\sEND(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_bpsqse = $CC_options_Root['bps_customcode_bpsqse'];
					
					} else {

						$bps_customcode_bpsqse = preg_replace( '/#####\sBEGIN(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####|#####\sEND(.*)BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS\s#####/', "", $Value );
				
					}
				}	
		
				if ( $Key == 2 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####\s*#####\sEND(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####/', $Value, $matches ) ) {

						$bps_customcode_deny_dot_folders = $CC_options_Root['bps_customcode_deny_dot_folders'];
					
					} else {

						$bps_customcode_deny_dot_folders = preg_replace( '/#####\sBEGIN(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####|#####\sEND(.*)DENY\sACCESS\sTO\sPROTECTED\sSERVER(.*)FOLDERS\s#####/', "", $Value );
				
					}
				}					

				if ( $Key == 3 ) {

					if ( preg_match( '/#####\sBEGIN(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####\s*#####\sEND(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_deny_files = $CC_options_Root['bps_customcode_deny_files'];
					
					} else {

						$bps_customcode_deny_files = preg_replace( '/#####\sBEGIN(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####|#####\sEND(.*)DENY\sBROWSER\sACCESS\sTO\sTHESE\sFILES\s#####/', "", $Value );
				
					}
				}	

				if ( $Key == 4 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####\s*#####\sEND(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_directory_index = $CC_options_Root['bps_customcode_directory_index'];
					
					} else {

						$bps_customcode_directory_index = preg_replace( '/#####\sBEGIN(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####|#####\sEND(.*)DO\sNOT\sSHOW\sDIRECTORY\sLISTING\/DIRECTORY\sINDEX\s#####/', "", $Value );
					
					}
				}

				if ( $Key == 5 ) {
					
					if ( preg_match( '/#####\sBEGIN(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####\s*#####\sEND(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_error_logging = $CC_options_Root['bps_customcode_error_logging'];
					
					} else {

						$bps_customcode_error_logging = preg_replace( '/#####\sBEGIN(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####|#####\sEND(.*)ERROR\sLOGGING\sAND\sTRACKING\s#####/', "", $Value );
					}
				}	

				if ( $Key == 6 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####\s*#####\sEND(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_one = $CC_options_Root['bps_customcode_one'];
					
					} else {
					
					
						$bps_customcode_one = preg_replace( '/#####\sBEGIN(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####|#####\sEND(.*)TOP\sPHP\/PHP\.INI\sHANDLER\/CACHE\sCODE\s#####/', "", $Value );
					
					}
				}

				if ( $Key == 7 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)REQUEST\sMETHODS\sFILTERED\s#####\s*#####\sEND(.*)REQUEST\sMETHODS\sFILTERED\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_request_methods = $CC_options_Root['bps_customcode_request_methods'];
					
					} else {

						$bps_customcode_request_methods = preg_replace( '/#####\sBEGIN(.*)REQUEST\sMETHODS\sFILTERED\s#####|#####\sEND(.*)REQUEST\sMETHODS\sFILTERED\s#####/', "", $Value );
					}
				}	

				if ( $Key == 8 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####\s*#####\sEND(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_server_protocol = $CC_options_Root['bps_customcode_server_protocol'];
					
					} else {

						$bps_customcode_server_protocol = preg_replace( '/#####\sBEGIN(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####|#####\sEND(.*)BRUTE\sFORCE\sLOGIN\sPAGE\sPROTECTION\s#####/', "", $Value );
				
					}
				}

				if ( $Key == 9 ) {

					if ( preg_match( '/#####\sBEGIN(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####\s*#####\sEND(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_server_signature = $CC_options_Root['bps_customcode_server_signature'];
					
					} else {

						$bps_customcode_server_signature = preg_replace( '/#####\sBEGIN(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####|#####\sEND(.*)TURN\sOFF\sYOUR\sSERVER\sSIGNATURE\s#####/', "", $Value );
				
					}
				}

				if ( $Key == 10 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####\s*#####\sEND(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_three = $CC_options_Root['bps_customcode_three'];
					
					} else {
				
						$bps_customcode_three = preg_replace( '/#####\sBEGIN(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####|#####\sEND(.*)BOTTOM\sHOTLINKING(.*)BLOCK\sIP\/REDIRECT\sCODE\s#####/', "", $Value );
				
					}
				}	

				if ( $Key == 11 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####\s*#####\sEND(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_timthumb_misc = $CC_options_Root['bps_customcode_timthumb_misc'];
					
					} else {

						$bps_customcode_timthumb_misc = preg_replace( '/#####\sBEGIN(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####|#####\sEND(.*)TIMTHUMB\sFORBID\sRFI\s(.*)BYPASS\sRULE\s#####/', "", $Value );
					
					}
				}	
				
				if ( $Key == 12 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####\s*#####\sEND(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_two = $CC_options_Root['bps_customcode_two'];
					
					} else {
				
						$bps_customcode_two = preg_replace( '/#####\sBEGIN(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####|#####\sEND(.*)PLUGIN\/THEME\sSKIP\/BYPASS\sRULES\s#####/', "", $Value );
					}
				}

				if ( $Key == 13 ) {

					if ( preg_match( '/#####\sBEGIN(.*)WP\sREWRITE\sLOOP\sSTART\s#####\s*#####\sEND(.*)WP\sREWRITE\sLOOP\sSTART\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_wp_rewrite_start = $CC_options_Root['bps_customcode_wp_rewrite_start'];
					
					} else {

						$bps_customcode_wp_rewrite_start = preg_replace( '/#####\sBEGIN(.*)WP\sREWRITE\sLOOP\sSTART\s#####|#####\sEND(.*)WP\sREWRITE\sLOOP\sSTART\s#####/', "", $Value );
					}
				}

				if ( $Key == 14 ) {

					if ( preg_match( '/#####\sBEGIN(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####\s*#####\sEND(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_bpsqse_wpa = $CC_options_wpadmin['bps_customcode_bpsqse_wpa'];
					
					} else {

						$bps_customcode_bpsqse_wpa = preg_replace( '/#####\sBEGIN(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####|#####\sEND(.*)BPSQSE-check(.*)QUERY\sSTRING\sEXPLOITS(.*)FILTERS\s#####/', "", $Value );
				
					}
				}

				if ( $Key == 15 ) {
				
					if ( preg_match( '/#####\sBEGIN(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####\s*#####\sEND(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_deny_files_wpa = $CC_options_wpadmin['bps_customcode_deny_files_wpa'];
					
					} else {

						$bps_customcode_deny_files_wpa = preg_replace( '/#####\sBEGIN(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####|#####\sEND(.*)WPADMIN\sDENY\sBROWSER\sACCESS\sTO\sFILES\s#####/', "", $Value );
				
					}
				}	

				if ( $Key == 16 ) {
				
					if ( preg_match( '/#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####\s*#####\sEND\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_one_wpa = $CC_options_wpadmin['bps_customcode_one_wpa'];
					
					} else {

						$bps_customcode_one_wpa = preg_replace( '/#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####|#####\sEND\sCUSTOM\sCODE\sWPADMIN\sTOP\s#####/', "", $Value );
					}
				}	

				if ( $Key == 17 ) {
				
					if ( preg_match( '/#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####\s*#####\sEND\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####/', $Value, $matches ) ) {
						
						$bps_customcode_two_wpa = $CC_options_wpadmin['bps_customcode_two_wpa'];
					
					} else {

						$bps_customcode_two_wpa = preg_replace( '/#####\sBEGIN\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####|#####\sEND\sCUSTOM\sCODE\sWPADMIN\sPLUGIN\/FILE\sSKIP\sRULES\s#####/', "", $Value );
				
					}
				}	
	
				$bps_Root_CC_Options = 'bulletproof_security_options_customcode';				

				if ( ! is_multisite() ) {

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
					'bps_customcode_deny_files' 		=> $bps_customcode_deny_files, 
					'bps_customcode_three' 				=> $bps_customcode_three
					);

				} else {
					
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
					'bps_customcode_wp_rewrite_end' 	=> $CC_options_Root['bps_customcode_wp_rewrite_end'], 
					'bps_customcode_deny_files' 		=> $bps_customcode_deny_files, 
					'bps_customcode_three' 				=> $bps_customcode_three
					);					
					
				}
				
				if ( ! get_option( $bps_Root_CC_Options ) ) {	
		
					foreach( $Root_CC_Options as $key => $value ) {
						update_option('bulletproof_security_options_customcode', $Root_CC_Options);
					}

				} else {

					foreach( $Root_CC_Options as $key => $value ) {
						update_option('bulletproof_security_options_customcode', $Root_CC_Options);
					}
				}				
			
				$bps_wpadmin_CC_Options = 'bulletproof_security_options_customcode_WPA';			

				$wpadmin_CC_Options = array(
				'bps_customcode_deny_files_wpa' => $bps_customcode_deny_files_wpa, 
				'bps_customcode_one_wpa' 		=> $bps_customcode_one_wpa, 
				'bps_customcode_two_wpa' 		=> $bps_customcode_two_wpa, 
				'bps_customcode_bpsqse_wpa' 	=> $bps_customcode_bpsqse_wpa
				);
			
				if ( ! get_option( $bps_wpadmin_CC_Options ) ) {	
					
					foreach( $wpadmin_CC_Options as $key => $value ) {
						update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
					}
	
				} else {

					foreach( $wpadmin_CC_Options as $key => $value ) {
						update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
					}
				}			
			} // end foreach ( $matches[0] as $Key => $Value ) {
		} // end if ( file_exists($CC_Master) ) {
	return true;
	}
}

// Custom Code Delete Form Processing: deletes all code from all of the Root and wp-admin Custom Code text boxes.
function bpsPro_CC_Delete() {
global $bps_topDiv, $bps_bottomDiv;
	
	if ( isset( $_POST['Submit-CC-Delete'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_cc_delete' );

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

		} else {

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
	
		} else {

			foreach( $wpadmin_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
			}
		}
	
		echo $bps_topDiv;		
		$text = '<strong><font color="green">'.__('Your Root and wp-admin Custom Code has been deleted successfully.', 'bulletproof-security').'</font></strong>';
		echo $text;
		echo $bps_bottomDiv;
	}
}

?>