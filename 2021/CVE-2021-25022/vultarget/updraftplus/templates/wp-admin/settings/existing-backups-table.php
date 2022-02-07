<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

$accept = apply_filters('updraftplus_accept_archivename', array());
if (!is_array($accept)) $accept = array();
$image_folder = UPDRAFTPLUS_DIR.'/images/icons/';
$image_folder_url = UPDRAFTPLUS_URL.'/images/icons/';

?>
<table class="existing-backups-table wp-list-table widefat striped">
	<thead>
		<tr style="margin-bottom: 4px;">
			<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
			<th class="check-column"><label class="screen-reader-text" for="cb-select-all"><?php _e('Select All'); ?></label><input id="cb-select-all" type="checkbox"></th>
			<?php endif; ?>
			<th class="backup-date"><?php _e('Backup date', 'updraftplus');?></th>
			<th class="backup-data"><?php _e('Backup data (click to download)', 'updraftplus');?></th>
			<th class="updraft_backup_actions"><?php _e('Actions', 'updraftplus');?></th>
		</tr>		
	</thead>
	<tbody>
		<?php
		
		if (!defined('UPDRAFTCENTRAL_COMMAND') && $backup_count <= count($backup_history) - 1) {
			$backup_history = array_slice($backup_history, 0, $backup_count, true);
			$show_paging_actions = true;
		}
		
		foreach ($backup_history as $key => $backup) {

			$remote_sent = !empty($backup['service']) && ((is_array($backup['service']) && in_array('remotesend', $backup['service'])) || 'remotesend' === $backup['service']);

			// https://core.trac.wordpress.org/ticket/25331 explains why the following line is wrong
			// $pretty_date = date_i18n('Y-m-d G:i',$key);
			// Convert to blog time zone
			// $pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int)$key), 'Y-m-d G:i');
			$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int) $key), 'M d, Y G:i');

			$esc_pretty_date = esc_attr($pretty_date);
			$entities = '';

			$nonce = $backup['nonce'];

			$jobdata = isset($backup['jobdata']) ? $backup['jobdata'] : $updraftplus->jobdata_getarray($nonce);

			$rawbackup = $updraftplus_admin->raw_backup_info($backup_history, $key, $nonce, $jobdata);

			$delete_button = $updraftplus_admin->delete_button($key, $nonce, $backup);

			$upload_button = $updraftplus_admin->upload_button($key, $nonce, $backup, $jobdata);

			$date_label = $updraftplus_admin->date_label($pretty_date, $key, $backup, $jobdata, $nonce);

			$log_button = $updraftplus_admin->log_button($backup);

			// Remote backups with no log result in useless empty rows. However, not showing anything messes up the "Existing backups (14)" display, until we tweak that code to count differently
			// if ($remote_sent && !$log_button) continue;

			?>
			<tr class="updraft_existing_backups_row updraft_existing_backups_row_<?php echo $key;?>" data-key="<?php echo $key;?>" data-nonce="<?php echo $nonce;?>">
				<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
				<td class="backup-select">
					<label class="screen-reader-text"><?php _e('Select All'); ?></label><input type="checkbox">
				</td>
				<?php endif; ?>
				<td class="updraft_existingbackup_date " data-nonce="<?php echo wp_create_nonce("updraftplus-credentialtest-nonce"); ?>" data-timestamp="<?php echo $key; ?>" data-label="<?php _e('Backup date', 'updraftplus');?>">
					<div tabindex="0" class="backup_date_label">
						<?php
							echo $date_label;
							if (!empty($backup['always_keep'])) {
								$wp_version = $updraftplus->get_wordpress_version();
								if (version_compare($wp_version, '3.8.0', '<')) {
									$image_url = $image_folder_url.'lock.png';
									?>
									<img class="stored_icon" src="<?php echo esc_attr($image_url);?>" title="<?php echo esc_attr(__('Only allow this backup to be deleted manually (i.e. keep it even if retention limits are hit).', 'updraftplus'));?>">
									<?php
								} else {
									echo '<span class="dashicons dashicons-lock"  title="'.esc_attr(__('Only allow this backup to be deleted manually (i.e. keep it even if retention limits are hit).', 'updraftplus')).'"></span>';
								}
							}
							if (!isset($backup['service'])) $backup['service'] = array();
							if (!is_array($backup['service'])) $backup['service'] = array($backup['service']);
							foreach ($backup['service'] as $service) {
								if ('none' === $service || '' === $service || (is_array($service) && (empty($service) || array('none') === $service || array('') === $service))) {
									// Do nothing
								} else {
									$image_url = file_exists($image_folder.$service.'.png') ? $image_folder_url.$service.'.png' : $image_folder_url.'folder.png';

									$remote_storage = ('remotesend' === $service) ? __('remote site', 'updraftplus') : $updraftplus->backup_methods[$service];
									?>
									<img class="stored_icon" src="<?php echo esc_attr($image_url);?>" title="<?php echo esc_attr(sprintf(__('Remote storage: %s', 'updraftplus'), $remote_storage));?>">
									<?php
								}
							}
						?>
					</div>
				</td>
				
				<td data-label="<?php _e('Backup data (click to download)', 'updraftplus');?>"><?php

				if ($remote_sent) {

					_e('Backup sent to remote site - not available for download.', 'updraftplus');
					if (!empty($backup['remotesend_url'])) echo '<br>'.__('Site', 'updraftplus').': <a href="'.esc_attr($backup['remotesend_url']).'">'.htmlspecialchars($backup['remotesend_url']).'</a>';

				} else {

					if (empty($backup['meta_foreign']) || !empty($accept[$backup['meta_foreign']]['separatedb'])) {

						if (isset($backup['db'])) {
							$entities .= '/db=0/';

							// Set a flag according to whether or not $backup['db'] ends in .crypt, then pick this up in the display of the decrypt field.
							$db = is_array($backup['db']) ? $backup['db'][0] : $backup['db'];
							if (UpdraftPlus_Encryption::is_file_encrypted($db)) $entities .= '/dbcrypted=1/';

							echo $updraftplus_admin->download_db_button('db', $key, $esc_pretty_date, $backup, $accept);
						}

						// External databases
						foreach ($backup as $bkey => $binfo) {
							if ('db' == $bkey || 'db' != substr($bkey, 0, 2) || '-size' == substr($bkey, -5, 5)) continue;
							echo $updraftplus_admin->download_db_button($bkey, $key, $esc_pretty_date, $backup);
						}

					} else {
						// Foreign without separate db
						$entities = '/db=0/meta_foreign=1/';
					}

					if (!empty($backup['meta_foreign']) && !empty($accept[$backup['meta_foreign']]) && !empty($accept[$backup['meta_foreign']]['separatedb'])) {
						$entities .= '/meta_foreign=2/';
					}

					echo $updraftplus_admin->download_buttons($backup, $key, $accept, $entities, $esc_pretty_date);

				}

				?>
				</td>
				<td class="before-restore-button" data-label="<?php _e('Actions', 'updraftplus');?>">
					<?php
					echo $updraftplus_admin->restore_button($backup, $key, $pretty_date, $entities);
					echo $upload_button;
					echo $delete_button;
					if (empty($backup['meta_foreign'])) echo $log_button;
					?>
				</td>
			</tr>
		<?php } ?>	

	</tbody>
	<?php if ($show_paging_actions) : ?>
	<tfoot>
		<tr class="updraft_existing_backups_page_actions">
			<td colspan="4" style="text-align: center;">
				<a class="updraft-load-more-backups"><?php _e('Show more backups...', 'updraftplus');?></a> | <a class="updraft-load-all-backups"><?php _e('Show all backups...', 'updraftplus');?></a>
			</td>
		</tr>
	</tfoot>
	<?php endif; ?>
