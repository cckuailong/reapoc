<?php
/**
 * Block: Tickets
 * Commerce Fields Woo
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/commerce/fields-woo.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @version 4.9.4
 *
 */

$provider     = $this->get( 'provider' );
$provider_id  = $this->get( 'provider_id' );
?>
<input
	type="hidden"
	id="wootickets_process"
	name="wootickets_process"
	value="1"
/>
