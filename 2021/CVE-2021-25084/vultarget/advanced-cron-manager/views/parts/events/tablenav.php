<?php
/**
 * Table navigation for events
 *
 * @package advanced-cron-manager
 */

?>

<div class="alignleft actions">
	<select>
		<option value="-1"><?php esc_html_e( 'Bulk Actions', 'advanced-cron-manager' ); ?></option>
		<option value="run"><?php esc_html_e( 'Execute now', 'advanced-cron-manager' ); ?></option>
		<?php
		/**
		 * Pause and unpause bulk actions.
		 *
		 * @todo There's a problem with table rerender which clears all the checkboxes
		 * Good to think about a queue of actions.
		 *
		 * <option value="pause"><?php esc_html_e( 'Pause' ); ?></option>
		 * <option value="unpause"><?php esc_html_e( 'Unpause' ); ?></option>
		 */
		?>
		<option value="remove"><?php esc_html_e( 'Remove', 'advanced-cron-manager' ); ?></option>
	</select>
	<input type="submit" class="button action" value="<?php esc_html_e( 'Apply', 'advanced-cron-manager' ); ?>">
</div>

<div class="alignleft actions">
	<select class="schedules-filter">
		<option value=""><?php esc_html_e( 'All Schedules', 'advanced-cron-manager' ); ?></option>
		<?php foreach ( $this->get_var( 'schedules' )->get_schedules() as $schedule ) : ?>
			<option value="<?php echo esc_attr( $schedule->slug ); ?>"><?php echo esc_html( $schedule->label ); ?></option>
		<?php endforeach ?>
		<option value="<?php echo esc_attr( $this->get_var( 'schedules' )->get_single_event_schedule()->slug ); ?>"><?php echo esc_html( $this->get_var( 'schedules' )->get_single_event_schedule()->label ); ?></option>
	</select>
</div>

<div class="tablenav-pages one-page">
	<span class="displaying-num"><?php echo esc_html( $this->get_var( 'events_count' ) . ' ' . __( 'events', 'advanced-cron-manager' ) ); ?></span>
</div>
