<?php
/**
* File Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/file.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'file');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');
$file_cost  = $fm->get_meta_value('file_cost');
$btn_class  = $fm->get_meta_value('button_class');
$btn_label  = $fm->get_meta_value('button_label_select');
$input_classes = $fm->input_classes();

$field_label = ($file_cost == '') ? $fm->field_label() : $fm->field_label() . ' - ' . wc_price($file_cost);
$btn_label   = ($btn_label == '' ? __('Select files', "ppom") : $btn_label);
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
			class="btn btn-primary <?php echo esc_attr($btn_class); ?> <?php echo esc_attr($input_classes); ?>"
		>
			<?php echo esc_html($btn_label); ?>
		</a>
		<span class="ppom-dragdrop-text"><?php echo _e("Drag File Here", "ppom"); ?></span>
	</div> <!-- ppom-file-container -->

	<div id="filelist-<?php echo esc_attr($fm->data_name()); ?>" class="filelist <?php echo esc_attr($fm->data_name()); ?>">
		
		<?php 
		if ( !empty( $default_value ) ){ 

			foreach ($default_value as $key => $file) {
				
				$file_preview = ppom_uploaded_file_preview($file['org'], $field_meta);
	        	if( !isset($file['org']) || $file_preview == '') continue;

	        	$file_name = $file['org'];
	        	$file_form_name = 'ppom[fields]['.$fm->data_name().']['.$key.'][org]';
	        	$file_form_class = 'ppom-file-cb ppom-file-cb-'.$fm->data_name();
	        	?>

	        	<div class="u_i_c_box" id="u_i_c_<?php echo esc_attr($key); ?>" data-fileid="<?php echo esc_attr($key); ?>">
	        		
	        		<?php echo $file_preview; ?>

	        		<!-- Adding CB for data handling -->
	        		<input 
						type="checkbox" 
						name="<?php echo esc_attr($file_form_name); ?>" 
						class="<?php echo esc_attr($file_form_class); ?>" 
						data-price="<?php echo esc_attr($file_cost); ?>" 
						data-label="<?php echo esc_attr($file_name); ?>" 
						data-title="<?php echo esc_attr($fm->title()); ?>" 
						value="<?php echo esc_attr($file_name); ?>" 
						checked="checked"
					>
	        	</div>

	        	<?php
			}
		}
		?>
	</div> <!-- filelist -->
</div>