<?php
/**
 * Block: RSVP
 * Details Description
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details/description.php
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

if ( ! $rsvp->show_description() ) {
	return;
}
?>
<div class="tribe-tickets__rsvp-description tribe-common-h6 tribe-common-h--alt tribe-common-b3--min-medium">
	<?php echo wpautop( wp_kses_post( $rsvp->description ) ); ?>
</div>
