<div class='ewd-upcp-catalog-sidebar-custom-field-div' data-custom_field_id='<?php echo $this->custom_field->id; ?>'>

	<div class='ewd-upcp-catalog-sidebar-title <?php echo ( $this->get_option( 'styling-sidebar-title-collapse' ) ? 'ewd-upcp-catalog-sidebar-collapsible' : '' ); ?> <?php echo ( $this->get_option( 'styling-sidebar-start-collapsed' ) ? 'ewd-upcp-sidebar-content-hidden' : '' ); ?>'>
		<?php echo esc_html( $this->custom_field->name ); ?>
	</div>

	<div class='ewd-upcp-catalog-sidebar-custom-field-slider' data-custom_field_id='<?php echo $this->custom_field->id; ?>'></div>

	<div class='ewd-upcp-catalog-sidebar-custom-field-slider-text-inputs'>

		<span>
	
			<input type='text' value='<?php echo min( array_keys( $this->sidebar_custom_fields[ $this->custom_field->id ] ) ); ?>' name='ewd-upcp-custom-field-slider-min' data-custom_field_id='<?php echo $this->custom_field->id; ?>' data-custom_field_minimum='<?php echo min( array_keys( $this->sidebar_custom_fields[ $this->custom_field->id ] ) ); ?>' />
	
		</span>
	
		<span class='ewd-upcp-custom-field-slider-divider'> - </span>
	
		<span>
	
			<input type='text' value='<?php echo max( array_keys( $this->sidebar_custom_fields[ $this->custom_field->id ] ) ); ?>' name='ewd-upcp-custom-field-slider-max' data-custom_field_id='<?php echo $this->custom_field->id; ?>' data-custom_field_maximum='<?php echo max( array_keys( $this->sidebar_custom_fields[ $this->custom_field->id ] ) ); ?>' />
	
		</span>

	</div>

</div>