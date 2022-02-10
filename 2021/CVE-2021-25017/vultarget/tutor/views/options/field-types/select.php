<select name="tutor_option[<?php esc_attr_e( $field['field_key'] ); ?>]" class="tutor_select2">
    <?php
    if ( ! isset( $field['select_options'] ) || $field['select_options'] !== false ) {
        echo '<option value="-1">'.__('Select Option', 'tutor').'</option>';
    }
	if ( ! empty( $field['options'] ) ) {
		foreach ( $field['options'] as $optionKey => $option ) {
			?>
			<option value="<?php esc_attr_e( $optionKey ) ?>" <?php selected( $this->get( $field['field_key'] ),  $optionKey ) ?> ><?php esc_html_e( $option ); ?></option>
			<?php
		}
	}
	?>
</select>