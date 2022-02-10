<?php
/**
 * This template renders the RSVP ticket form quantity input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/quantity.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.10.9 Uses new functions to get singular and plural texts.
 * @since   4.11.5 Added template override instructions in template comments.
 * @since   5.0.3 Add vars to docblock and removed duplicative vars.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template $this    Template object.
 * @var int                              $post_id [Global] The current Post ID to which RSVPs are attached.
 * @var Tribe__Tickets__Ticket_Object    $ticket  The ticket object with provider set to RSVP.
 * @var string                           $going   The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */
?>
<div class="tribe-block__rsvp__number-input">
	<div class="tribe-block__rsvp__number-input-inner">
		<?php $this->template( 'blocks/rsvp/form/quantity-minus' ); ?>

		<?php $this->template( 'blocks/rsvp/form/quantity-input' ); ?>

		<?php $this->template( 'blocks/rsvp/form/quantity-plus' ); ?>
	</div>
	<span class="tribe-block__rsvp__number-input-label">
		<?php echo esc_html( tribe_get_rsvp_label_plural( 'number_input_label' ) ); ?>
	</span>
</div>