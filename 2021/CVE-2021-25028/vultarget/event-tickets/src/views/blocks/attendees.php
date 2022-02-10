<?php
/**
 * Block: Attendees List
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/attendees.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.11.3 Fix malformed opening `<div>` tag.
 *
 * @version 4.11.3
 */

$title     = $this->attr( 'title' );
$attendees = $this->get( 'attendees', null );
$classes   = [ 'tribe-block', 'tribe-block__attendees' ];

if ( ! is_array( $attendees ) ) {
	return;
}
?>
<div id="tribe-block__attendees" <?php tribe_classes( $classes ); ?>>

	<?php $this->template( 'blocks/attendees/title', [ 'title' => $title ] ); ?>

	<?php $this->template( 'blocks/attendees/description', [ 'attendees' => $attendees ] ); ?>

	<?php foreach ( $attendees as $key => $attendee ) : ?>

		<?php $this->template( 'blocks/attendees/gravatar', [ 'attendee' => $attendee ] ); ?>

	<?php endforeach; ?>
</div>
