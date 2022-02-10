<?php
/**
 * Block: RSVP
 * Form base
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/form.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.12.0 Add $post_id to filter for hiding opt-outs.
 * @since   4.12.3 Add comments to help IDE auto-completion. Array short syntax.
 * @since   5.0.3 Add docblock vars and use $ticket->ID instead of duplicative $ticket_id.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template $this       Template object.
 * @var int                              $post_id    [Global] The current Post ID to which RSVPs are attached.
 * @var bool                             $must_login [Global] True if login is required and user is not logged in..
 * @var Tribe__Tickets__Ticket_Object    $ticket     The ticket object with provider set to RSVP.
 * @var string                           $going      The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$ticket_data = $tickets_handler->get_object_connections( $ticket->ID );

$event_id = $ticket_data->event;
?>
<form
	name="tribe-rsvp-form"
	data-product-id="<?php echo esc_attr( $ticket->ID ); ?>"
>
	<input type="hidden" name="product_id[]" value="<?php echo esc_attr( absint( $ticket->ID ) ); ?>">
	<input type="hidden" name="attendee[order_status]" value="<?php echo esc_attr( $going ); ?>">
	<!-- Maybe add nonce over here? Try to leave templates as clean as possible -->

	<div class="tribe-left">
		<?php if ( ! $must_login ) : ?>
			<?php $this->template( 'blocks/rsvp/form/quantity' ); ?>
		<?php endif; ?>
	</div>

	<div class="tribe-right">
		<?php $this->template( 'blocks/rsvp/form/error' ); ?>

		<?php if ( $must_login ) : ?>
			<?php $this->template( 'blocks/rsvp/form/submit-login', [ 'event_id' => $event_id ] ); ?>
		<?php else : ?>
			<?php $this->template( 'blocks/rsvp/form/details' ); ?>
			<?php $this->template( 'blocks/rsvp/form/attendee-meta' ); ?>
			<?php $this->template( 'blocks/rsvp/form/submit-button' ); ?>
		<?php endif; ?>
	</div>
</form>
