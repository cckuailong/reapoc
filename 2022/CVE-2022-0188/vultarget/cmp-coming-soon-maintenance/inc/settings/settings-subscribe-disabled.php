<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper content wrapper-disabled" id="subscribe-section">
	<h3><?php _e('Subscribe Form', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
	<tr>
		<th>
			<fieldset>
				<legend class="screen-reader-text">
					<span><?php _e('Subscribe Form Options', 'cmp-coming-soon-maintenance');?></span>
				</legend>

				<p>
					<label title="CMP Subscribe Form">
					 	<input disabled type="radio" name="niteoCS_subscribe_type" value="2">&nbsp;<?php _e('CMP Subscribe Form', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="3rd Party Plugin">
					 	<input disabled type="radio" name="niteoCS_subscribe_type" value="1">&nbsp;<?php _e('3rd Party Plugin', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="Disabled">
					 	<input disabled type="radio" name="niteoCS_subscribe_type" value="0" checked="checked">&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

			</fieldset>
		</th>

		<td id="subscribe-disabled">
			<p><?php _e('Subscribe Form is not supported by the selected Theme.', 'cmp-coming-soon-maintenance');?></p>
		</td>

	</tbody>
	</table>
</div>