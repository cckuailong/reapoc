<?php
/**
 * Edit Event Tickets.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/tickets/orders.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.7.4
 * @since   4.10.2 Only show Update button if ticket has meta.
 * @since   4.10.8 Show Update button if current user has either RSVP or Ticket with meta. Do not use the now-deprecated third parameter of `get_description_rsvp_ticket()`.
 * @since   4.10.9 Use function for text.
 * @since   4.11.3 Correct getting `$event_id` when using The Events Calendar's "Default Page Template" display template. `$event_id` now relies on the `WP_Query` queried object ID instead of the global `$post` object.
 * @since   4.11.3 Reformat a bit of the code around the button - no functional changes.
 * @since   4.12.1 Account for empty post type object, such as if post type got disabled.
 * @since   4.12.3 Account for inactive ticket providers.
 * @since   5.0.3 Add filter to control the re-sending emails option on email alteration.
 *
 * @version 5.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Event Tickets Plus would set this from its own injected template to let us know about editable values.
global $tribe_my_tickets_have_meta;

$rsvp      = Tribe__Tickets__RSVP::get_instance();
$view      = Tribe__Tickets__Tickets_View::instance();
$event_id  = get_queried_object_id();
$event     = get_post( $event_id );
$post_type = get_post_type_object( $event->post_type );
$user_id   = get_current_user_id();
$provider  = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $event_id );

/** @var Tribe__Tickets__Editor__Template $template */
$template = tribe( 'tickets.editor.template' );

$event_has_tickets = $event_has_rsvp = false;
$provider_class    = '';

if ( $provider ) {
	$event_has_tickets = ! empty( $provider->get_tickets( $event_id ) );
	$event_has_rsvp    = ! empty( $rsvp->get_tickets( $event ) );
	$provider_class    = $provider->class_name;
}

$user_has_tickets           = $view->has_ticket_attendees( $event_id, $user_id );
$user_has_rsvp              = $rsvp->get_attendees_count_going_for_user( $event_id, $user_id );
$tribe_my_tickets_have_meta = false;

/**
 * This filter allows the admin to control the re-send email option when an attendee's email is updated.
 *
 * @since 5.0.3
 * @since 5.1.0 Updated the parameters to match what is used in Event Tickets Plus.
 *
 * @param bool         $allow_resending_email Whether to allow email resending.
 * @param WP_Post|null $ticket                The ticket post object if available, otherwise null.
 * @param array|null   $attendee              The attendee information if available, otherwise null.
 */
$allow_resending_email = (int) apply_filters( 'tribe_tickets_my_tickets_allow_email_resend_on_attendee_email_update', true, null, null );

/**
 * Display a notice if the user doesn't have tickets
 */
if (
	(
		$event_has_tickets
		|| $event_has_rsvp
	)
	&& ! $user_has_tickets
	&& ! $user_has_rsvp
) {

	if ( $event_has_tickets ) {
		$no_ticket_message = sprintf(
			_x( "You don't have %s for this event", 'notice if user does not have tickets', 'event-tickets' ),
			tribe_get_ticket_label_plural_lowercase( 'notice_user_does_not_have_tickets' )
		);
	} else {
		$no_ticket_message = sprintf(
			_x( "You don't have %s for this event", 'notice if user does not have rsvps', 'event-tickets' ),
			tribe_get_rsvp_label_plural_lowercase( 'notice_user_does_not_have_rsvps' )
		);
	}

	Tribe__Notices::set_notice(
		'ticket-no-results',
		esc_html( $no_ticket_message )
	);
}

$post_type_singular = $post_type ? $post_type->labels->singular_name : _x( 'Post', 'fallback post type singular name', 'event-tickets' );

$is_event_page = class_exists( 'Tribe__Events__Main' ) && Tribe__Events__Main::POSTTYPE === $event->post_type;
?>
<div id="tribe-events-content" class="tribe-events-single">
	<p class="tribe-back">
		<a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>">
			<?php
			// Translators: %s: post type label.
			printf( '&laquo; ' . esc_html__( 'View %s', 'event-tickets' ), $post_type_singular );
			?>
		</a>
	</p>

	<?php if ( $is_event_page ) : ?>
		<?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>

		<div class="tribe-events-schedule tribe-clearfix">
			<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
			<?php if ( tribe_get_cost() ) : ?>
				<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<!-- Notices -->
	<?php tribe_the_notices() ?>

	<div
		class="tribe-tickets__tickets-page-wrapper"
		data-post-id="<?php echo esc_attr( $event_id ); ?>"
		data-provider="<?php echo esc_attr( $provider ); ?>"
		data-attendee-resend-email="<?php echo esc_attr( $allow_resending_email ); ?>"
	>

		<form method="post" autocomplete="off">

			<?php $template->template( 'tickets/orders-rsvp' ); ?>

			<?php
			if ( ! class_exists( 'Tribe__Tickets_Plus__Commerce__PayPal__Meta' ) && Tribe__Tickets__Commerce__PayPal__Main::class === $provider_class ) {
				$template->template( 'tickets/orders-pp-tickets' );
			}
			?>

			<?php
			if ( ! class_exists( 'Tribe__Tickets_Plus__Meta' ) && \TEC\Tickets\Commerce\Module::class === $provider_class ) {
				$template->template( 'tickets/orders-tc-tickets' );
			}
			?>

			<?php
			/**
			 * Fires before the process tickets submission button is rendered
			 */
			do_action( 'tribe_tickets_orders_before_submit' );
			?>

			<?php if (
				// Current user has RSVP (with or without meta) so needs to be able to edit status
				$view->has_rsvp_attendees( $event_id, get_current_user_id() )
				|| (
					// Current user has tickets with meta so needs to be able to edit meta
					$view->has_ticket_attendees( $event_id, get_current_user_id() )
					&& $tribe_my_tickets_have_meta
				)
			) : ?>
				<div class="tribe-submit-tickets-form">
					<button
						type="submit"
						name="process-tickets"
						value="1"
						class="button alt"
					>
						<?php echo sprintf( esc_html__( 'Update %s', 'event-tickets' ), $view->get_description_rsvp_ticket( $event_id, get_current_user_id() ) ); ?>
					</button>
				</div>
			<?php endif;
			// unset our global since we don't need it any more
			unset( $tribe_my_tickets_have_meta );
			?>
		</form>

	</div>

</div>
