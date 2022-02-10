<?php
/**
 * Block: RSVP
 * Form Submit Login
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/submit-login.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9.3
 * @since   4.10.8 Fixed text domain for string.
 * @since   5.0.3 Add docblock vars and use $ticket->ID instead of duplicative $ticket_id.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template $this    Template object.
 * @var int                              $post_id [Global] The current Post ID to which RSVPs are attached.
 * @var Tribe__Tickets__Ticket_Object    $ticket  The ticket object with provider set to RSVP.
 * @var string                           $going   The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */
$event_id = $this->get( 'event_id' );
$going    = $this->get( 'going' );
// Note: the anchor tag is urlencoded here ('%23tribe-block__rsvp__ticket-') so it passes through the login redirect
?>
<a href="<?php echo esc_url( Tribe__Tickets__Tickets::get_login_url( $event_id ) . '?going=' . $going . '%23tribe-block__rsvp__ticket-' . $ticket->ID ); ?>">
	<?php esc_html_e( 'Log in to RSVP', 'event-tickets' ); ?>
</a>
