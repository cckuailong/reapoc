<?php
/**
 * Block: RSVP
 * Actions - Success - Label Tooltip
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/actions/success/tooltip.php
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

?>
<div class="tribe-common-a11y-hidden">
	<div
		class="tribe-common-b3"
		id="tribe-tickets-tooltip-content-<?php echo esc_attr( $rsvp->ID ); ?>"
		role="tooltip"
	>
		<?php esc_html_e( 'Enabling this allows your gravatar and name to be present for other attendees to see.', 'event-tickets' ); ?>
	</div>
</div>
