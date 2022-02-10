<?php
/**
 * Block: RSVP
 * Form title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/title.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

$going = $this->get( 'going' );

if ( 'going' === $going ) {
	$this->template( 'v2/rsvp/form/going/title', [ 'rsvp' => $rsvp ] );
} else {
	$this->template( 'v2/rsvp/form/not-going/title', [ 'rsvp' => $rsvp ] );
}
