<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper theme-setup">
	<h3><?php _e('Customize Colors', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
	<tr>
		<th><?php _e('Active Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_active_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_active_color" value="<?php echo esc_attr( $active_color); ?>" data-default-color="#f37004" class="regular-text code"><br>
				<span><?php _e('Headings and active elements color (buttons, hover links, etc).', 'cmp-coming-soon-maintenance');?></span>
			</fieldset>
		</td>
	</tr>

	<tr>
		<th><?php _e('Font Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_font_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_font_color" value="<?php echo esc_attr( $font_color); ?>" data-default-color="#686868" class="regular-text code"><br>
			</fieldset>
		</td>
	</tr>

	<tr>
		<th><?php _e('Background Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_background_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_background_color" value="<?php echo esc_attr( $background_color); ?>" data-default-color="#ffffff" class="regular-text code"><br>
			</fieldset>
		</td>
	</tr>

	<tr>
		<th><?php _e('Social Background Color', 'cmp-coming-soon-maintenance');?></th>
		<td>
			<fieldset>
				<input type="text" name="niteoCS_social_background_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_social_background_color" value="<?php echo esc_attr( $social_background_color ); ?>" data-default-color="#f8f8f8" class="regular-text code"><br>
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
	jQuery('#niteoCS_active_color').wpColorPicker();
	jQuery('#niteoCS_background_color').wpColorPicker();
	jQuery('#niteoCS_social_background_color').wpColorPicker();
});
</script>
