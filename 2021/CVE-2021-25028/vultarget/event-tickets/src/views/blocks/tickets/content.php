<?php
/**
 * Block: Tickets
 * Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/content.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @version 4.11.0
 *
 */
$is_mini = $this->get( 'is_mini' );
$context = [
	'ticket' => $this->get( 'ticket' ),
	'key' => $this->get( 'key' ),
	'is_modal' => $this->get( 'is_modal' ),
	'is_mini' => $is_mini,
	'post_id' => $this->get( 'post_id' ),
	'provider' => $this->get( 'provider' ),
];
?>
<?php $this->template( 'blocks/tickets/content-title', $context ); ?>
<?php if ( ! $is_mini ) : ?>
	<?php $this->template( 'blocks/tickets/content-description', $context ); ?>
<?php endif; ?>
<?php $this->template( 'blocks/tickets/extra', $context ); ?>
