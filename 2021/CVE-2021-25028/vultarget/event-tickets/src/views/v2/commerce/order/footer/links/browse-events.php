<?php
/**
 * Tickets Commerce: Success Order Page Footer Links > Browse events.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/order/footer/links/browse-events.php
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
 * @var \WP_Post         $order                 [Global] The order object.
 * @var int              $order_id              [Global] The order ID.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 */

if ( empty( $is_tec_active ) ) {
	return;
}
?>
<a
	class="tribe-common-anchor-alt tribe-tickets__commerce-order-footer-link tribe-tickets__commerce-order-footer-link--browse-events"
	href="<?php echo tribe_events_get_url(); // phpcs:ignore ?>"
>
	<?php
		echo esc_html(
			sprintf(
				// Translators: %1$s: Plural `events` in lowercase.
				__( 'browse more %1$s', 'event-tickets' ),
				tribe_get_event_label_plural_lowercase( 'tickets_commerce_order_footer_link' ) // phpcs:ignore
			)
		);
		?>
</a>
