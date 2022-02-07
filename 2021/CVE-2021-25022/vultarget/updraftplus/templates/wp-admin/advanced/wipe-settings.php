<?php
	if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');
?>
<div class="advanced_tools wipe_settings">
	<h3><?php _e('Wipe settings', 'updraftplus');?></h3>
	<p><?php echo __('This button will delete all UpdraftPlus settings and progress information for in-progress backups (but not any of your existing backups from your cloud storage).', 'updraftplus').' '.__('You will then need to enter all your settings again. You can also do this before deactivating/deinstalling UpdraftPlus if you wish.', 'updraftplus');?></p>
	<form method="post" action="<?php echo esc_url(add_query_arg(array('error' => false, 'updraft_restore_success' => false, 'action' => false, 'page' => 'updraftplus'))); ?>">
		<input type="hidden" name="action" value="updraft_wipesettings" />
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('updraftplus-wipe-setting-nonce');?>">
		<input type="submit" class="button-primary updraft_wipe_settings" value="<?php _e('Wipe settings', 'updraftplus'); ?>" onclick="var updraftplus_wipe_settings = confirm('<?php echo esc_js(__('This will delete all your UpdraftPlus settings - are you sure you want to do this?', 'updraftplus'));?>'); if (updraftplus_wipe_settings) { updraft_settings_form_changed = false; }; return updraftplus_wipe_settings;">
	</form>
	<h3><?php _e('UpdraftPlus Tour', 'updraftplus');?></h3>
	<p><?php _e('Press this button to take a tour of the plugin.', 'updraftplus'); ?></p>
	<a href="<?php echo UpdraftPlus_Options::admin_page_url(); ?>?page=updraftplus&updraftplus_tour=1" class="js-updraftplus-tour button button-primary"><?php _e("Reset tour", "updraftplus"); ?></a>
</div>