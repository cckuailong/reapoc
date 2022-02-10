<?php
/**
 * This template renders the RSVP AR form guest template.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/guest-template.php
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

?>
<script
	class="tribe-tickets__rsvp-ar-form-guest-template"
	id="tmpl-tribe-tickets__rsvp-ar-form-guest-template-<?php echo esc_attr( $rsvp->ID ); ?>"
	type="text/template"
>
	<div
		class="tribe-tickets__rsvp-ar-form-guest tribe-common-a11y-hidden"
		data-guest-number="{{data.attendee_id + 1}}"
		tabindex="0"
		role="tabpanel"
		id="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-{{data.attendee_id + 1}}-tab"
		aria-labelledby="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-{{data.attendee_id + 1}}"
		hidden
	>

		<?php $this->template( 'v2/rsvp/ari/form/template/title', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/ari/form/template/fields', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/ari/form/buttons', [ 'rsvp' => $rsvp ] ); ?>

	</div>
</script>
