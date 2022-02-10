<?php
/**
 * Block: Tickets
 * Extra column
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/tickets/item/extra.php
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
 * @var Tribe__Tickets__Editor__Template   $this                Template object.
 * @var null|bool                          $is_modal            [Global] Whether the modal is enabled.
 * @var int                                $post_id             [Global] The current Post ID to which tickets are attached.
 * @var array                              $attributes          [Global] Ticket attributes (could be empty).
 * @var Tribe__Tickets__Tickets            $provider            [Global] The tickets provider class.
 * @var string                             $provider_id         [Global] The tickets provider class name.
 * @var string                             $cart_url            [Global] Link to Cart (could be empty).
 * @var Tribe__Tickets__Ticket_Object[]    $tickets             [Global] List of tickets.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale     [Global] List of tickets on sale.
 * @var bool                               $has_tickets_on_sale [Global] True if the event has any tickets on sale.
 * @var bool                               $is_sale_past        [Global] True if tickets' sale dates are all in the past.
 * @var bool                               $is_sale_future      [Global] True if no ticket sale dates have started yet.
 * @var Tribe__Tickets__Commerce__Currency $currency            [Global] Tribe Currency object.
 * @var Tribe__Tickets__Ticket_Object      $ticket              The ticket object.
 * @var int                                $key                 Ticket item index.
 * @var bool                               $show_unlimited      Whether to allow showing of "unlimited".
 * @var int                                $available_count     Quantity available based on the Attendees number.
 * @var bool                               $is_unlimited        Whether the ticket has unlimited quantity.
 * @var int                                $max_at_a_time       The maximum quantity able to be purchased in a single Add to Cart action.
 * @var Tribe__Tickets__Privacy            $privacy             Tribe Privacy instance.
 *
 * If Ticket:
 * @var Tribe__Tickets__Editor__Template   $this                        [Global] Template object.
 * @var int                                $post_id                     [Global] The current Post ID to which tickets are attached.
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
 * @var Tribe__Tickets__Privacy            $privacy                     [Global] Tribe Tickets Privacy object.
 * @var int                                $threshold                   [Global] The count at which "number of tickets left" message appears.
 * @var bool                               $show_original_price_on_sale [Global] Show original price on sale.
 * @var null|bool                          $is_mini                     [Global] If in "mini cart" context.
 * @var null|bool                          $is_modal                    [Global] Whether the modal is enabled.
 * @var string                             $submit_button_name          [Global] The button name for the tickets block.
 * @var string                             $cart_url                    [Global] Link to Cart (could be empty).
 * @var string                             $checkout_url                [Global] Link to Checkout (could be empty).
 * @var Tribe__Tickets__Ticket_Object      $ticket                      The ticket object.
 * @var int                                $key                         Ticket Item index.
 * @var string                             $data_available              Boolean string.
 * @var bool                               $has_shared_cap              True if ticket has shared capacity.
 * @var string                             $data_has_shared_cap         True text if ticket has shared capacity.
 * @var string                             $currency_symbol             The ticket's currency symbol, e.g. '$'.
 * @var bool                               $show_unlimited              Whether to allow showing of "unlimited".
 * @var int                                $available_count             The quantity of Available tickets based on the Attendees number.
 * @var bool                               $is_unlimited                Whether the ticket has unlimited quantity.
 * @var int                                $max_at_a_time               The maximum quantity able to be purchased in a single Add to Cart action.
 */

$has_suffix = ! empty( $ticket->price_suffix );

$classes = [
	'tribe-tickets__tickets-item-extra',
	'tribe-tickets__tickets-item-extra--price-suffix' => $has_suffix,
];

?>
<div <?php tribe_classes( $classes ); ?>>

	<?php $this->template( 'v2/tickets/item/extra/price' ); ?>

	<?php $this->template( 'v2/tickets/item/extra/available' ); ?>

	<?php $this->template( 'v2/tickets/item/extra/description-toggle' ); ?>

</div>
