<?php
/**
 * Modal: Cart
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/modal/cart.php
 *
 * @since 4.11.0
 *
 * @version 4.11.0
 *
 */

// We don't display anything if there is no provider or tickets
if ( ! $provider || empty( $tickets ) ) {
	return false;
}

$cart_classes = [
	'tribe-modal-cart',
	'tribe-modal__cart',
	'tribe-common',
];


/** @var Tribe__Tickets__Commerce__Currency $currency */
$currency        = tribe( 'tickets.commerce.currency' );

?>
<div
	id="tribe-modal__cart"
	action="<?php echo esc_url( $cart_url ) ?>"
	<?php tribe_classes( $cart_classes ); ?>
	method="post"
	enctype='multipart/form-data'
	data-provider="<?php echo esc_attr( $provider->class_name ); ?>"
	autocomplete="off"
	novalidate
>
	<?php $template_obj->template( 'blocks/tickets/commerce/fields', [ 'provider' => $provider, 'provider_id' => $provider_id ] ); ?>

	<?php if ( $has_tickets_on_sale ) : ?>
		<?php foreach ( $tickets_on_sale as $key => $ticket ) : ?>
		<?php $currency_symbol     = $currency->get_currency_symbol( $ticket->ID, true ); ?>
			<?php $template_obj->template( 'blocks/tickets/item', [ 'ticket' => $ticket, 'key' => $key, 'is_modal' => true, 'currency_symbol' => $currency_symbol ] ); ?>
		<?php endforeach; ?>
	<?php endif; ?>


	<?php
	/**
	 * Allows filtering of text used in the loader
	 *
	 * @since  4.11.0
	 *
	 * @param  string $value     The value that will be filtered.
	 */
	$text    = apply_filters( 'tribe_tickets_loader_text', __( 'One Moment...', 'event-tickets' ) );
	/**
	 * Allows filtering of extra classes used on the modal loader
	 *
	 * @since  4.11.0
	 *
	 * @param  array $classes The array of classes that will be filtered.
	 */
	$loader_classes = apply_filters( 'tribe_tickets_modal_loader_classes', [ 'tribe-tickets-loader__modal' ] );
	include Tribe__Tickets__Templates::get_template_hierarchy( 'components/loader.php' );
	?>
	<?php $template_obj->template( 'blocks/tickets/footer', [ 'is_modal' => true ] ); ?>
</div>
