<?php
/**
 * Block: RSVP
 * Form Cancel Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/cancel.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

?>
<button
	class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel"
	type="reset"
>
	<?php esc_html_e( 'Cancel', 'event-tickets' ); ?>
</button>
