<div class="umsAdminFooterShell">
	<div class="umsAdminFooterCell">
		<?php echo UMS_WP_PLUGIN_NAME?>
		<?php _e('Version', UMS_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org/plugins/<?php echo UMS_WP_NAME; ?>/changelog/"><?php echo UMS_VERSION_PLUGIN?></a>
	</div>
	<div class="umsAdminFooterCell">|</div>
	<?php  if(!frameUms::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="umsAdminFooterCell">
		<?php _e('Go', UMS_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo frameUms::_()->getModule('supsystic_promo')->getMainLink();?>"><?php _e('PRO', UMS_LANG_CODE)?></a>
	</div>
	<div class="umsAdminFooterCell">|</div>
	<?php } ?>
	<div class="umsAdminFooterCell">
		<a target="_blank" href="http://wordpress.org/support/plugin/<?php echo UMS_WP_NAME; ?>"><?php _e('Support', UMS_LANG_CODE)?></a>
	</div>
	<div class="umsAdminFooterCell">|</div>
	<div class="umsAdminFooterCell">
		<?php _e('Add your', UMS_LANG_CODE)?> <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/<?php echo UMS_WP_NAME; ?>?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>