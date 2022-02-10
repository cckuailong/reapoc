<?php
/**
 * Block: RSVP
 * Form Details
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/details.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.11.5 Corrected the template override instructions in template comments.
 * @since   4.12.0 Add $post_id to filter for hiding opt-outs.
 * @since   5.0.3 Add vars to docblock and removed duplicative vars.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template $this    Template object.
 * @var int                              $post_id [Global] The current Post ID to which RSVPs are attached.
 * @var Tribe__Tickets__Ticket_Object    $ticket  The ticket object with provider set to RSVP.
 * @var string                           $going   The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */

$this->template( 'blocks/rsvp/form/name' );
$this->template( 'blocks/rsvp/form/email' );
$this->template( 'blocks/rsvp/form/opt-out' );
