<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="table-wrapper content wrapper-disabled">
	<h3><?php _e('Image Slider Setup', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Slider setup', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
						 	<input type="radio" disabled name="niteoCS_slider_<?php echo esc_attr($themeslug);?>" value="1">&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
						 	<input type="radio" disabled name="niteoCS_slider_<?php echo esc_attr($themeslug);?>" value="0" checked="checked">&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td id="slider-disabled">
				<p><?php _e('Slider settings is not supported by the selected Theme.', 'cmp-coming-soon-maintenance');?></p>
			</td>

	</table>
</div>