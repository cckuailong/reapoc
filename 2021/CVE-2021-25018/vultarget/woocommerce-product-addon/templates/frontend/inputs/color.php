<?php
/**
* Color Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/color.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'color');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');
$price      = $fm->get_meta_value('price');

$price_without_tax = '';
if($onetime == 'on' && $taxable == 'on') {
	$price_without_tax = $price;
	$price = ppom_get_price_including_tax($price, $product);
}

$input_classes = $fm->input_classes();
if( $price !== '' ) {$input_classes .= ' ppom-priced';}
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- If title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<input 
		type="text" 
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		class="<?php echo esc_attr($input_classes); ?>" 
		placeholder="<?php echo esc_attr($fm->placeholder()); ?>" 
		autocomplete="off" 
		data-type="text" 
		data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
		data-title="<?php echo esc_attr($fm->title()); ?>" 
		data-price="<?php echo esc_attr($price); ?>" 
		data-onetime="<?php echo esc_attr($onetime); ?>" 
		data-taxable="<?php echo esc_attr($taxable); ?>" 
		data-without_tax="<?php echo esc_attr($price_without_tax); ?>" 
		value="<?php echo esc_attr($default_value); ?>" 

		<?php 
		// Add input extra attributes
		foreach ($input_attr as $key => $val){ echo $key . '="' . $val .'"'; }
		?>
	>
</div>