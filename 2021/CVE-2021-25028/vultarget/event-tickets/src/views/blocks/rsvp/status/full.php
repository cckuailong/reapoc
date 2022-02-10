<?php
/**
 * Block: RSVP
 * Status Full
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/status/full.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.10.9 Use function for text.
 *
 * @version 4.10.9
 */

echo esc_html( sprintf( _x( '%s Full', 'blocks rsvp status full', 'event-tickets' ), tribe_get_rsvp_label_singular( 'blocks_rsvp_status_full' ) ) );