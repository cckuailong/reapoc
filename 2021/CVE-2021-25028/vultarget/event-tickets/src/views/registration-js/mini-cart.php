<?php
/**
 * AR: Mini-Cart
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/mini-cart.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.11.0
 * @since   4.12.0 Prevent potential errors when $provider_obj is not valid.
 * @since   4.12.3 Update detecting ticket provider to account for possibly inactive provider. Rename $provider_obj to
 *              the more accurately named $cart_provider.
 * @since   5.0.4  Pass must_login variable to blocks/tickets/item template
 *
 * @version 5.0.4
 */
$provider = $this->get( 'provider' ) ?: tribe_get_request_var( tribe_tickets_get_provider_query_slug() );

if ( empty( $provider ) ) {
	$event_keys = array_keys( $events );
	$event_key  = array_shift( $event_keys );
	$provider   = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $event_key );
	$provider   = ! empty( $provider ) ? $provider::ATTENDEE_OBJECT : '';
}

/** @var Tribe__Tickets__Attendee_Registration__View $view */
$view          = tribe( 'tickets.attendee_registration.view' );
$cart_provider = $view->get_cart_provider( $provider );

if ( empty( $cart_provider ) ) {
	$provider_class = '';
} else {
	$provider_class = $cart_provider->class_name;
}

$tickets = $this->get( 'tickets' );

$cart_classes = [
	'tribe-common',
	'tribe-tickets__mini-cart',
];

/** @var Tribe__Tickets__Commerce__Currency $currency */
$currency = tribe( 'tickets.commerce.currency' );
$tickets             = $this->get( 'tickets', [] );
$cart_url            = $this->get( 'cart_url' );
?>
<aside id="tribe-tickets__mini-cart" <?php tribe_classes( $cart_classes ); ?> data-provider="<?php echo esc_attr( $provider_class ); ?>">
	<h3 class="tribe-common-h6 tribe-common-h5--min-medium tribe-common-h--alt tribe-tickets__mini-cart__title"><?php echo esc_html_x( 'Ticket Summary', 'Attendee registration mini-cart/ticket summary title.', 'event-tickets'); ?></h3>
		<?php foreach ( $events as $event_id => $tickets ) : ?>
			<?php foreach ( $tickets as $key => $ticket ) : ?>
				<?php if ( $provider_class !== $ticket['provider']->class_name ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php $currency_symbol = $currency->get_currency_symbol( $ticket['id'], true ); ?>
				<?php $this->template(
					'blocks/tickets/item',
					[
						'ticket'          => $cart_provider->get_ticket( $event_id, $ticket['id'] ),
						'key'             => $key,
						'is_mini'         => true,
						'must_login'      => ! is_user_logged_in() && $cart_provider->login_required(),
						'currency_symbol' => $currency_symbol,
						'provider'        => $cart_provider,
						'post_id'         => $event_id,
					]
				); ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php $this->template( 'blocks/tickets/footer', [ 'is_mini' => true, 'provider' => $cart_provider ] ); ?>
</aside>

<?php foreach ( $events as $event_id => $tickets ) : ?>
	<?php
	$event_provider = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $event_id );

	if (
		empty( $event_provider )
		|| $provider_class !== $event_provider->class_name
	) {
		continue;
	}

	$this->template(
		'registration-js/attendees/content',
		[
			'event_id' => $event_id,
			'tickets'  => $tickets,
			'provider' => $cart_provider,
		]
	);
	?>
<?php endforeach; ?>
