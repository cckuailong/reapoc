<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( isset($_POST['niteoCS_progress_bar']) && is_numeric( $_POST['niteoCS_progress_bar'] )) {
	update_option('niteoCS_progress_bar', sanitize_text_field( $_POST['niteoCS_progress_bar'] ));
}

if ( isset($_POST['niteoCS_progress_start_bar_date']) ) {
	update_option('niteoCS_progress_start_bar_date', sanitize_text_field( $_POST['niteoCS_progress_start_bar_date'] ));
}
if ( isset($_POST['niteoCS_progress_end_bar_date']) ) {
	update_option('niteoCS_progress_end_bar_date', sanitize_text_field( $_POST['niteoCS_progress_end_bar_date'] ));
}

if ( isset($_POST['niteoCS_progress_bar_type']) ) {
	update_option('niteoCS_progress_bar_type', sanitize_text_field( $_POST['niteoCS_progress_bar_type'] ));
}

if ( isset($_POST['niteoCS_progress_bar_percentage']) ) {
	update_option('niteoCS_progress_bar_percentage', sanitize_text_field( $_POST['niteoCS_progress_bar_percentage'] ));
}



// register and enqueue admin needed scripts
wp_enqueue_script('countdown_flatpicker_js');	
wp_enqueue_style( 'countdown_flatpicker_css');

// get counter settings
$progress_bar				= get_option('niteoCS_progress_bar', '0');
$progress_bar_start_date	= get_option('niteoCS_progress_start_bar_date', time());
$progress_bar_end_date		= get_option('niteoCS_progress_end_bar_date', time() + 86400);
$progress_bar_type  		= get_option('niteoCS_progress_bar_type', 'manual');
$progress_bar_percentage	= get_option('niteoCS_progress_bar_percentage', '0');

?>


<div class="table-wrapper content">
	<h3><?php _e('Progress Bar', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Progress Bar', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
						 	<input type="radio" name="niteoCS_progress_bar" class="progress-bar" value="1"<?php if ( $progress_bar == 1 ) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
						 	<input type="radio" name="niteoCS_progress_bar" class="progress-bar" value="0"<?php if ( $progress_bar == 0 ) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td id="progress-bar-disabled" class="progress-bar-switch x0">
				<p><?php _e('Progress Bar is disabled.', 'cmp-coming-soon-maintenance');?></p>
			</td>

			<td id="progress-bar-enabled" class="progress-bar-switch x1">

				<select name="niteoCS_progress_bar_type" id="niteoCS_progress_bar_type" class="progress-bar-type">
					<option value="manual" <?php selected( 'manual', $progress_bar_type ); ?>><?php _e('Set Progress Bar Manually', 'cmp-coming-soon-maintenance');?></option>
					<option value="auto" <?php selected( 'auto', $progress_bar_type ); ?>><?php _e('Set Automatic Progress Bar by Date', 'cmp-coming-soon-maintenance');?></option>
				</select>

				<div class="progress-bar-type manual">
					<fieldset style="margin: 1em 0">
						<h4><?php _e('Progress', 'cmp-coming-soon-maintenance');?>: <span><?php echo esc_attr( $progress_bar_percentage ); ?></span>%</h4>
						<input type="range" id="niteoCS_progress_bar_percentage" name="niteoCS_progress_bar_percentage" min="0" max="100" step="1" value="<?php echo esc_attr( $progress_bar_percentage ); ?>" />
					</fieldset>
				</div>

				<div class="progress-bar-type auto">

					<h4 style="margin-top:1em"><?php _e('Click to Set Starting Date', 'cmp-coming-soon-maintenance');?></h4>
					<fieldset>
						<input type="text" name="niteoCS_progress_start_bar_date" id="niteoCS_progress_start_bar_date" placeholder="<?php _e('Select Starting Date..','cmp-coming-soon-maintenance');?>" value="<?php echo esc_attr( $progress_bar_start_date); ?>" class="regular-text code"><br>
						<br>
					</fieldset>

					<h4><?php _e('Click to Set Ending Date', 'cmp-coming-soon-maintenance');?></h4>
					<fieldset>
						<input type="text" name="niteoCS_progress_end_bar_date" id="niteoCS_progress_end_bar_date" placeholder="<?php _e('Select Ending Date..','cmp-coming-soon-maintenance');?>" value="<?php echo esc_attr( $progress_bar_end_date); ?>" class="regular-text code"><br>
						<br>
					</fieldset>
				</div>
			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
	
	</table>

</div>

<script>
jQuery(document).ready(function($){
	var EndDate = false;
	var StartDate = false;
	<?php 
	if ( $progress_bar_end_date != '') { ?>
		var EndDate = new Date(<?php echo esc_attr( $progress_bar_end_date );?>*1000);
	<?php 
	} 
	if ( $progress_bar_start_date != '') { ?>
		var StartDate = new Date(<?php echo esc_attr( $progress_bar_start_date );?>*1000);
	<?php 
	} ?>
	// flatpicker
	$('#niteoCS_progress_start_bar_date').flatpickr({
		maxDate: new Date().fp_incr(1),
		defaultDate: StartDate,
		enableTime: true,
		altInput: true,
		dateFormat: 'U'
	});
	$('#niteoCS_progress_end_bar_date').flatpickr({
		minDate: 'today',
		defaultDate: EndDate,
		enableTime: true,
		altInput: true,
		dateFormat: 'U'
	});

	jQuery( '#niteoCS_progress_bar_percentage' ).on('input', function () {
		var value = jQuery(this).val();
		// change label value
		jQuery(this).parent().find('span').html(value);

	});
});
</script>