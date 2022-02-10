<?php
/**
* Select Template
*
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$input_id = isset($input_meta['input_id']) ? $input_meta['input_id'] : '';
$options  = isset($input_meta['options']) ? $input_meta['options'] : array();
$input_style = isset($input_meta['style']) ? $input_meta['style'] : '';

$selected_opt = $class_ins::get_saved_settings($input_id);

?>

<?php if ($input_style != 'multiselect') { ?>
    <select 
        name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>"
        data-rule-id="<?php echo esc_attr($input_id); ?>"
        id="<?php echo esc_attr($input_id); ?>"
    >   
        <?php foreach ($options as $key => $val) { ?>
            <option value="<?php echo esc_attr($key);  ?>" <?php selected($selected_opt, $key, true); ?>><?php echo $val;  ?></option>
        <?php } ?>
    </select>
<?php }else{?>
    <select 
        multiple="multiple" 
        name="<?php echo esc_attr($class_ins::get_form_name($input_id)); ?>[]" 
        data-rule-id="<?php echo esc_attr($input_id); ?>"
        id="<?php echo esc_attr($input_id); ?>"
        data-placeholder="<?php esc_attr_e( 'Choose Options', 'ppom' ); ?>" 
        class="nmsf-multiselect-js"
    >
    	<?php
    	if ( ! empty( $options ) ) {
    	    $selected = '';
    		foreach ( $options as $key => $val ) {
                if( !empty($selected_opt) ) {
                    $selected = in_array($key, $selected_opt) ? 'selected="selected"' : '';
                } ?>
    		    <option value="<?php echo esc_attr($key);  ?>" <?php echo $selected ?>><?php echo esc_html( $val );  ?></option>
    	<?php }
    	}
    	?>
    </select>
<?php } ?>
