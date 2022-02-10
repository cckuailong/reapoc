<?php
/**
 * This template renders the event summary description
 * for the registration page
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/description.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<?php if ( class_exists( 'Tribe__Events__Main' ) ) : ?>
<div class="tribe-common-b2 tribe-tickets__registration__description">
	<?php echo tribe_events_event_schedule_details( $event_id ); ?>
</div>
<?php endif;
