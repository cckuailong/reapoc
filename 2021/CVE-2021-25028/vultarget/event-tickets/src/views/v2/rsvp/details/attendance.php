<?php
/**
 * Block: RSVP
 * Details Attendance
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details/attendance.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 4.12.3
 * @version 4.12.3
 */

$classes = [ 'tribe-tickets__rsvp-attendance-number', 'tribe-common-h4' ];
if ( ! $rsvp->show_description() || empty( $rsvp->description ) ) {
	$classes[] = 'tribe-tickets__rsvp-attendance-number--no-description';
}
?>
<div class="tribe-tickets__rsvp-attendance">
	<span <?php tribe_classes( $classes ); ?>>
		<?php echo esc_html( $rsvp->qty_sold ); ?>
	</span>
	<span class="tribe-tickets__rsvp-attendance-going tribe-common-h7 tribe-common-h--alt tribe-common-b3--min-medium">
		<?php echo esc_html_x( 'Going', 'Label below the attendance number', 'event-tickets' ); ?>
	</span>
</div>
