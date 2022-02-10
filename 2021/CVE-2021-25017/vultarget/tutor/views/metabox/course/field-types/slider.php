<div class="tutor-field-type-slider" data-min="<?php echo tutor_utils()->avalue_dot('options.min', $field) ?>" data-max="<?php echo tutor_utils()->avalue_dot('options.max', $field) ?>">
	<p class="tutor-field-type-slider-value"><?php echo $this->get($field['field_key'], $field['default']); ?></p>
	<div class="tutor-field-slider"></div>
	<input type="hidden" value="<?php echo $this->get($field['field_key'], $field['default']); ?>" name="_tutor_course_settings[<?php echo $field['field_key']; ?>]" />
</div>
