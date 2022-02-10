<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap wpbs-wrap">

	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Backup/Restore', 'wp-booking-system' ); ?></h1>
	<hr class="wp-header-end" />

	<div id="poststuff" class="postbox-container">

		<!-- Postbox Export -->
		<div class="postbox">

			<form method="POST" action="">

				<h2 class="hndle"><?php echo __( 'Create export file', 'wp-booking-system' ); ?></h2>

				<!-- Form Fields -->
				<div class="inside">

					<p><?php echo __( 'To export a backup file for WP Booking System, please click the Download Export File button.', 'wp-booking-system' ); ?></p>

					<p><?php echo __( 'The export file will contain all information saved by the plugin, including calendars, bookings and general settings.', 'wp-booking-system' ); ?></p>
				
				</div>

				<!-- Card Bottom -->
				<div class="wpbs-plugin-card-bottom plugin-card-bottom">
					<input type="submit" class="button-primary" value="<?php echo __( 'Download Export File', 'wp-booking-system' ); ?>" />
				</div>

				<!-- Nonce -->
				<?php wp_nonce_field( 'wpbs_backup_export', 'wpbs_token', false ); ?>
				<input type="hidden" name="wpbs_action" value="backup_export" />

			</form>

		</div>

		<!-- Postbox Import -->
		<div class="postbox">

			<form enctype="multipart/form-data" method="POST" action="">

				<h2 class="hndle"><?php echo __( 'Import data from file', 'wp-booking-system' ); ?></h2>

				<!-- Form Fields -->
				<div class="inside">

					<p><?php echo __( 'To import all data from a WP Booking System export file, browse for that file and click the Import Data button.', 'wp-booking-system' ); ?></p>

					<p><?php echo __( 'Please take into account that this action will firstly remove all data that you currently have in the plugin, then import the data from the export file.', 'wp-booking-system' ); ?></p>

					<input type="file" name="import_file" />
				
				</div>

				<!-- Card Bottom -->
				<div class="wpbs-plugin-card-bottom plugin-card-bottom">
					<input onclick="return confirm('<?php echo __( 'Are you sure you want to import data from the file?', 'wp-booking-system' ); ?>' )" type="submit" class="button-primary" value="<?php echo __( 'Import Data', 'wp-booking-system' ); ?>" />
				</div>

				<!-- Nonce -->
				<?php wp_nonce_field( 'wpbs_backup_import', 'wpbs_token', false ); ?>
				<input type="hidden" name="wpbs_action" value="backup_import" />

			</form>

		</div>

	</div>

</div>