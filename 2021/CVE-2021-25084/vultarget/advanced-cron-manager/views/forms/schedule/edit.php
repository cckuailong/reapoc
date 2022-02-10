<?php
/**
 * Edit schedule form
 *
 * @package advanced-cron-manager
 */

$schedule     = $this->get_var( 'schedule' );
$interval_raw = $schedule->get_raw_human_interval();

?>

<?php $this->get_view( 'forms/header' ); ?>

<?php wp_nonce_field( 'acm/schedule/edit', 'nonce', false ); ?>

<label for="schedule-name"><?php esc_html_e( 'Display name', 'advanced-cron-manager' ); ?></label>
<input type="text" id="schedule-name" name="name" class="widefat" value="<?php echo esc_attr( $schedule->label ); ?>">

<label for="schedule-slug"><?php esc_html_e( 'Slug', 'advanced-cron-manager' ); ?></label>
<input type="text" id="schedule-slug" class="widefat" disabled="disabled" value="<?php echo esc_attr( $schedule->slug ); ?>">
<input type="hidden" name="slug" value="<?php echo esc_attr( $schedule->slug ); ?>">

<label><?php esc_html_e( 'Interval', 'advanced-cron-manager' ); ?></label>
<table>
	<tr>
		<td><?php esc_html_e( 'Days', 'advanced-cron-manager' ); ?>:</td>
		<td><input type="number" id="schedule-interval" min="0" value="<?php echo esc_attr( $interval_raw['days'] ); ?>" class="spinbox days"></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Hours', 'advanced-cron-manager' ); ?>:</td>
		<td><input type="number" id="schedule-interval" min="0" max="24" value="<?php echo esc_attr( $interval_raw['hours'] ); ?>" class="spinbox hours"></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Minutes', 'advanced-cron-manager' ); ?>:</td>
		<td><input type="number" id="schedule-interval" min="0" max="60" value="<?php echo esc_attr( $interval_raw['minutes'] ); ?>" class="spinbox minutes"></td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Seconds', 'advanced-cron-manager' ); ?>:</td>
		<td><input type="number" id="schedule-interval" min="0" max="60" value="<?php echo esc_attr( $interval_raw['seconds'] ); ?>" class="spinbox seconds"></td>
	</tr>
</table>

<div class="total-seconds"><?php esc_html_e( 'Total seconds:', 'advanced-cron-manager' ); ?> <span><?php echo esc_html( $schedule->interval ); ?></span></div>
<input type="hidden" name="interval" class="interval-input" value="<?php echo esc_attr( $schedule->interval ); ?>">

<?php $this->get_view( 'forms/footer' ); ?>
