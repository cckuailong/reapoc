<?php
/**
* Cropper Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/cropper.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'cropper');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');
$file_cost  = $fm->get_meta_value('file_cost');
$btn_class  = $fm->get_meta_value('button_class');
$btn_label  = $fm->get_meta_value('button_label_select');
$first_option = $fm->get_meta_value('first_option');

$field_label = ($file_cost == '') ? $fm->field_label() : $fm->field_label() . ' - ' . wc_price($file_cost);
$btn_label   = ($btn_label == '' ? __('Select files', "ppom") : $btn_label);

$options = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);

$input_classes = $fm->input_classes() .' ppom-cropping-size';

// ppom_pa($input_classes);
?>


<div id="ppom-file-container-<?php echo esc_attr($fm->data_name()); ?>" class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($field_label): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo sprintf(__("%s", "ppom"), $field_label); ?></label>
	<?php endif ?>

	
	<div class="ppom-file-container text-center" style="height: auto;">
		<a 
			href="javascript:;" 
			id="selectfiles-<?php echo esc_attr($fm->data_name()); ?>" 
			class="btn btn-primary <?php echo esc_attr($btn_class); ?>"
		>
			<?php echo esc_html($btn_label); ?>
		</a>
		<span class="ppom-dragdrop-text"><?php echo _e("Drag file/directory here", "ppom"); ?></span>
	</div> <!-- ppom-file-container -->

	<div id="filelist-<?php echo esc_attr($fm->data_name()); ?>" class="filelist"></div>
		
	<div class="ppom-croppie-wrapper-<?php echo esc_attr($fm->data_name()); ?> text-center">
		<div class="ppom-croppie-preview">
			<?php
			if($options && count($options) > 0){
				
    	    	$croppie_options	= ppom_get_croppie_options($field_meta);
    	    	
	    		$select_css = 'width:'.$croppie_options['boundary']['width'].'px;';
	    		$select_css .= 'margin:5px auto;display:none;';
	    		
				?>
	    	    <select style="<?php echo esc_attr($select_css); ?>" 
	    	        class="<?php echo esc_attr($input_classes); ?>" 
	    	        name="<?php echo esc_attr($fm->form_name()); ?>[ratio]" 
	    	        data-field_name="<?php echo esc_attr($fm->data_name()); ?>"
	    	        data-data_name="<?php echo esc_attr($fm->data_name()); ?>"
	    	        id="crop-size-<?php echo esc_attr($fm->data_name()); ?>"
	    	        disabled
	    	    >
	    	    	<?php
		    	        
	    	        if( $first_option ) {
	    	            echo sprintf(__('<option value="">%s</option>','ppom'), $first_option);
	    	        }
		    	        
	    	        foreach($options as $key => $size) {
	    	            
	    	            $option_label   = $size['label'];
	                    $option_price   = $size['price'];
	                    $raw_label      = $size['raw'];
	                    $without_tax    = $size['without_tax'];
	                    $option_id      = $size['option_id'];
	                    
	                    $selected_opt = selected( $default_value, $key, false );
	                    
	                    if( $option_id == "__first_option__" ) continue;
	    	        ?>
	    	            <option
	    	            	<?php echo $selected_opt; ?>
	                    	value="<?php echo esc_attr($option_id); ?>" 
	                    	data-price="<?php echo esc_attr($option_price); ?>" 
	                    	data-label="<?php echo esc_attr($raw_label); ?>" 
	                    	data-title="<?php echo esc_attr($fm->title()); ?>" 
	                    	data-without_tax="<?php echo esc_attr($without_tax); ?>" 
	                    	data-width="<?php echo esc_attr($size['width']); ?>" 
	                    	data-height="<?php echo esc_attr($size['height']); ?>" 
	                    ><?php echo $option_label; ?></option>
	    	        <?php } ?>
		    	        
	    	   	</select>
		    	   
	    	<?php } ?>
		</div> <!-- ppom-croppie-preview -->
	</div> <!-- ppom-croppie-wrapper -->
</div>