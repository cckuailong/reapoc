<?php
/**
* PriceMatrix Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/pricematrix.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'pricematrix');

$discount      = $fm->get_meta_value('discount');
$show_slider   = $fm->get_meta_value('show_slider');
$qty_step      = $fm->get_meta_value('qty_step', 1);
$hide_matrix_table  = $fm->get_meta_value('hide_matrix_table');

$ranges = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);

// If options empty
if ( ! $ranges ) {
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "Please Add Some Option.", "ppom" ) .'</p>';
    echo '</div>';
	return;
}

?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<!-- Check if price matrix table is not hidden by settings -->
	<?php 
	if ($hide_matrix_table != 'on'){ 
		
		foreach ($ranges as $opt) {
			
			$price    = isset( $opt['price'] ) ? trim($opt['price']) : 0;
			$label    = isset( $opt['label'] ) ? $opt['label'] : $opt['raw'];
			$range_id = isset($value['option_id']) ? $value['option_id'] : '';

			if( !empty($opt['percent']) ){
				
				$percent = $opt['percent'];
				if( $discount == 'on' ) {
				    $price = "-{$percent}";
				} else {
				    $price = "{$percent} (".wc_price( $price ).")";
				}
				
			}else {
				$price = wc_price( $price );	
			}
	?>
		<div class="ppom-pricematrix-range ppom-range-<?php echo esc_attr($range_id); ?>">
			<span class="pm-range"> <?php echo apply_filters('ppom_matrix_item_label', stripslashes(trim($label)), $opt); ?></span>
			<span class="pm-price" style="float:right"><?php echo apply_filters('ppom_matrix_item_price', $price, $opt); ?></span>
		</div>
	<?php 
		}
	} 
	?>

	<!-- Range Slider -->
	<?php 
	if ($show_slider == 'on'){ 

		$first_range = reset($ranges);
		$qty_ranges = explode('-', $first_range['raw']);
		$min_quantity	= $qty_ranges[0]-1;
	    
	    $last_range = end($ranges);
		$qty_ranges = explode('-', $last_range['raw']);
		$max_quantity	= $qty_ranges[1];
	?>
		<div class="ppom-slider-container">
			<?php if( apply_filters('ppom_range_slider_legacy', false, $field_meta) ) { ?>
				<input 
					type="text" 
					class="ppom-range-slide" 
					data-slider-id="ppomSlider"
					data-slider-min="<?php echo esc_attr($min_quantity); ?>" 
					data-slider-max="<?php echo esc_attr($max_quantity); ?>" 
					data-slider-step="<?php echo esc_attr($qty_step); ?>" 
					data-slider-value="0" 
				>
			<?php } else { ?>
				<input 
					type="range" 
					class="form-control-range ppom-range-bs-slider" 
					id="<?php echo esc_attr($fm->data_name()); ?>" 
					min="<?php echo esc_attr($min_quantity); ?>" 
					max="<?php echo esc_attr($max_quantity); ?>" 
					step="<?php echo esc_attr($qty_step); ?>" 
				>
			<?php } ?>
		</div>
	<?php } ?>

	<input 
		type="hidden" 
		name="ppom[ppom_pricematrix]" 
		data-dataname="<?php echo esc_attr($fm->form_name()); ?>" 
		class="active ppom_pricematrix ppom-input" 
		data-discount="<?php echo esc_attr($discount); ?>" 
		value="<?php echo esc_attr(json_encode($ranges)); ?>"
	>
</div>