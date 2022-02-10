<?php
/**
 * This template renders the summary ticket title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/ticket/title.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
$ticket_data = Tribe__Tickets__Tickets::load_ticket_object( $ticket['id'] );
?>
<div class="tribe-tickets__registration__tickets__item__title">
	<?php echo $ticket_data->name; ?>
</div>
