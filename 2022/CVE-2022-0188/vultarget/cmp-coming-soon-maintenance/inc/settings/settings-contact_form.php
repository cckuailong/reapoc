<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// contact form 
if ( isset($_POST['niteoCS_contact_form_type']) ) {
	update_option('niteoCS_contact_form_type', sanitize_text_field($_POST['niteoCS_contact_form_type']));
}

// contact form 
if ( isset($_POST['niteoCS_contact_form_id']) && is_numeric($_POST['niteoCS_contact_form_id']) ) {
	update_option('niteoCS_contact_form_id', sanitize_text_field($_POST['niteoCS_contact_form_id']));
}

// contact form 
if ( isset($_POST['niteoCS_contact_form_label']) ) {
	update_option('niteoCS_contact_form_label', sanitize_text_field($_POST['niteoCS_contact_form_label']));
}

// get contact form settings
$contact_form_type 			= get_option('niteoCS_contact_form_type', 'cf7');
$contact_form_id 			= get_option('niteoCS_contact_form_id', 'disabled');
$contact_form_label 		= get_option('niteoCS_contact_form_label', 'Get in Touch');

?>

<div class="table-wrapper content" id="contact-form-section">
	<h3><?php _e('Contact Form', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
	<tr>
		<th>

			<fieldset>
				<legend class="screen-reader-text">
					<span><?php _e('Contact Form Options', 'cmp-coming-soon-maintenance');?></span>
				</legend>

				<p>
					<label title="3rd Party">
					 	<input type="radio" class="contact-form" name="niteoCS_contact_form_type" value="cf7" <?php checked( 'cf7', $contact_form_type );?>>&nbsp;<?php _e('3rd Party', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="Disabled">
					 	<input type="radio" class="contact-form" name="niteoCS_contact_form_type" value="disabled" <?php checked( 'disabled', $contact_form_type );?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

			</fieldset>

		</th>

		<td id="subscribe-disabled" class="contact-form-switch disabled">
			<p><?php _e('Subscribe Form is disabled.', 'cmp-coming-soon-maintenance');?></p>
		</td>

		<td id="subscribe-3rdparty" class="contact-form-switch cf7">
			<fieldset>

				<label for="niteoCS_contact_form_label"><?php _e('Contact Form Heading', 'cmp-coming-soon-maintenance' );?>
					<input type="text" name="niteoCS_contact_form_label" value="<?php echo esc_attr( stripslashes( $contact_form_label ) );?>" class="regular-text code" placeholder="<?php _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>">
				</label>


				<label for="niteoCS_contact_form_id"><?php _e('Select your contact form', 'cmp-coming-soon-maintenance' );?>

					<?php 
					// if cf7 is activated
					if ( class_exists( 'WPCF7_ContactForm' ) ) {
						$args 			= array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
						$cf7forms 		= get_posts( $args );

						// if any wpcf7_contact_form exists
						if ( !empty( $cf7forms ) ) {
							$post_ids 		= wp_list_pluck( $cf7forms , 'ID' );
							$form_titles 	= wp_list_pluck( $cf7forms , 'post_title' );
							$cf7status 		= true;

						} else {
							$cf7status = __( 'No contact forms detected. Please create a new Contact Form 7.', 'cmp-coming-soon-maintenance' );
						}

					} else {
						$cf7status = __( 'Please install Contact Form 7 plugin to select contact form.', 'cmp-coming-soon-maintenance' );
					} ?>


					<select name="niteoCS_contact_form_id" <?php echo ( $cf7status !== true ) ? 'disabled' : '';?>>
						<?php 
						if ( $cf7status !== true ) {
							echo '<option value="disabled" selected>'.$cf7status.'</option>';

						}  else {
							$i = 0;
							foreach ( $post_ids as $id ) { ?>
								<option value="<?php echo esc_attr( $id );?>" <?php selected($id, $contact_form_id);?>><?php echo esc_html( $form_titles[$i] );?></option>
								<?php 
								$i++;
							}

						} ?>
					</select>

				</label>

				<p><?php printf( __('If the list is empty, please make sure you have installed %s plugin and you have created a Contact Form. If not, you can create new Contact Form in %s > Contact > New', 'cmp-coming-soon-maintenance'), '<a href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a>', '<a href="'.get_admin_url().'">Admin</a>');?></p>
			</fieldset>
		</td>
	</tr>

	<?php echo $this->render_settings->submit(); ?>
	
	</tbody>
	</table>
</div>