<?php
/**
 * This template renders the RSVP AR form guest.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/guest.php
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

?>

<div
	class="tribe-tickets__rsvp-ar-form-guest"
	data-guest-number="1"
	tabindex="0"
	role="tabpanel"
	id="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-1-tab"
	aria-labelledby="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-1"
>
	<?php $this->template( 'v2/rsvp/ari/form/title', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/fields', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/buttons', [ 'rsvp' => $rsvp ] ); ?>

</div>
