<div class='ewd-ufaq-faq-custom-fields'>

	<?php foreach ( $this->get_custom_fields() as $custom_field ) {?>

		<?php $value = $this->get_custom_field_value( $custom_field ); ?>

		<?php if ( $this->get_option( 'hide-blank-fields' ) and ! $value ) { continue; } ?>

		<div class='ewd-ufaq-custom-field-label'>
			<?php echo esc_html( $custom_field->name ); ?>:
		</div>
		
		<div class='ewd-ufaq-custom-field-value'>
			<?php echo ( is_array( $value ) ? implode( ', ', $value ) : $value ); ?>
		</div>

		<div class='ewd-ufaq-clear'></div>

	<?php } ?>

</div>