
<select name="<?php esc_attr_e( $input_name ); ?>">
	<?php
	if ( ! isset( $group_field['select_options'] ) || $group_field['select_options'] !== false ) {
		echo '<option value="-1">'.__('Select Option', 'tutor').'</option>';
	}
	if ( ! empty( $group_field['options'] ) ) {
		foreach ( $group_field['options'] as $optionKey => $option ) {
			?>
			<option value="<?php esc_attr_e( $optionKey ); ?>" <?php selected($input_value,  $optionKey) ?> ><?php esc_html_e( $option ); ?></option>
			<?php
		}
	}
	?>
</select>