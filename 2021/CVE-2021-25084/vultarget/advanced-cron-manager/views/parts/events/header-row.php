<?php
/**
 * Header row of events
 *
 * @package advanced-cron-manager
 */

?>

<div class="single-event header">
	<div class="columns">
		<div class="column cb"><input type="checkbox" class="select-all"></div>
		<div class="column event is-sortable" data-name="event"><?php esc_html_e( 'Event', 'advanced-cron-manager' ); ?></div>
		<div class="column schedule is-sortable" data-name="schedule"><?php esc_html_e( 'Schedule', 'advanced-cron-manager' ); ?></div>
		<div class="column arguments"><?php esc_html_e( 'Arguments', 'advanced-cron-manager' ); ?></div>
		<div class="column next-execution is-sortable asc" data-name="next-execution"><?php esc_html_e( 'Next execution', 'advanced-cron-manager' ); ?></div>
	</div>
</div>
