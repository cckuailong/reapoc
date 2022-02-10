<?php
/**
 * View: Month View - Mobile Event Cost
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/month/mobile-events/mobile-day/mobile-event/cost.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.10.9
 * @version 4.12.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */

if ( ! $event->featured ) {
	return;
}

if ( empty( $event->cost ) ) {
	return;
}
?>
<div class="tribe-events-c-small-cta tribe-common-b3 tribe-events-calendar-month-mobile-events__mobile-event-cost">
	<?php if ( $event->tickets->exist() && $event->tickets->in_date_range() && ! $event->tickets->sold_out() ) : ?>
		<a
			href="<?php echo esc_url( $event->tickets->link->anchor ); ?>"
			class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt"
		>
			<?php echo esc_html( $event->tickets->link->label ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $event->tickets->sold_out() ) : ?>
		<span class="tribe-events-c-small-cta__sold-out tribe-common-b3--bold">
			<?php echo esc_html( $event->tickets->stock->sold_out ); ?>
		</span>
	<?php endif; ?>
	<span class="tribe-events-c-small-cta__price">
		<?php echo esc_html( $event->cost ) ?>
	</span>
	<?php if ( ! empty( $event->tickets->stock->available ) && tribe_tickets_is_current_time_in_date_window( $event->ID ) ) : ?>
		<span class="tribe-events-c-small-cta__stock">
			<?php echo esc_html( $event->tickets->stock->available ); ?>
		</span>
	<?php endif; ?>
</div>
