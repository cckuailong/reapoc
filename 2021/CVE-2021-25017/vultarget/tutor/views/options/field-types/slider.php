<div class="tutor-field-type-slider" data-min="<?php esc_attr_e( tutor_utils()->avalue_dot('options.min', $field ) ); ?>" data-max="<?php esc_attr_e( tutor_utils()->avalue_dot('options.max', $field ) ); ?>">
	<p class="tutor-field-type-slider-value"><?php esc_html_e( $this->get($field['field_key'], $field['default'] ) ); ?></p>
	<div class="tutor-field-slider"></div>
	<input type="hidden" value="<?php esc_attr_e( $this->get( $field['field_key'], $field['default'] ) ); ?>" name="tutor_option[<?php esc_attr_e( $field['field_key'] ); ?>]" />
</div>
