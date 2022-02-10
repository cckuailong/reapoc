<?php
/**
 * Block: RSVP
 * Form Opt-Out
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/opt-out.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.11.0 Updated the optout markup and classes used.
 * @since   4.11.3 Ensure we always show the optout by default.
 * @since   4.12.0 Add $post_id to filter for hiding opt-outs.
 * @since   5.0.3 Add vars to docblock and removed duplicative vars.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template $this    Template object.
 * @var int                              $post_id [Global] The current Post ID to which RSVPs are attached.
 * @var Tribe__Tickets__Ticket_Object    $ticket  The ticket object with provider set to RSVP.
 * @var string                           $going   The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */
$modal   = $this->get( 'is_modal' );

/**
 * Use this filter to hide the Attendees List Optout
 *
 * @since 4.9
 * @since 4.12.0 Added $post_id parameter.
 *
 * @param bool $hide_attendee_list_optout Whether to hide attendees list opt-out.
 * @param int  $post_id                   The post ID this ticket belongs to.
 */
$hide_attendee_list_optout = apply_filters( 'tribe_tickets_plus_hide_attendees_list_optout', false, $post_id );

if ( $hide_attendee_list_optout ) {
	// Force optout.
	?>
	<input name="attendee[optout]" value="1" type="hidden" />
	<?php
	return;
}

/* var Tribe__Tickets__Privacy $privacy  */
$privacy = tribe( 'tickets.privacy' );

$field_id = [
	'tribe-tickets-attendees-list-optout',
	$ticket->ID,
];

$field_id = implode( '-', $field_id );
?>
<div class="tribe-common-form-control-checkbox tribe-tickets-attendees-list-optout--wrapper">
	<label
		class="tribe-common-form-control-checkbox__label"
		for="<?php echo esc_attr( $field_id ); ?>"
	>
		<input
			class="tribe-common-form-control-checkbox__input tribe-tickets__item__optout"
			id="<?php echo esc_attr( $field_id ); ?>"
			name="attendee[optout]"
			type="checkbox"
			<?php checked( true ); ?>
		/>
		<?php echo $privacy->get_opt_out_text(); ?>
	</label>
</div>
