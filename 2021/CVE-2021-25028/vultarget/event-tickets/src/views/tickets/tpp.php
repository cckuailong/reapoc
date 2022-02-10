<?php
/**
 * This template renders the Tribe Commerce ticket form
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/tickets/tpp.php
 *
 * @deprecated 4.11.0
 *
 * @since      4.5
 * @since      4.7 Make the ticket form more readable.
 * @since      4.7.6 Add support for showing description option.
 * @since      4.8.2 Add date_in_range() logic so past tickets do not show.
 * @since      4.9.3 Display login link if visitor is logged out and logging in is required to purchase.
 * @since      4.10.8 Removed the date_in_range() check per ticket, since it now happens upstream. Better checking of max quantity available.
 * @since      4.10.10 Use customizable ticket name functions.
 * @since      4.11.1 Corrected amount of available/remaining tickets when threshold is empty.
 * @since      4.11.3 Updated the button to include a type - helps avoid submitting forms unintentionally.
 * @since      4.11.3 Changed button ID to match the format of the non-tpp buttons. (`tribe-tickets` instead of `buy-tickets`)
 * @since      4.11.5 Display total available separately from setting max allowed to purchase at once and avoid the
 *                    potential of `$readable_amount` being a undefined variable.
 *
 * @version    4.11.3
 *
 * @var bool $must_login
 * @var bool $display_login_link
 */

$is_there_any_product_to_sell = false;

/** @var Tribe__Tickets__Commerce__PayPal__Main $commerce */
$commerce = tribe( 'tickets.commerce.paypal' );

$messages       = $commerce->get_messages();
$messages_class = $messages ? 'tribe-tpp-message-display' : '';
$now            = time();
$cart_url       = '';

/** @var Tribe__Settings_Manager $settings_manager */
$settings_manager = tribe( 'settings.manager' );

$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

/**
 * Overwrites the threshold to display "# tickets left".
 *
 * @since 4.11.1
 *
 * @param array $data      Ticket data.
 * @param int   $post_id   WP_Post/Event ID.
 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
 */
$threshold = absint( apply_filters( 'tribe_display_tickets_block_tickets_left_threshold', $threshold, tribe_events_get_ticket_event( $ticket ) ) );
?>
<form
	id="tpp-tribe-tickets"
	action="<?php echo esc_url( $cart_url ); ?>"
	class="tribe-tickets-tpp cart <?php echo esc_attr( $messages_class ); ?>"
	method="post"
	enctype='multipart/form-data'
