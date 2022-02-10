<?php
/**
 * Block: RSVP
 * Inactive Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/content-inactive.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.10.9 Use function for text.
 *
 * @version 4.10.9
 */

$message = $this->get( 'all_past' )
	? sprintf( _x( '%s are no longer available', 'RSVP block inactive content in the past', 'event-tickets' ), tribe_get_rsvp_label_plural( 'block_inactive_content_past' ) )
	: sprintf( _x( '%s are not yet available', 'RSVP block inactive content', 'event-tickets' ), tribe_get_rsvp_label_plural( 'block_inactive_content' ) );
?>
<div class="tribe-block__rsvp__content tribe-block__rsvp__content--inactive">
	<div class="tribe-block__rsvp__details__status">
		<div class="tribe-block__rsvp__details">
			<?php echo esc_html( $message ) ?>
		</div>
	</div>
</div>
