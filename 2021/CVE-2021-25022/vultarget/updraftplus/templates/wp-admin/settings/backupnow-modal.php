<?php
	if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

	global $updraftplus;
	
	$premium_link = apply_filters('updraftplus_com_link', 'https://updraftplus.com/landing/updraftplus-premium');
	
	$free_ret = '<em>'.__('All WordPress tables will be backed up.', 'updraftplus').' <a href="'.$premium_link.'">'. __('With UpdraftPlus Premium, you can choose to backup non-WordPress tables, backup only specified tables, and backup other databases too.', 'updraftplus').'</a></em>'."\n";
?>

<p>
	<h3 class="new-backups-only"><?php _e('Take a new backup', 'updraftplus');?></h3>
	<h3 class="incremental-backups-only"><?php _e('Take an incremental backup', 'updraftplus');?></h3>
</p>

<div class="incremental-free-only">
	<p><?php echo sprintf(__('Incremental backups are a feature of %s (upgrade by following this link).', 'updraftplus'), '<a href="'.$updraftplus->get_url('premium').'" target="_blank">UpdraftPlus Premium').'</a>'; ?>
	</a>
	<br>
	<a href="https://updraftplus.com/tell-me-more-about-incremental-backups/" target="_blank"><?php _e('Find out more about incremental backups here.', 'updraftplus'); ?></a></p>
</div>

<p id="backupnow_database_container" class="new-backups-only">

	<input type="checkbox" id="backupnow_includedb" checked="checked">
	<label for="backupnow_includedb"><?php _e('Include your database in the backup', 'updraftplus'); ?></label>

	(<a href="#" id="backupnow_database_showmoreoptions">...</a>)<br>

	<div id="backupnow_database_moreoptions" class="updraft-hidden" style="display:none;">

		<?php echo apply_filters('updraft_backupnow_database_showmoreoptions', $free_ret, '');?>

	</div>

</p>
	
<p>
	<input type="checkbox" class="new-backups-only" id="backupnow_includefiles" checked="checked">
	<label id="backupnow_includefiles_label" for="backupnow_includefiles"><?php _e("Include your files in the backup", 'updraftplus'); ?></label>
	
	(<a href="<?php echo $updraftplus->get_current_clean_url(); ?>" id="backupnow_includefiles_showmoreoptions">...</a>)<br>

	<div id="backupnow_includefiles_moreoptions" class="updraft-hidden" style="display:none;">
		<em><?php _e('Your saved settings also affect what is backed up - e.g. files excluded.', 'updraftplus'); ?></em><br>
		
		<?php echo $updraftplus_admin->files_selector_widgetry('backupnow_files_', false, 'sometimes'); ?>
	</div>
	
</p>

<div class="backupnow_modal_afterfileoptions">
	<?php echo apply_filters('updraft_backupnow_modal_afterfileoptions', '', ''); ?>
</div>

<span id="backupnow_remote_container"><?php echo $this->backup_now_remote_message(); ?></span>

<p id="backupnow_always_keep_container" class="new-backups-only">
	<input type="checkbox" id="always_keep">
	<label for="always_keep"><?php _e('Only allow this backup to be deleted manually (i.e. keep it even if retention limits are hit).', 'updraftplus'); ?></label>
</p>

<div class="backupnow_modal_afteroptions">
	<?php echo apply_filters('updraft_backupnow_modal_afteroptions', '', ''); ?>
</div>
<p class="incremental-backups-only">
	<a href="https://updraftplus.com/tell-me-more-about-incremental-backups/" target="_blank"><?php _e('Find out more about incremental backups here.', 'updraftplus'); ?></a>
</p>
