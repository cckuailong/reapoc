<?php 
/**
 * PPOM Main HTML Template
 * 
 * Rendering all fields on product page
 * 
 * @version 1.0
 * 
 **/
 
/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }


// check if duplicate ppom fields render
if( ! $form_obj::$ppom->has_unique_datanames() ) {
	$duplicate_found = apply_filters('ppom_duplicate_datanames_text', __('Some of your fields has duplicated datanames, please fix it', 'ppom' ) );

	echo '<div class="error">'.esc_html($duplicate_found).'</div>';

	return '';
}

// ppom meta ids
$ppom_wrapper_id = is_array($form_obj::$ppom->meta_id) ? implode('-',$form_obj::$ppom->meta_id) : $form_obj::$ppom->meta_id;
?>

<div id="ppom-box-<?php echo esc_attr($ppom_wrapper_id); ?>" class="ppom-wrapper">
	
	

	<!-- Display price table before fields -->
	<?php if( ppom_get_price_table_location() === 'before' ) {
		echo $form_obj->render_price_table_html();
	} ?>

	<!-- Render hidden inputs -->
	<?php $form_obj->form_contents(); ?>

	<div class="<?php echo esc_attr($form_obj->wrapper_inner_classes());?>">
		
		<?php
		/*
		** hook before ppom fields 
		*/
		do_action('ppom_before_ppom_fields', $form_obj);
		?>

		<?php $form_obj->ppom_fields_render(); ?>
		
		<?php
		/*
		** hook after ppom fields 
		*/
		do_action('ppom_after_ppom_fields', $form_obj);
		?>

	</div> <!-- end form-row -->
	
	

	<!-- Display price table after fields -->
	<?php if( ppom_get_price_table_location() === 'after' ) { 
		echo $form_obj->render_price_table_html();
	} ?>


	
	<div style="clear:both"></div>

</div>  <!-- end ppom-wrapper -->