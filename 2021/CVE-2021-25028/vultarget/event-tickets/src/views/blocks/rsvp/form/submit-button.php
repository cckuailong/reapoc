<?php
/**
 * Block: RSVP
 * Form Submit Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/submit-button.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.10.9 Use function for submit button text.
 *
 * @version 4.11.3
 */

?>
<button
	type="submit"
	name="tickets_process"
	value="1"
	class="tribe-block__rsvp__submit-button"
>
	<?php
	echo esc_html(
		sprintf(
			_x( 'Submit %s', 'blocks rsvp form submit button', 'event-tickets' ),
			tribe_get_rsvp_label_singular( 'blocks_rsvp_form_submit_button' )
		)
	); ?>
</button>