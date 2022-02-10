<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="table-wrapper theme-setup wrapper-disabled">
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
						 	<input type="radio" disabled class="niteoCS-logo-type" name="niteoCS_logo_type_<?php echo esc_attr($themeslug);?>" value="text">&nbsp;<?php _e('Text Logo', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="<?php _e('Graphic Logo', 'cmp-coming-soon-maintenance');?>">
						 	<input type="radio" disabled class="niteoCS-logo-type" name="niteoCS_logo_type_<?php echo esc_attr($themeslug);?>" value="graphic">&nbsp;<?php _e('Graphic Logo', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>">
						 	<input type="radio" disabled class="niteoCS-logo-type" name="niteoCS_logo_type_<?php echo esc_attr($themeslug);?>" value="disabled" checked="checked">&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>
			<td>
				<p class="disabled-logo"><?php _e('Logo settings is not supported by the selected Theme.', 'cmp-coming-soon-maintenance');?></p>
			</td>

		</tr>
		</tbody>
	</table>
</div>