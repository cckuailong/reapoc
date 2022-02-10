<?php
/**
 * Block: RSVP
 * Form Error
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/error.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.10.9 Uses new functions to get singular and plural texts.
 *
 * @version 4.11.3
 */
?>
<div class="tribe-block__rsvp__message__error">

	<?php
	echo esc_html(
		sprintf(
			__( 'Please fill in the %s confirmation name and email fields.', 'event-tickets' ), tribe_get_rsvp_label_singular( basename( __FILE__ ) )
		)
	); ?>

</div>