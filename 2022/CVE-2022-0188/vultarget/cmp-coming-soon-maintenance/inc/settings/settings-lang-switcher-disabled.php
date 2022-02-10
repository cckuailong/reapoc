<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="table-wrapper content wrapper-disabled">
	<h3><?php _e('Language Switcher', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Language Switcher', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
							<input type="radio" class="lang-switcher" name="niteoCS_lang_switcher" value="1">&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
							<input type="radio" class="lang-switcher" name="niteoCS_lang_switcher" value="0" checked>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td class="lang-switcher-switch x0">
				<fieldset>
					<p><?php _e('You must install Polylang or WPML with String Translation add-on to enable Multilanguage functionality.', 'cmp-coming-soon-maintenance')?></p>
				</fieldset>
            </td>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>

</div>