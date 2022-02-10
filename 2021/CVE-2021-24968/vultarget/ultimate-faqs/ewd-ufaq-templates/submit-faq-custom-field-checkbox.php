<div class='ewd-ufaq-meta-field'>

	<label for='<?php echo esc_attr( $this->get_custom_field_name() ); ?>' class='ewd-ufaq-submit-faq-label'>
		<?php echo esc_html( $this->get_custom_field_name() ); ?>
	</label>

	<div class='ewd-ufaq-submit-faq-radio-checkbox-container'>

		<?php $input_name = $this->get_custom_field_input_name(); ?>
	
		<?php foreach ( $this->get_custom_field_options() as $option ) { ?>

			<div class='ewd-ufaq-submit-faq-radio-checkbox-each'>
				<input type='checkbox' name='<?php echo esc_attr( $input_name ); ?>[]' value='<?php echo $option; ?>' <?php echo ( ( ! empty( $_POST[ $input_name ] ) and is_array( $_POST[ $input_name ] ) and in_array( $option, $_POST[ $input_name ] ) ) ? 'checked' : '' ); ?>/><?php echo $option; ?>
			</div>
			
		<?php } ?>

	</div>

</div>