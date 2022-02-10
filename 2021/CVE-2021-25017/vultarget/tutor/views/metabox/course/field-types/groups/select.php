
<select name="<?php echo $input_name; ?>">
	<?php
	if ( ! isset($group_field['select_options']) || $group_field['select_options'] !== false){
		echo '<option value="-1">'.__('Select Option', 'tutor').'</option>';
	}
	if ( ! empty($group_field['options'])){
		foreach ($group_field['options'] as $optionKey => $option){
			?>
			<option value="<?php echo $optionKey ?>" <?php selected($input_value,  $optionKey) ?> ><?php echo $option ?></option>
			<?php
		}
	}
	?>
</select>