<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( isset($_POST['niteoCS_counter']) && is_numeric( $_POST['niteoCS_counter'] )) {
	update_option('niteoCS_counter', sanitize_text_field( $_POST['niteoCS_counter'] ));
}

if ( isset($_POST['niteoCS_counter_date']) ) {
	update_option('niteoCS_counter_date', sanitize_text_field( $_POST['niteoCS_counter_date'] ));

	if ( $_POST['niteoCS_counter_date'] > time() ) {
		delete_option( 'niteoCS_counter_email' );
	}
}

if (isset($_POST['niteoCS_countdown_action'])) {
	update_option('niteoCS_countdown_action', sanitize_text_field( $_POST['niteoCS_countdown_action'] ));
}

if (isset($_POST['niteoCS_countdown_redirect'])) {
	update_option('niteoCS_countdown_redirect', esc_url_raw( $_POST['niteoCS_countdown_redirect'] ));
}

if (isset($_POST['niteoCS_counter_heading'])) {
	update_option('niteoCS_counter_heading', sanitize_text_field( $_POST['niteoCS_counter_heading'] ));
}

// register and enqueue admin needed scripts
wp_enqueue_script('countdown_flatpicker_js');	
wp_enqueue_style( 'countdown_flatpicker_css');

// get counter settings
$niteoCS_counter			= get_option('niteoCS_counter', '1');
$niteoCS_counter_date		= get_option('niteoCS_counter_date', time() + 86400);
$niteoCS_countdown_action	= get_option('niteoCS_countdown_action', 'no-action');
$niteoCS_countdown_redirect	= get_option('niteoCS_countdown_redirect');
$niteoCS_counter_heading 	= get_option('niteoCS_counter_heading', 'STAY TUNED, WE ARE LAUNCHING SOON...');

?>


<div class="table-wrapper content">
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
						 	<input type="radio" name="niteoCS_counter" class="counter" value="1"<?php if ( $niteoCS_counter == 1 ) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
						 	<input type="radio" name="niteoCS_counter" class="counter" value="0"<?php if ( $niteoCS_counter == 0 ) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td id="counter-disabled" class="counter-switch x0">
				<p><?php _e('Countdown timer is disabled.', 'cmp-coming-soon-maintenance');?></p>
			</td>

			<td id="counter-enabled" class="counter-switch x1">

				<?php if ( $this->cmp_selectedTheme() == 'eclipse' || ( isset( $theme_supports['counter_title'] ) && $theme_supports['counter_title']  ) ) :
					// heading used in Eclipse theme ?>
					<fieldset>
						<h4 for="niteoCS_counter_heading"><?php _e('Counter Heading', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" id="niteoCS_counter_heading" name="niteoCS_counter_heading" value="<?php echo esc_attr( $niteoCS_counter_heading ); ?>" class="regular-text code"><br>
						<br>
					</fieldset>
				<?php endif;?>

				<h4><?php _e('Click on a date input to set timer', 'cmp-coming-soon-maintenance');?></h4>
				<fieldset>

					<legend class="screen-reader-text">
						<span><?php _e('Counter setup', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<input type="text" name="niteoCS_counter_date" id="niteoCS_counter_date" placeholder="<?php _e('Select Date..','cmp-coming-soon-maintenance');?>" value="<?php echo esc_attr( $niteoCS_counter_date); ?>" class="regular-text code"><br>
					<br>
					<h4><?php _e('Countdown action:', 'cmp-coming-soon-maintenance');?></h4>
					<select name="niteoCS_countdown_action" id="niteoCS_countdown_action" class="counter-action">

						<option value="no-action" <?php selected($niteoCS_countdown_action, 'no-action'); ?>><?php _e('No Action', 'cmp-coming-soon-maintenance');?></option>
						<option value="hide" <?php selected($niteoCS_countdown_action, 'hide'); ?>><?php _e('Hide Counter', 'cmp-coming-soon-maintenance');?></option>
						<option value="disable-cmp" <?php selected($niteoCS_countdown_action, 'disable-cmp'); ?>><?php _e('Disable Coming Soon / Maintenance page.', 'cmp-coming-soon-maintenance');?></option>
					 	<option value="redirect" <?php selected($niteoCS_countdown_action, 'redirect'); ?>><?php _e('URL Redirect', 'cmp-coming-soon-maintenance');?></option>

					</select>

					<?php 

					if ( get_option('niteoCS_countdown_notification', '1')  == '1' ) { ?>
						<span class="cmp-hint">* <?php echo sprintf(__('Notification e-mail will be sent to %s email address once counter expires.', 'cmp-coming-soon-maintenance'), get_option('niteoCS_countdown_email_address', get_option( 'admin_email' )));?></span>
						<?php 
					} else { ?>
						<span class="cmp-hint">* <?php _e( 'E-mail notification is disabled.', 'cmp-coming-soon-maintenance' );?></span>
						<?php 
					} ?>

					<span class="cmp-hint"><a href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-notifications"><?php _e('Change e-mail notification settings.', 'cmp-coming-soon-maintenance');?></a></span>

					<div class="counter-action redirect">
						<h4 for="niteoCS_countdown_redirect" style="padding-top:1em"><?php _e('Enter redirect URL', 'cmp-coming-soon-maintenance');?></h4>
						<input type="text" id="niteoCS_countdown_redirect" name="niteoCS_countdown_redirect" value="<?php echo esc_url( $niteoCS_countdown_redirect ); ?>" class="regular-text code"><br>
					</div>


				</fieldset>
			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
	
	</table>

</div>

<script>
jQuery(document).ready(function($){
	<?php 
	if ( $niteoCS_counter_date != '') { ?>
		var date = new Date(<?php echo esc_attr( $niteoCS_counter_date );?>*1000);
	<?php 
	} else { ?>
		var date = false;
		<?php 
	} 	?>
	// flatpicker
	$('#niteoCS_counter_date').flatpickr({
		minDate: 'today',
		defaultDate: date,
		enableTime: true,
		altInput: true,
		dateFormat: 'U'
	});
});
</script>