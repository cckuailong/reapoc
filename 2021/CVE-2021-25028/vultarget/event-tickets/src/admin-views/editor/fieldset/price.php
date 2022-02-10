<?php

if ( ! isset( $post_id ) ) {
	$post_id = get_the_ID();
}

$validation_attrs = [
	'data-validation-error="' . esc_attr( sprintf(
		// Translators: %s: singular version of the Ticket label.
		_x( '%s price must be greater than zero.', 'ticket price validation error', 'event-tickets' ),
		tribe_get_ticket_label_singular( 'ticket_price_validation_error' )
	) ) . '"'
];

$ticket            = null;
$is_paypal_ticket  = false;
$price_description = '';
$price             = null;
$sale_price        = null;
$disabled          = false;

$provider         = ! empty( $ticket_id ) ? tribe_tickets_get_ticket_provider( $ticket_id ) : $provider;

$is_paypal_ticket = $provider instanceof Tribe__Tickets__Commerce__PayPal__Main || $provider instanceof \TEC\Tickets\Commerce\Module;

$description_string = sprintf( _x( 'Leave blank for free %s', 'price description', 'event-tickets' ), tribe_get_ticket_label_singular( 'price_description' ) );
$description_string = esc_html( apply_filters( 'tribe_tickets_price_description', $description_string, $ticket_id ) );
$price_description  = $is_paypal_ticket ? '' : $description_string;

if ( $is_paypal_ticket ) {
	$validation_attrs[] = 'data-required';
	$validation_attrs[] = 'data-validation-is-greater-than="0"';
}

/**
 * Filters whether we should disable the ticket - separate from tribe-dependency.
 *
 * @since 4.10.8
 *
 * @param boolean     $disabled  Whether the price field is disabled.
 * @param WP_Post|int $ticket_id The current ticket object or its ID
 */
$disabled = apply_filters( 'tribe_tickets_price_disabled', false, $ticket_id );
$disabled = (bool) filter_var( $disabled, FILTER_VALIDATE_BOOLEAN );
$ticket   = empty( $provider ) ? $ticket : $provider->get_ticket( $post_id, $ticket_id );

// If the ticket has a WC Memberships discount for the currently-logged-in user.
$ticket_has_wc_member_discount = tribe_tickets_ticket_in_wc_membership_for_user( $ticket_id );

if ( ! empty( $ticket ) ) {
	if (
		$ticket->on_sale
		|| $ticket_has_wc_member_discount
	) {
		$price      = $ticket->regular_price;
		$sale_price = $ticket->price;
	} else {
		$price = $ticket->price;
	}
}

?>
<div
	class="price <?php echo $disabled ? 'input_block' : 'tribe-dependent'; ?>"
	<?php if ( ! $disabled ) : ?>
	data-depends="#Tribe__Tickets__RSVP_radio"
	data-condition-is-not-checked
	<?php endif; ?>
>
	<div class="input_block">
		<label for="ticket_price" class="ticket_form_label ticket_form_left"><?php esc_html_e( 'Price:', 'event-tickets' ); ?></label>
		<input
			type="text"
			id="ticket_price"
			name="ticket_price"
			class="ticket_field ticket_form_right"
			size="7"
			value="<?php echo esc_attr( $price ); ?>"
			<?php echo $disabled ? ' disabled="disabled" ' : ''; ?>
			<?php echo implode( ' ', $validation_attrs ); ?>
		/>
		<?php
		if ( $price_description ) {
			?>
			<p class="description ticket_form_right">
				<?php echo esc_html( $price_description ); ?>
			</p>
			<?php
		}

		/**
		 * Allow to add messages under the price field.
		 *
		 * @since 4.10.7
		 *
		 * @param int $ticket_id Ticket ID.
		 * @param int $post_id   Post ID.
		 */
		do_action( 'tribe_tickets_price_input_description', $ticket_id, $post_id );
		?>
	</div>

	<?php if ( $ticket && ( $ticket->on_sale || $ticket_has_wc_member_discount ) ) : ?>

	<?php
		$sale_price_label = esc_html__( 'Sale Price:', 'event-tickets' );
		$sale_price_desc  = esc_html__( 'Current sale price. This can be managed via the product editor.', 'event-tickets' );

		if ( $ticket_has_wc_member_discount ) {
			$sale_price_label = esc_html__( 'Sale/Member Price:', 'event-tickets' );
			$sale_price_desc  = esc_html__( 'Current sale or member price. This can be managed via the product editor.', 'event-tickets' );
		}
		?>
		<div class="input_block">
			<label for="ticket_sale_price" class="ticket_form_label ticket_form_left"><?php echo esc_html( $sale_price_label ); ?></label>
			<input
				type="text"
				id="ticket_sale_price"
				name='ticket_sale_price'
				class="ticket_field ticket_form_right"
				size="7"
				value="<?php echo esc_attr( $sale_price ); ?>"
				readonly
			/>
			<p class="description ticket_form_right"><?php echo esc_html( $sale_price_desc ); ?></p>
		</div>
	<?php endif; ?>
</div>