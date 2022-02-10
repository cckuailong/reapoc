<?php
/**
* Palettes Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/palettes.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'palettes');

$onetime          = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$multiple_allowed = $fm->get_meta_value('multiple_allowed');
$color_height     = $fm->get_meta_value('color_height', 50);
$color_width      = $fm->get_meta_value('color_width', 50);
$circle           = $fm->get_meta_value('circle', 50);
$selected_palette_bclr = $fm->get_meta_value('selected_palette_bcolor', '#000');

$options = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);

// If options empty
if ( ! $options ) {
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "Please Add Some Option", "ppom" ) .'</p>';
    echo '</div>';
	return '';
}

// Check defualt value is array
if( !is_array($default_value) ) {
    $default_value = explode(',', $default_value);
}

// Defualt Checked Values
$checked_value = array_map('trim', $default_value);

$custom_css = '';
$custom_css .=  '.ppom-palettes label > input:checked + .ppom-single-palette {
        border: 2px solid '.esc_attr($selected_palette_bclr).' !important;
    }';

echo '<style>';
    echo esc_attr($custom_css);
echo '</style>';
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<!-- Palettes Box -->
	<div class="ppom-palettes ppom-palettes-<?php echo esc_attr($fm->data_name()); ?>">
		
		<?php 
		foreach ($options as $key => $value){
			
			// First Separate color code and label
			$color_label_arr = explode('-', $key);
			$color_code = trim($color_label_arr[0]);
			$color_label = '';
			if(isset($color_label_arr[1])){
				$color_label = trim($color_label_arr[1]);
			}
			
			$color_label   = $value['label'];
			$option_label   = $value['label'];
        	$option_price   = $value['price'];
        	$raw_label      = $value['raw'];
        	$without_tax    = $value['without_tax'];

			$option_id      = $value['option_id'];
			$dom_id         = apply_filters('ppom_dom_option_id', $option_id, $field_meta);
			
			// Checked value selected
			$checked_option = '';
            if( count($checked_value) > 0 && in_array($key, $checked_value) && !empty($key)){
            
                $checked_option = checked( $key, $key, false );
            }

            // Inline span style
            $span_style  = '';
            $span_style .= 'background-color:'. esc_attr($color_code) . ';';
            $span_style .= 'width:'. esc_attr($color_width) . 'px;';
            $span_style .= 'height:'. esc_attr($color_height) . 'px;';

            if ($circle == 'on') {
            	$span_style .= 'border-radius: 50%;';
            }
		?>
			
			<label for="<?php echo esc_attr($dom_id); ?>">
				<?php if ($multiple_allowed == 'on'){ ?>

					<input 
						type="checkbox" 
						name="<?php echo esc_attr($fm->form_name()); ?>[]" 
						id="<?php echo esc_attr($dom_id); ?>" 
						class="ppom-input"
						data-title="<?php echo esc_attr($fm->title()); ?>" 
						data-label="<?php echo esc_attr($color_label); ?>" 
						data-price="<?php echo esc_attr($option_price); ?>" 
						data-optionid="<?php echo esc_attr($option_id); ?>" 
						data-onetime="<?php echo esc_attr($onetime); ?>" 
						data-taxable="<?php echo esc_attr($taxable); ?>" 
						data-without_tax="<?php echo esc_attr($without_tax); ?>" 
						data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
						value="<?php echo esc_attr($raw_label); ?>" 
						<?php echo $checked_option; ?>
					>
				<?php }else{ ?>

					<input 
						type="radio" 
						name="<?php echo esc_attr($fm->form_name()); ?>[]" 
						id="<?php echo esc_attr($dom_id); ?>" 
						class="ppom-input"
						data-title="<?php echo esc_attr($fm->title()); ?>" 
						data-label="<?php echo esc_attr($color_label); ?>" 
						data-price="<?php echo esc_attr($option_price); ?>" 
						data-optionid="<?php echo esc_attr($option_id); ?>" 
						data-onetime="<?php echo esc_attr($onetime); ?>" 
						data-taxable="<?php echo esc_attr($taxable); ?>" 
						data-without_tax="<?php echo esc_attr($without_tax); ?>" 
						data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
						value="<?php echo esc_attr($raw_label); ?>" 
						<?php echo $checked_option; ?>
					>
				<?php } ?>

				<span 
					class="ppom-single-palette" 
					title="<?php echo esc_attr($option_label); ?>" 
					data-ppom-tooltip="ppom_tooltip" 
					style="<?php echo esc_attr($span_style); ?>"
				></span>
			</label>

		<?php 
		} 
		?>
	</div> <!-- ppom-palettes -->
</div>