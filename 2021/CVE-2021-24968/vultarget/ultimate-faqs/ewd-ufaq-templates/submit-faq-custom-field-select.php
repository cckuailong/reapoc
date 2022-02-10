<div class='ewd-ufaq-meta-field'>

	<label for='<?php echo esc_attr( $this->get_custom_field_name() ); ?>' class='ewd-ufaq-submit-faq-label'>
		<?php echo esc_html( $this->get_custom_field_name() ); ?>
	</label>

	<?php $input_name = $this->get_custom_field_input_name(); ?>

	<select name='<?php echo esc_attr( $input_name ); ?>' >
		
		<?php foreach ( $this->get_custom_field_options() as $option ) { ?>
			<option value='<?php echo $option; ?>' <?php echo ( ! empty( $_POST[ $input_name ] ) and $option == $_POST[ $input_name ] ? 'selected' : '' ); ?>><?php echo $option; ?></option>
		<?php } ?>

	</select>
</div>