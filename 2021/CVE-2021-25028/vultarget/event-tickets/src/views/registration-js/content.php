<?php
/**
 * This template renders the registration/purchase attendee fields
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/content.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.11.0
 * @since   4.11.3.1 Fix handling where $provider is an object.
 * @since   4.12.0   Prevent potential errors when $provider_obj is not valid.
 * @since   4.12.1   Set the notice as hidden by default. The JavaScript will show if needed.
 * @since   4.12.3 Update for getting ticket provider now returning instance or False. Remove duplicate array property.
 *              Retrieve $is_meta_up_to_date in a manner consistent with other template variables. Moved `novalidate` from
 *              div to form, as it used to be. Implement short array syntax.
 * @since 5.0.3 Add `event-tickets` class to the wrapper.
 * @since 5.0.4 Remove extraneous "Save and checkout" heading.
 *
 * @version 5.0.4
 *
 * @var Tribe__Tickets__Attendee_Registration__View $this
 */

$provider = $this->get( 'provider' ) ?: tribe_get_request_var( tribe_tickets_get_provider_query_slug() );
$events   = (array) $this->get( 'events' );

/** @var Tribe__Tickets_Plus__Meta $meta */
$meta = tribe( 'tickets-plus.meta' );

if ( empty( $provider ) ) {
	$event_keys   = array_keys( $events );
	$event_key    = array_shift( $event_keys );
	$provider_obj = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $event_key );
	$provider     = $provider_obj->attendee_object;
} elseif ( is_string( $provider ) ) {
	/** @var Tribe__Tickets__Attendee_Registration__View $reg_view */
	$reg_view     = tribe( 'tickets.attendee_registration.view' );
	$provider_obj = $reg_view->get_cart_provider( $provider );
	$provider     = $provider_obj->attendee_object;
} elseif ( $provider instanceof Tribe__Tickets__Tickets ) {
	$provider_obj = $provider;
	$provider     = $provider_obj->attendee_object;
}

if ( method_exists( $provider_obj, 'get_checkout_url' ) ) {
	$checkout_url = $provider_obj->get_checkout_url();
} else {
	$checkout_url = '';
}

