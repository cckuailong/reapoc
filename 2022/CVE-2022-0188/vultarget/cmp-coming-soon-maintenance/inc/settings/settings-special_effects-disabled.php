<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper theme-setup wrapper-disabled">
	<h3><?php _e('Special Effects', 'cmp-coming-soon-maintenance');?></h3>
	<table class="theme-setup">
	<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Special Effects', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Constellation">
						 	<input type="radio" class="special-effect" name="niteoCS_special_effect" value="constellation">&nbsp;<?php _e('Constellation', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Floating Bubbles">
						 	<input type="radio" class="special-effect" name="niteoCS_special_effect" value="bubbles">&nbsp;<?php _e('Floating Bubbles', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
						 	<input type="radio" class="special-effect" name="niteoCS_special_effect" value="disabled" checked>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td>
				<fieldset class="special-effect-switch disabled">
					<p><?php _e('Special Effects are not supported by the selected Theme. Please activate another Theme with Special Effects Support.', 'cmp-coming-soon-maintenance')?></p>

				</fieldset>

			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>
</div>