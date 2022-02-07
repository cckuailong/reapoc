<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<div id="updraft_migrate_tab_main">
	<?php $updraftplus_admin->include_template('wp-admin/settings/temporary-clone.php'); ?>

	<h2><?php _e('Migrate (create a copy of a site on hosting you control)', 'updraftplus'); ?></h2>
	<div id="updraft_migrate" class="postbox">
		<p>
			<strong><?php _e('Do you want to migrate or clone/duplicate a site?', 'updraftplus');?></strong>
		</p>

		<p>
			<?php _e('Then, try out our "Migrator" add-on which can perform a direct site-to-site migration. After using it once, you\'ll have saved the purchase price compared to the time needed to copy a site by hand.', 'updraftplus'); ?>
		</p>

		<p>
			<a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/landing/migrator/");?>" target="_blank"><?php _e('More information here.', 'updraftplus'); ?></a>
		</p>
	</div>

</div>
