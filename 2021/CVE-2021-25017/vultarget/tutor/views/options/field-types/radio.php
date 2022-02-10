

<?php
if ( ! isset( $field['select_options'] ) || $field['select_options'] !== false ) {
	echo '<option value="-1">'.__('Select Option', 'tutor').'</option>';
}
if ( ! empty( $field['options'] ) ) {
	foreach ( $field['options'] as $optionKey => $option ) {
		$option_value = $this->get( $field['field_key'], tutils()->array_get( 'default', $field ) );
		?>
        <p class="option-type-radio-wrap">
            <label>
                <input type="radio" name="tutor_option[<?php esc_attr_e( $field['field_key'] ); ?>]"  value="<?php esc_attr_e( $optionKey ); ?>" <?php checked($option_value,  $optionKey) ?> /> <?php esc_html_e( $option ); ?>
            </label>
        </p>
		<?php
	}
}
?>