<?php
/**
* Textarea Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/text.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }
	
$fm = new PPOM_InputManager($field_meta, 'textarea');

$rich_editor  = $fm->get_meta_value('rich_editor');
$postid       = $fm->get_meta_value('default_value');
$input_attr   = $fm->get_meta_value('attributes');

// Set Defualt value
$textarea_value = '';
if( !empty($default_value) ) {
	$textarea_value = str_replace(']]>', ']]&gt;', $default_value);
}

// Rich Editor
if ($rich_editor == 'on') {
	
    $wp_editor_setting = array( 'media_buttons' => false,
								'editor_class'  => $fm->input_classes(),
								'teeny'			=> true,
								'textarea_name'	=> $fm->form_name()	
						);
						
    $wp_editor_setting = apply_filters('ppom_textarea_rich_editor_settings', $wp_editor_setting, $field_meta);
}

if ($postid && is_numeric($postid) && $rich_editor != 'on') {
	$textarea_value  = str_replace('<br />',"\n",$textarea_value );
	$textarea_value  = strip_tags($textarea_value);
}

?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<?php 
	if ($rich_editor == 'on'){ 
		wp_editor($textarea_value, $fm->data_name(), $wp_editor_setting);
	}else{
	?>
		<textarea
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		class="<?php echo esc_attr($fm->input_classes()); ?>" 
		placeholder="<?php echo esc_attr($fm->placeholder()); ?>" 

		<?php 
		// Add input extra attributes
		foreach ($input_attr as $key => $val){ echo $key . '="' . $val .'"'; }
		?>
		><?php 
		if($textarea_value != ''){
			$textarea_value  = str_replace('<br />',"\n",$textarea_value );
			echo esc_html($textarea_value);
		} 
		?></textarea>

	<?php } ?>

</div>