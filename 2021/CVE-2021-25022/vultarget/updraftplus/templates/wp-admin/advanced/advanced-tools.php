<?php
	if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');
?>
<div class="expertmode">
	<p>
		<em><?php _e('Unless you have a problem, you can completely ignore everything here.', 'updraftplus');?></em>
	</p>
	<div class="advanced_settings_container">
		<div class="advanced_settings_menu">
			<?php
				$updraftplus_admin->include_template('/wp-admin/advanced/tools-menu.php');
			?>
		</div>
		<div class="advanced_settings_content">
			<?php
				if (empty($options)) $options = array();
				$updraftplus_admin->include_template('/wp-admin/advanced/site-info.php', false, array('options' => $options));
				$updraftplus_admin->include_template('/wp-admin/advanced/lock-admin.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/updraftcentral.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/search-replace.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/total-size.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/export-settings.php');
				$updraftplus_admin->include_template('/wp-admin/advanced/wipe-settings.php');
			?>
		</div>
	</div>
</div>
