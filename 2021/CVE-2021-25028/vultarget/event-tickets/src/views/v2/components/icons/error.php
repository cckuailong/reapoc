<?php
/**
 * Template for the error icon.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/components/icons/error.php
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
<svg <?php tribe_classes( $svg_classes ); ?> xmlns="http://www.w3.org/2000/svg" width="18" height="18"><g fill="none" fill-rule="evenodd" transform="translate(1 1)"><circle cx="8" cy="8" r="7.467" stroke="#141827" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/><circle id="dot" cx="8" cy="11.733" r="1.067" fill="#141827" fill-rule="nonzero"/><path stroke="#141827" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 3.733v4.8"/></g></svg>