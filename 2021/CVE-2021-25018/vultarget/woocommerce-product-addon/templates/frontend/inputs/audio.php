<?php
/**
* Audio/Video Input Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/inputs/audio.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'audio');

$multiple_allowed  = $fm->get_meta_value('multiple_allowed');
    
// If audio/video empty
if ( ! $fm->audio_video() ) {
	echo '<div class="ppom-option-notice">';
        echo '<p>'. __( "Please Add Some Audio/Video", "ppom" ) .'</p>';
    echo '</div>';
	return;
}
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<div class="ppom_audio_box">
		<?php 
		foreach ($fm->audio_video() as $audio){ 
			
			$audio_link = isset($audio['link']) ? $audio['link'] : 0;
			$audio_id   = isset($audio['id']) ? $audio['id'] : 0;
			$audio_title= isset($audio['title']) ? stripslashes($audio['title']) : 0;
			$audio_price= isset($audio['price']) ? $audio['price'] : 0;	

			// Actually image URL is link
			$audio_url  = wp_get_attachment_url( $audio_id );
			$audio_title_price = $audio_title . ' ' . ($audio_price > 0 ? ppom_price($audio_price) : '');
            
            $checked_option = '';
            if( ! empty($default_value) ){
			    if( is_array($default_value) ) {
			        foreach($default_value as $img_data) {
			            if( $audio_id == $img_data['id'] ) {
			                $checked_option = 'checked="checked"';
			            }
			        }
			    } else {
			        $checked_option = ($audio_id == $default_value ? 'checked=checked' : '' );
			    }
            }
		?>
			<div class="ppom_audio">
				<?php 
					if( !empty($audio_url) ) {
					    echo apply_filters( 'the_content', $audio_url );
					}
				?>

				<div class="input_image">
					
					<?php if ($multiple_allowed == 'on') { ?>
						<input 
							type="checkbox" 
							name="<?php echo esc_attr($fm->form_name()); ?>[]" 
							data-title="<?php echo esc_attr($fm->title()); ?>" 
							class="ppom-input"
							data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
							data-label="<?php echo esc_attr($audio_title); ?>" 
							data-price="<?php echo esc_attr($audio_price); ?>" 
							value="<?php echo esc_attr(json_encode($audio)); ?>" 
							<?php echo $checked_option; ?>
						>
					<?php } else { ?>
						<input 
							type="radio" 
							name="<?php echo esc_attr($fm->form_name()); ?>[]" 
							data-type="audio" 
							data-title="<?php echo esc_attr($fm->title()); ?>" 
							class="ppom-input"
							data-label="<?php echo esc_attr($audio_title); ?>" 
							data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
							data-price="<?php echo esc_attr($audio_price); ?>" 
							value="<?php echo esc_attr(json_encode($audio)); ?>" 
							<?php echo $checked_option; ?>
						>
					<?php } ?>

					<!-- Display Audio/video Price Label -->
					<div class="p_u_i_name"><?php echo $audio_title_price; ?></div>
				</div> <!-- input_image -->
			</div> <!-- ppom_audio -->
		<?php 
		} 
		?>
	</div> <!-- ppom_audio_box -->
</div>