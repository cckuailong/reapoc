<?php
/**
 * Block: Tickets
 * Form Opt-Out
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/tickets/opt-out/hidden.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.0.3
 *
 * @version 5.0.3
 *
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
 * @var Tribe__Tickets__Ticket_Object      $ticket                      Ticket Item.
 */

if ( empty( $is_modal ) ) {
	return;
}

/**
 * Use this filter to hide the Attendees List Optout
 *
 * @since 4.9
 *
 * @param bool
 */
$hide_attendee_list_optout = apply_filters( 'tribe_tickets_plus_hide_attendees_list_optout', false );

if ( $hide_attendee_list_optout ) {
	// Force opt-out.
	?>
	<input name="attendee[optout]" value="1" type="hidden" />
	<?php
	return;
}
?>

<input
	id="tribe-tickets-attendees-list-optout-<?php echo esc_attr( $ticket->ID ); ?>-modal"
	class="tribe-tickets__tickets-item-quantity"
	name="attendee[optout]"
	value="1"
	type="hidden"
/>
