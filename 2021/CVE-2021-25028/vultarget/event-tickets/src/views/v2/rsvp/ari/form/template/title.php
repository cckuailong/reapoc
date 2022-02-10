<?php
/**
 * This template renders the RSVP AR form title.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/form/template/title.php
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

?>
<header>
	<h3 class="tribe-tickets__rsvp-ar-form-title tribe-common-h5">
		<?php /* Translators: 1 the guest number. */ ?>
		<?php echo sprintf( esc_html_x( 'Guest %1$s', 'RSVP attendee registration form title', 'event-tickets' ), '{{data.attendee_id + 1}}' ); ?>
	</h3>
</header>
