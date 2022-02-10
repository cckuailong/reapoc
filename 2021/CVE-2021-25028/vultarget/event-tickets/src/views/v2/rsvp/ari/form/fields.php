<?php
/**
 * This template renders the RSVP AR form fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/fields.php
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var int $post_id The post ID the RSVP is linked to.
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

?>
<div class="tribe-tickets__form">

	<?php $this->template( 'v2/rsvp/ari/form/error', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/fields/name', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/fields/email', [ 'rsvp' => $rsvp ] ); ?>

	<?php $this->template( 'v2/rsvp/ari/form/fields/meta', [ 'rsvp' => $rsvp ] ); ?>

</div>
