<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// Form Processing: Rename|Create|Reset DB Backup Folder Location and DB Backup File Download Link|URL
function bpsPro_reset_db_backup_folder() {
	
	if ( isset( $_POST['Submit-DBB-Reset'] ) && current_user_can('manage_options') ) {
		check_admin_referer('bulletproof_security_db_backup_reset');

		?>
		
		<style>
		<!--
		.ui-accordion.bps-accordion .ui-accordion-content {overflow:hidden;}
		-->
		</style>
		
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($){
				$( "#bps-accordion-1" ).accordion({
				collapsible: true,
				active: 2,
				autoHeight: true,
				clearStyle: true,
				heightStyle: "content"
				});
			});
			/* ]]> */
			</script>
		
		<?php

		$source = WP_CONTENT_DIR . '/bps-backup';

		if ( is_dir($source) ) {
		
			$options = get_option('bulletproof_security_options_db_backup');
			$new_db_backup_folder = $_POST['DBBFolderReset'];
	
			if ( $options['bps_db_backup_folder'] != '' ) {
		
				$db_backup_folder_name = preg_match( '/[a-zA-Z0-9-_]{1,}$/', $options['bps_db_backup_folder'], $matches );
				
				if ( ! rename( WP_CONTENT_DIR . '/bps-backup/' . $matches[0], WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder ) ) {
					
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<strong><font color="#fb0101">'.__('Error: Unable to rename the DB Backup folder.', 'bulletproof-security').'</font><br>'.__('Did you enter a valid DB Backup folder name? Valid folder naming characters are: Letters A to Z upper or lowercase. Numbers 0 to 9. A dash "-" or an underscore "_". Did you manually change the old DB Backup folder name using FTP?', 'bulletproof-security').'</strong>';
					echo $text;
					echo '</p></div>';
				
				} else {
		
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<font color="green"><strong>'.__('The DB Backup folder name has been renamed to: ', 'bulletproof-security').$new_db_backup_folder.'</strong></font><br>';
					echo $text;
					echo '</p></div>';
					
					$dbb_options = 'bulletproof_security_options_db_backup';
					$bps_db_backup_folder = addslashes( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder );
					$bps_db_backup_download_link = content_url( '/bps-backup/' ) . $new_db_backup_folder . '/';
		
					$DBB_Options = array(
					'bps_db_backup' 						=> $options['bps_db_backup'], 
					'bps_db_backup_description' 			=> $options['bps_db_backup_description'], 
					'bps_db_backup_folder' 					=> $bps_db_backup_folder, 
					'bps_db_backup_download_link' 			=> $bps_db_backup_download_link, 
					'bps_db_backup_job_type' 				=> $options['bps_db_backup_job_type'], 
					'bps_db_backup_frequency' 				=> $options['bps_db_backup_frequency'], 		 
					'bps_db_backup_start_time_hour' 		=> $options['bps_db_backup_start_time_hour'], 
					'bps_db_backup_start_time_weekday' 		=> $options['bps_db_backup_start_time_weekday'], 
					'bps_db_backup_start_time_month_date' 	=> $options['bps_db_backup_start_time_month_date'], 
					'bps_db_backup_email_zip' 				=> $options['bps_db_backup_email_zip'], 
					'bps_db_backup_delete' 					=> $options['bps_db_backup_delete'], 
					'bps_db_backup_status_display' 			=> $options['bps_db_backup_status_display'] 
					);	
	
					if ( ! get_option( $dbb_options ) ) {	
		
						foreach( $DBB_Options as $key => $value ) {
							update_option('bulletproof_security_options_db_backup', $DBB_Options);
						}
			
					} else {

						foreach( $DBB_Options as $key => $value ) {
							update_option('bulletproof_security_options_db_backup', $DBB_Options);
						}	
					}
				}
			
			} else {

				if ( ! @mkdir( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder, 0755, true ) ) {
				
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<strong><font color="#fb0101">'.__('Error: Unable to create the DB Backup folder.', 'bulletproof-security').'</font><br>'.__('Go to the BPS System Info page File|Folder Permissions & UID checks table. Check the /wp-content/bps-backup/ folder permissions. The folder permissions should be 755 or 705. The Script Owner ID and File Owner ID should be the same matching ID. All of your other WordPress folders should also have the same matching ID\'s.', 'bulletproof-security').'</strong>';
					echo $text;
					echo '</p></div>';
				
				} else {
				
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<font color="green"><strong>'.__('The DB Backup folder: ', 'bulletproof-security').$new_db_backup_folder.__(' was created successfully.', 'bulletproof-security').'</strong></font>';
					echo $text;
					echo '</p></div>';
				
					@chmod( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder . '/', 0755 );
					@mkdir( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder . '/db-diff', 0755, true );
					@chmod( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder . '/db-diff/', 0755 );

					$dbb_options = 'bulletproof_security_options_db_backup';
					$bps_db_backup_folder = addslashes( WP_CONTENT_DIR . '/bps-backup/' . $new_db_backup_folder );
					$bps_db_backup_download_link = content_url( '/bps-backup/' ) . $new_db_backup_folder . '/';
		
					$DBB_Options = array(
					'bps_db_backup' 						=> $options['bps_db_backup'], 
					'bps_db_backup_description' 			=> $options['bps_db_backup_description'], 
					'bps_db_backup_folder' 					=> $bps_db_backup_folder, 
					'bps_db_backup_download_link' 			=> $bps_db_backup_download_link, 
					'bps_db_backup_job_type' 				=> $options['bps_db_backup_job_type'], 
					'bps_db_backup_frequency' 				=> $options['bps_db_backup_frequency'], 		 
					'bps_db_backup_start_time_hour' 		=> $options['bps_db_backup_start_time_hour'], 
					'bps_db_backup_start_time_weekday' 		=> $options['bps_db_backup_start_time_weekday'], 
					'bps_db_backup_start_time_month_date' 	=> $options['bps_db_backup_start_time_month_date'], 
					'bps_db_backup_email_zip' 				=> $options['bps_db_backup_email_zip'], 
					'bps_db_backup_delete' 					=> $options['bps_db_backup_delete'], 
					'bps_db_backup_status_display' 			=> $options['bps_db_backup_status_display'] 
					);
	
					if ( ! get_option( $dbb_options ) ) {	
		
						foreach( $DBB_Options as $key => $value ) {
							update_option('bulletproof_security_options_db_backup', $DBB_Options);
						}
			
					} else {

						foreach( $DBB_Options as $key => $value ) {
							update_option('bulletproof_security_options_db_backup', $DBB_Options);
						}	
					}			
				}
			}
		}
	}
}

?>