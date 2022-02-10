<?php
/**
 * Block: RSVP
 * Details
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details.php
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
<div class="tribe-tickets__rsvp-details-wrapper tribe-common-g-col">
	<div class="tribe-tickets__rsvp-details">
		<?php $this->template( 'v2/rsvp/details/title', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/details/description', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/details/attendance', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/details/availability', [ 'rsvp' => $rsvp ] ); ?>
	</div>
</div>
