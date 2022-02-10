<?php
/**
 * Block: RSVP
 * Actions
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/actions.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var string|null $step The step the views are on.
 *
 * @since 4.12.3
 * @version 4.12.3
 */

?>
<div class="tribe-tickets__rsvp-actions-wrapper tribe-common-g-col">
	<div class="tribe-tickets__rsvp-actions">

		<?php if ( in_array( $step, [ 'success', 'opt-in' ], true ) ) : ?>

			<?php $this->template( 'v2/rsvp/actions/success', [ 'rsvp' => $rsvp ] ); ?>

		<?php elseif ( ! $rsvp->is_in_stock() ) : ?>

			<?php $this->template( 'v2/rsvp/actions/full', [ 'rsvp' => $rsvp ] ); ?>

		<?php else : ?>

			<?php $this->template( 'v2/rsvp/actions/rsvp', [ 'rsvp' => $rsvp ] ); ?>

		<?php endif; ?>
	</div>

</div>
