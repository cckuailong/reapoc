<?php
/**
 * Block: RSVP
 * Details Availability
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details/availability.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

use Tribe__Date_Utils as Dates;

$is_unlimited = - 1 === $rsvp->remaining();
$is_in_stock  = $rsvp->is_in_stock();

$days_to_rsvp = Dates::date_diff( current_time( 'mysql' ), $rsvp->end_date );
$days_to_rsvp = floor( $days_to_rsvp );

// Only show Days to RSVP if it is happening within the next week and is in stock.
if ( ! $is_in_stock || 6 < $days_to_rsvp ) {
	$days_to_rsvp = false;
}
?>
<div class="tribe-tickets__rsvp-availability tribe-common-h6 tribe-common-h--alt tribe-common-b3--min-medium">
	<?php if ( ! $is_in_stock ) : ?>
		<?php $this->template( 'v2/rsvp/details/availability/full', [ 'rsvp' => $rsvp ] ); ?>
	<?php elseif ( $is_unlimited ) : ?>
		<?php
		$this->template(
			'v2/rsvp/details/availability/unlimited',
			[
				'is_unlimited' => $is_unlimited,
				'days_to_rsvp' => $days_to_rsvp,
			]
		);
		?>
	<?php else : ?>
		<?php
		$this->template(
			'v2/rsvp/details/availability/remaining',
			[
				'rsvp'         => $rsvp,
				'days_to_rsvp' => $days_to_rsvp,
			]
		);
		?>
	<?php endif; ?>

	<?php if ( false !== $days_to_rsvp ) : ?>
		<?php
		$this->template(
			'v2/rsvp/details/availability/days-to-rsvp',
			[
				'rsvp'         => $rsvp,
				'days_to_rsvp' => $days_to_rsvp,
			]
		);
		?>
	<?php endif; ?>
</div>
