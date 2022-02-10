<?php
/**
 * Block: RSVP
 * Actions - Success - Toggle
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/actions/success/toggle.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp                 The rsvp ticket object.
 * @var boolean                       $opt_in_toggle_hidden The order status of the RSVP that was made.
 * @var string                        $opt_in_attendee_ids  The list of attendee IDs to send.
 * @var string                        $opt_in_nonce         The nonce for opt-in AJAX requests.
 * @var boolean                       $opt_in_checked       Whether the opt-in field should be checked.
 *
 * @since 5.0.0
 * @version 5.0.0
 */

if ( $opt_in_toggle_hidden ) {
	return;
}

$toggle_id = 'toggle-rsvp-' . $rsvp->ID;
?>

<div class="tribe-tickets__rsvp-actions-success-going-toggle tribe-common-form-control-toggle">
	<input
		class="tribe-common-form-control-toggle__input tribe-tickets__rsvp-actions-success-going-toggle-input"
		id="<?php echo esc_attr( $toggle_id ); ?>"
		name="toggleGroup"
		type="checkbox"
		value="toggleOne"
		<?php checked( $opt_in_checked ); ?>
		data-rsvp-id="<?php echo esc_attr( $rsvp->ID ); ?>"
		data-attendee-ids="<?php echo esc_attr( $opt_in_attendee_ids ); ?>"
		data-opt-in-nonce="<?php echo esc_attr( $opt_in_nonce ); ?>"
	/>
	<label
		class="tribe-common-form-control-toggle__label tribe-tickets__rsvp-actions-success-going-toggle-label"
		for="<?php echo esc_attr( $toggle_id ); ?>"
	>
		<span
			data-js="tribe-tickets-tooltip"
			data-tooltip-content="#tribe-tickets-tooltip-content-<?php echo esc_attr( $rsvp->ID ); ?>"
			aria-describedby="tribe-tickets-tooltip-content-<?php echo esc_attr( $rsvp->ID ); ?>"
		>
			<?php
			echo wp_kses_post(
				sprintf(
					// Translators: 1: opening span. 2: Closing span.
					_x(
						'Show me on public %1$sattendee list%2$s',
						'Toggle for RSVP attendee list.',
						'event-tickets'
					),
					'<span class="tribe-tickets__rsvp-actions-success-going-toggle-label-underline">',
					'</span>'
				)
			);
			?>
		</span>
	</label>
	<?php $this->template( 'v2/rsvp/actions/success/tooltip', [ 'rsvp' => $rsvp ] ); ?>
</div>
