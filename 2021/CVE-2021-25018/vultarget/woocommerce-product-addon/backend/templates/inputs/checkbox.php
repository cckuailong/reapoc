<?php
/**
* Checkbox Input Template
*
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$input_id    = isset($input_meta['input_id']) ? $input_meta['input_id'] : '';
$input_style = isset($input_meta['style']) ? $input_meta['style'] : '';
$default     = isset($input_meta['default']) ? $input_meta['default'] : '';
$label       = isset($input_meta['label']) ? $input_meta['label'] : __( 'Enable', 'ppom' );

$checked = $class_ins::get_saved_settings($input_id);

?>

<?php if ('switcher' == $input_style) { ?>
    <label class="nmsf-switcher-checkbox">
    	<input 
            type="checkbox"
            name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>"
            data-rule-id="<?php echo esc_attr($input_id); ?>"
            id="<?php echo esc_attr($input_id); ?>"
            value="yes"
            <?php checked($checked, 'yes', true); ?>
        >   
    	<span></span>
    	<?php echo esc_html($label); ?>
    </label>
<?php }else{ ?>
    <div class="nmsf-fancy-checkbox">
        <input 
            type="checkbox"
            name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>"
            data-rule-id="<?php echo esc_attr($input_id); ?>"
            id="<?php echo esc_attr($input_id); ?>"
            value="yes"
            <?php checked($checked, 'yes', true); ?>
        >
        <label for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($label); ?></label>
    </div>
<?php } ?>