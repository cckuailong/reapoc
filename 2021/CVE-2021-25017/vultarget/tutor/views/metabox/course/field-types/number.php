<?php
$value = $this->get($field['field_key']);
if ( ! $value && isset($field['default'])){
	$value = $field['default'];
}
?>
<input type="number" name="_tutor_course_settings[<?php echo $field['field_key']; ?>]" value="<?php echo $value; ?>" >