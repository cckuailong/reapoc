<?php
/**
 * Block: RSVP
 * Loader
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/loader.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 5.1.0 Fixed the template loading process.
 *
 * @version 5.1.0
 *
 */

/**
 * Allows filtering of extra classes used on the rsvp-block loader.
 *
 * @since  4.11.1
 *
 * @param  array $classes The array of classes that will be filtered.
 */
$loader_classes = apply_filters( 'tribe_rsvp_block_loader_classes', [ 'tribe-block__rsvp__loading' ] );

$this->template( 'components/loader', [ 'loader_classes' => $loader_classes ] );
