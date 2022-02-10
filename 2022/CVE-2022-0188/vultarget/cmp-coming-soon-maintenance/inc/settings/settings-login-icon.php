<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( isset( $_POST['niteoCS_login_icon'] ) ) {
	update_option( 'niteoCS_login_icon', sanitize_text_field($_POST['niteoCS_login_icon']) );
}
if ( isset( $_POST['niteoCS_login_icon_background'] ) ) {
	update_option( 'niteoCS_login_icon[background]', sanitize_text_field($_POST['niteoCS_login_icon_background']) );
}
if ( isset( $_POST['niteoCS_login_icon_position'] ) ) {
	update_option( 'niteoCS_login_icon[position]', sanitize_text_field($_POST['niteoCS_login_icon_position']) );
}
if ( isset( $_POST['niteoCS_login_icon_opacity'] ) ) {
	update_option( 'niteoCS_login_icon[opacity]', sanitize_text_field($_POST['niteoCS_login_icon_opacity']) );
}
if ( isset( $_POST['niteoCS_login_icon_radius'] ) ) {
	update_option( 'niteoCS_login_icon[radius]', sanitize_text_field($_POST['niteoCS_login_icon_radius']) );
}


$login_icon_status  	= get_option('niteoCS_login_icon', '0');
$login_icon_position 	= get_option('niteoCS_login_icon[position]', '30');
$login_icon_background 	= get_option('niteoCS_login_icon[background]', '#000000');
$login_icon_opacity	    = get_option('niteoCS_login_icon[opacity]', '0.6');
$login_icon_radius		= get_option('niteoCS_login_icon[radius]', '0');
$login_icon_login_url	= get_option('niteoCS_login_icon[radius]', '0');
?>

<div class="table-wrapper content">
	<h3><?php _e('Login Icon', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Login Icons', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
							<input type="radio" class="login-icon" name="niteoCS_login_icon" value="1" <?php checked( '1', $login_icon_status );?>>&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
							<input type="radio" class="login-icon" name="niteoCS_login_icon" value="0" <?php checked( '0', $login_icon_status );?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td class="login-icon-switch x0">
				<fieldset>
					<p><?php _e('Login Icon is disabled', 'cmp-coming-soon-maintenance')?></p>
				</fieldset>
            </td>

            <td class="login-icon-switch x1">

				<fieldset>
					<h4><?php _e('Background Color', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" name="niteoCS_login_icon_background" id="niteoCS_login_icon_background" value="<?php echo esc_attr( $login_icon_background ); ?>" data-default-color="#000000" class="regular-text code"><br>
				</fieldset>

                <fieldset style="margin: 1em 0">
				    <h4><?php _e('Position from Top', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $login_icon_position ); ?></span>%</h4>
				    <input type="range" class="login-icon-position" name="niteoCS_login_icon_position" min="0" max="100" step="1" value="<?php echo esc_attr( $login_icon_position ); ?>" />
			    </fieldset>	

                <fieldset>
				    <h4><?php _e('Icon Opacity', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $login_icon_opacity ); ?></span></h4>
				    <input type="range" class="login-icon-opacity" name="niteoCS_login_icon_opacity" min="0" max="1" step="0.1" value="<?php echo esc_attr( $login_icon_opacity ); ?>" />
			    </fieldset>	
				<br>
                <fieldset>
				    <h4><?php _e('Rouned Corners', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $login_icon_radius ); ?></span>px</h4>
				    <input type="range" class="login-icon-radius" name="niteoCS_login_icon_radius" min="0" max="30" step="1" value="<?php echo esc_attr( $login_icon_radius ); ?>" />
			    </fieldset>	

                <br>
                <p class="cmp-hint"><?php echo sprintf(__('Login Icon URL is set to standard WordPress wp-login.php. To Change the URL go to %s > Custom Login URL.', 'cmp-coming-soon-maintenance'), '<a href="' . get_admin_url() . 'admin.php?page=cmp-advanced">CMP Advanced Settings</a>');?></p>

			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>

</div>

<script>
jQuery(document).ready(function($){
	// ini color picker
    jQuery('#niteoCS_login_icon_background').wpColorPicker();
    jQuery( '.login-icon-opacity' ).on('input', function () {
		var value = jQuery(this).val();
		jQuery(this).parent().find('span').html(value);
	});
    jQuery( '.login-icon-position' ).on('input', function () {
		var value = jQuery(this).val();
		jQuery(this).parent().find('span').html(value);
	});
    jQuery( '.login-icon-radius' ).on('input', function () {
		var value = jQuery(this).val();
		jQuery(this).parent().find('span').html(value);
	});
});
</script>
