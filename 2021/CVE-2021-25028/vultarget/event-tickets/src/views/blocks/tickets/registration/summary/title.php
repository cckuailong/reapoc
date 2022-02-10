
<?php
/**
 * Block: Tickets
 * Registration Summary Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/summary/title.php
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
<div class="tribe-tickets__registration__title">
	<header>
		<h2 class="tribe-common-h4 tribe-common-h3--min-medium"><?php
			echo esc_html(
				sprintf(
					__( '%s Registration', 'event-tickets' ),
					tribe_get_ticket_label_singular( basename( __FILE__ ) )
				)
			); ?>
		</h2>
	</header>
</div>