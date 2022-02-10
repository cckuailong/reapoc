<?php
/**
* Checkbox Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/checkbox.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'checkbox');
	
$options = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');

// If options empty
if ( ! $options ) {
	
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "Please add some options to render this input.", "ppom" ) .'</p>';
    echo '</div>';
    
	return '';
}

// Defualt Checked Values
$checked_value = array();
if( is_array($default_value) ){
	$checked_value = array_map('stripcslashes', $default_value);
}

$check_wrapper_class = apply_filters('ppom_checkbox_wrapper_class','form-check-inline');
$product_type = $product->get_type();

// change checkbox input form name 
// add_filter('ppom_input_name_attr', function($form_name, $meta){
// 	 if( $meta['type'] == 'checkbox' ) {
// 	 	$dataname = $meta['data_name'];
// 	 	$form_name = "ppom[fields][".esc_attr($dataname)."][]";
// 	 }
// 	 return $form_name;
// }, 11, 2);
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>


	<?php 
	foreach ($options as $key => $value){ 

		$option_label = $value['label'];
        $option_price = $value['price'];
        $raw_label    = $value['raw'];
        $without_tax  = $value['without_tax'];
        $option_id    = $value['option_id'];
        $dom_id       = apply_filters('ppom_dom_option_id', $option_id, $field_meta);
        $opt_percent    = isset($value['percent']) ? $value['percent']: '';
        
        $ppom_has_percent = $opt_percent !== '' ? 'ppom-option-has-percent' : '';
        $option_class     = array(
        						"ppom-option-{$option_id}",
                            	"ppom-{$product_type}-option",
                            	$ppom_has_percent,
                            );
                                
        $option_class	= apply_filters('ppom_option_classes', implode(" ", $option_class), $field_meta);
        $input_class	= $fm->input_classes()." ".$option_class;

        $checked_option = '';
        if( count($checked_value) > 0 && in_array($key, $checked_value) && !empty($key)){
        
            $checked_option = checked( $key, $key, false );
        }

	?>
		<div class="<?php echo esc_attr($check_wrapper_class); ?>">
			<label class="<?php echo esc_attr($fm->checkbox_label_classes()); ?>" for="<?php echo esc_attr($dom_id); ?>">
				
				<input 
					type="checkbox" 
					name="<?php echo esc_attr($fm->form_name()); ?>" 
					id="<?php echo esc_attr($dom_id); ?>" 
					class="<?php echo esc_attr($input_class); ?>" 
					data-optionid="<?php echo esc_attr($option_id); ?>" 
					data-price="<?php echo esc_attr($option_price); ?>"
					data-percent="<?php echo esc_attr($opt_percent); ?>"
					data-label="<?php echo esc_attr($raw_label); ?>"
					data-title="<?php echo esc_attr($fm->title()); ?>"
					data-onetime="<?php echo esc_attr($onetime); ?>" 
					data-taxable="<?php echo esc_attr($taxable); ?>" 
					data-without_tax="<?php echo esc_attr($without_tax); ?>" 
					data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
					value="<?php echo esc_attr($key); ?>" 
					<?php echo $checked_option; ?>
				>
				<span class="ppom-input-option-label ppom-label-checkbox"><?php echo $option_label; ?></span>
			</label>
		</div>

	<?php } ?>
</div>