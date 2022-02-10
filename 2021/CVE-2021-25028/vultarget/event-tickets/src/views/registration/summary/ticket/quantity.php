<?php
/**
 * This template renders the summary ticket quantity
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/ticket/quantity.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets__item__quantity">
	<?php echo esc_html( $ticket['qty'] ); ?>
</div>
