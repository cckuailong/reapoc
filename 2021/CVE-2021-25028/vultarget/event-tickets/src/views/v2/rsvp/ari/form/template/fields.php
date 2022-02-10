<?php
/**
 * This template renders the RSVP Attendee Registration form fields template for JS to use.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/template/fields.php
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 5.0.0
 * @since 5.0.3 Updated to use the has_meta_enabled logic check and removed the old filter.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var int $post_id The post ID the RSVP is linked to.
 */

if ( ! $rsvp->has_meta_enabled() ) {
	return;
}
?>
<div class="tribe-tickets__form">

	<?php $this->template( 'v2/rsvp/ari/form/error', [ 'rsvp' => $rsvp ] ); ?>

	<?php
		/**
		 * Allows injection of meta fields in the RSVP ARI form template.
		 *
		 * @since 5.0.0
		 *
		 * @see  Tribe__Template\do_entry_point()
		 * @link https://docs.theeventscalendar.com/reference/classes/tribe__template/do_entry_point/
		 */
		$this->do_entry_point( 'rsvp_attendee_fields_template' );
	?>
</div>
