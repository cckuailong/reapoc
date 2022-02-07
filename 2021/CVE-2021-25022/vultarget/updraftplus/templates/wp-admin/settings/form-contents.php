<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

$updraft_dir = $updraftplus->backups_dir_location();
$really_is_writable = UpdraftPlus_Filesystem_Functions::really_is_writable($updraft_dir);

// $options is passed through
$default_options = array(
	'include_database_decrypter' => true,
	'include_adverts' => true,
	'include_save_button' => true
);

foreach ($default_options as $k => $v) {
	if (!isset($options[$k])) $options[$k] = $v;
}

?>
<table class="form-table backup-schedule">
	<tr>
		<th><?php _e('Files backup schedule', 'updraftplus'); ?>:</th>
		<td class="js-file-backup-schedule">
			<div>
				<select title="<?php echo __('Files backup interval', 'updraftplus'); ?>" class="updraft_interval" name="updraft_interval">
				<?php
				$intervals = $updraftplus_admin->get_intervals('files');
				$selected_interval = UpdraftPlus_Options::get_updraft_option('updraft_interval', 'manual');
				foreach ($intervals as $cronsched => $descrip) {
					echo "<option value=\"$cronsched\" ";
					if ($cronsched == $selected_interval) echo 'selected="selected"';
					echo ">".htmlspecialchars($descrip)."</option>\n";
				}
				?>
				</select> <span class="updraft_files_timings"><?php echo apply_filters('updraftplus_schedule_showfileopts', '<input type="hidden" name="updraftplus_starttime_files" value="">', $selected_interval); ?></span>
			

				<?php

					$updraft_retain = max((int) UpdraftPlus_Options::get_updraft_option('updraft_retain', 2), 1);

					$retain_files_config = __('and retain this many scheduled backups', 'updraftplus').': <input type="number" min="1" step="1" title="'.__('Retain this many scheduled file backups', 'updraftplus').'" name="updraft_retain" value="'.$updraft_retain.'" class="retain-files" />';

					echo $retain_files_config;

				?>
			</div>
			<?php
				do_action('updraftplus_incremental_cell', $selected_interval);
				do_action('updraftplus_after_filesconfig');
			?>
		</td>
	</tr>

	<?php apply_filters('updraftplus_after_file_intervals', false, $selected_interval); ?>
	<tr>
		<th>
			<?php _e('Database backup schedule', 'updraftplus'); ?>:
		</th>
		<td class="js-database-backup-schedule">
		<div>
			<select class="updraft_interval_database" title="<?php echo __('Database backup interval', 'updraftplus'); ?>" name="updraft_interval_database">
			<?php
			$intervals = $updraftplus_admin->get_intervals('db');
			$selected_interval_db = UpdraftPlus_Options::get_updraft_option('updraft_interval_database', UpdraftPlus_Options::get_updraft_option('updraft_interval'));
			foreach ($intervals as $cronsched => $descrip) {
				echo "<option value=\"$cronsched\" ";
				if ($cronsched == $selected_interval_db) echo 'selected="selected"';
				echo ">$descrip</option>\n";
			}
			?>
			</select> <span class="updraft_same_schedules_message"><?php echo apply_filters('updraftplus_schedule_sametimemsg', '');?></span><span class="updraft_db_timings"><?php echo apply_filters('updraftplus_schedule_showdbopts', '<input type="hidden" name="updraftplus_starttime_db" value="">', $selected_interval_db); ?></span>

			<?php
				$updraft_retain_db = max((int) UpdraftPlus_Options::get_updraft_option('updraft_retain_db', $updraft_retain), 1);
				$retain_dbs_config = __('and retain this many scheduled backups', 'updraftplus').': <input type="number" min="1" step="1" title="'.__('Retain this many scheduled database backups', 'updraftplus').'" name="updraft_retain_db" value="'.$updraft_retain_db.'" class="retain-files" />';

				echo $retain_dbs_config;
			?>
			</div>
			<?php do_action('updraftplus_after_dbconfig'); ?>
		</td>
	</tr>
	<tr class="backup-interval-description">
		<th></th>
		<td><div>
		<?php
			echo apply_filters('updraftplus_fixtime_ftinfo', '<p>'.__('To fix the time at which a backup should take place,', 'updraftplus').' ('.__('e.g. if your server is busy at day and you want to run overnight', 'updraftplus').'), '.__('to take incremental backups', 'updraftplus').', '.__('or to configure more complex schedules', 'updraftplus').', <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.htmlspecialchars(__('use UpdraftPlus Premium', 'updraftplus')).'</a></p>');
		?>
		</div></td>
	</tr>
</table>

<h2 class="updraft_settings_sectionheading"><?php _e('Sending Your Backup To Remote Storage', 'updraftplus');?></h2>

<?php
	$debug_mode = UpdraftPlus_Options::get_updraft_option('updraft_debug_mode') ? 'checked="checked"' : "";
	$active_service = UpdraftPlus_Options::get_updraft_option('updraft_service');
?>

<table id="remote-storage-holder" class="form-table width-900">
	<tr>
		<th><?php
			echo __('Choose your remote storage', 'updraftplus').'<br>'.apply_filters('updraftplus_after_remote_storage_heading_message', '<em>'.__('(tap on an icon to select or unselect)', 'updraftplus').'</em>');
		?>:</th>
		<td>
		<div id="remote-storage-container">
		<?php
			if (is_array($active_service)) $active_service = $updraftplus->just_one($active_service);
			
			// Change this to give a class that we can exclude
			$multi = apply_filters('updraftplus_storage_printoptions_multi', '');
			
			foreach ($updraftplus->backup_methods as $method => $description) {
				$backup_using = esc_attr(sprintf(__("Backup using %s?", 'updraftplus'), $description));
				
				echo "<input aria-label=\"$backup_using\" name=\"updraft_service[]\" class=\"updraft_servicecheckbox $method $multi\" id=\"updraft_servicecheckbox_$method\" type=\"checkbox\" value=\"$method\"";
				if ($active_service === $method || (is_array($active_service) && in_array($method, $active_service))) echo ' checked="checked"';
				echo " data-labelauty=\"".esc_attr($description)."\">";
			}
		?>
		
		<?php
			if (false === apply_filters('updraftplus_storage_printoptions', false, $active_service)) {
				echo '</div>';
				echo '<p><a href="'.$updraftplus->get_url('premium').'" target="_blank">'.htmlspecialchars(__('You can send a backup to more than one destination with Premium.', 'updraftplus')).'</a></p>';
			}
		?>
		
		</td>
	</tr>

	<tr class="updraftplusmethod none ud_nostorage" style="display:none;">
		<td></td>
		<td><em><?php echo htmlspecialchars(__('If you choose no remote storage, then the backups remain on the web-server. This is not recommended (unless you plan to manually copy them to your computer), as losing the web-server would mean losing both your website and the backups in one event.', 'updraftplus'));?></em></td>
	</tr>
</table>

<hr class="updraft_separator">

<h2 class="updraft_settings_sectionheading"><?php _e('File Options', 'updraftplus');?></h2>

<table class="form-table js-tour-settings-more width-900" >
	<tr>
		<th><?php _e('Include in files backup', 'updraftplus');?>:</th>
		<td>
			<?php echo $updraftplus_admin->files_selector_widgetry('', true, true); ?>
			<p><?php echo apply_filters('updraftplus_admin_directories_description', __('The above directories are everything, except for WordPress core itself which you can download afresh from WordPress.org.', 'updraftplus').' <a href="'.apply_filters('updraftplus_com_link', "https://updraftplus.com/shop/").'" target="_blank">'.htmlspecialchars(__('See also the Premium version from our shop.', 'updraftplus')).'</a>'); ?></p>
		</td>
	</tr>
</table>

<h2 class="updraft_settings_sectionheading"><?php _e('Database Options', 'updraftplus');?></h2>

<table class="form-table width-900">

	<tr>
		<th><?php _e('Database encryption phrase', 'updraftplus');?>:</th>

		<td>
		<?php
			echo apply_filters('updraft_database_encryption_config', '<a href="'.apply_filters('updraftplus_com_link', "https://updraftplus.com/landing/updraftplus-premium").'" target="_blank">'.__("Don't want to be spied on? UpdraftPlus Premium can encrypt your database backup.", 'updraftplus').'</a> '.__('It can also backup external databases.', 'updraftplus'));
		?>
		</td>
	</tr>
	
	<?php
		if (!empty($options['include_database_decrypter'])) {
		?>
	
		<tr class="backup-crypt-description">
			<td></td>

			<td>

			<a href="<?php echo UpdraftPlus::get_current_clean_url();?>" class="updraft_show_decryption_widget"><?php _e('You can manually decrypt an encrypted database here.', 'updraftplus');?></a>

			<div id="updraft-manualdecrypt-modal" class="updraft-hidden" style="display:none;">
				<p><h3><?php _e("Manually decrypt a database backup file", 'updraftplus'); ?></h3></p>

				<?php
				if (version_compare($updraftplus->get_wordpress_version(), '3.3', '<')) {
					echo '<em>'.sprintf(__('This feature requires %s version %s or later', 'updraftplus'), 'WordPress', '3.3').'</em>';
				} else {
				?>

				<div id="plupload-upload-ui2">
					<div id="drag-drop-area2">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php _e('Drop encrypted database files (db.gz.crypt files) here to upload them for decryption', 'updraftplus'); ?></p>
							<p><?php _ex('or', 'Uploader: Drop db.gz.crypt files here to upload them for decryption - or - Select Files', 'updraftplus'); ?></p>
							<p class="drag-drop-buttons"><input id="plupload-browse-button2" type="button" value="<?php esc_attr_e('Select Files', 'updraftplus'); ?>" class="button" /></p>
							<p style="margin-top: 18px;"><?php _e('First, enter the decryption key', 'updraftplus'); ?>: <input id="updraftplus_db_decrypt" type="text" size="12"></p>
						</div>
					</div>
					<div id="filelist2">
					</div>
				</div>

				<?php } ?>

			</div>
			
			<?php
				$plugins = get_plugins();
				$wp_optimize_file = false;

				foreach ($plugins as $key => $value) {
					if ('wp-optimize' == $value['TextDomain']) {
						$wp_optimize_file = $key;
						break;
					}
				}
				
				if (!$wp_optimize_file) {
					?><br><a href="https://wordpress.org/plugins/wp-optimize/" target="_blank"><?php _e('Recommended: optimize your database with WP-Optimize.', 'updraftplus');?></a>
					<?php
				}
			?>
			



			</td>
		</tr>
	
	<?php
		}

		$moredbs_config = apply_filters('updraft_database_moredbs_config', false);
		if (!empty($moredbs_config)) {
		?>
			<tr>
				<th><?php _e('Backup more databases', 'updraftplus');?>:</th>
				<td><?php echo $moredbs_config; ?>
				</td>
			</tr>
		<?php
		}
	?>

</table>

<h2 class="updraft_settings_sectionheading"><?php _e('Reporting', 'updraftplus');?></h2>

<table class="form-table width-900">

<?php
	$report_rows = apply_filters('updraftplus_report_form', false);
	if (is_string($report_rows)) {
		echo $report_rows;
	} else {
	?>

	<tr id="updraft_report_row_no_addon">
		<th><?php _e('Email', 'updraftplus'); ?>:</th>
		<td>
			<?php
				$updraft_email = UpdraftPlus_Options::get_updraft_option('updraft_email');
				// in case that premium users doesn't have the reporting addon, then the same email report setting's functionality will be applied to the premium version
				// since the free version allows only one service at a time, $active_service contains just a string name of particular service, in this case 'email'
				// so we need to make the checking a bit more universal by transforming it into an array of services in which we can check whether email is the only service (free onestorage) or one of the services (premium multistorage)
				$temp_services = $active_service;
				if (is_string($temp_services)) $temp_services = (array) $temp_services;
				$is_email_storage = !empty($temp_services) && in_array('email', $temp_services);
			?>
			<label for="updraft_email" class="updraft_checkbox email_report">
				<input type="checkbox" id="updraft_email" name="updraft_email" value="<?php esc_attr_e(get_bloginfo('admin_email')); ?>"<?php if ($is_email_storage || !empty($updraft_email)) echo ' checked="checked"';?> <?php if ($is_email_storage) echo 'disabled onclick="return false"'; ?>> 
				<?php
					// have to add this hidden input so that when the form is submited and if the udpraft_email checkbox is disabled, this hidden input will be passed to the server along with other active elements
					if ($is_email_storage) echo '<input type="hidden" name="updraft_email" value="'.esc_attr(get_bloginfo('admin_email')).'">';
				?>
				<div id="cb_not_email_storage_label" <?php echo ($is_email_storage) ? 'style="display: none"' : 'style="display: inline"'; ?>>
					<?php echo __("Check this box to have a basic report sent to", 'updraftplus').' <a href="'.admin_url('options-general.php').'">'.__("your site's admin address", 'updraftplus').'</a> ('.htmlspecialchars(get_bloginfo('admin_email')).")."; ?>
				</div>
				<div id="cb_email_storage_label" <?php echo (!$is_email_storage) ? 'style="display: none"' : 'style="display: inline"'; ?>>
					<?php echo __("Your email backup and a report will be sent to", 'updraftplus').' <a href="'.admin_url('options-general.php').'">'.__("your site's admin address", 'updraftplus').'</a> ('.htmlspecialchars(get_bloginfo('admin_email')).').'; ?>
				</div>
			</label>
			<?php
				if (!class_exists('UpdraftPlus_Addon_Reporting')) echo '<a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('For more reporting features, use the Premium version', 'updraftplus').'</a>';
			?>
		</td>
	</tr>

	<?php
	}
?>
</table>

<script>
<?php
	$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array_keys($updraftplus->backup_methods));
	// In PHP 5.5+, there's array_column() for this
	$method_objects = array();
	foreach ($storage_objects_and_ids as $method => $method_information) {
		$method_objects[$method] = $method_information['object'];
	}

	echo $updraftplus_admin->get_settings_js($method_objects, $really_is_writable, $updraft_dir, $active_service);
