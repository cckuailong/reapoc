<div class='ewd-ufaq-meta-field'>

	<label for='<?php echo esc_attr( $this->get_custom_field_name() ); ?>' class='ewd-ufaq-submit-faq-label'>
		<?php echo esc_html( $this->get_custom_field_name() ); ?>
	</label>

	<?php $input_name = $this->get_custom_field_input_name(); ?>

	<input type='date' class='ewd-ufaq-jquery-datepicker' id='ewd-ufaq-<?php echo esc_attr( $this->get_custom_field_name() ); ?>' name='<?php echo esc_attr( $input_name ); ?>' value='<?php echo ( ! empty( $_POST[ $input_name ] ) ? esc_attr( $_POST[ $input_name ] ) : '' ); ?>'/>

</div>