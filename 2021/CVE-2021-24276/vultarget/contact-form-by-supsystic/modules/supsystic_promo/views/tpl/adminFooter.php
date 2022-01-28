<div class="cfsAdminFooterShell">
	<div class="cfsAdminFooterCell">
		<?php echo CFS_WP_PLUGIN_NAME?>
		<?php _e('Version', CFS_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org/plugins/contact-form-by-supsystic/changelog/"><?php echo CFS_VERSION?></a>
	</div>
	<div class="cfsAdminFooterCell">|</div>
	<?php  if(!frameCfs::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="cfsAdminFooterCell">
		<?php _e('Go', CFS_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->getMainLink();?>"><?php _e('PRO', CFS_LANG_CODE)?></a>
	</div>
	<div class="cfsAdminFooterCell">|</div>
	<?php } ?>
	<div class="cfsAdminFooterCell">
		<a target="_blank" href="http://wordpress.org/support/plugin/contact-form-by-supsystic"><?php _e('Support', CFS_LANG_CODE)?></a>
	</div>
	<div class="cfsAdminFooterCell">|</div>
	<div class="cfsAdminFooterCell">
		Add your <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/contact-form-by-supsystic?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>