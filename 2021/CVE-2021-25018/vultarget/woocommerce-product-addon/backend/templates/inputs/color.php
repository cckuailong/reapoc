<?php
/**
* Color Input Template
*
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$input_id  = isset($input_meta['input_id']) ? $input_meta['input_id'] : '';
?>

<input 
    type="text"
    name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>"
    class="nmsf-wp-colorpicker"
    data-rule-id="<?php echo esc_attr($input_id); ?>"
    id="<?php echo esc_attr($input_id); ?>"
    value="<?php echo esc_attr($class_ins::get_saved_settings($input_id)); ?>" 
    data-alpha-enabled="true"
>