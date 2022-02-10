<div class="table-wrapper content wrapper-disabled">
	<h3><?php _e('Countdown Timer Setup', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Counter setup', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
						 	<input disabled type="radio" name="niteoCS_counter" value="1">&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
						 	<input disabled type="radio" name="niteoCS_counter" value="0" checked="checked">&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td id="counter-disabled">
				<p><?php _e('Countdown Timer is not supported by the selected Theme.', 'cmp-coming-soon-maintenance');?></p>
			</td>

		</tr>
	</table>

</div>