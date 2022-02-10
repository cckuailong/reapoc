<?php
/**
 * Block: RSVP
 * Form base
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/form.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 * @since 5.0.0 Updated the input name used for submitting.
 *
 * @version 5.0.0
 */

$going = $this->get( 'going' );
?>

<form
	name="tribe-tickets-rsvp-form"
	data-rsvp-id="<?php echo esc_attr( $rsvp->ID ); ?>"
>
	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][ticket_id]" value="<?php echo esc_attr( absint( $rsvp->ID ) ); ?>">
	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][order_status]" value="<?php echo esc_attr( $going ); ?>">
	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][optout]" value="1">

	<div class="tribe-tickets__rsvp-form-wrapper">

		<?php $this->template( 'v2/rsvp/form/title', [ 'rsvp' => $rsvp, 'going' => $going ] ); ?>

		<div class="tribe-tickets__rsvp-form-content tribe-tickets__form">

			<?php $this->template( 'v2/rsvp/form/fields', [ 'rsvp' => $rsvp, 'going' => $going ] ); ?>

			<?php $this->template( 'v2/rsvp/form/buttons', [ 'rsvp' => $rsvp, 'going' => $going ] ); ?>

		</div>

	</div>

</form>