$non_meta_count = 0;
$provider_class = $this->get_form_class( $provider );
$all_tickets    = [];
$classes        = [
	'tribe-common',
	'event-tickets',
	'tribe-tickets__registration',
];
?>
<div <?php tribe_classes( $classes ); ?> data-provider="<?php echo esc_attr( $provider ); ?>">
	<?php
	/**
	 * Before the output, whether $events is empty.
	 *
	 * @since 4.11.0
	 *
	 * @param string $provider       The 'provider' $_REQUEST var.
	 * @param string $provider_class The class string or empty string if ticket provider is not found.
	 * @param array  $events         The array of events, which might be empty.
	 */
	do_action( 'tribe_tickets_registration_content_before_all_events', $provider, $provider_class, $events );
	?>

	<div class="tribe-common-h8 tribe-common-h--alt tribe-tickets__registration__actions">
		<?php $this->template( 'registration/button-cart', [ 'provider' => $provider ] ); ?>
	</div>

	<h1 class="tribe-common-h2 tribe-common-h1--min-medium tribe-common-h--alt tribe-tickets__registration__page-title">
		<?php echo esc_html( tribe( 'tickets.attendee_registration.template' )->get_page_title() ); ?>
	</h1>
	<form
		method="post"
		id="tribe-tickets__registration__form"
		action="<?php echo esc_url( $checkout_url ); ?>"
		data-provider="<?php echo esc_attr( $provider ); ?>"
		novalidate
	>
	<div class="tribe-tickets__registration__grid">
		<?php
		$this->template(
			'components/notice',
			[
				'id' => 'tribe-tickets__notice__attendee-registration',
				'notice_classes' => [
					'tribe-tickets__notice--error',
					'tribe-tickets__validation-notice',
				],
				'content' => sprintf(
					esc_html_x(
						'You have %s ticket(s) with a field that requires information.',
						'Note about missing required fields, %s is the html-wrapped number of tickets.',
						'event-tickets'
					),
					'<span class="tribe-tickets__notice--error__count">1</span>'
				),
			]
		);

		$args = [
			'cart_url'            => $this->get( 'cart_url' ),
			'events'              => $events,
			'has_tickets_on_sale' => $this->get( 'has_tickets_on_sale' ),
			'is_sale_past'        => $this->get( 'is_sale_past' ),
			'post_id'             => $this->get( 'post_id' ),
			'provider_id'         => $this->get( 'provider_id' ),
			'provider'            => $provider,
			'tickets_on_sale'     => $this->get( 'tickets_on_sale' ),
			'tickets'             => $this->get( 'tickets', [] ),
		];

		$this->template( 'registration-js/mini-cart', $args );
		?>
		<div class="tribe-tickets__registration__content">
			<input type="hidden" name="tribe_tickets_saving_attendees" value="1" />
			<input type="hidden" name="tribe_tickets_ar" value="1" />
			<input type="hidden" name="tribe_tickets_ar_page" value="1" />
			<input type="hidden" name="tribe_tickets_ar_data" value="" id="tribe_tickets_ar_data"  />
			<input type="hidden" name="tribe_tickets_provider" value="<?php echo esc_attr( $provider ); ?>"  />

			<?php foreach ( $events as $event_id => $tickets ) : ?>
				<?php
				$providers     = wp_list_pluck( $tickets, 'provider' );
				$providers_arr = array_unique( wp_list_pluck( $providers, 'attendee_object' ) );

				if (
					empty( $provider_class )
					&& ! empty( $providers_arr[ $event_id ] )
				) :
					$provider_class = 'tribe-tickets__item__attendee__fields__form--' . $providers_arr[ $event_id ];
				endif;

				$has_tpp = Tribe__Tickets__Commerce__PayPal__Main::ATTENDEE_OBJECT === $provider || in_array( Tribe__Tickets__Commerce__PayPal__Main::ATTENDEE_OBJECT, $providers_arr, true );
				?>
				<div
					class="tribe-tickets__registration__event"
					data-event-id="<?php echo esc_attr( $event_id ); ?>"
					data-is-meta-up-to-date="<?php echo absint( $this->get( 'is_meta_up_to_date' ) ); ?>"
				>
					<?php $this->template( 'registration/summary/content', [ 'event_id' => $event_id, 'tickets' => $tickets ] ); ?>

					<div class="tribe-tickets__item__attendee__fields">

						<?php $this->template( 'registration-js/attendees/error', [ 'event_id' => $event_id, 'tickets' => $tickets ] ); ?>

						<div
							class="tribe-tickets__item__attendee__fields__form <?php echo sanitize_html_class( $provider_class ); ?> tribe-validation"
							name="event<?php echo esc_attr( $event_id ); ?>"
						>
							<?php
							foreach ( $tickets as $ticket ) :
								$all_tickets[] = $ticket;

								// Only include tickets with meta.
								if ( ! $meta->ticket_has_meta( $ticket['id'] ) ) {
									$non_meta_count++;

									continue;
								}
								?>
									<div class="tribe-tickets__item__attendee__fields__container" data-ticket-id="<?php echo esc_attr( $ticket['id'] ); ?>">
										<h3 class="tribe-common-h5 tribe-common-h5--min-medium tribe-common-h--alt tribe-ticket__heading">
											<?php echo esc_html( get_the_title( $ticket['id'] ) ); ?>
										</h3>
									</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<?php $this->template( 'registration-js/attendees/content', [ 'event_id' => $event_id, 'tickets' => $tickets, 'provider' => $providers[0] ] ); ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="tribe-tickets__registration__footer">
		<?php
		$notice_classes = [
			'tribe-tickets__notice--non-ar',
			'tribe-common-a11y-hidden', // Set as hidden. JavaScript will show it if needed.
		];

		$this->template(
			'components/notice',
			[
				'notice_classes'  => $notice_classes,
				'content' => sprintf(
					esc_html_x(
						'There are %s other tickets in your cart that do not require attendee information.',
						'Note that there are more tickets in the cart, %s is the html-wrapped number.',
						'event-tickets'
					),
					'<span id="tribe-tickets__non-ar-count">' . absint( $non_meta_count ) . '</span>'
				)
			]
		); ?>
		<?php $this->template( 'blocks/tickets/registration/attendee/submit' ); ?>
	</div>
	</form>
</div>
<?php include Tribe__Tickets__Templates::get_template_hierarchy( 'components/loader.php' ); ?>
