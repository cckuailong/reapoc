<?php
/**
 * This template renders the summary ticket price
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/ticket/price.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets__item__price">
	<?php echo $ticket['provider']->get_price_html( $ticket['id'] ); ?>
</div>
