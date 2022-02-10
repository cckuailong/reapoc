<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( isset( $_POST['niteoCS_special_effect'] ) ) {
	update_option( 'niteoCS_special_effect', sanitize_text_field($_POST['niteoCS_special_effect']) );
}

if ( isset( $_POST['niteoCS_constellation_color'] ) ) {
	update_option( 'niteoCS_special_effect[constellation][color]', sanitize_text_field($_POST['niteoCS_constellation_color']) );
}

$special_effect  		= get_option('niteoCS_special_effect', 'disabled');
$constellation_color 	= get_option('niteoCS_special_effect[constellation][color]', '#ffffff');

?>

<div class="table-wrapper theme-setup special-effects">
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
							<input type="radio" class="special-effect" name="niteoCS_special_effect" value="constellation" <?php checked( 'constellation', $special_effect );?>>&nbsp;<?php _e('Constellation', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Floating Bubbles">
							<input type="radio" class="special-effect" name="niteoCS_special_effect" value="bubbles" <?php checked( 'bubbles', $special_effect );?>>&nbsp;<?php _e('Floating Bubbles', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Let It Snow">
							<input type="radio" class="special-effect" name="niteoCS_special_effect" value="snow" <?php checked( 'snow', $special_effect );?>>&nbsp;<?php _e('Let It Snow', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
							<input type="radio" class="special-effect" name="niteoCS_special_effect" value="disabled" <?php checked( 'disabled', $special_effect );?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td>
				<fieldset class="special-effect-switch disabled">
					<p><?php _e('Background effects are disabled', 'cmp-coming-soon-maintenance')?></p>
				</fieldset>

				<fieldset class="special-effect-switch constellation bubbles snow">
					<h4><?php _e('Effect Color', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" name="niteoCS_constellation_color" id="niteoCS_constellation_color" value="<?php echo esc_attr( $constellation_color ); ?>" data-default-color="#ffffff" class="regular-text code"><br>
				</fieldset>

			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>

</div>

<script>
jQuery(document).ready(function($){
	// ini color picker
	jQuery('#niteoCS_constellation_color').wpColorPicker();
});
</script>
