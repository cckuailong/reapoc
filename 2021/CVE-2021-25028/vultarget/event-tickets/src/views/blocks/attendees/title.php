<?php
/**
 * Block: Attendees List
 * Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/attendees/title.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9.2
 * @since   5.0.2 Fix template path in documentation block.
 * @version 5.0.2
 *
 */
$display_title = $this->attr( 'displayTitle' );

if ( is_bool( $display_title ) && ! $display_title ) {
	return;
}
?>
<h2 class="tribe-block__attendees__title"><?php echo esc_html( $title );?></h2>
