<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="wrap cmp-coming-soon-maintenance">

	<h1></h1>
	<div id="icon-users" class="icon32"></div>
	<div class="settings-wrap">
		<div class="cmp-inputs-wrapper help-settings">

			<h3><?php _e('Thank you for using our CMP – Coming Soon & Maintenance plugin', 'cmp-coming-soon-maintenance');?></h3>
			<p><?php _e('If you have any questions or issue feel free to ask on Wordpress Support Forum! We are happy to help :)', 'cmp-coming-soon-maintenance');?></p>

			<p><a href="https://wordpress.org/support/plugin/cmp-coming-soon-maintenance" class="button" target="_blank"><?php _e('WordPress Support Forum', 'cmp-coming-soon-maintenance');?></a></p>
		</div>

		<?php 
		// get sidebar with "widgets"
		if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
			require (dirname(__FILE__) . '/cmp-sidebar.php');
		} ?>
	</div>
</div>