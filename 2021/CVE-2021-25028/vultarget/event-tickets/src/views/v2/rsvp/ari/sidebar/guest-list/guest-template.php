<?php
/**
 * This template renders the RSVP ARI sidebar guest list item JS template.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/sidebar/guest-list/guest-template.php
 *
 * @var bool $must_login Whether the user has to login to RSVP or not.
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

?>
<script
	class="tribe-tickets__rsvp-ar-guest-list-item-template"
	id="tmpl-tribe-tickets__rsvp-ar-guest-list-item-template-<?php echo esc_attr( $rsvp->ID ); ?>"
	type="text/template"
>
	<li class="tribe-tickets__rsvp-ar-guest-list-item">
		<button
			class="tribe-tickets__rsvp-ar-guest-list-item-button tribe-tickets__rsvp-ar-guest-list-item-button--inactive"
			type="button"
			data-guest-number="{{data.attendee_id + 1}}"
			role="tab"
			aria-selected="false"
			aria-controls="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-{{data.attendee_id + 1}}-tab"
			id="tribe-tickets-rsvp-<?php echo esc_attr( $rsvp->ID ); ?>-guest-{{data.attendee_id + 1}}"
			<?php disabled( $must_login ); ?>
		>
			<?php $this->template( 'v2/components/icons/guest', [ 'classes' => [ 'tribe-tickets__rsvp-ar-guest-icon' ] ] ); ?>
			<span class="tribe-tickets__rsvp-ar-guest-list-item-title tribe-common-a11y-visual-hide">
				<?php /* Translators: 1 the guest number. */ ?>
				<?php echo sprintf( esc_html_x( 'Guest %1$s', 'RSVP attendee registration sidebar guest button', 'event-tickets' ), '{{data.attendee_id + 1}}' ); ?>
			</span>
		</button>
	</li>
</script>
