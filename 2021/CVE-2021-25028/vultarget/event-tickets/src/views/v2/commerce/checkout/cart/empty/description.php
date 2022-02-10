<?php
/**
 * Tickets Commerce: Checkout with Empty Cart Description
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/cart/empty/description.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.10
 *
 * @version 5.1.10
 *
 * @var \Tribe__Template $this                  [Global] Template object.
 * @var Module           $provider              [Global] The tickets provider instance.
 * @var string           $provider_id           [Global] The tickets provider class name.
 * @var array[]          $items                 [Global] List of Items on the cart to be checked out.
 * @var bool             $must_login            [Global] Whether login is required to buy tickets or not.
 * @var string           $login_url             [Global] The site's login URL.
 * @var string           $registration_url      [Global] The site's registration URL.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 * @var array[]          $gateways              [Global] An array with the gateways.
 * @var int              $gateways_active       [Global] The number of active gateways.
 * @var int              $section               Which Section that we are going to render for this table.
 * @var \WP_Post         $post                  Which Section that we are going to render for this table.
 */

if ( ! empty( $items ) ) {
	return;
}

if ( $is_tec_active ) {
	$description = sprintf(
		// Translators: %1$s: Opening `<a>` tag. %2$s: Plural `events` in lowercase. %3$s: Closing `</a>` tag. %4$s: Plural `tickets` in lowercase.
		__( 'Please %1$sbrowse %2$s%3$s and add %4$s to check out.', 'event-tickets' ),
		'<a href="' . tribe_events_get_url() . '" class="tribe-common-anchor-alt tribe-tickets__commerce-checkout-cart-empty-description-link">',
		tribe_get_event_label_plural_lowercase( 'tickets_commerce_checkout_empty_description' ),
		'</a>',
		tribe_get_ticket_label_plural_lowercase( 'tickets_commerce_checkout_empty_description' )
	);
} else {
	$description = sprintf(
		// Translators:  %1$s: Plural `tickets` in lowercase.
		__( 'Please add %1$s to check out.', 'event-tickets' ),
		tribe_get_ticket_label_plural_lowercase( 'tickets_commerce_checkout_empty_description' )
	);
}

?>
<div class="tribe-common-b1 tribe-tickets__commerce-checkout-cart-empty-description">
	<?php echo wp_kses_post( $description ); ?>
</div>

