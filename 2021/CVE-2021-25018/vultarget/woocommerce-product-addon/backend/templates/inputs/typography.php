<?php
/**
 * Typography Style Template
*/
 
/*
**========== Direct access not allowed =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$input_id = isset($input_meta['input_id']) ? $input_meta['input_id'] : '';
$support = isset($input_meta['support']) ? $input_meta['support'] : array();
$options   = $class_ins->typography_options();

$saved_settings = $class_ins::get_saved_settings($input_id);
?>

<div class="nmsf-css-editor-wrapper nmsf-css-editor-typo">
	<div class="row">
		<?php
		foreach ($options as $key => $val) {
			$title = isset($val['title']) ? $val['title']: '';
			$icon  = isset($val['icon']) ? $val['icon']: '';
			$form_name = $class_ins::get_form_name($input_id)."[".$key."]";
			$input_value = isset($saved_settings[$key]) ? $saved_settings[$key] : '';
			
			if (!empty($support) && !in_array($key, $support)) continue;
		?>
		    <?php if ($key != 'color') { ?>
		        
    			<div class="nmsf-css-editor-style">
    	            <span> <?php echo esc_html($title); ?> </span>
    	            <input 
    	            	type="text" 
    	            	name="<?php echo esc_attr($form_name); ?>" 
    	            	class="nmsf-css-editor-input" 
    	            	value ="<?php echo esc_attr($input_value); ?>"
    	            >
    	     	</div>
		    <?php }else{ ?>
    	            <input 
    	            	type="text"
    	            	name="<?php echo esc_attr($form_name); ?>" 
    	            	class="nmsf-wp-colorpicker" 
    	            	data-alpha-enabled="true" 
    	            	value ="<?php echo esc_attr($input_value); ?>"
    	            >
		    <?php } ?>
		<?php
		}
		?>
	</div>
</div>