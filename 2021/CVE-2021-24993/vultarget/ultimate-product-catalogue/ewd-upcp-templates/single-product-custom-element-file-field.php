<div class='ewd-upcp-single-product-custom-field'>

	<span class='ewd-upcp-single-product-extra-element-label'>
		<?php echo esc_html( $this->custom_field->name ); ?>:
	</span>

	<span class='ewd-upcp-single-product-extra-element-value'>

		<a href='<?php echo esc_attr( $this->product->custom_fields[ $this->custom_field->id ] );?>' download>
			<?php echo esc_html( basename( $this->product->custom_fields[ $this->custom_field->id ] ) ); ?>
		</a>
		
	</span>

</div>