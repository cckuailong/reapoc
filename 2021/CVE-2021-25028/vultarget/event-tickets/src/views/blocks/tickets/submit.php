<?php
/**
 * Block: Tickets
 * Submit
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/submit.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 5.0.3.1 Fix call to class that may not be active if ET+ has not fully met it's requirements.
 *
 * @version 5.0.3.1
 *
 */
$provider   = $this->get( 'provider' );
$must_login = ! is_user_logged_in() && $provider->login_required();
$event_id   = $this->get( 'event_id' );
$event      = get_post( $event_id );

try {
	/** @var \Tribe__Tickets__Attendee_Registration__Main $attendee_registration */
	$attendee_registration = tribe( 'tickets.attendee_registration' );
} catch ( RuntimeException $exception ) {
	$attendee_registration = null;
}

if ( $must_login ) {
	$this->template( 'blocks/tickets/submit-login' );
} elseif ( $attendee_registration && $attendee_registration->is_modal_enabled() ) {
	$this->template( 'blocks/tickets/submit-button-modal' );
} else {
	$this->template( 'blocks/tickets/submit-button' );
}
