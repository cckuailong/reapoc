<?php
	global $wpdb;
	$options_helper = new Wptc_Options_Helper();
	$wptc_settings = WPTC_Base_Factory::get('Wptc_Settings');
	$wptc_settings->load_page();
?>

	<div class="wrap" id="wp-time-capsule-page">
		<h1><?php
		if ( apply_filters('is_whitelabling_override_wptc', true) ) {
			echo sprintf( __( '%s &rsaquo; Settings', 'wp-time-capsule' ), $wptc_settings->get_plugin_data( 'name' ) ); ?></h1>
		<?php } else {
			echo sprintf( __( 'Settings', 'wp-time-capsule' ) ); ?></h1>
		<?php }
		echo '<h2 class="nav-tab-wrapper wptc-nav-tab-wrapper">';
		foreach ( $wptc_settings->tabs as $id => $name ) {
			echo '<a href="#wp-time-capsule-tab-' . esc_attr( $id ) . '" class="nav-tab">' . esc_attr( $name ). '</a>';
		}
		echo '</h2>';

		$other_process_going_on = $wptc_settings->is_setting_blocking_process_going_on();
		add_thickbox();
	?>

<div id="wptc-content-id" style="display:none;"> <p> This is my hidden content! It will appear in ThickBox when the link is clicked. </p></div>
<a style="display:none" href="#TB_inline?width=600&height=550&inlineId=wptc-content-id" class="thickbox wptc-thickbox">View my inline content!</a>

	<form id="wptc-settingsform" action="#" method="post">
		<?php wp_nonce_field( 'wp-time-capsulesettings_page' ); ?>
		<input type="hidden" name="page" value="wp-time-capsulesettings" />
		<input type="hidden" name="action" value="wp-time-capsule" />
		<?php
		$is_override_by_white_label_admin = apply_filters('is_whitelabling_override_wptc', true);
		$is_allowed_by_white_label_settings = apply_filters('is_general_tab_allowed_wptc', false);

		if ( $is_override_by_white_label_admin || $is_allowed_by_white_label_settings ) { ?>
			<input type="hidden" name="anchor_wptc" value="#wp-time-capsule-tab-general" />
			<?php
		} else { ?> 
			<input type="hidden" name="anchor_wptc" value="#wp-time-capsule-tab-backup" />
			<?php
		}	?>

		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-general" style="padding-top: 20px;">

			<h3 class="title"><?php _e( 'Account Settings', 'wp-time-capsule' ); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Account', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Account</span></legend>
							<label title="Account">
								<span class="">
									<?php echo $wptc_settings->get_account_email(); ?>
								</span>
							</label>
							<a class="button-secondary change_dbox_user_tc <?php echo  ($other_process_going_on) ? 'wptc-link-disabled': '' ;?>" href="<?php echo network_admin_url() . 'admin.php?page=wp-time-capsule&logout=true'; ?>" style="margin-left: 5px; margin-top : -3px;"> Logout </a>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Active Plan', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Active Plan', 'wp-time-capsule' ); ?></span></legend>
						   <label title="Active Plan">
								<span class="" style="text-transform: capitalize;">
									<?php echo $options_helper->get_plan_interval_from_subs_info(); ?>
								</span>
							</label>
							<a class="button-secondary change_dbox_user_tc <?php echo  ($other_process_going_on) ? 'wptc-link-disabled': '' ;?>" href="<?php echo WPTC_APSERVER_URL . '/my-account.php' ?>" target="_blank" style="margin-left: 5px; margin-top : -3px;"> Change </a>
							<input type="button" class="button-secondary" id="wptc_sync_purchase" style="margin-left: 5px; margin-top : -3px;" value="Sync Purchase" />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Cloud Storage Account', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Cloud Storage Account', 'wp-time-capsule' ); ?></span></legend>
							<?php echo $wptc_settings->get_connected_cloud_info(); ?>
							<a class="button-secondary change_dbox_user_tc <?php echo  ($other_process_going_on) ? 'wptc-link-disabled': '' ;?>"  href="<?php echo network_admin_url() . 'admin.php?page=wp-time-capsule&show_connect_pane=set'; ?>" style="margin-left: 5px; margin-top : -3px;">Change</a>
								<p class="description"><?php _e( 'Please do not modify the files backed up on the <strong>' . DEFAULT_REPO_LABEL . '</strong> as it will cause problems during restore.', 'wp-time-capsule' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Cloud Settings', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<input style="width: 30%" type="text" readonly="readonly" name="bulk_cloud_settings" id="bulk_cloud_settings" value="<?php echo $wptc_settings->get_bulk_settings_str(); ?>">
							
							<p class="description"><?php _e( 'Used for adding bulk sites on the WPTC main dashboard.', 'wp-time-capsule' ); ?><a target="_blank" href="https://docs.wptimecapsule.com/article/59-how-to-add-multiple-sites-on-wptc">  help?</a></p>
						</fieldset>
					</td>
				</tr>
				<?php if (DEFAULT_REPO === 'g_drive') { ?>
				<tr>
					<th scope="row"><?php _e( 'Google Drive Refresh Token', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Google Drive Refresh Token', 'wp-time-capsule' ); ?></span></legend>
						 	<input style="width: 30%" type="text" readonly="readonly" name="gdrive_refresh_token_wptc" id="gdrive_refresh_token_wptc" value="<?php echo $wptc_settings->get_gdrive_old_token(); ?>">
						 	<a class="copy_gdrive_token_wptc" id="copy_gdrive_token_wptc" style="margin-left: 5px;cursor: pointer;" data-clipboard-target="#gdrive_refresh_token_wptc">Copy</a>
						 	<span id="gdrive_token_copy_message_wptc" style="color: #008000; margin-left: 5px; display: none">Copied :)</span>
							<p class="description">
								<?php _e( 'Copy the above token if you intend to backup more sites to the same Google Account.', 'wp-time-capsule' ); ?>
								<a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" style="text-decoration: none" target="_blank"><?php _e('Show me how.', 'wp-time-capsule'); ?></a> </p>
						</fieldset>
					</td>
				</tr>
				<?php } ?>
			</table>
			<!-- Disabled the Anonymous temporarely !-->
			<h3 style="display: none" class="title"><?php _e( 'Anonymous Report Collection', 'wp-time-capsule' ); ?></h3>
			<p style="display: none"><?php _e( 'Send anonymous usage information to improve WPTC.', 'wp-time-capsule' ); ?></p>
			<table style="display: none" class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Send Anonymous Data', 'wp-time-capsule' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Send Anonymous Data', 'wp-time-capsule' ); ?></span></legend>
							<label title="Yes">
								<input name="anonymous_datasent" type="radio" id="anonymous_datasent_yes" <?php if ($wptc_settings->get_anonymouse_report_settings() == 'yes') { echo 'checked'; } ?> value="yes">
								<span class="">
									Yes
								</span>
							</label>
							<br>
							<label title="No">
								<input name="anonymous_datasent" type="radio" id="anonymous_datasent_no" <?php if ($wptc_settings->get_anonymouse_report_settings() == 'no') {	echo 'checked'; } ?> value="no">
								<span class="">
									No
								</span>
							</label>
							<p class="description"><?php _e( 'Non-personally identifiable usage data will be sent for the sole purpose of improvement of the plugin.', 'wp-time-capsule' ); ?></p>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php //do_action('wp-time-capsule_page_settings_tab_generel'); ?>

		</div>


		<?php $more_tables_div = apply_filters('page_settings_content_wptc', '');?>
		<?php echo $more_tables_div; ?>

		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-backup">
            <p></p>

			<table class="form-table">
					<tr>
					<th scope="row"><?php _e( 'Backup Schedule & Timezone', 'wp-time-capsule' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e( 'Backup Schedule & Timezone', 'wp-time-capsule' ); ?></span></legend>
								<label >
									<select <?php echo  ($other_process_going_on) ? 'disabled': '' ;?> name="select_wptc_backup_slots" id="select_wptc_backup_slots">
										<?php echo $wptc_settings->get_backup_slots_html(); ?>
									</select>
									<select  <?php echo  ($other_process_going_on) ? 'disabled': '' ;?>  style="display: none" name="select_wptc_default_schedule" id="select_wptc_default_schedule">
										<?php echo $wptc_settings->get_schedule_times_div_wptc($type = 'backup'); ?>
									</select>
									<select id="wptc_timezone" <?php echo  ($other_process_going_on) ? 'disabled': '' ;?> name="wptc_timezone"><?php echo $wptc_settings->get_all_timezone_html(); ?></select>
								</label>
								<p <?php echo WPTC_Base_Factory::get('Wptc_App_Functions')->is_free_user_wptc() ? "style='display: block;'" : "style='display: none;'"  ?>  class="description"><?php  esc_attr_e('Sheduled backup will happen every 7 days once.', 'wp-time-capsule' ); ?></p>
								<p <?php echo ($other_process_going_on) ? "style='display: block;'" : "style='display: none;'"  ?>  class="setting_backup_progress_note_wptc disable-setting-wptc description"><?php $message =  ($other_process_going_on) ? $other_process_going_on : 'Backup';  esc_attr_e($message.' is currently running. Please wait until it finishes to change above settings.', 'wp-time-capsule' ); ?></p>
							</fieldset>
						</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Restore Window', 'wp-time-capsule' ); ?></th>
						<td>
							<div style="font-style: italic; font-weight: bolder;" id="wptc_settings_revision_limit"><?php echo apply_filters('get_current_revision_limit_wptc', '') . ' Days'; ?></div>
							<br>
							<div style="width: 390px; border: 1px solid #ddd;" id="wptc-restore-window-slider"></div>
							<br>
							<p class="description"><?php _e( 'The number of past days from which you would be able to restore your website or files. This will reflect in the Calendar view.', 'wp-time-capsule' ); ?></p>
						</td>
				</tr>
				<?php echo apply_filters('get_on_demand_backuo_option_wptc', $other_process_going_on); ?>
				<tr>
					<th scope="row"><?php _e( 'Include/Exclude Content', 'wp-time-capsule' ); ?> (<a href="https://docs.wptimecapsule.com/article/32-how-to-exclude-or-include-the-files-from-backup" target="_blank" style="text-decoration: underline;">Need Help?</a>) </th>
						<td >
						<fieldset style="float: left; margin-right: 2%">
							<button class="button button-secondary wptc_dropdown" id="toggle_exlclude_files_n_folders" style="width: 408px; outline:none; text-align: left;">
								<span class="dashicons dashicons-portfolio" style="position: relative; top: 3px; font-size: 20px"></span>
								<span style="left: 10px; position: relative;">Folders &amp; Files </span>
								<span class="dashicons dashicons-arrow-down" style="position: relative; top: 3px; left: 255px;"></span>
							</button>
							<div style="display:none" id="wptc_exc_files"></div>
						</fieldset>
						<fieldset style="float: left; margin-right: 2%">
								<button class="button button-secondary wptc_dropdown" id="toggle_wptc_db_tables" style="width: 408px; outline:none; text-align: left;">
									<span class="dashicons dashicons-menu" style="position: relative;top: 3px; font-size: 20px"></span>
									<span style="left: 10px; position: relative;">Database</span>
									<span class="dashicons dashicons-arrow-down" style="position: relative;top: 3px;left: 288px;"></span>
								</button>
								<div style="display:none" id="wptc_exc_db_files"></div>
						</fieldset>
						<fieldset style="float: left;">
								<a class="button-secondary" id="wptc_analyze_inc_exc_lists"> Analyze tables	</a>
						</fieldset>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Encrypt Database ', 'wp-time-capsule' ); ?></th>
								<td>
									<?php echo $wptc_settings->database_encryption_html();	?>
								</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Exclude Files of These Extensions', 'wp-time-capsule' ); ?></th>
								<td>
									<fieldset>
										<input class="wptc-split-column" type="text" name="user_excluded_extenstions" id="user_excluded_extenstions"  placeholder="Eg. .mp4, .mov" value="<?php echo $wptc_settings->get_user_excluded_extenstions(); ?>" />
									</fieldset>
								</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Exclude any files bigger than a specific size', 'wp-time-capsule' ); ?></th>
								<td>
									<?php echo $wptc_settings->get_user_excluded_files_more_than_size();	?>
								</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'DB Rows Per Batch', 'wp-time-capsule' ); ?></th>
							<td>
								<input name="backup_db_query_limit" type="number"  style="width: 70px;" min="10" id="backup_db_query_limit"  value="<?php echo WPTC_Base_Factory::get('Wptc_App_Functions')->get_backup_db_query_limit(); ?>">
								<!-- <p class="description">'. __( 'Reduce this number when backup process hangs at <strong>Backing up database</strong>', 'wp-time-capsule' ).' </p> -->
							</td>
						</tr>
			</table>

		</div>


		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-advanced">
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Plugin - Server Communication Status', 'wp-time-capsule' ); ?></th>
					<td>
						<?php
							$is_backup_paused = is_backup_paused_wptc();
							$status = wptc_cron_status(1);
						?>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Plugin - Server Communication Status', 'wp-time-capsule' ); ?></span></legend>
							<div id="wptc_cron_status_paused" <?php echo ($is_backup_paused) ? "style='display:block'" : "style='display:none'"; ?>>
								<div>
									<span class='cron_current_status' style="color:red">Backup stopped due to server communication error</span> -
									<a class="resume_backup_wptc" style="cursor:pointer">Resume backup</a>
								</div>
							</div>
							<div id="wptc_cron_status_div" <?php echo ($is_backup_paused) ? "style='display:none'" : "style='display:block'"; ?> >
								<div id="wptc_cron_status_failed"<?php echo ($status['status'] == 'success') ? "style='display:none'" : "style='display:block'"; ?> >
									<div>
										<span class='cron_current_status' id="wptc_cron_failed_note">Failed</span>
										<a class="test_cron_wptc button-secondary" style="margin-top: -3px;margin-left: 10px;">Test Again</a>
									</div>
								</div>
								<div id ="wptc_cron_status_passed" <?php echo ($status['status'] == 'success') ? "style='display:block'" : "style='display:none'"; ?> >
									<span class='cron_current_status'>Success</span> <a class="test_cron_wptc button-secondary" style="margin-top: -3px;margin-left: 10px;">Test Again</a>
								</div>
							</div>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="disable_sentry_lib_wptc">
							Manually decrypt a database backup file ( <a href="https://docs.wptimecapsule.com/article/36-how-to-decrypt-database-backup-file" target="_blank"> Need help? </a> )
						</label>
					</th>
					<td>
						<label>Upload Encrypted Database File : </label>
						<input id="wptc-fileupload-show" type="button" name="wptc-fileupload-show" value="Click here to show upload options" title=" ">
						<input id="wptc-fileupload" style="display: none;" type="file" accept=".crypt" name="files" value="" title=" ">
						<br>
						<span id='wptc-file-selected-span'></span>
						<br><br>
						<label>Enter Decryption Key : </label>
						<input id="wptc-fileupload-decrpty-key" type="input" name="wptc-fileupload-decrpty-key" placeholder="Enter Decryption Key Here">
						<br><br>
						<input id="wptc-start-manual-decryption" type="button" value="Decrypt" class="button-primary">
						<br><br>
						<div id="files" class="files" style="font-size: 13px;"><p></p></div>
						<span id="wptc-files-progress"></span>
						<span id="index-file-create-error-wptc"></span>
					</td>
				</tr>
			</table>

		</div>

		<div class="table ui-tabs-hide" id="wp-time-capsule-tab-information">
			<br />
			<?php

			echo '<table class="wp-list-table widefat fixed" cellspacing="0" >';
			echo '<thead><tr><th width="35%">' . __( 'Setting', 'wp-time-capsule' ) . '</th><th>' . __( 'Value', 'wp-time-capsule' ) . '</th></tr></thead>';
			echo '<tr title="&gt;=3.9.14"><td>' . __( 'WordPress version', 'wp-time-capsule' ) . '</td><td>' . esc_html( $wptc_settings->get_plugin_data( 'wp_version' ) ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'WP Time Capsule version', 'wp-time-capsule' ) . '</td><td>' . esc_html( $wptc_settings->get_plugin_data( 'Version' ) ) . '</td></tr>';

			$bit = '';
			if ( PHP_INT_SIZE === 4 ) {
				$bit = ' (32bit)';
			}
			if ( PHP_INT_SIZE === 8 ) {
				$bit = ' (64bit)';
			}

			echo '<tr title="&gt;=5.3.1"><td>' . __( 'PHP version', 'wp-time-capsule' ) . '</td><td>' . esc_html( PHP_VERSION . ' ' . $bit ) . '</td></tr>';
			echo '<tr title="&gt;=5.0.15"><td>' . __( 'MySQL version', 'wp-time-capsule' ) . '</td><td>' . esc_html( $wpdb->get_var( "SELECT VERSION() AS version" ) ) . '</td></tr>';

			if ( function_exists( 'curl_version' ) ) {
				$curlversion = curl_version();
				echo '<tr title=""><td>' . __( 'cURL version', 'wp-time-capsule' ) . '</td><td>' . esc_html( $curlversion[ 'version' ] ) . '</td></tr>';
				echo '<tr title=""><td>' . __( 'cURL SSL version', 'wp-time-capsule' ) . '</td><td>' . esc_html( $curlversion[ 'ssl_version' ] ) . '</td></tr>';
			}
			else {
				echo '<tr title=""><td>' . __( 'cURL version', 'wp-time-capsule' ) . '</td><td>' . __( 'unavailable', 'wp-time-capsule' ) . '</td></tr>';
			}

			echo '</td></tr>';
			echo '<tr title=""><td>' . __( 'Server', 'wp-time-capsule' ) . '</td><td>' . esc_html( $_SERVER[ 'SERVER_SOFTWARE' ] ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Operating System', 'wp-time-capsule' ) . '</td><td>' . esc_html( PHP_OS ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'PHP SAPI', 'wp-time-capsule' ) . '</td><td>' . esc_html( PHP_SAPI ) . '</td></tr>';

			$php_user = __( 'Function Disabled', 'wp-time-capsule' );
			if ( function_exists( 'get_current_user' ) ) {
				$php_user = get_current_user();
			}

			echo '<tr title=""><td>' . __( 'Current PHP user', 'wp-time-capsule' ) . '</td><td>' . esc_html( $php_user )  . '</td></tr>';
			echo '<tr title="&gt;=30"><td>' . __( 'Maximum execution time', 'wp-time-capsule' ) . '</td><td>' . esc_html( ini_get( 'max_execution_time' ) ) . ' ' . __( 'seconds', 'wp-time-capsule' ) . '</td></tr>';

			if ( defined( 'FS_CHMOD_DIR' ) )
				echo '<tr title="FS_CHMOD_DIR"><td>' . __( 'CHMOD Dir', 'wp-time-capsule' ) . '</td><td>' . esc_html( FS_CHMOD_DIR ) . '</td></tr>';
			else
				echo '<tr title="FS_CHMOD_DIR"><td>' . __( 'CHMOD Dir', 'wp-time-capsule' ) . '</td><td>0755</td></tr>';

			$now = localtime( time(), TRUE );
			echo '<tr title=""><td>' . __( 'Server Time', 'wp-time-capsule' ) . '</td><td>' . esc_html( $now[ 'tm_hour' ] . ':' . $now[ 'tm_min' ] ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Blog Time', 'wp-time-capsule' ) . '</td><td>' . date( 'H:i', current_time( 'timestamp' ) ) . '</td></tr>';
			echo '<tr title="WPLANG"><td>' . __( 'Blog language', 'wp-time-capsule' ) . '</td><td>' . get_bloginfo( 'language' ) . '</td></tr>';
			echo '<tr title="utf8"><td>' . __( 'MySQL Client encoding', 'wp-time-capsule' ) . '</td><td>';
			echo defined( 'DB_CHARSET' ) ? DB_CHARSET : '';
			echo '</td></tr>';
			echo '<tr title="URF-8"><td>' . __( 'Blog charset', 'wp-time-capsule' ) . '</td><td>' . get_bloginfo( 'charset' ) . '</td></tr>';
			echo '<tr title="&gt;=128M"><td>' . __( 'PHP Memory limit', 'wp-time-capsule' ) . '</td><td>' . esc_html( ini_get( 'memory_limit' ) ) . '</td></tr>';
			echo '<tr title="WP_MEMORY_LIMIT"><td>' . __( 'WP memory limit', 'wp-time-capsule' ) . '</td><td>' . esc_html( WP_MEMORY_LIMIT ) . '</td></tr>';
			echo '<tr title="WP_MAX_MEMORY_LIMIT"><td>' . __( 'WP maximum memory limit', 'wp-time-capsule' ) . '</td><td>' . esc_html( WP_MAX_MEMORY_LIMIT ) . '</td></tr>';
			echo '<tr title=""><td>' . __( 'Memory in use', 'wp-time-capsule' ) . '</td><td>' . size_format( @memory_get_usage( TRUE ), 2 ) . '</td></tr>';

			//disabled PHP functions
			$disabled = esc_html( ini_get( 'disable_functions' ) );
			if ( ! empty( $disabled ) ) {
				$disabledarry = explode( ',', $disabled );
				echo '<tr title=""><td>' . __( 'Disabled PHP Functions:', 'wp-time-capsule' ) . '</td><td>';
				echo implode( ', ', $disabledarry );
				echo '</td></tr>';
			}

			//Loaded PHP Extensions
			echo '<tr title=""><td>' . __( 'Loaded PHP Extensions:', 'wp-time-capsule' ) . '</td><td>';
			$extensions = get_loaded_extensions();
			sort( $extensions );
			echo  esc_html( implode( ', ', $extensions ) );
			echo '</td></tr>';
			echo '</table>'
			?>
		</div>
		<?php //do_action( 'wp-time-capsule_page_settings_tab_content' ); ?>

		<?php
			echo '<input type="hidden" id="tables_count_total_wptc" value="'.$wptc_settings->get_total_tables_count_wptc(true) .'">';
			echo '<input type="hidden" id="excluded_tables_count_wptc" value="'.$wptc_settings->get_total_excluded_tables_count_wptc() .'">';
		?>

		<p class="submit">
			<input type="submit" name="submit" id="wptc_save_changes" class="button-primary" value="<?php _e( 'Save Changes', 'wp-time-capsule' ); ?>" />
		</p>
	</form>
<script type="text/javascript" language="javascript">
adminUrlWptc = '<?php echo network_admin_url(); ?>';
upload_decrypt_url_wptc = '<?php echo plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/wp-tcapsule-bridge/upload/php/index.php'; ?>';
wptc_current_revision_limit = '<?php echo apply_filters('get_current_revision_limit_wptc', ''); ?>';
wptc_current_repo = '<?php echo DEFAULT_REPO; ?>';
jQuery(document).ready(function($) {
	jQuery( "#wptc-restore-window-slider" ).slider({
		value : <?php echo apply_filters('get_current_revision_limit_wptc', ''); ?>,
		min   : 3,
		max   : <?php echo apply_filters('get_eligible_revision_limit_wptc', ''); ?>,
		step  : 1,
		slide: function( event, ui ) {
			jQuery( "#wptc_settings_revision_limit" ).html(  ui.value + " Days");
		}
	});
});
</script>
