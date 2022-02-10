<?php
/**
 * Block: Attendees List
 * Description
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/attendees/description.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var	int	$attendees_total Total number of attendees confirmed for the event.
 *
 * @since 4.9.2
 * @version 4.11.3
 *
 */
$display_subtitle = $this->attr( 'displaySubtitle' );

if ( is_bool( $display_subtitle ) && ! $display_subtitle ) {
	return;
}

$post_id         = $this->get( 'post_id' );
$message         = _n( 'One person is attending %2$s', '%d people are attending %s', $attendees_total, 'event-tickets' );
?>
<p><?php
	echo esc_html(
		sprintf(
			$message,
			$attendees_total,
			get_the_title( $post_id )
		)
	); ?>
</p>