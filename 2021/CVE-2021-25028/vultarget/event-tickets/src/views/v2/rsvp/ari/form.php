<?php
/**
 * Block: RSVP
 * ARI Form
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var WP_Post|int $post_id The post object or ID.
 *
 * @since 4.12.3
 *
 * @version 5.0.0
 */

?>
<div class="tribe-tickets__rsvp-ar-form">

	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][ticket_id]" value="<?php echo esc_attr( absint( $rsvp->ID ) ); ?>">
	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][order_status]" value="<?php echo esc_attr( $going ); ?>">
	<input type="hidden" name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][optout]" value="1">

	<?php $this->template( 'v2/rsvp/ari/form/guest', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/guest-template', [ 'rsvp' => $rsvp ] ); ?>

</div>
