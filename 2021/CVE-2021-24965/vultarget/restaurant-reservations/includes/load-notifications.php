<?php defined( 'ABSPATH' ) || exit;
/**
 * Functions to intercept email notifications and inject them into templates
 */

/**
 * Intercept an email notification and inject it into a template
 *
 * @param rtbNotificationEmail $notification The notification object. A copy of
 *  the booking will be found at $notification->booking. The message is at
 *  $notification->message.
 * @since 0.1
 */
if ( ! function_exists('etfrtb_notification_email_template') ) {
function etfrtb_notification_email_template( $notification ) {
	global $rtb_controller;

	if ( !is_a( $notification, 'rtbNotificationEmail' ) or ! $rtb_controller->permissions->check_permission( 'templates' ) ) {
		return;
	}

	$designer = new etfrtbDesigner();

	switch ( $notification->event ) {

		case 'new_submission' :
			if ( $notification->target == 'admin' ) {
				$designer->setup( 'booking-admin', $notification );
				$notification->message = $designer->render();
			} elseif ( $notification->target == 'user' ) {
				$designer->setup( 'booking-user', $notification );
				$notification->message = $designer->render();
			}
			break;

		case 'rtb_confirmed_booking' :
			$designer->setup( 'booking-admin', $notification );
			$notification->message = $designer->render();
			break;

		case 'pending_to_confirmed' :
			$designer->setup( 'confirmed-user', $notification );
			$notification->message = $designer->render();
			break;

		case 'pending_to_closed' :
			$designer->setup( 'rejected-user', $notification );
			$notification->message = $designer->render();
			break;

		case 'admin_email_notice' :
			$designer->setup( 'admin-notice', $notification );
			$notification->message = $designer->render();
			break;

		case 'reminder' :
			$designer->setup( 'reminder-user', $notification );
			$notification->message = $designer->render();
			break;

		case 'late_user' :
			$designer->setup( 'late-user', $notification );
			$notification->message = $designer->render();
			break;
	}
}
}
add_action( 'rtb_send_notification_before', 'etfrtb_notification_email_template', 100 );