</table>
<?php if (!defined('UPDRAFTCENTRAL_COMMAND')) : ?>
<div id="ud_massactions">
	<strong><?php _e('Actions upon selected backups', 'updraftplus');?></strong>
	<div class="updraftplus-remove"><button title="<?php _e('Delete selected backups', 'updraftplus');?>" type="button" class="button button-remove js--delete-selected-backups"><?php _e('Delete', 'updraftplus');?></button></div>
	<div class="updraft-viewlogdiv"><button title="<?php _e('Select all backups', 'updraftplus');?>" type="button" class="button js--select-all-backups" href="#"><?php _e('Select all', 'updraftplus');?></button></div>
	<div class="updraft-viewlogdiv"><button title="<?php _e('Deselect all backups', 'updraftplus');?>" type="button" class="button js--deselect-all-backups" href="#"><?php _e('Deselect', 'updraftplus');?></button></div>
	<small class="ud_massactions-tip"><?php _e('Use ctrl / cmd + press to select several items, or ctrl / cmd + shift + press to select all in between', 'updraftplus'); ?></small>
</div>
<div id="updraft-delete-waitwarning" class="updraft-hidden" style="display:none;">
	<span class="spinner"></span> <em><?php _e('Deleting...', 'updraftplus');?> <span class="updraft-deleting-remote"><?php _e('Please allow time for the communications with the remote storage to complete.', 'updraftplus');?><span></em>
	<p id="updraft-deleted-files-total"></p>
</div>
<?php endif;
