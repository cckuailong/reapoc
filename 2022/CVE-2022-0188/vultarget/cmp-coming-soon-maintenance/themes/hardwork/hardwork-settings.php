<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<style>
	#social-section tr:first-of-type {display: none!important;}
</style>

<div class="table-wrapper theme-setup">
	<h3><?php _e('Customize Colors', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">

	<tr>
		<th><?php _e('Font Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_font_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_font_color" value="<?php echo esc_attr( $font_color); ?>" data-default-color="#ffffff" class="regular-text code"><br>
			</fieldset>
		</td>
	</tr>

	<tr>
		<th><?php _e('Footer Background Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_footer_background_<?php echo esc_attr($themeslug);?>" id="niteoCS_footer_background_<?php echo esc_attr($themeslug);?>" value="<?php echo esc_attr( $footer_background); ?>" data-default-color="#0a0a0a" class="regular-text code"><br>
			</fieldset>

			<fieldset>
				<label for="niteoCS_footer_background_opacity_<?php echo esc_attr( $themeslug );?>"><?php _e('Footer Background Opacity', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $footer_opacity ); ?></span></label><br>

				<input type="range" class="footer-opacity-<?php echo esc_attr( $themeslug );?>" name="niteoCS_footer_background_opacity_<?php echo esc_attr( $themeslug );?>" min="0" max="1" step="0.1" value="<?php echo esc_attr( $footer_opacity ); ?>" />
			</fieldset>	
		</td>

	</tr>

	<?php echo $this->render_settings->submit(); ?>
	
	</table>

</div>


<script>
jQuery(document).ready(function($){
	// ini color picker
	jQuery('#niteoCS_font_color').wpColorPicker();
	jQuery('#niteoCS_footer_background_<?php echo esc_attr($themeslug);?>').wpColorPicker();
	jQuery( '.footer-opacity-<?php echo esc_attr( $themeslug );?>' ).on('input', function () {
		var value = jQuery(this).val();
		// change label value
		jQuery(this).parent().find('span').html(value);

	});
});
</script>
