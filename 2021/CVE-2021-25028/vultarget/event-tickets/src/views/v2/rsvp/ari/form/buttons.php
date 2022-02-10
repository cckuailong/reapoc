<?php
/**
 * This template renders the RSVP AR form buttons.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/buttons.php
 *
 * @since 4.12.3
 *
 * @var bool $must_login Whether the user has to login to RSVP or not.
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @version 5.0.0
 */

?>
<div class="tribe-tickets__rsvp-form-buttons">
	<button
		class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel"
		type="reset"
	>
		<?php esc_html_e( 'Cancel', 'event-tickets' ); ?>
	</button>

	<button
		class="tribe-common-c-btn tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--next tribe-common-a11y-hidden"
		type="button"
		<?php tribe_disabled( $must_login ); ?>
	>
		<?php esc_html_e( 'Next guest', 'event-tickets' ); ?>
	</button>

	<button
		class="tribe-common-c-btn tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--submit"
		type="submit"
		<?php tribe_disabled( $must_login ); ?>
	>
		<?php esc_html_e( 'Finish', 'event-tickets' ); ?>
	</button>
</div>
