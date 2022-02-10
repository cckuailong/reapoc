<?php
/**
* Timezone Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/timezone.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'timezone');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');
$show_time  = $fm->get_meta_value('show_time');
$regions      = $fm->get_meta_value('regions', 'All');
$first_option = $fm->get_meta_value('first_option');
$options = ppom_array_get_timezone_list($regions, $show_time);

if( !empty($first_option) ) {
	$options[''] = sprintf( __("%s","ppom"), $first_option );
}

if ( ! $options ) {
	
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "The timezone not found, please add different regions.", "ppom" ) .'</p>';
    echo '</div>';
    
	return '';
}

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
		foreach ($options as $key => $option_label){
		?>
			<option 
				data-title="<?php echo esc_attr($fm->title()); ?>"  
				value="<?php echo esc_attr($key); ?>" 
				<?php selected( $default_value, $key, true ); ?>
			><?php echo esc_html($option_label); ?></option>
			<?php
		}
		?>
	</select>
</div>