<?php
/**
 * Block: RSVP
 * Messages Success
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/messages/success.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var string|null $step The step the views are on.
 *
 * @since 4.12.3
 *
 * @version 5.0.0
 */

if ( ! in_array( $step, [ 'success', 'opt-in' ], true ) ) {
	return;
}
?>
<div class="tribe-tickets__rsvp-message tribe-tickets__rsvp-message--success tribe-common-b3">
	<?php $this->template( 'v2/components/icons/paper-plane', [ 'classes' => [ 'tribe-tickets__rsvp-message--success-icon' ] ] ); ?>

	<?php $this->template( 'v2/rsvp/messages/success/going' ); ?>

	<?php $this->template( 'v2/rsvp/messages/success/not-going' ); ?>

</div>