>
	<input type="hidden" name="provider" value="Tribe__Tickets__Commerce__PayPal__Main">
	<input type="hidden" name="add" value="1">
	<h2 class="tribe-events-tickets-title tribe--tpp">
		<?php echo esc_html( tribe_get_ticket_label_plural( basename( __FILE__ ) ) ); ?>
	</h2>

	<div class="tribe-tpp-messages">
		<?php
		if ( $messages ) {
			foreach ( $messages as $message ) {
				?>
				<div class="tribe-tpp-message tribe-tpp-message-<?php echo esc_attr( $message->type ); ?>">
					<?php echo esc_html( $message->message ); ?>
				</div>
				<?php
			}//end foreach
		}//end if
		?>

		<div
			class="tribe-tpp-message tribe-tpp-message-error tribe-tpp-message-confirmation-error" style="display:none;">
			<?php esc_html_e( 'Please fill in the ticket confirmation name and email fields.', 'event-tickets' ); ?>
		</div>
	</div>

	<table class="tribe-events-tickets tribe-events-tickets-tpp">
		<?php
		/** @var Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		$item_counter = 1;

		foreach ( $tickets as $ticket ) {
			if ( ! $ticket instanceof Tribe__Tickets__Ticket_Object ) {
				continue;
			}

			// if the ticket isn't a Tribe Commerce ticket, then let's skip it
			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| 'Tribe__Tickets__Commerce__PayPal__Main' !== $ticket->provider_class
			) {
				continue;
			}

			$available = $ticket->available();

			/**
			 * Allows hiding of "unlimited" to be toggled on/off conditionally.
			 *
			 * @since 4.11.1
			 *
			 * @param int $show_unlimited allow showing of "unlimited".
			 */
			$show_unlimited = apply_filters( 'tribe_tickets_block_show_unlimited_availability', false, $available );

			$is_there_any_product_to_sell = 0 !== $available;

			$max_at_a_time = $handler->get_ticket_max_purchase( $ticket->ID );
			?>
			<tr>
				<td class="tribe-ticket quantity" data-product-id="<?php echo esc_attr( $ticket->ID ); ?>">
					<input type="hidden" name="product_id[]" value="<?php echo absint( $ticket->ID ); ?>">
					<?php if ( $is_there_any_product_to_sell ) : ?>
						<input
							type="number"
							class="tribe-tickets-quantity qty"
							step="1"
							min="0"
							max="<?php echo esc_attr( $max_at_a_time ); ?>"
							name="quantity_<?php echo absint( $ticket->ID ); ?>"
							value="0"
							<?php disabled( $must_login ); ?>
						>
						<?php
						$readable_amount = tribe_tickets_get_readable_amount( $available, null, false );

						if ( - 1 !== $available && ( 0 === $threshold || $available <= $threshold ) ) :
						?>
							<span class="tribe-tickets-remaining">
							<?php
							echo sprintf( esc_html__( '%1$s available', 'event-tickets' ), '<span class="available-stock" data-product-id="' . esc_attr( $ticket->ID ) . '">' . esc_html( $readable_amount ) . '</span>' );
							?>
							</span>
						<?php elseif ( $show_unlimited ): ?>
							<span class="available-stock" data-product-id="<?php echo esc_attr( $ticket->ID ); ?>"><?php echo esc_html( $readable_amount ); ?></span>
						<?php endif; ?>
					<?php else: ?>
						<span class="tickets_nostock"><?php esc_html_e( 'Out of stock!', 'event-tickets' ); ?></span>
					<?php endif; ?>
				</td>
				<td class="tickets_name">
					<?php echo esc_html( $ticket->name ); ?>
				</td>
				<td class="tickets_price">
					<?php echo $this->main->get_price_html( $ticket->ID ); ?>
				</td>
				<td class="tickets_description" colspan="2">
					<?php echo esc_html( ( $ticket->show_description() ? $ticket->description : '' ) ); ?>
				</td>
				<td class="tickets_submit">
					<?php if ( ! $must_login ) : ?>
						<button
							type="submit"
							class="tpp-submit tribe-button"
						>
							<?php esc_html_e( 'Buy now', 'event-tickets' ); ?>
						</button>
					<?php endif; ?>
				</td>
			</tr>
			<?php

			/**
			 * Allows injection of HTML after an Tribe Commerce ticket table row
			 *
			 * @var WP_Post                       $post The post object the ticket is attached to.
			 * @var Tribe__Tickets__Ticket_Object $ticket
			 */
			do_action( 'event_tickets_tpp_after_ticket_row', tribe_events_get_ticket_event( $ticket->id ), $ticket );
		}

		$is_there_any_message_to_show = ! is_user_logged_in() && ( $must_login && $display_login_link );
		?>

		<?php if ( $is_there_any_product_to_sell && $is_there_any_message_to_show ) : ?>
			<tr>
				<td colspan="5" class="tpp-add">
					<?php if ( $must_login ) : ?>
						<?php include tribe( 'tickets.commerce.paypal' )->getTemplateHierarchy( 'login-to-purchase' ); ?>
					<?php endif; ?>
					<?php if ( ! $must_login && $display_login_link ) : ?>
						<?php include tribe( 'tickets.commerce.paypal' )->getTemplateHierarchy( 'login-before-purchase' ); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif ?>

		<?php if ( tribe( 'tickets.commerce.paypal.cart' )->has_items() ) : ?>
			<tr>
				<td colspan="5" class="tpp-add">
					<?php include tribe( 'tickets.commerce.paypal' )->getTemplateHierarchy( 'tickets/tpp-return-to-cart' ); ?>
				</td>
			</tr>
		<?php endif ?>

		<noscript>
			<tr>
				<td class="tribe-link-tickets-message">
					<div class="no-javascript-msg"><?php esc_html_e( 'You must have JavaScript activated to purchase tickets. Please enable JavaScript in your browser.', 'event-tickets' ); ?></div>
				</td>
			</tr>
		</noscript>
	</table>
</form>
