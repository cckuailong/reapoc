<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<style>
	#social-section tr:first-of-type {display: none!important;}
	.flatpickr-input {width: 50%;margin-bottom: 1em;}
</style>


<div class="table-wrapper theme-setup">
	<h3><?php _e('Customize Colors', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
	<tr>
		<th><?php _e('Active Color', 'cmp-coming-soon-maintenance');?></th>
		<td>

			<fieldset>
				<input type="text" name="niteoCS_active_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_active_color" value="<?php echo esc_attr( $active_color); ?>" data-default-color="#e82e1e" class="regular-text code"><br>
			</fieldset>

		</td>
	</tr>
	<tr>
		<th><?php _e('Font Color', 'cmp-coming-soon-maintenance');?></th>
		<td>

			<fieldset>
				<input type="text" name="niteoCS_font_color_<?php echo esc_attr($themeslug);?>" id="niteoCS_font_color" value="<?php echo esc_attr( $font_color); ?>" data-default-color="#ffffff" class="regular-text code"><br>
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
	jQuery('#niteoCS_background_color').wpColorPicker();
	jQuery('#niteoCS_active_color').wpColorPicker();

});
</script>
