<?php
/**
 * Textarea Input Template
*
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$input_id   = isset($input_meta['input_id']) ? $input_meta['input_id'] : '';
$default   = isset($input_meta['default']) ? $input_meta['default'] : '';

?>
<textarea 
    name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>"
    data-rule-id="<?php echo esc_attr($input_id); ?>"
    id="<?php echo esc_attr($input_id); ?>"
    cols="30" 
    rows="10"
><?php echo esc_html($class_ins::get_saved_settings($input_id, $default)); ?></textarea>