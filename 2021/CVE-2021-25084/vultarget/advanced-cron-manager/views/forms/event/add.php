<?php
/**
 * Add event form
 *
 * @package advanced-cron-manager
 */

$schedules       = $this->get_var( 'schedules' );
$single_schedule = $this->get_var( 'single_schedule' );

?>

<?php $this->get_view( 'forms/header' ); ?>

<?php wp_nonce_field( 'acm/event/insert', 'nonce', false ); ?>

<label for="event-hook"><?php esc_html_e( 'Hook', 'advanced-cron-manager' ); ?></label>
<p class="description"><?php esc_html_e( 'Should not contain any HTML tags including script nor style', 'advanced-cron-manager' ); ?></p>
<input type="text" id="event-hook" name="hook" class="widefat">

<label for="event-execution"><?php esc_html_e( 'First execution', 'advanced-cron-manager' ); ?></label>
<p class="description"><?php esc_html_e( 'When past date will be provided or left empty, event will be executed in the next queue', 'advanced-cron-manager' ); ?></p>
<input type="datetime-local" id="event-execution" name="execution" class="widefat"></input>
<input type="hidden" id="event-offset" name="execution_offset"></input>

<label for="event-schedule"><?php esc_html_e( 'Schedule', 'advanced-cron-manager' ); ?></label>
<p class="description"><?php esc_html_e( 'After first execution repeat:', 'advanced-cron-manager' ); ?></p>
<select id="event-schedule" class="widefat" name="schedule">
	<option value="<?php echo esc_attr( $single_schedule->slug ); ?>">
		<?php
		// Translators: schedule label.
		echo esc_html( sprintf( __( 'Don\'t repeat (%s)', 'advanced-cron-manager' ), $single_schedule->label ) );
		?>
	</option>
	<?php foreach ( $schedules as $schedule ) : ?>
		<option value="<?php echo esc_attr( $schedule->slug ); ?>"><?php echo esc_html( $schedule->label ); ?> (<?php echo esc_html( $schedule->slug ); ?>)</option>
	<?php endforeach ?>
</select>

<label><?php esc_html_e( 'Arguments', 'advanced-cron-manager' ); ?></label>
<p class="description"><?php esc_html_e( 'New inputs will be added automatically when you type', 'advanced-cron-manager' ); ?></p>
<div class="event-arguments">
	<input type="text" name="arguments[]" class="event-argument widefat">
</div>

<?php $this->get_view( 'forms/footer' ); ?>
