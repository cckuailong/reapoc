<?php
/**
 * Block: RSVP
 * Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/content.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.12.0 Add $post_id to filter for hiding opt-outs.
 *
 * @version 4.12.0
 *
 * @var Tribe__Tickets__Editor__Template $this            The template instance.
 * @var Tribe__Tickets__Ticket_Object    $ticket          The ticket object.
 * @var int                              $threshold       The threshold value to show or hide quantity available.
 * @var int                              $available_count The quantity of Available tickets based on the Attendees number.
 * @var bool                             $show_unlimited  Whether to allow showing of "unlimited".
 * @var bool                             $is_unlimited    Whether the ticket has unlimited quantity.
 * @var int                              $post_id         The Post ID the RSVP is attached to.
 */

$going = tribe_get_request_var( 'going', '' );
?>
<div class="tribe-block__rsvp__content">

	<div class="tribe-block__rsvp__details__status">
		<?php $this->template( 'blocks/rsvp/details' ); ?>
		<?php $this->template( 'blocks/rsvp/status' ); ?>
	</div>

	<?php $this->template( 'blocks/rsvp/form' ); ?>

</div>
