<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>
<tr>
	<th><?php _e('Social Icons Location', 'cmp-coming-soon-maintenance');?></th>
	<td>
		<fieldset>
			<select name="niteoCS_social_location">
			  <option value="body" <?php if ( $social_location == 'body' ) { echo ' selected="selected"'; } ?>><?php _e('Below Content (big icons)', 'cmp-coming-soon-maintenance');?></option>
			  <option value="footer" <?php if ( $social_location == 'footer' ) { echo ' selected="selected"'; } ?>><?php _e('Footer (small icons)', 'cmp-coming-soon-maintenance');?></option>
			</select>
		</fieldset>
	</td>
</tr>