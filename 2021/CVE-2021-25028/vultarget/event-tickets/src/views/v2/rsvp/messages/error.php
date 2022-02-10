<?php
/**
 * Block: RSVP
 * Messages Error
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/messages/error.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var string|array $error_message The error message(s).
 *
 * @since 5.0.0
 *
 * @version 5.0.0
 */

// Treat error messages as an array.
$error_messages = (array) $error_message;
?>
<div class="tribe-tickets__rsvp-message tribe-tickets__rsvp-message--error tribe-common-b3">
	<?php $this->template( 'v2/components/icons/error', [ 'classes' => [ 'tribe-tickets__rsvp-message--error-icon' ] ] ); ?>

	<?php foreach ( $error_messages as $message ) : ?>
		<span class="tribe-tickets__rsvp-message-text">
			<strong>
				<?php echo wp_kses_post( $message ); ?>
			</strong>
		</span>
	<?php endforeach; ?>
</div>
