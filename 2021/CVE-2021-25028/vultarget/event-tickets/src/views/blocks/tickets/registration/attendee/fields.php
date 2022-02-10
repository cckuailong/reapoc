<?php
/**
 * Block: Tickets
 * Registration Attendee Fields
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/fields.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @version 4.9.4
 *
 */


$ticket = $this->get( 'ticket' );

$meta   = Tribe__Tickets_Plus__Main::instance()->meta();
$fields = $meta->get_meta_fields_by_ticket( $ticket->ID );

?>
<?php foreach ( $fields as $field ) : ?>
	<?php $this->template( 'blocks/tickets/registration/attendee/fields/' . $field->type  , array( 'ticket' => $ticket, 'field' => $field ) ); ?>
<?php endforeach; ?>
