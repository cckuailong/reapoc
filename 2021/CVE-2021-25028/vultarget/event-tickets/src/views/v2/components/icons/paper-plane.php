<?php
/**
 * Template for the paper plane icon.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/components/icons/paper-plane.php
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
<svg <?php tribe_classes( $svg_classes ); ?> xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 15 15"><defs/><path stroke="#111" d="M14 1L6.5 8.5"/><path stroke="#111" stroke-linecap="square" d="M14 1L9.14286 13 6.85714 8.14286 2 5.85714 14 1z" clip-rule="evenodd"/></svg>