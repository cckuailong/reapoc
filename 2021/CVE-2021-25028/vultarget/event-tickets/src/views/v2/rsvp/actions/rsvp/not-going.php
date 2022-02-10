<?php
/**
 * Block: RSVP
 * Actions - RSVP - Not Going
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/actions/rsvp/not-going.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var bool $must_login Whether the user has to login to RSVP or not.
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 4.12.3
 * @version 4.12.3
 */

/**
 * @todo: Create a hook for the get_ticket method in order to set dynamic or custom properties into
 * the instance variable so we can set a new one called $ticket->show_not_going.
 *
 * Method is located on:
 * - https://github.com/the-events-calendar/event-tickets/blob/9e77f61f191bbc86ee9ec9a0277ed7dde66ba0d8/src/Tribe/RSVP.php#L1130
 *
 * For now we need to access directly the value of the meta field in order to render this field.
 */
$show_not_going = tribe_is_truthy(
	get_post_meta( $rsvp->ID, '_tribe_ticket_show_not_going', true )
);

if ( ! $show_not_going ) {
	return;
}

?>
<div class="tribe-tickets__rsvp-actions-rsvp-not-going">
	<button
		class="tribe-common-cta tribe-common-cta--alt tribe-tickets__rsvp-actions-button-not-going"
		<?php tribe_disabled( $must_login ); ?>
	>
		<?php echo esc_html_x( "Can't go", 'Label for the RSVP "can\'t go" version of the not going button', 'event-tickets' ); ?>
	</button>
</div>
