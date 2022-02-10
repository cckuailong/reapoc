<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="table-wrapper content">
	<h3><?php _e('Logo Setup', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
		<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Logo setup', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="<?php _e('Text Logo', 'cmp-coming-soon-maintenance');?>">
						 	<input type="radio" class="cmp-logo" name="niteoCS_logo_type" value="text"<?php if ( $niteoCS_logo_type == 'text') { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Text Logo', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="<?php _e('Graphic Logo', 'cmp-coming-soon-maintenance');?>">
						 	<input type="radio" class="cmp-logo" name="niteoCS_logo_type" value="graphic"<?php if ( $niteoCS_logo_type == 'graphic') { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Graphic Logo', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>">
						 	<input type="radio" class="cmp-logo" name="niteoCS_logo_type" value="disabled"<?php if ( $niteoCS_logo_type == 'disabled') { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td>
				<fieldset class="cmp-logo-switch text">
					<p style="margin:-2em 0">
						<input type="text" class="widefat" id="niteoCS-text-logo" name="niteoCS_text_logo" style="padding:0" placeholder="<?php _e('Click to set..', 'cmp-coming-soon-maintenance');?>" value="<?php echo esc_attr($niteoCS_text_logo); ?>" />
					</p>
				</fieldset>

				<fieldset class="cmp-logo-switch graphic">

			        <input type="hidden" class="widefat" id="niteoCS-logo-id" name="niteoCS_logo_id" value="<?php echo esc_attr( $niteoCS_logo_id ); ?>" />
			        <input id="add-logo" type="button" class="button" value="Select Logo" />
			        
			        <div class="logo-wrapper"><?php 
			        	if ( isset($logo_url) && $logo_url !== '' ) {
			        		echo '<img src="'.esc_url($logo_url).'" alt="CMP Logo">';
			        	} ?></div>
			        <input id="delete-logo" type="button" class="button" value="Remove Logo" /><br><br>

					<label for="niteoCS_logo_custom_size"><input type="checkbox" name="niteoCS_logo_custom_size" id="niteoCS_logo_custom_size" class="cmp-logo-size" value='1' <?php checked($niteoCS_logo_custom_size, '1');?>><?php _e('Set custom logo height', 'cmp-coming-soon-maintenance');?></label>

					<fieldset class="cmp-logo-size-switch x1">	
						<div class="logo-height-wrap">
							<input type="range" id="logo_size_slider" min="50" max="500" step="5" value="<?php echo esc_attr( $niteoCS_logo_size ); ?>" data-type="content" />
							<input type="number" id="niteoCS_logo_size" name="niteoCS_logo_size" min="50" max="500" step="5" value="<?php echo esc_attr( $niteoCS_logo_size ); ?>"><span>px</span>
						</div>
					</fieldset>

				</fieldset>

				<fieldset class="cmp-logo-switch text graphic" style="margin-top:1em">
					<h4><?php _e('Logo Link URL', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" class="widefat" id="niteoCS-logo-link" name="niteoCS_logo_link" placeholder="<?php _e('Custom Logo Link', 'cmp-coming-soon-maintenance');?>" value="<?php echo esc_url($logo_link); ?>" />
				</fieldset>
				
				


				<p class="cmp-logo-switch disabled"><?php _e('Logo is disabled', 'cmp-coming-soon-maintenance');?></p>
			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>
</div>