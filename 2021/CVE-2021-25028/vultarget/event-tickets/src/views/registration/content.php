<?php
/**
 * This template renders the registration/purchase attendee fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/content.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @deprecated 4.11.0 Starting with version 4.12.3, loading this file will cause errors.
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @since 4.10.9 Add Filter to show an event/post tickets on AR Page
 * @since 4.11.0 Add docblocks, pass missing 'provider' arg to registration/button-cart template, and add action hooks.
 * @since 4.12.3 This template has been marked as deprecated.
 * @since 5.0.3 Add `event-tickets` class to the wrapper.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Attendee_Registration__View $this
 */

$passed_provider = tribe_get_request_var( tribe_tickets_get_provider_query_slug() );

$passed_provider_class = $this->get_form_class( $passed_provider );

/**
 * Before the output, whether or not $events is empty.
 *
 * @since 4.11.0
 *
 * @param string $passed_provider       The 'provider' $_REQUEST var.
 * @param string $passed_provider_class The class string or empty string if ticket provider is not found.
 * @param array  $events                The array of events, which might be empty.
 */
do_action( 'tribe_tickets_registration_content_before_all_events', $passed_provider, $passed_provider_class, $events );

// If there are no events with tickets in cart, print the empty cart template.
if ( empty( $events ) ) {
	$this->template( 'registration/cart-empty' );
	return;
}

?>
<div class="tribe-common event-tickets tribe-tickets__registration">
	<div class="tribe-tickets__registration__actions">
		<?php
		$this->template(
			'registration/button-cart',
			[
				'provider' => $passed_provider,
			]
		);
		?>
	</div>
	<?php
	foreach ( $events as $event_id => $tickets ) :

		// Remove an event/post tickets if none have attendee registration.
		$show_tickets = tribe( 'tickets.attendee_registration' )->has_attendee_registration_enabled_in_array_of_tickets( $tickets );

		/**
		 * Filter to show an event/post tickets on Attendee Registration page regardless if they are enabled.
		 *
		 * @param boolean $show_tickets True or false to show tickets for an event.
		 * @param array   $tickets      An array of ticket products.
		 * @param int     $event_id     The event/post ID.
		 *
		 * @since 4.10.9
		 */
		$show_tickets = apply_filters( 'tribe_tickets_filter_showing_tickets_on_attendee_registration', $show_tickets, $tickets, $event_id );

		if ( ! $show_tickets ) {
			continue;
		}

		$provider_class = $passed_provider_class;

		$providers = array_unique( wp_list_pluck( wp_list_pluck( $tickets, 'provider'), 'attendee_object') );

		if (
			empty( $provider_class )
			&& ! empty( $providers[ $event_id ] )
		) {
			$provider_class = 'tribe-tickets__item__attendee__fields__form--' . $providers[ $event_id ];
		}

		$has_tpp = Tribe__Tickets__Commerce__PayPal__Main::ATTENDEE_OBJECT === $passed_provider || in_array( Tribe__Tickets__Commerce__PayPal__Main::ATTENDEE_OBJECT, $providers );
		?>
		<div
			class="tribe-common tribe-tickets__registration__event"
			data-event-id="<?php echo esc_attr( $event_id ); ?>"
			data-is-meta-up-to-date="<?php echo absint( $is_meta_up_to_date ); ?>"
		>
			<?php $this->template( 'registration/summary/content', [ 'event_id' => $event_id, 'tickets' => $tickets ] ); ?>

			<div class="tribe-tickets__item__attendee__fields">

				<?php $this->template( 'registration/attendees/error', [ 'event_id' => $event_id, 'tickets' => $tickets ] ); ?>

				<form
					method="post"
					class="tribe-tickets__item__attendee__fields__form <?php echo sanitize_html_class( $provider_class ); ?>"
					name="<?php echo 'event' . esc_attr( $event_id ); ?>"
					novalidate
				>
					<?php $this->template( 'registration/attendees/content', [ 'event_id' => $event_id, 'tickets' => $tickets ] ); ?>
					<input type="hidden" name="tribe_tickets_saving_attendees" value="1" />
					<?php if ( $has_tpp ) : ?>
						<button type="submit"><?php esc_html_e( 'Save and Checkout', 'event-tickets' ); ?></button>
					<?php else: ?>
						<button type="submit"><?php esc_html_e( 'Save Attendee Info', 'event-tickets' ); ?></button>
					<?php endif; ?>

				</form>

				<?php $this->template( 'registration/attendees/error', [] ); ?>
				<?php $this->template( 'registration/attendees/success', [] ); ?>

				<?php $this->template( 'registration/attendees/loader', [] ); ?>

			</div>

		</div>

	<?php endforeach; ?>

	<?php
	$this->template(
		'registration/button-checkout',
		[
			'checkout_url'           => $checkout_url,
			'cart_has_required_meta' => $cart_has_required_meta,
			'is_meta_up_to_date'     => $is_meta_up_to_date,
		]
	);

	/**
	 * After the output, only if $events is not empty.
	 *
	 * @since 4.11.0
	 *
	 * @param string $passed_provider       The 'provider' $_REQUEST var.
	 * @param string $passed_provider_class The class string or empty string if ticket provider is not found.
	 * @param array  $events                The non-empty array of events.
	 */
	do_action( 'tribe_tickets_registration_content_after_all_events', $passed_provider, $passed_provider_class, $events );
	?>
</div>
