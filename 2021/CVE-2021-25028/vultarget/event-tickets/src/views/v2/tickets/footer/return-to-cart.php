<?php
/**
 * Block: Tickets
 * Footer "Return to cart"
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/tickets/footer/return-to-cart.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.0.3
 *
 * @version 5.0.3
 *
 * If RSVP:
 * @var WP_Post|int                        $post_id                     The post object or ID.
 * @var Tribe__Tickets__Tickets            $provider                    The tickets provider class.
 * @var string                             $provider_id                 The tickets provider class name.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets                     List of tickets.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale             List of tickets on sale.
 * @var Tribe__Tickets__Commerce__Currency $currency                    The Currency instance.
 * @var boolean                            $is_mini                     Context of template.
 * @var Tribe__Tickets__Ticket_Object      $ticket                      The ticket.
 * @var int                                $key                         The ticket key.
 *
 * If Ticket, some of the above but not all.
 * @var Tribe__Tickets__Editor__Template   $this                        [Global] Template object.
 * @var Tribe__Tickets__Tickets            $provider                    [Global] The tickets provider class.
 * @var string                             $provider_id                 [Global] The tickets provider class name.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets                     [Global] List of tickets.
 * @var array                              $cart_classes                [Global] CSS classes.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale             [Global] List of tickets on sale.
 * @var bool                               $has_tickets_on_sale         [Global] True if the event has any tickets on sale.
 * @var bool                               $is_sale_past                [Global] True if tickets' sale dates are all in the past.
 * @var bool                               $is_sale_future              [Global] True if no ticket sale dates have started yet.
 * @var Tribe__Tickets__Commerce__Currency $currency                    [Global] Tribe Currency object.
 * @var Tribe__Tickets__Tickets_Handler    $handler                     [Global] Tribe Tickets Handler object.
 * @var int                                $threshold                   [Global] The count at which "number of tickets left" message appears.
 * @var bool                               $show_original_price_on_sale [Global] Show original price on sale.
 * @var null|bool                          $is_mini                     [Global] If in "mini cart" context.
 * @var null|bool                          $is_modal                    [Global] Whether the modal is enabled.
 * @var string                             $submit_button_name          [Global] The button name for the tickets block.
 * @var string                             $cart_url                    [Global] Link to Cart (could be empty).
 * @var string                             $checkout_url                [Global] Link to Checkout (could be empty).
 * @var Tribe__Tickets__Ticket_Object      $ticket                      The ticket object.
 * @var int                                $key                         Ticket Item index.
 * @var int                                $available_count             Quantity available based on the Attendees number.
 * @var bool                               $is_unlimited                Whether the ticket has unlimited quantity.
 * @var int                                $max_at_a_time               The maximum quantity able to be purchased in a single Add to Cart action.
 * @var Tribe__Tickets__Privacy            $privacy                     Tribe Privacy instance.
 */

if ( method_exists( $provider, 'get_cart_url' ) ) {
	$cart_url = $provider->get_cart_url( $post_id );
} else {
	$cart_url = '';
}

if ( method_exists( $provider, 'get_checkout_url' ) ) {
	$checkout_url = $provider->get_checkout_url( $post_id );
} else {
	$checkout_url = '';
}

if (
	! $is_mini
	|| strtok( $cart_url, '?' ) === strtok( $checkout_url, '?' ) // If URLs are the same before the '?'.
) {
	return;
}

?>
<a class="tribe-common-b2 tribe-tickets__tickets-footer-back-link" href="<?php echo esc_url( $cart_url ); ?>">
	<?php esc_html_e( 'Return to Cart', 'event-tickets' ); ?>
</a>
