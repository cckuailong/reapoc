<?php
/**
* Select Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/select.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'select');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');

$options = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);

// If options empty
if ( ! $options ) {
	
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "Please add some options to render this input.", "ppom" ) .'</p>';
    echo '</div>';
    
	return '';
}

// Get Product Type
$product_type = $product->get_type();
			// ppom_pa($options);
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<select 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		class="<?php echo esc_attr($fm->input_classes()); ?>" 
		data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 

		<?php 
		// Add input extra attributes
		foreach ($input_attr as $key => $val){ echo $key . '="' . $val .'"'; }
		?>
	>
		
		<?php 
		foreach ($options as $key => $value){

			$option_label   = $value['label'];
            $option_price   = $value['price'];
            $option_id      = isset($value['id']) ? $value['id'] : '';
            $raw_label      = $value['raw'];
            $without_tax    = $value['without_tax'];
            $opt_percent    = isset($value['percent']) ? $value['percent']: '';

            $ppom_has_percent = $opt_percent !== '' ? 'ppom-option-has-percent' : '';
            $option_class     = array(
            						"ppom-option-{$option_id}",
                                	"ppom-{$product_type}-option",
                                	$ppom_has_percent,
                                );
                                    
            $option_class = apply_filters('ppom_option_classes', implode(" ", $option_class), $field_meta);

            // if option has weight and price is not set, then set it zero for calculation
            if( empty($option_price) && !empty($value['option_weight']) ) {
                $option_price = 0;
            }

            $selected_value = selected( $default_value, $key, false )
		?>
		
			<option
				value="<?php echo esc_attr($key); ?>" 
				class="<?php echo esc_attr($option_class); ?>" 
				data-price="<?php echo esc_attr($option_price); ?>" 
				data-optionid="<?php echo esc_attr($option_id); ?>" 
				data-percent="<?php echo esc_attr($opt_percent); ?>" 
				data-label="<?php echo esc_attr($raw_label); ?>" 
				data-title="<?php echo esc_attr($fm->title()); ?>" 
				data-onetime="<?php echo esc_attr($onetime); ?>" 
				data-taxable="<?php echo esc_attr($taxable); ?>" 
				data-without_tax="<?php echo esc_attr($without_tax); ?>" 
				data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
				<?php echo $selected_value; ?>
			><?php echo esc_html($option_label); ?></option>

		<?php 
		} 
		?>
		
	</select>

</div>