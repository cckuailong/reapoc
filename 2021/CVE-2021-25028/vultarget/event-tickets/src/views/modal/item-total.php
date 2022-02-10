<?php
/**
 * Modal: Item Total
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/modal/item-total.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.11.0
 * @since   4.11.3 Updated code comments.
 *
 * @version 4.11.3
 */

/** @var Tribe__Tickets__Commerce__Currency $tribe_commerce_currency */
$tribe_commerce_currency = tribe( 'tickets.commerce.currency' );
?>
<div class="tribe-common-b2 tribe-tickets__item__total__wrap">
	<span class="tribe-tickets__item__total">
		<?php echo $tribe_commerce_currency->get_formatted_currency_with_symbol( 0, $post_id, $provider->class_name ); ?>
	</span>
</div>