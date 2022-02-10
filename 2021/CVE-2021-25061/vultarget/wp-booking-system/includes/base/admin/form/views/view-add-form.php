<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap wpbs-wrap wpbs-wrap-add-form">

	<form action="" method="POST">
		
		<!-- Icon -->
		<div id="wpbs-add-new-form-icon">
			<div class="wpbs-icon-wrap">
				<span class="dashicons dashicons-editor-justify"></span>
				<span class="dashicons dashicons-plus"></span>
			</div>
		</div>

		<!-- Heading -->
		<h1 id="wpbs-add-new-form-heading"><?php echo __( 'Add New Form', 'wp-booking-system' ); ?></h1>

		<!-- Postbox -->
		<div id="wpbs-add-new-form-postbox" class="postbox">

			<!-- Form Fields -->
			<div class="inside">

				<!-- Add Form Name -->
				<label for="wpbs-new-form-name"><?php echo __( 'Form Name', 'wp-booking-system' ); ?> *</label>
				<input id="wpbs-new-form-name" name="form_name" type="text" value="<?php echo ( ! empty( $_POST['form_name'] ) ? esc_attr( $_POST['form_name'] ) : '' ); ?>" />
			
			</div>

			<!-- Form Submit button -->
			<div id="major-publishing-actions">
				<a href="<?php echo admin_url( $this->admin_url ); ?>"><?php echo __( 'Cancel', 'wp-booking-system' ); ?></a>
				<input type="submit" class="button-primary wpbs-button-large" value="<?php echo __( 'Add Form', 'wp-booking-system' ); ?>" />
			</div>

			<!-- Action and nonce -->
			<input type="hidden" name="wpbs_action" value="add_form" />
			<?php wp_nonce_field( 'wpbs_add_form', 'wpbs_token', false ); ?>

		</div>

	</form>

</div>