<?php
/**
 * Admin setting for "Can't Go" checkbox when creating/editing RSVP in Classic Editor.
 */

?>

<div
	class="input_block ticket_advanced_Tribe__Tickets__RSVP tribe-dependent"
	data-depends="#Tribe__Tickets__RSVP_radio"
	data-condition-is-checked
>
	<label
		for="tribe-tickets-rsvp-not-going"
		class="ticket_form_label ticket_form_left"
	>
		<?php esc_html_e( "Can't Go:", 'event-tickets' ); ?>
	</label>
	<input
		type="checkbox"
		id="tribe-tickets-rsvp-not-going"
		name="tribe-ticket[not_going]"
		class="ticket_field tribe-rsvp-field-not-going ticket_form_right"
		value="yes"
		<?php checked( $not_going ); ?>
	/>
	<span class="tribe_soft_note ticket_form_right"><?php esc_html_e( "Enable \"Can't Go\" responses", 'event-tickets' ); ?></span>
</div>
