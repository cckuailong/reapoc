<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$calendars = wpbs_get_calendars( array( 'status' => 'active' ) );

?>

<div class="wrap wpbs-wrap wpbs-wrap-add-calendar">

	<form action="" method="POST">
		
		<!-- Icon -->
		<div id="wpbs-add-new-calendar-icon">
			<div class="wpbs-icon-wrap">
				<span class="dashicons dashicons-calendar-alt"></span>
				<span class="dashicons dashicons-plus"></span>
			</div>
		</div>

		<!-- Heading -->
		<h1 id="wpbs-add-new-calendar-heading"><?php echo __( 'Add New Calendar', 'wp-booking-system' ); ?></h1>

		<!-- Postbox -->
		<div id="wpbs-add-new-calendar-postbox" class="postbox">

			<!-- Form Fields -->
			<div class="inside">

				<!-- Add Calendar Name -->
				<label for="wpbs-new-calendar-name"><?php echo __( 'Calendar Name', 'wp-booking-system' ); ?> *</label>
				<input id="wpbs-new-calendar-name" name="calendar_name" type="text" value="<?php echo ( ! empty( $_POST['calendar_name'] ) ? esc_attr( $_POST['calendar_name'] ) : '' ); ?>" />
			
			</div>

			<!-- Form Submit button -->
			<div id="major-publishing-actions">
				<a href="<?php echo admin_url( $this->admin_url ); ?>"><?php echo __( 'Cancel', 'wp-booking-system' ); ?></a>
				<input type="submit" class="button-primary wpbs-button-large" value="<?php echo __( 'Add Calendar', 'wp-booking-system' ); ?>" />
			</div>

			<!-- Action and nonce -->
			<input type="hidden" name="wpbs_action" value="add_calendar" />
			<?php wp_nonce_field( 'wpbs_add_calendar', 'wpbs_token', false ); ?>

		</div>

	</form>

</div>