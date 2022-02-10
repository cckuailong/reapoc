<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper content wrapper-disabled" id="contact-form-section">
	<h3><?php _e('Contact Form', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
	<tr>
		<th>
			<fieldset>
				<legend class="screen-reader-text">
					<span><?php _e('Contact Form Options', 'cmp-coming-soon-maintenance');?></span>
				</legend>

				<p>
					<label title="3rd Party">
					 	<input disabled type="radio" name="niteoCS_contact_form_type" value="1">&nbsp;<?php _e('3rd Party', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="Disabled">
					 	<input disabled type="radio" name="niteoCS_contact_form_type" value="0" checked="checked">&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

			</fieldset>
		</th>

		<td id="contact-form-disabled">
			<p><?php _e('Contact Form is not supported by the selected Theme.', 'cmp-coming-soon-maintenance');?></p>
		</td>

	</tbody>
	</table>
</div>