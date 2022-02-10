<?php
/**
 * This template renders the RSVP ticket form.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/tickets/rsvp.php
 *
 * @since   4.0
 * @since   4.10.8 More similar display format to that of other ticket types, including better checking of max quantity available.
 * @since   4.10.9 Use customizable ticket name functions.
 * @since   4.11.0 Added RSVP/ticket view link to template.
 * @since   4.11.1 Corrected amount of available/remaining tickets when threshold is empty.
 * @since   4.11.5 Display total available separately from setting max allowed to purchase at once.
 * @since   5.1.5 Add label to the quantity input to improve accessibility.
 *
 * @version 5.1.5
 *
 * @var Tribe__Tickets__RSVP $this
 * @var bool                 $must_login
 */

$is_there_any_product_to_sell = false;

ob_start();
$messages = Tribe__Tickets__RSVP::get_instance()->get_messages();
$messages_class = $messages ? 'tribe-rsvp-message-display' : '';

/* var Tribe__Tickets__Privacy $privacy  */
$privacy = tribe( 'tickets.privacy' );

/** @var Tribe__Settings_Manager $settings_manager */
$settings_manager = tribe( 'settings.manager' );

$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

/**
 * Overwrites the threshold to display "# tickets left".
 *
 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
 * @param array $data      Ticket data.
 * @param int   $post_id   WP_Post/Event ID.
 *
 * @since 4.11.1
 */
$threshold = absint( apply_filters( 'tribe_display_rsvp_block_tickets_left_threshold', $threshold, tribe_events_get_ticket_event( $ticket ) ) );

/**
 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
 *
 * @since 4.5.6
 *
 * @param boolean $already_rendered Whether the order link template has already been rendered.
 *
 * @see Tribe__Tickets__Tickets_View::inject_link_template()
 */
$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

// Output order links / view link if we haven't already (for RSVPs).
if ( ! $already_rendered ) {
	include $this->getTemplateHierarchy( 'tickets/view-link' );

	add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
}
?>

<form
	id="rsvp-now"
	action=""
	class="tribe-tickets-rsvp cart <?php echo esc_attr( $messages_class ); ?>"
	method="post"
	enctype='multipart/form-data'
