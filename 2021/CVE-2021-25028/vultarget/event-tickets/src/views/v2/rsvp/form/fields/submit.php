<?php
/**
 * Block: RSVP
 * Form Submit Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/submit.php
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
	class="tribe-common-c-btn tribe-tickets__rsvp-form-button"
	type="submit"
>
	<?php esc_html_e( 'Finish', 'event-tickets' ); ?>
</button>
