<?php
/**
 * This template renders the summary tickets header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/tickets-header.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets__header">
	<div class="tribe-tickets__registration__tickets__header__summary">
		<?php esc_html_e( 'Ticket summary', 'event-tickets' ); ?>
	</div>
	<div class="tribe-tickets__registration__tickets__header__price">
		<?php esc_html_e( 'Price', 'event-tickets' ); ?>
	</div>
</div>
