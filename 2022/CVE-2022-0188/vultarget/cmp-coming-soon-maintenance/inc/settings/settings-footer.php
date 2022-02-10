<?php 
if ( isset($_POST['niteoCS_contact_email']) ) {
	update_option('niteoCS_contact_email', sanitize_text_field($_POST['niteoCS_contact_email']));
}

$niteoCS_contact_email 		= get_option('niteoCS_contact_email', 'john.doe@email.com');

?>

<div class="table-wrapper content" id="copyright-section">
	<h3><?php _e('Footer Content', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
		<tr>
			<th><?php _e('Copyright', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<input type="text" name="niteoCS_copyright" id="niteoCS_copyright" value="<?php echo esc_attr( $this->niteo_sanitize_html($niteoCS_copyright)); ?>" class="regular-text code">
				</fieldset>
			</td>
		</tr>

		<?php if ( $this->cmp_selectedTheme() == 'stylo' ): ?>
		<tr>
			<th><?php _e('Contact Email', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<input type="text" name="niteoCS_contact_email" value="<?php echo esc_attr( $niteoCS_contact_email );?>" class="regular-text code">
				</fieldset>
			</td>
		</tr>
		<?php endif;?>
		
		<?php echo $this->render_settings->submit(); ?>

	</tbody>
	</table>
</div>

<?php if ( $this->cmp_selectedTheme() == 'eclipse'  || ( isset( $theme_supports['extended_footer'] ) && $theme_supports['extended_footer']  )):

	if (isset($_POST['niteoCS_contact_content'])) {
		update_option('niteoCS_contact_content', $this->niteo_sanitize_html($_POST['niteoCS_contact_content']));
	}

	if (isset($_POST['niteoCS_contact_title'])) {
		update_option('niteoCS_contact_title', sanitize_text_field($_POST['niteoCS_contact_title']));
	}

	if (isset($_POST['niteoCS_contact_phone'])) {
		update_option('niteoCS_contact_phone', sanitize_text_field($_POST['niteoCS_contact_phone']));
	}

	$niteoCS_contact_content 	= stripslashes( get_option('niteoCS_contact_content', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.') );
	$niteoCS_contact_title 		= get_option('niteoCS_contact_title', 'Quick Contacts');
	$niteoCS_contact_phone 		= get_option('niteoCS_contact_phone', '+123456789'); ?>

	<div class="table-wrapper content">
		<h3><?php _e('Extended Footer Content', 'cmp-coming-soon-maintenance');?></h3>
		<table class="theme-setup">

		<tr>
			<th><?php _e('Content', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<textarea name="niteoCS_contact_content" rows="5"><?php echo $this->niteo_sanitize_html( $niteoCS_contact_content ); ?></textarea>
				</fieldset>
			</td>
		</tr>

		<tr>
			<th><?php _e('Contacts Title', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<input type="text" name="niteoCS_contact_title" value="<?php echo esc_attr( $niteoCS_contact_title );?>" class="regular-text code">
				</fieldset>
			</td>
		</tr>
		
		<?php if ( $this->cmp_selectedTheme() == 'agency' ): ?>

		<?php 
		if ( isset($_POST['niteoCS_contact_address']) ) {
			update_option('niteoCS_contact_address', $this->niteo_sanitize_html( $_POST['niteoCS_contact_address']) );
		}

		$contact_address 	= stripslashes( get_option('niteoCS_contact_address', '220 Central Park S, New York, NY 10019, USA') );
		?>
		<tr>
			<th><?php _e('Contact Address', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<textarea name="niteoCS_contact_address" rows="3"><?php echo $this->niteo_sanitize_html( $contact_address ); ?></textarea>
				</fieldset>
			</td>
		</tr>
		<?php endif; ?>

		<tr>
			<th><?php _e('Contact Email', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<input type="text" name="niteoCS_contact_email" value="<?php echo esc_attr( $niteoCS_contact_email );?>" class="regular-text code">
				</fieldset>
			</td>
		</tr>

		<tr>
			<th><?php _e('Contact Phone', 'cmp-coming-soon-maintenance');?></th>
			<td>
				<fieldset>
					<input type="text" name="niteoCS_contact_phone" value="<?php echo esc_attr( $niteoCS_contact_phone );?>" class="regular-text code">
				</fieldset>
			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</table>
	</div>
<?php endif;?>