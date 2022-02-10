<?php
/**
 * Block: RSVP
 * Actions - Success - Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/actions/success/title.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 * @version 5.0.0
 */

$success_text = ! empty( $is_going ) ? __( 'You are going', 'event-tickets' ) : __( "Can't go", 'event-tickets' );
?>
<div class="tribe-tickets__rsvp-actions-success-going">
	<em class="tribe-tickets__rsvp-actions-success-going-check-icon"></em>
	<span class="tribe-tickets__rsvp-actions-success-going-text tribe-common-h4 tribe-common-h6--min-medium">
		<?php echo esc_html( $success_text ); ?>
	</span>
</div>
