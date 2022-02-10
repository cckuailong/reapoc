<?php
if ( empty( $field['options'] ) ) {
    $default = isset( $field['default'] ) ? $field['default'] : '';
    $option_value = $this->get( $field['field_key'], $default );
    $label_title = isset( $field['label_title'] ) ? sanitize_text_field( $field['label_title'] ) : sanitize_text_field( $field['label'] );
	?>
	<label>
		<input type="checkbox" name="tutor_option[<?php esc_attr_e( $field['field_key'] ) ?>]" value="1" <?php checked($option_value, '1') ?> />
		<?php esc_html_e( $label_title ); ?>
	</label>
	<?php
}else{
	//Check if multi option exists
	foreach ( $field['options'] as $field_option_key => $field_option ) {
		?>
		<label>
			<input type="checkbox" name="tutor_option[<?php esc_attr_e( $field['field_key'] ); ?>][<?php esc_attr_e( $field_option_key ); ?>]" value="1" <?php checked($this->get($field['field_key'].'.'.$field_option_key), '1') ?> />
			<?php esc_html_e( $field_option ); ?>
		</label>
		<br />
		<?php
	}
}
?>