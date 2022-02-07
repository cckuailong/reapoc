<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.'); ?>
<div class="updraft_backup_content">
	<div id="updraft-insert-admin-warning"></div>
	<noscript>
		<div>
			<?php _e('JavaScript warning', 'updraftplus').': ';?><span style="color:red"><?php _e('This admin interface uses JavaScript heavily. You either need to activate it within your browser, or to use a JavaScript-capable browser.', 'updraftplus');?></span>
		</div>
	</noscript>
	
	<?php
	if ($backup_disabled) {
		$this->show_admin_warning(
			htmlspecialchars(__("The 'Backup Now' button is disabled as your backup directory is not writable (go to the 'Settings' tab and find the relevant option).", 'updraftplus')),
			'error'
		);
	}
	?>
	
	<h3 class="updraft_next_scheduled_backups_heading"><?php _e('Next scheduled backups', 'updraftplus');?>:</h3>
	<div class="updraft_next_scheduled_backups_wrapper postbox">
		<div class="schedule">
			<div class="updraft_next_scheduled_entity">
				<div class="updraft_next_scheduled_heading">
					<strong><?php echo __('Files', 'updraftplus').':';?></strong>
				</div>
				<div id="updraft-next-files-backup-inner">
					<?php
					$updraftplus_admin->next_scheduled_files_backups_output();
					?>
				</div>
			</div>
			<div class="updraft_next_scheduled_entity">
				<div class="updraft_next_scheduled_heading">
					<strong><?php echo __('Database', 'updraftplus').':';?></strong>
				</div>
				<div id="updraft-next-database-backup-inner">
					<?php
						$updraftplus_admin->next_scheduled_database_backups_output();
					?>
				</div>
			</div>
			<div class="updraft_time_now_wrapper">
				<?php
				// wp_date() is WP 5.3+, but performs translation into the site locale
				$current_time = function_exists('wp_date') ? wp_date('D, F j, Y H:i') : get_date_from_gmt(gmdate('Y-m-d H:i:s'), 'D, F j, Y H:i');
				?>
				<span class="updraft_time_now_label"><?php echo __('Time now', 'updraftplus').': ';?></span>
				<span class="updraft_time_now"><?php echo $current_time;?></span>
			</div>
		</div>
		<div class="updraft_backup_btn_wrapper">
			<button id="updraft-backupnow-button" type="button" <?php echo $backup_disabled; ?> class="button button-primary button-large button-hero" <?php if ($backup_disabled) echo 'title="'.esc_attr(__('This button is disabled because your backup directory is not writable (see the settings).', 'updraftplus')).'" ';?> onclick="updraft_backup_dialog_open(); return false;"><?php echo str_ireplace('Back Up', 'Backup', __('Backup Now', 'updraftplus'));?></button>
			<?php
				if (!$backup_disabled) {
					$link = '<p><a href="#" id="updraftplus_incremental_backup_link" onclick="updraft_backup_dialog_open(\'incremental\'); return false;" data-incremental="0">'.__('Add changed files (incremental backup) ...', ' updraftplus ') . '</a></p>';
					echo apply_filters('updraftplus_incremental_backup_link', $link);
				}
			?>
		</div>
		<div id="updraft_activejobs_table">
			<?php
			$active_jobs = $this->print_active_jobs();
			?>
			<div id="updraft_activejobsrow">
				<?php echo $active_jobs;?>
			</div>
		</div>
	</div>

	
	<div id="updraft_lastlogmessagerow">
		<h3><?php _e('Last log message', 'updraftplus');?>:</h3>
		<?php $this->most_recently_modified_log_link(); ?>
		<div class="postbox">
			<span id="updraft_lastlogcontainer"><?php echo htmlspecialchars(UpdraftPlus_Options::get_updraft_lastmessage()); ?></span>			
		</div>
	</div>
	
	<div id="updraft-iframe-modal">
		<div id="updraft-iframe-modal-innards">
		</div>
	</div>
	
	<div id="updraft-authenticate-modal" style="display:none;" title="<?php esc_attr_e('Remote storage authentication', 'updraftplus');?>">
		<p><?php _e('You have selected a remote storage option which has an authorization step to complete:', 'updraftplus'); ?></p>
		<div id="updraft-authenticate-modal-innards">
		</div>
	</div>
	
	<div id="updraft-backupnow-modal" title="UpdraftPlus - <?php _e('Perform a backup', 'updraftplus'); ?>">
		<?php echo $updraftplus_admin->backupnow_modal_contents(); ?>
	</div>
	
	<?php if (is_multisite() && !file_exists(UPDRAFTPLUS_DIR.'/addons/multisite.php')) { ?>
		<h2>UpdraftPlus <?php _e('Multisite', 'updraftplus');?></h2>
		<table>
			<tr>
				<td>
					<p class="multisite-advert-width"><?php echo __('Do you need WordPress Multisite support?', 'updraftplus').' <a href="'.$updraftplus->get_url('premium').'" target="_blank">'. __('Please check out UpdraftPlus Premium.', 'updraftplus');?></a>.</p>
				</td>
			</tr>
		</table>
	<?php } ?>
</div>
