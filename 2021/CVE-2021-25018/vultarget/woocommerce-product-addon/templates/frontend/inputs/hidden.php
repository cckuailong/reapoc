<?php
/**
* Hidden Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/hidden.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'hidden');

$field_value    = $fm->get_meta_value('field_value');
?>

<input 
	type="hidden" 
	name="<?php echo esc_attr($fm->form_name()); ?>" 
	id="<?php echo esc_attr($fm->data_name()); ?>" 
	value="<?php echo esc_attr($field_value); ?>"
>