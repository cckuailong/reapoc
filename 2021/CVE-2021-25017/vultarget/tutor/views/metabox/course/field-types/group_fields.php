<?php
if ( ! isset($field['group_fields']) || ! is_array($field['group_fields']) || ! count($field['group_fields']) ){
	return;
}
?>
<div class="tutor-option-gorup-fields-wrap">
	<?php
	foreach ($field['group_fields'] as $groupFieldKey => $group_field){
		$input_name = "_tutor_course_settings[{$field['field_key']}][{$groupFieldKey}]";
		$default_value = isset($group_field['default']) ? $group_field['default'] : false;
		$input_value = $this->get($field['field_key'].'.'.$groupFieldKey, $default_value);
		$label = tutor_utils()->avalue_dot('label', $group_field);
		?>
		<div class="tutor-option-group-field">
			<?php include tutor()->path."views/options/field-types/groups/{$group_field['type']}.php"; ?>
		</div>
		<?php
	}
	?>
</div>