?>
</script>
<table class="form-table width-900">
	<tr>
		<td colspan="2"><h2 class="updraft_settings_sectionheading"><?php _e('Advanced / Debugging Settings', 'updraftplus'); ?></h2></td>
	</tr>

	<tr>
		<th><?php _e('Expert settings', 'updraftplus');?>:</th>
		<td><a class="enableexpertmode" href="<?php echo UpdraftPlus::get_current_clean_url();?>#enableexpertmode"><?php _e('Show expert settings', 'updraftplus');?></a> - <?php _e("open this to show some further options; don't bother with this unless you have a problem or are curious.", 'updraftplus');?> <?php do_action('updraftplus_expertsettingsdescription'); ?></td>
	</tr>
	<?php
	$delete_local = UpdraftPlus_Options::get_updraft_option('updraft_delete_local', 1);
	$split_every_mb = UpdraftPlus_Options::get_updraft_option('updraft_split_every', 400);
	if (!is_numeric($split_every_mb)) $split_every_mb = 400;
	if ($split_every_mb < UPDRAFTPLUS_SPLIT_MIN) $split_every_mb = UPDRAFTPLUS_SPLIT_MIN;
	?>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Debug mode', 'updraftplus');?>:</th>
		<td><input type="checkbox" id="updraft_debug_mode" data-updraft_settings_test="debug_mode" name="updraft_debug_mode" value="1" <?php echo $debug_mode; ?> /> <br><label for="updraft_debug_mode"><?php _e('Check this to receive more information and emails on the backup process - useful if something is going wrong.', 'updraftplus');?> <?php _e('This will also cause debugging output from all plugins to be shown upon this screen - please do not be surprised to see these.', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Split archives every:', 'updraftplus');?></th>
		<td><input type="text" name="updraft_split_every" class="updraft_split_every" value="<?php echo $split_every_mb; ?>" size="5" /> MB<br><?php echo sprintf(__('UpdraftPlus will split up backup archives when they exceed this file size. The default value is %s megabytes. Be careful to leave some margin if your web-server has a hard size limit (e.g. the 2 GB / 2048 MB limit on some 32-bit servers/file systems).', 'updraftplus'), 400).' '.__('The higher the value, the more server resources are required to create the archive.', 'updraftplus'); ?></td>
	</tr>

	<tr class="deletelocal expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Delete local backup', 'updraftplus');?>:</th>
		<td><input type="checkbox" id="updraft_delete_local" name="updraft_delete_local" value="1" <?php if ($delete_local) echo 'checked="checked"'; ?>> <br><label for="updraft_delete_local"><?php _e('Check this to delete any superfluous backup files from your server after the backup run finishes (i.e. if you uncheck, then any files despatched remotely will also remain locally, and any files being kept locally will not be subject to the retention limits).', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode backupdirrow updraft-hidden" style="display:none;">
		<th><?php _e('Backup directory', 'updraftplus');?>:</th>
		<td><input type="text" name="updraft_dir" id="updraft_dir" style="width:525px" value="<?php echo htmlspecialchars(UpdraftPlus_Manipulation_Functions::prune_updraft_dir_prefix($updraft_dir)); ?>" /></td>
	</tr>
	<tr class="expertmode backupdirrow updraft-hidden" style="display:none;">
		<td></td>
		<td>
			<span id="updraft_writable_mess">
				<?php echo $updraftplus_admin->really_writable_message($really_is_writable, $updraft_dir); ?>
			</span>
			<?php
				echo __("This is where UpdraftPlus will write the zip files it creates initially.  This directory must be writable by your web server. It is relative to your content directory (which by default is called wp-content).", 'updraftplus').' '.__("<b>Do not</b> place it inside your uploads or plugins directory, as that will cause recursion (backups of backups of backups of...).", 'updraftplus');
			?>
		</td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e("Use the server's SSL certificates", 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="useservercerts" type="checkbox" id="updraft_ssl_useservercerts" name="updraft_ssl_useservercerts" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_useservercerts"><?php _e('By default UpdraftPlus uses its own store of SSL certificates to verify the identity of remote sites (i.e. to make sure it is talking to the real Dropbox, Amazon S3, etc., and not an attacker). We keep these up to date. However, if you get an SSL error, then choosing this option (which causes UpdraftPlus to use your web server\'s collection instead) may help.', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Do not verify SSL certificates', 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="disableverify" type="checkbox" id="updraft_ssl_disableverify" name="updraft_ssl_disableverify" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_disableverify"><?php _e('Choosing this option lowers your security by stopping UpdraftPlus from verifying the identity of encrypted sites that it connects to (e.g. Dropbox, Google Drive). It means that UpdraftPlus will be using SSL only for encryption of traffic, and not for authentication.', 'updraftplus');?> <?php _e('Note that not all cloud backup methods are necessarily using SSL authentication.', 'updraftplus');?></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Disable SSL entirely where possible', 'updraftplus');?>:</th>
		<td><input data-updraft_settings_test="nossl" type="checkbox" id="updraft_ssl_nossl" name="updraft_ssl_nossl" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_nossl')) echo 'checked="checked"'; ?>> <br><label for="updraft_ssl_nossl"><?php _e('Choosing this option lowers your security by stopping UpdraftPlus from using SSL for authentication and encrypted transport at all, where possible. Note that some cloud storage providers do not allow this (e.g. Dropbox), so with those providers this setting will have no effect.', 'updraftplus');?> <a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/faqs/i-get-ssl-certificate-errors-when-backing-up-andor-restoring/");?>" target="_blank"><?php _e('See this FAQ also.', 'updraftplus');?></a></label></td>
	</tr>

	<tr class="expertmode updraft-hidden" style="display:none;">
		<th><?php _e('Automatic updates', 'updraftplus');?>:</th>
		<td><label><input type="checkbox" id="updraft_auto_updates" data-updraft_settings_test="updraft_auto_updates" name="updraft_auto_updates" value="1" <?php if ($updraftplus->is_automatic_updating_enabled()) echo 'checked="checked"'; ?>><br /><?php _e('Ask WordPress to automatically update UpdraftPlus when it finds an available update.', 'updraftplus');?></label><p><a href="https://wordpress.org/plugins/stops-core-theme-and-plugin-updates/" target="_blank"><?php _e('Read more about Easy Updates Manager', 'updraftplus'); ?></a></p></td>
	</tr>

	<?php do_action('updraftplus_configprint_expertoptions'); ?>

	<tr>
		<td></td>
		<td>
			<?php
				if (!empty($options['include_adverts'])) {
					if (!class_exists('UpdraftPlus_Notices')) include_once(UPDRAFTPLUS_DIR.'/includes/updraftplus-notices.php');
					global $updraftplus_notices;
					$updraftplus_notices->do_notice(false, 'bottom');
				}
			?>
		</td>
	</tr>
	
	<?php if (!empty($options['include_save_button'])) { ?>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="action" value="update" />
			<input type="submit" class="button-primary" id="updraftplus-settings-save" value="<?php _e('Save Changes', 'updraftplus');?>" />
		</td>
	</tr>
	<?php } ?>
</table>
