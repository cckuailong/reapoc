<?php
/**
 * Tickets Commerce: Checkout Page Header Links > Back
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/header/links/back.php
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
 * @var array[]          $sections              [Global] Which events we have tickets for.
 * @var bool             $must_login            [Global] Whether login is required to buy tickets or not.
 * @var string           $login_url             [Global] The site's login URL.
 * @var string           $registration_url      [Global] The site's registration URL.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 * @var array[]          $gateways              [Global] An array with the gateways.
 * @var int              $gateways_active       [Global] The number of active gateways.
 */

if ( empty( $items ) ) {
	return;
}

$anchor_text = $is_tec_active ?
	sprintf(
		// Translators: %1$s: Singular `event` in lowercase.
		__( 'back to %1$s', 'event-tickets' ),
		tribe_get_event_label_singular_lowercase( 'tickets_commerce_checkout_header_link' )
	)
	: __( 'back', 'event-tickets' );
?>

<a
	class="tribe-common-anchor-alt tribe-tickets__commerce-checkout-header-link-back-to-event"
	href="<?php the_permalink( $sections[ key( $sections ) ] ); ?>"
><?php echo esc_html( $anchor_text ); ?></a>
