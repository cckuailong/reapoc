<?php
/**
 * Block: Attendees List
 *
 * Link to Tickets
 * Included on the Events Single Page after the meta
 * the Message that Will link to the Tickets Page
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/attendees/view-link.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.10.8 Renamed template from order-links.php to view-link.php. Updated to not use the now-deprecated
 *               third parameter of `get_description_rsvp_ticket()` and to simplify the template's logic.
 * @since 4.10.9 Uses new functions to get singular and plural texts.
 * @since 4.12.1 Account for empty post type object, such as if post type got disabled. Fix typo in sprintf placeholders.
 * @since 5.0.2 Fix template path in documentation block.
 *
 * @version 5.0.2
 *
 * @var Tribe__Tickets__Editor__Template $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$view      = Tribe__Tickets__Tickets_View::instance();
$event_id  = $this->get( 'post_id' );
$event     = get_post( $event_id );
$post_type = get_post_type_object( $event->post_type );
$user_id   = get_current_user_id();

$is_event_page = class_exists( 'Tribe__Events__Main' ) && Tribe__Events__Main::POSTTYPE === $event->post_type;

$post_type_singular = $post_type ? $post_type->labels->singular_name : _x( 'Post', 'fallback post type singular name', 'event-tickets' );
$counters           = [];
$rsvp_count         = $view->count_rsvp_attendees( $event_id, $user_id );
$ticket_count       = $view->count_ticket_attendees( $event_id, $user_id );

if ( 1 === $rsvp_count ) {
	// Translators: 1: the number one, 2: singular RSVP label.
	$counters[] = sprintf( _x( '%1$d %2$s', 'RSVP count singular', 'event-tickets' ), $rsvp_count, tribe_get_rsvp_label_singular( basename( __FILE__ ) ) );
} elseif ( 1 < $rsvp_count ) {
	// Translators: 1: the plural number of RSVPs, 2: plural RSVP label.
	$counters[] = sprintf( _x( '%1$d %2$s', 'RSVP count plural', 'event-tickets' ), $rsvp_count, tribe_get_rsvp_label_plural( basename( __FILE__ ) ) );
}

if ( 1 === $ticket_count ) {
	// Translators: 1: the number one, 2: singular Ticket label.
	$counters[] = sprintf( _x( '%1$d %2$s', 'Ticket count singular', 'event-tickets' ), $ticket_count, tribe_get_ticket_label_singular( basename( __FILE__ ) ) );
} elseif ( 1 < $ticket_count ) {
	// Translators: 1: the plural number of Tickets, 2: plural Ticket label.
	$counters[] = sprintf( _x( '%1$d %2$s', 'Ticket count plural', 'event-tickets' ), $ticket_count, tribe_get_ticket_label_plural( basename( __FILE__ ) ) );
}

if ( empty( $counters ) ) {
	return false;
}

$link = $view->get_tickets_page_url( $event_id, $is_event_page );

// Translators: 1: number of RSVPs and/or Tickets with accompanying ticket type text, 2: post type label
$message = esc_html( sprintf( __( 'You have %1s for this %2s.', 'event-tickets' ), implode( _x( ' and ', 'separator if there are both RSVPs and Tickets', 'event-tickets' ), $counters ), $post_type_singular ) );
?>

<div class="tribe-link-view-attendee">
	<?php echo $message ?>
	<a href="<?php echo esc_url( $link ) ?>"><?php echo sprintf( esc_html__( 'View your %s', 'event-tickets' ), $view->get_description_rsvp_ticket( $event_id, $user_id ) ) ?></a>
</div>
