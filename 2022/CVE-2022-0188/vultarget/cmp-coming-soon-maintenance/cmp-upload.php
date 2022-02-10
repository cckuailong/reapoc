<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<div class="wrap cmp-coming-soon-maintenance">
	<h1></h1>
	<div id="icon-users" class="icon32"></div>
	<div class="settings-wrap">
		<div class="cmp-inputs-wrapper help-settings">
			
			<form action="admin.php?page=cmp-settings" class="wp-upload-form cmp" method="post" enctype="multipart/form-data">
				<table class="install">
				<tbody>
					<tr>
						<td>
							<h3 style="text-align:left"><?php _e('Install New CMP Theme', 'cmp-coming-soon-maintenance');?></h3>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e('Select CMP Theme ZIP file to upload','cmp-coming-soon-maintenance');?>:
							<input type="file" name="fileToUpload" id="fileToUpload">
							<?php wp_nonce_field('save_options','save_options_field'); ?>
							<input id="install-theme" type="submit" value="Install Theme" name="submit_theme">
						</td>
					</tr>
				</tbody>
				</table>

			</form>
		</div>

		<?php 
		// get sidebar with "widgets"
		if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
			require (dirname(__FILE__) . '/cmp-sidebar.php');
		} ?>
	</div>
</div>