>
	<h2 class="tribe-events-tickets-title tribe--rsvp">
		<?php echo esc_html( tribe_get_rsvp_label_singular( 'form_heading' ) ); ?>
	</h2>

	<div class="tribe-rsvp-messages">
		<?php
		if ( $messages ) {
			foreach ( $messages as $message ) {
				?>
				<div class="tribe-rsvp-message tribe-rsvp-message-<?php echo esc_attr( $message->type ); ?>">
					<?php echo esc_html( $message->message ); ?>
				</div>
				<?php
			}//end foreach
		}//end if
		?>

		<div
			class="tribe-rsvp-message tribe-rsvp-message-error tribe-rsvp-message-confirmation-error" style="display:none;">
			<?php esc_html_e( 'Please fill in the RSVP quantity, confirmation name, and email fields.', 'event-tickets' ); ?>
		</div>
	</div>

	<table class="tribe-events-tickets tribe-events-tickets-rsvp">
		<?php
		/** @var Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		foreach ( $tickets as $ticket ) {
			if ( ! $ticket instanceof Tribe__Tickets__Ticket_Object ) {
				continue;
			}

			// if the ticket isn't an RSVP ticket, then let's skip it
			if (
				! $ticket instanceof Tribe__Tickets__Ticket_Object
				|| 'Tribe__Tickets__RSVP' !== $ticket->provider_class
			) {
				continue;
			}

			if ( ! $ticket->date_in_range() ) {
				continue;
			}

			$ticket_id = $ticket->ID;

			$is_there_any_rsvp_stock = false;

			$available = $ticket->available();

			$readable_amount = tribe_tickets_get_readable_amount( $available, null, false );

			/**
			 * Allows hiding of "unlimited" to be toggled on/off conditionally.
			 *
			 * @since 4.11.1
			 *
			 * @param int $show_unlimited allow showing of "unlimited".
			 *
			 */
			$show_unlimited = apply_filters( 'tribe_rsvp_block_show_unlimited_availability', false, $available );

			$is_there_any_rsvp_stock      = 0 !== $available;
			$is_there_any_product_to_sell = $is_there_any_rsvp_stock || $is_there_any_product_to_sell;

			$max_at_a_time = $handler->get_ticket_max_purchase( $ticket_id );
			?>
			<tr>
				<td class="tribe-ticket quantity" data-product-id="<?php echo esc_attr( $ticket_id ); ?>">
					<input type="hidden" name="product_id[]" value="<?php echo absint( $ticket_id ); ?>">
					<?php if ( $is_there_any_rsvp_stock ) : ?>
						<label
							class="screen-reader-text"
							for="quantity_<?php echo esc_attr( absint( $ticket_id ) ); ?>"
						>
							<?php esc_html_e( 'Quantity', 'event-tickets' ); ?>
						</label>
						<input
							type="number"
							class="tribe-tickets-quantity"
							step="1"
							min="0"
							max="<?php echo esc_attr( $max_at_a_time ); ?>"
							name="quantity_<?php echo esc_attr( absint( $ticket_id ) ); ?>"
							id="quantity_<?php echo esc_attr( absint( $ticket_id ) ); ?>"
							value="0"
							<?php disabled( $must_login ); ?>
						>
						<?php if ( - 1 !== $available && ( 0 === $threshold || $available <= $threshold ) ) : ?>
							<span class="tribe-tickets-remaining">
								<span class="available-stock" data-product-id="<?php echo esc_attr( $ticket_id ); ?>">
									<?php echo sprintf( esc_html__( '%1$s available', 'event-tickets' ), esc_html( $readable_amount ) ); ?>
								</span>
							</span>
						<?php elseif ( $show_unlimited ): ?>
							<span class="available-stock" data-product-id="<?php echo esc_attr( $ticket_id ); ?>"><?php echo esc_html( $handler->unlimited_term ); ?></span>
						<?php endif; ?>
					<?php elseif ( ! $ticket->is_in_stock() ) : ?>
						<span class="tickets_nostock"><?php esc_html_e( 'Out of stock!', 'event-tickets' ); ?></span>
					<?php endif; ?>
				</td>
				<td class="tickets_name">
					<?php echo esc_html( $ticket->name ); ?>
				</td>

				<td class="tickets_description" colspan="2">
					<?php echo esc_html( ( $ticket->show_description() ? $ticket->description : '' ) ); ?>
				</td>
			</tr>
			<?php

			/**
			 * Allows injection of HTML after an RSVP ticket table row.
			 *
			 * @var bool|WP_Post                  Event ID
			 * @var Tribe__Tickets__Ticket_Object
			 */
			do_action( 'event_tickets_rsvp_after_ticket_row', tribe_events_get_ticket_event( $ticket_id ), $ticket );

		}
		?>

		<?php if ( $is_there_any_product_to_sell ) : ?>
			<tr class="tribe-tickets-meta-row">
				<td colspan="4" class="tribe-tickets-attendees">
					<header><?php esc_html_e( 'Send RSVP confirmation to:', 'event-tickets' ); ?></header>
					<?php
					/**
					 * Allows injection of HTML before RSVP ticket confirmation fields
					 *
					 * @var array of Tribe__Tickets__Ticket_Object
					 */
					do_action( 'tribe_tickets_rsvp_before_confirmation_fields', $tickets );

					/**
					 * Set the default Full Name for the RSVP form
					 *
					 * @since 4.7.1
					 *
					 * @param string
					 */
					$name = apply_filters( 'tribe_tickets_rsvp_form_full_name', '' );

					/**
					 * Set the default value for the email on the RSVP form.
					 *
					 * @since 4.7.1
					 *
					 * * @param string
					 */
					$email = apply_filters( 'tribe_tickets_rsvp_form_email', '' );
					?>
					<table class="tribe-tickets-table">
						<tr class="tribe-tickets-full-name-row">
							<td>
								<label for="tribe-tickets-full-name"><?php esc_html_e( 'Full Name', 'event-tickets' ); ?>:</label>
							</td>
							<td colspan="3">
								<input type="text" name="attendee[full_name]" id="tribe-tickets-full-name" value="<?php echo esc_html( $name ); ?>">
							</td>
						</tr>
						<tr class="tribe-tickets-email-row">
							<td>
								<label for="tribe-tickets-email"><?php esc_html_e( 'Email', 'event-tickets' ); ?>:</label>
							</td>
							<td colspan="3">
								<input type="email" name="attendee[email]" id="tribe-tickets-email" value="<?php echo esc_html( $email ); ?>">
							</td>
						</tr>

						<tr class="tribe-tickets-order_status-row">
							<td>
								<label for="tribe-tickets-order_status"><?php echo esc_html( tribe_get_rsvp_label_singular( 'order_status_label' ) ); ?>:</label>
							</td>
							<td colspan="3">
								<?php Tribe__Tickets__Tickets_View::instance()->render_rsvp_selector( 'attendee[order_status]', '' ); ?>
							</td>
						</tr>

						<?php
						/**
						 * Use this filter to hide the Attendees List Optout
						 *
						 * @since 4.5.2
						 *
						 * @param bool
						 */
						$hide_attendee_list_optout = apply_filters( 'tribe_tickets_hide_attendees_list_optout', false );
						if ( ! $hide_attendee_list_optout
							 && class_exists( 'Tribe__Tickets_Plus__Attendees_List' )
							 && ! Tribe__Tickets_Plus__Attendees_List::is_hidden_on( get_the_ID() )
						) : ?>
							<tr class="tribe-tickets-attendees-list-optout">
								<td colspan="4">
									<input
										type="checkbox"
										name="attendee[optout]"
										id="tribe-tickets-attendees-list-optout"
										<?php checked( true ); ?>
									>
									<label for="tribe-tickets-attendees-list-optout">
										<?php echo $privacy->get_opt_out_text(); ?>
									</label>
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="add-to-cart">
					<?php if ( $must_login ) : ?>
						<a href="<?php echo esc_url( Tribe__Tickets__Tickets::get_login_url() ); ?>">
							<?php esc_html_e( 'Login to RSVP', 'event-tickets' );?>
						</a>
					<?php else: ?>
						<input type="hidden" name="tribe_tickets_rsvp_submission" value="1" />
						<button
							type="submit"
							name="tickets_process"
							value="1"
							class="tribe-button tribe-button--rsvp"
						>
							<?php
							echo esc_html(
								sprintf(
									_x( 'Confirm %s', 'tickets process button text', 'event-tickets' ),
									tribe_get_rsvp_label_singular( 'tickets_process_button_text' )
								)
							); ?>
						</button>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
		<noscript>
			<tr>
				<td class="tribe-link-tickets-message">
					<div class="no-javascript-msg"><?php esc_html_e( 'You must have JavaScript activated to purchase tickets. Please enable JavaScript in your browser.', 'event-tickets' ); ?></div>
				</td>
			</tr>
		</noscript>
	</table>
</form>

<?php
$content = ob_get_clean();
echo $content;

if ( $is_there_any_product_to_sell ) {
	// If we have available tickets there is generally no need to display a 'tickets unavailable' message
	// for this post
	$this->do_not_show_tickets_unavailable_message();
} else {
	// Indicate that there are not any tickets, so a 'tickets unavailable' message may be
	// appropriate (depending on whether other ticket providers are active and have a similar
	// result)
	$this->maybe_show_tickets_unavailable_message( $tickets );
}
