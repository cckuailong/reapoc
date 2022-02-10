<?php
/**
 * Template for the guest icon.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/components/icons/guest.php
 *
 * See more documentation about our views templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @version 4.12.3
 *
 * @var array $classes Additional classes to add to the svg icon.
 */

$svg_classes = [ 'tribe-tickets-svgicon' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}
?>
<svg <?php tribe_classes( $svg_classes ); ?> xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 11 14"><defs/><path fill="#141827" stroke="#141827" stroke-width="1.1" d="M8.24995 3.575c0 1.32005-1.18823 2.475-2.75 2.475s-2.75-1.15495-2.75-2.475v-.55c0-1.32005 1.18823-2.475 2.75-2.475s2.75 1.15495 2.75 2.475v.55zM.55 11.5868c0-2.12633 1.7237-3.85003 3.85-3.85003h2.2c2.1263 0 3.85 1.7237 3.85 3.85003v1.7435H.55v-1.7435z"/></svg>